<?php
// Script para actualizar la tabla categorias y añadir la columna imagen si no existe
require_once __DIR__ . '/../config/db.php';

// Función para registrar
function log_message($message) {
    echo $message . "\n";
    error_log($message);
    
    // Agregar log a un archivo específico para mejor seguimiento
    $log_file = __DIR__ . '/actualizar_categorias.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Mostrar mensaje de inicio
log_message("Iniciando verificación de la columna imagen en la tabla categorias...");

// Verificar permisos de directorios críticos
$assets_dir = __DIR__ . '/../assets/';
$categorias_dir = __DIR__ . '/../assets/categorias/';

// Verificar/crear directorio assets
if (!file_exists($assets_dir)) {
    log_message("El directorio assets no existe. Intentando crear...");
    if (!mkdir($assets_dir, 0777, true)) {
        log_message("ERROR: No se pudo crear el directorio assets. Permisos insuficientes.");
    } else {
        log_message("Directorio assets creado con éxito");
        // Establecer permisos explícitamente
        chmod($assets_dir, 0777);
    }
} else if (!is_writable($assets_dir)) {
    log_message("ADVERTENCIA: El directorio assets existe pero no tiene permisos de escritura");
    // Intentar corregir permisos
    chmod($assets_dir, 0777);
}

// Verificar/crear directorio categorias
if (!file_exists($categorias_dir)) {
    log_message("El directorio categorias no existe. Intentando crear...");
    if (!mkdir($categorias_dir, 0777, true)) {
        log_message("ERROR: No se pudo crear el directorio categorias. Permisos insuficientes.");
    } else {
        log_message("Directorio categorias creado con éxito");
        // Establecer permisos explícitamente
        chmod($categorias_dir, 0777);
    }
} else if (!is_writable($categorias_dir)) {
    log_message("ADVERTENCIA: El directorio categorias existe pero no tiene permisos de escritura");
    // Intentar corregir permisos
    chmod($categorias_dir, 0777);
}

try {
    // Verificar si la columna existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen'");
    $stmt->execute();
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        log_message("La columna 'imagen' no existe en la tabla categorias. Creándola...");
        
        // Añadir la columna
        $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen VARCHAR(255) COMMENT 'Ruta a la imagen de la categoría' AFTER descripcion");
        
        log_message("Columna 'imagen' añadida correctamente a la tabla 'categorias'");
        
        // Actualizar las categorías con imágenes predeterminadas
        $categorias = [
            ["nombre" => "Paz y Conflictos", "imagen" => "assets/categorias/ICONOPAZ.png"],
            ["nombre" => "Justicia y Derechos Humanos", "imagen" => "assets/categorias/ICONOJUSTICIA.png"],
            ["nombre" => "Igualdad y Diversidad", "imagen" => "assets/categorias/ICONODIVERSIDAD.png"],
            ["nombre" => "Participación Ciudadana", "imagen" => "assets/categorias/ICONOPARTICIPACION.png"],
            ["nombre" => "Corrupción y Transparencia", "imagen" => "assets/categorias/ICONOCORRUPCION.png"],
            ["nombre" => "Política y Gobernanza", "imagen" => "assets/categorias/ICONOPOLITICA.png"]
        ];
        
        foreach ($categorias as $categoria) {
            $sql = "UPDATE categorias SET imagen = ? WHERE nombre = ?";
            $stmtUpdate = $pdo->prepare($sql);
            $result = $stmtUpdate->execute([$categoria["imagen"], $categoria["nombre"]]);
            if ($result) {
                log_message("Actualizada categoría: " . $categoria["nombre"] . " con imagen: " . $categoria["imagen"]);
            } else {
                log_message("No se pudo actualizar la categoría: " . $categoria["nombre"]);
            }
        }
    } else {
        log_message("La columna 'imagen' ya existe en la tabla categorias.");
        
        // Verificar si hay categorías sin imagen
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE imagen IS NULL OR imagen = ''");
        $stmtCheck->execute();
        $sinImagen = $stmtCheck->fetchColumn();
        
        if ($sinImagen > 0) {
            log_message("Hay {$sinImagen} categorías sin imagen asignada. Actualizando...");
            
            // Actualizar solo las categorías que no tienen imagen
            $categorias = [
                ["nombre" => "Paz y Conflictos", "imagen" => "assets/categorias/ICONOPAZ.png"],
                ["nombre" => "Justicia y Derechos Humanos", "imagen" => "assets/categorias/ICONOJUSTICIA.png"],
                ["nombre" => "Igualdad y Diversidad", "imagen" => "assets/categorias/ICONODIVERSIDAD.png"],
                ["nombre" => "Participación Ciudadana", "imagen" => "assets/categorias/ICONOPARTICIPACION.png"],
                ["nombre" => "Corrupción y Transparencia", "imagen" => "assets/categorias/ICONOCORRUPCION.png"],
                ["nombre" => "Política y Gobernanza", "imagen" => "assets/categorias/ICONOPOLITICA.png"]
            ];
            
            foreach ($categorias as $categoria) {
                $sql = "UPDATE categorias SET imagen = ? WHERE nombre = ? AND (imagen IS NULL OR imagen = '')";
                $stmtUpdate = $pdo->prepare($sql);
                $result = $stmtUpdate->execute([$categoria["imagen"], $categoria["nombre"]]);
                if ($result && $stmtUpdate->rowCount() > 0) {
                    log_message("Actualizada categoría: " . $categoria["nombre"] . " con imagen: " . $categoria["imagen"]);
                }
            }
        } else {
            log_message("Todas las categorías ya tienen imágenes asignadas.");
        }
    }
    
    // Verificar si existe la columna imagen_fondo
    $stmtFondo = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen_fondo'");
    $stmtFondo->execute();
    $columnFondoExists = $stmtFondo->rowCount() > 0;
    
    if (!$columnFondoExists) {
        log_message("La columna 'imagen_fondo' no existe en la tabla categorias. Creándola...");
        
        // Añadir la columna
        $pdo->exec("ALTER TABLE categorias ADD COLUMN imagen_fondo VARCHAR(255) COMMENT 'Ruta a la imagen de fondo de la categoría' AFTER imagen");
        
        log_message("Columna 'imagen_fondo' añadida correctamente a la tabla 'categorias'");
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