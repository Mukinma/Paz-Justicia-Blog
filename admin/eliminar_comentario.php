<?php
// Permitir CORS para depuración
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Configurar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/db.php';

// Log de depuración
$log_file = fopen("debug_eliminar_comentario.log", "a");
fwrite($log_file, "=== Nueva solicitud " . date('Y-m-d H:i:s') . " ===\n");
fwrite($log_file, "POST: " . print_r($_POST, true) . "\n");
fwrite($log_file, "SESSION: " . print_r($_SESSION, true) . "\n");

// Verificar si el usuario está logueado y tiene permisos adecuados (admin o editor)
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    $response = [
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción.',
        'session' => $_SESSION
    ];
    fwrite($log_file, "Error de permisos: " . print_r($response, true) . "\n");
    header('Content-Type: application/json');
    echo json_encode($response);
    fclose($log_file);
    exit;
}

// Verificar que se recibió un ID de comentario válido
if (!isset($_POST['id_comentario']) || empty($_POST['id_comentario'])) {
    $response = [
        'success' => false,
        'message' => 'ID de comentario no proporcionado.'
    ];
    fwrite($log_file, "Error de ID: " . print_r($response, true) . "\n");
    header('Content-Type: application/json');
    echo json_encode($response);
    fclose($log_file);
    exit;
}

$id_comentario = $_POST['id_comentario'];
fwrite($log_file, "ID de comentario a eliminar: $id_comentario\n");

try {
    // Obtener información del comentario antes de eliminarlo para el registro
    $stmt = $pdo->prepare("SELECT id_post, id_usuario FROM comentarios WHERE id_comentario = ?");
    $stmt->execute([$id_comentario]);
    $comentario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    fwrite($log_file, "Datos del comentario: " . print_r($comentario, true) . "\n");
    
    if (!$comentario) {
        $response = [
            'success' => false,
            'message' => 'El comentario no existe.'
        ];
        fwrite($log_file, "Comentario no encontrado\n");
        header('Content-Type: application/json');
        echo json_encode($response);
        fclose($log_file);
        exit;
    }
    
    // Eliminar el comentario
    $delete_stmt = $pdo->prepare("DELETE FROM comentarios WHERE id_comentario = ?");
    $result = $delete_stmt->execute([$id_comentario]);
    
    fwrite($log_file, "Resultado de la eliminación: " . ($result ? 'true' : 'false') . "\n");
    fwrite($log_file, "Filas afectadas: " . $delete_stmt->rowCount() . "\n");
    
    if ($result && $delete_stmt->rowCount() > 0) {
        // Registrar la actividad
        $admin_id = $_SESSION['id_usuario'] ?? $_SESSION['user_id'];
        $descripcion = "Comentario ID: $id_comentario eliminado del post ID: {$comentario['id_post']}";
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $log_stmt = $pdo->prepare("INSERT INTO registro_actividades (id_usuario, tipo_actividad, descripcion, ip_address) VALUES (?, 'eliminar_comentario', ?, ?)");
        $log_result = $log_stmt->execute([$admin_id, $descripcion, $ip]);
        
        fwrite($log_file, "Registro de actividad: " . ($log_result ? 'true' : 'false') . "\n");
        
        $response = [
            'success' => true,
            'message' => 'Comentario eliminado correctamente.'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'No se pudo eliminar el comentario.'
        ];
    }
} catch (PDOException $e) {
    fwrite($log_file, "Error PDO: " . $e->getMessage() . "\n");
    $response = [
        'success' => false,
        'message' => 'Error al eliminar comentario: ' . $e->getMessage()
    ];
}

fwrite($log_file, "Respuesta final: " . print_r($response, true) . "\n\n");

// Asegurar que los encabezados de respuesta sean correctos
header('Content-Type: application/json');
echo json_encode($response);
fclose($log_file);
exit;
?> 