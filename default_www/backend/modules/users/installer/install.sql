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