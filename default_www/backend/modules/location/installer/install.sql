CREATE TABLE IF NOT EXISTS `location` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) NOT NULL,
 `extra_id` int(11) NOT NULL,
 `title` varchar(255) NOT NULL,
 `text` text,
 `street` varchar(255) NOT NULL,
 `number` varchar(255) NOT NULL,
 `zip` varchar(255) NOT NULL,
 `city` varchar(255) NOT NULL,
 `country` varchar(255) NOT NULL,
 `lat` float default NULL,
 `lng` float default NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;