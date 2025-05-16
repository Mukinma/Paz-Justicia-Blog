<?php
session_start();
require '../config/db.php';
require 'utils.php';

header('Content-Type: application/json');

// Activar reporte de errores para depurar
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Función para enviar respuesta JSON y terminar
function sendJsonResponse($data) {
    echo json_encode($data);
    exit;
}

// Log para depuración
$log_file = dirname(__FILE__) . '/editar_categoria_debug.log';
function log_debug($message, $data = []) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}\n";
    
    if (!empty($data)) {
        $log_message .= json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Iniciar log
log_debug("Recibida solicitud editar_categoria.php");
log_debug("Datos POST:", $_POST);
log_debug("Sesión:", $_SESSION);

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    log_debug("Acceso denegado: usuario no autorizado");
    sendJsonResponse(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        log_debug("Procesando solicitud POST");
        
        // Verificar que se recibieron los datos necesarios
        if (!isset($_POST['id']) || !isset($_POST['nombre'])) {
            log_debug("Faltan datos requeridos", ['id' => isset($_POST['id']), 'nombre' => isset($_POST['nombre'])]);
            sendJsonResponse(['success' => false, 'message' => 'Faltan datos requeridos']);
        }

        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        log_debug("Datos recibidos", ['id' => $id, 'nombre' => $nombre, 'descripcion' => $descripcion]);
        
        // Validar el nombre
        $validacion = validarNombreCategoria($nombre);
        if (!$validacion['valid']) {
            log_debug("Validación fallida", $validacion);
            sendJsonResponse(['success' => false, 'message' => $validacion['message']]);
        }
        
        log_debug("Validación exitosa");
        
        // Verificar si la categoría existe
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE id_categoria = ?");
        $checkStmt->execute([$id]);
        if ($checkStmt->fetchColumn() == 0) {
            log_debug("La categoría no existe", ['id' => $id]);
            sendJsonResponse(['success' => false, 'message' => 'La categoría no existe']);
        }
        
        log_debug("La categoría existe");
        
        // Generar el slug
        $slug = generarSlug($nombre, $pdo, $id);
        log_debug("Slug generado", ['slug' => $slug]);
        
        // Iniciar una transacción
        $pdo->beginTransaction();
        log_debug("Iniciada transacción");
        
        // Actualizar la categoría
        $stmt = $pdo->prepare("UPDATE categorias SET nombre = ?, slug = ?, descripcion = ? WHERE id_categoria = ?");
        $success = $stmt->execute([$nombre, $slug, $descripcion, $id]);
        
        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            log_debug("Error al actualizar", $errorInfo);
            $pdo->rollBack();
            sendJsonResponse(['success' => false, 'message' => 'Error al actualizar la categoría: ' . $errorInfo[2]]);
        }
        
        // Confirmar la transacción
        $pdo->commit();
        log_debug("Transacción confirmada");
        
        // Obtener la categoría actualizada
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        
        log_debug("Categoría actualizada", $categoria);
        
        // Respuesta exitosa
        sendJsonResponse([
            'success' => true, 
            'message' => 'Categoría actualizada exitosamente',
            'categoria' => [
                'id_categoria' => $id,
                'nombre' => $nombre,
                'slug' => $slug,
                'descripcion' => $descripcion
            ]
        ]);
    } else {
        log_debug("Método no permitido", ['method' => $_SERVER['REQUEST_METHOD']]);
        sendJsonResponse(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    // En caso de error con la base de datos
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        log_debug("Transacción revertida debido a error");
    }
    log_debug("Error PDO", ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    sendJsonResponse(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar cualquier otro error
    log_debug("Error inesperado", ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    sendJsonResponse(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
} 