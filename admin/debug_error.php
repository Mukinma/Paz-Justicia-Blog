<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Información sobre la versión de PHP
echo "<h2>Información de PHP</h2>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";

// Información sobre la conexión a la base de datos
echo "<h2>Prueba de conexión a la base de datos</h2>";
try {
    require_once '../config/db.php';
    echo "<p style='color:green'>Conexión a la base de datos establecida correctamente</p>";
    
    // Verificar la tabla categorías
    $stmt = $pdo->query("SHOW TABLES LIKE 'categorias'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>La tabla 'categorias' existe</p>";
        
        // Verificar estructura de la tabla
        $stmt = $pdo->query("DESCRIBE categorias");
        echo "<h3>Estructura de la tabla 'categorias'</h3>";
        echo "<pre>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "<p style='color:red'>La tabla 'categorias' no existe</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Error de conexión a la base de datos: " . $e->getMessage() . "</p>";
}

// Verificar la función generarSlug en utils.php
echo "<h2>Verificación de la función generarSlug</h2>";
try {
    require_once 'utils.php';
    if (function_exists('generarSlug')) {
        echo "<p style='color:green'>La función generarSlug existe</p>";
        echo "<p>Resultado de generarSlug('Prueba Categoría'): " . generarSlug('Prueba Categoría') . "</p>";
    } else {
        echo "<p style='color:red'>La función generarSlug no está definida en utils.php</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error al cargar utils.php: " . $e->getMessage() . "</p>";
}

// Verificación de sesiones
echo "<h2>Verificación de sesiones</h2>";
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Información del servidor
echo "<h2>Información del servidor</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>"; 