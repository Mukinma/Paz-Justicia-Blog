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
                if (!empty($usuario['avatar']) && file_exists('../' . $usuario['avatar']) && strpos($usuario['avatar'], 'avatars/') !== false) {
                    unlink('../' . $usuario['avatar']);
                }
                
                $avatar_path = 'assets/avatars/' . $newname;
                $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id_usuario = ?");
                if ($stmt->execute([$avatar_path, $id_usuario])) {
                    $mensaje = "Perfil actualizado correctamente.";
                    $tipo_mensaje = "success";
                    $usuario['avatar'] = $avatar_path;
                    $_SESSION['avatar'] = $avatar_path;
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

// Verificar si el usuario es editor o admin para mostrar estadísticas
$es_autor = ($usuario['rol'] === 'admin' || $usuario['rol'] === 'editor');

// Obtener estadísticas solo si es autor
$total_posts = 0;
$total_visitas = 0;
$articulos = [];

if ($es_autor) {
    // Obtener estadísticas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $total_posts = $stmt->fetchColumn();

    // Obtener artículos recientes
    $stmt = $pdo->prepare("SELECT id_post, titulo, fecha_publicacion, estado FROM posts WHERE id_usuario = ? ORDER BY fecha_publicacion DESC LIMIT 5");
    $stmt->execute([$id_usuario]);
    $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total de visitas
    if (!empty($articulos)) {
        $ids_articulos = array_column($articulos, 'id_post');
        $placeholders = str_repeat('?,', count($ids_articulos) - 1) . '?';
        $sql_visitas = "SELECT SUM(visitas) FROM posts WHERE id_post IN ($placeholders)";
        $stmt = $pdo->prepare($sql_visitas);
        $stmt->execute($ids_articulos);
        $total_visitas = $stmt->fetchColumn() ?: 0;
    }
}

// Obtener comentarios recientes para todos los usuarios
$stmt = $pdo->prepare("SELECT c.id_comentario, c.contenido, c.fecha_comentario, p.titulo, p.id_post FROM comentarios c JOIN posts p ON c.id_post = p.id_post WHERE c.id_usuario = ? ORDER BY c.fecha_comentario DESC LIMIT 5");
$stmt->execute([$id_usuario]);
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de comentarios
$stmt = $pdo->prepare("SELECT COUNT(*) FROM comentarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$total_comentarios = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - PeaceInProgress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="perfil2.css">
    <link rel="icon" href="../assets/minilogo.png">
</head>
<body>
    <div class="left-side">
        <h1>Centro de Paz</h1>
            <div class="profile-avatar">
                <?php if (!empty($usuario['avatar']) && file_exists('../' . $usuario['avatar'])): ?>
                    <img src="../<?php echo htmlspecialchars($usuario['avatar']); ?>" alt="Foto de perfil">
                <?php else: ?>
                <div class="avatar-initial">
                    <?php 
                    // Mostrar la primera letra del nombre del usuario
                    $inicial = strtoupper(substr($usuario['name'], 0, 1));
                    echo $inicial;
                    ?>
                </div>
                <?php endif; ?>
            </div>
        <h2 class="profile-name"><?php 
            $nombre_completo = $usuario['name'];
            $nombre_palabras = explode(' ', $nombre_completo);
            $primeras_dos_palabras = array_slice($nombre_palabras, 0, 2);
            echo implode(' ', $primeras_dos_palabras); 
        ?></h2>
        <p class="profile-role"><?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?></p>

        <div class="sidebar-nav">
            <ul>
                <li class="active"><a href="#mi-perfil"><i class="fas fa-user"></i> Mi Perfil</a></li>
                <li><a href="#mis-estadisticas"><i class="fas fa-chart-bar"></i> Estadísticas</a></li>
                <?php if ($es_autor): ?>
                <li><a href="#mis-articulos"><i class="fas fa-newspaper"></i> Mis Artículos</a></li>
                <?php endif; ?>
                <li><a href="#mis-comentarios"><i class="fas fa-comments"></i> Mis Comentarios</a></li>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')): ?>
                    <li><a href="adminControl.php"><i class="fas fa-shield-alt"></i> Panel de Administración</a></li>
                    <?php endif; ?>
                    <li><a href="../index.php"><i class="fas fa-home"></i> Volver al Inicio</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
        </div>
        
        <div class="logo-container">
            <a href="../index.php">
                <img src="../assets/logo.png" alt="Logo" class="logo">
            </a>
        </div>
    </div>
    
    <div class="right-side">
        <header>
            <a href="../index.php">
                <img src="../assets/logo.png" class="logo" alt="logo">
            </a>
            <div class="header-options">
                <a href="logout.php" class="logout-link" title="Cerrar sesión">
                    <img src="../assets/logout-icon.svg" alt="logout" class="logout-icon">
                </a>
            </div>
        </header>

        <div class="rectangle">
            <div class="rectangle-text">
                <h1>¡Bienvenido a tu perfil, <?php 
                    $nombre_completo = $usuario['name'];
                    $nombre_palabras = explode(' ', $nombre_completo);
                    $primera_palabra = array_slice($nombre_palabras, 0, 1);
                    echo implode(' ', $primera_palabra); 
                ?>!</h1>
                <p>Aquí puedes personalizar tu cuenta y revisar tu actividad en Peace In Progress.</p>
            </div>
            <img src="../assets/hand-header.svg" alt="hand" class="hand-header">
        </div>
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
        <div class="content-section" id="mi-perfil">
                <h2>Información Personal</h2>
            <div class="profile-info">
                    <div class="info-item">
                    <span class="info-label">Nombre Completo</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['name']); ?></span>
                    </div>
                    <div class="info-item">
                    <span class="info-label">Correo Electrónico</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                    </div>
                    <div class="info-item">
                    <span class="info-label">Rol</span>
                    <span class="info-value">
                        <span class="role-badge <?php echo strtolower($usuario['rol']); ?>">
                            <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?>
                        </span>
                    </span>
                    </div>
                    <div class="info-item">
                    <span class="info-label">Fecha de Registro</span>
                        <span class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
                </div>
                </div>
            </div>
            
        <div class="content-section">
                <h2>Foto de Perfil</h2>
                <div class="avatar-container">
                <div class="avatar-preview">
                        <?php if (!empty($usuario['avatar']) && file_exists('../' . $usuario['avatar'])): ?>
                            <img src="../<?php echo htmlspecialchars($usuario['avatar']); ?>" alt="Foto de perfil">
                        <?php else: ?>
                        <div class="avatar-initial">
                            <?php 
                            // Mostrar la primera letra del nombre del usuario
                            $inicial = strtoupper(substr($usuario['name'], 0, 1));
                            echo $inicial;
                            ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form action="" method="POST" enctype="multipart/form-data" class="avatar-form">
                    <div class="form-group">
                            <label for="avatar" class="file-input-label">
                                <i class="fas fa-camera"></i> Cambiar foto
                            </label>
                            <input type="file" id="avatar" name="avatar" class="file-input" accept="image/*">
                            <div id="file-name-display">No se ha seleccionado ningún archivo</div>
                        </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar foto
                    </button>
                    </form>
                </div>
            </div>
            
        <div class="content-section">
                <h2>Mi Biografía</h2>
                <form action="" method="POST" class="biografia-form">
                <div class="form-group">
                    <textarea name="biografia" rows="5" placeholder="Escribe algo sobre ti..."><?php echo htmlspecialchars($usuario['biografia'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar biografía
                </button>
                </form>
            </div>
            
        <div class="content-section" id="mis-estadisticas">
                <h2>Mis Estadísticas</h2>
                <div class="stats-container">
                <?php if ($es_autor): ?>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_posts; ?></div>
                        <div class="stat-label">Artículos publicados</div>
                    </div>
                <?php endif; ?>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_comentarios; ?></div>
                        <div class="stat-label">Comentarios realizados</div>
                    </div>
                <?php if ($es_autor): ?>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $total_visitas; ?></div>
                    <div class="stat-label">Visitas a tus artículos</div>
                </div>
                <?php endif; ?>
                </div>
            </div>
            
        <?php if ($es_autor): ?>
        <div class="content-section" id="mis-articulos">
                <h2>Mis Artículos Recientes</h2>
                <div class="articles-container">
                <?php if (count($articulos) > 0): ?>
                    <?php foreach ($articulos as $articulo): ?>
                    <div class="article-item">
                        <div>
                        <a href="../views/post.php?id=<?php echo $articulo['id_post']; ?>">
                            <?php echo htmlspecialchars($articulo['titulo']); ?>
                        </a>
                            <?php if ($articulo['estado'] === 'archivado'): ?>
                                <span class="status-badge archived">Archivado</span>
                            <?php elseif ($articulo['estado'] === 'borrador'): ?>
                                <span class="status-badge draft">Borrador</span>
                            <?php endif; ?>
                        </div>
                        <span class="article-date">
                            <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($total_posts > 5): ?>
                        <a href="#" class="btn btn-secondary view-all-articles">
                            <i class="fas fa-eye"></i> Ver todos mis artículos
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="no-items">
                        <i class="fas fa-newspaper"></i> No has publicado ningún artículo aún.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="content-section" id="mis-comentarios">
            <h2>Mis Comentarios Recientes</h2>
            <div class="articles-container">
                <?php if (count($comentarios) > 0): ?>
                    <?php foreach ($comentarios as $comentario): ?>
                    <div class="article-item">
                        <div>
                            <a href="../views/post.php?id=<?php echo $comentario['id_post']; ?>">
                                En: <?php echo htmlspecialchars($comentario['titulo']); ?>
                            </a>
                            <p class="comment-text"><?php echo htmlspecialchars(substr($comentario['contenido'], 0, 100)) . (strlen($comentario['contenido']) > 100 ? '...' : ''); ?></p>
                        </div>
                        <span class="article-date">
                            <?php echo date('d/m/Y', strtotime($comentario['fecha_comentario'])); ?>
                        </span>
                </div>
                    <?php endforeach; ?>
                    <?php if ($total_comentarios > 5): ?>
                        <a href="#" class="btn btn-secondary view-all-comments">
                            <i class="fas fa-eye"></i> Ver todos mis comentarios
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="no-items">
                        <i class="fas fa-comment-slash"></i> No has realizado ningún comentario aún.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Peace In Progress. Todos los derechos reservados.</p>
        </footer>
    </div>

    <div class="notification-modal" id="notificationModal"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Depuración para avatar
            console.log('Imagen de perfil: ', document.querySelector('.profile-avatar img')?.src || 'No hay imagen');
            
            // Manejador para la previsualización del avatar
            const avatarInput = document.getElementById('avatar');
            const fileNameDisplay = document.getElementById('file-name-display');
            
            if (avatarInput) {
                avatarInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        fileNameDisplay.textContent = file.name;
                        
                        // Validación de tamaño de archivo
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        if (file.size > maxSize) {
                            showNotification('El archivo es demasiado grande. Máximo 5MB permitido.', 'error');
                            this.value = '';
                            fileNameDisplay.textContent = 'No se ha seleccionado ningún archivo';
                            return;
                        }
                        
                        // Crear previsualización
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const previewContainer = document.querySelector('.avatar-preview');
                            
                            if (previewContainer) {
                                if (previewContainer.querySelector('.no-avatar')) {
                                    previewContainer.innerHTML = '';
                                }
                                
                                const existingImg = previewContainer.querySelector('img');
                                
                                if (existingImg) {
                                    existingImg.src = e.target.result;
                                } else {
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.alt = 'Vista previa de avatar';
                                    previewContainer.appendChild(img);
                                }
                            }
                        }
                        
                        reader.readAsDataURL(file);
                    } else {
                        fileNameDisplay.textContent = 'No se ha seleccionado ningún archivo';
                    }
                });
            }

            // Función para mostrar notificaciones
            window.showNotification = function(message, type = 'success') {
                const notificationModal = document.getElementById('notificationModal');
                
                if (notificationModal) {
                    notificationModal.textContent = message;
                    notificationModal.className = 'notification-modal';
                    notificationModal.classList.add(type);
                    notificationModal.style.display = 'block';
                    
                    setTimeout(() => {
                        notificationModal.style.display = 'none';
                    }, 3000);
                }
            };
            
            // Mostrar notificación si hay un mensaje
            <?php if (!empty($mensaje)): ?>
                showNotification('<?php echo addslashes($mensaje); ?>', '<?php echo $tipo_mensaje; ?>');
            <?php endif; ?>

            // Navegación por anclajes y resaltado de sección activa
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            
            function activateSection() {
                // Obtener la posición actual de scroll
                const scrollPosition = window.scrollY;
                
                // Encontrar la sección visible actual
                const sections = document.querySelectorAll('.content-section[id]');
                let currentSection = null;
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 100;
                    const sectionHeight = section.offsetHeight;
                    
                    if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                        currentSection = section.id;
                    }
                });
                
                // Actualizar navegación activa
                if (currentSection) {
                    navLinks.forEach(navLink => {
                        navLink.parentElement.classList.remove('active');
                        
                        const href = navLink.getAttribute('href');
                        if (href && href.substring(1) === currentSection) {
                            navLink.parentElement.classList.add('active');
                        }
                    });
                }
            }
            
            // Activar inicialmente y al hacer scroll
            activateSection();
            window.addEventListener('scroll', activateSection);
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        
                        if (targetElement) {
                            window.scrollTo({
                                top: targetElement.offsetTop - 20,
                                behavior: 'smooth'
                            });
                            
                            // Actualizar clase activa
                            navLinks.forEach(navLink => {
                                navLink.parentElement.classList.remove('active');
                            });
                            
                            this.parentElement.classList.add('active');
                        }
                    }
                });
            });
            
            // Animación en los elementos al entrar en viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            
            document.querySelectorAll('.content-section, .stat-item, .article-item').forEach(el => {
                el.classList.add('animate-ready');
                observer.observe(el);
            });
        });
    </script>
</body>
</html> 