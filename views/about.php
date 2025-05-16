<!DOCTYPE html>
<html lang="es">
    <link rel="icon" href="../assets/minilogo.png">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Peace in Progress</title>
    <link rel="stylesheet" href="css/about.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header>
        <div class="header-container">
            <img src="../assets/logo.png" class="logo" onclick="location.href='index.php'">
            
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

    <!-- Sección About Us -->
    <section class="about">
        <div class="about-container">
            <div class="about-text">
                <h1>About Us</h1>
                <p>
                    <strong>PEACE IN PROGRESS</strong> is a blog dedicated to informing, raising awareness, and
                    promoting peace, justice, and inclusive societies.
                    Our mission is to raise awareness about social conflicts, impunity, and lack of access to justice.
                    We believe that change begins with information, and a well-informed society has the power to build a
                    just future.
                </p>
            </div>
            <div class="about-images">
                <img src="../assets/imagenabout.jpg" alt="Peace Image">
                <img src="../assets/logo.png" alt="Logo Peace in Progress">
            </div>
        </div>

        <!-- Carrusel del equipo -->
        <div class="news-carousel">
            <h2>Meet our team</h2>

            <div class="carousel-wrapper">
                <button class="arrow left">&#10094;</button>

                <div class="carousel-container">
                    <div class="carousel-track">
                        <!-- Tarjetas q se insertan dinámicamente -->
                    </div>
                </div>

                <button class="arrow right">&#10095;</button>
            </div>

            <div class="carousel-dots"></div>
        </div>
    </section>

    <script src="../js/about-script.js"></script>
</body>

</html>