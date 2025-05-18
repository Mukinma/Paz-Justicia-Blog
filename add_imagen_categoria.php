<?php
// Conexión a la base de datos
require_once 'config/db.php';

try {
    // Verificar si la columna ya existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE 'imagen'");
    $stmt->execute();
    $columnExists = $stmt->fetchColumn();
    
    if (!$columnExists) {
        // Añadir la columna 'imagen' a la tabla 'categorias'
        $sql = "ALTER TABLE categorias ADD COLUMN imagen VARCHAR(255) COMMENT 'Ruta a la imagen de la categoría' AFTER descripcion";
        $pdo->exec($sql);
        echo "Columna 'imagen' añadida correctamente a la tabla 'categorias'";
        
        // Añadir imágenes por defecto a categorías existentes
        $categorias = [
            ["nombre" => "Paz y Conflictos", "imagen" => "assets/ICONOPAZ.png"],
            ["nombre" => "Justicia y Derechos Humanos", "imagen" => "assets/ICONOJUSTICIA.png"],
            ["nombre" => "Igualdad y Diversidad", "imagen" => "assets/ICONODIVERSIDAD.png"],
            ["nombre" => "Participación Ciudadana", "imagen" => "assets/ICONOPARTICIPACION.png"],
            ["nombre" => "Corrupción y Transparencia", "imagen" => "assets/ICONOCORRUPCION.png"],
            ["nombre" => "Política y Gobernanza", "imagen" => "assets/ICONOPOLITICA.png"]
        ];
        
        foreach ($categorias as $categoria) {
            $sql = "UPDATE categorias SET imagen = ? WHERE nombre = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$categoria["imagen"], $categoria["nombre"]]);
            echo "<br>Actualizada categoría: " . $categoria["nombre"];
        }
    } else {
        echo "La columna 'imagen' ya existe en la tabla 'categorias'";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 