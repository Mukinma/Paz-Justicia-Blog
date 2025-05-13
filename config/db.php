<?php
// db.php

$host = 'localhost'; // Database host
$dbname = 'blog'; // Databaseb name
$username = 'root'; // Database username
$password = '123456789'; // Database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>