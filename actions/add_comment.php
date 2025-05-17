<?php
session_start();
require_once '../config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    // Redirigir a la página de login con un mensaje
    $_SESSION['error'] = 'Debes iniciar sesión para comentar';
    header('Location: ../admin/usuario.php');
    exit();
}

// Verificar si se recibieron los datos necesarios
if (!isset($_POST['post_id']) || !isset($_POST['comment']) || empty($_POST['comment'])) {
    // Redirigir a la página anterior con un error
    $_SESSION['error'] = 'Datos incompletos para procesar el comentario';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Obtener y sanitizar los datos
$post_id = intval($_POST['post_id']);
$comment = trim($_POST['comment']);
$user_id = $_SESSION['id_usuario'];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Verificar si el post existe
$sql_check = "SELECT id_post FROM posts WHERE id_post = ? AND estado = 'publicado'";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([$post_id]);

if ($stmt_check->rowCount() === 0) {
    $_SESSION['error'] = 'El artículo no existe o no está disponible';
    header('Location: ../index.php');
    exit();
}

// Estado de aprobación predeterminado según rol del usuario
// Los administradores y editores tienen aprobación automática
$auto_aprobado = 0; // Por defecto, los comentarios requieren aprobación
if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
    $auto_aprobado = 1;
}

// Insertar el comentario en la base de datos
$sql = "INSERT INTO comentarios (id_post, id_usuario, contenido, fecha_comentario, aprobado, ip_address) 
        VALUES (?, ?, ?, NOW(), ?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $result = $stmt->execute([$post_id, $user_id, $comment, $auto_aprobado, $ip_address]);
    
    if ($result) {
        // Éxito
        if ($auto_aprobado) {
            $_SESSION['success'] = 'Comentario publicado correctamente';
        } else {
            $_SESSION['success'] = 'Comentario enviado y pendiente de aprobación';
        }
    } else {
        // Error
        $_SESSION['error'] = 'Error al publicar el comentario';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error en la base de datos: ' . $e->getMessage();
}

// Redirigir a la página del post
header('Location: ../views/post.php?id=' . $post_id);
exit();
?> 