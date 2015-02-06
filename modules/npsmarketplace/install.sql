CREATE TABLE IF NOT EXISTS `PREFIX_seller` (
  `id_seller` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL,
  `id_address` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL UNIQUE,
  `active` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `outer_adds` tinyint(1) NOT NULL,
  `requested` tinyint(1) NOT NULL,
  `request_date` datetime,
  `krs` varchar(16),
  `krs_reg` varchar(1024),
  `commision` DECIMAL(5,2),
  `nip` varchar(14),
  `regon` varchar(14),
  PRIMARY KEY (`id_seller`),
  KEY `id_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_lang` (
  `id_seller` int(10) unsigned NOT NULL,
  `description` text,
  `regulations` text,
  `link_rewrite` varchar(128) NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  KEY `id_seller` (`id_seller`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_product` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  KEY `id_seller` (`id_seller`),
  KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_province` (
  `id_province` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_feature_value` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_province`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_province_lang` (
  `id_lang` int(10) unsigned NOT NULL,
  `id_province` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  KEY `id_province` (`id_province`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_town` (
  `id_town` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_feature_value` int(10) unsigned NOT NULL,
  `id_province` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  `default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_town`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_town_lang` (
  `id_lang` int(10) unsigned NOT NULL,
  `id_town` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  KEY `id_town` (`id_town`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_district` (
  `id_district` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id_district`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_product_attribute_expiry_date` (
  `id_expiry` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product_attribute` int(10) unsigned NULL,
  `id_product` int(10) unsigned NULL,
  `expiry_date` datetime,
  PRIMARY KEY (`id_expiry`),
  KEY `id_product_attribute` (`id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
