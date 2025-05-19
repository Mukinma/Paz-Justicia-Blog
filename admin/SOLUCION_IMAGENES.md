# Solución para problemas con imágenes en el panel de administración

## Problema detectado

El panel de administración estaba teniendo problemas para subir y editar imágenes de categorías debido a:

1. La tabla `categorias` en la base de datos no tenía las columnas `imagen` e `imagen_fondo` necesarias para almacenar las rutas de las imágenes.
2. Había problemas de permisos en los directorios de subida.
3. Los nombres de archivos con espacios y caracteres especiales causaban errores.
4. El tamaño de los archivos subidos podía exceder el límite permitido por PHP (2MB).

## Solución implementada

Se han creado varias herramientas para diagnosticar y solucionar estos problemas:

1. **Mejoras en el código:**
   - Se agregó verificación y creación automática de las columnas `imagen` e `imagen_fondo`
   - Se implementó redimensionamiento y optimización de imágenes para reducir su tamaño
   - Se mejoró la sanitización de nombres de archivo para evitar problemas con espacios

2. **Nuevas herramientas:**
   - `admin/diagnostico.php`: Diagnostica y soluciona problemas con la base de datos, permisos y subida de archivos
   - `admin/tabla_categorias.php`: Muestra el estado actual de la tabla categorías y permite limpiar imágenes
   - `database/ejecutar_sql.php`: Ejecuta SQL para corregir la estructura de la tabla
   - `admin/upload_config.php`: Centraliza la configuración de subida de archivos

3. **Actualización de archivos existentes:**
   - `admin/editar_categoria.php`: Ahora verifica y crea las columnas necesarias
   - `admin/categorias_imagenes.php`: Mejorado con verificación de estructura de base de datos
   - `admin/insertar_categoria.php`: Actualizado para manejar mejor la subida de imágenes
   - `.htaccess`: Optimizado para permitir subida de archivos más grandes

## Cómo usar las nuevas herramientas

1. **Para diagnosticar problemas:**
   - Acceda a `http://localhost/PIP/admin/diagnostico.php`
   - Esta herramienta verificará la estructura de la base de datos, permisos y configuración PHP
   - También permite probar la subida de archivos para identificar errores

2. **Para corregir la estructura de la base de datos:**
   - Acceda a `http://localhost/PIP/database/ejecutar_sql.php`
   - Este script verificará y creará las columnas necesarias

3. **Para administrar las imágenes de categorías:**
   - Acceda a `http://localhost/PIP/admin/tabla_categorias.php`
   - Muestra el estado actual de las categorías y sus imágenes
   - Permite limpiar imágenes problemáticas

## Mantenimiento futuro

Si sigue teniendo problemas con las imágenes:

1. Verifique los permisos de los directorios:
   - `assets/`
   - `assets/categorias/`

2. Asegúrese de que PHP tiene los límites adecuados:
   - En el archivo `.htaccess` se ha configurado un límite de 20MB para subidas
   - Puede ajustarlo según sus necesidades

3. Para imágenes grandes:
   - Utilice el redimensionador automático que reduce el tamaño de las imágenes
   - Evite nombres de archivo con espacios o caracteres especiales

## Configuración actual del sistema

- Versión de PHP: 7.3.10
- Límite de subida de archivos: 20MB (configurado en .htaccess)
- Tamaño máximo de imágenes: 1200x1200 píxeles (redimensionado automáticamente)
- Directorio de imágenes: assets/categorias/ 