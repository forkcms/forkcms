-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 31, 2009 at 01:13 AM
-- Server version: 5.0.41
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `forkng`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) character set latin1 NOT NULL,
  `parameters` text character set latin1 COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` VALUES(1, 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups_rights_actions`
--

DROP TABLE IF EXISTS `groups_rights_actions`;
CREATE TABLE IF NOT EXISTS `groups_rights_actions` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) character set latin1 NOT NULL COMMENT 'name of the module',
  `action` varchar(255) character set latin1 NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL default '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `groups_rights_actions`
--

INSERT INTO `groups_rights_actions` VALUES(1, 1, 'dashboard', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(2, 1, 'blog', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(3, 1, 'users', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(4, 1, 'users', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(5, 1, 'users', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(6, 1, 'users', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES(7, 1, 'languages', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(8, 1, 'users', 'groups', 7);
INSERT INTO `groups_rights_actions` VALUES(9, 1, 'users', 'edit_group', 7);
INSERT INTO `groups_rights_actions` VALUES(10, 1, 'users', 'add_group', 7);
INSERT INTO `groups_rights_actions` VALUES(11, 1, 'spotlight', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(12, 1, 'spotlight', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(13, 1, 'spotlight', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(14, 1, 'spotlight', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES(15, 1, 'spotlight', 'sequence', 7);

-- --------------------------------------------------------

--
-- Table structure for table `groups_rights_modules`
--

DROP TABLE IF EXISTS `groups_rights_modules`;
CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) character set latin1 NOT NULL COMMENT 'name of the module',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `groups_rights_modules`
--

INSERT INTO `groups_rights_modules` VALUES(1, 1, 'dashboard');
INSERT INTO `groups_rights_modules` VALUES(2, 1, 'blog');
INSERT INTO `groups_rights_modules` VALUES(3, 1, 'users');
INSERT INTO `groups_rights_modules` VALUES(4, 1, 'languages');
INSERT INTO `groups_rights_modules` VALUES(5, 1, 'spotlight');

-- --------------------------------------------------------

--
-- Table structure for table `languages_labels`
--

DROP TABLE IF EXISTS `languages_labels`;
CREATE TABLE IF NOT EXISTS `languages_labels` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) character set latin1 NOT NULL,
  `application` varchar(255) character set latin1 NOT NULL,
  `module` varchar(255) character set latin1 NOT NULL,
  `type` enum('act','err','lbl','msg') character set latin1 NOT NULL default 'lbl',
  `name` varchar(255) character set latin1 NOT NULL,
  `value` text character set latin1,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `languages_labels`
--

INSERT INTO `languages_labels` VALUES(1, 'nl', 'backend', 'core', 'lbl', 'Edit', 'bewerken');
INSERT INTO `languages_labels` VALUES(2, 'nl', 'backend', 'core', 'lbl', 'Name', 'naam');
INSERT INTO `languages_labels` VALUES(3, 'nl', 'backend', 'core', 'lbl', 'Pages', 'pagina''s');
INSERT INTO `languages_labels` VALUES(4, 'en', 'backend', 'core', 'lbl', 'Surname', 'surname');

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

DROP TABLE IF EXISTS `meta`;
CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL auto_increment,
  `keywords` varchar(255) character set latin1 NOT NULL,
  `keywords_overwrite` enum('Y','N') character set latin1 NOT NULL default 'N',
  `description` varchar(255) character set latin1 NOT NULL,
  `description_overwrite` enum('Y','N') character set latin1 NOT NULL default 'N',
  `title` varchar(255) character set latin1 NOT NULL,
  `title_overwrite` enum('Y','N') character set latin1 NOT NULL default 'N',
  `url` varchar(255) character set latin1 NOT NULL,
  `url_overwrite` enum('Y','N') character set latin1 NOT NULL default 'N',
  `custom` text character set latin1 COMMENT 'used for custom meta-information',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Meta-information' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) character set latin1 NOT NULL COMMENT 'unique module name',
  `description` text character set latin1,
  `active` enum('Y','N') character set latin1 NOT NULL default 'Y',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `modules`
--


-- --------------------------------------------------------

--
-- Table structure for table `modules_settings`
--

DROP TABLE IF EXISTS `modules_settings`;
CREATE TABLE IF NOT EXISTS `modules_settings` (
  `module` varchar(255) character set latin1 NOT NULL COMMENT 'name of the module',
  `name` varchar(255) character set latin1 NOT NULL COMMENT 'name of the setting',
  `value` text character set latin1 NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`module`,`name`),
  KEY `fk_modules_settings_modules` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `modules_settings`
--

INSERT INTO `modules_settings` VALUES('', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES('core', 'site_title_nl', 's:7:"Fork NG";');

-- --------------------------------------------------------

--
-- Table structure for table `modules_tags`
--

DROP TABLE IF EXISTS `modules_tags`;
CREATE TABLE IF NOT EXISTS `modules_tags` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(255) character set latin1 NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_modules_tags_modules` (`module`),
  KEY `fk_modules_tags_tags` (`tag_id`)
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
  `type` enum('page','header','footer','alias') NOT NULL default 'page' COMMENT 'page, header, footer, ...',
  `title` varchar(255) NOT NULL,
  `content` text,
  `navigation_title` varchar(255) NOT NULL COMMENT 'title that will be used in the navigation',
  `navigation_title_overwrite` enum('Y','N') NOT NULL default 'N' COMMENT 'should we override the navigation title',
  `hidden` enum('Y','N') NOT NULL default 'N' COMMENT 'is the page hidden?',
  `status` enum('active','archive','draft') NOT NULL default 'active' COMMENT 'is this the active, archive or draft version',
  `parameters` text COMMENT 'serialized array that may contains type specific parameters',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `allow_move` enum('Y','N') NOT NULL default 'Y',
  `allow_children` enum('Y','N') NOT NULL default 'Y',
  `allow_content` enum('Y','N') NOT NULL default 'Y',
  `allow_edit` enum('Y','N') NOT NULL default 'Y',
  `allow_delete` enum('Y','N') NOT NULL default 'Y',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `fk_pages_meta` (`meta_id`),
  KEY `fk_pages_pages_templates` (`template_id`),
  KEY `fk_pages_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages_blocks`
--

DROP TABLE IF EXISTS `pages_blocks`;
CREATE TABLE IF NOT EXISTS `pages_blocks` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `block_id` int(11) NOT NULL,
  `extra_id` int(11) default NULL,
  `name` varchar(255) character set latin1 NOT NULL,
  `content` text character set latin1,
  `status` enum('active','archive','draft') character set latin1 NOT NULL default 'active',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pages_blocks`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages_extras`
--

DROP TABLE IF EXISTS `pages_extras`;
CREATE TABLE IF NOT EXISTS `pages_extras` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(255) character set latin1 NOT NULL COMMENT 'the name of the module this extra belongs to',
  `type` enum('homepage','block') character set latin1 NOT NULL,
  `title` varchar(255) character set latin1 NOT NULL COMMENT 'a label that will be used in the backend',
  `path` varchar(255) character set latin1 NOT NULL COMMENT 'the path to the extra',
  `parameters` text character set latin1 COMMENT 'a serialized value with the optional parameters',
  `hidden` enum('Y','N') character set latin1 NOT NULL default 'N' COMMENT 'should the extra be shown in the backend?',
  `sequence` int(11) NOT NULL COMMENT 'the sequence in the backend',
  PRIMARY KEY  (`id`),
  KEY `fk_pages_extras_modules` (`module`)
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
  `name` varchar(255) character set latin1 NOT NULL,
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
  KEY `fk_pages_groups_pages_pages_groups` (`group_id`)
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
  KEY `fk_pages_groups_profiles_pages_groups` (`group_id`),
  KEY `fk_pages_groups_profiles_profiles` (`profile_id`)
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
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) character set latin1 NOT NULL,
  `path` varchar(255) character set latin1 NOT NULL COMMENT 'the path to the template file',
  `parameters` text character set latin1 NOT NULL COMMENT 'serialized array containing type and name for the needed blocks',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The possible templates' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pages_templates`
--


-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) character set latin1 NOT NULL,
  `password` varchar(255) character set latin1 NOT NULL,
  `password_raw` varchar(255) character set latin1 NOT NULL,
  `active` enum('Y','N') character set latin1 NOT NULL default 'Y',
  `blocked` enum('Y','N') character set latin1 NOT NULL default 'N',
  `registered_on` datetime NOT NULL,
  `url` varchar(255) character set latin1 NOT NULL,
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
  `session_id` varchar(255) character set latin1 NOT NULL,
  `secret_key` varchar(255) character set latin1 NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_profiles_sessions_profiles` (`profile_id`)
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
  `name` varchar(255) character set latin1 NOT NULL,
  `value` text character set latin1 NOT NULL,
  PRIMARY KEY  (`profile_id`,`name`),
  KEY `fk_profiles_settings_profiles` (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profiles_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `spotlight`
--

DROP TABLE IF EXISTS `spotlight`;
CREATE TABLE IF NOT EXISTS `spotlight` (
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
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Sometimes we need editable parts in the templates, this modu' AUTO_INCREMENT=22 ;

--
-- Dumping data for table `spotlight`
--

INSERT INTO `spotlight` VALUES(1, 19, 1, 'nl', 'Item 1', '<p>Item 1</p>', 'N', 'active', '2009-05-30 20:21:49', '2009-05-30 20:21:49', 1);
INSERT INTO `spotlight` VALUES(2, 20, 1, 'nl', 'Item 2', '<p>Item 2</p>', 'N', 'active', '2009-05-30 20:22:19', '2009-05-30 20:22:19', 2);
INSERT INTO `spotlight` VALUES(3, 21, 1, 'nl', 'Item 3', '<p>Item 3</p>', 'N', 'active', '2009-05-30 20:22:34', '2009-05-30 20:22:34', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) character set latin1 NOT NULL,
  `tag` varchar(255) character set latin1 NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) character set latin1 NOT NULL,
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
  `username` varchar(255) character set latin1 NOT NULL COMMENT 'username, will be case-sensitive',
  `password` varchar(255) character set latin1 NOT NULL COMMENT 'will be case-sensitive',
  `password_raw` varchar(255) character set latin1 NOT NULL COMMENT 'used when a user forgot his password',
  `active` enum('Y','N') character set latin1 NOT NULL default 'Y' COMMENT 'is this user active?',
  `deleted` enum('Y','N') character set latin1 NOT NULL default 'N' COMMENT 'is the user deleted?',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The backend users' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 1, 'tijs', 'c3581516868fb3b71746931cac66390e', 'internet', 'Y', 'N');
INSERT INTO `users` VALUES(5, 0, 'davy', 'c3581516868fb3b71746931cac66390e', 'internet', 'N', 'Y');
INSERT INTO `users` VALUES(6, 0, 'johan', '7fedcb034ecf9df4be8c1ea13362053b', 'johan', 'Y', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` bigint(20) NOT NULL,
  `session_id` varchar(255) character set latin1 NOT NULL,
  `secret_key` varchar(255) character set latin1 NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users_sessions`
--

INSERT INTO `users_sessions` VALUES(1, 1, 0, 'c4309ea0f5ce94c0866a85e686945c61', '21be58d16e69a78abc848141199a2eac', '2009-05-31 01:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `users_settings`
--

DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE IF NOT EXISTS `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) character set latin1 NOT NULL COMMENT 'name of the setting',
  `value` text character set latin1 NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_settings`
--

INSERT INTO `users_settings` VALUES(1, 'avatar', 's:7:"0_1.jpg";');
INSERT INTO `users_settings` VALUES(1, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES(1, 'date_long_format', 's:11:"d/m/Y H:i:s";');
INSERT INTO `users_settings` VALUES(1, 'email', 's:16:"tijs@netlash.com";');
INSERT INTO `users_settings` VALUES(1, 'name', 's:4:"Tijs";');
INSERT INTO `users_settings` VALUES(1, 'nickname', 's:4:"Tijs";');
INSERT INTO `users_settings` VALUES(1, 'surname', 's:8:"Verkoyen";');
INSERT INTO `users_settings` VALUES(5, 'avatar', 's:13:"no-avatar.jpg";');
INSERT INTO `users_settings` VALUES(5, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES(5, 'email', 's:16:"davy@netlash.com";');
INSERT INTO `users_settings` VALUES(5, 'name', 's:4:"Davy";');
INSERT INTO `users_settings` VALUES(5, 'nickname', 's:4:"Davy";');
INSERT INTO `users_settings` VALUES(5, 'surname', 's:9:"Hellemans";');
INSERT INTO `users_settings` VALUES(6, 'avatar', 's:13:"no-avatar.jpg";');
INSERT INTO `users_settings` VALUES(6, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES(6, 'email', 's:17:"johan@netlash.com";');
INSERT INTO `users_settings` VALUES(6, 'name', 's:5:"Johan";');
INSERT INTO `users_settings` VALUES(6, 'nickname', 's:5:"Johan";');
INSERT INTO `users_settings` VALUES(6, 'surname', 's:6:"Ronsse";');
