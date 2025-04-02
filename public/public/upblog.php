<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "blog";

// Conexión a la base de datos
$conexion = new mysqli($server, $user, $pass, $db);

// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_POST['registro'])) {
    // Obtener los valores del formulario
    $tittle = $_POST['tittle'];
    $content = $_POST['content'];
    $date = $_POST['date'];

    // Manejo de la imagen
    if (isset($_FILES['imagen'])) {
        $imagen = $_FILES['imagen'];
        $imagen_nombre = $imagen['name'];
        $imagen_tmp = $imagen['tmp_name'];

        // Verificar que el archivo es una imagen válida
        $imagen_tipo = mime_content_type($imagen_tmp);  // Obtenemos el tipo MIME del archivo
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($imagen_tipo, $tipos_permitidos)) {
            // Obtener la extensión del archivo
            $extension = pathinfo($imagen_nombre, PATHINFO_EXTENSION);
            $imagen_destino = 'uploads/' . uniqid() . '.' . $extension;  // Usamos uniqid para evitar colisiones de nombres

            // Mover el archivo a la carpeta uploads
            move_uploaded_file($imagen_tmp, $imagen_destino);
        } else {
            echo "El archivo no es una imagen válida.";
            exit;
        }
    } else {
        $imagen_destino = ''; // Si no se sube una imagen
    }

    // Consulta SQL para insertar los datos
    $insertardatos = "INSERT INTO uploadblog (tittle, content, imagen, date) VALUES ('$tittle', '$content', '$imagen_destino', '$date')";
    $ejecutarinsertar = mysqli_query($conexion, $insertardatos);

    if ($ejecutarinsertar) {
        echo "Blog subido con éxito!";
    } else {
        echo "Error al subir el blog: " . mysqli_error($conexion);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Weed</title>
    <link rel="stylesheet" href="upblog.css">
</head>
<body>
    <h1>"UPLOAD BLOG"</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tittle">Tittle:</label>
        <input type="text" name="tittle" required>

        <label for="content">Content:</label>
        <input type="text" name="content" required>

        <label for="imagen">Imagen:</label>
        <input type="file" name="imagen" required>

        <label for="date">Date:</label>
        <input type="date" name="date" required>

        <input type="submit" name="registro" value="Enviar a la base de datos">
    </form>
</body>
</html>
