<?php
/**
 * Archivo de ayuda para subida de archivos
 * Implementa métodos alternativos cuando el método principal falla
 */

/**
 * Función para subir un archivo usando métodos alternativos cuando falla el método principal
 * @param string $archivo_temporal Ruta del archivo temporal
 * @param string $ruta_destino Ruta de destino para el archivo
 * @param string $prefijo_log Prefijo para los mensajes de log
 * @return bool|string Ruta relativa al archivo si se subió correctamente, false si falló
 */
function subir_archivo_seguro($archivo_temporal, $ruta_destino, $prefijo_log = "") {
    // Crear un log de lo que ocurre
    $log_file = __DIR__ . '/../upload_helper.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] {$prefijo_log} Intentando subir archivo desde {$archivo_temporal} a {$ruta_destino}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    // Asegurarse que el directorio de destino exista
    $dir_destino = dirname($ruta_destino);
    if (!file_exists($dir_destino)) {
        mkdir($dir_destino, 0777, true);
        chmod($dir_destino, 0777); // Asegurarse que tiene permisos adecuados
        file_put_contents($log_file, "[$timestamp] {$prefijo_log} Creado directorio destino: {$dir_destino}\n", FILE_APPEND);
    }
    
    $ruta_relativa = str_replace($_SERVER['DOCUMENT_ROOT'], '', $ruta_destino);
    $ruta_relativa = ltrim($ruta_relativa, "/\\");
    
    // Verificar si el archivo temporal existe
    if (!file_exists($archivo_temporal)) {
        file_put_contents($log_file, "[$timestamp] {$prefijo_log} ERROR: El archivo temporal no existe\n", FILE_APPEND);
        return false;
    }
    
    $upload_success = false;
    
    // Método 1: Intentar con move_uploaded_file (estándar)
    if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
        file_put_contents($log_file, "[$timestamp] {$prefijo_log} Éxito con move_uploaded_file\n", FILE_APPEND);
        $upload_success = true;
    } else {
        $error = error_get_last();
        file_put_contents($log_file, "[$timestamp] {$prefijo_log} Error con move_uploaded_file: " . print_r($error, true) . "\n", FILE_APPEND);
        
        // Método 2: Intentar con copy
        if (copy($archivo_temporal, $ruta_destino)) {
            file_put_contents($log_file, "[$timestamp] {$prefijo_log} Éxito con copy()\n", FILE_APPEND);
            $upload_success = true;
        } else {
            $error = error_get_last();
            file_put_contents($log_file, "[$timestamp] {$prefijo_log} Error con copy: " . print_r($error, true) . "\n", FILE_APPEND);
            
            // Método 3: Intentar con rename
            if (@rename($archivo_temporal, $ruta_destino)) {
                file_put_contents($log_file, "[$timestamp] {$prefijo_log} Éxito con rename()\n", FILE_APPEND);
                $upload_success = true;
            } else {
                $error = error_get_last();
                file_put_contents($log_file, "[$timestamp] {$prefijo_log} Error con rename: " . print_r($error, true) . "\n", FILE_APPEND);
                
                // Método 4: Último intento con file_get_contents/file_put_contents
                $contenido = @file_get_contents($archivo_temporal);
                if ($contenido !== false && @file_put_contents($ruta_destino, $contenido) !== false) {
                    file_put_contents($log_file, "[$timestamp] {$prefijo_log} Éxito con file_get/put_contents\n", FILE_APPEND);
                    $upload_success = true;
                } else {
                    $error = error_get_last();
                    file_put_contents($log_file, "[$timestamp] {$prefijo_log} Error con file_get/put_contents: " . print_r($error, true) . "\n", FILE_APPEND);
                }
            }
        }
    }
    
    if ($upload_success) {
        // Verificar que el archivo realmente existe en el destino
        if (file_exists($ruta_destino)) {
            // Asegurar permisos adecuados
            chmod($ruta_destino, 0644);
            file_put_contents($log_file, "[$timestamp] {$prefijo_log} Archivo subido exitosamente y verificado\n", FILE_APPEND);
            return $ruta_relativa;
        } else {
            file_put_contents($log_file, "[$timestamp] {$prefijo_log} ERROR: Aunque la operación de subida informó éxito, el archivo no existe en el destino\n", FILE_APPEND);
            return false;
        }
    }
    
    file_put_contents($log_file, "[$timestamp] {$prefijo_log} ERROR: Todos los métodos de subida fallaron\n", FILE_APPEND);
    return false;
}

/**
 * Función para sanitizar nombres de archivos
 * @param string $nombre_original Nombre original del archivo
 * @return string Nombre de archivo sanitizado
 */
function sanitizar_nombre_archivo($nombre_original) {
    // Obtener extensión
    $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
    
    // Sanitizar nombre base (quitar espacios y caracteres especiales)
    $nombre_base = pathinfo($nombre_original, PATHINFO_FILENAME);
    $nombre_base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre_base);
    $nombre_base = preg_replace('/_+/', '_', $nombre_base); // Evitar múltiples guiones bajos
    
    // Limitar la longitud del nombre para evitar problemas
    if (strlen($nombre_base) > 50) {
        $nombre_base = substr($nombre_base, 0, 50);
    }
    
    // Generar un nombre único basado en timestamp
    $nombre_unico = $nombre_base . '_' . time();
    
    return $nombre_unico . '.' . $extension;
} 