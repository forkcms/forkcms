-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 14, 2009 at 05:27 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `forkng`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups_rights_actions`
--

INSERT INTO `groups_rights_actions` VALUES(1, 1, 'dashboard', 'index', 5);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups_rights_modules`
--

INSERT INTO `groups_rights_modules` VALUES(1, 1, 'dashboard');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Meta-information' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `meta`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `modules`
--


-- --------------------------------------------------------

--
-- Table structure for table `modules_settings`
--

DROP TABLE IF EXISTS `modules_settings`;
CREATE TABLE IF NOT EXISTS `modules_settings` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(255) NOT NULL COMMENT 'name of the module',
  `name` varchar(255) NOT NULL COMMENT 'name of the setting',
  `value` text NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`id`),
  KEY `fk_modules_settings_modules` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `modules_settings`
--


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
  PRIMARY KEY  (`id`),
  KEY `fk_modules_tags_modules` (`module`),
  KEY `fk_modules_tags_tags` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `modules_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
  `page_id` int(11) NOT NULL COMMENT 'the real page_id',
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
  PRIMARY KEY  (`id`),
  KEY `fk_pages_meta` (`meta_id`),
  KEY `fk_pages_pages_templates` (`template_id`),
  KEY `fk_pages_users` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `name` varchar(255) NOT NULL,
  `content` text,
  `status` enum('active','archive','draft') NOT NULL default 'active',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pages_blocks`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT 'the path to the template file',
  `parameters` text NOT NULL COMMENT 'serialized array containing type and name for the needed blocks',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The possible templates' AUTO_INCREMENT=1 ;

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
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_raw` varchar(255) NOT NULL,
  `active` enum('Y','N') NOT NULL default 'Y',
  `blocked` enum('Y','N') NOT NULL default 'N',
  `registered_on` datetime NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY  (`id`),
  KEY `fk_profiles_sessions_profiles` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profiles_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `profiles_settings`
--

DROP TABLE IF EXISTS `profiles_settings`;
CREATE TABLE IF NOT EXISTS `profiles_settings` (
  `id` int(11) NOT NULL auto_increment,
  `profile_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_profiles_settings_profiles` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profiles_settings`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `password_raw` varchar(255) NOT NULL COMMENT 'used when a user forgot his password',
  `active` enum('Y','N') NOT NULL default 'Y' COMMENT 'is this user active?',
  `deleted` enum('Y','N') NOT NULL default 'N' COMMENT 'is the user deleted?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='The backend users' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 1, 'tijs', 'c3581516868fb3b71746931cac66390e', 'internet', 'Y', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `users_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_settings`
--

DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE IF NOT EXISTS `users_settings` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'name of the setting',
  `value` text NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users_settings`
--

