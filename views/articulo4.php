<!DOCTYPE html>
<html lang="es">
    <link rel="icon" href="../assets/minilogo.png">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Artículo - Marcha de Paz</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #35688e;
            color: #fff;
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
            max-width: 600px;
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

        /* Imagen y título del artículo */
        .article-header {
            width: 100%;
            height: 600px;
            background: url('image/img1.jpg') no-repeat center center / cover;
            position: relative;
        }

        .article-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
        }

        .article-header h1 {
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 3.2em;
            color: white;
            z-index: 1;
            padding: 0 20px;
            text-align: center;
        }

        .article-header h2 {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 1em;
            color: white;
            z-index: 1;
            text-align: right;
        }


        .container {
            display: flex;
            max-width: 1400px;
            gap: 40px;
            padding: 0 20px;
            padding-left: 0;
            margin-left: 0;
        }

        .main-content {
            position: relative;
            top: -200px;
            flex: 9;
            background: #35688e;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #fff;
            z-index: 2;
        }

        .main-content h2 {
            font-family: Georgia, serif;
            font-size: 2em;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .main-content p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #e0e0e0;
            margin-bottom: 20px;
            text-align: justify;
        }

        .sidebar {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding-top: 20px;
        }

        .card {
            background: #82aed0;
            border-radius: 10px;
            padding: 10px;
            font-size: 1em;
            color: #333;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            font-size: 1em;
            margin-bottom: 5px;
        }

        .card p {
            font-size: 0.95em;
        }

        .carousel {
            overflow-x: auto;
            display: flex;
            gap: 10px;
            scroll-snap-type: x mandatory;
            padding: 5px;
        }

        .carousel::-webkit-scrollbar {
            display: none;
        }

        .carousel-card {
            min-width: 100px;
            background: #82aed0;
            color: #333;
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
            scroll-snap-align: start;
            flex-shrink: 0;
            font-size: 0.7em;
        }

        .carousel-card h4 {
            font-size: 0.8em;
            margin-bottom: 4px;
        }

        .social-icons {
            display: flex;
            gap: 10px;
            padding: 15px;
        }

        .social-icons a {
            text-decoration: none;
            background: #82aed0;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333333;
            transition: background 0.3s;
        }

        .social-icons a:hover {
            background: #35688e;
            color: #fff;
        }

        a.back {
            display: inline-block;
            margin-top: 30px;
            color: #82aed0;
            text-decoration: none;
            font-weight: bold;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        a.back:hover {
            border-color: #82aed0;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                flex-direction: row;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .card,
            .social-icons {
                flex: 1 1 100%;
            }
        }

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

        .social-icons2 {
            display: flex;
            gap: 1.5rem;
        }

        .social-icons2 span {
            border-radius: 50%;
            width: 3rem;
            height: 3rem;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-icons2 span i {
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

        .interaction-buttons {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .like-button,
        .share-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 1.2em;
            color: #ccc;
            transition: color 0.3s ease;
        }

        .like-button.liked {
            color: #ff4f4f;
        }

        .share-button:hover {
            color: #4faaff;
        }

        .like-button .like-count,
        .share-button span {
            font-size: 1rem;
            color: #fff;
        }

        .interaction-buttons {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .like-button,
        .share-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 1.2em;
            color: #ccc;
            transition: color 0.3s ease;
        }

        .like-button.liked {
            color: #83d0f6;
        }

        .share-button:hover {
            color: #4faaff;
        }

        .like-button .like-count,
        .share-button span {
            font-size: 1rem;
            color: #fff;
        }

        /* Estilo para el mensaje flotante */
        .message {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s, visibility 0.5s;
            z-index: 1000;
        }

        .message.show {
            opacity: 1;
            visibility: visible;
        }
      </style>
    </head>

<body>

    <header>
        <img src="../assets/logo.png" class="logo" onclick="location.href='index.php'">

        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <span class="search-icon">🔍</span>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="contact.php">Contact</a>
            <a href="about.php">Info</a>
            <a href="../admin/usuario.php" class="btn">Login</a>
        </nav>
    </header>

    <div class="article-header">
        <h1>LA GUERRA DE RUSIA Y UCRANIA</h1>
        <h2>Autor: Juan</h2>
    </div>

    <div class="container">
        <div class="main-content">
            <h2>La censura de las protestas</h2>
            <p>
                Desde el inicio de la invasión a gran escala de Ucrania en febrero de 2022, el gobierno ruso ha
                intensificado su represión contra cualquier forma de disidencia, implementando leyes que criminalizan la
                protesta pacífica y la libertad de expresión. Estas medidas han resultado en la detención y
                encarcelamiento de miles de ciudadanos que se oponen a la guerra, según informes de Amnistía
                Internacional.
            </p>

            <h3>Legislación para silenciar la disidencia</h3>
            <p>
                Poco después de iniciada la guerra, Rusia introdujo leyes de censura que penalizan la difusión de
                "información falsa" y la "desacreditación" de las fuerzas armadas, con penas de hasta 15 años de
                prisión. Estas leyes han sido utilizadas para castigar a quienes expresan opiniones contrarias a la
                narrativa oficial sobre el conflicto en Ucrania.
            </p>

            <h3>Represalias más allá del encarcelamiento</h3>
            <p>
                Además de las penas de prisión, las autoridades rusas han empleado otras tácticas para reprimir la
                disidencia:
                • Confiscación de propiedades: En 2024, se aprobó una ley que permite confiscar bienes de personas
                acusadas bajo las leyes de censura de guerra
                • Represión a menores: Niños y niñas han sido víctimas de persecución política debido a las opiniones de
                sus padres o por expresar su desacuerdo con la guerra
                • Negación de contacto familiar: A los detenidos se les ha negado sistemáticamente el contacto con sus
                familias, como en el caso del político de oposición Vladimir Kara-Murza, quien estuvo más de un año sin
                comunicación con sus seres queridos.

            </p>

            <p>
                Amnistía Internacional insta a la comunidad internacional a exigir la derogación de las leyes de censura
                de guerra en Rusia y la liberación inmediata de todas las personas encarceladas por expresar
                pacíficamente sus opiniones. La organización también anima a firmar peticiones y enviar mensajes de
                solidaridad a los presos de conciencia.
                La represión en Rusia ha alcanzado niveles alarmantes, equiparando las penas por protestar contra la
                guerra con las impuestas por delitos graves como el atraco a mano armada. Es fundamental que la
                comunidad internacional se solidarice con quienes defienden la paz y la libertad de expresión en Rusia.

            </p>


            <img src="../image/img1.jpg" alt="Imagen ilustrativa" style="width: 100%; margin-top: 30px; margin-bottom: 30px;">

            <div class="interaction-buttons">
                <div class="like-button" onclick="toggleLike(this)">
                    <i class="fa-solid fa-heart"></i>
                    <span class="like-count">0</span>
                </div>
                <div class="share-button" onclick="shareArticle()">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span>Compartir</span>
                </div>
            </div>

            <div id="copyMessage" class="message">
                ¡Enlace copiado al portapapeles!
            </div>


            <a href="../index.php" class="back">← Volver al inicio</a>
        </div>

        <aside class="sidebar">
            <div class="card">
                <h3>Autor</h3>
                <p>Resumen del autor y fecha de publicación.</p>
            </div>

            <div class="card">
                <h3>Populares</h3>
                <div class="carousel">
                    <div class="carousel-card">
                        <h4>Nota 1</h4>
                        <p>Resumen 1.</p>
                    </div>
                    <div class="carousel-card">
                        <h4>Nota 2</h4>
                        <p>Resumen 2.</p>
                    </div>
                    <div class="carousel-card">
                        <h4>Nota 3</h4>
                        <p>Resumen 3.</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Recomendado</h3>
                <p>Contenido útil o sugerido para el lector.</p>
            </div>

            <div class="social-icons">
                <a href="#"><img src="https://img.icons8.com/ios-filled/20/facebook--v1.png" alt="Facebook" /></a>
                <a href="#"><img src="https://img.icons8.com/ios-filled/20/linkedin.png" alt="LinkedIn" /></a>
                <a href="#"><img src="https://img.icons8.com/ios-filled/20/twitterx.png" alt="X" /></a>
                <a href="#"><img src="https://img.icons8.com/ios-filled/20/instagram-new.png" alt="Instagram" /></a>
            </div>

            <div class="card">
                <h3>Comentarios</h3>
                <form id="commentForm">
                    <textarea id="commentInput" placeholder="Escribe tu comentario..." rows="3"
                        style="width: 100%; padding: 5px; border-radius: 5px; border: none;"></textarea>
                    <button type="submit"
                        style="margin-top: 10px; padding: 6px 12px; background: #35688e; color: white; border: none; border-radius: 5px; cursor: pointer;">Publicar</button>
                </form>
                <div id="commentList" style="margin-top: 10px;"></div>
            </div>

            <script>
                const form = document.getElementById('commentForm');
                const input = document.getElementById('commentInput');
                const list = document.getElementById('commentList');

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const text = input.value.trim();
                    if (text !== '') {
                        const comment = document.createElement('div');
                        comment.style.display = 'flex';
                        comment.style.alignItems = 'center';
                        comment.style.background = '#d0e3f1';
                        comment.style.padding = '5px';
                        comment.style.borderRadius = '5px';
                        comment.style.marginBottom = '10px';

                        // Imagen del usuario
                        const img = document.createElement('img');
                        img.src = 'Usuario.webp';
                        img.alt = 'Usuario';
                        img.style.width = '40px';
                        img.style.height = '40px';
                        img.style.borderRadius = '50%';
                        img.style.marginRight = '10px';

                        // Comentario de texto
                        const commentText = document.createElement('p');
                        commentText.textContent = text;
                        commentText.style.margin = '0';

                        // Agregar la imagen y el texto al comentario
                        comment.appendChild(img);
                        comment.appendChild(commentText);
                        list.prepend(comment); // Agrega el comentario arriba
                        input.value = '';
                    }
                });

                // like y compartir
                function toggleLike(button) {
                    const icon = button.querySelector('i');
                    const count = button.querySelector('.like-count');
                    let likes = parseInt(count.textContent);

                    if (button.classList.contains('liked')) {
                        button.classList.remove('liked');
                        count.textContent = likes - 1;
                    } else {
                        button.classList.add('liked');
                        count.textContent = likes + 1;
                    }
                }

                function shareArticle() {
                    const url = window.location.href;
                    const title = document.title;

                    if (navigator.share) {
                        navigator.share({
                            title: title,
                            url: url
                        }).catch((error) => console.log('Error al compartir:', error));
                    } else {
                        navigator.clipboard.writeText(url).then(() => {
                            showCopyMessage();
                        });
                    }
                }

                function showCopyMessage() {
                    const message = document.getElementById('copyMessage');
                    message.classList.add('show');

                    setTimeout(() => {
                        message.classList.remove('show');
                    }, 3000);
                }
            </script>

        </aside>
    </div>

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
                        <div class="social-icons2">
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
                            <li><a href="contact.php">Contactános</a></li>
                        </ul>
                    </div>
                </div>
                <div class="logo-footer">
                    <img src="../image/logo.png" alt="Logo Peace In Progress">
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
