<?php
session_start();//mantiene la sesion ya iniciada la anterior que uno registra
$mensaje_error = "";//mensajes para erro(usuario ya registrado, contraseña incorrecta etc..)

//este apartados nos irve para al tener errores en el login mande mensajes dependiendo el caso de error
$mensaje_error_login = ""; // para errores en login

if (!isset($_SESSION['intentos'])) {
    $_SESSION['intentos'] = 0;
}

if (!isset($_SESSION['bloqueo_tiempo'])) {
    $_SESSION['bloqueo_tiempo'] = null;
}


$server = "localhost";
$usuario = "root";
$pass = "";
$db = "blogweb";

//conexion
$conexion = new mysqli($server, $usuario, $pass, $db);
if ($conexion->connect_error){
    die("error: " .$conexion->connect_errno);
}else{
    echo "";
}

//este codigo es solo para crear una cuenta y registrarla a la base de satos
if(isset($_POST['register'])){//al precionar el botton login hace lo sig..
    $nombre = $_POST['name'];//aqui lo que hacemos es ingresar las variables que ingresemos en nuetsro formulario ejemplo $nombre = 'name'
    $email = $_POST['email'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);//aqui encriptamos nuestra contraseña con el pass_hash

     // Verifica si el correo ya está registrado
    $verificar_sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = $conexion->query($verificar_sql);

    if ($resultado->num_rows > 0) {
        $mensaje_error = "El correo ya está registrado. Intenta con otro.";
    } else {
    $sql = "INSERT INTO usuarios (name, email, pass) VALUES ('$nombre', '$email', '$pass')";
        if ($conexion->query($sql) === TRUE){
        echo "<script>window.location.href='../views/index.html';</script>";
    } else {
        $mensaje_error = "Error al registrar: " . $conexion->error;
    }
}
}

if(isset($_POST['login'])) {

    // Verificar si está bloqueado
    if ($_SESSION['intentos'] >= 3) {
        if (!$_SESSION['bloqueo_tiempo']) {
            $_SESSION['bloqueo_tiempo'] = time();
        }

        $tiempo_pasado = time() - $_SESSION['bloqueo_tiempo'];

        if ($tiempo_pasado < 30) {
            $mensaje_error_login = "Has sido bloqueado por " . (30 - $tiempo_pasado) . " segundos.";
        } else {
            // Reiniciar intentos después del tiempo de espera
            $_SESSION['intentos'] = 0;
            $_SESSION['bloqueo_tiempo'] = null;
        }
    }

    // Si no está bloqueado, validar login
    if ($_SESSION['intentos'] < 3 && empty($mensaje_error_login)) {
        $email = $_POST['email'];
        $pass = $_POST['pass'];

        $sql = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = $conexion->query($sql);

        if($resultado->num_rows > 0){
            $usuario = $resultado->fetch_assoc();

            if(password_verify($pass, $usuario['pass'])){
                $_SESSION['usuario'] = $usuario['name'];
                $_SESSION['intentos'] = 0; // reinicia intentos exitosamente
                echo "<script>window.location.href='../views/index.html';</script>";
            } else {
                $_SESSION['intentos']++;
                    $mensaje_error_login = "Contraseña incorrecta. Intento: " . $_SESSION['intentos'] . " de 3";

            }
        } else {
            $_SESSION['intentos']++;
                $mensaje_error_login = "Correo no registrado. Intento: " . $_SESSION['intentos'] . " de 3";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <link rel="icon" href="../assets/minilogo.png">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../views/css/login_style.css">
    <script src="../js/app.js"></script>
</head>

<body>

    <div class="container">
        <div class="signup-section">
            <header>Registrarse</header>
            <?php if (!empty($mensaje_error)) : ?>
    <div style="background-color: #ffe0e0; color: red; text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
        <?php echo $mensaje_error; ?>
    </div>
<?php endif; ?>

            <div class="social-buttons">
                <button><i class='bx bxl-google'></i> Usar Google</button>
            </div>
            <div class="separator">
                <div class="line"></div>
                <p>Or</p>
                <div class="line"></div>
            </div>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Full name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="pass" placeholder="Password" required>
                <a href="#">Olvide la contraseña?</a>
                <button type="submit" name="register" class="btn">Registrar</button>
            </form>
        </div>

        <div class="login-section">
            <header>Iniciar Session</header>
            <?php if (!empty($mensaje_error_login)) : ?>
    <div style="background-color: #ffe0e0; color: red; text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
        <?php echo $mensaje_error_login; ?>
    </div>

    <?php if ($_SESSION['intentos'] >= 3 && $_SESSION['bloqueo_tiempo']) : ?>
        <script>
            let segundos = <?php echo 30 - (time() - $_SESSION['bloqueo_tiempo']); ?>;
            const intervalo = setInterval(() => {
                if (segundos <= 0) {
                    clearInterval(intervalo);
                    location.reload();
                } else {
                    document.getElementById('bloqueo-texto').innerText = 'Bloqueado por ' + segundos + ' segundos';
                    segundos--;
                }
            }, 1000);
        </script>
        <p id="bloqueo-texto" style="text-align:center; color:red;"></p>
        <style>
            .login-section form {
                pointer-events: none;
                opacity: 0.5;
            }
        </style>
    <?php endif; ?>
<?php endif; ?>

            <div class="social-buttons">
                <button><i class='bx bxl-google'></i> Usar Google</button>
            </div>
            <div class="separator">
                <div class="line"></div>
                <p>Or</p>
                <div class="line"></div>
            </div>
            <form method="POST" action="">
                <input type="text" name="name" placeholder= "Full name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="pass" placeholder="Password" required>
                <a href="#">Olvide la contraseña?</a>
                <button type="submit" name='login' class="btn">Iniciar session</button>
            </form>
        </div>

    </div>

    <!-- imagen del logo -->
    <img src="../assets/logo.png" alt="Logo" class="logo">

    <script src="../js/login_script.js"></script>
</body>
</html>

