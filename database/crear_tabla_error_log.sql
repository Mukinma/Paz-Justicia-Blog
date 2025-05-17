CREATE TABLE IF NOT EXISTS `error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mensaje` text NOT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `linea` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 