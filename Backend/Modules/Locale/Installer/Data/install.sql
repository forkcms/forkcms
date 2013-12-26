CREATE TABLE IF NOT EXISTS `locale` (
 `id` int(11) NOT NULL auto_increment,
 `user_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `application` varchar(255) NOT NULL,
 `module` varchar(255) NOT NULL,
 `type` enum('act','err','lbl','msg') NOT NULL default 'lbl',
 `name` varchar(255) NOT NULL,
 `value` text,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `language` (`language`,`application`(20),`module`(20),`type`,`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;