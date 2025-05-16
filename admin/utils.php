<?php
/**
 * Genera un slug único a partir de un texto
 * @param string $texto El texto a convertir en slug
 * @param PDO $pdo Instancia de PDO para verificar duplicados
 * @param int|null $excludeId ID de categoría a excluir en la verificación de duplicados (para edición)
 * @return string El slug generado
 */
function generarSlug($texto, $pdo = null, $excludeId = null) {
    // Reemplazar acentos y caracteres especiales
    $unwanted_array = array(
        'á'=>'a', 'à'=>'a', 'ã'=>'a', 'â'=>'a', 'ä'=>'a', 'Á'=>'A', 'À'=>'A', 'Ã'=>'A', 'Â'=>'A', 'Ä'=>'A',
        'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E',
        'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I',
        'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ô'=>'o', 'ö'=>'o', 'Ó'=>'O', 'Ò'=>'O', 'Õ'=>'O', 'Ô'=>'O', 'Ö'=>'O',
        'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U',
        'ý'=>'y', 'ÿ'=>'y', 'Ý'=>'Y', 'Ÿ'=>'Y',
        'ñ'=>'n', 'Ñ'=>'N',
        'ç'=>'c', 'Ç'=>'C',
        '¿'=>'', '?'=>'', '¡'=>'', '!'=>'',
        '&'=>'y', '@'=>'a', '#'=>'', '$'=>'s',
        '€'=>'e', '£'=>'l', '¥'=>'y',
        '°'=>'', 'º'=>'', 'ª'=>'',
        '©'=>'', '®'=>'', '™'=>'',
        '|'=>'', '\\'=>'', '/'=>'',
        '['=>'', ']'=>'', '{'=>'', '}'=>'',
        '('=>'', ')'=>'', '<'=>'', '>'=>'',
        '='=>'', '+'=>'', '-'=>'', '*'=>'',
        ';'=>'', ':'=>'', '"'=>'', "'"=>'',
        ','=>'', '.'=>'', ' '=>'-'
    );
    
    // Convertir el texto
    $texto = strtr($texto, $unwanted_array);
    
    // Convertir a minúsculas
    $texto = strtolower($texto);
    
    // Reemplazar caracteres no alfanuméricos con guiones
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    
    // Eliminar guiones al inicio y final
    $texto = trim($texto, '-');
    
    // Si no hay PDO, retornar el slug básico
    if (!$pdo) {
        return $texto;
    }
    
    // Verificar duplicados y generar slug único
    $baseSlug = $texto;
    $counter = 1;
    $slug = $baseSlug;
    
    while (true) {
        $sql = "SELECT COUNT(*) FROM categorias WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId !== null) {
            $sql .= " AND id_categoria != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->fetchColumn() == 0) {
            break;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

/**
 * Valida el nombre de una categoría
 * @param string $nombre El nombre a validar
 * @return array Array con el resultado de la validación ['valid' => bool, 'message' => string]
 */
function validarNombreCategoria($nombre) {
    if (empty($nombre)) {
        return ['valid' => false, 'message' => 'El nombre de la categoría es requerido'];
    }
    
    if (strlen($nombre) < 3) {
        return ['valid' => false, 'message' => 'El nombre debe tener al menos 3 caracteres'];
    }
    
    if (strlen($nombre) > 50) {
        return ['valid' => false, 'message' => 'El nombre no puede tener más de 50 caracteres'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9\s\-_áéíóúÁÉÍÓÚñÑ]+$/', $nombre)) {
        return ['valid' => false, 'message' => 'El nombre solo puede contener letras, números, espacios, guiones y guiones bajos'];
    }
    
    return ['valid' => true];
}

// Función para quitar acentos
function quitar_acentos($texto) {
    $unwanted_array = array(
        'á'=>'a', 'à'=>'a', 'ã'=>'a', 'â'=>'a', 'ä'=>'a',
        'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e',
        'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i',
        'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ô'=>'o', 'ö'=>'o',
        'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u',
        'ý'=>'y', 'ÿ'=>'y',
        'ñ'=>'n',
        'Á'=>'A', 'À'=>'A', 'Ã'=>'A', 'Â'=>'A', 'Ä'=>'A',
        'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E',
        'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I',
        'Ó'=>'O', 'Ò'=>'O', 'Õ'=>'O', 'Ô'=>'O', 'Ö'=>'O',
        'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U',
        'Ý'=>'Y',
        'Ñ'=>'N'
    );
    return strtr($texto, $unwanted_array);
} 