<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';

// Check if the request is a POST request and has an ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get image ID before deleting the post (if exists)
        $sqlGetImage = "SELECT id_imagen_destacada FROM posts WHERE id_post = :id";
        $stmtGetImage = $pdo->prepare($sqlGetImage);
        $stmtGetImage->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtGetImage->execute();
        $imageId = $stmtGetImage->fetchColumn();
        
        // Delete post
        $sqlDeletePost = "DELETE FROM posts WHERE id_post = :id";
        $stmtDeletePost = $pdo->prepare($sqlDeletePost);
        $stmtDeletePost->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $stmtDeletePost->execute();
        
        // If image exists and was used only by this post, delete it too
        if ($imageId) {
            // Check if image is used by other posts
            $sqlCheckImage = "SELECT COUNT(*) FROM posts WHERE id_imagen_destacada = :image_id";
            $stmtCheckImage = $pdo->prepare($sqlCheckImage);
            $stmtCheckImage->bindParam(':image_id', $imageId, PDO::PARAM_INT);
            $stmtCheckImage->execute();
            
            if ($stmtCheckImage->fetchColumn() == 0) {
                // Get image path to delete the file
                $sqlGetImagePath = "SELECT ruta FROM imagenes WHERE id_imagen = :image_id";
                $stmtGetImagePath = $pdo->prepare($sqlGetImagePath);
                $stmtGetImagePath->bindParam(':image_id', $imageId, PDO::PARAM_INT);
                $stmtGetImagePath->execute();
                $imagePath = $stmtGetImagePath->fetchColumn();
                
                // Delete image record from database
                $sqlDeleteImage = "DELETE FROM imagenes WHERE id_imagen = :image_id";
                $stmtDeleteImage = $pdo->prepare($sqlDeleteImage);
                $stmtDeleteImage->bindParam(':image_id', $imageId, PDO::PARAM_INT);
                $stmtDeleteImage->execute();
                
                // Delete the actual file if it exists
                if ($imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
        }
    } catch (PDOException $e) {
        // Rollback on error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}