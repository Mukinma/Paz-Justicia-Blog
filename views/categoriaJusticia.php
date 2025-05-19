<!DOCTYPE html>
<html lang="es">
    <link rel="icon" href="../assets/minilogo.png">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Justicia y Derechos Humanos - Peace in Progress</title>
    <link rel="stylesheet" href="css/categorias.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../assets/minilogo.png">
</head>

<body>

    <header>
        <div class="header-container">
            <img src="../assets/logo.png" class="logo" onclick="location.href='../index.php'">
            
            <div class="profile-section">
                <?php
                session_start();
                if (!isset($_SESSION['usuario'])) {
                    echo '<a href="../admin/usuario.php" class="login-btn">Iniciar Sesión</a>';
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

    <!-- Hero principal -->
    <section class="hero-section" style="background-image: url('JUSTICIAYDERECHOS.jpg');">
        <div class="hero-content">
            <small> Categoria • 3 articulos</small>
            <h1>JUSTICIA Y DERECHOS HUMANOS</h1>
            <p>Acceso a la justicia, abusos de poder, sistema penitenciario y más. Esta sección examina la defensa 
               de los derechos fundamentales como base de sociedades pacíficas.
            </p>
        </div>
    </section>

    <!-- Título de sección -->
    <h2 class="section-title">Articulos</h2>

    <!-- Artículos -->
    <section class="grid-container" id="articulos"></section>

    <!-- Botón Ver más -->
    <div class="ver-mas">
        <button onclick="cargarMas()">Ver más artículos</button>
    </div>

    <script>
        const articulos = [
            { href: "articulo1.html", img: "img2.jpg", fecha: "Abril 18, 2025", autor: "Juan Pérez", titulo: "La guerra de rusia con ucrania", resumen: "En rusia la gente esta alterada pq blablablablbaba" },
            { href: "articulo2.html", img: "img3.jpg", fecha: "Abril 5, 2025", autor: "María Gómez", titulo: "La guerra de rusia con ucrania", resumen: "A moderate incline runs towards the foot of Maybury Hill, and down this we clattered..." },
            { href: "articulo3.html", img: "img4.jpg", fecha: "Abril 2, 2025", autor: "Luis Torres", titulo: "La guerra de rusia con ucrania", resumen: "Through two long weeks I wandered, stumbling through the nights guided only by the stars..." },
            { href: "articulo4.html", img: "img5.jpg", fecha: "Mayo 13, 2025", autor: "Carla Ruiz", titulo: "La guerra de rusia con ucrania", resumen: "Una travesía que desafía la lógica y la razón en busca de respuestas..." },
            { href: "articulo5.html", img: "img6.jpg", fecha: "Mayo 12, 2025", autor: "Pedro Ledesma", titulo: "La guerra de rusia con ucrania", resumen: "Los lagos antiguos revelan secretos escondidos bajo la superficie..." },
            { href: "articulo6.html", img: "img7.jpg", fecha: "Mayo 10, 2025", autor: "Ana Ortega", titulo: "La guerra de rusia con ucrania", resumen: "Caminando entre ruinas, la historia cobra vida en cada piedra..." },
            { href: "articulo7.html", img: "img8.jpg", fecha: "Mayo 8, 2025", autor: "Lucas Peña", titulo: "La guerra de rusia con ucrania", resumen: "Una expedición que revela más de lo esperado en el corazón del desierto..." },
            { href: "articulo8.html", img: "img9.jpg", fecha: "Mayo 6, 2025", autor: "Sofía Blanco", titulo: "La guerra de rusia con ucrania", resumen: "Un manuscrito perdido pone en jaque la historia tal como la conocemos..." }
        ];

        const porPagina = 6;
        let index = 0;

        function cargarMas() {
            const contenedor = document.getElementById("articulos");
            const fin = index + porPagina;
            const grupo = articulos.slice(index, fin);

            grupo.forEach(articulo => {
                const html = `
          <a href="${articulo.href}" class="card">
            <img src="${articulo.img}" alt="${articulo.titulo}">
            <div class="card-content">
              <small>${articulo.fecha} • por ${articulo.autor}</small>
              <h3>${articulo.titulo}</h3>
              <p>${articulo.resumen}</p>
            </div>
          </a>`;
                contenedor.insertAdjacentHTML('beforeend', html);
            });

            index = fin;

            if (index >= articulos.length) {
                document.querySelector('.ver-mas').style.display = 'none';
            }
        }

        window.onload = () => {
            cargarMas();
        };
    </script>

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
                            <li><a href="#">Contactános</a></li>
                        </ul>
                    </div>
                </div>
                <div class="logo-footer">
                    <img src="../assets/logo.png" alt="Logo Peace In Progress">
                </div>
            </div>

            <div class="copyright">
                <p>
                    PEACE IN PROGRESS &copy; 2025
                </p>
            </div>
        </div>
    </footer>

</body>

</html>