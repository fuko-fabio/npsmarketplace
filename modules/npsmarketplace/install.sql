CREATE TABLE IF NOT EXISTS `PREFIX_seller` (
  `id_seller` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `requested` tinyint(1) NOT NULL,
  `request_date` datetime,
  `phone` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `commision` int(10),
  `nip` int(14),
  `regon` int(14),
  PRIMARY KEY (`id_seller`),
  KEY `id_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_lang` (
  `id_seller` int(10) unsigned NOT NULL,
  `company_name` varchar(64) NOT NULL,
  `company_description` text,
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

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment` (
  `id_seller_comment` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `id_guest` int(10) unsigned NULL,
  `title` varchar(64) NULL,
  `content` text NOT NULL,
  `customer_name` varchar(64) NULL,
  `grade` float unsigned NOT NULL,
  `validate` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_seller_comment`),
  KEY `id_seller` (`id_seller`),
  KEY `id_customer` (`id_customer`),
  KEY `id_guest` (`id_guest`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment_criterion` (
  `id_seller_comment_criterion` int(10) unsigned NOT NULL auto_increment,
  `id_seller_comment_criterion_type` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_seller_comment_criterion`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment_criterion_seller` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_seller_comment_criterion` int(10) unsigned NOT NULL,
  PRIMARY KEY(`id_seller`, `id_seller_comment_criterion`),
  KEY `id_seller_comment_criterion` (`id_seller_comment_criterion`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment_criterion_lang` (
  `id_seller_comment_criterion` INT(11) UNSIGNED NOT NULL ,
  `id_lang` INT(11) UNSIGNED NOT NULL ,
  `name` VARCHAR(64) NOT NULL ,
  PRIMARY KEY ( `id_seller_comment_criterion` , `id_lang` )
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment_grade` (
  `id_seller_comment` int(10) unsigned NOT NULL,
  `id_seller_comment_criterion` int(10) unsigned NOT NULL,
  `grade` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_seller_comment`, `id_seller_comment_criterion`),
  KEY `id_seller_comment_criterion` (`id_seller_comment_criterion`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment_usefulness` (
  `id_seller_comment` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  `usefulness` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id_seller_comment`, `id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_seller_comment_report` (
  `id_seller_comment` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_seller_comment`, `id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `PREFIX_seller_comment_criterion` VALUES ('1', '1', '1');

INSERT IGNORE INTO `PREFIX_seller_comment_criterion_lang` (`id_seller_comment_criterion`, `id_lang`, `name`)
  (
    SELECT '1', l.`id_lang`, 'Quality'
    FROM `PREFIX_lang` l
  );

