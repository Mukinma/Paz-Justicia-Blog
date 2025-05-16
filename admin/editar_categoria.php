<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        // Crear el slug a partir del nombre
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre)));
        
        // Verificar si ya existe otra categoría con el mismo nombre o slug
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE (nombre = ? OR slug = ?) AND id_categoria != ?");
        $stmt->execute([$nombre, $slug, $id]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya existe otra categoría con ese nombre']);
            exit;
        }
        
        // Actualizar la categoría
        $stmt = $pdo->prepare("UPDATE categorias SET nombre = ?, slug = ?, descripcion = ? WHERE id_categoria = ?");
        $stmt->execute([$nombre, $slug, $descripcion, $id]);
        
        echo json_encode(['success' => true, 'message' => 'Categoría actualizada exitosamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la categoría: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
} 