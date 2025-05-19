<?php
// Archivo genérico para cargar cualquier categoría basado en el slug recibido por GET

// Iniciar sesión ANTES de acceder a variables de sesión
session_start();

// Verificar que se recibió un slug
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    // Si no hay slug, redirigir a la página principal
    header('Location: ../index.php');
    exit;
}

// Obtener el slug de la categoría
$categoria_slug = $_GET['slug'];

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
    <link rel="stylesheet" href="css/nav-fix.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="icon" href="../assets/minilogo.png">
    <meta name="description" content="<?php echo htmlspecialchars(substr($categoria['descripcion'] ?? '', 0, 160)); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($categoria['nombre']); ?> - Peace in Progress">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr($categoria['descripcion'] ?? '', 0, 160)); ?>">
    <?php if (!empty($categoria['imagen'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars('../' . $categoria['imagen']); ?>">
    <?php endif; ?>
    <style>
        /* Estilos adicionales para mejorar el contraste con el footer */
        .grid-container {
            background-color: #f5f7fa;
            padding: 40px 20px;
            border-radius: 10px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .ver-mas {
            background-color: #f5f7fa;
            padding-bottom: 20px;
            margin-bottom: 40px;
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
                    <li><a href="about.php">Sobre Nosotros</a></li>
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
        // Determinar qué imagen usar como fondo, priorizando imagen_fondo
        $imagen_bg = !empty($categoria['imagen_fondo']) ? '../' . $categoria['imagen_fondo'] : 
            (!empty($categoria['imagen']) ? '../' . $categoria['imagen'] : '../assets/image-placeholder.png');
        echo htmlspecialchars($imagen_bg); 
    ?>');">
        <div class="hero-content">
            <small>CATEGORÍA • <?php echo $categoria['total_posts'] ?? 0; ?> ARTÍCULOS</small>
            <h1><?php echo htmlspecialchars($categoria['nombre']); ?></h1>
            <p><?php echo htmlspecialchars($categoria['descripcion'] ?? 'Explora artículos sobre ' . $categoria['nombre']); ?></p>
        </div>
    </section>

    <!-- Contenedor principal con fondo que contrasta con el footer -->
    <div class="main-content-container">
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
                        <?php
                        // Determinar la ruta correcta de la imagen
                        $imagen_url = '../assets/image-placeholder.png';
                        if (!empty($post['imagen'])) {
                            // Si la ruta ya incluye '../', la usamos directamente
                            if (strpos($post['imagen'], '../') === 0) {
                                $imagen_url = $post['imagen'];
                            } else {
                                // Si no, añadimos '../'
                                $imagen_url = '../' . $post['imagen'];
                            }
                            
                            // Verificar que la imagen existe
                            $ruta_check = $imagen_url;
                            if (strpos($ruta_check, '../') === 0) {
                                $ruta_check = substr($ruta_check, 3);
                            }
                            
                            if (!file_exists("../$ruta_check")) {
                                $imagen_url = '../assets/image-placeholder.png';
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($imagen_url); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                        <div class="card-content">
                            <small><?php echo formatearFecha($post['fecha_publicacion']); ?> • por <?php echo htmlspecialchars($post['autor'] ?? 'Administrador'); ?></small>
                            <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($post['resumen'] ?? ''); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if (!empty($posts) && isset($categoria['total_posts']) && $categoria['total_posts'] > count($posts)): ?>
        <!-- Botón Ver más -->
        <div class="ver-mas">
            <button id="cargar-mas" data-categoria="<?php echo $categoria['id_categoria']; ?>" data-offset="<?php echo count($posts); ?>">
                Ver más artículos <i class="fas fa-arrow-down"></i>
            </button>
        </div>
        
        <script>
            // Script para cargar más artículos al hacer clic en el botón
            document.getElementById('cargar-mas').addEventListener('click', function() {
                const categoriaId = this.getAttribute('data-categoria');
                const offset = parseInt(this.getAttribute('data-offset'));
                const button = this;
                
                // Desactivar el botón mientras carga
                button.disabled = true;
                button.innerHTML = 'Cargando... <i class="fas fa-spinner fa-spin"></i>';
                
                // Hacer petición AJAX para cargar más artículos
                fetch(`cargar_mas_posts.php?categoria=${categoriaId}&offset=${offset}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.posts.length > 0) {
                        // Actualizar offset para la próxima carga
                        button.setAttribute('data-offset', offset + data.posts.length);
                        
                        // Añadir nuevos artículos al contenedor
                        const container = document.getElementById('articulos');
                        
                        data.posts.forEach(post => {
                            const card = document.createElement('a');
                            card.href = `post.php?id=${post.id_post}`;
                            card.className = 'card';
                            
                            // Determinar la ruta correcta de la imagen
                            let imagenUrl = '../assets/image-placeholder.png';
                            if (post.imagen) {
                                // Si la ruta ya incluye '../', la usamos directamente
                                if (post.imagen.startsWith('../')) {
                                    imagenUrl = post.imagen;
                                } else {
                                    // Si no, añadimos '../'
                                    imagenUrl = '../' + post.imagen;
                                }
                                
                                // No podemos verificar la existencia del archivo desde JS fácilmente
                                // Confiamos en que el servidor ya validó esto
                            }
                            
                            const fecha = new Date(post.fecha_publicacion).toLocaleDateString('es-ES', {
                                day: '2-digit', month: 'long', year: 'numeric'
                            });
                            
                            card.innerHTML = `
                                <img src="${imagenUrl}" alt="${post.titulo}" onerror="this.src='../assets/image-placeholder.png'">
                                <div class="card-content">
                                    <small>${fecha} • por ${post.autor || 'Administrador'}</small>
                                    <h3>${post.titulo}</h3>
                                    <p>${post.resumen || ''}</p>
                                </div>
                            `;
                            
                            container.appendChild(card);
                        });
                        
                        // Reactivar el botón
                        button.disabled = false;
                        button.innerHTML = 'Ver más artículos <i class="fas fa-arrow-down"></i>';
                        
                        // Ocultar el botón si no hay más artículos
                        if (data.no_more) {
                            button.parentElement.style.display = 'none';
                        }
                    } else {
                        // Ocultar el botón si no hay más artículos
                        button.parentElement.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reactivar el botón en caso de error
                    button.disabled = false;
                    button.innerHTML = 'Ver más artículos <i class="fas fa-arrow-down"></i>';
                });
            });
        </script>
        <?php endif; ?>
    </div>

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
        });
    </script>

    <script src="../js/categoria.js"></script>
    <script src="../views/js/nav-fix.js"></script>
    <script src="../js/profile-menu.js"></script>
    
    <?php include 'includes/footer.php'; ?>
</body>

</html> 