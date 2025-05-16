<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "123456789", "dbblog");

// Verifica que haya token en la URL
if (!isset($_GET['token'])) {
    die("Token no proporcionado.");
}

$token = $_GET['token'];

// Buscar token en la base de datos
$sql = "SELECT * FROM usuarios WHERE token_recuperacion='$token' AND fecha_expiracion_token > NOW()";
$resultado = $conexion->query($sql);

// Si el token es válido
if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nueva_pass = $_POST['nueva_pass'];
        $confirmar_pass = $_POST['confirmar_pass'];

        if ($nueva_pass !== $confirmar_pass) {
            $mensaje = "Las contraseñas no coinciden.";
        } else {
            $hash = password_hash($nueva_pass, PASSWORD_DEFAULT);

            // Actualizar contraseña y borrar token
            $update = "UPDATE usuarios SET pass='$hash', token_recuperacion=NULL, fecha_expiracion_token=NULL WHERE email='{$usuario['email']}'";
            if ($conexion->query($update)) {
                $mensaje = "¡Contraseña actualizada! Ya puedes iniciar sesión.";
            } else {
                $mensaje = "Error al actualizar contraseña.";
            }
        }
    }
} else {
    die("El token es inválido o ha expirado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="../views/css/login_style.css">
</head>
<body>
    <div class="container">
        <div class="signup-section">
            <header>Restablecer Contraseña</header>
            <?php if (isset($mensaje)) : ?>
                <p style="color: red; text-align: center;"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="nueva_pass" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar_pass" placeholder="Confirmar contraseña" required>
                <button type="submit" class="btn">Actualizar contraseña</button>
            </form>
        </div>
    </div>
</body>
</html>
