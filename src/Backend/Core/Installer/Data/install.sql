CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `custom` longtext COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `seo_follow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:seo_follow)',
  `seo_index` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:seo_index)',
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'unique module name',
  `installed_on` datetime NOT NULL,
  PRIMARY KEY (`name`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `modules_settings` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`module`(25),`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` text COLLATE utf8mb4_unicode_ci COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `groups` (`id`, `name`, `parameters`) VALUES
(1, 'admin', NULL) ON DUPLICATE KEY UPDATE id=1;


CREATE TABLE IF NOT EXISTS `groups_rights_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL DEFAULT '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


CREATE  TABLE IF NOT EXISTS `backend_navigation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_for` text COLLATE utf8mb4_unicode_ci,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
