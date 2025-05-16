<?php
session_start();

$server = "localhost";
$user = "root";
$pass = "123456789";
$db = "blog";

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
            $query = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                // El email existe, obtener los datos del usuario
                $usuario = $resultado->fetch_assoc();
                
                // Verificar la contraseña
                if (password_verify($password, $usuario['pass'])) {
                    // Iniciar sesión
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['nombre'] = $usuario['name'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['rol'] = $usuario['rol'];
                    
                    // Actualizar último login
                    $updateQuery = "UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = ?";
                    $updateStmt = $conexion->prepare($updateQuery);
                    $updateStmt->bind_param("i", $usuario['id_usuario']);
                    $updateStmt->execute();
                    
                    // Redirigir según el rol
                    if ($usuario['rol'] === 'admin') {
                        header("Location: adminControl.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $error_msg = "Contraseña incorrecta";
                }
            } else {
                $error_msg = "El correo electrónico no está registrado";
            }
        } else {
            $error_msg = "Por favor, ingrese su correo electrónico y contraseña";
        }
    }
}

// Si ya hay una sesión activa, redirigir
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: adminControl.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../views/css/login_style.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (!empty($error_msg)): ?>
            <div class="error-message"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" name="login-button">Iniciar Sesión</button>
        </form>
        
        <div class="links">
            <a href="../index.php">Volver al inicio</a>
        </div>
    </div>
</body>
</html>