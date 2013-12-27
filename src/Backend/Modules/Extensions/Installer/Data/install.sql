CREATE TABLE IF NOT EXISTS `modules_extras` (
 `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the extra.',
 `module` varchar(255) NOT NULL COMMENT 'The name of the module this extra belongs to.',
 `type` enum('homepage','block','widget') NOT NULL COMMENT 'The type of the block.',
 `label` varchar(255) NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
 `action` varchar(255) default NULL,
 `data` text COMMENT 'A serialized value with the optional parameters',
 `hidden` enum('N','Y') NOT NULL default 'N' COMMENT 'Should the extra be shown in the backend?',
 `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `themes_templates` (
 `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the template.',
 `theme` varchar(255) default NULL COMMENT 'The name of the theme.',
 `label` varchar(255) NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
 `path` varchar(255) NOT NULL COMMENT 'Filename for the template.',
 `active` enum('N','Y') NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
 `data` text COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=1 ;