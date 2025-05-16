<?php
session_start();
require_once '../config/db.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción'
    ]);
    exit;
}

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['id_usuario']) || !isset($_POST['rol'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos'
    ]);
    exit;
}

$id_usuario = $_POST['id_usuario'];
$nuevo_rol = $_POST['rol'];

// Validar que el nuevo rol sea uno de los permitidos
$roles_permitidos = ['admin', 'editor', 'lector'];
if (!in_array($nuevo_rol, $roles_permitidos)) {
    echo json_encode([
        'success' => false,
        'message' => 'Rol no válido'
    ]);
    exit;
}

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    
    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no encontrado'
        ]);
        exit;
    }

    // Actualizar el rol del usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id_usuario = ?");
    $stmt->execute([$nuevo_rol, $id_usuario]);

    echo json_encode([
        'success' => true,
        'message' => 'Rol actualizado exitosamente'
    ]);
} catch (PDOException $e) {
    error_log("Error al cambiar rol: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el rol'
    ]);
}
?> 