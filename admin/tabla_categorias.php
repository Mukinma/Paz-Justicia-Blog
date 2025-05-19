<?php
/**
 * Visualizador de la tabla categorias
 * Permite ver la estructura y contenido de la tabla
 */

// Incluir archivos necesarios
require_once '../config/db.php';
require_once 'upload_config.php';

// Verificar que el usuario está autorizado
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Acceso denegado. Debe ser administrador para ejecutar este script.');
}

// Procesar acciones
$mensaje = '';
$tipo_mensaje = 'info';

if (isset($_POST['accion'])) {
    if ($_POST['accion'] === 'crear_columnas') {
        try {
            $resultado = verificar_columnas_categorias($pdo);
            $mensaje = 'Se procesó la verificación de columnas: ' . implode(', ', $resultado['mensajes']);
            $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } elseif ($_POST['accion'] === 'limpiar_imagenes') {
        try {
            // Establecer NULL en las columnas de imagen para registros específicos
            if (isset($_POST['categorias']) && is_array($_POST['categorias'])) {
                $ids = implode(', ', array_map('intval', $_POST['categorias']));
                $stmt = $pdo->exec("UPDATE categorias SET imagen = NULL, imagen_fondo = NULL WHERE id_categoria IN ($ids)");
                $mensaje = "Se limpiaron las imágenes de " . count($_POST['categorias']) . " categorías.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "No se seleccionaron categorías para limpiar.";
                $tipo_mensaje = 'warning';
            }
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener la estructura de la tabla
$estructura = [];
$columnas_principales = ['id_categoria', 'nombre', 'slug', 'descripcion', 'imagen', 'imagen_fondo'];
$tiene_columnas_necesarias = true;

try {
    $stmt = $pdo->query("DESCRIBE categorias");
    $estructura = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar si existen las columnas necesarias
    $columnas_existentes = array_column($estructura, 'Field');
    foreach (['imagen', 'imagen_fondo'] as $col) {
        if (!in_array($col, $columnas_existentes)) {
            $tiene_columnas_necesarias = false;
            break;
        }
    }
} catch (PDOException $e) {
    $mensaje = 'Error al obtener la estructura de la tabla: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}

// Obtener los datos de la tabla
$categorias = [];
try {
    $stmt = $pdo->query("SELECT id_categoria, nombre, slug, LEFT(descripcion, 100) as descripcion_corta, imagen, imagen_fondo FROM categorias ORDER BY nombre");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = 'Error al obtener datos de la tabla: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de la Tabla Categorías</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
        }
        .success {
            background-color: #e8f5e9;
            border-left: 4px solid #4CAF50;
        }
        .warning {
            background-color: #fff8e1;
            border-left: 4px solid #FFC107;
        }
        .error {
            background-color: #ffebee;
            border-left: 4px solid #F44336;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions {
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 8px 15px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .button:hover {
            background-color: #1a252f;
        }
        .button-secondary {
            background-color: #95a5a6;
        }
        .button-warning {
            background-color: #e67e22;
        }
        .button-danger {
            background-color: #e74c3c;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .missing {
            color: red;
            font-weight: bold;
        }
        .thumbnail {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            padding: 3px;
            background: white;
        }
        .image-status-ok {
            color: green;
        }
        .image-status-error {
            color: red;
        }
    </style>
</head>
<body>
    <a href="adminControl.php" class="back-link">← Volver al Panel de Control</a>
    
    <h1>Administración de la Tabla Categorías</h1>
    
    <?php if ($mensaje): ?>
        <div class="message <?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!$tiene_columnas_necesarias): ?>
        <div class="message warning">
            <strong>Advertencia:</strong> La tabla categorías no tiene todas las columnas necesarias (imagen, imagen_fondo).
            <form method="post" action="">
                <input type="hidden" name="accion" value="crear_columnas">
                <button type="submit" class="button">Crear columnas faltantes</button>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>Estructura de la Tabla</h2>
        
        <table>
            <tr>
                <th>Campo</th>
                <th>Tipo</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
            </tr>
            <?php foreach ($estructura as $campo): ?>
                <tr>
                    <td><?php echo $campo['Field']; ?></td>
                    <td><?php echo $campo['Type']; ?></td>
                    <td><?php echo $campo['Null']; ?></td>
                    <td><?php echo $campo['Key']; ?></td>
                    <td><?php echo $campo['Default']; ?></td>
                    <td><?php echo $campo['Extra']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <div class="section">
        <h2>Datos de la Tabla</h2>
        
        <form method="post" action="">
            <table>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>Imagen de Fondo</th>
                </tr>
                <?php foreach ($categorias as $categoria): ?>
                    <?php 
                    $tiene_imagen = !empty($categoria['imagen']);
                    $tiene_imagen_fondo = !empty($categoria['imagen_fondo']);
                    $imagen_existe = $tiene_imagen && file_exists('../' . $categoria['imagen']);
                    $imagen_fondo_existe = $tiene_imagen_fondo && file_exists('../' . $categoria['imagen_fondo']);
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="categorias[]" value="<?php echo $categoria['id_categoria']; ?>" class="categoria-checkbox">
                        </td>
                        <td><?php echo $categoria['id_categoria']; ?></td>
                        <td><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($categoria['slug']); ?></td>
                        <td><?php echo htmlspecialchars($categoria['descripcion_corta']); ?></td>
                        <td>
                            <?php if ($tiene_imagen): ?>
                                <?php if ($imagen_existe): ?>
                                    <img src="../<?php echo $categoria['imagen']; ?>" alt="Imagen de <?php echo htmlspecialchars($categoria['nombre']); ?>" class="thumbnail">
                                    <div class="image-status-ok">✓ Imagen existe</div>
                                <?php else: ?>
                                    <div class="image-status-error">❌ La imagen no existe en la ruta:</div>
                                    <div><?php echo htmlspecialchars($categoria['imagen']); ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="missing">No tiene imagen asignada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tiene_imagen_fondo): ?>
                                <?php if ($imagen_fondo_existe): ?>
                                    <img src="../<?php echo $categoria['imagen_fondo']; ?>" alt="Fondo de <?php echo htmlspecialchars($categoria['nombre']); ?>" class="thumbnail">
                                    <div class="image-status-ok">✓ Imagen de fondo existe</div>
                                <?php else: ?>
                                    <div class="image-status-error">❌ La imagen de fondo no existe en la ruta:</div>
                                    <div><?php echo htmlspecialchars($categoria['imagen_fondo']); ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="missing">No tiene imagen de fondo asignada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <div class="actions">
                <input type="hidden" name="accion" value="limpiar_imagenes">
                <button type="submit" class="button button-danger" onclick="return confirm('¿Está seguro de que desea limpiar las imágenes de las categorías seleccionadas? Esta acción no puede deshacerse.');">Limpiar imágenes de seleccionadas</button>
            </div>
        </form>
    </div>
    
    <a href="adminControl.php" class="back-link">← Volver al Panel de Control</a>
    
    <script>
        // Script para seleccionar/deseleccionar todos los checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.categoria-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = document.getElementById('select-all').checked;
            });
        });
    </script>
</body>
</html> 