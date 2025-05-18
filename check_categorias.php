<?php
// Archivo para verificar las imágenes de categorías
require_once 'config/db.php';

// Obtener todas las categorías con sus imágenes
$sql = "SELECT id_categoria, nombre, slug, imagen FROM categorias";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Establecer el tipo de contenido como HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Imágenes de Categorías</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin-top: 0;
            font-size: 18px;
            color: #444;
        }
        .card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .card p {
            margin: 5px 0;
            color: #666;
        }
        .status {
            font-weight: bold;
        }
        .status.success {
            color: green;
        }
        .status.error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Verificación de Imágenes de Categorías</h1>
    
    <div class="grid">
        <?php foreach ($categorias as $categoria): ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($categoria['nombre']); ?></h2>
                
                <?php 
                $imagen_path = !empty($categoria['imagen']) ? $categoria['imagen'] : 'assets/image-placeholder.png';
                $imagen_existe = file_exists($imagen_path);
                $ruta_completa = __DIR__ . '/' . $imagen_path;
                ?>
                
                <img src="<?php echo htmlspecialchars($imagen_path); ?>" alt="<?php echo htmlspecialchars($categoria['nombre']); ?>" onerror="this.src='assets/image-placeholder.png';">
                
                <p>Slug: <?php echo htmlspecialchars($categoria['slug']); ?></p>
                <p>Ruta: <?php echo htmlspecialchars($imagen_path); ?></p>
                <p class="status <?php echo $imagen_existe ? 'success' : 'error'; ?>">
                    <?php echo $imagen_existe ? 'Imagen encontrada' : 'Imagen no encontrada'; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html> 