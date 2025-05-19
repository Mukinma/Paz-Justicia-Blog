<?php
session_start();

// Determinar la ruta base para los enlaces
$es_index = basename($_SERVER['PHP_SELF']) === 'index.php';
$base_url = $es_index ? '' : '../';

// Determinar página actual para agregar clase active al enlace correspondiente
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<header class="main-header">
    <div class="header-container">
        <div class="logo-container">
            <a href="<?php echo $base_url; ?>index.php">
                <img src="<?php echo $base_url; ?>assets/logo.png" class="logo" alt="Peace in Progress">
            </a>
        </div>
        
        <nav class="main-nav">
            <ul class="nav-menu">
                <?php if ($pagina_actual !== 'blog.php'): ?>
                <li><a href="<?php echo $base_url; ?>views/blog.php" <?php echo ($pagina_actual === 'blog.php') ? 'class="active"' : ''; ?>>Blog</a></li>
                <?php endif; ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">Categorías <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <?php
                        // Consulta para obtener todas las categorías con sus imágenes
                        require_once $base_url . 'config/db.php';
                        
                        $sqlCategorias = "SELECT id_categoria, nombre, slug, imagen FROM categorias ORDER BY nombre";
                        $stmtCategorias = $pdo->prepare($sqlCategorias);
                        $stmtCategorias->execute();
                        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($categorias as $cat) {
                            $nombre = htmlspecialchars($cat['nombre']);
                            $slug = htmlspecialchars($cat['slug']);
                            
                            // Verificar y preparar la ruta de la imagen
                            $imagen = !empty($cat['imagen']) ? $cat['imagen'] : 'assets/image-placeholder.png';
                            
                            // Si la ruta comienza con "../", quitar esa parte
                            if (strpos($imagen, '../') === 0) {
                                $imagen = substr($imagen, 3);
                            }
                            
                            // Verificar si la imagen existe
                            if (!file_exists($base_url . $imagen)) {
                                $imagen = 'assets/image-placeholder.png';
                            }
                            
                            // Crear enlace a la página de categoría
                            echo '<li>
                                    <a href="' . $base_url . 'views/categoria.php?slug=' . $slug . '">
                                        <img src="' . $base_url . $imagen . '" alt="' . $nombre . '" class="categoria-icono">
                                        ' . $nombre . '
                                    </a>
                                  </li>';
                        }
                        ?>
                    </ul>
                </li>
                <li><a href="<?php echo $base_url; ?>views/about.php" <?php echo ($pagina_actual === 'about.php') ? 'class="active"' : ''; ?>>Sobre Nosotros</a></li>
                <li><a href="<?php echo $base_url; ?>views/contact.php" <?php echo ($pagina_actual === 'contact.php') ? 'class="active"' : ''; ?>>Contacto</a></li>
            </ul>
        </nav>
        
        <div class="profile-section">
            <?php
            if (!isset($_SESSION['usuario'])) {
                echo '<a href="' . $base_url . 'admin/usuario.php" class="login-btn">
                    <svg class="login-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="m14 6c0 3.309-2.691 6-6 6s-6-2.691-6-6 2.691-6 6-6 6 2.691 6 6zm1 15v-6c0-.551.448-1 1-1h2v-2h-2c-1.654 0-3 1.346-3 3v6c0 1.654 1.346 3 3 3h2v-2h-2c-.552 0-1-.449-1-1zm8.583-3.841-3.583-3.159v3h-3v2h3v3.118l3.583-3.159c.556-.48.556-1.32 0-1.8zm-12.583-2.159c0-.342.035-.677.101-1h-6.601c-2.481 0-4.5 2.019-4.5 4.5v5.5h12.026c-.635-.838-1.026-1.87-1.026-3z"/>
                    </svg>
                </a>';
            } else {
                echo '<div class="profile-dropdown">
                        <button class="profile-btn">';
                if (!empty($_SESSION['avatar']) && file_exists($base_url . $_SESSION['avatar'])) {
                    echo '<img src="' . $base_url . htmlspecialchars($_SESSION['avatar']) . '" alt="Foto de perfil">';
                } else {
                    echo '<div class="avatar-initial">' . strtoupper(substr($_SESSION['usuario'], 0, 1)) . '</div>';
                }
                echo '</button>
                        <div class="dropdown-content">
                            <a href="' . $base_url . 'admin/perfil.php"><i class="fas fa-user"></i> Perfil</a>';
                if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
                    echo '<a href="' . $base_url . 'admin/adminControl.php"><i class="fas fa-cog"></i> Admin</a>';
                }
                echo '<a href="' . $base_url . 'admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
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

<script>
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