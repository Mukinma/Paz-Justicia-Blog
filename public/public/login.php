<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "dbadmin";

// Conectar a la base de datos
$conexion = new mysqli($server, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Variable de error
$error_msg = "";

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login-button'])) {
        // Verificar si 'email' y 'password' están definidos
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = mysqli_real_escape_string($conexion, $_POST['email']);
            $password = mysqli_real_escape_string($conexion, $_POST['password']);

            // Consulta para verificar si el email existe
            $query = "SELECT * FROM admin1 WHERE email = '$email'";
            $resultado = mysqli_query($conexion, $query);

            if (mysqli_num_rows($resultado) > 0) {
                // El email existe, obtener los datos del usuario
                $usuario = mysqli_fetch_assoc($resultado);  
                $hashed_password = $usuario['password']; // Suponiendo que la contraseña está almacenada como hash

                // Verificar si la contraseña coincide
                if (password_verify($password, $hashed_password)) {
                    // La contraseña es correcta, redirigir al usuario
                    exit(); // Asegúrate de llamar a exit() después de header para evitar más ejecuciones.
                } else {
                    // Contraseña incorrecta
                    header("Location: index.html");

                }
            } else {
                // El email no existe en la base de datos
                $error_msg = "El correo electrónico no está registrado. Por favor, verifica.";
            }
        } else {
            // En caso de que no se haya enviado el email o la contraseña
            $error_msg = "Por favor, ingrese su correo electrónico y contraseña.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="main-container">
        <img src="./images/PeaceInProgress.jpeg" alt="Imagen de teclado con la palabra peace" class="login-image">
        <div class="right-container">
            <h2>WELCOME!</h2>
            <form action="" method="POST" class="login-form">
                <div class="login-container">
                    <h3>Log In</h3>

                    <!-- Mostrar mensaje de error si existe -->
                    <?php if (!empty($error_msg)): ?>
                        <div class="error-message">
                            <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <a href="forgotpassword.html" class="forgot-password"><label for="recordar">Forgot Password?</label></a>
                    <div class="button-group">
                        <button type="submit" name="login-button" class="login-button">Log In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

