<?php
// Script simple para probar la carga de imágenes
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/simplificar_debug.log');

// Aumentar límites de subida de archivos
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '21M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');

// Función para log
function log_message($message, $data = null) {
    $log = "[" . date('Y-m-d H:i:s') . "] $message";
    if ($data !== null) {
        $log .= "\n" . json_encode($data, JSON_PRETTY_PRINT);
    }
    $log .= "\n";
    file_put_contents(__DIR__ . '/simplificar_debug.log', $log, FILE_APPEND);
    return $message;
}

// Información del sistema
log_message("Información del sistema", [
    'php_version' => phpversion(),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time')
]);

// Verificar si es una solicitud de carga
$mensaje = '';
$ruta_imagen = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_message("Recibida solicitud POST", $_POST);
    log_message("Archivos recibidos", $_FILES);

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_size = $_FILES['imagen']['size'];
        $file_type = $_FILES['imagen']['type'];
        
        log_message("Archivo recibido correctamente", [
            'nombre' => $file_name,
            'tipo' => $file_type,
            'tamaño' => $file_size,
            'tmp_name' => $file_tmp
        ]);
        
        // Verificar directorio de destino
        $upload_dir = '../assets/categorias/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $mensaje = "Error: No se pudo crear el directorio de destino";
                log_message("Error al crear directorio", ['error' => error_get_last()]);
            } else {
                log_message("Directorio creado exitosamente");
            }
        }
        
        // Verificar permisos
        $permisos = substr(sprintf('%o', fileperms($upload_dir)), -4);
        $es_escribible = is_writable($upload_dir) ? 'Sí' : 'No';
        log_message("Permisos del directorio", [
            'permisos' => $permisos,
            'es_escribible' => $es_escribible
        ]);
        
        // Intentar mover el archivo
        $new_file_name = uniqid() . '_' . $file_name;
        $destino = $upload_dir . $new_file_name;
        
        if (move_uploaded_file($file_tmp, $destino)) {
            $mensaje = "Imagen cargada exitosamente";
            $ruta_imagen = '../assets/categorias/' . $new_file_name;
            log_message("Imagen movida exitosamente", ['ruta' => $destino]);
        } else {
            $mensaje = "Error: No se pudo mover el archivo";
            log_message("Error al mover el archivo", ['error' => error_get_last()]);
            
            // Intentar con copy como alternativa
            if (copy($file_tmp, $destino)) {
                $mensaje = "Imagen copiada exitosamente (usando copy)";
                $ruta_imagen = '../assets/categorias/' . $new_file_name;
                log_message("Imagen copiada exitosamente usando copy", ['ruta' => $destino]);
            } else {
                $mensaje = "Error: También falló al intentar copiar el archivo";
                log_message("Error al copiar el archivo", ['error' => error_get_last()]);
            }
        }
    } else if (isset($_FILES['imagen'])) {
        $error_code = $_FILES['imagen']['error'];
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "El archivo excede el tamaño máximo permitido por PHP",
            UPLOAD_ERR_FORM_SIZE => "El archivo excede el tamaño máximo permitido por el formulario",
            UPLOAD_ERR_PARTIAL => "El archivo fue cargado parcialmente",
            UPLOAD_ERR_NO_FILE => "No se seleccionó ningún archivo",
            UPLOAD_ERR_NO_TMP_DIR => "Falta la carpeta temporal",
            UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en el disco",
            UPLOAD_ERR_EXTENSION => "La carga del archivo fue detenida por una extensión de PHP"
        ];
        $mensaje = "Error: " . ($error_messages[$error_code] ?? "Error desconocido ($error_code)");
        log_message("Error en la carga", [
            'error_code' => $error_code,
            'error_message' => $error_messages[$error_code] ?? "Error desconocido"
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de carga de imágenes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        form {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0069d9;
        }
        .image-preview {
            margin-top: 20px;
            text-align: center;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Prueba de carga de imágenes</h1>
    
    <?php if (!empty($mensaje)): ?>
        <div class="message <?php echo strpos($mensaje, 'Error') === 0 ? 'error' : 'success'; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>
    
    <form action="simplificar_carga_imagen.php" method="POST" enctype="multipart/form-data">
        <label for="imagen">Selecciona una imagen:</label>
        <input type="file" id="imagen" name="imagen" accept="image/*">
        <button type="submit">Subir imagen</button>
    </form>
    
    <?php if (!empty($ruta_imagen)): ?>
        <div class="image-preview">
            <h2>Imagen cargada</h2>
            <img src="<?php echo $ruta_imagen; ?>" alt="Imagen cargada">
            <p>Ruta: <?php echo $ruta_imagen; ?></p>
        </div>
    <?php endif; ?>

    <h2>Información de PHP</h2>
    <ul>
        <li>Versión de PHP: <?php echo phpversion(); ?></li>
        <li>upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
        <li>post_max_size: <?php echo ini_get('post_max_size'); ?></li>
        <li>max_execution_time: <?php echo ini_get('max_execution_time'); ?></li>
        <li>max_input_time: <?php echo ini_get('max_input_time'); ?></li>
    </ul>
    
    <a href="adminControl.php">Volver al panel de control</a>
</body>
</html> 