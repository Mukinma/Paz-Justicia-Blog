<?php
// Ajusta la conexión a tu base de datos
require '../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $id_categoria = $_POST['categoria']; // Asegúrate de que este valor sea un ID numérico
    $contenido   = $_POST['contenido'];
    $fecha       = $_POST['fecha'];

    try {
        $sql = "INSERT INTO posts (titulo, descripcion, id_categoria, contenido, fecha_publicacion)
                VALUES (:titulo, :descripcion, :id_categoria, :contenido, :fecha)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();

        header('Location: adminControl.php');
        exit;
    } catch (PDOException $e) {
        echo "Error al insertar: " . $e->getMessage();
    }
}
?>
