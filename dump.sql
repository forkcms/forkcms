-- phpMyAdmin SQL Dump
-- version 3.4.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 29, 2011 at 06:24 PM
-- Server version: 5.5.12
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mlitn`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics_keywords`
--

CREATE TABLE IF NOT EXISTS `analytics_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_landing_pages`
--

CREATE TABLE IF NOT EXISTS `analytics_landing_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `bounces` int(11) NOT NULL,
  `bounce_rate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_pages`
--

CREATE TABLE IF NOT EXISTS `analytics_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_viewed` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_referrers`
--

CREATE TABLE IF NOT EXISTS `analytics_referrers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referrer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `meta_id`, `language`, `title`) VALUES
(1, 8, 'en', 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` text COLLATE utf8_unicode_ci,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('comment','trackback') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'comment',
  `status` enum('published','moderation','spam') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'moderation',
  `data` text COLLATE utf8_unicode_ci COMMENT 'Serialized array with extra data',
  PRIMARY KEY (`id`),
  KEY `idx_post_id_status` (`post_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL COMMENT 'The real post id',
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `introduction` text COLLATE utf8_unicode_ci,
  `text` text COLLATE utf8_unicode_ci,
  `status` enum('active','archived','draft') COLLATE utf8_unicode_ci NOT NULL,
  `publish_on` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `hidden` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `allow_comments` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `num_comments` int(11) NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks`
--

CREATE TABLE IF NOT EXISTS `content_blocks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default.tpl',
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `hidden` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `status` enum('active','archived') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `from_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_to_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_to_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `html` text COLLATE utf8_unicode_ci NOT NULL,
  `plain_text` text COLLATE utf8_unicode_ci NOT NULL,
  `attachments` text COLLATE utf8_unicode_ci,
  `send_on` datetime DEFAULT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE IF NOT EXISTS `faq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `extra_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_questions`
--

CREATE TABLE IF NOT EXISTS `faq_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `question` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `answer` text COLLATE utf8_unicode_ci NOT NULL,
  `hidden` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `sequence` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_faq_questions_faq_categories` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method` enum('database','database_email') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'database_email',
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `success_message` text COLLATE utf8_unicode_ci,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `language`, `user_id`, `name`, `method`, `email`, `success_message`, `identifier`, `created_on`, `edited_on`) VALUES
(1, 'en', 1, 'Contact', 'database_email', 'matthias@netlash.com', 'Your e-mail was sent.', 'contact-en', '2011-06-23 08:07:19', '2011-06-23 08:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `forms_data`
--

CREATE TABLE IF NOT EXISTS `forms_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL,
  `session_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sent_on` datetime NOT NULL,
  `data` text COLLATE utf8_unicode_ci COMMENT 'Serialized array with extra information.',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forms_data_fields`
--

CREATE TABLE IF NOT EXISTS `forms_data_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `data_id` (`data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forms_fields`
--

CREATE TABLE IF NOT EXISTS `forms_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL,
  `type` enum('textbox','textarea','dropdown','checkbox','radiobutton','heading','paragraph','submit') COLLATE utf8_unicode_ci NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sequence` (`sequence`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `forms_fields`
--

INSERT INTO `forms_fields` (`id`, `form_id`, `type`, `settings`, `sequence`) VALUES
(1, 1, 'submit', 'a:1:{s:6:"values";s:4:"Send";}', NULL),
(2, 1, 'textbox', 'a:1:{s:5:"label";s:4:"Name";}', NULL),
(3, 1, 'textbox', 'a:1:{s:5:"label";s:6:"E-mail";}', NULL),
(4, 1, 'textarea', 'a:1:{s:5:"label";s:7:"Message";}', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `forms_fields_validation`
--

CREATE TABLE IF NOT EXISTS `forms_fields_validation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `type` enum('required','email','numeric') COLLATE utf8_unicode_ci NOT NULL,
  `parameter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'If you want to validate higher then a number, the number would be the parameter',
  `error_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `forms_fields_validation`
--

INSERT INTO `forms_fields_validation` (`id`, `field_id`, `type`, `parameter`, `error_message`) VALUES
(1, 2, 'required', NULL, 'Please provide a name.'),
(2, 3, 'email', NULL, 'Please provide a valid e-email.'),
(3, 4, 'required', NULL, 'Message is a required field.');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parameters` text COLLATE utf8_unicode_ci COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `parameters`) VALUES
(1, 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups_rights_actions`
--

CREATE TABLE IF NOT EXISTS `groups_rights_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL DEFAULT '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=158 ;

--
-- Dumping data for table `groups_rights_actions`
--

INSERT INTO `groups_rights_actions` (`id`, `group_id`, `module`, `action`, `level`) VALUES
(1, 1, 'dashboard', 'index', 7),
(2, 1, 'dashboard', 'alter_sequence', 7),
(3, 1, 'locale', 'add', 7),
(4, 1, 'locale', 'analyse', 7),
(5, 1, 'locale', 'edit', 7),
(6, 1, 'locale', 'export', 7),
(7, 1, 'locale', 'export_analyse', 7),
(8, 1, 'locale', 'import', 7),
(9, 1, 'locale', 'index', 7),
(10, 1, 'locale', 'mass_action', 7),
(11, 1, 'locale', 'save_translation', 7),
(12, 1, 'locale', 'delete', 7),
(13, 1, 'users', 'add', 7),
(14, 1, 'users', 'delete', 7),
(15, 1, 'users', 'edit', 7),
(16, 1, 'users', 'index', 7),
(17, 1, 'users', 'undo_delete', 7),
(18, 1, 'groups', 'index', 7),
(19, 1, 'groups', 'add', 7),
(20, 1, 'groups', 'edit', 7),
(21, 1, 'groups', 'delete', 7),
(22, 1, 'settings', 'index', 7),
(23, 1, 'settings', 'themes', 7),
(24, 1, 'settings', 'email', 7),
(25, 1, 'settings', 'test_email_connection', 7),
(26, 1, 'pages', 'get_info', 7),
(27, 1, 'pages', 'move', 7),
(28, 1, 'pages', 'index', 7),
(29, 1, 'pages', 'add', 7),
(30, 1, 'pages', 'delete', 7),
(31, 1, 'pages', 'edit', 7),
(32, 1, 'pages', 'templates', 7),
(33, 1, 'pages', 'add_template', 7),
(34, 1, 'pages', 'edit_template', 7),
(35, 1, 'pages', 'delete_template', 7),
(36, 1, 'pages', 'settings', 7),
(37, 1, 'search', 'add_synonym', 7),
(38, 1, 'search', 'edit_synonym', 7),
(39, 1, 'search', 'delete_synonym', 7),
(40, 1, 'search', 'settings', 7),
(41, 1, 'search', 'statistics', 7),
(42, 1, 'search', 'synonyms', 7),
(43, 1, 'content_blocks', 'add', 7),
(44, 1, 'content_blocks', 'delete', 7),
(45, 1, 'content_blocks', 'edit', 7),
(46, 1, 'content_blocks', 'index', 7),
(47, 1, 'tags', 'autocomplete', 7),
(48, 1, 'tags', 'edit', 7),
(49, 1, 'tags', 'index', 7),
(50, 1, 'tags', 'mass_action', 7),
(51, 1, 'analytics', 'add_landing_page', 7),
(52, 1, 'analytics', 'all_pages', 7),
(53, 1, 'analytics', 'check_status', 7),
(54, 1, 'analytics', 'content', 7),
(55, 1, 'analytics', 'delete_landing_page', 7),
(56, 1, 'analytics', 'detail_page', 7),
(57, 1, 'analytics', 'exit_pages', 7),
(58, 1, 'analytics', 'get_traffic_sources', 7),
(59, 1, 'analytics', 'index', 7),
(60, 1, 'analytics', 'landing_pages', 7),
(61, 1, 'analytics', 'loading', 7),
(62, 1, 'analytics', 'mass_landing_page_action', 7),
(63, 1, 'analytics', 'refresh_traffic_sources', 7),
(64, 1, 'analytics', 'settings', 7),
(65, 1, 'blog', 'add_category', 7),
(66, 1, 'blog', 'add', 7),
(67, 1, 'blog', 'categories', 7),
(68, 1, 'blog', 'comments', 7),
(69, 1, 'blog', 'delete_category', 7),
(70, 1, 'blog', 'delete_spam', 7),
(71, 1, 'blog', 'delete', 7),
(72, 1, 'blog', 'edit_category', 7),
(73, 1, 'blog', 'edit_comment', 7),
(74, 1, 'blog', 'edit', 7),
(75, 1, 'blog', 'import_blogger', 7),
(76, 1, 'blog', 'index', 7),
(77, 1, 'blog', 'mass_comment_action', 7),
(78, 1, 'blog', 'settings', 7),
(79, 1, 'faq', 'index', 7),
(80, 1, 'faq', 'add', 7),
(81, 1, 'faq', 'edit', 7),
(82, 1, 'faq', 'delete', 7),
(83, 1, 'faq', 'sequence', 7),
(84, 1, 'faq', 'categories', 7),
(85, 1, 'faq', 'add_category', 7),
(86, 1, 'faq', 'edit_category', 7),
(87, 1, 'faq', 'delete_category', 7),
(88, 1, 'faq', 'sequence_questions', 7),
(89, 1, 'form_builder', 'add', 7),
(90, 1, 'form_builder', 'edit', 7),
(91, 1, 'form_builder', 'delete', 7),
(92, 1, 'form_builder', 'index', 7),
(93, 1, 'form_builder', 'data', 7),
(94, 1, 'form_builder', 'data_details', 7),
(95, 1, 'form_builder', 'mass_data_action', 7),
(96, 1, 'form_builder', 'get_field', 7),
(97, 1, 'form_builder', 'delete_field', 7),
(98, 1, 'form_builder', 'save_field', 7),
(99, 1, 'form_builder', 'sequence', 7),
(100, 1, 'form_builder', 'export_data', 7),
(101, 1, 'location', 'index', 7),
(102, 1, 'location', 'add', 7),
(103, 1, 'location', 'edit', 7),
(104, 1, 'location', 'delete', 7),
(105, 1, 'location', 'settings', 7),
(106, 1, 'mailmotor', 'add', 7),
(107, 1, 'mailmotor', 'add_address', 7),
(108, 1, 'mailmotor', 'add_campaign', 7),
(109, 1, 'mailmotor', 'add_custom_field', 7),
(110, 1, 'mailmotor', 'add_group', 7),
(111, 1, 'mailmotor', 'addresses', 7),
(112, 1, 'mailmotor', 'campaigns', 7),
(113, 1, 'mailmotor', 'copy', 7),
(114, 1, 'mailmotor', 'custom_fields', 7),
(115, 1, 'mailmotor', 'delete_bounces', 7),
(116, 1, 'mailmotor', 'delete_custom_field', 7),
(117, 1, 'mailmotor', 'edit', 7),
(118, 1, 'mailmotor', 'edit_address', 7),
(119, 1, 'mailmotor', 'edit_campaign', 7),
(120, 1, 'mailmotor', 'edit_custom_field', 7),
(121, 1, 'mailmotor', 'edit_group', 7),
(122, 1, 'mailmotor', 'edit_mailing_campaign', 7),
(123, 1, 'mailmotor', 'edit_mailing_iframe', 7),
(124, 1, 'mailmotor', 'export_addresses', 7),
(125, 1, 'mailmotor', 'export_statistics', 7),
(126, 1, 'mailmotor', 'export_statistics_campaign', 7),
(127, 1, 'mailmotor', 'groups', 7),
(128, 1, 'mailmotor', 'import_addresses', 7),
(129, 1, 'mailmotor', 'import_groups', 7),
(130, 1, 'mailmotor', 'index', 7),
(131, 1, 'mailmotor', 'link_account', 7),
(132, 1, 'mailmotor', 'load_client_info', 7),
(133, 1, 'mailmotor', 'mass_address_action', 7),
(134, 1, 'mailmotor', 'mass_campaign_action', 7),
(135, 1, 'mailmotor', 'mass_custom_field_action', 7),
(136, 1, 'mailmotor', 'mass_group_action', 7),
(137, 1, 'mailmotor', 'mass_mailing_action', 7),
(138, 1, 'mailmotor', 'save_content', 7),
(139, 1, 'mailmotor', 'save_send_date', 7),
(140, 1, 'mailmotor', 'send_mailing', 7),
(141, 1, 'mailmotor', 'settings', 7),
(142, 1, 'mailmotor', 'statistics', 7),
(143, 1, 'mailmotor', 'statistics_bounces', 7),
(144, 1, 'mailmotor', 'statistics_campaign', 7),
(145, 1, 'mailmotor', 'statistics_link', 7),
(146, 1, 'profiles', 'add_group', 7),
(147, 1, 'profiles', 'add_profile_group', 7),
(148, 1, 'profiles', 'block', 7),
(149, 1, 'profiles', 'delete_group', 7),
(150, 1, 'profiles', 'delete_profile_group', 7),
(151, 1, 'profiles', 'delete', 7),
(152, 1, 'profiles', 'edit_group', 7),
(153, 1, 'profiles', 'edit_profile_group', 7),
(154, 1, 'profiles', 'edit', 7),
(155, 1, 'profiles', 'groups', 7),
(156, 1, 'profiles', 'index', 7),
(157, 1, 'profiles', 'mass_action', 7);

-- --------------------------------------------------------

--
-- Table structure for table `groups_rights_modules`
--

CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

--
-- Dumping data for table `groups_rights_modules`
--

INSERT INTO `groups_rights_modules` (`id`, `group_id`, `module`) VALUES
(1, 1, 'dashboard'),
(2, 1, 'locale'),
(3, 1, 'users'),
(4, 1, 'groups'),
(5, 1, 'settings'),
(6, 1, 'pages'),
(7, 1, 'search'),
(8, 1, 'content_blocks'),
(9, 1, 'tags'),
(10, 1, 'analytics'),
(11, 1, 'blog'),
(12, 1, 'faq'),
(13, 1, 'form_builder'),
(14, 1, 'location'),
(15, 1, 'mailmotor'),
(16, 1, 'profiles');

-- --------------------------------------------------------

--
-- Table structure for table `groups_settings`
--

CREATE TABLE IF NOT EXISTS `groups_settings` (
  `group_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`group_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `groups_settings`
--

INSERT INTO `groups_settings` (`group_id`, `name`, `value`) VALUES
(1, 'dashboard_sequence', 'a:3:{s:8:"settings";a:1:{s:7:"analyse";a:4:{s:6:"column";s:4:"left";s:8:"position";i:1;s:6:"hidden";b:0;s:7:"present";b:1;}}s:4:"blog";a:1:{s:8:"comments";a:4:{s:6:"column";s:6:"middle";s:8:"position";i:1;s:6:"hidden";b:0;s:7:"present";b:1;}}s:9:"mailmotor";a:1:{s:10:"statistics";a:4:{s:6:"column";s:5:"right";s:8:"position";i:1;s:6:"hidden";b:0;s:7:"present";b:1;}}}');

-- --------------------------------------------------------

--
-- Table structure for table `locale`
--

CREATE TABLE IF NOT EXISTS `locale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `application` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('act','err','lbl','msg') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'lbl',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1229 ;

--
-- Dumping data for table `locale`
--

INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(1, 1, 'en', 'backend', 'locale', 'err', 'AlreadyExists', 'This translation already exists.', '2011-06-23 08:07:18'),
(2, 1, 'en', 'backend', 'locale', 'err', 'InvalidXML', 'This is an invalid XML-file.', '2011-06-23 08:07:18'),
(3, 1, 'en', 'backend', 'locale', 'err', 'InvalidActionValue', 'Only alphanumeric characters, - and _ are allowed.', '2011-06-23 08:07:18'),
(4, 1, 'en', 'backend', 'locale', 'err', 'ModuleHasToBeCore', 'The module needs to be core for frontend translations.', '2011-06-23 08:07:18'),
(5, 1, 'en', 'backend', 'locale', 'err', 'NoSelection', 'No translations were selected.', '2011-06-23 08:07:18'),
(6, 1, 'en', 'backend', 'locale', 'lbl', 'Actions', 'actions', '2011-06-23 08:07:18'),
(7, 1, 'en', 'backend', 'locale', 'lbl', 'Add', 'add translation', '2011-06-23 08:07:18'),
(8, 1, 'en', 'backend', 'locale', 'lbl', 'Copy', 'copy', '2011-06-23 08:07:18'),
(9, 1, 'en', 'backend', 'locale', 'lbl', 'Errors', 'errors', '2011-06-23 08:07:18'),
(10, 1, 'en', 'backend', 'locale', 'lbl', 'EN', 'english', '2011-06-23 08:07:18'),
(11, 1, 'en', 'backend', 'locale', 'lbl', 'FR', 'french', '2011-06-23 08:07:18'),
(12, 1, 'en', 'backend', 'locale', 'lbl', 'Labels', 'labels', '2011-06-23 08:07:18'),
(13, 1, 'en', 'backend', 'locale', 'lbl', 'Messages', 'messages', '2011-06-23 08:07:18'),
(14, 1, 'en', 'backend', 'locale', 'lbl', 'NL', 'dutch', '2011-06-23 08:07:18'),
(15, 1, 'en', 'backend', 'locale', 'lbl', 'Types', 'types', '2011-06-23 08:07:18'),
(16, 1, 'en', 'backend', 'locale', 'msg', 'Added', 'The translation "%1$s" was added.', '2011-06-23 08:07:18'),
(17, 1, 'en', 'backend', 'locale', 'msg', 'ConfirmDelete', 'Are you sure you want to delete this translation?', '2011-06-23 08:07:18'),
(18, 1, 'en', 'backend', 'locale', 'msg', 'Deleted', 'The translation "%1$s" was deleted.', '2011-06-23 08:07:18'),
(19, 1, 'en', 'backend', 'locale', 'msg', 'Edited', 'The translation "%1$s" was saved.', '2011-06-23 08:07:18'),
(20, 1, 'en', 'backend', 'locale', 'msg', 'EditTranslation', 'edit translation "%1$s"', '2011-06-23 08:07:18'),
(21, 1, 'en', 'backend', 'locale', 'msg', 'HelpActionValue', 'Only use alphanumeric characters (no capitals), - and _ for these translations, because they will be used in URLs.', '2011-06-23 08:07:18'),
(22, 1, 'en', 'backend', 'locale', 'msg', 'HelpAddName', 'The English reference for the translation', '2011-06-23 08:07:18'),
(23, 1, 'en', 'backend', 'locale', 'msg', 'HelpAddValue', 'The translation', '2011-06-23 08:07:18'),
(24, 1, 'en', 'backend', 'locale', 'msg', 'HelpDateField', 'eg. 20/06/2011', '2011-06-23 08:07:18'),
(25, 1, 'en', 'backend', 'locale', 'msg', 'HelpEditName', 'The English reference for the translation', '2011-06-23 08:07:18'),
(26, 1, 'en', 'backend', 'locale', 'msg', 'HelpEditValue', 'The translation', '2011-06-23 08:07:18'),
(27, 1, 'en', 'backend', 'locale', 'msg', 'HelpImageField', 'Only jp(e)g, gif or png-files are allowed.', '2011-06-23 08:07:18'),
(28, 1, 'en', 'backend', 'locale', 'msg', 'HelpName', 'The english reference for this translation', '2011-06-23 08:07:18'),
(29, 1, 'en', 'backend', 'locale', 'msg', 'HelpTimeField', 'eg. 14:35', '2011-06-23 08:07:18'),
(30, 1, 'en', 'backend', 'locale', 'msg', 'HelpValue', 'The translation', '2011-06-23 08:07:18'),
(31, 1, 'en', 'backend', 'locale', 'msg', 'Imported', '%1$s translations were imported.', '2011-06-23 08:07:18'),
(32, 1, 'en', 'backend', 'locale', 'msg', 'NoItems', 'There are no translations yet. <a href="%1$s">Add the first translation</a>.', '2011-06-23 08:07:18'),
(33, 1, 'en', 'backend', 'locale', 'msg', 'NoItemsFilter', 'There are no translations yet for this filter. <a href="%1$s">Add the first translation</a>.', '2011-06-23 08:07:18'),
(34, 1, 'en', 'backend', 'locale', 'msg', 'NoItemsAnalyse', 'No missing translations were found.', '2011-06-23 08:07:18'),
(35, 1, 'en', 'backend', 'locale', 'msg', 'OverwriteConflicts', 'Overwrite if the translation exists.', '2011-06-23 08:07:18'),
(36, 1, 'en', 'backend', 'dashboard', 'lbl', 'AllStatistics', 'all statistics', '2011-06-23 08:07:18'),
(37, 1, 'en', 'backend', 'dashboard', 'lbl', 'TopKeywords', 'top keywords', '2011-06-23 08:07:18'),
(38, 1, 'en', 'backend', 'dashboard', 'lbl', 'TopReferrers', 'top referrers', '2011-06-23 08:07:18'),
(39, 1, 'en', 'backend', 'dashboard', 'msg', 'EditYourDashboard', 'Personalize your dashboard', '2011-06-23 08:07:18'),
(40, 1, 'en', 'backend', 'dashboard', 'msg', 'HelpEditDashboard', 'Personalize your dashboard by dragging the boxes in the way you want. Close the boxes to remove them.', '2011-06-23 08:07:18'),
(41, 1, 'en', 'backend', 'dashboard', 'msg', 'WillBeEnabledOnSave', 'This widget wil be reenabled on save.', '2011-06-23 08:07:18'),
(42, 1, 'en', 'backend', 'core', 'err', 'ActionNotAllowed', 'You have insufficient rights for this action.', '2011-06-23 08:07:18'),
(43, 1, 'en', 'backend', 'core', 'err', 'AddingCategoryFailed', 'Something went wrong.', '2011-06-23 08:07:18'),
(44, 1, 'en', 'backend', 'core', 'err', 'AddTagBeforeSubmitting', 'Add the tag before submitting.', '2011-06-23 08:07:18'),
(45, 1, 'en', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key is not yet configured.', '2011-06-23 08:07:18'),
(46, 1, 'en', 'backend', 'core', 'err', 'AlphaNumericCharactersOnly', 'Only alphanumeric characters are allowed.', '2011-06-23 08:07:18'),
(47, 1, 'en', 'backend', 'core', 'err', 'AuthorIsRequired', 'Please provide an author.', '2011-06-23 08:07:18'),
(48, 1, 'en', 'backend', 'core', 'err', 'BrowserNotSupported', '<p>You''re using an older browser that is not supported by Fork CMS. Use one of the following alternatives:</p><ul><li><a href="http://www.firefox.com/">Firefox</a>: a very good browser with a lot of free extensions.</li><li><a href="http://www.apple.com/safari">Safari</a>: one of the fastest and most advanced browsers. Good for Mac users.</li><li><a href="http://www.google.com/chrome">Chrome</a>: Google''s browser - also very fast.</li></li><a href="http://www.microsoft.com/windows/products/winfamily/ie/default.mspx">Internet Explorer*</a>: update to the latest version of Internet Explorer.</li></ul>', '2011-06-23 08:07:18'),
(49, 1, 'en', 'backend', 'core', 'err', 'CookiesNotEnabled', 'You need to enable cookies in order to use Fork CMS. Activate cookies and refresh this page.', '2011-06-23 08:07:18'),
(50, 1, 'en', 'backend', 'core', 'err', 'DateIsInvalid', 'Invalid date.', '2011-06-23 08:07:18'),
(51, 1, 'en', 'backend', 'core', 'err', 'DateRangeIsInvalid', 'Invalid date range.', '2011-06-23 08:07:18'),
(52, 1, 'en', 'backend', 'core', 'err', 'DebugModeIsActive', 'Debug-mode is active.', '2011-06-23 08:07:18'),
(53, 1, 'en', 'backend', 'core', 'err', 'EmailAlreadyExists', 'This e-mailaddress is in use.', '2011-06-23 08:07:18'),
(54, 1, 'en', 'backend', 'core', 'err', 'EmailIsInvalid', 'Please provide a valid e-mailaddress.', '2011-06-23 08:07:18'),
(55, 1, 'en', 'backend', 'core', 'err', 'EmailIsRequired', 'Please provide a valid e-mailaddress.', '2011-06-23 08:07:18'),
(56, 1, 'en', 'backend', 'core', 'err', 'EmailIsUnknown', 'This e-mailaddress is not in our database.', '2011-06-23 08:07:18'),
(57, 1, 'en', 'backend', 'core', 'err', 'EndDateIsInvalid', 'Invalid end date.', '2011-06-23 08:07:18'),
(58, 1, 'en', 'backend', 'core', 'err', 'ErrorWhileSendingEmail', 'Error while sending email.', '2011-06-23 08:07:18'),
(59, 1, 'en', 'backend', 'core', 'err', 'ExtensionNotAllowed', 'Invalid file type. (allowed: %1$s)', '2011-06-23 08:07:18'),
(60, 1, 'en', 'backend', 'core', 'err', 'FieldIsRequired', 'This field is required.', '2011-06-23 08:07:18'),
(61, 1, 'en', 'backend', 'core', 'err', 'ForkAPIKeys', 'Fork API-keys are not configured.', '2011-06-23 08:07:18'),
(62, 1, 'en', 'backend', 'core', 'err', 'FormError', 'Something went wrong', '2011-06-23 08:07:18'),
(63, 1, 'en', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key is not configured.', '2011-06-23 08:07:18'),
(64, 1, 'en', 'backend', 'core', 'err', 'InvalidAPIKey', 'Invalid API key.', '2011-06-23 08:07:18'),
(65, 1, 'en', 'backend', 'core', 'err', 'InvalidDomain', 'Invalid domain.', '2011-06-23 08:07:18'),
(66, 1, 'en', 'backend', 'core', 'err', 'InvalidEmailPasswordCombination', 'Your e-mail and password combination is incorrect. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Did you forget your password?</a>', '2011-06-23 08:07:18'),
(67, 1, 'en', 'backend', 'core', 'err', 'InvalidName', 'Invalid name.', '2011-06-23 08:07:18'),
(68, 1, 'en', 'backend', 'core', 'err', 'InvalidNumber', 'Invalid number.', '2011-06-23 08:07:18'),
(69, 1, 'en', 'backend', 'core', 'err', 'InvalidParameters', 'Invalid parameters.', '2011-06-23 08:07:18'),
(70, 1, 'en', 'backend', 'core', 'err', 'InvalidURL', 'Invalid URL.', '2011-06-23 08:07:18'),
(71, 1, 'en', 'backend', 'core', 'err', 'InvalidValue', 'Invalid value.', '2011-06-23 08:07:18'),
(72, 1, 'en', 'backend', 'core', 'err', 'JavascriptNotEnabled', 'To use Fork CMS, javascript needs to be enabled. Activate javascript and refresh this page.', '2011-06-23 08:07:18'),
(73, 1, 'en', 'backend', 'core', 'err', 'JPGGIFAndPNGOnly', 'Only jpg', '2011-06-23 08:07:18'),
(74, 1, 'en', 'backend', 'core', 'err', 'ModuleNotAllowed', 'You have insufficient rights for this module.', '2011-06-23 08:07:18'),
(75, 1, 'en', 'backend', 'core', 'err', 'NameIsRequired', 'Please provide a name.', '2011-06-23 08:07:18'),
(76, 1, 'en', 'backend', 'core', 'err', 'NicknameIsRequired', 'Please provide a publication name.', '2011-06-23 08:07:18'),
(77, 1, 'en', 'backend', 'core', 'err', 'NoCommentsSelected', 'No comments were selected.', '2011-06-23 08:07:18'),
(78, 1, 'en', 'backend', 'core', 'err', 'NoItemsSelected', 'No items were selected.', '2011-06-23 08:07:18'),
(79, 1, 'en', 'backend', 'core', 'err', 'NoModuleLinked', 'Cannot generate URL. Create a page that has this module attached to it.', '2011-06-23 08:07:18'),
(80, 1, 'en', 'backend', 'core', 'err', 'NonExisting', 'This item doesn''t exist.', '2011-06-23 08:07:18'),
(81, 1, 'en', 'backend', 'core', 'err', 'NoSelection', 'No items were selected.', '2011-06-23 08:07:18'),
(82, 1, 'en', 'backend', 'core', 'err', 'NoTemplatesAvailable', 'The selected theme does not yet have templates. Please create at least one template first.', '2011-06-23 08:07:18'),
(83, 1, 'en', 'backend', 'core', 'err', 'PasswordIsRequired', 'Please provide a password.', '2011-06-23 08:07:18'),
(84, 1, 'en', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Please repeat the desired password.', '2011-06-23 08:07:18'),
(85, 1, 'en', 'backend', 'core', 'err', 'PasswordsDontMatch', 'The passwords differ', '2011-06-23 08:07:18'),
(86, 1, 'en', 'backend', 'core', 'err', 'RobotsFileIsNotOK', 'robots.txt will block search-engines.', '2011-06-23 08:07:18'),
(87, 1, 'en', 'backend', 'core', 'err', 'RSSTitle', 'Blog RSS title is not configured. <a href="%1$s">Configure</a>', '2011-06-23 08:07:18'),
(88, 1, 'en', 'backend', 'core', 'err', 'SettingsForkAPIKeys', 'The Fork API-keys are not configured.', '2011-06-23 08:07:18'),
(89, 1, 'en', 'backend', 'core', 'err', 'SomethingWentWrong', 'Something went wrong.', '2011-06-23 08:07:18'),
(90, 1, 'en', 'backend', 'core', 'err', 'StartDateIsInvalid', 'Invalid start date.', '2011-06-23 08:07:18'),
(91, 1, 'en', 'backend', 'core', 'err', 'SurnameIsRequired', 'Please provide a last name.', '2011-06-23 08:07:18'),
(92, 1, 'en', 'backend', 'core', 'err', 'TooManyLoginAttempts', 'Too many login attempts. Click the forgot password link if you forgot your password.', '2011-06-23 08:07:18'),
(93, 1, 'en', 'backend', 'core', 'err', 'TimeIsInvalid', 'Invalid time.', '2011-06-23 08:07:18'),
(94, 1, 'en', 'backend', 'core', 'err', 'TitleIsRequired', 'Provide a title.', '2011-06-23 08:07:18'),
(95, 1, 'en', 'backend', 'core', 'err', 'URLAlreadyExists', 'This URL already exists.', '2011-06-23 08:07:18'),
(96, 1, 'en', 'backend', 'core', 'err', 'ValuesDontMatch', 'The values don''t match.', '2011-06-23 08:07:18'),
(97, 1, 'en', 'backend', 'core', 'err', 'XMLFilesOnly', 'Only XMl files are allowed.', '2011-06-23 08:07:18'),
(98, 1, 'en', 'backend', 'core', 'lbl', 'AccountManagement', 'account management', '2011-06-23 08:07:18'),
(99, 1, 'en', 'backend', 'core', 'lbl', 'Active', 'active', '2011-06-23 08:07:18'),
(100, 1, 'en', 'backend', 'core', 'lbl', 'Add', 'add', '2011-06-23 08:07:18'),
(101, 1, 'en', 'backend', 'core', 'lbl', 'AddCategory', 'add category', '2011-06-23 08:07:18'),
(102, 1, 'en', 'backend', 'core', 'lbl', 'AddTemplate', 'add template', '2011-06-23 08:07:18'),
(103, 1, 'en', 'backend', 'core', 'lbl', 'Advanced', 'advanced', '2011-06-23 08:07:18'),
(104, 1, 'en', 'backend', 'core', 'lbl', 'AllComments', 'all comments', '2011-06-23 08:07:18'),
(105, 1, 'en', 'backend', 'core', 'lbl', 'AllowComments', 'allow comments', '2011-06-23 08:07:18'),
(106, 1, 'en', 'backend', 'core', 'lbl', 'AllPages', 'all pages', '2011-06-23 08:07:18'),
(107, 1, 'en', 'backend', 'core', 'lbl', 'Amount', 'amount', '2011-06-23 08:07:18'),
(108, 1, 'en', 'backend', 'core', 'lbl', 'Analyse', 'analyse', '2011-06-23 08:07:18'),
(109, 1, 'en', 'backend', 'core', 'lbl', 'Analysis', 'analysis', '2011-06-23 08:07:18'),
(110, 1, 'en', 'backend', 'core', 'lbl', 'Analytics', 'analytics', '2011-06-23 08:07:18'),
(111, 1, 'en', 'backend', 'core', 'lbl', 'APIKey', 'API key', '2011-06-23 08:07:18'),
(112, 1, 'en', 'backend', 'core', 'lbl', 'APIKeys', 'API keys', '2011-06-23 08:07:18'),
(113, 1, 'en', 'backend', 'core', 'lbl', 'APIURL', 'API URL', '2011-06-23 08:07:18'),
(114, 1, 'en', 'backend', 'core', 'lbl', 'Application', 'application', '2011-06-23 08:07:18'),
(115, 1, 'en', 'backend', 'core', 'lbl', 'Approve', 'approve', '2011-06-23 08:07:18'),
(116, 1, 'en', 'backend', 'core', 'lbl', 'Archive', 'archive', '2011-06-23 08:07:18'),
(117, 1, 'en', 'backend', 'core', 'lbl', 'Archived', 'archived', '2011-06-23 08:07:18'),
(118, 1, 'en', 'backend', 'core', 'lbl', 'Article', 'article', '2011-06-23 08:07:18'),
(119, 1, 'en', 'backend', 'core', 'lbl', 'Articles', 'articles', '2011-06-23 08:07:18'),
(120, 1, 'en', 'backend', 'core', 'lbl', 'At', 'at', '2011-06-23 08:07:18'),
(121, 1, 'en', 'backend', 'core', 'lbl', 'Authentication', 'authentication', '2011-06-23 08:07:18'),
(122, 1, 'en', 'backend', 'core', 'lbl', 'Author', 'author', '2011-06-23 08:07:18'),
(123, 1, 'en', 'backend', 'core', 'lbl', 'Avatar', 'avatar', '2011-06-23 08:07:18'),
(124, 1, 'en', 'backend', 'core', 'lbl', 'Back', 'back', '2011-06-23 08:07:18'),
(125, 1, 'en', 'backend', 'core', 'lbl', 'Backend', 'backend', '2011-06-23 08:07:18'),
(126, 1, 'en', 'backend', 'core', 'lbl', 'Block', 'block', '2011-06-23 08:07:18'),
(127, 1, 'en', 'backend', 'core', 'lbl', 'BrowserNotSupported', 'browser not supported', '2011-06-23 08:07:18'),
(128, 1, 'en', 'backend', 'core', 'lbl', 'By', 'by', '2011-06-23 08:07:18'),
(129, 1, 'en', 'backend', 'core', 'lbl', 'Cancel', 'cancel', '2011-06-23 08:07:18'),
(130, 1, 'en', 'backend', 'core', 'lbl', 'Categories', 'categories', '2011-06-23 08:07:18'),
(131, 1, 'en', 'backend', 'core', 'lbl', 'Category', 'category', '2011-06-23 08:07:18'),
(132, 1, 'en', 'backend', 'core', 'lbl', 'ChangePassword', 'change password', '2011-06-23 08:07:18'),
(133, 1, 'en', 'backend', 'core', 'lbl', 'ChooseALanguage', 'choose a language', '2011-06-23 08:07:18'),
(134, 1, 'en', 'backend', 'core', 'lbl', 'ChooseAModule', 'choose a module', '2011-06-23 08:07:18'),
(135, 1, 'en', 'backend', 'core', 'lbl', 'ChooseAnApplication', 'choose an application', '2011-06-23 08:07:18'),
(136, 1, 'en', 'backend', 'core', 'lbl', 'ChooseATemplate', 'choose a template', '2011-06-23 08:07:18'),
(137, 1, 'en', 'backend', 'core', 'lbl', 'ChooseAType', 'choose a type', '2011-06-23 08:07:18'),
(138, 1, 'en', 'backend', 'core', 'lbl', 'ChooseContent', 'choose content', '2011-06-23 08:07:18'),
(139, 1, 'en', 'backend', 'core', 'lbl', 'Comment', 'comment', '2011-06-23 08:07:18'),
(140, 1, 'en', 'backend', 'core', 'lbl', 'Comments', 'comments', '2011-06-23 08:07:18'),
(141, 1, 'en', 'backend', 'core', 'lbl', 'ConfirmPassword', 'confirm password', '2011-06-23 08:07:18'),
(142, 1, 'en', 'backend', 'core', 'lbl', 'Contact', 'contact', '2011-06-23 08:07:18'),
(143, 1, 'en', 'backend', 'core', 'lbl', 'ContactForm', 'contact form', '2011-06-23 08:07:18'),
(144, 1, 'en', 'backend', 'core', 'lbl', 'Content', 'content', '2011-06-23 08:07:18'),
(145, 1, 'en', 'backend', 'core', 'lbl', 'ContentBlocks', 'content blocks', '2011-06-23 08:07:18'),
(146, 1, 'en', 'backend', 'core', 'lbl', 'Core', 'core', '2011-06-23 08:07:18'),
(147, 1, 'en', 'backend', 'core', 'lbl', 'CustomURL', 'custom URL', '2011-06-23 08:07:18'),
(148, 1, 'en', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard', '2011-06-23 08:07:18'),
(149, 1, 'en', 'backend', 'core', 'lbl', 'Date', 'date', '2011-06-23 08:07:18'),
(150, 1, 'en', 'backend', 'core', 'lbl', 'DateAndTime', 'date and time', '2011-06-23 08:07:18'),
(151, 1, 'en', 'backend', 'core', 'lbl', 'DateFormat', 'date format', '2011-06-23 08:07:18'),
(152, 1, 'en', 'backend', 'core', 'lbl', 'Dear', 'dear', '2011-06-23 08:07:18'),
(153, 1, 'en', 'backend', 'core', 'lbl', 'DebugMode', 'debug mode', '2011-06-23 08:07:18'),
(154, 1, 'en', 'backend', 'core', 'lbl', 'Default', 'default', '2011-06-23 08:07:18'),
(155, 1, 'en', 'backend', 'core', 'lbl', 'Delete', 'delete', '2011-06-23 08:07:18'),
(156, 1, 'en', 'backend', 'core', 'lbl', 'DeleteThisTag', 'delete this tag', '2011-06-23 08:07:18'),
(157, 1, 'en', 'backend', 'core', 'lbl', 'Description', 'description', '2011-06-23 08:07:18'),
(158, 1, 'en', 'backend', 'core', 'lbl', 'Developer', 'developer', '2011-06-23 08:07:18'),
(159, 1, 'en', 'backend', 'core', 'lbl', 'Domains', 'domains', '2011-06-23 08:07:18'),
(160, 1, 'en', 'backend', 'core', 'lbl', 'Done', 'done', '2011-06-23 08:07:18'),
(161, 1, 'en', 'backend', 'core', 'lbl', 'Draft', 'draft', '2011-06-23 08:07:18'),
(162, 1, 'en', 'backend', 'core', 'lbl', 'Drafts', 'drafts', '2011-06-23 08:07:18'),
(163, 1, 'en', 'backend', 'core', 'lbl', 'Edit', 'edit', '2011-06-23 08:07:18'),
(164, 1, 'en', 'backend', 'core', 'lbl', 'EditedOn', 'edited on', '2011-06-23 08:07:18'),
(165, 1, 'en', 'backend', 'core', 'lbl', 'Editor', 'editor', '2011-06-23 08:07:18'),
(166, 1, 'en', 'backend', 'core', 'lbl', 'EditProfile', 'edit profile', '2011-06-23 08:07:18'),
(167, 1, 'en', 'backend', 'core', 'lbl', 'EditTemplate', 'edit template', '2011-06-23 08:07:18'),
(168, 1, 'en', 'backend', 'core', 'lbl', 'Email', 'e-mail', '2011-06-23 08:07:18'),
(169, 1, 'en', 'backend', 'core', 'lbl', 'EnableModeration', 'enable moderation', '2011-06-23 08:07:18'),
(170, 1, 'en', 'backend', 'core', 'lbl', 'EndDate', 'end date', '2011-06-23 08:07:18'),
(171, 1, 'en', 'backend', 'core', 'lbl', 'Error', 'error', '2011-06-23 08:07:18'),
(172, 1, 'en', 'backend', 'core', 'lbl', 'Example', 'example', '2011-06-23 08:07:18'),
(173, 1, 'en', 'backend', 'core', 'lbl', 'Execute', 'execute', '2011-06-23 08:07:18'),
(174, 1, 'en', 'backend', 'core', 'lbl', 'ExitPages', 'exit pages', '2011-06-23 08:07:18'),
(175, 1, 'en', 'backend', 'core', 'lbl', 'Export', 'export', '2011-06-23 08:07:18'),
(176, 1, 'en', 'backend', 'core', 'lbl', 'ExtraMetaTags', 'extra metatags', '2011-06-23 08:07:18'),
(177, 1, 'en', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL', '2011-06-23 08:07:18'),
(178, 1, 'en', 'backend', 'core', 'lbl', 'File', 'file', '2011-06-23 08:07:18'),
(179, 1, 'en', 'backend', 'core', 'lbl', 'Filename', 'filename', '2011-06-23 08:07:18'),
(180, 1, 'en', 'backend', 'core', 'lbl', 'FilterCommentsForSpam', 'filter comments for spam', '2011-06-23 08:07:18'),
(181, 1, 'en', 'backend', 'core', 'lbl', 'From', 'from', '2011-06-23 08:07:18'),
(182, 1, 'en', 'backend', 'core', 'lbl', 'Frontend', 'frontend', '2011-06-23 08:07:18'),
(183, 1, 'en', 'backend', 'core', 'lbl', 'General', 'general', '2011-06-23 08:07:18'),
(184, 1, 'en', 'backend', 'core', 'lbl', 'GeneralSettings', 'general settings', '2011-06-23 08:07:18'),
(185, 1, 'en', 'backend', 'core', 'lbl', 'Generate', 'generate', '2011-06-23 08:07:18'),
(186, 1, 'en', 'backend', 'core', 'lbl', 'GoToPage', 'go to page', '2011-06-23 08:07:18'),
(187, 1, 'en', 'backend', 'core', 'lbl', 'Group', 'group', '2011-06-23 08:07:18'),
(188, 1, 'en', 'backend', 'core', 'lbl', 'Hidden', 'hidden', '2011-06-23 08:07:18'),
(189, 1, 'en', 'backend', 'core', 'lbl', 'Home', 'home', '2011-06-23 08:07:18'),
(190, 1, 'en', 'backend', 'core', 'lbl', 'Image', 'image', '2011-06-23 08:07:18'),
(191, 1, 'en', 'backend', 'core', 'lbl', 'Images', 'images', '2011-06-23 08:07:18'),
(192, 1, 'en', 'backend', 'core', 'lbl', 'Import', 'import', '2011-06-23 08:07:18'),
(193, 1, 'en', 'backend', 'core', 'lbl', 'Interface', 'interface', '2011-06-23 08:07:18'),
(194, 1, 'en', 'backend', 'core', 'lbl', 'InterfacePreferences', 'interface preferences', '2011-06-23 08:07:18'),
(195, 1, 'en', 'backend', 'core', 'lbl', 'IP', 'IP', '2011-06-23 08:07:18'),
(196, 1, 'en', 'backend', 'core', 'lbl', 'ItemsPerPage', 'items per page', '2011-06-23 08:07:18'),
(197, 1, 'en', 'backend', 'core', 'lbl', 'Keyword', 'keyword', '2011-06-23 08:07:18'),
(198, 1, 'en', 'backend', 'core', 'lbl', 'Keywords', 'keywords', '2011-06-23 08:07:18'),
(199, 1, 'en', 'backend', 'core', 'lbl', 'Label', 'label', '2011-06-23 08:07:18'),
(200, 1, 'en', 'backend', 'core', 'lbl', 'LandingPages', 'landing pages', '2011-06-23 08:07:18'),
(201, 1, 'en', 'backend', 'core', 'lbl', 'Language', 'language', '2011-06-23 08:07:18'),
(202, 1, 'en', 'backend', 'core', 'lbl', 'Languages', 'languages', '2011-06-23 08:07:18'),
(203, 1, 'en', 'backend', 'core', 'lbl', 'LastEdited', 'last edited', '2011-06-23 08:07:18'),
(204, 1, 'en', 'backend', 'core', 'lbl', 'LastEditedOn', 'last edited on', '2011-06-23 08:07:18'),
(205, 1, 'en', 'backend', 'core', 'lbl', 'LastSaved', 'last saved', '2011-06-23 08:07:18'),
(206, 1, 'en', 'backend', 'core', 'lbl', 'LatestComments', 'latest comments', '2011-06-23 08:07:18'),
(207, 1, 'en', 'backend', 'core', 'lbl', 'Layout', 'layout', '2011-06-23 08:07:18'),
(208, 1, 'en', 'backend', 'core', 'lbl', 'Loading', 'loading', '2011-06-23 08:07:18'),
(209, 1, 'en', 'backend', 'core', 'lbl', 'Locale', 'locale', '2011-06-23 08:07:18'),
(210, 1, 'en', 'backend', 'core', 'lbl', 'LoginDetails', 'login details', '2011-06-23 08:07:18'),
(211, 1, 'en', 'backend', 'core', 'lbl', 'LongDateFormat', 'long date format', '2011-06-23 08:07:18'),
(212, 1, 'en', 'backend', 'core', 'lbl', 'MainContent', 'main content', '2011-06-23 08:07:18'),
(213, 1, 'en', 'backend', 'core', 'lbl', 'MarkAsSpam', 'mark as spam', '2011-06-23 08:07:18'),
(214, 1, 'en', 'backend', 'core', 'lbl', 'Marketing', 'marketing', '2011-06-23 08:07:18'),
(215, 1, 'en', 'backend', 'core', 'lbl', 'Meta', 'meta', '2011-06-23 08:07:18'),
(216, 1, 'en', 'backend', 'core', 'lbl', 'MetaData', 'metadata', '2011-06-23 08:07:18'),
(217, 1, 'en', 'backend', 'core', 'lbl', 'MetaInformation', 'meta information', '2011-06-23 08:07:18'),
(218, 1, 'en', 'backend', 'core', 'lbl', 'MetaNavigation', 'meta navigation', '2011-06-23 08:07:18'),
(219, 1, 'en', 'backend', 'core', 'lbl', 'Moderate', 'moderate', '2011-06-23 08:07:18'),
(220, 1, 'en', 'backend', 'core', 'lbl', 'Moderation', 'moderation', '2011-06-23 08:07:18'),
(221, 1, 'en', 'backend', 'core', 'lbl', 'Module', 'module', '2011-06-23 08:07:18'),
(222, 1, 'en', 'backend', 'core', 'lbl', 'Modules', 'modules', '2011-06-23 08:07:18'),
(223, 1, 'en', 'backend', 'core', 'lbl', 'ModuleSettings', 'module settings', '2011-06-23 08:07:18'),
(224, 1, 'en', 'backend', 'core', 'lbl', 'Move', 'move', '2011-06-23 08:07:18'),
(225, 1, 'en', 'backend', 'core', 'lbl', 'MoveToModeration', 'move to moderation', '2011-06-23 08:07:18'),
(226, 1, 'en', 'backend', 'core', 'lbl', 'MoveToPublished', 'move to published', '2011-06-23 08:07:18'),
(227, 1, 'en', 'backend', 'core', 'lbl', 'MoveToSpam', 'move to spam', '2011-06-23 08:07:18'),
(228, 1, 'en', 'backend', 'core', 'lbl', 'Name', 'name', '2011-06-23 08:07:18'),
(229, 1, 'en', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigation title', '2011-06-23 08:07:18'),
(230, 1, 'en', 'backend', 'core', 'lbl', 'NewPassword', 'new password', '2011-06-23 08:07:18'),
(231, 1, 'en', 'backend', 'core', 'lbl', 'News', 'news', '2011-06-23 08:07:18'),
(232, 1, 'en', 'backend', 'core', 'lbl', 'Next', 'next', '2011-06-23 08:07:18'),
(233, 1, 'en', 'backend', 'core', 'lbl', 'NextPage', 'next page', '2011-06-23 08:07:18'),
(234, 1, 'en', 'backend', 'core', 'lbl', 'Nickname', 'publication name', '2011-06-23 08:07:18'),
(235, 1, 'en', 'backend', 'core', 'lbl', 'None', 'none', '2011-06-23 08:07:18'),
(236, 1, 'en', 'backend', 'core', 'lbl', 'Notifications', 'notifications', '2011-06-23 08:07:18'),
(237, 1, 'en', 'backend', 'core', 'lbl', 'NoTheme', 'no theme', '2011-06-23 08:07:18'),
(238, 1, 'en', 'backend', 'core', 'lbl', 'NumberFormat', 'number format', '2011-06-23 08:07:18'),
(239, 1, 'en', 'backend', 'core', 'lbl', 'NumberOfPositions', 'number of positions', '2011-06-23 08:07:18'),
(240, 1, 'en', 'backend', 'core', 'lbl', 'Numbers', 'numbers', '2011-06-23 08:07:18'),
(241, 1, 'en', 'backend', 'core', 'lbl', 'OK', 'OK', '2011-06-23 08:07:18'),
(242, 1, 'en', 'backend', 'core', 'lbl', 'Or', 'or', '2011-06-23 08:07:18'),
(243, 1, 'en', 'backend', 'core', 'lbl', 'Overview', 'overview', '2011-06-23 08:07:18'),
(244, 1, 'en', 'backend', 'core', 'lbl', 'Page', 'page', '2011-06-23 08:07:18'),
(245, 1, 'en', 'backend', 'core', 'lbl', 'Pages', 'pages', '2011-06-23 08:07:18'),
(246, 1, 'en', 'backend', 'core', 'lbl', 'PageTitle', 'pagetitle', '2011-06-23 08:07:18'),
(247, 1, 'en', 'backend', 'core', 'lbl', 'Pageviews', 'pageviews', '2011-06-23 08:07:18'),
(248, 1, 'en', 'backend', 'core', 'lbl', 'Pagination', 'pagination', '2011-06-23 08:07:18'),
(249, 1, 'en', 'backend', 'core', 'lbl', 'Password', 'password', '2011-06-23 08:07:18'),
(250, 1, 'en', 'backend', 'core', 'lbl', 'PerDay', 'per day', '2011-06-23 08:07:18'),
(251, 1, 'en', 'backend', 'core', 'lbl', 'PerVisit', 'per visit', '2011-06-23 08:07:18'),
(252, 1, 'en', 'backend', 'core', 'lbl', 'Permissions', 'permissions', '2011-06-23 08:07:18'),
(253, 1, 'en', 'backend', 'core', 'lbl', 'PersonalInformation', 'personal information', '2011-06-23 08:07:18'),
(254, 1, 'en', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices', '2011-06-23 08:07:18'),
(255, 1, 'en', 'backend', 'core', 'lbl', 'Port', 'port', '2011-06-23 08:07:18'),
(256, 1, 'en', 'backend', 'core', 'lbl', 'Preview', 'preview', '2011-06-23 08:07:18'),
(257, 1, 'en', 'backend', 'core', 'lbl', 'Previous', 'previous', '2011-06-23 08:07:18'),
(258, 1, 'en', 'backend', 'core', 'lbl', 'PreviousPage', 'previous page', '2011-06-23 08:07:18'),
(259, 1, 'en', 'backend', 'core', 'lbl', 'PreviousVersions', 'previous versions', '2011-06-23 08:07:18'),
(260, 1, 'en', 'backend', 'core', 'lbl', 'Profile', 'profile', '2011-06-23 08:07:18'),
(261, 1, 'en', 'backend', 'core', 'lbl', 'Publish', 'publish', '2011-06-23 08:07:18'),
(262, 1, 'en', 'backend', 'core', 'lbl', 'Published', 'published', '2011-06-23 08:07:18'),
(263, 1, 'en', 'backend', 'core', 'lbl', 'PublishedArticles', 'published articles', '2011-06-23 08:07:18'),
(264, 1, 'en', 'backend', 'core', 'lbl', 'PublishedOn', 'published on', '2011-06-23 08:07:18'),
(265, 1, 'en', 'backend', 'core', 'lbl', 'PublishOn', 'publish on', '2011-06-23 08:07:18'),
(266, 1, 'en', 'backend', 'core', 'lbl', 'RecentArticlesFull', 'recent articles (full)', '2011-06-23 08:07:18'),
(267, 1, 'en', 'backend', 'core', 'lbl', 'RecentArticlesList', 'recent articles (list)', '2011-06-23 08:07:18'),
(268, 1, 'en', 'backend', 'core', 'lbl', 'RecentComments', 'recent comments', '2011-06-23 08:07:18'),
(269, 1, 'en', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recently edited', '2011-06-23 08:07:18'),
(270, 1, 'en', 'backend', 'core', 'lbl', 'RecentVisits', 'recent visits', '2011-06-23 08:07:18'),
(271, 1, 'en', 'backend', 'core', 'lbl', 'ReferenceCode', 'reference code', '2011-06-23 08:07:18'),
(272, 1, 'en', 'backend', 'core', 'lbl', 'Referrer', 'referrer', '2011-06-23 08:07:18'),
(273, 1, 'en', 'backend', 'core', 'lbl', 'RepeatPassword', 'repeat password', '2011-06-23 08:07:18'),
(274, 1, 'en', 'backend', 'core', 'lbl', 'ReplyTo', 'reply-to', '2011-06-23 08:07:18'),
(275, 1, 'en', 'backend', 'core', 'lbl', 'RequiredField', 'required field', '2011-06-23 08:07:18'),
(276, 1, 'en', 'backend', 'core', 'lbl', 'ResetAndSignIn', 'reset and sign in', '2011-06-23 08:07:18'),
(277, 1, 'en', 'backend', 'core', 'lbl', 'ResetYourPassword', 'reset your password', '2011-06-23 08:07:18'),
(278, 1, 'en', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed', '2011-06-23 08:07:18'),
(279, 1, 'en', 'backend', 'core', 'lbl', 'Save', 'save', '2011-06-23 08:07:18'),
(280, 1, 'en', 'backend', 'core', 'lbl', 'SaveDraft', 'save draft', '2011-06-23 08:07:18'),
(281, 1, 'en', 'backend', 'core', 'lbl', 'Scripts', 'scripts', '2011-06-23 08:07:18'),
(282, 1, 'en', 'backend', 'core', 'lbl', 'Search', 'search', '2011-06-23 08:07:18'),
(283, 1, 'en', 'backend', 'core', 'lbl', 'SearchAgain', 'search again', '2011-06-23 08:07:18'),
(284, 1, 'en', 'backend', 'core', 'lbl', 'SearchForm', 'search form', '2011-06-23 08:07:18'),
(285, 1, 'en', 'backend', 'core', 'lbl', 'Send', 'send', '2011-06-23 08:07:18'),
(286, 1, 'en', 'backend', 'core', 'lbl', 'SendingEmails', 'sending e-mails', '2011-06-23 08:07:18'),
(287, 1, 'en', 'backend', 'core', 'lbl', 'SEO', 'SEO', '2011-06-23 08:07:18'),
(288, 1, 'en', 'backend', 'core', 'lbl', 'Server', 'server', '2011-06-23 08:07:18'),
(289, 1, 'en', 'backend', 'core', 'lbl', 'Settings', 'settings', '2011-06-23 08:07:18'),
(290, 1, 'en', 'backend', 'core', 'lbl', 'ShortDateFormat', 'short date format', '2011-06-23 08:07:18'),
(291, 1, 'en', 'backend', 'core', 'lbl', 'SignIn', 'log in', '2011-06-23 08:07:18'),
(292, 1, 'en', 'backend', 'core', 'lbl', 'SignOut', 'sign out', '2011-06-23 08:07:18'),
(293, 1, 'en', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap', '2011-06-23 08:07:18'),
(294, 1, 'en', 'backend', 'core', 'lbl', 'SMTP', 'SMTP', '2011-06-23 08:07:18'),
(295, 1, 'en', 'backend', 'core', 'lbl', 'SortAscending', 'sort ascending', '2011-06-23 08:07:18'),
(296, 1, 'en', 'backend', 'core', 'lbl', 'SortDescending', 'sort descending', '2011-06-23 08:07:18'),
(297, 1, 'en', 'backend', 'core', 'lbl', 'SortedAscending', 'sorted ascending', '2011-06-23 08:07:18'),
(298, 1, 'en', 'backend', 'core', 'lbl', 'SortedDescending', 'sorted descending', '2011-06-23 08:07:18'),
(299, 1, 'en', 'backend', 'core', 'lbl', 'Spam', 'spam', '2011-06-23 08:07:18'),
(300, 1, 'en', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter', '2011-06-23 08:07:18'),
(301, 1, 'en', 'backend', 'core', 'lbl', 'StartDate', 'start date', '2011-06-23 08:07:18'),
(302, 1, 'en', 'backend', 'core', 'lbl', 'Statistics', 'statistics', '2011-06-23 08:07:18'),
(303, 1, 'en', 'backend', 'core', 'lbl', 'Status', 'status', '2011-06-23 08:07:18'),
(304, 1, 'en', 'backend', 'core', 'lbl', 'Strong', 'strong', '2011-06-23 08:07:18'),
(305, 1, 'en', 'backend', 'core', 'lbl', 'Summary', 'summary', '2011-06-23 08:07:18'),
(306, 1, 'en', 'backend', 'core', 'lbl', 'Surname', 'surname', '2011-06-23 08:07:18'),
(307, 1, 'en', 'backend', 'core', 'lbl', 'Synonym', 'synonym', '2011-06-23 08:07:18'),
(308, 1, 'en', 'backend', 'core', 'lbl', 'Synonyms', 'synonyms', '2011-06-23 08:07:18'),
(309, 1, 'en', 'backend', 'core', 'lbl', 'Tags', 'tags', '2011-06-23 08:07:18'),
(310, 1, 'en', 'backend', 'core', 'lbl', 'Template', 'template', '2011-06-23 08:07:18'),
(311, 1, 'en', 'backend', 'core', 'lbl', 'Templates', 'templates', '2011-06-23 08:07:18'),
(312, 1, 'en', 'backend', 'core', 'lbl', 'Term', 'term', '2011-06-23 08:07:18'),
(313, 1, 'en', 'backend', 'core', 'lbl', 'Text', 'text', '2011-06-23 08:07:18'),
(314, 1, 'en', 'backend', 'core', 'lbl', 'Themes', 'themes', '2011-06-23 08:07:18'),
(315, 1, 'en', 'backend', 'core', 'lbl', 'ThemesSelection', 'theme selection', '2011-06-23 08:07:18'),
(316, 1, 'en', 'backend', 'core', 'lbl', 'Till', 'till', '2011-06-23 08:07:18'),
(317, 1, 'en', 'backend', 'core', 'lbl', 'TimeFormat', 'time format', '2011-06-23 08:07:18'),
(318, 1, 'en', 'backend', 'core', 'lbl', 'Title', 'title', '2011-06-23 08:07:18'),
(319, 1, 'en', 'backend', 'core', 'lbl', 'Titles', 'titles', '2011-06-23 08:07:18'),
(320, 1, 'en', 'backend', 'core', 'lbl', 'To', 'to', '2011-06-23 08:07:18'),
(321, 1, 'en', 'backend', 'core', 'lbl', 'Today', 'today', '2011-06-23 08:07:18'),
(322, 1, 'en', 'backend', 'core', 'lbl', 'TrafficSources', 'traffic sources', '2011-06-23 08:07:18'),
(323, 1, 'en', 'backend', 'core', 'lbl', 'Translation', 'translation', '2011-06-23 08:07:18'),
(324, 1, 'en', 'backend', 'core', 'lbl', 'Translations', 'translations', '2011-06-23 08:07:18'),
(325, 1, 'en', 'backend', 'core', 'lbl', 'Type', 'type', '2011-06-23 08:07:18'),
(326, 1, 'en', 'backend', 'core', 'lbl', 'UpdateFilter', 'update filter', '2011-06-23 08:07:18'),
(327, 1, 'en', 'backend', 'core', 'lbl', 'URL', 'URL', '2011-06-23 08:07:18'),
(328, 1, 'en', 'backend', 'core', 'lbl', 'UsedIn', 'used in', '2011-06-23 08:07:18'),
(329, 1, 'en', 'backend', 'core', 'lbl', 'Userguide', 'userguide', '2011-06-23 08:07:18'),
(330, 1, 'en', 'backend', 'core', 'lbl', 'Username', 'username', '2011-06-23 08:07:18'),
(331, 1, 'en', 'backend', 'core', 'lbl', 'Users', 'users', '2011-06-23 08:07:18'),
(332, 1, 'en', 'backend', 'core', 'lbl', 'UseThisDraft', 'use this draft', '2011-06-23 08:07:18'),
(333, 1, 'en', 'backend', 'core', 'lbl', 'UseThisVersion', 'use this version', '2011-06-23 08:07:18'),
(334, 1, 'en', 'backend', 'core', 'lbl', 'Value', 'value', '2011-06-23 08:07:18'),
(335, 1, 'en', 'backend', 'core', 'lbl', 'View', 'view', '2011-06-23 08:07:18'),
(336, 1, 'en', 'backend', 'core', 'lbl', 'ViewReport', 'view report', '2011-06-23 08:07:18'),
(337, 1, 'en', 'backend', 'core', 'lbl', 'VisibleOnSite', 'visible on site', '2011-06-23 08:07:18'),
(338, 1, 'en', 'backend', 'core', 'lbl', 'Visitors', 'visitors', '2011-06-23 08:07:18'),
(339, 1, 'en', 'backend', 'core', 'lbl', 'VisitWebsite', 'visit website', '2011-06-23 08:07:18'),
(340, 1, 'en', 'backend', 'core', 'lbl', 'WaitingForModeration', 'waiting for moderation', '2011-06-23 08:07:18'),
(341, 1, 'en', 'backend', 'core', 'lbl', 'Weak', 'weak', '2011-06-23 08:07:18'),
(342, 1, 'en', 'backend', 'core', 'lbl', 'WebmasterEmail', 'e-mail webmaster', '2011-06-23 08:07:18'),
(343, 1, 'en', 'backend', 'core', 'lbl', 'Website', 'website', '2011-06-23 08:07:18'),
(344, 1, 'en', 'backend', 'core', 'lbl', 'WebsiteTitle', 'website title', '2011-06-23 08:07:18'),
(345, 1, 'en', 'backend', 'core', 'lbl', 'Weight', 'weight', '2011-06-23 08:07:18'),
(346, 1, 'en', 'backend', 'core', 'lbl', 'WhichModule', 'which module', '2011-06-23 08:07:18'),
(347, 1, 'en', 'backend', 'core', 'lbl', 'WhichWidget', 'which widget', '2011-06-23 08:07:18'),
(348, 1, 'en', 'backend', 'core', 'lbl', 'Widget', 'widget', '2011-06-23 08:07:18'),
(349, 1, 'en', 'backend', 'core', 'lbl', 'Widgets', 'widgets', '2011-06-23 08:07:18'),
(350, 1, 'en', 'backend', 'core', 'lbl', 'WithSelected', 'with selected', '2011-06-23 08:07:18'),
(351, 1, 'en', 'backend', 'core', 'msg', 'ACT', 'action', '2011-06-23 08:07:18'),
(352, 1, 'en', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activate <code>rel="nofollow"</code>', '2011-06-23 08:07:18'),
(353, 1, 'en', 'backend', 'core', 'msg', 'Added', 'The item was added.', '2011-06-23 08:07:18'),
(354, 1, 'en', 'backend', 'core', 'msg', 'AddedCategory', 'The category "%1$s" was added.', '2011-06-23 08:07:18'),
(355, 1, 'en', 'backend', 'core', 'msg', 'ClickToEdit', 'Click to edit', '2011-06-23 08:07:18'),
(356, 1, 'en', 'backend', 'core', 'msg', 'CommentDeleted', 'The comment was deleted.', '2011-06-23 08:07:18'),
(357, 1, 'en', 'backend', 'core', 'msg', 'CommentMovedModeration', 'The comment was moved to moderation.', '2011-06-23 08:07:18'),
(358, 1, 'en', 'backend', 'core', 'msg', 'CommentMovedPublished', 'The comment was published.', '2011-06-23 08:07:18'),
(359, 1, 'en', 'backend', 'core', 'msg', 'CommentMovedSpam', 'The comment was marked as spam.', '2011-06-23 08:07:18'),
(360, 1, 'en', 'backend', 'core', 'msg', 'CommentsDeleted', 'The comments were deleted.', '2011-06-23 08:07:18'),
(361, 1, 'en', 'backend', 'core', 'msg', 'CommentsMovedModeration', 'The comments were moved to moderation.', '2011-06-23 08:07:18'),
(362, 1, 'en', 'backend', 'core', 'msg', 'CommentsMovedPublished', 'The comments were published.', '2011-06-23 08:07:18'),
(363, 1, 'en', 'backend', 'core', 'msg', 'CommentsMovedSpam', 'The comments were marked as spam.', '2011-06-23 08:07:18'),
(364, 1, 'en', 'backend', 'core', 'msg', 'CommentsToModerate', '%1$s comment(s) to moderate.', '2011-06-23 08:07:18'),
(365, 1, 'en', 'backend', 'core', 'msg', 'ConfigurationError', 'Some settings aren''t configured yet:', '2011-06-23 08:07:18'),
(366, 1, 'en', 'backend', 'core', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the item "%1$s"?', '2011-06-23 08:07:18'),
(367, 1, 'en', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Are you sure you want to delete the category "%1$s"?', '2011-06-23 08:07:18'),
(368, 1, 'en', 'backend', 'core', 'msg', 'ConfirmMassDelete', 'Are your sure you want to delete this/these item(s)?', '2011-06-23 08:07:18'),
(369, 1, 'en', 'backend', 'core', 'msg', 'ConfirmMassSpam', 'Are your sure you want to mark this/these item(s) as spam?', '2011-06-23 08:07:18'),
(370, 1, 'en', 'backend', 'core', 'msg', 'DE', 'German', '2011-06-23 08:07:18'),
(371, 1, 'en', 'backend', 'core', 'msg', 'Deleted', 'The item was deleted.', '2011-06-23 08:07:18'),
(372, 1, 'en', 'backend', 'core', 'msg', 'DeletedCategory', 'The category "%1$s" was deleted.', '2011-06-23 08:07:18'),
(373, 1, 'en', 'backend', 'core', 'msg', 'EditCategory', 'edit category "%1$s"', '2011-06-23 08:07:18'),
(374, 1, 'en', 'backend', 'core', 'msg', 'EditComment', 'edit comment', '2011-06-23 08:07:18'),
(375, 1, 'en', 'backend', 'core', 'msg', 'Edited', 'The item was saved.', '2011-06-23 08:07:18'),
(376, 1, 'en', 'backend', 'core', 'msg', 'EditedCategory', 'The category "%1$s" was saved.', '2011-06-23 08:07:18'),
(377, 1, 'en', 'backend', 'core', 'msg', 'EditorImagesWithoutAlt', 'There are images without an alt-attribute.', '2011-06-23 08:07:18'),
(378, 1, 'en', 'backend', 'core', 'msg', 'EditorInvalidLinks', 'There are invalid links.', '2011-06-23 08:07:18'),
(379, 1, 'en', 'backend', 'core', 'msg', 'EN', 'English', '2011-06-23 08:07:18'),
(380, 1, 'en', 'backend', 'core', 'msg', 'ERR', 'error', '2011-06-23 08:07:18'),
(381, 1, 'en', 'backend', 'core', 'msg', 'ES', 'Spanish', '2011-06-23 08:07:18'),
(382, 1, 'en', 'backend', 'core', 'msg', 'ForgotPassword', 'Forgot password?', '2011-06-23 08:07:18'),
(383, 1, 'en', 'backend', 'core', 'msg', 'FR', 'French', '2011-06-23 08:07:18'),
(384, 1, 'en', 'backend', 'core', 'msg', 'HelpAvatar', 'A square picture produces the best results.', '2011-06-23 08:07:18'),
(385, 1, 'en', 'backend', 'core', 'msg', 'HelpBlogger', 'Select the file that you exported from <a href="http://blogger.com">Blogger</a>.', '2011-06-23 08:07:18'),
(386, 1, 'en', 'backend', 'core', 'msg', 'HelpDrafts', 'Here you can see your draft. These are temporary versions.', '2011-06-23 08:07:18'),
(387, 1, 'en', 'backend', 'core', 'msg', 'HelpEmailFrom', 'E-mails sent from the CMS use these settings.', '2011-06-23 08:07:18'),
(388, 1, 'en', 'backend', 'core', 'msg', 'HelpEmailTo', 'Notifications from the CMS are sent here.', '2011-06-23 08:07:18'),
(389, 1, 'en', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'eg. http://feeds.feedburner.com/your-website', '2011-06-23 08:07:18'),
(390, 1, 'en', 'backend', 'core', 'msg', 'HelpForgotPassword', 'Below enter your e-mail. You will receive an e-mail containing instructions on how to get a new password.', '2011-06-23 08:07:18'),
(391, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaCustom', 'These custom metatags will be placed in the <code>&lt;head&gt;</code> section of the page.', '2011-06-23 08:07:18'),
(392, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaDescription', 'Briefly summarize the content. This summary is shown in the results of search engines.', '2011-06-23 08:07:18'),
(393, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'Choose a number of wellthought terms that describe the content.', '2011-06-23 08:07:18'),
(394, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaURL', 'Replace the automaticly generated URL by a custom one.', '2011-06-23 08:07:18'),
(395, 1, 'en', 'backend', 'core', 'msg', 'HelpNickname', 'The name you want to be published as (e.g. as the author of an article).', '2011-06-23 08:07:18'),
(396, 1, 'en', 'backend', 'core', 'msg', 'HelpPageTitle', 'The title in the browser window (<code>&lt;title&gt;</code>).', '2011-06-23 08:07:18'),
(397, 1, 'en', 'backend', 'core', 'msg', 'HelpResetPassword', 'Provide your new password.', '2011-06-23 08:07:18'),
(398, 1, 'en', 'backend', 'core', 'msg', 'HelpRevisions', 'The last saved versions are kept here. The current version will only be overwritten when you save your changes.', '2011-06-23 08:07:18'),
(399, 1, 'en', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Briefly describe what kind of content the RSS feed will contain.', '2011-06-23 08:07:18'),
(400, 1, 'en', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Provide a clear title for the RSS feed.', '2011-06-23 08:07:18'),
(401, 1, 'en', 'backend', 'core', 'msg', 'HelpSMTPServer', 'Mailserver that should be used for sending e-mails.', '2011-06-23 08:07:18'),
(402, 1, 'en', 'backend', 'core', 'msg', 'Imported', 'The data was imported.', '2011-06-23 08:07:18'),
(403, 1, 'en', 'backend', 'core', 'msg', 'LBL', 'label', '2011-06-23 08:07:18'),
(404, 1, 'en', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!', '2011-06-23 08:07:18'),
(405, 1, 'en', 'backend', 'core', 'msg', 'MSG', 'message', '2011-06-23 08:07:18'),
(406, 1, 'en', 'backend', 'core', 'msg', 'NL', 'Dutch', '2011-06-23 08:07:18'),
(407, 1, 'en', 'backend', 'core', 'msg', 'NoAkismetKey', 'If you want to enable the spam-protection you should <a href="%1$s">configure</a> an Akismet-key.', '2011-06-23 08:07:18'),
(408, 1, 'en', 'backend', 'core', 'msg', 'NoComments', 'There are no comments in this category yet.', '2011-06-23 08:07:18'),
(409, 1, 'en', 'backend', 'core', 'msg', 'NoItems', 'There are no items yet.', '2011-06-23 08:07:18'),
(410, 1, 'en', 'backend', 'core', 'msg', 'NoPublishedComments', 'There are no published comments.', '2011-06-23 08:07:18'),
(411, 1, 'en', 'backend', 'core', 'msg', 'NoRevisions', 'There are no previous versions yet.', '2011-06-23 08:07:18'),
(412, 1, 'en', 'backend', 'core', 'msg', 'NoTags', 'You didn''t add tags yet.', '2011-06-23 08:07:18'),
(413, 1, 'en', 'backend', 'core', 'msg', 'NoUsage', 'Not yet used.', '2011-06-23 08:07:18'),
(414, 1, 'en', 'backend', 'core', 'msg', 'NowEditing', 'now editing', '2011-06-23 08:07:18'),
(415, 1, 'en', 'backend', 'core', 'msg', 'PasswordResetSuccess', 'Your password has been changed.', '2011-06-23 08:07:18'),
(416, 1, 'en', 'backend', 'core', 'msg', 'Redirecting', 'You are being redirected.', '2011-06-23 08:07:18'),
(417, 1, 'en', 'backend', 'core', 'msg', 'ResetYourPasswordMailContent', 'Reset your password by clicking the link below. If you didn''t ask for this', '2011-06-23 08:07:18'),
(418, 1, 'en', 'backend', 'core', 'msg', 'ResetYourPasswordMailSubject', 'Change your password', '2011-06-23 08:07:18'),
(419, 1, 'en', 'backend', 'core', 'msg', 'Saved', 'The changes were saved.', '2011-06-23 08:07:18'),
(420, 1, 'en', 'backend', 'core', 'msg', 'SavedAsDraft', '"%1$s" saved as draft.', '2011-06-23 08:07:18'),
(421, 1, 'en', 'backend', 'core', 'msg', 'UsingADraft', 'You''re using a draft.', '2011-06-23 08:07:18'),
(422, 1, 'en', 'backend', 'core', 'msg', 'UsingARevision', 'You''re using an older version. Save to overwrite the current version.', '2011-06-23 08:07:18'),
(423, 1, 'en', 'backend', 'core', 'msg', 'ValuesAreChanged', 'Changes will be lost.', '2011-06-23 08:07:18'),
(424, 1, 'en', 'frontend', 'core', 'act', 'Archive', 'archive', '2011-06-23 08:07:18'),
(425, 1, 'en', 'frontend', 'core', 'act', 'Category', 'category', '2011-06-23 08:07:18'),
(426, 1, 'en', 'frontend', 'core', 'act', 'Comment', 'comment', '2011-06-23 08:07:18'),
(427, 1, 'en', 'frontend', 'core', 'act', 'Comments', 'comments', '2011-06-23 08:07:18'),
(428, 1, 'en', 'frontend', 'core', 'act', 'CommentsRss', 'comments-rss', '2011-06-23 08:07:18'),
(429, 1, 'en', 'frontend', 'core', 'act', 'Detail', 'detail', '2011-06-23 08:07:18'),
(430, 1, 'en', 'frontend', 'core', 'act', 'Rss', 'rss', '2011-06-23 08:07:18'),
(431, 1, 'en', 'frontend', 'core', 'err', 'AuthorIsRequired', 'Author is a required field.', '2011-06-23 08:07:18'),
(432, 1, 'en', 'frontend', 'core', 'err', 'CommentTimeout', 'Slow down cowboy', '2011-06-23 08:07:18'),
(433, 1, 'en', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Something went wrong while trying to send', '2011-06-23 08:07:18'),
(434, 1, 'en', 'frontend', 'core', 'err', 'EmailIsInvalid', 'Please provide a valid e-email.', '2011-06-23 08:07:18'),
(435, 1, 'en', 'frontend', 'core', 'err', 'EmailIsRequired', 'E-mail is a required field.', '2011-06-23 08:07:18'),
(436, 1, 'en', 'frontend', 'core', 'err', 'FieldIsRequired', 'This field is required.', '2011-06-23 08:07:18'),
(437, 1, 'en', 'frontend', 'core', 'err', 'FormError', 'Something went wrong', '2011-06-23 08:07:18'),
(438, 1, 'en', 'frontend', 'core', 'err', 'InvalidURL', 'This is an invalid URL.', '2011-06-23 08:07:18'),
(439, 1, 'en', 'frontend', 'core', 'err', 'MessageIsRequired', 'Message is a required field.', '2011-06-23 08:07:18'),
(440, 1, 'en', 'frontend', 'core', 'err', 'NameIsRequired', 'Please provide a name.', '2011-06-23 08:07:18'),
(441, 1, 'en', 'frontend', 'core', 'err', 'SomethingWentWrong', 'Something went wrong.', '2011-06-23 08:07:18'),
(442, 1, 'en', 'frontend', 'core', 'lbl', 'Advertisement', 'advertisement', '2011-06-23 08:07:18'),
(443, 1, 'en', 'frontend', 'core', 'lbl', 'Archive', 'archive', '2011-06-23 08:07:18'),
(444, 1, 'en', 'frontend', 'core', 'lbl', 'Archives', 'archives', '2011-06-23 08:07:18'),
(445, 1, 'en', 'frontend', 'core', 'lbl', 'Breadcrumb', 'breadcrumb', '2011-06-23 08:07:18'),
(446, 1, 'en', 'frontend', 'core', 'lbl', 'By', 'by', '2011-06-23 08:07:18'),
(447, 1, 'en', 'frontend', 'core', 'lbl', 'Category', 'category', '2011-06-23 08:07:18'),
(448, 1, 'en', 'frontend', 'core', 'lbl', 'Categories', 'categories', '2011-06-23 08:07:18'),
(449, 1, 'en', 'frontend', 'core', 'lbl', 'Close', 'close', '2011-06-23 08:07:18'),
(450, 1, 'en', 'frontend', 'core', 'lbl', 'Comment', 'comment', '2011-06-23 08:07:18'),
(451, 1, 'en', 'frontend', 'core', 'lbl', 'CommentedOn', 'commented on', '2011-06-23 08:07:18'),
(452, 1, 'en', 'frontend', 'core', 'lbl', 'Comments', 'comments', '2011-06-23 08:07:18'),
(453, 1, 'en', 'frontend', 'core', 'lbl', 'Date', 'date', '2011-06-23 08:07:18'),
(454, 1, 'en', 'frontend', 'core', 'lbl', 'Disclaimer', 'disclaimer', '2011-06-23 08:07:18'),
(455, 1, 'en', 'frontend', 'core', 'lbl', 'Email', 'e-mail', '2011-06-23 08:07:18'),
(456, 1, 'en', 'frontend', 'core', 'lbl', 'EN', 'English', '2011-06-23 08:07:18'),
(457, 1, 'en', 'frontend', 'core', 'lbl', 'EnableJavascript', 'enable javascript', '2011-06-23 08:07:18'),
(458, 1, 'en', 'frontend', 'core', 'lbl', 'FooterNavigation', 'footer navigation', '2011-06-23 08:07:18'),
(459, 1, 'en', 'frontend', 'core', 'lbl', 'FR', 'French', '2011-06-23 08:07:18'),
(460, 1, 'en', 'frontend', 'core', 'lbl', 'GoTo', 'go to', '2011-06-23 08:07:18'),
(461, 1, 'en', 'frontend', 'core', 'lbl', 'GoToPage', 'go to page', '2011-06-23 08:07:18'),
(462, 1, 'en', 'frontend', 'core', 'lbl', 'In', 'in', '2011-06-23 08:07:18'),
(463, 1, 'en', 'frontend', 'core', 'lbl', 'Language', 'language', '2011-06-23 08:07:18'),
(464, 1, 'en', 'frontend', 'core', 'lbl', 'MainNavigation', 'main navigation', '2011-06-23 08:07:18'),
(465, 1, 'en', 'frontend', 'core', 'lbl', 'Message', 'message', '2011-06-23 08:07:18'),
(466, 1, 'en', 'frontend', 'core', 'lbl', 'More', 'more', '2011-06-23 08:07:18'),
(467, 1, 'en', 'frontend', 'core', 'lbl', 'Name', 'name', '2011-06-23 08:07:18'),
(468, 1, 'en', 'frontend', 'core', 'lbl', 'Next', 'next', '2011-06-23 08:07:18'),
(469, 1, 'en', 'frontend', 'core', 'lbl', 'NextPage', 'next page', '2011-06-23 08:07:18'),
(470, 1, 'en', 'frontend', 'core', 'lbl', 'NL', 'Dutch', '2011-06-23 08:07:18'),
(471, 1, 'en', 'frontend', 'core', 'lbl', 'On', 'on', '2011-06-23 08:07:18'),
(472, 1, 'en', 'frontend', 'core', 'lbl', 'Previous', 'previous', '2011-06-23 08:07:18'),
(473, 1, 'en', 'frontend', 'core', 'lbl', 'PreviousPage', 'previous page', '2011-06-23 08:07:18'),
(474, 1, 'en', 'frontend', 'core', 'lbl', 'RecentComments', 'recent comments', '2011-06-23 08:07:18'),
(475, 1, 'en', 'frontend', 'core', 'lbl', 'RequiredField', 'required field', '2011-06-23 08:07:18'),
(476, 1, 'en', 'frontend', 'core', 'lbl', 'Send', 'send', '2011-06-23 08:07:18'),
(477, 1, 'en', 'frontend', 'core', 'lbl', 'Search', 'search', '2011-06-23 08:07:18'),
(478, 1, 'en', 'frontend', 'core', 'lbl', 'SearchAgain', 'search again', '2011-06-23 08:07:18'),
(479, 1, 'en', 'frontend', 'core', 'lbl', 'SearchTerm', 'searchterm', '2011-06-23 08:07:18'),
(480, 1, 'en', 'frontend', 'core', 'lbl', 'Sitemap', 'sitemap', '2011-06-23 08:07:18'),
(481, 1, 'en', 'frontend', 'core', 'lbl', 'SkipToContent', 'skip to content', '2011-06-23 08:07:18'),
(482, 1, 'en', 'frontend', 'core', 'lbl', 'Subnavigation', 'subnavigation', '2011-06-23 08:07:18'),
(483, 1, 'en', 'frontend', 'core', 'lbl', 'Tags', 'tags', '2011-06-23 08:07:18'),
(484, 1, 'en', 'frontend', 'core', 'lbl', 'Title', 'title', '2011-06-23 08:07:18'),
(485, 1, 'en', 'frontend', 'core', 'lbl', 'Website', 'website', '2011-06-23 08:07:18'),
(486, 1, 'en', 'frontend', 'core', 'lbl', 'WrittenOn', 'written on', '2011-06-23 08:07:18'),
(487, 1, 'en', 'frontend', 'core', 'lbl', 'YouAreHere', 'you are here', '2011-06-23 08:07:18'),
(488, 1, 'en', 'frontend', 'core', 'msg', 'Comment', 'comment', '2011-06-23 08:07:18'),
(489, 1, 'en', 'frontend', 'core', 'msg', 'CommentsOn', 'Comments on %1$s', '2011-06-23 08:07:18'),
(490, 1, 'en', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Your e-mail was sent.', '2011-06-23 08:07:18'),
(491, 1, 'en', 'frontend', 'core', 'msg', 'ContactSubject', 'E-mail via contact form.', '2011-06-23 08:07:18'),
(492, 1, 'en', 'frontend', 'core', 'msg', 'EN', 'English', '2011-06-23 08:07:18');
INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(493, 1, 'en', 'frontend', 'core', 'msg', 'EnableJavascript', 'Having javascript enabled is recommended for using this site.', '2011-06-23 08:07:18'),
(494, 1, 'en', 'frontend', 'core', 'msg', 'FR', 'French', '2011-06-23 08:07:18'),
(495, 1, 'en', 'frontend', 'core', 'msg', 'HelpDateField', 'eg. 20/06/2011', '2011-06-23 08:07:18'),
(496, 1, 'en', 'frontend', 'core', 'msg', 'HelpImageField', 'Only jp(e)g, gif or png-files are allowed.', '2011-06-23 08:07:18'),
(497, 1, 'en', 'frontend', 'core', 'msg', 'HelpTimeField', 'eg. 14:35', '2011-06-23 08:07:18'),
(498, 1, 'en', 'frontend', 'core', 'msg', 'MoreResults', 'Find more results…', '2011-06-23 08:07:18'),
(499, 1, 'en', 'frontend', 'core', 'msg', 'NL', 'Dutch', '2011-06-23 08:07:18'),
(500, 1, 'en', 'frontend', 'core', 'msg', 'NotificationSubject', 'Notification', '2011-06-23 08:07:18'),
(501, 1, 'en', 'frontend', 'core', 'msg', 'SearchNoItems', 'There were no results.', '2011-06-23 08:07:18'),
(502, 1, 'en', 'frontend', 'core', 'msg', 'TagsNoItems', 'No tags were used.', '2011-06-23 08:07:18'),
(503, 1, 'en', 'frontend', 'core', 'msg', 'WrittenBy', 'written by %1$s', '2011-06-23 08:07:18'),
(504, 0, 'en', 'backend', 'users', 'lbl', 'Add', 'add user', '2011-06-23 08:07:18'),
(505, 0, 'en', 'backend', 'users', 'msg', 'Added', 'The user "%1$s" was added.', '2011-06-23 08:07:18'),
(506, 0, 'en', 'backend', 'users', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the user "%1$s"?', '2011-06-23 08:07:18'),
(507, 0, 'en', 'backend', 'users', 'msg', 'Deleted', 'The user "%1$s" was deleted.', '2011-06-23 08:07:18'),
(508, 0, 'en', 'backend', 'users', 'msg', 'Edited', 'The settings for "%1$s" were saved.', '2011-06-23 08:07:18'),
(509, 0, 'en', 'backend', 'users', 'msg', 'EditUser', 'edit user "%1$s"', '2011-06-23 08:07:18'),
(510, 0, 'en', 'backend', 'users', 'msg', 'HelpActive', 'Enable CMS access for this account.', '2011-06-23 08:07:18'),
(511, 0, 'en', 'backend', 'users', 'msg', 'HelpAPIAccess', 'Enable API access for this account.', '2011-06-23 08:07:18'),
(512, 0, 'en', 'backend', 'users', 'msg', 'HelpStrongPassword', 'Strong passwords consist of a combination of capitals', '2011-06-23 08:07:18'),
(513, 0, 'en', 'backend', 'users', 'msg', 'Restored', 'The user "%1$s" is restored.', '2011-06-23 08:07:18'),
(514, 0, 'en', 'backend', 'users', 'err', 'NonExisting', 'This user doesn''t exist.', '2011-06-23 08:07:18'),
(515, 0, 'en', 'backend', 'users', 'err', 'EmailWasDeletedBefore', 'A user with this emailaddress was deleted. <a href="%1$s">Restore this user</a>.', '2011-06-23 08:07:18'),
(516, 0, 'en', 'backend', 'users', 'err', 'CantChangeGodsEmail', 'You can''t change the emailaddres of the GOD-user.', '2011-06-23 08:07:18'),
(517, 0, 'en', 'backend', 'users', 'err', 'CantDeleteGod', 'You can''t delete the GOD-user.', '2011-06-23 08:07:18'),
(518, 1, 'en', 'backend', 'groups', 'lbl', 'Presets', 'presets', '2011-06-23 08:07:18'),
(519, 1, 'en', 'backend', 'groups', 'lbl', 'DisplayWidgets', 'widgets to display', '2011-06-23 08:07:18'),
(520, 1, 'en', 'backend', 'groups', 'lbl', 'SetPermissions', 'set permissions', '2011-06-23 08:07:18'),
(521, 1, 'en', 'backend', 'groups', 'lbl', 'Action', 'action', '2011-06-23 08:07:18'),
(522, 1, 'en', 'backend', 'groups', 'lbl', 'NumUsers', 'number of users', '2011-06-23 08:07:18'),
(523, 1, 'en', 'backend', 'groups', 'lbl', 'Screenname', 'screen name', '2011-06-23 08:07:18'),
(524, 1, 'en', 'backend', 'groups', 'lbl', 'Checkbox', '&nbsp;', '2011-06-23 08:07:18'),
(525, 1, 'en', 'backend', 'groups', 'msg', 'NoUsers', 'This group does not contain any users.', '2011-06-23 08:07:18'),
(526, 1, 'en', 'backend', 'groups', 'msg', 'Added', '"%1$s" has been added.', '2011-06-23 08:07:18'),
(527, 1, 'en', 'backend', 'groups', 'msg', 'Edited', 'changes for "%1$s" has been saved.', '2011-06-23 08:07:18'),
(528, 1, 'en', 'backend', 'groups', 'msg', 'Deleted', '"%1$s" has been deleted.', '2011-06-23 08:07:18'),
(529, 1, 'en', 'backend', 'groups', 'msg', 'NoWidgets', 'There are no widgets available.', '2011-06-23 08:07:18'),
(530, 1, 'en', 'backend', 'groups', 'err', 'GroupAlreadyExists', 'This group already exists.', '2011-06-23 08:07:18'),
(531, 1, 'en', 'backend', 'core', 'lbl', 'Groups', 'groups', '2011-06-23 08:07:18'),
(532, 1, 'en', 'backend', 'settings', 'err', 'PortIsRequired', 'Port is required.', '2011-06-23 08:07:18'),
(533, 1, 'en', 'backend', 'settings', 'err', 'ServerIsRequired', 'Server is required.', '2011-06-23 08:07:18'),
(534, 1, 'en', 'backend', 'settings', 'lbl', 'AdminIds', 'admin ids', '2011-06-23 08:07:18'),
(535, 1, 'en', 'backend', 'settings', 'lbl', 'ApplicationId', 'application id', '2011-06-23 08:07:18'),
(536, 1, 'en', 'backend', 'settings', 'lbl', 'ApplicationSecret', 'app secret', '2011-06-23 08:07:18'),
(537, 1, 'en', 'backend', 'settings', 'msg', 'ConfigurationError', 'Some settings are not yet configured.', '2011-06-23 08:07:18'),
(538, 1, 'en', 'backend', 'settings', 'msg', 'HelpAPIKeys', 'Access codes for webservices.', '2011-06-23 08:07:18'),
(539, 1, 'en', 'backend', 'settings', 'msg', 'HelpDateFormatLong', 'Format that''s used on overview and detail pages.', '2011-06-23 08:07:18'),
(540, 1, 'en', 'backend', 'settings', 'msg', 'HelpDateFormatShort', 'This format is mostly used in table overviews.', '2011-06-23 08:07:18'),
(541, 1, 'en', 'backend', 'settings', 'msg', 'HelpDomains', 'Enter the domains on which this website can be reached. (Split domains with linebreaks.)', '2011-06-23 08:07:18'),
(542, 1, 'en', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Send CMS notifications to this e-mailaddress.', '2011-06-23 08:07:18'),
(543, 1, 'en', 'backend', 'settings', 'msg', 'HelpFacebookAdminIds', 'Either Facebook user IDs or a Facebook Platform application ID that administers this website.', '2011-06-23 08:07:18'),
(544, 1, 'en', 'backend', 'settings', 'msg', 'HelpFacebookApiKey', 'The API key of your Facebook application.', '2011-06-23 08:07:18'),
(545, 1, 'en', 'backend', 'settings', 'msg', 'HelpFacebookApplicationId', 'The id of your Facebook application', '2011-06-23 08:07:18'),
(546, 1, 'en', 'backend', 'settings', 'msg', 'HelpFacebookApplicationSecret', 'The secret of your Facebook application.', '2011-06-23 08:07:18'),
(547, 1, 'en', 'backend', 'settings', 'msg', 'HelpLanguages', 'Select the languages that are accessible for visitors.', '2011-06-23 08:07:18'),
(548, 1, 'en', 'backend', 'settings', 'msg', 'HelpNumberFormat', 'This format is used to display numbers on the website.', '2011-06-23 08:07:18'),
(549, 1, 'en', 'backend', 'settings', 'msg', 'HelpRedirectLanguages', 'Select the languages that people may automatically be redirect to by their browser.', '2011-06-23 08:07:18'),
(550, 1, 'en', 'backend', 'settings', 'msg', 'HelpSendingEmails', 'You can send emails in 2 ways. By using PHP''s built-in mail method or via SMTP. We advice you to use SMTP', '2011-06-23 08:07:18'),
(551, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsFoot', 'Paste code that needs to be loaded at the end of the <code>&lt;body&gt;</code> tag here (e.g. Google Analytics).', '2011-06-23 08:07:18'),
(552, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsFootLabel', 'End of <code>&lt;body&gt;</code> script(s)', '2011-06-23 08:07:18'),
(553, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsHead', 'Paste code that needs to be loaded in the <code>&lt;head&gt;</code> section here.', '2011-06-23 08:07:18'),
(554, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsHeadLabel', '<code>&lt;head&gt;</code> script(s)', '2011-06-23 08:07:18'),
(555, 1, 'en', 'backend', 'settings', 'msg', 'HelpThemes', 'Select the theme you wish to use.', '2011-06-23 08:07:18'),
(556, 1, 'en', 'backend', 'settings', 'msg', 'HelpTimeFormat', 'This format is used to display dates on the website.', '2011-06-23 08:07:18'),
(557, 1, 'en', 'backend', 'settings', 'msg', 'NoAdminIds', 'No admin ids yet.', '2011-06-23 08:07:18'),
(558, 1, 'en', 'backend', 'settings', 'msg', 'SendTestMail', 'send test email', '2011-06-23 08:07:18'),
(559, 1, 'en', 'backend', 'settings', 'msg', 'TestMessage', 'this is just a test', '2011-06-23 08:07:18'),
(560, 1, 'en', 'backend', 'settings', 'msg', 'TestWasSent', 'The test email was sent.', '2011-06-23 08:07:18'),
(561, 1, 'en', 'backend', 'pages', 'err', 'CantBeMoved', 'Page can''t be moved.', '2011-06-23 08:07:18'),
(562, 1, 'en', 'backend', 'pages', 'err', 'HomeCantHaveBlocks', 'You can''t link a module to the homepage.', '2011-06-23 08:07:18'),
(563, 1, 'en', 'backend', 'pages', 'err', 'InvalidTemplateSyntax', 'Invalid syntax.', '2011-06-23 08:07:18'),
(564, 1, 'en', 'backend', 'pages', 'err', 'DeletedTemplate', 'You can''t delete this template.', '2011-06-23 08:07:18'),
(565, 1, 'en', 'backend', 'pages', 'lbl', 'Add', 'add page', '2011-06-23 08:07:18'),
(566, 1, 'en', 'backend', 'pages', 'lbl', 'AddBlock', 'add block', '2011-06-23 08:07:18'),
(567, 1, 'en', 'backend', 'pages', 'lbl', 'EditModuleContent', 'edit module content', '2011-06-23 08:07:18'),
(568, 1, 'en', 'backend', 'pages', 'lbl', 'ExternalLink', 'external link', '2011-06-23 08:07:18'),
(569, 1, 'en', 'backend', 'pages', 'lbl', 'ExtraTypeBlock', 'module', '2011-06-23 08:07:18'),
(570, 1, 'en', 'backend', 'pages', 'lbl', 'ExtraTypeWidget', 'widget', '2011-06-23 08:07:18'),
(571, 1, 'en', 'backend', 'pages', 'lbl', 'Footer', 'footer navigation', '2011-06-23 08:07:18'),
(572, 1, 'en', 'backend', 'pages', 'lbl', 'InternalLink', 'internal link', '2011-06-23 08:07:18'),
(573, 1, 'en', 'backend', 'pages', 'lbl', 'MainNavigation', 'main navigation', '2011-06-23 08:07:18'),
(574, 1, 'en', 'backend', 'pages', 'lbl', 'Meta', 'meta navigation', '2011-06-23 08:07:18'),
(575, 1, 'en', 'backend', 'pages', 'lbl', 'Position', 'position', '2011-06-23 08:07:18'),
(576, 1, 'en', 'backend', 'pages', 'lbl', 'Redirect', 'redirect', '2011-06-23 08:07:18'),
(577, 1, 'en', 'backend', 'pages', 'lbl', 'Root', 'single pages', '2011-06-23 08:07:18'),
(578, 1, 'en', 'backend', 'pages', 'msg', 'Added', 'The page "%1$s" was added.', '2011-06-23 08:07:18'),
(579, 1, 'en', 'backend', 'pages', 'msg', 'AddedTemplate', 'The template "%1$s" was added.', '2011-06-23 08:07:18'),
(580, 1, 'en', 'backend', 'pages', 'msg', 'BlockAttached', 'The module <strong>%1$s</strong> is attached to this section.', '2011-06-23 08:07:18'),
(581, 1, 'en', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the page "%1$s"?', '2011-06-23 08:07:18'),
(582, 1, 'en', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Are your sure you want to delete the template "%1$s"?', '2011-06-23 08:07:18'),
(583, 1, 'en', 'backend', 'pages', 'msg', 'Deleted', 'The page "%1$s" was deleted.', '2011-06-23 08:07:18'),
(584, 1, 'en', 'backend', 'pages', 'msg', 'DeletedTemplate', 'The template "%1$s" was deleted.', '2011-06-23 08:07:18'),
(585, 1, 'en', 'backend', 'pages', 'msg', 'Edited', 'The page "%1$s" was saved.', '2011-06-23 08:07:18'),
(586, 1, 'en', 'backend', 'pages', 'msg', 'EditedTemplate', 'The template "%1$s" was saved.', '2011-06-23 08:07:18'),
(587, 1, 'en', 'backend', 'pages', 'msg', 'HelpBlockContent', 'What kind of content do you want to show here?', '2011-06-23 08:07:18'),
(588, 1, 'en', 'backend', 'pages', 'msg', 'HelpExternalRedirect', 'Use this if you need to redirect a menu-item to an external website.', '2011-06-23 08:07:18'),
(589, 1, 'en', 'backend', 'pages', 'msg', 'HelpInternalRedirect', 'Use this if you need to redirect a menu-item to another page on this website.', '2011-06-23 08:07:18'),
(590, 1, 'en', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigation (above/below the menu) on every page.', '2011-06-23 08:07:18'),
(591, 1, 'en', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'The title that is shown in the menu.', '2011-06-23 08:07:18'),
(592, 1, 'en', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Makes sure that this page doesn''t influence the internal PageRank.', '2011-06-23 08:07:18'),
(593, 1, 'en', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'e.g. [left,main,right],[/,main,/]', '2011-06-23 08:07:18'),
(594, 1, 'en', 'backend', 'pages', 'msg', 'HelpTemplateLocation', 'Put your templates in the <code>core/layout/templates</code> folder of your theme.', '2011-06-23 08:07:18'),
(595, 1, 'en', 'backend', 'pages', 'msg', 'IsAction', 'Use this page as a module action.', '2011-06-23 08:07:18'),
(596, 1, 'en', 'backend', 'pages', 'msg', 'MetaNavigation', 'Enable metanavigation for this website.', '2011-06-23 08:07:18'),
(597, 1, 'en', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'A module has already been linked to this page.', '2011-06-23 08:07:18'),
(598, 1, 'en', 'backend', 'pages', 'msg', 'PageIsMoved', 'The page "%1$s" was moved.', '2011-06-23 08:07:18'),
(599, 1, 'en', 'backend', 'pages', 'msg', 'PathToTemplate', 'Path to template', '2011-06-23 08:07:18'),
(600, 1, 'en', 'backend', 'pages', 'msg', 'RichText', 'Editor', '2011-06-23 08:07:18'),
(601, 1, 'en', 'backend', 'pages', 'msg', 'TemplateChangeWarning', '<strong>Warning:</strong> Changing the template can cause existing content to be in another place or no longer be shown.', '2011-06-23 08:07:18'),
(602, 1, 'en', 'backend', 'pages', 'msg', 'TemplateInUse', 'This template is in use. You can''t change the number of blocks.', '2011-06-23 08:07:18'),
(603, 1, 'en', 'backend', 'pages', 'msg', 'WidgetAttached', 'The widget <strong>%1$s</strong> is attached to this section.', '2011-06-23 08:07:18'),
(604, 1, 'en', 'frontend', 'core', 'lbl', 'AboutUs', 'about us', '2011-06-23 08:07:18'),
(605, 1, 'en', 'frontend', 'core', 'lbl', 'History', 'history', '2011-06-23 08:07:18'),
(606, 1, 'en', 'frontend', 'core', 'lbl', 'Location', 'location', '2011-06-23 08:07:18'),
(607, 1, 'en', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synonyms are required.', '2011-06-23 08:07:18'),
(608, 1, 'en', 'backend', 'search', 'err', 'TermIsRequired', 'The searchterm is required.', '2011-06-23 08:07:18'),
(609, 1, 'en', 'backend', 'search', 'err', 'TermExists', 'Synonyms for this searchterm already exist.', '2011-06-23 08:07:18'),
(610, 1, 'en', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn', '2011-06-23 08:07:18'),
(611, 1, 'en', 'backend', 'search', 'lbl', 'AddSynonym', 'add synonym', '2011-06-23 08:07:18'),
(612, 1, 'en', 'backend', 'search', 'lbl', 'DeleteSynonym', 'delete synonym', '2011-06-23 08:07:18'),
(613, 1, 'en', 'backend', 'search', 'lbl', 'EditSynonym', 'edit synonym', '2011-06-23 08:07:18'),
(614, 1, 'en', 'backend', 'search', 'lbl', 'ItemsForAutocomplete', 'Items in autocomplete (search results: search term suggestions)', '2011-06-23 08:07:18'),
(615, 1, 'en', 'backend', 'search', 'lbl', 'ItemsForAutosuggest', 'Items in autosuggest (search widget: results)', '2011-06-23 08:07:18'),
(616, 1, 'en', 'backend', 'search', 'lbl', 'ModuleWeight', 'module weight', '2011-06-23 08:07:18'),
(617, 1, 'en', 'backend', 'search', 'lbl', 'SearchedOn', 'searched on', '2011-06-23 08:07:18'),
(618, 1, 'en', 'backend', 'search', 'msg', 'AddedSynonym', 'The synonym for the searchterm "%1$s" was added.', '2011-06-23 08:07:18'),
(619, 1, 'en', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Are you sure you want to delete the synonyms for the searchterm "%1$s"?', '2011-06-23 08:07:18'),
(620, 1, 'en', 'backend', 'search', 'msg', 'DeletedSynonym', 'The synonym for the searchterm "%1$s" was deleted.', '2011-06-23 08:07:18'),
(621, 1, 'en', 'backend', 'search', 'msg', 'EditedSynonym', 'The synonym for the searchterm "%1$s" was saved.', '2011-06-23 08:07:18'),
(622, 1, 'en', 'backend', 'search', 'msg', 'HelpWeight', 'The default weight is 1. If you want to give search results from a specific module more importance.', '2011-06-23 08:07:18'),
(623, 1, 'en', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Define the importance of each module in search results here.', '2011-06-23 08:07:18'),
(624, 1, 'en', 'backend', 'search', 'msg', 'IncludeInSearch', 'Include in search results?', '2011-06-23 08:07:18'),
(625, 1, 'en', 'backend', 'search', 'msg', 'NoStatistics', 'There are no statistics yet.', '2011-06-23 08:07:18'),
(626, 1, 'en', 'backend', 'search', 'msg', 'NoSynonyms', 'There are no synonyms yet. <a href="%1$s">Add the first synonym</a>.', '2011-06-23 08:07:18'),
(627, 1, 'en', 'backend', 'search', 'msg', 'NoSynonymsBox', 'There are no synonyms yet.', '2011-06-23 08:07:18'),
(628, 1, 'en', 'frontend', 'core', 'err', 'TermIsRequired', 'The searchterm is required.', '2011-06-23 08:07:18'),
(629, 1, 'en', 'backend', 'content_blocks', 'lbl', 'Add', 'add content block', '2011-06-23 08:07:18'),
(630, 1, 'en', 'backend', 'content_blocks', 'msg', 'NoTemplate', 'No template', '2011-06-23 08:07:18'),
(631, 1, 'en', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'edit content block "%1$s"', '2011-06-23 08:07:18'),
(632, 1, 'en', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the content block "%1$s"?', '2011-06-23 08:07:18'),
(633, 1, 'en', 'backend', 'content_blocks', 'msg', 'Added', 'The content block "%1$s" was added.', '2011-06-23 08:07:18'),
(634, 1, 'en', 'backend', 'content_blocks', 'msg', 'Edited', 'The content block "%1$s" was saved.', '2011-06-23 08:07:18'),
(635, 1, 'en', 'backend', 'content_blocks', 'msg', 'Deleted', 'The content block "%1$s" was deleted.', '2011-06-23 08:07:18'),
(636, 1, 'en', 'frontend', 'core', 'lbl', 'Blog', 'blog', '2011-06-23 08:07:18'),
(637, 1, 'en', 'frontend', 'core', 'lbl', 'ItemsWithTag', 'items with tag "%1$s"', '2011-06-23 08:07:18'),
(638, 1, 'en', 'frontend', 'core', 'lbl', 'Pages', 'pages', '2011-06-23 08:07:18'),
(639, 1, 'en', 'frontend', 'core', 'lbl', 'Related', 'related', '2011-06-23 08:07:18'),
(640, 1, 'en', 'frontend', 'core', 'lbl', 'ToTagsOverview', 'to tags overview', '2011-06-23 08:07:18'),
(641, 1, 'en', 'backend', 'core', 'lbl', 'Related', 'related', '2011-06-23 08:07:18'),
(642, 1, 'en', 'backend', 'core', 'lbl', 'TagCloud', 'tagcloud', '2011-06-23 08:07:18'),
(643, 1, 'en', 'backend', 'tags', 'msg', 'Edited', 'The tag "%1$s" was saved.', '2011-06-23 08:07:18'),
(644, 1, 'en', 'backend', 'tags', 'msg', 'EditTag', 'edit tag "%1$s"', '2011-06-23 08:07:18'),
(645, 1, 'en', 'backend', 'tags', 'msg', 'Deleted', 'The selected tag(s) was/were deleted.', '2011-06-23 08:07:18'),
(646, 1, 'en', 'backend', 'tags', 'msg', 'NoItems', 'There are no tags yet.', '2011-06-23 08:07:18'),
(647, 1, 'en', 'backend', 'tags', 'err', 'NonExisting', 'This tag doesn''t exist.', '2011-06-23 08:07:18'),
(648, 1, 'en', 'backend', 'tags', 'err', 'NoSelection', 'No tags were selected.', '2011-06-23 08:07:18'),
(649, 1, 'en', 'backend', 'tags', 'err', 'TagAlreadyExists', 'This tag already exists.', '2011-06-23 08:07:18'),
(650, 1, 'en', 'backend', 'core', 'msg', 'NoReferrers', 'There are no referrers yet.', '2011-06-23 08:07:19'),
(651, 1, 'en', 'backend', 'analytics', 'err', 'AnalyseNoSessionToken', 'There is no link with a Google analytics account yet. <a href="%1$s">Configure</a>', '2011-06-23 08:07:19'),
(652, 1, 'en', 'backend', 'analytics', 'err', 'AnalyseNoTableId', 'There is no link with an analytics website profile yet. <a href="%1$s">Configure</a>', '2011-06-23 08:07:19'),
(653, 1, 'en', 'backend', 'analytics', 'err', 'NoSessionToken', 'There is no link with a Google analytics account yet.', '2011-06-23 08:07:19'),
(654, 1, 'en', 'backend', 'analytics', 'err', 'NoTableId', 'There is no link with an analytics website profile yet.', '2011-06-23 08:07:19'),
(655, 1, 'en', 'backend', 'analytics', 'lbl', 'AddLandingPage', 'add landing page', '2011-06-23 08:07:19'),
(656, 1, 'en', 'backend', 'analytics', 'lbl', 'AllStatistics', 'all statistics', '2011-06-23 08:07:19'),
(657, 1, 'en', 'backend', 'analytics', 'lbl', 'AverageTimeOnPage', 'average time on page', '2011-06-23 08:07:19'),
(658, 1, 'en', 'backend', 'analytics', 'lbl', 'AverageTimeOnSite', 'average time on site', '2011-06-23 08:07:19'),
(659, 1, 'en', 'backend', 'analytics', 'lbl', 'BounceRate', 'bounce rate', '2011-06-23 08:07:19'),
(660, 1, 'en', 'backend', 'analytics', 'lbl', 'Bounces', 'bounces', '2011-06-23 08:07:19'),
(661, 1, 'en', 'backend', 'analytics', 'lbl', 'ChangePeriod', 'change period', '2011-06-23 08:07:19'),
(662, 1, 'en', 'backend', 'analytics', 'lbl', 'ChooseThisAccount', 'choose this account', '2011-06-23 08:07:19'),
(663, 1, 'en', 'backend', 'analytics', 'lbl', 'DirectTraffic', 'direct traffic', '2011-06-23 08:07:19'),
(664, 1, 'en', 'backend', 'analytics', 'lbl', 'Entrances', 'entrances', '2011-06-23 08:07:19'),
(665, 1, 'en', 'backend', 'analytics', 'lbl', 'ExitRate', 'exit rate', '2011-06-23 08:07:19'),
(666, 1, 'en', 'backend', 'analytics', 'lbl', 'Exits', 'exits', '2011-06-23 08:07:19'),
(667, 1, 'en', 'backend', 'analytics', 'lbl', 'GetLiveData', 'collect live data', '2011-06-23 08:07:19'),
(668, 1, 'en', 'backend', 'analytics', 'lbl', 'GoogleAnalyticsLink', 'link to Google Analytics', '2011-06-23 08:07:19'),
(669, 1, 'en', 'backend', 'analytics', 'lbl', 'LinkedAccount', 'linked account', '2011-06-23 08:07:19'),
(670, 1, 'en', 'backend', 'analytics', 'lbl', 'LinkedProfile', 'linked profile', '2011-06-23 08:07:19'),
(671, 1, 'en', 'backend', 'analytics', 'lbl', 'LinkThisProfile', 'link this profile', '2011-06-23 08:07:19'),
(672, 1, 'en', 'backend', 'analytics', 'lbl', 'NewVisitsPercentage', 'new visits percentage', '2011-06-23 08:07:19'),
(673, 1, 'en', 'backend', 'analytics', 'lbl', 'PagesPerVisit', 'pages per visit', '2011-06-23 08:07:19'),
(674, 1, 'en', 'backend', 'analytics', 'lbl', 'Pageviews', 'pageviews', '2011-06-23 08:07:19'),
(675, 1, 'en', 'backend', 'analytics', 'lbl', 'PageviewsByTrafficSources', 'pageviews per traffic source', '2011-06-23 08:07:19'),
(676, 1, 'en', 'backend', 'analytics', 'lbl', 'PercentageOfSiteTotal', 'percentage of site total', '2011-06-23 08:07:19'),
(677, 1, 'en', 'backend', 'analytics', 'lbl', 'PeriodStatistics', 'period statistics', '2011-06-23 08:07:19'),
(678, 1, 'en', 'backend', 'analytics', 'lbl', 'Referral', 'referring site', '2011-06-23 08:07:19'),
(679, 1, 'en', 'backend', 'analytics', 'lbl', 'SearchEngines', 'search engines', '2011-06-23 08:07:19'),
(680, 1, 'en', 'backend', 'analytics', 'lbl', 'SiteAverage', 'site average', '2011-06-23 08:07:19'),
(681, 1, 'en', 'backend', 'analytics', 'lbl', 'TimeOnSite', 'time on site', '2011-06-23 08:07:19'),
(682, 1, 'en', 'backend', 'analytics', 'lbl', 'TopContent', 'top content', '2011-06-23 08:07:19'),
(683, 1, 'en', 'backend', 'analytics', 'lbl', 'TopExitPages', 'top exit pages', '2011-06-23 08:07:19'),
(684, 1, 'en', 'backend', 'analytics', 'lbl', 'TopKeywords', 'top keywords', '2011-06-23 08:07:19'),
(685, 1, 'en', 'backend', 'analytics', 'lbl', 'TopLandingPages', 'top landing pages', '2011-06-23 08:07:19'),
(686, 1, 'en', 'backend', 'analytics', 'lbl', 'TopPages', 'top pages', '2011-06-23 08:07:19'),
(687, 1, 'en', 'backend', 'analytics', 'lbl', 'TopReferrers', 'top referrers', '2011-06-23 08:07:19'),
(688, 1, 'en', 'backend', 'analytics', 'lbl', 'UniquePageviews', 'unique pageviews', '2011-06-23 08:07:19'),
(689, 1, 'en', 'backend', 'analytics', 'lbl', 'Views', 'views', '2011-06-23 08:07:19'),
(690, 1, 'en', 'backend', 'analytics', 'lbl', 'ViewStatistics', 'view statistics', '2011-06-23 08:07:19'),
(691, 1, 'en', 'backend', 'analytics', 'lbl', 'Visits', 'visits', '2011-06-23 08:07:19'),
(692, 1, 'en', 'backend', 'analytics', 'msg', 'AuthenticateAtGoogle', 'Link your Google account', '2011-06-23 08:07:19'),
(693, 1, 'en', 'backend', 'analytics', 'msg', 'ChooseWebsiteProfile', 'Choose an Analytics website profile...', '2011-06-23 08:07:19'),
(694, 1, 'en', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkAccount', 'Are you sure you want to remove the link with the account "%1$s"?<br />All saves statistics will be deleted from the CMS.', '2011-06-23 08:07:19'),
(695, 1, 'en', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkGoogleAccount', 'Are you sure you want to remove the link with your Google account?', '2011-06-23 08:07:19'),
(696, 1, 'en', 'backend', 'analytics', 'msg', 'GetDataError', 'Something went wrong while collecting the data from Google Analytics. Our appologies for the inconvenience. Please try again later.', '2011-06-23 08:07:19'),
(697, 1, 'en', 'backend', 'analytics', 'msg', 'LinkGoogleAccount', 'Link your Google account to Fork CMS.', '2011-06-23 08:07:19'),
(698, 1, 'en', 'backend', 'analytics', 'msg', 'LinkWebsiteProfile', 'Link your Google Analytics website profile to Fork CMS.', '2011-06-23 08:07:19'),
(699, 1, 'en', 'backend', 'analytics', 'msg', 'LoadingData', 'Fork is collecting the data from Google Analytics.', '2011-06-23 08:07:19'),
(700, 1, 'en', 'backend', 'analytics', 'msg', 'NoAccounts', 'There are no website profiles linked to this Google account. Log off at Google and try with a different account.', '2011-06-23 08:07:19'),
(701, 1, 'en', 'backend', 'analytics', 'msg', 'NoContent', 'There is no content yet.', '2011-06-23 08:07:19'),
(702, 1, 'en', 'backend', 'analytics', 'msg', 'NoData', 'Google has no Analytics data yet for your website. This could take a few days. Also check you Google Analytics account to make sure all settings are correct.', '2011-06-23 08:07:19'),
(703, 1, 'en', 'backend', 'analytics', 'msg', 'NoExitPages', 'There are no exit pages yet.', '2011-06-23 08:07:19'),
(704, 1, 'en', 'backend', 'analytics', 'msg', 'NoKeywords', 'There are no keywords yet.', '2011-06-23 08:07:19'),
(705, 1, 'en', 'backend', 'analytics', 'msg', 'NoLandingPages', 'There are no landing pages yet.', '2011-06-23 08:07:19'),
(706, 1, 'en', 'backend', 'analytics', 'msg', 'NoPages', 'There are ni statistics for any pages.', '2011-06-23 08:07:19'),
(707, 1, 'en', 'backend', 'analytics', 'msg', 'PagesHaveBeenViewedTimes', 'Pages on this site have been viewed %1$s times.', '2011-06-23 08:07:19'),
(708, 1, 'en', 'backend', 'analytics', 'msg', 'RefreshedTrafficSources', 'The traffic sources have been refreshed.', '2011-06-23 08:07:19'),
(709, 1, 'en', 'backend', 'analytics', 'msg', 'RemoveAccountLink', 'Remove the link with your Google account', '2011-06-23 08:07:19'),
(710, 1, 'en', 'backend', 'analytics', 'msg', 'RemoveProfileLink', 'Remove the link with your Analytics website profile', '2011-06-23 08:07:19'),
(711, 1, 'en', 'backend', 'blog', 'err', 'DeleteCategoryNotAllowed', 'It is not allowed to delete the category "%1$s".', '2011-06-23 08:07:19'),
(712, 1, 'en', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS description is not yet provided. <a href="%1$s">Configure</a>', '2011-06-23 08:07:19'),
(713, 1, 'en', 'backend', 'blog', 'lbl', 'Add', 'add article', '2011-06-23 08:07:19'),
(714, 1, 'en', 'backend', 'blog', 'msg', 'Added', 'The article "%1$s" was added.', '2011-06-23 08:07:19'),
(715, 1, 'en', 'backend', 'blog', 'msg', 'ArticlesFor', 'Articles for "%1$s"', '2011-06-23 08:07:19'),
(716, 1, 'en', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Comment on: <a href="%1$s">%2$s</a>', '2011-06-23 08:07:19'),
(717, 1, 'en', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the article "%1$s"?', '2011-06-23 08:07:19'),
(718, 1, 'en', 'backend', 'blog', 'msg', 'Deleted', 'The selected articles were deleted.', '2011-06-23 08:07:19'),
(719, 1, 'en', 'backend', 'blog', 'msg', 'DeletedSpam', 'All spam-comments were deleted.', '2011-06-23 08:07:19'),
(720, 1, 'en', 'backend', 'blog', 'msg', 'DeleteAllSpam', 'Delete all spam:', '2011-06-23 08:07:19'),
(721, 1, 'en', 'backend', 'blog', 'msg', 'EditArticle', 'edit article "%1$s"', '2011-06-23 08:07:19'),
(722, 1, 'en', 'backend', 'blog', 'msg', 'EditCommentOn', 'edit comment on "%1$s"', '2011-06-23 08:07:19'),
(723, 1, 'en', 'backend', 'blog', 'msg', 'Edited', 'The article "%1$s" was saved.', '2011-06-23 08:07:19'),
(724, 1, 'en', 'backend', 'blog', 'msg', 'EditedComment', 'The comment was saved.', '2011-06-23 08:07:19'),
(725, 1, 'en', 'backend', 'blog', 'msg', 'FollowAllCommentsInRSS', 'Follow all comments in a RSS feed: <a href="%1$s">%1$s</a>.', '2011-06-23 08:07:19'),
(726, 1, 'en', 'backend', 'blog', 'msg', 'HelpMeta', 'Show the meta information for this blogpost in the RSS feed (category', '2011-06-23 08:07:19'),
(727, 1, 'en', 'backend', 'blog', 'msg', 'HelpPingServices', 'Let various blogservices know when you''ve posted a new article.', '2011-06-23 08:07:19'),
(728, 1, 'en', 'backend', 'blog', 'msg', 'HelpSummary', 'Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.', '2011-06-23 08:07:19'),
(729, 1, 'en', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Enable the built-in spamfilter (Akismet) to help avoid spam comments.', '2011-06-23 08:07:19'),
(730, 1, 'en', 'backend', 'blog', 'msg', 'NoCategoryItems', 'There are no categories yet. <a href="%1$s">Create the first category</a>.', '2011-06-23 08:07:19'),
(731, 1, 'en', 'backend', 'blog', 'msg', 'NoItems', 'There are no articles yet. <a href="%1$s">Write the first article</a>.', '2011-06-23 08:07:19'),
(732, 1, 'en', 'backend', 'blog', 'msg', 'NotifyByEmailOnNewComment', 'Notify by email when there is a new comment.', '2011-06-23 08:07:19'),
(733, 1, 'en', 'backend', 'blog', 'msg', 'NotifyByEmailOnNewCommentToModerate', 'Notify by email when there is a new comment to moderate.', '2011-06-23 08:07:19'),
(734, 1, 'en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesFull', 'Number of articles in the recent articles (full) widget', '2011-06-23 08:07:19'),
(735, 1, 'en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesList', 'Number of articles in the recent articles (list) widget', '2011-06-23 08:07:19'),
(736, 1, 'en', 'backend', 'blog', 'msg', 'ShowOnlyItemsInCategory', 'Show only articles for:', '2011-06-23 08:07:19'),
(737, 1, 'en', 'backend', 'core', 'lbl', 'Blog', 'blog', '2011-06-23 08:07:19'),
(738, 1, 'en', 'frontend', 'core', 'act', 'ArticleCommentsRss', 'comments-on-rss', '2011-06-23 08:07:19'),
(739, 1, 'en', 'frontend', 'core', 'lbl', 'ArticlesInCategory', 'articles in category', '2011-06-23 08:07:19'),
(740, 1, 'en', 'frontend', 'core', 'lbl', 'InTheCategory', 'in category', '2011-06-23 08:07:19'),
(741, 1, 'en', 'frontend', 'core', 'lbl', 'SubscribeToTheRSSFeed', 'subscribe to the RSS feed', '2011-06-23 08:07:19'),
(742, 1, 'en', 'frontend', 'core', 'lbl', 'BlogArchive', 'blog archive', '2011-06-23 08:07:19'),
(743, 1, 'en', 'frontend', 'core', 'lbl', 'NextArticle', 'next article', '2011-06-23 08:07:19'),
(744, 1, 'en', 'frontend', 'core', 'lbl', 'PreviousArticle', 'previous article', '2011-06-23 08:07:19'),
(745, 1, 'en', 'frontend', 'core', 'lbl', 'RecentArticles', 'recent articles', '2011-06-23 08:07:19'),
(746, 1, 'en', 'frontend', 'core', 'lbl', 'Wrote', 'wrote', '2011-06-23 08:07:19'),
(747, 1, 'en', 'frontend', 'core', 'lbl', 'The', 'the', '2011-06-23 08:07:19'),
(748, 1, 'en', 'frontend', 'core', 'lbl', 'With', 'with', '2011-06-23 08:07:19'),
(749, 1, 'en', 'frontend', 'core', 'msg', 'BlogAllComments', 'All comments on your blog.', '2011-06-23 08:07:19'),
(750, 1, 'en', 'frontend', 'core', 'msg', 'BlogNoComments', 'Be the first to comment', '2011-06-23 08:07:19'),
(751, 1, 'en', 'frontend', 'core', 'msg', 'BlogNumberOfComments', '%1$s comments', '2011-06-23 08:07:19'),
(752, 1, 'en', 'frontend', 'core', 'msg', 'BlogOneComment', '1 comment already', '2011-06-23 08:07:19'),
(753, 1, 'en', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Your comment was added.', '2011-06-23 08:07:19'),
(754, 1, 'en', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Your comment is awaiting moderation.', '2011-06-23 08:07:19'),
(755, 1, 'en', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Your comment was marked as spam.', '2011-06-23 08:07:19'),
(756, 1, 'en', 'frontend', 'core', 'msg', 'BlogEmailNotificationsNewComment', '%1$s commented on <a href="%2$s">%3$s</a>.', '2011-06-23 08:07:19'),
(757, 1, 'en', 'frontend', 'core', 'msg', 'BlogEmailNotificationsNewCommentToModerate', '%1$s commented on <a href="%2$s">%3$s</a>. <a href="%4$s">Moderate</a> the comment to publish it.', '2011-06-23 08:07:19'),
(758, 1, 'en', 'frontend', 'core', 'msg', 'BlogNoItems', 'There are no articles yet.', '2011-06-23 08:07:19'),
(759, 1, 'en', 'backend', 'core', 'lbl', 'Faq', 'FAQ', '2011-06-23 08:07:19'),
(760, 1, 'en', 'backend', 'core', 'lbl', 'Questions', 'questions', '2011-06-23 08:07:19'),
(761, 1, 'en', 'backend', 'faq', 'lbl', 'AddQuestion', 'add question', '2011-06-23 08:07:19'),
(762, 1, 'en', 'backend', 'faq', 'lbl', 'Answer', 'answer', '2011-06-23 08:07:19'),
(763, 1, 'en', 'backend', 'faq', 'lbl', 'Question', 'question', '2011-06-23 08:07:19'),
(764, 1, 'en', 'backend', 'faq', 'err', 'AnswerIsRequired', 'The answer is required.', '2011-06-23 08:07:19'),
(765, 1, 'en', 'backend', 'faq', 'err', 'CategoryIsRequired', 'Please select a category.', '2011-06-23 08:07:19'),
(766, 1, 'en', 'backend', 'faq', 'err', 'QuestionIsRequired', 'The question is required.', '2011-06-23 08:07:19'),
(767, 1, 'en', 'backend', 'faq', 'msg', 'EditQuestion', 'Edit question "%1$s', '2011-06-23 08:07:19'),
(768, 1, 'en', 'backend', 'faq', 'msg', 'NoQuestionInCategory', 'There are no questions in this category.', '2011-06-23 08:07:19'),
(769, 1, 'en', 'backend', 'faq', 'msg', 'NoCategories', 'There are no categories yet', '2011-06-23 08:07:19'),
(770, 1, 'en', 'frontend', 'core', 'msg', 'NoQuestionsInCategory', 'There are no questions in this category.', '2011-06-23 08:07:19'),
(771, 1, 'en', 'backend', 'core', 'lbl', 'FormBuilder', 'formbuilder', '2011-06-23 08:07:19'),
(772, 1, 'en', 'backend', 'form_builder', 'err', 'ErrorMessageIsRequired', 'Please provide an error message.', '2011-06-23 08:07:19'),
(773, 1, 'en', 'backend', 'form_builder', 'err', 'InvalidIdentifier', 'Please provide a valid identifier. (only . - _ and alphanumeric characters)', '2011-06-23 08:07:19'),
(774, 1, 'en', 'backend', 'form_builder', 'err', 'LabelIsRequired', 'Please provide a label.', '2011-06-23 08:07:19'),
(775, 1, 'en', 'backend', 'form_builder', 'err', 'SuccessMessageIsRequired', 'Please provide a success message.', '2011-06-23 08:07:19'),
(776, 1, 'en', 'backend', 'form_builder', 'err', 'UniqueIdentifier', 'This identifier is already in use.', '2011-06-23 08:07:19'),
(777, 1, 'en', 'backend', 'form_builder', 'err', 'ValueIsRequired', 'Please provide a value.', '2011-06-23 08:07:19'),
(778, 1, 'en', 'backend', 'form_builder', 'lbl', 'Add', 'add form', '2011-06-23 08:07:19'),
(779, 1, 'en', 'backend', 'form_builder', 'lbl', 'AddFields', 'add fields', '2011-06-23 08:07:19'),
(780, 1, 'en', 'backend', 'form_builder', 'lbl', 'BackToData', 'back to submissions', '2011-06-23 08:07:19'),
(781, 1, 'en', 'backend', 'form_builder', 'lbl', 'Basic', 'basic', '2011-06-23 08:07:19'),
(782, 1, 'en', 'backend', 'form_builder', 'lbl', 'Checkbox', 'checkbox', '2011-06-23 08:07:19'),
(783, 1, 'en', 'backend', 'form_builder', 'lbl', 'DefaultValue', 'default value', '2011-06-23 08:07:19'),
(784, 1, 'en', 'backend', 'form_builder', 'lbl', 'Details', 'details', '2011-06-23 08:07:19'),
(785, 1, 'en', 'backend', 'form_builder', 'lbl', 'Drag', 'move', '2011-06-23 08:07:19'),
(786, 1, 'en', 'backend', 'form_builder', 'lbl', 'Dropdown', 'dropdown', '2011-06-23 08:07:19'),
(787, 1, 'en', 'backend', 'form_builder', 'lbl', 'EditForm', 'edit form "%1$s"', '2011-06-23 08:07:19'),
(788, 1, 'en', 'backend', 'form_builder', 'lbl', 'ErrorMessage', 'error mesage', '2011-06-23 08:07:19'),
(789, 1, 'en', 'backend', 'form_builder', 'lbl', 'Extra', 'extra', '2011-06-23 08:07:19'),
(790, 1, 'en', 'backend', 'form_builder', 'lbl', 'Fields', 'fields', '2011-06-23 08:07:19'),
(791, 1, 'en', 'backend', 'form_builder', 'lbl', 'FormData', 'submissions for "%1$s"', '2011-06-23 08:07:19'),
(792, 1, 'en', 'backend', 'form_builder', 'lbl', 'FormElements', 'form elements', '2011-06-23 08:07:19'),
(793, 1, 'en', 'backend', 'form_builder', 'lbl', 'Heading', 'heading', '2011-06-23 08:07:19'),
(794, 1, 'en', 'backend', 'form_builder', 'lbl', 'Identifier', 'identifier', '2011-06-23 08:07:19'),
(795, 1, 'en', 'backend', 'form_builder', 'lbl', 'Method', 'method', '2011-06-23 08:07:19'),
(796, 1, 'en', 'backend', 'form_builder', 'lbl', 'MethodDatabase', 'save in the database', '2011-06-23 08:07:19'),
(797, 1, 'en', 'backend', 'form_builder', 'lbl', 'MethodDatabaseEmail', 'save in the database and send email', '2011-06-23 08:07:19'),
(798, 1, 'en', 'backend', 'form_builder', 'lbl', 'MinutesAgo', '%1$s minutes ago', '2011-06-23 08:07:19'),
(799, 1, 'en', 'backend', 'form_builder', 'lbl', 'Numeric', 'numeric', '2011-06-23 08:07:19'),
(800, 1, 'en', 'backend', 'form_builder', 'lbl', 'OneMinuteAgo', '1 minute ago', '2011-06-23 08:07:19'),
(801, 1, 'en', 'backend', 'form_builder', 'lbl', 'OneSecondAgo', '1 second ago', '2011-06-23 08:07:19'),
(802, 1, 'en', 'backend', 'form_builder', 'lbl', 'Paragraph', 'paragraph', '2011-06-23 08:07:19'),
(803, 1, 'en', 'backend', 'form_builder', 'lbl', 'Parameter', 'parameter', '2011-06-23 08:07:19'),
(804, 1, 'en', 'backend', 'form_builder', 'lbl', 'Preview', 'preview', '2011-06-23 08:07:19'),
(805, 1, 'en', 'backend', 'form_builder', 'lbl', 'Properties', 'properties', '2011-06-23 08:07:19'),
(806, 1, 'en', 'backend', 'form_builder', 'lbl', 'Radiobutton', 'radiobutton', '2011-06-23 08:07:19'),
(807, 1, 'en', 'backend', 'form_builder', 'lbl', 'Recipient', 'recipient', '2011-06-23 08:07:19'),
(808, 1, 'en', 'backend', 'form_builder', 'lbl', 'Required', 'required', '2011-06-23 08:07:19'),
(809, 1, 'en', 'backend', 'form_builder', 'lbl', 'SecondsAgo', '%1$s seconds ago', '2011-06-23 08:07:19'),
(810, 1, 'en', 'backend', 'form_builder', 'lbl', 'SenderInformation', 'sender information', '2011-06-23 08:07:19'),
(811, 1, 'en', 'backend', 'form_builder', 'lbl', 'SentOn', 'sent on', '2011-06-23 08:07:19'),
(812, 1, 'en', 'backend', 'form_builder', 'lbl', 'SessionId', 'session id', '2011-06-23 08:07:19'),
(813, 1, 'en', 'backend', 'form_builder', 'lbl', 'SubmitButton', 'send button', '2011-06-23 08:07:19'),
(814, 1, 'en', 'backend', 'form_builder', 'lbl', 'SuccessMessage', 'success message', '2011-06-23 08:07:19'),
(815, 1, 'en', 'backend', 'form_builder', 'lbl', 'Textarea', 'textarea', '2011-06-23 08:07:19'),
(816, 1, 'en', 'backend', 'form_builder', 'lbl', 'Textbox', 'textbox', '2011-06-23 08:07:19'),
(817, 1, 'en', 'backend', 'form_builder', 'lbl', 'TextElements', 'text elements', '2011-06-23 08:07:19'),
(818, 1, 'en', 'backend', 'form_builder', 'lbl', 'Today', 'today', '2011-06-23 08:07:19'),
(819, 1, 'en', 'backend', 'form_builder', 'lbl', 'Validation', 'validation', '2011-06-23 08:07:19'),
(820, 1, 'en', 'backend', 'form_builder', 'lbl', 'Values', 'values', '2011-06-23 08:07:19'),
(821, 1, 'en', 'backend', 'form_builder', 'lbl', 'Yesterday', 'yesterday', '2011-06-23 08:07:19'),
(822, 1, 'en', 'backend', 'form_builder', 'msg', 'Added', 'The form "%1$s" was added.', '2011-06-23 08:07:19'),
(823, 1, 'en', 'backend', 'form_builder', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the form "%1$s" and all its submissons?', '2011-06-23 08:07:19'),
(824, 1, 'en', 'backend', 'form_builder', 'msg', 'ConfirmDeleteData', 'Are you sure you want to delete this submission?', '2011-06-23 08:07:19'),
(825, 1, 'en', 'backend', 'form_builder', 'msg', 'Deleted', 'The form "%1$s" was removed.', '2011-06-23 08:07:19'),
(826, 1, 'en', 'backend', 'form_builder', 'msg', 'Edited', 'The form "%1$s" was saved.', '2011-06-23 08:07:19'),
(827, 1, 'en', 'backend', 'form_builder', 'msg', 'HelpIdentifier', 'The identifier is placed in the URL after successfully submitting a form.', '2011-06-23 08:07:19'),
(828, 1, 'en', 'backend', 'form_builder', 'msg', 'ImportantImmediateUpdate', '<strong>Important</strong>: modifications made here are immediately saved.', '2011-06-23 08:07:19'),
(829, 1, 'en', 'backend', 'form_builder', 'msg', 'ItemDeleted', 'Submission removed.', '2011-06-23 08:07:19'),
(830, 1, 'en', 'backend', 'form_builder', 'msg', 'ItemsDeleted', 'Submissions removed.', '2011-06-23 08:07:19'),
(831, 1, 'en', 'backend', 'form_builder', 'msg', 'NoData', 'There are no submissions yet.', '2011-06-23 08:07:19'),
(832, 1, 'en', 'backend', 'form_builder', 'msg', 'NoFields', 'There are no fields yet.', '2011-06-23 08:07:19'),
(833, 1, 'en', 'backend', 'form_builder', 'msg', 'NoItems', 'There are no forms yet.', '2011-06-23 08:07:19'),
(834, 1, 'en', 'backend', 'form_builder', 'msg', 'NoValues', 'There are no values yet.', '2011-06-23 08:07:19'),
(835, 1, 'en', 'backend', 'form_builder', 'msg', 'OneSentForm', '1 submission', '2011-06-23 08:07:19'),
(836, 1, 'en', 'backend', 'form_builder', 'msg', 'SentForms', '%1$s submissions', '2011-06-23 08:07:19'),
(837, 1, 'en', 'frontend', 'core', 'err', 'FormTimeout', 'Slow down cowboy', '2011-06-23 08:07:19'),
(838, 1, 'en', 'frontend', 'core', 'err', 'NumericCharactersOnly', 'Only numeric characters are allowed.', '2011-06-23 08:07:19'),
(839, 1, 'en', 'frontend', 'core', 'lbl', 'Contact', 'contact', '2011-06-23 08:07:19'),
(840, 1, 'en', 'frontend', 'core', 'lbl', 'Content', 'content', '2011-06-23 08:07:19'),
(841, 1, 'en', 'frontend', 'core', 'lbl', 'SenderInformation', 'sender information', '2011-06-23 08:07:19'),
(842, 1, 'en', 'frontend', 'core', 'lbl', 'SentOn', 'sent on', '2011-06-23 08:07:19'),
(843, 1, 'en', 'frontend', 'core', 'msg', 'FormBuilderSubject', 'New submission for form "%1$s".', '2011-06-23 08:07:19'),
(844, 1, 'en', 'backend', 'core', 'lbl', 'Address', 'address', '2011-06-23 08:07:19'),
(845, 1, 'en', 'backend', 'core', 'lbl', 'City', 'city', '2011-06-23 08:07:19'),
(846, 1, 'en', 'backend', 'core', 'lbl', 'Country', 'country', '2011-06-23 08:07:19'),
(847, 1, 'en', 'backend', 'core', 'lbl', 'GroupMap', 'general map: all locations', '2011-06-23 08:07:19'),
(848, 1, 'en', 'backend', 'core', 'lbl', 'Height', 'height', '2011-06-23 08:07:19'),
(849, 1, 'en', 'backend', 'core', 'lbl', 'IndividualMap', 'widget: individual map', '2011-06-23 08:07:19'),
(850, 1, 'en', 'backend', 'core', 'lbl', 'Location', 'location', '2011-06-23 08:07:19'),
(851, 1, 'en', 'backend', 'core', 'lbl', 'Number', 'number', '2011-06-23 08:07:19'),
(852, 1, 'en', 'backend', 'core', 'lbl', 'Street', 'street', '2011-06-23 08:07:19'),
(853, 1, 'en', 'backend', 'core', 'lbl', 'Width', 'width', '2011-06-23 08:07:19'),
(854, 1, 'en', 'backend', 'core', 'lbl', 'Zip', 'zip code', '2011-06-23 08:07:19'),
(855, 1, 'en', 'backend', 'location', 'lbl', 'Auto', 'automatic', '2011-06-23 08:07:19'),
(856, 1, 'en', 'backend', 'location', 'lbl', 'Hybrid', 'hybrid', '2011-06-23 08:07:19'),
(857, 1, 'en', 'backend', 'location', 'lbl', 'Map', 'map', '2011-06-23 08:07:19'),
(858, 1, 'en', 'backend', 'location', 'lbl', 'MapType', 'map type', '2011-06-23 08:07:19'),
(859, 1, 'en', 'backend', 'location', 'lbl', 'Roadmap', 'road map', '2011-06-23 08:07:19'),
(860, 1, 'en', 'backend', 'location', 'lbl', 'Satellite', 'satellite', '2011-06-23 08:07:19'),
(861, 1, 'en', 'backend', 'location', 'lbl', 'Terrain', 'terrain', '2011-06-23 08:07:19'),
(862, 1, 'en', 'backend', 'location', 'lbl', 'ZoomLevel', 'zoom level', '2011-06-23 08:07:19'),
(863, 1, 'en', 'backend', 'location', 'err', 'AddressCouldNotBeGeocoded', 'Address couldn''t be converted into coordinates.', '2011-06-23 08:07:19'),
(864, 1, 'en', 'frontend', 'core', 'act', 'Preview', 'preview', '2011-06-23 08:07:19'),
(865, 1, 'en', 'frontend', 'core', 'act', 'Subscribe', 'subscribe', '2011-06-23 08:07:19'),
(866, 1, 'en', 'frontend', 'core', 'act', 'Unsubscribe', 'unsubscribe', '2011-06-23 08:07:19'),
(867, 1, 'en', 'frontend', 'core', 'err', 'AlreadySubscribed', 'This e-mail address is already subscribed to the newsletter.', '2011-06-23 08:07:19'),
(868, 1, 'en', 'frontend', 'core', 'err', 'AlreadyUnsubscribed', 'This e-mail address is already unsubscribed from the newsletter', '2011-06-23 08:07:19'),
(869, 1, 'en', 'frontend', 'core', 'err', 'EmailNotInDatabase', 'This e-mail address does not exist in the database.', '2011-06-23 08:07:19'),
(870, 1, 'en', 'frontend', 'core', 'err', 'SubscribeFailed', 'Subscribing failed', '2011-06-23 08:07:19'),
(871, 1, 'en', 'frontend', 'core', 'err', 'UnsubscribeFailed', 'Unsubscribing failed', '2011-06-23 08:07:19'),
(872, 1, 'en', 'frontend', 'core', 'lbl', 'Sent', 'sent', '2011-06-23 08:07:19'),
(873, 1, 'en', 'frontend', 'core', 'lbl', 'SentMailings', 'sent mailings', '2011-06-23 08:07:19'),
(874, 1, 'en', 'frontend', 'core', 'lbl', 'Subscribe', 'subscribe', '2011-06-23 08:07:19'),
(875, 1, 'en', 'frontend', 'core', 'lbl', 'Unsubscribe', 'unsubscribe', '2011-06-23 08:07:19'),
(876, 1, 'en', 'frontend', 'core', 'msg', 'NoSentMailings', 'So far', '2011-06-23 08:07:19'),
(877, 1, 'en', 'frontend', 'core', 'msg', 'SubscribeSuccess', 'You have successfully subscribed to the newsletter.', '2011-06-23 08:07:19'),
(878, 1, 'en', 'frontend', 'core', 'msg', 'UnsubscribeSuccess', 'You have successfully unsubscribed from the newsletter.', '2011-06-23 08:07:19'),
(879, 1, 'en', 'backend', 'core', 'lbl', 'AccountSettings', 'account settings', '2011-06-23 08:07:19'),
(880, 1, 'en', 'backend', 'core', 'lbl', 'Addresses', 'e-mail addresses', '2011-06-23 08:07:19'),
(881, 1, 'en', 'backend', 'core', 'lbl', 'AllAddresses', 'all e-mail addresses', '2011-06-23 08:07:19'),
(882, 1, 'en', 'backend', 'core', 'lbl', 'Bounces', 'bounces', '2011-06-23 08:07:19'),
(883, 1, 'en', 'backend', 'core', 'lbl', 'BounceType', 'bounce type', '2011-06-23 08:07:19'),
(884, 1, 'en', 'backend', 'core', 'lbl', 'Campaigns', 'campaigns', '2011-06-23 08:07:19'),
(885, 1, 'en', 'backend', 'core', 'lbl', 'ClientSettings', 'client settings', '2011-06-23 08:07:19'),
(886, 1, 'en', 'backend', 'core', 'lbl', 'Copy', 'copy', '2011-06-23 08:07:19'),
(887, 1, 'en', 'backend', 'core', 'lbl', 'Created', 'created', '2011-06-23 08:07:19'),
(888, 1, 'en', 'backend', 'core', 'lbl', 'CreatedOn', 'created on', '2011-06-23 08:07:19'),
(889, 1, 'en', 'backend', 'core', 'lbl', 'EN', 'english', '2011-06-23 08:07:19'),
(890, 1, 'en', 'backend', 'core', 'lbl', 'For', 'for', '2011-06-23 08:07:19'),
(891, 1, 'en', 'backend', 'core', 'lbl', 'FR', 'french', '2011-06-23 08:07:19'),
(892, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorGroups', 'groups', '2011-06-23 08:07:19'),
(893, 1, 'en', 'backend', 'core', 'lbl', 'ImportNoun', 'import', '2011-06-23 08:07:19'),
(894, 1, 'en', 'backend', 'core', 'lbl', 'In', 'in', '2011-06-23 08:07:19'),
(895, 1, 'en', 'backend', 'core', 'lbl', 'Mailmotor', 'mailmotor', '2011-06-23 08:07:19'),
(896, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorClicks', 'clicks', '2011-06-23 08:07:19'),
(897, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorLatestMailing', 'last sent mailing', '2011-06-23 08:07:19'),
(898, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorOpened', 'opened', '2011-06-23 08:07:19'),
(899, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorSendDate', 'send date', '2011-06-23 08:07:19'),
(900, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorSent', 'sent', '2011-06-23 08:07:19'),
(901, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorStatistics', 'statistics', '2011-06-23 08:07:19'),
(902, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorSubscriptions', 'subscriptions', '2011-06-23 08:07:19'),
(903, 1, 'en', 'backend', 'core', 'lbl', 'MailmotorUnsubscriptions', 'unsubscriptions', '2011-06-23 08:07:19'),
(904, 1, 'en', 'backend', 'core', 'lbl', 'Newsletters', 'mailings', '2011-06-23 08:07:19'),
(905, 1, 'en', 'backend', 'core', 'lbl', 'NL', 'dutch', '2011-06-23 08:07:19'),
(906, 1, 'en', 'backend', 'core', 'lbl', 'Person', 'person', '2011-06-23 08:07:19'),
(907, 1, 'en', 'backend', 'core', 'lbl', 'Persons', 'people', '2011-06-23 08:07:19'),
(908, 1, 'en', 'backend', 'core', 'lbl', 'Price', 'price', '2011-06-23 08:07:19'),
(909, 1, 'en', 'backend', 'core', 'lbl', 'QuantityNo', 'no', '2011-06-23 08:07:19'),
(910, 1, 'en', 'backend', 'core', 'lbl', 'SentMailings', 'sent mailings', '2011-06-23 08:07:19'),
(911, 1, 'en', 'backend', 'core', 'lbl', 'SentOn', 'sent on', '2011-06-23 08:07:19'),
(912, 1, 'en', 'backend', 'core', 'lbl', 'Source', 'source', '2011-06-23 08:07:19'),
(913, 1, 'en', 'backend', 'core', 'lbl', 'Subscriptions', 'subscriptions', '2011-06-23 08:07:19'),
(914, 1, 'en', 'backend', 'core', 'lbl', 'SubscribeForm', 'subscribe form', '2011-06-23 08:07:19'),
(915, 1, 'en', 'backend', 'core', 'lbl', 'Timezone', 'timezone', '2011-06-23 08:07:19'),
(916, 1, 'en', 'backend', 'core', 'lbl', 'ToStep', 'to step', '2011-06-23 08:07:19'),
(917, 1, 'en', 'backend', 'core', 'lbl', 'Unsubscriptions', 'unsubscriptions', '2011-06-23 08:07:19'),
(918, 1, 'en', 'backend', 'core', 'lbl', 'UnsubscribeForm', 'unsubscribe form', '2011-06-23 08:07:19'),
(919, 1, 'en', 'backend', 'core', 'msg', 'AllAddresses', 'All addresses sorted by subscription date.', '2011-06-23 08:07:19'),
(920, 1, 'en', 'backend', 'core', 'msg', 'NoSentMailings', 'No mailings have been sent yet.', '2011-06-23 08:07:19'),
(921, 1, 'en', 'backend', 'core', 'msg', 'NoSubscriptions', 'No one subscribed to the mailinglist yet.', '2011-06-23 08:07:19'),
(922, 1, 'en', 'backend', 'core', 'msg', 'NoUnsubscriptions', 'No one unsubscribed from from the mailinglist yet.', '2011-06-23 08:07:19'),
(923, 1, 'en', 'backend', 'mailmotor', 'err', 'AnalysisNoCMAccount', 'There is no link with a CampaignMonitor account yet. <a href="%1$s">Configure</a>', '2011-06-23 08:07:19'),
(924, 1, 'en', 'backend', 'mailmotor', 'err', 'AnalysisNoCMClientID', 'There is no client linked to the CampaignMonitor account yet. <a href="%1$s">Configure</a>', '2011-06-23 08:07:19'),
(925, 1, 'en', 'backend', 'mailmotor', 'err', 'AddressDoesNotExist', 'The given e-mail address does not exist.', '2011-06-23 08:07:19'),
(926, 1, 'en', 'backend', 'mailmotor', 'err', 'AlreadySubscribed', 'This e-mail address is already subscribed to the mailinglist.', '2011-06-23 08:07:19'),
(927, 1, 'en', 'backend', 'mailmotor', 'err', 'AddMailingNoGroups', 'There are no groups to put subscribers in yet. Create a group first.', '2011-06-23 08:07:19'),
(928, 1, 'en', 'backend', 'mailmotor', 'err', 'CampaignDoesNotExist', 'The given campaign does not exist.', '2011-06-23 08:07:19'),
(929, 1, 'en', 'backend', 'mailmotor', 'err', 'CampaignExists', 'This campaign name already exists.', '2011-06-23 08:07:19'),
(930, 1, 'en', 'backend', 'mailmotor', 'err', 'CampaignNotEdited', 'The campaign wasn''t edited.', '2011-06-23 08:07:19'),
(931, 1, 'en', 'backend', 'mailmotor', 'err', 'CampaignMonitorError', 'CampaignMonitor error: %1$s', '2011-06-23 08:07:19'),
(932, 1, 'en', 'backend', 'mailmotor', 'err', 'ChooseAtLeastOneGroup', 'You need to choose at least one group.', '2011-06-23 08:07:19');
INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(933, 1, 'en', 'backend', 'mailmotor', 'err', 'ChooseTemplateLanguage', 'Choose the language of the template to use.', '2011-06-23 08:07:19'),
(934, 1, 'en', 'backend', 'mailmotor', 'err', 'ClassDoesNotExist', 'The CampaignMonitor wrapper class is not found. Please locate and place it in /library/external', '2011-06-23 08:07:19'),
(935, 1, 'en', 'backend', 'mailmotor', 'err', 'CmTimeout', 'Could not make a connection with CampaignMonitor,m try again.', '2011-06-23 08:07:19'),
(936, 1, 'en', 'backend', 'mailmotor', 'err', 'CompleteStep2', 'Complete step 2 first', '2011-06-23 08:07:19'),
(937, 1, 'en', 'backend', 'mailmotor', 'err', 'CompleteStep3', 'Complete step 3 first', '2011-06-23 08:07:19'),
(938, 1, 'en', 'backend', 'mailmotor', 'err', 'ContainsInvalidEmail', 'This field contains an invalid emailaddress.', '2011-06-23 08:07:19'),
(939, 1, 'en', 'backend', 'mailmotor', 'err', 'CSVIsRequired', 'Choose a .csv file to upload.', '2011-06-23 08:07:19'),
(940, 1, 'en', 'backend', 'mailmotor', 'err', 'CouldNotConnect', 'Could not connect to CampaignMonitor', '2011-06-23 08:07:19'),
(941, 1, 'en', 'backend', 'mailmotor', 'err', 'CustomFieldExists', 'This field name is already in use.', '2011-06-23 08:07:19'),
(942, 1, 'en', 'backend', 'mailmotor', 'err', 'DuplicateCampaignName', 'The name of this mailing already exists in the archives of CampaignMonitor. Change the name before sending.', '2011-06-23 08:07:19'),
(943, 1, 'en', 'backend', 'mailmotor', 'err', 'GroupAlreadyExists', 'This group already exists', '2011-06-23 08:07:19'),
(944, 1, 'en', 'backend', 'mailmotor', 'err', 'GroupsNoRecipients', 'The selected group(s) don''t contain any addresses.', '2011-06-23 08:07:19'),
(945, 1, 'en', 'backend', 'mailmotor', 'err', 'HTMLContentURLRequired', 'CampaignMonitor could not find an URL to the HTML content. (The URL needs to be accessible)', '2011-06-23 08:07:19'),
(946, 1, 'en', 'backend', 'mailmotor', 'err', 'ImportedAddresses', '%1$s addresses are imported in %2$s group(s)', '2011-06-23 08:07:19'),
(947, 1, 'en', 'backend', 'mailmotor', 'err', 'InvalidAccountCredentials', 'The CampaignMonitor account credentials are invalid.', '2011-06-23 08:07:19'),
(948, 1, 'en', 'backend', 'mailmotor', 'err', 'InvalidCSV', 'The CSV file is empty', '2011-06-23 08:07:19'),
(949, 1, 'en', 'backend', 'mailmotor', 'err', 'LinkDoesNotExist', 'This link doesn''t exists.', '2011-06-23 08:07:19'),
(950, 1, 'en', 'backend', 'mailmotor', 'err', 'LinkDoesNotExists', 'This link doesn''t exists.', '2011-06-23 08:07:19'),
(951, 1, 'en', 'backend', 'mailmotor', 'err', 'MailingAlreadyExists', 'This mailing already exists', '2011-06-23 08:07:19'),
(952, 1, 'en', 'backend', 'mailmotor', 'err', 'MailingAlreadySent', 'The given mailing has already been sent!', '2011-06-23 08:07:19'),
(953, 1, 'en', 'backend', 'mailmotor', 'err', 'MailingDoesNotExist', 'The given mailing does not exist.', '2011-06-23 08:07:19'),
(954, 1, 'en', 'backend', 'mailmotor', 'err', 'NoActionSelected', 'No action selected.', '2011-06-23 08:07:19'),
(955, 1, 'en', 'backend', 'mailmotor', 'err', 'NoBounces', 'There are no bounces for this mailing.', '2011-06-23 08:07:19'),
(956, 1, 'en', 'backend', 'mailmotor', 'err', 'NoCMAccount', 'There is no link with a CampaignMonitor account yet.', '2011-06-23 08:07:19'),
(957, 1, 'en', 'backend', 'mailmotor', 'err', 'NoCMClientID', 'There is no client linked to the CampaignMonitor account yet.', '2011-06-23 08:07:19'),
(958, 1, 'en', 'backend', 'mailmotor', 'err', 'NoCMAccountCredentials', 'Please enter your CampaignMonitor credentials.', '2011-06-23 08:07:19'),
(959, 1, 'en', 'backend', 'mailmotor', 'err', 'NoGroups', 'Select a group.', '2011-06-23 08:07:19'),
(960, 1, 'en', 'backend', 'mailmotor', 'err', 'NoSubject', 'Enter a subject for this mailing.', '2011-06-23 08:07:19'),
(961, 1, 'en', 'backend', 'mailmotor', 'err', 'NoSubscribers', 'None of your groups have subscribers yet! You can import your current subscriber list by uploading a .csv-file.', '2011-06-23 08:07:19'),
(962, 1, 'en', 'backend', 'mailmotor', 'err', 'NoTemplates', 'No templates are available for this language', '2011-06-23 08:07:19'),
(963, 1, 'en', 'backend', 'mailmotor', 'err', 'NoPreviewSent', 'The preview-mail to %1$s was not sent.', '2011-06-23 08:07:19'),
(964, 1, 'en', 'backend', 'mailmotor', 'err', 'NoPricePerEmail', 'No price per sent mail has been set yet.', '2011-06-23 08:07:19'),
(965, 1, 'en', 'backend', 'mailmotor', 'err', 'NoStatisticsLoaded', 'There are no statistics available yet for mailing &ldquo;%1$s&rdquo;.', '2011-06-23 08:07:19'),
(966, 1, 'en', 'backend', 'mailmotor', 'err', 'PaymentDetailsRequired', 'The payment details of the active user (%1$s) are not yet set in the CampaignMonitor backend.', '2011-06-23 08:07:19'),
(967, 1, 'en', 'backend', 'mailmotor', 'err', 'TemplateIsRequired', 'Choose a template first before proceeding to the next step.', '2011-06-23 08:07:19'),
(968, 1, 'en', 'backend', 'mailmotor', 'err', 'TemplateDoesNotExist', 'This template does not exist', '2011-06-23 08:07:19'),
(969, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddCampaign', 'add campaign', '2011-06-23 08:07:19'),
(970, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddCustomField', 'add custom field', '2011-06-23 08:07:19'),
(971, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddEmail', 'add e-mail address', '2011-06-23 08:07:19'),
(972, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddGroup', 'add group', '2011-06-23 08:07:19'),
(973, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddNewMailing', 'create new mailing', '2011-06-23 08:07:19'),
(974, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddMailing', 'add mailing', '2011-06-23 08:07:19'),
(975, 1, 'en', 'backend', 'mailmotor', 'lbl', 'AddressList', 'mailinglist', '2011-06-23 08:07:19'),
(976, 1, 'en', 'backend', 'mailmotor', 'lbl', 'BounceRate', 'bounce-rate', '2011-06-23 08:07:19'),
(977, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Campaign', 'campaign', '2011-06-23 08:07:19'),
(978, 1, 'en', 'backend', 'mailmotor', 'lbl', 'CampaignName', 'campaign', '2011-06-23 08:07:19'),
(979, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ChooseTemplate', 'choose a template', '2011-06-23 08:07:19'),
(980, 1, 'en', 'backend', 'mailmotor', 'lbl', 'CompanyName', 'company name', '2011-06-23 08:07:19'),
(981, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ContactName', 'contact', '2011-06-23 08:07:19'),
(982, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ClickRate', 'click-rate', '2011-06-23 08:07:19'),
(983, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Clicks', 'clicks', '2011-06-23 08:07:19'),
(984, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Client', 'client', '2011-06-23 08:07:19'),
(985, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ClientID', 'client ID', '2011-06-23 08:07:19'),
(986, 1, 'en', 'backend', 'mailmotor', 'lbl', 'CreateNewClient', 'create a new client', '2011-06-23 08:07:19'),
(987, 1, 'en', 'backend', 'mailmotor', 'lbl', 'CustomFields', 'custom fields', '2011-06-23 08:07:19'),
(988, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EditCampaign', 'edit campaign', '2011-06-23 08:07:19'),
(989, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EditMailingCampaign', 'edit campaign', '2011-06-23 08:07:19'),
(990, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EditCustomField', 'edit custom field', '2011-06-23 08:07:19'),
(991, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EditEmail', 'edit e-mail address', '2011-06-23 08:07:19'),
(992, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EditGroup', 'edit group', '2011-06-23 08:07:19'),
(993, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EmailAddress', 'e-mail address', '2011-06-23 08:07:19'),
(994, 1, 'en', 'backend', 'mailmotor', 'lbl', 'EmailAddresses', 'e-mail addresses', '2011-06-23 08:07:19'),
(995, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ExampleFile', 'an example file', '2011-06-23 08:07:19'),
(996, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ExportAddresses', 'export addresses', '2011-06-23 08:07:19'),
(997, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ExportStatistics', 'export statistics', '2011-06-23 08:07:19'),
(998, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Group', 'group', '2011-06-23 08:07:19'),
(999, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Groups', 'groups', '2011-06-23 08:07:19'),
(1000, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ImportAddresses', 'import addresses', '2011-06-23 08:07:19'),
(1001, 1, 'en', 'backend', 'mailmotor', 'lbl', 'IpAddress', 'IP address', '2011-06-23 08:07:19'),
(1002, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Manual', 'manual', '2011-06-23 08:07:19'),
(1003, 1, 'en', 'backend', 'mailmotor', 'lbl', 'MailingsWithoutCampaign', 'mailings without campaign', '2011-06-23 08:07:19'),
(1004, 1, 'en', 'backend', 'mailmotor', 'lbl', 'NoCampaign', 'no campaign', '2011-06-23 08:07:19'),
(1005, 1, 'en', 'backend', 'mailmotor', 'lbl', 'OpenedMailings', 'opened mailings', '2011-06-23 08:07:19'),
(1006, 1, 'en', 'backend', 'mailmotor', 'lbl', 'PlainTextVersion', 'plain text version', '2011-06-23 08:07:19'),
(1007, 1, 'en', 'backend', 'mailmotor', 'lbl', 'PricePerSentMailing', 'price per sent mailing', '2011-06-23 08:07:19'),
(1008, 1, 'en', 'backend', 'mailmotor', 'lbl', 'QueuedMailings', 'queued mailings', '2011-06-23 08:07:19'),
(1009, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Recipients', 'groups', '2011-06-23 08:07:19'),
(1010, 1, 'en', 'backend', 'mailmotor', 'lbl', 'ReplyTo', 'reply-to address', '2011-06-23 08:07:19'),
(1011, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Reset', 'reset', '2011-06-23 08:07:19'),
(1012, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SendDate', 'send date', '2011-06-23 08:07:19'),
(1013, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Sender', 'sender', '2011-06-23 08:07:19'),
(1014, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SendMailing', 'send mailing', '2011-06-23 08:07:19'),
(1015, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SendOn', 'send on', '2011-06-23 08:07:19'),
(1016, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SendPreview', 'send preview', '2011-06-23 08:07:19'),
(1017, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Sent', 'sent', '2011-06-23 08:07:19'),
(1018, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SentMailings', 'sent mailings', '2011-06-23 08:07:19'),
(1019, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SettingsAccount', 'account settings', '2011-06-23 08:07:19'),
(1020, 1, 'en', 'backend', 'mailmotor', 'lbl', 'SettingsClient', 'client settings', '2011-06-23 08:07:19'),
(1021, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Subject', 'subject', '2011-06-23 08:07:19'),
(1022, 1, 'en', 'backend', 'mailmotor', 'lbl', 'TemplateDefault', 'default template', '2011-06-23 08:07:19'),
(1023, 1, 'en', 'backend', 'mailmotor', 'lbl', 'TemplateEmpty', 'empty template', '2011-06-23 08:07:19'),
(1024, 1, 'en', 'backend', 'mailmotor', 'lbl', 'TemplateFork', 'Fork CMS template', '2011-06-23 08:07:19'),
(1025, 1, 'en', 'backend', 'mailmotor', 'lbl', 'TemplateLanguage', 'template language', '2011-06-23 08:07:19'),
(1026, 1, 'en', 'backend', 'mailmotor', 'lbl', 'TotalSentMailings', 'sent mailings', '2011-06-23 08:07:19'),
(1027, 1, 'en', 'backend', 'mailmotor', 'lbl', 'UnopenedMailings', 'unopened mailings', '2011-06-23 08:07:19'),
(1028, 1, 'en', 'backend', 'mailmotor', 'lbl', 'UnsentMailings', 'concepts', '2011-06-23 08:07:19'),
(1029, 1, 'en', 'backend', 'mailmotor', 'lbl', 'Who', 'who?', '2011-06-23 08:07:19'),
(1030, 1, 'en', 'backend', 'mailmotor', 'lbl', 'WillBeSentOn', 'will be sent on', '2011-06-23 08:07:19'),
(1031, 1, 'en', 'backend', 'mailmotor', 'lbl', 'WizardInformation', 'configuration', '2011-06-23 08:07:19'),
(1032, 1, 'en', 'backend', 'mailmotor', 'lbl', 'WizardTemplate', 'template', '2011-06-23 08:07:19'),
(1033, 1, 'en', 'backend', 'mailmotor', 'lbl', 'WizardContent', 'content', '2011-06-23 08:07:19'),
(1034, 1, 'en', 'backend', 'mailmotor', 'lbl', 'WizardSend', 'send', '2011-06-23 08:07:19'),
(1035, 1, 'en', 'backend', 'mailmotor', 'msg', 'AccountLinked', 'Your CampaignMonitor account is now linked to Fork.', '2011-06-23 08:07:19'),
(1036, 1, 'en', 'backend', 'mailmotor', 'msg', 'AddMultipleEmails', 'add multiple email addresses by using a comma (,) as a delimiter', '2011-06-23 08:07:19'),
(1037, 1, 'en', 'backend', 'mailmotor', 'msg', 'BackToCampaigns', 'Back to campaign overview', '2011-06-23 08:07:19'),
(1038, 1, 'en', 'backend', 'mailmotor', 'msg', 'BackToMailings', 'Back to mailings overview', '2011-06-23 08:07:19'),
(1039, 1, 'en', 'backend', 'mailmotor', 'msg', 'BackToStatistics', 'Back to statistics for &ldquo;%1$s&rdquo;', '2011-06-23 08:07:19'),
(1040, 1, 'en', 'backend', 'mailmotor', 'msg', 'CampaignAdded', 'The campaign has been added successfully.', '2011-06-23 08:07:19'),
(1041, 1, 'en', 'backend', 'mailmotor', 'msg', 'CampaignEdited', 'The campaign has been edited successfully.', '2011-06-23 08:07:19'),
(1042, 1, 'en', 'backend', 'mailmotor', 'msg', 'CampaignMailings', 'mailings in this campaign', '2011-06-23 08:07:19'),
(1043, 1, 'en', 'backend', 'mailmotor', 'msg', 'ClickedLinks', 'ontvangers hebben op links geklikt.', '2011-06-23 08:07:19'),
(1044, 1, 'en', 'backend', 'mailmotor', 'msg', 'ClicksAmount', 'number of clicks', '2011-06-23 08:07:19'),
(1045, 1, 'en', 'backend', 'mailmotor', 'msg', 'ClicksBreakdown', '%1$s of opened', '2011-06-23 08:07:19'),
(1046, 1, 'en', 'backend', 'mailmotor', 'msg', 'ClicksOpened', 'times opened', '2011-06-23 08:07:19'),
(1047, 1, 'en', 'backend', 'mailmotor', 'msg', 'ClientLinked', 'The client &ldquo;%1$s&rdquo; is now linked to Fork.', '2011-06-23 08:07:19'),
(1048, 1, 'en', 'backend', 'mailmotor', 'msg', 'CreateGroupByAddresses', 'Create a new group with the addresses below.', '2011-06-23 08:07:19'),
(1049, 1, 'en', 'backend', 'mailmotor', 'msg', 'DefaultGroup', 'Selecting a language here will mark this as the default group for that language. This means visitors who subscribe to your mailinglist in this language version of your website will end up in this group. Only one default group can be set for each language.', '2011-06-23 08:07:19'),
(1050, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeleteAddresses', 'The address(es) have been deleted successfully.', '2011-06-23 08:07:19'),
(1051, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeleteBounces', 'Delete all hard bounces', '2011-06-23 08:07:19'),
(1052, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeletedBounces', 'The hard bounces for this mailing have been deleted.', '2011-06-23 08:07:19'),
(1053, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeletedCustomFields', 'The custom fields for group &ldquo;%1$s&rdquo; have been deleted successfully.', '2011-06-23 08:07:19'),
(1054, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeleteCampaigns', 'The campaigns have been deleted successfully.', '2011-06-23 08:07:19'),
(1055, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeleteGroups', 'The groups have been deleted successfully.', '2011-06-23 08:07:19'),
(1056, 1, 'en', 'backend', 'mailmotor', 'msg', 'DeleteMailings', 'The mailings have been deleted successfully.', '2011-06-23 08:07:19'),
(1057, 1, 'en', 'backend', 'mailmotor', 'msg', 'EditMailingCampaign', 'Edit campaign', '2011-06-23 08:07:19'),
(1058, 1, 'en', 'backend', 'mailmotor', 'msg', 'ExportFailed', 'Export failed.', '2011-06-23 08:07:19'),
(1059, 1, 'en', 'backend', 'mailmotor', 'msg', 'GroupAdded', 'The group has been added successfully.', '2011-06-23 08:07:19'),
(1060, 1, 'en', 'backend', 'mailmotor', 'msg', 'GroupsImported', '%1$s group(s) and %2$s email-addresses were imported from CampaignMonitor. Don''t forget to select a default group for each language!', '2011-06-23 08:07:19'),
(1061, 1, 'en', 'backend', 'mailmotor', 'msg', 'GroupsNumberOfRecipients', 'This group contains %1$s address(es).', '2011-06-23 08:07:19'),
(1062, 1, 'en', 'backend', 'mailmotor', 'msg', 'HelpCMURL', 'The URL of the CampaignMonitor API for your account. (ex. *.createsend.com)', '2011-06-23 08:07:19'),
(1063, 1, 'en', 'backend', 'mailmotor', 'msg', 'HelpCustomFields', 'Custom fields are variables that hold a unique value for each e-mail address in a group. This way you can send personalized mailings.', '2011-06-23 08:07:19'),
(1064, 1, 'en', 'backend', 'mailmotor', 'msg', 'ImportedAddresses', '%1$s addresses are imported in %2$s group(s).', '2011-06-23 08:07:19'),
(1065, 1, 'en', 'backend', 'mailmotor', 'msg', 'ImportFailedDownloadCSV', 'Download a CSV with the failed addresses <a href="%1$s">here</a>.', '2011-06-23 08:07:19'),
(1066, 1, 'en', 'backend', 'mailmotor', 'msg', 'ImportGroupsTitle', 'Import groups from CampaignMonitor', '2011-06-23 08:07:19'),
(1067, 1, 'en', 'backend', 'mailmotor', 'msg', 'ImportGroups', 'Fork has found the following groups in Campaignmonitor', '2011-06-23 08:07:19'),
(1068, 1, 'en', 'backend', 'mailmotor', 'msg', 'ImportRecentlyFailed', 'Not all addresses were imported.', '2011-06-23 08:07:19'),
(1069, 1, 'en', 'backend', 'mailmotor', 'msg', 'LinkCMAccount', 'Link CampaignMonitor account', '2011-06-23 08:07:19'),
(1070, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingAdded', 'The mailing has been added successfully.', '2011-06-23 08:07:19'),
(1071, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingConfirmSend', 'Are you sure you want to send the mailing?', '2011-06-23 08:07:19'),
(1072, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingConfirmTitle', 'Send this mailing.', '2011-06-23 08:07:19'),
(1073, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCopied', 'The mailing &ldquo;%1$s&rdquo; has been copied successfully.', '2011-06-23 08:07:19'),
(1074, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVBounces', 'bounces', '2011-06-23 08:07:19'),
(1075, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVBouncesPercentage', '% bounces', '2011-06-23 08:07:19'),
(1076, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVOpens', 'opened mailings', '2011-06-23 08:07:19'),
(1077, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVRecipients', 'total sent mailings', '2011-06-23 08:07:19'),
(1078, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVUniqueOpens', 'uniquely opened mailings', '2011-06-23 08:07:19'),
(1079, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVUniqueOpensPercentage', '% opened mailings', '2011-06-23 08:07:19'),
(1080, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVUnopens', 'opened mailings', '2011-06-23 08:07:19'),
(1081, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVUnopensPercentage', '% unopened mailings', '2011-06-23 08:07:19'),
(1082, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingCSVUnsubscribes', 'unsubscribes', '2011-06-23 08:07:19'),
(1083, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingEdited', 'The mailing has been edited successfully.', '2011-06-23 08:07:19'),
(1084, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingLinks', 'links in this mailing', '2011-06-23 08:07:19'),
(1085, 1, 'en', 'backend', 'mailmotor', 'msg', 'MailingSent', 'The mailing has been sent successfully.', '2011-06-23 08:07:19'),
(1086, 1, 'en', 'backend', 'mailmotor', 'msg', 'NameInternalUseOnly', 'This name is for internal use only.', '2011-06-23 08:07:19'),
(1087, 1, 'en', 'backend', 'mailmotor', 'msg', 'NoClientID', 'No CampaignMonitor client has been linked to Fork yet. Choose an existing client from the dropdown', '2011-06-23 08:07:19'),
(1088, 1, 'en', 'backend', 'mailmotor', 'msg', 'NoDefault', 'This is not a default group.', '2011-06-23 08:07:19'),
(1089, 1, 'en', 'backend', 'mailmotor', 'msg', 'NoDefaultsSet', 'A default group for a language is the group where visitors end up in when they subscribe through the subscribe-forms on your Fork website. Edit a group and select a language to set it as the default group for that language.', '2011-06-23 08:07:19'),
(1090, 1, 'en', 'backend', 'mailmotor', 'msg', 'NoDefaultsSetTitle', 'Not all default groups are set yet.', '2011-06-23 08:07:19'),
(1091, 1, 'en', 'backend', 'mailmotor', 'msg', 'NoResultsForFilter', 'No results for search term &ldquo;%1$s&rdquo;.', '2011-06-23 08:07:19'),
(1092, 1, 'en', 'backend', 'mailmotor', 'msg', 'NoUnsentMailings', 'There are no concepts available.', '2011-06-23 08:07:19'),
(1093, 1, 'en', 'backend', 'mailmotor', 'msg', 'PeopleGroups', 'These people come from the following groups:', '2011-06-23 08:07:19'),
(1094, 1, 'en', 'backend', 'mailmotor', 'msg', 'PlainTextEditable', 'Make the textual version of each individual mail adjustable.', '2011-06-23 08:07:19'),
(1095, 1, 'en', 'backend', 'mailmotor', 'msg', 'PreviewSent', 'The preview-mail has been sent to %1$s.', '2011-06-23 08:07:19'),
(1096, 1, 'en', 'backend', 'mailmotor', 'msg', 'Reason', 'reason', '2011-06-23 08:07:19'),
(1097, 1, 'en', 'backend', 'mailmotor', 'msg', 'RecipientStatisticsCampaign', 'You are about to send the mailing "%1$s" from campaign "%2$s" to %3$s %4$s.', '2011-06-23 08:07:19'),
(1098, 1, 'en', 'backend', 'mailmotor', 'msg', 'RecipientStatisticsNoCampaign', 'You are about to send the mailing "%1$s" to %2$s %3$s.', '2011-06-23 08:07:19'),
(1099, 1, 'en', 'backend', 'mailmotor', 'msg', 'ResetCampaigns', 'campaigns', '2011-06-23 08:07:19'),
(1100, 1, 'en', 'backend', 'mailmotor', 'msg', 'ResetDone', 'Don''t forget to remove the client in the CampaignMonitor backend if it''s no longer to be used.<br />This is done because there is a limit to the number of clients you can add through the API.<br /><br />Use the following client ID if you don''t wish to remove your client: <strong>%1$s</strong>', '2011-06-23 08:07:19'),
(1101, 1, 'en', 'backend', 'mailmotor', 'msg', 'ResetGroups', 'groups (with addresses)', '2011-06-23 08:07:19'),
(1102, 1, 'en', 'backend', 'mailmotor', 'msg', 'ResetLabels', 'labels', '2011-06-23 08:07:19'),
(1103, 1, 'en', 'backend', 'mailmotor', 'msg', 'ResetMailings', 'mailings', '2011-06-23 08:07:19'),
(1104, 1, 'en', 'backend', 'mailmotor', 'msg', 'ResetSettings', 'module settings', '2011-06-23 08:07:19'),
(1105, 1, 'en', 'backend', 'mailmotor', 'msg', 'SendOn', 'The mailing will be sent on %1$s at %2$s.', '2011-06-23 08:07:19'),
(1106, 1, 'en', 'backend', 'mailmotor', 'msg', 'TemplateLanguage', 'language of the template', '2011-06-23 08:07:19'),
(1107, 1, 'en', 'backend', 'mailmotor', 'msg', 'UnlinkCMAccount', 'Unlink CampaignMonitor account', '2011-06-23 08:07:19'),
(1108, 1, 'en', 'backend', 'mailmotor', 'msg', 'Unlinked', 'The CampaignMonitor account has been unlinked.', '2011-06-23 08:07:19'),
(1109, 1, 'en', 'backend', 'mailmotor', 'msg', 'ViewMailings', 'Go to your mailings overview', '2011-06-23 08:07:19'),
(1110, 1, 'en', 'backend', 'pages', 'lbl', 'SentMailings', 'sent mailings', '2011-06-23 08:07:19'),
(1111, 1, 'en', 'backend', 'pages', 'lbl', 'SubscribeForm', 'subscribe form', '2011-06-23 08:07:19'),
(1112, 1, 'en', 'backend', 'pages', 'lbl', 'UnsubscribeForm', 'unsubscribe form', '2011-06-23 08:07:19'),
(1113, 1, 'en', 'backend', 'core', 'lbl', 'Activate', 'activate', '2011-06-23 08:07:19'),
(1114, 1, 'en', 'backend', 'core', 'lbl', 'ForgotPassword', 'forgot password', '2011-06-23 08:07:19'),
(1115, 1, 'en', 'backend', 'core', 'lbl', 'Login', 'login', '2011-06-23 08:07:19'),
(1116, 1, 'en', 'backend', 'core', 'lbl', 'Logout', 'logout', '2011-06-23 08:07:19'),
(1117, 1, 'en', 'backend', 'core', 'lbl', 'ProfileEmail', 'change e-mail', '2011-06-23 08:07:19'),
(1118, 1, 'en', 'backend', 'core', 'lbl', 'ProfilePassword', 'change password', '2011-06-23 08:07:19'),
(1119, 1, 'en', 'backend', 'core', 'lbl', 'Profiles', 'profiles', '2011-06-23 08:07:19'),
(1120, 1, 'en', 'backend', 'core', 'lbl', 'ProfileSettings', 'profile settings', '2011-06-23 08:07:19'),
(1121, 1, 'en', 'backend', 'core', 'lbl', 'Register', 'register', '2011-06-23 08:07:19'),
(1122, 1, 'en', 'backend', 'core', 'lbl', 'ResendActivation', 'resend activation e-mail', '2011-06-23 08:07:19'),
(1123, 1, 'en', 'backend', 'core', 'lbl', 'ResetPassword', 'reset password', '2011-06-23 08:07:19'),
(1124, 1, 'en', 'backend', 'profiles', 'err', 'DisplayNameExists', 'This display name is in use.', '2011-06-23 08:07:19'),
(1125, 1, 'en', 'backend', 'profiles', 'err', 'DisplayNameIsRequired', 'Display name is a required field.', '2011-06-23 08:07:19'),
(1126, 1, 'en', 'backend', 'profiles', 'err', 'EmailExists', 'This e-mailaddress is in use.', '2011-06-23 08:07:19'),
(1127, 1, 'en', 'backend', 'profiles', 'err', 'NoGroupSelected', 'You must select a group to perfom this action.', '2011-06-23 08:07:19'),
(1128, 1, 'en', 'backend', 'profiles', 'err', 'NoProfilesSelected', 'You must select minimum 1 profile to perfom this action.', '2011-06-23 08:07:19'),
(1129, 1, 'en', 'backend', 'profiles', 'err', 'UnknownAction', 'Unknown action.', '2011-06-23 08:07:19'),
(1130, 1, 'en', 'backend', 'profiles', 'lbl', 'Active', 'active', '2011-06-23 08:07:19'),
(1131, 1, 'en', 'backend', 'profiles', 'lbl', 'AddGroup', 'add group', '2011-06-23 08:07:19'),
(1132, 1, 'en', 'backend', 'profiles', 'lbl', 'AddMembership', 'add membership', '2011-06-23 08:07:19'),
(1133, 1, 'en', 'backend', 'profiles', 'lbl', 'AddToGroup', 'add to a group', '2011-06-23 08:07:19'),
(1134, 1, 'en', 'backend', 'profiles', 'lbl', 'BirthDate', 'birth date', '2011-06-23 08:07:19'),
(1135, 1, 'en', 'backend', 'profiles', 'lbl', 'Blocked', 'blocked', '2011-06-23 08:07:19'),
(1136, 1, 'en', 'backend', 'profiles', 'lbl', 'City', 'city', '2011-06-23 08:07:19'),
(1137, 1, 'en', 'backend', 'profiles', 'lbl', 'Country', 'country', '2011-06-23 08:07:19'),
(1138, 1, 'en', 'backend', 'profiles', 'lbl', 'Deleted', 'deleted', '2011-06-23 08:07:19'),
(1139, 1, 'en', 'backend', 'profiles', 'lbl', 'DisplayName', 'display name', '2011-06-23 08:07:19'),
(1140, 1, 'en', 'backend', 'profiles', 'lbl', 'EditGroup', 'edit group', '2011-06-23 08:07:19'),
(1141, 1, 'en', 'backend', 'profiles', 'lbl', 'EditMembership', 'edit membership', '2011-06-23 08:07:19'),
(1142, 1, 'en', 'backend', 'profiles', 'lbl', 'ExpiresOn', 'expires on', '2011-06-23 08:07:19'),
(1143, 1, 'en', 'backend', 'profiles', 'lbl', 'Female', 'female', '2011-06-23 08:07:19'),
(1144, 1, 'en', 'backend', 'profiles', 'lbl', 'FirstName', 'first name', '2011-06-23 08:07:19'),
(1145, 1, 'en', 'backend', 'profiles', 'lbl', 'Gender', 'gender', '2011-06-23 08:07:19'),
(1146, 1, 'en', 'backend', 'profiles', 'lbl', 'GroupName', 'group name', '2011-06-23 08:07:19'),
(1147, 1, 'en', 'backend', 'profiles', 'lbl', 'Groups', 'groups', '2011-06-23 08:07:19'),
(1148, 1, 'en', 'backend', 'profiles', 'lbl', 'Inactive', 'inactive', '2011-06-23 08:07:19'),
(1149, 1, 'en', 'backend', 'profiles', 'lbl', 'LastName', 'last name', '2011-06-23 08:07:19'),
(1150, 1, 'en', 'backend', 'profiles', 'lbl', 'Male', 'male', '2011-06-23 08:07:19'),
(1151, 1, 'en', 'backend', 'profiles', 'lbl', 'MembersCount', 'members count', '2011-06-23 08:07:19'),
(1152, 1, 'en', 'backend', 'profiles', 'lbl', 'RegisteredOn', 'registered on', '2011-06-23 08:07:19'),
(1153, 1, 'en', 'backend', 'profiles', 'lbl', 'Unblock', 'unblock', '2011-06-23 08:07:19'),
(1154, 1, 'en', 'backend', 'profiles', 'lbl', 'Undelete', 'undelete', '2011-06-23 08:07:19'),
(1155, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmBlock', 'Are you sure you want to block "%1$s"?', '2011-06-23 08:07:19'),
(1156, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmDelete', 'Are you sure you want to delete "%1$s"?', '2011-06-23 08:07:19'),
(1157, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmDeleteGroup', 'Are you sure you want to delete the group "%1$s"?', '2011-06-23 08:07:19'),
(1158, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmMassAddToGroup', 'Are you sure you want to add these profiles to the following group?', '2011-06-23 08:07:19'),
(1159, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmProfileGroupDelete', 'Are you sure you want to delete this profile from the group "%1$s"?', '2011-06-23 08:07:19'),
(1160, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmUnblock', 'Are you sure you want to unblock "%1$s"?', '2011-06-23 08:07:19'),
(1161, 1, 'en', 'backend', 'profiles', 'msg', 'ConfirmUndelete', 'Are you sure you want to undelete "%1$s"?', '2011-06-23 08:07:19'),
(1162, 1, 'en', 'backend', 'profiles', 'msg', 'GroupAdded', 'The group was added.', '2011-06-23 08:07:19'),
(1163, 1, 'en', 'backend', 'profiles', 'msg', 'GroupSaved', 'The group was saved.', '2011-06-23 08:07:19'),
(1164, 1, 'en', 'backend', 'profiles', 'msg', 'MembershipAdded', 'The group membership was added.', '2011-06-23 08:07:19'),
(1165, 1, 'en', 'backend', 'profiles', 'msg', 'MembershipDeleted', 'The group membership was deleted.', '2011-06-23 08:07:19'),
(1166, 1, 'en', 'backend', 'profiles', 'msg', 'MembershipSaved', 'The group membership was saved.', '2011-06-23 08:07:19'),
(1167, 1, 'en', 'backend', 'profiles', 'msg', 'NoGroups', 'There are no groups yet.', '2011-06-23 08:07:19'),
(1168, 1, 'en', 'backend', 'profiles', 'msg', 'ProfileAddedToGroup', 'The profile was added to the group.', '2011-06-23 08:07:19'),
(1169, 1, 'en', 'backend', 'profiles', 'msg', 'ProfilesAddedToGroup', 'The profiles are added to the group.', '2011-06-23 08:07:19'),
(1170, 1, 'en', 'backend', 'profiles', 'msg', 'ProfileBlocked', 'The profiel "%1$s" was blocked.', '2011-06-23 08:07:19'),
(1171, 1, 'en', 'backend', 'profiles', 'msg', 'ProfileDeleted', 'The profile was deleted.', '2011-06-23 08:07:19'),
(1172, 1, 'en', 'backend', 'profiles', 'msg', 'ProfileUnblocked', 'The profiel "%1$s" was unblocked.', '2011-06-23 08:07:19'),
(1173, 1, 'en', 'backend', 'profiles', 'msg', 'ProfileUndeleted', 'The profiel "%1$s" was undeleted.', '2011-06-23 08:07:19'),
(1174, 1, 'en', 'backend', 'profiles', 'msg', 'Saved', 'The profiel "%1$s" was saved.', '2011-06-23 08:07:19'),
(1175, 1, 'en', 'frontend', 'core', 'err', 'DateIsInvalid', 'Invalid date.', '2011-06-23 08:07:19'),
(1176, 1, 'en', 'frontend', 'core', 'err', 'DisplayNameExists', 'This display name is in use.', '2011-06-23 08:07:19'),
(1177, 1, 'en', 'frontend', 'core', 'err', 'DisplayNameIsRequired', 'Display name is a required field.', '2011-06-23 08:07:19'),
(1178, 1, 'en', 'frontend', 'core', 'err', 'EmailExists', 'This e-mailaddress is in use.', '2011-06-23 08:07:19'),
(1179, 1, 'en', 'frontend', 'core', 'err', 'EmailIsUnknown', 'This e-mailaddress is unknown in our database.', '2011-06-23 08:07:19'),
(1180, 1, 'en', 'frontend', 'core', 'err', 'InvalidPassword', 'Invalid password.', '2011-06-23 08:07:19'),
(1181, 1, 'en', 'frontend', 'core', 'err', 'ProfilesBlockedLogin', 'Login failed. This profile is blocked.', '2011-06-23 08:07:19'),
(1182, 1, 'en', 'frontend', 'core', 'err', 'ProfilesDeletedLogin', 'Login failed. This profile has been deleted.', '2011-06-23 08:07:19'),
(1183, 1, 'en', 'frontend', 'core', 'err', 'ProfilesInactiveLogin', 'Login failed. This profile is not yet activated.', '2011-06-23 08:07:19'),
(1184, 1, 'en', 'frontend', 'core', 'err', 'ProfilesInvalidLogin', 'Login failed. Please check your e-mail and your password.', '2011-06-23 08:07:19'),
(1185, 1, 'en', 'frontend', 'core', 'err', 'ProfileIsActive', 'This profile is already activated.', '2011-06-23 08:07:19'),
(1186, 1, 'en', 'frontend', 'core', 'err', 'PasswordIsRequired', 'Password is a required field.', '2011-06-23 08:07:19'),
(1187, 1, 'en', 'frontend', 'core', 'lbl', 'BirthDate', 'birth date', '2011-06-23 08:07:19'),
(1188, 1, 'en', 'frontend', 'core', 'lbl', 'City', 'city', '2011-06-23 08:07:19'),
(1189, 1, 'en', 'frontend', 'core', 'lbl', 'Country', 'country', '2011-06-23 08:07:19'),
(1190, 1, 'en', 'frontend', 'core', 'lbl', 'DisplayName', 'display name', '2011-06-23 08:07:19'),
(1191, 1, 'en', 'frontend', 'core', 'lbl', 'Female', 'female', '2011-06-23 08:07:19'),
(1192, 1, 'en', 'frontend', 'core', 'lbl', 'FirstName', 'first name', '2011-06-23 08:07:19'),
(1193, 1, 'en', 'frontend', 'core', 'lbl', 'Gender', 'gender', '2011-06-23 08:07:19'),
(1194, 1, 'en', 'frontend', 'core', 'lbl', 'ProfileSettings', 'settings', '2011-06-23 08:07:19'),
(1195, 1, 'en', 'frontend', 'core', 'lbl', 'LastName', 'last name', '2011-06-23 08:07:19'),
(1196, 1, 'en', 'frontend', 'core', 'lbl', 'Login', 'login', '2011-06-23 08:07:19'),
(1197, 1, 'en', 'frontend', 'core', 'lbl', 'Logout', 'logout', '2011-06-23 08:07:19'),
(1198, 1, 'en', 'frontend', 'core', 'lbl', 'Male', 'male', '2011-06-23 08:07:19'),
(1199, 1, 'en', 'frontend', 'core', 'lbl', 'NewPassword', 'new password', '2011-06-23 08:07:19'),
(1200, 1, 'en', 'frontend', 'core', 'lbl', 'OldPassword', 'old password', '2011-06-23 08:07:19'),
(1201, 1, 'en', 'frontend', 'core', 'lbl', 'Or', 'or', '2011-06-23 08:07:19'),
(1202, 1, 'en', 'frontend', 'core', 'lbl', 'Password', 'password', '2011-06-23 08:07:19'),
(1203, 1, 'en', 'frontend', 'core', 'lbl', 'Register', 'register', '2011-06-23 08:07:19'),
(1204, 1, 'en', 'frontend', 'core', 'lbl', 'RememberMe', 'remember me', '2011-06-23 08:07:19'),
(1205, 1, 'en', 'frontend', 'core', 'lbl', 'Save', 'save', '2011-06-23 08:07:19'),
(1206, 1, 'en', 'frontend', 'core', 'lbl', 'ShowPassword', 'show password', '2011-06-23 08:07:19'),
(1207, 1, 'en', 'frontend', 'core', 'lbl', 'YourData', 'your data', '2011-06-23 08:07:19'),
(1208, 1, 'en', 'frontend', 'core', 'lbl', 'YourLocationData', 'your location', '2011-06-23 08:07:19'),
(1209, 1, 'en', 'frontend', 'core', 'msg', 'ActivationIsSuccess', 'Your profile was activated.', '2011-06-23 08:07:19'),
(1210, 1, 'en', 'frontend', 'core', 'msg', 'ChangeEmail', 'change your e-mail address', '2011-06-23 08:07:19'),
(1211, 1, 'en', 'frontend', 'core', 'msg', 'ForgotPassword', 'Forgot your password?', '2011-06-23 08:07:19'),
(1212, 1, 'en', 'frontend', 'core', 'msg', 'ForgotPasswordBody', 'You just requested to reset your password on <a href="%1$s">Fork CMS</a>. Follow the link below to reset your password.<br /><br /><a href="%2$s">%2$s</a>', '2011-06-23 08:07:19'),
(1213, 1, 'en', 'frontend', 'core', 'msg', 'ForgotPasswordClosure', 'With kind regards,<br/><br/>The Fork CMS team', '2011-06-23 08:07:19'),
(1214, 1, 'en', 'frontend', 'core', 'msg', 'ForgotPasswordIsSuccess', 'In less than ten minutes you will receive an e-mail to reset your password.', '2011-06-23 08:07:19'),
(1215, 1, 'en', 'frontend', 'core', 'msg', 'ForgotPasswordSalutation', 'Dear,', '2011-06-23 08:07:19'),
(1216, 1, 'en', 'frontend', 'core', 'msg', 'ForgotPasswordSubject', 'Forgot your password?', '2011-06-23 08:07:19'),
(1217, 1, 'en', 'frontend', 'core', 'msg', 'HelpDisplayNameChanges', 'The amount of display name changes is limited to %1$s. You have %2$s change(s) left.', '2011-06-23 08:07:19'),
(1218, 1, 'en', 'frontend', 'core', 'msg', 'RegisterBody', 'You have just registered on the <a href="%1$s">Fork CMS</a> site. To activate your profile you need to follow the link below.<br /><br /><a href="%2$s">%2$s</a>', '2011-06-23 08:07:19'),
(1219, 1, 'en', 'frontend', 'core', 'msg', 'RegisterClosure', 'With kind regards,<br/><br/>The Fork CMS team', '2011-06-23 08:07:19'),
(1220, 1, 'en', 'frontend', 'core', 'msg', 'RegisterIsSuccess', 'Welcome! In less than ten minutes you will receive an activation mail. In the mean while you can use the website in a limited form.', '2011-06-23 08:07:19'),
(1221, 1, 'en', 'frontend', 'core', 'msg', 'RegisterSalutation', 'Dear,', '2011-06-23 08:07:19'),
(1222, 1, 'en', 'frontend', 'core', 'msg', 'RegisterSubject', 'Activate your Fork CMS-profile', '2011-06-23 08:07:19'),
(1223, 1, 'en', 'frontend', 'core', 'msg', 'ResendActivationIsSuccess', 'In less than ten minutes you will receive an new activation mail. A simple click on the link and you will be able to log in.', '2011-06-23 08:07:19'),
(1224, 1, 'en', 'frontend', 'core', 'msg', 'ResetPasswordIsSuccess', 'Your password was saved.', '2011-06-23 08:07:19'),
(1225, 1, 'en', 'frontend', 'core', 'msg', 'UpdateEmailIsSuccess', 'Your e-mail was saved.', '2011-06-23 08:07:19'),
(1226, 1, 'en', 'frontend', 'core', 'msg', 'UpdatePasswordIsSuccess', 'Your password was saved.', '2011-06-23 08:07:19'),
(1227, 1, 'en', 'frontend', 'core', 'msg', 'UpdateSettingsIsSuccess', 'The settings were saved.', '2011-06-23 08:07:19'),
(1228, 1, 'en', 'frontend', 'core', 'msg', 'WelcomeUserX', 'Welcome, %1$s', '2011-06-23 08:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `extra_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_addresses`
--

CREATE TABLE IF NOT EXISTS `mailmotor_addresses` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_addresses_groups`
--

CREATE TABLE IF NOT EXISTS `mailmotor_addresses_groups` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(11) NOT NULL,
  `custom_fields` text COLLATE utf8_unicode_ci,
  `status` enum('subscribed','unsubscribed','inserted') COLLATE utf8_unicode_ci NOT NULL,
  `subscribed_on` datetime DEFAULT NULL,
  `unsubscribed_on` datetime DEFAULT NULL,
  PRIMARY KEY (`email`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_campaignmonitor_ids`
--

CREATE TABLE IF NOT EXISTS `mailmotor_campaignmonitor_ids` (
  `cm_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('campaign','list','template') COLLATE utf8_unicode_ci NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY (`type`,`cm_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_campaigns`
--

CREATE TABLE IF NOT EXISTS `mailmotor_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_groups`
--

CREATE TABLE IF NOT EXISTS `mailmotor_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_fields` text COLLATE utf8_unicode_ci,
  `is_default` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_mailings`
--

CREATE TABLE IF NOT EXISTS `mailmotor_mailings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_to_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_to_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content_html` text COLLATE utf8_unicode_ci,
  `content_plain` text COLLATE utf8_unicode_ci,
  `data` text COLLATE utf8_unicode_ci,
  `send_on` datetime DEFAULT NULL,
  `status` enum('concept','queued','sent') COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mailmotor_mailings_groups`
--

CREATE TABLE IF NOT EXISTS `mailmotor_mailings_groups` (
  `mailing_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`mailing_id`,`group_id`),
  KEY `group_id` (`group_id`),
  KEY `mailing_id` (`mailing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `keywords_overwrite` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description_overwrite` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title_overwrite` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url_overwrite` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `custom` text CHARACTER SET utf8 COMMENT 'used for custom meta-information',
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Meta-information' AUTO_INCREMENT=25 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `keywords`, `keywords_overwrite`, `description`, `description_overwrite`, `title`, `title_overwrite`, `url`, `url_overwrite`, `custom`) VALUES
(1, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL),
(2, 'Search', 'N', 'Search', 'N', 'Search', 'N', 'search', 'N', NULL),
(3, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL),
(4, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL),
(5, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL),
(6, 'Search', 'N', 'Search', 'N', 'Search', 'N', 'search', 'N', NULL),
(7, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL),
(8, 'Default', 'N', 'Default', 'N', 'Default', 'N', 'default', 'N', NULL),
(9, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL),
(10, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL),
(11, 'Sent mailings', 'N', 'Sent mailings', 'N', 'Sent mailings', 'N', 'sent-mailings', 'N', NULL),
(12, 'Subscribe', 'N', 'Subscribe', 'N', 'Subscribe', 'N', 'subscribe', 'N', NULL),
(13, 'Unsubscribe', 'N', 'Unsubscribe', 'N', 'Unsubscribe', 'N', 'unsubscribe', 'N', NULL),
(14, 'Activate', 'N', 'Activate', 'N', 'Activate', 'N', 'activate', 'N', NULL),
(15, 'Forgot password', 'N', 'Forgot password', 'N', 'Forgot password', 'N', 'forgot-password', 'N', NULL),
(16, 'Reset password', 'N', 'Reset password', 'N', 'Reset password', 'N', 'reset-password', 'N', NULL),
(17, 'Resend activation e-mail', 'N', 'Resend activation e-mail', 'N', 'Resend activation e-mail', 'N', 'resend-activation-e-mail', 'N', NULL),
(18, 'Login', 'N', 'Login', 'N', 'Login', 'N', 'login', 'N', NULL),
(19, 'Register', 'N', 'Register', 'N', 'Register', 'N', 'register', 'N', NULL),
(20, 'Logout', 'N', 'Logout', 'N', 'Logout', 'N', 'logout', 'N', NULL),
(21, 'Profile', 'N', 'Profile', 'N', 'Profile', 'N', 'profile', 'N', NULL),
(22, 'Profile settings', 'N', 'Profile settings', 'N', 'Profile settings', 'N', 'profile-settings', 'N', NULL),
(23, 'Change email', 'N', 'Change email', 'N', 'Change email', 'N', 'change-email', 'N', NULL),
(24, 'Change password', 'N', 'Change password', 'N', 'Change password', 'N', 'change-password', 'N', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique module name',
  `description` text COLLATE utf8_unicode_ci,
  `active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`name`),
  KEY `idx_active_name` (`active`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`name`, `description`, `active`) VALUES
('core', 'The Fork CMS core module.', 'Y'),
('authentication', 'The module to manage authentication', 'Y'),
('dashboard', 'The dashboard containing module specific widgets.', 'Y'),
('error', 'The error module, used for displaying errors.', 'Y'),
('locale', 'The module to manage your website/cms locale.', 'Y'),
('users', 'User management.', 'Y'),
('groups', 'The module to manage usergroups.', 'Y'),
('settings', 'The module to manage your settings.', 'Y'),
('pages', 'The module to manage your pages and website structure.', 'Y'),
('search', 'The search module.', 'Y'),
('content_blocks', 'The content blocks module.', 'Y'),
('tags', 'The tags module.', 'Y'),
('analytics', 'The analytics module.', 'Y'),
('blog', 'The blog module.', 'Y'),
('faq', 'The faq module.', 'Y'),
('form_builder', 'The module to create and manage forms.', 'Y'),
('location', 'The location module.', 'Y'),
('mailmotor', 'The module to manage and send mailings.', 'Y'),
('profiles', 'The profiles module.', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `modules_settings`
--

CREATE TABLE IF NOT EXISTS `modules_settings` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`module`(25),`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules_settings`
--

INSERT INTO `modules_settings` (`module`, `name`, `value`) VALUES
('core', 'languages', 'a:1:{i:0;s:2:"en";}'),
('core', 'active_languages', 'a:1:{i:0;s:2:"en";}'),
('core', 'redirect_languages', 'a:1:{i:0;s:2:"en";}'),
('core', 'default_language', 's:2:"en";'),
('core', 'interface_languages', 'a:1:{i:0;s:2:"en";}'),
('core', 'default_interface_language', 's:2:"en";'),
('core', 'theme', 's:6:"triton";'),
('core', 'akismet_key', 's:0:"";'),
('core', 'google_maps_keky', 's:0:"";'),
('core', 'max_num_revisions', 'i:20;'),
('core', 'site_domains', 'a:1:{i:0;s:11:"mlitn.local";}'),
('core', 'site_html_header', 's:0:"";'),
('core', 'site_html_footer', 's:0:"";'),
('core', 'date_format_short', 's:5:"j.n.Y";'),
('core', 'date_formats_short', 'a:24:{i:0;s:5:"j/n/Y";i:1;s:5:"j-n-Y";i:2;s:5:"j.n.Y";i:3;s:5:"n/j/Y";i:4;s:5:"n/j/Y";i:5;s:5:"n/j/Y";i:6;s:5:"d/m/Y";i:7;s:5:"d-m-Y";i:8;s:5:"d.m.Y";i:9;s:5:"m/d/Y";i:10;s:5:"m-d-Y";i:11;s:5:"m.d.Y";i:12;s:5:"j/n/y";i:13;s:5:"j-n-y";i:14;s:5:"j.n.y";i:15;s:5:"n/j/y";i:16;s:5:"n-j-y";i:17;s:5:"n.j.y";i:18;s:5:"d/m/y";i:19;s:5:"d-m-y";i:20;s:5:"d.m.y";i:21;s:5:"m/d/y";i:22;s:5:"m-d-y";i:23;s:5:"m.d.y";}'),
('core', 'date_format_long', 's:7:"l j F Y";'),
('core', 'date_formats_long', 'a:14:{i:0;s:5:"j F Y";i:1;s:7:"D j F Y";i:2;s:7:"l j F Y";i:3;s:6:"j F, Y";i:4;s:8:"D j F, Y";i:5;s:8:"l j F, Y";i:6;s:5:"d F Y";i:7;s:6:"d F, Y";i:8;s:5:"F j Y";i:9;s:7:"D F j Y";i:10;s:7:"l F j Y";i:11;s:6:"F d, Y";i:12;s:8:"D F d, Y";i:13;s:8:"l F d, Y";}'),
('core', 'time_format', 's:3:"H:i";'),
('core', 'time_formats', 'a:4:{i:0;s:3:"H:i";i:1;s:5:"H:i:s";i:2;s:5:"g:i a";i:3;s:5:"g:i A";}'),
('core', 'number_format', 's:11:"dot_nothing";'),
('core', 'number_formats', 'a:6:{s:13:"comma_nothing";s:8:"10000,25";s:11:"dot_nothing";s:8:"10000.25";s:9:"dot_comma";s:9:"10,000.25";s:9:"comma_dot";s:9:"10.000,25";s:9:"dot_space";s:8:"10000.25";s:11:"comma_space";s:9:"10 000,25";}'),
('core', 'mailer_from', 'a:2:{s:4:"name";s:8:"Fork CMS";s:5:"email";s:20:"matthias@netlash.com";}'),
('core', 'mailer_to', 'a:2:{s:4:"name";s:8:"Fork CMS";s:5:"email";s:20:"matthias@netlash.com";}'),
('core', 'mailer_reply_to', 'a:2:{s:4:"name";s:8:"Fork CMS";s:5:"email";s:20:"matthias@netlash.com";}'),
('core', 'smtp_server', 's:0:"";'),
('core', 'smtp_port', 's:0:"";'),
('core', 'smtp_username', 's:0:"";'),
('core', 'smtp_password', 's:0:"";'),
('core', 'site_title_en', 's:10:"My website";'),
('core', 'fork_api_public_key', 's:32:"ca95a9be4f517327520ffae7578e5197";'),
('core', 'fork_api_private_key', 's:32:"44ebd7ce797495033e6cd5dae5f88a09";'),
('core', 'ping_services', 'a:2:{s:8:"services";a:3:{i:0;a:3:{s:3:"url";s:27:"http://rpc.weblogs.com/RPC2";s:4:"port";i:80;s:4:"type";s:8:"extended";}i:1;a:3:{s:3:"url";s:30:"http://rpc.pingomatic.com/RPC2";s:4:"port";i:80;s:4:"type";s:8:"extended";}i:2;a:3:{s:3:"url";s:39:"http://blogsearch.google.com/ping/RPC2 ";s:4:"port";i:80;s:4:"type";s:8:"extended";}}s:4:"date";i:1308816438;}'),
('locale', 'languages', 'a:5:{i:0;s:2:"de";i:1;s:2:"en";i:2;s:2:"es";i:3;s:2:"fr";i:4;s:2:"nl";}'),
('users', 'default_group', 'i:1;'),
('users', 'date_formats', 'a:4:{i:0;s:5:"j/n/Y";i:1;s:5:"d/m/Y";i:2;s:5:"j F Y";i:3;s:6:"F j, Y";}'),
('users', 'time_formats', 'a:4:{i:0;s:3:"H:i";i:1;s:5:"H:i:s";i:2;s:5:"g:i a";i:3;s:5:"g:i A";}'),
('pages', 'default_template', 'i:3;'),
('pages', 'meta_navigation', 'b:0;'),
('search', 'overview_num_items', 'i:10;'),
('search', 'validate_search', 'b:1;'),
('content_blocks', 'max_num_revisions', 'i:20;'),
('blog', 'allow_comments', 'b:1;'),
('blog', 'requires_akismet', 'b:1;'),
('blog', 'spamfilter', 'b:0;'),
('blog', 'moderation', 'b:1;'),
('blog', 'ping_services', 'b:1;'),
('blog', 'overview_num_items', 'i:10;'),
('blog', 'recent_articles_full_num_items', 'i:3;'),
('blog', 'recent_articles_list_num_items', 'i:5;'),
('blog', 'max_num_revisions', 'i:20;'),
('blog', 'feedburner_url_en', 's:0:"";'),
('blog', 'rss_meta_en', 'b:1;'),
('blog', 'rss_title_en', 's:3:"RSS";'),
('blog', 'rss_description_en', 's:0:"";'),
('location', 'zoom_level', 's:4:"auto";'),
('location', 'width', 'i:400;'),
('location', 'height', 'i:300;'),
('location', 'map_type', 's:7:"ROADMAP";'),
('location', 'zoom_level_widget', 'i:13;'),
('location', 'width_widget', 'i:400;'),
('location', 'height_widget', 'i:300;'),
('location', 'map_type_widget', 's:7:"ROADMAP";'),
('mailmotor', 'from_email', 's:20:"matthias@netlash.com";'),
('mailmotor', 'from_name', 's:8:"Fork CMS";'),
('mailmotor', 'plain_text_editable', 'b:1;'),
('mailmotor', 'reply_to_email', 's:20:"matthias@netlash.com";'),
('mailmotor', 'price_per_email', 'i:0;'),
('mailmotor', 'cm_url', 's:0:"";'),
('mailmotor', 'cm_username', 's:0:"";'),
('mailmotor', 'cm_password', 's:0:"";'),
('mailmotor', 'cm_client_company_name', 's:8:"Fork CMS";'),
('mailmotor', 'cm_client_contact_email', 's:20:"matthias@netlash.com";'),
('mailmotor', 'cm_client_contact_name', 's:8:"Fork CMS";'),
('mailmotor', 'cm_client_country', 's:7:"Belgium";'),
('mailmotor', 'cm_client_timezone', 's:0:"";'),
('mailmotor', 'cm_account', 'b:0;');

-- --------------------------------------------------------

--
-- Table structure for table `modules_tags`
--

CREATE TABLE IF NOT EXISTS `modules_tags` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL COMMENT 'the real page_id',
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the parent_id for the page ',
  `template_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the template to use',
  `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'language of the content',
  `type` enum('home','root','page','meta','footer') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root' COMMENT 'page, header, footer, ...',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `navigation_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'title that will be used in the navigation',
  `navigation_title_overwrite` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'should we override the navigation title',
  `hidden` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'is the page hidden?',
  `status` enum('active','archive','draft') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'is this the active, archive or draft version',
  `publish_on` datetime NOT NULL,
  `data` text COLLATE utf8_unicode_ci COMMENT 'serialized array that may contain type specific parameters',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `allow_move` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `allow_children` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `allow_edit` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `allow_delete` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `no_follow` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `sequence` int(11) NOT NULL,
  `has_extra` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL,
  `extra_ids` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `revision_id`, `user_id`, `parent_id`, `template_id`, `meta_id`, `language`, `type`, `title`, `navigation_title`, `navigation_title_overwrite`, `hidden`, `status`, `publish_on`, `data`, `created_on`, `edited_on`, `allow_move`, `allow_children`, `allow_edit`, `allow_delete`, `no_follow`, `sequence`, `has_extra`, `extra_ids`) VALUES
(1, 1, 1, 0, 4, 1, 'en', 'page', 'Home', 'Home', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL),
(2, 2, 1, 0, 3, 2, 'en', 'root', 'Search', 'Search', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'Y', '2'),
(3, 3, 1, 0, 3, 3, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL),
(4, 4, 1, 0, 3, 4, 'en', 'footer', 'Disclaimer', 'Disclaimer', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'Y', 'Y', 'Y', 'Y', 'N', 1, 'N', NULL),
(404, 5, 1, 0, 3, 5, 'en', 'root', '404', '404', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'N', 'Y', 'Y', 'N', 'N', 1, 'N', NULL),
(405, 6, 1, 0, 3, 6, 'en', 'root', 'Search', 'Search', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'Y', 'Y', 'Y', 'Y', 'N', 2, 'Y', '4'),
(406, 7, 1, 0, 3, 7, 'en', 'root', 'Tags', 'Tags', 'N', 'N', 'active', '2011-06-23 08:07:18', NULL, '2011-06-23 08:07:18', '2011-06-23 08:07:18', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'Y', '5'),
(407, 8, 1, 1, 3, 9, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'Y', '8'),
(408, 9, 1, 1, 3, 10, 'en', 'page', 'Contact', 'Contact', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 1, 'N', NULL),
(409, 10, 1, 0, 3, 11, 'en', 'root', 'Sent mailings', 'Sent mailings', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'Y', '18'),
(410, 11, 1, 409, 3, 12, 'en', 'page', 'Subscribe', 'Subscribe', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'Y', '19'),
(411, 12, 1, 409, 3, 13, 'en', 'page', 'Unsubscribe', 'Unsubscribe', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 1, 'Y', '20'),
(412, 13, 1, 0, 3, 14, 'en', 'root', 'Activate', 'Activate', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 5, 'N', NULL),
(413, 14, 1, 0, 3, 15, 'en', 'root', 'Forgot password', 'Forgot password', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'N', NULL),
(414, 15, 1, 0, 3, 16, 'en', 'root', 'Reset password', 'Reset password', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 7, 'N', NULL),
(415, 16, 1, 0, 3, 17, 'en', 'root', 'Resend activation e-mail', 'Resend activation e-mail', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 8, 'N', NULL),
(416, 17, 1, 0, 3, 18, 'en', 'root', 'Login', 'Login', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 9, 'N', NULL),
(417, 18, 1, 0, 3, 19, 'en', 'root', 'Register', 'Register', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 10, 'N', NULL),
(418, 19, 1, 0, 3, 20, 'en', 'root', 'Logout', 'Logout', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 11, 'N', NULL),
(419, 20, 1, 0, 3, 21, 'en', 'root', 'Profile', 'Profile', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 12, 'N', NULL),
(420, 21, 1, 419, 3, 22, 'en', 'page', 'Profile settings', 'Profile settings', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL),
(421, 22, 1, 419, 3, 23, 'en', 'page', 'Change email', 'Change email', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 1, 'N', NULL),
(422, 23, 1, 419, 3, 24, 'en', 'page', 'Change password', 'Change password', 'N', 'N', 'active', '2011-06-23 08:07:19', NULL, '2011-06-23 08:07:19', '2011-06-23 08:07:19', 'Y', 'Y', 'Y', 'Y', 'N', 2, 'N', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages_blocks`
--

CREATE TABLE IF NOT EXISTS `pages_blocks` (
  `id` int(11) NOT NULL COMMENT 'An ID that will be the same over the revisions.\n',
  `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
  `position` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extra_id` int(11) DEFAULT NULL COMMENT 'The linked extra.',
  `html` text COLLATE utf8_unicode_ci COMMENT 'if this block is HTML this field should contain the real HTML.',
  `status` enum('active','archive','draft') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  KEY `idx_rev_status` (`revision_id`,`status`),
  KEY `idx_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pages_blocks`
--

INSERT INTO `pages_blocks` (`id`, `revision_id`, `position`, `extra_id`, `html`, `status`, `created_on`, `edited_on`) VALUES
(0, 1, 'main', NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 2, 'main', 2, '', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 3, 'main', NULL, '<p>Take a look at all the pages in our website:</p>', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 4, 'main', NULL, '<p><strong>This website is property of [Bedrijfsnaam].</strong></p>\n<p><strong>Contact info:</strong><br />[Bedrijfsnaam]<br /> [Straatnaam] [Nummer]<br /> [Postcode] [Gemeente]</p>\n<p><strong>Adres maatschappelijk zetel:</strong><br />[Maatschappelijke zetel]<br /> [Straatnaam] [Nummer]<br /> [Postcode] [Gemeente]</p>\n<p>Telefoon:<br />E-mail:</p>\n<p>Ondernemingsnummer: BTW BE 0 [BTW-nummer]</p>\n<p>De toezichthoudende autoriteit: (wanneer uw activiteit aan een vergunningsstelsel is onderworpen)</p>\n<p>By accessing and using the website, you have expressly agreed to the following general conditions.</p>\n<h3>Intellectual property rights</h3>\n<p>The contents of this site, including trade marks, logos, drawings, data, product or company names, texts, images, etc. are protected by intellectual property rights and belong to [Bedrijfsnaam] or entitled third parties.</p>\n<h3>Liability limitation</h3>\n<p>The information on the website is general in nature. It is not adapted to personal or specific circumstances and can therefore not be regarded as personal, professional or judicial advice for the user.</p>\n<p>[Bedrijfsnaam] does everything in its power to ensure that the information made available is complete, correct, accurate and updated. However, despite these efforts inaccuracies may occur when providing information. If the information provided contains inaccuracies or if specific information on or via the site is unavailable, [Bedrijfsnaam] shall make the greatest effort to ensure that this is rectified as soon as possible.</p>\n<p>[Bedrijfsnaam] cannot be held responsible for direct or indirect damage caused by the use of the information on this site.&nbsp;  The site manager should be contacted if the user has noticed any inaccuracies in the information provided by the site.</p>\n<p>The contents of the site (including links) may be adjusted, changed or extended at any time without any announcement or advance notice. [Bedrijfsnaam] gives no guarantees for the smooth operation of the website and cannot be held responsible in any way for the poor operation or temporary unavailability of the website or for any type of damage, direct or indirect, which may occur due to the access to or use of the website.</p>\n<p>[Bedrijfsnaam] can in no case be held liable, directly or indirectly, specifically or otherwise, vis-&agrave;-vis anyone for any damage attributable to the use of this site or any other one, in particular as the result of links or hyperlinks including, but not limited to, any loss, work interruption, damage of the user&rsquo;s programs or other data on the computer system, hardware, software or otherwise.</p>\n<p>The website may contain hyperlinks to websites or pages of third parties or refer to these indirectly. The placing of links on these websites or pages shall not imply in any way the implicit approval of the contents thereof.&nbsp;  [Bedrijfsnaam] expressly declares that it has no authority over the contents or over other features of these websites and can in no case be held responsible for the contents or features thereof or for any other type of damage resulting from their use.</p>\n<h3>Applicable legislation and competent courts</h3>\n<p>This site is governed by Belgian law. Only the courts of the district of Ghent are competent to settle any disputes.</p>\n<h3>Privacy policy</h3>\n<p>[Bedrijfsnaam] believes that your privacy is important. While most of the information on this site is available without having to ask the user for personal information,&nbsp; the user may be asked for some personal details.&nbsp;&nbsp; This information will only be used to ensure a better service.&nbsp;&nbsp; (e.g. for our customer database, to keep users informed of our activities, etc.). The user may, free of charge and on request, always prevent the use of his personal details for the purposes of direct marketing. In this regard, the user should contact [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf]. Your personal details will never been transferred to any third parties (if this should occur, you will be informed).</p>\n<p>In accordance with the law on the processing of personal data of 8 December 1992, the user has the legal right to examine and possibly correct any of his/her personal details. Subject to proof of identity (copy of the user&rsquo;s identity card), you can via a written, dated and signed request to [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf], receive free of charge a written statement of the user&rsquo;s personal details.&nbsp; If necessary, you may also ask for any incorrect, incomplete or irrelevant data to be adjusted.</p>\n<p>[Bedrijfsnaam] can collect non-personal anonymous or aggregate data such as browser type, IP address or operating system in use or the domain name of the website that led you to and from our website, ensuring optimum effectiveness of our website for all users.</p>\n<h3>The use of cookies</h3>\n<p>During a visit to the site, cookies may be placed on the hard drive of your computer. This is only in order to ensure that our site is geared to the needs of users returning to our website. These tiny files known as cookies are not used to ascertain the surfing habits of the visitor on other websites. Your internet browser enables you to disable these cookies, receive a warning when a cookie has been installed or have the cookies removed from your hard disc.&nbsp; For this purpose, consult the help function of your internet browser.</p>', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 5, 'main', NULL, '<p>This page doesn''t exist or is not accessible at this time. Take a look at the sitemap:</p>', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 6, 'main', 4, '', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 7, 'main', 5, '', 'active', '2011-06-23 08:07:18', '2011-06-23 08:07:18'),
(0, 8, 'main', 8, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 9, 'main', NULL, '<p>Enter your question and contact information and we''ll get back to you as soon as possible.</p>', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 10, 'main', 18, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 11, 'main', 19, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 12, 'main', 20, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 13, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 14, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 15, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 16, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 17, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 18, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 19, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 20, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 21, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 22, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19'),
(0, 23, 'main', NULL, '', 'active', '2011-06-23 08:07:19', '2011-06-23 08:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `pages_extras`
--

CREATE TABLE IF NOT EXISTS `pages_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the extra.',
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` enum('homepage','block','widget') COLLATE utf8_unicode_ci NOT NULL COMMENT 'The type of the block.',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci COMMENT 'A serialized value with the optional parameters',
  `hidden` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Should the extra be shown in the backend?',
  `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=33 ;

--
-- Dumping data for table `pages_extras`
--

INSERT INTO `pages_extras` (`id`, `module`, `type`, `label`, `action`, `data`, `hidden`, `sequence`) VALUES
(1, 'search', 'widget', 'SearchForm', 'form', NULL, 'N', 2001),
(2, 'search', 'block', 'Search', NULL, NULL, 'N', 2000),
(3, 'pages', 'widget', 'Sitemap', 'sitemap', NULL, 'N', 1),
(4, 'search', 'block', 'Search', NULL, 'a:1:{s:3:"url";s:40:"/private/nl/search/statistics?token=true";}', 'N', 2000),
(5, 'tags', 'block', 'Tags', NULL, NULL, 'N', 30),
(6, 'tags', 'widget', 'TagCloud', 'tagcloud', NULL, 'N', 31),
(7, 'tags', 'widget', 'Related', 'related', NULL, 'N', 32),
(8, 'blog', 'block', 'Blog', NULL, NULL, 'N', 1000),
(9, 'blog', 'widget', 'RecentComments', 'recent_comments', NULL, 'N', 1001),
(10, 'blog', 'widget', 'Categories', 'categories', NULL, 'N', 1002),
(11, 'blog', 'widget', 'Archive', 'archive', NULL, 'N', 1003),
(12, 'blog', 'widget', 'RecentArticlesFull', 'recent_articles_full', NULL, 'N', 1004),
(13, 'blog', 'widget', 'RecentArticlesList', 'recent_articles_list', NULL, 'N', 1005),
(14, 'faq', 'block', 'Faq', 'index', NULL, 'N', 9001),
(15, 'faq', 'block', 'Category', 'category', NULL, 'N', 9002),
(16, 'form_builder', 'widget', 'FormBuilder', 'form', 'a:3:{s:8:"language";s:2:"en";s:11:"extra_label";s:7:"Contact";s:2:"id";i:1;}', 'N', 4001),
(17, 'location', 'block', 'Location', NULL, 'a:1:{s:3:"url";s:37:"/private/nl/location/index?token=true";}', 'N', 10000),
(18, 'mailmotor', 'block', 'SentMailings', NULL, NULL, 'N', 3000),
(19, 'mailmotor', 'block', 'SubscribeForm', 'subscribe', NULL, 'N', 3001),
(20, 'mailmotor', 'block', 'UnsubscribeForm', 'unsubscribe', NULL, 'N', 3002),
(21, 'mailmotor', 'widget', 'SubscribeForm', 'subscribe', NULL, 'N', 3003),
(22, 'profiles', 'block', 'Activate', 'activate', NULL, 'N', 5000),
(23, 'profiles', 'block', 'ForgotPassword', 'forgot_password', NULL, 'N', 5001),
(24, 'profiles', 'block', 'Dashboard', NULL, NULL, 'N', 5002),
(25, 'profiles', 'block', 'Login', 'login', NULL, 'N', 5003),
(26, 'profiles', 'block', 'Logout', 'logout', NULL, 'N', 5004),
(27, 'profiles', 'block', 'ProfileEmail', 'profile_email', NULL, 'N', 5005),
(28, 'profiles', 'block', 'ProfilePassword', 'profile_password', NULL, 'N', 5006),
(29, 'profiles', 'block', 'ProfileSettings', 'profile_settings', NULL, 'N', 5007),
(30, 'profiles', 'block', 'Register', 'register', NULL, 'N', 5008),
(31, 'profiles', 'block', 'ResetPassword', 'reset_password', NULL, 'N', 5008),
(32, 'profiles', 'block', 'ResendActivation', 'resend_activation', NULL, 'N', 5009);

-- --------------------------------------------------------

--
-- Table structure for table `pages_templates`
--

CREATE TABLE IF NOT EXISTS `pages_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the template.',
  `theme` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'The name of the theme.',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  `active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Is this template active (as in: will it be used).',
  `data` text COLLATE utf8_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pages_templates`
--

INSERT INTO `pages_templates` (`id`, `theme`, `label`, `path`, `active`, `data`) VALUES
(1, 'core', 'Default', 'core/layout/templates/default.tpl', 'Y', 'a:2:{s:6:"format";s:12:"[main,right]";s:5:"names";a:2:{i:0;s:4:"main";i:1;s:5:"right";}}'),
(2, 'core', 'Home', 'core/layout/templates/home.tpl', 'Y', 'a:2:{s:6:"format";s:12:"[main,right]";s:5:"names";a:2:{i:0;s:4:"main";i:1;s:5:"right";}}'),
(3, 'triton', 'Default', 'core/layout/templates/default.tpl', 'Y', 'a:2:{s:6:"format";s:45:"[/,/,top,top],[/,/,/,/],[left,main,main,main]";s:5:"names";a:3:{i:0;s:4:"main";i:1;s:4:"left";i:2;s:3:"top";}}'),
(4, 'triton', 'Home', 'core/layout/templates/home.tpl', 'Y', 'a:2:{s:6:"format";s:69:"[/,/,top,top],[/,/,/,/],[main,main,main,main],[left,left,right,right]";s:5:"names";a:4:{i:0;s:4:"main";i:1;s:4:"left";i:2;s:5:"right";i:3;s:3:"top";}}');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','inactive','deleted','blocked') COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registered_on` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_groups`
--

CREATE TABLE IF NOT EXISTS `profiles_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_groups_rights`
--

CREATE TABLE IF NOT EXISTS `profiles_groups_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `starts_on` datetime DEFAULT NULL,
  `expires_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_id__group__id__expires_on` (`profile_id`,`group_id`,`expires_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_sessions`
--

CREATE TABLE IF NOT EXISTS `profiles_sessions` (
  `session_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profile_id` int(11) NOT NULL,
  `secret_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`session_id`,`profile_id`),
  KEY `fk_profiles_sessions_profiles1` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles_settings`
--

CREATE TABLE IF NOT EXISTS `profiles_settings` (
  `profile_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`,`profile_id`),
  KEY `fk_profiles_settings_profiles1` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `search_index`
--

CREATE TABLE IF NOT EXISTS `search_index` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `other_id` int(11) NOT NULL,
  `field` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`module`,`other_id`,`field`,`language`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Search index';

--
-- Dumping data for table `search_index`
--

INSERT INTO `search_index` (`module`, `other_id`, `field`, `value`, `language`, `active`) VALUES
('pages', 1, 'title', 'Home', 'en', 'Y'),
('pages', 1, 'text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.', 'en', 'Y'),
('pages', 2, 'title', 'Search', 'en', 'Y'),
('pages', 2, 'text', '', 'en', 'Y'),
('pages', 3, 'title', 'Sitemap', 'en', 'Y'),
('pages', 3, 'text', 'Take a look at all the pages in our website:', 'en', 'Y'),
('pages', 4, 'title', 'Disclaimer', 'en', 'Y'),
('pages', 4, 'text', 'This website is property of [Bedrijfsnaam].\nContact info:[Bedrijfsnaam] [Straatnaam] [Nummer] [Postcode] [Gemeente]\nAdres maatschappelijk zetel:[Maatschappelijke zetel] [Straatnaam] [Nummer] [Postcode] [Gemeente]\nTelefoon:E-mail:\nOndernemingsnummer: BTW BE 0 [BTW-nummer]\nDe toezichthoudende autoriteit: (wanneer uw activiteit aan een vergunningsstelsel is onderworpen)\nBy accessing and using the website, you have expressly agreed to the following general conditions.\nIntellectual property rights\nThe contents of this site, including trade marks, logos, drawings, data, product or company names, texts, images, etc. are protected by intellectual property rights and belong to [Bedrijfsnaam] or entitled third parties.\nLiability limitation\nThe information on the website is general in nature. It is not adapted to personal or specific circumstances and can therefore not be regarded as personal, professional or judicial advice for the user.\n[Bedrijfsnaam] does everything in its power to ensure that the information made available is complete, correct, accurate and updated. However, despite these efforts inaccuracies may occur when providing information. If the information provided contains inaccuracies or if specific information on or via the site is unavailable, [Bedrijfsnaam] shall make the greatest effort to ensure that this is rectified as soon as possible.\n[Bedrijfsnaam] cannot be held responsible for direct or indirect damage caused by the use of the information on this site.&nbsp;  The site manager should be contacted if the user has noticed any inaccuracies in the information provided by the site.\nThe contents of the site (including links) may be adjusted, changed or extended at any time without any announcement or advance notice. [Bedrijfsnaam] gives no guarantees for the smooth operation of the website and cannot be held responsible in any way for the poor operation or temporary unavailability of the website or for any type of damage, direct or indirect, which may occur due to the access to or use of the website.\n[Bedrijfsnaam] can in no case be held liable, directly or indirectly, specifically or otherwise, vis-&agrave;-vis anyone for any damage attributable to the use of this site or any other one, in particular as the result of links or hyperlinks including, but not limited to, any loss, work interruption, damage of the user&rsquo;s programs or other data on the computer system, hardware, software or otherwise.\nThe website may contain hyperlinks to websites or pages of third parties or refer to these indirectly. The placing of links on these websites or pages shall not imply in any way the implicit approval of the contents thereof.&nbsp;  [Bedrijfsnaam] expressly declares that it has no authority over the contents or over other features of these websites and can in no case be held responsible for the contents or features thereof or for any other type of damage resulting from their use.\nApplicable legislation and competent courts\nThis site is governed by Belgian law. Only the courts of the district of Ghent are competent to settle any disputes.\nPrivacy policy\n[Bedrijfsnaam] believes that your privacy is important. While most of the information on this site is available without having to ask the user for personal information,&nbsp; the user may be asked for some personal details.&nbsp;&nbsp; This information will only be used to ensure a better service.&nbsp;&nbsp; (e.g. for our customer database, to keep users informed of our activities, etc.). The user may, free of charge and on request, always prevent the use of his personal details for the purposes of direct marketing. In this regard, the user should contact [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf]. Your personal details will never been transferred to any third parties (if this should occur, you will be informed).\nIn accordance with the law on the processing of personal data of 8 December 1992, the user has the legal right to examine and possibly correct any of his/her personal details. Subject to proof of identity (copy of the user&rsquo;s identity card), you can via a written, dated and signed request to [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf], receive free of charge a written statement of the user&rsquo;s personal details.&nbsp; If necessary, you may also ask for any incorrect, incomplete or irrelevant data to be adjusted.\n[Bedrijfsnaam] can collect non-personal anonymous or aggregate data such as browser type, IP address or operating system in use or the domain name of the website that led you to and from our website, ensuring optimum effectiveness of our website for all users.\nThe use of cookies\nDuring a visit to the site, cookies may be placed on the hard drive of your computer. This is only in order to ensure that our site is geared to the needs of users returning to our website. These tiny files known as cookies are not used to ascertain the surfing habits of the visitor on other websites. Your internet browser enables you to disable these cookies, receive a warning when a cookie has been installed or have the cookies removed from your hard disc.&nbsp; For this purpose, consult the help function of your internet browser.', 'en', 'Y'),
('pages', 404, 'title', '404', 'en', 'Y'),
('pages', 404, 'text', 'This page doesn''t exist or is not accessible at this time. Take a look at the sitemap:', 'en', 'Y'),
('pages', 405, 'title', 'Search', 'en', 'Y'),
('pages', 405, 'text', '', 'en', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `search_modules`
--

CREATE TABLE IF NOT EXISTS `search_modules` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `searchable` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `search_modules`
--

INSERT INTO `search_modules` (`module`, `searchable`, `weight`) VALUES
('pages', 'Y', 1),
('blog', 'Y', 1),
('location', 'Y', 1);

-- --------------------------------------------------------

--
-- Table structure for table `search_statistics`
--

CREATE TABLE IF NOT EXISTS `search_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `num_results` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `search_synonyms`
--

CREATE TABLE IF NOT EXISTS `search_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `synonym` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`term`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `timezones`
--

CREATE TABLE IF NOT EXISTS `timezones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=454 ;

--
-- Dumping data for table `timezones`
--

INSERT INTO `timezones` (`id`, `timezone`) VALUES
(1, 'Africa/Abidjan'),
(2, 'Africa/Accra'),
(3, 'Africa/Addis_Ababa'),
(4, 'Africa/Algiers'),
(5, 'Africa/Asmara'),
(6, 'Africa/Asmera'),
(7, 'Africa/Bamako'),
(8, 'Africa/Bangui'),
(9, 'Africa/Banjul'),
(10, 'Africa/Bissau'),
(11, 'Africa/Blantyre'),
(12, 'Africa/Brazzaville'),
(13, 'Africa/Bujumbura'),
(14, 'Africa/Cairo'),
(15, 'Africa/Casablanca'),
(16, 'Africa/Ceuta'),
(17, 'Africa/Conakry'),
(18, 'Africa/Dakar'),
(19, 'Africa/Dar_es_Salaam'),
(20, 'Africa/Djibouti'),
(21, 'Africa/Douala'),
(22, 'Africa/El_Aaiun'),
(23, 'Africa/Freetown'),
(24, 'Africa/Gaborone'),
(25, 'Africa/Harare'),
(26, 'Africa/Johannesburg'),
(27, 'Africa/Kampala'),
(28, 'Africa/Khartoum'),
(29, 'Africa/Kigali'),
(30, 'Africa/Kinshasa'),
(31, 'Africa/Lagos'),
(32, 'Africa/Libreville'),
(33, 'Africa/Lome'),
(34, 'Africa/Luanda'),
(35, 'Africa/Lubumbashi'),
(36, 'Africa/Lusaka'),
(37, 'Africa/Malabo'),
(38, 'Africa/Maputo'),
(39, 'Africa/Maseru'),
(40, 'Africa/Mbabane'),
(41, 'Africa/Mogadishu'),
(42, 'Africa/Monrovia'),
(43, 'Africa/Nairobi'),
(44, 'Africa/Ndjamena'),
(45, 'Africa/Niamey'),
(46, 'Africa/Nouakchott'),
(47, 'Africa/Ouagadougou'),
(48, 'Africa/Porto-Novo'),
(49, 'Africa/Sao_Tome'),
(50, 'Africa/Timbuktu'),
(51, 'Africa/Tripoli'),
(52, 'Africa/Tunis'),
(53, 'Africa/Windhoek'),
(54, 'America/Adak'),
(55, 'America/Anchorage'),
(56, 'America/Anguilla'),
(57, 'America/Antigua'),
(58, 'America/Araguaina'),
(59, 'America/Argentina/Buenos_Aires'),
(60, 'America/Argentina/Catamarca'),
(61, 'America/Argentina/ComodRivadavia'),
(62, 'America/Argentina/Cordoba'),
(63, 'America/Argentina/Jujuy'),
(64, 'America/Argentina/La_Rioja'),
(65, 'America/Argentina/Mendoza'),
(66, 'America/Argentina/Rio_Gallegos'),
(67, 'America/Argentina/Salta'),
(68, 'America/Argentina/San_Juan'),
(69, 'America/Argentina/San_Luis'),
(70, 'America/Argentina/Tucuman'),
(71, 'America/Argentina/Ushuaia'),
(72, 'America/Aruba'),
(73, 'America/Asuncion'),
(74, 'America/Atikokan'),
(75, 'America/Atka'),
(76, 'America/Bahia'),
(77, 'America/Barbados'),
(78, 'America/Belem'),
(79, 'America/Belize'),
(80, 'America/Blanc-Sablon'),
(81, 'America/Boa_Vista'),
(82, 'America/Bogota'),
(83, 'America/Boise'),
(84, 'America/Buenos_Aires'),
(85, 'America/Cambridge_Bay'),
(86, 'America/Campo_Grande'),
(87, 'America/Cancun'),
(88, 'America/Caracas'),
(89, 'America/Catamarca'),
(90, 'America/Cayenne'),
(91, 'America/Cayman'),
(92, 'America/Chicago'),
(93, 'America/Chihuahua'),
(94, 'America/Coral_Harbour'),
(95, 'America/Cordoba'),
(96, 'America/Costa_Rica'),
(97, 'America/Cuiaba'),
(98, 'America/Curacao'),
(99, 'America/Danmarkshavn'),
(100, 'America/Dawson'),
(101, 'America/Dawson_Creek'),
(102, 'America/Denver'),
(103, 'America/Detroit'),
(104, 'America/Dominica'),
(105, 'America/Edmonton'),
(106, 'America/Eirunepe'),
(107, 'America/El_Salvador'),
(108, 'America/Ensenada'),
(109, 'America/Fort_Wayne'),
(110, 'America/Fortaleza'),
(111, 'America/Glace_Bay'),
(112, 'America/Godthab'),
(113, 'America/Goose_Bay'),
(114, 'America/Grand_Turk'),
(115, 'America/Grenada'),
(116, 'America/Guadeloupe'),
(117, 'America/Guatemala'),
(118, 'America/Guayaquil'),
(119, 'America/Guyana'),
(120, 'America/Halifax'),
(121, 'America/Havana'),
(122, 'America/Hermosillo'),
(123, 'America/Indiana/Indianapolis'),
(124, 'America/Indiana/Knox'),
(125, 'America/Indiana/Marengo'),
(126, 'America/Indiana/Petersburg'),
(127, 'America/Indiana/Tell_City'),
(128, 'America/Indiana/Vevay'),
(129, 'America/Indiana/Vincennes'),
(130, 'America/Indiana/Winamac'),
(131, 'America/Indianapolis'),
(132, 'America/Inuvik'),
(133, 'America/Iqaluit'),
(134, 'America/Jamaica'),
(135, 'America/Jujuy'),
(136, 'America/Juneau'),
(137, 'America/Kentucky/Louisville'),
(138, 'America/Kentucky/Monticello'),
(139, 'America/Knox_IN'),
(140, 'America/La_Paz'),
(141, 'America/Lima'),
(142, 'America/Los_Angeles'),
(143, 'America/Louisville'),
(144, 'America/Maceio'),
(145, 'America/Managua'),
(146, 'America/Manaus'),
(147, 'America/Marigot'),
(148, 'America/Martinique'),
(149, 'America/Matamoros'),
(150, 'America/Mazatlan'),
(151, 'America/Mendoza'),
(152, 'America/Menominee'),
(153, 'America/Merida'),
(154, 'America/Mexico_City'),
(155, 'America/Miquelon'),
(156, 'America/Moncton'),
(157, 'America/Monterrey'),
(158, 'America/Montevideo'),
(159, 'America/Montreal'),
(160, 'America/Montserrat'),
(161, 'America/Nassau'),
(162, 'America/New_York'),
(163, 'America/Nipigon'),
(164, 'America/Nome'),
(165, 'America/Noronha'),
(166, 'America/North_Dakota/Center'),
(167, 'America/North_Dakota/New_Salem'),
(168, 'America/Ojinaga'),
(169, 'America/Panama'),
(170, 'America/Pangnirtung'),
(171, 'America/Paramaribo'),
(172, 'America/Phoenix'),
(173, 'America/Port-au-Prince'),
(174, 'America/Port_of_Spain'),
(175, 'America/Porto_Acre'),
(176, 'America/Porto_Velho'),
(177, 'America/Puerto_Rico'),
(178, 'America/Rainy_River'),
(179, 'America/Rankin_Inlet'),
(180, 'America/Recife'),
(181, 'America/Regina'),
(182, 'America/Resolute'),
(183, 'America/Rio_Branco'),
(184, 'America/Rosario'),
(185, 'America/Santa_Isabel'),
(186, 'America/Santarem'),
(187, 'America/Santiago'),
(188, 'America/Santo_Domingo'),
(189, 'America/Sao_Paulo'),
(190, 'America/Scoresbysund'),
(191, 'America/Shiprock'),
(192, 'America/St_Barthelemy'),
(193, 'America/St_Johns'),
(194, 'America/St_Kitts'),
(195, 'America/St_Lucia'),
(196, 'America/St_Thomas'),
(197, 'America/St_Vincent'),
(198, 'America/Swift_Current'),
(199, 'America/Tegucigalpa'),
(200, 'America/Thule'),
(201, 'America/Thunder_Bay'),
(202, 'America/Tijuana'),
(203, 'America/Toronto'),
(204, 'America/Tortola'),
(205, 'America/Vancouver'),
(206, 'America/Virgin'),
(207, 'America/Whitehorse'),
(208, 'America/Winnipeg'),
(209, 'America/Yakutat'),
(210, 'America/Yellowknife'),
(211, 'Antarctica/Casey'),
(212, 'Antarctica/Davis'),
(213, 'Antarctica/DumontDUrville'),
(214, 'Antarctica/Mawson'),
(215, 'Antarctica/McMurdo'),
(216, 'Antarctica/Palmer'),
(217, 'Antarctica/Rothera'),
(218, 'Antarctica/South_Pole'),
(219, 'Antarctica/Syowa'),
(220, 'Antarctica/Vostok'),
(221, 'Arctic/Longyearbyen'),
(222, 'Asia/Aden'),
(223, 'Asia/Almaty'),
(224, 'Asia/Amman'),
(225, 'Asia/Anadyr'),
(226, 'Asia/Aqtau'),
(227, 'Asia/Aqtobe'),
(228, 'Asia/Ashgabat'),
(229, 'Asia/Ashkhabad'),
(230, 'Asia/Baghdad'),
(231, 'Asia/Bahrain'),
(232, 'Asia/Baku'),
(233, 'Asia/Bangkok'),
(234, 'Asia/Beirut'),
(235, 'Asia/Bishkek'),
(236, 'Asia/Brunei'),
(237, 'Asia/Calcutta'),
(238, 'Asia/Choibalsan'),
(239, 'Asia/Chongqing'),
(240, 'Asia/Chungking'),
(241, 'Asia/Colombo'),
(242, 'Asia/Dacca'),
(243, 'Asia/Damascus'),
(244, 'Asia/Dhaka'),
(245, 'Asia/Dili'),
(246, 'Asia/Dubai'),
(247, 'Asia/Dushanbe'),
(248, 'Asia/Gaza'),
(249, 'Asia/Harbin'),
(250, 'Asia/Ho_Chi_Minh'),
(251, 'Asia/Hong_Kong'),
(252, 'Asia/Hovd'),
(253, 'Asia/Irkutsk'),
(254, 'Asia/Istanbul'),
(255, 'Asia/Jakarta'),
(256, 'Asia/Jayapura'),
(257, 'Asia/Jerusalem'),
(258, 'Asia/Kabul'),
(259, 'Asia/Kamchatka'),
(260, 'Asia/Karachi'),
(261, 'Asia/Kashgar'),
(262, 'Asia/Kathmandu'),
(263, 'Asia/Katmandu'),
(264, 'Asia/Kolkata'),
(265, 'Asia/Krasnoyarsk'),
(266, 'Asia/Kuala_Lumpur'),
(267, 'Asia/Kuching'),
(268, 'Asia/Kuwait'),
(269, 'Asia/Macao'),
(270, 'Asia/Macau'),
(271, 'Asia/Magadan'),
(272, 'Asia/Makassar'),
(273, 'Asia/Manila'),
(274, 'Asia/Muscat'),
(275, 'Asia/Nicosia'),
(276, 'Asia/Novokuznetsk'),
(277, 'Asia/Novosibirsk'),
(278, 'Asia/Omsk'),
(279, 'Asia/Oral'),
(280, 'Asia/Phnom_Penh'),
(281, 'Asia/Pontianak'),
(282, 'Asia/Pyongyang'),
(283, 'Asia/Qatar'),
(284, 'Asia/Qyzylorda'),
(285, 'Asia/Rangoon'),
(286, 'Asia/Riyadh'),
(287, 'Asia/Saigon'),
(288, 'Asia/Sakhalin'),
(289, 'Asia/Samarkand'),
(290, 'Asia/Seoul'),
(291, 'Asia/Shanghai'),
(292, 'Asia/Singapore'),
(293, 'Asia/Taipei'),
(294, 'Asia/Tashkent'),
(295, 'Asia/Tbilisi'),
(296, 'Asia/Tehran'),
(297, 'Asia/Tel_Aviv'),
(298, 'Asia/Thimbu'),
(299, 'Asia/Thimphu'),
(300, 'Asia/Tokyo'),
(301, 'Asia/Ujung_Pandang'),
(302, 'Asia/Ulaanbaatar'),
(303, 'Asia/Ulan_Bator'),
(304, 'Asia/Urumqi'),
(305, 'Asia/Vientiane'),
(306, 'Asia/Vladivostok'),
(307, 'Asia/Yakutsk'),
(308, 'Asia/Yekaterinburg'),
(309, 'Asia/Yerevan'),
(310, 'Atlantic/Azores'),
(311, 'Atlantic/Bermuda'),
(312, 'Atlantic/Canary'),
(313, 'Atlantic/Cape_Verde'),
(314, 'Atlantic/Faeroe'),
(315, 'Atlantic/Faroe'),
(316, 'Atlantic/Jan_Mayen'),
(317, 'Atlantic/Madeira'),
(318, 'Atlantic/Reykjavik'),
(319, 'Atlantic/South_Georgia'),
(320, 'Atlantic/St_Helena'),
(321, 'Atlantic/Stanley'),
(322, 'Australia/ACT'),
(323, 'Australia/Adelaide'),
(324, 'Australia/Brisbane'),
(325, 'Australia/Broken_Hill'),
(326, 'Australia/Canberra'),
(327, 'Australia/Currie'),
(328, 'Australia/Darwin'),
(329, 'Australia/Eucla'),
(330, 'Australia/Hobart'),
(331, 'Australia/LHI'),
(332, 'Australia/Lindeman'),
(333, 'Australia/Lord_Howe'),
(334, 'Australia/Melbourne'),
(335, 'Australia/North'),
(336, 'Australia/NSW'),
(337, 'Australia/Perth'),
(338, 'Australia/Queensland'),
(339, 'Australia/South'),
(340, 'Australia/Sydney'),
(341, 'Australia/Tasmania'),
(342, 'Australia/Victoria'),
(343, 'Australia/West'),
(344, 'Australia/Yancowinna'),
(345, 'Europe/Amsterdam'),
(346, 'Europe/Andorra'),
(347, 'Europe/Athens'),
(348, 'Europe/Belfast'),
(349, 'Europe/Belgrade'),
(350, 'Europe/Berlin'),
(351, 'Europe/Bratislava'),
(352, 'Europe/Brussels'),
(353, 'Europe/Bucharest'),
(354, 'Europe/Budapest'),
(355, 'Europe/Chisinau'),
(356, 'Europe/Copenhagen'),
(357, 'Europe/Dublin'),
(358, 'Europe/Gibraltar'),
(359, 'Europe/Guernsey'),
(360, 'Europe/Helsinki'),
(361, 'Europe/Isle_of_Man'),
(362, 'Europe/Istanbul'),
(363, 'Europe/Jersey'),
(364, 'Europe/Kaliningrad'),
(365, 'Europe/Kiev'),
(366, 'Europe/Lisbon'),
(367, 'Europe/Ljubljana'),
(368, 'Europe/London'),
(369, 'Europe/Luxembourg'),
(370, 'Europe/Madrid'),
(371, 'Europe/Malta'),
(372, 'Europe/Mariehamn'),
(373, 'Europe/Minsk'),
(374, 'Europe/Monaco'),
(375, 'Europe/Moscow'),
(376, 'Europe/Nicosia'),
(377, 'Europe/Oslo'),
(378, 'Europe/Paris'),
(379, 'Europe/Podgorica'),
(380, 'Europe/Prague'),
(381, 'Europe/Riga'),
(382, 'Europe/Rome'),
(383, 'Europe/Samara'),
(384, 'Europe/San_Marino'),
(385, 'Europe/Sarajevo'),
(386, 'Europe/Simferopol'),
(387, 'Europe/Skopje'),
(388, 'Europe/Sofia'),
(389, 'Europe/Stockholm'),
(390, 'Europe/Tallinn'),
(391, 'Europe/Tirane'),
(392, 'Europe/Tiraspol'),
(393, 'Europe/Uzhgorod'),
(394, 'Europe/Vaduz'),
(395, 'Europe/Vatican'),
(396, 'Europe/Vienna'),
(397, 'Europe/Vilnius'),
(398, 'Europe/Volgograd'),
(399, 'Europe/Warsaw'),
(400, 'Europe/Zagreb'),
(401, 'Europe/Zaporozhye'),
(402, 'Europe/Zurich'),
(403, 'Indian/Antananarivo'),
(404, 'Indian/Chagos'),
(405, 'Indian/Christmas'),
(406, 'Indian/Cocos'),
(407, 'Indian/Comoro'),
(408, 'Indian/Kerguelen'),
(409, 'Indian/Mahe'),
(410, 'Indian/Maldives'),
(411, 'Indian/Mauritius'),
(412, 'Indian/Mayotte'),
(413, 'Indian/Reunion'),
(414, 'Pacific/Apia'),
(415, 'Pacific/Auckland'),
(416, 'Pacific/Chatham'),
(417, 'Pacific/Easter'),
(418, 'Pacific/Efate'),
(419, 'Pacific/Enderbury'),
(420, 'Pacific/Fakaofo'),
(421, 'Pacific/Fiji'),
(422, 'Pacific/Funafuti'),
(423, 'Pacific/Galapagos'),
(424, 'Pacific/Gambier'),
(425, 'Pacific/Guadalcanal'),
(426, 'Pacific/Guam'),
(427, 'Pacific/Honolulu'),
(428, 'Pacific/Johnston'),
(429, 'Pacific/Kiritimati'),
(430, 'Pacific/Kosrae'),
(431, 'Pacific/Kwajalein'),
(432, 'Pacific/Majuro'),
(433, 'Pacific/Marquesas'),
(434, 'Pacific/Midway'),
(435, 'Pacific/Nauru'),
(436, 'Pacific/Niue'),
(437, 'Pacific/Norfolk'),
(438, 'Pacific/Noumea'),
(439, 'Pacific/Pago_Pago'),
(440, 'Pacific/Palau'),
(441, 'Pacific/Pitcairn'),
(442, 'Pacific/Ponape'),
(443, 'Pacific/Port_Moresby'),
(444, 'Pacific/Rarotonga'),
(445, 'Pacific/Saipan'),
(446, 'Pacific/Samoa'),
(447, 'Pacific/Tahiti'),
(448, 'Pacific/Tarawa'),
(449, 'Pacific/Tongatapu'),
(450, 'Pacific/Truk'),
(451, 'Pacific/Wake'),
(452, 'Pacific/Wallis'),
(453, 'Pacific/Yap');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'will be case-sensitive',
  `active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'is this user active?',
  `deleted` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'is the user deleted?',
  `is_god` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The backend users' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `active`, `deleted`, `is_god`) VALUES
(1, 'matthias@netlash.com', '644e0adbee63f4e844b85ab1115bc23ac03e7fbb', 'Y', 'N', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`group_id`, `user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_session_id_secret_key` (`session_id`(100),`secret_key`(100))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users_sessions`
--

INSERT INTO `users_sessions` (`id`, `user_id`, `session_id`, `secret_key`, `date`) VALUES
(3, 1, 'hvc5khtvt20lfg2d1clcel8dk1', '0af6561134bf72db8b91cddb4e29461b0c36838a', '2011-06-29 14:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `users_settings`
--

CREATE TABLE IF NOT EXISTS `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_settings`
--

INSERT INTO `users_settings` (`user_id`, `name`, `value`) VALUES
(1, 'nickname', 's:8:"Fork CMS";'),
(1, 'name', 's:4:"Fork";'),
(1, 'surname', 's:3:"CMS";'),
(1, 'interface_language', 's:2:"en";'),
(1, 'date_format', 's:5:"j F Y";'),
(1, 'time_format', 's:3:"H:i";'),
(1, 'datetime_format', 's:9:"j F Y H:i";'),
(1, 'number_format', 's:11:"dot_nothing";'),
(1, 'password_key', 's:13:"4e02f436bd746";'),
(1, 'avatar', 's:7:"god.jpg";'),
(1, 'dashboard_sequence', 'a:3:{s:8:"settings";a:1:{s:7:"analyse";a:4:{s:6:"column";s:4:"left";s:8:"position";i:1;s:6:"hidden";b:0;s:7:"present";b:1;}}s:4:"blog";a:1:{s:8:"comments";a:4:{s:6:"column";s:6:"middle";s:8:"position";i:1;s:6:"hidden";b:0;s:7:"present";b:1;}}s:9:"mailmotor";a:1:{s:10:"statistics";a:4:{s:6:"column";s:5:"right";s:8:"position";i:1;s:6:"hidden";b:0;s:7:"present";b:1;}}}');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
