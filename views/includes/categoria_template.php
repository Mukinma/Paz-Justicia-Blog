<?php
// Verificar que las variables necesarias estén definidas
if (!isset($categoria_slug)) {
    die("Error: No se ha definido la categoría");
}

// Incluir la configuración de la base de datos
require_once '../config/db.php';

// Incluir funciones de categoría
require_once 'includes/categoria_functions.php';

// Intentar obtener la información de la categoría
$categoria = obtenerCategoriaPorSlug($pdo, $categoria_slug);

// Si no existe la categoría, redirigir a la página principal
if (!$categoria) {
    header('Location: ../index.php');
    exit;
}

// Obtener posts de la categoría (inicialmente 6)
$posts = obtenerPostsPorCategoria($pdo, $categoria['id_categoria']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($categoria['nombre']); ?> - Peace in Progress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/categorias.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="icon" href="../assets/minilogo.png">
    <meta name="description" content="<?php echo htmlspecialchars(substr($categoria['descripcion'], 0, 160)); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($categoria['nombre']); ?> - Peace in Progress">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr($categoria['descripcion'], 0, 160)); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($categoria_imagen); ?>">
</head>

<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <img src="../assets/logo.png" class="logo" alt="Peace in Progress">
            </div>
            
            <div class="search-bar">
                <input type="text" placeholder="Buscar contenido...">
                <span class="search-icon"><i class="fas fa-search"></i></span>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="../index.php">Inicio</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Categorías <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="categoriaCorrupcion.php">Corrupción y Transparencia</a></li>
                            <li><a href="categoriaIgualdad.php">Igualdad y Diversidad</a></li>
                            <li><a href="categoriaJusticia.php">Justicia y Derechos Humanos</a></li>
                            <li><a href="categoriaParticipacion.php">Participación Ciudadana</a></li>
                            <li><a href="categoriaPaz.php">Paz y Conflictos</a></li>
                            <li><a href="categoriaPolitica.php">Política y Gobernanza</a></li>
                        </ul>
                    </li>
                    <li><a href="about.php">Sobre Nosotros</a></li>
                    <li><a href="contact.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="profile-section">
                <?php echo getProfileSectionHTML(); ?>
            </div>
            
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" id="errorMessage">
            <i class="fas fa-exclamation-circle"></i>
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
        <script>
            setTimeout(() => {
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) {
                    errorMessage.style.opacity = '0';
                    setTimeout(() => {
                        errorMessage.remove();
                    }, 300);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <!-- Hero principal con efecto parallax -->
    <section class="hero-section" style="background-image: url('<?php 
        $imagen_bg = isset($categoria_imagen) ? $categoria_imagen : 
            (!empty($categoria['imagen']) ? '../' . $categoria['imagen'] : '../assets/image-placeholder.png');
        echo htmlspecialchars($imagen_bg); 
    ?>');">
        <div class="hero-content">
            <small>CATEGORÍA • <?php echo $categoria['total_posts']; ?> ARTÍCULOS</small>
            <h1><?php echo htmlspecialchars($categoria['nombre']); ?></h1>
            <p><?php echo htmlspecialchars($categoria['descripcion']); ?></p>
        </div>
    </section>

    <!-- Título de sección -->
    <h2 class="section-title">Artículos Destacados</h2>

    <!-- Artículos -->
    <section class="grid-container" id="articulos">
        <?php if (empty($posts)): ?>
            <div class="no-posts">
                <i class="fas fa-newspaper"></i>
                <h3>No hay artículos disponibles</h3>
                <p>Actualmente no hay artículos publicados en esta categoría. ¡Vuelve pronto para ver nuevos contenidos!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <a href="post.php?id=<?php echo $post['id_post']; ?>" class="card">
                    <img src="<?php echo !empty($post['imagen']) ? '../' . $post['imagen'] : '../assets/img/default-post.jpg'; ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                    <div class="card-content">
                        <small><?php echo formatearFecha($post['fecha_publicacion']); ?> • por <?php echo htmlspecialchars($post['autor']); ?></small>
                        <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($post['resumen']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php if (!empty($posts) && $categoria['total_posts'] > count($posts)): ?>
    <!-- Botón Ver más -->
    <div class="ver-mas">
        <button id="cargar-mas">Ver más artículos <i class="fas fa-arrow-down"></i></button>
    </div>
    <?php endif; ?>

    <script>
        // Gestión de la navegación y menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            // Hacer que el logo redireccione a la página principal
            document.querySelector('.logo').addEventListener('click', function() {
                window.location.href = '../index.php';
            });
            
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
            
            // Efecto de entrada para las tarjetas
            const cards = document.querySelectorAll('.card');
            const animateCards = () => {
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, 100 * index);
                });
            };
            
            // Iniciar animación cuando el contenido esté cargado
            if (cards.length > 0) {
                animateCards();
            }
            
            // AJAX para cargar más artículos con animación
            const cargarMasBtn = document.getElementById('cargar-mas');
            if (cargarMasBtn) {
                let offset = <?php echo count($posts); ?>;
                let isLoading = false;
                
                cargarMasBtn.addEventListener('click', function() {
                    if (isLoading) return;
                    
                    // Mostrar estado de carga
                    isLoading = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
                    this.disabled = true;
                    
                    fetch(`get_more_posts.php?categoria=<?php echo $categoria['id_categoria']; ?>&offset=${offset}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.posts && data.posts.length > 0) {
                                const contenedor = document.getElementById('articulos');
                                
                                // Agregar nuevas tarjetas con animación
                                data.posts.forEach((post, index) => {
                                    const card = document.createElement('a');
                                    card.href = `post.php?id=${post.id_post}`;
                                    card.className = 'card';
                                    card.style.opacity = '0';
                                    card.style.transform = 'translateY(20px)';
                                    
                                    card.innerHTML = `
                                        <img src="${post.imagen ? '../' + post.imagen : '../assets/img/default-post.jpg'}" alt="${post.titulo}">
                                        <div class="card-content">
                                            <small>${post.fecha_formateada} • por ${post.autor}</small>
                                            <h3>${post.titulo}</h3>
                                            <p>${post.resumen}</p>
                                        </div>
                                    `;
                                    
                                    contenedor.appendChild(card);
                                    
                                    // Aplicar animación con retraso escalonado
                                    setTimeout(() => {
                                        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                        card.style.opacity = '1';
                                        card.style.transform = 'translateY(0)';
                                    }, 100 * index);
                                });
                                
                                offset += data.posts.length;
                                
                                // Ocultar el botón si ya no hay más posts para cargar
                                if (data.posts.length < 6 || offset >= <?php echo $categoria['total_posts']; ?>) {
                                    cargarMasBtn.parentElement.style.display = 'none';
                                } else {
                                    // Restaurar estado del botón
                                    cargarMasBtn.innerHTML = 'Ver más artículos <i class="fas fa-arrow-down"></i>';
                                    cargarMasBtn.disabled = false;
                                }
                            } else {
                                cargarMasBtn.parentElement.style.display = 'none';
                            }
                            
                            isLoading = false;
                        })
                        .catch(error => {
                            console.error('Error al cargar más posts:', error);
                            cargarMasBtn.innerHTML = 'Intentar de nuevo <i class="fas fa-redo"></i>';
                            cargarMasBtn.disabled = false;
                            isLoading = false;
                        });
                });
            }
            
            // Búsqueda en tiempo real
            const searchInput = document.querySelector('.search-bar input');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        window.location.href = `search.php?q=${encodeURIComponent(this.value.trim())}`;
                    }
                });
            }
        });
    </script>

    <footer class="footer">
        <div class="container-footer">
            <div class="container-container-container-footer">
                <div class="menu-footer">
                    <div class="contact-info">
                        <p class="title-footer">Información de Contacto</p>
                        <ul>
                            <li><i class="fas fa-phone"></i> 314-149-5596</li>
                            <li><i class="fas fa-envelope"></i> PeaceInProgress1@gmail.com</li>
                            <li><i class="fas fa-map-marker-alt"></i> Ciudad de México, México</li>
                        </ul>
                        <div class="social-icons">
                            <span class="facebook">
                                <i class="fab fa-facebook-f"></i>
                            </span>
                            <span class="twitter">
                                <i class="fab fa-twitter"></i>
                            </span>
                            <span class="instagram">
                                <i class="fab fa-instagram"></i>
                            </span>
                            <span class="youtube">
                                <i class="fab fa-youtube"></i>
                            </span>
                        </div>
                    </div>

                    <div class="information">
                        <p class="title-footer">Información</p>
                        <ul>
                            <li><a href="about.php"><i class="fas fa-angle-right"></i> Acerca de Nosotros</a></li>
                            <li><a href="contact.php"><i class="fas fa-angle-right"></i> Contacto</a></li>
                            <li><a href="privacy.php"><i class="fas fa-angle-right"></i> Política de Privacidad</a></li>
                            <li><a href="terms.php"><i class="fas fa-angle-right"></i> Términos y Condiciones</a></li>
                        </ul>
                    </div>
                    
                    <div class="my-account">
                        <p class="title-footer">Categorías</p>
                        <ul>
                            <li><a href="categoriaCorrupcion.php"><i class="fas fa-angle-right"></i> Corrupción y Transparencia</a></li>
                            <li><a href="categoriaIgualdad.php"><i class="fas fa-angle-right"></i> Igualdad y Diversidad</a></li>
                            <li><a href="categoriaJusticia.php"><i class="fas fa-angle-right"></i> Justicia y Derechos Humanos</a></li>
                            <li><a href="categoriaParticipacion.php"><i class="fas fa-angle-right"></i> Participación Ciudadana</a></li>
                            <li><a href="categoriaPaz.php"><i class="fas fa-angle-right"></i> Paz y Conflictos</a></li>
                            <li><a href="categoriaPolitica.php"><i class="fas fa-angle-right"></i> Política y Gobernanza</a></li>
                        </ul>
                    </div>
                </div>
                <div class="logo-footer">
                    <img src="../assets/logo.png" alt="Logo Peace In Progress">
                </div>
            </div>

            <div class="copyright">
                <p>
                    PEACE IN PROGRESS &copy; <?php echo date('Y'); ?> - Todos los derechos reservados
                </p>
            </div>
        </div>
    </footer>

</body>

</html> 