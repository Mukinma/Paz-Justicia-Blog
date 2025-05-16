<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        $sql = "SELECT id_categoria, nombre, descripcion FROM categorias WHERE id_categoria = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($categoria) {
            echo json_encode([
                'success' => true,
                'categoria' => $categoria
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener la categoría: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido o ID no proporcionado'
    ]);
} 