CREATE TABLE IF NOT EXISTS `themes_templates` (
 `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the template.',
 `theme` varchar(255) default NULL COMMENT 'The name of the theme.',
 `label` varchar(255) NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
 `path` varchar(255) NOT NULL COMMENT 'Filename for the template.',
 `active` VARCHAR(1) NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
 `data` text COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=1 ;
