<!DOCTYPE html>
<html lang="es">
    <link rel="icon" href="assets/minilogo.png">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Involúcrate: Paz en Acción</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            max-width: 500px;
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

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #0C3958;
            color: #fff;
            line-height: 1.6;
            overflow-x: hidden;
        }


        .hero {
            background-image: url('protesta3.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            padding: 200px 20px;
            color: #ffffff;
            text-align: center;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(14, 49, 72, 0.52);
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section-subtitle {
            font-size: 1.5em;
            max-width: 700px;
            margin: 0 auto 40px;
            color: #D0EAFB;
        }

        section {
            padding: 60px 20px;
            text-align: center;
            transition: all 0.5s ease;
        }

        .block {
            background-color: #0C3958;
            padding: 80px 40px;
            position: relative;
            transition: background-color 0.5s ease;
            opacity: 0;
            transform: translateY(30px);
            text-align: right;
        }


        .block:nth-child(even) {
            background-color: #479AD2;
        }

        .block.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .block h3 {
            font-size: 1.8em;
            margin-bottom: 10px;
            margin-left: auto;
        }

        .block p {
            font-size: 1em;
            max-width: 600px;
            margin-left: auto;
            color: #bbb;
        }


        .wave {
            position: relative;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .wave svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }

        .wave-top {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2em;
            }

            .block h3 {
                font-size: 1.5em;
            }

            .icon {
                font-size: 2.5em;
            }
        }

        /* cartas de las peticiones */

        .services-section {
            background-color: #083050;
            padding: 60px 20px;
            text-align: center;
        }

        .cards-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 40px;
            align-items: stretch;
        }

        .service-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 100px;
            padding: 25px 20px;
            width: 280px;
            min-height: 340px;
            text-align: center;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .icono {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .icono img {
            max-width: 80px;
            height: auto;
        }


        .service-card h3 {
            font-size: 1.4em;
            margin-bottom: 10px;
        }

        .service-card p {
            font-size: 0.95em;
            color: #cddde6;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .card-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #B2DDF7;
            color: #024365;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .card-button:hover {
            background-color: #2D91C4;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .cards-container {
                flex-direction: column;
                align-items: center;
            }
        }

        /* Sección Organizaciones Aliadas */

        .allied-section {
            padding: 60px 20px;
            background: #0C3958;
            text-align: center;
            color: #ffffff;
        }

        .allied-section .section-title {
            font-size: 2.5em;
            color: #f5fbff;
            margin-bottom: 10px;
        }

        .allied-section .section-subtitle {
            font-size: 1.1em;
            color: #73aad5;
            margin-bottom: 40px;
        }

        .allied-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .allied-card {
            display: flex;
            align-items: flex-start;
            background: #0C3958;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.307);
            transition: transform 0.2s ease;
            text-align: left;
            color: #73aad5;
        }

        .allied-card:hover {
            transform: translateY(-5px);
        }

        .allied-card .icon {
            font-size: 1.8em;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }


        .allied-card.yellow .icon {
            background-color: #fff3cd;
        }

        .allied-card.green .icon {
            background-color: #d4edda;
        }

        .allied-card.purple .icon {
            background-color: #e2d6f3;
        }

        .allied-card.pink .icon {
            background-color: #fce4ec;
        }

        .allied-card.orange .icon {
            background-color: #ffe0b2;
        }

        .allied-card.blue .icon {
            background-color: #d1ecf1;
        }

        .allied-card h3 {
            margin: 0 0 8px;
            font-size: 1.2em;
        }

        .allied-card p {
            margin: 0;
            color: #fff7f7;
            font-size: 0.95em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .allied-section .section-title {
                font-size: 2em;
            }

            .allied-card {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .allied-card .icon {
                margin: 0 0 10px 0;
            }
        }

        /*marchas y eventos */

        .event-section {
            padding: 10px 20px;
            background-color: #83BFE3;
        }

        .event-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            justify-content: center;
        }

        .event-image img {
            max-width: 420px;
            height: auto;
        }

        .event-content {
            max-width: 800px;
            text-align: left;
            padding-bottom: 4%;
        }

        .event-title {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #0C3958;
        }

        .event-description {
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #133c58;
        }

        .event-details {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            color: #444;
        }

        .event-details li {
            margin-bottom: 8px;
        }

        .event-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #B2DDF7;
            color: #024365;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .event-button:hover {
            background-color: #367aad;
        }

        /*               FOOTER               */

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

        .social-icons {
            display: flex;
            gap: 1.5rem;
        }

        .social-icons span {
            border-radius: 50%;
            width: 3rem;
            height: 3rem;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-icons span i {
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
    </style>
</head>

<body>

    <header>
        <img src="logo.png" class="logo" onclick="location.href='index.php'">

        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <span class="search-icon">🔍</span>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="../views/contact.php">Contact</a>
            <a href="../views/about.php">Info</a>
            <a href="../admin/usuario.php" class="btn">Login</a>
        </nav>
    </header>



    <section class="hero">
        <div class="hero-content">
            <h2 class="section-title">PAZ EN ACCIÓN</h2>
            <p class="section-subtitle">¿Quieres sumarte al cambio y no sabes cómo? Te mostramos 3 formas de lograrlo
                desde donde estés.</p>
        </div>
    </section>


    <section class="services-section">
        <h2 class="section-title">Peticiones y Firmas Activas</h2>
        <p class="section-subtitle">Tu firma puede ser el primer paso hacia el cambio.
            Descubre causas urgentes y apoya con un solo clic.
        </p>
        <div class="cards-container">
            <div class="service-card">
                <div class="icono">
                    <img src="justiciajuvenil.png" alt="Justicia">
                </div>
                <h3>Justicia Juvenil</h3>
                <p>Firma para exigir reformas que protejan los derechos de los menores en situación vulnerable.</p>
                <a href="https://www.change.org" target="_blank" class="card-button">Firmar ahora</a>
            </div>

            <div class="service-card">
                <div class="icono">
                    <img src="corrupcionicono.png" alt="Corrupción">
                </div>
                <h3>Cero Corrupción</h3>
                <p>Apoya peticiones para la implementación de mecanismos de transparencia y rendición de cuentas en
                    instituciones públicas.</p>
                <a href="https://www.change.org" target="_blank" class="card-button">Firmar ahora</a>
            </div>

            <div class="service-card">
                <div class="icono">
                    <img src="ICONOJUSTICIA.png" alt="Verdad">
                </div>
                <h3>Verdad y Reparación</h3>
                <p>Hazte parte de los llamados que buscan justicia para víctimas del conflicto.</p>
                <a href="https://www.change.org" target="_blank" class="card-button">Firmar ahora</a>
            </div>
        </div>
    </section>


    <section class="event-section">
        <div class="event-container">
            <div class="event-image">
                <img src="cale.png" alt="Descripción del evento" />
            </div>
            <div class="event-content">
                <h2 class="event-title">Próximas Marchas y Eventos</h2>
                <p class="event-description">La paz también se camina. Infórmate, asiste y súmate a las voces que se
                    movilizan por un mundo más justo. Participa en los eventos que promueven la paz y la justicia, y
                    únete a los movimientos que generan un impacto positivo en tu comunidad. Cada paso cuenta.</p>
                <a href="calendario.html" class="event-button">Ver marchas</a>
            </div>
        </div>
    </section>

    <div class="wave">
        <svg viewBox="0 0 500 60" preserveAspectRatio="none">
            <path d="M0,0 C150,60 350,0 500,60 L500,00 L0,0 Z" style="fill:#83BFE3;"></path>
        </svg>
    </div>

    <section class="allied-section">
        <h2 class="section-title">Organizaciones Aliadas</h2>
        <p class="section-subtitle">Conoce colectivos que ya están actuando y encuentra tu lugar junto a ellos.</p>


        <div class="allied-grid">
            <div class="allied-card yellow">
                <div class="icon">🕊️</div>
                <div>
                    <h3>Human Rights Watch</h3>
                    <p>Documentación de violaciones a los derechos humanos.</p>
                </div>
            </div>
            <div class="allied-card green">
                <div class="icon">🌱</div>
                <div>
                    <h3>Peace Direct</h3>
                    <p>Apoyo a líderes de paz locales en zonas de conflicto.</p>
                </div>
            </div>
            <div class="allied-card purple">
                <div class="icon">📚</div>
                <div>
                    <h3>CIVICUS</h3>
                    <p>Apoyo a la sociedad civil, libertad de expresión y participación ciudadana.</p>
                </div>
            </div>
            <div class="allied-card pink">
                <div class="icon">⚖️</div>
                <div>
                    <h3>Amnistía Internacional</h3>
                    <p>Ayudan a garantizar justicia e instituciones responsables.</p>
                </div>
            </div>
            <div class="allied-card orange">
                <div class="icon">🤝</div>
                <div>
                    <h3>GPPAC</h3>
                    <p>Capacitan a mediadores comunitarios y crean redes de paz en zonas vulnerables.</p>
                </div>
            </div>
            <div class="allied-card blue">
                <div class="icon">📢</div>
                <div>
                    <h3> Pathfinders for Peaceful, Just and Inclusive Societies</h3>
                    <p>Agrupan a gobiernos, ONGs, universidades y el sector privado para implementar el ODS 16 en
                        diferentes países.</p>
                </div>
            </div>
        </div>
    </section>


    <div class="wave">
        <svg viewBox="0 0 500 60" preserveAspectRatio="none">
            <path d="M0,0 C150,60 350,0 500,60 L500,00 L0,0 Z" style="fill:#83BFE3;"></path>
        </svg>
    </div>

    <script>
        const blocks = document.querySelectorAll('.block');

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.2
        });

        blocks.forEach(block => observer.observe(block));
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
                            <li><a href="../views/about.php">Acerca de Nosotros</a></li>
                            <li><a href="../views/contact.php">Contactános</a></li>
                        </ul>
                    </div>
                </div>
                <div class="logo-footer">
                    <img src="logo.png" alt="Logo Peace In Progress">
                </div>
            </div>

            <div class="copyright">
                <p>
                    PEACE IN PROGRESS &copy; 2025
            </div>
        </div>
    </footer>

</body>

</html>