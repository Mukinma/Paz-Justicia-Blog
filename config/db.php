<?php
// db.php

$host = 'localhost'; // Database host
$dbname = 'blog'; // Databaseb name
$username = 'root'; // Database username
$password = '123456789'; // Database password

try {
    // Configurar opciones de PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    // Crear nueva instancia PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        $options
    );

    // Verificar la conexión
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    throw new Exception("Error al conectar con la base de datos");
}
?>