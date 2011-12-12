CREATE TABLE `meta_sitemap` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) DEFAULT NULL,
  `language` varchar(5) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `priority` decimal(10,2) NOT NULL,
  `change_frequency` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'monthly',
  `visible` enum('N','Y') NOT NULL DEFAULT 'Y',
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sitemap-information' AUTO_INCREMENT=1 ;