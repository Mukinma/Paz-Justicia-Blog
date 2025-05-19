-- Script para añadir las columnas imagen e imagen_fondo a la tabla categorias

-- Verificar si la columna 'imagen' existe, y si no, crearla
SET @existeImagen = (SELECT COUNT(*) FROM information_schema.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'categorias' 
                    AND COLUMN_NAME = 'imagen');

SET @sqlImagen = IF(@existeImagen = 0,
               'ALTER TABLE categorias ADD COLUMN imagen VARCHAR(255) AFTER descripcion',
               'SELECT "La columna imagen ya existe" as mensaje');

PREPARE stmt FROM @sqlImagen;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar si la columna 'imagen_fondo' existe, y si no, crearla
SET @existeImagenFondo = (SELECT COUNT(*) FROM information_schema.COLUMNS 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'categorias' 
                         AND COLUMN_NAME = 'imagen_fondo');

SET @sqlImagenFondo = IF(@existeImagenFondo = 0,
                    'ALTER TABLE categorias ADD COLUMN imagen_fondo VARCHAR(255) AFTER imagen',
                    'SELECT "La columna imagen_fondo ya existe" as mensaje');

PREPARE stmt FROM @sqlImagenFondo;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mostrar la estructura actual de la tabla
DESCRIBE categorias; 