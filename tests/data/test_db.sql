/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table analytics_keywords
# ------------------------------------------------------------

DROP TABLE IF EXISTS `analytics_keywords`;

CREATE TABLE `analytics_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table analytics_landing_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `analytics_landing_pages`;

CREATE TABLE `analytics_landing_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `bounces` int(11) NOT NULL,
  `bounce_rate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table analytics_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `analytics_pages`;

CREATE TABLE `analytics_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_viewed` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table analytics_referrers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `analytics_referrers`;

CREATE TABLE `analytics_referrers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referrer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table backend_navigation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `backend_navigation`;

CREATE TABLE `backend_navigation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_for` text COLLATE utf8mb4_unicode_ci,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `backend_navigation` WRITE;
/*!40000 ALTER TABLE `backend_navigation` DISABLE KEYS */;

INSERT INTO `backend_navigation` (`id`, `parent_id`, `label`, `url`, `selected_for`, `sequence`)
VALUES
	(1,0,'Dashboard','dashboard/index',NULL,1),
	(2,0,'Modules','',NULL,3),
	(3,0,'Settings','',NULL,999),
	(4,3,'Translations','locale/index','a:4:{i:0;s:10:\"locale/add\";i:1;s:11:\"locale/edit\";i:2;s:13:\"locale/import\";i:3;s:14:\"locale/analyse\";}',4),
	(5,3,'General','settings/index',NULL,1),
	(6,3,'Advanced','',NULL,2),
	(7,6,'Email','settings/email',NULL,1),
	(8,6,'SEO','settings/seo',NULL,2),
	(9,3,'Modules','',NULL,6),
	(10,3,'Themes','',NULL,7),
	(11,3,'Users','users/index','a:2:{i:0;s:9:\"users/add\";i:1;s:10:\"users/edit\";}',4),
	(12,3,'Groups','groups/index','a:2:{i:0;s:10:\"groups/add\";i:1;s:11:\"groups/edit\";}',5),
	(13,9,'Overview','extensions/modules','a:2:{i:0;s:24:\"extensions/detail_module\";i:1;s:24:\"extensions/upload_module\";}',1),
	(14,10,'ThemesSelection','extensions/themes','a:2:{i:0;s:23:\"extensions/upload_theme\";i:1;s:23:\"extensions/detail_theme\";}',1),
	(15,10,'Templates','extensions/theme_templates','a:2:{i:0;s:29:\"extensions/add_theme_template\";i:1;s:30:\"extensions/edit_theme_template\";}',2),
	(16,0,'Pages','pages/index','a:2:{i:0;s:9:\"pages/add\";i:1;s:10:\"pages/edit\";}',2),
	(17,9,'Pages','pages/settings',NULL,2),
	(18,2,'Search','',NULL,1),
	(19,18,'Statistics','search/statistics',NULL,1),
	(20,18,'Synonyms','search/synonyms','a:2:{i:0;s:18:\"search/add_synonym\";i:1;s:19:\"search/edit_synonym\";}',2),
	(21,9,'Search','search/settings',NULL,3),
	(22,2,'ContentBlocks','content_blocks/index','a:2:{i:0;s:18:\"content_blocks/add\";i:1;s:19:\"content_blocks/edit\";}',2),
	(23,2,'Tags','tags/index','a:1:{i:0;s:9:\"tags/edit\";}',3),
	(24,0,'Marketing','analytics/index',NULL,4),
	(25,24,'Analytics','analytics/index','a:1:{i:0;s:17:\"analytics/loading\";}',1),
	(26,25,'Content','analytics/content',NULL,1),
	(27,25,'AllPages','analytics/all_pages',NULL,2),
	(28,25,'ExitPages','analytics/exit_pages',NULL,3),
	(29,25,'LandingPages','analytics/landing_pages','a:3:{i:0;s:26:\"analytics/add_landing_page\";i:1;s:27:\"analytics/edit_landing_page\";i:2;s:21:\"analytics/detail_page\";}',4),
	(30,9,'Analytics','analytics/settings',NULL,4),
	(31,2,'Blog','',NULL,4),
	(32,31,'Articles','blog/index','a:3:{i:0;s:8:\"blog/add\";i:1;s:9:\"blog/edit\";i:2;s:21:\"blog/import_wordpress\";}',1),
	(33,31,'Comments','blog/comments','a:1:{i:0;s:17:\"blog/edit_comment\";}',2),
	(34,31,'Categories','blog/categories','a:2:{i:0;s:17:\"blog/add_category\";i:1;s:18:\"blog/edit_category\";}',3),
	(35,9,'Blog','blog/settings',NULL,5),
	(36,2,'Faq','',NULL,5),
	(37,36,'Questions','faq/index','a:2:{i:0;s:7:\"faq/add\";i:1;s:8:\"faq/edit\";}',1),
	(38,36,'Categories','faq/categories','a:2:{i:0;s:16:\"faq/add_category\";i:1;s:17:\"faq/edit_category\";}',2),
	(39,9,'Faq','faq/settings',NULL,6),
	(40,2,'FormBuilder','form_builder/index','a:4:{i:0;s:16:\"form_builder/add\";i:1;s:17:\"form_builder/edit\";i:2;s:17:\"form_builder/data\";i:3;s:25:\"form_builder/data_details\";}',6),
	(41,2,'Location','location/index','a:2:{i:0;s:12:\"location/add\";i:1;s:13:\"location/edit\";}',7),
	(42,9,'Location','location/settings',NULL,7),
	(43,0,'Mailmotor','mailmotor/settings',NULL,5),
	(49,2,'Profiles','',NULL,8),
	(50,49,'Overview','profiles/index','a:5:{i:0;s:12:\"profiles/add\";i:1;s:13:\"profiles/edit\";i:2;s:26:\"profiles/add_profile_group\";i:3;s:27:\"profiles/edit_profile_group\";i:4;s:15:\"profiles/import\";}',1),
	(51,49,'Groups','profiles/groups','a:2:{i:0;s:18:\"profiles/add_group\";i:1;s:19:\"profiles/edit_group\";}',2),
	(52, 6, 'Tools', 'settings/tools', NULL, 3),
	(53, 0, 'MediaLibrary', 'media_library/media_item_index', 'a:2:{i:0;s:31:\"media_library/media_item_upload\";i:1;s:29:\"media_library/media_item_edit\";}', 3),
	(54, 2, 'MediaGalleries', 'media_galleries/media_gallery_index', 'a:2:{i:0;s:33:\"media_galleries/media_gallery_add\";i:1;s:34:\"media_galleries/media_gallery_edit\";}', 6);


/*!40000 ALTER TABLE `backend_navigation` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table blog_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blog_categories`;

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


# Dump of table blog_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blog_comments`;

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` text COLLATE utf8mb4_unicode_ci,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `status` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderation',
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'Serialized array with extra data',
  PRIMARY KEY (`id`),
  KEY `idx_post_id_status` (`post_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table blog_posts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blog_posts`;

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL COMMENT 'The real post id',
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `introduction` text COLLATE utf8mb4_unicode_ci,
  `text` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(244) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_on` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '0',
  `num_comments` int(11) NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table content_blocks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `content_blocks`;

CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Default.html.twig',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT '(DC2Type:content_blocks_status)',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table faq_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `faq_categories`;

CREATE TABLE `faq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table faq_feedback
# ------------------------------------------------------------

DROP TABLE IF EXISTS `faq_feedback`;

CREATE TABLE `faq_feedback` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table faq_questions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `faq_questions`;

CREATE TABLE `faq_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `num_views` int(11) NOT NULL DEFAULT '0',
  `num_usefull_yes` int(11) NOT NULL DEFAULT '0',
  `num_usefull_no` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_faq_questions_faq_categories` (`hidden`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms`;

CREATE TABLE `forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'database_email',
  `email` text COLLATE utf8mb4_unicode_ci,
  `success_message` text COLLATE utf8mb4_unicode_ci,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_template` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Form.html.twig',
  `email_subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forms_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms_data`;

CREATE TABLE `forms_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_on` datetime NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'Serialized array with extra information.',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forms_data_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms_data_fields`;

CREATE TABLE `forms_data_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `data_id` (`data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forms_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms_fields`;

CREATE TABLE `forms_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sequence` (`sequence`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table forms_fields_validation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms_fields_validation`;

CREATE TABLE `forms_fields_validation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'If you want to validate higher then a number, the number would be the parameter',
  `error_message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` text COLLATE utf8mb4_unicode_ci COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;

INSERT INTO `groups` (`id`, `name`, `parameters`)
VALUES
	(1,'admin',NULL),
	(2,'pages user',NULL),
	(3,'users user',NULL);

/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table groups_rights_actions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups_rights_actions`;

CREATE TABLE `groups_rights_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL DEFAULT '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `groups_rights_actions` WRITE;
/*!40000 ALTER TABLE `groups_rights_actions` DISABLE KEYS */;

INSERT INTO `groups_rights_actions` (`id`, `group_id`, `module`, `action`, `level`)
VALUES
	(1,1,'Dashboard','Index',7),
	(2,1,'Dashboard','AlterSequence',7),
	(3,1,'Locale','Add',7),
	(4,1,'Locale','Analyse',7),
	(5,1,'Locale','Edit',7),
	(6,1,'Locale','ExportAnalyse',7),
	(7,1,'Locale','Index',7),
	(8,1,'Locale','SaveTranslation',7),
	(9,1,'Locale','Export',7),
	(10,1,'Locale','Import',7),
	(11,1,'Locale','Delete',7),
	(12,1,'Settings','Index',7),
	(13,1,'Settings','Email',7),
	(14,1,'Settings','Seo',7),
	(15,1,'Settings','TestEmailConnection',7),
	(16,1,'Users','Add',7),
	(17,1,'Users','Delete',7),
	(18,1,'Users','Edit',7),
	(19,1,'Users','Index',7),
	(20,1,'Users','UndoDelete',7),
	(21,1,'Groups','Index',7),
	(22,1,'Groups','Add',7),
	(23,1,'Groups','Edit',7),
	(24,1,'Groups','Delete',7),
	(25,1,'Extensions','Modules',7),
	(26,1,'Extensions','DetailModule',7),
	(27,1,'Extensions','InstallModule',7),
	(28,1,'Extensions','UploadModule',7),
	(29,1,'Extensions','Themes',7),
	(30,1,'Extensions','DetailTheme',7),
	(31,1,'Extensions','InstallTheme',7),
	(32,1,'Extensions','UploadTheme',7),
	(33,1,'Extensions','ThemeTemplates',7),
	(34,1,'Extensions','AddThemeTemplate',7),
	(35,1,'Extensions','EditThemeTemplate',7),
	(36,1,'Extensions','DeleteThemeTemplate',7),
	(37,1,'Pages','GetInfo',7),
	(38,1,'Pages','Move',7),
	(39,1,'Pages','Index',7),
	(40,1,'Pages','Add',7),
	(41,1,'Pages','Delete',7),
	(42,1,'Pages','Edit',7),
	(43,1,'Pages','Settings',7),
	(44,1,'Search','AddSynonym',7),
	(45,1,'Search','EditSynonym',7),
	(46,1,'Search','DeleteSynonym',7),
	(47,1,'Search','Settings',7),
	(48,1,'Search','Statistics',7),
	(49,1,'Search','Synonyms',7),
	(50,1,'ContentBlocks','Add',7),
	(51,1,'ContentBlocks','Delete',7),
	(52,1,'ContentBlocks','Edit',7),
	(53,1,'ContentBlocks','Index',7),
	(54,1,'Tags','Autocomplete',7),
	(55,1,'Tags','Edit',7),
	(56,1,'Tags','Index',7),
	(57,1,'Tags','MassAction',7),
	(58,1,'Analytics','AddLandingPage',7),
	(59,1,'Analytics','AllPages',7),
	(60,1,'Analytics','CheckStatus',7),
	(61,1,'Analytics','Content',7),
	(62,1,'Analytics','DeleteLandingPage',7),
	(63,1,'Analytics','DetailPage',7),
	(64,1,'Analytics','ExitPages',7),
	(65,1,'Analytics','GetTrafficSources',7),
	(66,1,'Analytics','Index',7),
	(67,1,'Analytics','LandingPages',7),
	(68,1,'Analytics','Loading',7),
	(69,1,'Analytics','MassLandingPageAction',7),
	(70,1,'Analytics','RefreshTrafficSources',7),
	(71,1,'Analytics','Settings',7),
	(72,1,'Analytics','TrafficSources',7),
	(73,1,'Analytics','Visitors',7),
	(74,1,'Blog','AddCategory',7),
	(75,1,'Blog','Add',7),
	(76,1,'Blog','Categories',7),
	(77,1,'Blog','Comments',7),
	(78,1,'Blog','DeleteCategory',7),
	(79,1,'Blog','DeleteSpam',7),
	(80,1,'Blog','Delete',7),
	(81,1,'Blog','EditCategory',7),
	(82,1,'Blog','EditComment',7),
	(83,1,'Blog','Edit',7),
	(84,1,'Blog','ImportWordpress',7),
	(85,1,'Blog','Index',7),
	(86,1,'Blog','MassCommentAction',7),
	(87,1,'Blog','Settings',7),
	(88,1,'Faq','Index',7),
	(89,1,'Faq','Add',7),
	(90,1,'Faq','Edit',7),
	(91,1,'Faq','Delete',7),
	(92,1,'Faq','Sequence',7),
	(93,1,'Faq','Categories',7),
	(94,1,'Faq','AddCategory',7),
	(95,1,'Faq','EditCategory',7),
	(96,1,'Faq','DeleteCategory',7),
	(97,1,'Faq','SequenceQuestions',7),
	(98,1,'Faq','DeleteFeedback',7),
	(99,1,'Faq','Settings',7),
	(100,1,'FormBuilder','Add',7),
	(101,1,'FormBuilder','Edit',7),
	(102,1,'FormBuilder','Delete',7),
	(103,1,'FormBuilder','Index',7),
	(104,1,'FormBuilder','Data',7),
	(105,1,'FormBuilder','DataDetails',7),
	(106,1,'FormBuilder','MassDataAction',7),
	(107,1,'FormBuilder','GetField',7),
	(108,1,'FormBuilder','DeleteField',7),
	(109,1,'FormBuilder','SaveField',7),
	(110,1,'FormBuilder','Sequence',7),
	(111,1,'FormBuilder','ExportData',7),
	(112,1,'Location','Index',7),
	(113,1,'Location','Add',7),
	(114,1,'Location','Edit',7),
	(115,1,'Location','Delete',7),
	(116,1,'Location','SaveLiveLocation',7),
	(117,1,'Location','UpdateMarker',7),
	(142,1,'Mailmotor','Index',7),
	(153,1,'Mailmotor','Settings',7),
	(158,1,'Profiles','Add',7),
	(159,1,'Profiles','AddGroup',7),
	(160,1,'Profiles','AddProfileGroup',7),
	(161,1,'Profiles','Block',7),
	(162,1,'Profiles','DeleteGroup',7),
	(163,1,'Profiles','DeleteProfileGroup',7),
	(164,1,'Profiles','Delete',7),
	(165,1,'Profiles','EditGroup',7),
	(166,1,'Profiles','EditProfileGroup',7),
	(167,1,'Profiles','Edit',7),
	(168,1,'Profiles','ExportTemplate',7),
	(169,1,'Profiles','Groups',7),
	(170,1,'Profiles','Import',7),
	(171,1,'Profiles','Index',7),
	(172,1,'Profiles','MassAction',7),
	(173,2,'Pages','GetInfo',7),
	(174,2,'Pages','Move',7),
	(175,2,'Pages','Index',7),
	(176,2,'Pages','Add',7),
	(177,2,'Pages','Delete',7),
	(178,2,'Pages','Edit',7),
	(179,2,'Pages','Settings',7),
	(180,3,'Users','Edit',7),
	(181,1,'Analytics','Reset',7),
	(182,1,'Extensions','ExportThemeTemplates',7),
	(183,1,'Location','Settings',7),
	(184,1,'Mailmotor','Ping',7),
	(185,1,'Pages','Copy',7),
	(186,1,'Pages','RemoveUploadedFile',7),
	(187,1,'Pages','UploadFile',7),
	(188,1,'Profiles','Settings',7),
	(189,1,'Settings','Tools',7),
	(190,1,'Settings','ClearCache',7),
	(191,1,'Tags','GetAllTags',7),
	(192, 1, 'MediaGalleries', 'MediaGalleryAdd', 7),
	(193, 1, 'MediaGalleries', 'MediaGalleryDelete', 7),
	(194, 1, 'MediaGalleries', 'MediaGalleryEdit', 7),
	(195, 1, 'MediaGalleries', 'MediaGalleryEditWidgetAction', 7),
	(196, 1, 'MediaGalleries', 'MediaGalleryIndex', 7),
	(197, 1, 'MediaLibrary', 'MediaFolderAdd', 7),
	(198, 1, 'MediaLibrary', 'MediaFolderDelete', 7),
	(199, 1, 'MediaLibrary', 'MediaFolderEdit', 7),
	(200, 1, 'MediaLibrary', 'MediaFolderFindAll', 7),
	(201, 1, 'MediaLibrary', 'MediaFolderGetCountsForGroup', 7),
	(202, 1, 'MediaLibrary', 'MediaFolderInfo', 7),
	(203, 1, 'MediaLibrary', 'MediaFolderMovie', 7),
	(204, 1, 'MediaLibrary', 'MediaItemAddMovie', 7),
	(205, 1, 'MediaLibrary', 'MediaItemCleanup', 7),
	(206, 1, 'MediaLibrary', 'MediaItemDelete', 7),
	(207, 1, 'MediaLibrary', 'MediaItemEdit', 7),
	(208, 1, 'MediaLibrary', 'MediaItemFindAll', 7),
	(209, 1, 'MediaLibrary', 'MediaItemGetAllById', 7),
	(210, 1, 'MediaLibrary', 'MediaItemIndex', 7),
	(211, 1, 'MediaLibrary', 'MediaItemMassAction', 7),
	(212, 1, 'MediaLibrary', 'MediaItemUpload', 7);

/*!40000 ALTER TABLE `groups_rights_actions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table groups_rights_modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups_rights_modules`;

CREATE TABLE `groups_rights_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `groups_rights_modules` WRITE;
/*!40000 ALTER TABLE `groups_rights_modules` DISABLE KEYS */;

INSERT INTO `groups_rights_modules` (`id`, `group_id`, `module`)
VALUES
	(1,1,'Dashboard'),
	(2,1,'Locale'),
	(3,1,'Settings'),
	(4,1,'Users'),
	(5,1,'Groups'),
	(6,1,'Extensions'),
	(7,1,'Pages'),
	(8,1,'Search'),
	(9,1,'ContentBlocks'),
	(10,1,'Tags'),
	(11,1,'Analytics'),
	(12,1,'Blog'),
	(13,1,'Faq'),
	(14,1,'FormBuilder'),
	(15,1,'Location'),
	(16,1,'Mailmotor'),
	(17,1,'Profiles'),
	(18,2,'Pages'),
	(19,3,'Users'),
	(20,1,'MediaLibrary'),
	(21,1,'MediaGalleries');

/*!40000 ALTER TABLE `groups_rights_modules` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table groups_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups_settings`;

CREATE TABLE `groups_settings` (
  `group_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`group_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `groups_settings` WRITE;
/*!40000 ALTER TABLE `groups_settings` DISABLE KEYS */;

INSERT INTO `groups_settings` (`group_id`, `name`, `value`)
VALUES
	(1,'dashboard_sequence','a:6:{s:8:\"Settings\";a:1:{s:7:\"Analyse\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:5:\"Users\";a:1:{s:10:\"Statistics\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:2;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:9:\"Analytics\";a:2:{s:14:\"TrafficSources\";a:4:{s:6:\"column\";s:6:\"middle\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}s:8:\"Visitors\";a:4:{s:6:\"column\";s:6:\"middle\";s:8:\"position\";i:2;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:4:\"Blog\";a:1:{s:8:\"Comments\";a:4:{s:6:\"column\";s:5:\"right\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:3:\"Faq\";a:1:{s:8:\"Feedback\";a:4:{s:6:\"column\";s:5:\"right\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:9:\"Mailmotor\";a:1:{s:10:\"Statistics\";a:4:{s:6:\"column\";s:5:\"right\";s:8:\"position\";i:2;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}}');

/*!40000 ALTER TABLE `groups_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table locale
# ------------------------------------------------------------

DROP TABLE IF EXISTS `locale`;

CREATE TABLE `locale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` varchar(110) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lbl',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`application`(20),`module`(20),`type`,`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `locale` WRITE;
/*!40000 ALTER TABLE `locale` DISABLE KEYS */;

INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`)
VALUES
	(1, 1, 'en', 'Backend', 'Locale', 'lbl', 'Actions', 'actions', '2017-08-31 14:28:18'),
	(2, 1, 'en', 'Backend', 'Locale', 'lbl', 'Add', 'add translation', '2017-08-31 14:28:18'),
	(3, 1, 'en', 'Backend', 'Locale', 'lbl', 'Copy', 'copy', '2017-08-31 14:28:18'),
	(4, 1, 'en', 'Backend', 'Locale', 'lbl', 'Edited', 'edited', '2017-08-31 14:28:18'),
	(5, 1, 'en', 'Backend', 'Locale', 'lbl', 'EN', 'english', '2017-08-31 14:28:18'),
	(6, 1, 'en', 'Backend', 'Locale', 'lbl', 'Errors', 'errors', '2017-08-31 14:28:18'),
	(7, 1, 'en', 'Backend', 'Locale', 'lbl', 'FR', 'french', '2017-08-31 14:28:18'),
	(8, 1, 'en', 'Backend', 'Locale', 'lbl', 'Labels', 'labels', '2017-08-31 14:28:18'),
	(9, 1, 'en', 'Backend', 'Locale', 'lbl', 'Messages', 'messages', '2017-08-31 14:28:18'),
	(10, 1, 'en', 'Backend', 'Locale', 'lbl', 'NL', 'Dutch', '2017-08-31 14:28:18'),
	(11, 1, 'en', 'Backend', 'Locale', 'lbl', 'UK', 'Ukrainian', '2017-08-31 14:28:18'),
	(12, 1, 'en', 'Backend', 'Locale', 'lbl', 'SV', 'Swedish', '2017-08-31 14:28:18'),
	(13, 1, 'en', 'Backend', 'Locale', 'lbl', 'ES', 'Spanish', '2017-08-31 14:28:18'),
	(14, 1, 'en', 'Backend', 'Locale', 'lbl', 'RU', 'Russian', '2017-08-31 14:28:18'),
	(15, 1, 'en', 'Backend', 'Locale', 'lbl', 'LT', 'Lithuanian', '2017-08-31 14:28:18'),
	(16, 1, 'en', 'Backend', 'Locale', 'lbl', 'IT', 'Italian', '2017-08-31 14:28:18'),
	(17, 1, 'en', 'Backend', 'Locale', 'lbl', 'HU', 'Hungarian', '2017-08-31 14:28:18'),
	(18, 1, 'en', 'Backend', 'Locale', 'lbl', 'EL', 'Greek', '2017-08-31 14:28:18'),
	(19, 1, 'en', 'Backend', 'Locale', 'lbl', 'DE', 'German', '2017-08-31 14:28:18'),
	(20, 1, 'en', 'Backend', 'Locale', 'lbl', 'ZH', 'Chinese', '2017-08-31 14:28:18'),
	(21, 1, 'en', 'Backend', 'Locale', 'lbl', 'Types', 'types', '2017-08-31 14:28:18'),
	(22, 1, 'en', 'Backend', 'Locale', 'msg', 'Added', 'The translation \"%1$s\" was added.', '2017-08-31 14:28:18'),
	(23, 1, 'en', 'Backend', 'Locale', 'msg', 'ConfirmDelete', 'Are you sure you want to delete this translation?', '2017-08-31 14:28:18'),
	(24, 1, 'en', 'Backend', 'Locale', 'msg', 'Deleted', 'The translation \"%1$s\" was deleted.', '2017-08-31 14:28:18'),
	(25, 1, 'en', 'Backend', 'Locale', 'msg', 'Edited', 'The translation \"%1$s\" was saved.', '2017-08-31 14:28:18'),
	(26, 1, 'en', 'Backend', 'Locale', 'msg', 'EditTranslation', 'edit translation \"%1$s\"', '2017-08-31 14:28:18'),
	(27, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpActionValue', 'Only use alphanumeric characters (no capitals), - and _ for these translations, because they will be used in URLs.', '2017-08-31 14:28:18'),
	(28, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpAddName', 'The English reference for the translation', '2017-08-31 14:28:18'),
	(29, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpAddValue', 'The translation', '2017-08-31 14:28:18'),
	(30, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpDateField', 'eg. 20/06/2011', '2017-08-31 14:28:18'),
	(31, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpEditName', 'The English reference for the translation', '2017-08-31 14:28:18'),
	(32, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpEditValue', 'The translation', '2017-08-31 14:28:18'),
	(33, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpImageField', 'Only jp(e)g, gif or png-files are allowed.', '2017-08-31 14:28:18'),
	(34, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpName', 'The english reference for this translation', '2017-08-31 14:28:18'),
	(35, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpTimeField', 'eg. 14:35', '2017-08-31 14:28:18'),
	(36, 1, 'en', 'Backend', 'Locale', 'msg', 'HelpValue', 'The translation', '2017-08-31 14:28:18'),
	(37, 1, 'en', 'Backend', 'Locale', 'msg', 'Imported', '%1$s translations were imported.', '2017-08-31 14:28:18'),
	(38, 1, 'en', 'Backend', 'Locale', 'msg', 'NoItems', 'There are no translations yet. <a href=\"%1$s\">Add the first translation</a>.', '2017-08-31 14:28:18'),
	(39, 1, 'en', 'Backend', 'Locale', 'msg', 'NoItemsAnalyse', 'No missing translations were found.', '2017-08-31 14:28:18'),
	(40, 1, 'en', 'Backend', 'Locale', 'msg', 'NoItemsFilter', 'There are no translations yet for this filter. <a href=\"%1$s\">Add the first translation</a>.', '2017-08-31 14:28:18'),
	(41, 1, 'en', 'Backend', 'Locale', 'msg', 'StartSearch', 'Make a search result.', '2017-08-31 14:28:18'),
	(42, 1, 'en', 'Backend', 'Locale', 'msg', 'OverwriteConflicts', 'Overwrite if the translation exists.', '2017-08-31 14:28:18'),
	(43, 1, 'en', 'Backend', 'Locale', 'err', 'AlreadyExists', 'This translation already exists.', '2017-08-31 14:28:18'),
	(44, 1, 'en', 'Backend', 'Locale', 'err', 'InvalidActionValue', 'The action name contains invalid characters.', '2017-08-31 14:28:18'),
	(45, 1, 'en', 'Backend', 'Locale', 'err', 'InvalidXML', 'This is an invalid XML-file.', '2017-08-31 14:28:18'),
	(46, 1, 'en', 'Backend', 'Locale', 'err', 'ModuleHasToBeCore', 'The module needs to be core for frontend translations.', '2017-08-31 14:28:18'),
	(47, 1, 'en', 'Backend', 'Locale', 'err', 'NoSelection', 'No translations were selected.', '2017-08-31 14:28:18'),
	(48, 1, 'en', 'Backend', 'Core', 'err', 'InvalidCSV', 'This is an invalid CSV-file.', '2017-08-31 14:28:18'),
	(49, 1, 'en', 'Frontend', 'Core', 'err', 'NumberIsInvalid', 'Invalid number.', '2017-08-31 14:28:18'),
	(50, 1, 'en', 'Frontend', 'Core', 'lbl', 'AboutUs', 'about us', '2017-08-31 14:28:18'),
	(51, 1, 'en', 'Frontend', 'Core', 'lbl', 'Advertisement', 'advertisement', '2017-08-31 14:28:18'),
	(52, 1, 'en', 'Frontend', 'Core', 'lbl', 'Archive', 'archive', '2017-08-31 14:28:18'),
	(53, 1, 'en', 'Frontend', 'Core', 'lbl', 'Archives', 'archives', '2017-08-31 14:28:18'),
	(54, 1, 'en', 'Frontend', 'Core', 'lbl', 'ArticlesInCategory', 'articles in category', '2017-08-31 14:28:18'),
	(55, 1, 'en', 'Frontend', 'Core', 'lbl', 'Avatar', 'avatar', '2017-08-31 14:28:18'),
	(56, 1, 'en', 'Frontend', 'Core', 'lbl', 'BirthDate', 'birth date', '2017-08-31 14:28:18'),
	(57, 1, 'en', 'Frontend', 'Core', 'lbl', 'Blog', 'blog', '2017-08-31 14:28:18'),
	(58, 1, 'en', 'Frontend', 'Core', 'lbl', 'BlogArchive', 'blog archive', '2017-08-31 14:28:18'),
	(59, 1, 'en', 'Frontend', 'Core', 'lbl', 'Breadcrumb', 'breadcrumb', '2017-08-31 14:28:18'),
	(60, 1, 'en', 'Frontend', 'Core', 'lbl', 'By', 'by', '2017-08-31 14:28:18'),
	(61, 1, 'en', 'Frontend', 'Core', 'lbl', 'Categories', 'categories', '2017-08-31 14:28:18'),
	(62, 1, 'en', 'Frontend', 'Core', 'lbl', 'Category', 'category', '2017-08-31 14:28:18'),
	(63, 1, 'en', 'Frontend', 'Core', 'lbl', 'City', 'city', '2017-08-31 14:28:18'),
	(64, 1, 'en', 'Frontend', 'Core', 'lbl', 'Close', 'close', '2017-08-31 14:28:18'),
	(65, 1, 'en', 'Frontend', 'Core', 'lbl', 'Comment', 'comment', '2017-08-31 14:28:18'),
	(66, 1, 'en', 'Frontend', 'Core', 'lbl', 'CommentedOn', 'commented on', '2017-08-31 14:28:18'),
	(67, 1, 'en', 'Frontend', 'Core', 'lbl', 'Comments', 'comments', '2017-08-31 14:28:18'),
	(68, 1, 'en', 'Frontend', 'Core', 'lbl', 'Contact', 'contact', '2017-08-31 14:28:18'),
	(69, 1, 'en', 'Frontend', 'Core', 'lbl', 'Content', 'content', '2017-08-31 14:28:18'),
	(70, 1, 'en', 'Frontend', 'Core', 'lbl', 'Country', 'country', '2017-08-31 14:28:18'),
	(71, 1, 'en', 'Frontend', 'Core', 'lbl', 'Date', 'date', '2017-08-31 14:28:18'),
	(72, 1, 'en', 'Frontend', 'Core', 'lbl', 'Disclaimer', 'disclaimer', '2017-08-31 14:28:18'),
	(73, 1, 'en', 'Frontend', 'Core', 'lbl', 'DisplayName', 'display name', '2017-08-31 14:28:18'),
	(74, 1, 'en', 'Frontend', 'Core', 'lbl', 'Email', 'e-mail', '2017-08-31 14:28:18'),
	(75, 1, 'en', 'Frontend', 'Core', 'lbl', 'EN', 'English', '2017-08-31 14:28:18'),
	(76, 1, 'en', 'Frontend', 'Core', 'lbl', 'EnableJavascript', 'enable javascript', '2017-08-31 14:28:18'),
	(77, 1, 'en', 'Frontend', 'Core', 'lbl', 'ES', 'Spanish', '2017-08-31 14:28:18'),
	(78, 1, 'en', 'Frontend', 'Core', 'lbl', 'Faq', 'FAQ', '2017-08-31 14:28:18'),
	(79, 1, 'en', 'Frontend', 'Core', 'lbl', 'Feedback', 'feedback', '2017-08-31 14:28:18'),
	(80, 1, 'en', 'Frontend', 'Core', 'lbl', 'Female', 'female', '2017-08-31 14:28:18'),
	(81, 1, 'en', 'Frontend', 'Core', 'lbl', 'FirstName', 'first name', '2017-08-31 14:28:18'),
	(82, 1, 'en', 'Frontend', 'Core', 'lbl', 'FooterNavigation', 'footer navigation', '2017-08-31 14:28:18'),
	(83, 1, 'en', 'Frontend', 'Core', 'lbl', 'FR', 'French', '2017-08-31 14:28:18'),
	(84, 1, 'en', 'Frontend', 'Core', 'lbl', 'Gender', 'gender', '2017-08-31 14:28:18'),
	(85, 1, 'en', 'Frontend', 'Core', 'lbl', 'GoTo', 'go to', '2017-08-31 14:28:18'),
	(86, 1, 'en', 'Frontend', 'Core', 'lbl', 'GoToPage', 'go to page', '2017-08-31 14:28:18'),
	(87, 1, 'en', 'Frontend', 'Core', 'lbl', 'History', 'history', '2017-08-31 14:28:18'),
	(88, 1, 'en', 'Frontend', 'Core', 'lbl', 'IAgree', 'I agree', '2017-08-31 14:28:18'),
	(89, 1, 'en', 'Frontend', 'Core', 'lbl', 'IDisagree', 'I disagree', '2017-08-31 14:28:18'),
	(90, 1, 'en', 'Frontend', 'Core', 'lbl', 'In', 'in', '2017-08-31 14:28:18'),
	(91, 1, 'en', 'Frontend', 'Core', 'lbl', 'InTheCategory', 'in category', '2017-08-31 14:28:18'),
	(92, 1, 'en', 'Frontend', 'Core', 'lbl', 'ItemsWithTag', 'items with tag \"%1$s\"', '2017-08-31 14:28:18'),
	(93, 1, 'en', 'Frontend', 'Core', 'lbl', 'Language', 'language', '2017-08-31 14:28:18'),
	(94, 1, 'en', 'Frontend', 'Core', 'lbl', 'LastName', 'last name', '2017-08-31 14:28:18'),
	(95, 1, 'en', 'Frontend', 'Core', 'lbl', 'Location', 'location', '2017-08-31 14:28:18'),
	(96, 1, 'en', 'Frontend', 'Core', 'lbl', 'Login', 'login', '2017-08-31 14:28:18'),
	(97, 1, 'en', 'Frontend', 'Core', 'lbl', 'Logout', 'logout', '2017-08-31 14:28:18'),
	(98, 1, 'en', 'Frontend', 'Core', 'lbl', 'LT', 'Lithuanian', '2017-08-31 14:28:18'),
	(99, 1, 'en', 'Frontend', 'Core', 'lbl', 'MainNavigation', 'main navigation', '2017-08-31 14:28:18'),
	(100, 1, 'en', 'Frontend', 'Core', 'lbl', 'Male', 'male', '2017-08-31 14:28:18'),
	(101, 1, 'en', 'Frontend', 'Core', 'lbl', 'Message', 'message', '2017-08-31 14:28:18'),
	(102, 1, 'en', 'Frontend', 'Core', 'lbl', 'More', 'more', '2017-08-31 14:28:18'),
	(103, 1, 'en', 'Frontend', 'Core', 'lbl', 'MostReadQuestions', 'Most read questions', '2017-08-31 14:28:18'),
	(104, 1, 'en', 'Frontend', 'Core', 'lbl', 'Name', 'name', '2017-08-31 14:28:18'),
	(105, 1, 'en', 'Frontend', 'Core', 'lbl', 'NewPassword', 'new password', '2017-08-31 14:28:18'),
	(106, 1, 'en', 'Frontend', 'Core', 'lbl', 'VerifyNewPassword', 'verify new password', '2017-08-31 14:28:18'),
	(107, 1, 'en', 'Frontend', 'Core', 'err', 'PasswordsDontMatch', 'The passwords differ', '2017-08-31 14:28:18'),
	(108, 1, 'en', 'Frontend', 'Core', 'lbl', 'Next', 'next', '2017-08-31 14:28:18'),
	(109, 1, 'en', 'Frontend', 'Core', 'lbl', 'NextArticle', 'next article', '2017-08-31 14:28:18'),
	(110, 1, 'en', 'Frontend', 'Core', 'lbl', 'NextPage', 'next page', '2017-08-31 14:28:18'),
	(111, 1, 'en', 'Frontend', 'Core', 'lbl', 'NL', 'Dutch', '2017-08-31 14:28:18'),
	(112, 1, 'en', 'Frontend', 'Core', 'lbl', 'UK', 'Ukrainian', '2017-08-31 14:28:18'),
	(113, 1, 'en', 'Frontend', 'Core', 'lbl', 'SV', 'Swedish', '2017-08-31 14:28:18'),
	(114, 1, 'en', 'Frontend', 'Core', 'lbl', 'RU', 'Russian', '2017-08-31 14:28:18'),
	(115, 1, 'en', 'Frontend', 'Core', 'lbl', 'IT', 'Italian', '2017-08-31 14:28:18'),
	(116, 1, 'en', 'Frontend', 'Core', 'lbl', 'HU', 'Hungarian', '2017-08-31 14:28:18'),
	(117, 1, 'en', 'Frontend', 'Core', 'lbl', 'EL', 'Greek', '2017-08-31 14:28:18'),
	(118, 1, 'en', 'Frontend', 'Core', 'lbl', 'ZH', 'Chinese', '2017-08-31 14:28:18'),
	(119, 1, 'en', 'Frontend', 'Core', 'lbl', 'DE', 'German', '2017-08-31 14:28:18'),
	(120, 1, 'en', 'Frontend', 'Core', 'lbl', 'No', 'no', '2017-08-31 14:28:18'),
	(121, 1, 'en', 'Frontend', 'Core', 'lbl', 'OldPassword', 'old password', '2017-08-31 14:28:18'),
	(122, 1, 'en', 'Frontend', 'Core', 'lbl', 'On', 'on', '2017-08-31 14:28:18'),
	(123, 1, 'en', 'Frontend', 'Core', 'lbl', 'Or', 'or', '2017-08-31 14:28:18'),
	(124, 1, 'en', 'Frontend', 'Core', 'lbl', 'Pages', 'pages', '2017-08-31 14:28:18'),
	(125, 1, 'en', 'Frontend', 'Core', 'lbl', 'Parent', 'parent', '2017-08-31 14:28:18'),
	(126, 1, 'en', 'Frontend', 'Core', 'lbl', 'ParentPage', 'parent page', '2017-08-31 14:28:18'),
	(127, 1, 'en', 'Frontend', 'Core', 'lbl', 'Password', 'password', '2017-08-31 14:28:18'),
	(128, 1, 'en', 'Frontend', 'Core', 'lbl', 'Previous', 'previous', '2017-08-31 14:28:18'),
	(129, 1, 'en', 'Frontend', 'Core', 'lbl', 'PreviousArticle', 'previous article', '2017-08-31 14:28:18'),
	(130, 1, 'en', 'Frontend', 'Core', 'lbl', 'PreviousPage', 'previous page', '2017-08-31 14:28:18'),
	(131, 1, 'en', 'Frontend', 'Core', 'lbl', 'ProfileSettings', 'settings', '2017-08-31 14:28:18'),
	(132, 1, 'en', 'Frontend', 'Core', 'lbl', 'Question', 'question', '2017-08-31 14:28:18'),
	(133, 1, 'en', 'Frontend', 'Core', 'lbl', 'Questions', 'questions', '2017-08-31 14:28:18'),
	(134, 1, 'en', 'Frontend', 'Core', 'lbl', 'RecentArticles', 'recent articles', '2017-08-31 14:28:18'),
	(135, 1, 'en', 'Frontend', 'Core', 'lbl', 'RecentComments', 'recent comments', '2017-08-31 14:28:18'),
	(136, 1, 'en', 'Frontend', 'Core', 'lbl', 'Register', 'register', '2017-08-31 14:28:18'),
	(137, 1, 'en', 'Frontend', 'Core', 'lbl', 'Related', 'related', '2017-08-31 14:28:18'),
	(138, 1, 'en', 'Frontend', 'Core', 'lbl', 'RememberMe', 'remember me', '2017-08-31 14:28:18'),
	(139, 1, 'en', 'Frontend', 'Core', 'lbl', 'RequiredField', 'required field', '2017-08-31 14:28:18'),
	(140, 1, 'en', 'Frontend', 'Core', 'lbl', 'Save', 'save', '2017-08-31 14:28:18'),
	(141, 1, 'en', 'Frontend', 'Core', 'lbl', 'Search', 'search', '2017-08-31 14:28:18'),
	(142, 1, 'en', 'Frontend', 'Core', 'lbl', 'SearchAgain', 'search again', '2017-08-31 14:28:18'),
	(143, 1, 'en', 'Frontend', 'Core', 'lbl', 'SearchTerm', 'searchterm', '2017-08-31 14:28:18'),
	(144, 1, 'en', 'Frontend', 'Core', 'lbl', 'Send', 'send', '2017-08-31 14:28:18'),
	(145, 1, 'en', 'Frontend', 'Core', 'lbl', 'SenderInformation', 'sender information', '2017-08-31 14:28:18'),
	(146, 1, 'en', 'Frontend', 'Core', 'lbl', 'Sent', 'sent', '2017-08-31 14:28:18'),
	(147, 1, 'en', 'Frontend', 'Core', 'lbl', 'SentMailings', 'sent mailings', '2017-08-31 14:28:18'),
	(148, 1, 'en', 'Frontend', 'Core', 'lbl', 'SentOn', 'sent on', '2017-08-31 14:28:18'),
	(149, 1, 'en', 'Frontend', 'Core', 'lbl', 'Settings', 'settings', '2017-08-31 14:28:18'),
	(150, 1, 'en', 'Frontend', 'Core', 'lbl', 'Share', 'share', '2017-08-31 14:28:18'),
	(151, 1, 'en', 'Frontend', 'Core', 'lbl', 'ShowDirections', 'Show directions', '2017-08-31 14:28:18'),
	(152, 1, 'en', 'Frontend', 'Core', 'lbl', 'ShowPassword', 'show password', '2017-08-31 14:28:18'),
	(153, 1, 'en', 'Frontend', 'Core', 'lbl', 'Sitemap', 'sitemap', '2017-08-31 14:28:18'),
	(154, 1, 'en', 'Frontend', 'Core', 'lbl', 'SkipToContent', 'skip to content', '2017-08-31 14:28:18'),
	(155, 1, 'en', 'Frontend', 'Core', 'lbl', 'Start', 'startpoint', '2017-08-31 14:28:18'),
	(156, 1, 'en', 'Frontend', 'Core', 'lbl', 'Subnavigation', 'subnavigation', '2017-08-31 14:28:18'),
	(157, 1, 'en', 'Frontend', 'Core', 'lbl', 'Subscribe', 'subscribe', '2017-08-31 14:28:18'),
	(158, 1, 'en', 'Frontend', 'Core', 'lbl', 'SubscribeToTheRSSFeed', 'subscribe to the RSS feed', '2017-08-31 14:28:18'),
	(159, 1, 'en', 'Frontend', 'Core', 'lbl', 'Tags', 'tags', '2017-08-31 14:28:18'),
	(160, 1, 'en', 'Frontend', 'Core', 'lbl', 'The', 'the', '2017-08-31 14:28:18'),
	(161, 1, 'en', 'Frontend', 'Core', 'lbl', 'Title', 'title', '2017-08-31 14:28:18'),
	(162, 1, 'en', 'Frontend', 'Core', 'lbl', 'ToFaqOverview', 'to the FAQ overview', '2017-08-31 14:28:18'),
	(163, 1, 'en', 'Frontend', 'Core', 'lbl', 'ToTagsOverview', 'to tags overview', '2017-08-31 14:28:18'),
	(164, 1, 'en', 'Frontend', 'Core', 'lbl', 'Unsubscribe', 'unsubscribe', '2017-08-31 14:28:18'),
	(165, 1, 'en', 'Frontend', 'Core', 'lbl', 'ViewLargeMap', 'Display large map', '2017-08-31 14:28:18'),
	(166, 1, 'en', 'Frontend', 'Core', 'lbl', 'Website', 'website', '2017-08-31 14:28:18'),
	(167, 1, 'en', 'Frontend', 'Core', 'lbl', 'With', 'with', '2017-08-31 14:28:18'),
	(168, 1, 'en', 'Frontend', 'Core', 'lbl', 'WrittenOn', 'written on', '2017-08-31 14:28:18'),
	(169, 1, 'en', 'Frontend', 'Core', 'lbl', 'Wrote', 'wrote', '2017-08-31 14:28:18'),
	(170, 1, 'en', 'Frontend', 'Core', 'lbl', 'Yes', 'yes', '2017-08-31 14:28:18'),
	(171, 1, 'en', 'Frontend', 'Core', 'lbl', 'YouAreHere', 'you are here', '2017-08-31 14:28:18'),
	(172, 1, 'en', 'Frontend', 'Core', 'lbl', 'YourAvatar', 'your avatar', '2017-08-31 14:28:18'),
	(173, 1, 'en', 'Frontend', 'Core', 'lbl', 'YourData', 'your data', '2017-08-31 14:28:18'),
	(174, 1, 'en', 'Frontend', 'Core', 'lbl', 'YourEmail', 'your e-mail address', '2017-08-31 14:28:18'),
	(175, 1, 'en', 'Frontend', 'Core', 'lbl', 'YourLocationData', 'your location', '2017-08-31 14:28:18'),
	(176, 1, 'en', 'Frontend', 'Core', 'lbl', 'YourName', 'your name', '2017-08-31 14:28:18'),
	(177, 1, 'en', 'Frontend', 'Core', 'lbl', 'YourQuestion', 'your question', '2017-08-31 14:28:18'),
	(178, 1, 'en', 'Frontend', 'Core', 'msg', 'ActivationIsSuccess', 'Your profile was activated.', '2017-08-31 14:28:18'),
	(179, 1, 'en', 'Frontend', 'Core', 'msg', 'AlsoInteresting', 'Also interesting for you', '2017-08-31 14:28:18'),
	(180, 1, 'en', 'Frontend', 'Core', 'msg', 'AskOwnQuestion', 'Didn\'t find what you were looking for? Ask your own question!', '2017-08-31 14:28:18'),
	(181, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogAllComments', 'All comments on your blog.', '2017-08-31 14:28:18'),
	(182, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogCommentInModeration', 'Your comment is awaiting moderation.', '2017-08-31 14:28:18'),
	(183, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogCommentIsAdded', 'Your comment was added.', '2017-08-31 14:28:18'),
	(184, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogCommentIsSpam', 'Your comment was marked as spam.', '2017-08-31 14:28:18'),
	(185, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogEmailNotificationsNewComment', '%1$s commented on <a href=\"%2$s\">%3$s</a>.', '2017-08-31 14:28:18'),
	(186, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogEmailNotificationsNewCommentToModerate', '%1$s commented on <a href=\"%2$s\">%3$s</a>. <a href=\"%4$s\">Moderate</a> the comment to publish it.', '2017-08-31 14:28:18'),
	(187, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogNoComments', 'Be the first to comment', '2017-08-31 14:28:18'),
	(188, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogNoItems', 'There are no articles yet.', '2017-08-31 14:28:18'),
	(189, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogNumberOfComments', '%1$s comments', '2017-08-31 14:28:18'),
	(190, 1, 'en', 'Frontend', 'Core', 'msg', 'BlogOneComment', '1 comment already', '2017-08-31 14:28:18'),
	(191, 1, 'en', 'Frontend', 'Core', 'msg', 'ChangeEmail', 'change your e-mail address', '2017-08-31 14:28:18'),
	(192, 1, 'en', 'Frontend', 'Core', 'msg', 'Comment', 'comment', '2017-08-31 14:28:18'),
	(193, 1, 'en', 'Frontend', 'Core', 'msg', 'CommentsOn', 'Comments on %1$s', '2017-08-31 14:28:18'),
	(194, 1, 'en', 'Frontend', 'Core', 'msg', 'ContactMessageSent', 'Your e-mail was sent.', '2017-08-31 14:28:18'),
	(195, 1, 'en', 'Frontend', 'Core', 'msg', 'ContactSubject', 'E-mail via contact form.', '2017-08-31 14:28:18'),
	(196, 1, 'en', 'Frontend', 'Core', 'msg', 'CookiesWarning', 'To improve the user experience on this site we use <a href=\"/disclaimer\">cookies</a>.', '2017-08-31 14:28:18'),
	(197, 1, 'en', 'Frontend', 'Core', 'msg', 'EN', 'English', '2017-08-31 14:28:18'),
	(198, 1, 'en', 'Frontend', 'Core', 'msg', 'EnableJavascript', 'Having javascript enabled is recommended for using this site.', '2017-08-31 14:28:18'),
	(199, 1, 'en', 'Frontend', 'Core', 'msg', 'FaqFeedbackSubject', 'There is feedback on \"%1$s\"', '2017-08-31 14:28:18'),
	(200, 1, 'en', 'Frontend', 'Core', 'msg', 'FaqNoItems', 'There are no questions yet.', '2017-08-31 14:28:18'),
	(201, 1, 'en', 'Frontend', 'Core', 'msg', 'FaqOwnQuestionSubject', 'A question from %1$s.', '2017-08-31 14:28:18'),
	(202, 1, 'en', 'Frontend', 'Core', 'msg', 'Feedback', 'Was this answer helpful?', '2017-08-31 14:28:18'),
	(203, 1, 'en', 'Frontend', 'Core', 'msg', 'FeedbackSuccess', 'Your feedback has been sent.', '2017-08-31 14:28:18'),
	(204, 1, 'en', 'Frontend', 'Core', 'msg', 'ForgotPassword', 'Forgot your password?', '2017-08-31 14:28:18'),
	(205, 1, 'en', 'Frontend', 'Core', 'msg', 'ForgotPasswordBody', 'You just requested to reset your password on <a href=\"%1$s\">Fork CMS</a>. Follow the link below to reset your password.<br /><br /><a href=\"%2$s\">%2$s</a>', '2017-08-31 14:28:18'),
	(206, 1, 'en', 'Frontend', 'Core', 'msg', 'ForgotPasswordClosure', 'With kind regards,<br/><br/>The Fork CMS team', '2017-08-31 14:28:18'),
	(207, 1, 'en', 'Frontend', 'Core', 'msg', 'ForgotPasswordIsSuccess', 'In less than ten minutes you will receive an e-mail to reset your password.', '2017-08-31 14:28:18'),
	(208, 1, 'en', 'Frontend', 'Core', 'msg', 'ForgotPasswordSalutation', 'Dear,', '2017-08-31 14:28:18'),
	(209, 1, 'en', 'Frontend', 'Core', 'msg', 'ForgotPasswordSubject', 'Forgot your password?', '2017-08-31 14:28:18'),
	(210, 1, 'en', 'Frontend', 'Core', 'msg', 'FormBuilderSubject', 'New submission for form \"%1$s\".', '2017-08-31 14:28:18'),
	(211, 1, 'en', 'Frontend', 'Core', 'msg', 'FR', 'French', '2017-08-31 14:28:18'),
	(212, 1, 'en', 'Frontend', 'Core', 'msg', 'HelpDateField', 'eg. 20/06/2011', '2017-08-31 14:28:18'),
	(213, 1, 'en', 'Frontend', 'Core', 'msg', 'HelpDisplayNameChanges', 'The amount of display name changes is limited to %1$s. You have %2$s change(s) left.', '2017-08-31 14:28:18'),
	(214, 1, 'en', 'Frontend', 'Core', 'msg', 'HelpImageField', 'Only jp(e)g, gif or png-files are allowed.', '2017-08-31 14:28:18'),
	(215, 1, 'en', 'Frontend', 'Core', 'msg', 'HelpTimeField', 'eg. 14:35', '2017-08-31 14:28:18'),
	(216, 1, 'en', 'Frontend', 'Core', 'msg', 'HowToImprove', 'How can we improve this answer?', '2017-08-31 14:28:18'),
	(217, 1, 'en', 'Frontend', 'Core', 'msg', 'MoreResults', 'Find more results…', '2017-08-31 14:28:18'),
	(218, 1, 'en', 'Frontend', 'Core', 'msg', 'NL', 'Dutch', '2017-08-31 14:28:18'),
	(219, 1, 'en', 'Frontend', 'Core', 'msg', 'UK', 'Ukrainian', '2017-08-31 14:28:18'),
	(220, 1, 'en', 'Frontend', 'Core', 'msg', 'SV', 'Swedish', '2017-08-31 14:28:18'),
	(221, 1, 'en', 'Frontend', 'Core', 'msg', 'ES', 'Spanish', '2017-08-31 14:28:18'),
	(222, 1, 'en', 'Frontend', 'Core', 'msg', 'RU', 'Russian', '2017-08-31 14:28:18'),
	(223, 1, 'en', 'Frontend', 'Core', 'msg', 'LT', 'Lithuanian', '2017-08-31 14:28:18'),
	(224, 1, 'en', 'Frontend', 'Core', 'msg', 'IT', 'Italian', '2017-08-31 14:28:18'),
	(225, 1, 'en', 'Frontend', 'Core', 'msg', 'HU', 'Hungarian', '2017-08-31 14:28:18'),
	(226, 1, 'en', 'Frontend', 'Core', 'msg', 'EL', 'Greek', '2017-08-31 14:28:18'),
	(227, 1, 'en', 'Frontend', 'Core', 'msg', 'DE', 'German', '2017-08-31 14:28:18'),
	(228, 1, 'en', 'Frontend', 'Core', 'msg', 'ZH', 'Chinese', '2017-08-31 14:28:18'),
	(229, 1, 'en', 'Frontend', 'Core', 'msg', 'NoQuestionsInCategory', 'There are no questions in this category.', '2017-08-31 14:28:18'),
	(230, 1, 'en', 'Frontend', 'Core', 'msg', 'NoSentMailings', 'So far', '2017-08-31 14:28:18'),
	(231, 1, 'en', 'Frontend', 'Core', 'msg', 'NotificationSubject', 'Notification', '2017-08-31 14:28:18'),
	(232, 1, 'en', 'Frontend', 'Core', 'msg', 'OtherQuestions', 'Other questions', '2017-08-31 14:28:18'),
	(233, 1, 'en', 'Frontend', 'Core', 'msg', 'OwnQuestionSuccess', 'Your question has been sent. We\'ll give you an answer as soon as possible.', '2017-08-31 14:28:18'),
	(234, 1, 'en', 'Frontend', 'Core', 'msg', 'ProfilesLoggedInAs', 'You are logged on as <a href=\"%2$s\">%1$s</a>.', '2017-08-31 14:28:18'),
	(235, 1, 'en', 'Frontend', 'Core', 'msg', 'QuestionsInSameCategory', 'Other questions in this category', '2017-08-31 14:28:18'),
	(236, 1, 'en', 'Frontend', 'Core', 'msg', 'RegisterBody', 'You have just registered on the <a href=\"%1$s\">Fork CMS</a> site. To activate your profile you need to follow the link below.<br /><br /><a href=\"%2$s\">%2$s</a>', '2017-08-31 14:28:18'),
	(237, 1, 'en', 'Frontend', 'Core', 'msg', 'RegisterClosure', 'With kind regards,<br/><br/>The Fork CMS team', '2017-08-31 14:28:18'),
	(238, 1, 'en', 'Frontend', 'Core', 'msg', 'RegisterIsSuccess', 'Welcome! In less than ten minutes you will receive an activation mail. In the mean while you can use the website in a limited form.', '2017-08-31 14:28:18'),
	(239, 1, 'en', 'Frontend', 'Core', 'msg', 'RegisterSalutation', 'Dear,', '2017-08-31 14:28:18'),
	(240, 1, 'en', 'Frontend', 'Core', 'msg', 'RegisterSubject', 'Activate your Fork CMS-profile', '2017-08-31 14:28:18'),
	(241, 1, 'en', 'Frontend', 'Core', 'msg', 'RelatedQuestions', 'Also read', '2017-08-31 14:28:18'),
	(242, 1, 'en', 'Frontend', 'Core', 'msg', 'ResendActivationIsSuccess', 'In less than ten minutes you will receive an new activation mail. A simple click on the link and you will be able to log in.', '2017-08-31 14:28:18'),
	(243, 1, 'en', 'Frontend', 'Core', 'msg', 'ResetPasswordIsSuccess', 'Your password was saved.', '2017-08-31 14:28:18'),
	(244, 1, 'en', 'Frontend', 'Core', 'msg', 'SearchNoItems', 'There were no results.', '2017-08-31 14:28:18'),
	(245, 1, 'en', 'Frontend', 'Core', 'msg', 'SubscribeSuccess', 'You have successfully subscribed to the newsletter.', '2017-08-31 14:28:18'),
	(246, 1, 'en', 'Frontend', 'Core', 'msg', 'TagsNoItems', 'No tags were used.', '2017-08-31 14:28:18'),
	(247, 1, 'en', 'Frontend', 'Core', 'msg', 'UnsubscribeSuccess', 'You have successfully unsubscribed from the newsletter.', '2017-08-31 14:28:18'),
	(248, 1, 'en', 'Frontend', 'Core', 'msg', 'UpdateEmailIsSuccess', 'Your e-mail was saved.', '2017-08-31 14:28:18'),
	(249, 1, 'en', 'Frontend', 'Core', 'msg', 'UpdatePasswordIsSuccess', 'Your password was saved.', '2017-08-31 14:28:18'),
	(250, 1, 'en', 'Frontend', 'Core', 'msg', 'UpdateSettingsIsSuccess', 'The settings were saved.', '2017-08-31 14:28:18'),
	(251, 1, 'en', 'Frontend', 'Core', 'msg', 'WelcomeUserX', 'Welcome, %1$s', '2017-08-31 14:28:18'),
	(252, 1, 'en', 'Frontend', 'Core', 'msg', 'WrittenBy', 'written by %1$s', '2017-08-31 14:28:18'),
	(253, 1, 'en', 'Frontend', 'Core', 'err', 'AlreadySubscribed', 'This e-mail address is already subscribed to the newsletter.', '2017-08-31 14:28:18'),
	(254, 1, 'en', 'Frontend', 'Core', 'err', 'AlreadyUnsubscribed', 'This e-mail address is already unsubscribed from the newsletter', '2017-08-31 14:28:18'),
	(255, 1, 'en', 'Frontend', 'Core', 'err', 'AuthorIsRequired', 'Author is a required field.', '2017-08-31 14:28:18'),
	(256, 1, 'en', 'Frontend', 'Core', 'err', 'CommentTimeout', 'Slow down cowboy', '2017-08-31 14:28:18'),
	(257, 1, 'en', 'Frontend', 'Core', 'err', 'ContactErrorWhileSending', 'Something went wrong while trying to send', '2017-08-31 14:28:18'),
	(258, 1, 'en', 'Frontend', 'Core', 'err', 'DateIsInvalid', 'Invalid date.', '2017-08-31 14:28:18'),
	(259, 1, 'en', 'Frontend', 'Core', 'err', 'DisplayNameExists', 'This display name is in use.', '2017-08-31 14:28:18'),
	(260, 1, 'en', 'Frontend', 'Core', 'err', 'DisplayNameIsRequired', 'Display name is a required field.', '2017-08-31 14:28:18'),
	(261, 1, 'en', 'Frontend', 'Core', 'err', 'EmailExists', 'This e-mailaddress is in use.', '2017-08-31 14:28:18'),
	(262, 1, 'en', 'Frontend', 'Core', 'err', 'EmailIsInvalid', 'Please provide a valid e-mail address.', '2017-08-31 14:28:18'),
	(263, 1, 'en', 'Frontend', 'Core', 'err', 'EmailIsRequired', 'E-mail is a required field.', '2017-08-31 14:28:18'),
	(264, 1, 'en', 'Frontend', 'Core', 'err', 'EmailIsUnknown', 'This e-mailaddress is unknown in our database.', '2017-08-31 14:28:18'),
	(265, 1, 'en', 'Frontend', 'Core', 'err', 'EmailNotInDatabase', 'This e-mail address does not exist in the database.', '2017-08-31 14:28:18'),
	(266, 1, 'en', 'Frontend', 'Core', 'err', 'FeedbackIsRequired', 'Please provide feedback.', '2017-08-31 14:28:18'),
	(267, 1, 'en', 'Frontend', 'Core', 'err', 'FeedbackSpam', 'Your feedback was marked as spam.', '2017-08-31 14:28:18'),
	(268, 1, 'en', 'Frontend', 'Core', 'err', 'FieldIsRequired', 'This field is required.', '2017-08-31 14:28:18'),
	(269, 1, 'en', 'Frontend', 'Core', 'err', 'FileTooBig', 'maximum filesize: %1$s', '2017-08-31 14:28:18'),
	(270, 1, 'en', 'Frontend', 'Core', 'err', 'FormError', 'Something went wrong', '2017-08-31 14:28:18'),
	(271, 1, 'en', 'Frontend', 'Core', 'err', 'FormTimeout', 'Slow down cowboy', '2017-08-31 14:28:18'),
	(272, 1, 'en', 'Frontend', 'Core', 'err', 'InvalidPassword', 'Invalid password.', '2017-08-31 14:28:18'),
	(273, 1, 'en', 'Frontend', 'Core', 'err', 'InvalidValue', 'Invalid value.', '2017-08-31 14:28:18'),
	(274, 1, 'en', 'Frontend', 'Core', 'err', 'InvalidPrice', 'Please insert a valid price.', '2017-08-31 14:28:18'),
	(275, 1, 'en', 'Frontend', 'Core', 'err', 'InvalidURL', 'This is an invalid URL.', '2017-08-31 14:28:18'),
	(276, 1, 'en', 'Frontend', 'Core', 'err', 'JPGGIFAndPNGOnly', 'Only jpg, gif, png', '2017-08-31 14:28:18'),
	(277, 1, 'en', 'Frontend', 'Core', 'err', 'MessageIsRequired', 'Message is a required field.', '2017-08-31 14:28:18'),
	(278, 1, 'en', 'Frontend', 'Core', 'err', 'NameIsRequired', 'Please provide a name.', '2017-08-31 14:28:18'),
	(279, 1, 'en', 'Frontend', 'Core', 'err', 'NumericCharactersOnly', 'Only numeric characters are allowed.', '2017-08-31 14:28:18'),
	(280, 1, 'en', 'Frontend', 'Core', 'err', 'OwnQuestionSpam', 'Your question was marked as spam.', '2017-08-31 14:28:18'),
	(281, 1, 'en', 'Frontend', 'Core', 'err', 'PasswordIsRequired', 'Password is a required field.', '2017-08-31 14:28:18'),
	(282, 1, 'en', 'Frontend', 'Core', 'err', 'ProfileIsActive', 'This profile is already activated.', '2017-08-31 14:28:18'),
	(283, 1, 'en', 'Frontend', 'Core', 'err', 'ProfilesBlockedLogin', 'Login failed. This profile is blocked.', '2017-08-31 14:28:18'),
	(284, 1, 'en', 'Frontend', 'Core', 'err', 'ProfilesDeletedLogin', 'Login failed. This profile has been deleted.', '2017-08-31 14:28:18'),
	(285, 1, 'en', 'Frontend', 'Core', 'err', 'ProfilesInactiveLogin', 'Login failed. This profile is not yet activated. <a href=\"%1$s\">Resend activation e-mail</a>.', '2017-08-31 14:28:18'),
	(286, 1, 'en', 'Frontend', 'Core', 'err', 'ProfilesInvalidLogin', 'Login failed. Please check your e-mail and your password.', '2017-08-31 14:28:18'),
	(287, 1, 'en', 'Frontend', 'Core', 'err', 'QuestionIsRequired', 'Please provide a question.', '2017-08-31 14:28:18'),
	(288, 1, 'en', 'Frontend', 'Core', 'err', 'SomethingWentWrong', 'Something went wrong.', '2017-08-31 14:28:18'),
	(289, 1, 'en', 'Frontend', 'Core', 'err', 'SubscribeFailed', 'Subscribing failed', '2017-08-31 14:28:18'),
	(290, 1, 'en', 'Frontend', 'Core', 'err', 'TermIsRequired', 'The searchterm is required.', '2017-08-31 14:28:18'),
	(291, 1, 'en', 'Frontend', 'Core', 'err', 'UnsubscribeFailed', 'Unsubscribing failed', '2017-08-31 14:28:18'),
	(292, 1, 'en', 'Frontend', 'Core', 'act', 'Archive', 'archive', '2017-08-31 14:28:18'),
	(293, 1, 'en', 'Frontend', 'Core', 'act', 'ArticleCommentsRss', 'comments-on-rss', '2017-08-31 14:28:18'),
	(294, 1, 'en', 'Frontend', 'Core', 'act', 'Category', 'category', '2017-08-31 14:28:18'),
	(295, 1, 'en', 'Frontend', 'Core', 'act', 'Comment', 'comment', '2017-08-31 14:28:18'),
	(296, 1, 'en', 'Frontend', 'Core', 'act', 'Comments', 'comments', '2017-08-31 14:28:18'),
	(297, 1, 'en', 'Frontend', 'Core', 'act', 'CommentsRss', 'comments-rss', '2017-08-31 14:28:18'),
	(298, 1, 'en', 'Frontend', 'Core', 'act', 'Detail', 'detail', '2017-08-31 14:28:18'),
	(299, 1, 'en', 'Frontend', 'Core', 'act', 'Feedback', 'feedback', '2017-08-31 14:28:18'),
	(300, 1, 'en', 'Frontend', 'Core', 'act', 'OwnQuestion', 'ask-your-question', '2017-08-31 14:28:18'),
	(301, 1, 'en', 'Frontend', 'Core', 'act', 'Preview', 'preview', '2017-08-31 14:28:18'),
	(302, 1, 'en', 'Frontend', 'Core', 'act', 'Rss', 'rss', '2017-08-31 14:28:18'),
	(303, 1, 'en', 'Frontend', 'Core', 'act', 'Spam', 'spam', '2017-08-31 14:28:18'),
	(304, 1, 'en', 'Frontend', 'Core', 'act', 'Subscribe', 'subscribe', '2017-08-31 14:28:18'),
	(305, 1, 'en', 'Frontend', 'Core', 'act', 'Success', 'success', '2017-08-31 14:28:18'),
	(306, 1, 'en', 'Frontend', 'Core', 'act', 'Unsubscribe', 'unsubscribe', '2017-08-31 14:28:18'),
	(307, 1, 'en', 'Frontend', 'Core', 'lbl', 'Loading', 'loading', '2017-08-31 14:28:18'),
	(308, 1, 'en', 'Backend', 'Core', 'err', 'NumericCharactersOnly', 'Only numeric characters are allowed.', '2017-08-31 14:28:18'),
	(309, 1, 'en', 'Backend', 'Core', 'msg', 'ConfirmDefault', 'Are you sure you want to perform this actions?', '2017-08-31 14:28:18'),
	(310, 1, 'en', 'Backend', 'Core', 'lbl', 'ProfileSettings', 'settings', '2017-08-31 14:28:18'),
	(311, 1, 'en', 'Backend', 'Core', 'lbl', 'AccountManagement', 'account management', '2017-08-31 14:28:18'),
	(312, 1, 'en', 'Backend', 'Core', 'lbl', 'AccountSettings', 'account settings', '2017-08-31 14:28:18'),
	(313, 1, 'en', 'Backend', 'Core', 'lbl', 'Activate', 'activate', '2017-08-31 14:28:18'),
	(314, 1, 'en', 'Backend', 'Core', 'lbl', 'Active', 'active', '2017-08-31 14:28:18'),
	(315, 1, 'en', 'Backend', 'Core', 'lbl', 'Add', 'add', '2017-08-31 14:28:18'),
	(316, 1, 'en', 'Backend', 'Core', 'lbl', 'AddBlock', 'add block', '2017-08-31 14:28:18'),
	(317, 1, 'en', 'Backend', 'Core', 'lbl', 'AddCategory', 'add category', '2017-08-31 14:28:18'),
	(318, 1, 'en', 'Backend', 'Core', 'lbl', 'Address', 'address', '2017-08-31 14:28:18'),
	(319, 1, 'en', 'Backend', 'Core', 'lbl', 'EmailAddresses', 'e-mail addresses', '2017-08-31 14:28:18'),
	(320, 1, 'en', 'Backend', 'Core', 'lbl', 'AddTemplate', 'add template', '2017-08-31 14:28:18'),
	(321, 1, 'en', 'Backend', 'Core', 'lbl', 'Advanced', 'advanced', '2017-08-31 14:28:18'),
	(322, 1, 'en', 'Backend', 'Core', 'lbl', 'AllEmailAddresses', 'all e-mail addresses', '2017-08-31 14:28:18'),
	(323, 1, 'en', 'Backend', 'Core', 'lbl', 'AllComments', 'all comments', '2017-08-31 14:28:18'),
	(324, 1, 'en', 'Backend', 'Core', 'lbl', 'AllowComments', 'allow comments', '2017-08-31 14:28:18'),
	(325, 1, 'en', 'Backend', 'Core', 'lbl', 'AllPages', 'all pages', '2017-08-31 14:28:18'),
	(326, 1, 'en', 'Backend', 'Core', 'lbl', 'AllQuestions', 'all questions', '2017-08-31 14:28:18'),
	(327, 1, 'en', 'Backend', 'Core', 'lbl', 'Amount', 'amount', '2017-08-31 14:28:18'),
	(328, 1, 'en', 'Backend', 'Core', 'lbl', 'Analyse', 'analyse', '2017-08-31 14:28:18'),
	(329, 1, 'en', 'Backend', 'Core', 'lbl', 'Analysis', 'analysis', '2017-08-31 14:28:18'),
	(330, 1, 'en', 'Backend', 'Core', 'lbl', 'Analytics', 'analytics', '2017-08-31 14:28:18'),
	(331, 1, 'en', 'Backend', 'Core', 'lbl', 'APIKey', 'API key', '2017-08-31 14:28:18'),
	(332, 1, 'en', 'Backend', 'Core', 'lbl', 'APIKeys', 'API keys', '2017-08-31 14:28:18'),
	(333, 1, 'en', 'Backend', 'Core', 'lbl', 'APIURL', 'API URL', '2017-08-31 14:28:18'),
	(334, 1, 'en', 'Backend', 'Core', 'lbl', 'Application', 'application', '2017-08-31 14:28:18'),
	(335, 1, 'en', 'Backend', 'Core', 'lbl', 'Approve', 'approve', '2017-08-31 14:28:18'),
	(336, 1, 'en', 'Backend', 'Core', 'lbl', 'Archive', 'archive', '2017-08-31 14:28:18'),
	(337, 1, 'en', 'Backend', 'Core', 'lbl', 'Archived', 'archived', '2017-08-31 14:28:18'),
	(338, 1, 'en', 'Backend', 'Core', 'lbl', 'Article', 'article', '2017-08-31 14:28:18'),
	(339, 1, 'en', 'Backend', 'Core', 'lbl', 'Articles', 'articles', '2017-08-31 14:28:18'),
	(340, 1, 'en', 'Backend', 'Core', 'lbl', 'AskOwnQuestion', 'ask own question', '2017-08-31 14:28:18'),
	(341, 1, 'en', 'Backend', 'Core', 'lbl', 'At', 'at', '2017-08-31 14:28:18'),
	(342, 1, 'en', 'Backend', 'Core', 'lbl', 'Authentication', 'authentication', '2017-08-31 14:28:18'),
	(343, 1, 'en', 'Backend', 'Core', 'lbl', 'Author', 'author', '2017-08-31 14:28:18'),
	(344, 1, 'en', 'Backend', 'Core', 'lbl', 'Avatar', 'avatar', '2017-08-31 14:28:18'),
	(345, 1, 'en', 'Backend', 'Core', 'lbl', 'Average', 'average', '2017-08-31 14:28:18'),
	(346, 1, 'en', 'Backend', 'Core', 'lbl', 'Back', 'back', '2017-08-31 14:28:18'),
	(347, 1, 'en', 'Backend', 'Core', 'lbl', 'Backend', 'backend', '2017-08-31 14:28:18'),
	(348, 1, 'en', 'Backend', 'Core', 'lbl', 'BG', 'Bulgarian', '2017-08-31 14:28:18'),
	(349, 1, 'en', 'Backend', 'Core', 'lbl', 'Block', 'block', '2017-08-31 14:28:18'),
	(350, 1, 'en', 'Backend', 'Core', 'lbl', 'Blog', 'blog', '2017-08-31 14:28:18'),
	(351, 1, 'en', 'Backend', 'Core', 'lbl', 'Bounces', 'bounces', '2017-08-31 14:28:18'),
	(352, 1, 'en', 'Backend', 'Core', 'lbl', 'BounceType', 'bounce type', '2017-08-31 14:28:18'),
	(353, 1, 'en', 'Backend', 'Core', 'lbl', 'BrowserNotSupported', 'browser not supported', '2017-08-31 14:28:18'),
	(354, 1, 'en', 'Backend', 'Core', 'lbl', 'By', 'by', '2017-08-31 14:28:18'),
	(355, 1, 'en', 'Backend', 'Core', 'lbl', 'Campaigns', 'campaigns', '2017-08-31 14:28:18'),
	(356, 1, 'en', 'Backend', 'Core', 'lbl', 'Cancel', 'cancel', '2017-08-31 14:28:18'),
	(357, 1, 'en', 'Backend', 'Core', 'lbl', 'Categories', 'categories', '2017-08-31 14:28:18'),
	(358, 1, 'en', 'Backend', 'Core', 'lbl', 'Category', 'category', '2017-08-31 14:28:18'),
	(359, 1, 'en', 'Backend', 'Core', 'lbl', 'ChangeEmail', 'change e-mail', '2017-08-31 14:28:18'),
	(360, 1, 'en', 'Backend', 'Core', 'lbl', 'ChangePassword', 'change password', '2017-08-31 14:28:18'),
	(361, 1, 'en', 'Backend', 'Core', 'lbl', 'ChooseALanguage', 'choose a language', '2017-08-31 14:28:18'),
	(362, 1, 'en', 'Backend', 'Core', 'lbl', 'ChooseAModule', 'choose a module', '2017-08-31 14:28:18'),
	(363, 1, 'en', 'Backend', 'Core', 'lbl', 'ChooseAnApplication', 'choose an application', '2017-08-31 14:28:18'),
	(364, 1, 'en', 'Backend', 'Core', 'lbl', 'ChooseATemplate', 'choose a template', '2017-08-31 14:28:18'),
	(365, 1, 'en', 'Backend', 'Core', 'lbl', 'ChooseAType', 'choose a type', '2017-08-31 14:28:18'),
	(366, 1, 'en', 'Backend', 'Core', 'lbl', 'ChooseContent', 'choose content', '2017-08-31 14:28:18'),
	(367, 1, 'en', 'Backend', 'Core', 'lbl', 'City', 'city', '2017-08-31 14:28:18'),
	(368, 1, 'en', 'Backend', 'Core', 'lbl', 'ClientSettings', 'client settings', '2017-08-31 14:28:18'),
	(369, 1, 'en', 'Backend', 'Core', 'lbl', 'CN', 'Chinese', '2017-08-31 14:28:18'),
	(370, 1, 'en', 'Backend', 'Core', 'lbl', 'Comment', 'comment', '2017-08-31 14:28:18'),
	(371, 1, 'en', 'Backend', 'Core', 'lbl', 'Comments', 'comments', '2017-08-31 14:28:18'),
	(372, 1, 'en', 'Backend', 'Core', 'lbl', 'ConfirmPassword', 'confirm password', '2017-08-31 14:28:18'),
	(373, 1, 'en', 'Backend', 'Core', 'lbl', 'Contact', 'contact', '2017-08-31 14:28:18'),
	(374, 1, 'en', 'Backend', 'Core', 'lbl', 'ContactForm', 'contact form', '2017-08-31 14:28:18'),
	(375, 1, 'en', 'Backend', 'Core', 'lbl', 'Content', 'content', '2017-08-31 14:28:18'),
	(376, 1, 'en', 'Backend', 'Core', 'lbl', 'ContentBlocks', 'content blocks', '2017-08-31 14:28:18'),
	(377, 1, 'en', 'Backend', 'Core', 'lbl', 'Copy', 'copy', '2017-08-31 14:28:18'),
	(378, 1, 'en', 'Backend', 'Core', 'lbl', 'Core', 'core', '2017-08-31 14:28:18'),
	(379, 1, 'en', 'Backend', 'Core', 'lbl', 'Country', 'country', '2017-08-31 14:28:18'),
	(380, 1, 'en', 'Backend', 'Core', 'lbl', 'Created', 'created', '2017-08-31 14:28:18'),
	(381, 1, 'en', 'Backend', 'Core', 'lbl', 'CreatedOn', 'created on', '2017-08-31 14:28:18'),
	(382, 1, 'en', 'Backend', 'Core', 'lbl', 'CS', 'Czech', '2017-08-31 14:28:18'),
	(383, 1, 'en', 'Backend', 'Core', 'lbl', 'CSV', 'CSV', '2017-08-31 14:28:18'),
	(384, 1, 'en', 'Backend', 'Core', 'lbl', 'CurrentPassword', 'current password', '2017-08-31 14:28:18'),
	(385, 1, 'en', 'Backend', 'Core', 'lbl', 'CustomURL', 'custom URL', '2017-08-31 14:28:18'),
	(386, 1, 'en', 'Backend', 'Core', 'lbl', 'Dashboard', 'dashboard', '2017-08-31 14:28:18'),
	(387, 1, 'en', 'Backend', 'Core', 'lbl', 'Date', 'date', '2017-08-31 14:28:18'),
	(388, 1, 'en', 'Backend', 'Core', 'lbl', 'DateAndTime', 'date and time', '2017-08-31 14:28:18'),
	(389, 1, 'en', 'Backend', 'Core', 'lbl', 'DateFormat', 'date format', '2017-08-31 14:28:18'),
	(390, 1, 'en', 'Backend', 'Core', 'lbl', 'DE', 'German', '2017-08-31 14:28:18'),
	(391, 1, 'en', 'Backend', 'Core', 'lbl', 'Dear', 'dear', '2017-08-31 14:28:18'),
	(392, 1, 'en', 'Backend', 'Core', 'lbl', 'DebugMode', 'debug', '2017-08-31 14:28:18'),
	(393, 1, 'en', 'Backend', 'Core', 'lbl', 'Default', 'default', '2017-08-31 14:28:18'),
	(394, 1, 'en', 'Backend', 'Core', 'lbl', 'Delete', 'delete', '2017-08-31 14:28:18'),
	(395, 1, 'en', 'Backend', 'Core', 'lbl', 'Description', 'description', '2017-08-31 14:28:18'),
	(396, 1, 'en', 'Backend', 'Core', 'lbl', 'Details', 'details', '2017-08-31 14:28:18'),
	(397, 1, 'en', 'Backend', 'Core', 'lbl', 'Developer', 'developer', '2017-08-31 14:28:18'),
	(398, 1, 'en', 'Backend', 'Core', 'lbl', 'Domains', 'domains', '2017-08-31 14:28:18'),
	(399, 1, 'en', 'Backend', 'Core', 'lbl', 'Done', 'done', '2017-08-31 14:28:18'),
	(400, 1, 'en', 'Backend', 'Core', 'lbl', 'Draft', 'draft', '2017-08-31 14:28:18'),
	(401, 1, 'en', 'Backend', 'Core', 'lbl', 'Drafts', 'drafts', '2017-08-31 14:28:18'),
	(402, 1, 'en', 'Backend', 'Core', 'lbl', 'Edit', 'edit', '2017-08-31 14:28:18'),
	(403, 1, 'en', 'Backend', 'Core', 'lbl', 'EditedOn', 'edited on', '2017-08-31 14:28:18'),
	(404, 1, 'en', 'Backend', 'Core', 'lbl', 'Editor', 'editor', '2017-08-31 14:28:18'),
	(405, 1, 'en', 'Backend', 'Core', 'lbl', 'EditProfile', 'edit profile', '2017-08-31 14:28:18'),
	(406, 1, 'en', 'Backend', 'Core', 'lbl', 'EditTemplate', 'edit template', '2017-08-31 14:28:18'),
	(407, 1, 'en', 'Backend', 'Core', 'lbl', 'Email', 'e-mail', '2017-08-31 14:28:18'),
	(408, 1, 'en', 'Backend', 'Core', 'lbl', 'EN', 'English', '2017-08-31 14:28:18'),
	(409, 1, 'en', 'Backend', 'Core', 'lbl', 'EnableModeration', 'enable moderation', '2017-08-31 14:28:18'),
	(410, 1, 'en', 'Backend', 'Core', 'lbl', 'EndDate', 'end date', '2017-08-31 14:28:18'),
	(411, 1, 'en', 'Backend', 'Core', 'lbl', 'Error', 'error', '2017-08-31 14:28:18'),
	(412, 1, 'en', 'Backend', 'Core', 'lbl', 'ES', 'Spanish', '2017-08-31 14:28:18'),
	(413, 1, 'en', 'Backend', 'Core', 'lbl', 'Example', 'example', '2017-08-31 14:28:18'),
	(414, 1, 'en', 'Backend', 'Core', 'lbl', 'Execute', 'execute', '2017-08-31 14:28:18'),
	(415, 1, 'en', 'Backend', 'Core', 'lbl', 'ExitPages', 'exit pages', '2017-08-31 14:28:18'),
	(416, 1, 'en', 'Backend', 'Core', 'lbl', 'Export', 'export', '2017-08-31 14:28:18'),
	(417, 1, 'en', 'Backend', 'Core', 'lbl', 'Extensions', 'extensions', '2017-08-31 14:28:18'),
	(418, 1, 'en', 'Backend', 'Core', 'lbl', 'ExtraMetaTags', 'extra metatags', '2017-08-31 14:28:18'),
	(419, 1, 'en', 'Backend', 'Core', 'lbl', 'Faq', 'FAQ', '2017-08-31 14:28:18'),
	(420, 1, 'en', 'Backend', 'Core', 'lbl', 'Feedback', 'feedback', '2017-08-31 14:28:18'),
	(421, 1, 'en', 'Backend', 'Core', 'lbl', 'File', 'file', '2017-08-31 14:28:18'),
	(422, 1, 'en', 'Backend', 'Core', 'lbl', 'Filename', 'filename', '2017-08-31 14:28:18'),
	(423, 1, 'en', 'Backend', 'Core', 'lbl', 'FilterCommentsForSpam', 'filter comments for spam', '2017-08-31 14:28:18'),
	(424, 1, 'en', 'Backend', 'Core', 'lbl', 'Follow', 'follow', '2017-08-31 14:28:18'),
	(425, 1, 'en', 'Backend', 'Core', 'lbl', 'For', 'for', '2017-08-31 14:28:18'),
	(426, 1, 'en', 'Backend', 'Core', 'lbl', 'ForgotPassword', 'forgot password', '2017-08-31 14:28:18'),
	(427, 1, 'en', 'Backend', 'Core', 'lbl', 'FormBuilder', 'formbuilder', '2017-08-31 14:28:18'),
	(428, 1, 'en', 'Backend', 'Core', 'lbl', 'FR', 'French', '2017-08-31 14:28:18'),
	(429, 1, 'en', 'Backend', 'Core', 'lbl', 'From', 'from', '2017-08-31 14:28:18'),
	(430, 1, 'en', 'Backend', 'Core', 'lbl', 'Frontend', 'frontend', '2017-08-31 14:28:18'),
	(431, 1, 'en', 'Backend', 'Core', 'lbl', 'General', 'general', '2017-08-31 14:28:18'),
	(432, 1, 'en', 'Backend', 'Core', 'lbl', 'GeneralSettings', 'general settings', '2017-08-31 14:28:18'),
	(433, 1, 'en', 'Backend', 'Core', 'lbl', 'Generate', 'generate', '2017-08-31 14:28:18'),
	(434, 1, 'en', 'Backend', 'Core', 'lbl', 'GoToPage', 'go to page', '2017-08-31 14:28:18'),
	(435, 1, 'en', 'Backend', 'Core', 'lbl', 'Group', 'group', '2017-08-31 14:28:18'),
	(436, 1, 'en', 'Backend', 'Core', 'lbl', 'GroupMap', 'general map: all locations', '2017-08-31 14:28:18'),
	(437, 1, 'en', 'Backend', 'Core', 'lbl', 'Groups', 'groups', '2017-08-31 14:28:18'),
	(438, 1, 'en', 'Backend', 'Core', 'lbl', 'Height', 'height', '2017-08-31 14:28:18'),
	(439, 1, 'en', 'Backend', 'Core', 'lbl', 'Hidden', 'hidden', '2017-08-31 14:28:18'),
	(440, 1, 'en', 'Backend', 'Core', 'lbl', 'Home', 'home', '2017-08-31 14:28:18'),
	(441, 1, 'en', 'Backend', 'Core', 'lbl', 'HU', 'Hungarian', '2017-08-31 14:28:18'),
	(442, 1, 'en', 'Backend', 'Core', 'lbl', 'Image', 'image', '2017-08-31 14:28:18'),
	(443, 1, 'en', 'Backend', 'Core', 'lbl', 'Images', 'images', '2017-08-31 14:28:18'),
	(444, 1, 'en', 'Backend', 'Core', 'lbl', 'Import', 'import', '2017-08-31 14:28:18'),
	(445, 1, 'en', 'Backend', 'Core', 'lbl', 'ImportNoun', 'import', '2017-08-31 14:28:18'),
	(446, 1, 'en', 'Backend', 'Core', 'lbl', 'In', 'in', '2017-08-31 14:28:18'),
	(447, 1, 'en', 'Backend', 'Core', 'lbl', 'Index', 'index', '2017-08-31 14:28:18'),
	(448, 1, 'en', 'Backend', 'Core', 'lbl', 'IndividualMap', 'widget: individual map', '2017-08-31 14:28:18'),
	(449, 1, 'en', 'Backend', 'Core', 'lbl', 'Interface', 'interface', '2017-08-31 14:28:18'),
	(450, 1, 'en', 'Backend', 'Core', 'lbl', 'InterfacePreferences', 'interface preferences', '2017-08-31 14:28:18'),
	(451, 1, 'en', 'Backend', 'Core', 'lbl', 'IP', 'IP', '2017-08-31 14:28:18'),
	(452, 1, 'en', 'Backend', 'Core', 'lbl', 'IT', 'Italian', '2017-08-31 14:28:18'),
	(453, 1, 'en', 'Backend', 'Core', 'lbl', 'ItemsPerPage', 'items per page', '2017-08-31 14:28:18'),
	(454, 1, 'en', 'Backend', 'Core', 'lbl', 'JA', 'Japanese', '2017-08-31 14:28:18'),
	(455, 1, 'en', 'Backend', 'Core', 'lbl', 'Keyword', 'keyword', '2017-08-31 14:28:18'),
	(456, 1, 'en', 'Backend', 'Core', 'lbl', 'Keywords', 'keywords', '2017-08-31 14:28:18'),
	(457, 1, 'en', 'Backend', 'Core', 'lbl', 'Label', 'label', '2017-08-31 14:28:18'),
	(458, 1, 'en', 'Backend', 'Core', 'lbl', 'LandingPages', 'landing pages', '2017-08-31 14:28:18'),
	(459, 1, 'en', 'Backend', 'Core', 'lbl', 'Language', 'language', '2017-08-31 14:28:18'),
	(460, 1, 'en', 'Backend', 'Core', 'lbl', 'Languages', 'languages', '2017-08-31 14:28:18'),
	(461, 1, 'en', 'Backend', 'Core', 'lbl', 'LastEdited', 'last edited', '2017-08-31 14:28:18'),
	(462, 1, 'en', 'Backend', 'Core', 'lbl', 'LastEditedOn', 'last edited on', '2017-08-31 14:28:18'),
	(463, 1, 'en', 'Backend', 'Core', 'lbl', 'LastFailedLoginAttempt', 'last failed login attempt', '2017-08-31 14:28:18'),
	(464, 1, 'en', 'Backend', 'Core', 'lbl', 'LastLogin', 'last login', '2017-08-31 14:28:18'),
	(465, 1, 'en', 'Backend', 'Core', 'lbl', 'LastPasswordChange', 'last password change', '2017-08-31 14:28:18'),
	(466, 1, 'en', 'Backend', 'Core', 'lbl', 'LastSaved', 'last saved', '2017-08-31 14:28:18'),
	(467, 1, 'en', 'Backend', 'Core', 'lbl', 'LatestComments', 'latest comments', '2017-08-31 14:28:18'),
	(468, 1, 'en', 'Backend', 'Core', 'lbl', 'Layout', 'layout', '2017-08-31 14:28:18'),
	(469, 1, 'en', 'Backend', 'Core', 'lbl', 'LineEnding', 'line ending', '2017-08-31 14:28:18'),
	(470, 1, 'en', 'Backend', 'Core', 'lbl', 'Loading', 'loading', '2017-08-31 14:28:18'),
	(471, 1, 'en', 'Backend', 'Core', 'lbl', 'Locale', 'locale', '2017-08-31 14:28:18'),
	(472, 1, 'en', 'Backend', 'Core', 'lbl', 'Location', 'location', '2017-08-31 14:28:18'),
	(473, 1, 'en', 'Backend', 'Core', 'lbl', 'Login', 'login', '2017-08-31 14:28:18'),
	(474, 1, 'en', 'Backend', 'Core', 'lbl', 'LoginBox', 'login box', '2017-08-31 14:28:18'),
	(475, 1, 'en', 'Backend', 'Core', 'lbl', 'LoginDetails', 'login details', '2017-08-31 14:28:18'),
	(476, 1, 'en', 'Backend', 'Core', 'lbl', 'Logout', 'logout', '2017-08-31 14:28:18'),
	(477, 1, 'en', 'Backend', 'Core', 'lbl', 'LongDateFormat', 'long date format', '2017-08-31 14:28:18'),
	(478, 1, 'en', 'Backend', 'Core', 'lbl', 'LT', 'Lithuanian', '2017-08-31 14:28:18'),
	(479, 1, 'en', 'Backend', 'Core', 'lbl', 'Mailmotor', 'mailmotor', '2017-08-31 14:28:18'),
	(480, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorClicks', 'clicks', '2017-08-31 14:28:18'),
	(481, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorGroups', 'groups', '2017-08-31 14:28:18'),
	(482, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorLatestMailing', 'last sent mailing', '2017-08-31 14:28:18'),
	(483, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorOpened', 'opened', '2017-08-31 14:28:18'),
	(484, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorSendDate', 'send date', '2017-08-31 14:28:18'),
	(485, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorSent', 'sent', '2017-08-31 14:28:18'),
	(486, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorStatistics', 'statistics', '2017-08-31 14:28:18'),
	(487, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorSubscriptions', 'subscriptions', '2017-08-31 14:28:18'),
	(488, 1, 'en', 'Backend', 'Core', 'lbl', 'MailmotorUnsubscriptions', 'unsubscriptions', '2017-08-31 14:28:18'),
	(489, 1, 'en', 'Backend', 'Core', 'lbl', 'MainContent', 'main content', '2017-08-31 14:28:18'),
	(490, 1, 'en', 'Backend', 'Core', 'lbl', 'MarkAsSpam', 'mark as spam', '2017-08-31 14:28:18'),
	(491, 1, 'en', 'Backend', 'Core', 'lbl', 'Marketing', 'marketing', '2017-08-31 14:28:18'),
	(492, 1, 'en', 'Backend', 'Core', 'lbl', 'Meta', 'meta', '2017-08-31 14:28:18'),
	(493, 1, 'en', 'Backend', 'Core', 'lbl', 'MetaData', 'metadata', '2017-08-31 14:28:18'),
	(494, 1, 'en', 'Backend', 'Core', 'lbl', 'MetaInformation', 'meta information', '2017-08-31 14:28:18'),
	(495, 1, 'en', 'Backend', 'Core', 'lbl', 'MetaNavigation', 'meta navigation', '2017-08-31 14:28:18'),
	(496, 1, 'en', 'Backend', 'Core', 'lbl', 'Moderate', 'moderate', '2017-08-31 14:28:18'),
	(497, 1, 'en', 'Backend', 'Core', 'lbl', 'Moderation', 'moderation', '2017-08-31 14:28:18'),
	(498, 1, 'en', 'Backend', 'Core', 'lbl', 'Module', 'module', '2017-08-31 14:28:18'),
	(499, 1, 'en', 'Backend', 'Core', 'lbl', 'Modules', 'modules', '2017-08-31 14:28:18'),
	(500, 1, 'en', 'Backend', 'Core', 'lbl', 'ModuleSettings', 'module settings', '2017-08-31 14:28:18'),
	(501, 1, 'en', 'Backend', 'Core', 'lbl', 'More', 'more', '2017-08-31 14:28:18'),
	(502, 1, 'en', 'Backend', 'Core', 'lbl', 'MostReadQuestions', 'most read questions', '2017-08-31 14:28:18'),
	(503, 1, 'en', 'Backend', 'Core', 'lbl', 'Move', 'move', '2017-08-31 14:28:18'),
	(504, 1, 'en', 'Backend', 'Core', 'lbl', 'MoveToModeration', 'move to moderation', '2017-08-31 14:28:18'),
	(505, 1, 'en', 'Backend', 'Core', 'lbl', 'MoveToPublished', 'move to published', '2017-08-31 14:28:18'),
	(506, 1, 'en', 'Backend', 'Core', 'lbl', 'MoveToSpam', 'move to spam', '2017-08-31 14:28:18'),
	(507, 1, 'en', 'Backend', 'Core', 'lbl', 'Name', 'name', '2017-08-31 14:28:18'),
	(508, 1, 'en', 'Backend', 'Core', 'lbl', 'Navigation', 'navigation', '2017-08-31 14:28:18'),
	(509, 1, 'en', 'Backend', 'Core', 'lbl', 'NavigationTitle', 'navigation title', '2017-08-31 14:28:18'),
	(510, 1, 'en', 'Backend', 'Core', 'lbl', 'Never', 'never', '2017-08-31 14:28:18'),
	(511, 1, 'en', 'Backend', 'Core', 'lbl', 'NewPassword', 'new password', '2017-08-31 14:28:18'),
	(512, 1, 'en', 'Backend', 'Core', 'lbl', 'News', 'news', '2017-08-31 14:28:18'),
	(513, 1, 'en', 'Backend', 'Core', 'lbl', 'Newsletters', 'mailings', '2017-08-31 14:28:18'),
	(514, 1, 'en', 'Backend', 'Core', 'lbl', 'Next', 'next', '2017-08-31 14:28:18'),
	(515, 1, 'en', 'Backend', 'Core', 'lbl', 'NextPage', 'next page', '2017-08-31 14:28:18'),
	(516, 1, 'en', 'Backend', 'Core', 'lbl', 'Nickname', 'publication name', '2017-08-31 14:28:18'),
	(517, 1, 'en', 'Backend', 'Core', 'lbl', 'NL', 'Dutch', '2017-08-31 14:28:18'),
	(518, 1, 'en', 'Backend', 'Core', 'lbl', 'UK', 'Ukrainian', '2017-08-31 14:28:18'),
	(519, 1, 'en', 'Backend', 'Core', 'lbl', 'SV', 'Swedish', '2017-08-31 14:28:18'),
	(520, 1, 'en', 'Backend', 'Core', 'lbl', 'EL', 'Greek', '2017-08-31 14:28:18'),
	(521, 1, 'en', 'Backend', 'Core', 'lbl', 'ZH', 'Chinese', '2017-08-31 14:28:18'),
	(522, 1, 'en', 'Backend', 'Core', 'lbl', 'None', 'none', '2017-08-31 14:28:18'),
	(523, 1, 'en', 'Backend', 'Core', 'lbl', 'NoPreviousLogin', 'no previous login', '2017-08-31 14:28:18'),
	(524, 1, 'en', 'Backend', 'Core', 'lbl', 'NoTheme', 'no theme', '2017-08-31 14:28:18'),
	(525, 1, 'en', 'Backend', 'Core', 'lbl', 'Notifications', 'notifications', '2017-08-31 14:28:18'),
	(526, 1, 'en', 'Backend', 'Core', 'lbl', 'Number', 'number', '2017-08-31 14:28:18'),
	(527, 1, 'en', 'Backend', 'Core', 'lbl', 'NumberFormat', 'number format', '2017-08-31 14:28:18'),
	(528, 1, 'en', 'Backend', 'Core', 'lbl', 'NumberOfPositions', 'number of positions', '2017-08-31 14:28:18'),
	(529, 1, 'en', 'Backend', 'Core', 'lbl', 'Numbers', 'numbers', '2017-08-31 14:28:18'),
	(530, 1, 'en', 'Backend', 'Core', 'lbl', 'OK', 'OK', '2017-08-31 14:28:18'),
	(531, 1, 'en', 'Backend', 'Core', 'lbl', 'Or', 'or', '2017-08-31 14:28:18'),
	(532, 1, 'en', 'Backend', 'Core', 'lbl', 'Overview', 'overview', '2017-08-31 14:28:18'),
	(533, 1, 'en', 'Backend', 'Core', 'lbl', 'Page', 'page', '2017-08-31 14:28:18'),
	(534, 1, 'en', 'Backend', 'Core', 'lbl', 'Pages', 'pages', '2017-08-31 14:28:18'),
	(535, 1, 'en', 'Backend', 'Core', 'lbl', 'PageTitle', 'pagetitle', '2017-08-31 14:28:18'),
	(536, 1, 'en', 'Backend', 'Core', 'lbl', 'Pageviews', 'pageviews', '2017-08-31 14:28:18'),
	(537, 1, 'en', 'Backend', 'Core', 'lbl', 'Pagination', 'pagination', '2017-08-31 14:28:18'),
	(538, 1, 'en', 'Backend', 'Core', 'lbl', 'Password', 'password', '2017-08-31 14:28:18'),
	(539, 1, 'en', 'Backend', 'Core', 'lbl', 'PasswordStrength', 'password strength', '2017-08-31 14:28:18'),
	(540, 1, 'en', 'Backend', 'Core', 'lbl', 'PerDay', 'per day', '2017-08-31 14:28:18'),
	(541, 1, 'en', 'Backend', 'Core', 'lbl', 'Permissions', 'permissions', '2017-08-31 14:28:18'),
	(542, 1, 'en', 'Backend', 'Core', 'lbl', 'Person', 'person', '2017-08-31 14:28:18'),
	(543, 1, 'en', 'Backend', 'Core', 'lbl', 'PersonalInformation', 'personal information', '2017-08-31 14:28:18'),
	(544, 1, 'en', 'Backend', 'Core', 'lbl', 'Persons', 'people', '2017-08-31 14:28:18'),
	(545, 1, 'en', 'Backend', 'Core', 'lbl', 'PerVisit', 'per visit', '2017-08-31 14:28:18'),
	(546, 1, 'en', 'Backend', 'Core', 'lbl', 'PingBlogServices', 'ping blogservices', '2017-08-31 14:28:18'),
	(547, 1, 'en', 'Backend', 'Core', 'lbl', 'PL', 'Polish', '2017-08-31 14:28:18'),
	(548, 1, 'en', 'Backend', 'Core', 'lbl', 'Port', 'port', '2017-08-31 14:28:18'),
	(549, 1, 'en', 'Backend', 'Core', 'lbl', 'Position', 'position', '2017-08-31 14:28:18'),
	(550, 1, 'en', 'Backend', 'Core', 'lbl', 'Positions', 'positions', '2017-08-31 14:28:18'),
	(551, 1, 'en', 'Backend', 'Core', 'lbl', 'Preview', 'preview', '2017-08-31 14:28:18'),
	(552, 1, 'en', 'Backend', 'Core', 'lbl', 'Previous', 'previous', '2017-08-31 14:28:18'),
	(553, 1, 'en', 'Backend', 'Core', 'lbl', 'PreviousPage', 'previous page', '2017-08-31 14:28:18'),
	(554, 1, 'en', 'Backend', 'Core', 'lbl', 'PreviousVersions', 'previous versions', '2017-08-31 14:28:18'),
	(555, 1, 'en', 'Backend', 'Core', 'lbl', 'Price', 'price', '2017-08-31 14:28:18'),
	(556, 1, 'en', 'Backend', 'Core', 'lbl', 'Profile', 'profile', '2017-08-31 14:28:18'),
	(557, 1, 'en', 'Backend', 'Core', 'lbl', 'Profiles', 'profiles', '2017-08-31 14:28:18'),
	(558, 1, 'en', 'Backend', 'Core', 'lbl', 'Publish', 'publish', '2017-08-31 14:28:18'),
	(559, 1, 'en', 'Backend', 'Core', 'lbl', 'Published', 'published', '2017-08-31 14:28:18'),
	(560, 1, 'en', 'Backend', 'Core', 'lbl', 'PublishedArticles', 'published articles', '2017-08-31 14:28:18'),
	(561, 1, 'en', 'Backend', 'Core', 'lbl', 'PublishedOn', 'published on', '2017-08-31 14:28:18'),
	(562, 1, 'en', 'Backend', 'Core', 'lbl', 'PublishOn', 'publish on', '2017-08-31 14:28:18'),
	(563, 1, 'en', 'Backend', 'Core', 'lbl', 'QuantityNo', 'no', '2017-08-31 14:28:18'),
	(564, 1, 'en', 'Backend', 'Core', 'lbl', 'Questions', 'questions', '2017-08-31 14:28:18'),
	(565, 1, 'en', 'Backend', 'Core', 'lbl', 'RecentArticlesFull', 'recent articles (full)', '2017-08-31 14:28:18'),
	(566, 1, 'en', 'Backend', 'Core', 'lbl', 'RecentArticlesList', 'recent articles (list)', '2017-08-31 14:28:18'),
	(567, 1, 'en', 'Backend', 'Core', 'lbl', 'RecentComments', 'recent comments', '2017-08-31 14:28:18'),
	(568, 1, 'en', 'Backend', 'Core', 'lbl', 'RecentlyEdited', 'recently edited', '2017-08-31 14:28:18'),
	(569, 1, 'en', 'Backend', 'Core', 'lbl', 'RecentVisits', 'recent visits', '2017-08-31 14:28:18'),
	(570, 1, 'en', 'Backend', 'Core', 'lbl', 'Visits', 'visits', '2017-08-31 14:28:18'),
	(571, 1, 'en', 'Backend', 'Core', 'lbl', 'ReferenceCode', 'reference code', '2017-08-31 14:28:18'),
	(572, 1, 'en', 'Backend', 'Core', 'lbl', 'Referrer', 'referrer', '2017-08-31 14:28:18'),
	(573, 1, 'en', 'Backend', 'Core', 'lbl', 'Register', 'register', '2017-08-31 14:28:18'),
	(574, 1, 'en', 'Backend', 'Core', 'lbl', 'Related', 'related', '2017-08-31 14:28:18'),
	(575, 1, 'en', 'Backend', 'Core', 'lbl', 'RepeatPassword', 'repeat password', '2017-08-31 14:28:18'),
	(576, 1, 'en', 'Backend', 'Core', 'lbl', 'ReplyTo', 'reply-to', '2017-08-31 14:28:18'),
	(577, 1, 'en', 'Backend', 'Core', 'lbl', 'RequiredField', 'required field', '2017-08-31 14:28:18'),
	(578, 1, 'en', 'Backend', 'Core', 'lbl', 'ResendActivation', 'resend activation e-mail', '2017-08-31 14:28:18'),
	(579, 1, 'en', 'Backend', 'Core', 'lbl', 'ResetAndSignIn', 'reset and sign in', '2017-08-31 14:28:18'),
	(580, 1, 'en', 'Backend', 'Core', 'lbl', 'ResetPassword', 'reset password', '2017-08-31 14:28:18'),
	(581, 1, 'en', 'Backend', 'Core', 'lbl', 'ResetYourPassword', 'reset your password', '2017-08-31 14:28:18'),
	(582, 1, 'en', 'Backend', 'Core', 'lbl', 'RO', 'Romanian', '2017-08-31 14:28:18'),
	(583, 1, 'en', 'Backend', 'Core', 'lbl', 'RSSFeed', 'RSS feed', '2017-08-31 14:28:18'),
	(584, 1, 'en', 'Backend', 'Core', 'lbl', 'RU', 'Russian', '2017-08-31 14:28:18'),
	(585, 1, 'en', 'Backend', 'Core', 'lbl', 'Save', 'save', '2017-08-31 14:28:18'),
	(586, 1, 'en', 'Backend', 'Core', 'lbl', 'SaveDraft', 'save draft', '2017-08-31 14:28:18'),
	(587, 1, 'en', 'Backend', 'Core', 'lbl', 'Scripts', 'scripts', '2017-08-31 14:28:18'),
	(588, 1, 'en', 'Backend', 'Core', 'lbl', 'Search', 'search', '2017-08-31 14:28:18'),
	(589, 1, 'en', 'Backend', 'Core', 'lbl', 'SearchAgain', 'search again', '2017-08-31 14:28:18'),
	(590, 1, 'en', 'Backend', 'Core', 'lbl', 'SearchForm', 'search form', '2017-08-31 14:28:18'),
	(591, 1, 'en', 'Backend', 'Core', 'lbl', 'Send', 'send', '2017-08-31 14:28:18'),
	(592, 1, 'en', 'Backend', 'Core', 'lbl', 'SendingEmails', 'sending e-mails', '2017-08-31 14:28:18'),
	(593, 1, 'en', 'Backend', 'Core', 'lbl', 'SentMailings', 'sent mailings', '2017-08-31 14:28:18'),
	(594, 1, 'en', 'Backend', 'Core', 'lbl', 'SentOn', 'sent on', '2017-08-31 14:28:18'),
	(595, 1, 'en', 'Backend', 'Core', 'lbl', 'SEO', 'SEO', '2017-08-31 14:28:18'),
	(596, 1, 'en', 'Backend', 'Core', 'lbl', 'Server', 'server', '2017-08-31 14:28:18'),
	(597, 1, 'en', 'Backend', 'Core', 'lbl', 'Settings', 'settings', '2017-08-31 14:28:18'),
	(598, 1, 'en', 'Backend', 'Core', 'lbl', 'ShortDateFormat', 'short date format', '2017-08-31 14:28:18'),
	(599, 1, 'en', 'Backend', 'Core', 'lbl', 'SignIn', 'log in', '2017-08-31 14:28:18'),
	(600, 1, 'en', 'Backend', 'Core', 'lbl', 'SignOut', 'sign out', '2017-08-31 14:28:18'),
	(601, 1, 'en', 'Backend', 'Core', 'lbl', 'Sitemap', 'sitemap', '2017-08-31 14:28:18'),
	(602, 1, 'en', 'Backend', 'Core', 'lbl', 'SMTP', 'SMTP', '2017-08-31 14:28:18'),
	(603, 1, 'en', 'Backend', 'Core', 'lbl', 'SortAscending', 'sort ascending', '2017-08-31 14:28:18'),
	(604, 1, 'en', 'Backend', 'Core', 'lbl', 'SortDescending', 'sort descending', '2017-08-31 14:28:18'),
	(605, 1, 'en', 'Backend', 'Core', 'lbl', 'SortedAscending', 'sorted ascending', '2017-08-31 14:28:18'),
	(606, 1, 'en', 'Backend', 'Core', 'lbl', 'SortedDescending', 'sorted descending', '2017-08-31 14:28:18'),
	(607, 1, 'en', 'Backend', 'Core', 'lbl', 'Source', 'source', '2017-08-31 14:28:18'),
	(608, 1, 'en', 'Backend', 'Core', 'lbl', 'Spam', 'spam', '2017-08-31 14:28:18'),
	(609, 1, 'en', 'Backend', 'Core', 'lbl', 'SpamFilter', 'spamfilter', '2017-08-31 14:28:18'),
	(610, 1, 'en', 'Backend', 'Core', 'lbl', 'SplitCharacter', 'split character', '2017-08-31 14:28:18'),
	(611, 1, 'en', 'Backend', 'Core', 'lbl', 'StartDate', 'start date', '2017-08-31 14:28:18'),
	(612, 1, 'en', 'Backend', 'Core', 'lbl', 'Statistics', 'statistics', '2017-08-31 14:28:18'),
	(613, 1, 'en', 'Backend', 'Core', 'lbl', 'Status', 'status', '2017-08-31 14:28:18'),
	(614, 1, 'en', 'Backend', 'Core', 'lbl', 'Street', 'street', '2017-08-31 14:28:18'),
	(615, 1, 'en', 'Backend', 'Core', 'lbl', 'Strong', 'strong', '2017-08-31 14:28:18'),
	(616, 1, 'en', 'Backend', 'Core', 'lbl', 'Subpages', 'subpages', '2017-08-31 14:28:18'),
	(617, 1, 'en', 'Backend', 'Core', 'lbl', 'SubscribeForm', 'subscribe form', '2017-08-31 14:28:18'),
	(618, 1, 'en', 'Backend', 'Core', 'lbl', 'Subscriptions', 'subscriptions', '2017-08-31 14:28:18'),
	(619, 1, 'en', 'Backend', 'Core', 'lbl', 'Summary', 'summary', '2017-08-31 14:28:18'),
	(620, 1, 'en', 'Backend', 'Core', 'lbl', 'Surname', 'surname', '2017-08-31 14:28:18'),
	(621, 1, 'en', 'Backend', 'Core', 'lbl', 'Synonym', 'synonym', '2017-08-31 14:28:18'),
	(622, 1, 'en', 'Backend', 'Core', 'lbl', 'Synonyms', 'synonyms', '2017-08-31 14:28:18'),
	(623, 1, 'en', 'Backend', 'Core', 'lbl', 'TagCloud', 'tagcloud', '2017-08-31 14:28:18'),
	(624, 1, 'en', 'Backend', 'Core', 'lbl', 'Tags', 'tags', '2017-08-31 14:28:18'),
	(625, 1, 'en', 'Backend', 'Core', 'lbl', 'Template', 'template', '2017-08-31 14:28:18'),
	(626, 1, 'en', 'Backend', 'Core', 'lbl', 'Templates', 'templates', '2017-08-31 14:28:18'),
	(627, 1, 'en', 'Backend', 'Core', 'lbl', 'Term', 'term', '2017-08-31 14:28:18'),
	(628, 1, 'en', 'Backend', 'Core', 'lbl', 'Text', 'text', '2017-08-31 14:28:18'),
	(629, 1, 'en', 'Backend', 'Core', 'lbl', 'Themes', 'themes', '2017-08-31 14:28:18'),
	(630, 1, 'en', 'Backend', 'Core', 'lbl', 'ThemesSelection', 'theme selection', '2017-08-31 14:28:18'),
	(631, 1, 'en', 'Backend', 'Core', 'lbl', 'Till', 'till', '2017-08-31 14:28:18'),
	(632, 1, 'en', 'Backend', 'Core', 'lbl', 'TimeFormat', 'time format', '2017-08-31 14:28:18'),
	(633, 1, 'en', 'Backend', 'Core', 'lbl', 'Timezone', 'timezone', '2017-08-31 14:28:18'),
	(634, 1, 'en', 'Backend', 'Core', 'lbl', 'Title', 'title', '2017-08-31 14:28:18'),
	(635, 1, 'en', 'Backend', 'Core', 'lbl', 'Titles', 'titles', '2017-08-31 14:28:18'),
	(636, 1, 'en', 'Backend', 'Core', 'lbl', 'To', 'to', '2017-08-31 14:28:18'),
	(637, 1, 'en', 'Backend', 'Core', 'lbl', 'Today', 'today', '2017-08-31 14:28:18'),
	(638, 1, 'en', 'Backend', 'Core', 'lbl', 'ToStep', 'to step', '2017-08-31 14:28:18'),
	(639, 1, 'en', 'Backend', 'Core', 'lbl', 'TR', 'Turkish', '2017-08-31 14:28:18'),
	(640, 1, 'en', 'Backend', 'Core', 'lbl', 'TrafficSources', 'traffic sources', '2017-08-31 14:28:18'),
	(641, 1, 'en', 'Backend', 'Core', 'lbl', 'Translation', 'translation', '2017-08-31 14:28:18'),
	(642, 1, 'en', 'Backend', 'Core', 'lbl', 'Translations', 'translations', '2017-08-31 14:28:18'),
	(643, 1, 'en', 'Backend', 'Core', 'lbl', 'Type', 'type', '2017-08-31 14:28:18'),
	(644, 1, 'en', 'Backend', 'Core', 'lbl', 'UnsubscribeForm', 'unsubscribe form', '2017-08-31 14:28:18'),
	(645, 1, 'en', 'Backend', 'Core', 'lbl', 'Unsubscriptions', 'unsubscriptions', '2017-08-31 14:28:18'),
	(646, 1, 'en', 'Backend', 'Core', 'lbl', 'UpdateFilter', 'update filter', '2017-08-31 14:28:18'),
	(647, 1, 'en', 'Backend', 'Core', 'lbl', 'URL', 'URL', '2017-08-31 14:28:18'),
	(648, 1, 'en', 'Backend', 'Core', 'lbl', 'UsedIn', 'used in', '2017-08-31 14:28:18'),
	(649, 1, 'en', 'Backend', 'Core', 'lbl', 'Userguide', 'userguide', '2017-08-31 14:28:18'),
	(650, 1, 'en', 'Backend', 'Core', 'lbl', 'Username', 'username', '2017-08-31 14:28:18'),
	(651, 1, 'en', 'Backend', 'Core', 'lbl', 'Users', 'users', '2017-08-31 14:28:18'),
	(652, 1, 'en', 'Backend', 'Core', 'lbl', 'UseThisDraft', 'use this draft', '2017-08-31 14:28:18'),
	(653, 1, 'en', 'Backend', 'Core', 'lbl', 'UseThisVersion', 'use this version', '2017-08-31 14:28:18'),
	(654, 1, 'en', 'Backend', 'Core', 'lbl', 'Value', 'value', '2017-08-31 14:28:18'),
	(655, 1, 'en', 'Backend', 'Core', 'lbl', 'Versions', 'versions', '2017-08-31 14:28:18'),
	(656, 1, 'en', 'Backend', 'Core', 'lbl', 'View', 'view', '2017-08-31 14:28:18'),
	(657, 1, 'en', 'Backend', 'Core', 'lbl', 'ViewReport', 'view report', '2017-08-31 14:28:18'),
	(658, 1, 'en', 'Backend', 'Core', 'lbl', 'VisibleOnSite', 'visible on site', '2017-08-31 14:28:18'),
	(659, 1, 'en', 'Backend', 'Core', 'lbl', 'Visitors', 'visitors', '2017-08-31 14:28:18'),
	(660, 1, 'en', 'Backend', 'Core', 'lbl', 'VisitWebsite', 'visit website', '2017-08-31 14:28:18'),
	(661, 1, 'en', 'Backend', 'Core', 'lbl', 'WaitingForModeration', 'waiting for moderation', '2017-08-31 14:28:18'),
	(662, 1, 'en', 'Backend', 'Core', 'lbl', 'Weak', 'weak', '2017-08-31 14:28:18'),
	(663, 1, 'en', 'Backend', 'Core', 'lbl', 'WebmasterEmail', 'e-mail webmaster', '2017-08-31 14:28:18'),
	(664, 1, 'en', 'Backend', 'Core', 'lbl', 'Website', 'website', '2017-08-31 14:28:18'),
	(665, 1, 'en', 'Backend', 'Core', 'lbl', 'WebsiteTitle', 'website title', '2017-08-31 14:28:18'),
	(666, 1, 'en', 'Backend', 'Core', 'lbl', 'Weight', 'weight', '2017-08-31 14:28:18'),
	(667, 1, 'en', 'Backend', 'Core', 'lbl', 'WhichModule', 'which module', '2017-08-31 14:28:18'),
	(668, 1, 'en', 'Backend', 'Core', 'lbl', 'WhichWidget', 'which widget', '2017-08-31 14:28:18'),
	(669, 1, 'en', 'Backend', 'Core', 'lbl', 'Widget', 'widget', '2017-08-31 14:28:18'),
	(670, 1, 'en', 'Backend', 'Core', 'lbl', 'Widgets', 'widgets', '2017-08-31 14:28:18'),
	(671, 1, 'en', 'Backend', 'Core', 'lbl', 'Width', 'width', '2017-08-31 14:28:18'),
	(672, 1, 'en', 'Backend', 'Core', 'lbl', 'WithSelected', 'with selected', '2017-08-31 14:28:18'),
	(673, 1, 'en', 'Backend', 'Core', 'lbl', 'Zip', 'zip code', '2017-08-31 14:28:18'),
	(674, 1, 'en', 'Backend', 'Core', 'msg', 'ACT', 'action', '2017-08-31 14:28:18'),
	(675, 1, 'en', 'Backend', 'Core', 'msg', 'Added', 'The item was added.', '2017-08-31 14:28:18'),
	(676, 1, 'en', 'Backend', 'Core', 'msg', 'AddedCategory', 'The category \"%1$s\" was added.', '2017-08-31 14:28:18'),
	(677, 1, 'en', 'Backend', 'Core', 'msg', 'AllAddresses', 'All addresses sorted by subscription date.', '2017-08-31 14:28:18'),
	(678, 1, 'en', 'Backend', 'Core', 'msg', 'BG', 'Bulgarian', '2017-08-31 14:28:18'),
	(679, 1, 'en', 'Backend', 'Core', 'msg', 'ChangedOrderSuccessfully', 'Changed order successfully.', '2017-08-31 14:28:18'),
	(680, 1, 'en', 'Backend', 'Core', 'msg', 'ClickToEdit', 'Click to edit', '2017-08-31 14:28:18'),
	(681, 1, 'en', 'Backend', 'Core', 'msg', 'CN', 'Chinese', '2017-08-31 14:28:18'),
	(682, 1, 'en', 'Backend', 'Core', 'msg', 'CommentDeleted', 'The comment was deleted.', '2017-08-31 14:28:18'),
	(683, 1, 'en', 'Backend', 'Core', 'msg', 'CommentMovedModeration', 'The comment was moved to moderation.', '2017-08-31 14:28:18'),
	(684, 1, 'en', 'Backend', 'Core', 'msg', 'CommentMovedPublished', 'The comment was published.', '2017-08-31 14:28:18'),
	(685, 1, 'en', 'Backend', 'Core', 'msg', 'CommentMovedSpam', 'The comment was marked as spam.', '2017-08-31 14:28:18'),
	(686, 1, 'en', 'Backend', 'Core', 'msg', 'CommentsDeleted', 'The comments were deleted.', '2017-08-31 14:28:18'),
	(687, 1, 'en', 'Backend', 'Core', 'msg', 'CommentsMovedModeration', 'The comments were moved to moderation.', '2017-08-31 14:28:18'),
	(688, 1, 'en', 'Backend', 'Core', 'msg', 'CommentsMovedPublished', 'The comments were published.', '2017-08-31 14:28:18'),
	(689, 1, 'en', 'Backend', 'Core', 'msg', 'CommentsMovedSpam', 'The comments were marked as spam.', '2017-08-31 14:28:18'),
	(690, 1, 'en', 'Backend', 'Core', 'msg', 'CommentsToModerate', '%1$s comment(s) to moderate.', '2017-08-31 14:28:18'),
	(691, 1, 'en', 'Backend', 'Core', 'msg', 'ConfigurationError', 'Some settings aren\'t configured yet:', '2017-08-31 14:28:18'),
	(692, 1, 'en', 'Backend', 'Core', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the item \"%1$s\"?', '2017-08-31 14:28:18'),
	(693, 1, 'en', 'Backend', 'Core', 'msg', 'ConfirmDeleteCategory', 'Are you sure you want to delete the category \"%1$s\"?', '2017-08-31 14:28:18'),
	(694, 1, 'en', 'Backend', 'Core', 'msg', 'ConfirmMassDelete', 'Are your sure you want to delete this/these item(s)?', '2017-08-31 14:28:18'),
	(695, 1, 'en', 'Backend', 'Core', 'msg', 'ConfirmMassSpam', 'Are your sure you want to mark this/these item(s) as spam?', '2017-08-31 14:28:18'),
	(696, 1, 'en', 'Backend', 'Core', 'msg', 'DE', 'German', '2017-08-31 14:28:18'),
	(697, 1, 'en', 'Backend', 'Core', 'msg', 'Deleted', 'The item was deleted.', '2017-08-31 14:28:18'),
	(698, 1, 'en', 'Backend', 'Core', 'msg', 'DeletedCategory', 'The category \"%1$s\" was deleted.', '2017-08-31 14:28:18'),
	(699, 1, 'en', 'Backend', 'Core', 'msg', 'EditCategory', 'edit category \"%1$s\"', '2017-08-31 14:28:18'),
	(700, 1, 'en', 'Backend', 'Core', 'msg', 'EditComment', 'edit comment', '2017-08-31 14:28:18'),
	(701, 1, 'en', 'Backend', 'Core', 'msg', 'Edited', 'The item was saved.', '2017-08-31 14:28:18'),
	(702, 1, 'en', 'Backend', 'Core', 'msg', 'EditedCategory', 'The category \"%1$s\" was saved.', '2017-08-31 14:28:18'),
	(703, 1, 'en', 'Backend', 'Core', 'msg', 'EditorImagesWithoutAlt', 'There are images without an alt-attribute.', '2017-08-31 14:28:18'),
	(704, 1, 'en', 'Backend', 'Core', 'msg', 'EditorInvalidLinks', 'There are invalid links.', '2017-08-31 14:28:18'),
	(705, 1, 'en', 'Backend', 'Core', 'msg', 'EditorSelectInternalPage', 'Select internal page', '2017-08-31 14:28:18'),
	(706, 1, 'en', 'Backend', 'Core', 'msg', 'EN', 'English', '2017-08-31 14:28:18'),
	(707, 1, 'en', 'Backend', 'Core', 'msg', 'ERR', 'error', '2017-08-31 14:28:18'),
	(708, 1, 'en', 'Backend', 'Core', 'msg', 'ES', 'Spanish', '2017-08-31 14:28:18'),
	(709, 1, 'en', 'Backend', 'Core', 'msg', 'ForgotPassword', 'Forgot password?', '2017-08-31 14:28:18'),
	(710, 1, 'en', 'Backend', 'Core', 'msg', 'FR', 'French', '2017-08-31 14:28:18'),
	(711, 1, 'en', 'Backend', 'Core', 'msg', 'HelpAvatar', 'A square picture produces the best results.', '2017-08-31 14:28:18'),
	(712, 1, 'en', 'Backend', 'Core', 'msg', 'HelpBlogger', 'Select the file that you exported from <a href=\"http://blogger.com\">Blogger</a>.', '2017-08-31 14:28:18'),
	(713, 1, 'en', 'Backend', 'Core', 'msg', 'HelpDrafts', 'Here you can see your draft. These are temporary versions.', '2017-08-31 14:28:18'),
	(714, 1, 'en', 'Backend', 'Core', 'msg', 'HelpEmailFrom', 'E-mails sent from the CMS use these settings.', '2017-08-31 14:28:18'),
	(715, 1, 'en', 'Backend', 'Core', 'msg', 'HelpEmailReplyTo', 'Answers on e-mails sent from the CMS will be sent to this e-mailaddress.', '2017-08-31 14:28:18'),
	(716, 1, 'en', 'Backend', 'Core', 'msg', 'HelpEmailTo', 'Notifications from the CMS are sent here.', '2017-08-31 14:28:18'),
	(717, 1, 'en', 'Backend', 'Core', 'msg', 'HelpFileFieldWithMaxFileSize', 'Only files with the extension %1$s are allowed, maximum file size: %2$s.', '2017-08-31 14:28:18'),
	(718, 1, 'en', 'Backend', 'Core', 'msg', 'HelpForgotPassword', 'Below enter your e-mail. You will receive an e-mail containing instructions on how to get a new password.', '2017-08-31 14:28:18'),
	(719, 1, 'en', 'Backend', 'Core', 'msg', 'HelpImageFieldWithMaxFileSize', 'Only jp(e)g, gif or png-files are allowed, maximum filesize: %1$s.', '2017-08-31 14:28:18'),
	(720, 1, 'en', 'Backend', 'Core', 'msg', 'HelpMaxFileSize', 'maximum filesize: %1$s', '2017-08-31 14:28:18'),
	(721, 1, 'en', 'Backend', 'Core', 'msg', 'HelpMetaCustom', 'These custom metatags will be placed in the <code><head></code> section of the page.', '2017-08-31 14:28:18'),
	(722, 1, 'en', 'Backend', 'Core', 'msg', 'HelpMetaDescription', 'Briefly summarize the content. This summary is shown in the results of search engines.', '2017-08-31 14:28:18'),
	(723, 1, 'en', 'Backend', 'Core', 'msg', 'HelpMetaKeywords', 'Choose a number of wellthought terms that describe the content. From an SEO point of view, these do not longer present an added value though.', '2017-08-31 14:28:18'),
	(724, 1, 'en', 'Backend', 'Core', 'msg', 'HelpMetaURL', 'Replace the automaticly generated URL by a custom one.', '2017-08-31 14:28:18'),
	(725, 1, 'en', 'Backend', 'Core', 'msg', 'HelpNickname', 'The name you want to be published as (e.g. as the author of an article).', '2017-08-31 14:28:18'),
	(726, 1, 'en', 'Backend', 'Core', 'msg', 'HelpPageTitle', 'The title in the browser window (<code>&lt;title&gt;</code>).', '2017-08-31 14:28:18'),
	(727, 1, 'en', 'Backend', 'Core', 'msg', 'HelpResetPassword', 'Provide your new password.', '2017-08-31 14:28:18'),
	(728, 1, 'en', 'Backend', 'Core', 'msg', 'HelpRevisions', 'The last saved versions are kept here. The current version will only be overwritten when you save your changes.', '2017-08-31 14:28:18'),
	(729, 1, 'en', 'Backend', 'Core', 'msg', 'HelpRSSDescription', 'Briefly describe what kind of content the RSS feed will contain.', '2017-08-31 14:28:18'),
	(730, 1, 'en', 'Backend', 'Core', 'msg', 'HelpRSSTitle', 'Provide a clear title for the RSS feed.', '2017-08-31 14:28:18'),
	(731, 1, 'en', 'Backend', 'Core', 'msg', 'HelpSMTPServer', 'Mailserver that should be used for sending e-mails.', '2017-08-31 14:28:18'),
	(732, 1, 'en', 'Backend', 'Core', 'msg', 'HU', 'Hungarian', '2017-08-31 14:28:18'),
	(733, 1, 'en', 'Backend', 'Core', 'msg', 'Imported', 'The data was imported.', '2017-08-31 14:28:18'),
	(734, 1, 'en', 'Backend', 'Core', 'msg', 'IT', 'Italian', '2017-08-31 14:28:18'),
	(735, 1, 'en', 'Backend', 'Core', 'msg', 'LBL', 'label', '2017-08-31 14:28:18'),
	(736, 1, 'en', 'Backend', 'Core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!', '2017-08-31 14:28:18'),
	(737, 1, 'en', 'Backend', 'Core', 'msg', 'LT', 'Lithuanian', '2017-08-31 14:28:18'),
	(738, 1, 'en', 'Backend', 'Core', 'msg', 'MSG', 'message', '2017-08-31 14:28:18'),
	(739, 1, 'en', 'Backend', 'Core', 'msg', 'NL', 'Dutch', '2017-08-31 14:28:18'),
	(740, 1, 'en', 'Backend', 'Core', 'msg', 'UK', 'Ukrainian', '2017-08-31 14:28:18'),
	(741, 1, 'en', 'Backend', 'Core', 'msg', 'SV', 'Swedish', '2017-08-31 14:28:18'),
	(742, 1, 'en', 'Backend', 'Core', 'msg', 'EL', 'Greek', '2017-08-31 14:28:18'),
	(743, 1, 'en', 'Backend', 'Core', 'msg', 'ZH', 'Chinese', '2017-08-31 14:28:18'),
	(744, 1, 'en', 'Backend', 'Core', 'msg', 'NoAkismetKey', 'If you want to enable the spam-protection you should <a href=\"%1$s\">configure</a> an Akismet-key.', '2017-08-31 14:28:18'),
	(745, 1, 'en', 'Backend', 'Core', 'msg', 'NoComments', 'There are no comments in this category yet.', '2017-08-31 14:28:18'),
	(746, 1, 'en', 'Backend', 'Core', 'msg', 'NoEmailaddresses', 'No email addresses.', '2017-08-31 14:28:18'),
	(747, 1, 'en', 'Backend', 'Core', 'msg', 'NoFeedback', 'There is no feedback yet.', '2017-08-31 14:28:18'),
	(748, 1, 'en', 'Backend', 'Core', 'msg', 'NoItems', 'There are no items yet.', '2017-08-31 14:28:18'),
	(749, 1, 'en', 'Backend', 'Core', 'msg', 'NoKeywords', 'There are no keywords yet.', '2017-08-31 14:28:18'),
	(750, 1, 'en', 'Backend', 'Core', 'msg', 'NoPublishedComments', 'There are no published comments.', '2017-08-31 14:28:18'),
	(751, 1, 'en', 'Backend', 'Core', 'msg', 'NoReferrers', 'There are no referrers yet.', '2017-08-31 14:28:18'),
	(752, 1, 'en', 'Backend', 'Core', 'msg', 'NoRevisions', 'There are no previous versions yet.', '2017-08-31 14:28:18'),
	(753, 1, 'en', 'Backend', 'Core', 'msg', 'NoSentMailings', 'No mailings have been sent yet.', '2017-08-31 14:28:18'),
	(754, 1, 'en', 'Backend', 'Core', 'msg', 'NoSubscriptions', 'No one subscribed to the mailinglist yet.', '2017-08-31 14:28:18'),
	(755, 1, 'en', 'Backend', 'Core', 'msg', 'NoTags', 'You didn\'t add tags yet.', '2017-08-31 14:28:18'),
	(756, 1, 'en', 'Backend', 'Core', 'msg', 'NoUnsubscriptions', 'No one unsubscribed from from the mailinglist yet.', '2017-08-31 14:28:18'),
	(757, 1, 'en', 'Backend', 'Core', 'msg', 'NoUsage', 'Not yet used.', '2017-08-31 14:28:18'),
	(758, 1, 'en', 'Backend', 'Core', 'msg', 'NowEditing', 'now editing', '2017-08-31 14:28:18'),
	(759, 1, 'en', 'Backend', 'Core', 'msg', 'PasswordResetSuccess', 'Your password has been changed.', '2017-08-31 14:28:18'),
	(760, 1, 'en', 'Backend', 'Core', 'msg', 'PL', 'Polish', '2017-08-31 14:28:18'),
	(761, 1, 'en', 'Backend', 'Core', 'msg', 'Redirecting', 'You are being redirected.', '2017-08-31 14:28:18'),
	(762, 1, 'en', 'Backend', 'Core', 'msg', 'ResetYourPasswordMailContent', 'Reset your password by clicking the link below. If you didn\'t ask for this, you can ignore this message.', '2017-08-31 14:28:18'),
	(763, 1, 'en', 'Backend', 'Core', 'msg', 'ResetYourPasswordMailSubject', 'Change your password', '2017-08-31 14:28:18'),
	(764, 1, 'en', 'Backend', 'Core', 'msg', 'RU', 'Russian', '2017-08-31 14:28:18'),
	(765, 1, 'en', 'Backend', 'Core', 'msg', 'Saved', 'The changes were saved.', '2017-08-31 14:28:18'),
	(766, 1, 'en', 'Backend', 'Core', 'msg', 'SavedAsDraft', '\"%1$s\" saved as draft.', '2017-08-31 14:28:18'),
	(767, 1, 'en', 'Backend', 'Core', 'msg', 'SequenceSaved', 'Sequence saved', '2017-08-31 14:28:18'),
	(768, 1, 'en', 'Backend', 'Core', 'msg', 'TR', 'Turkish', '2017-08-31 14:28:18'),
	(769, 1, 'en', 'Backend', 'Core', 'msg', 'UsingADraft', 'You\'re using a draft.', '2017-08-31 14:28:18'),
	(770, 1, 'en', 'Backend', 'Core', 'msg', 'UsingARevision', 'You\'re using an older version. Save to overwrite the current version.', '2017-08-31 14:28:18'),
	(771, 1, 'en', 'Backend', 'Core', 'msg', 'ValuesAreChanged', 'Changes will be lost.', '2017-08-31 14:28:18'),
	(772, 1, 'en', 'Backend', 'Core', 'err', 'ActionNotAllowed', 'You have insufficient rights for this action.', '2017-08-31 14:28:18'),
	(773, 1, 'en', 'Backend', 'Core', 'err', 'AddingCategoryFailed', 'Something went wrong.', '2017-08-31 14:28:18'),
	(774, 1, 'en', 'Backend', 'Core', 'err', 'AddTagBeforeSubmitting', 'Add the tag before submitting.', '2017-08-31 14:28:18'),
	(775, 1, 'en', 'Backend', 'Core', 'err', 'AkismetKey', 'Akismet API-key is not yet configured. <a href=\"%1$s\">Configure</a>', '2017-08-31 14:28:18'),
	(776, 1, 'en', 'Backend', 'Core', 'err', 'AlphaNumericCharactersOnly', 'Only alphanumeric characters are allowed.', '2017-08-31 14:28:18'),
	(777, 1, 'en', 'Backend', 'Core', 'err', 'AlterSequenceFailed', 'Alter sequence failed.', '2017-08-31 14:28:18'),
	(778, 1, 'en', 'Backend', 'Core', 'err', 'AuthorIsRequired', 'Please provide an author.', '2017-08-31 14:28:18'),
	(779, 1, 'en', 'Backend', 'Core', 'err', 'BrowserNotSupported', '<p>You\'re using an older browser that is not supported by Fork CMS. Use one of the following alternatives:</p><ul><li><a href=\"http://www.firefox.com/\">Firefox</a>: a very good browser with a lot of free extensions.</li><li><a href=\"http://www.apple.com/safari\">Safari</a>: one of the fastest and most advanced browsers. Good for Mac users.</li><li><a href=\"http://www.google.com/chrome\">Chrome</a>: Google\'s browser - also very fast.</li><li><a href=\"http://www.microsoft.com/windows/products/winfamily/ie/default.mspx\">Internet Explorer*</a>: update to the latest version of Internet Explorer.</li></ul>', '2017-08-31 14:28:18'),
	(780, 1, 'en', 'Backend', 'Core', 'err', 'CookiesNotEnabled', 'You need to enable cookies in order to use Fork CMS. Activate cookies and refresh this page.', '2017-08-31 14:28:18'),
	(781, 1, 'en', 'Backend', 'Core', 'err', 'DateIsInvalid', 'Invalid date.', '2017-08-31 14:28:18'),
	(782, 1, 'en', 'Backend', 'Core', 'err', 'DateRangeIsInvalid', 'Invalid date range.', '2017-08-31 14:28:18'),
	(783, 1, 'en', 'Backend', 'Core', 'err', 'DebugModeIsActive', 'Debug-mode is active.', '2017-08-31 14:28:18'),
	(784, 1, 'en', 'Backend', 'Core', 'msg', 'WarningDebugMode', 'Debug-mode is active.', '2017-08-31 14:28:18'),
	(785, 1, 'en', 'Backend', 'Core', 'err', 'EmailAlreadyExists', 'This e-mailaddress is in use.', '2017-08-31 14:28:18'),
	(786, 1, 'en', 'Backend', 'Core', 'err', 'EmailIsInvalid', 'Please provide a valid e-mailaddress.', '2017-08-31 14:28:18'),
	(787, 1, 'en', 'Backend', 'Core', 'err', 'EmailIsRequired', 'Please provide a valid e-mailaddress.', '2017-08-31 14:28:18'),
	(788, 1, 'en', 'Backend', 'Core', 'err', 'EmailIsUnknown', 'This e-mailaddress is not in our database.', '2017-08-31 14:28:18'),
	(789, 1, 'en', 'Backend', 'Core', 'err', 'EndDateIsInvalid', 'Invalid end date.', '2017-08-31 14:28:18'),
	(790, 1, 'en', 'Backend', 'Core', 'err', 'ErrorWhileSendingEmail', 'Error while sending email.', '2017-08-31 14:28:18'),
	(791, 1, 'en', 'Backend', 'Core', 'err', 'ExtensionNotAllowed', 'Invalid file type. (allowed: %1$s)', '2017-08-31 14:28:18'),
	(792, 1, 'en', 'Backend', 'Core', 'err', 'FieldIsRequired', 'This field is required.', '2017-08-31 14:28:18'),
	(793, 1, 'en', 'Backend', 'Core', 'err', 'FileTooBig', 'maximum filesize: %1$s', '2017-08-31 14:28:18'),
	(794, 1, 'en', 'Backend', 'Core', 'err', 'ForkAPIKeys', 'Fork API-keys are not configured.', '2017-08-31 14:28:18'),
	(795, 1, 'en', 'Backend', 'Core', 'err', 'FormError', 'Something went wrong', '2017-08-31 14:28:18'),
	(796, 1, 'en', 'Backend', 'Core', 'err', 'GoogleMapsKey', 'Google maps API-key is not configured. <a href=\"%1$s\">Configure</a>', '2017-08-31 14:28:18'),
	(797, 1, 'en', 'Backend', 'Core', 'err', 'InvalidAPIKey', 'Invalid API key.', '2017-08-31 14:28:18'),
	(798, 1, 'en', 'Backend', 'Core', 'err', 'InvalidDomain', 'Invalid domain.', '2017-08-31 14:28:18'),
	(799, 1, 'en', 'Backend', 'Core', 'err', 'InvalidEmailPasswordCombination', 'Your e-mail and password combination is incorrect. <a href=\"#\" id=\"forgotPasswordLink\" rel=\"forgotPasswordHolder\" data-toggle=\"modal\" data-target=\"#forgotPasswordHolder\">Did you forget your password?</a>', '2017-08-31 14:28:18'),
	(800, 1, 'en', 'Backend', 'Core', 'err', 'InvalidInteger', 'Invalid number.', '2017-08-31 14:28:18'),
	(801, 1, 'en', 'Backend', 'Core', 'err', 'InvalidName', 'Invalid name.', '2017-08-31 14:28:18'),
	(802, 1, 'en', 'Backend', 'Core', 'err', 'InvalidNumber', 'Invalid number.', '2017-08-31 14:28:18'),
	(803, 1, 'en', 'Backend', 'Core', 'err', 'InvalidParameters', 'Invalid parameters.', '2017-08-31 14:28:18'),
	(804, 1, 'en', 'Backend', 'Core', 'err', 'InvalidURL', 'Invalid URL.', '2017-08-31 14:28:18'),
	(805, 1, 'en', 'Backend', 'Core', 'err', 'InvalidValue', 'Invalid value.', '2017-08-31 14:28:18'),
	(806, 1, 'en', 'Backend', 'Core', 'err', 'JavascriptNotEnabled', 'To use Fork CMS, javascript needs to be enabled. Activate javascript and refresh this page.', '2017-08-31 14:28:18'),
	(807, 1, 'en', 'Backend', 'Core', 'err', 'JPGGIFAndPNGOnly', 'Only jpg, gif, png', '2017-08-31 14:28:18'),
	(808, 1, 'en', 'Backend', 'Core', 'err', 'ModuleNotAllowed', 'You have insufficient rights for this module.', '2017-08-31 14:28:18'),
	(809, 1, 'en', 'Backend', 'Core', 'err', 'NameIsRequired', 'Please provide a name.', '2017-08-31 14:28:18'),
	(810, 1, 'en', 'Backend', 'Core', 'err', 'NicknameIsRequired', 'Please provide a publication name.', '2017-08-31 14:28:18'),
	(811, 1, 'en', 'Backend', 'Core', 'err', 'NoActionSelected', 'No action selected.', '2017-08-31 14:28:18'),
	(812, 1, 'en', 'Backend', 'Core', 'err', 'NoCommentsSelected', 'No comments were selected.', '2017-08-31 14:28:18'),
	(813, 1, 'en', 'Backend', 'Core', 'err', 'NoItemsSelected', 'No items were selected.', '2017-08-31 14:28:18'),
	(814, 1, 'en', 'Backend', 'Core', 'err', 'NoModuleLinked', 'Cannot generate URL. Create a page that has this module linked to it.', '2017-08-31 14:28:18'),
	(815, 1, 'en', 'Backend', 'Core', 'err', 'NonExisting', 'This item doesn\'t exist.', '2017-08-31 14:28:18'),
	(816, 1, 'en', 'Backend', 'Core', 'err', 'NoSelection', 'No items were selected.', '2017-08-31 14:28:18'),
	(817, 1, 'en', 'Backend', 'Core', 'err', 'NoTemplatesAvailable', 'The selected theme does not yet have templates. Please create at least one template first.', '2017-08-31 14:28:18'),
	(818, 1, 'en', 'Backend', 'Core', 'err', 'PasswordIsRequired', 'Please provide a password.', '2017-08-31 14:28:18'),
	(819, 1, 'en', 'Backend', 'Core', 'err', 'PasswordRepeatIsRequired', 'Please repeat the desired password.', '2017-08-31 14:28:18'),
	(820, 1, 'en', 'Backend', 'Core', 'err', 'PasswordsDontMatch', 'The passwords differ', '2017-08-31 14:28:18'),
	(821, 1, 'en', 'Backend', 'Core', 'err', 'RobotsFileIsNotOK', 'robots.txt will block search-engines.', '2017-08-31 14:28:18'),
	(822, 1, 'en', 'Backend', 'Core', 'err', 'RSSTitle', 'Blog RSS title is not configured. <a href=\"%1$s\">Configure</a>', '2017-08-31 14:28:18'),
	(823, 1, 'en', 'Backend', 'Core', 'err', 'SettingsForkAPIKeys', 'The Fork API-keys are not configured.', '2017-08-31 14:28:18'),
	(824, 1, 'en', 'Backend', 'Core', 'err', 'SomethingWentWrong', 'Something went wrong.', '2017-08-31 14:28:18'),
	(825, 1, 'en', 'Backend', 'Core', 'err', 'StartDateIsInvalid', 'Invalid start date.', '2017-08-31 14:28:18'),
	(826, 1, 'en', 'Backend', 'Core', 'err', 'SurnameIsRequired', 'Please provide a last name.', '2017-08-31 14:28:18'),
	(827, 1, 'en', 'Backend', 'Core', 'err', 'TimeIsInvalid', 'Invalid time.', '2017-08-31 14:28:18'),
	(828, 1, 'en', 'Backend', 'Core', 'err', 'TitleIsRequired', 'Provide a title.', '2017-08-31 14:28:18'),
	(829, 1, 'en', 'Backend', 'Core', 'err', 'TooManyLoginAttempts', 'Too many login attempts. Click the forgot password link if you forgot your password.', '2017-08-31 14:28:18'),
	(830, 1, 'en', 'Backend', 'Core', 'err', 'URLAlreadyExists', 'This URL already exists.', '2017-08-31 14:28:18'),
	(831, 1, 'en', 'Backend', 'Core', 'err', 'ValuesDontMatch', 'The values don\'t match.', '2017-08-31 14:28:18'),
	(832, 1, 'en', 'Backend', 'Core', 'err', 'XMLFilesOnly', 'Only XMl files are allowed.', '2017-08-31 14:28:18'),
	(833, 1, 'en', 'Backend', 'Core', 'lbl', 'AllStatistics', 'all statistics', '2017-08-31 14:28:20'),
	(834, 1, 'en', 'Backend', 'Dashboard', 'lbl', 'TopKeywords', 'top keywords', '2017-08-31 14:28:20'),
	(835, 1, 'en', 'Backend', 'Dashboard', 'lbl', 'TopReferrers', 'top referrers', '2017-08-31 14:28:20'),
	(836, 1, 'en', 'Backend', 'Dashboard', 'msg', 'WillBeEnabledOnSave', 'This widget wil be reenabled on save.', '2017-08-31 14:28:20'),
	(837, 1, 'en', 'Backend', 'Settings', 'lbl', 'AdminIds', 'admin ids', '2017-08-31 14:28:20'),
	(838, 1, 'en', 'Backend', 'Settings', 'lbl', 'ApplicationId', 'application id', '2017-08-31 14:28:20'),
	(839, 1, 'en', 'Backend', 'Settings', 'lbl', 'ApplicationSecret', 'app secret', '2017-08-31 14:28:20'),
	(840, 1, 'en', 'Backend', 'Settings', 'lbl', 'Cookies', 'cookies', '2017-08-31 14:28:20'),
	(841, 1, 'en', 'Backend', 'Settings', 'lbl', 'ClearCache', 'clear cache', '2017-08-31 14:28:20'),
	(842, 1, 'en', 'Backend', 'Settings', 'lbl', 'LicenseKey', 'License key', '2017-08-31 14:28:20'),
	(843, 1, 'en', 'Backend', 'Settings', 'lbl', 'LicenseName', 'License name', '2017-08-31 14:28:20'),
	(844, 1, 'en', 'Backend', 'Settings', 'lbl', 'MaximumHeight', 'maximum height', '2017-08-31 14:28:20'),
	(845, 1, 'en', 'Backend', 'Settings', 'lbl', 'MaximumWidth', 'maximum width', '2017-08-31 14:28:20'),
	(846, 1, 'en', 'Backend', 'Settings', 'lbl', 'SEOSettings', 'SEO settings', '2017-08-31 14:28:20'),
	(847, 1, 'en', 'Backend', 'Settings', 'lbl', 'SmtpSecureLayer', 'security', '2017-08-31 14:28:20'),
	(848, 1, 'en', 'Backend', 'Settings', 'lbl', 'Twitter', 'twitter', '2017-08-31 14:28:20'),
	(849, 1, 'en', 'Backend', 'Settings', 'lbl', 'Tools', 'tools', '2017-08-31 14:28:20'),
	(850, 1, 'en', 'Backend', 'Settings', 'lbl', 'Facebook', 'facebook', '2017-08-31 14:28:20'),
	(851, 1, 'en', 'Backend', 'Settings', 'lbl', 'CkFinder', 'ckfinder', '2017-08-31 14:28:20'),
	(852, 1, 'en', 'Backend', 'Settings', 'msg', 'ConfigurationError', 'Some settings are not yet configured.', '2017-08-31 14:28:20'),
	(853, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpAPIKeys', 'Access codes for webservices.', '2017-08-31 14:28:20'),
	(854, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpCkfinderMaximumHeight', 'Configure the maximum height (in pixels) of uploaded images. If an uploaded image is larger, it gets scaled down proportionally. Set to 0 to disable this feature.', '2017-08-31 14:28:20'),
	(855, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpCkfinderMaximumWidth', 'Configure the maximum width (in pixels) of uploaded images. If an uploaded image is larger, it gets scaled down proportionally. Set to 0 to disable this feature.', '2017-08-31 14:28:20'),
	(856, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpCookies', 'There are several laws in Europe about the use of cookies. With this Cookie-bar you fulfill the most strict law.', '2017-08-31 14:28:20'),
	(857, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpDateFormatLong', 'Format that\'s used on overview and detail pages.', '2017-08-31 14:28:20'),
	(858, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpDateFormatShort', 'This format is mostly used in table overviews.', '2017-08-31 14:28:20'),
	(859, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpDomains', 'Enter the domains on which this website can be reached. (Split domains with linebreaks.)', '2017-08-31 14:28:20'),
	(860, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpEmailWebmaster', 'Send CMS notifications to this e-mailaddress.', '2017-08-31 14:28:20'),
	(861, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpFacebookAdminIds', 'Either Facebook user IDs or a Facebook Platform application ID that administers this website.', '2017-08-31 14:28:20'),
	(862, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpFacebookApiKey', 'The API key of your Facebook application.', '2017-08-31 14:28:20'),
	(863, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpFacebookApplicationId', 'The id of your Facebook application', '2017-08-31 14:28:20'),
	(864, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpFacebookApplicationSecret', 'The secret of your Facebook application.', '2017-08-31 14:28:20'),
	(865, 1, 'en', 'Backend', 'Settings', 'lbl', 'TwitterSiteName', 'twitter username', '2017-08-31 14:28:20'),
	(866, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpLanguages', 'Select the languages that are accessible for visitors.', '2017-08-31 14:28:20'),
	(867, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpNumberFormat', 'This format is used to display numbers on the website.', '2017-08-31 14:28:20'),
	(868, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpRedirectLanguages', 'Select the languages that people may automatically be redirected to based upon their browser language.', '2017-08-31 14:28:20'),
	(869, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpScriptsFoot', 'Paste code that needs to be loaded at the end of the <code><body></code> tag here.', '2017-08-31 14:28:20'),
	(870, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpScriptsFootLabel', 'End of <code>&lt;body&gt;</code> script(s)', '2017-08-31 14:28:20'),
	(871, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpScriptsHead', 'Paste code that needs to be loaded in the <code>&lt;head&gt;</code> section here.', '2017-08-31 14:28:20'),
	(872, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpScriptsHeadLabel', '<code>&lt;head&gt;</code> script(s)', '2017-08-31 14:28:20'),
	(873, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpScriptsStartOfBody', 'Paste code that needs to be loaded right after the opening <code>&lt;body&gt;</code> tag here.', '2017-08-31 14:28:20'),
	(874, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpScriptsStartOfBodyLabel', '<code>Start of &lt;body&gt;</code> script(s)', '2017-08-31 14:28:20'),
	(875, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpSendingEmails', 'You can send emails in 2 ways. By using PHP\'s built-in mail method or via SMTP. We advise you to use SMTP', '2017-08-31 14:28:20'),
	(876, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpSEONoodp', 'Opt out of the <a href=\"http://www.dmoz.org/\" class=\"targetBlank\">open directory project</a> override.', '2017-08-31 14:28:20'),
	(877, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpSEONoydir', 'Opt out of the Yahoo! Directory override.', '2017-08-31 14:28:20'),
	(878, 1, 'en', 'Backend', 'Settings', 'msg', 'HelpTimeFormat', 'This format is used to display dates on the website.', '2017-08-31 14:28:20'),
	(879, 1, 'en', 'Backend', 'Settings', 'msg', 'ClearCache', 'Remove all cached files. Useful for when problems arise which possibly have something to do with the cache. Remember clearing the cache might cause some errors when a page is reloaded for the first time.', '2017-08-31 14:28:20'),
	(880, 1, 'en', 'Backend', 'Settings', 'msg', 'ClearingCache', 'The cache is being cleared, hang on', '2017-08-31 14:28:20'),
	(881, 1, 'en', 'Backend', 'Settings', 'msg', 'CacheCleared', 'The cache has been successfully cleared.', '2017-08-31 14:28:20'),
	(882, 1, 'en', 'Backend', 'Settings', 'msg', 'NoAdminIds', 'No admin ids yet.', '2017-08-31 14:28:20'),
	(883, 1, 'en', 'Backend', 'Settings', 'msg', 'SendTestMail', 'send test email', '2017-08-31 14:28:20'),
	(884, 1, 'en', 'Backend', 'Settings', 'msg', 'SEONoFollowInComments', 'add <code>rel=\"nofollow\"</code> on links inside a comment', '2017-08-31 14:28:20'),
	(885, 1, 'en', 'Backend', 'Settings', 'msg', 'ShowCookieBar', 'show the cookie bar', '2017-08-31 14:28:20'),
	(886, 1, 'en', 'Backend', 'Settings', 'msg', 'TestMessage', 'this is just a test', '2017-08-31 14:28:20'),
	(887, 1, 'en', 'Backend', 'Settings', 'msg', 'TestWasSent', 'The test email was sent.', '2017-08-31 14:28:20'),
	(888, 1, 'en', 'Backend', 'Settings', 'err', 'PortIsRequired', 'Port is required.', '2017-08-31 14:28:20'),
	(889, 1, 'en', 'Backend', 'Settings', 'err', 'ServerIsRequired', 'Server is required.', '2017-08-31 14:28:20'),
	(890, 1, 'en', 'Frontend', 'Core', 'err', 'RecaptchaInvalid', 'Captcha code is invalid.', '2017-08-31 14:28:20'),
	(891, 0, 'en', 'Backend', 'Users', 'lbl', 'Add', 'add user', '2017-08-31 14:28:20'),
	(892, 0, 'en', 'Backend', 'Users', 'msg', 'Added', 'The user \"%1$s\" was added.', '2017-08-31 14:28:20'),
	(893, 0, 'en', 'Backend', 'Users', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the user \"%1$s\"?', '2017-08-31 14:28:20'),
	(894, 0, 'en', 'Backend', 'Users', 'msg', 'Deleted', 'The user \"%1$s\" was deleted.', '2017-08-31 14:28:20'),
	(895, 0, 'en', 'Backend', 'Users', 'msg', 'Edited', 'The settings for \"%1$s\" were saved.', '2017-08-31 14:28:20'),
	(896, 0, 'en', 'Backend', 'Users', 'msg', 'EditUser', 'edit user \"%1$s\"', '2017-08-31 14:28:20'),
	(897, 0, 'en', 'Backend', 'Users', 'msg', 'HelpActive', 'Enable CMS access for this account.', '2017-08-31 14:28:20'),
	(898, 0, 'en', 'Backend', 'Users', 'msg', 'HelpStrongPassword', 'Strong passwords consist of a combination of capitals', '2017-08-31 14:28:20'),
	(899, 0, 'en', 'Backend', 'Users', 'msg', 'Restored', 'The user \"%1$s\" is restored.', '2017-08-31 14:28:20'),
	(900, 0, 'en', 'Backend', 'Users', 'err', 'CantChangeGodsEmail', 'You can\'t change the emailaddres of the GOD-user.', '2017-08-31 14:28:20'),
	(901, 0, 'en', 'Backend', 'Users', 'err', 'CantDeleteGod', 'You can\'t delete the GOD-user.', '2017-08-31 14:28:20'),
	(902, 0, 'en', 'Backend', 'Users', 'err', 'EmailWasDeletedBefore', 'A user with this emailaddress was deleted. <a href=\"%1$s\">Restore this user</a>.', '2017-08-31 14:28:20'),
	(903, 0, 'en', 'Backend', 'Users', 'err', 'NonExisting', 'This user doesn\'t exist.', '2017-08-31 14:28:20'),
	(904, 1, 'en', 'Backend', 'Groups', 'lbl', 'Action', 'action', '2017-08-31 14:28:20'),
	(905, 1, 'en', 'Backend', 'Groups', 'lbl', 'AddGroup', 'add group', '2017-08-31 14:28:20'),
	(906, 1, 'en', 'Backend', 'Groups', 'lbl', 'Checkbox', ' ', '2017-08-31 14:28:20'),
	(907, 1, 'en', 'Backend', 'Groups', 'lbl', 'DisplayWidgets', 'widgets to display', '2017-08-31 14:28:20'),
	(908, 1, 'en', 'Backend', 'Groups', 'lbl', 'NumUsers', 'number of users', '2017-08-31 14:28:20'),
	(909, 1, 'en', 'Backend', 'Groups', 'lbl', 'Presets', 'presets', '2017-08-31 14:28:20'),
	(910, 1, 'en', 'Backend', 'Groups', 'lbl', 'SetPermissions', 'set permissions', '2017-08-31 14:28:20'),
	(911, 1, 'en', 'Backend', 'Groups', 'msg', 'Added', '\"%1$s\" has been added.', '2017-08-31 14:28:20'),
	(912, 1, 'en', 'Backend', 'Groups', 'msg', 'Deleted', '\"%1$s\" has been deleted.', '2017-08-31 14:28:20'),
	(913, 1, 'en', 'Backend', 'Groups', 'msg', 'Edited', 'changes for \"%1$s\" has been saved.', '2017-08-31 14:28:20'),
	(914, 1, 'en', 'Backend', 'Groups', 'msg', 'NoUsers', 'This group does not contain any users.', '2017-08-31 14:28:20'),
	(915, 1, 'en', 'Backend', 'Groups', 'msg', 'NoWidgets', 'There are no widgets available.', '2017-08-31 14:28:20'),
	(916, 1, 'en', 'Backend', 'Groups', 'err', 'GroupAlreadyExists', 'This group already exists.', '2017-08-31 14:28:20'),
	(917, 1, 'en', 'Backend', 'Extensions', 'msg', 'AddedTemplate', 'The template \"%1$s\" was added.', '2017-08-31 14:28:21'),
	(918, 1, 'en', 'Backend', 'Extensions', 'msg', 'ConfirmDeleteTemplate', 'Are your sure you want to delete the template \"%1$s\"?', '2017-08-31 14:28:21'),
	(919, 1, 'en', 'Backend', 'Extensions', 'msg', 'ConfirmModuleInstall', 'Are you sure you want to install the module \"%1$s\"?', '2017-08-31 14:28:21'),
	(920, 1, 'en', 'Backend', 'Extensions', 'msg', 'ConfirmModuleInstallDefault', 'Are you sure you want to install the module?', '2017-08-31 14:28:21'),
	(921, 1, 'en', 'Backend', 'Extensions', 'msg', 'ConfirmThemeInstall', 'Are you sure you want to install this theme?', '2017-08-31 14:28:21'),
	(922, 1, 'en', 'Backend', 'Extensions', 'msg', 'ShowImageForm', 'The user can upload a file.', '2017-08-31 14:28:21'),
	(923, 1, 'en', 'Backend', 'Extensions', 'msg', 'DeletedTemplate', 'The template \"%1$s\" was deleted.', '2017-08-31 14:28:21'),
	(924, 1, 'en', 'Backend', 'Extensions', 'msg', 'EditedTemplate', 'The template \"%1$s\" was saved.', '2017-08-31 14:28:21'),
	(925, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpInstallableThemes', 'Click a theme to install it.', '2017-08-31 14:28:21'),
	(926, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpOverwrite', '<strong>Attention!</strong> Checking this checkbox will cause the content of every page to be reset to the defaults chosen here-above.', '2017-08-31 14:28:21'),
	(927, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpPositionsLayoutText', '<strong>A visual representation to be used in the pages-module.</strong><ul><li>Add a row: use <strong>[]</strong></li><li>Reflect a position: use <strong>position name</strong></li><li>Reflect a non-editable area: use <strong>/</strong></li></ul><p>If you want a position to display wider or higher in it\'s graphical representation, repeat the position multiple times (both horizontal and vertical, but the shape should form a rectangle)</p>', '2017-08-31 14:28:21'),
	(928, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpPositionsLayoutExample', '<strong>A template could look like the chart below:</strong><pre>[  /   ,  /   ,  /   ,  /   ,  top ],<br />[  /   ,  /   ,  /   ,  /   ,  /   ],<br />[ left , main , main , main , right],<br />[bottom,bottom,bottom,bottom,bottom]</pre>', '2017-08-31 14:28:21'),
	(929, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpTemplateFormat', 'e.g. [left,main,right],[/,main,/]', '2017-08-31 14:28:21'),
	(930, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpTemplateLocation', 'Put your templates in the <code>Core/Layout/Templates</code> folder of your theme.', '2017-08-31 14:28:21'),
	(931, 1, 'en', 'Backend', 'Extensions', 'msg', 'HelpThemes', 'Select the theme you wish to use.', '2017-08-31 14:28:21'),
	(932, 1, 'en', 'Backend', 'Extensions', 'msg', 'InformationFileCouldNotBeLoaded', 'A info.xml file is present but it could not be loaded. Verify if the content is valid XML.', '2017-08-31 14:28:21'),
	(933, 1, 'en', 'Backend', 'Extensions', 'msg', 'InformationFileIsEmpty', 'A info.xml file is present but its either empty or it does not contain valuable information.', '2017-08-31 14:28:21'),
	(934, 1, 'en', 'Backend', 'Extensions', 'msg', 'InformationFileIsMissing', 'There is no information available.', '2017-08-31 14:28:21'),
	(935, 1, 'en', 'Backend', 'Extensions', 'msg', 'InformationModuleIsNotInstalled', 'This module is not yet installed.', '2017-08-31 14:28:21'),
	(936, 1, 'en', 'Backend', 'Extensions', 'msg', 'InformationThemeIsNotInstalled', 'This theme is not yet installed.', '2017-08-31 14:28:21'),
	(937, 1, 'en', 'Backend', 'Extensions', 'msg', 'Module', 'module \"%1$s\"', '2017-08-31 14:28:21'),
	(938, 1, 'en', 'Backend', 'Extensions', 'msg', 'ModuleInstalled', 'The module \"%1$s\" was installed.', '2017-08-31 14:28:21'),
	(939, 1, 'en', 'Backend', 'Extensions', 'msg', 'ModulesNotWritable', 'We do not have write rights to the modules folders. Check if you have write rights on the modules folders in all applications.', '2017-08-31 14:28:21'),
	(940, 1, 'en', 'Backend', 'Extensions', 'msg', 'ModulesWarnings', 'There are some warnings for following module(s)', '2017-08-31 14:28:21'),
	(941, 1, 'en', 'Backend', 'Extensions', 'msg', 'NoModulesInstalled', 'No modules installed.', '2017-08-31 14:28:21'),
	(942, 1, 'en', 'Backend', 'Extensions', 'msg', 'NoThemes', 'No themes available.', '2017-08-31 14:28:21'),
	(943, 1, 'en', 'Backend', 'Extensions', 'msg', 'PathToTemplate', 'Path to template', '2017-08-31 14:28:21'),
	(944, 1, 'en', 'Backend', 'Extensions', 'msg', 'TemplateInUse', 'This template is in use.', '2017-08-31 14:28:21'),
	(945, 1, 'en', 'Backend', 'Extensions', 'msg', 'Theme', 'theme \"%1$s\"', '2017-08-31 14:28:21'),
	(946, 1, 'en', 'Backend', 'Extensions', 'msg', 'ThemeInstalled', 'The theme \"%1$s\" was installed.', '2017-08-31 14:28:21'),
	(947, 1, 'en', 'Backend', 'Extensions', 'msg', 'ThemesNotWritable', 'We do not have write rights to the themes folder. Check if you have write rights on the themes folders in the frontend-application.', '2017-08-31 14:28:21'),
	(948, 1, 'en', 'Backend', 'Extensions', 'msg', 'ZlibIsMissing', 'Your server is missing the required PHP \"<a href=\"http://www.php.net/manual/en/book.zlib.php\">Zlib</a>\" extension. Fork CMS needs this extension to be able to unpack your uploaded module.<br /><br />      <ul>        <li>Contact your server administrator with the above message.</li>        <li>Or unpack the ZIP archive on your computer and upload the folders manually (most likely via FTP) to your website root.</li>      </ul>    ', '2017-08-31 14:28:21'),
	(949, 1, 'en', 'Backend', 'Extensions', 'lbl', 'AddPosition', 'add position', '2017-08-31 14:28:21'),
	(950, 1, 'en', 'Backend', 'Extensions', 'lbl', 'AddThemeTemplate', 'add theme template', '2017-08-31 14:28:21'),
	(951, 1, 'en', 'Backend', 'Extensions', 'lbl', 'Authors', 'authors', '2017-08-31 14:28:21'),
	(952, 1, 'en', 'Backend', 'Extensions', 'lbl', 'DeletePosition', 'delete position', '2017-08-31 14:28:21'),
	(953, 1, 'en', 'Backend', 'Extensions', 'lbl', 'DeleteBlock', 'delete block', '2017-08-31 14:28:21'),
	(954, 1, 'en', 'Backend', 'Extensions', 'lbl', 'EditThemeTemplate', 'edit theme template', '2017-08-31 14:28:21'),
	(955, 1, 'en', 'Backend', 'Extensions', 'lbl', 'Events', 'events (hooks)', '2017-08-31 14:28:21'),
	(956, 1, 'en', 'Backend', 'Extensions', 'lbl', 'FindModules', 'find modules', '2017-08-31 14:28:21'),
	(957, 1, 'en', 'Backend', 'Extensions', 'lbl', 'FindThemes', 'find themes', '2017-08-31 14:28:21'),
	(958, 1, 'en', 'Backend', 'Extensions', 'lbl', 'Install', 'install', '2017-08-31 14:28:21'),
	(959, 1, 'en', 'Backend', 'Extensions', 'lbl', 'InstallableModules', 'not installed modules', '2017-08-31 14:28:21'),
	(960, 1, 'en', 'Backend', 'Extensions', 'lbl', 'InstallableThemes', 'not installed themes', '2017-08-31 14:28:21'),
	(961, 1, 'en', 'Backend', 'Extensions', 'lbl', 'InstalledModules', 'installed modules', '2017-08-31 14:28:21'),
	(962, 1, 'en', 'Backend', 'Extensions', 'lbl', 'InstalledThemes', 'installed themes', '2017-08-31 14:28:21'),
	(963, 1, 'en', 'Backend', 'Extensions', 'lbl', 'Overwrite', 'overwrite', '2017-08-31 14:28:21'),
	(964, 1, 'en', 'Backend', 'Extensions', 'lbl', 'Theme', 'theme', '2017-08-31 14:28:21'),
	(965, 1, 'en', 'Backend', 'Extensions', 'lbl', 'ThemeTemplates', 'theme templates', '2017-08-31 14:28:21'),
	(966, 1, 'en', 'Backend', 'Extensions', 'lbl', 'SelectedTheme', 'selected theme', '2017-08-31 14:28:21'),
	(967, 1, 'en', 'Backend', 'Extensions', 'lbl', 'UseThisTheme', 'use this theme', '2017-08-31 14:28:21'),
	(968, 1, 'en', 'Backend', 'Extensions', 'lbl', 'UploadModule', 'upload module', '2017-08-31 14:28:21'),
	(969, 1, 'en', 'Backend', 'Extensions', 'lbl', 'UploadTheme', 'upload theme', '2017-08-31 14:28:21'),
	(970, 1, 'en', 'Backend', 'Extensions', 'lbl', 'Version', 'version', '2017-08-31 14:28:21'),
	(971, 1, 'en', 'Backend', 'Extensions', 'err', 'AlreadyInstalled', '\"%1$s\" is already installed.', '2017-08-31 14:28:21'),
	(972, 1, 'en', 'Backend', 'Extensions', 'err', 'CorruptedFile', 'The uploaded file is not a valid ZIP file and could not be extracted.', '2017-08-31 14:28:21'),
	(973, 1, 'en', 'Backend', 'Extensions', 'err', 'DeleteTemplate', 'You can\'t delete this template.', '2017-08-31 14:28:21'),
	(974, 1, 'en', 'Backend', 'Extensions', 'err', 'DuplicatePositionName', 'Position %s is duplicated.', '2017-08-31 14:28:21'),
	(975, 1, 'en', 'Backend', 'Extensions', 'err', 'FileContentsIsUseless', 'We could not find a module in the uploaded file. Verify the contents.', '2017-08-31 14:28:21'),
	(976, 1, 'en', 'Backend', 'Extensions', 'err', 'FileIsEmpty', 'The file is empty. Verify the contents.', '2017-08-31 14:28:21'),
	(977, 1, 'en', 'Backend', 'Extensions', 'err', 'TemplateFileNotFound', 'The template file is missing.', '2017-08-31 14:28:21'),
	(978, 1, 'en', 'Backend', 'Extensions', 'err', 'InvalidTemplateSyntax', 'Invalid syntax.', '2017-08-31 14:28:21'),
	(979, 1, 'en', 'Backend', 'Extensions', 'err', 'LibraryFileAlreadyExists', 'The library-file \"%1$s\" already existed by another module. This module may not function properly.', '2017-08-31 14:28:21'),
	(980, 1, 'en', 'Backend', 'Extensions', 'err', 'ModuleAlreadyExists', 'The module \"%1$s\" already exists, you can not upload it again.', '2017-08-31 14:28:21'),
	(981, 1, 'en', 'Backend', 'Extensions', 'err', 'NoAlphaNumPositionName', 'Position %s is not alphanumerical.', '2017-08-31 14:28:21'),
	(982, 1, 'en', 'Backend', 'Extensions', 'err', 'NoInformationFile', 'We could not find an info.xml file for \"%1$s\".', '2017-08-31 14:28:21'),
	(983, 1, 'en', 'Backend', 'Extensions', 'err', 'NoInstallerFile', 'We could not find an installer for the module \"%1$s\".', '2017-08-31 14:28:21'),
	(984, 1, 'en', 'Backend', 'Extensions', 'err', 'NonExistingPositionName', 'Position %s is not defined.', '2017-08-31 14:28:21'),
	(985, 1, 'en', 'Backend', 'Extensions', 'err', 'NoThemes', 'No themes available.', '2017-08-31 14:28:21'),
	(986, 1, 'en', 'Backend', 'Extensions', 'err', 'ReservedPositionName', 'Position %s is reserved.', '2017-08-31 14:28:21'),
	(987, 1, 'en', 'Backend', 'Extensions', 'err', 'ThemeAlreadyExists', 'The theme \"%1$s\" already exists, you can not upload it again.', '2017-08-31 14:28:21'),
	(988, 1, 'en', 'Backend', 'Extensions', 'err', 'ThemeNameDoesntMatch', 'The theme\'s folder name doesn\'t match the theme name in info.xml.', '2017-08-31 14:28:21'),
	(989, 1, 'en', 'Backend', 'Pages', 'lbl', 'Add', 'add', '2017-08-31 14:28:21'),
	(990, 1, 'en', 'Backend', 'Pages', 'lbl', 'AddPage', 'add page', '2017-08-31 14:28:21'),
	(991, 1, 'en', 'Backend', 'Pages', 'lbl', 'AddSubPage', 'add sub page', '2017-08-31 14:28:21'),
	(992, 1, 'en', 'Backend', 'Pages', 'lbl', 'ToggleAddPageDropdown', 'toggle add page dropdown', '2017-08-31 14:28:21'),
	(993, 1, 'en', 'Backend', 'Pages', 'lbl', 'ChangeTemplate', 'Change template', '2017-08-31 14:28:21'),
	(994, 1, 'en', 'Backend', 'Pages', 'lbl', 'ChooseTemplate', 'choose template', '2017-08-31 14:28:21'),
	(995, 1, 'en', 'Backend', 'Pages', 'lbl', 'DeleteBlock', 'delete block', '2017-08-31 14:28:21'),
	(996, 1, 'en', 'Backend', 'Pages', 'lbl', 'EditModuleContent', 'edit module content', '2017-08-31 14:28:21'),
	(997, 1, 'en', 'Backend', 'Pages', 'lbl', 'ExternalLink', 'external link', '2017-08-31 14:28:21'),
	(998, 1, 'en', 'Backend', 'Pages', 'lbl', 'ExtraTypeBlock', 'module', '2017-08-31 14:28:21'),
	(999, 1, 'en', 'Backend', 'Pages', 'lbl', 'ExtraTypeWidget', 'widget', '2017-08-31 14:28:21'),
	(1000, 1, 'en', 'Backend', 'Pages', 'lbl', 'Fallback', 'Unassigned blocks', '2017-08-31 14:28:21'),
	(1001, 1, 'en', 'Backend', 'Pages', 'lbl', 'Footer', 'footer navigation', '2017-08-31 14:28:21'),
	(1002, 1, 'en', 'Backend', 'Pages', 'lbl', 'InternalLink', 'internal link', '2017-08-31 14:28:21'),
	(1003, 1, 'en', 'Backend', 'Pages', 'lbl', 'MainNavigation', 'main navigation', '2017-08-31 14:28:21'),
	(1004, 1, 'en', 'Backend', 'Pages', 'lbl', 'Meta', 'meta navigation', '2017-08-31 14:28:21'),
	(1005, 1, 'en', 'Backend', 'Pages', 'lbl', 'Navigation', 'navigation', '2017-08-31 14:28:21'),
	(1006, 1, 'en', 'Backend', 'Pages', 'lbl', 'Redirect', 'redirect', '2017-08-31 14:28:21'),
	(1007, 1, 'en', 'Backend', 'Pages', 'lbl', 'Root', 'single pages', '2017-08-31 14:28:21'),
	(1008, 1, 'en', 'Backend', 'Pages', 'lbl', 'SentMailings', 'sent mailings', '2017-08-31 14:28:21'),
	(1009, 1, 'en', 'Backend', 'Pages', 'lbl', 'ShowImage', 'show the image', '2017-08-31 14:28:21'),
	(1010, 1, 'en', 'Backend', 'Pages', 'lbl', 'SubscribeForm', 'subscribe form', '2017-08-31 14:28:21'),
	(1011, 1, 'en', 'Backend', 'Pages', 'lbl', 'UnsubscribeForm', 'unsubscribe form', '2017-08-31 14:28:21'),
	(1012, 1, 'en', 'Backend', 'Pages', 'lbl', 'UserTemplate', 'user template', '2017-08-31 14:28:21'),
	(1013, 1, 'en', 'Backend', 'Pages', 'lbl', 'WhichTemplate', 'template', '2017-08-31 14:28:21'),
	(1014, 1, 'en', 'Backend', 'Pages', 'msg', 'Added', 'The page \"%1$s\" was added.', '2017-08-31 14:28:21'),
	(1015, 1, 'en', 'Backend', 'Pages', 'msg', 'HelpImage', 'This image can be used in the template. for example as a header', '2017-08-31 14:28:21'),
	(1016, 1, 'en', 'Backend', 'Pages', 'msg', 'AllowChildren', 'This page can have subpages.', '2017-08-31 14:28:21'),
	(1017, 1, 'en', 'Backend', 'Pages', 'msg', 'AllowDelete', 'This page can be deleted.', '2017-08-31 14:28:21'),
	(1018, 1, 'en', 'Backend', 'Pages', 'msg', 'AllowEdit', 'This page can be edited.', '2017-08-31 14:28:21'),
	(1019, 1, 'en', 'Backend', 'Pages', 'msg', 'AllowMove', 'The position of this page can be changed.', '2017-08-31 14:28:21'),
	(1020, 1, 'en', 'Backend', 'Pages', 'msg', 'AllowImage', 'This page can have an image.', '2017-08-31 14:28:21'),
	(1021, 1, 'en', 'Backend', 'Pages', 'msg', 'BlockAttached', 'The module <strong>%1$s</strong> is attached to this section.', '2017-08-31 14:28:21'),
	(1022, 1, 'en', 'Backend', 'Pages', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the page \"%1$s\"?', '2017-08-31 14:28:21'),
	(1023, 1, 'en', 'Backend', 'Pages', 'msg', 'ConfirmDeleteBlock', 'Are your sure you want to delete this block?', '2017-08-31 14:28:21'),
	(1024, 1, 'en', 'Backend', 'Pages', 'msg', 'ContentSaveWarning', '<p><strong>Important:</strong> This content will not be updated until the page has been saved.</p>', '2017-08-31 14:28:21'),
	(1025, 1, 'en', 'Backend', 'Pages', 'msg', 'CopyAdded', 'Copy added', '2017-08-31 14:28:21'),
	(1026, 1, 'en', 'Backend', 'Pages', 'msg', 'Deleted', 'The page \"%1$s\" was deleted.', '2017-08-31 14:28:21'),
	(1027, 1, 'en', 'Backend', 'Pages', 'msg', 'Edited', 'The page \"%1$s\" was saved.', '2017-08-31 14:28:21'),
	(1028, 1, 'en', 'Backend', 'Pages', 'msg', 'FallbackInfo', '<p><strong>Not every block could automatically be assigned to a position.</strong></p><p>Blocks that were added to positions that are not available in this template, are shown here. Default blocks from the previous template that are not present in the current template are also displayed here.<br />You can easily drag them to the desired position.</p><p>These blocks will disappear after saving the page or selecting another template.</p>', '2017-08-31 14:28:21'),
	(1029, 1, 'en', 'Backend', 'Pages', 'msg', 'HelpBlockContent', 'What kind of content do you want to show here?', '2017-08-31 14:28:21'),
	(1030, 1, 'en', 'Backend', 'Pages', 'msg', 'HelpExternalRedirect', 'Use this if you need to redirect a menu-item to an external website.', '2017-08-31 14:28:21'),
	(1031, 1, 'en', 'Backend', 'Pages', 'msg', 'HelpInternalRedirect', 'Use this if you need to redirect a menu-item to another page on this website.', '2017-08-31 14:28:21'),
	(1032, 1, 'en', 'Backend', 'Pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigation (above/below the menu) on every page.', '2017-08-31 14:28:21'),
	(1033, 1, 'en', 'Backend', 'Pages', 'msg', 'HelpNavigationTitle', 'The title that is shown in the menu.', '2017-08-31 14:28:21'),
	(1034, 1, 'en', 'Backend', 'Pages', 'msg', 'HomeNoBlock', 'A module can\'t be linked to the homepage.', '2017-08-31 14:28:21'),
	(1035, 1, 'en', 'Backend', 'Pages', 'msg', 'IsAction', 'Use this page as a module action.', '2017-08-31 14:28:21'),
	(1036, 1, 'en', 'Backend', 'Pages', 'msg', 'MetaNavigation', 'Enable metanavigation for this website.', '2017-08-31 14:28:21'),
	(1037, 1, 'en', 'Backend', 'Pages', 'msg', 'ModuleBlockAlreadyLinked', 'A module has already been linked to this page.', '2017-08-31 14:28:21'),
	(1038, 1, 'en', 'Backend', 'Pages', 'msg', 'PageIsMoved', 'The page \"%1$s\" was moved.', '2017-08-31 14:28:21'),
	(1039, 1, 'en', 'Backend', 'Pages', 'msg', 'RemoveFromSearchIndex', 'Do not show this page or content in the search results.', '2017-08-31 14:28:21'),
	(1040, 1, 'en', 'Backend', 'Pages', 'msg', 'RichText', 'Editor', '2017-08-31 14:28:21'),
	(1041, 1, 'en', 'Backend', 'Pages', 'msg', 'TemplateChangeWarning', '<strong>Warning:</strong> Changing the template can cause existing content to be in another place or no longer be shown.', '2017-08-31 14:28:21'),
	(1042, 1, 'en', 'Backend', 'Pages', 'msg', 'WidgetAttached', 'The widget <strong>%1$s</strong> is attached to this section.', '2017-08-31 14:28:21'),
	(1043, 1, 'en', 'Backend', 'Pages', 'msg', 'AddTagsHere', 'Add tags here.', '2017-08-31 14:28:21'),
	(1044, 1, 'en', 'Backend', 'Pages', 'msg', 'MovePagesNotPossible', 'Moving pages on touch devices is not possible.', '2017-08-31 14:28:21'),
	(1045, 1, 'en', 'Backend', 'Pages', 'err', 'CantAdd2Blocks', 'It isn\'t possible to link 2 (or more) modules to the same page.', '2017-08-31 14:28:21'),
	(1046, 1, 'en', 'Backend', 'Pages', 'err', 'HomeCantHaveBlocks', 'You can\'t link a module to the homepage.', '2017-08-31 14:28:21'),
	(1047, 1, 'en', 'Backend', 'Pages', 'msg', 'AuthRequired', 'Users need to be logged to view this page.', '2017-08-31 14:28:21'),
	(1048, 1, 'en', 'Backend', 'Core', 'err', 'CantBeMoved', 'Page can\'t be moved.', '2017-08-31 14:28:21'),
	(1049, 1, 'en', 'Backend', 'Search', 'msg', 'AddedSynonym', 'The synonym for the searchterm \"%1$s\" was added.', '2017-08-31 14:28:21'),
	(1050, 1, 'en', 'Backend', 'Search', 'msg', 'ConfirmDeleteSynonym', 'Are you sure you want to delete the synonyms for the searchterm \"%1$s\"?', '2017-08-31 14:28:21'),
	(1051, 1, 'en', 'Backend', 'Search', 'msg', 'DeletedSynonym', 'The synonym for the searchterm \"%1$s\" was deleted.', '2017-08-31 14:28:21'),
	(1052, 1, 'en', 'Backend', 'Search', 'msg', 'EditedSynonym', 'The synonym for the searchterm \"%1$s\" was saved.', '2017-08-31 14:28:21'),
	(1053, 1, 'en', 'Backend', 'Search', 'msg', 'HelpWeight', 'The default weight is 1. Increase the value to increase the importance of results from a specific module.', '2017-08-31 14:28:21'),
	(1054, 1, 'en', 'Backend', 'Search', 'msg', 'HelpWeightGeneral', 'Define the importance of each module in search results here.', '2017-08-31 14:28:21'),
	(1055, 1, 'en', 'Backend', 'Search', 'msg', 'HelpSitelinksSearchBox', 'You can find more info in <a href=\"https://developers.google.com/webmasters/richsnippets/sitelinkssearch\">Googles official documentation</a>.', '2017-08-31 14:28:21'),
	(1056, 1, 'en', 'Backend', 'Search', 'msg', 'IncludeInSearch', 'Include in search results?', '2017-08-31 14:28:21'),
	(1057, 1, 'en', 'Backend', 'Search', 'msg', 'NoStatistics', 'There are no statistics yet.', '2017-08-31 14:28:21'),
	(1058, 1, 'en', 'Backend', 'Search', 'msg', 'NoSynonyms', 'There are no synonyms yet. <a href=\"%1$s\">Add the first synonym</a>.', '2017-08-31 14:28:21'),
	(1059, 1, 'en', 'Backend', 'Search', 'msg', 'NoSynonymsBox', 'There are no synonyms yet.', '2017-08-31 14:28:21'),
	(1060, 1, 'en', 'Backend', 'Search', 'lbl', 'AddSynonym', 'add synonym', '2017-08-31 14:28:21'),
	(1061, 1, 'en', 'Backend', 'Search', 'lbl', 'DeleteSynonym', 'delete synonym', '2017-08-31 14:28:21'),
	(1062, 1, 'en', 'Backend', 'Search', 'lbl', 'EditSynonym', 'edit synonym', '2017-08-31 14:28:21'),
	(1063, 1, 'en', 'Backend', 'Search', 'lbl', 'ItemsForAutocomplete', 'Items in autocomplete (search results: search term suggestions)', '2017-08-31 14:28:21'),
	(1064, 1, 'en', 'Backend', 'Search', 'lbl', 'ItemsForAutosuggest', 'Items in autosuggest (search widget: results)', '2017-08-31 14:28:21'),
	(1065, 1, 'en', 'Backend', 'Search', 'lbl', 'ModuleWeight', 'module weight', '2017-08-31 14:28:21'),
	(1066, 1, 'en', 'Backend', 'Search', 'lbl', 'SearchedOn', 'searched on', '2017-08-31 14:28:21'),
	(1067, 1, 'en', 'Backend', 'Search', 'lbl', 'UseSitelinksSearchBox', 'Enable Googles Sitelinks Search Box.', '2017-08-31 14:28:21'),
	(1068, 1, 'en', 'Backend', 'Search', 'err', 'SynonymIsRequired', 'Synonyms are required.', '2017-08-31 14:28:21'),
	(1069, 1, 'en', 'Backend', 'Search', 'err', 'TermExists', 'Synonyms for this searchterm already exist.', '2017-08-31 14:28:21'),
	(1070, 1, 'en', 'Backend', 'Search', 'err', 'TermIsRequired', 'The searchterm is required.', '2017-08-31 14:28:21'),
	(1071, 1, 'en', 'Backend', 'Search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn', '2017-08-31 14:28:21'),
	(1072, 1, 'en', 'Backend', 'ContentBlocks', 'lbl', 'Add', 'add content block', '2017-08-31 14:28:22'),
	(1073, 1, 'en', 'Backend', 'ContentBlocks', 'msg', 'Added', 'The content block \"%1$s\" was added.', '2017-08-31 14:28:22'),
	(1074, 1, 'en', 'Backend', 'ContentBlocks', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the content block \"%1$s\"?', '2017-08-31 14:28:22'),
	(1075, 1, 'en', 'Backend', 'ContentBlocks', 'msg', 'Deleted', 'The content block \"%1$s\" was deleted.', '2017-08-31 14:28:22'),
	(1076, 1, 'en', 'Backend', 'ContentBlocks', 'lbl', 'Edit', 'edit content block', '2017-08-31 14:28:22'),
	(1077, 1, 'en', 'Backend', 'ContentBlocks', 'msg', 'Edited', 'The content block \"%1$s\" was saved.', '2017-08-31 14:28:22'),
	(1078, 1, 'en', 'Backend', 'ContentBlocks', 'msg', 'NoTemplate', 'No template', '2017-08-31 14:28:22'),
	(1079, 1, 'en', 'Backend', 'Tags', 'msg', 'Deleted', 'The selected tag(s) was/were deleted.', '2017-08-31 14:28:22'),
	(1080, 1, 'en', 'Backend', 'Tags', 'msg', 'Edited', 'The tag \"%1$s\" was saved.', '2017-08-31 14:28:22'),
	(1081, 1, 'en', 'Backend', 'Tags', 'msg', 'EditTag', 'edit tag \"%1$s\"', '2017-08-31 14:28:22'),
	(1082, 1, 'en', 'Backend', 'Tags', 'msg', 'NoItems', 'There are no tags yet.', '2017-08-31 14:28:22'),
	(1083, 1, 'en', 'Backend', 'Tags', 'err', 'NonExisting', 'This tag doesn\'t exist.', '2017-08-31 14:28:22'),
	(1084, 1, 'en', 'Backend', 'Tags', 'err', 'NoSelection', 'No tags were selected.', '2017-08-31 14:28:22'),
	(1085, 1, 'en', 'Backend', 'Tags', 'err', 'TagAlreadyExists', 'This tag already exists.', '2017-08-31 14:28:22'),
	(1086, 1, 'en', 'Backend', 'Analytics', 'lbl', 'AverageTimeOnSite', 'average time on site', '2017-08-31 14:28:22'),
	(1087, 1, 'en', 'Backend', 'Analytics', 'lbl', 'BounceRate', 'bounce rate', '2017-08-31 14:28:22'),
	(1088, 1, 'en', 'Backend', 'Analytics', 'lbl', 'ChangePeriod', 'change period', '2017-08-31 14:28:22'),
	(1089, 1, 'en', 'Backend', 'Analytics', 'lbl', 'ChooseThisAccount', 'choose this account', '2017-08-31 14:28:22'),
	(1090, 1, 'en', 'Backend', 'Analytics', 'lbl', 'ChooseWebsiteProfile', 'Choose an Analytics website profile...', '2017-08-31 14:28:22'),
	(1091, 1, 'en', 'Backend', 'Analytics', 'lbl', 'GaPagePath', 'page', '2017-08-31 14:28:22'),
	(1092, 1, 'en', 'Backend', 'Analytics', 'lbl', 'GaPageviews', 'pageviews', '2017-08-31 14:28:22'),
	(1093, 1, 'en', 'Backend', 'Analytics', 'lbl', 'LinkedProfile', 'linked profile', '2017-08-31 14:28:22'),
	(1094, 1, 'en', 'Backend', 'Analytics', 'lbl', 'NewVisitsPercentage', 'new visits percentage', '2017-08-31 14:28:22'),
	(1095, 1, 'en', 'Backend', 'Analytics', 'lbl', 'PagesPerVisit', 'pages per visit', '2017-08-31 14:28:22'),
	(1096, 1, 'en', 'Backend', 'Analytics', 'lbl', 'Pageviews', 'pageviews', '2017-08-31 14:28:22'),
	(1097, 1, 'en', 'Backend', 'Analytics', 'lbl', 'Certificate', 'certificate (.p12 file)', '2017-08-31 14:28:22'),
	(1098, 1, 'en', 'Backend', 'Analytics', 'lbl', 'MostViewedPages', 'most viewed pages', '2017-08-31 14:28:22'),
	(1099, 1, 'en', 'Backend', 'Analytics', 'lbl', 'Visitors', 'visitors', '2017-08-31 14:28:22'),
	(1100, 1, 'en', 'Backend', 'Analytics', 'err', 'P12Only', 'Only p12 files are allowed.', '2017-08-31 14:28:22'),
	(1101, 1, 'en', 'Backend', 'Analytics', 'msg', 'CertificateHelp', '\n          <h3>How to get your secret file?</h3>\n          <br/>\n          <p>Enable the Analytics API</p>\n          <p>\n            <ol>\n              <li>Go to the <a href=\"https://console.developers.google.com/\" target=\"_blank\">Google Developers Console</a>.</li>\n              <li>Make sure you\'re logged in with a Google account that has access to the wanted Analytics account.</li>\n              <li>Select a project in the header, or create a new one.</li>\n              <li>Click on <strong>Library</strong> in the sidebar on the left.</li>\n              <li>Go to the <strong>Analytics API</strong> page by clicking on it in the <strong>Other popular API\'s</strong> category or typing it in the search bar.</li>\n              <li>You can enable the API if you haven\'t done that yet by clicking on <strong>ENABLE API</strong> underneath the header.</li>\n            </ol>\n          </p>\n          <p>Creating credentials for Fork CMS.</p>\n          <p>\n            <ol>\n              <li>In the sidebar on the left, select <strong>Credentials</strong>.</li>\n              <li>Click on <strong>Create credentials</strong> and select <strong>Service account key</strong> in the dropdown.</li>\n              <li>Create a new service account with the role <strong>Project - Editor</strong> and <strong>P12</strong> as Key type.</li>\n              <li>Download the generated certificate (.p12 file).</li>\n              <li>Go back to the <strong>Credentials</strong> page and click on <strong>Manage service accounts</strong></li>\n              <li>Copy the <strong>Service account ID</strong> of the newly created account. It should look something like <code>name@spheric-passkey-123456.iam.gserviceaccount.com</code></li>\n              <li>Login to analytics, go to admin page and add the generated e-mail adress to the prefered property with \"read and analyze\" rights.</li>\n              <li>Grab a cup of coffee, and come back to Fork in some minutes. It can take some time before the coupling is fully done.</li>\n            </ol>\n          </p>', '2017-08-31 14:28:22'),
	(1102, 1, 'en', 'Backend', 'Analytics', 'msg', 'NoAccounts', 'There are no analytics accounts coupled to the given email address. Make sure you added the email address %1$s to the wanted account. It can take a while before the coupling is completed.', '2017-08-31 14:28:22'),
	(1103, 1, 'en', 'Backend', 'Analytics', 'msg', 'RemoveAccountLink', 'Remove the link with your Google account', '2017-08-31 14:28:22'),
	(1104, 1, 'en', 'Backend', 'Core', 'lbl', 'PageviewsByTrafficSources', 'pageviews per traffic source', '2017-08-31 14:28:22'),
	(1105, 1, 'en', 'Backend', 'Blog', 'lbl', 'Add', 'add article', '2017-08-31 14:28:23'),
	(1106, 1, 'en', 'Backend', 'Blog', 'lbl', 'WordpressFilter', 'filter', '2017-08-31 14:28:23'),
	(1107, 1, 'en', 'Backend', 'Blog', 'msg', 'Added', 'The article \"%1$s\" was added.', '2017-08-31 14:28:23'),
	(1108, 1, 'en', 'Backend', 'Blog', 'msg', 'ArticlesFor', 'Articles for \"%1$s\"', '2017-08-31 14:28:23'),
	(1109, 1, 'en', 'Backend', 'Blog', 'msg', 'CommentOnWithURL', 'Comment on: <a href=\"%1$s\">%2$s</a>', '2017-08-31 14:28:23'),
	(1110, 1, 'en', 'Backend', 'Blog', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the article \"%1$s\"?', '2017-08-31 14:28:23'),
	(1111, 1, 'en', 'Backend', 'Blog', 'msg', 'DeleteAllSpam', 'Delete all spam:', '2017-08-31 14:28:23'),
	(1112, 1, 'en', 'Backend', 'Blog', 'msg', 'Deleted', 'The selected articles were deleted.', '2017-08-31 14:28:23'),
	(1113, 1, 'en', 'Backend', 'Blog', 'msg', 'DeletedSpam', 'All spam-comments were deleted.', '2017-08-31 14:28:23'),
	(1114, 1, 'en', 'Backend', 'Blog', 'msg', 'EditArticle', 'edit article \"%1$s\"', '2017-08-31 14:28:23'),
	(1115, 1, 'en', 'Backend', 'Blog', 'msg', 'EditCommentOn', 'edit comment on \"%1$s\"', '2017-08-31 14:28:23'),
	(1116, 1, 'en', 'Backend', 'Blog', 'msg', 'Edited', 'The article \"%1$s\" was saved.', '2017-08-31 14:28:23'),
	(1117, 1, 'en', 'Backend', 'Blog', 'msg', 'EditedComment', 'The comment was saved.', '2017-08-31 14:28:23'),
	(1118, 1, 'en', 'Backend', 'Blog', 'msg', 'FollowAllCommentsInRSS', 'Follow all comments in a RSS feed: <a href=\"%1$s\">%1$s</a>.', '2017-08-31 14:28:23'),
	(1119, 1, 'en', 'Backend', 'Blog', 'msg', 'HelpMeta', 'Show the meta information for this blogpost in the RSS feed (category)', '2017-08-31 14:28:23'),
	(1120, 1, 'en', 'Backend', 'Blog', 'msg', 'HelpPingServices', 'Let various blogservices know when you\'ve posted a new article.', '2017-08-31 14:28:23'),
	(1121, 1, 'en', 'Backend', 'Blog', 'msg', 'HelpSpamFilter', 'Enable the built-in spamfilter (Akismet) to help avoid spam comments.', '2017-08-31 14:28:23'),
	(1122, 1, 'en', 'Backend', 'Blog', 'msg', 'HelpSummary', 'Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.', '2017-08-31 14:28:23'),
	(1123, 1, 'en', 'Backend', 'Blog', 'msg', 'HelpWordpress', 'Hier kan je een export bestand vanuit een wordpress site uploaden.', '2017-08-31 14:28:23'),
	(1124, 1, 'en', 'Backend', 'Blog', 'msg', 'HelpWordpressFilter', 'De zoekterm die in bestaande blogposts in een link voor moet komen, alvorens wij de link kunnen omzetten naar een actieve link op de fork blog module.', '2017-08-31 14:28:23'),
	(1125, 1, 'en', 'Backend', 'Blog', 'msg', 'NoCategoryItems', 'There are no categories yet. <a href=\"%1$s\">Create the first category</a>.', '2017-08-31 14:28:23'),
	(1126, 1, 'en', 'Backend', 'Blog', 'err', 'NoFromSelected', 'No origin selected.', '2017-08-31 14:28:23'),
	(1127, 1, 'en', 'Backend', 'Blog', 'msg', 'NoItems', 'There are no articles yet. <a href=\"%1$s\">Write the first article</a>.', '2017-08-31 14:28:23'),
	(1128, 1, 'en', 'Backend', 'Blog', 'msg', 'NotifyByEmailOnNewComment', 'Notify by email when there is a new comment.', '2017-08-31 14:28:23'),
	(1129, 1, 'en', 'Backend', 'Blog', 'msg', 'NotifyByEmailOnNewCommentToModerate', 'Notify by email when there is a new comment to moderate.', '2017-08-31 14:28:23'),
	(1130, 1, 'en', 'Backend', 'Blog', 'msg', 'NumItemsInRecentArticlesFull', 'Number of articles in the recent articles (full) widget', '2017-08-31 14:28:23'),
	(1131, 1, 'en', 'Backend', 'Blog', 'msg', 'NumItemsInRecentArticlesList', 'Number of articles in the recent articles (list) widget', '2017-08-31 14:28:23'),
	(1132, 1, 'en', 'Backend', 'Blog', 'msg', 'ShowImageForm', 'The user can upload a file.', '2017-08-31 14:28:23'),
	(1133, 1, 'en', 'Backend', 'Blog', 'msg', 'ShowOnlyItemsInCategory', 'Show only articles for:', '2017-08-31 14:28:23'),
	(1134, 1, 'en', 'Backend', 'Blog', 'msg', 'NoSpam', 'There is no spam yet.', '2017-08-31 14:28:23'),
	(1135, 1, 'en', 'Backend', 'Blog', 'err', 'DeleteCategoryNotAllowed', 'It is not allowed to delete the category \"%1$s\".', '2017-08-31 14:28:23'),
	(1136, 1, 'en', 'Backend', 'Blog', 'err', 'RSSDescription', 'Blog RSS description is not yet provided. <a href=\"%1$s\">Configure</a>', '2017-08-31 14:28:23'),
	(1137, 1, 'en', 'Backend', 'Blog', 'err', 'XMLFilesOnly', 'Only XML files can be uploaded.', '2017-08-31 14:28:23'),
	(1138, 1, 'en', 'Backend', 'Faq', 'lbl', 'Add', 'add question', '2017-08-31 14:28:23'),
	(1139, 1, 'en', 'Backend', 'Faq', 'lbl', 'AllCategories', 'all categories', '2017-08-31 14:28:23'),
	(1140, 1, 'en', 'Backend', 'Faq', 'lbl', 'AddQuestion', 'add question', '2017-08-31 14:28:23'),
	(1141, 1, 'en', 'Backend', 'Faq', 'lbl', 'AllowFeedback', 'allow feedback', '2017-08-31 14:28:23'),
	(1142, 1, 'en', 'Backend', 'Faq', 'lbl', 'AllowMultipleCategories', 'multiple categories allowed', '2017-08-31 14:28:23'),
	(1143, 1, 'en', 'Backend', 'Faq', 'lbl', 'AllowOwnQuestion', 'allow user questions', '2017-08-31 14:28:23'),
	(1144, 1, 'en', 'Backend', 'Faq', 'lbl', 'Answer', 'answer', '2017-08-31 14:28:23'),
	(1145, 1, 'en', 'Backend', 'Faq', 'lbl', 'Feedback', 'feedback', '2017-08-31 14:28:23'),
	(1146, 1, 'en', 'Backend', 'Faq', 'lbl', 'ItemsPerCategory', 'items per category', '2017-08-31 14:28:23'),
	(1147, 1, 'en', 'Backend', 'Faq', 'lbl', 'Process', 'process', '2017-08-31 14:28:23'),
	(1148, 1, 'en', 'Backend', 'Faq', 'lbl', 'Question', 'question', '2017-08-31 14:28:23'),
	(1149, 1, 'en', 'Backend', 'Faq', 'lbl', 'Questions', 'questions', '2017-08-31 14:28:23'),
	(1150, 1, 'en', 'Backend', 'Faq', 'lbl', 'SendEmailOnNewFeedback', 'send an email if there is new feedback', '2017-08-31 14:28:23'),
	(1151, 1, 'en', 'Backend', 'Faq', 'err', 'AnswerIsRequired', 'The answer is required.', '2017-08-31 14:28:23'),
	(1152, 1, 'en', 'Backend', 'Faq', 'err', 'CategoryIsRequired', 'Please select a category.', '2017-08-31 14:28:23'),
	(1153, 1, 'en', 'Backend', 'Faq', 'msg', 'ConfirmDeleteFeedback', 'Are you sure you want to delete this feedback?', '2017-08-31 14:28:23'),
	(1154, 1, 'en', 'Backend', 'Faq', 'err', 'DeleteCategoryNotAllowed', 'It is not allowed to delete the category \"%1$s\".', '2017-08-31 14:28:23'),
	(1155, 1, 'en', 'Backend', 'Faq', 'err', 'OnlyOneCategoryAllowed', 'The use of multiple categories is not allowed.', '2017-08-31 14:28:23'),
	(1156, 1, 'en', 'Backend', 'Faq', 'err', 'QuestionIsRequired', 'The question is required.', '2017-08-31 14:28:23'),
	(1157, 1, 'en', 'Backend', 'Faq', 'msg', 'EditCategory', 'Edit category \"%1$s\"', '2017-08-31 14:28:23'),
	(1158, 1, 'en', 'Backend', 'Faq', 'msg', 'EditQuestion', 'Edit question \"%1$s', '2017-08-31 14:28:23'),
	(1159, 1, 'en', 'Backend', 'Faq', 'msg', 'FeedbackInfo', 'These are all the messages from visitors who did\'t find this answer useful.', '2017-08-31 14:28:23'),
	(1160, 1, 'en', 'Backend', 'Faq', 'msg', 'HelpSpamFilter', 'Enable the built-in spamfilter (Akismet) to help avoid spam in feedback and user questions.', '2017-08-31 14:28:23'),
	(1161, 1, 'en', 'Backend', 'Faq', 'msg', 'NoCategories', 'There are no categories yet', '2017-08-31 14:28:23'),
	(1162, 1, 'en', 'Backend', 'Faq', 'msg', 'NoFeedbackItems', 'There are no feedback comments at the moment.', '2017-08-31 14:28:23'),
	(1163, 1, 'en', 'Backend', 'Faq', 'msg', 'NoQuestionInCategory', 'There are no questions in this category.', '2017-08-31 14:28:23'),
	(1164, 1, 'en', 'Backend', 'Faq', 'msg', 'NumMostReadItems', 'Number of most read items', '2017-08-31 14:28:23'),
	(1165, 1, 'en', 'Backend', 'Faq', 'msg', 'NumRelatedItems', 'Number of related items', '2017-08-31 14:28:23'),
	(1166, 1, 'en', 'Backend', 'Faq', 'msg', 'Processed', 'The feedback is processed.', '2017-08-31 14:28:23'),
	(1167, 1, 'en', 'Frontend', 'Core', 'lbl', 'AllCategories', 'all categories', '2017-08-31 14:28:23'),
	(1168, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Add', 'add', '2017-08-31 14:28:23'),
	(1169, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'AddFields', 'add fields', '2017-08-31 14:28:23'),
	(1170, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'BackToData', 'back to submissions', '2017-08-31 14:28:23'),
	(1171, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Basic', 'basic', '2017-08-31 14:28:23'),
	(1172, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Checkbox', 'checkbox', '2017-08-31 14:28:23'),
	(1173, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'DefaultValue', 'default value', '2017-08-31 14:28:23'),
	(1174, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Details', 'details', '2017-08-31 14:28:23'),
	(1175, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Drag', 'move', '2017-08-31 14:28:23'),
	(1176, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Dropdown', 'dropdown', '2017-08-31 14:28:23'),
	(1177, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'EditForm', 'edit form \"%1$s\"', '2017-08-31 14:28:23'),
	(1178, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'ErrorMessage', 'error mesage', '2017-08-31 14:28:23'),
	(1179, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Extra', 'extra', '2017-08-31 14:28:23'),
	(1180, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Fields', 'fields', '2017-08-31 14:28:23'),
	(1181, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'FormData', 'submissions for \"%1$s\"', '2017-08-31 14:28:23'),
	(1182, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'FormElements', 'form elements', '2017-08-31 14:28:23'),
	(1183, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Heading', 'heading', '2017-08-31 14:28:23'),
	(1184, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Identifier', 'identifier', '2017-08-31 14:28:23'),
	(1185, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Method', 'method', '2017-08-31 14:28:23'),
	(1186, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'MethodDatabase', 'save in the database', '2017-08-31 14:28:23'),
	(1187, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'MethodDatabaseEmail', 'save in the database and send email', '2017-08-31 14:28:23'),
	(1188, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'MethodEmail', 'send email', '2017-08-31 14:28:23'),
	(1189, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'MinutesAgo', '%1$s minutes ago', '2017-08-31 14:28:23'),
	(1190, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Numeric', 'numeric', '2017-08-31 14:28:23'),
	(1191, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'OneMinuteAgo', '1 minute ago', '2017-08-31 14:28:23'),
	(1192, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'OneSecondAgo', '1 second ago', '2017-08-31 14:28:23'),
	(1193, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Paragraph', 'paragraph', '2017-08-31 14:28:23'),
	(1194, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Parameter', 'parameter', '2017-08-31 14:28:23'),
	(1195, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Placeholder', 'placeholder', '2017-08-31 14:28:23'),
	(1196, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Classname', 'CSS class', '2017-08-31 14:28:23'),
	(1197, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Preview', 'preview', '2017-08-31 14:28:23'),
	(1198, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Properties', 'properties', '2017-08-31 14:28:23'),
	(1199, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Radiobutton', 'radiobutton', '2017-08-31 14:28:23'),
	(1200, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Recaptcha', 'reCAPTCHA', '2017-08-31 14:28:23'),
	(1201, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Recipient', 'recipient', '2017-08-31 14:28:23'),
	(1202, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'ReplyTo', 'reply to', '2017-08-31 14:28:23'),
	(1203, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Required', 'required', '2017-08-31 14:28:23'),
	(1204, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SecondsAgo', '%1$s seconds ago', '2017-08-31 14:28:23'),
	(1205, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SenderInformation', 'sender information', '2017-08-31 14:28:23'),
	(1206, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SentOn', 'sent on', '2017-08-31 14:28:23'),
	(1207, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SessionId', 'session id', '2017-08-31 14:28:23'),
	(1208, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SubmitButton', 'send button', '2017-08-31 14:28:23'),
	(1209, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SuccessMessage', 'success message', '2017-08-31 14:28:23'),
	(1210, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Textarea', 'textarea', '2017-08-31 14:28:23'),
	(1211, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Textbox', 'textbox', '2017-08-31 14:28:23'),
	(1212, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Datetime', 'date & time', '2017-08-31 14:28:23'),
	(1213, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Time', 'time', '2017-08-31 14:28:23'),
	(1214, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'TextElements', 'text elements', '2017-08-31 14:28:23'),
	(1215, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Today', 'today', '2017-08-31 14:28:23'),
	(1216, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Validation', 'validation', '2017-08-31 14:28:23'),
	(1217, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Values', 'values', '2017-08-31 14:28:23'),
	(1218, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Yesterday', 'yesterday', '2017-08-31 14:28:23'),
	(1219, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Day', 'day', '2017-08-31 14:28:23'),
	(1220, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Week', 'week', '2017-08-31 14:28:23'),
	(1221, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Month', 'month', '2017-08-31 14:28:23'),
	(1222, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'Year', 'year', '2017-08-31 14:28:23'),
	(1223, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'Added', 'The form \"%1$s\" was added.', '2017-08-31 14:28:23'),
	(1224, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the form \"%1$s\" and all its submissons?', '2017-08-31 14:28:23'),
	(1225, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'ConfirmDeleteData', 'Are you sure you want to delete this submission?', '2017-08-31 14:28:23'),
	(1226, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'Deleted', 'The form \"%1$s\" was removed.', '2017-08-31 14:28:23'),
	(1227, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'Edited', 'The form \"%1$s\" was saved.', '2017-08-31 14:28:23'),
	(1228, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'HelpIdentifier', 'The identifier is placed in the URL after successfully submitting a form.', '2017-08-31 14:28:23'),
	(1229, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'HelpReplyTo', 'Use the value in this field as \'reply to\' emailaddress', '2017-08-31 14:28:23'),
	(1230, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'ImportantImmediateUpdate', '<strong>Important</strong>: modifications made here are immediately saved.', '2017-08-31 14:28:23'),
	(1231, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'ItemDeleted', 'Submission removed.', '2017-08-31 14:28:23'),
	(1232, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'ItemsDeleted', 'Submissions removed.', '2017-08-31 14:28:23'),
	(1233, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'NoData', 'There are no submissions yet.', '2017-08-31 14:28:23'),
	(1234, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'NoFields', 'There are no fields yet.', '2017-08-31 14:28:23'),
	(1235, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'NoItems', 'There are no forms yet.', '2017-08-31 14:28:23'),
	(1236, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'NoValues', 'There are no values yet.', '2017-08-31 14:28:23'),
	(1237, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'OneSentForm', '1 submission', '2017-08-31 14:28:23'),
	(1238, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'RecaptchaKeysMissing', '<strong>Important</strong>: One or both reCAPTCHA keys are missing. Add them on the <a href=\"%1$s\">settings page</a>', '2017-08-31 14:28:23'),
	(1239, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'SentForms', '%1$s submissions', '2017-08-31 14:28:23'),
	(1240, 1, 'en', 'Backend', 'FormBuilder', 'msg', 'TimeAndDate', 'Time and date', '2017-08-31 14:28:23'),
	(1241, 1, 'en', 'Backend', 'FormBuilder', 'err', 'ErrorMessageIsRequired', 'Please provide an error message.', '2017-08-31 14:28:23'),
	(1242, 1, 'en', 'Backend', 'FormBuilder', 'err', 'IdentifierExists', 'This identifier already exists.', '2017-08-31 14:28:23'),
	(1243, 1, 'en', 'Backend', 'FormBuilder', 'err', 'InvalidIdentifier', 'Please provide a valid identifier. (only . - _ and alphanumeric characters)', '2017-08-31 14:28:23'),
	(1244, 1, 'en', 'Backend', 'FormBuilder', 'err', 'LabelIsRequired', 'Please provide a label.', '2017-08-31 14:28:23'),
	(1245, 1, 'en', 'Backend', 'FormBuilder', 'err', 'SuccessMessageIsRequired', 'Please provide a success message.', '2017-08-31 14:28:23'),
	(1246, 1, 'en', 'Backend', 'FormBuilder', 'err', 'UniqueIdentifier', 'This identifier is already in use.', '2017-08-31 14:28:23'),
	(1247, 1, 'en', 'Backend', 'FormBuilder', 'err', 'ValueIsRequired', 'Please provide a value.', '2017-08-31 14:28:23'),
	(1248, 1, 'en', 'Backend', 'FormBuilder', 'err', 'EmailValidationIsRequired', 'Email validation is required when using this field as \'reply to\' emailaddress.', '2017-08-31 14:28:23'),
	(1249, 1, 'en', 'Backend', 'FormBuilder', 'err', 'ActivateEmailValidationToUseThisOption', 'Activate email validation to use this option.', '2017-08-31 14:28:23'),
	(1250, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'SendConfirmationMailTo', 'send confirmation mail to', '2017-08-31 14:28:23'),
	(1251, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'EmailSubject', 'email subject', '2017-08-31 14:28:23'),
	(1252, 1, 'en', 'Backend', 'FormBuilder', 'lbl', 'ConfirmationMailSubject', 'subject confirmation mail', '2017-08-31 14:28:23'),
	(1253, 1, 'en', 'Frontend', 'Core', 'err', 'TimeIsInvalid', 'Invalid time', '2017-08-31 14:28:23'),
	(1254, 1, 'en', 'Frontend', 'Core', 'msg', 'YouJustSentTheFollowingMessage', 'You just sent the following message.', '2017-08-31 14:28:23'),
	(1255, 1, 'en', 'Backend', 'Location', 'err', 'AddressCouldNotBeGeocoded', 'Address couldn\'t be converted into coordinates.', '2017-08-31 14:28:24'),
	(1256, 1, 'en', 'Backend', 'Location', 'lbl', 'AddToMap', 'add to map', '2017-08-31 14:28:24'),
	(1257, 1, 'en', 'Backend', 'Location', 'lbl', 'Auto', 'automatic', '2017-08-31 14:28:24'),
	(1258, 1, 'en', 'Backend', 'Location', 'lbl', 'Blue', 'blue', '2017-08-31 14:28:24'),
	(1259, 1, 'en', 'Backend', 'Location', 'lbl', 'Custom', 'custom', '2017-08-31 14:28:24'),
	(1260, 1, 'en', 'Backend', 'Location', 'lbl', 'Gray', 'gray', '2017-08-31 14:28:24'),
	(1261, 1, 'en', 'Backend', 'Location', 'lbl', 'Hybrid', 'hybrid', '2017-08-31 14:28:24'),
	(1262, 1, 'en', 'Backend', 'Location', 'lbl', 'Map', 'map', '2017-08-31 14:28:24'),
	(1263, 1, 'en', 'Backend', 'Location', 'lbl', 'MapStyle', 'map style', '2017-08-31 14:28:24'),
	(1264, 1, 'en', 'Backend', 'Location', 'lbl', 'MapType', 'map type', '2017-08-31 14:28:24'),
	(1265, 1, 'en', 'Backend', 'Location', 'lbl', 'Roadmap', 'road map', '2017-08-31 14:28:24'),
	(1266, 1, 'en', 'Backend', 'Location', 'lbl', 'Satellite', 'satellite', '2017-08-31 14:28:24'),
	(1267, 1, 'en', 'Backend', 'Location', 'lbl', 'StreetView', 'street view', '2017-08-31 14:28:24'),
	(1268, 1, 'en', 'Backend', 'Location', 'lbl', 'Terrain', 'terrain', '2017-08-31 14:28:24'),
	(1269, 1, 'en', 'Backend', 'Location', 'lbl', 'UpdateMap', 'update map', '2017-08-31 14:28:24'),
	(1270, 1, 'en', 'Backend', 'Location', 'lbl', 'ZoomLevel', 'zoom level', '2017-08-31 14:28:24'),
	(1271, 1, 'en', 'Backend', 'Location', 'msg', 'HeightHelp', 'Minimum %1$spx', '2017-08-31 14:28:24'),
	(1272, 1, 'en', 'Backend', 'Location', 'msg', 'MapSaved', 'The map is saved.', '2017-08-31 14:28:24'),
	(1273, 1, 'en', 'Backend', 'Location', 'msg', 'ShowDirections', 'Show directions', '2017-08-31 14:28:24'),
	(1274, 1, 'en', 'Backend', 'Location', 'msg', 'ShowMapUrl', 'Show the link to Google Maps', '2017-08-31 14:28:24'),
	(1275, 1, 'en', 'Backend', 'Location', 'msg', 'ShowMarkerOverview', 'Show the marker on the large map', '2017-08-31 14:28:24'),
	(1276, 1, 'en', 'Backend', 'Location', 'msg', 'WidthHelp', 'Minimum %1$spx and maximum %2$spx', '2017-08-31 14:28:24'),
	(1278, 1, 'en', 'Backend', 'Mailmotor', 'lbl', 'ApiKey', 'API key', '2017-08-31 14:28:24'),
	(1279, 1, 'en', 'Backend', 'Mailmotor', 'msg', 'DoubleOptIn', 'Enable double opt-in. If checked people who enroll will get a mail where they have to click the confirmation link. This helps to prevent your list from being spammed with fictional email addresses.', '2017-08-31 14:28:24'),
	(1280, 1, 'en', 'Backend', 'Mailmotor', 'lbl', 'ListId', 'list id', '2017-08-31 14:28:24'),
	(1281, 1, 'en', 'Backend', 'Mailmotor', 'lbl', 'MailEngine', 'mail engine', '2017-08-31 14:28:24'),
	(1282, 1, 'en', 'Backend', 'Mailmotor', 'lbl', 'None', 'none', '2017-08-31 14:28:24'),
	(1283, 1, 'en', 'Backend', 'Mailmotor', 'msg', 'OverwriteInterests', 'When somebody re-subscribes, overwrite their current interests.', '2017-08-31 14:28:24'),
	(1284, 1, 'en', 'Backend', 'Mailmotor', 'msg', 'SuccessfulMailEngineApiConnection', 'Successful API Connection', '2017-08-31 14:28:24'),
	(1285, 1, 'en', 'Backend', 'Mailmotor', 'err', 'WrongMailEngineCredentials', 'Wrong mail engine API-key and/or List-ID.', '2017-08-31 14:28:24'),
	(1286, 1, 'en', 'Frontend', 'Core', 'err', 'EmailAlreadySubscribedInMailingList', 'This e-mail address is already subscribed to the newsletter.', '2017-08-31 14:28:24'),
	(1287, 1, 'en', 'Frontend', 'Core', 'err', 'EmailIsAlreadyUnsubscribedInMailingList', 'This e-mail address is already unsubscribed from the newsletter.', '2017-08-31 14:28:24'),
	(1288, 1, 'en', 'Frontend', 'Core', 'err', 'EmailNotExistsInMailingList', 'This e-mail address does not exist in the mailing list.', '2017-08-31 14:28:24'),
	(1289, 1, 'en', 'Frontend', 'Core', 'lbl', 'Interests', 'interests', '2017-08-31 14:28:24'),
	(1290, 1, 'en', 'Frontend', 'Core', 'err', 'MailingListInterestsIsRequired', 'Please select one or more interests.', '2017-08-31 14:28:24'),
	(1291, 1, 'en', 'Frontend', 'Core', 'lbl', 'MailTitleSubscribeSubscriber', 'Please subscribe \"%1$s\" (%2$s) to the mailing list.', '2017-08-31 14:28:24'),
	(1292, 1, 'en', 'Frontend', 'Core', 'lbl', 'MailTitleUnsubscribeSubscriber', 'Please unsubscribe \"%1$s\" (%2$s) from the mailing list.', '2017-08-31 14:28:24'),
	(1293, 1, 'en', 'Frontend', 'Core', 'lbl', 'Newsletter', 'newsletter', '2017-08-31 14:28:24'),
	(1294, 1, 'en', 'Frontend', 'Core', 'lbl', 'SubscribeToNewsletter', 'subscribe to newsletter', '2017-08-31 14:28:24'),
	(1295, 1, 'en', 'Frontend', 'Core', 'msg', 'SubscribeSuccessForDoubleOptIn', '<strong>CHECK YOUR MAILBOX TO CONFIRM YOUR MAIL ADDRESS</strong><br/>Within 10 minutes you will receive a mail from us.<br/>You need to click the confirmation link to confirm your subscription.<br/>Otherwise your mail address will not be added to our mailing list.<br/> Don\'t forget to check your SPAM-box.', '2017-08-31 14:28:24'),
	(1296, 1, 'en', 'Frontend', 'Core', 'lbl', 'UnsubscribeFromNewsletter', 'unsubscribe from newsletter', '2017-08-31 14:28:24'),
	(1297, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaGalleries', 'media galleries', '2017-08-31 14:28:24'),
	(1298, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'Action', 'view', '2017-08-31 14:28:24'),
	(1299, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'Connected', 'connected', '2017-08-31 14:28:24'),
	(1300, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'Galleries', 'galleries', '2017-08-31 14:28:24'),
	(1301, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'Gallery', 'gallery', '2017-08-31 14:28:24'),
	(1302, 1, 'en', 'Backend', 'MediaGalleries', 'err', 'GroupTypeNotExisting', 'Can\'t create a gallery with this type.', '2017-08-31 14:28:24'),
	(1303, 1, 'en', 'Backend', 'MediaGalleries', 'err', 'InvalidWidgetAction', 'Invalid widget action.', '2017-08-31 14:28:24'),
	(1304, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'MediaGalleryAdd', 'add gallery', '2017-08-31 14:28:24'),
	(1305, 1, 'en', 'Backend', 'MediaGalleries', 'msg', 'MediaGalleryAdded', 'The gallery \"%1$s\" was added.', '2017-08-31 14:28:24'),
	(1306, 1, 'en', 'Backend', 'MediaGalleries', 'msg', 'MediaGalleryDeleted', 'Deleted media gallery %1$s\" successfull.', '2017-08-31 14:28:24'),
	(1307, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'MediaGalleryEdit', 'edit gallery', '2017-08-31 14:28:24'),
	(1308, 1, 'en', 'Backend', 'MediaGalleries', 'msg', 'MediaGalleryEdited', 'The gallery \"%1$s\" has been saved.', '2017-08-31 14:28:24'),
	(1309, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'MediaGalleryIndex', 'overview', '2017-08-31 14:28:24'),
	(1310, 1, 'en', 'Backend', 'MediaGalleries', 'err', 'MediaLibraryModuleRequired', 'You must <a href=\'%1$s\'>install the MediaLibrary module</a> for this MediaGalleries module.', '2017-08-31 14:28:24'),
	(1311, 1, 'en', 'Backend', 'MediaGalleries', 'err', 'NonExistingMediaGallery', 'Media gallery does not exist.', '2017-08-31 14:28:24'),
	(1312, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'SaveAndEdit', 'save and edit', '2017-08-31 14:28:24'),
	(1313, 1, 'en', 'Backend', 'MediaGalleries', 'lbl', 'WidgetView', 'view', '2017-08-31 14:28:24'),
	(1314, 1, 'en', 'Backend', 'MediaGalleries', 'msg', 'WidgetViewsHelp', 'As a developer, you can add your own custom widgets by adding them in the folder /src/Frontend/Modules/MediaLibrary/Widgets/. Each widget that you add gets added in this dropdown automatically and can have its own image sizes in the Frontend.', '2017-08-31 14:28:24'),
	(1315, 1, 'en', 'Backend', 'Core', 'lbl', 'AddMediaItems', 'add media', '2017-08-31 14:28:25'),
	(1316, 1, 'en', 'Backend', 'Core', 'msg', 'ChooseTypeForNewGroup', 'with the following type:', '2017-08-31 14:28:25'),
	(1317, 1, 'en', 'Backend', 'Core', 'lbl', 'DropFilesHere', 'drop files here', '2017-08-31 14:28:25'),
	(1318, 1, 'en', 'Backend', 'Core', 'msg', 'FolderIsAdded', 'The new folder is added.', '2017-08-31 14:28:25'),
	(1319, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaAddFolder', 'add folder', '2017-08-31 14:28:25'),
	(1320, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaAddMovie', 'add movie', '2017-08-31 14:28:25'),
	(1321, 1, 'en', 'Backend', 'Core', 'lbl', 'EnableCropper', 'enable cropper', '2017-08-31 14:28:25'),
	(1322, 1, 'en', 'Backend', 'Core', 'msg', 'TheCropperIsMandatoryBecauseTheImagesNeedToHaveACertainFormat', 'The cropper is mandatory because the images need to have a certain aspect ratio.', '2017-08-31 14:28:25'),
	(1323, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaAudio', 'audio', '2017-08-31 14:28:25'),
	(1324, 1, 'en', 'Backend', 'Core', 'msg', 'MediaChoseToUpload', 'Choose the media to upload.', '2017-08-31 14:28:25'),
	(1325, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaConnect', 'connect media', '2017-08-31 14:28:25'),
	(1326, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaConnectNow', 'connect / upload', '2017-08-31 14:28:25'),
	(1327, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaDisconnect', 'disconnect', '2017-08-31 14:28:25'),
	(1328, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaFiles', 'files', '2017-08-31 14:28:25'),
	(1329, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaFolder', 'folder', '2017-08-31 14:28:25'),
	(1330, 1, 'en', 'Backend', 'Core', 'msg', 'MediaGroupEdited', 'The media is changed correctly.<br/><strong>Attention:</strong> these changes will be used after saving.', '2017-08-31 14:28:25'),
	(1331, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaImages', 'images', '2017-08-31 14:28:25'),
	(1332, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaInTheFolder', 'in the folder', '2017-08-31 14:28:25'),
	(1333, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaItemDelete', 'charging media', '2017-08-31 14:28:25'),
	(1334, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaItemIndex', 'overview', '2017-08-31 14:28:25'),
	(1335, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaItemUpload', 'removing media', '2017-08-31 14:28:25'),
	(1336, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibrary', 'media', '2017-08-31 14:28:25'),
	(1337, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeAll', 'all file types', '2017-08-31 14:28:25'),
	(1338, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeAudio', 'only audio-files', '2017-08-31 14:28:25'),
	(1339, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeFile', 'only files (.pdf, .doc, .docx, ...)', '2017-08-31 14:28:25'),
	(1340, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeImage', 'only images (.jpg, .png, .gif)', '2017-08-31 14:28:25'),
	(1341, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeImageFile', 'only images and files', '2017-08-31 14:28:25'),
	(1342, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeImageMovie', 'only images and movies', '2017-08-31 14:28:25'),
	(1343, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryGroupTypeMovie', 'only movies (.avi, .mov, .mp4)', '2017-08-31 14:28:25'),
	(1344, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaLibraryTab', 'media library', '2017-08-31 14:28:25'),
	(1345, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMovieId', 'movie ID', '2017-08-31 14:28:25'),
	(1346, 1, 'en', 'Backend', 'Core', 'err', 'MediaMovieIdAlreadyExists', 'Movie ID is already inserted.', '2017-08-31 14:28:25'),
	(1347, 1, 'en', 'Backend', 'Core', 'msg', 'MediaMovieIdHelp', 'This is the unique ID for a movie.', '2017-08-31 14:28:25'),
	(1348, 1, 'en', 'Backend', 'Core', 'msg', 'MediaMovieIsAdded', 'Movie id is added.', '2017-08-31 14:28:25'),
	(1349, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMovies', 'movies', '2017-08-31 14:28:25'),
	(1350, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMovieSource', 'source', '2017-08-31 14:28:25'),
	(1351, 1, 'en', 'Backend', 'Core', 'err', 'MediaMovieSourceIsRequired', 'Movie source is a required field.', '2017-08-31 14:28:25'),
	(1352, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMovieTitle', 'movie title', '2017-08-31 14:28:25'),
	(1353, 1, 'en', 'Backend', 'Core', 'err', 'MediaMovieTitleIsRequired', 'Movie title is a required field.', '2017-08-31 14:28:25'),
	(1354, 1, 'en', 'Backend', 'Core', 'err', 'MediaFolderIsRequired', 'Folder is a required field.', '2017-08-31 14:28:25'),
	(1355, 1, 'en', 'Backend', 'Core', 'err', 'GroupIdIsRequired', 'group_id is a required field.', '2017-08-31 14:28:25'),
	(1356, 1, 'en', 'Backend', 'Core', 'err', 'FolderIdIsRequired', 'folder_id is a required field.', '2017-08-31 14:28:25'),
	(1357, 1, 'en', 'Backend', 'Core', 'err', 'MediaMovieIdIsRequired', 'id is a required field.', '2017-08-31 14:28:25'),
	(1358, 1, 'en', 'Backend', 'Core', 'lbl', 'ZoomIn', 'zoom in', '2017-08-31 14:28:25'),
	(1359, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaConnected', 'connected media', '2017-08-31 14:28:25'),
	(1360, 1, 'en', 'Backend', 'Core', 'lbl', 'ZoomOut', 'zoom out', '2017-08-31 14:28:25'),
	(1361, 1, 'en', 'Backend', 'Core', 'err', 'ParentNotExists', 'parent directory not found', '2017-08-31 14:28:25'),
	(1362, 1, 'en', 'Backend', 'Core', 'lbl', 'Reset', 'reset', '2017-08-31 14:28:25'),
	(1363, 1, 'en', 'Backend', 'Core', 'err', 'MovieStorageTypeNotExists', 'Can\'t create a movie with this type.', '2017-08-31 14:28:25'),
	(1364, 1, 'en', 'Backend', 'Core', 'err', 'MediaUploadedError', '%1$s file(s) could not be uploaded.', '2017-08-31 14:28:25'),
	(1365, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMultipleAudio', 'audio', '2017-08-31 14:28:25'),
	(1366, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMultipleFile', 'files', '2017-08-31 14:28:25'),
	(1367, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMultipleImage', 'images', '2017-08-31 14:28:25'),
	(1368, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaMultipleMovie', 'movies', '2017-08-31 14:28:25'),
	(1369, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaNew', 'nieuwe media', '2017-08-31 14:28:25'),
	(1370, 1, 'en', 'Backend', 'Core', 'msg', 'MediaNoItemsConnected', 'You didn\'t have any media connected.', '2017-08-31 14:28:25'),
	(1371, 1, 'en', 'Backend', 'Core', 'msg', 'MediaNoItemsInFolder', 'There is no media in this folder.', '2017-08-31 14:28:25'),
	(1372, 1, 'en', 'Backend', 'Core', 'msg', 'MediaOrAddMediaFolder', 'or add new folder', '2017-08-31 14:28:25'),
	(1373, 1, 'en', 'Backend', 'Core', 'lbl', 'MediaStorageType', 'source', '2017-08-31 14:28:25'),
	(1374, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploaded', 'Uploaded media.', '2017-08-31 14:28:25'),
	(1375, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploadedSuccess', '%1$s file(s) successful uploaded.', '2017-08-31 14:28:25'),
	(1376, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploadedSuccessful', 'Media \"%1$s\" uploaded successful.', '2017-08-31 14:28:25'),
	(1377, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploadThisType', 'What do you want to add?', '2017-08-31 14:28:25'),
	(1378, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploadToThisFolder', 'Upload to this folder:', '2017-08-31 14:28:25'),
	(1379, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploadTypeFiles', 'Images, files', '2017-08-31 14:28:25'),
	(1380, 1, 'en', 'Backend', 'Core', 'msg', 'MediaUploadTypeMovies', 'Online movies (Youtube, Vimeo, ...)', '2017-08-31 14:28:25'),
	(1381, 1, 'en', 'Backend', 'Core', 'msg', 'MediaYouAreHere', 'You are here:', '2017-08-31 14:28:25'),
	(1382, 1, 'en', 'Backend', 'Core', 'msg', 'MediaWhichMovieToAdd', 'Which movie do you want to add?', '2017-08-31 14:28:25'),
	(1383, 1, 'en', 'Backend', 'Core', 'msg', 'MediaWillBeConnected', 'The following media will be connected when pressing \"OK\":', '2017-08-31 14:28:25'),
	(1384, 1, 'en', 'Backend', 'Core', 'lbl', 'Crop', 'crop', '2017-08-31 14:28:25'),
	(1385, 1, 'en', 'Backend', 'Core', 'lbl', 'RotateLeft', 'rotate left', '2017-08-31 14:28:25'),
	(1386, 1, 'en', 'Backend', 'Core', 'lbl', 'RotateRight', 'rotate right', '2017-08-31 14:28:25'),
	(1387, 1, 'en', 'Backend', 'Core', 'lbl', 'CropImage', 'crop image', '2017-08-31 14:28:25'),
	(1388, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Action', 'action class', '2017-08-31 14:28:25'),
	(1389, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'AddMediaFolder', 'add folder', '2017-08-31 14:28:25'),
	(1390, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'AddMediaItems', 'add media', '2017-08-31 14:28:25'),
	(1391, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'AllMedia', 'all media', '2017-08-31 14:28:25'),
	(1392, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'BackToOverview', 'back to overview', '2017-08-31 14:28:25'),
	(1393, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'CleanedUpMediaItems', 'Removed %1$s media items.', '2017-08-31 14:28:25'),
	(1395, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'ConfirmMediaFolderDelete', 'Are you sure you want to delete the folder \"%1$s\"?', '2017-08-31 14:28:25'),
	(1396, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'ConfirmMediaFolderDeleteAndFiles', 'Are you sure you want to delete the folder \"%1$s\" and all its files? This files will be completely removed and you can\'t use them anymore.', '2017-08-31 14:28:25'),
	(1397, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'ConfirmMediaItemDelete', 'Are you sure you want to delete this media item \"%1$s\" and all it connections?', '2017-08-31 14:28:25'),
	(1398, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Delete', 'delete', '2017-08-31 14:28:25'),
	(1399, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'DeletedMediaItem', 'The media item \"%1$s\" and all its connections are deleted.', '2017-08-31 14:28:25'),
	(1400, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'DeletedMediaFolder', 'The folder \"%1$s\" and all its files are deleted.', '2017-08-31 14:28:25'),
	(1401, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'DeletedView', 'View deleted.', '2017-08-31 14:28:25'),
	(1402, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'EditFile', 'edit file \"%1$s\"', '2017-08-31 14:28:25'),
	(1403, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'EditMediaFolder', 'edit folder', '2017-08-31 14:28:25'),
	(1404, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'File', 'file', '2017-08-31 14:28:25'),
	(1405, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'FileTypes', 'file types', '2017-08-31 14:28:25'),
	(1406, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'FileExtensionNotAllowed', 'File extension not allowed.', '2017-08-31 14:28:25'),
	(1407, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'FilesFor', 'in the folder \"%1$s\"', '2017-08-31 14:28:25'),
	(1408, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Folders', 'folders', '2017-08-31 14:28:25'),
	(1409, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Image', 'image', '2017-08-31 14:28:25'),
	(1410, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'InvalidWidgetAction', 'Invalid widget action.', '2017-08-31 14:28:25'),
	(1411, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'LabelIsRequired', 'Title is a required field.', '2017-08-31 14:28:25'),
	(1412, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Library', 'library', '2017-08-31 14:28:25'),
	(1413, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaDeleted', 'Media deleted.', '2017-08-31 14:28:25'),
	(1414, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'MediaFolderDelete', 'Delete folder', '2017-08-31 14:28:25'),
	(1415, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaFolderDeleted', 'Media folder \"%1$s\" deleted.', '2017-08-31 14:28:25'),
	(1416, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'MediaFolderDeleteNotPossible', 'Delete folder not possible because it\'s the last folder.', '2017-08-31 14:28:25'),
	(1417, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'MediaFolderDeleteNotPossibleBecauseOfConnectedMediaItems', 'Delete folder not possible because it contains media that is connected somewhere.', '2017-08-31 14:28:25'),
	(1418, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaDisconnected', 'File disconnected from gallery \"%1$s\".', '2017-08-31 14:28:25'),
	(1419, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'MediaFolderDoesNotExists', 'The folder doesn\'t exists.', '2017-08-31 14:28:25'),
	(1420, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'MediaFolderExists', 'This folder already exists.', '2017-08-31 14:28:25'),
	(1421, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaFolderIsEdited', 'Folder \"%1$s\" edited.', '2017-08-31 14:28:25'),
	(1422, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaFolderMoved', 'Media folder \"%1$s\" moved.', '2017-08-31 14:28:25'),
	(1424, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaItemDeleted', 'Media item \"%1$s\" deleted.', '2017-08-31 14:28:25'),
	(1425, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'MediaItemEdit', 'edit item', '2017-08-31 14:28:25'),
	(1426, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaItemEdited', 'The item \"%1$s\" has been saved.', '2017-08-31 14:28:25'),
	(1427, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MediaMoved', 'Media moved.', '2017-08-31 14:28:25'),
	(1428, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Mime', 'filetype', '2017-08-31 14:28:25'),
	(1429, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'Move', 'move', '2017-08-31 14:28:25'),
	(1430, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'MoveMedia', 'move media', '2017-08-31 14:28:25'),
	(1431, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MoveMediaFoldersNotPossible', 'It\'s not possible to move media folders.', '2017-08-31 14:28:25'),
	(1432, 1, 'en', 'Backend', 'MediaLibrary', 'msg', 'MoveMediaToFolder', 'Move media to this folder:', '2017-08-31 14:28:25'),
	(1433, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'NonExistingMediaFolder', 'Folder does not exist.', '2017-08-31 14:28:25'),
	(1434, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'NumConnected', '# connections', '2017-08-31 14:28:25'),
	(1435, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'PleaseSelectAFolder', 'Please select a folder when trying to move items.', '2017-08-31 14:28:25'),
	(1436, 1, 'en', 'Backend', 'MediaLibrary', 'lbl', 'SaveAndEdit', 'save and edit', '2017-08-31 14:28:25'),
	(1437, 1, 'en', 'Backend', 'MediaLibrary', 'err', 'YouAreRequiredToConnectMedia', 'You are required to connect media.', '2017-08-31 14:28:25'),
	(1438, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Active', 'active', '2017-08-31 14:28:26'),
	(1439, 1, 'en', 'Backend', 'Profiles', 'lbl', 'FilterProfiles', 'filter profiles', '2017-08-31 14:28:26'),
	(1440, 1, 'en', 'Backend', 'Profiles', 'lbl', 'FilterGroups', 'filter groups', '2017-08-31 14:28:26'),
	(1441, 1, 'en', 'Backend', 'Profiles', 'lbl', 'AddGroup', 'add group', '2017-08-31 14:28:26'),
	(1442, 1, 'en', 'Backend', 'Profiles', 'lbl', 'AddMembership', 'add membership', '2017-08-31 14:28:26'),
	(1443, 1, 'en', 'Backend', 'Profiles', 'lbl', 'AddToGroup', 'add to a group', '2017-08-31 14:28:26'),
	(1444, 1, 'en', 'Backend', 'Profiles', 'lbl', 'BirthDate', 'birth date', '2017-08-31 14:28:26'),
	(1445, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Blocked', 'blocked', '2017-08-31 14:28:26'),
	(1446, 1, 'en', 'Backend', 'Profiles', 'lbl', 'City', 'city', '2017-08-31 14:28:26'),
	(1447, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Country', 'country', '2017-08-31 14:28:26'),
	(1448, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Deleted', 'deleted', '2017-08-31 14:28:26'),
	(1449, 1, 'en', 'Backend', 'Profiles', 'lbl', 'DisplayName', 'display name', '2017-08-31 14:28:26'),
	(1450, 1, 'en', 'Backend', 'Profiles', 'lbl', 'EditGroup', 'edit group', '2017-08-31 14:28:26'),
	(1451, 1, 'en', 'Backend', 'Profiles', 'lbl', 'EditMembership', 'edit membership', '2017-08-31 14:28:26'),
	(1452, 1, 'en', 'Backend', 'Profiles', 'err', 'EmailMatchesPrevious', 'Please add a new mail address.', '2017-08-31 14:28:26'),
	(1453, 1, 'en', 'Backend', 'Profiles', 'lbl', 'ExpiresOn', 'expires on', '2017-08-31 14:28:26'),
	(1454, 1, 'en', 'Backend', 'Profiles', 'lbl', 'ExportTemplate', 'download import template', '2017-08-31 14:28:26'),
	(1455, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Female', 'female', '2017-08-31 14:28:26'),
	(1456, 1, 'en', 'Backend', 'Profiles', 'lbl', 'FirstName', 'first name', '2017-08-31 14:28:26'),
	(1457, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Gender', 'gender', '2017-08-31 14:28:26'),
	(1458, 1, 'en', 'Backend', 'Profiles', 'lbl', 'GroupName', 'group name', '2017-08-31 14:28:26'),
	(1459, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Groups', 'groups', '2017-08-31 14:28:26'),
	(1460, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Inactive', 'inactive', '2017-08-31 14:28:26'),
	(1461, 1, 'en', 'Backend', 'Profiles', 'lbl', 'LastName', 'last name', '2017-08-31 14:28:26'),
	(1462, 1, 'en', 'Backend', 'Profiles', 'lbl', 'LoginBox', 'login box', '2017-08-31 14:28:26'),
	(1463, 1, 'en', 'Backend', 'Profiles', 'lbl', 'LoginLink', 'login link', '2017-08-31 14:28:26'),
	(1464, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Male', 'male', '2017-08-31 14:28:26'),
	(1465, 1, 'en', 'Backend', 'Profiles', 'lbl', 'MembersCount', 'members count', '2017-08-31 14:28:26'),
	(1466, 1, 'en', 'Backend', 'Profiles', 'lbl', 'NotificationNewProfileToAdmin', 'New profile for %1$s has been created.', '2017-08-31 14:28:26'),
	(1467, 1, 'en', 'Backend', 'Profiles', 'lbl', 'NotificationNewProfileToProfile', 'Your profile has been added.', '2017-08-31 14:28:26'),
	(1468, 1, 'en', 'Backend', 'Profiles', 'lbl', 'NotificationUpdatedProfileToProfile', 'Your profile has been edited.', '2017-08-31 14:28:26'),
	(1469, 1, 'en', 'Backend', 'Profiles', 'lbl', 'OverwriteProfileNotificationEmail', 'Use a custom mail address.', '2017-08-31 14:28:26'),
	(1470, 1, 'en', 'Backend', 'Profiles', 'lbl', 'UpdateEmail', 'update e-mail', '2017-08-31 14:28:26'),
	(1471, 1, 'en', 'Backend', 'Profiles', 'lbl', 'NewProfileWillBeNotified', 'When saving, this new profile will receive an e-mail with the login credentials.', '2017-08-31 14:28:26'),
	(1472, 1, 'en', 'Backend', 'Profiles', 'lbl', 'RegisteredOn', 'registered on', '2017-08-31 14:28:26'),
	(1473, 1, 'en', 'Backend', 'Profiles', 'lbl', 'SendNewProfileAdminMail', 'Send mail to administrator on new profile.', '2017-08-31 14:28:26'),
	(1474, 1, 'en', 'Backend', 'Profiles', 'lbl', 'SendNewProfileMail', 'Send mail to new profile on creation or when e-mail or password has been changed.', '2017-08-31 14:28:26'),
	(1475, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Unblock', 'unblock', '2017-08-31 14:28:26'),
	(1476, 1, 'en', 'Backend', 'Profiles', 'lbl', 'Undelete', 'undelete', '2017-08-31 14:28:26'),
	(1477, 1, 'en', 'Backend', 'Profiles', 'lbl', 'UpdatedProfileWillBeNotified', 'When you change the mail address or password, this profile will receive an e-mail with the new login credentials.', '2017-08-31 14:28:26'),
	(1478, 1, 'en', 'Backend', 'Profiles', 'lbl', 'YourExistingPassword', 'your existing password', '2017-08-31 14:28:26'),
	(1479, 1, 'en', 'Backend', 'Profiles', 'msg', 'AutoGeneratedPasswordIfEmpty', 'If empty, a password will automatically be generated.', '2017-08-31 14:28:26'),
	(1480, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmBlock', 'Are you sure you want to block \"%1$s\"?', '2017-08-31 14:28:26'),
	(1481, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmDelete', 'Are you sure you want to delete \"%1$s\"?', '2017-08-31 14:28:26'),
	(1482, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmDeleteGroup', 'Are you sure you want to delete the group \"%1$s\"?', '2017-08-31 14:28:26'),
	(1483, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmMassAddToGroup', 'Are you sure you want to add these profiles to the following group?', '2017-08-31 14:28:26'),
	(1484, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmProfileGroupDelete', 'Are you sure you want to delete this profile from the group \"%1$s\"?', '2017-08-31 14:28:26'),
	(1485, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmUnblock', 'Are you sure you want to unblock \"%1$s\"?', '2017-08-31 14:28:26'),
	(1486, 1, 'en', 'Backend', 'Profiles', 'msg', 'ConfirmUndelete', 'Are you sure you want to undelete \"%1$s\"?', '2017-08-31 14:28:26'),
	(1487, 1, 'en', 'Backend', 'Profiles', 'msg', 'EmailIsRequired', 'Please fill in a valid mail address.', '2017-08-31 14:28:26'),
	(1488, 1, 'en', 'Backend', 'Profiles', 'msg', 'EditProfile', 'editing profile from \"%1$s\"', '2017-08-31 14:28:26'),
	(1489, 1, 'en', 'Backend', 'Profiles', 'msg', 'GroupAdded', 'The group was added.', '2017-08-31 14:28:26'),
	(1490, 1, 'en', 'Backend', 'Profiles', 'msg', 'GroupSaved', 'The group was saved.', '2017-08-31 14:28:26'),
	(1491, 1, 'en', 'Backend', 'Profiles', 'msg', 'MembershipAdded', 'The group membership was added.', '2017-08-31 14:28:26'),
	(1492, 1, 'en', 'Backend', 'Profiles', 'msg', 'MembershipDeleted', 'The group membership was deleted.', '2017-08-31 14:28:26'),
	(1493, 1, 'en', 'Backend', 'Profiles', 'msg', 'MembershipSaved', 'The group membership was saved.', '2017-08-31 14:28:26'),
	(1494, 1, 'en', 'Backend', 'Profiles', 'msg', 'NoGroups', 'There are no groups yet.', '2017-08-31 14:28:26'),
	(1495, 1, 'en', 'Backend', 'Profiles', 'msg', 'NotificationNewProfileLoginCredentials', '<p>Dear,</p><p>from now on you can log in to our website with the following information: </p><p>Email: %1$s<br/>Password: %2$s</p><p>Log in on <a href=\"%3$s/fr\">%3$s</a></p>', '2017-08-31 14:28:26'),
	(1496, 1, 'en', 'Backend', 'Profiles', 'msg', 'NotificationNewProfileToAdmin', 'Dear,<br/><br/>a new profile has been added:<br/><a href=\"%3$s\" title=\"Click to edit\">%1$s - %2$s</a>', '2017-08-31 14:28:26'),
	(1497, 1, 'en', 'Backend', 'Profiles', 'msg', 'OverwriteExisting', 'Overwrite existing profiles?', '2017-08-31 14:28:26'),
	(1498, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfileAddedToGroup', 'The profile was added to the group.', '2017-08-31 14:28:26'),
	(1499, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfileBlocked', 'The profile \"%1$s\" was blocked.', '2017-08-31 14:28:26'),
	(1500, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfileDeleted', 'The profile was deleted.', '2017-08-31 14:28:26'),
	(1501, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfilesAddedToGroup', 'The profiles are added to the group.', '2017-08-31 14:28:26'),
	(1502, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfilesImported', 'There have been added %1$s profiles, %2$s already existed and are not updated.', '2017-08-31 14:28:26'),
	(1503, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfilesImportedAndUpdated', 'There have been added %1$s profiles, %2$s already existed and are updated.', '2017-08-31 14:28:26'),
	(1504, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfileUnblocked', 'The profile \"%1$s\" was unblocked.', '2017-08-31 14:28:26'),
	(1505, 1, 'en', 'Backend', 'Profiles', 'msg', 'ProfileUndeleted', 'The profile \"%1$s\" was undeleted.', '2017-08-31 14:28:26'),
	(1506, 1, 'en', 'Backend', 'Profiles', 'msg', 'Saved', 'The profile \"%1$s\" was saved.', '2017-08-31 14:28:26'),
	(1507, 1, 'en', 'Backend', 'Profiles', 'msg', 'SavedAndNotified', 'The profile \"%1$s\" was saved and notified by mail.', '2017-08-31 14:28:26'),
	(1508, 1, 'en', 'Backend', 'Profiles', 'msg', 'SavedSettings', 'Profile settings were saved.', '2017-08-31 14:28:26'),
	(1509, 1, 'en', 'Backend', 'Profiles', 'err', 'DisplayNameExists', 'This display name is in use.', '2017-08-31 14:28:26'),
	(1510, 1, 'en', 'Backend', 'Profiles', 'err', 'DisplayNameIsRequired', 'Display name is a required field.', '2017-08-31 14:28:26'),
	(1511, 1, 'en', 'Backend', 'Profiles', 'err', 'EmailExists', 'This e-mailaddress is in use.', '2017-08-31 14:28:26'),
	(1512, 1, 'en', 'Backend', 'Profiles', 'err', 'GroupIsRequired', 'Group is a required field.', '2017-08-31 14:28:26'),
	(1513, 1, 'en', 'Backend', 'Profiles', 'err', 'GroupNameExists', 'This group name is in use.', '2017-08-31 14:28:26'),
	(1514, 1, 'en', 'Backend', 'Profiles', 'err', 'NoGroupSelected', 'You must select a group to perfom this action.', '2017-08-31 14:28:26'),
	(1515, 1, 'en', 'Backend', 'Profiles', 'err', 'NoProfilesSelected', 'You must select minimum 1 profile to perfom this action.', '2017-08-31 14:28:26'),
	(1516, 1, 'en', 'Backend', 'Profiles', 'err', 'UnknownAction', 'Unknown action.', '2017-08-31 14:28:26'),
	(1517, 1, 'en', 'Backend', 'Core', 'lbl', 'LoginLink', 'login link', '2017-08-31 14:28:26'),
	(1518, 1, 'en', 'Backend', 'Core', 'lbl', 'SecurePage', 'secure page', '2017-08-31 14:28:26'),
	(1519, 1, 'nl', 'Backend', 'Core', 'lbl', 'MoveUpOnePosition', 'verplaats één positie omhoog', '2017-08-31 14:28:26'),
	(1520, 1, 'en', 'Backend', 'Core', 'lbl', 'MoveUpOnePosition', 'move up one position', '2017-08-31 14:28:26'),
	(1521, 1, 'nl', 'Backend', 'Core', 'lbl', 'MoveDownOnePosition', 'verplaats één positie omlaag', '2017-08-31 14:28:26'),
	(1522, 1, 'en', 'Backend', 'Core', 'lbl', 'MoveDownOnePosition', 'move down one position', '2017-08-31 14:28:26'),
	(1523, 1, 'en', 'Backend', 'Core', 'lbl', 'Close', 'close', '2017-08-31 14:28:26'),
    (1524, 1, 'en', 'Backend', 'Pages', 'lbl', 'MoveThisPage', 'move this page', '2017-08-31 14:28:26'),
    (1525, 1, 'en', 'Backend', 'Pages', 'lbl', 'MovePageTree', 'navigation tree', '2017-08-31 14:28:26'),
    (1526, 1, 'en', 'Backend', 'Pages', 'lbl', 'MovePageReferencePage', 'reference page', '2017-08-31 14:28:26'),
    (1527, 1, 'en', 'Backend', 'Pages', 'lbl', 'MovePageType', 'action with respect to the reference page', '2017-08-31 14:28:26'),
    (1528, 1, 'en', 'Backend', 'Pages', 'lbl', 'AppendToTree', 'append to navigation tree', '2017-08-31 14:28:26'),
    (1529, 1, 'en', 'Backend', 'Pages', 'lbl', 'BeforePage', 'add before reference page', '2017-08-31 14:28:26'),
    (1530, 1, 'en', 'Backend', 'Pages', 'lbl', 'InsidePage', 'add as subpage of the reference page', '2017-08-31 14:28:26'),
    (1531, 1, 'en', 'Backend', 'Pages', 'lbl', 'AfterPage', 'add after reference page', '2017-08-31 14:28:26');

/*!40000 ALTER TABLE `locale` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table location
# ------------------------------------------------------------

DROP TABLE IF EXISTS `location`;

CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  `show_overview` tinyint(1) NOT NULL DEFAULT '1',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table location_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `location_settings`;

CREATE TABLE `location_settings` (
  `map_id` int(11) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`map_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `meta`;

CREATE TABLE `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_overwrite` tinyint(1) NOT NULL DEFAULT '0',
  `custom` longtext COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `seo_follow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:seo_follow)',
  `seo_index` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:seo_index)',
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `meta` WRITE;
/*!40000 ALTER TABLE `meta` DISABLE KEYS */;

INSERT INTO `meta` (`id`, `keywords`, `keywords_overwrite`, `description`, `description_overwrite`, `title`, `title_overwrite`, `url`, `url_overwrite`, `custom`, `data`, `seo_follow`, `seo_index`)
VALUES
	(1,'Home',0,'Home',0,'Home',0,'home',0,NULL,NULL,NULL,NULL),
	(2,'Sitemap',0,'Sitemap',0,'Sitemap',0,'sitemap',0,NULL,NULL,NULL,NULL),
	(3,'Disclaimer',0,'Disclaimer',0,'Disclaimer',0,'disclaimer',0,NULL,'a:2:{s:9:\"seo_index\";s:7:\"noindex\";s:10:\"seo_follow\";s:8:\"nofollow\";}','nofollow','noindex'),
	(4,'404',0,'404',0,'404',0,'404',0,NULL,NULL,NULL,NULL),
	(5,'Search',0,'Search',0,'Search',0,'search',0,NULL,NULL,NULL,NULL),
	(6,'Tags',0,'Tags',0,'Tags',0,'tags',0,NULL,NULL,NULL,NULL),
	(8,'Blog',0,'Blog',0,'Blog',0,'blog',0,NULL,NULL,NULL,NULL),
	(10,'FAQ',0,'FAQ',0,'FAQ',0,'faq',0,NULL,NULL,NULL,NULL),
	(11,'Contact',0,'Contact',0,'Contact',0,'contact',0,NULL,NULL,NULL,NULL),
	(12,'Sent mailings',0,'Sent mailings',0,'Sent mailings',0,'sent-mailings',0,NULL,NULL,NULL,NULL),
	(13,'Subscribe',0,'Subscribe',0,'Subscribe',0,'subscribe',0,NULL,NULL,NULL,NULL),
	(14,'Unsubscribe',0,'Unsubscribe',0,'Unsubscribe',0,'unsubscribe',0,NULL,NULL,NULL,NULL),
	(15,'Activate',0,'Activate',0,'Activate',0,'activate',0,NULL,NULL,NULL,NULL),
	(16,'Forgot password',0,'Forgot password',0,'Forgot password',0,'forgot-password',0,NULL,NULL,NULL,NULL),
	(17,'Reset password',0,'Reset password',0,'Reset password',0,'reset-password',0,NULL,NULL,NULL,NULL),
	(18,'Resend activation e-mail',0,'Resend activation e-mail',0,'Resend activation e-mail',0,'resend-activation-e-mail',0,NULL,NULL,NULL,NULL),
	(19,'Login',0,'Login',0,'Login',0,'login',0,NULL,NULL,NULL,NULL),
	(20,'Register',0,'Register',0,'Register',0,'register',0,NULL,NULL,NULL,NULL),
	(21,'Logout',0,'Logout',0,'Logout',0,'logout',0,NULL,NULL,NULL,NULL),
	(22,'Profile',0,'Profile',0,'Profile',0,'profile',0,NULL,NULL,NULL,NULL),
	(23,'Profile settings',0,'Profile settings',0,'Profile settings',0,'profile-settings',0,NULL,NULL,NULL,NULL),
	(24,'Change email',0,'Change email',0,'Change email',0,'change-email',0,NULL,NULL,NULL,NULL),
	(25,'Change password',0,'Change password',0,'Change password',0,'change-password',0,NULL,NULL,NULL,NULL),
	(26,'BlogCategory for tests',0,'BlogCategory for tests',0,'BlogCategory for tests',0,'blogcategory-for-tests',0,NULL,NULL,NULL,NULL),
	(27,'Blogpost for functional tests',0,'Blogpost for functional tests',0,'Blogpost for functional tests',0,'blogpost-for-functional-tests',0,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `meta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `modules`;

CREATE TABLE `modules` (
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'unique module name',
  `installed_on` datetime NOT NULL,
  PRIMARY KEY (`name`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;

INSERT INTO `modules` (`name`, `installed_on`)
VALUES
	('Core','2015-02-23 19:48:47'),
	('Authentication','2015-02-23 19:48:47'),
	('Dashboard','2015-02-23 19:48:47'),
	('Error','2015-02-23 19:48:47'),
	('Locale','2015-02-23 19:48:52'),
	('Settings','2015-02-23 19:48:52'),
	('Users','2015-02-23 19:48:52'),
	('Groups','2015-02-23 19:48:52'),
	('Extensions','2015-02-23 19:48:53'),
	('Pages','2015-02-23 19:48:53'),
	('Search','2015-02-23 19:48:53'),
	('ContentBlocks','2015-02-23 19:48:53'),
	('Tags','2015-02-23 19:48:53'),
	('Analytics','2015-02-23 19:48:53'),
	('Blog','2015-02-23 19:48:53'),
	('Faq','2015-02-23 19:48:53'),
	('FormBuilder','2015-02-23 19:48:53'),
	('Location','2015-02-23 19:48:53'),
	('Mailmotor','2015-02-23 19:48:53'),
	('MediaLibrary','2015-02-23 19:48:53'),
	('MediaGalleries','2015-02-23 19:48:53'),
	('Profiles','2015-02-23 19:48:54');

/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table modules_extras
# ------------------------------------------------------------

DROP TABLE IF EXISTS `modules_extras`;

CREATE TABLE `modules_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the extra.',
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'A serialized value with the optional parameters',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The possible extras';

LOCK TABLES `modules_extras` WRITE;
/*!40000 ALTER TABLE `modules_extras` DISABLE KEYS */;

INSERT INTO `modules_extras` (`id`, `module`, `type`, `label`, `action`, `data`, `hidden`, `sequence`)
VALUES
	(1,'Search','widget','SearchForm','Form',NULL,0,2001),
	(2,'Search','block','Search',NULL,NULL,0,2000),
	(3,'Pages','widget','Sitemap','Sitemap',NULL,0,1),
	(4,'Pages','widget','Navigation','PreviousNextNavigation',NULL,0,2),
	(5,'Pages','widget','Subpages','Subpages','a:1:{s:8:\"template\";s:25:\"SubpagesDefault.html.twig\";}',0,2),
	(6,'Tags','block','Tags',NULL,NULL,0,30),
	(7,'Tags','widget','TagCloud','TagCloud',NULL,0,31),
	(8,'Tags','widget','Related','Related',NULL,0,32),
	(9,'Blog','block','Blog',NULL,NULL,0,1000),
	(10,'Blog','widget','RecentComments','RecentComments',NULL,0,1001),
	(11,'Blog','widget','Categories','Categories',NULL,0,1002),
	(12,'Blog','widget','Archive','Archive',NULL,0,1003),
	(13,'Blog','widget','RecentArticlesFull','RecentArticlesFull',NULL,0,1004),
	(14,'Blog','widget','RecentArticlesList','RecentArticlesList',NULL,0,1005),
	(15,'Faq','block','Faq',NULL,NULL,0,3000),
	(16,'Faq','widget','MostReadQuestions','MostReadQuestions',NULL,0,3001),
	(17,'Faq','widget','AskOwnQuestion','AskOwnQuestion',NULL,0,3002),
	(18,'Faq','widget','Categories','Categories',NULL,0,3003),
	(19,'Faq','widget','Faq','CategoryList',NULL,0,3004),
	(21,'Location','block','Location',NULL,'a:1:{s:3:\"url\";s:34:\"/private/location/index?token=true\";}',0,5000),
	(23,'Mailmotor','block','SubscribeForm','Subscribe',NULL,0,3001),
	(24,'Mailmotor','block','UnsubscribeForm','Unsubscribe',NULL,0,3002),
	(25,'Mailmotor','widget','SubscribeForm','Subscribe',NULL,0,3003),
	(26,'Profiles','block','Activate','Activate',NULL,0,5000),
	(27,'Profiles','block','ForgotPassword','ForgotPassword',NULL,0,5001),
	(28,'Profiles','block','Dashboard',NULL,NULL,0,5002),
	(29,'Profiles','block','Login','Login',NULL,0,5003),
	(30,'Profiles','block','Logout','Logout',NULL,0,5004),
	(31,'Profiles','block','ChangeEmail','ChangeEmail',NULL,0,5005),
	(32,'Profiles','block','ChangePassword','ChangePassword',NULL,0,5006),
	(33,'Profiles','block','Settings','Settings',NULL,0,5007),
	(34,'Profiles','block','Register','Register',NULL,0,5008),
	(35,'Profiles','block','ResetPassword','ResetPassword',NULL,0,5008),
	(36,'Profiles','block','ResendActivation','ResendActivation',NULL,0,5009),
	(37,'Profiles','widget','LoginBox','LoginBox',NULL,0,5010),
	(38,'Profiles','widget','LoginLink','LoginLink',NULL,0,5011);

/*!40000 ALTER TABLE `modules_extras` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table modules_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `modules_settings`;

CREATE TABLE `modules_settings` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the module',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`module`(25),`name`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `modules_settings` WRITE;
/*!40000 ALTER TABLE `modules_settings` DISABLE KEYS */;

INSERT INTO `modules_settings` (`module`, `name`, `value`)
VALUES
	('Core','languages','a:1:{i:0;s:2:\"en\";}'),
	('Core','active_languages','a:1:{i:0;s:2:\"en\";}'),
	('Core','redirect_languages','a:1:{i:0;s:2:\"en\";}'),
	('Core','default_language','s:2:\"en\";'),
	('Core','interface_languages','a:1:{i:0;s:2:\"en\";}'),
	('Core','default_interface_language','s:2:\"en\";'),
	('Core','theme','s:4:\"Fork\";'),
	('Core','akismet_key','s:0:\"\";'),
	('Core','google_maps_key','s:0:\"\";'),
	('Core','max_num_revisions','i:20;'),
	('Core','site_domains','a:1:{i:0;s:13:\"fork.dev:8088\";}'),
	('Core','site_html_header','s:0:\"\";'),
	('Core','site_html_footer','s:0:\"\";'),
	('Core','date_format_short','s:5:\"j.n.Y\";'),
	('Core','date_formats_short','a:24:{i:0;s:5:\"j/n/Y\";i:1;s:5:\"j-n-Y\";i:2;s:5:\"j.n.Y\";i:3;s:5:\"n/j/Y\";i:4;s:5:\"n/j/Y\";i:5;s:5:\"n/j/Y\";i:6;s:5:\"d/m/Y\";i:7;s:5:\"d-m-Y\";i:8;s:5:\"d.m.Y\";i:9;s:5:\"m/d/Y\";i:10;s:5:\"m-d-Y\";i:11;s:5:\"m.d.Y\";i:12;s:5:\"j/n/y\";i:13;s:5:\"j-n-y\";i:14;s:5:\"j.n.y\";i:15;s:5:\"n/j/y\";i:16;s:5:\"n-j-y\";i:17;s:5:\"n.j.y\";i:18;s:5:\"d/m/y\";i:19;s:5:\"d-m-y\";i:20;s:5:\"d.m.y\";i:21;s:5:\"m/d/y\";i:22;s:5:\"m-d-y\";i:23;s:5:\"m.d.y\";}'),
	('Core','date_format_long','s:7:\"l j F Y\";'),
	('Core','date_formats_long','a:14:{i:0;s:5:\"j F Y\";i:1;s:7:\"D j F Y\";i:2;s:7:\"l j F Y\";i:3;s:6:\"j F, Y\";i:4;s:8:\"D j F, Y\";i:5;s:8:\"l j F, Y\";i:6;s:5:\"d F Y\";i:7;s:6:\"d F, Y\";i:8;s:5:\"F j Y\";i:9;s:7:\"D F j Y\";i:10;s:7:\"l F j Y\";i:11;s:6:\"F d, Y\";i:12;s:8:\"D F d, Y\";i:13;s:8:\"l F d, Y\";}'),
	('Core','time_format','s:3:\"H:i\";'),
	('Core','time_formats','a:4:{i:0;s:3:\"H:i\";i:1;s:5:\"H:i:s\";i:2;s:5:\"g:i a\";i:3;s:5:\"g:i A\";}'),
	('Core','number_format','s:11:\"dot_nothing\";'),
	('Core','number_formats','a:6:{s:13:\"comma_nothing\";s:8:\"10000,25\";s:11:\"dot_nothing\";s:8:\"10000.25\";s:9:\"dot_comma\";s:9:\"10,000.25\";s:9:\"comma_dot\";s:9:\"10.000,25\";s:9:\"dot_space\";s:8:\"10000.25\";s:11:\"comma_space\";s:9:\"10 000,25\";}'),
	('Core','mailer_from','a:2:{s:4:\"name\";s:8:\"Fork CMS\";s:5:\"email\";s:20:\"noreply@fork-cms.com\";}'),
	('Core','mailer_to','a:2:{s:4:\"name\";s:8:\"Fork CMS\";s:5:\"email\";s:20:\"noreply@fork-cms.com\";}'),
	('Core','mailer_reply_to','a:2:{s:4:\"name\";s:8:\"Fork CMS\";s:5:\"email\";s:20:\"noreply@fork-cms.com\";}'),
	('Core','smtp_server','s:0:\"\";'),
	('Core','smtp_port','s:0:\"\";'),
	('Core','smtp_username','s:0:\"\";'),
	('Core','smtp_password','s:0:\"\";'),
	('Core','site_title_en','s:10:\"My website\";'),
	('Core','ckfinder_license_name','s:8:\"Fork CMS\";'),
  ('Core','ckfinder_license_key','s:34:\"QFKH-MNCN-19A8-32XW-35GK-Q58G-UPMC\";'),
	('Users','default_group','i:1;'),
	('Users','date_formats','a:4:{i:0;s:5:\"j/n/Y\";i:1;s:5:\"d/m/Y\";i:2;s:5:\"j F Y\";i:3;s:6:\"F j, Y\";}'),
	('Users','time_formats','a:4:{i:0;s:3:\"H:i\";i:1;s:5:\"H:i:s\";i:2;s:5:\"g:i a\";i:3;s:5:\"g:i A\";}'),
	('Pages','default_template','i:3;'),
	('Pages','meta_navigation','b:0;'),
	('Search','overview_num_items','i:10;'),
	('Search','validate_search','b:1;'),
	('ContentBlocks','max_num_revisions','i:20;'),
	('Blog','allow_comments','b:1;'),
	('Blog','requires_akismet','b:1;'),
	('Blog','spamfilter','b:0;'),
	('Blog','moderation','b:1;'),
	('Blog','ping_services','b:1;'),
	('Blog','overview_num_items','i:10;'),
	('Blog','recent_articles_full_num_items','i:3;'),
	('Blog','recent_articles_list_num_items','i:5;'),
	('Blog','max_num_revisions','i:20;'),
	('Blog','rss_meta_en','b:1;'),
	('Blog','rss_title_en','s:3:\"RSS\";'),
	('Blog','rss_description_en','s:0:\"\";'),
	('Faq','overview_num_items_per_category','i:10;'),
	('Faq','most_read_num_items','i:5;'),
	('Faq','related_num_items','i:5;'),
	('Faq','spamfilter','b:0;'),
	('Faq','allow_feedback','b:0;'),
	('Faq','allow_own_question','b:0;'),
	('Faq','allow_multiple_categories','b:1;'),
	('Faq','send_email_on_new_feedback','b:0;'),
	('Location','zoom_level','s:4:\"auto\";'),
	('Location','width','i:400;'),
	('Location','height','i:300;'),
	('Location','map_type','s:7:\"ROADMAP\";'),
	('Location','zoom_level_widget','i:13;'),
	('Location','width_widget','i:400;'),
	('Location','height_widget','i:300;'),
	('Location','map_type_widget','s:7:\"ROADMAP\";'),
	('Mailmotor','mail_engine','s:15:\"not_implemented\";'),
	('Mailmotor','overwrite_interests','b:0;'),
	('Profiles','allow_gravatar','b:1;');

/*!40000 ALTER TABLE `modules_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table modules_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `modules_tags`;

CREATE TABLE `modules_tags` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;

INSERT INTO `pages` (`id`, `revision_id`, `user_id`, `parent_id`, `template_id`, `meta_id`, `language`, `type`, `title`, `navigation_title`, `navigation_title_overwrite`, `hidden`, `status`, `publish_on`, `data`, `created_on`, `edited_on`, `allow_move`, `allow_children`, `allow_edit`, `allow_delete`, `sequence`)
VALUES
	(1,1,1,0,4,1,'en','page','Home','Home',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',0,1,1,0,0),
	(2,2,1,0,3,2,'en','footer','Sitemap','Sitemap',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,0),
	(3,3,1,0,3,3,'en','footer','Disclaimer','Disclaimer',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,1),
	(404,4,1,0,3,4,'en','root','404','404',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',0,1,1,0,0),
	(405,5,1,0,3,5,'en','root','Search','Search',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,1),
	(406,6,1,0,3,6,'en','root','Tags','Tags',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,2),
	(407,7,1,1,3,8,'en','page','Blog','Blog',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,0),
	(408,8,1,1,3,10,'en','page','FAQ','FAQ',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,1),
	(409,9,1,1,3,11,'en','page','Contact','Contact',0,0,'active','2015-02-23 19:48:53',NULL,'2015-02-23 19:48:53','2015-02-23 19:48:53',1,1,1,1,2),
	(410,10,1,0,3,12,'en','root','Sent mailings','Sent mailings',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,3),
	(411,11,1,410,3,13,'en','page','Subscribe','Subscribe',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,0),
	(412,12,1,410,3,14,'en','page','Unsubscribe','Unsubscribe',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,1),
	(413,13,1,0,3,15,'en','root','Activate','Activate',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,4),
	(414,14,1,0,3,16,'en','root','Forgot password','Forgot password',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,5),
	(415,15,1,0,3,17,'en','root','Reset password','Reset password',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,6),
	(416,16,1,0,3,18,'en','root','Resend activation e-mail','Resend activation e-mail',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,7),
	(417,17,1,0,3,19,'en','root','Login','Login',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,8),
	(418,18,1,0,3,20,'en','root','Register','Register',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,9),
	(419,19,1,0,3,21,'en','root','Logout','Logout',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,10),
	(420,20,1,0,3,22,'en','root','Profile','Profile',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,11),
	(421,21,1,420,3,23,'en','page','Profile settings','Profile settings',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,0),
	(422,22,1,420,3,24,'en','page','Change email','Change email',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,1),
	(423,23,1,420,3,25,'en','page','Change password','Change password',0,0,'active','2015-02-23 19:48:54',NULL,'2015-02-23 19:48:54','2015-02-23 19:48:54',1,1,1,1,2);

/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table pages_blocks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pages_blocks`;

CREATE TABLE `pages_blocks` (
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

LOCK TABLES `pages_blocks` WRITE;
/*!40000 ALTER TABLE `pages_blocks` DISABLE KEYS */;

INSERT INTO `pages_blocks` (`revision_id`, `position`, `extra_id`, `extra_type`, `extra_data`, `html`, `created_on`, `edited_on`, `visible`, `sequence`)
VALUES
	(1,'main',NULL,'rich_text',NULL,'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(1,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(2,'main',NULL,'rich_text',NULL,'<p>Take a look at all the pages in our website:</p>','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(2,'main',3,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,1),
	(2,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(3,'main',NULL,'rich_text',NULL,'<p><strong>This website is property of [Bedrijfsnaam].</strong></p>\n<p><strong>Contact info:</strong><br />[Bedrijfsnaam]<br /> [Straatnaam] [Nummer]<br /> [Postcode] [Gemeente]</p>\n<p><strong>Adres maatschappelijk zetel:</strong><br />[Maatschappelijke zetel]<br /> [Straatnaam] [Nummer]<br /> [Postcode] [Gemeente]</p>\n<p>Telefoon:<br />E-mail:</p>\n<p>Ondernemingsnummer: BTW BE 0 [BTW-nummer]</p>\n<p>De toezichthoudende autoriteit: (wanneer uw activiteit aan een vergunningsstelsel is onderworpen)</p>\n<p>By accessing and using the website, you have expressly agreed to the following general conditions.</p>\n<h3>Intellectual property rights</h3>\n<p>The contents of this site, including trade marks, logos, drawings, data, product or company names, texts, images, etc. are protected by intellectual property rights and belong to [Bedrijfsnaam] or entitled third parties.</p>\n<h3>Liability limitation</h3>\n<p>The information on the website is general in nature. It is not adapted to personal or specific circumstances and can therefore not be regarded as personal, professional or judicial advice for the user.</p>\n<p>[Bedrijfsnaam] does everything in its power to ensure that the information made available is complete, correct, accurate and updated. However, despite these efforts inaccuracies may occur when providing information. If the information provided contains inaccuracies or if specific information on or via the site is unavailable, [Bedrijfsnaam] shall make the greatest effort to ensure that this is rectified as soon as possible.</p>\n<p>[Bedrijfsnaam] cannot be held responsible for direct or indirect damage caused by the use of the information on this site.&nbsp; The site manager should be contacted if the user has noticed any inaccuracies in the information provided by the site.</p>\n<p>The contents of the site (including links) may be adjusted, changed or extended at any time without any announcement or advance notice. [Bedrijfsnaam] gives no guarantees for the smooth operation of the website and cannot be held responsible in any way for the poor operation or temporary unavailability of the website or for any type of damage, direct or indirect, which may occur due to the access to or use of the website.</p>\n<p>[Bedrijfsnaam] can in no case be held liable, directly or indirectly, specifically or otherwise, vis-&agrave;-vis anyone for any damage attributable to the use of this site or any other one, in particular as the result of links or hyperlinks including, but not limited to, any loss, work interruption, damage of the user&rsquo;s programs or other data on the computer system, hardware, software or otherwise.</p>\n<p>The website may contain hyperlinks to websites or pages of third parties or refer to these indirectly. The placing of links on these websites or pages shall not imply in any way the implicit approval of the contents thereof.&nbsp; [Bedrijfsnaam] expressly declares that it has no authority over the contents or over other features of these websites and can in no case be held responsible for the contents or features thereof or for any other type of damage resulting from their use.</p>\n<h3>Applicable legislation and competent courts</h3>\n<p>This site is governed by Belgian law. Only the courts of the district of Ghent are competent to settle any disputes.</p>\n<h3>Privacy policy</h3>\n<p>[Bedrijfsnaam] believes that your privacy is important. While most of the information on this site is available without having to ask the user for personal information,&nbsp; the user may be asked for some personal details.&nbsp;&nbsp; This information will only be used to ensure a better service.&nbsp;&nbsp; (e.g. for our customer database, to keep users informed of our activities, etc.). The user may, free of charge and on request, always prevent the use of his personal details for the purposes of direct marketing. In this regard, the user should contact [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf]. Your personal details will never been transferred to any third parties (if this should occur, you will be informed).</p>\n<p>In accordance with the law on the processing of personal data of 8 December 1992, the user has the legal right to examine and possibly correct any of his/her personal details. Subject to proof of identity (copy of the user&rsquo;s identity card), you can via a written, dated and signed request to [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf], receive free of charge a written statement of the user&rsquo;s personal details.&nbsp; If necessary, you may also ask for any incorrect, incomplete or irrelevant data to be adjusted.</p>\n<p>[Bedrijfsnaam] can collect non-personal anonymous or aggregate data such as browser type, IP address or operating system in use or the domain name of the website that led you to and from our website, ensuring optimum effectiveness of our website for all users.</p>\n<h3>The use of cookies</h3>\n<p>During a visit to the site, cookies may be placed on the hard drive of your computer. This is only in order to ensure that our site is geared to the needs of users returning to our website. These tiny files known as cookies are not used to ascertain the surfing habits of the visitor on other websites. Your internet browser enables you to disable these cookies, receive a warning when a cookie has been installed or have the cookies removed from your hard disc.&nbsp; For this purpose, consult the help function of your internet browser.</p>','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(3,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(4,'main',NULL,'rich_text',NULL,'<iframe src=\"http://notfound-static.fwebservices.be/404/index.html\" width=\"100%\" height=\"650\" frameborder=\"0\"></iframe>\n<p>This page doesn\'t exist or is not accessible at this time. Take a look at the sitemap:</p>\n','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(4,'main',3,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,1),
	(4,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(5,'main',2,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(6,'main',6,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(6,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(7,'main',9,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(7,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(8,'main',15,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(9,'main',NULL,'rich_text',NULL,'<p>Enter your question and contact information and we\'ll get back to you as soon as possible.</p>','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(9,'main',20,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,1),
	(9,'top',1,'rich_text',NULL,'','2015-02-23 19:48:53','2015-02-23 19:48:53',1,0),
	(10,'main',22,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(10,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(11,'main',23,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(11,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(12,'main',24,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(12,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(13,'main',26,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(13,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(14,'main',27,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(14,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(15,'main',35,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(15,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(16,'main',36,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(16,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(17,'main',29,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(17,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(18,'main',34,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(18,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(19,'main',30,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(19,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(20,'main',28,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(20,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(21,'main',33,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(21,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(22,'main',31,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(22,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(23,'main',32,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0),
	(23,'top',1,'rich_text',NULL,'','2015-02-23 19:48:54','2015-02-23 19:48:54',1,0);

/*!40000 ALTER TABLE `pages_blocks` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table profiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profiles`;

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registered_on` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table profiles_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profiles_groups`;

CREATE TABLE `profiles_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table profiles_groups_rights
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profiles_groups_rights`;

CREATE TABLE `profiles_groups_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `starts_on` datetime DEFAULT NULL,
  `expires_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_id__group__id__expires_on` (`profile_id`,`group_id`,`expires_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table profiles_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profiles_sessions`;

CREATE TABLE `profiles_sessions` (
  `session_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `profile_id` int(11) NOT NULL,
  `secret_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`session_id`,`profile_id`),
  KEY `fk_profiles_sessions_profiles1` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table profiles_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profiles_settings`;

CREATE TABLE `profiles_settings` (
  `profile_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`,`profile_id`),
  KEY `fk_profiles_settings_profiles1` (`profile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table search_index
# ------------------------------------------------------------

DROP TABLE IF EXISTS `search_index`;

CREATE TABLE `search_index` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL,
  `other_id` int(11) NOT NULL,
  `field` varchar(64) CHARACTER SET utf8 NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(5) CHARACTER SET utf8 NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module`,`other_id`,`field`,`language`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Search index';

LOCK TABLES `search_index` WRITE;
/*!40000 ALTER TABLE `search_index` DISABLE KEYS */;

INSERT INTO `search_index` (`module`, `other_id`, `field`, `value`, `language`, `active`)
VALUES
	('Pages',1,'title','Home','en',1),
	('Pages',1,'text','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan. ','en',1),
	('Pages',2,'title','Sitemap','en',1),
	('Pages',2,'text','Take a look at all the pages in our website:  ','en',1),
	('Pages',3,'title','Disclaimer','en',1),
	('Pages',3,'text','This website is property of [Bedrijfsnaam].\nContact info:[Bedrijfsnaam] [Straatnaam] [Nummer] [Postcode] [Gemeente]\nAdres maatschappelijk zetel:[Maatschappelijke zetel] [Straatnaam] [Nummer] [Postcode] [Gemeente]\nTelefoon:E-mail:\nOndernemingsnummer: BTW BE 0 [BTW-nummer]\nDe toezichthoudende autoriteit: (wanneer uw activiteit aan een vergunningsstelsel is onderworpen)\nBy accessing and using the website, you have expressly agreed to the following general conditions.\nIntellectual property rights\nThe contents of this site, including trade marks, logos, drawings, data, product or company names, texts, images, etc. are protected by intellectual property rights and belong to [Bedrijfsnaam] or entitled third parties.\nLiability limitation\nThe information on the website is general in nature. It is not adapted to personal or specific circumstances and can therefore not be regarded as personal, professional or judicial advice for the user.\n[Bedrijfsnaam] does everything in its power to ensure that the information made available is complete, correct, accurate and updated. However, despite these efforts inaccuracies may occur when providing information. If the information provided contains inaccuracies or if specific information on or via the site is unavailable, [Bedrijfsnaam] shall make the greatest effort to ensure that this is rectified as soon as possible.\n[Bedrijfsnaam] cannot be held responsible for direct or indirect damage caused by the use of the information on this site.&nbsp; The site manager should be contacted if the user has noticed any inaccuracies in the information provided by the site.\nThe contents of the site (including links) may be adjusted, changed or extended at any time without any announcement or advance notice. [Bedrijfsnaam] gives no guarantees for the smooth operation of the website and cannot be held responsible in any way for the poor operation or temporary unavailability of the website or for any type of damage, direct or indirect, which may occur due to the access to or use of the website.\n[Bedrijfsnaam] can in no case be held liable, directly or indirectly, specifically or otherwise, vis-&agrave;-vis anyone for any damage attributable to the use of this site or any other one, in particular as the result of links or hyperlinks including, but not limited to, any loss, work interruption, damage of the user&rsquo;s programs or other data on the computer system, hardware, software or otherwise.\nThe website may contain hyperlinks to websites or pages of third parties or refer to these indirectly. The placing of links on these websites or pages shall not imply in any way the implicit approval of the contents thereof.&nbsp; [Bedrijfsnaam] expressly declares that it has no authority over the contents or over other features of these websites and can in no case be held responsible for the contents or features thereof or for any other type of damage resulting from their use.\nApplicable legislation and competent courts\nThis site is governed by Belgian law. Only the courts of the district of Ghent are competent to settle any disputes.\nPrivacy policy\n[Bedrijfsnaam] believes that your privacy is important. While most of the information on this site is available without having to ask the user for personal information,&nbsp; the user may be asked for some personal details.&nbsp;&nbsp; This information will only be used to ensure a better service.&nbsp;&nbsp; (e.g. for our customer database, to keep users informed of our activities, etc.). The user may, free of charge and on request, always prevent the use of his personal details for the purposes of direct marketing. In this regard, the user should contact [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf]. Your personal details will never been transferred to any third parties (if this should occur, you will be informed).\nIn accordance with the law on the processing of personal data of 8 December 1992, the user has the legal right to examine and possibly correct any of his/her personal details. Subject to proof of identity (copy of the user&rsquo;s identity card), you can via a written, dated and signed request to [Bedrijfsnaam], [Adres bedrijf] or via [Email adres bedrijf], receive free of charge a written statement of the user&rsquo;s personal details.&nbsp; If necessary, you may also ask for any incorrect, incomplete or irrelevant data to be adjusted.\n[Bedrijfsnaam] can collect non-personal anonymous or aggregate data such as browser type, IP address or operating system in use or the domain name of the website that led you to and from our website, ensuring optimum effectiveness of our website for all users.\nThe use of cookies\nDuring a visit to the site, cookies may be placed on the hard drive of your computer. This is only in order to ensure that our site is geared to the needs of users returning to our website. These tiny files known as cookies are not used to ascertain the surfing habits of the visitor on other websites. Your internet browser enables you to disable these cookies, receive a warning when a cookie has been installed or have the cookies removed from your hard disc.&nbsp; For this purpose, consult the help function of your internet browser. ','en',1),
	('Pages',404,'title','404','en',1),
	('Pages',404,'text','\nThis page doesn\'t exist or is not accessible at this time. Take a look at the sitemap:\n  ','en',1),
	('Pages',405,'title','Search','en',1),
	('Pages',405,'text','','en',1);

/*!40000 ALTER TABLE `search_index` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table search_modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `search_modules`;

CREATE TABLE `search_modules` (
  `module` varchar(255) CHARACTER SET utf8 NOT NULL,
  `searchable` tinyint(1) NOT NULL DEFAULT '0',
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `search_modules` WRITE;
/*!40000 ALTER TABLE `search_modules` DISABLE KEYS */;

INSERT INTO `search_modules` (`module`, `searchable`, `weight`)
VALUES
	('Pages',1,1),
	('Blog',1,1),
	('Faq',1,1);

/*!40000 ALTER TABLE `search_modules` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table search_statistics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `search_statistics`;

CREATE TABLE `search_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  `num_results` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table search_synonyms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `search_synonyms`;

CREATE TABLE `search_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(245) COLLATE utf8mb4_unicode_ci NOT NULL,
  `synonym` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`term`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table themes_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `themes_templates`;

CREATE TABLE `themes_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the template.',
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The name of the theme.',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is this template active (as in: will it be used).',
  `data` text COLLATE utf8mb4_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The possible templates';

LOCK TABLES `themes_templates` WRITE;
/*!40000 ALTER TABLE `themes_templates` DISABLE KEYS */;

INSERT INTO `themes_templates` (`id`, `theme`, `label`, `path`, `active`, `data`)
VALUES
	(1,'Core','Default','Core/Layout/Templates/Default.html.twig',1,'a:2:{s:6:\"format\";s:6:\"[main]\";s:5:\"names\";a:1:{i:0;s:4:\"main\";}}'),
	(2,'Core','Home','Core/Layout/Templates/Home.html.twig',1,'a:2:{s:6:\"format\";s:6:\"[main]\";s:5:\"names\";a:1:{i:0;s:4:\"main\";}}'),
	(3,'Fork','Default','Core/Layout/Templates/Default.html.twig',1,'a:3:{s:6:\"format\";s:91:\"[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[left,main,main,main]\";s:5:\"names\";a:4:{i:0;s:4:\"main\";i:1;s:4:\"left\";i:2;s:3:\"top\";i:3;s:13:\"advertisement\";}s:14:\"default_extras\";a:1:{s:3:\"top\";a:1:{i:0;i:1;}}}'),
	(4,'Fork','Home','Core/Layout/Templates/Home.html.twig',1,'a:3:{s:6:\"format\";s:115:\"[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[main,main,main,main],[left,left,right,right]\";s:5:\"names\";a:5:{i:0;s:4:\"main\";i:1;s:4:\"left\";i:2;s:5:\"right\";i:3;s:3:\"top\";i:4;s:13:\"advertisement\";}s:14:\"default_extras\";a:1:{s:3:\"top\";a:1:{i:0;i:1;}}}');

/*!40000 ALTER TABLE `themes_templates` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'will be case-sensitive',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'is this user active?',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is the user deleted?',
  `is_god` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The backend users';

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `email`, `password`, `active`, `deleted`, `is_god`)
VALUES
	(1,'noreply@fork-cms.com','$2y$10$hWAUhjKF/64HZLHG/whEqu0LfHaPPCAtZc7JQUyMvIst8I9.r1p9.',1,0,1),
	(2,'pages-user@fork-cms.com','$2y$10$hWAUhjKF/64HZLHG/whEqu0LfHaPPCAtZc7JQUyMvIst8I9.r1p9.',1,0,0),
	(3,'users-edit-user@fork-cms.com','$2y$10$hWAUhjKF/64HZLHG/whEqu0LfHaPPCAtZc7JQUyMvIst8I9.r1p9.',1,0,0);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_groups`;

CREATE TABLE `users_groups` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users_groups` WRITE;
/*!40000 ALTER TABLE `users_groups` DISABLE KEYS */;

INSERT INTO `users_groups` (`group_id`, `user_id`)
VALUES
	(1,1),
	(2,2),
	(3,3);

/*!40000 ALTER TABLE `users_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_sessions`;

CREATE TABLE `users_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_session_id_secret_key` (`session_id`(100),`secret_key`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table users_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_settings`;

CREATE TABLE `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users_settings` WRITE;
/*!40000 ALTER TABLE `users_settings` DISABLE KEYS */;

INSERT INTO `users_settings` (`user_id`, `name`, `value`)
VALUES
	(1,'nickname','s:8:\"Fork CMS\";'),
	(1,'name','s:4:\"Fork\";'),
	(1,'surname','s:3:\"CMS\";'),
	(1,'interface_language','s:2:\"en\";'),
	(1,'date_format','s:5:\"j F Y\";'),
	(1,'time_format','s:3:\"H:i\";'),
	(1,'datetime_format','s:9:\"j F Y H:i\";'),
	(1,'number_format','s:11:\"dot_nothing\";'),
	(1,'password_key','s:13:\"54eb8424f2b6e\";'),
	(1,'password_strength','s:4:\"weak\";'),
	(1,'current_password_change','i:1424720932;'),
	(1,'avatar','s:7:\"god.jpg\";'),
	(1,'dashboard_sequence','a:5:{s:8:\"Settings\";a:1:{s:7:\"Analyse\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:5:\"Users\";a:1:{s:10:\"Statistics\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:2;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:9:\"Analytics\";a:2:{s:14:\"TrafficSources\";a:4:{s:6:\"column\";s:6:\"middle\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}s:8:\"Visitors\";a:4:{s:6:\"column\";s:6:\"middle\";s:8:\"position\";i:2;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:4:\"Blog\";a:1:{s:8:\"Comments\";a:4:{s:6:\"column\";s:5:\"right\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}s:3:\"Faq\";a:1:{s:8:\"Feedback\";a:4:{s:6:\"column\";s:5:\"right\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:1;}}}'),
	(2,'nickname','s:10:\"Pages User\";'),
	(2,'name','s:5:\"Pages\";'),
	(2,'surname','s:4:\"User\";'),
	(2,'interface_language','s:2:\"en\";'),
	(2,'date_format','s:5:\"j F Y\";'),
	(2,'time_format','s:3:\"H:i\";'),
	(2,'datetime_format','s:9:\"j F Y H:i\";'),
	(2,'number_format','s:11:\"dot_nothing\";'),
	(2,'password_key','s:13:\"54eb8424f2b6e\";'),
	(2,'password_strength','s:4:\"weak\";'),
	(2,'current_password_change','i:1424720932;'),
	(2,'avatar','s:7:\"god.jpg\";'),
	(2,'dashboard_sequence','a:3:{s:4:\"Blog\";a:1:{s:8:\"Comments\";a:4:{s:6:\"column\";s:6:\"middle\";s:8:\"position\";i:0;s:6:\"hidden\";b:0;s:7:\"present\";b:0;}}s:8:\"Settings\";a:1:{s:7:\"Analyse\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:0;}}s:5:\"Users\";a:1:{s:10:\"Statistics\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:0;}}}'),
	(3,'name','s:5:\"Users\";'),
	(3,'surname','s:4:\"User\";'),
	(3,'interface_language','s:2:\"en\";'),
	(3,'date_format','s:5:\"j F Y\";'),
	(3,'time_format','s:3:\"H:i\";'),
	(3,'datetime_format','s:9:\"j F Y H:i\";'),
	(3,'number_format','s:11:\"dot_nothing\";'),
	(3,'password_key','s:13:\"54eb8424f2b6e\";'),
	(3,'password_strength','s:4:\"weak\";'),
	(3,'current_password_change','i:1424720932;'),
	(3,'avatar','s:7:\"god.jpg\";'),
	(3,'dashboard_sequence','a:3:{s:4:\"Blog\";a:1:{s:8:\"Comments\";a:4:{s:6:\"column\";s:6:\"middle\";s:8:\"position\";i:0;s:6:\"hidden\";b:0;s:7:\"present\";b:0;}}s:8:\"Settings\";a:1:{s:7:\"Analyse\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:0;}}s:5:\"Users\";a:1:{s:10:\"Statistics\";a:4:{s:6:\"column\";s:4:\"left\";s:8:\"position\";i:1;s:6:\"hidden\";b:0;s:7:\"present\";b:0;}}}'),
	(1,'current_login','s:10:\"1501830186\";'),
	(1,'last_login','s:10:\"1501830130\";');
/*!40000 ALTER TABLE `users_settings` ENABLE KEYS */;
UNLOCK TABLES;

-- Create syntax for TABLE 'MediaFolder'
CREATE TABLE `MediaFolder` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`userId` int(11) NOT NULL,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`createdOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`editedOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`parentMediaFolderId` int(11) DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `IDX_F8B3AB017897CFE7` (`parentMediaFolderId`),
CONSTRAINT `FK_F8B3AB017897CFE7` FOREIGN KEY (`parentMediaFolderId`) REFERENCES `MediaFolder` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create syntax for TABLE 'MediaGallery'
CREATE TABLE `MediaGallery` (
`id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
`userId` int(11) NOT NULL,
`moduleExtraId` int(11) NOT NULL,
`action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`createdOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`editedOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:media_gallery_status)',
`mediaGroupId` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
PRIMARY KEY (`id`),
UNIQUE KEY `UNIQ_D5EDE7036776CC71` (`mediaGroupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create syntax for TABLE 'MediaGroup'
CREATE TABLE `MediaGroup` (
`id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
`type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:media_group_type)',
`editedOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`numberOfConnectedItems` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create syntax for TABLE 'MediaGroupMediaItem'
CREATE TABLE `MediaGroupMediaItem` (
`id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
`createdOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`sequence` int(11) NOT NULL,
`mediaGroupId` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
`mediaItemId` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
PRIMARY KEY (`id`),
KEY `IDX_BCC51AD86776CC71` (`mediaGroupId`),
KEY `IDX_BCC51AD827759A6A` (`mediaItemId`),
CONSTRAINT `FK_BCC51AD827759A6A` FOREIGN KEY (`mediaItemId`) REFERENCES `MediaItem` (`id`) ON DELETE CASCADE,
CONSTRAINT `FK_BCC51AD86776CC71` FOREIGN KEY (`mediaGroupId`) REFERENCES `MediaGroup` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create syntax for TABLE 'MediaItem'
CREATE TABLE `MediaItem` (
`id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
`userId` int(11) NOT NULL,
`storageType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '(DC2Type:media_item_storage_type)',
`type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:media_item_type)',
`mime` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`shardingFolderName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`size` int(11) DEFAULT NULL,
`width` int(11) DEFAULT NULL,
`height` int(11) DEFAULT NULL,
`aspectRatio` decimal(13,2) DEFAULT NULL COMMENT '(DC2Type:media_item_aspect_ratio)',
`createdOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`editedOn` datetime NOT NULL COMMENT '(DC2Type:datetime)',
`mediaFolderId` int(11) DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `IDX_3BE26384C1DCBB95` (`mediaFolderId`),
CONSTRAINT `FK_3BE26384C1DCBB95` FOREIGN KEY (`mediaFolderId`) REFERENCES `MediaFolder` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
