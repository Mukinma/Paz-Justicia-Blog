<?php
// Script para cargar más posts de una categoría específica mediante AJAX

// Incluir la configuración de la base de datos
require_once '../config/db.php';

// Incluir funciones de categoría
require_once 'includes/categoria_functions.php';

// Verificar que los parámetros necesarios están presentes
if (!isset($_GET['categoria']) || !isset($_GET['offset'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Faltan parámetros requeridos'
    ]);
    exit;
}

// Obtener parámetros
$categoria_id = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_NUMBER_INT);
$offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT);
$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT) ?: 6; // Por defecto 6 posts

try {
    // Obtener más posts
    $posts = obtenerPostsPorCategoria($pdo, $categoria_id, $limit, $offset);
    
    // Obtener el total de posts para determinar si hay más
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM posts WHERE id_categoria = ? AND estado = 'publicado'");
    $stmt->execute([$categoria_id]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Determinar si hay más posts disponibles
    $hay_mas = ($offset + $limit) < $total;
    
    // Devolver respuesta
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'no_more' => !$hay_mas,
        'total' => $total,
        'loaded' => $offset + count($posts)
    ]);
} catch (Exception $e) {
    // Manejar errores
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar más posts: ' . $e->getMessage()
    ]);
}
?> 