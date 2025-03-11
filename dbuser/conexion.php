<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "bdadmin";

$conexion = new mysqli($server, $user, $pass, $db);
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
            <form action="conexion.php" method="POST" class="login-form">
                <div class="login-container">
                    <h3>Log In</h3>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <a href="#" class="forgot-password"><label for="recordar">Forgot Password?</label></a>
                    <div class="button-group">
                        <button type="submit" class="login-button" name="registro">Sign up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

<?php
if(isset($_POST['registro'])){
    $email = $_POST ['email'];
    $password = $_POST ['password'];

    $insertardatos = "INSERT INTO dbad VALUES('$email','$password','')";
    $ejecutarinsertar = mysqli_query ($conexion, $insertardatos);

    if($ejecutarinsertar){
        header("Location: index.html");
        exit();
    } else{
        echo "error al insertar los datos";
    }
}
?>

</html>