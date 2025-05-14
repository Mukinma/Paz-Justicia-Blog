<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que el usuario esté logueado
    if (!isset($_SESSION['usuario'])) {
        header('Location: ../admin/usuario.php');
        exit();
    }
    
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    $contenido = trim($_POST['contenido']);
    
    if ($post_id && !empty($contenido)) {
        try {
            // Obtener el ID del usuario
            $sql_user = "SELECT id_usuario FROM usuarios WHERE name = ?";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([$_SESSION['usuario']]);
            $user_id = $stmt_user->fetchColumn();
            
            // Insertar comentario
            $sql = "INSERT INTO comentarios (id_post, id_usuario, contenido, fecha_comentario, aprobado) 
                    VALUES (?, ?, ?, NOW(), 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$post_id, $user_id, $contenido]);
            
            // Redireccionar de vuelta al post
            header("Location: ../views/post.php?id=" . $post_id);
            exit();
        } catch (PDOException $e) {
            error_log("Error al insertar comentario: " . $e->getMessage());
            header("Location: ../views/post.php?id=" . $post_id . "&error=comment");
            exit();
        }
    }
}

// Si no es POST, redireccionar al index
header('Location: ../index.php');
exit();
?>