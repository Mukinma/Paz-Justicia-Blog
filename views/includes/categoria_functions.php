<?php
/**
 * Funciones para el manejo de categorías y posts
 */

/**
 * Obtiene posts por categoría
 * 
 * @param PDO $pdo Conexión a la base de datos
 * @param int $id_categoria ID de la categoría
 * @param int $limit Límite de posts a obtener
 * @param int $offset Desde qué registro comenzar
 * @return array Posts de la categoría
 */
function obtenerPostsPorCategoria($pdo, $id_categoria, $limit = 6, $offset = 0) {
    try {
        $sql = "SELECT p.id_post, p.titulo, p.resumen, p.fecha_publicacion, p.slug,
                      i.ruta AS imagen, 
                      u.name AS autor
               FROM posts p
               LEFT JOIN imagenes i ON p.id_imagen_destacada = i.id_imagen
               LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
               WHERE p.id_categoria = :id_categoria AND p.estado = 'publicado'
               ORDER BY p.fecha_publicacion DESC
               LIMIT :offset, :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener posts: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los detalles de una categoría por su slug
 * 
 * @param PDO $pdo Conexión a la base de datos
 * @param string $slug Slug de la categoría
 * @return array|null Datos de la categoría o null si no existe
 */
function obtenerCategoriaPorSlug($pdo, $slug) {
    try {
        $sql = "SELECT c.id_categoria, c.nombre, c.descripcion, c.imagen, c.slug, 
                      COUNT(p.id_post) AS total_posts
               FROM categorias c
               LEFT JOIN posts p ON c.id_categoria = p.id_categoria AND p.estado = 'publicado'
               WHERE c.slug = :slug
               GROUP BY c.id_categoria, c.nombre, c.descripcion, c.imagen, c.slug";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener categoría: " . $e->getMessage());
        return false;
    }
}

/**
 * Formatea la fecha para mostrar en el frontend
 * 
 * @param string $fecha Fecha en formato MySQL
 * @return string Fecha formateada
 */
function formatearFecha($fecha) {
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
    $timestamp = strtotime($fecha);
    return strftime('%d %b %Y', $timestamp);
}

/**
 * Verifica la sesión y devuelve HTML para la sección de perfil
 * 
 * @return string HTML para la sección de perfil
 */
function getProfileSectionHTML() {
    session_start();
    
    if (!isset($_SESSION['usuario'])) {
        return '<a href="../admin/usuario.php" class="login-btn">Iniciar Sesión</a>';
    } else {
        $html = '<div class="profile-dropdown">
                  <button class="profile-btn">';
        
        if (!empty($_SESSION['avatar']) && file_exists('../' . $_SESSION['avatar'])) {
            $html .= '<img src="../' . htmlspecialchars($_SESSION['avatar']) . '" alt="Foto de perfil">';
        } else {
            $html .= '<i class="fas fa-user-circle"></i>';
        }
        
        $html .= '</button>
                  <div class="dropdown-content">
                      <a href="../admin/perfil.php"><i class="fas fa-user"></i> Perfil</a>';
        
        if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
            $html .= '<a href="../admin/adminControl.php"><i class="fas fa-cog"></i> Admin</a>';
        }
        
        $html .= '<a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                  </div>
                </div>';
        
        return $html;
    }
}
?> 