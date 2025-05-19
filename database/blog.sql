-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 17-05-2025 a las 20:12:30
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
CREATE DEFINER=`root`@`localhost` FUNCTION `quitar_acentos` (`texto` VARCHAR(255)) RETURNS VARCHAR(255) CHARSET utf8 COLLATE utf8_spanish2_ci BEGIN
            DECLARE resultado VARCHAR(255);
            SET resultado = texto;
            SET resultado = REPLACE(resultado, 'á', 'a');
            SET resultado = REPLACE(resultado, 'é', 'e');
            SET resultado = REPLACE(resultado, 'í', 'i');
            SET resultado = REPLACE(resultado, 'ó', 'o');
            SET resultado = REPLACE(resultado, 'ú', 'u');
            SET resultado = REPLACE(resultado, 'Á', 'A');
            SET resultado = REPLACE(resultado, 'É', 'E');
            SET resultado = REPLACE(resultado, 'Í', 'I');
            SET resultado = REPLACE(resultado, 'Ó', 'O');
            SET resultado = REPLACE(resultado, 'Ú', 'U');
            SET resultado = REPLACE(resultado, 'ñ', 'n');
            SET resultado = REPLACE(resultado, 'Ñ', 'N');
            RETURN resultado;
        END$$

DELIMITER ;

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
(10, 'Paz y Conflictos', 'paz-y-conflictos', 'Cobertura de guerras, procesos de reconciliación y contextos de conflicto global.'),
(13, 'Justicia y Derechos Humanos', 'justicia-y-derechos-humanos', 'Acceso a la justicia, abusos de poder, sistema penitenciario y más. Esta sección examina la defensa de los derechos fundamentales como base de sociedades pacíficas.'),
(14, 'Igualdad y Diversidad', 'igualdad-y-diversidad', 'Causas y luchas por una sociedad más tolerante e inclusiva.'),
(15, 'Participación Ciudadana', 'participacion-ciudadana', 'Activismo, protestas pacificas y organizaciones que protegen.'),
(16, 'Corrupción y Transparencia', 'corrupcion-y-transparencia', 'Investigaciones sobre corrupción y reformas por un sistema justo.'),
(17, 'Política y Gobernanza', 'politica-y-gobernanza', 'Cobertura de política, programas y acciones del gobierno para fortalecer la paz y seguridad.');

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
-- Estructura de tabla para la tabla `error_log`
--

CREATE TABLE `error_log` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mensaje` text COLLATE utf8mb4_general_ci NOT NULL,
  `archivo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `linea` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(9, '../assets/6827f882d940b.webp', 'Imagen ilustrativa para: Trump asegura que no habrá avances sobre la guerr', 'Imagen ilustrativa del post: Trump asegura que no habrá avances sobre la guerra en Ucrania hasta que se reúna con Putin', '2025-05-16 20:46:26', 2, 'ilustrativa'),
(10, '../assets/6827f882dc760.jpeg', 'Imagen de fondo para: Trump asegura que no habrá avances sobre la guerr', 'Imagen de fondo del post: Trump asegura que no habrá avances sobre la guerra en Ucrania hasta que se reúna con Putin', '2025-05-16 20:46:26', 2, 'background'),
(11, '../assets/682802d37d178.jpg', 'Imagen ilustrativa para: Abogan por soluciones africanas a problemas de paz', 'Imagen ilustrativa del post: Abogan por soluciones africanas a problemas de paz y seguridad', '2025-05-16 21:30:27', 2, 'ilustrativa'),
(12, '../assets/682802d3805e3.webp', 'Imagen de fondo para: Abogan por soluciones africanas a problemas de paz', 'Imagen de fondo del post: Abogan por soluciones africanas a problemas de paz y seguridad', '2025-05-16 21:30:27', 2, 'background'),
(13, '../assets/6828037563cfa.jfif', 'Imagen ilustrativa para: Durango se posiciona entre los tres estados más p', 'Imagen ilustrativa del post: Durango se posiciona entre los tres estados más pacíficos del país, según el Índice de Paz Méx', '2025-05-16 21:33:09', 2, 'ilustrativa'),
(14, '../assets/6828037566260.webp', 'Imagen de fondo para: Durango se posiciona entre los tres estados más p', 'Imagen de fondo del post: Durango se posiciona entre los tres estados más pacíficos del país, según el Índice de Paz Méx', '2025-05-16 21:33:09', 2, 'background'),
(15, '../assets/682803a73d0db.jpg', 'Imagen ilustrativa para: Durango alcanza primer lugar nacional en seguridad', 'Imagen ilustrativa del post: Durango alcanza primer lugar nacional en seguridad; registra cero homicidios dolosos en abril', '2025-05-16 21:33:59', 2, 'ilustrativa'),
(16, '../assets/682803a73f3e0.webp', 'Imagen de fondo para: Durango alcanza primer lugar nacional en seguridad', 'Imagen de fondo del post: Durango alcanza primer lugar nacional en seguridad; registra cero homicidios dolosos en abril', '2025-05-16 21:33:59', 2, 'background'),
(17, '../assets/682803ee60788.webp', 'Imagen ilustrativa para: Presidenta de México dialoga con primer ministro ', 'Imagen ilustrativa del post: Presidenta de México dialoga con primer ministro de Canadá', '2025-05-16 21:35:10', 2, 'ilustrativa'),
(18, '../assets/682803ee62537.jpeg', 'Imagen de fondo para: Presidenta de México dialoga con primer ministro ', 'Imagen de fondo del post: Presidenta de México dialoga con primer ministro de Canadá', '2025-05-16 21:35:10', 2, 'background');

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
(8, 'Trump asegura que no habrá avances sobre la guerra en Ucrania hasta que se reúna con Putin', 'trump-asegura-que-no-habr-avances-sobre-la-guerra-en-ucrania-hasta-que-se-re-na-con-putin', 'El presidente estadounidense Donald Trump, ha declarado que no se realizarán avances en las negociaciones entre Rusia y Ucrania hasta que él se reúna con el presidente ruso Vladímir Putin.', 'El presidente estadounidense, Donald Trump, ha asegurado este jueves 15 de mayo de 2025 que no habrá avances en las potenciales conversaciones en Turquía entre Rusia y Ucrania para encontrar una solución a la guerra hasta que él y su homólogo ruso, Vladímir Putin, se encuentren en persona.\r\n\r\nRusia y Estados Unidos tienen ya preparadas sus delegaciones para participar en Estambul en negociaciones directas con las autoridades ucranianas para buscar una salida negociada a la guerra causada por la invasión rusa de Ucrania, tal y como ha anunciado este jueves el Gobierno turco.\r\n\r\nAnkara no ha confirmado la participación de mediadores turcos en el encuentro ruso-ucraniano, el primero desde 2022, que en teoría debe arrancar esta tarde en Estambul.\r\n\r\nPor su parte, el presidente ucraniano, Volodímir Zelenski, está en Ankara, donde mantiene desde el mediodía un encuentro con su homólogo turco, Recep Tayyip Erdogan, y se está a la espera de que informe sobre la participación ucraniana en el encuentro previsto en Estambul. El Kremlin ya ha confirmado que Putin no tiene planes de viajar a Estambul, pero su posible viaje a Turquía dependerá del resultado de esos contactos.', 10, 9, 10, 2, '2025-05-17 02:46:26', NULL, 'publicado', 0, NULL),
(9, 'Abogan por soluciones africanas a problemas de paz y seguridad', 'abogan-por-soluciones-africanas-a-problemas-de-paz-y-seguridad', 'El presidente de la UA dijo que colaborará mucho más estrechamente con el presidente Yoweri Musseveni en el caso de Sudán.', 'Durante la ceremonia de traspaso de poderes de la Presidencia de la Comisión de la UA, en Etiopía, el jefe de Estado remarcó la necesidad de actuar para lograr el silencio de las armas y que esta cuestión no siga dominando las agendas y debates del continente casi eternamente.\r\n\r\nAl respecto, consideró útil celebrar una conferencia exclusivamente para el análisis de los conflictos en África, cuyo eje central sería la cuestión de la paz como bien obligatorio e inalienable para todos los pueblos, en la búsqueda de crear una sólida arquitectura de paz y seguridad.\r\n\r\n“Aquellos que promueven tensiones y conflictos en nuestro continente deben ser desanimados, responsabilizados y penalizados con duras sanciones por parte de la organización, lo que tendrá graves consecuencias para ellos, las personas y los países”, remarcó en torno al asunto.\r\n\r\nAñadió que la UA debería sentir vergüenza de que instituciones foráneas como la Unión Europea o el Consejo de Seguridad de Naciones Unidas, sean a veces más rigurosas, exigentes y enérgicas en sus posiciones a la hora de abordar los conflictos que se desarrollan en el continente.', 10, 11, 12, 2, '2025-05-17 03:30:27', NULL, 'publicado', 0, NULL),
(10, 'Durango se posiciona entre los tres estados más pacíficos del país, según el Índice de Paz México 2025', 'durango-se-posiciona-entre-los-tres-estados-m-s-pac-ficos-del-pa-s-seg-n-el-ndice-de-paz-m-xico-2025', 'Durango se distingue por su baja incidencia de violencia y se sitúa en el tercer lugar a nivel nacional, superado únicamente por Yucatán y Tlaxcala', 'Durango ha sido reconocido como uno de los tres estados más pacíficos de la República Mexicana, de acuerdo con la reciente edición del Índice de Paz México 2025, elaborado por el Instituto para la Economía y la Paz (IEP), una organización internacional dedicada al análisis de seguridad, paz y bienestar social.\r\n\r\nEl estado se distingue por su baja incidencia de violencia y se sitúa en el tercer lugar a nivel nacional, superado únicamente por Yucatán y Tlaxcala, y por encima de entidades como Chiapas, Nayarit y Coahuila. El índice evalúa a las 32 entidades federativas a través de cinco indicadores fundamentales: homicidios, delitos con violencia, crímenes cometidos con armas de fuego, delitos que ameritan prisión preventiva y miedo a la violencia.', 10, 13, 14, 2, '2025-05-17 03:33:09', NULL, 'publicado', 0, NULL),
(11, 'Durango alcanza primer lugar nacional en seguridad; registra cero homicidios dolosos en abril', 'durango-alcanza-primer-lugar-nacional-en-seguridad-registra-cero-homicidios-dolosos-en-abril', 'Durango alcanza primer lugar nacional en seguridad; registra cero homicidios dolosos en abril.', 'Durango se posicionó como la entidad más segura a nivel nacional al no contabilizar ningún homicidio doloso durante el mes de abril de 2025. Este dato preliminar fue emitido por el Secretariado Ejecutivo del Sistema Nacional de Seguridad Pública (SESNSP) y fue dado a conocer por el gobernador Esteban Villegas Villarreal en un evento de entrega de equipamiento a policías municipales, donde estuvo acompañado por el alcalde Homero Martínez Cabrera.\r\n\r\nEl gobernador Villegas Villarreal enfatizó la importancia de este logro, considerando la ubicación geográfica de Durango, que comparte frontera con seis estados. Subrayó que, a diferencia de entidades peninsulares con accesos limitados, la seguridad en Durango representa un desafío mayor, lo que incrementa el valor de este resultado.', 17, 15, 16, 2, '2025-05-17 03:33:59', '2025-05-16 22:41:06', 'publicado', 0, NULL),
(12, 'Presidenta de México dialoga con primer ministro de Canadá', 'presidenta-de-m-xico-dialoga-con-primer-ministro-de-canad', 'La presidenta Claudia Sheinbaum, y el primer ministro de Canadá, Mark Carney, sostuvieron un diálogo estratégico sobre la importancia del Tratado entre México, Estados Unidos y Canadá (T-MEC).', 'A través de un mensaje en la red social X, el gobierno de esta nación latinoamericana informó sobre la conversación que abordó asuntos prioritarios de la relación bilateral y la continuidad y fortalecimiento del Programa de Trabajadores Agrícolas Temporales.\r\n\r\nLa mandataria también felicitó a Carney por la ratificación de su mandato.\r\n\r\nAmbos países integran junto a Estados Unidos el tratado de libre comercio de Norteamérica, el cual debe comenzar su revisión en el segundo semestre del año, en un contexto marcado por la cuestionada política comercial del mandatario estadounidense, Donald Trump.', 17, 17, 18, 2, '2025-05-17 03:35:10', NULL, 'publicado', 0, NULL);

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
-- Estructura de tabla para la tabla `post_likes`
--

CREATE TABLE `post_likes` (
  `id_like` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `post_likes`
--

INSERT INTO `post_likes` (`id_like`, `id_post`, `id_usuario`, `fecha`) VALUES
(4, 8, 2, '2025-05-16 22:14:00'),
(5, 10, 2, '2025-05-16 22:14:23');

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

--
-- Volcado de datos para la tabla `registro_actividades`
--

INSERT INTO `registro_actividades` (`id_registro`, `id_usuario`, `tipo_actividad`, `descripcion`, `ip_address`, `fecha_actividad`) VALUES
(1, 2, 'desarchivar_post', 'Post desarchivado', '::1', '2025-05-16 23:24:58'),
(2, 2, 'archivar_post', 'Post archivado', '::1', '2025-05-16 23:25:04'),
(3, 2, 'desarchivar_post', 'Post desarchivado', '::1', '2025-05-16 23:42:41'),
(4, 2, 'eliminar_comentario', 'Comentario ID: 6 eliminado del post ID: 8', '::1', '2025-05-17 00:56:21'),
(5, 2, 'eliminar_comentario', 'Comentario ID: 5 eliminado del post ID: 8', '::1', '2025-05-17 01:24:02');

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
(2, 'Christopher Eugenio Nieves Martínez', 'cnieves0@ucol.mx', '$2y$10$A3VAT6BsMOQRrdCltWM69.wnhcvWZFoQzvFwQ1vBfOOT8lzWtuDSG', '2025-05-13 15:39:04', '2025-05-15 17:03:50', 'admin', 1, 'assets/avatars/6826f8454b688.jpg', NULL, NULL, NULL, 0, NULL),
(3, 'Prueba 1', 'prueba1@peaceinprogress.com', '$2y$10$mxknS64S8saF6ew38ZHH9.CDi5FROZCTmPE7CbwD5ElGnAOM72ZDW', '2025-05-15 01:58:45', '2025-05-15 13:57:36', 'editor', 1, NULL, NULL, NULL, NULL, 0, NULL);

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
-- Indices de la tabla `error_log`
--
ALTER TABLE `error_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_usuario` (`usuario_id`);

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
-- Indices de la tabla `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id_like`),
  ADD UNIQUE KEY `uk_post_usuario` (`id_post`,`id_usuario`),
  ADD KEY `idx_likes_post` (`id_post`),
  ADD KEY `idx_likes_usuario` (`id_usuario`);

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
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `error_log`
--
ALTER TABLE `error_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id_like` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `post_metadatos`
--
ALTER TABLE `post_metadatos`
  MODIFY `id_metadato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_actividades`
--
ALTER TABLE `registro_actividades`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tags`
--
ALTER TABLE `tags`
  MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Filtros para la tabla `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `fk_likes_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_likes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

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
