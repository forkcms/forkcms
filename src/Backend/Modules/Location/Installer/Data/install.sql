CREATE TABLE IF NOT EXISTS `location` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) NOT NULL,
 `extra_id` int(11) NOT NULL,
 `title` varchar(255) NOT NULL,
 `address` text NOT NULL,
 `country` varchar(255) NOT NULL,
 `lat` float default NULL,
 `lng` float default NULL,
 `show_overview` enum('N','Y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `location_settings` (
  `map_id` int(11) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`map_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
