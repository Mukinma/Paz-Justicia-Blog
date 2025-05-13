<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_post = intval($_POST['id']);
        
        // Actualizar el estado del post a 'publicado'
        $sql = "UPDATE posts SET estado = 'publicado' WHERE id_post = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_post]);
        
        // Verificar si se actualizó correctamente
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Post desarchivado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo desarchivar el post']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al desarchivar el post: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
} 