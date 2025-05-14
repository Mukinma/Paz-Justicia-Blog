<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';

// Configuración para subida de imágenes
$uploadDir = '../assets/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Función para generar el slug
function generarSlug($titulo) {
    $slug = strtolower($titulo);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Función para subir imagen
function subirImagen($file, $uploadDir, $allowedTypes, $maxFileSize, $tipo_imagen) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error al subir el archivo: " . $file['error']);
    }
    
    // Verificar tipo de archivo
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("Tipo de archivo no permitido. Solo se aceptan JPEG, PNG y GIF.");
    }
    
    // Verificar tamaño
    if ($file['size'] > $maxFileSize) {
        throw new Exception("El archivo es demasiado grande. Tamaño máximo: 2MB.");
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("No se pudo guardar el archivo subido.");
    }
    
    return $uploadDir . $filename; // Return full path
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recoger y validar los datos del formulario
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $contenido = trim($_POST['contenido']);
        $id_categoria = intval($_POST['categoria']);
        $fecha_publicacion = date('Y-m-d H:i:s'); // Usar fecha actual
        $id_usuario = 1; // Asumimos que es el admin con ID 1
        
        // Validaciones básicas
        if (empty($titulo) || strlen($titulo) > 255) {
            throw new Exception("El título es obligatorio y no debe exceder 255 caracteres");
        }
        
        if (empty($contenido)) {
            throw new Exception("El contenido es obligatorio");
        }
        
        if ($id_categoria <= 0) {
            throw new Exception("Debe seleccionar una categoría válida");
        }
        
        // Procesar imágenes si se subieron
        $id_imagen_destacada = null;
        $id_imagen_background = null;

        // Procesar imagen ilustrativa
        if (!empty($_FILES['imagen_ilustrativa']['name'])) {
            $imagenPath = subirImagen($_FILES['imagen_ilustrativa'], $uploadDir, $allowedTypes, $maxFileSize, 'ilustrativa');
            
            // Insertar la imagen ilustrativa en la base de datos
            $sqlImagen = "INSERT INTO imagenes (ruta, titulo, alt_text, tipo_imagen, id_usuario) 
                         VALUES (:ruta, :titulo, :alt_text, :tipo_imagen, :id_usuario)";
            
            $stmtImagen = $pdo->prepare($sqlImagen);
            $tituloImagen = "Imagen ilustrativa para: " . substr($titulo, 0, 50);
            $altText = "Imagen ilustrativa del post: " . substr($titulo, 0, 100);
            
            $stmtImagen->execute([
                ':ruta' => $imagenPath,
                ':titulo' => $tituloImagen,
                ':alt_text' => $altText,
                ':tipo_imagen' => 'ilustrativa',
                ':id_usuario' => $id_usuario
            ]);
            
            $id_imagen_destacada = $pdo->lastInsertId();
        }

        // Procesar imagen de fondo
        if (!empty($_FILES['imagen_background']['name'])) {
            $imagenPath = subirImagen($_FILES['imagen_background'], $uploadDir, $allowedTypes, $maxFileSize, 'background');
            
            // Insertar la imagen de fondo en la base de datos
            $sqlImagen = "INSERT INTO imagenes (ruta, titulo, alt_text, tipo_imagen, id_usuario) 
                         VALUES (:ruta, :titulo, :alt_text, :tipo_imagen, :id_usuario)";
            
            $stmtImagen = $pdo->prepare($sqlImagen);
            $tituloImagen = "Imagen de fondo para: " . substr($titulo, 0, 50);
            $altText = "Imagen de fondo del post: " . substr($titulo, 0, 100);
            
            $stmtImagen->execute([
                ':ruta' => $imagenPath,
                ':titulo' => $tituloImagen,
                ':alt_text' => $altText,
                ':tipo_imagen' => 'background',
                ':id_usuario' => $id_usuario
            ]);
            
            $id_imagen_background = $pdo->lastInsertId();
        }
        
        // Generar el slug
        $slug = generarSlug($titulo);
        
        // Verificar si el slug ya existe
        $sqlVerificarSlug = "SELECT COUNT(*) FROM posts WHERE slug = :slug";
        $stmtVerificar = $pdo->prepare($sqlVerificarSlug);
        $stmtVerificar->execute([':slug' => $slug]);
        $existeSlug = $stmtVerificar->fetchColumn();
        
        if ($existeSlug > 0) {
            $slug = $slug . '-' . uniqid();
        }
        
        // Preparar la consulta SQL para insertar el post
        $sql = "INSERT INTO posts (titulo, slug, resumen, contenido, id_categoria, id_imagen_destacada, id_imagen_background, id_usuario, fecha_publicacion, estado) 
                VALUES (:titulo, :slug, :resumen, :contenido, :id_categoria, :id_imagen_destacada, :id_imagen_background, :id_usuario, :fecha_publicacion, 'publicado')";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind de parámetros
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':resumen', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':contenido', $contenido, PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':id_imagen_destacada', $id_imagen_destacada, PDO::PARAM_INT);
        $stmt->bindParam(':id_imagen_background', $id_imagen_background, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_publicacion', $fecha_publicacion, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir con mensaje de éxito
            header('Location: adminControl.php?success=1');
            exit();
        } else {
            throw new Exception("Error al insertar el post en la base de datos");
        }
        
    } catch (Exception $e) {
        // Manejar errores - puedes registrar el error en un archivo log si es necesario
        error_log("Error al insertar post: " . $e->getMessage());
        
        // Redirigir con mensaje de error
        header('Location: adminControl.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no es POST, redirigir
    header('Location: adminControl.php');
    exit();
}