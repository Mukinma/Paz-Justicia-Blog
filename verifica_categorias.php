<?php
// Script para verificar las categorías en la base de datos
require_once 'config/db.php';

// Consulta para obtener todas las categorías
$sql = "SELECT id_categoria, nombre, slug, imagen, imagen_fondo FROM categorias ORDER BY nombre";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mostrar cantidad de categorías
echo "Total de categorías: " . count($categorias) . "\n\n";

// Mostrar cada categoría
if (count($categorias) > 0) {
    foreach ($categorias as $cat) {
        echo "ID: " . $cat['id_categoria'] . "\n";
        echo "Nombre: " . $cat['nombre'] . "\n";
        echo "Slug: " . $cat['slug'] . "\n";
        echo "Imagen: " . (empty($cat['imagen']) ? "No tiene" : $cat['imagen']) . "\n";
        echo "Imagen de fondo: " . (empty($cat['imagen_fondo']) ? "No tiene" : $cat['imagen_fondo']) . "\n";
        echo "------------------------------\n";
    }
} else {
    echo "No hay categorías en la base de datos.\n";
    echo "Es necesario crear categorías para que aparezcan en el menú.\n";
}
?> 