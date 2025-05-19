<?php
/**
 * Herramienta de diagnóstico para el panel de administración
 * Verifica la estructura de la base de datos, permisos de archivos y configuración PHP
 */

// Incluir archivos necesarios
require_once '../config/db.php';
require_once 'upload_config.php';

// Verificar que el usuario está autorizado
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Acceso denegado. Debe ser administrador para ejecutar este script.');
}

// Función para imprimir un elemento en formato legible
function print_formatted($label, $value, $type = 'info') {
    $class = 'info';
    if ($type == 'error') $class = 'error';
    if ($type == 'success') $class = 'success';
    if ($type == 'warning') $class = 'warning';
    
    echo "<div class='result {$class}'><strong>{$label}:</strong> ";
    
    if (is_array($value) || is_object($value)) {
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    } else {
        echo $value;
    }
    
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Panel de Administración</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .result {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
        }
        .success {
            background-color: #e8f5e9;
            border-left: 4px solid #4CAF50;
        }
        .warning {
            background-color: #fff8e1;
            border-left: 4px solid #FFC107;
        }
        .error {
            background-color: #ffebee;
            border-left: 4px solid #F44336;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #1a252f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Diagnóstico del Panel de Administración</h1>
    <a href="adminControl.php" class="back-link">← Volver al Panel de Control</a>
    
    <div class="section">
        <h2>1. Información de PHP</h2>
        <?php
        print_formatted("Versión de PHP", phpversion());
        print_formatted("Sistema operativo", PHP_OS);
        print_formatted("Máximo tamaño de subida", ini_get('upload_max_filesize'));
        print_formatted("Máximo tamaño de POST", ini_get('post_max_size'));
        print_formatted("Extensiones cargadas", implode(', ', get_loaded_extensions()));
        
        // Verificar extensión GD (necesaria para manipular imágenes)
        if (extension_loaded('gd')) {
            print_formatted("Extensión GD", "Disponible", "success");
            // Verificar soporte de formatos
            $gd_info = gd_info();
            print_formatted("Información GD", $gd_info);
        } else {
            print_formatted("Extensión GD", "No disponible - Necesaria para la manipulación de imágenes", "error");
        }
        ?>
    </div>
    
    <div class="section">
        <h2>2. Permisos de Directorios</h2>
        <?php
        $directorios = [
            ASSETS_DIR,
            CATEGORIAS_DIR,
            AVATARS_DIR,
            POSTS_DIR
        ];
        
        $resultado_dirs = verificar_directorios($directorios);
        
        foreach ($directorios as $dir) {
            $exists = file_exists($dir);
            $writable = is_writable($dir);
            $status = $exists && $writable ? "success" : ($exists ? "warning" : "error");
            $message = $exists 
                ? ($writable ? "Existe y es escribible" : "Existe pero NO es escribible") 
                : "No existe";
            
            print_formatted($dir, $message, $status);
            
            if ($exists) {
                print_formatted("Permisos", substr(sprintf('%o', fileperms($dir)), -4));
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Estructura de la Base de Datos</h2>
        <?php
        try {
            // Verificar la tabla categorias
            $stmt = $pdo->query("SHOW TABLES LIKE 'categorias'");
            if ($stmt->rowCount() > 0) {
                print_formatted("Tabla 'categorias'", "Existe", "success");
                
                // Verificar columnas
                $stmt = $pdo->query("DESCRIBE categorias");
                $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<table>";
                echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
                
                $tiene_imagen = false;
                $tiene_imagen_fondo = false;
                
                foreach ($columnas as $columna) {
                    echo "<tr>";
                    echo "<td>{$columna['Field']}</td>";
                    echo "<td>{$columna['Type']}</td>";
                    echo "<td>{$columna['Null']}</td>";
                    echo "<td>{$columna['Key']}</td>";
                    echo "<td>{$columna['Default']}</td>";
                    echo "<td>{$columna['Extra']}</td>";
                    echo "</tr>";
                    
                    if ($columna['Field'] == 'imagen') $tiene_imagen = true;
                    if ($columna['Field'] == 'imagen_fondo') $tiene_imagen_fondo = true;
                }
                
                echo "</table>";
                
                // Verificar columnas específicas
                if (!$tiene_imagen) {
                    print_formatted("Columna 'imagen'", "No existe - Necesaria para subir imágenes", "error");
                } else {
                    print_formatted("Columna 'imagen'", "Existe", "success");
                }
                
                if (!$tiene_imagen_fondo) {
                    print_formatted("Columna 'imagen_fondo'", "No existe - Necesaria para subir imágenes de fondo", "error");
                } else {
                    print_formatted("Columna 'imagen_fondo'", "Existe", "success");
                }
                
                // Intentar corregir la estructura
                if (!$tiene_imagen || !$tiene_imagen_fondo) {
                    print_formatted("Acción", "Intentando corregir la estructura de la tabla...", "warning");
                    $resultado = verificar_columnas_categorias($pdo);
                    print_formatted("Resultado de la corrección", $resultado['mensajes']);
                }
                
                // Mostrar algunos registros
                $stmt = $pdo->query("SELECT id_categoria, nombre, LEFT(descripcion, 50) as descripcion, imagen, imagen_fondo FROM categorias LIMIT 5");
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($categorias) > 0) {
                    print_formatted("Primeros registros", "Se encontraron registros", "success");
                    
                    echo "<table>";
                    echo "<tr>";
                    foreach (array_keys($categorias[0]) as $key) {
                        echo "<th>$key</th>";
                    }
                    echo "</tr>";
                    
                    foreach ($categorias as $categoria) {
                        echo "<tr>";
                        foreach ($categoria as $key => $value) {
                            $value = $value ?? 'NULL';
                            if ($key == 'imagen' || $key == 'imagen_fondo') {
                                $file_exists = !empty($value) && file_exists("../{$value}");
                                $style = $file_exists ? "" : "color: red; font-weight: bold;";
                                $value = !empty($value) ? $value . ($file_exists ? " (archivo existe)" : " (archivo NO existe)") : "NULL";
                                echo "<td style=\"$style\">$value</td>";
                            } else {
                                echo "<td>$value</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                } else {
                    print_formatted("Registros", "No hay registros en la tabla", "warning");
                }
            } else {
                print_formatted("Tabla 'categorias'", "No existe", "error");
            }
        } catch (PDOException $e) {
            print_formatted("Error en la base de datos", $e->getMessage(), "error");
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Prueba de Subida de Archivos</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label for="test_file">Seleccionar imagen para probar:</label>
                <input type="file" name="test_file" id="test_file" accept="image/*">
            </div>
            <button type="submit" name="test_upload" style="padding: 8px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Probar Subida
            </button>
        </form>
        
        <?php
        if (isset($_POST['test_upload']) && isset($_FILES['test_file'])) {
            $file = $_FILES['test_file'];
            
            print_formatted("Archivo subido", [
                'name' => $file['name'],
                'type' => $file['type'],
                'size' => $file['size'],
                'tmp_name' => $file['tmp_name'],
                'error' => $file['error'] . ' - ' . getUploadErrorMessage($file['error'])
            ]);
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_extensions = array_keys($allowedExtensions);
                
                if (in_array($extension, $allowed_extensions)) {
                    // Generar nombre único
                    $nombre_sanitizado = sanitizar_nombre_archivo($file['name']);
                    $destino = ASSETS_DIR . 'test_' . $nombre_sanitizado;
                    
                    // Intentar mover el archivo
                    if (move_uploaded_file($file['tmp_name'], $destino)) {
                        print_formatted("Resultado", "Archivo subido correctamente a: " . $destino, "success");
                        
                        // Verificar permisos del archivo subido
                        $permisos = fileperms($destino);
                        print_formatted("Permisos del archivo", substr(sprintf('%o', $permisos), -4));
                        
                        // Mostrar la imagen
                        echo '<div style="margin-top: 15px;">';
                        echo '<strong>Vista previa:</strong><br>';
                        echo '<img src="../assets/test_' . $nombre_sanitizado . '" style="max-width: 300px; max-height: 300px; margin-top: 10px; border: 1px solid #ddd;">';
                        echo '</div>';
                    } else {
                        print_formatted("Resultado", "Error al mover el archivo a su destino final", "error");
                    }
                } else {
                    print_formatted("Resultado", "Tipo de archivo no permitido. Extensiones permitidas: " . implode(', ', $allowed_extensions), "error");
                }
            } else {
                print_formatted("Resultado", "Error al subir el archivo: " . getUploadErrorMessage($file['error']), "error");
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Logs de Error</h2>
        <?php
        $log_files = [
            'PHP Error Log' => __DIR__ . '/php_errors.log',
            'Editar Categoría Debug' => __DIR__ . '/editar_categoria_debug.log',
            'Upload Helper Log' => __DIR__ . '/upload_helper.log',
            'Imágenes Debug' => __DIR__ . '/imagenes_debug.log',
            'Insertar Categoría Debug' => __DIR__ . '/insertar_categoria_debug.log'
        ];
        
        foreach ($log_files as $name => $file) {
            if (file_exists($file)) {
                $size = filesize($file);
                $readable = is_readable($file);
                
                if ($readable && $size > 0) {
                    $log_content = file_get_contents($file);
                    // Mostrar solo las últimas 20 líneas
                    $lines = explode("\n", $log_content);
                    $last_lines = array_slice($lines, -20);
                    
                    print_formatted("$name (últimas 20 líneas)", "", "info");
                    echo "<pre style='max-height: 200px; overflow-y: auto;'>";
                    echo htmlspecialchars(implode("\n", $last_lines));
                    echo "</pre>";
                } else if ($readable) {
                    print_formatted($name, "El archivo existe pero está vacío", "warning");
                } else {
                    print_formatted($name, "El archivo existe pero no se puede leer", "error");
                }
            } else {
                print_formatted($name, "El archivo no existe", "warning");
            }
        }
        ?>
    </div>
    
    <a href="adminControl.php" class="back-link">← Volver al Panel de Control</a>
</body>
</html> 