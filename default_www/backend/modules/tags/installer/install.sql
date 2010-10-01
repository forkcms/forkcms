CREATE TABLE IF NOT EXISTS `tags` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) collate utf8_unicode_ci NOT NULL,
 `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
 `number` int(11) NOT NULL,
 `url` varchar(255) collate utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;