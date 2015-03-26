CREATE TABLE IF NOT EXISTS `PREFIX_seller_cart_rule` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_cart_rule` int(10) unsigned NOT NULL,
  KEY `id_seller` (`id_seller`),
  KEY `id_cart_rule` (`id_cart_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_order_seller_cart_rule` (
  `id_order_seller_cart_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_seller` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `id_order_cart_rule` int(10) unsigned NOT NULL,
  KEY `id_seller` (`id_seller`),
  KEY `id_product` (`id_product`),
  KEY `id_order_cart_rule` (`id_order_cart_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
