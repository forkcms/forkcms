CREATE TABLE IF NOT EXISTS `tags` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) NOT NULL,
 `name` varchar(255) NOT NULL,
 `number_of_connections` int(11) NOT NULL,
 `url` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `modules_tags` (
 `module` varchar(255) NOT NULL,
 `tag_id` int(11) NOT NULL,
 `other_id` int(11) NOT NULL,
 PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
