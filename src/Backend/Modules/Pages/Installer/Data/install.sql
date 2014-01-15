CREATE TABLE IF NOT EXISTS `pages` (
 `id` int(11) NOT NULL COMMENT 'the real page_id',
 `revision_id` int(11) NOT NULL auto_increment,
 `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
 `parent_id` int(11) NOT NULL default '0' COMMENT 'the parent_id for the page ',
 `template_id` int(11) NOT NULL default '0' COMMENT 'the template to use',
 `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
 `language` varchar(5) NOT NULL COMMENT 'language of the content',
 `type` enum('home','root','page','meta','footer') NOT NULL default 'root' COMMENT 'page, header, footer, ...',
 `title` varchar(255) NOT NULL,
 `navigation_title` varchar(255) NOT NULL COMMENT 'title that will be used in the navigation',
 `navigation_title_overwrite` enum('N','Y') NOT NULL default 'N' COMMENT 'should we override the navigation title',
 `hidden` enum('N','Y') NOT NULL default 'N' COMMENT 'is the page hidden?',
 `status` enum('active','archive','draft') NOT NULL default 'active' COMMENT 'is this the active, archive or draft version',
 `publish_on` datetime NOT NULL,
 `data` text COMMENT 'serialized array that may contain type specific parameters',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 `allow_move` enum('N','Y') NOT NULL default 'Y',
 `allow_children` enum('N','Y') NOT NULL default 'Y',
 `allow_edit` enum('N','Y') NOT NULL default 'Y',
 `allow_delete` enum('N','Y') NOT NULL default 'Y',
 `sequence` int(11) NOT NULL,
 PRIMARY KEY (`revision_id`),
 KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `pages_blocks` (
 `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
 `position` varchar(255) NOT NULL,
 `extra_id` int(11) default NULL COMMENT 'The linked extra.',
 `html` text COMMENT 'if this block is HTML this field should contain the real HTML.',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 `visible` enum('N','Y') NOT NULL,
 `sequence` int(11) NOT NULL,
 KEY `idx_rev_status` (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
