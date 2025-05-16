<?php
// Configuración de errores - solo para este script
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Solo permitir acceso a administradores
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Acceso denegado";
    exit;
}

// Rutas posibles de logs de error
$posibles_logs = [
    'C:/AppServ/www/PIP/php_error.log',
    'C:/AppServ/www/PIP/error.log',
    'C:/AppServ/Apache24/logs/error.log',
    'C:/AppServ/Apache24/logs/php_error.log',
    'C:/AppServ/php/logs/php_error.log',
    'C:/Windows/Temp/php_errors.log',
    ini_get('error_log')
];

echo "<h1>Buscador de logs de errores PHP</h1>";

foreach ($posibles_logs as $log_path) {
    echo "<h3>Verificando: {$log_path}</h3>";
    
    if (file_exists($log_path)) {
        echo "<p style='color:green'>Archivo encontrado</p>";
        
        // Mostrar las últimas 50 líneas
        $contenido = file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($contenido) {
            $lineas = array_slice($contenido, -50);
            echo "<pre style='background-color:#f8f8f8;padding:10px;border:1px solid #ddd;max-height:400px;overflow:auto'>";
            foreach ($lineas as $linea) {
                // Resaltar líneas con editar_categoria.php
                if (strpos($linea, 'editar_categoria.php') !== false) {
                    echo "<span style='color:red;font-weight:bold'>{$linea}</span>\n";
                } else {
                    echo htmlspecialchars($linea) . "\n";
                }
            }
            echo "</pre>";
        } else {
            echo "<p>Archivo vacío o no se puede leer</p>";
        }
    } else {
        echo "<p style='color:red'>Archivo no encontrado</p>";
    }
}

// Verificar configuración actual de PHP para logs
echo "<h2>Configuración actual de PHP para logs</h2>";
echo "<ul>";
echo "<li>error_reporting: " . ini_get('error_reporting') . "</li>";
echo "<li>display_errors: " . ini_get('display_errors') . "</li>";
echo "<li>log_errors: " . ini_get('log_errors') . "</li>";
echo "<li>error_log: " . ini_get('error_log') . "</li>";
echo "</ul>"; 