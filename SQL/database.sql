#
# tabla foros
#
CREATE TABLE `foros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `master` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '1',
  `titulo` varchar(64) DEFAULT NULL,
  `descripcion` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `master` (`master`),
  KEY `orden` (`orden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='Tabla de foros'

#
# tabla forotes
#
CREATE TABLE `forotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='Categorías principales de foros'

#
# tabla posts
#
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `padre` int(11) NOT NULL DEFAULT '0',
  `me` int(11) NOT NULL,
  `foro` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `titulo` varchar(64) DEFAULT NULL,
  `contenido` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `padre` (`padre`),
  KEY `me` (`me`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='Posts'

