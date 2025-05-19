<?php
session_start();
require_once '../config/db.php';

// Verificar si el usuario está logueado y tiene permisos
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

// Verificar que se recibieron los parámetros necesarios
if (!isset($_POST['id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit();
}

$post_id = intval($_POST['id']);
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Determinar el nuevo estado y la descripción de la actividad
    $nuevo_estado = ($action === 'archive') ? 'archivado' : 'publicado';
    $tipo_actividad = ($action === 'archive') ? 'archivar_post' : 'desarchivar_post';
    $descripcion = ($action === 'archive') ? 'Post archivado' : 'Post desarchivado';

    // Actualizar el estado del post
    $stmt = $pdo->prepare("UPDATE posts SET estado = ? WHERE id_post = ?");
    $resultado = $stmt->execute([$nuevo_estado, $post_id]);

    if ($resultado) {
        // Registrar la actividad
        $stmt = $pdo->prepare("INSERT INTO registro_actividades (id_usuario, tipo_actividad, descripcion, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $tipo_actividad, $descripcion, $ip_address]);

        // Confirmar transacción
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => ($action === 'archive') ? 'Post archivado correctamente' : 'Post desarchivado correctamente'
        ]);
    } else {
        throw new Exception('Error al actualizar el estado del post');
    }
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 