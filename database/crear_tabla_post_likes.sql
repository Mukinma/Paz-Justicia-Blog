-- Estructura de tabla para la tabla `post_likes`
CREATE TABLE `post_likes` (
  `id_like` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_like`),
  UNIQUE KEY `uk_post_usuario` (`id_post`,`id_usuario`),
  KEY `idx_likes_post` (`id_post`),
  KEY `idx_likes_usuario` (`id_usuario`),
  CONSTRAINT `fk_likes_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 