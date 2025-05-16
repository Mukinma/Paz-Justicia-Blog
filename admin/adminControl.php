<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: usuario.php');
    exit();
}

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    // Redirigir a la página principal con un mensaje de error
    $_SESSION['error'] = "No tienes permisos para acceder a esta sección.";
    header('Location: ../index.php');
    exit();
}

require '../config/db.php';

// Obtener avatar del usuario si no está en la sesión
if (!isset($_SESSION['avatar'])) {
    $stmt = $pdo->prepare("SELECT avatar FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['avatar'] = $stmt->fetchColumn();
}

try {
    // Prepare and execute the SQL query to fetch posts
    $sqlCategorias = "SELECT c.id_categoria, c.nombre AS NombreCategoria, c.slug AS SlugCategoria, COUNT(p.id_post) AS CantidadArticulos FROM categorias c LEFT JOIN posts p ON c.id_categoria = p.id_categoria GROUP BY c.id_categoria, c.nombre, c.slug ORDER BY CantidadArticulos DESC";
    $sqlCategoriasOpcion = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC";
    $sqlUsuarios = "SELECT u.id_usuario, u.name AS NombreUsuario, u.email AS EmailUsuario, u.rol AS RolUsuario, u.fecha_registro AS FechaRegistro, COUNT(p.id_post) AS CantidadPosts 
                    FROM usuarios u 
                    LEFT JOIN posts p ON u.id_usuario = p.id_usuario 
                    GROUP BY u.id_usuario, u.name, u.email, u.rol, u.fecha_registro";
    $sqlResources = "SELECT r.titulo AS NombreRecurso, r.fecha_subida AS FechaSubida, r.alt_text AS TextoAlternativo FROM imagenes r ORDER BY r.fecha_subida DESC";
    $sqlArticulos = "SELECT p.id_post AS ID, p.titulo AS Titulo, c.nombre AS Categoria, p.estado AS Estado, u.name AS Autor 
                     FROM posts p 
                     LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                     LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                     WHERE p.estado = 'publicado' 
                     ORDER BY p.id_post DESC";

    $sqlArticulosArchivados = "SELECT p.id_post AS ID, p.titulo AS Titulo, c.nombre AS Categoria, p.estado AS Estado, u.name AS Autor 
                              FROM posts p 
                              LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                              LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                              WHERE p.estado = 'archivado' 
                              ORDER BY p.id_post DESC";

    $sqlComentarios = "SELECT c.id_comentario, 
                          c.contenido,
                          c.fecha_comentario,
                          c.aprobado,
                          c.ip_address,
                          u.name as usuario_nombre,
                          p.titulo as post_titulo,
                          p.id_post
                   FROM comentarios c
                   LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                   LEFT JOIN posts p ON c.id_post = p.id_post
                   ORDER BY c.fecha_comentario DESC";

    // Execute the query and fetch results
    $stmt = $pdo->query($sqlCategorias);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query($sqlUsuarios);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query($sqlCategoriasOpcion);
    $categoriasOpcion = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query($sqlResources);
    $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query($sqlArticulos);
    $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query($sqlComentarios);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener publicaciones: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin control</title>
    <link rel="stylesheet" href="crud2.css">
    <script src="crud.js" defer></script>
    <script src="navigation.js" defer></script>
</head>
<body>
    <div class="left-side">
        <h1>Admin control</h1>
        <?php if (!empty($_SESSION['avatar']) && file_exists('../' . $_SESSION['avatar'])): ?>
            <img src="../<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="profile" class="profile-icon">
        <?php else: ?>
            <img src="../assets/profile-icon.svg" alt="profile" class="profile-icon">
        <?php endif; ?>
        <h2><?php 
            $nombre_completo = $_SESSION['usuario'];
            $nombre_palabras = explode(' ', $nombre_completo);
            $primeras_dos_palabras = array_slice($nombre_palabras, 0, 2);
            echo implode(' ', $primeras_dos_palabras); 
        ?></h2>
        <p><?php 
            $sql = "SELECT rol FROM usuarios WHERE name = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['usuario']]);
            $rol = $stmt->fetchColumn();
            echo ucfirst($rol); // Capitalizar la primera letra del rol
        ?></p>

        <div class="left-side-options">
            <button id="articlesButton">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24">
                    <path fill="currentColor" d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384c53 0 96-43 96-96V96c0-53-43-96-96-96H96zM208 64h96c8.8 0 16 7.2 16 16s-7.2 16-16 16H208c-8.8 0-16-7.2-16-16s7.2-16 16-16zM96 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H96c-8.8 0-16-7.2-16-16s7.2-16 16-16zM384 448H96c-17.7 0-32-14.3-32-32V192H416V416c0 17.7-14.3 32-32 32z"/>
                </svg>
                Articles
            </button>
            <button id="categoriesButton">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24">
                    <path fill="currentColor" d="M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z"/>
                </svg>
                Categories
            </button>
            <button id="commentsButton">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24">
                    <path fill="currentColor" d="M256 32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26S14.4 480 24 480c61.5 0 110-25.7 139.1-46.3C192 442.8 223.2 448 256 448c141.4 0 256-93.1 256-208S397.4 32 256 32zM128 272c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32z"/>
                </svg>
                Comments
            </button>
            <button id="usersButton">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="24" height="24">
                    <path fill="currentColor" d="M144 0a48 48 0 1 0 0 96 48 48 0 1 0 0-96zM96 144c-26.5 0-48 21.5-48 48s21.5 48 48 48H192c26.5 0 48-21.5 48-48s-21.5-48-48-48H96zM352 0a48 48 0 1 0 0 96 48 48 0 1 0 0-96zM304 144c-26.5 0-48 21.5-48 48s21.5 48 48 48H448c26.5 0 48-21.5 48-48s-21.5-48-48-48H304zM144 512a48 48 0 1 0 0-96 48 48 0 1 0 0 96zM96 368c-26.5 0-48 21.5-48 48s21.5 48 48 48H192c26.5 0 48-21.5 48-48s-21.5-48-48-48H96zM352 512a48 48 0 1 0 0-96 48 48 0 1 0 0 96zM304 368c-26.5 0-48 21.5-48 48s21.5 48 48 48H448c26.5 0 48-21.5 48-48s-21.5-48-48-48H304zM416 208c0-26.5-21.5-48-48-48H272c-26.5 0-48 21.5-48 48s21.5 48 48 48H368c26.5 0 48-21.5 48-48zM160 464c0-26.5-21.5-48-48-48H48c-26.5 0-48 21.5-48 48s21.5 48 48 48H112c26.5 0 48-21.5 48-48zM592 464c0-26.5-21.5-48-48-48H528c-26.5 0-48 21.5-48 48s21.5 48 48 48h16c26.5 0 48-21.5 48-48z"/>
                </svg>
                Users
            </button>
        </div>
    </div>
    <div class="right-side">
        <header>
            <a href="../index.php">
                <img src="../assets/logo.png" class="logo" alt="logo">
            </a>
            <div class="header-options">
                <a href="logout.php" class="logout-link">
                    <img src="../assets/logout-icon.svg" alt="logout" class="logout-icon">
                </a>
            </div>
        </header>
        <div class="rectangle">
            <div class="rectangle-text">
                <h1>Welcome back, <?php 
                    $nombre_completo = $_SESSION['usuario'];
                    $nombre_palabras = explode(' ', $nombre_completo);
                    $primera_palabra = array_slice($nombre_palabras, 0, 1);
                    echo implode(' ', $primera_palabra); 
                ?></h1>
                <p>Here you can manage what you need.</p>
            </div>
            <img src="../assets/hand-header.svg" alt="hand" class="hand-header">
        </div>
        <div class="content">
            <div id="articles" class="content-section">
                <div class="content-header">
                    <div class="content-tools">
                        <h2>Articles List</h2>
                        <div class="filter-options">
                            <button class="filter-button" id="showPublished">Published</button>
                            <button class="filter-button" id="showArchived">Archived</button>
                        </div>
                        <input type="text" class="search-bar">
                        <button class="add-button" id="article-add-button"></button>
                    </div>
                </div>
                <div class="content-body" id="published-articles">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>State</th>
                                <th>Author</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($articulos)): ?>
                                <tr>
                                    <td colspan="6" class="no-results">
                                        <div class="no-results-message">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="no-results-icon">
                                                <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H352c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
                                            </svg>
                                            <p>No hay artículos publicados</p>
                                            <span>Comienza agregando un nuevo artículo</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($articulos as $articulo): ?>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td><?php echo htmlspecialchars($articulo['Titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($articulo['Categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($articulo['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($articulo['Autor']); ?></td>
                                        <td>
                                            <button class="view-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentcolor" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                            </button>
                                            <button class="edit-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg>
                                            </button>
                                            <button class="delete-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentcolor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            </button>
                                            <button class="archive-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M32 32l448 0c17.7 0 32 14.3 32 32l0 32c0 17.7-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96L0 64C0 46.3 14.3 32 32 32zm0 128l448 0 0 256c0 35.3-28.7 64-64 64L96 480c-35.3 0-64-28.7-64-64l0-256zm128 80c0 8.8 7.2 16 16 16l160 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-160 0c-8.8 0-16 7.2-16 16z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="content-body" id="archived-articles" style="display: none;">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>State</th>
                                <th>Author</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stmt = $pdo->query($sqlArticulosArchivados);
                            $articulosArchivados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($articulosArchivados)): ?>
                                <tr>
                                    <td colspan="6" class="no-results">
                                        <div class="no-results-message">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="no-results-icon">
                                                <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H352c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
                                            </svg>
                                            <p>No hay artículos archivados</p>
                                            <span>Los artículos archivados aparecerán aquí</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($articulosArchivados as $articulo): ?>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td><?php echo htmlspecialchars($articulo['Titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($articulo['Categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($articulo['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($articulo['Autor']); ?></td>
                                        <td>
                                            <button class="view-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentcolor" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                            </button>
                                            <button class="edit-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg>
                                            </button>
                                            <button class="delete-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentcolor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            </button>
                                            <button class="unarchive-button" data-id="<?php echo $articulo['ID']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M32 32l448 0c17.7 0 32 14.3 32 32l0 32c0 17.7-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96L0 64C0 46.3 14.3 32 32 32zm0 128l448 0 0 256c0 35.3-28.7 64-64 64L96 480c-35.3 0-64-28.7-64-64l0-256zm128 80c0 8.8 7.2 16 16 16l160 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-160 0c-8.8 0-16 7.2-16 16z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div id="categories" class="content-section">
                <div class="content-header">
                    <div class="content-tools">
                        <h2>Categories List</h2>
                        <input type="text" class="search-bar">
                        <button class="add-button" id="category-add-button"></button>
                    </div>
                </div>
                <div class="content-body">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Associated article</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categorias)): ?>
                                <tr>
                                    <td colspan="5" class="no-results">
                                        <div class="no-results-message">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="no-results-icon">
                                                <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H352c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
                                            </svg>
                                            <p>No hay categorías disponibles</p>
                                            <span>Comienza agregando una nueva categoría</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td><?php echo htmlspecialchars($categoria['NombreCategoria']); ?></td>
                                        <td><?php echo htmlspecialchars($categoria['SlugCategoria']); ?></td>
                                        <td><?php echo htmlspecialchars($categoria['CantidadArticulos']); ?></td>
                                        <td>
                                            <button class="edit-category-button" data-id="<?php echo $categoria['id_categoria']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/></svg>
                                            </button>
                                            <button class="delete-category-button" data-id="<?php echo $categoria['id_categoria']; ?>" data-nombre="<?php echo htmlspecialchars($categoria['NombreCategoria']); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentcolor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>  
            </div>

            <div id="comments" class="content-section">
                <div class="content-header">
                    <div class="content-tools">
                        <h2>Comments List</h2>
                        <input type="text" class="search-bar">
                    </div>
                </div>
                <div class="content-body">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Comment fragment</th>
                                <th>User</th>
                                <th>Associated article</th>
                                <th>State</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($comentarios)): ?>
                                <tr>
                                    <td colspan="7" class="no-results">
                                        <div class="no-results-message">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="no-results-icon">
                                                <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H352c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
                                            </svg>
                                            <p>No hay comentarios disponibles</p>
                                            <span>Los comentarios aparecerán aquí</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($comentarios as $comentario): ?>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td><?php echo htmlspecialchars(substr($comentario['contenido'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($comentario['usuario_nombre'] ?? 'Anónimo'); ?></td>
                                        <td>
                                            <a href="../views/post.php?id=<?php echo $comentario['id_post']; ?>" target="_blank">
                                                <?php echo htmlspecialchars(substr($comentario['post_titulo'], 0, 30)) . '...'; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $comentario['aprobado'] ? 'approved' : 'pending'; ?>">
                                                <?php echo $comentario['aprobado'] ? 'Aprobado' : 'Pendiente'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($comentario['fecha_comentario'])); ?></td>
                                        <td>
                                            <?php if (!$comentario['aprobado']): ?>
                                                <button class="approve-button" data-id="<?php echo $comentario['id_comentario']; ?>" title="Aprobar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/></svg>
                                                </button>
                                            <?php endif; ?>
                                            <button class="ban-user-button" data-id="<?php echo $comentario['id_usuario']; ?>" title="Banear usuario">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentcolor" d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L355.7 253.5 444.3 159c13.1-11.4 20.3-27.8 20.3-44.6V96c0-17.7-14.3-32-32-32H224c-17.7 0-32 14.3-32 32v18.3L38.8 5.1zM0 128v128c0 53 43 96 96 96H352c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32H96c-17.7 0-32 14.3-32 32zm416 96c0 53-43 96-96 96H96c-53 0-96-43-96-96V128c0-53 43-96 96-96H320c53 0 96 43 96 96v96z"/></svg>
                                            </button>
                                            <button class="delete-comment-button" data-id="<?php echo $comentario['id_comentario']; ?>" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentcolor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>  
            </div>

            <div id="users" class="content-section">
                <div class="content-header">
                    <div class="content-tools">
                        <h2>Users List</h2>
                        <input type="text" class="search-bar">
                    </div>
                </div>
                <div class="content-body">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Number of posts</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($usuarios as $usuario) {
                                    echo '<tr>';
                                    echo '<td><input type="checkbox"></td>';
                                    echo '<td>' . htmlspecialchars($usuario['NombreUsuario']) . '</td>';
                                    echo '<td>' . htmlspecialchars($usuario['EmailUsuario']) . '</td>';
                                    echo '<td><span class="role-badge ' . strtolower($usuario['RolUsuario']) . '">' . htmlspecialchars($usuario['RolUsuario']) . '</span></td>';
                                    echo '<td>' . htmlspecialchars($usuario['CantidadPosts']) . '</td>';
                                    echo '<td>
                                        <button class="change-role-button" data-id="' . $usuario['id_usuario'] . '" data-rol="' . htmlspecialchars($usuario['RolUsuario']) . '" title="Cambiar rol">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentcolor" d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>
                                        </button>
                                    </td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>  
            </div>
        </div>
    </div>
    <div class="overlay" id="overlay"></div>
    <div class="modal" id="modal-article">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Add a new article</h1>
                <div class="buttons-modal">
                    <button class="cancel-article">Cancel</button>
                    <button class="add-article" id="submit-button">Add article</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <form action="insertar_post.php" method="POST" id="myForm1" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="titulo">Título:</label>
                            <input type="text" id="titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="contenido">Contenido:</label>
                            <textarea id="contenido" name="contenido" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" required>
                                <?php
                                // Obtener categorías de la base de datos
                                $sql = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre";
                                $stmt = $pdo->query($sql);
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='" . $row['id_categoria'] . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="imagen_ilustrativa">Imagen Ilustrativa:</label>
                            <input type="file" id="imagen_ilustrativa" name="imagen_ilustrativa" accept="image/*">
                            <small>Esta imagen se mostrará como imagen principal del post</small>
                            <img id="preview_ilustrativa" class="image-preview" alt="Vista previa de la imagen ilustrativa">
                        </div>
                        <div class="form-group">
                            <label for="imagen_background">Imagen de Fondo:</label>
                            <input type="file" id="imagen_background" name="imagen_background" accept="image/*">
                            <small>Esta imagen se usará como fondo del post</small>
                            <img id="preview_background" class="image-preview" alt="Vista previa de la imagen de fondo">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-category">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Add a new category</h1>
                <div class="buttons-modal">
                    <button class="cancel-article">Cancel</button>
                    <button class="add-article" id="submit-category-button">Add category</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <form action="insertar_categoria.php" method="POST" id="categoryForm">
                        <div class="form-group">
                            <label for="nombre">Nombre de la categoría:</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-resource">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Add a new resource</h1>
                <div class="buttons-modal">
                    <button class="cancel-article">Cancel</button>
                    <button class="add-article" id="submit-button">Add article</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <form action="insertar_post.php" method="POST" id="myForm">
                        <!-- Título del Post -->
                        <label for="titulo">Título del Post</label>
                        <input type="text" id="titulo" name="titulo" required>
            
                        <!-- Descripción del Post -->
                        <label for="descripcion">Descripción del Post</label>
                        <textarea id="descripcion" name="descripcion" required></textarea>
            
                        <!-- Categoría del Post -->
                        <label for="categoria">Categoría</label>
                        <select id="categoria" name="categoria" required>
                            <?php
                            foreach ($categoriasOpcion as $categoria) {
                                echo '<option value="' . htmlspecialchars($categoria['id_categoria']) . '">' . htmlspecialchars($categoria['nombre']) . '</option>';
                            }
                            ?>
                        </select>
            
                        <!-- Contenido del Post -->
                        <label for="contenido">Contenido del Post</label>
                        <textarea id="contenido" name="contenido" required></textarea>
            
                        <!-- Fecha de Publicación -->
                        <label for="fecha">Fecha de Publicación</label>
                        <input type="date" id="fecha" name="fecha" required>
            
                        <!-- Imagen o Multimedia -->
                        <label for="imagen">Imagen</label>
                        <input id="imagen" name="imagen">            
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-edit-article">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Edit article</h1>
                <div class="buttons-modal">
                    <button class="cancel-article" id="cancel-edit">Cancel</button>
                    <button class="add-article" id="submit-edit-button">Save changes</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-edit-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <form action="editar_post.php" method="POST" id="editForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id" name="id" value="">
                        
                        <!-- Título del Post -->
                        <label for="edit-titulo">Título del Post</label>
                        <input type="text" id="edit-titulo" name="titulo" required>
            
                        <!-- Descripción del Post -->
                        <label for="edit-descripcion">Descripción del Post</label>
                        <textarea id="edit-descripcion" name="descripcion" required></textarea>
            
                        <!-- Categoría del Post -->
                        <label for="edit-categoria">Categoría</label>
                        <select id="edit-categoria" name="categoria" required>
                            <?php
                            foreach ($categoriasOpcion as $categoria) {
                                echo '<option value="' . htmlspecialchars($categoria['id_categoria']) . '">' . htmlspecialchars($categoria['nombre']) . '</option>';
                            }
                            ?>
                        </select>
            
                        <!-- Contenido del Post -->
                        <label for="edit-contenido">Contenido del Post</label>
                        <textarea id="edit-contenido" name="contenido" required></textarea>
                        
                        <!-- Imagen actual -->
                        <div id="current-image-container">
                            <p>Current image:</p>
                            <img id="current-image" src="" alt="Current article image" style="max-width:200px;">
                        </div>
                        
                        <!-- Nueva Imagen (opcional) -->
                        <label for="edit-imagen">Nueva Imagen (opcional)</label>
                        <input type="file" id="edit-imagen" name="imagen">            
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-delete-confirmation">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Confirm Deletion</h1>
                <div class="buttons-modal">
                    <button class="cancel-article" id="cancel-delete">Cancel</button>
                    <button class="delete-article" id="confirm-delete-button">Delete</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-delete-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <h2>Are you sure you want to delete this article?</h2>
                    <p id="post-delete-title"></p>
                    <p class="warning-text">This action cannot be undone.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-edit-category">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Editar Categoría</h1>
                <div class="buttons-modal">
                    <button class="cancel-article" id="cancel-edit-category">Cancelar</button>
                    <button class="add-article" id="submit-edit-category-button">Guardar Cambios</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-edit-category-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <form action="editar_categoria.php" method="POST" id="editCategoryForm">
                        <input type="hidden" id="edit-category-id" name="id" value="">
                        <div class="form-group">
                            <label for="edit-category-nombre">Nombre de la categoría:</label>
                            <input type="text" id="edit-category-nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-category-descripcion">Descripción:</label>
                            <textarea id="edit-category-descripcion" name="descripcion"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-delete-category">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Confirmar Eliminación</h1>
                <div class="buttons-modal">
                    <button class="cancel-article" id="cancel-delete-category">Cancelar</button>
                    <button class="delete-article" id="confirm-delete-category-button">Eliminar</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-delete-category-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <h2>¿Estás seguro de que deseas eliminar esta categoría?</h2>
                    <p id="category-delete-name"></p>
                    <p class="warning-text">Esta acción no se puede deshacer.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-change-role">
        <div class="modal-body">
            <div class="left-modal">
                <h1>Cambiar Rol</h1>
                <div class="buttons-modal">
                    <button class="cancel-article" id="cancel-change-role">Cancelar</button>
                    <button class="add-article" id="confirm-change-role-button">Guardar Cambios</button>
                </div>
            </div>
            <div class="right-modal">
                <div class="modal-header">
                    <button id="close-change-role-modal" class="close-button">&times;</button>
                </div>
                <div class="container">
                    <form id="changeRoleForm">
                        <input type="hidden" id="change-role-user-id" name="id_usuario" value="">
                        <div class="form-group">
                            <label for="new-role">Nuevo Rol:</label>
                            <select id="new-role" name="rol" required>
                                <option value="admin">Administrador</option>
                                <option value="editor">Editor</option>
                                <option value="lector">Lector</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="notification-modal" id="notificationModal"></div>
</body>
</html>