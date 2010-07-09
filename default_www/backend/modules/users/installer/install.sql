CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `password` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'will be case-sensitive',
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT 'is this user active?',
  `deleted` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'is the user deleted?',
  `is_god` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The backend users' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) collate utf8_unicode_ci NOT NULL,
  `secret_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_session_id_secret_key` (`session_id`(100),`secret_key`(100))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text collate utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `locale` (`user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(1, 'nl', 'backend', 'users', 'lbl', 'Add', 'gebruiker toevoegen', '2010-06-16 13:40:50'),
(1, 'nl', 'backend', 'users', 'msg', 'HelpStrongPassword', 'Sterke wachtwoorden bestaan uit een combinatie van hoofdletters, kleine letters, cijfers en speciale karakters.', '2010-06-16 13:53:04'),
(1, 'nl', 'backend', 'users', 'msg', 'HelpActive', 'Geef deze account toegang tot het CMS.', '2010-06-16 14:50:29'),
(1, 'nl', 'backend', 'users', 'msg', 'EditUser', 'bewerk gebruiker "%1$s"', '2010-06-17 09:52:17'),
(1, 'nl', 'backend', 'users', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de gebruiker "%1$s" wil verwijderen?', '2010-06-17 11:52:50'),
(1, 'nl', 'backend', 'users', 'err', 'NonExisting', 'Deze gebruiker bestaat niet.', '2010-06-17 12:12:09'),
(1, 'nl', 'backend', 'users', 'msg', 'Edited', 'De instellingen voor "%1$s" werden opgeslagen.', '2010-06-23 14:03:37'),
(1, 'nl', 'backend', 'users', 'msg', 'Added', 'De gebruiker "%1$s" werd toegevoegd.', '2010-06-17 12:34:16'),
(1, 'nl', 'backend', 'users', 'msg', 'Deleted', 'De gebruiker "%1$s" werd verwijderd.', '2010-06-17 12:35:12');