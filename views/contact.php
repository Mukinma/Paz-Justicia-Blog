<?php
// Iniciar la sesión
session_start();

// Importar configuración de la base de datos
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contacto - Peace in Progress</title>
    <link rel="icon" href="../assets/minilogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/contact.css" />
    <link rel="stylesheet" href="css/nav-fix.css" />
    <link rel="stylesheet" href="css/footer.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
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
        
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .contact-section {
            padding: 120px 40px 80px;
            position: relative;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .contact-section h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            color: #1a3a5c;
            position: relative;
        }
        
        .contact-section h2:after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: #7cbcbc;
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .contact-box {
            display: flex;
            max-width: 1100px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        }
        
        .left-panel {
            background-color: #1a3a5c;
            color: #fff;
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 40px;
        }
        
        .right-panel {
            background-color: #fff;
            flex: 1.5;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 20px;
        }
        
        .right-panel h3 {
            margin: 0 0 20px 0;
            font-size: 1.8rem;
            color: #1a3a5c;
            font-weight: 600;
        }
        
        .info-block {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .icon {
            font-size: 20px;
            background-color: #7cbcbc;
            color: #1a3a5c;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .label {
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .detail {
            margin: 5px 0 0;
            font-size: 0.95rem;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }
        
        .socials .icons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social {
            font-size: 18px;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 12px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .social:hover {
            transform: translateY(-5px);
            background-color: #7cbcbc;
            color: #1a3a5c;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 5px;
        }
        
        .glass-input {
            padding: 15px;
            border: 1px solid #e1e5ea;
            border-radius: 10px;
            font-size: 0.95rem;
            background: #fff;
            color: #333;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        
        .glass-input:focus {
            border-color: #7cbcbc;
            box-shadow: 0 0 0 3px rgba(124, 188, 188, 0.2);
            outline: none;
        }
        
        textarea.glass-input {
            min-height: 150px;
            resize: none;
        }
        
        .right-panel button {
            align-self: flex-end;
            padding: 12px 30px;
            background-color: #7cbcbc;
            color: #1a3a5c;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(124, 188, 188, 0.3);
        }
        
        .right-panel button:hover {
            background-color: #1a3a5c;
            color: #fff;
            transform: translateY(-2px);
        }
        
        @media screen and (max-width: 992px) {
            .contact-box {
                flex-direction: column;
            }
            
            .left-panel, .right-panel {
                padding: 40px 30px;
            }
            
            .contact-section {
                padding: 100px 20px 60px;
            }
        }
        
        @media screen and (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .contact-section h2 {
                font-size: 2rem;
            }
        }
        
        /* Estilos para el footer */
        .footer {
            background-color: #024365;
            color: #fff;
            padding: 0;
            margin-top: 40px;
        }
        
        .container-footer {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .container-container-container-footer {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .menu-footer {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .title-footer {
            font-weight: 600;
            font-size: 1.4rem;
            text-transform: uppercase;
            margin-bottom: 1.2rem;
            color: #fff;
        }
        
        .contact-info,
        .information {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .contact-info ul,
        .information ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .contact-info ul li,
        .information ul li {
            color: #fff;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .contact-info ul li i {
            width: 20px;
            color: #7cbcbc;
        }
        
        .information ul li a {
            text-decoration: none;
            color: #fff;
            transition: color 0.3s ease;
        }
        
        .information ul li a:hover {
            color: #7cbcbc;
        }
        
        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-icons span {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }
        
        .social-icons span:hover {
            transform: translateY(-5px);
        }
        
        .social-icons span i {
            color: #fff;
            font-size: 1rem;
        }
        
        .facebook {
            background-color: #3b5998;
        }
        
        .twitter {
            background-color: #00acee;
        }
        
        .instagram {
            background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d);
        }
        
        .logo-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-left: 2rem;
        }
        
        .logo-footer img {
            max-width: 180px;
            height: auto;
        }
        
        .copyright {
            display: flex;
            justify-content: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        
        .copyright p {
            font-weight: 400;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Corrección para las categorías */
        .dropdown-menu {
            padding: 0.8rem 0;
        }
        
        .dropdown-menu li {
            padding: 0;
        }
        
        .dropdown-menu li a {
            display: flex;
            align-items: center;
            padding: 8px 20px;
            text-decoration: none;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        
        .dropdown-menu li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .categoria-icono {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            object-fit: contain;
            border-radius: 4px;
        }
        
        @media screen and (max-width: 768px) {
            .container-container-container-footer {
                flex-direction: column;
            }
            
            .logo-footer {
                justify-content: center;
                margin: 1rem 0;
            }
            
            .menu-footer {
                justify-content: center;
            }
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
                    <li><a href="contact.php" class="active">Contacto</a></li>
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

    <section class="contact-section">
        <h2>Contáctanos</h2>
      <div class="contact-box">
        <!-- Panel izquierdo -->
        <div class="left-panel">
          <div class="info-block">
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <div>
              <p class="label">Nuestro Email</p>
                        <p class="detail">info@peaceinprogress.com</p>
            </div>
          </div>
          <div class="info-block">
            <div class="icon"><i class="fas fa-phone"></i></div>
            <div>
                        <p class="label">Teléfono</p>
                        <p class="detail">+34 123 456 789</p>
                    </div>
                </div>
                <div class="info-block">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <p class="label">Ubicación</p>
                        <p class="detail">Calle Principal #123, Madrid</p>
            </div>
          </div>
          <div class="socials">
                    <p class="label">Síguenos en Redes Sociales</p>
            <div class="icons">
              <a href="#"><i class="fab fa-facebook-f social"></i></a>
                        <a href="https://www.instagram.com/peace.in_progress/" target="_blank">
                <i class="fab fa-instagram social"></i>
              </a>
                        <a href="#"><i class="fab fa-twitter social"></i></a>
                        <a href="https://www.tiktok.com/@peaceinprogressblog" target="_blank">
                <i class="fab fa-tiktok social"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Panel derecho -->
        <form class="right-panel" id="contactForm">
                <h3>Envíanos un Mensaje</h3>
          <div class="form-row">
            <input
              type="text"
                        placeholder="Nombre"
              class="glass-input"
              name="name"
              required
            />
            <input
              type="text"
                        placeholder="Apellido"
              class="glass-input"
              name="lastname"
              required
            />
          </div>
          <input
            type="email"
            placeholder="Email"
            class="glass-input"
            name="email"
            required
          />
          <textarea
                    placeholder="Mensaje"
            class="glass-input"
            name="message"
            required
          ></textarea>
                <button type="submit">Enviar Mensaje</button>
        </form>
      </div>
    </section>

    <script src="../js/contact.js"></script>
    <script src="../views/js/nav-fix.js"></script>
    <script src="../js/profile-menu.js"></script>
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

    <?php include 'includes/footer.php'; ?>
  </body>
</html>
