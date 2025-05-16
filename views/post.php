<?php
session_start();
require_once '../config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../admin/usuario.php');
    exit();
}

// Obtener el ID del post de la URL
$id_post = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_post <= 0) {
    header('Location: ../index.php');
    exit();
}

try {
    // Obtener el post con la información de la imagen y categoría
    $sql = "SELECT p.*, u.name as autor_nombre, u.avatar as autor_avatar, 
            i_destacada.ruta as ruta_imagen_destacada, i_background.ruta as ruta_imagen_background,
            c.nombre as categoria_nombre
            FROM posts p 
            LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
            LEFT JOIN imagenes i_destacada ON p.id_imagen_destacada = i_destacada.id_imagen
            LEFT JOIN imagenes i_background ON p.id_imagen_background = i_background.id_imagen
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE p.id_post = ? AND p.estado = 'publicado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_post]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: ../index.php');
        exit();
    }

    // Obtener comentarios del post
    $stmt = $pdo->prepare("SELECT c.*, u.name as autor_nombre 
                          FROM comentarios c 
                          LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario 
                          WHERE c.id_post = ? AND c.aprobado = 1 
                          ORDER BY c.fecha_comentario DESC");
    $stmt->execute([$id_post]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener posts relacionados
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre 
                          FROM posts p 
                          LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                          WHERE p.id_categoria = ? AND p.id_post != ? AND p.estado = 'publicado' 
                          ORDER BY p.fecha_publicacion DESC LIMIT 3");
    $stmt->execute([$post['id_categoria'], $id_post]);
    $posts_relacionados = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en post.php: " . $e->getMessage());
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['titulo']); ?> - PeaceInProgress</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #35688e;
            color: #fff;
        }

        header {
            width: 100%;
            height: 60px;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
        }

        header .logo {
            height: 40px;
            cursor: pointer;
        }

        .search-bar {
            position: relative;
            flex-grow: 1;
            max-width: 600px;
            margin-left: 30px;
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .search-bar input {
            width: 100%;
            color: #eee;
            padding: 6px 36px 6px 12px;
            border: none;
            outline: none;
            font-size: 14px;
            background-color: transparent;
            font-weight: 400;
        }

        .search-bar .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 16px;
            color: #555;
        }

        header nav {
            display: flex;
            gap: 30px;
        }

        header nav a {
            color: #eee;
            text-decoration: none;
            font-weight: 500;
        }

        /* Imagen y título del artículo */
        .article-header {
            width: 100%;
            height: 600px;
            background: url('../assets/<?php echo htmlspecialchars($post['ruta_imagen_background'] ?? $post['ruta_imagen_destacada']); ?>') no-repeat center center / cover;
            position: relative;
        }

        .article-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
        }

        .article-header h1 {
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 3.2em;
            color: white;
            z-index: 1;
            padding: 0 20px;
            text-align: center;
        }

        .article-header h2 {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 1em;
            color: white;
            z-index: 1;
            text-align: right;
        }


        .container {
            display: flex;
            max-width: 1400px;
            gap: 40px;
            padding: 0 20px;
            padding-left: 0;
            margin-left: 0;
        }

        .main-content {
            position: relative;
            top: -200px;
            flex: 9;
            background: #35688e;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #fff;
            z-index: 2;
            max-width: 800px;
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .main-content h2 {
            font-family: Georgia, serif;
            font-size: 2em;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .main-content p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #e0e0e0;
            margin-bottom: 20px;
            text-align: justify;
        }

        .sidebar {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding-top: 20px;
        }

        .card {
            background: #82aed0;
            border-radius: 10px;
            padding: 10px;
            font-size: 1em;
            color: #333;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            font-size: 1em;
            margin-bottom: 5px;
        }

        .card p {
            font-size: 0.95em;
        }

        .carousel {
            overflow-x: auto;
            display: flex;
            gap: 10px;
            scroll-snap-type: x mandatory;
            padding: 5px;
        }

        .carousel::-webkit-scrollbar {
            display: none;
        }

        .carousel-card {
            min-width: 100px;
            background: #82aed0;
            color: #333;
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
            scroll-snap-align: start;
            flex-shrink: 0;
            font-size: 0.7em;
        }

        .carousel-card h4 {
            font-size: 0.8em;
            margin-bottom: 4px;
        }

        .social-icons {
            display: flex;
            gap: 10px;
            padding: 15px;
        }

        .social-icons a {
            text-decoration: none;
            background: #82aed0;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333333;
            transition: background 0.3s;
        }

        .social-icons a:hover {
            background: #35688e;
            color: #fff;
        }

        a.back {
            display: inline-block;
            margin-top: 30px;
            color: #82aed0;
            text-decoration: none;
            font-weight: bold;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        a.back:hover {
            border-color: #82aed0;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                flex-direction: row;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .card,
            .social-icons {
                flex: 1 1 100%;
            }
        }

        .footer {
            background-color: #024365;
        }

        .container-footer {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            padding: 2rem;
        }

        .menu-footer {
            display: flex;
            justify-items: space-between 300px;
            grid-template-columns: repeat(3, 1fr) 30rem;
            gap: 2rem;

        }

        .title-footer {
            font-weight: 600;
            font-size: 1.6rem;
            text-transform: uppercase;
        }

        .contact-info,
        .information,
        .my-account,
        .newsletter {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .contact-info ul,
        .information ul,
        .my-account ul {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .contact-info ul li,
        .information ul li,
        .my-account ul li {
            list-style: none;
            color: #fff;
            font-size: 1.4rem;
            font-weight: 300;
        }

        .information ul li a,
        .my-account ul li a {
            text-decoration: none;
            color: #fff;
            font-weight: 300;
        }

        .information ul li a:hover,
        .my-account ul li a:hover {
            color: var(--dark-color);
        }

        .social-icons2 {
            display: flex;
            gap: 1.5rem;
        }

        .social-icons2 span {
            border-radius: 50%;
            width: 3rem;
            height: 3rem;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-icons2 span i {
            color: #fff;
            font-size: 1.2rem;
        }

        .facebook {
            background-color: #3b5998;
        }

        .twitter {
            background-color: #00acee;
        }

        .youtube {
            background-color: #c4302b;
        }

        .pinterest {
            background-color: #c8232c;
        }

        .instagram {
            background: linear-gradient(#405de6,
                    #833ab4,
                    #c13584,
                    #e1306c,
                    #fd1d1d,
                    #f56040,
                    #fcaf45);
        }

        .content p {
            font-size: 1.4rem;
            color: #fff;
            font-weight: 300;
        }

        .content input {
            outline: none;
            background: none;
            border: none;
            border-bottom: 2px solid #d2b495;
            cursor: pointer;
            padding: 0.5rem 0 1.2rem;
            color: var(--dark-color);
            display: block;
            margin-bottom: 3rem;
            margin-top: 2rem;
            width: 100%;
            font-family: inherit;
        }

        .content input::-webkit-input-placeholder {
            color: #eee;
        }

        .content button {
            border: none;
            background-color: #000;
            color: #fff;
            text-transform: uppercase;
            padding: 1rem 3rem;
            border-radius: 2rem;
            font-size: 1.4rem;
            font-family: inherit;
            cursor: pointer;
            font-weight: 600;
        }

        .content button:hover {
            background-color: var(--background-color);
            color: var(--primary-color);
        }

        .copyright {
            display: flex;
            justify-content: space-between;
            padding-top: 2rem;

            border-top: 1px solid #d2b495;
        }

        .copyright p {
            font-weight: 400;
            font-size: 1.6rem;
        }

        .logo-footer {
            display: flex;
            align-items: right;
            justify-content: right;

        }

        .logo-footer img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            align-items: 0px;
        }

        .container-container-container-footer {
            display: flex;
            justify-content: space-between;
        }

        .interaction-buttons {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .like-button,
        .share-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 1.2em;
            color: #ccc;
            transition: color 0.3s ease;
        }

        .like-button.liked {
            color: #ff4f4f;
        }

        .share-button:hover {
            color: #4faaff;
        }

        .like-button .like-count,
        .share-button span {
            font-size: 1rem;
            color: #fff;
        }

        .interaction-buttons {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .like-button,
        .share-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 1.2em;
            color: #ccc;
            transition: color 0.3s ease;
        }

        .like-button.liked {
            color: #83d0f6;
        }

        .share-button:hover {
            color: #4faaff;
        }

        .like-button .like-count,
        .share-button span {
            font-size: 1rem;
            color: #fff;
        }

        /* Estilo para el mensaje flotante */
        .message {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s, visibility 0.5s;
            z-index: 1000;
        }

        .message.show {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 450px) {
            .main-content {
            position: relative;
            top:30px;
            left: 10px;
            flex: 9;
            background: #35688e;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #fff;
            z-index: 2;
            max-width: 800px;
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .main-content h2 {
            font-family: Georgia, serif;
            font-size: 2em;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .main-content p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #e0e0e0;
            margin-bottom: 20px;
            text-align: justify;
        }
        .sidebar {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding-top: 20px;
            position: relative;
            left: 10px;

        }

        .container-footer {
            
            display: flex;
            flex-direction: column;
            gap: 2rem;
            padding: 2rem;
        }

        .menu-footer {
            display: flex;
            flex-direction: column;
            justify-items: space-between 300px;
            grid-template-columns: repeat(3, 1fr) 30rem;
            gap: 2rem;

        }


    }

    </style>
</head>

<body>

    <header>
        <img src="../assets/logo.png" class="logo" onclick="window.location.href='../index.php'">

        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <span class="search-icon">🔍</span>
        </div>

        <nav>
            <a href="index.html">Home</a>
            <a href="contact/contact.html">Contact</a>
            <a href="">Info</a>
            <a href="login/login.html" class="btn">Login</a>
        </nav>
    </header>

    <div class="article-header" style="background-image: url('../assets/<?php echo htmlspecialchars($post['ruta_imagen_background'] ?? $post['ruta_imagen_destacada']); ?>')">
        <h1><?php echo htmlspecialchars($post['titulo']); ?></h1>
        <h2>Autor: <?php 
            $nombre_completo = $post['autor_nombre'];
            $nombre_palabras = explode(' ', $nombre_completo);
            echo htmlspecialchars($nombre_palabras[0]); 
        ?></h2>
    </div>

    <div class="container">
        <div class="main-content">
            <p><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>

            <?php if (!empty($post['ruta_imagen_destacada'])): ?>
            <img src="../assets/<?php echo htmlspecialchars($post['ruta_imagen_destacada']); ?>" alt="Imagen ilustrativa" style="width: 100%; margin-top: 30px; margin-bottom: 30px;">
            <?php endif; ?>

            <div class="interaction-buttons">
                <div class="like-button" onclick="toggleLike(this)" data-post-id="<?php echo $id_post; ?>">
                    <i class="fa-solid fa-heart"></i>
                    <span class="like-count"><?php echo $post['visitas']; ?></span>
                </div>
                <div class="share-button" onclick="shareArticle()">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span>Compartir</span>
                </div>
            </div>

            <div id="copyMessage" class="message">
                ¡Enlace copiado al portapapeles!
            </div>

            <a href="index.php" class="back">← Volver al inicio</a>
        </div>

        <aside class="sidebar">
            <div class="card">
                <p>Publicado: <?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></p>
                <p>Categoría: <?php echo htmlspecialchars($post['categoria_nombre']); ?></p>
            </div>

            <div class="card">
                <h3>Artículos Relacionados</h3>
                <div class="carousel">
                    <?php foreach ($posts_relacionados as $relacionado): ?>
                    <div class="carousel-card">
                        <?php if (!empty($relacionado['imagen_destacada'])): ?>
                        <img src="<?php echo htmlspecialchars($relacionado['imagen_destacada']); ?>" alt="Imagen relacionada" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($relacionado['titulo']); ?></h4>
                        <p><?php echo substr(htmlspecialchars($relacionado['resumen']), 0, 100) . '...'; ?></p>
                        <a href="post.php?id=<?php echo $relacionado['id_post']; ?>" class="see-more-button">Ver más</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <h3>Comentarios</h3>
                <form id="commentForm" method="POST" action="../controllers/comment_controller.php">
                    <input type="hidden" name="post_id" value="<?php echo $id_post; ?>">
                    <textarea name="contenido" id="commentInput" placeholder="Escribe tu comentario..." rows="3"
                        style="width: 100%; padding: 5px; border-radius: 5px; border: none;" required></textarea>
                    <button type="submit"
                        style="margin-top: 10px; padding: 6px 12px; background: #35688e; color: white; border: none; border-radius: 5px; cursor: pointer;">Publicar</button>
                </form>
                <div id="commentList" style="margin-top: 10px;">
                    <?php foreach ($comentarios as $comentario): ?>
                    <div class="comment" style="display: flex; align-items: center; background: #d0e3f1; padding: 5px; border-radius: 5px; margin-bottom: 10px;">
                        <?php if (!empty($comentario['autor_avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($comentario['autor_avatar']); ?>" alt="Usuario" 
                             style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                        <?php else: ?>
                        <img src="../assets/default-avatar.png" alt="Usuario" 
                             style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                        <?php endif; ?>
                        <div>
                            <p style="margin: 0; font-weight: bold;"><?php echo htmlspecialchars($comentario['autor_nombre']); ?></p>
                            <p style="margin: 0;"><?php echo htmlspecialchars($comentario['contenido']); ?></p>
                            <small style="color: #666;"><?php echo date('d/m/Y H:i', strtotime($comentario['fecha_comentario'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>

    <footer class="footer">
        <div class="container container-footer">
            <div class="container-container-container-footer">
                <div class="menu-footer">
                    <div class="contact-info">
                        <p class="title-footer">Información de Contacto</p>
                        <ul>
                            <li>Teléfono: 314-149-5596</li>
                            <li>EmaiL: PeaceInProgress.com</li>
                        </ul>
                        <div class="social-icons2">
                            <span class="facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </span>
                            <span class="twitter">
                                <i class="fa-brands fa-twitter"></i>
                            </span>
                            <span class="instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </span>
                        </div>
                    </div>

                    <div class="information">
                        <p class="title-footer">Información</p>
                        <ul>
                            <li><a href="#">Acerca de Nosotros</a></li>
                            <li><a href="#">Contactános</a></li>
                        </ul>
                    </div>
                </div>
                <div class="logo-footer">
                    <img src="image/logo.png" alt="Logo Peace In Progress">
                </div>
            </div>

            <div class="copyright">
                <p>
                    PEACE IN PROGRESS &copy; 2025
            </div>
        </div>
    </footer>

    <script>
    // Función para manejar likes (visitas)
    function toggleLike(button) {
        const postId = button.dataset.postId;
        fetch('../controllers/visit_controller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                post_id: postId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const count = button.querySelector('.like-count');
                let visits = parseInt(count.textContent);
                count.textContent = visits + 1;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Función para compartir
    function shareArticle() {
        const url = window.location.href;
        const title = document.title;

        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch((error) => console.log('Error al compartir:', error));
        } else {
            navigator.clipboard.writeText(url).then(() => {
                showCopyMessage();
            });
        }
    }

    function showCopyMessage() {
        const message = document.getElementById('copyMessage');
        message.classList.add('show');

        setTimeout(() => {
            message.classList.remove('show');
        }, 3000);
    }
    </script>

</body>

</html>