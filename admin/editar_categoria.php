<?php
session_start();
require '../config/db.php';
require 'utils.php';
// Incluir el archivo de ayuda para subida de archivos
require_once 'utils/upload_helper.php';
require_once 'utils/image_resizer.php';

header('Content-Type: application/json');

// Activar reporte de errores para depurar
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Establecer límites de subida de archivos - valores solo documentales 
// El verdadero límite está en php.ini (2M actualmente)
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '22M'); 
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '256M');

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
log_debug("Datos FILES:", $_FILES);
log_debug("Ruta de imagen temporal:", $_FILES['imagen_fondo']['tmp_name'] ?? 'No definido');

// Si hay un error en el archivo, mostrar el detalle
if (isset($_FILES['imagen_fondo']) && $_FILES['imagen_fondo']['error'] !== 0) {
    log_debug("Error en el archivo imagen_fondo:", [
        'error_code' => $_FILES['imagen_fondo']['error'],
        'error_message' => getUploadErrorMessage($_FILES['imagen_fondo']['error'])
    ]);
}

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== 0) {
    log_debug("Error en el archivo imagen:", [
        'error_code' => $_FILES['imagen']['error'],
        'error_message' => getUploadErrorMessage($_FILES['imagen']['error'])
    ]);
}

log_debug("Sesión:", $_SESSION);

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    log_debug("Acceso denegado: usuario no autorizado");
    sendJsonResponse(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
}

// Verificar y crear directorios necesarios
$assets_dir = __DIR__ . '/../assets/';
$categorias_dir = __DIR__ . '/../assets/categorias/';

// Verificar/crear directorio assets
if (!file_exists($assets_dir)) {
    log_debug("El directorio assets no existe. Intentando crear...");
    if (!mkdir($assets_dir, 0777, true)) {
        log_debug("ERROR: No se pudo crear el directorio assets", ['error' => error_get_last()]);
        sendJsonResponse(['success' => false, 'message' => 'No se pudo crear el directorio para almacenar imágenes']);
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
        sendJsonResponse(['success' => false, 'message' => 'No se pudo crear el directorio para almacenar imágenes de categorías']);
    }
    chmod($categorias_dir, 0777);
    log_debug("Directorio categorias creado con éxito");
} else if (!is_writable($categorias_dir)) {
    log_debug("ADVERTENCIA: El directorio categorias existe pero no tiene permisos de escritura");
    chmod($categorias_dir, 0777);
}

// Verificar la configuración PHP actual
log_debug("Configuración PHP:", [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit')
]);

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
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        // Es una solicitud para obtener los datos de la categoría
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        try {
            // Incluye la columna imagen_fondo en la consulta
            $stmt = $pdo->prepare("SELECT id_categoria, nombre, slug, descripcion, imagen, imagen_fondo FROM categorias WHERE id_categoria = ?");
            $stmt->execute([$id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($categoria) {
                // Devolver los datos en formato JSON
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'categoria' => $categoria]);
                exit;
            } else {
                // Categoría no encontrada
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Categoría no encontrada']);
                exit;
            }
        } catch (PDOException $e) {
            error_log("Error en editar_categoria.php: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al obtener los datos de la categoría']);
            exit;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $checkStmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $checkStmt->execute([$id]);
        $categoria_actual = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$categoria_actual) {
            log_debug("La categoría no existe", ['id' => $id]);
            sendJsonResponse(['success' => false, 'message' => 'La categoría no existe']);
        }
        
        log_debug("La categoría existe", $categoria_actual);
        
        // Generar el slug
        $slug = generarSlug($nombre, $pdo, $id);
        log_debug("Slug generado", ['slug' => $slug]);
        
        // Iniciar una transacción
        $pdo->beginTransaction();
        log_debug("Iniciada transacción");
        
        // Manejar la carga de la imagen si se proporciona
        // Por defecto mantener la imagen actual de la base de datos
        $imagen = $categoria_actual['imagen'] ?? null; 
        $imagen_fondo = $categoria_actual['imagen_fondo'] ?? null;
        log_debug("Imagen actual de la categoría", ['imagen' => $imagen, 'imagen_fondo' => $imagen_fondo]);
        
        // Verificar si hay una nueva imagen para subir
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK && $_FILES['imagen']['size'] > 0) {
            log_debug("Nueva imagen detectada", $_FILES['imagen']);
            
            // Validar tipo de archivo
            $fileType = $_FILES['imagen']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($fileType, $allowedTypes)) {
                log_debug("Tipo de archivo no permitido", ['tipo' => $fileType]);
                sendJsonResponse(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG, GIF y WEBP.']);
            }
            
            // Mover y procesar la imagen
            $nombre_temporal = $_FILES['imagen']['tmp_name'];
            $nombre_original = $_FILES['imagen']['name'];
            
            // Sanitizar el nombre de archivo para evitar problemas con espacios y caracteres especiales
            $nombre_sanitizado = sanitizar_nombre_archivo($nombre_original);
            $nombre_archivo = 'icono_' . $id . '_' . $nombre_sanitizado;
            
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
                
                // Como fallback, intentar el método normal de carga
                $ruta_destino = $categorias_dir . $nombre_archivo;
                
                log_debug("Intentando subir archivo con método alternativo", [
                    'origen' => $nombre_temporal, 
                    'destino' => $ruta_destino,
                    'nombre_sanitizado' => $nombre_sanitizado
                ]);
                
                // Utilizar la función auxiliar para subir el archivo con múltiples métodos
                $prefijo_log = "Categoría {$id} (icono)";
                $resultado_subida = subir_archivo_seguro($nombre_temporal, $ruta_destino, $prefijo_log);
                
                if ($resultado_subida) {
                    $imagen = 'assets/categorias/' . $nombre_archivo;
                    log_debug("Imagen cargada exitosamente", ['ruta' => $imagen]);
                } else {
                    // Mantener la imagen actual en caso de error
                    $imagen = $categoria_actual['imagen'];
                    log_debug("Error al subir imagen. Manteniendo imagen actual", ['imagen' => $imagen]);
                }
            }
        } else if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Error al subir el archivo (que no sea "no se seleccionó archivo")
            log_debug("Error en la subida de imagen", [
                'error_code' => $_FILES['imagen']['error'],
                'error_message' => getUploadErrorMessage($_FILES['imagen']['error'])
            ]);
            
            // Mantener la imagen actual en caso de error
            $imagen = $categoria_actual['imagen'];
            log_debug("Manteniendo imagen actual debido a error de carga", ['imagen' => $imagen]);
            
            // Mostrar mensaje de error pero continuar con la actualización
            if ($_FILES['imagen']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['imagen']['error'] === UPLOAD_ERR_FORM_SIZE) {
                // No interrumpir en caso de error de tamaño
                log_debug("Error de tamaño de archivo, continuando con la actualización");
            }
        } else if (isset($_POST['imagen_actual']) && !empty($_POST['imagen_actual'])) {
            // Si se proporcionó un valor para imagen_actual en el formulario, usarlo
            $imagen = $_POST['imagen_actual'];
            log_debug("Usando imagen actual desde POST", ['imagen' => $imagen]);
        } else {
            // Si no hay una nueva imagen ni valor en imagen_actual, mantener el valor de la BD
            log_debug("No se proporcionó nueva imagen, manteniendo valor en BD", ['imagen' => $imagen]);
        }
        
        // Verificar si hay una nueva imagen de fondo para subir
        if (isset($_FILES['imagen_fondo']) && $_FILES['imagen_fondo']['error'] === UPLOAD_ERR_OK && $_FILES['imagen_fondo']['size'] > 0) {
            log_debug("Nueva imagen de fondo detectada", $_FILES['imagen_fondo']);
            
            try {
                // Validar tipo de archivo
                $fileType = $_FILES['imagen_fondo']['type'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    log_debug("Tipo de archivo de fondo no permitido", ['tipo' => $fileType]);
                    sendJsonResponse(['success' => false, 'message' => 'Tipo de archivo no permitido para imagen de fondo. Solo se permiten imágenes JPG, PNG, GIF y WEBP.']);
                }
                
                // Mover y procesar la imagen de fondo
                $nombre_temporal = $_FILES['imagen_fondo']['tmp_name'];
                $nombre_original = $_FILES['imagen_fondo']['name'];
                
                // Sanitizar el nombre de archivo para evitar problemas con espacios y caracteres especiales
                $nombre_sanitizado = sanitizar_nombre_archivo($nombre_original);
                $nombre_archivo = 'bg_' . $id . '_' . $nombre_sanitizado;
                
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
                    
                    // Como fallback, intentar el método normal de carga
                    $ruta_destino = $categorias_dir . $nombre_archivo;
                    
                    log_debug("Intentando subir archivo de fondo con método alternativo", [
                        'origen' => $nombre_temporal, 
                        'destino' => $ruta_destino,
                        'nombre_sanitizado' => $nombre_sanitizado
                    ]);
                    
                    // Utilizar la función auxiliar para subir el archivo con múltiples métodos
                    $prefijo_log = "Categoría {$id} (fondo)";
                    $resultado_subida = subir_archivo_seguro($nombre_temporal, $ruta_destino, $prefijo_log);
                    
                    if ($resultado_subida) {
                        $imagen_fondo = 'assets/categorias/' . $nombre_archivo;
                        log_debug("Imagen de fondo cargada exitosamente", ['ruta' => $imagen_fondo]);
                    } else {
                        // Mantener la imagen de fondo actual en caso de error
                        $imagen_fondo = $categoria_actual['imagen_fondo'];
                        log_debug("Error al subir imagen de fondo. Manteniendo imagen actual", ['imagen_fondo' => $imagen_fondo]);
                    }
                }
                
            } catch (Exception $e) {
                log_debug("Excepción al procesar imagen de fondo", [
                    'mensaje' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Mantener la imagen actual pero continuar con la actualización
                $imagen_fondo = $categoria_actual['imagen_fondo'];
                log_debug("Manteniendo imagen de fondo actual debido a error", ['imagen_fondo' => $imagen_fondo]);
            }
        } else if (isset($_FILES['imagen_fondo']) && $_FILES['imagen_fondo']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Error al subir el archivo (que no sea "no se seleccionó archivo")
            log_debug("Error en la subida de imagen de fondo", [
                'error_code' => $_FILES['imagen_fondo']['error'],
                'error_message' => getUploadErrorMessage($_FILES['imagen_fondo']['error'])
            ]);
            
            // Mantener la imagen de fondo actual en caso de error
            $imagen_fondo = $categoria_actual['imagen_fondo'];
            log_debug("Manteniendo imagen de fondo actual debido a error de carga", ['imagen_fondo' => $imagen_fondo]);
            
            // Mostrar mensaje de error pero continuar con la actualización
            if ($_FILES['imagen_fondo']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['imagen_fondo']['error'] === UPLOAD_ERR_FORM_SIZE) {
                // No interrumpir en caso de error de tamaño
                log_debug("Error de tamaño de archivo de fondo, continuando con la actualización");
            }
        } else if (isset($_POST['imagen_fondo_actual']) && !empty($_POST['imagen_fondo_actual'])) {
            // Si se proporcionó un valor para imagen_fondo_actual en el formulario, usarlo
            $imagen_fondo = $_POST['imagen_fondo_actual'];
            log_debug("Usando imagen de fondo actual desde POST", ['imagen_fondo' => $imagen_fondo]);
        } else {
            // Si no hay una nueva imagen de fondo ni valor en imagen_fondo_actual, mantener el valor de la BD
            log_debug("No se proporcionó nueva imagen de fondo, manteniendo valor en BD", ['imagen_fondo' => $imagen_fondo]);
        }
        
        log_debug("Imagen final a usar", ['imagen' => $imagen]);
        log_debug("Imagen de fondo final a usar", ['imagen_fondo' => $imagen_fondo]);
        
        // Actualizar la categoría
        $sql = "UPDATE categorias SET nombre = ?, slug = ?, descripcion = ?, imagen = ?, imagen_fondo = ? WHERE id_categoria = ?";
        $params = [$nombre, $slug, $descripcion, $imagen, $imagen_fondo, $id];
        
        log_debug("Ejecutando actualización con los parámetros:", $params);
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($params);
        
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
                'descripcion' => $descripcion,
                'imagen' => $imagen,
                'imagen_fondo' => $imagen_fondo
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

// Función para obtener mensaje de error de carga de archivos
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return "El archivo excede el tamaño máximo permitido por PHP";
        case UPLOAD_ERR_FORM_SIZE:
            return "El archivo excede el tamaño máximo permitido por el formulario";
        case UPLOAD_ERR_PARTIAL:
            return "El archivo fue cargado parcialmente";
        case UPLOAD_ERR_NO_FILE:
            return "No se seleccionó ningún archivo";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Falta la carpeta temporal";
        case UPLOAD_ERR_CANT_WRITE:
            return "No se pudo escribir el archivo en el disco";
        case UPLOAD_ERR_EXTENSION:
            return "La carga del archivo fue detenida por una extensión de PHP";
        default:
            return "Error desconocido al cargar el archivo";
    }
}

// Función para generar un slug a partir de un texto
function generarSlug($texto, $pdo, $id) {
    // Convertir a minúsculas y reemplazar espacios por guiones
    $slug = strtolower(trim($texto));
    $slug = preg_replace('/\s+/', '-', $slug);
    // Eliminar caracteres no alfanuméricos (excepto guiones)
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    // Eliminar guiones múltiples
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Verificar si el slug ya existe
    $checkStmt = $pdo->prepare("SELECT * FROM categorias WHERE slug = ?");
    $checkStmt->execute([$slug]);
    $existingCategoria = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCategoria && $existingCategoria['id_categoria'] !== $id) {
        // Si el slug ya existe, generar uno nuevo
        $slug = $slug . '-' . uniqid();
    }
    
    return $slug;
} 