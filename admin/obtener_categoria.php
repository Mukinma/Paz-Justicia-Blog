<?php
session_start();
require_once '../config/db.php';

// Verificar que el usuario tiene los permisos necesarios
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit;
}

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    try {
        // Incluye la columna imagen_fondo en la consulta
        $stmt = $pdo->prepare("SELECT id_categoria, nombre, slug, descripcion, imagen, imagen_fondo FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($categoria) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'categoria' => $categoria]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Categoría no encontrada']);
        }
    } catch (PDOException $e) {
        error_log("Error en obtener_categoria.php: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al obtener los datos de la categoría']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID de categoría no especificado']);
}
?> 