CREATE TABLE IF NOT EXISTS `locale` (
 `id` int(11) NOT NULL auto_increment,
 `user_id` int(11) NOT NULL,
 `language` varchar(5) collate utf8_unicode_ci NOT NULL,
 `application` varchar(255) collate utf8_unicode_ci NOT NULL,
 `module` varchar(255) collate utf8_unicode_ci NOT NULL,
 `type` enum('act','err','lbl','msg') collate utf8_unicode_ci NOT NULL default 'lbl',
 `name` varchar(255) collate utf8_unicode_ci NOT NULL,
 `value` text collate utf8_unicode_ci,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;