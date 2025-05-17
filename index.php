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
</head>

<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <img src="assets/logo.png" class="logo" alt="Peace in Progress">
            </div>
            
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="views/about.php">Sobre Nosotros</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Categorías <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="views/categoriaCorrupcion.php">Corrupción y Transparencia</a></li>
                            <li><a href="views/categoriaIgualdad.php">Igualdad y Diversidad</a></li>
                            <li><a href="views/categoriaJusticia.php">Justicia y Derechos Humanos</a></li>
                            <li><a href="views/categoriaParticipacion.php">Participación Ciudadana</a></li>
                            <li><a href="views/categoriaPaz.php">Paz y Conflictos</a></li>
                            <li><a href="views/categoriaPolitica.php">Política y Gobernanza</a></li>
                        </ul>
                    </li>
                    <li><a href="views/contact.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="profile-section">
                <?php
                session_start();
                if (!isset($_SESSION['usuario'])) {
                    echo '<a href="admin/usuario.php" class="login-btn">Iniciar Sesión</a>';
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
            require_once 'config/db.php';
            
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
                    <div class="topic"><?php echo htmlspecialchars($post['categoria']); ?></div>
                    <div class="title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                    <div class="author">Por <?php echo htmlspecialchars($post['autor']); ?> | <?php echo $fechaFormateada; ?></div>
                    <div class="des"><?php echo htmlspecialchars($post['resumen']); ?></div>
                    <div class="metrics">
                        <span class="views"><i class="fas fa-eye"></i> <?php echo $post['visitas']; ?></span>
                        <span class="likes"><i class="fas fa-heart"></i> <?php echo $post['likes']; ?></span>
                    </div>
                    <div class="buttons">
                        <button onclick="window.location.href='views/post.php?id=<?php echo $post['id_post']; ?>'">Leer Artículo</button>
                        <button onclick="window.location.href='views/categorias.php?categoria=<?php echo urlencode($post['categoria']); ?>'">Más en <?php echo htmlspecialchars($post['categoria']); ?></button>
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
                <img src="<?php echo htmlspecialchars($imagenURL); ?>" alt="Miniatura">
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

            <!-- Noticia principal -->
            <div class="noticia-principal">
                <img src="image/img5.jpg" alt="Imagen principal">
                <small>19 de marzo de 2025 | Administrador</small>
                <h3><a href="articulo1.html">México: Autoridades deberían investigar aparente sitio de asesinatos
                        masivos</a></h3>
                <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial sobre el
                    reciente hallazgo...</p>
            </div>

            <!-- Noticias secundarias -->
            <div class="noticias-secundarias">
                <div class="noticia-secundaria">
                    <img src="image/img1.jpg" alt="">
                    <div>
                        <small>27 de febrero de 2025 | Administrador</small>
                        <h4><a href="articulo2.html">Israel reproduce los métodos militares de Gaza en Cisjordania</a>
                        </h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
                <div class="noticia-secundaria">
                    <img src="image/img2.jpg" alt="">
                    <div>
                        <small>25 de marzo de 2025 | Administrador</small>
                        <h4><a href="articulo3.html">La FIFA debe reconocer y apoyar al equipo de mujeres afganas en el
                                exilio</a></h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
                <div class="noticia-secundaria">
                    <img src="image/img3.jpg" alt="">
                    <div>
                        <small>22 de enero de 2025 | Administrador</small>
                        <h4><a href="articulo4.html">Órdenes ejecutivas de Trump amenazan un amplio espectro de derechos
                                humanos</a></h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
                <div class="noticia-secundaria">
                    <img src="image/img4.jpg" alt="">
                    <div>
                        <small>24 de enero de 2025 | Administrador</small>
                        <h4><a href="articulo4.html">Estados Unidos cierra sus puertas a refugiados, solicitantes de
                                asilo y migrantes</a></h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="banner-involucrarse">
        <div class="contenido-banner">
            <h2>Involúcrate</h2>
            <p>¿Quieres sumar al cambio y no sabes cómo? Te mostramos como lograrlo desde donde estés.
            </p>
            <a href="involucrate/involucrate.html" class="boton-involucrarse">Actuar Ahora</a>
        </div>
    </section>

    <section class="categorias-temas">
        <h2 class="titulo-categorias">Explora por Temas</h2>

        <div class="grid-categorias">

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONOJUSTICIA.png" alt="Justicia y Derechos Humanos">
                </div>
                <h3 class="titulo">Justicia y Derechos Humanos</h3>
                <p class="descripcion">Acceso a la justicia, abusos de poder, sistema penitenciario..</p>
                <a href="Categorias/categoriaJusticia.html" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONOPAZ.png" alt="Paz y Conflictos">
                </div>
                <h3 class="titulo">Paz y Conflictos</h3>
                <p class="descripcion">Cobertura de guerras, procesos de reconciliación y contextos de conflicto global.
                </p>
                <a href="Categorias/categoriapaz.html" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONODIVERSIDAD.png" alt="Igualdad y Diversidad">
                </div>
                <h3 class="titulo">Igualdad y Diversidad</h3>
                <p class="descripcion">Causas y luchas por una sociedad más tolerante e inclusiva.</p>
                <a href="Categorias/categoriaIgualdad.html" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONOPARTICIPACION.png" alt="Participación Ciudadana">
                </div>
                <h3 class="titulo">Participación Ciudadana</h3>
                <p class="descripcion">Activismo, protestas pacíficas y organizaciones que protegen.</p>
                <a href="Categorias/categoriaParticipacion.html" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONOCORRUPCION.png" alt="Corrupción y Transparencia">
                </div>
                <h3 class="titulo">Corrupción y Transparencia</h3>
                <p class="descripcion">Investigaciones sobre corrupción y reformas por un sistema justo.</p>
                <a href="Categorias/categoriacorrupcion.html" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONOPOLITICA.png" alt="Politica y gobernanza">
                </div>
                <h3 class="titulo">Politica y gobernanza</h3>
                <p class="descripcion">Cobertura de politica, programas y acciones del gobierno para fortalecer la paz y
                    seguridad.</p>
                <a href="Categorias/categoriapolitica.html" class="btn-categoria">Ver artículos</a>
            </div>

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

    <!-- Scripts del carousel -->
    <script src="js/noticia-script.js"></script>

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
                        <div class="social-icons">
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
                            <li><a href="#">Contáctanos</a></li>
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

    <!-- Script para menú móvil -->
    <script>
        // Código para el menú móvil existente
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mainNav = document.querySelector('.main-nav');
            
            if (mobileMenuToggle && mainNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                });
            }
            
            // Dropdown en móvil
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.closest('.dropdown').classList.toggle('active');
                });
            });
        });
    </script>
</body>

</html>