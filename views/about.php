<!DOCTYPE html>
<html lang="es">
    <link rel="icon" href="../assets/minilogo.png">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sobre nosotros - Peace in Progress</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/nav-fix.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
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
    // Inicio de sesión
    session_start();
    
    // Conexión a la base de datos para obtener categorías
    require_once '../config/db.php';
    ?>

    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <a href="../index.php">
                    <img src="../assets/logo.png" class="logo" alt="Peace in Progress">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="../views/blog.php">Blog</a></li>
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
                                $imagen = !empty($cat['imagen']) ? htmlspecialchars($cat['imagen']) : '../assets/image-placeholder.png';
                                
                                // Verificar si la imagen existe
                                if (!file_exists('../' . $imagen) && strpos($imagen, '/') !== false) {
                                    $imagen = '../assets/image-placeholder.png';
                                } else {
                                    $imagen = '../' . $imagen;
                                }
                                
                                echo '<li>
                                    <a href="categoria.php?slug=' . $slug . '">
                                        <img src="' . $imagen . '" alt="' . $nombre . '" class="categoria-icono">
                                        ' . $nombre . '
                                    </a>
                                </li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="about.php" class="active">Sobre Nosotros</a></li>
                    <li><a href="contact.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="profile-section">
                <?php 
                if (!isset($_SESSION['usuario'])) {
                    echo '<a href="../admin/usuario.php" class="login-btn">
                        <svg class="login-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m14 6c0 3.309-2.691 6-6 6s-6-2.691-6-6 2.691-6 6-6 6 2.691 6 6zm1 15v-6c0-.551.448-1 1-1h2v-2h-2c-1.654 0-3 1.346-3 3v6c0 1.654 1.346 3 3 3h2v-2h-2c-.552 0-1-.449-1-1zm8.583-3.841-3.583-3.159v3h-3v2h3v3.118l3.583-3.159c.556-.48.556-1.32 0-1.8zm-12.583-2.159c0-.342.035-.677.101-1h-6.601c-2.481 0-4.5 2.019-4.5 4.5v5.5h12.026c-.635-.838-1.026-1.87-1.026-3z"/>
                        </svg>
                    </a>';
                } else {
                    echo '<div class="profile-dropdown">
                        <button class="profile-btn">';
                    if (!empty($_SESSION['avatar']) && file_exists('../' . $_SESSION['avatar'])) {
                        echo '<img src="../' . htmlspecialchars($_SESSION['avatar']) . '" alt="Foto de perfil">';
                    } else {
                        echo '<i class="fas fa-user-circle"></i>';
                    }
                    echo '</button>
                        <div class="dropdown-content">
                            <a href="../admin/perfil.php"><i class="fas fa-user"></i> Perfil</a>';
                    
                    if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
                        echo '<a href="../admin/adminControl.php"><i class="fas fa-cog"></i> Admin</a>';
                    }
                    
                    echo '<a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
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

    <!-- Sección About Us -->
    <main>
        <section class="about">
            <div class="about-container">
                <div class="about-text">
                    <h1>Sobre Nosotros</h1>
                    <p>
                        <strong>PEACE IN PROGRESS</strong> es un blog dedicado a informar, crear conciencia y promover la paz, la justicia y sociedades inclusivas. Nuestra misión es concienciar sobre los conflictos sociales, la impunidad y la falta de acceso a la justicia. Creemos que el cambio comienza con la información, y una sociedad bien informada tiene el poder de construir un futuro justo.
                    </p>
                </div>
                <div class="about-images">
                    <img src="../assets/imagenabout.jpg" alt="Peace Image">
                    <img src="../assets/logo.png" alt="Logo Peace in Progress">
                </div>
            </div>

            <!-- Sección del equipo con tarjetas fijas -->
            <div class="news-carousel">
                <h2>Conoce a nuestro equipo</h2>

                <!-- Contenedor de partículas para efectos visuales -->
                <div id="particles-container"></div>

                <div class="team-grid">
                    <!-- Christopher -->
                    <div class="team-card">
                        <div class="image-placeholder">
                            <img src="../assets/icons/Nieves.jpg" alt="Christopher Eugenio Nieves Martínez">
                        </div>
                        <div class="team-content">
                            <h3>Christopher Eugenio Nieves Martínez</h3>
                            <span class="role">Líder del Equipo y Programador Full Stack</span>
                            <p>Coordinador del proyecto, experto en desarrollo web integral, manejo de bases de datos, seguridad y arquitectura de sistemas.</p>
                            <div class="social-links">
                                <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://github.com/Mukinma" target="_blank"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Fatima -->
                    <div class="team-card">
                        <div class="image-placeholder">
                            <img src="../assets/icons/Fati.jpg" alt="Fatima Isabel Contreras Avalos">
                        </div>
                        <div class="team-content">
                            <h3>Fatima Isabel Contreras Avalos</h3>
                            <span class="role">Diseñadora UI/UX y Programadora Frontend</span>
                            <p>Especialista en experiencia de usuario, creando interfaces atractivas e intuitivas que conectan con nuestros visitantes.</p>
                            <div class="social-links">
                                <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://github.com/Fatima2581" target="_blank"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Karol -->
                    <div class="team-card">
                        <div class="image-placeholder">
                            <img src="../assets/icons/Karol.jpg" alt="Karol Magdalena Sinsel Torres">
                        </div>
                        <div class="team-content">
                            <h3>Karol Magdalena Sinsel Torres</h3>
                            <span class="role">Programadora Frontend Principal</span>
                            <p>Experta en desarrollo de interfaces responsivas y accesibles, enfocada en la optimización y rendimiento del sitio.</p>
                            <div class="social-links">
                                <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://github.com/KobayashiTao" target="_blank"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Dimas -->
                    <div class="team-card">
                        <div class="image-placeholder">
                            <img src="../assets/icons/Dimas.jpg" alt="Dimas Rolon Aram Sebastian">
                        </div>
                        <div class="team-content">
                            <h3>Dimas Rolon Aram Sebastian</h3>
                            <span class="role">Programador Backend</span>
                            <p>Desarrollador de la estructura del servidor, bases de datos y seguridad de la plataforma, garantizando un funcionamiento óptimo.</p>
                            <div class="social-links">
                                <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://github.com/DIMAS0717" target="_blank"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="js/nav-fix.js"></script>
    <script src="../js/profile-menu.js"></script>
    <script>
        // Animación de elementos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Aplicar animate a los elementos con un ligero retraso para asegurar que la transición sea visible
            setTimeout(() => {
                const aboutText = document.querySelector('.about-text');
                const aboutImages = document.querySelector('.about-images');
                const carousel = document.querySelector('.news-carousel');
                
                if (aboutText) aboutText.classList.add('animate');
                if (aboutImages) aboutImages.classList.add('animate');
                if (carousel) carousel.classList.add('animate');
            }, 100);
            
            // Crear efecto de partículas para el fondo de la sección del equipo
            createParticles();
            
            // Añadir efecto 3D/parallax a las tarjetas
            setupCardTiltEffect();
            
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
        
        // Seguimos manteniendo la animación al hacer scroll como respaldo
        window.addEventListener('scroll', () => {
            const aboutText = document.querySelector('.about-text');
            const aboutImages = document.querySelector('.about-images');
            const carousel = document.querySelector('.news-carousel');
            
            if (isElementInViewport(aboutText) && !aboutText.classList.contains('animate')) {
                aboutText.classList.add('animate');
            }
            
            if (isElementInViewport(aboutImages) && !aboutImages.classList.contains('animate')) {
                aboutImages.classList.add('animate');
            }
            
            if (isElementInViewport(carousel) && !carousel.classList.contains('animate')) {
                carousel.classList.add('animate');
            }
        });

        function isElementInViewport(el) {
            if (!el) return false;
            
            const rect = el.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 &&
                rect.bottom >= 0
            );
        }
        
        // Función para crear partículas en el fondo de la sección
        function createParticles() {
            const container = document.getElementById('particles-container');
            if (!container) return;
            
            // Limpiar contenedor
            container.innerHTML = '';
            
            // Número de partículas según el tamaño de la pantalla
            const particleCount = window.innerWidth < 768 ? 30 : 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'sparkle';
                
                // Posición aleatoria
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const size = Math.random() * 4 + 2;
                const opacity = Math.random() * 0.6 + 0.2;
                const duration = Math.random() * 15 + 10;
                const delay = Math.random() * 5;
                
                // Aplicar estilos
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.opacity = opacity;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${delay}s`;
                
                container.appendChild(particle);
            }
        }
        
        // Función para añadir efecto 3D a las tarjetas
        function setupCardTiltEffect() {
            const cards = document.querySelectorAll('.team-card');
            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const angleX = (y - centerY) / 20;
                    const angleY = (centerX - x) / 20;
                    
                    card.style.transform = `translateY(-15px) rotateX(${angleX}deg) rotateY(${angleY}deg)`;
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                    setTimeout(() => {
                        card.style.transition = 'transform 0.6s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.6s';
                    }, 100);
                });
                
                card.addEventListener('mouseenter', () => {
                    card.style.transition = 'transform 0.1s';
                });
            });
            
            // Reajustar partículas en redimensión
            window.addEventListener('resize', () => {
                createParticles();
            });
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>