<?php
require '../config/db.php';
require 'verificar_sesion.php';

// Verificar que el usuario es administrador
verificarAdmin();

// Verificar si se recibió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener y validar los datos
        $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $nuevo_rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_STRING);

        // Validar que el rol sea válido
        $roles_permitidos = ['admin', 'editor', 'lector'];
        if (!in_array($nuevo_rol, $roles_permitidos)) {
            throw new Exception("Rol no válido");
        }

        // Preparar y ejecutar la consulta
        $sql = "UPDATE usuarios SET rol = :rol WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':rol' => $nuevo_rol,
            ':id_usuario' => $id_usuario
        ]);

        if ($stmt->rowCount() > 0) {
            // Redirigir con mensaje de éxito
            header('Location: adminControl.php?success=rol_actualizado');
        } else {
            throw new Exception("No se pudo actualizar el rol del usuario");
        }
    } catch (Exception $e) {
        // Redirigir con mensaje de error
        header('Location: adminControl.php?error=' . urlencode($e->getMessage()));
    }
    exit();
}

// Si no es POST, mostrar el formulario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Rol de Usuario</title>
    <link rel="stylesheet" href="../views/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Cambiar Rol de Usuario</h2>
        
        <?php
        // Obtener lista de usuarios
        $sql = "SELECT id_usuario, name, email, rol FROM usuarios ORDER BY name";
        $stmt = $pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="id_usuario">Seleccionar Usuario:</label>
                <select name="id_usuario" id="id_usuario" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id_usuario']; ?>">
                            <?php echo htmlspecialchars($usuario['name'] . ' (' . $usuario['email'] . ') - Rol actual: ' . $usuario['rol']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="rol">Nuevo Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="admin">Administrador</option>
                    <option value="editor">Editor</option>
                    <option value="lector">Lector</option>
                </select>
            </div>

            <button type="submit">Cambiar Rol</button>
        </form>

        <div class="links">
            <a href="adminControl.php">Volver al Panel de Control</a>
        </div>
    </div>
</body>
</html> 