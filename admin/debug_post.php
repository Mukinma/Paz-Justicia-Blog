<?php
// Este script es una herramienta para diagnosticar problemas con la actualización de posts
// NOTA: Elimina este archivo en producción una vez resuelto el problema

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Diagnóstico de Edición de Posts</h1>";

session_start();
require '../config/db.php';

// Verificar si hay un post_id en la URL
$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;

echo "<h2>Información de Configuración</h2>";
echo "<p>Driver PDO: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "</p>";
echo "<p>Versión de la base de datos: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "</p>";
echo "<p>Estado de la conexión: " . ($pdo ? "Activa" : "Inactiva") . "</p>";

if ($post_id) {
    echo "<h2>Información del Post ID: $post_id</h2>";
    
    try {
        // Verificar si el post existe
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id_post = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            echo "<h3>Datos del Post</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            
            foreach ($post as $campo => $valor) {
                echo "<tr>";
                echo "<td>$campo</td>";
                if ($campo === 'contenido') {
                    echo "<td>" . htmlspecialchars(substr($valor, 0, 100)) . "...</td>";
                } else {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Verificar imágenes
            if ($post['id_imagen_destacada']) {
                echo "<h3>Imagen Destacada (ID: {$post['id_imagen_destacada']})</h3>";
                
                $stmt_img = $pdo->prepare("SELECT * FROM imagenes WHERE id_imagen = ?");
                $stmt_img->execute([$post['id_imagen_destacada']]);
                $imagen = $stmt_img->fetch(PDO::FETCH_ASSOC);
                
                if ($imagen) {
                    echo "<p>La imagen destacada existe en la base de datos.</p>";
                    echo "<p>Ruta: " . htmlspecialchars($imagen['ruta']) . "</p>";
                    
                    // Verificar si el archivo existe físicamente
                    $rutaFisica = $imagen['ruta'];
                    if (strpos($rutaFisica, '../') === 0) {
                        $rutaFisica = substr($rutaFisica, 3);
                    }
                    
                    if (file_exists($rutaFisica)) {
                        echo "<p>El archivo de imagen existe físicamente. ✓</p>";
                        echo "<p><img src='" . htmlspecialchars($imagen['ruta']) . "' height='150'></p>";
                    } else {
                        echo "<p style='color:red'>¡Advertencia! El archivo de imagen NO existe físicamente: $rutaFisica</p>";
                    }
                } else {
                    echo "<p style='color:red'>¡Error! La imagen destacada con ID {$post['id_imagen_destacada']} no existe en la tabla imagenes.</p>";
                }
            } else {
                echo "<p>Este post no tiene imagen destacada asociada.</p>";
            }
            
            if ($post['id_imagen_background']) {
                echo "<h3>Imagen de Fondo (ID: {$post['id_imagen_background']})</h3>";
                
                $stmt_bg = $pdo->prepare("SELECT * FROM imagenes WHERE id_imagen = ?");
                $stmt_bg->execute([$post['id_imagen_background']]);
                $imagen_bg = $stmt_bg->fetch(PDO::FETCH_ASSOC);
                
                if ($imagen_bg) {
                    echo "<p>La imagen de fondo existe en la base de datos.</p>";
                    echo "<p>Ruta: " . htmlspecialchars($imagen_bg['ruta']) . "</p>";
                    
                    // Verificar si el archivo existe físicamente
                    $rutaFisica = $imagen_bg['ruta'];
                    if (strpos($rutaFisica, '../') === 0) {
                        $rutaFisica = substr($rutaFisica, 3);
                    }
                    
                    if (file_exists($rutaFisica)) {
                        echo "<p>El archivo de imagen existe físicamente. ✓</p>";
                        echo "<p><img src='" . htmlspecialchars($imagen_bg['ruta']) . "' height='150'></p>";
                    } else {
                        echo "<p style='color:red'>¡Advertencia! El archivo de imagen NO existe físicamente: $rutaFisica</p>";
                    }
                } else {
                    echo "<p style='color:red'>¡Error! La imagen de fondo con ID {$post['id_imagen_background']} no existe en la tabla imagenes.</p>";
                }
            } else {
                echo "<p>Este post no tiene imagen de fondo asociada.</p>";
            }
            
            // Verificar categoría
            if ($post['id_categoria']) {
                echo "<h3>Categoría (ID: {$post['id_categoria']})</h3>";
                
                $stmt_cat = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
                $stmt_cat->execute([$post['id_categoria']]);
                $categoria = $stmt_cat->fetch(PDO::FETCH_ASSOC);
                
                if ($categoria) {
                    echo "<p>La categoría existe en la base de datos: " . htmlspecialchars($categoria['nombre']) . " ✓</p>";
                } else {
                    echo "<p style='color:red'>¡Error! La categoría con ID {$post['id_categoria']} no existe en la tabla categorias.</p>";
                }
            } else {
                echo "<p style='color:red'>Este post no tiene categoría asignada.</p>";
            }
            
            // Ver últimas actualizaciones
            echo "<h3>Historial de Ediciones (últimas 10)</h3>";
            
            $sql_log = "SELECT * FROM error_log WHERE message LIKE '%post ID: $post_id%' OR message LIKE '%Iniciando actualización del post ID: $post_id%' ORDER BY id DESC LIMIT 10";
            $stmt_log = $pdo->query($sql_log);
            $logs = $stmt_log->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($logs) > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Fecha</th><th>Mensaje</th></tr>";
                
                foreach ($logs as $log) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($log['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($log['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($log['message']) . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>No se encontraron registros de edición para este post.</p>";
            }
            
        } else {
            echo "<p style='color:red'>No se encontró ningún post con el ID $post_id.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error en la base de datos: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>Test de Permisos</h3>";
    echo "<p>Este servidor está ejecutándose como: " . exec('whoami') . "</p>";
    
    // Test de escritura en directorio de imágenes
    $uploadDir = '../assets/';
    echo "<p>Directorio de carga: $uploadDir</p>";
    
    if (file_exists($uploadDir)) {
        echo "<p>El directorio existe ✓</p>";
        if (is_writable($uploadDir)) {
            echo "<p>El directorio tiene permisos de escritura ✓</p>";
        } else {
            echo "<p style='color:red'>¡Advertencia! El directorio NO tiene permisos de escritura.</p>";
        }
    } else {
        echo "<p style='color:red'>¡Error! El directorio de carga no existe.</p>";
    }
    
    // Test de actualización en la base de datos
    echo "<h3>Test de Actualización en Base de Datos</h3>";
    echo "<p>Probando permiso de actualización (sin hacer cambios reales)...</p>";
    
    try {
        $pdo->beginTransaction();
        $stmt_test = $pdo->prepare("UPDATE posts SET fecha_actualizacion = fecha_actualizacion WHERE id_post = ?");
        $resultado = $stmt_test->execute([$post_id]);
        $pdo->rollBack();
        
        if ($resultado) {
            echo "<p>El test de actualización fue exitoso. El servidor tiene permisos para actualizar registros. ✓</p>";
        } else {
            echo "<p style='color:red'>El test de actualización falló. Error: " . implode(", ", $stmt_test->errorInfo()) . "</p>";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<p style='color:red'>Error en el test de actualización: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>No se ha especificado un ID de post para diagnosticar.</p>";
    echo "<p>Uso: debug_post.php?id=X donde X es el ID del post a diagnosticar.</p>";
    
    // Mostrar lista de posts
    echo "<h2>Posts Disponibles</h2>";
    try {
        $stmt = $pdo->query("SELECT id_post, titulo, fecha_actualizacion FROM posts ORDER BY fecha_actualizacion DESC LIMIT 20");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($posts) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Título</th><th>Última Modificación</th><th>Acción</th></tr>";
            
            foreach ($posts as $post) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($post['id_post']) . "</td>";
                echo "<td>" . htmlspecialchars($post['titulo']) . "</td>";
                echo "<td>" . htmlspecialchars($post['fecha_actualizacion']) . "</td>";
                echo "<td><a href='?id=" . $post['id_post'] . "'>Diagnosticar</a></td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No hay posts en la base de datos.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error al listar posts: " . $e->getMessage() . "</p>";
    }
}

// Footer
echo "<hr>";
echo "<p>Esta es una herramienta de diagnóstico. Por seguridad, elimine este archivo una vez resuelto el problema.</p>";
?> 