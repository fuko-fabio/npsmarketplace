CREATE TABLE IF NOT EXISTS `PREFIX_seller_cart_rule` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_cart_rule` int(10) unsigned NOT NULL,
  KEY `id_seller` (`id_seller`),
  KEY `id_cart_rule` (`id_cart_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

