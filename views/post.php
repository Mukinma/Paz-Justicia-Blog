<?php
// Iniciar sesión para manejar el estado de "me gusta"
session_start();

// Requerir archivo de conexión a la base de datos
require_once '../config/db.php';

// Verificar si se ha proporcionado un ID de post
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirigir a la página principal si no hay ID
    header('Location: ../index.php');
    exit();
}

$post_id = intval($_GET['id']);

// Consulta para obtener información del post
$sql = "SELECT p.*, c.nombre as categoria_nombre, c.slug as categoria_slug, u.name as autor_nombre, 
        i1.ruta as imagen_destacada, i2.ruta as imagen_background 
        FROM posts p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
        LEFT JOIN imagenes i1 ON p.id_imagen_destacada = i1.id_imagen
        LEFT JOIN imagenes i2 ON p.id_imagen_background = i2.id_imagen
        WHERE p.id_post = ? AND p.estado = 'publicado'";
        
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);

// Verificar si el post existe
if ($stmt->rowCount() === 0) {
    // Redirigir si el post no existe o no está publicado
    header('Location: ../index.php');
    exit();
}

// Obtener datos del post
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Formatear la fecha
$fecha_formateada = date('d/m/Y', strtotime($post['fecha_publicacion']));

// Obtener posts relacionados (de la misma categoría)
$sql_relacionados = "SELECT p.id_post, p.titulo, p.resumen, i.ruta as imagen_destacada 
                    FROM posts p
                    LEFT JOIN imagenes i ON p.id_imagen_destacada = i.id_imagen
                    WHERE p.id_categoria = ? AND p.id_post != ? AND p.estado = 'publicado' 
                    ORDER BY p.fecha_publicacion DESC 
                    LIMIT 5";
$stmt_relacionados = $pdo->prepare($sql_relacionados);
$stmt_relacionados->execute([$post['id_categoria'], $post_id]);
$posts_relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener comentarios aprobados
$sql_comentarios = "SELECT c.*, u.name as nombre_usuario, u.avatar 
                    FROM comentarios c 
                    LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario 
                    WHERE c.id_post = ? AND c.aprobado = 1 
                    ORDER BY c.fecha_comentario DESC";
$stmt_comentarios = $pdo->prepare($sql_comentarios);
$stmt_comentarios->execute([$post_id]);
$comentarios = $stmt_comentarios->fetchAll(PDO::FETCH_ASSOC);

// Verificar si el usuario ha dado "me gusta"
$is_liked = false;
if (isset($_SESSION['id_usuario'])) {
    // Verificar primero si la tabla existe para evitar errores
    $sql_check_table = "SHOW TABLES LIKE 'post_likes'";
    $stmt_check_table = $pdo->prepare($sql_check_table);
    $stmt_check_table->execute();
    
    if ($stmt_check_table->rowCount() > 0) {
        $sql_check_like = "SELECT * FROM post_likes WHERE id_post = ? AND id_usuario = ?";
        $stmt_check_like = $pdo->prepare($sql_check_like);
        $stmt_check_like->execute([$post_id, $_SESSION['id_usuario']]);
        $is_liked = $stmt_check_like->rowCount() > 0;
    }
}

// Obtener el número de "me gusta"
$likes_count = 0;
$sql_check_table = "SHOW TABLES LIKE 'post_likes'";
$stmt_check_table = $pdo->prepare($sql_check_table);
$stmt_check_table->execute();

if ($stmt_check_table->rowCount() > 0) {
    $sql_likes = "SELECT COUNT(*) as likes FROM post_likes WHERE id_post = ?";
    $stmt_likes = $pdo->prepare($sql_likes);
    $stmt_likes->execute([$post_id]);
    $likes_count = $stmt_likes->fetch(PDO::FETCH_ASSOC)['likes'];
}

// Validación y depuración de rutas de imágenes
$background_image = '';
if (!empty($post['imagen_background'])) {
    $background_image = $post['imagen_background'];
    // Si la ruta ya incluye "../assets/", eliminamos el "../" al verificar la existencia
    $ruta_check = $background_image;
    if (strpos($ruta_check, '../') === 0) {
        $ruta_check = substr($ruta_check, 3);
    }
    if (!file_exists("../{$ruta_check}")) {
        error_log("Imagen de fondo no encontrada: ../{$ruta_check}");
        $background_image = '';
    }
}

if (empty($background_image) && !empty($post['imagen_destacada'])) {
    $background_image = $post['imagen_destacada'];
    // Si la ruta ya incluye "../assets/", eliminamos el "../" al verificar la existencia
    $ruta_check = $background_image;
    if (strpos($ruta_check, '../') === 0) {
        $ruta_check = substr($ruta_check, 3);
    }
    if (!file_exists("../{$ruta_check}")) {
        error_log("Imagen destacada (usada como fondo) no encontrada: ../{$ruta_check}");
        $background_image = '';
    }
}

// Usar una imagen predeterminada si no hay imágenes válidas
if (empty($background_image)) {
    $background_image = 'assets/fondopeace.jpg';
    if (!file_exists("../{$background_image}")) {
        error_log("Imagen predeterminada no encontrada: ../{$background_image}");
        $background_image = '';
    }
}

// Eliminamos "../" al principio si existe para evitar doble ruta
if (strpos($background_image, '../') === 0) {
    $background_image = substr($background_image, 3);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['titulo']); ?> - Peace In Progress</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/post_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    // Mostrar notificaciones si existen en la sesión
    if (isset($_SESSION['success'])) {
        echo '<div class="notification success"><i class="fas fa-check-circle"></i>' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="notification error"><i class="fas fa-exclamation-circle"></i>' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <header>
        <img src="../assets/logo.png" alt="Logo Peace In Progress" class="logo">

        <div class="search-bar">
            <input type="text" placeholder="Buscar artículos...">
            <i class="fas fa-search search-icon"></i>
        </div>

        <nav>
            <a href="../index.php">Inicio</a>
            <a href="#">Categorías</a>
            <a href="#">Contacto</a>
        </nav>
    </header>

    <div class="article-header" style="background-image: url('../<?php echo htmlspecialchars($background_image); ?>');">
        <h1><?php echo htmlspecialchars($post['titulo']); ?></h1>
    </div>

    <div class="container">
        <div class="main-content">
            <div class="post-meta">
                <span class="category"><?php echo htmlspecialchars($post['categoria_nombre']); ?></span>
                <span><i class="far fa-clock"></i> <?php echo $fecha_formateada; ?></span>
                <span><i class="far fa-user"></i> <?php echo htmlspecialchars($post['autor_nombre']); ?></span>
            </div>
            
            <div class="post-content">
                <?php echo $post['contenido']; ?>
            </div>
            
            <?php
            $mostrar_imagen_destacada = false;
            if (!empty($post['imagen_destacada']) && $post['imagen_destacada'] != $background_image) {
                $ruta_img = $post['imagen_destacada'];
                // Si la ruta ya incluye "../assets/", eliminamos el "../" al verificar la existencia
                if (strpos($ruta_img, '../') === 0) {
                    $ruta_img = substr($ruta_img, 3);
                }
                if (file_exists("../{$ruta_img}")) {
                    $mostrar_imagen_destacada = true;
                } else {
                    error_log("Imagen destacada no encontrada: ../{$ruta_img}");
                }
            }
            ?>
            
            <?php if ($mostrar_imagen_destacada): ?>
            <?php 
            // Eliminamos "../" al principio si existe para evitar doble ruta
            $ruta_img_destacada = $post['imagen_destacada'];
            if (strpos($ruta_img_destacada, '../') === 0) {
                $ruta_img_destacada = substr($ruta_img_destacada, 3);
            }
            ?>
            <figure>
                <img src="../<?php echo htmlspecialchars($ruta_img_destacada); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>" class="featured-image">
                <figcaption>Imagen destacada: <?php echo htmlspecialchars($post['titulo']); ?></figcaption>
            </figure>
            <?php endif; ?>

            <div class="interaction-buttons">
                <div class="like-button <?php echo $is_liked ? 'liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                    <i class="<?php echo $is_liked ? 'fas' : 'far'; ?> fa-heart"></i>
                    <span class="like-count"><?php echo $likes_count; ?></span>
                </div>
                <div class="share-button">
                    <i class="fas fa-share-alt"></i>
                    <span>Compartir</span>
                </div>
            </div>

            <a href="../index.php" class="back">← Volver al inicio</a>
            
            <!-- Sección de comentarios -->
            <div class="comments-section">
                <h3>Comentarios (<?php echo count($comentarios); ?>)</h3>
                
                <?php if (count($comentarios) > 0): ?>
                <div class="comments-list">
                    <?php foreach ($comentarios as $comentario): ?>
                    <div class="comment">
                        <div class="comment-avatar">
                            <?php if (!empty($comentario['avatar']) && file_exists('../' . $comentario['avatar'])): ?>
                                <img src="../<?php echo htmlspecialchars($comentario['avatar']); ?>" alt="Avatar">
                            <?php else: ?>
                                <img src="../assets/profile-icon.svg" alt="Avatar">
                            <?php endif; ?>
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo htmlspecialchars($comentario['nombre_usuario'] ?? 'Anónimo'); ?></span>
                                <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comentario['fecha_comentario'])); ?></span>
                            </div>
                            <div class="comment-text">
                                <?php echo htmlspecialchars($comentario['contenido']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="no-comments">
                    <p>No hay comentarios todavía. ¡Sé el primero en comentar!</p>
                </div>
                <?php endif; ?>
                
                <!-- Formulario para añadir comentarios -->
                <div class="comment-form">
                    <h4>Deja un comentario</h4>
                    <?php if (isset($_SESSION['usuario'])): ?>
                    <form action="../actions/add_comment.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                        <textarea name="comment" placeholder="Escribe tu comentario..." required></textarea>
                        <button type="submit" class="submit-comment">Enviar comentario</button>
                    </form>
                    <?php else: ?>
                    <div class="login-prompt">
                        <p>Debes <a href="../admin/usuario.php">iniciar sesión</a> para comentar.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="card">
                <h3>Sobre el autor</h3>
                <p><?php echo htmlspecialchars($post['autor_nombre']); ?></p>
            </div>

            <div class="card">
                <h3>Artículos relacionados</h3>
                <div class="carousel">
                    <?php foreach ($posts_relacionados as $relacionado): ?>
                    <?php 
                    $mostrar_imagen_relacionada = false;
                    if (!empty($relacionado['imagen_destacada'])) {
                        $ruta_rel = $relacionado['imagen_destacada'];
                        // Si la ruta ya incluye "../assets/", eliminamos el "../" al verificar la existencia
                        if (strpos($ruta_rel, '../') === 0) {
                            $ruta_rel = substr($ruta_rel, 3);
                        }
                        if (file_exists("../{$ruta_rel}")) {
                            $mostrar_imagen_relacionada = true;
                        } else {
                            error_log("Imagen de post relacionado no encontrada: ../{$ruta_rel}");
                        }
                    }
                    ?>
                    <div class="carousel-card">
                        <?php if ($mostrar_imagen_relacionada): ?>
                        <?php 
                        // Eliminamos "../" al principio si existe para evitar doble ruta
                        $ruta_img_rel = $relacionado['imagen_destacada'];
                        if (strpos($ruta_img_rel, '../') === 0) {
                            $ruta_img_rel = substr($ruta_img_rel, 3);
                        }
                        ?>
                        <div class="carousel-img" style="background-image: url('../<?php echo htmlspecialchars($ruta_img_rel); ?>');"></div>
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($relacionado['titulo']); ?></h4>
                        <p><?php echo substr(htmlspecialchars($relacionado['resumen']), 0, 50) . '...'; ?></p>
                        <a href="post.php?id=<?php echo $relacionado['id_post']; ?>">Leer más</a>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($posts_relacionados)): ?>
                    <p>No hay artículos relacionados disponibles.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
            </div>
    </div>

    <div id="message" class="message">Enlace copiado al portapapeles</div>

    <footer class="footer">
        <div class="container-footer">
                <div class="menu-footer">
                    <div class="contact-info">
                        <p class="title-footer">Información de Contacto</p>
                        <ul>
                        <li>Dirección: 123 Calle Principal, Ciudad</li>
                        <li>Teléfono: 123-456-789</li>
                        <li>Email: info@peaceinprogress.com</li>
                        </ul>
                    </div>
                    <div class="information">
                        <p class="title-footer">Información</p>
                        <ul>
                            <li><a href="#">Acerca de Nosotros</a></li>
                        <li><a href="#">Información de Envío</a></li>
                        <li><a href="#">Políticas de Privacidad</a></li>
                        <li><a href="#">Términos y Condiciones</a></li>
                            <li><a href="#">Contactános</a></li>
                        </ul>
                    </div>
                <div class="my-account">
                    <p class="title-footer">Mi cuenta</p>
                    <ul>
                        <li><a href="#">Mi cuenta</a></li>
                        <li><a href="#">Historial de Órdenes</a></li>
                        <li><a href="#">Lista de Deseos</a></li>
                        <li><a href="#">Boletín</a></li>
                        <li><a href="#">Reembolsos</a></li>
                    </ul>
                </div>
                <div class="logo-footer">
                    <img src="../assets/logo.png" alt="Logo Peace In Progress">
                </div>
            </div>

            <div class="copyright">
                <p>
                    PEACE IN PROGRESS &copy; <?php echo date('Y'); ?>
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para manejar el botón "Me gusta"
            const likeButton = document.querySelector('.like-button');
            if (likeButton) {
                likeButton.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    
                    // Enviar solicitud AJAX al servidor
                    fetch('../ajax/like_post.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'post_id=' + postId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar UI
                            const likeCount = this.querySelector('.like-count');
                            likeCount.textContent = data.likes;
                            
                            if (data.liked) {
                                this.classList.add('liked');
                                this.querySelector('i').classList.replace('far', 'fas');
                            } else {
                                this.classList.remove('liked');
                                this.querySelector('i').classList.replace('fas', 'far');
                            }
                        } else {
                            // Mostrar mensaje de error
                            showMessage(data.message || 'Debes iniciar sesión para dar me gusta');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Error al procesar la solicitud');
                    });
                });
            }
            
            // Función para manejar el botón de compartir
            const shareButton = document.querySelector('.share-button');
            if (shareButton) {
                shareButton.addEventListener('click', function() {
                    const url = window.location.href;
                    
                    // Copiar al portapapeles
                    navigator.clipboard.writeText(url).then(function() {
                        showMessage('Enlace copiado al portapapeles');
                    }, function(err) {
                        console.error('No se pudo copiar el texto: ', err);
                    });
                });
            }
            
            // Función para mostrar mensajes
            function showMessage(text) {
                const message = document.getElementById('message');
                message.textContent = text;
                message.classList.add('show');
                
                setTimeout(() => {
                    message.classList.remove('show');
                }, 3000);
            }
        });
    </script>

</body>

</html>