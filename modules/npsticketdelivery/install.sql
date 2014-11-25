CREATE TABLE IF NOT EXISTS `PREFIX_cart_ticket` (
  `id_cart_ticket` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_cart` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_currency` int(10) unsigned NOT NULL,
  `email` varchar(128) NOT NULL,
  `persons` text,
  PRIMARY KEY (`id_cart_ticket`),
  KEY `id_cart` (`id_cart`),
  KEY `id_customer` (`id_customer`),
  KEY `id_currency` (`id_currency`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ticket` (
  `id_ticket` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_cart_ticket` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `price` decimal(20,6) NOT NULL,
  `tax` int(10) unsigned,
  `name` varchar(64) NOT NULL,
  `address` varchar(256) NOT NULL,
  `town` varchar(64),
  `district` varchar(128),
  `person` varchar(128),
  `type` tinyint(1) NOT NULL,
  `entries` int(10) unsigned,
  `date` datetime,
  `from` datetime,
  `to` datetime,
  `generated` datetime,
  PRIMARY KEY (`id_ticket`),
  KEY `id_cart_ticket` (`id_cart_ticket`),
  KEY `id_seller` (`id_seller`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
