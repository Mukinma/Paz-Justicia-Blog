<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        
        // Verificar si la categoría tiene posts asociados
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE id_categoria = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar la categoría porque tiene posts asociados']);
            exit;
        }
        
        // Eliminar la categoría
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
} 