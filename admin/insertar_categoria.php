<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        // Crear el slug a partir del nombre
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre)));
        
        // Verificar si ya existe una categoría con el mismo nombre o slug
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE nombre = ? OR slug = ?");
        $stmt->execute([$nombre, $slug]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya existe una categoría con ese nombre']);
            exit;
        }
        
        // Insertar la nueva categoría
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, slug, descripcion) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $slug, $descripcion]);
        
        echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear la categoría: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
} 