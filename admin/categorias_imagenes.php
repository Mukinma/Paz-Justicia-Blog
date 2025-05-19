<?php
session_start();
require_once '../config/db.php';
// Incluir el archivo de ayuda para subida de archivos
require_once 'utils/upload_helper.php';
// Añadir después de los requires existentes
require_once 'utils/image_resizer.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirigir a la página principal con un mensaje de error
    $_SESSION['error'] = "No tienes permisos para acceder a esta sección.";
    header('Location: ../index.php');
    exit();
}

// Para depuración
$debug_info = [];
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/imagenes_debug.log');

// Establecer límites de subida de archivos - estos valores no tienen efecto sin php.ini
// pero lo dejamos documentado
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '22M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '256M');

// Función para añadir información de depuración
function add_debug($message, $data = []) {
    global $debug_info;
    $debug_info[] = ['message' => $message, 'data' => $data];
    
    // También guardar en el log
    $log_message = "[" . date('Y-m-d H:i:s') . "] $message\n";
    if (!empty($data)) {
        $log_message .= json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    file_put_contents(__DIR__ . '/imagenes_debug.log', $log_message, FILE_APPEND);
}

// Verificar y crear directorios necesarios
$assets_dir = __DIR__ . '/../assets/';
$categorias_dir = __DIR__ . '/../assets/categorias/';

// Verificar/crear directorio assets
if (!file_exists($assets_dir)) {
    add_debug("El directorio assets no existe. Intentando crear...");
    if (!mkdir($assets_dir, 0777, true)) {
        add_debug("ERROR: No se pudo crear el directorio assets", ['error' => error_get_last()]);
        $_SESSION['error'] = "No se pudo crear el directorio para almacenar imágenes.";
        header('Location: ../index.php');
        exit();
    }
    chmod($assets_dir, 0777);
    add_debug("Directorio assets creado con éxito");
} else if (!is_writable($assets_dir)) {
    add_debug("ADVERTENCIA: El directorio assets existe pero no tiene permisos de escritura");
    chmod($assets_dir, 0777);
}

// Verificar/crear directorio categorias
if (!file_exists($categorias_dir)) {
    add_debug("El directorio categorias no existe. Intentando crear...");
    if (!mkdir($categorias_dir, 0777, true)) {
        add_debug("ERROR: No se pudo crear el directorio categorias", ['error' => error_get_last()]);
        $_SESSION['error'] = "No se pudo crear el directorio para almacenar imágenes de categorías.";
        header('Location: ../index.php');
        exit();
    }
    chmod($categorias_dir, 0777);
    add_debug("Directorio categorias creado con éxito");
} else if (!is_writable($categorias_dir)) {
    add_debug("ADVERTENCIA: El directorio categorias existe pero no tiene permisos de escritura");
    chmod($categorias_dir, 0777);
}

// Mensaje de acción realizada
$mensaje = '';

// Proceso para actualizar imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    add_debug("Recibida solicitud POST", $_POST);
    add_debug("Archivos recibidos", $_FILES);

    $id_categoria = $_POST['id_categoria'];
    
    // Determinar qué tipo de imagen se está actualizando
    $tipo_imagen = ($_POST['accion'] === 'actualizar_fondo') ? 'fondo' : 'icono';
    $campo_db = ($tipo_imagen === 'fondo') ? 'imagen_fondo' : 'imagen';
    $input_name = ($tipo_imagen === 'fondo') ? 'imagen_fondo' : 'imagen';
    
    add_debug("Tipo de imagen a actualizar", [
        'tipo' => $tipo_imagen,
        'campo_db' => $campo_db,
        'input_name' => $input_name
    ]);
    
    // Verificar si se subió una imagen
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK && $_FILES[$input_name]['size'] > 0) {
        // Validar tipo de archivo
        $fileType = $_FILES[$input_name]['type'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($fileType, $allowedTypes)) {
            add_debug("Tipo de archivo no permitido", ['tipo' => $fileType]);
            $mensaje = "Error: Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG, GIF y WEBP.";
        } else {
            // Preparar el nombre del archivo
            $nombre_temporal = $_FILES[$input_name]['tmp_name'];
            $nombre_original = $_FILES[$input_name]['name'];
            
            // Sanitizar el nombre de archivo para evitar problemas con espacios y caracteres especiales
            $nombre_sanitizado = sanitizar_nombre_archivo($nombre_original);
            $nombre_archivo = ($tipo_imagen === 'fondo' ? 'bg_' : 'icono_') . $id_categoria . '_' . $nombre_sanitizado;
            
            add_debug("Intentando procesar imagen", [
                'origen' => $nombre_temporal, 
                'nombre_sanitizado' => $nombre_sanitizado,
                'tamaño' => filesize($nombre_temporal)
            ]);
            
            // Intentar redimensionar la imagen para reducir su tamaño
            $resultado_optimizacion = process_uploaded_image(
                $_FILES[$input_name],
                $categorias_dir,
                $nombre_archivo
            );
            
            if ($resultado_optimizacion['success']) {
                add_debug("Imagen redimensionada y optimizada correctamente", $resultado_optimizacion);
                $ruta_db = 'assets/categorias/' . $nombre_archivo;
                
                // Actualizar en la base de datos
                $stmt = $pdo->prepare("UPDATE categorias SET $campo_db = ? WHERE id_categoria = ?");
                if ($stmt->execute([$ruta_db, $id_categoria])) {
                    $mensaje = "Imagen " . ($tipo_imagen === 'fondo' ? "de fondo" : "") . " actualizada correctamente para la categoría.";
                    add_debug("Imagen actualizada en la base de datos", [
                        'campo' => $campo_db, 
                        'id' => $id_categoria, 
                        'ruta' => $ruta_db,
                        'tamaño_original' => $resultado_optimizacion['original_size'],
                        'tamaño_nuevo' => $resultado_optimizacion['new_size']
                    ]);
                } else {
                    $errorInfo = $stmt->errorInfo();
                    add_debug("Error al actualizar la imagen en la base de datos", ['error' => $errorInfo]);
                    $mensaje = "Error al actualizar la imagen en la base de datos: " . $errorInfo[2];
                }
            } else {
                add_debug("Error al optimizar la imagen", $resultado_optimizacion);
                
                // Como fallback, intentar el método normal
                $ruta_destino = $categorias_dir . $nombre_archivo;
                
                // Utilizar la función auxiliar para subir el archivo con múltiples métodos
                $prefijo_log = "Categoría {$id_categoria} ({$tipo_imagen})";
                $resultado_subida = subir_archivo_seguro($nombre_temporal, $ruta_destino, $prefijo_log);
                
                if ($resultado_subida) {
                    $ruta_db = 'assets/categorias/' . $nombre_archivo;
                    // Actualizar en la base de datos
                    $stmt = $pdo->prepare("UPDATE categorias SET $campo_db = ? WHERE id_categoria = ?");
                    if ($stmt->execute([$ruta_db, $id_categoria])) {
                        $mensaje = "Imagen " . ($tipo_imagen === 'fondo' ? "de fondo" : "") . " actualizada correctamente para la categoría.";
                        add_debug("Imagen actualizada en la base de datos", ['campo' => $campo_db, 'id' => $id_categoria, 'ruta' => $ruta_db]);
                    } else {
                        $errorInfo = $stmt->errorInfo();
                        add_debug("Error al actualizar la imagen en la base de datos", ['error' => $errorInfo]);
                        $mensaje = "Error al actualizar la imagen en la base de datos: " . $errorInfo[2];
                    }
                } else {
                    add_debug("Todos los métodos de subida fallaron", ['archivo' => $nombre_original]);
                    $mensaje = "Error al subir la imagen. Se intentaron varios métodos sin éxito. Por favor, revise el log para más detalles.";
                }
            }
        }
    } else if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
        // Error al subir el archivo (que no sea "no se seleccionó archivo")
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "El archivo excede el tamaño máximo permitido por PHP (2M)",
            UPLOAD_ERR_FORM_SIZE => "El archivo excede el tamaño máximo permitido por el formulario",
            UPLOAD_ERR_PARTIAL => "El archivo fue cargado parcialmente",
            UPLOAD_ERR_NO_TMP_DIR => "No se encuentra la carpeta temporal",
            UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en el disco",
            UPLOAD_ERR_EXTENSION => "Una extensión de PHP detuvo la carga del archivo"
        ];
        
        $error_code = $_FILES[$input_name]['error'];
        $mensaje = "Error al subir la imagen" . ($tipo_imagen === 'fondo' ? " de fondo" : "") . ": " . ($error_messages[$error_code] ?? "Error desconocido");
        add_debug("Error en la subida del archivo", [
            'error_code' => $error_code,
            'error_message' => $error_messages[$error_code] ?? "Error desconocido"
        ]);
    }
}

// Verificar que existan las columnas necesarias en la tabla categorias
$columnsToCheck = ['imagen', 'imagen_fondo'];
foreach ($columnsToCheck as $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE ?");
        $stmt->execute([$column]);
        $columnExists = $stmt->rowCount() > 0;
        
        if (!$columnExists) {
            add_debug("La columna '$column' no existe, intentando crearla");
            
            // Determinar la posición de la nueva columna
            $afterColumn = ($column == 'imagen') ? 'descripcion' : 'imagen';
            
            $sql = "ALTER TABLE categorias ADD COLUMN $column VARCHAR(255) AFTER $afterColumn";
            $pdo->exec($sql);
            
            add_debug("Columna '$column' creada exitosamente");
        } else {
            add_debug("La columna '$column' ya existe en la tabla");
        }
    } catch (PDOException $e) {
        add_debug("Error al verificar/crear columna $column", ['error' => $e->getMessage()]);
        // Continuar de todos modos, pero alertar del error
    }
}

// Obtener todas las categorías con sus imágenes
$sql = "SELECT id_categoria, nombre, slug, descripcion, imagen, imagen_fondo FROM categorias ORDER BY nombre";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar configuración PHP actual
$php_config = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit')
];
add_debug("Configuración PHP actual", $php_config);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Imágenes de Categorías</title>
    <link rel="stylesheet" href="crud2.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .card-header h3 {
            margin: 0;
            font-size: 18px;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .imagen-categoria {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border: 1px solid #eee;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        
        .imagen-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px dashed #ddd;
        }
        
        .imagen-section:last-child {
            border-bottom: none;
        }
        
        .imagen-section h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        
        .imagen-form {
            padding: 15px 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0069d9;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #007bff;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .imagen-fondo-categoria {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border: 1px solid #eee;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        
        .aviso-importante {
            background-color: #ffffcc;
            border-left: 6px solid #ffeb3b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="adminControl.php" class="back-link">&larr; Volver al Panel de Control</a>
        
        <h1>Gestor de Imágenes de Categorías</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="message <?php echo strpos($mensaje, 'Error') === 0 ? 'error' : 'success'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <div class="aviso-importante">
            <h3>⚠️ Aviso importante sobre imágenes</h3>
            <p>Por limitaciones del servidor, se recomienda:</p>
            <ul>
                <li>Usar imágenes de menos de 2MB (límite de PHP)</li>
                <li>Evitar nombres con espacios o caracteres especiales</li>
                <li>Preferir formatos JPG o PNG</li>
                <li>Si tienes problemas, intenta usar la <a href="../corregir_subida.php" target="_blank">herramienta de diagnóstico</a></li>
            </ul>
        </div>
        
        <div class="card-grid">
            <?php foreach ($categorias as $categoria): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="imagen-section">
                            <h4>Icono de Categoría</h4>
                            <?php 
                            $imagen_url = !empty($categoria['imagen']) ? '../' . $categoria['imagen'] : '../assets/image-placeholder.png';
                            $imagen_existe = !empty($categoria['imagen']) && file_exists('../' . $categoria['imagen']);
                            ?>
                            
                            <img src="<?php echo htmlspecialchars($imagen_url); ?>" alt="<?php echo htmlspecialchars($categoria['nombre']); ?>" class="imagen-categoria" <?php if (!$imagen_existe) echo 'style="opacity:0.5;"'; ?>>
                            
                            <?php if (!$imagen_existe && !empty($categoria['imagen'])): ?>
                                <p style="color: red; margin-top: -10px;">¡La imagen no se encuentra en la ruta especificada!</p>
                            <?php endif; ?>
                            
                            <p><strong>Ruta de icono:</strong> <?php echo !empty($categoria['imagen']) ? htmlspecialchars($categoria['imagen']) : 'No asignada'; ?></p>
                            
                            <form action="categorias_imagenes.php" method="POST" enctype="multipart/form-data" class="imagen-form">
                                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
                                <input type="hidden" name="accion" value="actualizar">
                                
                                <div class="form-group">
                                    <label for="imagen_<?php echo $categoria['id_categoria']; ?>">Actualizar icono:</label>
                                    <input type="file" name="imagen" id="imagen_<?php echo $categoria['id_categoria']; ?>" accept="image/*">
                                </div>
                                
                                <button type="submit" class="btn">Guardar Icono</button>
                            </form>
                        </div>
                        
                        <div class="imagen-section">
                            <h4>Imagen de Fondo</h4>
                            <?php 
                            $imagen_fondo_url = !empty($categoria['imagen_fondo']) ? '../' . $categoria['imagen_fondo'] : '../assets/image-placeholder.png';
                            $imagen_fondo_existe = !empty($categoria['imagen_fondo']) && file_exists('../' . $categoria['imagen_fondo']);
                            ?>
                            
                            <img src="<?php echo htmlspecialchars($imagen_fondo_url); ?>" alt="Fondo de <?php echo htmlspecialchars($categoria['nombre']); ?>" class="imagen-fondo-categoria" <?php if (!$imagen_fondo_existe) echo 'style="opacity:0.5;"'; ?>>
                            
                            <?php if (!$imagen_fondo_existe && !empty($categoria['imagen_fondo'])): ?>
                                <p style="color: red; margin-top: -10px;">¡La imagen de fondo no se encuentra en la ruta especificada!</p>
                            <?php endif; ?>
                            
                            <p><strong>Ruta de fondo:</strong> <?php echo !empty($categoria['imagen_fondo']) ? htmlspecialchars($categoria['imagen_fondo']) : 'No asignada'; ?></p>
                            
                            <form action="categorias_imagenes.php" method="POST" enctype="multipart/form-data" class="imagen-form">
                                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
                                <input type="hidden" name="accion" value="actualizar_fondo">
                                
                                <div class="form-group">
                                    <label for="imagen_fondo_<?php echo $categoria['id_categoria']; ?>">Actualizar imagen de fondo:</label>
                                    <input type="file" name="imagen_fondo" id="imagen_fondo_<?php echo $categoria['id_categoria']; ?>" accept="image/*">
                                </div>
                                
                                <button type="submit" class="btn">Guardar Imagen de Fondo</button>
                            </form>
                        </div>
                        
                        <p><strong>Slug:</strong> <?php echo htmlspecialchars($categoria['slug']); ?></p>
                        
                        <?php if (!empty($categoria['descripcion'])): ?>
                            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($categoria['descripcion']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($debug_info) && isset($_GET['debug'])): ?>
        <div class="debug-section">
            <h2>Información de depuración</h2>
            <pre><?php echo json_encode($debug_info, JSON_PRETTY_PRINT); ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 