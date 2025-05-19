<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Involúcrate: Paz en Acción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../views/css/style.css">
    <link rel="stylesheet" href="../views/css/footer.css">
    <link rel="stylesheet" href="../views/css/involucrate.css">
</head>

<body>
    <?php include_once '../views/includes/header.php'; ?>

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
                    <img src="../assets/involucrate/justiciajuvenil.png" alt="Justicia">
                </div>
                <h3>Justicia Juvenil</h3>
                <p>Firma para exigir reformas que protejan los derechos de los menores en situación vulnerable.</p>
                <a href="https://www.change.org" target="_blank" class="card-button">Firmar ahora</a>
            </div>

            <div class="service-card">
                <div class="icono">
                    <img src="../assets/involucrate/corrupcionicono.png" alt="Corrupción">
                </div>
                <h3>Cero Corrupción</h3>
                <p>Apoya peticiones para la implementación de mecanismos de transparencia y rendición de cuentas en
                    instituciones públicas.</p>
                <a href="https://www.change.org" target="_blank" class="card-button">Firmar ahora</a>
            </div>

            <div class="service-card">
                <div class="icono">
                    <img src="../assets/involucrate/ICONOJUSTICIA.png" alt="Verdad">
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
                <img src="../assets/involucrate/cale.png" alt="Descripción del evento" />
            </div>
            <div class="event-content">
                <h2 class="event-title">Próximas Marchas y Eventos</h2>
                <p class="event-description">La paz también se camina. Infórmate, asiste y súmate a las voces que se
                    movilizan por un mundo más justo. Participa en los eventos que promueven la paz y la justicia, y
                    únete a los movimientos que generan un impacto positivo en tu comunidad. Cada paso cuenta.</p>
                <a href="calendario.php" class="event-button">Ver marchas</a>
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

    <?php include_once '../views/includes/footer.php'; ?>
</body>

</html>