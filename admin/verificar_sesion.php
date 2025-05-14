<?php
session_start();

function verificarSesion() {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: usuario.php');
        exit();
    }
}

function verificarAdmin() {
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
        header('Location: usuario.php');
        exit();
    }
}
?> 