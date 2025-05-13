<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';

// Configuration for image uploads
$uploadDir = '../assets/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Function for generating slug
function generarSlug($titulo) {
    $slug = strtolower($titulo);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Function for uploading image
function subirImagen($file, $uploadDir, $allowedTypes, $maxFileSize) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error uploading the file: " . $file['error']);
    }
    
    // Verify file type
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("File type not allowed. Only JPEG, PNG and GIF are accepted.");
    }
    
    // Verify size
    if ($file['size'] > $maxFileSize) {
        throw new Exception("The file is too large. Maximum size: 2MB.");
    }
    
    // Generate unique name
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Could not save the uploaded file.");
    }
    
    return $destination; // Return full path
}

// Endpoint to get post data for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Get post data
        $sql = "SELECT p.*, c.id_categoria FROM posts p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_post = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            http_response_code(404);
            echo json_encode(['error' => 'Post not found']);
            exit();
        }
        
        // Get image information if exists
        $imagen = null;
        if ($post['id_imagen_destacada']) {
            $sqlImagen = "SELECT * FROM imagenes WHERE id_imagen = :id";
            $stmtImagen = $pdo->prepare($sqlImagen);
            $stmtImagen->bindParam(':id', $post['id_imagen_destacada'], PDO::PARAM_INT);
            $stmtImagen->execute();
            $imagen = $stmtImagen->fetch(PDO::FETCH_ASSOC);
        }
        
        $post['imagen'] = $imagen;
        
        echo json_encode($post);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
}

// Handle POST request to update post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect and validate form data
        $id = intval($_POST['id']);
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $contenido = trim($_POST['contenido']);
        $id_categoria = intval($_POST['categoria']);
        $estado = $_POST['estado'];
        $id_usuario = 1; // Assume admin with ID 1
        
        // Basic validations
        if (empty($titulo) || strlen($titulo) > 255) {
            throw new Exception("Title is required and must not exceed 255 characters");
        }
        
        if (empty($contenido)) {
            throw new Exception("Content is required");
        }
        
        if ($id_categoria <= 0) {
            throw new Exception("You must select a valid category");
        }
        
        // Get current post data to check if slug needs to be updated
        $sqlGetPost = "SELECT titulo FROM posts WHERE id_post = :id";
        $stmtGetPost = $pdo->prepare($sqlGetPost);
        $stmtGetPost->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtGetPost->execute();
        $currentPost = $stmtGetPost->fetch(PDO::FETCH_ASSOC);
        
        // Update slug if title changed
        $slug = null;
        if ($currentPost && $currentPost['titulo'] !== $titulo) {
            $slug = generarSlug($titulo);
            
            // Verify if slug already exists
            $sqlVerifySlug = "SELECT COUNT(*) FROM posts WHERE slug = :slug AND id_post != :id";
            $stmtVerify = $pdo->prepare($sqlVerifySlug);
            $stmtVerify->execute([
                ':slug' => $slug,
                ':id' => $id
            ]);
            
            if ($stmtVerify->fetchColumn() > 0) {
                $slug = $slug . '-' . uniqid();
            }
        }
        
        // Process image if uploaded
        $id_imagen_destacada = null;
        if (!empty($_FILES['imagen']['name'])) {
            $imagenPath = subirImagen($_FILES['imagen'], $uploadDir, $allowedTypes, $maxFileSize);
            
            // Insert image in database
            $sqlImagen = "INSERT INTO imagenes (ruta, titulo, alt_text, id_usuario) 
                         VALUES (:ruta, :titulo, :alt_text, :id_usuario)";
            
            $stmtImagen = $pdo->prepare($sqlImagen);
            $tituloImagen = "Featured image for: " . substr($titulo, 0, 50);
            $altText = "Illustrative image for post: " . substr($titulo, 0, 100);
            
            $stmtImagen->execute([
                ':ruta' => $imagenPath,
                ':titulo' => $tituloImagen,
                ':alt_text' => $altText,
                ':id_usuario' => $id_usuario
            ]);
            
            $id_imagen_destacada = $pdo->lastInsertId();
        } else if (isset($_POST['current_image_id'])) {
            // Keep current image
            $id_imagen_destacada = $_POST['current_image_id'];
        }
        
        // Prepare SQL query to update post
        $sql = "UPDATE posts SET 
                titulo = :titulo,
                " . ($slug ? "slug = :slug," : "") . "
                resumen = :resumen,
                contenido = :contenido,
                id_categoria = :id_categoria,
                " . ($id_imagen_destacada ? "id_imagen_destacada = :id_imagen_destacada," : "") . "
                estado = :estado,
                fecha_actualizacion = NOW()
                WHERE id_post = :id";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        if ($slug) {
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        }
        $stmt->bindParam(':resumen', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':contenido', $contenido, PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        if ($id_imagen_destacada) {
            $stmt->bindParam(':id_imagen_destacada', $id_imagen_destacada, PDO::PARAM_INT);
        }
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Execute query
        if ($stmt->execute()) {
            // Redirect with success message
            header('Location: adminControl.php?success=2');
            exit();
        } else {
            throw new Exception("Error updating the post in the database");
        }
        
    } catch (Exception $e) {
        // Handle errors
        error_log("Error updating post: " . $e->getMessage());
        
        // Redirect with error message
        header('Location: adminControl.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    // If not GET or POST, redirect
    header('Location: adminControl.php');
    exit();
}