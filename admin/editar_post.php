<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Añadido para manejar sesiones

require '../config/db.php';

// Configuration for image uploads
$uploadDir = '../assets/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Función para depuración
function debug_log($message) {
    error_log("[DEBUG] " . $message);
}

// Function for generating slug
function generarSlug($titulo) {
    $slug = strtolower($titulo);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Función para subir imágenes
function subirImagen($file, $uploadDir, $allowedTypes, $maxFileSize) {
    debug_log("Iniciando carga de imagen: " . $file['name']);
    
    // Verificar si hubo algún error en la carga
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se cargó parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la carga'
        ];
        
        $errorMessage = isset($errorMessages[$file['error']]) 
                       ? $errorMessages[$file['error']] 
                       : 'Error desconocido al cargar el archivo';
        
        debug_log("Error en carga: " . $errorMessage);
        return [
            'success' => false, 
            'message' => $errorMessage
        ];
    }
    
    // Verificar tipo de archivo
    if (!in_array($file['type'], $allowedTypes)) {
        debug_log("Tipo de archivo no permitido: " . $file['type']);
        return [
            'success' => false, 
            'message' => 'Tipo de archivo no permitido. Sólo se permiten: ' . implode(', ', $allowedTypes)
        ];
    }
    
    // Verificar tamaño de archivo
    if ($file['size'] > $maxFileSize) {
        debug_log("Archivo excede el tamaño máximo: " . $file['size'] . " > " . $maxFileSize);
        return [
            'success' => false, 
            'message' => 'El archivo excede el tamaño máximo permitido de ' . ($maxFileSize / (1024 * 1024)) . 'MB'
        ];
    }
    
    // Generar nombre único para el archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nuevoNombre = uniqid() . '.' . $extension;
    $rutaDestino = $uploadDir . $nuevoNombre;
    
    // Asegurarse de que la ruta del directorio no contenga "../" al principio
    $rutaDestino = ltrim($rutaDestino, '../');
    
    // Verificar que el directorio exista
    $directorio = dirname($rutaDestino);
    if (!file_exists($directorio)) {
        debug_log("El directorio no existe, intentando crear: " . $directorio);
        if (!mkdir($directorio, 0755, true)) {
            debug_log("Error al crear directorio: " . $directorio);
            return [
                'success' => false, 
                'message' => 'Error al crear el directorio para guardar la imagen'
            ];
        }
    }
    
    // Intentar mover el archivo
    $rutaCompleta = __DIR__ . '/../' . $rutaDestino;
    debug_log("Intentando mover archivo a: " . $rutaCompleta);
    
    if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
        debug_log("Error al mover el archivo de " . $file['tmp_name'] . " a " . $rutaCompleta);
        return [
            'success' => false, 
            'message' => 'Error al guardar el archivo en el servidor'
        ];
    }
    
    debug_log("Archivo subido exitosamente: " . $rutaDestino);
    return [
        'success' => true, 
        'ruta' => $rutaDestino,
        'message' => 'Archivo cargado exitosamente'
    ];
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
        $imagen_destacada = null;
        $imagen_background = null;
        
        if (isset($post['id_imagen_destacada']) && $post['id_imagen_destacada']) {
            $sqlImagen = "SELECT id_imagen, ruta FROM imagenes WHERE id_imagen = :id";
            $stmtImagen = $pdo->prepare($sqlImagen);
            $stmtImagen->bindParam(':id', $post['id_imagen_destacada'], PDO::PARAM_INT);
            $stmtImagen->execute();
            $imagen = $stmtImagen->fetch(PDO::FETCH_ASSOC);
            if ($imagen && isset($imagen['ruta'])) {
                $imagen_destacada = $imagen['ruta'];
                
                // Verificar que la imagen exista físicamente
                $rutaFisica = $imagen_destacada;
                if (strpos($rutaFisica, '../') === 0) {
                    $rutaFisica = substr($rutaFisica, 3);
                }
                
                if (!file_exists($rutaFisica)) {
                    debug_log("Imagen destacada no encontrada físicamente: " . $rutaFisica);
                }
            }
        }
        
        if (isset($post['id_imagen_background']) && $post['id_imagen_background']) {
            $sqlBackground = "SELECT id_imagen, ruta FROM imagenes WHERE id_imagen = :id";
            $stmtBackground = $pdo->prepare($sqlBackground);
            $stmtBackground->bindParam(':id', $post['id_imagen_background'], PDO::PARAM_INT);
            $stmtBackground->execute();
            $background = $stmtBackground->fetch(PDO::FETCH_ASSOC);
            if ($background && isset($background['ruta'])) {
                $imagen_background = $background['ruta'];
                
                // Verificar que la imagen exista físicamente
                $rutaFisica = $imagen_background;
                if (strpos($rutaFisica, '../') === 0) {
                    $rutaFisica = substr($rutaFisica, 3);
                }
                
                if (!file_exists($rutaFisica)) {
                    debug_log("Imagen de fondo no encontrada físicamente: " . $rutaFisica);
                }
            }
        }
        
        // Añadir información sobre la URL base para construir rutas absolutas
        $server_protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $server_host = $_SERVER['HTTP_HOST'];
        $base_url = $server_protocol . $server_host;
        
        // Create the response structure that matches what the frontend expects
        $response = [
            'post' => [
                'id_post' => $post['id_post'],
                'titulo' => $post['titulo'],
                'resumen' => $post['resumen'],
                'contenido' => $post['contenido'],
                'id_categoria' => $post['id_categoria'],
                'imagen_destacada' => $imagen_destacada,
                'id_imagen_destacada' => $post['id_imagen_destacada'],
                'imagen_background' => $imagen_background,
                'id_imagen_background' => $post['id_imagen_background'],
                'base_url' => $base_url
            ]
        ];
        
        // Enviar respuesta con cabeceras para debug
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } catch (PDOException $e) {
        debug_log("Error al obtener los datos del post: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
}

// Procesar actualización de un post existente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    debug_log("Iniciando actualización del post ID: " . $_POST['id']);
    
    // Imprimir todos los datos recibidos para depuración
    debug_log("Datos POST recibidos: " . json_encode($_POST));
    
    $post_id = intval($_POST['id']);
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $contenido = $_POST['contenido'];
    $categoria = intval($_POST['categoria']);
    
    // Registrar datos de archivos
    if (isset($_FILES)) {
        debug_log("Archivos recibidos: " . json_encode($_FILES));
    }
    
    // Generar slug
    $slug = generarSlug($titulo);
    debug_log("Slug generado: " . $slug);
    
    try {
        // Obtener los IDs de las imágenes actuales para preservarlos en caso de que no se suban nuevas imágenes
        $stmtGetImagenes = $pdo->prepare("SELECT id_imagen_destacada, id_imagen_background FROM posts WHERE id_post = ?");
        $stmtGetImagenes->execute([$post_id]);
        $imagenes_actuales = $stmtGetImagenes->fetch(PDO::FETCH_ASSOC);
        
        $id_imagen_destacada = $imagenes_actuales['id_imagen_destacada'];  // ID actual por defecto
        $id_imagen_background = $imagenes_actuales['id_imagen_background']; // ID actual por defecto
        
        // Si se enviaron campos con los IDs actuales de las imágenes, guardarlos
        if (isset($_POST['current_image_id']) && !empty($_POST['current_image_id'])) {
            $id_imagen_destacada = $_POST['current_image_id'];
            debug_log("Usando ID de imagen destacada existente: $id_imagen_destacada");
        }
        
        if (isset($_POST['current_background_id']) && !empty($_POST['current_background_id'])) {
            $id_imagen_background = $_POST['current_background_id'];
            debug_log("Usando ID de imagen de fondo existente: $id_imagen_background");
        }
        
        // Procesamiento de la nueva imagen destacada (si se subió)
        if (isset($_FILES['imagen_ilustrativa']) && $_FILES['imagen_ilustrativa']['error'] !== UPLOAD_ERR_NO_FILE) {
            debug_log("Procesando nueva imagen destacada");
            $resultado_subida = subirImagen($_FILES['imagen_ilustrativa'], $uploadDir, $allowedTypes, $maxFileSize);
            
            if ($resultado_subida['success']) {
                // Insertar en la tabla de imágenes y obtener el ID
                $stmt = $pdo->prepare("INSERT INTO imagenes (titulo, ruta, alt_text, fecha_subida) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$_POST['titulo'], $resultado_subida['ruta'], $_POST['titulo']]);
                $id_imagen_destacada = $pdo->lastInsertId();
                debug_log("Nueva imagen destacada subida, ID: $id_imagen_destacada, Ruta: " . $resultado_subida['ruta']);
            } else {
                debug_log("Error al subir imagen destacada: " . $resultado_subida['message']);
                // No cambiar el ID, mantener el actual
            }
        }
        
        // Procesamiento de la nueva imagen de fondo (si se subió)
        if (isset($_FILES['imagen_background']) && $_FILES['imagen_background']['error'] !== UPLOAD_ERR_NO_FILE) {
            debug_log("Procesando nueva imagen de fondo");
            $resultado_subida = subirImagen($_FILES['imagen_background'], $uploadDir, $allowedTypes, $maxFileSize);
            
            if ($resultado_subida['success']) {
                // Insertar en la tabla de imágenes y obtener el ID
                $stmt = $pdo->prepare("INSERT INTO imagenes (titulo, ruta, alt_text, fecha_subida) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$_POST['titulo'] . ' (fondo)', $resultado_subida['ruta'], $_POST['titulo'] . ' fondo']);
                $id_imagen_background = $pdo->lastInsertId();
                debug_log("Nueva imagen de fondo subida, ID: $id_imagen_background, Ruta: " . $resultado_subida['ruta']);
            } else {
                debug_log("Error al subir imagen de fondo: " . $resultado_subida['message']);
                // No cambiar el ID, mantener el actual
            }
        }
        
        // Actualizar el post
        $sql = "UPDATE posts SET 
                titulo = :titulo, 
                slug = :slug, 
                resumen = :resumen, 
                contenido = :contenido, 
                id_categoria = :id_categoria,
                id_imagen_destacada = :id_imagen_destacada,
                id_imagen_background = :id_imagen_background,
                fecha_actualizacion = NOW()
                WHERE id_post = :id_post";
                
        debug_log("Ejecutando SQL de actualización: " . $sql);
        debug_log("Parámetros: " . json_encode([
            'titulo' => $titulo,
            'slug' => $slug,
            'resumen' => $descripcion,
            'contenido' => substr($contenido, 0, 50) . '...',
            'id_categoria' => $categoria,
            'id_imagen_destacada' => $id_imagen_destacada,
            'id_imagen_background' => $id_imagen_background,
            'id_post' => $post_id
        ]));
                
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':titulo' => $titulo,
            ':slug' => $slug,
            ':resumen' => $descripcion,
            ':contenido' => $contenido,
            ':id_categoria' => $categoria,
            ':id_imagen_destacada' => $id_imagen_destacada,
            ':id_imagen_background' => $id_imagen_background,
            ':id_post' => $post_id
        ]);
        
        if (!$resultado) {
            debug_log("Error al ejecutar la actualización: " . implode(", ", $stmt->errorInfo()));
            throw new Exception("No se pudo actualizar el post en la base de datos: " . implode(", ", $stmt->errorInfo()));
        }
        
        // Verificar filas afectadas
        $filasAfectadas = $stmt->rowCount();
        debug_log("Filas afectadas por la actualización: $filasAfectadas");
        
        if ($filasAfectadas === 0) {
            debug_log("Advertencia: La consulta se ejecutó correctamente pero no se modificaron filas. Esto puede indicar que no hubo cambios en los datos o que el post no existe.");
            // Vamos a hacer una comprobación adicional para confirmar que el post existe
            $check_post = $pdo->prepare("SELECT id_post FROM posts WHERE id_post = ?");
            $check_post->execute([$post_id]);
            if (!$check_post->fetch()) {
                throw new Exception("No se encontró el post con ID $post_id");
            }
        }
        
        // Registrar los valores finales para depuración
        debug_log("Valores finales de imágenes - Destacada: $id_imagen_destacada, Fondo: $id_imagen_background");
        
        // Confirmar transacción
        debug_log("Confirmando transacción");
        $pdo->commit();
        
        // Redirigir de vuelta al panel de control
        $_SESSION['success'] = "El artículo se ha actualizado correctamente.";
        debug_log("Operación completada con éxito. Redirigiendo a adminControl.php");
        header('Location: adminControl.php');
        exit();
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        debug_log("Error en la transacción: " . $e->getMessage());
        $pdo->rollBack();
        debug_log("Transacción revertida");
        $_SESSION['error'] = "Error al actualizar el artículo: " . $e->getMessage();
        header('Location: adminControl.php');
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    // If not GET or POST, redirect
    debug_log("Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    header('Location: adminControl.php');
    exit();
}