CREATE TABLE IF NOT EXISTS `PREFIX_p24_payment` (
  `id_payment` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` char(100) NOT NULL, 
  `token` char(128) NOT NULL,
  `id_cart` INT UNSIGNED NOT NULL ,
  `amount` INT UNSIGNED NOT NULL,
  `currency_iso` char(4) NOT NULL,
  `timestamp` INT NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_p24_payment_statement` (
  `id_payment_statement` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_payment` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `payment_method` INT UNSIGNED NOT NULL ,
  `statement` char(64) NOT NULL,
  KEY `id_payment` (`id_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_p24_seller_company` (
  `id_p24_seller_company` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_seller` INT UNSIGNED NOT NULL,
  `spid` char(64) NOT NULL,
  `registration_date` datetime,
  `acceptance` tinyint(1) NOT NULL,
  `register_link` text,
  `company_name` char(64) NOT NULL,
  `city` char(64) NOT NULL,
  `street` char(128) NOT NULL,
  `post_code` char(12) NOT NULL,
  `email` char(128) NOT NULL,
  `nip` char(12) NOT NULL,
  `person` char(64) NOT NULL,
  `regon` char(12) NOT NULL,
  `iban` char(64),
  KEY `id_seller` (`id_seller`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_p24_dispatch_history` (
  `id_p24_dispatch_history` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_payment` INT UNSIGNED NOT NULL,
  `sellers_number` INT UNSIGNED NOT NULL,
  `sellers_amount` INT UNSIGNED NOT NULL,
  `merchant_amount` INT UNSIGNED NOT NULL,
  `p24_amount` INT UNSIGNED NOT NULL,
  `total_amount` INT UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date` datetime,
  KEY `id_payment` (`id_payment`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `PREFIX_p24_dispatch_history_detail` (
  `id_p24_dispatch_history_detail` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_p24_dispatch_history` INT UNSIGNED NOT NULL,
  `id_seller` INT UNSIGNED,
  `session_id` char(100) NOT NULL, 
  `spid` char(64) NOT NULL,
  `amount` INT UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL,
  `merchant` tinyint(1) NOT NULL,
  `error` text,
  KEY `id_p24_dispatch_history` (`id_p24_dispatch_history`),
  KEY `id_seller` (`id_seller`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
