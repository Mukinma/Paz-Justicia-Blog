<!DOCTYPE html>
<html lang="en">
  <link rel="icon" href="../assets/minilogo.png" />
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recuperar Contraseña</title>
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/recuperar.css" />
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
          action="../admin/send_reset_email.php"
          style="margin-top: 30px"
        >
          <input
            type="email"
            name="email"
            placeholder="Correo electrónico"
            required
          />
          <button type="submit" class="btn">Enviar enlace</button>
          <a href="../index.php" style="text-align: center; margin-top: 10px"
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
