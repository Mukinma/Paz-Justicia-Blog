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
$log_file = fopen("debug_banear.log", "a");
fwrite($log_file, "=== Nueva solicitud " . date('Y-m-d H:i:s') . " ===\n");
fwrite($log_file, "POST: " . print_r($_POST, true) . "\n");
fwrite($log_file, "SESSION: " . print_r($_SESSION, true) . "\n");

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

// Verificar que se recibió un ID de usuario válido
if (!isset($_POST['id_usuario']) || empty($_POST['id_usuario'])) {
    $response = [
        'success' => false,
        'message' => 'ID de usuario no proporcionado.'
    ];
    fwrite($log_file, "Error de ID: " . print_r($response, true) . "\n");
    header('Content-Type: application/json');
    echo json_encode($response);
    fclose($log_file);
    exit;
}

$id_usuario = $_POST['id_usuario'];
fwrite($log_file, "ID de usuario a banear: $id_usuario\n");

try {
    // Verificar si el usuario existe
    $check_stmt = $pdo->prepare("SELECT id_usuario, estado FROM usuarios WHERE id_usuario = ?");
    $check_stmt->execute([$id_usuario]);
    $usuario = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        $response = [
            'success' => false,
            'message' => 'El usuario no existe.'
        ];
        fwrite($log_file, "Usuario no encontrado\n");
        header('Content-Type: application/json');
        echo json_encode($response);
        fclose($log_file);
        exit;
    }
    
    fwrite($log_file, "Usuario encontrado: " . print_r($usuario, true) . "\n");
    
    if ($usuario['estado'] === 'baneado') {
        $response = [
            'success' => false,
            'message' => 'El usuario ya está baneado.'
        ];
        fwrite($log_file, "Usuario ya baneado\n");
        header('Content-Type: application/json');
        echo json_encode($response);
        fclose($log_file);
        exit;
    }
    
    // Actualizar el estado del usuario a 'baneado'
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'baneado' WHERE id_usuario = ?");
    $result = $stmt->execute([$id_usuario]);
    
    fwrite($log_file, "Resultado de la actualización: " . ($result ? 'true' : 'false') . "\n");
    fwrite($log_file, "Filas afectadas: " . $stmt->rowCount() . "\n");
    
    if ($result && $stmt->rowCount() > 0) {
        // Registrar la actividad
        $admin_id = $_SESSION['id_usuario'] ?? $_SESSION['user_id'];
        $descripcion = "Usuario ID: $id_usuario ha sido baneado";
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $log_stmt = $pdo->prepare("INSERT INTO registro_actividades (id_usuario, tipo_actividad, descripcion, ip_address) VALUES (?, 'banear_usuario', ?, ?)");
        $log_result = $log_stmt->execute([$admin_id, $descripcion, $ip]);
        
        fwrite($log_file, "Registro de actividad: " . ($log_result ? 'true' : 'false') . "\n");
        
        $response = [
            'success' => true,
            'message' => 'Usuario baneado correctamente.'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'No se pudo banear al usuario. El usuario no existe o ya está baneado.'
        ];
    }
} catch (PDOException $e) {
    fwrite($log_file, "Error PDO: " . $e->getMessage() . "\n");
    $response = [
        'success' => false,
        'message' => 'Error al banear usuario: ' . $e->getMessage()
    ];
}

fwrite($log_file, "Respuesta final: " . print_r($response, true) . "\n\n");

// Asegurar que los encabezados de respuesta sean correctos
header('Content-Type: application/json');
echo json_encode($response);
fclose($log_file);
exit;
?> 