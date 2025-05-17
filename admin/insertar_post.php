<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';
require 'verificar_sesion.php';

// Verificar que el usuario es administrador
verificarAdmin();

// Verificar si el usuario está logueado y obtener su ID
if (!isset($_SESSION['id_usuario'])) {
    // Redirigir silenciosamente sin mostrar información de depuración
    header('Location: usuario.php');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar que el usuario existe en la base de datos
$stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_usuario = ?");
$stmtCheck->execute([$id_usuario]);
if ($stmtCheck->fetchColumn() == 0) {
    header('Location: usuario.php?error=usuario_no_encontrado');
    exit();
}

// Configuración para subida de imágenes
$uploadDir = '../assets/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
// Agregar soporte para webp
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
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
        $errores = [
            UPLOAD_ERR_INI_SIZE => "El archivo excede el tamaño máximo permitido por PHP.",
            UPLOAD_ERR_FORM_SIZE => "El archivo excede el tamaño máximo permitido por el formulario.",
            UPLOAD_ERR_PARTIAL => "El archivo se subió parcialmente.",
            UPLOAD_ERR_NO_FILE => "No se subió ningún archivo.",
            UPLOAD_ERR_NO_TMP_DIR => "Falta la carpeta temporal.",
            UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en el disco.",
            UPLOAD_ERR_EXTENSION => "Una extensión de PHP detuvo la subida."
        ];
        $mensaje = isset($errores[$file['error']]) ? $errores[$file['error']] : "Error desconocido al subir el archivo";
        throw new Exception($mensaje);
    }
    
    // Verificar tipo de archivo
    $fileType = null;
    
    // Intentar obtener el MIME type con mime_content_type si está disponible
    if (function_exists('mime_content_type')) {
        $fileType = mime_content_type($file['tmp_name']);
    } 
    // Alternativa si mime_content_type no está disponible
    else {
        // Usar la extensión para determinar el tipo
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        if (isset($mimeTypes[$extension])) {
            $fileType = $mimeTypes[$extension];
        } else {
            throw new Exception("No se pudo determinar el tipo de archivo. Extensión no reconocida.");
        }
    }
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("Tipo de archivo no permitido ($fileType). Solo se aceptan JPEG, PNG, GIF y WebP.");
    }
    
    // Verificar tamaño
    if ($file['size'] > $maxFileSize) {
        $maxSizeMB = $maxFileSize / (1024 * 1024);
        throw new Exception("El archivo es demasiado grande. Tamaño máximo: {$maxSizeMB}MB.");
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    // Verificar y crear el directorio de destino si no existe
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("No se pudo crear el directorio para guardar las imágenes.");
        }
    }
    
    if (!is_writable($uploadDir)) {
        throw new Exception("No se puede escribir en el directorio de destino.");
    }
    
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

        // Verificar que la categoría existe
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE id_categoria = ?");
        $stmtCheck->execute([$id_categoria]);
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("La categoría seleccionada no existe");
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
            error_log("Imagen ilustrativa insertada con ID: " . $id_imagen_destacada);
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
            error_log("Imagen de fondo insertada con ID: " . $id_imagen_background);
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
        
        // Agregar logging para depuración
        error_log("Intentando insertar post con los siguientes datos:");
        error_log("Título: " . $titulo);
        error_log("Slug: " . $slug);
        error_log("ID Categoría: " . $id_categoria);
        error_log("ID Imagen Destacada: " . $id_imagen_destacada);
        error_log("ID Imagen Background: " . $id_imagen_background);
        error_log("ID Usuario: " . $id_usuario);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            $id_post = $pdo->lastInsertId();
            error_log("Post insertado exitosamente con ID: " . $id_post);
            // Redirigir con mensaje de éxito
            header('Location: adminControl.php?success=1');
            exit();
        } else {
            error_log("Error al insertar post: " . print_r($stmt->errorInfo(), true));
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