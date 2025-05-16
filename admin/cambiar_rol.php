<?php
session_start();
require_once '../config/db.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit;
}

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['id_usuario']) || !isset($_POST['rol'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

$id_usuario = $_POST['id_usuario'];
$nuevo_rol = $_POST['rol'];

// Validar que el rol sea uno de los permitidos
$roles_permitidos = ['admin', 'editor', 'lector'];
if (!in_array($nuevo_rol, $roles_permitidos)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Rol no válido']);
    exit;
}

try {
    // Actualizar el rol del usuario
    $sql = "UPDATE usuarios SET rol = ? WHERE id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$nuevo_rol, $id_usuario]);

    if ($result) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Rol actualizado exitosamente']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el rol']);
    }
} catch (PDOException $e) {
    error_log('Error en cambiar_rol.php: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
}
?> 