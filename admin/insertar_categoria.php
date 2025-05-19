<?php
session_start();
require_once '../config/db.php';
require_once 'utils.php';
// Incluir el archivo de ayuda para subida de archivos
require_once 'utils/upload_helper.php';
// Añadir después de los requires existentes
require_once 'utils/image_resizer.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/insertar_categoria_debug.log');

// Establecer límites de subida de archivos (solo documentativo)
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '22M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '256M');

// Función para logs
function log_debug($message, $data = []) {
    $log_file = __DIR__ . '/insertar_categoria_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}\n";
    
    if (!empty($data)) {
        $log_message .= json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Iniciar log
log_debug("Recibida solicitud insertar_categoria.php");
log_debug("Datos POST:", $_POST);
log_debug("Datos FILES:", $_FILES);

// Verificar y crear directorios necesarios
$assets_dir = __DIR__ . '/../assets/';
$categorias_dir = __DIR__ . '/../assets/categorias/';

// Verificar/crear directorio assets
if (!file_exists($assets_dir)) {
    log_debug("El directorio assets no existe. Intentando crear...");
    if (!mkdir($assets_dir, 0777, true)) {
        log_debug("ERROR: No se pudo crear el directorio assets", ['error' => error_get_last()]);
        echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio para almacenar imágenes']);
        exit;
    }
    chmod($assets_dir, 0777);
    log_debug("Directorio assets creado con éxito");
} else if (!is_writable($assets_dir)) {
    log_debug("ADVERTENCIA: El directorio assets existe pero no tiene permisos de escritura");
    chmod($assets_dir, 0777);
}

// Verificar/crear directorio categorias
if (!file_exists($categorias_dir)) {
    log_debug("El directorio categorias no existe. Intentando crear...");
    if (!mkdir($categorias_dir, 0777, true)) {
        log_debug("ERROR: No se pudo crear el directorio categorias", ['error' => error_get_last()]);
        echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio para almacenar imágenes de categorías']);
        exit;
    }
    chmod($categorias_dir, 0777);
    log_debug("Directorio categorias creado con éxito");
} else if (!is_writable($categorias_dir)) {
    log_debug("ADVERTENCIA: El directorio categorias existe pero no tiene permisos de escritura");
    chmod($categorias_dir, 0777);
}

// Después de la verificación de directorios, agregar la verificación de columnas
// Alrededor de la línea 67

// Verificar que existan las columnas necesarias en la tabla categorias
$columnsToCheck = ['imagen', 'imagen_fondo'];
foreach ($columnsToCheck as $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE ?");
        $stmt->execute([$column]);
        $columnExists = $stmt->rowCount() > 0;
        
        if (!$columnExists) {
            log_debug("La columna '$column' no existe, intentando crearla");
            
            // Determinar la posición de la nueva columna
            $afterColumn = ($column == 'imagen') ? 'descripcion' : 'imagen';
            
            $sql = "ALTER TABLE categorias ADD COLUMN $column VARCHAR(255) AFTER $afterColumn";
            $pdo->exec($sql);
            
            log_debug("Columna '$column' creada exitosamente");
        } else {
            log_debug("La columna '$column' ya existe en la tabla");
        }
    } catch (PDOException $e) {
        log_debug("Error al verificar/crear columna $column", ['error' => $e->getMessage()]);
        // Continuar de todos modos, pero alertar del error
    }
}

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
            $fileType = $_FILES['imagen']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($fileType, $allowedTypes)) {
                log_debug("Tipo de archivo no permitido", ['tipo' => $fileType]);
                throw new Exception('Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG, GIF y WEBP.');
            }
            
            $nombre_temporal = $_FILES['imagen']['tmp_name'];
            $nombre_original = $_FILES['imagen']['name'];
            
            // Sanitizar el nombre de archivo para evitar problemas con espacios y caracteres especiales
            $nombre_sanitizado = sanitizar_nombre_archivo($nombre_original);
            $nombre_archivo = 'icono_categoria_' . $nombre_sanitizado;
            
            log_debug("Procesando archivo de imagen", [
                'origen' => $nombre_temporal, 
                'nombre_sanitizado' => $nombre_sanitizado,
                'tamaño' => filesize($nombre_temporal)
            ]);
            
            // Intentar redimensionar la imagen para reducir su tamaño
            $resultado_optimizacion = process_uploaded_image(
                $_FILES['imagen'],
                $categorias_dir,
                $nombre_archivo
            );
            
            if ($resultado_optimizacion['success']) {
                log_debug("Imagen redimensionada y optimizada correctamente", $resultado_optimizacion);
                $imagen = 'assets/categorias/' . $nombre_archivo;
                log_debug("Imagen cargada exitosamente", [
                    'ruta' => $imagen,
                    'tamaño_original' => $resultado_optimizacion['original_size'],
                    'tamaño_nuevo' => $resultado_optimizacion['new_size']
                ]);
            } else {
                log_debug("Error al optimizar la imagen", $resultado_optimizacion);
                
                // Como fallback, intentar el método normal
                $ruta_destino = $categorias_dir . $nombre_archivo;
                
                log_debug("Intentando método alternativo de carga", [
                    'origen' => $nombre_temporal, 
                    'destino' => $ruta_destino
                ]);
                
                // Utilizar la función auxiliar para subir el archivo
                $prefijo_log = "Categoría nueva (icono)";
                $resultado_subida = subir_archivo_seguro($nombre_temporal, $ruta_destino, $prefijo_log);
                
                if ($resultado_subida) {
                    $imagen = 'assets/categorias/' . $nombre_archivo;
                    log_debug("Imagen cargada exitosamente con método alternativo", ['ruta' => $imagen]);
                } else {
                    throw new Exception("No se pudo subir la imagen. Todos los métodos de carga fallaron.");
                }
            }
        }
        
        // Procesar imagen de fondo si se ha subido una
        $imagen_fondo = null;
        if (isset($_FILES['imagen_fondo']) && $_FILES['imagen_fondo']['error'] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['imagen_fondo']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($fileType, $allowedTypes)) {
                log_debug("Tipo de archivo de fondo no permitido", ['tipo' => $fileType]);
                throw new Exception('Tipo de archivo no permitido para imagen de fondo. Solo se permiten imágenes JPG, PNG, GIF y WEBP.');
            }
            
            $nombre_temporal = $_FILES['imagen_fondo']['tmp_name'];
            $nombre_original = $_FILES['imagen_fondo']['name'];
            
            // Sanitizar el nombre de archivo para evitar problemas con espacios y caracteres especiales
            $nombre_sanitizado = sanitizar_nombre_archivo($nombre_original);
            $nombre_archivo = 'bg_categoria_' . $nombre_sanitizado;
            
            log_debug("Procesando archivo de imagen de fondo", [
                'origen' => $nombre_temporal, 
                'nombre_sanitizado' => $nombre_sanitizado,
                'tamaño' => filesize($nombre_temporal)
            ]);
            
            // Intentar redimensionar la imagen para reducir su tamaño
            $resultado_optimizacion = process_uploaded_image(
                $_FILES['imagen_fondo'],
                $categorias_dir,
                $nombre_archivo
            );
            
            if ($resultado_optimizacion['success']) {
                log_debug("Imagen de fondo redimensionada y optimizada correctamente", $resultado_optimizacion);
                $imagen_fondo = 'assets/categorias/' . $nombre_archivo;
                log_debug("Imagen de fondo cargada exitosamente", [
                    'ruta' => $imagen_fondo,
                    'tamaño_original' => $resultado_optimizacion['original_size'],
                    'tamaño_nuevo' => $resultado_optimizacion['new_size']
                ]);
            } else {
                log_debug("Error al optimizar la imagen de fondo", $resultado_optimizacion);
                
                // Como fallback, intentar el método normal
                $ruta_destino = $categorias_dir . $nombre_archivo;
                
                log_debug("Intentando método alternativo de carga para fondo", [
                    'origen' => $nombre_temporal, 
                    'destino' => $ruta_destino
                ]);
                
                // Utilizar la función auxiliar para subir el archivo
                $prefijo_log = "Categoría nueva (fondo)";
                $resultado_subida = subir_archivo_seguro($nombre_temporal, $ruta_destino, $prefijo_log);
                
                if ($resultado_subida) {
                    $imagen_fondo = 'assets/categorias/' . $nombre_archivo;
                    log_debug("Imagen de fondo cargada exitosamente con método alternativo", ['ruta' => $imagen_fondo]);
                } else {
                    throw new Exception("No se pudo subir la imagen de fondo. Todos los métodos de carga fallaron.");
                }
            }
        }

        // Iniciar transacción
        $pdo->beginTransaction();

        try {
            // Insertar la nueva categoría con imagen e imagen de fondo
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, slug, descripcion, imagen, imagen_fondo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $slug, $descripcion, $imagen, $imagen_fondo]);

            $pdo->commit();
            log_debug("Categoría creada exitosamente", [
                'nombre' => $nombre,
                'slug' => $slug
            ]);
            echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
        } catch (Exception $e) {
            $pdo->rollBack();
            log_debug("Error en la transacción", ['error' => $e->getMessage()]);
            throw $e;
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    log_debug("Error en la base de datos", ['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    log_debug("Error general", ['error' => $e->getMessage()]);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 