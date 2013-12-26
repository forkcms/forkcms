CREATE TABLE IF NOT EXISTS `content_blocks` (
 `id` int(11) NOT NULL,
 `revision_id` int(11) NOT NULL auto_increment,
 `user_id` int(11) NOT NULL,
 `extra_id` int(11) NOT NULL,
 `template` varchar(255) NOT NULL default 'default.tpl',
 `language` varchar(10) NOT NULL,
 `title` varchar(255) NOT NULL,
 `text` text,
 `hidden` enum('N','Y') NOT NULL default 'N',
 `status` enum('active','archived') NOT NULL default 'active',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
