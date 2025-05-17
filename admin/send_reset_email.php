<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "123456789", "dbblog");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE LOWER(TRIM(email)) = LOWER(TRIM('$email'))";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $token = bin2hex(random_bytes(50)); // Generar un token aleatorio
        $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour')); // Expira en 1 hora

        // Actualizar el token y la fecha de expiración en la base de datos
        $update = "UPDATE usuarios SET token_recuperacion='$token', fecha_expiracion_token='$expiracion' WHERE email='$email'";
        $conexion->query($update);

        // Enviar correo electrónico
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'peaceinprogres1@gmail.com';
            $mail->Password = 'gkfl sigu nkoi fzkg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Receptor
            $mail->setFrom('peaceinprogres1@gmail.com', 'Recuperación de Contraseña');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de Contraseña';
            $mail->Body    = "Haz clic en el siguiente enlace para restablecer tu contraseña: 
            <a href='http://localhost/admin/reset_password.php?token=$token'>Restablecer Contraseña</a>";

            $mail->send();
            echo "Se ha enviado un correo con instrucciones para restablecer tu contraseña.";
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        echo "Este correo no está registrado en nuestro sistema.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recuperar Contraseña</title>
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../views/css/recuperar.css" />
  </head>

  <body>
    <div class="container">
      <div class="signup-section">
        <header>Recuperar Contraseña</header>
        <p style="text-align: center; margin-top: 20px; color: #b8daf5">
          Ingresa tu dirección de correo electrónico y te enviaremos un enlace
          para restablecer tu contraseña.
        </p>
        <form
          method="POST"
          action="send_reset_email.php"
          style="margin-top: 30px"
        >
          <input
            type="email"
            name="email"
            placeholder="Correo electrónico"
            required
          />
          <button type="submit" class="btn">Enviar enlace</button>
          <a href="../views/index.html" style="text-align: center; margin-top: 10px"
            >Volver al inicio de sesión</a
          >
        </form>
      </div>
    </div>

    <!-- imagen del logo -->
    <img src="../assets/logo.png" alt="Logo" class="logo" />

    <script src="../js/login_script.js"></script>
  </body>
</html>
