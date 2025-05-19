<?php
// Obtener la ruta base para los enlaces
$es_index = basename($_SERVER['PHP_SELF']) === 'index.php';
$base_url = $es_index ? '' : '../';

// Consultar categorías para el footer si no están definidas
if (!isset($categorias) && isset($pdo)) {
    $sql_footer_cats = "SELECT c.id_categoria, c.nombre, c.slug,
                    (SELECT COUNT(*) FROM posts p WHERE p.id_categoria = c.id_categoria AND p.estado = 'publicado') as total_posts
                    FROM categorias c
                    ORDER BY c.nombre ASC";
    $stmt_footer_cats = $pdo->query($sql_footer_cats);
    $categorias_footer = $stmt_footer_cats->fetchAll(PDO::FETCH_ASSOC);
} else {
    $categorias_footer = $categorias ?? [];
}
?>

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
                    <li><a href="<?php echo $base_url; ?>index.php"><i class="fas fa-angle-right"></i> Página Principal</a></li>
                    <li><a href="<?php echo $base_url; ?>views/about.php"><i class="fas fa-angle-right"></i> Sobre Nosotros</a></li>
                    <li><a href="<?php echo $base_url; ?>views/blog.php"><i class="fas fa-angle-right"></i> Blog</a></li>
                    <li><a href="<?php echo $base_url; ?>views/contact.php"><i class="fas fa-angle-right"></i> Contacto</a></li>
                    <li><a href="<?php echo $base_url; ?>admin/usuario.php"><i class="fas fa-angle-right"></i> Iniciar Sesión</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Categorías</h3>
                <ul>
                    <?php 
                    $max_categories = 5;
                    $count = 0;
                    foreach($categorias_footer as $cat): 
                        if(isset($cat['total_posts']) && $cat['total_posts'] > 0 && $count < $max_categories):
                            $count++;
                    ?>
                    <li>
                        <a href="<?php echo $base_url; ?>views/blog.php?categoria=<?php echo $cat['id_categoria']; ?>">
                            <i class="fas fa-angle-right"></i> <?php echo htmlspecialchars($cat['nombre']); ?>
                        </a>
                    </li>
                    <?php 
                        endif;
                    endforeach; 

                    // Si no hay categorías con posts, mostrar algunas categorías
                    if ($count === 0):
                        $count_alt = 0;
                        foreach($categorias_footer as $cat):
                            if ($count_alt < $max_categories):
                                $count_alt++;
                    ?>
                    <li>
                        <a href="<?php echo $base_url; ?>views/blog.php?categoria=<?php echo $cat['id_categoria']; ?>">
                            <i class="fas fa-angle-right"></i> <?php echo htmlspecialchars($cat['nombre']); ?>
                        </a>
                    </li>
                    <?php
                            endif;
                        endforeach;
                    endif;
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