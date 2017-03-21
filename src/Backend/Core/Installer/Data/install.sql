CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
  `custom` longtext COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `modules` (
 `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'unique module name',
 `installed_on` datetime NOT NULL,
 PRIMARY KEY (`name`),
 KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_extras` (
 `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the extra.',
 `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'The name of the module this extra belongs to.',
 `type` enum('homepage','block','widget') NOT NULL COMMENT 'The type of the block.',
 `label` varchar(255) NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
 `action` varchar(255) default NULL,
 `data` text COMMENT 'A serialized value with the optional parameters',
 `hidden` enum('N','Y') NOT NULL default 'N' COMMENT 'Should the extra be shown in the backend?',
 `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `modules_settings` (
 `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
 `name` varchar(255) NOT NULL COMMENT 'name of the setting',
 `value` text NOT NULL COMMENT 'serialized value',
 PRIMARY KEY (`module`(25),`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_tags` (
 `module` varchar(255) CHARACTER SET utf8 NOT NULL,
 `tag_id` int(11) NOT NULL,
 `other_id` int(11) NOT NULL,
 PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `groups` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(255) NOT NULL,
 `parameters` text COMMENT 'serialized array containing default user module/action rights',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `groups` (`id`, `name`, `parameters`) VALUES
(1, 'admin', NULL) ON DUPLICATE KEY UPDATE id=1;


CREATE TABLE IF NOT EXISTS `groups_rights_actions` (
 `id` int(11) NOT NULL auto_increment,
 `group_id` int(11) NOT NULL,
 `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
 `action` varchar(255) NOT NULL COMMENT 'name of the action',
 `level` double NOT NULL default '1' COMMENT 'unix type levels 1, 3, 5 and 7',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
 `id` int(11) NOT NULL auto_increment,
 `group_id` int(11) NOT NULL,
 `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
 PRIMARY KEY (`id`),
 KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


CREATE  TABLE IF NOT EXISTS `backend_navigation` (
  `id` INT(11) UNSIGNED NOT NULL auto_increment,
  `parent_id` INT(11) NOT NULL ,
  `label` VARCHAR(255) NOT NULL ,
  `url` VARCHAR(255) NULL ,
  `selected_for` TEXT NULL ,
  `sequence` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
 ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
