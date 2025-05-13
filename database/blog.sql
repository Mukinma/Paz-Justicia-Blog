-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 13-05-2025 a las 19:21:12
-- Versión del servidor: 8.0.17
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `blog`
--

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `quitar_acentos` (`cadena` VARCHAR(255)) RETURNS VARCHAR(255) CHARSET utf8 COLLATE utf8_spanish2_ci BEGIN
    SET cadena = REPLACE(cadena, 'á', 'a');
    SET cadena = REPLACE(cadena, 'é', 'e');
    SET cadena = REPLACE(cadena, 'í', 'i');
    SET cadena = REPLACE(cadena, 'ó', 'o');
    SET cadena = REPLACE(cadena, 'ú', 'u');
    SET cadena = REPLACE(cadena, 'Á', 'a');
    SET cadena = REPLACE(cadena, 'É', 'e');
    SET cadena = REPLACE(cadena, 'Í', 'i');
    SET cadena = REPLACE(cadena, 'Ó', 'o');
    SET cadena = REPLACE(cadena, 'Ú', 'u');
    SET cadena = REPLACE(cadena, 'ñ', 'n');
    SET cadena = REPLACE(cadena, 'Ñ', 'n');
    RETURN cadena;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `slug`, `descripcion`) VALUES
(1, 'Justicia y derechos', 'justicia-y-derechos', 'Artículos sobre leyes, justicia, y derechos humanos.'),
(2, 'Cultura de paz', 'cultura-de-paz', 'Educación para la paz, mediación, diálogo y no violencia.'),
(3, 'Instituciones sólidas', 'instituciones-solidas', 'Transparencia, gobiernos responsables, ONGs.'),
(4, 'Participación ciudadana', 'participacion-ciudadana', 'Activismo, proyectos sociales, voz ciudadana.'),
(5, 'Noticias y actualidad', 'noticias-y-actualidad', 'Últimos acontecimientos relacionados con el ODS 16.'),
(6, 'Opinión', 'opinion', 'Análisis personales o críticas constructivas.'),
(7, 'Educación cívica', 'educacion-civica', 'Información sobre democracia, constitución y ciudadanía.'),
(8, 'Historias de impacto', 'historias-de-impacto', 'Relatos que inspiran y promueven el cambio social.'),
(9, 'Recursos y herramientas', 'recursos-y-herramientas', 'Guías, documentos y enlaces útiles sobre ODS 16.');

--
-- Disparadores `categorias`
--
DELIMITER $$
CREATE TRIGGER `before_insert_categoria` BEFORE INSERT ON `categorias` FOR EACH ROW BEGIN
  SET NEW.slug = LOWER(REPLACE(quitar_acentos(NEW.nombre), ' ', '-'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_autor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_autor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenido` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_comentario` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aprobado` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `id_imagen` int(11) NOT NULL,
  `ruta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_subida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int(11) NOT NULL,
  `tipo_imagen` ENUM('background', 'ilustrativa') NOT NULL DEFAULT 'ilustrativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes`
--

INSERT INTO `imagenes` (`id_imagen`, `ruta`, `titulo`, `alt_text`, `fecha_subida`, `id_usuario`) VALUES
(1, '681db3e354e95.png', 'Imagen destacada para: Prueba 14', 'Imagen ilustrativa del post: Prueba 14', '2025-05-09 01:50:59', 1),
(2, '../assets/681e26e219cf9.png', 'Imagen destacada para: Prueba 15', 'Imagen ilustrativa del post: Prueba 15', '2025-05-09 10:01:38', 1),
(3, '../assets/681e4ff08565e.png', 'Imagen destacada para: Prueba 16', 'Imagen ilustrativa del post: Prueba 16', '2025-05-09 12:56:48', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

CREATE TABLE `posts` (
  `id_post` int(11) NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `resumen` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_imagen_destacada` int(11) DEFAULT NULL,
  `id_imagen_background` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_publicacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `estado` enum('borrador','publicado','archivado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `visitas` int(11) NOT NULL DEFAULT '0',
  `referencia_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id_post`, `titulo`, `slug`, `resumen`, `contenido`, `id_categoria`, `id_imagen_destacada`, `id_imagen_background`, `id_usuario`, `fecha_publicacion`, `fecha_actualizacion`, `estado`, `visitas`, `referencia_url`) VALUES
(1, 'Prueba 1', 'prueba-1', 'Primer intento de insertar', 'me eestaa volviendo locoo', 2, NULL, NULL, 1, '2025-05-08 00:00:00', '2025-05-11 04:12:56', 'publicado', 0, NULL),
(2, 'Prueba 2', 'prueba-2', 'Segundo intento de insertar', 'me eestaa volviendo locoo, me eestaa volviendo locoo, me eestaa volviendo locoo, me eestaa volviendo locoo', 2, NULL, NULL, 1, '2025-05-08 00:00:00', '2025-05-11 03:32:29', 'publicado', 0, NULL),
(3, 'Prueba 12', 'prueba-12', 'Ayuda', 'No puedo amar, ¿no puedo amar?\r\n¿O solo no amo como aman los demás?\r\n¿Cómo hay que amar? ¿Hay que amar?\r\nHay que desarmar los preceptos hechos y tirarse al mar', 2, NULL, NULL, 1, '2025-05-09 00:00:00', NULL, 'publicado', 0, NULL),
(4, 'Prueba 14', 'prueba-14', ':(', '...', 6, 1, NULL, 1, '2025-05-09 00:00:00', NULL, 'publicado', 0, NULL),
(5, 'Prueba 15', 'prueba-15', 'AAAAAAAAAAAa', 'Matenme', 9, 2, NULL, 1, '2025-05-09 00:00:00', NULL, 'publicado', 0, NULL),
(6, 'Prueba 16-2', 'prueba-16-2', 'Ahuevo', '¡Cómo han pasado los años!, pero sé muy bien, que lo han escuchado\r\nTambién deben de saber que todas las hazañas que este hombre ha logrado\r\nQue por los años ochentas ya era el encargado del mundo del narco\r\nY que formó un gran imperio que hasta la fecha\r\nAún sigue dando\r\n\r\nVoy a presentarme, mi nombre es Rafael Caro Quintero\r\nPregunten a sus abuelos, yo sé que más de alguno\r\nMe escuchó en el noticiero\r\nSoy considerado el narco de narcos y el número uno\r\nSoy de Sinaloa, proveniente de La Noria\r\nY pa todo el mundo\r\n\r\nLa cárcel no es pa siempre, como lo aclaré en aquella entrevista\r\nBien recuerdo las preguntas que me estaba haciendo aquella señorita\r\nYo no conseguí lo que alcancé a tener, \"fácil\", como ella decía\r\nCreo que le quedó bien clara la respuesta que le di\r\n\"¡Nada es fácil en la vida!\"\r\n\r\nPorque nada es fácil en la vida\r\nPor más sencillo que sea, ¡ja, jay!\r\nSomos T3r Elemento, con la R records\r\n\r\nHoy el tiempo ha pasado, la tormenta terminó, pienso hacer muchas cosas\r\nArrepentido, jamás, lo hecho, hecho está, así se escribió en la historia\r\nMe relacionaron con la muerte de un agente de la DEA\r\nCreo que están equivocados, como dice aquel corrido\r\n\"¡Yo no maté a Camarena!\"\r\n\r\nMis sentencias he cumplido, después de veintiocho años\r\nHoy salgo por la puerta\r\nFue bastante tiempo para pensar bien las cosas, vengo con más experiencia\r\nY hoy mi pelo ya no es negro, pues también cambió, de canas se ha pintado\r\nY aunque soy un poco viejo, sigo haciendo bien las cosas\r\nSiempre lo he demostrado\r\n\r\nYa me voy a despedir, respiro aire libre, hoy estoy muy contento\r\nVisitar a mi familia, tengo mucho que contarles, tengo ganas de verlos\r\nDon Rafa ya salió y viene con todo, su legado sigue activo\r\nSu apellido es Caro y el señor sigue siendo\r\nMuy seguro de sí mismo', 8, 3, NULL, 1, '2025-05-09 00:00:00', '2025-05-12 07:49:56', 'publicado', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts_tags`
--

CREATE TABLE `posts_tags` (
  `id_post` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id_sesion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultima_actividad` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags`
--

CREATE TABLE `tags` (
  `id_tag` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_login` datetime DEFAULT NULL,
  `rol` enum('admin','editor','lector') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lector',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biografia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `name`, `email`, `pass`, `fecha_registro`, `ultimo_login`, `rol`, `activo`, `avatar`, `biografia`) VALUES
(1, 'Juan Pérez', 'alphalogic.peaceinprogress@gmail.com', 'fatima2581', '2025-05-07 14:00:20', NULL, 'lector', 1, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_post` (`id_post`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_comentarios_post` (`id_post`);

--
-- Indices de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_post`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_imagen_destacada` (`id_imagen_destacada`),
  ADD KEY `id_imagen_background` (`id_imagen_background`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_posts_slug` (`slug`),
  ADD KEY `idx_posts_imagen_background` (`id_imagen_background`);

--
-- Indices de la tabla `posts_tags`
--
ALTER TABLE `posts_tags`
  ADD PRIMARY KEY (`id_post`,`id_tag`),
  ADD KEY `id_tag` (`id_tag`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id_tag`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tags`
--
ALTER TABLE `tags`
  MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD CONSTRAINT `fk_imagenes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `imagenes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `fk_posts_imagen` FOREIGN KEY (`id_imagen_destacada`) REFERENCES `imagenes` (`id_imagen`),
  ADD CONSTRAINT `fk_posts_imagen_background` FOREIGN KEY (`id_imagen_background`) REFERENCES `imagenes` (`id_imagen`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_posts_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`id_imagen_destacada`) REFERENCES `imagenes` (`id_imagen`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `posts_tags`
--
ALTER TABLE `posts_tags`
  ADD CONSTRAINT `fk_posts_tags_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`),
  ADD CONSTRAINT `fk_posts_tags_tag` FOREIGN KEY (`id_tag`) REFERENCES `tags` (`id_tag`),
  ADD CONSTRAINT `posts_tags_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_tags_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tags` (`id_tag`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Mejorar la tabla usuarios con campos adicionales y mejor seguridad
ALTER TABLE `usuarios` 
  ADD COLUMN `token_recuperacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ADD COLUMN `fecha_expiracion_token` datetime DEFAULT NULL,
  ADD COLUMN `intentos_login` int(11) NOT NULL DEFAULT '0',
  ADD COLUMN `bloqueado_hasta` datetime DEFAULT NULL,
  ADD INDEX `idx_usuarios_email` (`email`),
  ADD INDEX `idx_usuarios_rol` (`rol`);

-- Mejorar la tabla posts con índices adicionales
ALTER TABLE `posts` 
  ADD INDEX `idx_posts_fecha` (`fecha_publicacion`),
  ADD INDEX `idx_posts_estado` (`estado`),
  ADD INDEX `idx_posts_categoria` (`id_categoria`);

-- Mejorar la tabla comentarios con índices adicionales
ALTER TABLE `comentarios` 
  ADD INDEX `idx_comentarios_fecha` (`fecha_comentario`),
  ADD INDEX `idx_comentarios_aprobado` (`aprobado`);

-- Mejorar la tabla sesiones con índice adicional
ALTER TABLE `sesiones` 
  ADD INDEX `idx_sesiones_fecha` (`fecha_ultima_actividad`);

-- Agregar tabla para registro de actividades
CREATE TABLE `registro_actividades` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo_actividad` varchar(50) NOT NULL,
  `descripcion` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `fecha_actividad` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_registro`),
  KEY `id_usuario` (`id_usuario`),
  KEY `idx_registro_fecha` (`fecha_actividad`),
  CONSTRAINT `fk_registro_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar tabla para metadatos de posts
CREATE TABLE `post_metadatos` (
  `id_metadato` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`id_metadato`),
  UNIQUE KEY `idx_post_meta` (`id_post`,`meta_key`),
  CONSTRAINT `fk_metadatos_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar tabla para suscripciones
CREATE TABLE `suscripciones` (
  `id_suscripcion` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token_confirmacion` varchar(255) DEFAULT NULL,
  `confirmado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_suscripcion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_confirmacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_suscripcion`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Actualizar los datos existentes (asumiendo que las imágenes actuales son ilustrativas)
UPDATE `imagenes` SET `tipo_imagen` = 'ilustrativa' WHERE `tipo_imagen` IS NULL;
