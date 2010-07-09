CREATE TABLE IF NOT EXISTS `content_blocks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` varchar(10) collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `text` text collate utf8_unicode_ci,
  `hidden` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `status` enum('active','archived') collate utf8_unicode_ci NOT NULL default 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `locale` (`user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(1, 'nl', 'backend', 'content_blocks', 'lbl', 'Add', 'Inhoudsblok toevoegen', '2010-06-24 14:50:25'),
(1, 'nl', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'bewerk inhoudsblok "%1$s"', '2010-06-24 14:00:48'),
(1, 'nl', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de inhoudsblok "%1$s" wil verwijderen?', '2010-06-24 14:04:30'),
(1, 'nl', 'backend', 'content_blocks', 'msg', 'Added', 'Het inhoudsblok "%1$s" werd toegevoegd.', '2010-06-24 14:26:41'),
(1, 'nl', 'backend', 'content_blocks', 'msg', 'Edited', 'Het inhoudsblok "%1$s" werd opgeslagen.', '2010-06-30 12:10:16'),
(1, 'nl', 'backend', 'content_blocks', 'msg', 'Deleted', 'Het inhoudsblok "%1$s" werd verwijderd.', '2010-06-24 14:31:42');