CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL COMMENT 'the real page_id',
  `revision_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
  `parent_id` int(11) NOT NULL default '0' COMMENT 'the parent_id for the page ',
  `template_id` int(11) NOT NULL default '0' COMMENT 'the template to use',
  `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
  `language` varchar(5) collate utf8_unicode_ci NOT NULL COMMENT 'language of the content',
  `type` enum('home','root','page','meta','footer','external_alias','internal_alias') collate utf8_unicode_ci NOT NULL default 'root' COMMENT 'page, header, footer, ...',
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `navigation_title` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'title that will be used in the navigation',
  `navigation_title_overwrite` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'should we override the navigation title',
  `hidden` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'is the page hidden?',
  `status` enum('active','archive','draft') collate utf8_unicode_ci NOT NULL default 'active' COMMENT 'is this the active, archive or draft version',
  `publish_on` datetime NOT NULL,
  `data` text collate utf8_unicode_ci COMMENT 'serialized array that may contain type specific parameters',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `allow_move` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `allow_children` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `allow_edit` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `allow_delete` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `no_follow` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `sequence` int(11) NOT NULL,
  `has_extra` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  `extra_ids` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `pages_blocks` (
  `id` int(11) NOT NULL COMMENT 'An ID that will be the same over the revisions.\n',
  `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
  `extra_id` int(11) default NULL COMMENT 'The linked extra.',
  `html` text collate utf8_unicode_ci COMMENT 'if this block is HTML this field should contain the real HTML.',
  `status` enum('active','archive','draft') collate utf8_unicode_ci NOT NULL default 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  KEY `idx_rev_status` (`revision_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `pages_extras` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the extra.',
  `module` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` enum('homepage','block','widget') collate utf8_unicode_ci NOT NULL COMMENT 'The type of the block.',
  `label` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `action` varchar(255) collate utf8_unicode_ci default NULL,
  `data` text collate utf8_unicode_ci COMMENT 'A serialized value with the optional parameters',
  `hidden` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'Should the extra be shown in the backend?',
  `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `pages_templates` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the template.',
  `label` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  `num_blocks` int(11) NOT NULL default '1' COMMENT 'The number of blocks used in the template.',
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
  `data` text collate utf8_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=1 ;


INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(370, 1, 'nl', 'backend', 'pages', 'err', 'CantBeMoved', 'Pagina kan niet verplaatst worden.', '2010-06-30 13:19:25'),
(353, 1, 'nl', 'backend', 'pages', 'err', 'DeleteTemplate', 'Je kan deze template niet verwijderen.', '2010-06-30 09:03:02'),
(290, 1, 'nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen', '2010-06-25 09:21:29'),
(314, 1, 'nl', 'backend', 'pages', 'lbl', 'Footer', 'navigatie onderaan', '2010-06-30 12:25:15'),
(315, 1, 'nl', 'backend', 'pages', 'lbl', 'MainNavigation', 'Hoofdnavigatie', '2010-06-25 09:23:29'),
(313, 1, 'nl', 'backend', 'pages', 'lbl', 'Meta', 'metanavigatie', '2010-06-30 12:27:31'),
(316, 1, 'nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina''s', '2010-06-25 09:25:01'),
(344, 1, 'nl', 'backend', 'pages', 'msg', 'Added', 'De pagina "%1$s" werd toegevoegd.', '2010-06-29 14:26:06'),
(350, 1, 'nl', 'backend', 'pages', 'msg', 'AddedTemplate', 'De template "%1$s" werd toegevoegd.', '2010-06-30 07:40:37'),
(363, 1, 'nl', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de pagina "%1$s" wil verwijderen?', '2010-06-30 12:47:19'),
(352, 1, 'nl', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Ben je zeker dat je de template "%1$s" wil verwijderen?', '2010-06-30 08:20:20'),
(342, 1, 'nl', 'backend', 'pages', 'msg', 'Deleted', 'De pagina "%1$s" werd verwijderd.', '2010-06-29 14:24:09'),
(354, 1, 'nl', 'backend', 'pages', 'msg', 'DeletedTemplate', 'De template "%1$s" werd verwijderd.', '2010-06-30 09:04:02'),
(343, 1, 'nl', 'backend', 'pages', 'msg', 'Edited', 'De pagina "%1$s" werd opgeslagen.', '2010-06-30 12:10:03'),
(332, 1, 'nl', 'backend', 'pages', 'msg', 'HelpBlockContent', 'Welk soort inhoud wil je hier tonen?', '2010-06-30 12:22:17'),
(359, 1, 'nl', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigatie die (boven het hoofdmenu) op elke pagina staat.', '2010-06-30 13:29:05'),
(324, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'De titel die in het menu getoond wordt.', '2010-06-30 12:16:51'),
(325, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Zorgt ervoor dat deze pagina de interne PageRank niet beïnvloedt.', '2010-06-25 11:21:12'),
(322, 1, 'nl', 'backend', 'pages', 'msg', 'HelpPageTitle', 'De titel die in het browservenster staat (<code>&lt;title&gt;</code>).', '2010-06-30 12:16:02'),
(351, 1, 'nl', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'e.g. [0,1],[2,none]', '2010-06-30 07:50:46'),
(383, 1, 'nl', 'backend', 'pages', 'msg', 'MetaNavigation', 'Metanavigatie inschakelen voor deze website.', '2010-06-30 14:28:09'),
(375, 1, 'nl', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'Er werd reeds een module gekoppeld aan deze pagina.', '2010-06-30 13:32:44'),
(362, 1, 'nl', 'backend', 'pages', 'msg', 'PageIsMoved', 'De pagina "%1$s" werd verplaatst.', '2010-06-30 12:45:33'),
(330, 1, 'nl', 'backend', 'pages', 'msg', 'RichText', 'Editor', '2010-06-25 13:09:49');

