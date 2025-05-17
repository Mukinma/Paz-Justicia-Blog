<?php
// Iniciar sesión para verificar si el usuario está autenticado
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para dar me gusta'
    ]);
    exit;
}

// Verificar si se recibió el ID del post
if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de post no válido'
    ]);
    exit;
}

// Obtener datos
$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['id_usuario'];

// Conectar a la base de datos
require_once '../config/db.php';

try {
    // Verificar si ya existe un "me gusta" de este usuario para este post
    $sql_check = "SELECT * FROM post_likes WHERE id_post = ? AND id_usuario = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$post_id, $user_id]);
    
    if ($stmt_check->rowCount() > 0) {
        // Si ya existe, eliminar el "me gusta"
        $sql_delete = "DELETE FROM post_likes WHERE id_post = ? AND id_usuario = ?";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute([$post_id, $user_id]);
        
        $liked = false;
    } else {
        // Si no existe, agregar nuevo "me gusta"
        $sql_insert = "INSERT INTO post_likes (id_post, id_usuario, fecha) VALUES (?, ?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$post_id, $user_id]);
        
        $liked = true;
    }
    
    // Obtener el número actual de "me gusta"
    $sql_count = "SELECT COUNT(*) as likes FROM post_likes WHERE id_post = ?";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute([$post_id]);
    $likes = $stmt_count->fetch(PDO::FETCH_ASSOC)['likes'];
    
    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'likes' => $likes
    ]);
    
} catch (PDOException $e) {
    // En caso de error, devolver mensaje de error
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} 