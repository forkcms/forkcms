CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) NOT NULL,
  `keywords_overwrite` enum('N','Y') NOT NULL DEFAULT 'N',
  `description` varchar(255) NOT NULL,
  `description_overwrite` enum('N','Y') NOT NULL DEFAULT 'N',
  `title` varchar(255) NOT NULL,
  `title_overwrite` enum('N','Y') NOT NULL DEFAULT 'N',
  `url` varchar(255) NOT NULL,
  `url_overwrite` enum('N','Y') NOT NULL DEFAULT 'N',
  `custom` text CHARACTER SET utf8 COMMENT 'used for custom meta-information',
  `data` text COMMENT 'used for extra meta-information',
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Meta-information' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `modules` (
 `name` varchar(255) NOT NULL COMMENT 'unique module name',
 `installed_on` datetime NOT NULL,
 PRIMARY KEY (`name`),
 KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_settings` (
 `module` varchar(255) NOT NULL COMMENT 'name of the module',
 `name` varchar(255) NOT NULL COMMENT 'name of the setting',
 `value` text NOT NULL COMMENT 'serialized value',
 PRIMARY KEY (`module`(25),`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_tags` (
 `module` varchar(255) NOT NULL,
 `tag_id` int(11) NOT NULL,
 `other_id` int(11) NOT NULL,
 PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `timezones`;
CREATE TABLE IF NOT EXISTS `timezones` (
 `id` int(11) NOT NULL auto_increment,
 `timezone` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `groups` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(255) NOT NULL,
 `parameters` text COMMENT 'serialized array containing default user module/action rights',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `groups` (`id`, `name`, `parameters`) VALUES
(1, 'admin', NULL) ON DUPLICATE KEY UPDATE id=1;


CREATE TABLE IF NOT EXISTS `groups_rights_actions` (
 `id` int(11) NOT NULL auto_increment,
 `group_id` int(11) NOT NULL,
 `module` varchar(255) NOT NULL COMMENT 'name of the module',
 `action` varchar(255) NOT NULL COMMENT 'name of the action',
 `level` double NOT NULL default '1' COMMENT 'unix type levels 1, 3, 5 and 7',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
 `id` int(11) NOT NULL auto_increment,
 `group_id` int(11) NOT NULL,
 `module` varchar(255) NOT NULL COMMENT 'name of the module',
 PRIMARY KEY (`id`),
 KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE  TABLE IF NOT EXISTS `backend_navigation` (
  `id` INT(11) UNSIGNED NOT NULL auto_increment,
  `parent_id` INT(11) NOT NULL ,
  `label` VARCHAR(255) NOT NULL ,
  `url` VARCHAR(255) NULL ,
  `selected_for` TEXT NULL ,
  `sequence` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
 ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `hooks_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `callback` text NOT NULL,
  `data` text ,
  `status` enum('busy','error','queued') NOT NULL DEFAULT 'queued',
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `hooks_subscriptions` (
  `event_module` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `callback` text NOT NULL,
  `created_on` datetime NOT NULL,
  UNIQUE KEY `event_module` (`event_module`(100),`event_name`(100),`module`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
