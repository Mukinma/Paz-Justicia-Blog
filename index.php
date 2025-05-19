<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PeaceInProgress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="views/css/style.css" />
    <link rel="stylesheet" href="views/css/noticias.css">
    <link rel="stylesheet" href="views/css/categorias.css">
    <link rel="stylesheet" href="views/css/nav-fix.css">
    <link rel="stylesheet" href="views/css/footer.css">
    <style>
        /* Mejoras específicas para el menú de perfil en index */
        .profile-dropdown {
            position: relative;
        }
        
        .profile-dropdown .dropdown-content {
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
        }
        
        .profile-btn {
            cursor: pointer;
            z-index: 1003;
            position: relative;
        }
        
        /* Área invisible ampliada para el hover */
        .profile-dropdown::after {
            content: '';
            position: absolute;
            top: 100%;
            left: -30px;
            width: calc(100% + 60px);
            height: 25px;
            background: transparent;
            z-index: 1000;
        }
        
        /* Crear un puente visual entre el botón de perfil y el menú */
        .dropdown-content::before {
            content: '';
            position: absolute;
            top: -10px;
            right: 10px;
            width: 20px;
            height: 10px;
            background-color: transparent;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid rgba(0, 0, 0, 0.85);
            z-index: 1003;
            pointer-events: none;
        }
        
        .dropdown-content a {
            padding: 10px 15px;
            font-size: 0.95rem;
        }
        
        /* Estilos para el icono de login */
        .login-icon {
            width: 20px;
            height: 20px;
            fill: white;
            transition: all 0.3s ease;
        }
        .login-btn:hover .login-icon {
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <?php
    // Incluir la configuración de la base de datos al inicio
    require_once 'config/db.php';
    
    // Iniciar sesión
    session_start();
    ?>
    
    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php">
                    <img src="assets/logo.png" class="logo" alt="Peace in Progress">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="views/blog.php">Blog</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Categorías <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <?php
                            // Consultar todas las categorías para el menú
                            $sqlCategorias = "SELECT id_categoria, nombre, slug, imagen FROM categorias ORDER BY nombre";
                            $stmtCategorias = $pdo->prepare($sqlCategorias);
                            $stmtCategorias->execute();
                            $categorias_menu = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($categorias_menu as $cat) {
                                $nombre = htmlspecialchars($cat['nombre']);
                                $slug = htmlspecialchars($cat['slug']);
                                $imagen = !empty($cat['imagen']) ? htmlspecialchars($cat['imagen']) : 'assets/image-placeholder.png';
                                
                                // Verificar si la imagen existe
                                if (!file_exists($imagen) && strpos($imagen, '/') !== false) {
                                    $imagen = 'assets/image-placeholder.png';
                                }
                                
                                echo '<li>
                                    <a href="views/categoria.php?slug=' . $slug . '">
                                        <img src="' . $imagen . '" alt="' . $nombre . '" class="categoria-icono">
                                        ' . $nombre . '
                                    </a>
                                </li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="views/about.php">Sobre Nosotros</a></li>
                    <li><a href="views/contact.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="profile-section">
                <?php 
                if (!isset($_SESSION['usuario'])) {
                    echo '<a href="admin/usuario.php" class="login-btn">
                        <svg class="login-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m14 6c0 3.309-2.691 6-6 6s-6-2.691-6-6 2.691-6 6-6 6 2.691 6 6zm1 15v-6c0-.551.448-1 1-1h2v-2h-2c-1.654 0-3 1.346-3 3v6c0 1.654 1.346 3 3 3h2v-2h-2c-.552 0-1-.449-1-1zm8.583-3.841-3.583-3.159v3h-3v2h3v3.118l3.583-3.159c.556-.48.556-1.32 0-1.8zm-12.583-2.159c0-.342.035-.677.101-1h-6.601c-2.481 0-4.5 2.019-4.5 4.5v5.5h12.026c-.635-.838-1.026-1.87-1.026-3z"/>
                        </svg>
                    </a>';
                } else {
                    echo '<div class="profile-dropdown">
                        <button class="profile-btn">';
                    if (!empty($_SESSION['avatar']) && file_exists($_SESSION['avatar'])) {
                        echo '<img src="' . htmlspecialchars($_SESSION['avatar']) . '" alt="Foto de perfil">';
                    } else {
                        echo '<i class="fas fa-user-circle"></i>';
                    }
                    echo '</button>
                        <div class="dropdown-content">
                            <a href="admin/perfil.php"><i class="fas fa-user"></i> Perfil</a>';
                    
                    if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
                        echo '<a href="admin/adminControl.php"><i class="fas fa-cog"></i> Admin</a>';
                    }
                    
                    echo '<a href="admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                        </div>
                    </div>';
                }
                ?>
            </div>
            
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" id="errorMessage">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
        <script>
            setTimeout(() => {
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <div class="carousel">
        <div class="list">
            <?php
            // Variable para debug
            $debugImagenes = false; // Cambiar a true para ver información de depuración
            
            // Consulta mejorada para obtener los 5 posts más populares/tendencia
            // Combinando visitas y likes para determinar popularidad
            $sql = "SELECT p.id_post, p.titulo, p.resumen, p.fecha_publicacion, p.slug, 
                           i1.ruta AS imagen_destacada, i2.ruta AS imagen_background, 
                           c.nombre AS categoria, u.name AS autor,
                           p.visitas, 
                           (SELECT COUNT(*) FROM post_likes pl WHERE pl.id_post = p.id_post) AS likes,
                           (p.visitas * 0.7 + (SELECT COUNT(*) FROM post_likes pl WHERE pl.id_post = p.id_post) * 0.3) AS popularidad
                    FROM posts p
                    LEFT JOIN imagenes i1 ON p.id_imagen_destacada = i1.id_imagen
                    LEFT JOIN imagenes i2 ON p.id_imagen_background = i2.id_imagen
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                    WHERE p.estado = 'publicado'
                    ORDER BY popularidad DESC, p.fecha_publicacion DESC
                    LIMIT 5";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Si no hay posts, mostrar mensaje
            if (empty($posts)) {
                echo '<div class="no-posts">No hay posts destacados disponibles</div>';
            } else {
                // Mostrar cada post en el carousel
                foreach ($posts as $index => $post) {
                    // Determinar si es el primer item (activo por defecto)
                    $activeClass = ($index === 0) ? 'active' : '';
                    
                    // Formatear fecha
                    $fecha = new DateTime($post['fecha_publicacion']);
                    $fechaFormateada = $fecha->format('d \d\e F \d\e Y');
                    
                    // Construir URL de la imagen (con verificación)
                    $imagenURL = !empty($post['imagen_background']) ? $post['imagen_background'] : 
                                (!empty($post['imagen_destacada']) ? $post['imagen_destacada'] : 'assets/default-post.jpg');
                    
                    // Corregir la ruta de la imagen si comienza con ../
                    if (strpos($imagenURL, '../') === 0) {
                        $imagenURL = substr($imagenURL, 3); // Eliminar el prefijo '../'
                    }
                    
                    // Verificar si el archivo de imagen existe
                    $rutaCompleta = __DIR__ . '/' . $imagenURL;
                    $imagenExiste = file_exists($rutaCompleta);
                    
                    if ($debugImagenes) {
                        echo "<!-- DEBUG: ID Post: {$post['id_post']} -->\n";
                        echo "<!-- DEBUG: Ruta original: {$post['imagen_background']} o {$post['imagen_destacada']} -->\n";
                        echo "<!-- DEBUG: Ruta final: {$imagenURL} -->\n";
                        echo "<!-- DEBUG: Ruta completa: {$rutaCompleta} -->\n";
                        echo "<!-- DEBUG: Imagen existe: " . ($imagenExiste ? 'SÍ' : 'NO') . " -->\n";
                    }
                    
                    // Si la imagen no existe, usar imagen por defecto
                    if (!$imagenExiste) {
                        // Verificar si existe la imagen por defecto
                        $defaultImage = 'assets/image-placeholder.png';
                        if (file_exists(__DIR__ . '/' . $defaultImage)) {
                            $imagenURL = $defaultImage;
                        } else {
                            // Si ni siquiera existe la imagen predeterminada, usar el logo
                            $imagenURL = 'assets/logo.png';
                        }
                    }
                    
                    // Recortar descripción para cards pequeñas
                    $descripcionCorta = strlen($post['resumen']) > 120 ? 
                                      substr($post['resumen'], 0, 120) . '...' : 
                                      $post['resumen'];
            ?>
            <div class="item <?php echo $activeClass; ?>" data-id="<?php echo $post['id_post']; ?>">
                <img src="<?php echo htmlspecialchars($imagenURL); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                <div class="content">
                    <div class="scrollable-content">
                        <div class="topic"><?php echo htmlspecialchars($post['categoria']); ?></div>
                        <div class="title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                        <div class="des"><?php echo htmlspecialchars($post['resumen']); ?></div>
                        <div class="metrics">
                            <span class="views"><i class="fas fa-eye"></i> <?php echo $post['visitas']; ?></span>
                            <span class="likes"><i class="fas fa-heart"></i> <?php echo $post['likes']; ?></span>
                        </div>
                    </div>
                    <div class="button-container">
                        <div class="buttons">
                            <button onclick="window.location.href='views/post.php?id=<?php echo $post['id_post']; ?>'">Leer</button>
                            <button onclick="window.location.href='views/categoria<?php echo str_replace(' ', '', ucfirst($post['categoria'])); ?>.php'">Más en <?php echo htmlspecialchars($post['categoria']); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                } // fin foreach
            } // fin else
            ?>
        </div>
        
        <div class="thumbnail">
            <?php
            if (!empty($posts)) {
                foreach ($posts as $index => $post) {
                    $activeClass = ($index === 0) ? 'active' : '';
                    $imagenURL = !empty($post['imagen_destacada']) ? $post['imagen_destacada'] : 'assets/default-thumbnail.jpg';
                    
                    // Corregir la ruta de la imagen si comienza con ../
                    if (strpos($imagenURL, '../') === 0) {
                        $imagenURL = substr($imagenURL, 3); // Eliminar el prefijo '../'
                    }
                    
                    // Verificar si el archivo de imagen existe
                    $rutaCompleta = __DIR__ . '/' . $imagenURL;
                    $imagenExiste = file_exists($rutaCompleta);
                    
                    if ($debugImagenes) {
                        echo "<!-- DEBUG THUMB: ID Post: {$post['id_post']} -->\n";
                        echo "<!-- DEBUG THUMB: Ruta original: {$post['imagen_destacada']} -->\n";
                        echo "<!-- DEBUG THUMB: Ruta final: {$imagenURL} -->\n";
                        echo "<!-- DEBUG THUMB: Ruta completa: {$rutaCompleta} -->\n";
                        echo "<!-- DEBUG THUMB: Imagen existe: " . ($imagenExiste ? 'SÍ' : 'NO') . " -->\n";
                    }
                    
                    // Si la imagen no existe, usar imagen por defecto
                    if (!$imagenExiste) {
                        // Verificar si existe la imagen por defecto
                        $defaultImage = 'assets/image-placeholder.png';
                        if (file_exists(__DIR__ . '/' . $defaultImage)) {
                            $imagenURL = $defaultImage;
                        } else {
                            // Si ni siquiera existe la imagen predeterminada, usar el logo
                            $imagenURL = 'assets/logo.png';
                        }
                    }
                    
                    // Recortar título y descripción para las miniaturas
                    $tituloCorto = strlen($post['titulo']) > 50 ? substr($post['titulo'], 0, 50) . '...' : $post['titulo'];
                    $descripcionMiniatura = strlen($post['resumen']) > 80 ? substr($post['resumen'], 0, 80) . '...' : $post['resumen'];
            ?>
            <div class="item <?php echo $activeClass; ?>" data-id="<?php echo $post['id_post']; ?>">
                <img src="<?php echo htmlspecialchars($imagenURL); ?>" alt="<?php echo htmlspecialchars($tituloCorto); ?>">
                <div class="content">
                    <div class="title"><?php echo htmlspecialchars($tituloCorto); ?></div>
                    <div class="description"><?php echo htmlspecialchars($descripcionMiniatura); ?></div>
                </div>
            </div>
            <?php
                } // fin foreach
            } // fin if
            ?>
        </div>
        
        <div class="arrows">
            <button id="prev"><i class="fas fa-chevron-left"></i></button>
            <button id="next"><i class="fas fa-chevron-right"></i></button>
        </div>
        
        <div class="time"></div>
    </div>


    <section class="destacados-section">
        <h2>Más Recientes</h2>
        <div class="destacados-container">
            <?php
            // Consulta para obtener los posts más recientes
            $sqlRecientes = "SELECT p.id_post, p.titulo, p.resumen, p.fecha_publicacion, 
                           i1.ruta AS imagen_destacada, 
                           c.nombre AS categoria, c.slug AS categoria_slug, u.name AS autor
                    FROM posts p
                    LEFT JOIN imagenes i1 ON p.id_imagen_destacada = i1.id_imagen
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                    WHERE p.estado = 'publicado'
                    ORDER BY p.fecha_publicacion DESC
                    LIMIT 5";
            
            $stmtRecientes = $pdo->prepare($sqlRecientes);
            $stmtRecientes->execute();
            $postsRecientes = $stmtRecientes->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($postsRecientes)) {
                // Primer post (principal)
                $postPrincipal = $postsRecientes[0];
                
                // Preparar imagen principal
                $imagenPrincipal = !empty($postPrincipal['imagen_destacada']) ? $postPrincipal['imagen_destacada'] : 'assets/default-post.jpg';
                
                // Corregir la ruta si es necesario
                if (strpos($imagenPrincipal, '../') === 0) {
                    $imagenPrincipal = substr($imagenPrincipal, 3);
                }
                
                // Verificar que exista la imagen
                if (!file_exists(__DIR__ . '/' . $imagenPrincipal)) {
                    $imagenPrincipal = 'assets/image-placeholder.png';
                }
                
                // Formatear fecha
                $fechaPrincipal = new DateTime($postPrincipal['fecha_publicacion']);
                $fechaPrincipalFormateada = $fechaPrincipal->format('d \d\e F \d\e Y');
                
                // Link para categoría
                $categoriaPrincipalSlug = !empty($postPrincipal['categoria_slug']) ? 
                                         $postPrincipal['categoria_slug'] : 
                                         str_replace(' ', '', ucfirst($postPrincipal['categoria']));
            ?>
            <!-- Noticia principal -->
            <div class="noticia-principal">
                <a href="views/post.php?id=<?php echo $postPrincipal['id_post']; ?>" class="noticia-imagen-link">
                    <img src="<?php echo htmlspecialchars($imagenPrincipal); ?>" alt="<?php echo htmlspecialchars($postPrincipal['titulo']); ?>">
                </a>
                <div class="noticia-categoria"><?php echo htmlspecialchars($postPrincipal['categoria']); ?></div>
                <div class="contenido">
                    <small><?php echo $fechaPrincipalFormateada; ?> | <?php echo htmlspecialchars($postPrincipal['autor']); ?></small>
                    <h3><a href="views/post.php?id=<?php echo $postPrincipal['id_post']; ?>"><?php echo htmlspecialchars($postPrincipal['titulo']); ?></a></h3>
                    <p><?php echo htmlspecialchars(substr($postPrincipal['resumen'], 0, 150) . (strlen($postPrincipal['resumen']) > 150 ? '...' : '')); ?></p>
                    <a href="views/post.php?id=<?php echo $postPrincipal['id_post']; ?>" class="leer-mas">Leer más</a>
                </div>
            </div>

            <!-- Noticias secundarias -->
            <div class="noticias-secundarias">
                <?php
                    // Posts secundarios (del 2 al 5)
                    $postsSecundarios = array_slice($postsRecientes, 1);
                    
                    foreach ($postsSecundarios as $post) {
                        // Preparar imagen
                        $imagen = !empty($post['imagen_destacada']) ? $post['imagen_destacada'] : 'assets/default-thumbnail.jpg';
                        
                        // Corregir la ruta si es necesario
                        if (strpos($imagen, '../') === 0) {
                            $imagen = substr($imagen, 3);
                        }
                        
                        // Verificar que exista la imagen
                        if (!file_exists(__DIR__ . '/' . $imagen)) {
                            $imagen = 'assets/image-placeholder.png';
                        }
                        
                        // Formatear fecha
                        $fecha = new DateTime($post['fecha_publicacion']);
                        $fechaFormateada = $fecha->format('d \d\e F \d\e Y');
                        
                        // Recortar descripción
                        $descripcionCorta = strlen($post['resumen']) > 80 ? substr($post['resumen'], 0, 80) . '...' : $post['resumen'];
                ?>
                <div class="noticia-secundaria">
                    <a href="views/post.php?id=<?php echo $post['id_post']; ?>" class="noticia-imagen-link">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                    </a>
                    <div class="info">
                        <div class="noticia-categoria-small"><?php echo htmlspecialchars($post['categoria']); ?></div>
                        <small><?php echo $fechaFormateada; ?></small>
                        <h4><a href="views/post.php?id=<?php echo $post['id_post']; ?>"><?php echo htmlspecialchars($post['titulo']); ?></a></h4>
                        <p><?php echo htmlspecialchars($descripcionCorta); ?></p>
                    </div>
                </div>
                <?php
                    }
                ?>
            </div>
            <?php
            } else {
                echo '<div class="no-posts-message">No hay artículos recientes disponibles</div>';
            }
            ?>
        </div>
    </section>

    <section class="banner-involucrarse">
        <div class="contenido-banner">
            <h2>Involúcrate</h2>
            <p>¿Quieres sumar al cambio y no sabes cómo? Te mostramos como lograrlo desde donde estés.
            </p>
            <a href="views/involucrate.php" class="boton-involucrarse">Actuar Ahora</a>
        </div>
    </section>

    <section class="categorias-temas">
        <h2 class="titulo-categorias">Explora por Temas</h2>

        <div class="grid-categorias">
            <?php
            // Consulta para obtener todas las categorías con sus imágenes de la base de datos
            $sqlCats = "SELECT id_categoria, nombre, slug, descripcion, imagen, imagen_fondo FROM categorias ORDER BY nombre";
            $stmtCats = $pdo->prepare($sqlCats);
            $stmtCats->execute();
            $todasCategorias = $stmtCats->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($todasCategorias as $cat) {
                // Obtener imagen desde la BD o usar una por defecto
                $imagen = !empty($cat['imagen']) ? $cat['imagen'] : 'assets/image-placeholder.png';
                
                // Verificar si la imagen existe
                if (!file_exists($imagen)) {
                    $imagen = 'assets/image-placeholder.png';
                }
                
                // Ya no usamos la imagen de fondo
                // $imagenFondo = !empty($cat['imagen_fondo']) ? $cat['imagen_fondo'] : null;
                // $tieneFondo = !empty($imagenFondo) && file_exists($imagenFondo);
                
                // Generar el nombre de archivo PHP adecuado para la vista de categoría
                $slug = $cat['slug'];
                
                // Descripción recortada
                $descripcion = !empty($cat['descripcion']) ? 
                    (strlen($cat['descripcion']) > 150 ? substr($cat['descripcion'], 0, 150) . '...' : $cat['descripcion']) :
                    'Artículos sobre ' . $cat['nombre'];
                    
                // Asegurar que la descripción tenga al menos cierta longitud
                if (strlen($descripcion) < 30) {
                    $descripcion = $descripcion . '. Explora nuestros artículos sobre esta temática.';
                }
            ?>
            <div class="categoria-card clean-design">
                <div class="icono">
                    <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($cat['nombre']); ?>">
                </div>
                <h3 class="titulo"><?php echo htmlspecialchars($cat['nombre']); ?></h3>
                <p class="descripcion"><?php echo htmlspecialchars($descripcion); ?></p>
                <a href="views/categoria.php?slug=<?php echo $slug; ?>" class="btn-categoria">Ver artículos</a>
            </div>
            <?php
            } // fin foreach
            ?>
        </div>
    </section>


    <section class="experience-section">
        <div class="experience-content">
            <h1>Comparte tu experiencia<br>con nosotros.</h1>
            <p>
                Tu opinión es importante. Ya seas un usuario que ha usado nuestros servicios o un experto,
                queremos conocer tus experiencias y sugerencias para mejorar.
            </p>
            <ul>
                <li>✔️ Formulario fácil de llenar</li>
                <li>✔️ Participa como usuario o experto</li>
                <li>✔️ Tus comentarios nos ayudan a mejorar</li>
            </ul>
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSeut6SoqCMiJZ0cOx1oiCqulzv_Zi0WGnmgqkuvwE__W4JU7A/viewform"
                target="_blank" class="btn1">Ir al Formulario</a>

        </div>
        <div class="experience-image">
            <img src="image/formulario2.png" alt="Persona escribiendo experiencia">
        </div>
    </section>


    <script src="js/app.js"></script>
    <script src="js/header.js"></script>
    <script src="views/js/nav-fix.js"></script>
    <script src="js/profile-menu.js"></script>

    <!-- Scripts del carousel -->
    <script src="js/noticia-script.js"></script>

    <?php include 'views/includes/footer.php'; ?>

    <!-- Script para menú móvil -->
    <script>
        // Gestión de la navegación y menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            // Control de menú móvil
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mainNav = document.querySelector('.main-nav');
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                    this.querySelector('i').classList.toggle('fa-bars');
                    this.querySelector('i').classList.toggle('fa-times');
                });
            }
            
            // Control de dropdowns en móvil
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
                
                if (dropdownToggle && window.innerWidth <= 992) {
                    dropdownToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        dropdown.classList.toggle('active');
                    });
                }
            });
            
            // Cambiar estilo de header al hacer scroll
            window.addEventListener('scroll', function() {
                const header = document.querySelector('header.main-header');
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
            
            // Control del menú de perfil para dispositivos táctiles
            const profileBtn = document.querySelector('.profile-btn');
            const dropdownContent = document.querySelector('.dropdown-content');
            
            if (profileBtn && dropdownContent) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownContent.classList.toggle('active');
                });
                
                // Cerrar el menú al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.profile-dropdown')) {
                        dropdownContent.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>

</html>