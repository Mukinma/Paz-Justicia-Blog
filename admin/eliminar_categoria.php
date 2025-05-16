<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Activar reporte de errores para depurar
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit;
}

// Obtener el contenido JSON del cuerpo de la petición
$json = file_get_contents('php://input');
$data = json_decode($json, true);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar que se recibió el ID
        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de categoría no proporcionado']);
            exit;
        }

        $id = $data['id'];
        
        // Verificar si existen artículos que utilizan esta categoría
        $checkPostsStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE id_categoria = ?");
        $checkPostsStmt->execute([$id]);
        $postsCount = $checkPostsStmt->fetchColumn();
        
        if ($postsCount > 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'No se puede eliminar la categoría porque hay ' . $postsCount . ' artículos que la utilizan'
            ]);
            exit;
        }

        // Verificar si la categoría existe
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE id_categoria = ?");
        $checkStmt->execute([$id]);
        if ($checkStmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'La categoría no existe']);
            exit;
        }
        
        // Eliminar la categoría
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $success = $stmt->execute([$id]);
        
        if ($success) {
            echo json_encode([
                'success' => true, 
                'message' => 'Categoría eliminada correctamente'
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("Error SQL: " . implode(', ', $errorInfo));
            echo json_encode([
                'success' => false, 
                'message' => 'Error al eliminar la categoría: ' . $errorInfo[2]
            ]);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    error_log("Error en eliminar_categoria.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error inesperado en eliminar_categoria.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
} 