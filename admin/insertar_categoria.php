<?php
session_start();
require_once '../config/db.php';
require_once 'utils.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar la conexión a la base de datos
    $pdo->query("SELECT 1");
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener y validar datos
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            throw new Exception('El nombre de la categoría es requerido');
        }

        // Validar el nombre de la categoría
        $validacion = validarNombreCategoria($nombre);
        if (!$validacion['valid']) {
            throw new Exception($validacion['message']);
        }

        // Generar slug usando la función de PHP
        $slug = generarSlug($nombre, $pdo);

        // Verificar si ya existe una categoría con el mismo nombre o slug
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE nombre = ? OR slug = ?");
        $stmt->execute([$nombre, $slug]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Ya existe una categoría con ese nombre o slug');
        }

        // Procesar imagen si se ha subido una
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombre_temporal = $_FILES['imagen']['tmp_name'];
            $nombre_archivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
            $ruta_destino = '../assets/categorias/' . $nombre_archivo;
            
            // Crear el directorio si no existe
            if (!file_exists('../assets/categorias/')) {
                mkdir('../assets/categorias/', 0777, true);
            }
            
            // Mover el archivo subido
            if (move_uploaded_file($nombre_temporal, $ruta_destino)) {
                $imagen = 'assets/categorias/' . $nombre_archivo;
            }
        }

        // Iniciar transacción
        $pdo->beginTransaction();

        try {
            // Insertar la nueva categoría
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, slug, descripcion, imagen) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $slug, $descripcion, $imagen]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 