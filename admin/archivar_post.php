<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_post = intval($_POST['id']);
        
        // Actualizar el estado del post a 'archivado'
        $sql = "UPDATE posts SET estado = 'archivado' WHERE id_post = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_post]);
        
        // Verificar si se actualizó correctamente
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Post archivado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo archivar el post']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al archivar el post: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
} 