CREATE TABLE IF NOT EXISTS `content_blocks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Default.html.twig',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `hidden` enum('N','Y') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
  `status` enum('active','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT '(DC2Type:content_blocks_status)',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
