<?php
// Este script recibe peticiones AJAX para cargar más posts de una categoría específica

// Incluir la configuración de la base de datos
require_once '../config/db.php';

// Incluir funciones de categoría
require_once 'includes/categoria_functions.php';

// Verificar que los parámetros necesarios estén presentes
if (!isset($_GET['categoria']) || !isset($_GET['offset'])) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incorrectos']);
    exit;
}

// Obtener y sanear los parámetros
$categoria_id = (int)$_GET['categoria'];
$offset = (int)$_GET['offset'];
$limit = 6; // Número de posts a cargar por petición

// Validar los parámetros
if ($categoria_id <= 0 || $offset < 0) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

try {
    // Obtener posts de la categoría
    $posts = obtenerPostsPorCategoria($pdo, $categoria_id, $limit, $offset);
    
    // Formatear las fechas para cada post
    foreach ($posts as &$post) {
        $post['fecha_formateada'] = formatearFecha($post['fecha_publicacion']);
    }
    
    // Devolver los posts en formato JSON
    echo json_encode(['success' => true, 'posts' => $posts]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener los posts: ' . $e->getMessage()]);
}
?> 