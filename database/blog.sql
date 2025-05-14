-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-05-2025 a las 13:38:43
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id_comentario`, `id_post`, `id_usuario`, `nombre_autor`, `email_autor`, `contenido`, `fecha_comentario`, `aprobado`, `ip_address`, `user_agent`) VALUES
(1, 7, 2, NULL, NULL, 'Prueba 1', '2025-05-14 01:21:49', 1, NULL, NULL),
(2, 7, 2, NULL, NULL, 'ingaturroña', '2025-05-14 01:22:01', 1, NULL, NULL),
(3, 7, 2, NULL, NULL, 'si jala', '2025-05-14 01:22:10', 1, NULL, NULL),
(4, 7, 2, NULL, NULL, 'XD', '2025-05-14 01:22:23', 1, NULL, NULL);

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
  `tipo_imagen` enum('background','ilustrativa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ilustrativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes`
--

INSERT INTO `imagenes` (`id_imagen`, `ruta`, `titulo`, `alt_text`, `fecha_subida`, `id_usuario`, `tipo_imagen`) VALUES
(7, '../assets/68240907ba2b3.jpeg', 'Imagen ilustrativa para: LA GUERRA DE RUSIA Y UCRANIA', 'Imagen ilustrativa del post: LA GUERRA DE RUSIA Y UCRANIA', '2025-05-13 21:07:51', 2, 'ilustrativa'),
(8, '../assets/68240907bc9d4.jpg', 'Imagen de fondo para: LA GUERRA DE RUSIA Y UCRANIA', 'Imagen de fondo del post: LA GUERRA DE RUSIA Y UCRANIA', '2025-05-13 21:07:51', 2, 'background');

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
(7, 'LA GUERRA DE RUSIA Y UCRANIA', 'la-guerra-de-rusia-y-ucrania', 'En Rusia, protestar contra la guerra puede costarte hasta 15 años de prisión. La censura no solo silencia, también castiga.', 'La censura de las protestas\r\nDesde el inicio de la invasión a gran escala de Ucrania en febrero de 2022, el gobierno ruso ha intensificado su represión contra cualquier forma de disidencia, implementando leyes que criminalizan la protesta pacífica y la libertad de expresión. Estas medidas han resultado en la detención y encarcelamiento de miles de ciudadanos que se oponen a la guerra, según informes de Amnistía Internacional.\r\n\r\nLegislación para silenciar la disidencia\r\nPoco después de iniciada la guerra, Rusia introdujo leyes de censura que penalizan la difusión de \"información falsa\" y la \"desacreditación\" de las fuerzas armadas, con penas de hasta 15 años de prisión. Estas leyes han sido utilizadas para castigar a quienes expresan opiniones contrarias a la narrativa oficial sobre el conflicto en Ucrania.\r\n\r\nRepresalias más allá del encarcelamiento\r\nAdemás de las penas de prisión, las autoridades rusas han empleado otras tácticas para reprimir la disidencia: • Confiscación de propiedades: En 2024, se aprobó una ley que permite confiscar bienes de personas acusadas bajo las leyes de censura de guerra • Represión a menores: Niños y niñas han sido víctimas de persecución política debido a las opiniones de sus padres o por expresar su desacuerdo con la guerra • Negación de contacto familiar: A los detenidos se les ha negado sistemáticamente el contacto con sus familias, como en el caso del político de oposición Vladimir Kara-Murza, quien estuvo más de un año sin comunicación con sus seres queridos.\r\n\r\nAmnistía Internacional insta a la comunidad internacional a exigir la derogación de las leyes de censura de guerra en Rusia y la liberación inmediata de todas las personas encarceladas por expresar pacíficamente sus opiniones. La organización también anima a firmar peticiones y enviar mensajes de solidaridad a los presos de conciencia. La represión en Rusia ha alcanzado niveles alarmantes, equiparando las penas por protestar contra la guerra con las impuestas por delitos graves como el atraco a mano armada. Es fundamental que la comunidad internacional se solidarice con quienes defienden la paz y la libertad de expresión en Rusia.', 1, 7, 8, 2, '2025-05-14 03:07:51', NULL, 'publicado', 0, NULL);

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
-- Estructura de tabla para la tabla `post_metadatos`
--

CREATE TABLE `post_metadatos` (
  `id_metadato` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_actividades`
--

CREATE TABLE `registro_actividades` (
  `id_registro` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo_actividad` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_actividad` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `biografia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `token_recuperacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_expiracion_token` datetime DEFAULT NULL,
  `intentos_login` int(11) NOT NULL DEFAULT '0',
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `name`, `email`, `pass`, `fecha_registro`, `ultimo_login`, `rol`, `activo`, `avatar`, `biografia`, `token_recuperacion`, `fecha_expiracion_token`, `intentos_login`, `bloqueado_hasta`) VALUES
(2, 'Christopher Eugenio Nieves Martínez', 'cnieves0@ucol.mx', '$2y$10$A3VAT6BsMOQRrdCltWM69.wnhcvWZFoQzvFwQ1vBfOOT8lzWtuDSG', '2025-05-13 15:39:04', NULL, 'admin', 1, NULL, NULL, NULL, NULL, 0, NULL);

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
  ADD KEY `idx_comentarios_post` (`id_post`),
  ADD KEY `idx_comentarios_fecha` (`fecha_comentario`),
  ADD KEY `idx_comentarios_aprobado` (`aprobado`);

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
  ADD KEY `idx_posts_imagen_background` (`id_imagen_background`),
  ADD KEY `idx_posts_fecha` (`fecha_publicacion`),
  ADD KEY `idx_posts_estado` (`estado`),
  ADD KEY `idx_posts_categoria` (`id_categoria`);

--
-- Indices de la tabla `posts_tags`
--
ALTER TABLE `posts_tags`
  ADD PRIMARY KEY (`id_post`,`id_tag`),
  ADD KEY `id_tag` (`id_tag`);

--
-- Indices de la tabla `post_metadatos`
--
ALTER TABLE `post_metadatos`
  ADD PRIMARY KEY (`id_metadato`),
  ADD UNIQUE KEY `idx_post_meta` (`id_post`,`meta_key`);

--
-- Indices de la tabla `registro_actividades`
--
ALTER TABLE `registro_actividades`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_registro_fecha` (`fecha_actividad`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_sesiones_fecha` (`fecha_ultima_actividad`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_rol` (`rol`);

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
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `post_metadatos`
--
ALTER TABLE `post_metadatos`
  MODIFY `id_metadato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_actividades`
--
ALTER TABLE `registro_actividades`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tags`
--
ALTER TABLE `tags`
  MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Filtros para la tabla `post_metadatos`
--
ALTER TABLE `post_metadatos`
  ADD CONSTRAINT `fk_metadatos_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registro_actividades`
--
ALTER TABLE `registro_actividades`
  ADD CONSTRAINT `fk_registro_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

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
