<?php
// Script para actualizar la tabla categorias y añadir la columna imagen_fondo
require_once __DIR__ . '/../config/db.php';

// Función para registrar mensajes
function log_message($message) {
    echo $message . "\n";
    error_log($message);
}

// Mostrar mensaje de inicio
log_message("Iniciando verificación de la columna imagen_fondo en la tabla categorias...");

try {
    // Verificar si la columna existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen_fondo'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        log_message("La columna 'imagen_fondo' no existe en la tabla categorias. Creándola...");
        
        // Añadir la columna
        $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen_fondo VARCHAR(255) COMMENT 'Ruta a la imagen de fondo de la categoría' AFTER imagen");
        
        log_message("Columna 'imagen_fondo' añadida correctamente a la tabla 'categorias'");
        
        // Si hay categorías con imágenes, establecer las mismas como imágenes de fondo por defecto
        $stmtUpdate = $pdo->prepare("UPDATE categorias SET imagen_fondo = imagen WHERE imagen IS NOT NULL AND imagen != ''");
        $result = $stmtUpdate->execute();
        if ($result) {
            $affected = $stmtUpdate->rowCount();
            log_message("Se establecieron temporalmente las mismas imágenes como fondo para {$affected} categorías.");
        }
    } else {
        log_message("La columna 'imagen_fondo' ya existe en la tabla categorias.");
    }
    
    log_message("Proceso completado con éxito");
    
} catch (PDOException $e) {
    log_message("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    log_message("Error inesperado: " . $e->getMessage());
}
?> 