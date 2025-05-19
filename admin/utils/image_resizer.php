<?php
/**
 * Script para redimensionar y optimizar imágenes antes de subirlas
 * Esto ayuda a reducir el tamaño de las imágenes para cumplir con el límite de 2MB
 */

/**
 * Función para redimensionar imágenes manteniendo la relación de aspecto
 * @param string $source_path Ruta del archivo temporal
 * @param string $destination_path Ruta de destino
 * @param int $max_width Ancho máximo (por defecto 1200px)
 * @param int $max_height Alto máximo (por defecto 1200px)
 * @param int $quality Calidad de la imagen (0-100, por defecto 80)
 * @param bool $create_thumb Crear también una miniatura
 * @return bool True si la operación fue exitosa, False en caso contrario
 */
function resize_and_save_image($source_path, $destination_path, $max_width = 1200, $max_height = 1200, $quality = 80, $create_thumb = false) {
    // Verificar si GD está instalado
    if (!extension_loaded('gd')) {
        error_log("La extensión GD no está instalada. No se puede redimensionar la imagen.");
        return false;
    }
    
    // Verificar que el archivo existe
    if (!file_exists($source_path)) {
        error_log("El archivo de origen no existe: $source_path");
        return false;
    }
    
    // Crear directorio destino si no existe
    $destination_dir = dirname($destination_path);
    if (!file_exists($destination_dir)) {
        mkdir($destination_dir, 0777, true);
    }
    
    // Obtener información del archivo
    $file_info = getimagesize($source_path);
    if ($file_info === false) {
        error_log("No se pudo obtener información de la imagen: $source_path");
        return false;
    }
    
    $mime_type = $file_info['mime'];
    
    // Crear recurso de imagen según el tipo de archivo
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $source_image = imagecreatefromwebp($source_path);
            break;
        default:
            error_log("Tipo de imagen no soportado: $mime_type");
            return false;
    }
    
    if (!$source_image) {
        error_log("No se pudo crear el recurso de imagen desde: $source_path");
        return false;
    }
    
    // Obtener dimensiones originales
    $original_width = imagesx($source_image);
    $original_height = imagesy($source_image);
    
    // Calcular nuevas dimensiones manteniendo la relación de aspecto
    if ($original_width > $max_width || $original_height > $max_height) {
        $ratio = min($max_width / $original_width, $max_height / $original_height);
        $new_width = round($original_width * $ratio);
        $new_height = round($original_height * $ratio);
    } else {
        // Si la imagen es más pequeña que las dimensiones máximas, mantenerla igual
        $new_width = $original_width;
        $new_height = $original_height;
    }
    
    // Crear una nueva imagen con las dimensiones calculadas
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Manejar transparencia para PNG y GIF
    if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
        // Desactivar el color de fondo
        imagealphablending($new_image, false);
        // Guardar la información de transparencia
        imagesavealpha($new_image, true);
        // Color transparente
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        // Rellenar el fondo con transparencia
        imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Redimensionar la imagen
    imagecopyresampled(
        $new_image, $source_image,
        0, 0, 0, 0,
        $new_width, $new_height, $original_width, $original_height
    );
    
    // Guardar la imagen según el tipo
    $result = false;
    
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($new_image, $destination_path, $quality);
            break;
        case 'image/png':
            // Para PNG, la calidad es de 0-9, así que convertimos el rango
            $png_quality = round(9 - (($quality / 100) * 9));
            $result = imagepng($new_image, $destination_path, $png_quality);
            break;
        case 'image/gif':
            $result = imagegif($new_image, $destination_path);
            break;
        case 'image/webp':
            $result = imagewebp($new_image, $destination_path, $quality);
            break;
    }
    
    // Crear miniatura si se solicita
    if ($create_thumb && $result) {
        $thumb_width = 300;
        $thumb_height = 300;
        $thumb_path = dirname($destination_path) . '/thumb_' . basename($destination_path);
        
        $thumb_ratio = min($thumb_width / $original_width, $thumb_height / $original_height);
        $thumb_new_width = round($original_width * $thumb_ratio);
        $thumb_new_height = round($original_height * $thumb_ratio);
        
        $thumb_image = imagecreatetruecolor($thumb_new_width, $thumb_new_height);
        
        // Manejar transparencia para PNG y GIF en la miniatura
        if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
            imagealphablending($thumb_image, false);
            imagesavealpha($thumb_image, true);
            $transparent = imagecolorallocatealpha($thumb_image, 255, 255, 255, 127);
            imagefilledrectangle($thumb_image, 0, 0, $thumb_new_width, $thumb_new_height, $transparent);
        }
        
        imagecopyresampled(
            $thumb_image, $source_image,
            0, 0, 0, 0,
            $thumb_new_width, $thumb_new_height, $original_width, $original_height
        );
        
        switch ($mime_type) {
            case 'image/jpeg':
                imagejpeg($thumb_image, $thumb_path, $quality);
                break;
            case 'image/png':
                imagepng($thumb_image, $thumb_path, $png_quality);
                break;
            case 'image/gif':
                imagegif($thumb_image, $thumb_path);
                break;
            case 'image/webp':
                imagewebp($thumb_image, $thumb_path, $quality);
                break;
        }
        
        // Liberar memoria
        imagedestroy($thumb_image);
    }
    
    // Liberar memoria
    imagedestroy($source_image);
    imagedestroy($new_image);
    
    return $result;
}

/**
 * Procesar archivo desde $_FILES y crear versión optimizada
 * @param array $file_data Datos del archivo desde $_FILES
 * @param string $destination_dir Directorio de destino
 * @param string $new_filename Nuevo nombre de archivo (opcional)
 * @return array Información sobre el resultado de la operación
 */
function process_uploaded_image($file_data, $destination_dir, $new_filename = null) {
    $result = [
        'success' => false,
        'error' => '',
        'original_size' => 0,
        'new_size' => 0,
        'path' => '',
        'filename' => ''
    ];
    
    // Verificar que el archivo es válido
    if (!isset($file_data['tmp_name']) || !file_exists($file_data['tmp_name'])) {
        $result['error'] = 'Archivo temporal no existe o no es válido';
        return $result;
    }
    
    // Verificar que es una imagen
    $file_info = getimagesize($file_data['tmp_name']);
    if ($file_info === false) {
        $result['error'] = 'El archivo no es una imagen válida';
        return $result;
    }
    
    // Registrar el tamaño original
    $result['original_size'] = filesize($file_data['tmp_name']);
    
    // Determinar el nombre de archivo
    if ($new_filename === null) {
        // Usar un nombre basado en timestamp
        $extension = pathinfo($file_data['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $extension;
    }
    
    // Asegurar que el directorio destino existe
    if (!file_exists($destination_dir)) {
        mkdir($destination_dir, 0777, true);
    }
    
    $destination_path = $destination_dir . '/' . $new_filename;
    
    // Redimensionar y guardar la imagen
    if (resize_and_save_image($file_data['tmp_name'], $destination_path, 1200, 1200, 80, true)) {
        $result['success'] = true;
        $result['path'] = $destination_path;
        $result['filename'] = $new_filename;
        $result['new_size'] = filesize($destination_path);
    } else {
        $result['error'] = 'Error al procesar la imagen';
    }
    
    return $result;
} 