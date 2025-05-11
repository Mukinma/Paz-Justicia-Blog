<?php
require '../config/db.php';

try {
    // Prepare and execute the SQL query to fetch posts
    $sqlCategorias = "SELECT c.nombre AS NombreCategoria, c.slug AS SlugCategoria, COUNT(p.id_post) AS CantidadArticulos FROM categorias c LEFT JOIN posts p ON c.id_categoria = p.id_categoria GROUP BY c.id_categoria, c.nombre, c.slug ORDER BY CantidadArticulos DESC";
    $sqlCategoriasOpcion = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC";
    $sqlUsuarios = "SELECT u.nombre AS NombreUsuario, u.email AS EmailUsuario, u.rol AS RolUsuario, u.fecha_registro AS FechaRegistro, COUNT(p.id_post) AS CantidadPosts FROM usuarios u LEFT JOIN posts p ON u.id_usuario = p.id_usuario GROUP BY u.id_usuario, u.nombre, u.email, u.rol, u.fecha_registro";
    $sqlResources = "SELECT r.titulo AS NombreRecurso, r.fecha_subida AS FechaSubida, r.alt_text AS TextoAlternativo FROM imagenes r ORDER BY r.fecha_subida DESC";
    $sqlArticulos = "SELECT p.id_post AS ID, p.titulo AS Titulo, c.nombre AS Categoria, p.estado AS Estado, u.nombre AS Autor FROM posts p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario ORDER BY p.id_post DESC";

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
                <p>Here you can manage your articles and contacts.</p>
            </div>
            <img src="../assets/hand-header.svg" alt="hand" class="hand-header">
        </div>
        <div class="content" id="articles">
            <div class="content-header">
                <div class="content-tools">
                    <h2>Articles List</h2>
                    <input type="text" class="search-bar">
                    <button class="filter-button">
                        <img src="../assets/filters-icon.svg" alt="filter" class="filter-icon">
                        Filters
                    </button>
                    <button class="add-button" id="article-add-button"></button>
                </div>
            </div>
            <div class="content-body">
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
                        <?php foreach ($articulos as $articulo): ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?php echo htmlspecialchars($articulo['Titulo']); ?></td>
                                <td><?php echo htmlspecialchars($articulo['Categoria']); ?></td>
                                <td><?php echo htmlspecialchars($articulo['Estado']); ?></td>
                                <td><?php echo htmlspecialchars($articulo['Autor']); ?></td>
                                <td>
                                    <button class="edit-button" data-id="<?php echo $articulo['ID']; ?>">Edit</button>
                                    <button class="delete-button" data-id="<?php echo $articulo['ID']; ?>">Delete</button>
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
                            <th>Registration date</th>
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
                                echo '<td>' . htmlspecialchars($usuario['FechaRegistro']) . '</td>';
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
            
                        <!-- Fecha de Publicación -->
                        <label for="fecha">Fecha de Publicación</label>
                        <input type="date" id="fecha" name="fecha" required>
            
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
            
                        <!-- Estado -->
                        <label for="edit-estado">Estado</label>
                        <select id="edit-estado" name="estado">
                            <option value="publicado">Publicado</option>
                            <option value="borrador">Borrador</option>
                        </select>
                        
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
</body>
</html>