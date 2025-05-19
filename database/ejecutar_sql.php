<?php
// Script para ejecutar SQL en la base de datos

// Incluir la configuración de la base de datos
require_once '../config/db.php';

// Verificar que el usuario está autorizado (esto es opcional pero recomendado)
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Acceso denegado. Debe ser administrador para ejecutar este script.');
}

echo "<h1>Actualización de la tabla categorías</h1>";

try {
    // Verificar si la columna 'imagen' existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen'");
    $stmt->execute();
    $columnaImagenExiste = $stmt->rowCount() > 0;
    
    if (!$columnaImagenExiste) {
        echo "<p>La columna 'imagen' no existe. Añadiendo...</p>";
        $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen VARCHAR(255) AFTER descripcion");
        echo "<p>✅ Columna 'imagen' añadida correctamente.</p>";
    } else {
        echo "<p>✅ La columna 'imagen' ya existe.</p>";
    }
    
    // Verificar si la columna 'imagen_fondo' existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen_fondo'");
    $stmt->execute();
    $columnaImagenFondoExiste = $stmt->rowCount() > 0;
    
    if (!$columnaImagenFondoExiste) {
        echo "<p>La columna 'imagen_fondo' no existe. Añadiendo...</p>";
        $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen_fondo VARCHAR(255) AFTER imagen");
        echo "<p>✅ Columna 'imagen_fondo' añadida correctamente.</p>";
    } else {
        echo "<p>✅ La columna 'imagen_fondo' ya existe.</p>";
    }
    
    // Mostrar estructura actual de la tabla
    echo "<h2>Estructura actual de la tabla 'categorias':</h2>";
    $stmt = $pdo->query("DESCRIBE categorias");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
    
    foreach ($columnas as $columna) {
        echo "<tr>";
        echo "<td>{$columna['Field']}</td>";
        echo "<td>{$columna['Type']}</td>";
        echo "<td>{$columna['Null']}</td>";
        echo "<td>{$columna['Key']}</td>";
        echo "<td>{$columna['Default']}</td>";
        echo "<td>{$columna['Extra']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Verificar contenido de la tabla
    echo "<h2>Contenido actual de la tabla 'categorias':</h2>";
    $stmt = $pdo->query("SELECT id_categoria, nombre, slug, LEFT(descripcion, 50) as descripcion_corta, imagen, imagen_fondo FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($categorias) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        foreach (array_keys($categorias[0]) as $columna) {
            echo "<th>$columna</th>";
        }
        echo "</tr>";
        
        foreach ($categorias as $categoria) {
            echo "<tr>";
            foreach ($categoria as $valor) {
                echo "<td>" . htmlspecialchars($valor ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No hay categorías en la tabla.</p>";
    }
    
    echo "<h2>✅ Actualización completada correctamente</h2>";
    echo "<p><a href='../admin/adminControl.php'>Volver al Panel de Administración</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Error en la base de datos</h2>";
    echo "<p>Mensaje de error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='../admin/adminControl.php'>Volver al Panel de Administración</a></p>";
}
?> 