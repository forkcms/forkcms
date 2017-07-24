CREATE TABLE IF NOT EXISTS `themes_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the template.',
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The name of the theme.',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is this template active (as in: will it be used).',
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=1 ;
