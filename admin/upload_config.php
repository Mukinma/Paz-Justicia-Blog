<?php
/**
 * Configuración centralizada para la subida de archivos
 * Este archivo contiene funciones y configuraciones comunes para la gestión de imágenes
 */

// Directorios de subida
define('ASSETS_DIR', __DIR__ . '/../assets/');
define('CATEGORIAS_DIR', __DIR__ . '/../assets/categorias/');
define('AVATARS_DIR', __DIR__ . '/../assets/avatars/');
define('POSTS_DIR', __DIR__ . '/../assets/posts/');

// Configuración de imágenes
define('MAX_IMAGEN_WIDTH', 1200);      // Ancho máximo para imágenes grandes
define('MAX_IMAGEN_HEIGHT', 1200);     // Alto máximo para imágenes grandes
define('MAX_IMAGEN_QUALITY', 80);      // Calidad de compresión (0-100)
define('ENABLE_THUMBNAILS', true);     // ¿Generar miniaturas?
define('THUMB_WIDTH', 300);            // Ancho de miniaturas
define('THUMB_HEIGHT', 300);           // Alto de miniaturas

// Tipos de archivos permitidos
$allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
];

// Extensiones permitidas y su correspondiente tipo MIME
$allowedExtensions = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

/**
 * Sanitiza el nombre de un archivo eliminando espacios y caracteres especiales
 * @param string $nombre Nombre original del archivo
 * @return string Nombre sanitizado
 */
function sanitizar_nombre_archivo($nombre) {
    // Extraer la extensión
    $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    $nombreSinExt = pathinfo($nombre, PATHINFO_FILENAME);
    
    // Reemplazar espacios por guiones
    $nombreSanitizado = str_replace(' ', '-', $nombreSinExt);
    
    // Quitar acentos y caracteres especiales
    $nombreSanitizado = preg_replace('/[^a-zA-Z0-9\-]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nombreSanitizado));
    
    // Convertir a minúsculas
    $nombreSanitizado = strtolower($nombreSanitizado);
    
    // Añadir timestamp para evitar colisiones
    $nombreSanitizado = uniqid() . '_' . $nombreSanitizado;
    
    // Devolver con la extensión
    return $nombreSanitizado . '.' . $extension;
}

/**
 * Verifica que los directorios necesarios existan y tengan permisos
 * @param array $directorios Lista de directorios a verificar
 * @return array Resultado de la verificación
 */
function verificar_directorios($directorios) {
    $resultado = ['success' => true, 'mensajes' => []];
    
    foreach ($directorios as $directorio) {
        if (!file_exists($directorio)) {
            if (!mkdir($directorio, 0777, true)) {
                $resultado['success'] = false;
                $resultado['mensajes'][] = "No se pudo crear el directorio: $directorio";
            } else {
                chmod($directorio, 0777);
                $resultado['mensajes'][] = "Directorio creado: $directorio";
            }
        } else if (!is_writable($directorio)) {
            chmod($directorio, 0777);
            $resultado['mensajes'][] = "Permisos corregidos para: $directorio";
        }
    }
    
    return $resultado;
}

/**
 * Verifica y crea las columnas necesarias en la tabla categorias
 * @param PDO $pdo Conexión a la base de datos
 * @return array Resultado de la verificación
 */
function verificar_columnas_categorias($pdo) {
    $resultado = ['success' => true, 'mensajes' => []];
    
    $columnas = ['imagen', 'imagen_fondo'];
    foreach ($columnas as $columna) {
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM categorias LIKE ?");
            $stmt->execute([$columna]);
            $existe = $stmt->rowCount() > 0;
            
            if (!$existe) {
                $afterColumn = ($columna == 'imagen') ? 'descripcion' : 'imagen';
                $sql = "ALTER TABLE categorias ADD COLUMN $columna VARCHAR(255) AFTER $afterColumn";
                $pdo->exec($sql);
                $resultado['mensajes'][] = "Columna '$columna' creada en tabla categorias";
            }
        } catch (PDOException $e) {
            $resultado['success'] = false;
            $resultado['mensajes'][] = "Error al verificar columna $columna: " . $e->getMessage();
        }
    }
    
    return $resultado;
}

/**
 * Obtiene el mensaje de error correspondiente a un código de error de subida
 * @param int $errorCode Código de error
 * @return string Mensaje de error
 */
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return "El archivo excede el tamaño máximo permitido por PHP (" . ini_get('upload_max_filesize') . ")";
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