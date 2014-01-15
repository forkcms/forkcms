CREATE TABLE IF NOT EXISTS `groups_settings` (
 `group_id` int(11) NOT NULL,
 `name` varchar(255) NOT NULL COMMENT 'name of the setting',
 `value` text NOT NULL COMMENT 'serialized value',
 PRIMARY KEY (`group_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;