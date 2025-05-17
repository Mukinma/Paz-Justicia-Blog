<!DOCTYPE html>
<html lang="en">

<link rel="icon" href="assets/minilogo.png">


<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PeaceInProgress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="views/css/style.css" />
    <link rel="stylesheet" href="views/css/index_style.css">
    <link rel="stylesheet" href="views/css/noticias.css">
</head>

<body>
        <header>
        <img src="assets/logo.png" class="logo" onclick="location.href='index.php'">

        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <span class="search-icon">🔍</span>
        </div>

        <nav>
        <select class="traductor-select" onchange="translatePage(this.value)">
            <option value="es">Español</option>
            <option value="en">Inglés</option>
        </select>
            <a href="index.php">Home</a>
            <a href="views/contact.php">Contact</a>
            <a href="views/about.php">Info</a>
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
            <div class="item">
                <img src="image/img1.jpg" />
                <div class="content">
                    <div class="title">LA CENSURA</div>
                    <div class="topic">DE LAS PROTESTAS</div>
                    <div class="des">
                        En Rusia, la gente sigue protestando contra la guerra </br> de Ucrania. Sin embargo, las autoridades
                        rusas están decididas </br> a acabar con las protestas por completo...
                    </div>
                    <div class="buttons">
                        <a href="views/articulo1.php" class="see-more-button">Ver mas</a>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/img2.jpg">
                <div class="content">
                    <div class="title">LA IGLESIA</div>
                    <div class="topic"></div>
                    <div class="des">
                        En un contexto de creciente violencia que afecta de manera alarmante a los jóvenes de México, la
                        Iglesia Católica ha emitido un mensaje de solidaridad y acción, invitando a los agentes de
                        pastoral de adolescentes y jóvenes a unirse en la tarea urgente de construir la paz en el país.
                    </div>
                    <div class="buttons">
                        <a href="views/articulo2.php" class="see-more-button">Ver mas</a>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/img3.jpg">
                <div class="content">
                    <div class="title">MARCHA </div>
                    <div class="topic">DE PAZ</div>
                    <div class="des">
                        Culiacán, Sinaloa, ha sido escenario de dos manifestaciones en menos de 72 horas.
                        La primera, ocurrida hace dos días, culminó con la irrupción de manifestantes en el Palacio de
                        Gobierno. La segunda, este domingo, reunió a miles de personas que exigieron justicia por las
                        victimas.
                    </div>
                    <div class="buttons">
                        <a href="views/articulo3.php" class="see-more-button">Ver mas</a>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/img4.jpg">
                <div class="content">
                    <div class="title">PLAN DE </div>
                    <div class="topic">SEGURIDAD</div>
                    <div class="des">
                        "No va a regresar la guerra contra el narco", advirtió Sheinbaum en conferencia de prensa.
                        "Nosotros vamos a usar prevención y atención a las causas (…) Los delitos de alto impacto van a
                        disminuir porque hay una estrategia y se va a cumplir"..."
                    </div>
                    <div class="buttons">
                        <a href="views/articulo4.php" class="see-more-button">SEE MORE</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- list thumnail -->
        <div class="thumbnail">
            <div class="item">
                <img src="image/img1.jpg">
                <div class="content">
                    <div class="title">
                        LA CENSURA
                    </div>
                    <div class="description">
                        de las protestas contra la guerra
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/img2.jpg">
                <div class="content">
                    <div class="title">
                        LA IGLESIA
                    </div>
                    <div class="description">
                        urge a contruir la paz ante ola de violencia
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/img3.jpg">
                <div class="content">
                    <div class="title">
                        MARCHA DE PAZ
                    </div>
                    <div class="description">
                        en Sinaloa por los ciudadanos
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="image/img4.jpg">
                <div class="content">
                    <div class="title">
                        PLAN DE SEGURIDAD
                    </div>
                    <div class="description">
                        Anunció Claudia Sheinbaum
                    </div>
                </div>
            </div>
        </div>
        <!-- next prev -->

        <div class="arrows">
            <button id="prev">&lt;</button>
            <button id="next">&gt;</button>
        </div>
        <!-- time running -->
        <div class="time"></div>
    </div>


    <section class="destacados-section">
        <h2>Más Recientes</h2>
        <div class="destacados-container">

            <!-- Noticia principal -->
            <div class="noticia-principal">
                <img src="image/img5.jpg" alt="Imagen principal">
                <small>19 de marzo de 2025 | Administrador</small>
                <h3><a href="views/articulo1.php">México: Autoridades deberían investigar aparente sitio de asesinatos
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
                        <h4><a href="views/articulo2.php">Israel reproduce los métodos militares de Gaza en Cisjordania</a>
                        </h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
                <div class="noticia-secundaria">
                    <img src="image/img2.jpg" alt="">
                    <div>
                        <small>25 de marzo de 2025 | Administrador</small>
                        <h4><a href="views/articulo3.php">La FIFA debe reconocer y apoyar al equipo de mujeres afganas en el
                                exilio</a></h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
                <div class="noticia-secundaria">
                    <img src="image/img3.jpg" alt="">
                    <div>
                        <small>22 de enero de 2025 | Administrador</small>
                        <h4><a href="views/articulo4.php">Órdenes ejecutivas de Trump amenazan un amplio espectro de derechos
                                humanos</a></h4>
                        <p>Las autoridades mexicanas deberían llevar a cabo una investigación exhaustiva e imparcial
                            sobre el reciente hallazgo...</p>
                    </div>
                </div>
                <div class="noticia-secundaria">
                    <img src="image/img4.jpg" alt="">
                    <div>
                        <small>24 de enero de 2025 | Administrador</small>
                        <h4><a href="views/articulo4.php">Estados Unidos cierra sus puertas a refugiados, solicitantes de
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
            <a href="involucrate/involucrate.php" class="boton-involucrarse">Actuar Ahora</a>
        </div>
    </section>

    <section class="categorias-temas">
        <h2 class="titulo-categorias">Explora por Temas</h2>

        <div class="grid-categorias">

            <div class="categoria-card">
                <div class="icono">
                    <img src="assets/imginvolucrate/ICONOJUSTICIA.png" alt="Justicia y Derechos Humanos">
                    <img src="image/ICONOJUSTICIA.png" alt="Justicia y Derechos Humanos">
                </div>
                <h3 class="titulo">Justicia y Derechos Humanos</h3>
                <p class="descripcion">Acceso a la justicia, abusos de poder, sistema penitenciario..</p>
                <a href="views/categoriaJusticia.php" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="assets/imginvolucrate/ICONOPAZ.png" alt="Paz y Conflictos">
                    <img src="image/ICONOPAZ.png" alt="Paz y Conflictos">
                </div>
                <h3 class="titulo">Paz y Conflictos</h3>
                <p class="descripcion">Cobertura de guerras, procesos de reconciliación y contextos de conflicto global.
                </p>
                <a href="views/categoriapaz.php" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="image/ICONODIVERSIDAD.png" alt="Igualdad y Diversidad">
                </div>
                <h3 class="titulo">Igualdad y Diversidad</h3>
                <p class="descripcion">Causas y luchas por una sociedad más tolerante e inclusiva.</p>
                <a href="views/categoriaIgualdad.php" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="assets/imginvolucrate/ICONOPARTICIPACION.png" alt="Participación Ciudadana">
                    <img src="image/ICONOPARTICIPACION.png" alt="Participación Ciudadana">
                </div>
                <h3 class="titulo">Participación Ciudadana</h3>
                <p class="descripcion">Activismo, protestas pacíficas y organizaciones que protegen.</p>
                <a href="views/categoriaParticipacion.php" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="assets/imginvolucrate/ICONOCORRUPCION.png" alt="Corrupción y Transparencia">
                    <img src="image/ICONOCORRUPCION.png" alt="Corrupción y Transparencia">
                </div>
                <h3 class="titulo">Corrupción y Transparencia</h3>
                <p class="descripcion">Investigaciones sobre corrupción y reformas por un sistema justo.</p>
                <a href="views/categoriacorrupcion.php" class="btn-categoria">Ver artículos</a>
            </div>

            <div class="categoria-card">
                <div class="icono">
                    <img src="assets/imginvolucrate/ICONOPOLITICA.png" alt="Politica y gobernanza">
                    <img src="image/ICONOPOLITICA.png" alt="Politica y gobernanza">
                </div>
                <h3 class="titulo">Politica y gobernanza</h3>
                <p class="descripcion">Cobertura de politica, programas y acciones del gobierno para fortalecer la paz y
                    seguridad.</p>
                <a href="views/categoriapolitica.php" class="btn-categoria">Ver artículos</a>
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
            <img src="assets/imginvolucrate/formulario2.png" alt="Persona escribiendo experiencia">
            <img src="image/formulario2.png" alt="Persona escribiendo experiencia">
        </div>
    </section>


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
                            <li><a href="about.php">Acerca de Nosotros</a></li>
                            <li><a href="contact.php">Contáctanos</a></li>
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
    <script src="js/app.js"></script>
    <script defer src="js/traslate.js"></script>
</body>

</html>