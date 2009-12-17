-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 17, 2009 at 05:37 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `forkng`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `emails`
-- 

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(11) NOT NULL auto_increment,
  `to_email` varchar(255) NOT NULL,
  `to_name` varchar(255) default NULL,
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) default NULL,
  `reply_to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `HTML` text NOT NULL,
  `plain_text` text NOT NULL,
  `date_to_send` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `emails`
-- 

INSERT INTO `emails` VALUES (1, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '2009-11-20 14:49:12');
INSERT INTO `emails` VALUES (2, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (3, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (4, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (5, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (6, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (7, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (8, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (9, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (10, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (11, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (12, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (13, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (14, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (15, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (16, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');
INSERT INTO `emails` VALUES (17, 'tijs@netlash.com', '', 'no-reply@fork-cms.be', 'Fork CMS', 'no-reply@fork-cms.be', '{$msgAuthenticationResetyourpassword}', 'U kan uw wachtwoord resetten door op de link te klikken...', 'U kan uw wachtwoord resetten door op de link te klikken...', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `parameters` text COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `groups`
-- 

INSERT INTO `groups` VALUES (1, 'admin', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `groups_rights_actions`
-- 

DROP TABLE IF EXISTS `groups_rights_actions`;
CREATE TABLE IF NOT EXISTS `groups_rights_actions` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL COMMENT 'name of the module',
  `action` varchar(255) NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL default '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- 
-- Dumping data for table `groups_rights_actions`
-- 

INSERT INTO `groups_rights_actions` VALUES (1, 1, 'dashboard', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (3, 1, 'users', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (4, 1, 'users', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (5, 1, 'users', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES (6, 1, 'users', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES (8, 1, 'users', 'groups', 7);
INSERT INTO `groups_rights_actions` VALUES (9, 1, 'users', 'edit_group', 7);
INSERT INTO `groups_rights_actions` VALUES (10, 1, 'users', 'add_group', 7);
INSERT INTO `groups_rights_actions` VALUES (16, 1, 'pages', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (17, 1, 'pages', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES (18, 1, 'snippets', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (19, 1, 'snippets', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES (20, 1, 'pages', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (21, 1, 'snippets', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (22, 1, 'settings', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (24, 1, 'blog', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (25, 1, 'snippets', 'delete', 7);

-- --------------------------------------------------------

-- 
-- Table structure for table `groups_rights_modules`
-- 

DROP TABLE IF EXISTS `groups_rights_modules`;
CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL COMMENT 'name of the module',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `groups_rights_modules`
-- 

INSERT INTO `groups_rights_modules` VALUES (1, 1, 'dashboard');
INSERT INTO `groups_rights_modules` VALUES (3, 1, 'users');
INSERT INTO `groups_rights_modules` VALUES (6, 1, 'pages');
INSERT INTO `groups_rights_modules` VALUES (7, 1, 'snippets');
INSERT INTO `groups_rights_modules` VALUES (8, 1, 'settings');
INSERT INTO `groups_rights_modules` VALUES (9, 1, 'blog');

-- --------------------------------------------------------

-- 
-- Table structure for table `languages_labels`
-- 

DROP TABLE IF EXISTS `languages_labels`;
CREATE TABLE IF NOT EXISTS `languages_labels` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) NOT NULL,
  `application` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `type` enum('act','err','lbl','msg') NOT NULL default 'lbl',
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `languages_labels`
-- 

INSERT INTO `languages_labels` VALUES (1, 'nl', 'backend', 'core', 'lbl', 'Edit', 'bewerken');
INSERT INTO `languages_labels` VALUES (2, 'nl', 'backend', 'core', 'lbl', 'Name', 'naam');
INSERT INTO `languages_labels` VALUES (3, 'nl', 'backend', 'core', 'lbl', 'Pages', 'pagina''s');
INSERT INTO `languages_labels` VALUES (4, 'en', 'backend', 'core', 'lbl', 'Surname', 'surname');

-- --------------------------------------------------------

-- 
-- Table structure for table `meta`
-- 

DROP TABLE IF EXISTS `meta`;
CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL auto_increment,
  `keywords` varchar(255) NOT NULL,
  `keywords_overwrite` enum('Y','N') NOT NULL default 'N',
  `description` varchar(255) NOT NULL,
  `description_overwrite` enum('Y','N') NOT NULL default 'N',
  `title` varchar(255) NOT NULL,
  `title_overwrite` enum('Y','N') NOT NULL default 'N',
  `url` varchar(255) NOT NULL,
  `url_overwrite` enum('Y','N') NOT NULL default 'N',
  `custom` text COMMENT 'used for custom meta-information',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Meta-information' AUTO_INCREMENT=19 ;

-- 
-- Dumping data for table `meta`
-- 

INSERT INTO `meta` VALUES (1, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (2, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (3, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (4, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (5, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (6, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (7, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (8, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (9, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (10, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (11, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (12, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (13, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (14, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (15, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (16, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (17, 'meta_keywords', 'Y', 'meta_description', 'Y', 'page_title', 'Y', 'url', 'Y', 'meta_custom');
INSERT INTO `meta` VALUES (18, 'Test', 'N', 'Test', 'N', 'Test', 'N', 'test', 'N', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `modules`
-- 

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) NOT NULL COMMENT 'unique module name',
  `description` text,
  `active` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `modules`
-- 

INSERT INTO `modules` VALUES ('blog', NULL, 'N');
INSERT INTO `modules` VALUES ('pages', 'Manage the pages for this website.', 'Y');
INSERT INTO `modules` VALUES ('settings', NULL, 'Y');
INSERT INTO `modules` VALUES ('snippets', NULL, 'Y');
INSERT INTO `modules` VALUES ('statistics', NULL, 'Y');

-- --------------------------------------------------------

-- 
-- Table structure for table `modules_settings`
-- 

DROP TABLE IF EXISTS `modules_settings`;
CREATE TABLE IF NOT EXISTS `modules_settings` (
  `module` varchar(255) NOT NULL COMMENT 'name of the module',
  `name` varchar(255) NOT NULL COMMENT 'name of the setting',
  `value` text NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`module`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `modules_settings`
-- 

INSERT INTO `modules_settings` VALUES ('', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('core', 'core_akismet_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'core_google_maps_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'default_template', 'i:1;');
INSERT INTO `modules_settings` VALUES ('core', 'email_nl', 's:18:"forkng@verkoyen.eu";');
INSERT INTO `modules_settings` VALUES ('core', 'fork_api_private_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'fork_api_public_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'site_domains', 'a:1:{i:0;s:0:"";}');
INSERT INTO `modules_settings` VALUES ('core', 'site_title_fr', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('core', 'site_title_nl', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('core', 'site_wide_html', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_password', 's:8:"Jishaik6";');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_port', 'i:587;');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_server', 's:16:"mail.fork-cms.be";');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_username', 's:16:"bugs@fork-cms.be";');
INSERT INTO `modules_settings` VALUES ('core', 'template_max_blocks', 'i:5;');
INSERT INTO `modules_settings` VALUES ('core', 'website_title_nl', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('email_nl', 'forkng@verkoyen.eu', 'N;');

-- --------------------------------------------------------

-- 
-- Table structure for table `modules_tags`
-- 

DROP TABLE IF EXISTS `modules_tags`;
CREATE TABLE IF NOT EXISTS `modules_tags` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(255) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `modules_tags`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pages`
-- 

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL COMMENT 'the real page_id',
  `revision_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
  `parent_id` int(11) NOT NULL default '0' COMMENT 'the parent_id for the page ',
  `template_id` int(11) NOT NULL default '0' COMMENT 'the template to use',
  `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
  `language` varchar(5) NOT NULL COMMENT 'language of the content',
  `type` enum('root','page','meta','footer','external_alias','internal_alias') NOT NULL default 'page' COMMENT 'page, header, footer, ...',
  `title` varchar(255) NOT NULL,
  `navigation_title` varchar(255) NOT NULL COMMENT 'title that will be used in the navigation',
  `navigation_title_overwrite` enum('Y','N') NOT NULL default 'N' COMMENT 'should we override the navigation title',
  `hidden` enum('Y','N') NOT NULL default 'N' COMMENT 'is the page hidden?',
  `status` enum('active','archive','draft') NOT NULL default 'active' COMMENT 'is this the active, archive or draft version',
  `publish_on` datetime NOT NULL,
  `data` text COMMENT 'serialized array that may contains type specific parameters',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `allow_move` enum('Y','N') NOT NULL default 'Y',
  `allow_children` enum('Y','N') NOT NULL default 'Y',
  `allow_content` enum('Y','N') NOT NULL default 'Y',
  `allow_edit` enum('Y','N') NOT NULL default 'Y',
  `allow_delete` enum('Y','N') NOT NULL default 'Y',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1338 ;

-- 
-- Dumping data for table `pages`
-- 

INSERT INTO `pages` VALUES (2, 1, 1, 1, 1, 1, 'nl', 'page', 'title', 'navigation_title', 'Y', 'N', 'active', '2009-10-21 16:08:28', NULL, '2009-10-21 16:08:28', '2009-10-21 16:08:28', 'Y', 'Y', 'Y', 'Y', 'Y', 1);
INSERT INTO `pages` VALUES (1, 17, 1, 0, 1, 17, 'nl', 'root', 'title', 'navigation_title', 'Y', 'N', 'active', '2009-10-21 16:56:58', NULL, '2009-10-21 16:08:28', '2009-10-21 16:56:58', 'Y', 'Y', 'Y', 'Y', 'Y', 1);
INSERT INTO `pages` VALUES (3, 1337, 1, 1, 1, 17, 'nl', 'page', 'you suck', 'sucker', 'Y', 'N', 'active', '2009-10-21 16:56:58', NULL, '2009-10-21 16:08:28', '2009-10-21 16:56:58', 'Y', 'Y', 'Y', 'Y', 'Y', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `pages_blocks`
-- 

DROP TABLE IF EXISTS `pages_blocks`;
CREATE TABLE IF NOT EXISTS `pages_blocks` (
  `id` int(11) NOT NULL COMMENT 'An ID that will be the same over the revisions.\n',
  `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
  `extra_id` int(11) default NULL COMMENT 'The linked extra.',
  `HTML` text COMMENT 'if this block is HTML this field should contain the real HTML.',
  `status` enum('active','archive','draft') NOT NULL default 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pages_blocks`
-- 

INSERT INTO `pages_blocks` VALUES (1, 1, NULL, '', 'archive', '2009-10-21 16:08:28', '2009-10-21 16:08:28');
INSERT INTO `pages_blocks` VALUES (1, 1, NULL, '', 'archive', '2009-10-21 16:08:28', '2009-10-21 16:08:28');
INSERT INTO `pages_blocks` VALUES (1, 1, NULL, '', 'archive', '2009-10-21 16:08:28', '2009-10-21 16:08:28');
INSERT INTO `pages_blocks` VALUES (1, 2, NULL, '', 'archive', '2009-10-21 16:08:42', '2009-10-21 16:08:42');
INSERT INTO `pages_blocks` VALUES (1, 2, NULL, '', 'archive', '2009-10-21 16:08:42', '2009-10-21 16:08:42');
INSERT INTO `pages_blocks` VALUES (1, 2, NULL, '', 'archive', '2009-10-21 16:08:42', '2009-10-21 16:08:42');
INSERT INTO `pages_blocks` VALUES (1, 3, NULL, '', 'archive', '2009-10-21 16:09:32', '2009-10-21 16:09:32');
INSERT INTO `pages_blocks` VALUES (1, 3, NULL, '', 'archive', '2009-10-21 16:09:32', '2009-10-21 16:09:32');
INSERT INTO `pages_blocks` VALUES (1, 3, NULL, '', 'archive', '2009-10-21 16:09:32', '2009-10-21 16:09:32');
INSERT INTO `pages_blocks` VALUES (1, 4, NULL, '', 'archive', '2009-10-21 16:50:38', '2009-10-21 16:50:38');
INSERT INTO `pages_blocks` VALUES (1, 4, NULL, '', 'archive', '2009-10-21 16:50:38', '2009-10-21 16:50:38');
INSERT INTO `pages_blocks` VALUES (1, 4, NULL, '', 'archive', '2009-10-21 16:50:38', '2009-10-21 16:50:38');
INSERT INTO `pages_blocks` VALUES (1, 5, NULL, '', 'archive', '2009-10-21 16:50:52', '2009-10-21 16:50:52');
INSERT INTO `pages_blocks` VALUES (1, 5, NULL, '', 'archive', '2009-10-21 16:50:52', '2009-10-21 16:50:52');
INSERT INTO `pages_blocks` VALUES (1, 5, NULL, '', 'archive', '2009-10-21 16:50:52', '2009-10-21 16:50:52');
INSERT INTO `pages_blocks` VALUES (1, 6, NULL, '', 'archive', '2009-10-21 16:51:04', '2009-10-21 16:51:04');
INSERT INTO `pages_blocks` VALUES (1, 6, NULL, '', 'archive', '2009-10-21 16:51:04', '2009-10-21 16:51:04');
INSERT INTO `pages_blocks` VALUES (1, 6, NULL, '', 'archive', '2009-10-21 16:51:04', '2009-10-21 16:51:04');
INSERT INTO `pages_blocks` VALUES (1, 7, NULL, '', 'archive', '2009-10-21 16:51:47', '2009-10-21 16:51:47');
INSERT INTO `pages_blocks` VALUES (1, 7, NULL, '', 'archive', '2009-10-21 16:51:47', '2009-10-21 16:51:47');
INSERT INTO `pages_blocks` VALUES (1, 7, NULL, '', 'archive', '2009-10-21 16:51:47', '2009-10-21 16:51:47');
INSERT INTO `pages_blocks` VALUES (1, 8, NULL, '', 'archive', '2009-10-21 16:52:08', '2009-10-21 16:52:08');
INSERT INTO `pages_blocks` VALUES (1, 8, NULL, '', 'archive', '2009-10-21 16:52:08', '2009-10-21 16:52:08');
INSERT INTO `pages_blocks` VALUES (1, 8, NULL, '', 'archive', '2009-10-21 16:52:08', '2009-10-21 16:52:08');
INSERT INTO `pages_blocks` VALUES (1, 9, NULL, '', 'archive', '2009-10-21 16:52:14', '2009-10-21 16:52:14');
INSERT INTO `pages_blocks` VALUES (1, 9, NULL, '', 'archive', '2009-10-21 16:52:14', '2009-10-21 16:52:14');
INSERT INTO `pages_blocks` VALUES (1, 9, NULL, '', 'archive', '2009-10-21 16:52:14', '2009-10-21 16:52:14');
INSERT INTO `pages_blocks` VALUES (1, 10, NULL, '', 'archive', '2009-10-21 16:52:23', '2009-10-21 16:52:23');
INSERT INTO `pages_blocks` VALUES (1, 10, NULL, '', 'archive', '2009-10-21 16:52:23', '2009-10-21 16:52:23');
INSERT INTO `pages_blocks` VALUES (1, 10, NULL, '', 'archive', '2009-10-21 16:52:23', '2009-10-21 16:52:23');
INSERT INTO `pages_blocks` VALUES (1, 11, NULL, '', 'archive', '2009-10-21 16:52:44', '2009-10-21 16:52:44');
INSERT INTO `pages_blocks` VALUES (1, 11, NULL, '', 'archive', '2009-10-21 16:52:44', '2009-10-21 16:52:44');
INSERT INTO `pages_blocks` VALUES (1, 11, NULL, '', 'archive', '2009-10-21 16:52:44', '2009-10-21 16:52:44');
INSERT INTO `pages_blocks` VALUES (1, 12, NULL, '', 'archive', '2009-10-21 16:53:00', '2009-10-21 16:53:00');
INSERT INTO `pages_blocks` VALUES (1, 12, NULL, '', 'archive', '2009-10-21 16:53:00', '2009-10-21 16:53:00');
INSERT INTO `pages_blocks` VALUES (1, 12, NULL, '', 'archive', '2009-10-21 16:53:00', '2009-10-21 16:53:00');
INSERT INTO `pages_blocks` VALUES (1, 13, NULL, '', 'archive', '2009-10-21 16:53:29', '2009-10-21 16:53:29');
INSERT INTO `pages_blocks` VALUES (1, 13, NULL, '', 'archive', '2009-10-21 16:53:29', '2009-10-21 16:53:29');
INSERT INTO `pages_blocks` VALUES (1, 13, NULL, '', 'archive', '2009-10-21 16:53:29', '2009-10-21 16:53:29');
INSERT INTO `pages_blocks` VALUES (1, 14, NULL, '', 'archive', '2009-10-21 16:54:01', '2009-10-21 16:54:01');
INSERT INTO `pages_blocks` VALUES (1, 14, NULL, '', 'archive', '2009-10-21 16:54:01', '2009-10-21 16:54:01');
INSERT INTO `pages_blocks` VALUES (1, 14, NULL, '', 'archive', '2009-10-21 16:54:01', '2009-10-21 16:54:01');
INSERT INTO `pages_blocks` VALUES (1, 15, NULL, '', 'archive', '2009-10-21 16:55:03', '2009-10-21 16:55:03');
INSERT INTO `pages_blocks` VALUES (1, 15, NULL, '', 'archive', '2009-10-21 16:55:03', '2009-10-21 16:55:03');
INSERT INTO `pages_blocks` VALUES (1, 15, NULL, '', 'archive', '2009-10-21 16:55:03', '2009-10-21 16:55:03');
INSERT INTO `pages_blocks` VALUES (1, 16, NULL, '', 'archive', '2009-10-21 16:55:18', '2009-10-21 16:55:18');
INSERT INTO `pages_blocks` VALUES (1, 16, NULL, '', 'archive', '2009-10-21 16:55:18', '2009-10-21 16:55:18');
INSERT INTO `pages_blocks` VALUES (1, 16, NULL, '', 'archive', '2009-10-21 16:55:18', '2009-10-21 16:55:18');
INSERT INTO `pages_blocks` VALUES (1, 17, NULL, '', 'active', '2009-10-21 16:56:58', '2009-10-21 16:56:58');
INSERT INTO `pages_blocks` VALUES (1, 17, NULL, '', 'active', '2009-10-21 16:56:58', '2009-10-21 16:56:58');
INSERT INTO `pages_blocks` VALUES (1, 17, NULL, '', 'active', '2009-10-21 16:56:58', '2009-10-21 16:56:58');
INSERT INTO `pages_blocks` VALUES (2, 1338, NULL, '', 'active', '2009-11-04 16:49:36', '2009-11-04 16:49:36');
INSERT INTO `pages_blocks` VALUES (2, 1338, NULL, '', 'active', '2009-11-04 16:49:36', '2009-11-04 16:49:36');
INSERT INTO `pages_blocks` VALUES (2, 1338, NULL, '', 'active', '2009-11-04 16:49:36', '2009-11-04 16:49:36');

-- --------------------------------------------------------

-- 
-- Table structure for table `pages_extras`
-- 

DROP TABLE IF EXISTS `pages_extras`;
CREATE TABLE IF NOT EXISTS `pages_extras` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the extra.',
  `module` varchar(255) NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` enum('homepage','block','widget','html') NOT NULL COMMENT 'The type of the block.',
  `label` varchar(255) NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `file` varchar(255) NOT NULL COMMENT 'The filename for the extra.',
  `data` text COMMENT 'A serialized value with the optional parameters',
  `hidden` enum('Y','N') NOT NULL default 'N' COMMENT 'Should the extra be shown in the backend?',
  `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The possible extras' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `pages_extras`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pages_groups`
-- 

DROP TABLE IF EXISTS `pages_groups`;
CREATE TABLE IF NOT EXISTS `pages_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `pages_groups`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pages_groups_pages`
-- 

DROP TABLE IF EXISTS `pages_groups_pages`;
CREATE TABLE IF NOT EXISTS `pages_groups_pages` (
  `page_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY  (`page_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pages_groups_pages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pages_groups_profiles`
-- 

DROP TABLE IF EXISTS `pages_groups_profiles`;
CREATE TABLE IF NOT EXISTS `pages_groups_profiles` (
  `profile_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY  (`profile_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pages_groups_profiles`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pages_templates`
-- 

DROP TABLE IF EXISTS `pages_templates`;
CREATE TABLE IF NOT EXISTS `pages_templates` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Unique ID for the template.',
  `label` varchar(255) NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) NOT NULL COMMENT 'Filename for the template.',
  `number_of_blocks` int(11) NOT NULL default '1' COMMENT 'The number of blocks used in the template.',
  `active` enum('Y','N') NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
  `is_default` enum('Y','N') NOT NULL default 'N' COMMENT 'Is this the default template.',
  `data` text COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The possible templates' AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `pages_templates`
-- 

INSERT INTO `pages_templates` VALUES (1, 'home', 'core/layout/templates/home.tpl', 3, 'Y', 'N', 'a:1:{s:5:"names";a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}} ');
INSERT INTO `pages_templates` VALUES (2, 'content', 'core/layout/templates/index.tpl', 2, 'Y', 'Y', 'a:1:{s:5:"names";a:2:{i:0;s:1:"a";i:1;s:1:"b";}} ');

-- --------------------------------------------------------

-- 
-- Table structure for table `profiles`
-- 

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` enum('Y','N') NOT NULL default 'Y',
  `blocked` enum('Y','N') NOT NULL default 'N',
  `registered_on` datetime NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `profiles`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `profiles_sessions`
-- 

DROP TABLE IF EXISTS `profiles_sessions`;
CREATE TABLE IF NOT EXISTS `profiles_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `profile_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `profiles_sessions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `profiles_settings`
-- 

DROP TABLE IF EXISTS `profiles_settings`;
CREATE TABLE IF NOT EXISTS `profiles_settings` (
  `profile_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`profile_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `profiles_settings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `snippets`
-- 

DROP TABLE IF EXISTS `snippets`;
CREATE TABLE IF NOT EXISTS `snippets` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `hidden` enum('Y','N') NOT NULL default 'N',
  `status` enum('active','archived') NOT NULL default 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Sometimes we need editable parts in the templates, this modu' AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `snippets`
-- 

INSERT INTO `snippets` VALUES (1, 1, 1, 'nl', 'test', '<p>test</p>', 'N', 'archived', '2009-10-21 14:04:25', '2009-10-21 14:04:25');
INSERT INTO `snippets` VALUES (2, 2, 1, 'nl', 'snipper de snip', '<p>Hier de inhoud van mijn magnifieke snippet</p>', 'N', 'archived', '2009-10-21 14:16:26', '2009-10-21 14:16:26');
INSERT INTO `snippets` VALUES (3, 7, 1, 'nl', 'sucker', '<p>Inhoud van mijn snippet</p>', 'N', 'archived', '2009-10-21 14:59:54', '2009-10-21 17:13:55');
INSERT INTO `snippets` VALUES (3, 8, 1, 'nl', 'nog een titeltje dan', '<p>Lets go!</p>', 'Y', 'archived', '2009-10-21 14:59:54', '2009-10-21 17:14:10');
INSERT INTO `snippets` VALUES (3, 9, 1, 'nl', 'nog een titeltje dan', '<p>Lets go!</p>', 'Y', 'archived', '2009-10-21 14:59:54', '2009-10-21 17:15:46');
INSERT INTO `snippets` VALUES (3, 10, 1, 'nl', 'nog een titeltje dan', '<p>Lets go!</p>', 'N', 'archived', '2009-10-21 14:59:54', '2009-10-21 17:15:51');
INSERT INTO `snippets` VALUES (3, 11, 1, 'nl', 'nog een titeltje dan', '<p>Lets go!</p>', 'Y', 'archived', '2009-10-21 14:59:54', '2009-10-21 17:16:00');
INSERT INTO `snippets` VALUES (3, 12, 1, 'nl', 'nog een titeltje dan', '<p>Lets go!</p>', 'N', 'active', '2009-10-21 14:59:54', '2009-10-21 17:16:05');
INSERT INTO `snippets` VALUES (2, 13, 1, 'nl', 'snipper de snip', '<p>Hier de inhoud van mijn magnifieke snippet</p>', 'N', 'active', '2009-10-21 14:16:26', '2009-11-19 09:08:01');
INSERT INTO `snippets` VALUES (1, 14, 1, 'nl', 'test', '<p>test</p>', 'N', 'active', '2009-10-21 14:04:25', '2009-12-02 15:55:11');
INSERT INTO `snippets` VALUES (4, 15, 1, 'nl', 'dikke test', '<p>test</p>', 'N', 'archived', '2009-12-16 13:12:48', '2009-12-16 13:12:48');
INSERT INTO `snippets` VALUES (4, 16, 1, 'nl', 'dikke test', '<p>test</p>', 'N', 'active', '2009-12-16 13:12:48', '2009-12-16 13:12:56');

-- --------------------------------------------------------

-- 
-- Table structure for table `tags`
-- 

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `tags`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT 'username, will be case-sensitive',
  `password` varchar(255) NOT NULL COMMENT 'will be case-sensitive',
  `active` enum('Y','N') NOT NULL default 'Y' COMMENT 'is this user active?',
  `deleted` enum('Y','N') NOT NULL default 'N' COMMENT 'is the user deleted?',
  `is_god` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The backend users' AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 1, 'tijs', 'c3581516868fb3b71746931cac66390e', 'Y', 'N', 'Y');

-- --------------------------------------------------------

-- 
-- Table structure for table `users_sessions`
-- 

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` varchar(5) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `users_sessions`
-- 

INSERT INTO `users_sessions` VALUES (1, 1, 'nl', '7d4d1a563bf0c89f6ea14a57a2ce6875', '8ff05372853e556c798f439c4a7b3f7a', '2009-12-17 17:35:43');

-- --------------------------------------------------------

-- 
-- Table structure for table `users_settings`
-- 

DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE IF NOT EXISTS `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'name of the setting',
  `value` text NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `users_settings`
-- 

INSERT INTO `users_settings` VALUES (1, 'avatar', 's:7:"0_1.jpg";');
INSERT INTO `users_settings` VALUES (1, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (1, 'date_long_format', 's:11:"d/m/Y H:i:s";');
INSERT INTO `users_settings` VALUES (1, 'edit', 's:8:"bewerken";');
INSERT INTO `users_settings` VALUES (1, 'email', 's:16:"tijs@netlash.com";');
INSERT INTO `users_settings` VALUES (1, 'form', 's:4:"edit";');
INSERT INTO `users_settings` VALUES (1, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (1, 'name', 's:4:"Tijs";');
INSERT INTO `users_settings` VALUES (1, 'nickname', 's:4:"Tijs";');
INSERT INTO `users_settings` VALUES (1, 'surname', 's:8:"Verkoyen";');