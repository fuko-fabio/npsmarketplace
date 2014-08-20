CREATE TABLE IF NOT EXISTS `PREFIX_p24_payment` (
  `id_payment` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` char(100) NOT NULL,
  `id_cart` INT UNSIGNED NOT NULL ,
  `amount` INT UNSIGNED NOT NULL,
  `currency_iso` char(3) NOT NULL,
  `timestamp` INT NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_p24_payment_statement` (
  `id_payment_statement` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_payment` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `payment_method` INT UNSIGNED NOT NULL ,
  `statement` char(40) NOT NULL,
  KEY `id_payment` (`id_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
