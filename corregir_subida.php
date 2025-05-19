<?php
// corregir_subida.php - Script para diagnosticar y corregir problemas de subida de archivos
session_start();

// Activar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cabeceras para evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");

// Función para crear log
function log_message($message) {
    echo $message . "<br>\n";
    error_log($message);
    
    // Agregar log a un archivo específico
    $log_file = __DIR__ . '/corregir_subida.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Crear directorio temporal si no existe
$temp_dir = __DIR__ . '/temp';
if (!file_exists($temp_dir)) {
    if (!mkdir($temp_dir, 0777, true)) {
        log_message("ERROR: No se pudo crear el directorio temporal.");
    } else {
        log_message("Directorio temporal creado con éxito.");
    }
}

// Crear directorio de categorías si no existe
$assets_dir = __DIR__ . '/assets';
$categorias_dir = __DIR__ . '/assets/categorias';

if (!file_exists($assets_dir)) {
    if (!mkdir($assets_dir, 0777, true)) {
        log_message("ERROR: No se pudo crear el directorio assets.");
    } else {
        log_message("Directorio assets creado con éxito.");
    }
}

if (!file_exists($categorias_dir)) {
    if (!mkdir($categorias_dir, 0777, true)) {
        log_message("ERROR: No se pudo crear el directorio categorias.");
    } else {
        log_message("Directorio categorias creado con éxito.");
    }
}

// En Windows, no podemos usar chmod directamente, pero podemos verificar permisos
function check_dir_writable($dir) {
    $test_file = $dir . '/' . uniqid() . '.test';
    $result = @file_put_contents($test_file, 'test');
    if ($result === false) {
        return false;
    }
    @unlink($test_file);
    return true;
}

$temp_writable = check_dir_writable($temp_dir);
$assets_writable = check_dir_writable($assets_dir);
$categorias_writable = check_dir_writable($categorias_dir);

// Procesar una subida de prueba
$mensaje = '';
$upload_success = false;
$upload_path = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['test_image']) && $_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
        $nombre_temporal = $_FILES['test_image']['tmp_name'];
        $nombre_original = $_FILES['test_image']['name'];
        
        // Sanitizar el nombre del archivo (quitar espacios y caracteres especiales)
        $nombre_seguro = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nombre_original);
        
        // Usar rutas absolutas para mayor seguridad
        $ruta_destino = $categorias_dir . '/' . $nombre_seguro;
        
        $method = $_POST['upload_method'] ?? 'move_uploaded_file';
        
        try {
            if ($method === 'move_uploaded_file') {
                // Método 1: move_uploaded_file (método estándar de PHP)
                if (move_uploaded_file($nombre_temporal, $ruta_destino)) {
                    $mensaje = "Subida exitosa usando move_uploaded_file.";
                    $upload_success = true;
                    $upload_path = 'assets/categorias/' . $nombre_seguro;
                } else {
                    $mensaje = "Error al subir usando move_uploaded_file: " . print_r(error_get_last(), true);
                }
            } else if ($method === 'copy') {
                // Método 2: copy (alternativa para algunos servidores)
                if (copy($nombre_temporal, $ruta_destino)) {
                    $mensaje = "Subida exitosa usando copy.";
                    $upload_success = true;
                    $upload_path = 'assets/categorias/' . $nombre_seguro;
                } else {
                    $mensaje = "Error al subir usando copy: " . print_r(error_get_last(), true);
                }
            } else if ($method === 'rename') {
                // Método 3: rename (otra alternativa para Windows)
                if (rename($nombre_temporal, $ruta_destino)) {
                    $mensaje = "Subida exitosa usando rename.";
                    $upload_success = true;
                    $upload_path = 'assets/categorias/' . $nombre_seguro;
                } else {
                    $mensaje = "Error al subir usando rename: " . print_r(error_get_last(), true);
                }
            } else if ($method === 'file_contents') {
                // Método 4: file_get_contents/file_put_contents (última alternativa)
                $contenido = file_get_contents($nombre_temporal);
                if ($contenido !== false && file_put_contents($ruta_destino, $contenido) !== false) {
                    $mensaje = "Subida exitosa usando file_get_contents/file_put_contents.";
                    $upload_success = true;
                    $upload_path = 'assets/categorias/' . $nombre_seguro;
                } else {
                    $mensaje = "Error al subir usando file_get_contents/file_put_contents: " . print_r(error_get_last(), true);
                }
            }
            
            // Si tuvimos éxito, intentar actualizar la base de datos para la categoría 10
            if ($upload_success && isset($_POST['update_db']) && $_POST['update_db'] === 'yes') {
                require_once __DIR__ . '/config/db.php';
                
                // Primero verificar si la columna existe
                $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen_fondo'");
                $stmt->execute();
                $columnaExiste = $stmt->rowCount() > 0;
                
                if (!$columnaExiste) {
                    $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen_fondo VARCHAR(255) AFTER imagen");
                    $mensaje .= " Se creó la columna imagen_fondo.";
                }
                
                // Actualizar la categoría 10 con la nueva imagen
                $stmt = $pdo->prepare("UPDATE categorias SET imagen_fondo = ? WHERE id_categoria = 10");
                if ($stmt->execute([$upload_path])) {
                    $mensaje .= " Base de datos actualizada para la categoría 10.";
                } else {
                    $mensaje .= " Error al actualizar la base de datos: " . print_r($stmt->errorInfo(), true);
                }
            }
        } catch (Exception $e) {
            $mensaje = "Excepción: " . $e->getMessage();
            log_message($mensaje);
        }
    } else if (isset($_FILES['test_image'])) {
        $error_code = $_FILES['test_image']['error'];
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "El archivo excede el tamaño máximo permitido por PHP (upload_max_filesize)",
            UPLOAD_ERR_FORM_SIZE => "El archivo excede el tamaño máximo permitido por el formulario",
            UPLOAD_ERR_PARTIAL => "El archivo fue cargado parcialmente",
            UPLOAD_ERR_NO_FILE => "No se seleccionó ningún archivo",
            UPLOAD_ERR_NO_TMP_DIR => "Falta la carpeta temporal",
            UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en el disco",
            UPLOAD_ERR_EXTENSION => "La carga del archivo fue detenida por una extensión de PHP"
        ];
        $mensaje = "Error en la subida: " . ($error_messages[$error_code] ?? "Error desconocido ($error_code)");
    }
}

// Intentar cargar configuración de PHP actual
$php_config = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit'),
    'file_uploads' => ini_get('file_uploads'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir')
];

// Obtener información del sistema
$system_info = [
    'OS' => PHP_OS,
    'PHP Version' => PHP_VERSION,
    'Server API' => php_sapi_name(),
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Desconocido',
    'Temp Directory' => sys_get_temp_dir()
];

// Generar sugerencias basadas en diagnóstico
$sugerencias = [];
if (!$temp_writable) {
    $sugerencias[] = "El directorio temporal no es escribible. Esto puede causar problemas al subir archivos.";
}
if (!$categorias_writable) {
    $sugerencias[] = "El directorio categorias no es escribible. Las imágenes no podrán guardarse.";
}
if (strpos(PHP_OS, 'WIN') !== false) {
    $sugerencias[] = "Estás usando Windows. Las funciones de permisos como chmod() pueden no funcionar como se espera.";
}
if (isset($php_config['upload_max_filesize']) && $php_config['upload_max_filesize'] < '10M') {
    $sugerencias[] = "El límite de subida de archivos (upload_max_filesize) es bajo. Considera aumentarlo en php.ini.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Subida de Archivos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1, h2 { color: #333; }
        .info { background-color: #e7f3fe; border-left: 6px solid #2196F3; padding: 10px; margin: 10px 0; }
        .success { background-color: #ddffdd; border-left: 6px solid #4CAF50; padding: 10px; margin: 10px 0; }
        .warning { background-color: #ffffcc; border-left: 6px solid #ffeb3b; padding: 10px; margin: 10px 0; }
        .error { background-color: #ffdddd; border-left: 6px solid #f44336; padding: 10px; margin: 10px 0; }
        code { background-color: #f8f8f8; padding: 2px 5px; border-radius: 3px; font-family: Consolas, monospace; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 8px; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tabs { display: flex; margin-bottom: 10px; }
        .tab { padding: 10px 15px; background-color: #f2f2f2; cursor: pointer; border: 1px solid #ddd; border-radius: 4px 4px 0 0; margin-right: 5px; }
        .tab.active { background-color: white; border-bottom: 1px solid white; }
        input[type="file"], input[type="submit"], select { margin: 10px 0; }
        input[type="submit"], .btn { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        input[type="submit"]:hover, .btn:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico de Subida de Archivos</h1>
        
        <div class="tabs">
            <div class="tab active" onclick="showTab('tab1')">Diagnóstico</div>
            <div class="tab" onclick="showTab('tab2')">Prueba de Subida</div>
            <div class="tab" onclick="showTab('tab3')">Soluciones</div>
        </div>
        
        <div id="tab1" class="tab-content active">
            <h2>Estado del Sistema</h2>
            
            <div class="info">
                <h3>Información del Sistema</h3>
                <table>
                    <?php foreach ($system_info as $key => $value): ?>
                    <tr>
                        <th><?php echo htmlspecialchars($key); ?></th>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="info">
                <h3>Configuración PHP</h3>
                <table>
                    <?php foreach ($php_config as $key => $value): ?>
                    <tr>
                        <th><?php echo htmlspecialchars($key); ?></th>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="info">
                <h3>Estado de Directorios</h3>
                <table>
                    <tr>
                        <th>Directorio</th>
                        <th>Ruta</th>
                        <th>Existe</th>
                        <th>Escribible</th>
                    </tr>
                    <tr>
                        <td>Temporal</td>
                        <td><?php echo htmlspecialchars($temp_dir); ?></td>
                        <td><?php echo file_exists($temp_dir) ? '✅' : '❌'; ?></td>
                        <td><?php echo $temp_writable ? '✅' : '❌'; ?></td>
                    </tr>
                    <tr>
                        <td>Assets</td>
                        <td><?php echo htmlspecialchars($assets_dir); ?></td>
                        <td><?php echo file_exists($assets_dir) ? '✅' : '❌'; ?></td>
                        <td><?php echo $assets_writable ? '✅' : '❌'; ?></td>
                    </tr>
                    <tr>
                        <td>Categorías</td>
                        <td><?php echo htmlspecialchars($categorias_dir); ?></td>
                        <td><?php echo file_exists($categorias_dir) ? '✅' : '❌'; ?></td>
                        <td><?php echo $categorias_writable ? '✅' : '❌'; ?></td>
                    </tr>
                </table>
            </div>
            
            <?php if (!empty($sugerencias)): ?>
            <div class="warning">
                <h3>Sugerencias</h3>
                <ul>
                    <?php foreach ($sugerencias as $sugerencia): ?>
                    <li><?php echo htmlspecialchars($sugerencia); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <div id="tab2" class="tab-content">
            <h2>Prueba de Subida de Archivos</h2>
            
            <?php if (!empty($mensaje)): ?>
            <div class="<?php echo strpos($mensaje, 'exitosa') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($upload_success): ?>
            <div class="success">
                <h3>Archivo subido correctamente</h3>
                <p>Ruta: <?php echo htmlspecialchars($upload_path); ?></p>
                <img src="<?php echo htmlspecialchars($upload_path); ?>" alt="Imagen subida" style="max-width: 300px; max-height: 200px;">
            </div>
            <?php endif; ?>
            
            <form action="corregir_subida.php" method="post" enctype="multipart/form-data">
                <h3>Selecciona una imagen para probar la subida</h3>
                <p>Recomendación: elige una imagen pequeña (menos de 1MB) y sin espacios ni caracteres especiales en el nombre.</p>
                <input type="file" name="test_image" accept="image/*" required>
                
                <h3>Método de subida</h3>
                <select name="upload_method">
                    <option value="move_uploaded_file">move_uploaded_file (método estándar)</option>
                    <option value="copy">copy (alternativa 1)</option>
                    <option value="rename">rename (alternativa 2)</option>
                    <option value="file_contents">file_get_contents/file_put_contents (alternativa 3)</option>
                </select>
                
                <h3>Actualizar base de datos</h3>
                <label>
                    <input type="checkbox" name="update_db" value="yes">
                    Actualizar imagen_fondo para la categoría "Paz y Conflictos" (ID: 10)
                </label>
                
                <div>
                    <input type="submit" value="Subir Imagen">
                </div>
            </form>
        </div>
        
        <div id="tab3" class="tab-content">
            <h2>Soluciones Recomendadas</h2>
            
            <div class="info">
                <h3>1. Editar php.ini</h3>
                <p>Si tienes acceso a php.ini, considera estas configuraciones:</p>
                <pre>
upload_max_filesize = 20M
post_max_size = 22M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
file_uploads = On
</pre>
            </div>
            
            <div class="info">
                <h3>2. Crear un archivo .htaccess</h3>
                <p>Si usas Apache, puedes crear un archivo .htaccess en la raíz de tu sitio con:</p>
                <pre>
php_value upload_max_filesize 20M
php_value post_max_size 22M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
</pre>
            </div>
            
            <div class="info">
                <h3>3. Cambiar el método de subida en editar_categoria.php</h3>
                <p>Si identificaste un método de subida que funciona en la pestaña "Prueba de Subida", puedes modificar el archivo para usar ese método.</p>
                <p>Por ejemplo, si <code>copy()</code> funciona pero <code>move_uploaded_file()</code> no, edita editar_categoria.php para usar copy.</p>
            </div>
            
            <div class="info">
                <h3>4. Evitar nombres de archivo con espacios</h3>
                <p>Asegúrate de que tus archivos no tengan espacios o caracteres especiales en sus nombres.</p>
                <p>Puedes renombrar "paz y conflictos2.jpg" a "paz_y_conflictos2.jpg" antes de subirlo.</p>
            </div>
            
            <div class="info">
                <h3>5. Verificar la carpeta temp de PHP</h3>
                <p>Si el problema persiste, verifica que la carpeta temporal de PHP existe y tiene permisos adecuados.</p>
                <p>Ubicación actual: <?php echo htmlspecialchars(sys_get_temp_dir()); ?></p>
            </div>
            
            <a href="admin/categorias_imagenes.php" class="btn" style="display: inline-block; margin-top: 20px;">Volver a Gestión de Categorías</a>
        </div>
    </div>
    
    <script>
        function showTab(tabId) {
            // Ocultar todos los contenidos de pestañas
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.remove('active');
            });
            
            // Desactivar todos los botones de pestaña
            document.querySelectorAll('.tab').forEach(function(tab) {
                tab.classList.remove('active');
            });
            
            // Mostrar el contenido seleccionado
            document.getElementById(tabId).classList.add('active');
            
            // Activar la pestaña seleccionada
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html> 