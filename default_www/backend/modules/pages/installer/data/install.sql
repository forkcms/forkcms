CREATE TABLE IF NOT EXISTS `pages` (
 `id` int(11) NOT NULL COMMENT 'the real page_id',
 `revision_id` int(11) NOT NULL auto_increment,
 `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
 `parent_id` int(11) NOT NULL default '0' COMMENT 'the parent_id for the page ',
 `template_id` int(11) NOT NULL default '0' COMMENT 'the template to use',
 `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
 `language` varchar(5) NOT NULL COMMENT 'language of the content',
 `type` enum('home','root','page','meta','footer','external_alias','internal_alias') NOT NULL default 'root' COMMENT 'page, header, footer, ...',
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
 `no_follow` enum('N','Y') NOT NULL default 'N',
 `sequence` int(11) NOT NULL,
 `has_extra` enum('N','Y') NOT NULL,
 `extra_ids` varchar(255) default NULL,
 PRIMARY KEY (`revision_id`),
 KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `pages_blocks` (
 `id` int(11) NOT NULL COMMENT 'An ID that will be the same over the revisions.\n',
 `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
 `extra_id` int(11) default NULL COMMENT 'The linked extra.',
 `html` text COMMENT 'if this block is HTML this field should contain the real HTML.',
 `status` enum('active','archive','draft') NOT NULL default 'active',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 KEY `idx_rev_status` (`revision_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `pages_extras` (
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


CREATE TABLE IF NOT EXISTS `pages_templates` (
 `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the template.',
 `label` varchar(255) NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
 `path` varchar(255) NOT NULL COMMENT 'Filename for the template.',
 `num_blocks` int(11) NOT NULL default '1' COMMENT 'The number of blocks used in the template.',
 `active` enum('N','Y') NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
 `data` text COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=1 ;