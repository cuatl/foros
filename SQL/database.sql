#
# tabla foros
#
CREATE TABLE `foros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `master` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '1',
  `titulo` varchar(64) DEFAULT NULL,
  `descripcion` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `master` (`master`),
  KEY `orden` (`orden`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='Tabla de foros'

#
# tabla forotes
#
CREATE TABLE `forotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `nombre` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='Categorías principales de foros'

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='Posts'

#
# tabla postslog
#
CREATE TABLE `postslog` (
  `postid` int(11) NOT NULL,
  `me` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  UNIQUE KEY `postid` (`postid`,`me`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4

#
# tabla users
#
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `socialid` varchar(64) NOT NULL DEFAULT '',
  `alta` date DEFAULT NULL,
  `nombre` varchar(64) DEFAULT NULL,
  `apellido` varchar(64) DEFAULT NULL,
  `correo` varchar(64) DEFAULT NULL,
  `genero` varchar(16) DEFAULT NULL,
  `alias` varchar(16) DEFAULT NULL,
  `avatar` varchar(16) DEFAULT NULL,
  `seen` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `tipo` enum('FB','GO') DEFAULT 'FB',
  `perfil` varchar(512) DEFAULT NULL,
  `vinculada` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`socialid`),
  KEY `tipo` (`tipo`),
  KEY `vinculada` (`vinculada`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4

