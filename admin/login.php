<?php
session_start();

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
//iniciar contador
if(!isset($_SESSION['login_attempts'])){
    $_SESSION['login_attempts'] = 0;
}
//verificar si hay un bloqueo y si ha pasado el tiempo de espera
if(isset($_SESSION['blocked_time'])){
    $tiempo_transcurrido = time() - $_SESSION['blocked_time'];
    if($tiempo_transcurrido >= 30){
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['blocked_time']);
    }
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login-button'])) {
            //bloquar en caso de aver 4 intentos o mas
            if($_SESSION['login_attempts'] >= 3){
                if(isset($_SESSION['blocked_time'])){
                    $_SESSION['blocked_time'] = time();
                }
                $tiempo_restante = 30 - (time() - $_SESSION['blocked_time']);
                if ($tiempo_restante <= 0){
                    $_SESSION['login_attempts'] = 0;
                    unset($_SESSION['blocked_time']);
                } else {
                    $error_msg = "Has superado el numero de intentos disponibles (Espera : $tiempo_restante segundos.)";
                }
            } else{
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
                        $_SESSION['login_attempts'] = 0;
                        unset($_SESSION['blocked_time']);
                        header("Location: index.html");    // La contraseña es correcta, redirigir al usuario
                        exit(); // Asegúrate de llamar a exit() después de header para evitar más ejecuciones.
                    } else {
                        // Contraseña incorrecta
                        $_SESSION['login_attempts']++;
                        $error_msg = "La contraseña es incorrecta. Intento #" . $_SESSION['login_attempts'];
                    }
                } else {
                    // El email no existe en la base de datos
                    $_SESSION['login_attempts']++;
                    $error_msg = "El correo electrónico no está registrado. Por favor, verifica.";
                }
            } else {
                // En caso de que no se haya enviado el email o la contraseña
                $error_msg = "Por favor, ingrese su correo electrónico y contraseña.";
            }
        }
    }
}

?>