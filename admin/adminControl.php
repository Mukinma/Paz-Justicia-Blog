<?php
require '../config/db.php';

try {
    // Prepare and execute the SQL query to fetch posts
    $sqlCategorias = "SELECT c.nombre AS NombreCategoria, c.slug AS SlugCategoria, COUNT(p.id_post) AS CantidadArticulos FROM categorias c LEFT JOIN posts p ON c.id_categoria = p.id_categoria GROUP BY c.id_categoria, c.nombre, c.slug ORDER BY CantidadArticulos DESC";
    $sqlCategoriasOpcion = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC";
    $sqlUsuarios = "SELECT u.nombre AS NombreUsuario, u.email AS EmailUsuario, u.rol AS RolUsuario, u.fecha_registro AS FechaRegistro, COUNT(p.id_post) AS CantidadPosts FROM usuarios u LEFT JOIN posts p ON u.id_usuario = p.id_usuario GROUP BY u.id_usuario, u.nombre, u.email, u.rol, u.fecha_registro";
    $sqlResources = "SELECT r.titulo AS NombreRecurso, r.fecha_subida AS FechaSubida, r.alt_text AS TextoAlternativo FROM imagenes r ORDER BY r.fecha_subida DESC";
    $sqlArticulos = "SELECT p.id_post AS ID, p.titulo AS Titulo, c.nombre AS Categoria, p.estado AS Estado, u.nombre AS Autor 
                     FROM posts p 
                     LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                     LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                     WHERE p.estado = 'publicado' 
                     ORDER BY p.id_post DESC";

    $sqlArticulosArchivados = "SELECT p.id_post AS ID, p.titulo AS Titulo, c.nombre AS Categoria, p.estado AS Estado, u.nombre AS Autor 
                              FROM posts p 
                              LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                              LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                              WHERE p.estado = 'archivado' 
                              ORDER BY p.id_post DESC";

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
</head>
<body>
    <div class="left-side">
        <h1>Admin control</h1>
        <img src="../assets/profile-icon.svg" alt="profile" class="profile-icon">
        <h2>Juan Mastercrack</h2>
        <p>Admin</p>

        <div class="left-side-options">
            <button id="articlesButton">Articles</button>
            <button id="categoriesButton">Categories</button>
            <button id="commentsButton">Comments</button>
            <button id="usersButton">Users</button>
            <button id="statisticsButton">Statistics</button>
            <button id="resourcesButton">Resources</button>
        </div>
    </div>
    <div class="right-side">
        <header>
            <img src="../assets/logo.png" class="logo" alt="logo">
            <div class="header-options">
                <img src="../assets/home-icon.svg" alt="home" class="home-icon">
                <img src="../assets/logout-icon.svg" alt="logout" class="logout-icon">
            </div>
        </header>
        <div class="rectangle">
            <div class="rectangle-text">
                <h1>Welcome back, Juan</h1>
                <p>Here you can manage what you need.</p>
            </div>
            <img src="../assets/hand-header.svg" alt="hand" class="hand-header">
        </div>
        <div class="content" id="articles">
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
                                            <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zM416 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H352c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
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
                                            <path fill="currentColor" d="M256 32c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9L264 94.6l24.7 24.7c9.2 9.2 11.9 22.9 6.9 34.9S268.9 176 256 176s-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9L248 94.6l-24.7-24.7c-9.2-9.2-11.9-22.9-6.9-34.9S243.1 32 256 32zM160 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zm128 0c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64zM416 256c17.7 0 32 14.3 32 32v96c0 17.7-14.3 32-32 32H352c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h64z"/>
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
        <div class="content" id="categories">
            <div class="content-header">
                <div class="content-tools">
                    <h2>Categories List</h2>
                    <input type="text" class="search-bar">
                    <button class="filter-button">
                        <img src="../assets/filters-icon.svg" alt="filter" class="filter-icon">
                        Filters
                    </button>
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
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?php echo htmlspecialchars($categoria['NombreCategoria']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['SlugCategoria']); ?></td>
                                <td><?php echo htmlspecialchars($categoria['CantidadArticulos']); ?></td>
                                <td>
                                    <button class="edit-button">Edit</button>
                                    <button class="delete-button">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
        <div class="content" id="comments">
            <div class="content-header">
                <div class="content-tools">
                    <h2>Comments List</h2>
                    <input type="text" class="search-bar">
                    <button class="filter-button">
                        <img src="../assets/filters-icon.svg" alt="filter" class="filter-icon">
                        Filters
                    </button>
                    <button class="add-button"></button>
                </div>
            </div>
            <div class="content-body">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Comment fragment</th>
                            <th>User</th>
                            <th>State</th>
                            <th>Associated article</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>30</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Sam Brown</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>30</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Sam Brown</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>30</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Sam Brown</td>
                            <td>22</td>
                        </tr>
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
        <div class="content" id="users">
            <div class="content-header">
                <div class="content-tools">
                    <h2>Users List</h2>
                    <input type="text" class="search-bar">
                    <button class="filter-button">
                        <img src="../assets/filters-icon.svg" alt="filter" class="filter-icon">
                        Filters
                    </button>
                    <button class="add-button"></button>
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
                                echo '<td>' . htmlspecialchars($usuario['RolUsuario']) . '</td>';
                                echo '<td>' . htmlspecialchars($usuario['CantidadPosts']) . '</td>';
                                echo '<td><button class="edit-button">Edit</button><button class="delete-button">Delete</button></td>';
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
        <div class="content" id="statistics">
            <div class="content-header">
                <div class="content-tools">
                    <h2>Statistics</h2>
                    <input type="text" class="search-bar">
                    <button class="filter-button">
                        <img src="../assets/filters-icon.svg" alt="filter" class="filter-icon">
                        Filters
                    </button>
                    <button class="add-button"></button>
                </div>
            </div>
            <div class="content-body">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Name</th>
                            <th>Last update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>30</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Sam Brown</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>30</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Sam Brown</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>30</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Sam Brown</td>
                            <td>22</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
        <div class="content" id="resources">
            <div class="content-header">
                <div class="content-tools">
                    <h2>Resources List</h2>
                    <input type="text" class="search-bar">
                    <button class="filter-button">
                        <img src="../assets/filters-icon.svg" alt="filter" class="filter-icon">
                        Filters
                    </button>
                    <button class="add-button" id="resource-add-button"></button>
                </div>
            </div>
            <div class="content-body">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Name</th>
                            <th>Upload date</th>
                            <th>Alternative text</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($recursos as $recurso) {
                                echo '<tr>';
                                echo '<td><input type="checkbox"></td>';
                                echo '<td>' . htmlspecialchars($recurso['NombreRecurso']) . '</td>';
                                echo '<td>' . htmlspecialchars($recurso['FechaSubida']) . '</td>';
                                echo '<td>' . htmlspecialchars($recurso['TextoAlternativo']) . '</td>';
                                echo '<td><button class="edit-button">Edit</button><button class="delete-button">Delete</button></td>';
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
                        </tr>
                    </tfoot>
                </table>
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

                        <!-- Imagen o Multimedia -->
                        <label for="imagen">Imagen</label>
                        <input type="file" id="imagen" name="imagen">            
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
    <div class="notification-modal" id="notificationModal"></div>
</body>
</html>