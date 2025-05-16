-- Script para instalar la función quitar_acentos
-- Ejecutar este script en cada base de datos donde se necesite la función

DELIMITER $$

-- Eliminar la función si ya existe para evitar errores
DROP FUNCTION IF EXISTS `quitar_acentos`$$

-- Crear la función
CREATE FUNCTION `quitar_acentos` (`texto` VARCHAR(255)) 
RETURNS VARCHAR(255) CHARSET utf8 COLLATE utf8_spanish2_ci 
BEGIN
    DECLARE resultado VARCHAR(255);
    SET resultado = texto;
    SET resultado = REPLACE(resultado, 'á', 'a');
    SET resultado = REPLACE(resultado, 'é', 'e');
    SET resultado = REPLACE(resultado, 'í', 'i');
    SET resultado = REPLACE(resultado, 'ó', 'o');
    SET resultado = REPLACE(resultado, 'ú', 'u');
    SET resultado = REPLACE(resultado, 'Á', 'A');
    SET resultado = REPLACE(resultado, 'É', 'E');
    SET resultado = REPLACE(resultado, 'Í', 'I');
    SET resultado = REPLACE(resultado, 'Ó', 'O');
    SET resultado = REPLACE(resultado, 'Ú', 'U');
    SET resultado = REPLACE(resultado, 'ñ', 'n');
    SET resultado = REPLACE(resultado, 'Ñ', 'N');
    RETURN resultado;
END$$

DELIMITER ;

-- Mensaje de confirmación
SELECT 'Función quitar_acentos instalada correctamente' AS Mensaje; 