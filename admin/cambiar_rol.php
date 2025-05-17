<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción'
    ]);
    exit;
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener y decodificar los datos JSON enviados
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Verificar que los datos necesarios existan
if (!isset($data['id_usuario']) || !isset($data['rol'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos'
    ]);
    exit;
}

// Validar el rol
$roles_permitidos = ['admin', 'editor', 'lector'];
if (!in_array($data['rol'], $roles_permitidos)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Rol no válido'
    ]);
    exit;
}

// Conectar a la base de datos
require '../config/db.php';

try {
    // Preparar la consulta SQL
    $sql = "UPDATE usuarios SET rol = ? WHERE id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta
    $stmt->execute([$data['rol'], $data['id_usuario']]);
    
    // Verificar si se actualizó algún registro
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Rol actualizado correctamente'
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo actualizar el rol o el usuario no existe'
        ]);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?> 