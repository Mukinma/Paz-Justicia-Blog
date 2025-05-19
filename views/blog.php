<?php
// Iniciar la sesión
session_start();

// Importar configuración de la base de datos
require_once __DIR__ . '/../config/db.php';

// Variables para filtrado y paginación
$categoria_actual = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$posts_por_pagina = 9; // Cantidad de posts a mostrar por página
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'recientes'; // recientes, populares, antiguos

// Preparar la consulta base
$sql_base = "SELECT p.id_post, p.titulo, p.resumen, p.slug, p.fecha_publicacion, p.visitas, 
                    c.nombre AS categoria_nombre, c.slug AS categoria_slug, 
                    u.name AS autor_nombre, 
                    i.ruta AS imagen_destacada,
                    (SELECT COUNT(*) FROM post_likes pl WHERE pl.id_post = p.id_post) AS likes
             FROM posts p
             LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
             LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
             LEFT JOIN imagenes i ON p.id_imagen_destacada = i.id_imagen
             WHERE p.estado = 'publicado'";

// Aplicar filtros de categoría y búsqueda
$params = [];
$param_types = '';

if ($categoria_actual > 0) {
    $sql_base .= " AND p.id_categoria = ?";
    $params[] = $categoria_actual;
    $param_types .= 'i'; // Entero para id_categoria
}

if (!empty($busqueda)) {
    $sql_base .= " AND (p.titulo LIKE ? OR p.resumen LIKE ? OR p.contenido LIKE ?)";
    $busqueda_param = '%' . $busqueda . '%';
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $param_types .= 'sss'; // Strings para los LIKE
}

// Consulta para contar el total de artículos (para la paginación)
$sql_count = "SELECT COUNT(*) AS total FROM posts p 
              WHERE p.estado = 'publicado'";

// Agregar los mismos filtros a la consulta de conteo
if ($categoria_actual > 0) {
    $sql_count .= " AND p.id_categoria = ?";
}

if (!empty($busqueda)) {
    $sql_count .= " AND (p.titulo LIKE ? OR p.resumen LIKE ? OR p.contenido LIKE ?)";
}

// Preparar y ejecutar la consulta de conteo
$stmt_count = $pdo->prepare($sql_count);

// Vincular parámetros si existen
if (!empty($params)) {
    // Ejecutar con parámetros
    $stmt_count->execute($params);
} else {
    // Ejecutar sin parámetros
    $stmt_count->execute();
}

$total_posts = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular total de páginas
$total_paginas = ceil($total_posts / $posts_por_pagina);
$pagina_actual = max(1, min($pagina_actual, $total_paginas > 0 ? $total_paginas : 1));
$offset = ($pagina_actual - 1) * $posts_por_pagina;

// Aplicar ordenamiento
$sql_orden = '';
switch ($orden) {
    case 'populares':
        $sql_orden = " ORDER BY p.visitas DESC, p.fecha_publicacion DESC";
        break;
    case 'antiguos':
        $sql_orden = " ORDER BY p.fecha_publicacion ASC";
        break;
    case 'recientes':
    default:
        $sql_orden = " ORDER BY p.fecha_publicacion DESC";
}

$sql_final = $sql_base . $sql_orden . " LIMIT ?, ?";
$stmt = $pdo->prepare($sql_final);

// Añadir offset y limit a los parámetros
$params[] = $offset;
$params[] = $posts_por_pagina;
$param_types .= 'ii'; // Enteros para offset y limit

// Bindear parámetros usando bindValue con tipos específicos
for ($i = 0; $i < count($params); $i++) {
    $tipo = $i < strlen($param_types) ? $param_types[$i] : 's'; // Predeterminado a string
    
    switch ($tipo) {
        case 'i':
            $stmt->bindValue($i + 1, $params[$i], PDO::PARAM_INT);
            break;
        case 's':
        default:
            $stmt->bindValue($i + 1, $params[$i], PDO::PARAM_STR);
            break;
    }
}

$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las categorías para los filtros (sin filtro HAVING)
$sql_categorias = "SELECT c.id_categoria, c.nombre, c.slug, c.imagen,
                  (SELECT COUNT(*) FROM posts p WHERE p.id_categoria = c.id_categoria AND p.estado = 'publicado') as total_posts
                  FROM categorias c
                  ORDER BY c.nombre ASC";
$stmt_categorias = $pdo->query($sql_categorias);
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

// Incluir la estructura HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - Peace in Progress</title>
    <link rel="icon" href="../assets/minilogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/nav-fix.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #024365;
            --secondary-color: #7cbcbc;
            --accent-color: #1a3a5c;
            --text-color: #333;
            --light-text: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-color);
            line-height: 1.6;
            padding-top: 70px;
        }
        
        .blog-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .blog-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/pattern-dots.png');
            opacity: 0.1;
            z-index: 0;
        }
        
        .blog-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .blog-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            font-weight: 300;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .blog-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, rgba(16, 79, 112, 0.85) 0%, rgba(9, 58, 88, 0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .blog-filters::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
            z-index: 0;
        }
        
        .search-box {
            position: relative;
            flex: 1;
            margin-right: 20px;
            z-index: 1;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 20px;
            padding-right: 50px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-size: 1rem;
            transition: var(--transition);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-box input:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .search-box button:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        
        .filter-options {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            z-index: 1;
            position: relative;
        }
        
        .category-filter select,
        .order-filter select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }
        
        .category-filter select:focus,
        .order-filter select:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .category-filter select option,
        .order-filter select option {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        /* Nuevos estilos para los filtros activos */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .active-filters-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .filter-tag {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 30px;
            color: white;
            font-size: 0.85rem;
            transition: var(--transition);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .filter-tag:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .filter-tag .remove-filter {
            margin-left: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .filter-tag .remove-filter:hover {
            color: white;
            transform: scale(1.2);
        }
        
        .clear-filters {
            margin-left: 10px;
        }
        
        .clear-filters-btn {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .clear-filters-btn i {
            margin-right: 5px;
        }
        
        .clear-filters-btn:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        /* Estilos para el grid de posts */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .post-card {
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .post-image {
            position: relative;
            height: 200px;
            overflow: hidden;
            display: block;
        }
        
        .post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .post-card:hover .post-image img {
            transform: scale(1.05);
        }
        
        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            opacity: 0.9;
            text-transform: uppercase;
        }
        
        .post-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .post-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: #777;
            font-size: 0.85rem;
        }
        
        .post-meta span {
            display: flex;
            align-items: center;
        }
        
        .post-meta i {
            margin-right: 5px;
        }
        
        .post-content h3 {
            font-size: 1.25rem;
            margin-bottom: 15px;
            font-weight: 600;
            line-height: 1.4;
        }
        
        .post-content h3 a {
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .post-content h3 a:hover {
            color: var(--primary-color);
        }
        
        .post-content p {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
            flex: 1;
        }
        
        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        
        .read-more {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .read-more i {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }
        
        .read-more:hover {
            color: var(--accent-color);
        }
        
        .read-more:hover i {
            transform: translateX(5px);
        }
        
        .author {
            font-size: 0.85rem;
            color: #777;
            font-style: italic;
        }
        
        /* Estilos para la paginación */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin: 40px 0;
        }
        
        .pagination-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: white;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        .pagination-link:hover {
            background-color: #f2f2f2;
        }
        
        .pagination-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Estilos para chips de categoría */
        .category-chips {
            margin: 40px 0;
        }
        
        .category-chips h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .chips-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .category-chip {
            background-color: white;
            color: var(--text-color);
            padding: 8px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        .category-chip:hover {
            background-color: #f2f2f2;
        }
        
        .category-chip.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Estilos para mensaje de no resultados */
        .no-results {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: var(--box-shadow);
        }
        
        .no-results-message {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .no-results-message i {
            color: var(--secondary-color);
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        .no-results-message h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .no-results-message p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .btn-reset {
            display: inline-block;
            background-color: var(--secondary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-reset:hover {
            background-color: var(--primary-color);
        }
        
        /* Estilos para el footer */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 60px 0 20px;
            margin-top: 60px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background-color: var(--secondary-color);
        }
        
        .footer-column p {
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.95rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .footer-column ul li a i {
            margin-right: 8px;
            font-size: 0.8rem;
        }
        
        .footer-column ul li a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .social-links a:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .posts-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
            
            .blog-header h1 {
                font-size: 2.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .blog-filters {
                flex-direction: column;
            }
            
            .search-box {
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .filter-options {
                width: 100%;
                justify-content: space-between;
            }
            
            .active-filters {
                margin-top: 20px;
                justify-content: flex-start;
            }
            
            .active-filters-label {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .posts-grid {
                grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
            }
            
            .pagination-link {
                width: 35px;
                height: 35px;
            }
            
            .blog-header {
                padding: 40px 20px;
            }
            
            .blog-header h1 {
                font-size: 1.8rem;
            }
            
            .blog-header p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .post-meta {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .post-meta span {
                font-size: 0.8rem;
            }
            
            .filter-options {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .clear-filters {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .filter-tag {
                width: 100%;
                justify-content: space-between;
            }
            
            .pagination {
                gap: 3px;
            }
            
            .pagination-link {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .post-card {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .categoria-icono {
            width: 24px;
            height: 24px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        /* Estilos adicionales para la experiencia de usuario */
        .container.loading {
            opacity: 0.7;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .remove-filter.highlight {
            transform: scale(1.3);
            color: white;
        }
        
        @media (max-width: 480px) {
            .filter-tag {
                padding: 10px 15px;
            }
            
            .filter-tag .remove-filter {
                padding: 5px;
            }
        }
    </style>
</head>

<body>
<header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <a href="../index.php">
                    <img src="../assets/logo.png" class="logo" alt="Peace in Progress">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="../views/blog.php">Blog</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Categorías <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <?php
                            // Consultar todas las categorías para el menú
                            $sqlCategorias = "SELECT id_categoria, nombre, slug, imagen FROM categorias ORDER BY nombre";
                            $stmtCategorias = $pdo->prepare($sqlCategorias);
                            $stmtCategorias->execute();
                            $categorias_menu = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($categorias_menu as $cat) {
                                $nombre = htmlspecialchars($cat['nombre']);
                                $slug = htmlspecialchars($cat['slug']);
                                $imagen = !empty($cat['imagen']) ? htmlspecialchars($cat['imagen']) : '../assets/image-placeholder.png';
                                
                                // Verificar si la imagen existe
                                if (!file_exists('../' . $imagen) && strpos($imagen, '/') !== false) {
                                    $imagen = '../assets/image-placeholder.png';
                                } else {
                                    $imagen = '../' . $imagen;
                                }
                                
                                echo '<li>
                                    <a href="categoria.php?slug=' . $slug . '">
                                        <img src="' . $imagen . '" alt="' . $nombre . '" class="categoria-icono">
                                        ' . $nombre . '
                                    </a>
                                </li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="about.php" class="active">Sobre Nosotros</a></li>
                    <li><a href="contact.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="profile-section">
                <?php 
                if (!isset($_SESSION['usuario'])) {
                    echo '<a href="../admin/usuario.php" class="login-btn">
                        <svg class="login-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="m14 6c0 3.309-2.691 6-6 6s-6-2.691-6-6 2.691-6 6-6 6 2.691 6 6zm1 15v-6c0-.551.448-1 1-1h2v-2h-2c-1.654 0-3 1.346-3 3v6c0 1.654 1.346 3 3 3h2v-2h-2c-.552 0-1-.449-1-1zm8.583-3.841-3.583-3.159v3h-3v2h3v3.118l3.583-3.159c.556-.48.556-1.32 0-1.8zm-12.583-2.159c0-.342.035-.677.101-1h-6.601c-2.481 0-4.5 2.019-4.5 4.5v5.5h12.026c-.635-.838-1.026-1.87-1.026-3z"/>
                        </svg>
                    </a>';
                } else {
                    echo '<div class="profile-dropdown">
                        <button class="profile-btn">';
                    if (!empty($_SESSION['avatar']) && file_exists('../' . $_SESSION['avatar'])) {
                        echo '<img src="../' . htmlspecialchars($_SESSION['avatar']) . '" alt="Foto de perfil">';
                    } else {
                        echo '<i class="fas fa-user-circle"></i>';
                    }
                    echo '</button>
                        <div class="dropdown-content">
                            <a href="../admin/perfil.php"><i class="fas fa-user"></i> Perfil</a>';
                    
                    if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor')) {
                        echo '<a href="../admin/adminControl.php"><i class="fas fa-cog"></i> Admin</a>';
                    }
                    
                    echo '<a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                        </div>
                    </div>';
                }
                ?>
            </div>
            
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <div class="blog-header">
        <h1>Nuestro Blog</h1>
        <p>Explora todos nuestros artículos sobre paz, cooperación internacional y desarrollo sostenible</p>
    </div>

    <div class="container">
        <div class="blog-filters">
            <form method="GET" action="blog.php" class="search-box" id="searchForm">
                <input type="text" name="busqueda" placeholder="Buscar artículos..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
                
                <?php if ($categoria_actual > 0): ?>
                <input type="hidden" name="categoria" value="<?php echo $categoria_actual; ?>">
                <?php endif; ?>
                
                <?php if ($orden !== 'recientes'): ?>
                <input type="hidden" name="orden" value="<?php echo htmlspecialchars($orden); ?>">
                <?php endif; ?>
            </form>
            
            <div class="filter-options">
                <div class="category-filter">
                    <select name="categoria" id="categoriaSelect" onchange="redirigirConFiltros()">
                        <option value="0" <?php echo $categoria_actual == 0 ? 'selected' : ''; ?>>Todas las Categorías</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" 
                                <?php echo $categoria_actual == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); 
                                if ($cat['total_posts'] > 0) {
                                    echo ' (' . $cat['total_posts'] . ')';
                                } else {
                                    echo ' (0)';
                                } ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="order-filter">
                    <select name="orden" id="ordenSelect" onchange="redirigirConFiltros()">
                        <option value="recientes" <?php echo $orden === 'recientes' ? 'selected' : ''; ?>>Más recientes</option>
                        <option value="populares" <?php echo $orden === 'populares' ? 'selected' : ''; ?>>Más populares</option>
                        <option value="antiguos" <?php echo $orden === 'antiguos' ? 'selected' : ''; ?>>Más antiguos</option>
                    </select>
                </div>
                
                <?php if ($categoria_actual > 0 || !empty($busqueda) || $orden !== 'recientes'): ?>
                <div class="clear-filters">
                    <a href="blog.php" class="clear-filters-btn"><i class="fas fa-times-circle"></i> Limpiar filtros</a>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($categoria_actual > 0 || !empty($busqueda) || $orden !== 'recientes'): ?>
            <div class="active-filters">
                <div class="active-filters-label">Filtros activos:</div>
                <?php if ($categoria_actual > 0): 
                    $categoria_nombre = '';
                    foreach ($categorias as $cat) {
                        if ($cat['id_categoria'] == $categoria_actual) {
                            $categoria_nombre = $cat['nombre'];
                            break;
                        }
                    }
                ?>
                <div class="filter-tag">
                    <span>Categoría: <?php echo htmlspecialchars($categoria_nombre); ?></span>
                    <a href="<?php 
                        $params = [];
                        if (!empty($busqueda)) $params[] = 'busqueda=' . urlencode($busqueda);
                        if ($orden !== 'recientes') $params[] = 'orden=' . urlencode($orden);
                        echo 'blog.php' . (!empty($params) ? '?' . implode('&', $params) : '');
                    ?>" class="remove-filter"><i class="fas fa-times"></i></a>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($busqueda)): ?>
                <div class="filter-tag">
                    <span>Búsqueda: "<?php echo htmlspecialchars($busqueda); ?>"</span>
                    <a href="<?php 
                        $params = [];
                        if ($categoria_actual > 0) $params[] = 'categoria=' . $categoria_actual;
                        if ($orden !== 'recientes') $params[] = 'orden=' . urlencode($orden);
                        echo 'blog.php' . (!empty($params) ? '?' . implode('&', $params) : '');
                    ?>" class="remove-filter"><i class="fas fa-times"></i></a>
                </div>
                <?php endif; ?>
                
                <?php if ($orden !== 'recientes'): 
                    $orden_texto = '';
                    switch ($orden) {
                        case 'populares':
                            $orden_texto = 'Más populares';
                            break;
                        case 'antiguos':
                            $orden_texto = 'Más antiguos';
                            break;
                    }
                ?>
                <div class="filter-tag">
                    <span>Orden: <?php echo $orden_texto; ?></span>
                    <a href="<?php 
                        $params = [];
                        if ($categoria_actual > 0) $params[] = 'categoria=' . $categoria_actual;
                        if (!empty($busqueda)) $params[] = 'busqueda=' . urlencode($busqueda);
                        echo 'blog.php' . (!empty($params) ? '?' . implode('&', $params) : '');
                    ?>" class="remove-filter"><i class="fas fa-times"></i></a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (empty($posts)): ?>
        <div class="no-results">
            <div class="no-results-message">
                <i class="fas fa-search fa-3x"></i>
                <h2>No se encontraron artículos</h2>
                <?php if (!empty($busqueda)): ?>
                    <p>No hay resultados para "<?php echo htmlspecialchars($busqueda); ?>"</p>
                    <a href="blog.php" class="btn-reset">Limpiar filtros</a>
                <?php elseif ($categoria_actual > 0): ?>
                    <p>No hay artículos en esta categoría</p>
                    <a href="blog.php" class="btn-reset">Ver todas las categorías</a>
                <?php else: ?>
                    <p>Pronto añadiremos nuevo contenido</p>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        
        <div class="posts-grid">
            <?php foreach ($posts as $post): 
                // Determinar la ruta de la imagen correctamente
                $imagen = '';
                if (!empty($post['imagen_destacada'])) {
                    // Verificar si ya tiene el prefijo ../
                    if (strpos($post['imagen_destacada'], '../') === 0) {
                        $imagen = substr($post['imagen_destacada'], 3); // Eliminar el prefijo ../
                    } else {
                        $imagen = $post['imagen_destacada'];
                    }
                    
                    // Verificar si el archivo existe en alguna ubicación posible
                    if (!file_exists('../' . $imagen) && !file_exists($imagen)) {
                        $imagen = 'assets/image-placeholder.png';
                    }
                } else {
                    $imagen = 'assets/image-placeholder.png';
                }
                
                // Formatear la fecha
                $fecha = new DateTime($post['fecha_publicacion']);
                $fecha_formateada = $fecha->format('d \d\e F \d\e Y');
                
                // Acortar la descripción si es necesaria
                $descripcion_corta = strlen($post['resumen']) > 120 ? substr($post['resumen'], 0, 120) . '...' : $post['resumen'];
            ?>
            <div class="post-card">
                <a href="post.php?id=<?php echo $post['id_post']; ?>" class="post-image">
                    <img src="../<?php echo $imagen; ?>" 
                         alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                         onerror="this.src='../assets/image-placeholder.png';">
                    <div class="category-badge"><?php echo htmlspecialchars($post['categoria_nombre']); ?></div>
                </a>
                <div class="post-content">
                    <div class="post-meta">
                        <span><i class="far fa-calendar"></i> <?php echo $fecha_formateada; ?></span>
                        <span><i class="far fa-eye"></i> <?php echo number_format($post['visitas']); ?></span>
                        <span><i class="far fa-heart"></i> <?php echo number_format($post['likes']); ?></span>
                    </div>
                    <h3><a href="post.php?id=<?php echo $post['id_post']; ?>"><?php echo htmlspecialchars($post['titulo']); ?></a></h3>
                    <p><?php echo htmlspecialchars($descripcion_corta); ?></p>
                    <div class="post-footer">
                        <a href="post.php?id=<?php echo $post['id_post']; ?>" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
                        <span class="author"><?php echo htmlspecialchars($post['autor_nombre']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_paginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=1<?php echo ($categoria_actual > 0 ? '&categoria='.$categoria_actual : '').(!empty($busqueda) ? '&busqueda='.urlencode($busqueda) : '').($orden != 'recientes' ? '&orden='.$orden : ''); ?>" class="pagination-link first">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                <a href="?pagina=<?php echo ($pagina_actual - 1); ?><?php echo ($categoria_actual > 0 ? '&categoria='.$categoria_actual : '').(!empty($busqueda) ? '&busqueda='.urlencode($busqueda) : '').($orden != 'recientes' ? '&orden='.$orden : ''); ?>" class="pagination-link prev">
                    <i class="fas fa-angle-left"></i>
                </a>
            <?php endif; ?>
            
            <?php
            // Mostrar un número limitado de links de páginas
            $max_links = 5;
            $start_page = max(1, min($pagina_actual - floor($max_links / 2), $total_paginas - $max_links + 1));
            $end_page = min($total_paginas, $start_page + $max_links - 1);
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <a href="?pagina=<?php echo $i; ?><?php echo ($categoria_actual > 0 ? '&categoria='.$categoria_actual : '').(!empty($busqueda) ? '&busqueda='.urlencode($busqueda) : '').($orden != 'recientes' ? '&orden='.$orden : ''); ?>" 
                   class="pagination-link <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?php echo ($pagina_actual + 1); ?><?php echo ($categoria_actual > 0 ? '&categoria='.$categoria_actual : '').(!empty($busqueda) ? '&busqueda='.urlencode($busqueda) : '').($orden != 'recientes' ? '&orden='.$orden : ''); ?>" class="pagination-link next">
                    <i class="fas fa-angle-right"></i>
                </a>
                <a href="?pagina=<?php echo $total_paginas; ?><?php echo ($categoria_actual > 0 ? '&categoria='.$categoria_actual : '').(!empty($busqueda) ? '&busqueda='.urlencode($busqueda) : '').($orden != 'recientes' ? '&orden='.$orden : ''); ?>" class="pagination-link last">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        
        <div class="category-chips">
            <h3>Explorar por categoría</h3>
            <div class="chips-container">
                <a href="blog.php<?php echo !empty($busqueda) ? '?busqueda='.urlencode($busqueda) : ''; ?><?php echo $orden != 'recientes' ? (!empty($busqueda) ? '&' : '?').'orden='.$orden : ''; ?>" 
                   class="category-chip <?php echo $categoria_actual == 0 ? 'active' : ''; ?>">
                    Todas
                </a>
                <?php foreach($categorias as $cat): ?>
                    <a href="blog.php?categoria=<?php echo $cat['id_categoria']; ?><?php echo !empty($busqueda) ? '&busqueda='.urlencode($busqueda) : ''; ?><?php echo $orden != 'recientes' ? '&orden='.$orden : ''; ?>" 
                       class="category-chip <?php echo $categoria_actual == $cat['id_categoria'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                        <?php if ($cat['total_posts'] > 0): ?>
                            (<?php echo $cat['total_posts']; ?>)
                        <?php else: ?>
                            (0)
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Sobre Nosotros</h3>
                    <p>Peace in Progress es una organización dedicada a promover la paz, el entendimiento intercultural y el desarrollo sostenible a través de investigación, educación y acción.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="../index.php"><i class="fas fa-angle-right"></i> Página Principal</a></li>
                        <li><a href="about.php"><i class="fas fa-angle-right"></i> Sobre Nosotros</a></li>
                        <li><a href="contact.php"><i class="fas fa-angle-right"></i> Contacto</a></li>
                        <li><a href="../admin/usuario.php"><i class="fas fa-angle-right"></i> Iniciar Sesión</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Categorías</h3>
                    <ul>
                        <?php 
                        $max_categories = 5;
                        $count = 0;
                        foreach($categorias as $cat): 
                            if($cat['total_posts'] > 0 && $count < $max_categories):
                                $count++;
                        ?>
                        <li>
                            <a href="blog.php?categoria=<?php echo $cat['id_categoria']; ?>">
                                <i class="fas fa-angle-right"></i> <?php echo htmlspecialchars($cat['nombre']); ?>
                            </a>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Universidad de Colima, Campus Villa de Álvarez</p>
                    <p><i class="fas fa-phone"></i> +52 312 123 4567</p>
                    <p><i class="fas fa-envelope"></i> info@peaceinprogress.org</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Peace in Progress. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar filtros de categoría y orden
            const categoriaSelect = document.getElementById('categoriaSelect');
            const ordenSelect = document.getElementById('ordenSelect');
            const searchForm = document.getElementById('searchForm');
            
            if (categoriaSelect) {
                categoriaSelect.addEventListener('change', function() {
                    // Construcción directa de URL en lugar de manipular el DOM
                    redirigirConFiltros();
                });
            }
            
            if (ordenSelect) {
                ordenSelect.addEventListener('change', function() {
                    // Construcción directa de URL en lugar de manipular el DOM
                    redirigirConFiltros();
                });
            }
            
            // Nueva función simplificada que evita manipular el DOM
            function redirigirConFiltros() {
                const categoriaSelect = document.getElementById('categoriaSelect');
                const ordenSelect = document.getElementById('ordenSelect');
                const searchInput = document.querySelector('input[name="busqueda"]');
                
                const categoria = categoriaSelect ? categoriaSelect.value : '0';
                const orden = ordenSelect ? ordenSelect.value : 'recientes';
                const busqueda = searchInput ? searchInput.value.trim() : '';
                
                // Construir URL directamente
                let url = 'blog.php';
                let params = [];
                
                if (categoria !== '0') {
                    params.push(`categoria=${categoria}`);
                }
                
                if (busqueda) {
                    params.push(`busqueda=${encodeURIComponent(busqueda)}`);
                }
                
                if (orden !== 'recientes') {
                    params.push(`orden=${orden}`);
                }
                
                if (params.length > 0) {
                    url += '?' + params.join('&');
                }
                
                // Mostrar indicador de carga
                const bloqueContenido = document.querySelector('.container');
                if (bloqueContenido) {
                    bloqueContenido.classList.add('loading');
                }
                
                // Redirigir directamente
                window.location.href = url;
            }
            
            // Hacer que las tarjetas de posts tengan la misma altura en cada fila
            function equalizeCardHeights() {
                // Solo ejecutar en pantallas más grandes donde hay múltiples columnas
                if (window.innerWidth >= 768) {
                    const cards = document.querySelectorAll('.post-card');
                    let maxHeight = 0;
                    
                    // Restablecer alturas
                    cards.forEach(card => {
                        card.style.height = 'auto';
                        const height = card.offsetHeight;
                        maxHeight = Math.max(maxHeight, height);
                    });
                    
                    // Aplicar altura máxima
                    cards.forEach(card => {
                        card.style.height = maxHeight + 'px';
                    });
                } else {
                    // En móvil, resetear alturas
                    const cards = document.querySelectorAll('.post-card');
                    cards.forEach(card => {
                        card.style.height = 'auto';
                    });
                }
            }
            
            // Funcionalidad para filtros táctiles en móviles
            const filterTags = document.querySelectorAll('.filter-tag');
            filterTags.forEach(tag => {
                tag.addEventListener('click', function(e) {
                    // Solo ejecutar si se hace clic en el tag pero no en el icono de cierre
                    if (e.target.closest('.remove-filter') === null && e.target.classList.contains('remove-filter') === false) {
                        const removeLink = this.querySelector('.remove-filter');
                        if (removeLink) {
                            removeLink.classList.add('highlight');
                            setTimeout(() => {
                                removeLink.classList.remove('highlight');
                            }, 300);
                        }
                    }
                });
            });
            
            // Ejecutar al cargar la página
            equalizeCardHeights();
            
            // Ejecutar al cambiar el tamaño de la ventana
            window.addEventListener('resize', equalizeCardHeights);
        });
    </script>
    <script src="../views/js/nav-fix.js"></script>
    <script src="../js/profile-menu.js"></script>
</body>
</html> 