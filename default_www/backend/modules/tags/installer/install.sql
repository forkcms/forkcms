CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `locale` (`user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(1, 'nl', 'backend', 'tags', 'msg', 'NoItems', 'Er zijn geen tags.', '2010-06-23 15:42:34'),
(1, 'nl', 'backend', 'tags', 'msg', 'EditTag', 'bewerk tag "%1$s"', '2010-06-17 13:12:15'),
(1, 'nl', 'backend', 'tags', 'err', 'NonExisting', 'Deze tag bestaat niet.', '2010-06-17 13:14:17'),
(1, 'nl', 'backend', 'tags', 'msg', 'Edited', 'De tag "%1$s" werd opgeslagen.', '2010-06-23 14:03:50'),
(1, 'nl', 'backend', 'tags', 'err', 'NoSelection', 'Er waren geen tags geselecteerd.', '2010-06-23 15:45:03'),
(1, 'nl', 'backend', 'tags', 'msg', 'Deleted', 'De geselecteerde tag(s) werd(en) verwijderd.', '2010-06-22 12:09:01');