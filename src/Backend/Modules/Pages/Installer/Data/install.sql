CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL COMMENT 'the real page_id',
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the parent_id for the page ',
  `template_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the template to use',
  `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'language of the content',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'root' COMMENT 'page, header, footer, ...',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `navigation_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'title that will be used in the navigation',
  `navigation_title_overwrite` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'should we override the navigation title',
  `hidden` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(243) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'is this the active, archive or draft version',
  `publish_on` datetime NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'serialized array that may contain type specific parameters',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `allow_move` tinyint(1) NOT NULL DEFAULT '1',
  `allow_children` tinyint(1) NOT NULL DEFAULT '1',
  `allow_edit` tinyint(1) NOT NULL DEFAULT '1',
  `allow_delete` tinyint(1) NOT NULL DEFAULT '1',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `pages_blocks` (
  `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_id` int(11) DEFAULT NULL COMMENT 'The linked extra.',
  `extra_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'rich_text',
  `extra_data` text COLLATE utf8mb4_unicode_ci,
  `html` text COLLATE utf8mb4_unicode_ci COMMENT 'if this block is HTML this field should contain the real HTML.',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `sequence` int(11) NOT NULL,
  KEY `idx_rev_status` (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
