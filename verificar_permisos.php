<?php
// verificar_permisos.php - Script para verificar y corregir permisos de directorios

// Activar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cabecera para evitar problemas de caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");

// Función para crear log
function log_message($message) {
    echo $message . "<br>\n";
    error_log($message);
    
    // Agregar log a un archivo específico
    $log_file = __DIR__ . '/verificar_permisos.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación de Permisos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        .info { background-color: #e7f3fe; border-left: 6px solid #2196F3; padding: 10px; margin: 10px 0; }
        .success { background-color: #ddffdd; border-left: 6px solid #4CAF50; padding: 10px; margin: 10px 0; }
        .warning { background-color: #ffffcc; border-left: 6px solid #ffeb3b; padding: 10px; margin: 10px 0; }
        .error { background-color: #ffdddd; border-left: 6px solid #f44336; padding: 10px; margin: 10px 0; }
        code { background-color: #f8f8f8; padding: 2px 5px; border-radius: 3px; font-family: Consolas, monospace; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Verificación de Permisos</h1>
        <div class='info'>
            <strong>Información del Sistema</strong><br>
            PHP Version: <?php echo PHP_VERSION; ?><br>
            Server Software: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'; ?><br>
            OS: <?php echo PHP_OS; ?><br>
        </div>

<?php
// Verificar configuración PHP para carga de archivos
echo "<h2>Configuración PHP</h2>";
echo "<div class='info'>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "max_input_time: " . ini_get('max_input_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "</div>";

// Lista de directorios a verificar
$directorios = [
    'assets' => __DIR__ . '/assets',
    'assets/categorias' => __DIR__ . '/assets/categorias',
    'admin' => __DIR__ . '/admin',
    'temp' => __DIR__ . '/temp'
];

// Crear la tabla de directorios
echo "<h2>Verificación de Directorios</h2>";
echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background-color: #f2f2f2;'>
        <th style='padding: 8px; text-align: left;'>Directorio</th>
        <th style='padding: 8px; text-align: left;'>Existe</th>
        <th style='padding: 8px; text-align: left;'>Permisos</th>
        <th style='padding: 8px; text-align: left;'>Es Escribible</th>
        <th style='padding: 8px; text-align: left;'>Acción</th>
      </tr>";

foreach ($directorios as $nombre => $ruta) {
    $existe = file_exists($ruta);
    $permisos = $existe ? substr(sprintf('%o', fileperms($ruta)), -4) : 'N/A';
    $escribible = $existe ? (is_writable($ruta) ? 'Sí' : 'No') : 'N/A';
    
    echo "<tr>";
    echo "<td style='padding: 8px;'><code>$nombre</code></td>";
    echo "<td style='padding: 8px;'>" . ($existe ? "✅" : "❌") . "</td>";
    echo "<td style='padding: 8px;'>" . $permisos . "</td>";
    echo "<td style='padding: 8px;'>" . $escribible . "</td>";
    
    echo "<td style='padding: 8px;'>";
    if (!$existe) {
        // Intentar crear directorio
        if (mkdir($ruta, 0777, true)) {
            chmod($ruta, 0777); // Establecer permisos explícitamente
            log_message("Directorio '$nombre' creado con éxito.");
            echo "<span style='color: green;'>Directorio creado</span>";
        } else {
            log_message("Error al crear directorio '$nombre'. " . error_get_last()['message']);
            echo "<span style='color: red;'>Error al crear</span>";
        }
    } elseif (!is_writable($ruta)) {
        // Intentar cambiar permisos
        if (chmod($ruta, 0777)) {
            log_message("Permisos del directorio '$nombre' corregidos.");
            echo "<span style='color: green;'>Permisos corregidos</span>";
        } else {
            log_message("Error al cambiar permisos del directorio '$nombre'. " . error_get_last()['message']);
            echo "<span style='color: red;'>Error al corregir permisos</span>";
        }
    } else {
        echo "<span style='color: blue;'>OK</span>";
    }
    echo "</td>";
    
    echo "</tr>";
}
echo "</table>";

// Verificar si existe la columna imagen_fondo en la tabla categorias
echo "<h2>Verificación de Base de Datos</h2>";
try {
    require_once __DIR__ . '/config/db.php';
    
    echo "<div class='info'>Conexión a base de datos: <strong>Correcta</strong></div>";
    
    // Verificar si la columna imagen existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen'");
    $stmt->execute();
    $imagenExiste = $stmt->rowCount() > 0;
    
    // Verificar si la columna imagen_fondo existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen_fondo'");
    $stmt->execute();
    $imagenFondoExiste = $stmt->rowCount() > 0;
    
    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #f2f2f2;'>
            <th style='padding: 8px; text-align: left;'>Columna</th>
            <th style='padding: 8px; text-align: left;'>Existe</th>
            <th style='padding: 8px; text-align: left;'>Acción</th>
          </tr>";
    
    // Fila para columna imagen
    echo "<tr>";
    echo "<td style='padding: 8px;'><code>categorias.imagen</code></td>";
    echo "<td style='padding: 8px;'>" . ($imagenExiste ? "✅" : "❌") . "</td>";
    echo "<td style='padding: 8px;'>";
    
    if (!$imagenExiste) {
        try {
            $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen VARCHAR(255) COMMENT 'Ruta a la imagen de la categoría' AFTER descripcion");
            log_message("Columna 'imagen' añadida correctamente a la tabla 'categorias'");
            echo "<span style='color: green;'>Columna creada</span>";
        } catch (PDOException $e) {
            log_message("Error al crear columna 'imagen': " . $e->getMessage());
            echo "<span style='color: red;'>Error al crear: " . $e->getMessage() . "</span>";
        }
    } else {
        echo "<span style='color: blue;'>OK</span>";
    }
    
    echo "</td></tr>";
    
    // Fila para columna imagen_fondo
    echo "<tr>";
    echo "<td style='padding: 8px;'><code>categorias.imagen_fondo</code></td>";
    echo "<td style='padding: 8px;'>" . ($imagenFondoExiste ? "✅" : "❌") . "</td>";
    echo "<td style='padding: 8px;'>";
    
    if (!$imagenFondoExiste) {
        try {
            $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen_fondo VARCHAR(255) COMMENT 'Ruta a la imagen de fondo de la categoría' AFTER imagen");
            log_message("Columna 'imagen_fondo' añadida correctamente a la tabla 'categorias'");
            echo "<span style='color: green;'>Columna creada</span>";
        } catch (PDOException $e) {
            log_message("Error al crear columna 'imagen_fondo': " . $e->getMessage());
            echo "<span style='color: red;'>Error al crear: " . $e->getMessage() . "</span>";
        }
    } else {
        echo "<span style='color: blue;'>OK</span>";
    }
    
    echo "</td></tr>";
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='error'>Error de base de datos: " . $e->getMessage() . "</div>";
    log_message("Error de base de datos: " . $e->getMessage());
}

// Instrucciones para el siguiente paso
echo "<h2>Instrucciones</h2>";
echo "<div class='info'>
    <p>Si todos los directorios muestran 'OK', el problema de permisos ha sido resuelto. Prueba nuevamente la actualización de categorías.</p>
    <p>Si continúa el error, verifica los logs en la carpeta admin para más detalles sobre el error específico.</p>
</div>";

echo "<p><a href='admin/categorias_imagenes.php' style='display: inline-block; background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Volver a Gestión de Categorías</a></p>";
?>
    </div>
</body>
</html> 