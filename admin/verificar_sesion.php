w<?php
// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay una sesión activa
function verificarSesion() {
    if (!isset($_SESSION['id_usuario'])) {
        // Redirigir a la página de login (ubicación correcta)
        header('Location: usuario.php');
        exit();
    }
    // Asegurar que las variables de sesión estén configuradas correctamente
    if (isset($_SESSION['role']) && !isset($_SESSION['rol'])) {
        $_SESSION['rol'] = $_SESSION['role'];
    } else if (isset($_SESSION['rol']) && !isset($_SESSION['role'])) {
        $_SESSION['role'] = $_SESSION['rol'];
    }
}

// Verificar si el usuario es administrador
function verificarAdmin() {
    verificarSesion();
    if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['role'] !== 'admin')) {
        // Redirigir a la página principal con mensaje de error
        header('Location: ../index.php?error=permisos_insuficientes');
        exit();
    }
}
?> 