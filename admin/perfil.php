<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: usuario.php');
    exit();
}

require '../config/db.php';

// Obtener la información del usuario actual
$id_usuario = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Manejar la actualización del perfil
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar la biografía
    if (isset($_POST['biografia'])) {
        $biografia = trim($_POST['biografia']);
        $stmt = $pdo->prepare("UPDATE usuarios SET biografia = ? WHERE id_usuario = ?");
        if ($stmt->execute([$biografia, $id_usuario])) {
            $mensaje = "Biografía actualizada correctamente.";
            $tipo_mensaje = "success";
            $usuario['biografia'] = $biografia;
        } else {
            $mensaje = "Error al actualizar la biografía.";
            $tipo_mensaje = "error";
        }
    }
    
    // Actualizar la foto de perfil
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Crear un nombre de archivo único
            $newname = uniqid() . '.' . $filetype;
            $destination = '../assets/avatars/' . $newname;
            
            // Crear el directorio si no existe
            if (!file_exists('../assets/avatars/')) {
                mkdir('../assets/avatars/', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                // Borrar avatar anterior si existe
                if (!empty($usuario['avatar']) && file_exists('../' . $usuario['avatar'])) {
                    unlink('../' . $usuario['avatar']);
                }
                
                $avatar_path = 'assets/avatars/' . $newname;
                $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id_usuario = ?");
                if ($stmt->execute([$avatar_path, $id_usuario])) {
                    $mensaje = "Perfil actualizado correctamente.";
                    $tipo_mensaje = "success";
                    $usuario['avatar'] = $avatar_path;
                } else {
                    $mensaje = "Error al actualizar la foto de perfil.";
                    $tipo_mensaje = "error";
                }
            } else {
                $mensaje = "Error al subir la imagen.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = "Formato de archivo no permitido. Use jpg, jpeg, png o gif.";
            $tipo_mensaje = "error";
        }
    }
}

// Actualizar sesión con posibles cambios
$_SESSION['avatar'] = $usuario['avatar'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - PeaceInProgress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="perfil.css">
    <link rel="icon" href="../assets/minilogo.png">
</head>
<body>
    <div class="profile-container">
        <div class="sidebar">
            <div class="logo-container">
                <a href="../index.php">
                    <img src="../assets/logo.png" alt="Logo" class="logo">
                </a>
            </div>
            
            <div class="profile-avatar">
                <?php if (!empty($usuario['avatar']) && file_exists('../' . $usuario['avatar'])): ?>
                    <img src="../<?php echo htmlspecialchars($usuario['avatar']); ?>" alt="Foto de perfil">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
            </div>
            
            <div class="profile-name">
                <?php echo htmlspecialchars($usuario['name']); ?>
            </div>
            
            <div class="profile-role">
                <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="#"><i class="fas fa-user"></i> Mi Perfil</a></li>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')): ?>
                        <li><a href="adminControl.php"><i class="fas fa-cog"></i> Panel de Administración</a></li>
                    <?php endif; ?>
                    <li><a href="../index.php"><i class="fas fa-home"></i> Volver al Inicio</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="main-content">
            <h1>Mi Perfil</h1>
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-section">
                <h2>Información Personal</h2>
                <div class="info-container">
                    <div class="info-item">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Rol:</span>
                        <span class="info-value"><?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha de registro:</span>
                        <span class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="profile-section">
                <h2>Foto de Perfil</h2>
                <div class="avatar-container">
                    <div class="current-avatar">
                        <?php if (!empty($usuario['avatar']) && file_exists('../' . $usuario['avatar'])): ?>
                            <img src="../<?php echo htmlspecialchars($usuario['avatar']); ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <div class="no-avatar">
                                <i class="fas fa-user-circle"></i>
                                <p>Sin foto de perfil</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form action="" method="POST" enctype="multipart/form-data" class="avatar-form">
                        <div class="file-input-wrapper">
                            <label for="avatar" class="file-input-label">
                                <i class="fas fa-camera"></i> Cambiar foto
                            </label>
                            <input type="file" id="avatar" name="avatar" class="file-input" accept="image/*">
                            <div id="file-name-display">No se ha seleccionado ningún archivo</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar foto</button>
                    </form>
                </div>
            </div>
            
            <div class="profile-section">
                <h2>Mi Biografía</h2>
                <form action="" method="POST" class="biografia-form">
                    <textarea name="biografia" rows="5" placeholder="Escribe algo sobre ti..."><?php echo htmlspecialchars($usuario['biografia'] ?? ''); ?></textarea>
                    <button type="submit" class="btn btn-primary">Guardar biografía</button>
                </form>
            </div>
            
            <div class="profile-section">
                <h2>Mis Estadísticas</h2>
                <div class="stats-container">
                    <?php
                    // Obtener estadísticas
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE id_usuario = ?");
                    $stmt->execute([$id_usuario]);
                    $total_posts = $stmt->fetchColumn();
                    
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comentarios WHERE id_usuario = ?");
                    $stmt->execute([$id_usuario]);
                    $total_comentarios = $stmt->fetchColumn();
                    ?>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_posts; ?></div>
                        <div class="stat-label">Artículos publicados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_comentarios; ?></div>
                        <div class="stat-label">Comentarios realizados</div>
                    </div>
                </div>
            </div>
            
            <div class="profile-section">
                <h2>Mis Artículos Recientes</h2>
                <div class="articles-container">
                    <?php
                    $stmt = $pdo->prepare("SELECT id_post, titulo, fecha_publicacion FROM posts WHERE id_usuario = ? ORDER BY fecha_publicacion DESC LIMIT 5");
                    $stmt->execute([$id_usuario]);
                    $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($articulos) > 0):
                        foreach ($articulos as $articulo):
                    ?>
                    <div class="article-item">
                        <a href="../views/post.php?id=<?php echo $articulo['id_post']; ?>">
                            <?php echo htmlspecialchars($articulo['titulo']); ?>
                        </a>
                        <span class="article-date">
                            <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?>
                        </span>
                    </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <p class="no-items">No has publicado ningún artículo aún.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para mostrar el nombre del archivo seleccionado
        document.getElementById('avatar').addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'No se ha seleccionado ningún archivo';
            document.getElementById('file-name-display').textContent = fileName;
        });
    </script>
</body>
</html> 