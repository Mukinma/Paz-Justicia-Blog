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

try {
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
        exit;
    }
    
    $id = $_GET['id'];
    
    // Verificar si la categoría existe y obtener sus datos
    $stmt = $pdo->prepare("SELECT id_categoria, nombre, slug, descripcion FROM categorias WHERE id_categoria = ?");
    $stmt->execute([$id]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$categoria) {
        echo json_encode(['success' => false, 'message' => 'Categoría no encontrada']);
        exit;
    }
    
    echo json_encode(['success' => true, 'categoria' => $categoria]);
    
} catch (PDOException $e) {
    error_log("Error en obtener_categoria.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error inesperado en obtener_categoria.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
} 