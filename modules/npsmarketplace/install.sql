CREATE TABLE IF NOT EXISTS `PREFIX_seller` (
  `id_seller` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `requested` tinyint(1) NOT NULL,
  `regulations_active` tinyint(1) NOT NULL,
  `request_date` datetime,
  `phone` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `commision` int(10),
  `nip` varchar(14),
  `regon` varchar(14),
  PRIMARY KEY (`id_seller`),
  KEY `id_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_lang` (
  `id_seller` int(10) unsigned NOT NULL,
  `company_name` varchar(64) NOT NULL,
  `company_description` text,
  `regulations` text,
  `name` varchar(128) NOT NULL,
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

