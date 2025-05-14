<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener el ID del post a eliminar
        $id_post = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id_post <= 0) {
            throw new Exception('ID de post inválido');
        }

        // Primero, eliminar las imágenes asociadas
        $sql_imagenes = "SELECT id_imagen_destacada, id_imagen_background FROM posts WHERE id_post = ?";
        $stmt = $pdo->prepare($sql_imagenes);
        $stmt->execute([$id_post]);
        $imagenes = $stmt->fetch(PDO::FETCH_ASSOC);

        // Eliminar las imágenes físicamente si existen
        if ($imagenes) {
            $sql_rutas = "SELECT ruta FROM imagenes WHERE id_imagen IN (?, ?)";
            $stmt = $pdo->prepare($sql_rutas);
            $stmt->execute([$imagenes['id_imagen_destacada'], $imagenes['id_imagen_background']]);
            $rutas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($rutas as $ruta) {
                if ($ruta && file_exists("../assets/" . $ruta)) {
                    unlink("../assets/" . $ruta);
                }
            }
        }

        // Eliminar las relaciones en posts_tags
        $sql_delete_tags = "DELETE FROM posts_tags WHERE id_post = ?";
        $stmt = $pdo->prepare($sql_delete_tags);
        $stmt->execute([$id_post]);

        // Eliminar los comentarios asociados
        $sql_delete_comentarios = "DELETE FROM comentarios WHERE id_post = ?";
        $stmt = $pdo->prepare($sql_delete_comentarios);
        $stmt->execute([$id_post]);

        // Finalmente, eliminar el post
        $sql_delete_post = "DELETE FROM posts WHERE id_post = ?";
        $stmt = $pdo->prepare($sql_delete_post);
        $stmt->execute([$id_post]);

        // Eliminar las imágenes de la base de datos
        if ($imagenes) {
            $sql_delete_imagenes = "DELETE FROM imagenes WHERE id_imagen IN (?, ?)";
            $stmt = $pdo->prepare($sql_delete_imagenes);
            $stmt->execute([$imagenes['id_imagen_destacada'], $imagenes['id_imagen_background']]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Post eliminado correctamente'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar el post: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}