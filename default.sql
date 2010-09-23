-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 23, 2010 at 06:25 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forkng_test2309`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics_keywords`
--

CREATE TABLE `analytics_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `analytics_keywords`
--


-- --------------------------------------------------------

--
-- Table structure for table `analytics_landing_pages`
--

CREATE TABLE `analytics_landing_pages` (
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

--
-- Dumping data for table `analytics_landing_pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `analytics_pages`
--

CREATE TABLE `analytics_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_viewed` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `analytics_pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `analytics_referrers`
--

CREATE TABLE `analytics_referrers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referrer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entrances` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `analytics_referrers`
--

INSERT INTO `analytics_referrers` VALUES(1, 'netlash.com/', 2, '2010-09-23 00:00:00');
INSERT INTO `analytics_referrers` VALUES(2, 'netlash.com/downloads/bedankt/fork-ng-beta-2-0-0.zip', 2, '2010-09-23 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` VALUES(1, 'en', 'Default', 'default');
INSERT INTO `blog_categories` VALUES(2, 'en', 'General', 'general');
INSERT INTO `blog_categories` VALUES(3, 'en', 'Not so general', 'not-so-general');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('comment','trackback') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'comment',
  `status` enum('published','moderation','spam') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'moderation',
  `data` text COLLATE utf8_unicode_ci COMMENT 'Serialized array with extra data',
  PRIMARY KEY (`id`),
  KEY `idx_post_id_status` (`post_id`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` VALUES(1, 1, '2010-09-23 11:22:00', 'Matthias Mullie', 'matthias@spoon-library.com', 'http://www.anantasoft.com', 'cool!', 'comment', 'published', NULL);
INSERT INTO `blog_comments` VALUES(2, 1, '2010-09-23 11:22:00', 'Davy Hellemans', 'davy@spoon-library.com', 'http://www.spoon-library.com', 'awesome!', 'comment', 'published', NULL);
INSERT INTO `blog_comments` VALUES(3, 1, '2010-09-23 11:22:00', 'Tijs Verkoyen', 'tijs@spoon-library.com', 'http://www.sumocoders.com', 'wicked!', 'comment', 'published', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
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
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `allow_comments` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `num_comments` int(11) NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` VALUES(1, 1, 1, 1, 11, 'en', 'Introducing', '', '<p>Welcome to the new Fork CMS blog.</p>\n<h4>What?</h4>\n<p>We are open sourcing our content management system for the world to use. Fork CMS has been in use internally at our company for over three years. We’re building a new major version and you’re invited to the ride. We’re committed to building the best CMS out there.</p>\n<h4>Why?</h4>\n<p>Our main goal is to help anyone responsible for a website kick ass at their job. To enable them to communicate their message they way they want to. To start a conversation.</p>\n<p>Too many websites don’t change for over a year because they require a call to a web developer to update. That guy then sends over his bill and is frustrated that all of his clients can’t figure out his CMS. Those CMSes are either super-hacked <a href="http://wordpress.org">Wordpress</a> installs that nobody “gets” anymore, or overly complicated <a href="http://drupal.org">Drupal</a>/<a href="http://joomla.com">Joomla</a> installs that no non-developer dares to update.</p>\n<p>We believe that the launch of a website is just the start. We believe anyone should be able to update any part of a website. We believe in a user interface without developer-speak. We believe putting something into a setting is a sign of weakness. We think modes make confusing software, and that a UI should be as simple as possible.</p>\n<p>We are web developers. We want to code in a friendly and clean environment. We want structure and cleanliness. We hate trailing whitespace. We built a tool to help us create the best websites possible. Joel Spolsky says you have to define what your company is about.</p>\n<blockquote>\n<p>(…) if you can’t explain your mission in the form, “We help $TYPEOFPERSON be awesome at $THING,” you are not going to have passionate users. What’s your tagline? Can you fit it into that template?</p>\n</blockquote>\n<p>We help website owners to be awesome at communication.</p>\n<h4>Who?</h4>\n<p>We are <a href="http://www.netlash.com">Netlash</a>, a web agency based in Ghent, Belgium. We’re eighteen people who build websites day in, day out. We build small business websites like Equazion. We build e-commerce shops like Cookstore and community websites like AB Concerts. In order to make these websites awesome, we built Fork CMS.</p>\n<h4>Stay up to date</h4>\n<p>Subscribe to the <a href="/nl/blog/rss">RSS feed</a>.</p>', 'archived', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 11:22:00', 'N', 'Y', 3);
INSERT INTO `blog_posts` VALUES(2, 2, 1, 1, 12, 'en', 'Lorem ipsum', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vel lorem neque, eget sollicitudin justo. Donec volutpat nisi ac est tempus semper. Fusce in dictum eros. Cras lorem velit, dignissim quis imperdiet sit amet, lacinia vel risus. Donec et felis turpis. Aenean tempor porta odio, quis egestas nibh condimentum ut. Mauris arcu nisl, dapibus vitae blandit vel, auctor in dui. Sed lorem velit, placerat nec porta sit amet, molestie vel justo. Maecenas eget congue est. Aliquam vitae velit vitae elit laoreet pretium sit amet a ligula. Nulla facilisi. Pellentesque tempus pharetra lacus, sed pulvinar metus consectetur vel. Maecenas ac orci elit. Nunc lacinia, quam in dignissim mattis, neque risus gravida neque, at consectetur lectus purus varius odio. Integer sed nunc quis erat euismod consequat. Donec lobortis pretium leo quis posuere. Suspendisse potenti. Aenean ac libero non purus euismod fringilla. Donec aliquet, erat ac porta luctus, sem metus faucibus turpis, non gravida nibh eros vel lectus. Phasellus at mi leo, sed accumsan odio.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vel lorem neque, eget sollicitudin justo. Donec volutpat nisi ac est tempus semper. Fusce in dictum eros. Cras lorem velit, dignissim quis imperdiet sit amet, lacinia vel risus. Donec et felis turpis. Aenean tempor porta odio, quis egestas nibh condimentum ut. Mauris arcu nisl, dapibus vitae blandit vel, auctor in dui. Sed lorem velit, placerat nec porta sit amet, molestie vel justo. Maecenas eget congue est. Aliquam vitae velit vitae elit laoreet pretium sit amet a ligula. Nulla facilisi. Pellentesque tempus pharetra lacus, sed pulvinar metus consectetur vel. Maecenas ac orci elit. Nunc lacinia, quam in dignissim mattis, neque risus gravida neque, at consectetur lectus purus varius odio. Integer sed nunc quis erat euismod consequat. Donec lobortis pretium leo quis posuere. Suspendisse potenti. Aenean ac libero non purus euismod fringilla. Donec aliquet, erat ac porta luctus, sem metus faucibus turpis, non gravida nibh eros vel lectus. Phasellus at mi leo, sed accumsan odio.</p>\n<p>In hac habitasse platea dictumst. Vestibulum sollicitudin, diam at pretium hendrerit, quam mauris rhoncus purus, ut placerat tortor dui a tellus. Vivamus dignissim tincidunt sapien, ac ornare sapien egestas vel. Quisque iaculis odio et sem condimentum tincidunt. Sed volutpat arcu vitae dui rutrum faucibus sed a tortor. Fusce sed justo risus, sit amet varius nisi. Sed pretium sem eu leo hendrerit id convallis augue venenatis. Cras eu ultricies lorem. Integer vitae sem ipsum, sit amet convallis purus. Morbi et nunc quis lectus ultrices consectetur at sit amet metus. Ut luctus, urna quis eleifend egestas, nisl sapien auctor eros, a suscipit nisl quam ac libero. Aliquam erat volutpat. Quisque feugiat pretium velit, a suscipit dui consectetur non. Donec ac lorem mi. Sed porttitor vestibulum volutpat.</p>\n<p>Nunc auctor dictum congue. Aliquam suscipit felis in libero elementum quis condimentum urna volutpat. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed tempus imperdiet porta. Nunc suscipit ultrices lacus tincidunt porta. Donec vitae metus sed mi sagittis faucibus. Mauris eros urna, consequat sed pellentesque ornare, euismod ac mauris. Aliquam pellentesque diam convallis sem sagittis vel imperdiet felis blandit. Etiam eu purus a mi blandit porttitor. Mauris sodales dapibus magna venenatis gravida. In sit amet leo ligula, vitae tincidunt sem. Integer vel quam eros. Cras in mauris sit amet nulla vestibulum commodo. Nulla ultrices blandit eros, in porta lectus dapibus vitae. Vestibulum sit amet felis velit, quis tempus enim. In sed massa sed nibh tempor ornare. Sed lobortis luctus nunc non viverra. Aenean sit amet tellus quis ipsum sagittis auctor. Vestibulum at sapien lacus. Suspendisse potenti.</p>', 'archived', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 11:22:00', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES(1, 3, 1, 1, 33, 'en', 'Introducing', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'archived', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 14:37:24', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES(2, 4, 1, 1, 34, 'en', 'Lorem ipsum', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'archived', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 14:37:35', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES(1, 5, 1, 1, 70, 'en', 'Dolor sit amet', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'archived', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 16:22:55', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES(2, 6, 2, 1, 71, 'en', 'Lorem ipsum', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 16:23:50', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES(1, 7, 3, 1, 72, 'en', 'Dolor sit amet', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 11:22:00', '2010-09-23 11:22:00', '2010-09-23 16:23:53', 'N', 'Y', 0);

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks`
--

CREATE TABLE `content_blocks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `status` enum('active','archived') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `content_blocks`
--


-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
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
  `send_on` datetime DEFAULT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `emails`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parameters` text COLLATE utf8_unicode_ci COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` VALUES(1, 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups_rights_actions`
--

CREATE TABLE `groups_rights_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL DEFAULT '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65 ;

--
-- Dumping data for table `groups_rights_actions`
--

INSERT INTO `groups_rights_actions` VALUES(1, 1, 'dashboard', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(2, 1, 'locale', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(3, 1, 'locale', 'analyse', 7);
INSERT INTO `groups_rights_actions` VALUES(4, 1, 'locale', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(5, 1, 'locale', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(6, 1, 'locale', 'mass_action', 7);
INSERT INTO `groups_rights_actions` VALUES(7, 1, 'users', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(8, 1, 'users', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES(9, 1, 'users', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(10, 1, 'users', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(11, 1, 'example', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(12, 1, 'example', 'layout', 7);
INSERT INTO `groups_rights_actions` VALUES(13, 1, 'settings', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(14, 1, 'pages', 'get_info', 7);
INSERT INTO `groups_rights_actions` VALUES(15, 1, 'pages', 'move', 7);
INSERT INTO `groups_rights_actions` VALUES(16, 1, 'pages', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(17, 1, 'pages', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(18, 1, 'pages', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES(19, 1, 'pages', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(20, 1, 'pages', 'templates', 7);
INSERT INTO `groups_rights_actions` VALUES(21, 1, 'pages', 'add_template', 7);
INSERT INTO `groups_rights_actions` VALUES(22, 1, 'pages', 'edit_template', 7);
INSERT INTO `groups_rights_actions` VALUES(23, 1, 'pages', 'delete_template', 7);
INSERT INTO `groups_rights_actions` VALUES(24, 1, 'pages', 'settings', 7);
INSERT INTO `groups_rights_actions` VALUES(25, 1, 'search', 'add_synonym', 7);
INSERT INTO `groups_rights_actions` VALUES(26, 1, 'search', 'edit_synonym', 7);
INSERT INTO `groups_rights_actions` VALUES(27, 1, 'search', 'delete_synonym', 7);
INSERT INTO `groups_rights_actions` VALUES(28, 1, 'search', 'settings', 7);
INSERT INTO `groups_rights_actions` VALUES(29, 1, 'search', 'statistics', 7);
INSERT INTO `groups_rights_actions` VALUES(30, 1, 'search', 'synonyms', 7);
INSERT INTO `groups_rights_actions` VALUES(31, 1, 'content_blocks', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(32, 1, 'content_blocks', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES(33, 1, 'content_blocks', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(34, 1, 'content_blocks', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(35, 1, 'tags', 'autocomplete', 7);
INSERT INTO `groups_rights_actions` VALUES(36, 1, 'tags', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(37, 1, 'tags', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(38, 1, 'tags', 'mass_action', 7);
INSERT INTO `groups_rights_actions` VALUES(39, 1, 'analytics', 'add_landing_page', 7);
INSERT INTO `groups_rights_actions` VALUES(40, 1, 'analytics', 'all_pages', 7);
INSERT INTO `groups_rights_actions` VALUES(41, 1, 'analytics', 'check_status', 7);
INSERT INTO `groups_rights_actions` VALUES(42, 1, 'analytics', 'content', 7);
INSERT INTO `groups_rights_actions` VALUES(43, 1, 'analytics', 'delete_landing_page', 7);
INSERT INTO `groups_rights_actions` VALUES(44, 1, 'analytics', 'detail_page', 7);
INSERT INTO `groups_rights_actions` VALUES(45, 1, 'analytics', 'exit_pages', 7);
INSERT INTO `groups_rights_actions` VALUES(46, 1, 'analytics', 'get_traffic_sources', 7);
INSERT INTO `groups_rights_actions` VALUES(47, 1, 'analytics', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(48, 1, 'analytics', 'landing_pages', 7);
INSERT INTO `groups_rights_actions` VALUES(49, 1, 'analytics', 'loading', 7);
INSERT INTO `groups_rights_actions` VALUES(50, 1, 'analytics', 'mass_landing_page_action', 7);
INSERT INTO `groups_rights_actions` VALUES(51, 1, 'analytics', 'refresh_traffic_sources', 7);
INSERT INTO `groups_rights_actions` VALUES(52, 1, 'analytics', 'settings', 7);
INSERT INTO `groups_rights_actions` VALUES(53, 1, 'blog', 'add_category', 7);
INSERT INTO `groups_rights_actions` VALUES(54, 1, 'blog', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES(55, 1, 'blog', 'categories', 7);
INSERT INTO `groups_rights_actions` VALUES(56, 1, 'blog', 'comments', 7);
INSERT INTO `groups_rights_actions` VALUES(57, 1, 'blog', 'delete_category', 7);
INSERT INTO `groups_rights_actions` VALUES(58, 1, 'blog', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES(59, 1, 'blog', 'edit_category', 7);
INSERT INTO `groups_rights_actions` VALUES(60, 1, 'blog', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES(61, 1, 'blog', 'import_blogger', 7);
INSERT INTO `groups_rights_actions` VALUES(62, 1, 'blog', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES(63, 1, 'blog', 'mass_comment_action', 7);
INSERT INTO `groups_rights_actions` VALUES(64, 1, 'blog', 'settings', 7);

-- --------------------------------------------------------

--
-- Table structure for table `groups_rights_modules`
--

CREATE TABLE `groups_rights_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Dumping data for table `groups_rights_modules`
--

INSERT INTO `groups_rights_modules` VALUES(1, 1, 'dashboard');
INSERT INTO `groups_rights_modules` VALUES(2, 1, 'locale');
INSERT INTO `groups_rights_modules` VALUES(3, 1, 'users');
INSERT INTO `groups_rights_modules` VALUES(4, 1, 'example');
INSERT INTO `groups_rights_modules` VALUES(5, 1, 'settings');
INSERT INTO `groups_rights_modules` VALUES(6, 1, 'pages');
INSERT INTO `groups_rights_modules` VALUES(7, 1, 'search');
INSERT INTO `groups_rights_modules` VALUES(8, 1, 'content_blocks');
INSERT INTO `groups_rights_modules` VALUES(9, 1, 'tags');
INSERT INTO `groups_rights_modules` VALUES(10, 1, 'analytics');
INSERT INTO `groups_rights_modules` VALUES(11, 1, 'blog');

-- --------------------------------------------------------

--
-- Table structure for table `locale`
--

CREATE TABLE `locale` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1224 ;

--
-- Dumping data for table `locale`
--

INSERT INTO `locale` VALUES(1, 1, 'nl', 'backend', 'locale', 'err', 'AlreadyExists', 'Deze vertaling bestaat reeds.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(2, 1, 'nl', 'backend', 'locale', 'err', 'ModuleHasToBeCore', 'De module moet core zijn voor vertalingen in de frontend.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(3, 1, 'nl', 'backend', 'locale', 'err', 'NoSelection', 'Er waren geen vertalingen geselecteerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(4, 1, 'nl', 'backend', 'locale', 'lbl', 'Add', 'vertaling toevoegen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(5, 1, 'nl', 'backend', 'locale', 'msg', 'Added', 'De vertaling "%1$s" werd toegevoegd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(6, 1, 'nl', 'backend', 'locale', 'msg', 'Deleted', 'De geselecteerde vertalingen werden verwijderd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(7, 1, 'nl', 'backend', 'locale', 'msg', 'Edited', 'De vertaling "%1$s" werd opgeslagen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(8, 1, 'nl', 'backend', 'locale', 'msg', 'EditTranslation', 'bewerk vertaling "%1$s"', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(9, 1, 'nl', 'backend', 'locale', 'msg', 'HelpAddName', 'De Engelstalige referentie naar de vertaling, bvb. "Add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(10, 1, 'nl', 'backend', 'locale', 'msg', 'HelpAddValue', 'De vertaling zelf, bvb. "toevoegen".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(11, 1, 'nl', 'backend', 'locale', 'msg', 'HelpEditName', 'De Engelstalige referentie naar de vertaling, bvb. "Add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(12, 1, 'nl', 'backend', 'locale', 'msg', 'HelpEditValue', 'De vertaling zelf, bvb. "toevoegen".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(13, 1, 'nl', 'backend', 'locale', 'msg', 'HelpName', 'De Engelstalige referentie naar de vertaling, bvb. "Add".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(14, 1, 'nl', 'backend', 'locale', 'msg', 'HelpValue', 'De vertaling zelf, bvb. "toevoegen".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(15, 1, 'nl', 'backend', 'locale', 'msg', 'NoItems', 'Er zijn nog geen vertalingen. <a href="%1$s">Voeg de eerste vertaling toe</a>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(16, 1, 'nl', 'backend', 'locale', 'msg', 'NoItemsFilter', 'Er zijn geen vertalingen voor deze filter. <a href="%1$s">Voeg de eerste vertaling toe</a>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(17, 1, 'nl', 'backend', 'locale', 'msg', 'NoItemsAnalyse', 'Er werden geen ontbrekende vertalingen gevonden.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(18, 1, 'en', 'backend', 'locale', 'err', 'AlreadyExists', 'This translation already exists.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(19, 1, 'en', 'backend', 'locale', 'err', 'ModuleHasToBeCore', 'The module needs to be core for frontend translations.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(20, 1, 'en', 'backend', 'locale', 'err', 'NoSelection', 'No translations were selected.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(21, 1, 'en', 'backend', 'locale', 'lbl', 'Add', 'add translation', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(22, 1, 'en', 'backend', 'locale', 'msg', 'Added', 'The translation "%1$s" was added.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(23, 1, 'en', 'backend', 'locale', 'msg', 'Deleted', 'The selected translations were deleted.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(24, 1, 'en', 'backend', 'locale', 'msg', 'Edited', 'The translation "%1$s" was saved.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(25, 1, 'en', 'backend', 'locale', 'msg', 'EditTranslation', 'edit translation "%1$s"', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(26, 1, 'en', 'backend', 'locale', 'msg', 'HelpAddName', 'The English reference for the translation, eg. "Add" This value should start with a capital and may not contain special characters.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(27, 1, 'en', 'backend', 'locale', 'msg', 'HelpAddValue', 'The translation, eg. "add".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(28, 1, 'en', 'backend', 'locale', 'msg', 'HelpEditName', 'The English reference for the translation, eg. "Add". This value should start with a capital and may not contain spaces.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(29, 1, 'en', 'backend', 'locale', 'msg', 'HelpEditValue', 'The translation, eg. "add".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(30, 1, 'en', 'backend', 'locale', 'msg', 'HelpName', 'The english reference for this translation, eg. "Add".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(31, 1, 'en', 'backend', 'locale', 'msg', 'HelpValue', 'The translation, eg. "add".', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(32, 1, 'en', 'backend', 'locale', 'msg', 'NoItems', 'There are no translations yet. <a href="%1$s">Add the first translation</a>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(33, 1, 'en', 'backend', 'locale', 'msg', 'NoItemsFilter', 'There are no translations yet for this filter. <a href="%1$s">Add the first translation</a>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(34, 1, 'en', 'backend', 'locale', 'msg', 'NoItemsAnalyse', 'No missing translations were found.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(35, 1, 'nl', 'backend', 'dashboard', 'lbl', 'AllStatistics', 'alle statistieken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(36, 1, 'nl', 'backend', 'dashboard', 'lbl', 'TopKeywords', 'top zoekwoorden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(37, 1, 'nl', 'backend', 'dashboard', 'lbl', 'TopReferrers', 'top verwijzende sites', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(38, 1, 'en', 'backend', 'dashboard', 'lbl', 'AllStatistics', 'all statistics', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(39, 1, 'en', 'backend', 'dashboard', 'lbl', 'TopKeywords', 'top keywords', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(40, 1, 'en', 'backend', 'dashboard', 'lbl', 'TopReferrers', 'top referrers', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(41, 1, 'nl', 'backend', 'core', 'err', 'ActionNotAllowed', 'Je hebt onvoldoende rechten voor deze actie.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(42, 1, 'nl', 'backend', 'core', 'err', 'AddingCategoryFailed', 'Er ging iets mis.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(43, 1, 'nl', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key werd nog niet geconfigureerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(44, 1, 'nl', 'backend', 'core', 'err', 'AlphaNumericCharactersOnly', 'Enkel alfanumerieke karakters zijn toegestaan.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(45, 1, 'nl', 'backend', 'core', 'err', 'AuthorIsRequired', 'Gelieve een auteur in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(46, 1, 'nl', 'backend', 'core', 'err', 'BrowserNotSupported', '<p>Je gebruikt een verouderde browser die niet ondersteund wordt door Fork CMS. Gebruik een van de volgende goeie alternatieven:</p><ul><li><a href="http://www.microsoft.com/windows/products/winfamily/ie/default.mspx">Internet Explorer *</a>: update naar de nieuwe versie van Internet Explorer.</li><li><a href="http://www.firefox.com/">Firefox</a>: een zeer goeie browser met veel gratis extensies.</li><li><a href="http://www.opera.com/">Opera:</a> Snel en met vele functionaliteiten.</li></ul>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(47, 1, 'nl', 'backend', 'core', 'err', 'CookiesNotEnabled', 'Om Fork CMS te gebruiken moeten cookies geactiveerd zijn in uw browser. Activeer cookies en vernieuw deze pagina.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(48, 1, 'nl', 'backend', 'core', 'err', 'DateIsInvalid', 'Ongeldige datum.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(49, 1, 'nl', 'backend', 'core', 'err', 'DateRangeIsInvalid', 'Ongeldig datum bereik', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(50, 1, 'nl', 'backend', 'core', 'err', 'DebugModeIsActive', 'Debug-mode is nog actief.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(51, 1, 'nl', 'backend', 'core', 'err', 'EmailAlreadyExists', 'Dit e-mailadres is al in gebruik.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(52, 1, 'nl', 'backend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(53, 1, 'nl', 'backend', 'core', 'err', 'EmailIsRequired', 'Gelieve een e-mailadres in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(54, 1, 'nl', 'backend', 'core', 'err', 'EmailIsUnknown', 'Dit e-mailadres zit niet in onze database.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(55, 1, 'nl', 'backend', 'core', 'err', 'EndDateIsInvalid', 'Ongeldige einddatum', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(56, 1, 'nl', 'backend', 'core', 'err', 'FieldIsRequired', 'Dit veld is verplicht.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(57, 1, 'nl', 'backend', 'core', 'err', 'ForkAPIKeys', 'Fork API-keys nog niet geconfigureerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(58, 1, 'nl', 'backend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(59, 1, 'nl', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key werd nog niet geconfigureerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(60, 1, 'nl', 'backend', 'core', 'err', 'InvalidAPIKey', 'Ongeldige API key.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(61, 1, 'nl', 'backend', 'core', 'err', 'InvalidDomain', 'Ongeldig domein.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(62, 1, 'nl', 'backend', 'core', 'err', 'InvalidEmailPasswordCombination', 'De combinatie van e-mail en wachtwoord is niet correct. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Bent u uw wachtwoord vergeten?</a>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(63, 1, 'nl', 'backend', 'core', 'err', 'InvalidName', 'Ongeldige naam.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(64, 1, 'nl', 'backend', 'core', 'err', 'InvalidURL', 'Ongeldige URL.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(65, 1, 'nl', 'backend', 'core', 'err', 'InvalidValue', 'Ongeldige waarde.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(66, 1, 'nl', 'backend', 'core', 'err', 'JavascriptNotEnabled', 'Om Fork CMS te gebruiken moet Javascript geactiveerd zijn in uw browser. Activeer javascript en vernieuw deze pagina.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(67, 1, 'nl', 'backend', 'core', 'err', 'JPGAndGIFOnly', 'Enkel jpg en gif bestanden zijn toegelaten.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(68, 1, 'nl', 'backend', 'core', 'err', 'ModuleNotAllowed', 'Je hebt onvoldoende rechten voor deze module.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(69, 1, 'nl', 'backend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(70, 1, 'nl', 'backend', 'core', 'err', 'NicknameIsRequired', 'Gelieve een publicatienaam in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(71, 1, 'nl', 'backend', 'core', 'err', 'NoCommentsSelected', 'Er waren geen reacties geselecteerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(72, 1, 'nl', 'backend', 'core', 'err', 'NoItemsSelected', 'Er waren geen items geselecteerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(73, 1, 'nl', 'backend', 'core', 'err', 'NoModuleLinked', 'Kan de URL niet genereren. Zorg dat deze module aan een pagina hangt.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(74, 1, 'nl', 'backend', 'core', 'err', 'NonExisting', 'Dit item bestaat niet.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(75, 1, 'nl', 'backend', 'core', 'err', 'NoSelection', 'Er waren geen items geselecteerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(76, 1, 'nl', 'backend', 'core', 'err', 'PasswordIsRequired', 'Gelieve een wachtwoord in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(77, 1, 'nl', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Gelieve het gewenste wachtwoord te herhalen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(78, 1, 'nl', 'backend', 'core', 'err', 'PasswordsDontMatch', 'De wachtwoorden zijn verschillend, probeer het opnieuw.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(79, 1, 'nl', 'backend', 'core', 'err', 'RobotsFileIsNotOK', 'robots.txt zal zoekmachines blokkeren.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(80, 1, 'nl', 'backend', 'core', 'err', 'RSSTitle', 'Blog RSS titel is nog niet ingevuld. <a href="%1$s">Configureer</a>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(81, 1, 'nl', 'backend', 'core', 'err', 'SettingsForkAPIKeys', 'De Fork API-keys zijn niet goed geconfigureerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(82, 1, 'nl', 'backend', 'core', 'err', 'SomethingWentWrong', 'Er liep iets fout.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(83, 1, 'nl', 'backend', 'core', 'err', 'StartDateIsInvalid', 'Ongeldige startdatum', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(84, 1, 'nl', 'backend', 'core', 'err', 'SurnameIsRequired', 'Gelieve een achternaam in te geven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(85, 1, 'nl', 'backend', 'core', 'err', 'TooManyLoginAttempts', 'Te veel loginpogingen. Gelieve even te wachten.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(86, 1, 'nl', 'backend', 'core', 'err', 'TimeIsInvalid', 'Ongeldige tijd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(87, 1, 'nl', 'backend', 'core', 'err', 'TitleIsRequired', 'Geef een titel in.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(88, 1, 'nl', 'backend', 'core', 'err', 'URLAlreadyExists', 'Deze URL bestaat reeds.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(89, 1, 'nl', 'backend', 'core', 'err', 'ValuesDontMatch', 'De waarden komen niet overeen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(90, 1, 'nl', 'backend', 'core', 'err', 'XMLFilesOnly', 'Enkel xml bestanden zijn toegelaten.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(91, 1, 'nl', 'backend', 'core', 'lbl', 'AccountManagement', 'account beheer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(92, 1, 'nl', 'backend', 'core', 'lbl', 'Active', 'actief', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(93, 1, 'nl', 'backend', 'core', 'lbl', 'Add', 'toevoegen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(94, 1, 'nl', 'backend', 'core', 'lbl', 'AddCategory', 'categorie toevoegen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(95, 1, 'nl', 'backend', 'core', 'lbl', 'AddTemplate', 'template toevoegen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(96, 1, 'nl', 'backend', 'core', 'lbl', 'Advanced', 'geavanceerd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(97, 1, 'nl', 'backend', 'core', 'lbl', 'AllComments', 'alle reacties', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(98, 1, 'nl', 'backend', 'core', 'lbl', 'AllowComments', 'reacties toestaan', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(99, 1, 'nl', 'backend', 'core', 'lbl', 'AllPages', 'alle pagina''s', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(100, 1, 'nl', 'backend', 'core', 'lbl', 'Amount', 'aantal', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(101, 1, 'nl', 'backend', 'core', 'lbl', 'Analyse', 'analyse', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(102, 1, 'nl', 'backend', 'core', 'lbl', 'Analysis', 'analysi', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(103, 1, 'nl', 'backend', 'core', 'lbl', 'Analytics', 'analytics', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(104, 1, 'nl', 'backend', 'core', 'lbl', 'APIKey', 'API key', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(105, 1, 'nl', 'backend', 'core', 'lbl', 'APIKeys', 'API keys', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(106, 1, 'nl', 'backend', 'core', 'lbl', 'APIURL', 'API URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(107, 1, 'nl', 'backend', 'core', 'lbl', 'Application', 'applicatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(108, 1, 'nl', 'backend', 'core', 'lbl', 'Approve', 'goedkeuren', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(109, 1, 'nl', 'backend', 'core', 'lbl', 'Archive', 'archief', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(110, 1, 'nl', 'backend', 'core', 'lbl', 'Archived', 'gearchiveerd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(111, 1, 'nl', 'backend', 'core', 'lbl', 'Articles', 'artikels', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(112, 1, 'nl', 'backend', 'core', 'lbl', 'At', 'om', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(113, 1, 'nl', 'backend', 'core', 'lbl', 'Authentication', 'authenticatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(114, 1, 'nl', 'backend', 'core', 'lbl', 'Author', 'auteur', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(115, 1, 'nl', 'backend', 'core', 'lbl', 'Avatar', 'avatar', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(116, 1, 'nl', 'backend', 'core', 'lbl', 'Back', 'terug', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(117, 1, 'nl', 'backend', 'core', 'lbl', 'Backend', 'backend', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(118, 1, 'nl', 'backend', 'core', 'lbl', 'Block', 'blok', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(119, 1, 'nl', 'backend', 'core', 'lbl', 'Blog', 'blog', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(120, 1, 'nl', 'backend', 'core', 'lbl', 'BrowserNotSupported', 'browser niet ondersteund', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(121, 1, 'nl', 'backend', 'core', 'lbl', 'By', 'door', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(122, 1, 'nl', 'backend', 'core', 'lbl', 'Cancel', 'annuleer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(123, 1, 'nl', 'backend', 'core', 'lbl', 'Categories', 'categorieën', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(124, 1, 'nl', 'backend', 'core', 'lbl', 'Category', 'categorie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(125, 1, 'nl', 'backend', 'core', 'lbl', 'ChangePassword', 'wijzig wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(126, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseALanguage', 'kies een taal', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(127, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAModule', 'kies een module', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(128, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAnApplication', 'kies een applicatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(129, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseATemplate', 'kies een template', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(130, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAType', 'kies een type', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(131, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseContent', 'kies inhoud', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(132, 1, 'nl', 'backend', 'core', 'lbl', 'Comment', 'reactie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(133, 1, 'nl', 'backend', 'core', 'lbl', 'Comments', 'reacties', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(134, 1, 'nl', 'backend', 'core', 'lbl', 'ConfirmPassword', 'bevestig wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(135, 1, 'nl', 'backend', 'core', 'lbl', 'Contact', 'contact', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(136, 1, 'nl', 'backend', 'core', 'lbl', 'ContactForm', 'contactformulier', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(137, 1, 'nl', 'backend', 'core', 'lbl', 'Content', 'inhoud', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(138, 1, 'nl', 'backend', 'core', 'lbl', 'ContentBlocks', 'inhoudsblokken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(139, 1, 'nl', 'backend', 'core', 'lbl', 'Core', 'core', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(140, 1, 'nl', 'backend', 'core', 'lbl', 'CustomURL', 'aangepaste URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(141, 1, 'nl', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(142, 1, 'nl', 'backend', 'core', 'lbl', 'Date', 'datum', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(143, 1, 'nl', 'backend', 'core', 'lbl', 'DateAndTime', 'datum en tijd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(144, 1, 'nl', 'backend', 'core', 'lbl', 'DateFormat', 'formaat datums', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(145, 1, 'nl', 'backend', 'core', 'lbl', 'Dear', 'beste', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(146, 1, 'nl', 'backend', 'core', 'lbl', 'DebugMode', 'debug mode', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(147, 1, 'nl', 'backend', 'core', 'lbl', 'Default', 'standaard', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(148, 1, 'nl', 'backend', 'core', 'lbl', 'Delete', 'verwijderen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(149, 1, 'nl', 'backend', 'core', 'lbl', 'DeleteThisTag', 'verwijder deze tag', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(150, 1, 'nl', 'backend', 'core', 'lbl', 'Description', 'beschrijving', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(151, 1, 'nl', 'backend', 'core', 'lbl', 'Developer', 'developer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(152, 1, 'nl', 'backend', 'core', 'lbl', 'Domains', 'domeinen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(153, 1, 'nl', 'backend', 'core', 'lbl', 'Draft', 'kladversie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(154, 1, 'nl', 'backend', 'core', 'lbl', 'Drafts', 'kladversies', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(155, 1, 'nl', 'backend', 'core', 'lbl', 'Edit', 'wijzigen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(156, 1, 'nl', 'backend', 'core', 'lbl', 'EditedOn', 'bewerkt op', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(157, 1, 'nl', 'backend', 'core', 'lbl', 'Editor', 'editor', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(158, 1, 'nl', 'backend', 'core', 'lbl', 'EditProfile', 'bewerk profiel', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(159, 1, 'nl', 'backend', 'core', 'lbl', 'EditTemplate', 'template wijzigen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(160, 1, 'nl', 'backend', 'core', 'lbl', 'Email', 'e-mail', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(161, 1, 'nl', 'backend', 'core', 'lbl', 'EnableModeration', 'moderatie inschakelen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(162, 1, 'nl', 'backend', 'core', 'lbl', 'EndDate', 'einddatum', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(163, 1, 'nl', 'backend', 'core', 'lbl', 'Error', 'error', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(164, 1, 'nl', 'backend', 'core', 'lbl', 'Example', 'voorbeeld', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(165, 1, 'nl', 'backend', 'core', 'lbl', 'Execute', 'uitvoeren', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(166, 1, 'nl', 'backend', 'core', 'lbl', 'ExitPages', 'uitstappagina''s', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(167, 1, 'nl', 'backend', 'core', 'lbl', 'ExtraMetaTags', 'extra metatags', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(168, 1, 'nl', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(169, 1, 'nl', 'backend', 'core', 'lbl', 'File', 'bestand', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(170, 1, 'nl', 'backend', 'core', 'lbl', 'Filename', 'bestandsnaam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(171, 1, 'nl', 'backend', 'core', 'lbl', 'FilterCommentsForSpam', 'filter reacties op spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(172, 1, 'nl', 'backend', 'core', 'lbl', 'From', 'van', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(173, 1, 'nl', 'backend', 'core', 'lbl', 'Frontend', 'frontend', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(174, 1, 'nl', 'backend', 'core', 'lbl', 'General', 'algemeen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(175, 1, 'nl', 'backend', 'core', 'lbl', 'GeneralSettings', 'algemene instellingen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(176, 1, 'nl', 'backend', 'core', 'lbl', 'GoToPage', 'ga naar pagina', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(177, 1, 'nl', 'backend', 'core', 'lbl', 'Group', 'groep', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(178, 1, 'nl', 'backend', 'core', 'lbl', 'Hidden', 'verborgen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(179, 1, 'nl', 'backend', 'core', 'lbl', 'Home', 'home', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(180, 1, 'nl', 'backend', 'core', 'lbl', 'Import', 'importeer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(181, 1, 'nl', 'backend', 'core', 'lbl', 'Interface', 'interface', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(182, 1, 'nl', 'backend', 'core', 'lbl', 'InterfacePreferences', 'voorkeuren interface', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(183, 1, 'nl', 'backend', 'core', 'lbl', 'IP', 'IP', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(184, 1, 'nl', 'backend', 'core', 'lbl', 'ItemsPerPage', 'items per pagina', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(185, 1, 'nl', 'backend', 'core', 'lbl', 'Keyword', 'zoekwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(186, 1, 'nl', 'backend', 'core', 'lbl', 'Keywords', 'sleutelwoorden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(187, 1, 'nl', 'backend', 'core', 'lbl', 'Label', 'label', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(188, 1, 'nl', 'backend', 'core', 'lbl', 'LandingPages', 'landingpagina''s', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(189, 1, 'nl', 'backend', 'core', 'lbl', 'Language', 'taal', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(190, 1, 'nl', 'backend', 'core', 'lbl', 'Languages', 'talen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(191, 1, 'nl', 'backend', 'core', 'lbl', 'LastEdited', 'laatst bewerkt', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(192, 1, 'nl', 'backend', 'core', 'lbl', 'LastEditedOn', 'laatst bewerkt op', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(193, 1, 'nl', 'backend', 'core', 'lbl', 'LastSaved', 'laatst opgeslagen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(194, 1, 'nl', 'backend', 'core', 'lbl', 'LatestComments', 'laatste reacties', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(195, 1, 'nl', 'backend', 'core', 'lbl', 'Layout', 'layout', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(196, 1, 'nl', 'backend', 'core', 'lbl', 'Loading', 'loading', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(197, 1, 'nl', 'backend', 'core', 'lbl', 'Locale', 'locale', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(198, 1, 'nl', 'backend', 'core', 'lbl', 'LoginDetails', 'login gegevens', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(199, 1, 'nl', 'backend', 'core', 'lbl', 'LongDateFormat', 'lange datumformaat', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(200, 1, 'nl', 'backend', 'core', 'lbl', 'MainContent', 'hoofdinhoud', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(201, 1, 'nl', 'backend', 'core', 'lbl', 'MarkAsSpam', 'markeer als spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(202, 1, 'nl', 'backend', 'core', 'lbl', 'Marketing', 'marketing', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(203, 1, 'nl', 'backend', 'core', 'lbl', 'Meta', 'meta', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(204, 1, 'nl', 'backend', 'core', 'lbl', 'MetaData', 'metadata', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(205, 1, 'nl', 'backend', 'core', 'lbl', 'MetaInformation', 'meta-informatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(206, 1, 'nl', 'backend', 'core', 'lbl', 'MetaNavigation', 'metanavigatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(207, 1, 'nl', 'backend', 'core', 'lbl', 'Moderate', 'modereer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(208, 1, 'nl', 'backend', 'core', 'lbl', 'Moderation', 'moderatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(209, 1, 'nl', 'backend', 'core', 'lbl', 'Module', 'module', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(210, 1, 'nl', 'backend', 'core', 'lbl', 'Modules', 'modules', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(211, 1, 'nl', 'backend', 'core', 'lbl', 'ModuleSettings', 'module-instellingen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(212, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToModeration', 'verplaats naar moderatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(213, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToPublished', 'verplaats naar gepubliceerd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(214, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToSpam', 'verplaats naar spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(215, 1, 'nl', 'backend', 'core', 'lbl', 'Name', 'naam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(216, 1, 'nl', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigatietitel', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(217, 1, 'nl', 'backend', 'core', 'lbl', 'NewPassword', 'nieuw wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(218, 1, 'nl', 'backend', 'core', 'lbl', 'News', 'nieuws', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(219, 1, 'nl', 'backend', 'core', 'lbl', 'Next', 'volgende', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(220, 1, 'nl', 'backend', 'core', 'lbl', 'NextPage', 'volgende pagina', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(221, 1, 'nl', 'backend', 'core', 'lbl', 'Nickname', 'publicatienaam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(222, 1, 'nl', 'backend', 'core', 'lbl', 'None', 'geen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(223, 1, 'nl', 'backend', 'core', 'lbl', 'NoTheme', 'geen thema', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(224, 1, 'nl', 'backend', 'core', 'lbl', 'NumberOfBlocks', 'aantal blokken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(225, 1, 'nl', 'backend', 'core', 'lbl', 'OK', 'OK', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(226, 1, 'nl', 'backend', 'core', 'lbl', 'Or', 'of', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(227, 1, 'nl', 'backend', 'core', 'lbl', 'Overview', 'overzicht', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(228, 1, 'nl', 'backend', 'core', 'lbl', 'Page', 'pagina', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(229, 1, 'nl', 'backend', 'core', 'lbl', 'Pages', 'pagina''s', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(230, 1, 'nl', 'backend', 'core', 'lbl', 'PageTitle', 'paginatitel', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(231, 1, 'nl', 'backend', 'core', 'lbl', 'Pageviews', 'paginaweergaves', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(232, 1, 'nl', 'backend', 'core', 'lbl', 'Pagination', 'paginering', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(233, 1, 'nl', 'backend', 'core', 'lbl', 'Password', 'wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(234, 1, 'nl', 'backend', 'core', 'lbl', 'PerDay', 'per dag', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(235, 1, 'nl', 'backend', 'core', 'lbl', 'PerVisit', 'per bezoek', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(236, 1, 'nl', 'backend', 'core', 'lbl', 'Permissions', 'rechten', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(237, 1, 'nl', 'backend', 'core', 'lbl', 'PersonalInformation', 'persoonlijke gegevens', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(238, 1, 'nl', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(239, 1, 'nl', 'backend', 'core', 'lbl', 'Port', 'poort', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(240, 1, 'nl', 'backend', 'core', 'lbl', 'Preview', 'preview', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(241, 1, 'nl', 'backend', 'core', 'lbl', 'Previous', 'vorige', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(242, 1, 'nl', 'backend', 'core', 'lbl', 'PreviousPage', 'vorige pagina', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(243, 1, 'nl', 'backend', 'core', 'lbl', 'PreviousVersions', 'vorige versies', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(244, 1, 'nl', 'backend', 'core', 'lbl', 'Profile', 'profiel', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(245, 1, 'nl', 'backend', 'core', 'lbl', 'Publish', 'publiceer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(246, 1, 'nl', 'backend', 'core', 'lbl', 'Published', 'gepubliceerd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(247, 1, 'nl', 'backend', 'core', 'lbl', 'PublishedArticles', 'gepubliceerde artikels', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(248, 1, 'nl', 'backend', 'core', 'lbl', 'PublishedOn', 'gepubliceerd op', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(249, 1, 'nl', 'backend', 'core', 'lbl', 'PublishOn', 'publiceer op', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(250, 1, 'nl', 'backend', 'core', 'lbl', 'RecentArticlesFull', 'recente artikels (volledig)', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(251, 1, 'nl', 'backend', 'core', 'lbl', 'RecentArticlesList', 'recente artikels (lijst)', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(252, 1, 'nl', 'backend', 'core', 'lbl', 'RecentComments', 'recente reacties', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(253, 1, 'nl', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recent bewerkt', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(254, 1, 'nl', 'backend', 'core', 'lbl', 'RecentVisits', 'recente bezoeken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(255, 1, 'nl', 'backend', 'core', 'lbl', 'ReferenceCode', 'referentiecode', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(256, 1, 'nl', 'backend', 'core', 'lbl', 'Referrer', 'referrer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(257, 1, 'nl', 'backend', 'core', 'lbl', 'RepeatPassword', 'herhaal wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(258, 1, 'nl', 'backend', 'core', 'lbl', 'ReplyTo', 'reply-to', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(259, 1, 'nl', 'backend', 'core', 'lbl', 'RequiredField', 'verplicht veld', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(260, 1, 'nl', 'backend', 'core', 'lbl', 'ResetAndSignIn', 'resetten en aanmelden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(261, 1, 'nl', 'backend', 'core', 'lbl', 'ResetYourPassword', 'reset je wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(262, 1, 'nl', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(263, 1, 'nl', 'backend', 'core', 'lbl', 'Save', 'opslaan', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(264, 1, 'nl', 'backend', 'core', 'lbl', 'SaveDraft', 'kladversie opslaan', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(265, 1, 'nl', 'backend', 'core', 'lbl', 'Scripts', 'scripts', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(266, 1, 'nl', 'backend', 'core', 'lbl', 'Search', 'zoeken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(267, 1, 'nl', 'backend', 'core', 'lbl', 'SearchForm', 'zoekformulier', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(268, 1, 'nl', 'backend', 'core', 'lbl', 'Send', 'verzenden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(269, 1, 'nl', 'backend', 'core', 'lbl', 'SendingEmails', 'e-mails versturen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(270, 1, 'nl', 'backend', 'core', 'lbl', 'SEO', 'SEO', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(271, 1, 'nl', 'backend', 'core', 'lbl', 'Server', 'server', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(272, 1, 'nl', 'backend', 'core', 'lbl', 'Settings', 'instellingen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(273, 1, 'nl', 'backend', 'core', 'lbl', 'ShortDateFormat', 'korte datumformaat', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(274, 1, 'nl', 'backend', 'core', 'lbl', 'SignIn', 'aanmelden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(275, 1, 'nl', 'backend', 'core', 'lbl', 'SignOut', 'afmelden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(276, 1, 'nl', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(277, 1, 'nl', 'backend', 'core', 'lbl', 'SMTP', 'SMTP', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(278, 1, 'nl', 'backend', 'core', 'lbl', 'SortAscending', 'sorteer oplopend', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(279, 1, 'nl', 'backend', 'core', 'lbl', 'SortDescending', 'sorteer aflopend', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(280, 1, 'nl', 'backend', 'core', 'lbl', 'SortedAscending', 'oplopend gesorteerd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(281, 1, 'nl', 'backend', 'core', 'lbl', 'SortedDescending', 'aflopend gesorteerd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(282, 1, 'nl', 'backend', 'core', 'lbl', 'Spam', 'spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(283, 1, 'nl', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(284, 1, 'nl', 'backend', 'core', 'lbl', 'StartDate', 'startdatum', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(285, 1, 'nl', 'backend', 'core', 'lbl', 'Statistics', 'statistieken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(286, 1, 'nl', 'backend', 'core', 'lbl', 'Status', 'status', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(287, 1, 'nl', 'backend', 'core', 'lbl', 'Strong', 'sterk', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(288, 1, 'nl', 'backend', 'core', 'lbl', 'Summary', 'samenvatting', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(289, 1, 'nl', 'backend', 'core', 'lbl', 'Surname', 'achternaam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(290, 1, 'nl', 'backend', 'core', 'lbl', 'Synonym', 'synoniem', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(291, 1, 'nl', 'backend', 'core', 'lbl', 'Synonyms', 'synoniemen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(292, 1, 'nl', 'backend', 'core', 'lbl', 'Tags', 'tags', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(293, 1, 'nl', 'backend', 'core', 'lbl', 'Template', 'template', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(294, 1, 'nl', 'backend', 'core', 'lbl', 'Templates', 'templates', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(295, 1, 'nl', 'backend', 'core', 'lbl', 'Term', 'term', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(296, 1, 'nl', 'backend', 'core', 'lbl', 'Text', 'tekst', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(297, 1, 'nl', 'backend', 'core', 'lbl', 'Themes', 'thema''s', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(298, 1, 'nl', 'backend', 'core', 'lbl', 'ThemesSelection', 'thema-keuze', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(299, 1, 'nl', 'backend', 'core', 'lbl', 'Till', 'tot', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(300, 1, 'nl', 'backend', 'core', 'lbl', 'TimeFormat', 'formaat tijd', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(301, 1, 'nl', 'backend', 'core', 'lbl', 'Title', 'titel', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(302, 1, 'nl', 'backend', 'core', 'lbl', 'Titles', 'titels', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(303, 1, 'nl', 'backend', 'core', 'lbl', 'To', 'aan', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(304, 1, 'nl', 'backend', 'core', 'lbl', 'Today', 'vandaag', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(305, 1, 'nl', 'backend', 'core', 'lbl', 'TrafficSources', 'verkeersbronnen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(306, 1, 'nl', 'backend', 'core', 'lbl', 'Translation', 'vertaling', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(307, 1, 'nl', 'backend', 'core', 'lbl', 'Translations', 'vertalingen', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(308, 1, 'nl', 'backend', 'core', 'lbl', 'Type', 'type', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(309, 1, 'nl', 'backend', 'core', 'lbl', 'UpdateFilter', 'filter updaten', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(310, 1, 'nl', 'backend', 'core', 'lbl', 'URL', 'URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(311, 1, 'nl', 'backend', 'core', 'lbl', 'UsedIn', 'gebruikt in', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(312, 1, 'nl', 'backend', 'core', 'lbl', 'Userguide', 'userguide', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(313, 1, 'nl', 'backend', 'core', 'lbl', 'Username', 'gebruikersnaam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(314, 1, 'nl', 'backend', 'core', 'lbl', 'Users', 'gebruikers', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(315, 1, 'nl', 'backend', 'core', 'lbl', 'UseThisDraft', 'gebruik deze kladversie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(316, 1, 'nl', 'backend', 'core', 'lbl', 'UseThisVersion', 'laad deze versie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(317, 1, 'nl', 'backend', 'core', 'lbl', 'Value', 'waarde', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(318, 1, 'nl', 'backend', 'core', 'lbl', 'View', 'bekijken', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(319, 1, 'nl', 'backend', 'core', 'lbl', 'ViewReport', 'bekijk rapport', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(320, 1, 'nl', 'backend', 'core', 'lbl', 'VisibleOnSite', 'Zichtbaar op de website', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(321, 1, 'nl', 'backend', 'core', 'lbl', 'Visitors', 'bezoekers', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(322, 1, 'nl', 'backend', 'core', 'lbl', 'VisitWebsite', 'bezoek website', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(323, 1, 'nl', 'backend', 'core', 'lbl', 'WaitingForModeration', 'wachten op moderatie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(324, 1, 'nl', 'backend', 'core', 'lbl', 'Weak', 'zwak', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(325, 1, 'nl', 'backend', 'core', 'lbl', 'WebmasterEmail', 'e-mailadres webmaster', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(326, 1, 'nl', 'backend', 'core', 'lbl', 'Website', 'website', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(327, 1, 'nl', 'backend', 'core', 'lbl', 'WebsiteTitle', 'titel website', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(328, 1, 'nl', 'backend', 'core', 'lbl', 'Weight', 'gewicht', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(329, 1, 'nl', 'backend', 'core', 'lbl', 'WhichModule', 'welke module', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(330, 1, 'nl', 'backend', 'core', 'lbl', 'WhichWidget', 'welke widget', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(331, 1, 'nl', 'backend', 'core', 'lbl', 'Widget', 'widget', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(332, 1, 'nl', 'backend', 'core', 'lbl', 'Widgets', 'widgets', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(333, 1, 'nl', 'backend', 'core', 'lbl', 'WithSelected', 'met geselecteerde', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(334, 1, 'nl', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activeer <code>rel="nofollow"</code>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(335, 1, 'nl', 'backend', 'core', 'msg', 'Added', 'Het item werd toegevoegd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(336, 1, 'nl', 'backend', 'core', 'msg', 'AddedCategory', 'De categorie "%1$s" werd toegevoegd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(337, 1, 'nl', 'backend', 'core', 'msg', 'ClickToEdit', 'Klik om te wijzigen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(338, 1, 'nl', 'backend', 'core', 'msg', 'CommentDeleted', 'De reactie werd verwijderd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(339, 1, 'nl', 'backend', 'core', 'msg', 'CommentMovedModeration', 'De reactie werd verplaatst naar moderatie.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(340, 1, 'nl', 'backend', 'core', 'msg', 'CommentMovedPublished', 'De reactie werd gepubliceerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(341, 1, 'nl', 'backend', 'core', 'msg', 'CommentMovedSpam', 'De reactie werd gemarkeerd als spam.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(342, 1, 'nl', 'backend', 'core', 'msg', 'CommentsDeleted', 'De reacties werden verwijderd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(343, 1, 'nl', 'backend', 'core', 'msg', 'CommentsMovedModeration', 'De reacties werden verplaatst naar moderatie.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(344, 1, 'nl', 'backend', 'core', 'msg', 'CommentsMovedPublished', 'De reacties werden gepubliceerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(345, 1, 'nl', 'backend', 'core', 'msg', 'CommentsMovedSpam', 'De reacties werden gemarkeerd als spam.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(346, 1, 'nl', 'backend', 'core', 'msg', 'CommentsToModerate', '%1$s reactie(s) te modereren.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(347, 1, 'nl', 'backend', 'core', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(348, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het item "%1$s" wil verwijderen?', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(349, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Ben je zeker dat je deze categorie "%1$s" wil verwijderen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(350, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmMassDelete', 'Ben je zeker dat je deze item(s) wil verwijderen?', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(351, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmMassSpam', 'Ben je zeker dat je deze item(s) wil markeren als spam?', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(352, 1, 'nl', 'backend', 'core', 'msg', 'DE', 'Duits', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(353, 1, 'nl', 'backend', 'core', 'msg', 'Deleted', 'Het item werd verwijderd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(354, 1, 'nl', 'backend', 'core', 'msg', 'DeletedCategory', 'De categorie "%1$s" werd verwijderd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(355, 1, 'nl', 'backend', 'core', 'msg', 'EditCategory', 'bewerk categorie "%1$s"', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(356, 1, 'nl', 'backend', 'core', 'msg', 'EditComment', 'bewerk reactie', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(357, 1, 'nl', 'backend', 'core', 'msg', 'Edited', 'Het item werd opgeslagen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(358, 1, 'nl', 'backend', 'core', 'msg', 'EditedCategory', 'De categorie "%1$s" werd opgeslagen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(359, 1, 'nl', 'backend', 'core', 'msg', 'EditorImagesWithoutAlt', 'Er zijn afbeeldingen zonder alt-attribute <small>(<a href="http://www.anysurfer.org/elke-afbeelding-heeft-een-alt-attribuut" target="_blank">lees meer</a>)</small>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(360, 1, 'nl', 'backend', 'core', 'msg', 'EditorInvalidLinks', 'Er zijn ongeldige links.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(361, 1, 'nl', 'backend', 'core', 'msg', 'EN', 'Engels', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(362, 1, 'nl', 'backend', 'core', 'msg', 'ES', 'Spaans', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(363, 1, 'nl', 'backend', 'core', 'msg', 'ForgotPassword', 'Wachtwoord vergeten?', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(364, 1, 'nl', 'backend', 'core', 'msg', 'FR', 'Frans', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(365, 1, 'nl', 'backend', 'core', 'msg', 'HelpAvatar', 'Een vierkante foto van je gezicht geeft het beste resultaat.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(366, 1, 'nl', 'backend', 'core', 'msg', 'HelpBlogger', 'Selecteer het bestand dat u heeft geëxporteerd van <a href="http://blogger.com">Blogger</a>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(367, 1, 'nl', 'backend', 'core', 'msg', 'HelpDrafts', 'Hier kan je jouw kladversie zien. Dit zijn tijdelijke versies.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(368, 1, 'nl', 'backend', 'core', 'msg', 'HelpEmailFrom', 'E-mails verzonden vanuit het CMS gebruiken deze instellingen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(369, 1, 'nl', 'backend', 'core', 'msg', 'HelpEmailTo', 'Notificaties van het CMS worden hiernaar verstuurd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(370, 1, 'nl', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'bijv. http://feeds.feedburner.com/jouw-website', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(371, 1, 'nl', 'backend', 'core', 'msg', 'HelpForgotPassword', 'Vul hieronder je e-mail adres in. Je krijgt een e-mail met instructies hoe je een nieuw wachtwoord instelt.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(372, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaCustom', 'Voeg extra, op maat gemaakte metatags toe.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(373, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaDescription', 'Vat de inhoud kort samen. Deze samenvatting wordt getoond in de resultaten van zoekmachines.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(374, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'Kies een aantal goed gekozen termen die de inhoud omschrijven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(375, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaURL', 'Vervang de automatisch gegenereerde URL door een zelfgekozen URL.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(376, 1, 'nl', 'backend', 'core', 'msg', 'HelpNickname', 'De naam waaronder je wilt publiceren (bijvoorbeeld als auteur van een blogartikel).', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(377, 1, 'nl', 'backend', 'core', 'msg', 'HelpResetPassword', 'Vul je gewenste, nieuwe wachtwoord in.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(378, 1, 'nl', 'backend', 'core', 'msg', 'HelpRevisions', 'De laatst opgeslagen versies worden hier bijgehouden. De huidige versie wordt pas overschreven als je opslaat.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(379, 1, 'nl', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Beschrijf bondig wat voor soort inhoud de RSS-feed zal bevatten.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(380, 1, 'nl', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(381, 1, 'nl', 'backend', 'core', 'msg', 'HelpSMTPServer', 'Mailserver die wordt gebruikt voor het versturen van e-mails.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(382, 1, 'nl', 'backend', 'core', 'msg', 'Imported', 'De data werd geïmporteerd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(383, 1, 'nl', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(384, 1, 'nl', 'backend', 'core', 'msg', 'NL', 'Nederlands', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(385, 1, 'nl', 'backend', 'core', 'msg', 'NoAkismetKey', 'Om de spamfilter te activeren moet je een Akismet-key <a href="%1$s">ingeven</a>.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(386, 1, 'nl', 'backend', 'core', 'msg', 'NoComments', 'Er zijn geen reacties in deze categorie.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(387, 1, 'nl', 'backend', 'core', 'msg', 'NoItems', 'Er zijn geen items.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(388, 1, 'nl', 'backend', 'core', 'msg', 'NoPublishedComments', 'Er zijn geen gepubliceerde reacties.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(389, 1, 'nl', 'backend', 'core', 'msg', 'NoRevisions', 'Er zijn nog geen vorige versies.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(390, 1, 'nl', 'backend', 'core', 'msg', 'NoTags', 'Je hebt nog geen tags ingegeven.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(391, 1, 'nl', 'backend', 'core', 'msg', 'NoUsage', 'Nog niet gebruikt.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(392, 1, 'nl', 'backend', 'core', 'msg', 'NowEditing', 'je bewerkt nu', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(393, 1, 'nl', 'backend', 'core', 'msg', 'PasswordResetSuccess', 'Je wachtwoord werd gewijzigd.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(394, 1, 'nl', 'backend', 'core', 'msg', 'Redirecting', 'U wordt omgeleidt.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(395, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailContent', 'Reset je wachtwoord door op de link hieronder te klikken. Indien je niet hier niet om gevraagd hebt hoef je geen actie te ondernemen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(396, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailSubject', 'Wijzig je wachtwoord', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(397, 1, 'nl', 'backend', 'core', 'msg', 'Saved', 'De wijzigingen werden opgeslagen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(398, 1, 'nl', 'backend', 'core', 'msg', 'SavedAsDraft', '"%1$s" als kladversie opgeslagen.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(399, 1, 'nl', 'backend', 'core', 'msg', 'UsingADraft', 'Je gebruikt een kladversie.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(400, 1, 'nl', 'backend', 'core', 'msg', 'UsingARevision', 'Je hebt een oudere versie ingeladen. Sla op om deze versie te gebruiken.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(401, 1, 'en', 'backend', 'core', 'err', 'ActionNotAllowed', 'You have insufficient rights for this action.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(402, 1, 'en', 'backend', 'core', 'err', 'AddingCategoryFailed', 'Something went wrong.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(403, 1, 'en', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key is not yet configured.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(404, 1, 'en', 'backend', 'core', 'err', 'AlphaNumericCharactersOnly', 'Only alphanumeric characters are allowed.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(405, 1, 'en', 'backend', 'core', 'err', 'AuthorIsRequired', 'Please provide an author.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(406, 1, 'en', 'backend', 'core', 'err', 'BrowserNotSupported', '<p>You''re using an outdated browser, which is not supported by Fork CMS. Use one of the following alterntives::</p><ul><li><a href="http://www.microsoft.com/windows/products/winfamily/ie/default.mspx">Internet Explorer *</a>: update to the latest version of Internet Explorer.</li><li><a href="http://www.firefox.com/">Firefox</a>: a very decent browser with a lot of free extensions.</li><li><a href="http://www.opera.com/">Opera:</a> Quick with quite some functionality.</li></ul>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(407, 1, 'en', 'backend', 'core', 'err', 'CookiesNotEnabled', 'You need to enable cookies in order to use Fork CMS. Activate cookies and refresh this page.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(408, 1, 'en', 'backend', 'core', 'err', 'DateIsInvalid', 'Invalid date.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(409, 1, 'en', 'backend', 'core', 'err', 'DateRangeIsInvalid', 'Invalid date range.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(410, 1, 'en', 'backend', 'core', 'err', 'DebugModeIsActive', 'Debug-mode is active.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(411, 1, 'en', 'backend', 'core', 'err', 'EndDateIsInvalid', 'Invalid end date.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(412, 1, 'en', 'backend', 'core', 'err', 'EmailAlreadyExists', 'This e-mailaddress is in use.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(413, 1, 'en', 'backend', 'core', 'err', 'EmailIsInvalid', 'Please provide a valid e-mailaddress.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(414, 1, 'en', 'backend', 'core', 'err', 'EmailIsRequired', 'Please provide a valid e-mailaddress.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(415, 1, 'en', 'backend', 'core', 'err', 'EmailIsUnknown', 'This e-mailaddress is not in our database.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(416, 1, 'en', 'backend', 'core', 'err', 'FieldIsRequired', 'This field is required.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(417, 1, 'en', 'backend', 'core', 'err', 'ForkAPIKeys', 'Fork API-keys are not configured.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(418, 1, 'en', 'backend', 'core', 'err', 'FormError', 'Something went wrong, check the marked fields.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(419, 1, 'en', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key is not configured.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(420, 1, 'en', 'backend', 'core', 'err', 'InvalidAPIKey', 'Invalid API key.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(421, 1, 'en', 'backend', 'core', 'err', 'InvalidDomain', 'Invalid domain.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(422, 1, 'en', 'backend', 'core', 'err', 'InvalidEmailPasswordCombination', 'Your e-mail and password combination is incorrect. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Did you forget your password?</a>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(423, 1, 'en', 'backend', 'core', 'err', 'InvalidName', 'Invalid name.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(424, 1, 'en', 'backend', 'core', 'err', 'InvalidURL', 'Invalid URL.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(425, 1, 'en', 'backend', 'core', 'err', 'InvalidValue', 'Invalid value.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(426, 1, 'en', 'backend', 'core', 'err', 'JavascriptNotEnabled', 'To use Fork CMS, javascript needs to be enabled. Activate javascript and refresh this page.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(427, 1, 'en', 'backend', 'core', 'err', 'JPGAndGIFOnly', 'Only jpg and gif files are allowed.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(428, 1, 'en', 'backend', 'core', 'err', 'ModuleNotAllowed', 'You have insufficient rights for this module.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(429, 1, 'en', 'backend', 'core', 'err', 'NameIsRequired', 'Please provide a name.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(430, 1, 'en', 'backend', 'core', 'err', 'NicknameIsRequired', 'Please provide a publication name.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(431, 1, 'en', 'backend', 'core', 'err', 'NoCommentsSelected', 'No comments were selected.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(432, 1, 'en', 'backend', 'core', 'err', 'NoItemsSelected', 'No items were selected.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(433, 1, 'en', 'backend', 'core', 'err', 'NoModuleLinked', 'Cannot generate URL. Create a page that has this module attached to it.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(434, 1, 'en', 'backend', 'core', 'err', 'NonExisting', 'This item doesn''t exist.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(435, 1, 'en', 'backend', 'core', 'err', 'NoSelection', 'No items were selected.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(436, 1, 'en', 'backend', 'core', 'err', 'PasswordIsRequired', 'Please provide a password.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(437, 1, 'en', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Please repeat the desired password.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(438, 1, 'en', 'backend', 'core', 'err', 'PasswordsDontMatch', 'The passwords differ, please try again.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(439, 1, 'en', 'backend', 'core', 'err', 'RobotsFileIsNotOK', 'robots.txt will block search-engines.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(440, 1, 'en', 'backend', 'core', 'err', 'RSSTitle', 'Blog RSS title is not configured. <a href="%1$s">Configure</a>', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(441, 1, 'en', 'backend', 'core', 'err', 'SettingsForkAPIKeys', 'The Fork API-keys are not configured.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(442, 1, 'en', 'backend', 'core', 'err', 'SomethingWentWrong', 'Something went wrong.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(443, 1, 'en', 'backend', 'core', 'err', 'StartDateIsInvalid', 'Invalid start date.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(444, 1, 'en', 'backend', 'core', 'err', 'SurnameIsRequired', 'Please provide a last name.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(445, 1, 'en', 'backend', 'core', 'err', 'TooManyLoginAttempts', 'Too many login attempts, you need to cool down.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(446, 1, 'en', 'backend', 'core', 'err', 'TimeIsInvalid', 'Invalid time.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(447, 1, 'en', 'backend', 'core', 'err', 'TitleIsRequired', 'Provide a title.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(448, 1, 'en', 'backend', 'core', 'err', 'URLAlreadyExists', 'This URL already exists.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(449, 1, 'en', 'backend', 'core', 'err', 'ValuesDontMatch', 'The values don''t match.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(450, 1, 'en', 'backend', 'core', 'err', 'XMLFilesOnly', 'Only XMl files are allowed.', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(451, 1, 'en', 'backend', 'core', 'lbl', 'AccountManagement', 'account management', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(452, 1, 'en', 'backend', 'core', 'lbl', 'Active', 'active', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(453, 1, 'en', 'backend', 'core', 'lbl', 'Add', 'add', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(454, 1, 'en', 'backend', 'core', 'lbl', 'AddCategory', 'add category', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(455, 1, 'en', 'backend', 'core', 'lbl', 'AddTemplate', 'add template', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(456, 1, 'en', 'backend', 'core', 'lbl', 'Advanced', 'advanced', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(457, 1, 'en', 'backend', 'core', 'lbl', 'AllComments', 'all comments', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(458, 1, 'en', 'backend', 'core', 'lbl', 'AllowComments', 'allow comments', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(459, 1, 'en', 'backend', 'core', 'lbl', 'AllPages', 'all pages', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(460, 1, 'en', 'backend', 'core', 'lbl', 'Amount', 'amount', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(461, 1, 'en', 'backend', 'core', 'lbl', 'Analyse', 'analyse', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(462, 1, 'en', 'backend', 'core', 'lbl', 'Analysis', 'analysis', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(463, 1, 'en', 'backend', 'core', 'lbl', 'Analytics', 'analytics', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(464, 1, 'en', 'backend', 'core', 'lbl', 'APIKey', 'API key', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(465, 1, 'en', 'backend', 'core', 'lbl', 'APIKeys', 'API keys', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(466, 1, 'en', 'backend', 'core', 'lbl', 'APIURL', 'API URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(467, 1, 'en', 'backend', 'core', 'lbl', 'Application', 'application', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(468, 1, 'en', 'backend', 'core', 'lbl', 'Approve', 'approve', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(469, 1, 'en', 'backend', 'core', 'lbl', 'Archive', 'archive', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(470, 1, 'en', 'backend', 'core', 'lbl', 'Archived', 'archived', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(471, 1, 'en', 'backend', 'core', 'lbl', 'Articles', 'articles', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(472, 1, 'en', 'backend', 'core', 'lbl', 'At', 'at', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(473, 1, 'en', 'backend', 'core', 'lbl', 'Authentication', 'authentication', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(474, 1, 'en', 'backend', 'core', 'lbl', 'Author', 'author', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(475, 1, 'en', 'backend', 'core', 'lbl', 'Avatar', 'avatar', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(476, 1, 'en', 'backend', 'core', 'lbl', 'Back', 'back', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(477, 1, 'en', 'backend', 'core', 'lbl', 'Backend', 'backend', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(478, 1, 'en', 'backend', 'core', 'lbl', 'Block', 'block', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(479, 1, 'en', 'backend', 'core', 'lbl', 'Blog', 'blog', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(480, 1, 'en', 'backend', 'core', 'lbl', 'BrowserNotSupported', 'browser not supported', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(481, 1, 'en', 'backend', 'core', 'lbl', 'By', 'by', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(482, 1, 'en', 'backend', 'core', 'lbl', 'Cancel', 'cancel', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(483, 1, 'en', 'backend', 'core', 'lbl', 'Categories', 'categories', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(484, 1, 'en', 'backend', 'core', 'lbl', 'Category', 'category', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(485, 1, 'en', 'backend', 'core', 'lbl', 'ChangePassword', 'change password', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(486, 1, 'en', 'backend', 'core', 'lbl', 'ChooseALanguage', 'choose a language', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(487, 1, 'en', 'backend', 'core', 'lbl', 'ChooseAModule', 'choose a module', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(488, 1, 'en', 'backend', 'core', 'lbl', 'ChooseAnApplication', 'choose an application', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(489, 1, 'en', 'backend', 'core', 'lbl', 'ChooseATemplate', 'choose a template', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(490, 1, 'en', 'backend', 'core', 'lbl', 'ChooseAType', 'choose a type', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(491, 1, 'en', 'backend', 'core', 'lbl', 'ChooseContent', 'choose content', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(492, 1, 'en', 'backend', 'core', 'lbl', 'Comment', 'comment', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(493, 1, 'en', 'backend', 'core', 'lbl', 'Comments', 'comments', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(494, 1, 'en', 'backend', 'core', 'lbl', 'ConfirmPassword', 'confirm password', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(495, 1, 'en', 'backend', 'core', 'lbl', 'Contact', 'contact', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(496, 1, 'en', 'backend', 'core', 'lbl', 'ContactForm', 'contact form', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(497, 1, 'en', 'backend', 'core', 'lbl', 'Content', 'content', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(498, 1, 'en', 'backend', 'core', 'lbl', 'ContentBlocks', 'content blocks', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(499, 1, 'en', 'backend', 'core', 'lbl', 'Core', 'core', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(500, 1, 'en', 'backend', 'core', 'lbl', 'CustomURL', 'custom URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(501, 1, 'en', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(502, 1, 'en', 'backend', 'core', 'lbl', 'Date', 'date', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(503, 1, 'en', 'backend', 'core', 'lbl', 'DateAndTime', 'date and time', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(504, 1, 'en', 'backend', 'core', 'lbl', 'DateFormat', 'date format', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(505, 1, 'en', 'backend', 'core', 'lbl', 'Dear', 'dear', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(506, 1, 'en', 'backend', 'core', 'lbl', 'DebugMode', 'debug mode', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(507, 1, 'en', 'backend', 'core', 'lbl', 'Default', 'default', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(508, 1, 'en', 'backend', 'core', 'lbl', 'Delete', 'delete', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(509, 1, 'en', 'backend', 'core', 'lbl', 'DeleteThisTag', 'delete this tag', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(510, 1, 'en', 'backend', 'core', 'lbl', 'Description', 'description', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(511, 1, 'en', 'backend', 'core', 'lbl', 'Developer', 'developer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(512, 1, 'en', 'backend', 'core', 'lbl', 'Domains', 'domains', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(513, 1, 'en', 'backend', 'core', 'lbl', 'Draft', 'draft', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(514, 1, 'en', 'backend', 'core', 'lbl', 'Drafts', 'drafts', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(515, 1, 'en', 'backend', 'core', 'lbl', 'Edit', 'edit', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(516, 1, 'en', 'backend', 'core', 'lbl', 'EditedOn', 'edited on', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(517, 1, 'en', 'backend', 'core', 'lbl', 'Editor', 'editor', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(518, 1, 'en', 'backend', 'core', 'lbl', 'EditProfile', 'edit profile', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(519, 1, 'en', 'backend', 'core', 'lbl', 'EditTemplate', 'edit template', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(520, 1, 'en', 'backend', 'core', 'lbl', 'Email', 'e-mail', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(521, 1, 'en', 'backend', 'core', 'lbl', 'EnableModeration', 'enable moderation', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(522, 1, 'en', 'backend', 'core', 'lbl', 'EndDate', 'end date', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(523, 1, 'en', 'backend', 'core', 'lbl', 'Error', 'error', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(524, 1, 'en', 'backend', 'core', 'lbl', 'Example', 'example', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(525, 1, 'en', 'backend', 'core', 'lbl', 'Execute', 'execute', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(526, 1, 'en', 'backend', 'core', 'lbl', 'ExitPages', 'exit pages', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(527, 1, 'en', 'backend', 'core', 'lbl', 'ExtraMetaTags', 'extra metatags', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(528, 1, 'en', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(529, 1, 'en', 'backend', 'core', 'lbl', 'File', 'file', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(530, 1, 'en', 'backend', 'core', 'lbl', 'Filename', 'filename', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(531, 1, 'en', 'backend', 'core', 'lbl', 'FilterCommentsForSpam', 'filter comments for spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(532, 1, 'en', 'backend', 'core', 'lbl', 'From', 'from', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(533, 1, 'en', 'backend', 'core', 'lbl', 'Frontend', 'frontend', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(534, 1, 'en', 'backend', 'core', 'lbl', 'General', 'general', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(535, 1, 'en', 'backend', 'core', 'lbl', 'GeneralSettings', 'general settings', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(536, 1, 'en', 'backend', 'core', 'lbl', 'GoToPage', 'go to page', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(537, 1, 'en', 'backend', 'core', 'lbl', 'Group', 'group', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(538, 1, 'en', 'backend', 'core', 'lbl', 'Hidden', 'hidden', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(539, 1, 'en', 'backend', 'core', 'lbl', 'Home', 'home', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(540, 1, 'en', 'backend', 'core', 'lbl', 'Import', 'import', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(541, 1, 'en', 'backend', 'core', 'lbl', 'Interface', 'interface', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(542, 1, 'en', 'backend', 'core', 'lbl', 'InterfacePreferences', 'interface preferences', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(543, 1, 'en', 'backend', 'core', 'lbl', 'IP', 'IP', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(544, 1, 'en', 'backend', 'core', 'lbl', 'ItemsPerPage', 'items per page', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(545, 1, 'en', 'backend', 'core', 'lbl', 'Keyword', 'keyword', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(546, 1, 'en', 'backend', 'core', 'lbl', 'Keywords', 'keywords', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(547, 1, 'en', 'backend', 'core', 'lbl', 'Label', 'label', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(548, 1, 'en', 'backend', 'core', 'lbl', 'LandingPages', 'landing pages', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(549, 1, 'en', 'backend', 'core', 'lbl', 'Language', 'language', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(550, 1, 'en', 'backend', 'core', 'lbl', 'Languages', 'languages', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(551, 1, 'en', 'backend', 'core', 'lbl', 'LastEdited', 'last edited', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(552, 1, 'en', 'backend', 'core', 'lbl', 'LastEditedOn', 'last edited on', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(553, 1, 'en', 'backend', 'core', 'lbl', 'LastSaved', 'last saved', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(554, 1, 'en', 'backend', 'core', 'lbl', 'LatestComments', 'latest comments', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(555, 1, 'en', 'backend', 'core', 'lbl', 'Layout', 'layout', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(556, 1, 'en', 'backend', 'core', 'lbl', 'Loading', 'loading', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(557, 1, 'en', 'backend', 'core', 'lbl', 'Locale', 'locale', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(558, 1, 'en', 'backend', 'core', 'lbl', 'LoginDetails', 'login details', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(559, 1, 'en', 'backend', 'core', 'lbl', 'LongDateFormat', 'long date format', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(560, 1, 'en', 'backend', 'core', 'lbl', 'MainContent', 'main content', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(561, 1, 'en', 'backend', 'core', 'lbl', 'Marketing', 'marketing', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(562, 1, 'en', 'backend', 'core', 'lbl', 'MarkAsSpam', 'mark as spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(563, 1, 'en', 'backend', 'core', 'lbl', 'Meta', 'meta', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(564, 1, 'en', 'backend', 'core', 'lbl', 'MetaData', 'metadata', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(565, 1, 'en', 'backend', 'core', 'lbl', 'MetaInformation', 'meta information', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(566, 1, 'en', 'backend', 'core', 'lbl', 'MetaNavigation', 'meta navigation', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(567, 1, 'en', 'backend', 'core', 'lbl', 'Moderate', 'moderate', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(568, 1, 'en', 'backend', 'core', 'lbl', 'Moderation', 'moderation', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(569, 1, 'en', 'backend', 'core', 'lbl', 'Module', 'module', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(570, 1, 'en', 'backend', 'core', 'lbl', 'Modules', 'modules', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(571, 1, 'en', 'backend', 'core', 'lbl', 'ModuleSettings', 'module settings', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(572, 1, 'en', 'backend', 'core', 'lbl', 'MoveToModeration', 'move to moderation', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(573, 1, 'en', 'backend', 'core', 'lbl', 'MoveToPublished', 'move to published', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(574, 1, 'en', 'backend', 'core', 'lbl', 'MoveToSpam', 'move to spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(575, 1, 'en', 'backend', 'core', 'lbl', 'Name', 'name', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(576, 1, 'en', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigation title', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(577, 1, 'en', 'backend', 'core', 'lbl', 'NewPassword', 'new password', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(578, 1, 'en', 'backend', 'core', 'lbl', 'News', 'news', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(579, 1, 'en', 'backend', 'core', 'lbl', 'Next', 'next', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(580, 1, 'en', 'backend', 'core', 'lbl', 'NextPage', 'next page', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(581, 1, 'en', 'backend', 'core', 'lbl', 'Nickname', 'publication name', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(582, 1, 'en', 'backend', 'core', 'lbl', 'None', 'none', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(583, 1, 'en', 'backend', 'core', 'lbl', 'NoTheme', 'no theme', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(584, 1, 'en', 'backend', 'core', 'lbl', 'NumberOfBlocks', 'number of blocks', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(585, 1, 'en', 'backend', 'core', 'lbl', 'OK', 'OK', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(586, 1, 'en', 'backend', 'core', 'lbl', 'Or', 'or', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(587, 1, 'en', 'backend', 'core', 'lbl', 'Overview', 'overview', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(588, 1, 'en', 'backend', 'core', 'lbl', 'Page', 'page', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(589, 1, 'en', 'backend', 'core', 'lbl', 'Pages', 'pages', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(590, 1, 'en', 'backend', 'core', 'lbl', 'PageTitle', 'pagetitle', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(591, 1, 'en', 'backend', 'core', 'lbl', 'Pageviews', 'pageviews', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(592, 1, 'en', 'backend', 'core', 'lbl', 'Pagination', 'pagination', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(593, 1, 'en', 'backend', 'core', 'lbl', 'Password', 'password', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(594, 1, 'en', 'backend', 'core', 'lbl', 'PerDay', 'per day', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(595, 1, 'en', 'backend', 'core', 'lbl', 'PerVisit', 'per visit', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(596, 1, 'en', 'backend', 'core', 'lbl', 'Permissions', 'permissions', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(597, 1, 'en', 'backend', 'core', 'lbl', 'PersonalInformation', 'personal information', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(598, 1, 'en', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(599, 1, 'en', 'backend', 'core', 'lbl', 'Port', 'port', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(600, 1, 'en', 'backend', 'core', 'lbl', 'Preview', 'preview', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(601, 1, 'en', 'backend', 'core', 'lbl', 'Previous', 'previous', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(602, 1, 'en', 'backend', 'core', 'lbl', 'PreviousPage', 'previous page', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(603, 1, 'en', 'backend', 'core', 'lbl', 'PreviousVersions', 'previous versions', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(604, 1, 'en', 'backend', 'core', 'lbl', 'Profile', 'profile', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(605, 1, 'en', 'backend', 'core', 'lbl', 'Publish', 'publish', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(606, 1, 'en', 'backend', 'core', 'lbl', 'Published', 'published', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(607, 1, 'en', 'backend', 'core', 'lbl', 'PublishedArticles', 'published articles', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(608, 1, 'en', 'backend', 'core', 'lbl', 'PublishedOn', 'published on', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(609, 1, 'en', 'backend', 'core', 'lbl', 'PublishOn', 'publish on', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(610, 1, 'en', 'backend', 'core', 'lbl', 'RecentArticlesFull', 'recent articles (full)', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(611, 1, 'en', 'backend', 'core', 'lbl', 'RecentArticlesList', 'recent articles (list)', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(612, 1, 'en', 'backend', 'core', 'lbl', 'RecentComments', 'recent comments', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(613, 1, 'en', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recently edited', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(614, 1, 'en', 'backend', 'core', 'lbl', 'RecentVisits', 'recent visits', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(615, 1, 'en', 'backend', 'core', 'lbl', 'ReferenceCode', 'reference code', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(616, 1, 'en', 'backend', 'core', 'lbl', 'Referrer', 'referrer', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(617, 1, 'en', 'backend', 'core', 'lbl', 'RepeatPassword', 'repeat password', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(618, 1, 'en', 'backend', 'core', 'lbl', 'ReplyTo', 'reply-to', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(619, 1, 'en', 'backend', 'core', 'lbl', 'RequiredField', 'required field', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(620, 1, 'en', 'backend', 'core', 'lbl', 'ResetAndSignIn', 'reset and sign in', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(621, 1, 'en', 'backend', 'core', 'lbl', 'ResetYourPassword', 'reset your password', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(622, 1, 'en', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(623, 1, 'en', 'backend', 'core', 'lbl', 'Save', 'save', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(624, 1, 'en', 'backend', 'core', 'lbl', 'SaveDraft', 'save draft', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(625, 1, 'en', 'backend', 'core', 'lbl', 'Scripts', 'scripts', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(626, 1, 'en', 'backend', 'core', 'lbl', 'Search', 'search', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(627, 1, 'en', 'backend', 'core', 'lbl', 'SearchForm', 'search form', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(628, 1, 'en', 'backend', 'core', 'lbl', 'Send', 'send', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(629, 1, 'en', 'backend', 'core', 'lbl', 'SendingEmails', 'sending e-mails', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(630, 1, 'en', 'backend', 'core', 'lbl', 'SEO', 'SEO', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(631, 1, 'en', 'backend', 'core', 'lbl', 'Server', 'server', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(632, 1, 'en', 'backend', 'core', 'lbl', 'Settings', 'settings', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(633, 1, 'en', 'backend', 'core', 'lbl', 'ShortDateFormat', 'short date format', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(634, 1, 'en', 'backend', 'core', 'lbl', 'SignIn', 'log in', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(635, 1, 'en', 'backend', 'core', 'lbl', 'SignOut', 'sign out', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(636, 1, 'en', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(637, 1, 'en', 'backend', 'core', 'lbl', 'SMTP', 'SMTP', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(638, 1, 'en', 'backend', 'core', 'lbl', 'SortAscending', 'sort ascending', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(639, 1, 'en', 'backend', 'core', 'lbl', 'SortDescending', 'sort descending', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(640, 1, 'en', 'backend', 'core', 'lbl', 'SortedAscending', 'sorted ascending', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(641, 1, 'en', 'backend', 'core', 'lbl', 'SortedDescending', 'sorted descending', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(642, 1, 'en', 'backend', 'core', 'lbl', 'Spam', 'spam', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(643, 1, 'en', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(644, 1, 'en', 'backend', 'core', 'lbl', 'StartDate', 'start date', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(645, 1, 'en', 'backend', 'core', 'lbl', 'Statistics', 'statistics', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(646, 1, 'en', 'backend', 'core', 'lbl', 'Status', 'status', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(647, 1, 'en', 'backend', 'core', 'lbl', 'Strong', 'strong', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(648, 1, 'en', 'backend', 'core', 'lbl', 'Summary', 'summary', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(649, 1, 'en', 'backend', 'core', 'lbl', 'Surname', 'surname', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(650, 1, 'en', 'backend', 'core', 'lbl', 'Synonym', 'synonym', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(651, 1, 'en', 'backend', 'core', 'lbl', 'Synonyms', 'synonyms', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(652, 1, 'en', 'backend', 'core', 'lbl', 'Tags', 'tags', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(653, 1, 'en', 'backend', 'core', 'lbl', 'Template', 'template', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(654, 1, 'en', 'backend', 'core', 'lbl', 'Templates', 'templates', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(655, 1, 'en', 'backend', 'core', 'lbl', 'Term', 'term', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(656, 1, 'en', 'backend', 'core', 'lbl', 'Text', 'text', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(657, 1, 'en', 'backend', 'core', 'lbl', 'Themes', 'themes', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(658, 1, 'en', 'backend', 'core', 'lbl', 'ThemesSelection', 'theme selection', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(659, 1, 'en', 'backend', 'core', 'lbl', 'Till', 'till', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(660, 1, 'en', 'backend', 'core', 'lbl', 'TimeFormat', 'time format', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(661, 1, 'en', 'backend', 'core', 'lbl', 'Title', 'title', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(662, 1, 'en', 'backend', 'core', 'lbl', 'Titles', 'titles', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(663, 1, 'en', 'backend', 'core', 'lbl', 'To', 'to', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(664, 1, 'en', 'backend', 'core', 'lbl', 'Today', 'today', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(665, 1, 'en', 'backend', 'core', 'lbl', 'TrafficSources', 'traffic sources', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(666, 1, 'en', 'backend', 'core', 'lbl', 'Translation', 'translation', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(667, 1, 'en', 'backend', 'core', 'lbl', 'Translations', 'translations', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(668, 1, 'en', 'backend', 'core', 'lbl', 'Type', 'type', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(669, 1, 'en', 'backend', 'core', 'lbl', 'UpdateFilter', 'update filter', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(670, 1, 'en', 'backend', 'core', 'lbl', 'URL', 'URL', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(671, 1, 'en', 'backend', 'core', 'lbl', 'UsedIn', 'used in', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(672, 1, 'en', 'backend', 'core', 'lbl', 'Userguide', 'userguide', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(673, 1, 'en', 'backend', 'core', 'lbl', 'Username', 'username', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(674, 1, 'en', 'backend', 'core', 'lbl', 'Users', 'users', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(675, 1, 'en', 'backend', 'core', 'lbl', 'UseThisDraft', 'use this draft', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(676, 1, 'en', 'backend', 'core', 'lbl', 'UseThisVersion', 'use this version', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(677, 1, 'en', 'backend', 'core', 'lbl', 'Value', 'value', '2010-09-23 11:22:14');
INSERT INTO `locale` VALUES(678, 1, 'en', 'backend', 'core', 'lbl', 'View', 'view', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(679, 1, 'en', 'backend', 'core', 'lbl', 'ViewReport', 'view report', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(680, 1, 'en', 'backend', 'core', 'lbl', 'VisibleOnSite', 'visible on site', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(681, 1, 'en', 'backend', 'core', 'lbl', 'Visitors', 'visitors', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(682, 1, 'en', 'backend', 'core', 'lbl', 'VisitWebsite', 'visit website', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(683, 1, 'en', 'backend', 'core', 'lbl', 'WaitingForModeration', 'waiting for moderation', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(684, 1, 'en', 'backend', 'core', 'lbl', 'Weak', 'weak', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(685, 1, 'en', 'backend', 'core', 'lbl', 'WebmasterEmail', 'e-mail webmaster', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(686, 1, 'en', 'backend', 'core', 'lbl', 'Website', 'website', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(687, 1, 'en', 'backend', 'core', 'lbl', 'WebsiteTitle', 'website title', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(688, 1, 'en', 'backend', 'core', 'lbl', 'Weight', 'weight', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(689, 1, 'en', 'backend', 'core', 'lbl', 'WhichModule', 'which module', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(690, 1, 'en', 'backend', 'core', 'lbl', 'WhichWidget', 'which widget', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(691, 1, 'en', 'backend', 'core', 'lbl', 'Widget', 'widget', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(692, 1, 'en', 'backend', 'core', 'lbl', 'Widgets', 'widgets', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(693, 1, 'en', 'backend', 'core', 'lbl', 'WithSelected', 'with selected', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(694, 1, 'en', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activate <code>rel="nofollow"</code>', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(695, 1, 'en', 'backend', 'core', 'msg', 'Added', 'The item was added.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(696, 1, 'en', 'backend', 'core', 'msg', 'AddedCategory', 'The category "%1$s" was added.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(697, 1, 'en', 'backend', 'core', 'msg', 'ClickToEdit', 'Click to edit', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(698, 1, 'en', 'backend', 'core', 'msg', 'CommentDeleted', 'The comment was deleted.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(699, 1, 'en', 'backend', 'core', 'msg', 'CommentMovedModeration', 'The comment was moved to moderation.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(700, 1, 'en', 'backend', 'core', 'msg', 'CommentMovedPublished', 'The comment was published.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(701, 1, 'en', 'backend', 'core', 'msg', 'CommentMovedSpam', 'The comment was marked as spam.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(702, 1, 'en', 'backend', 'core', 'msg', 'CommentsDeleted', 'The comments were deleted.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(703, 1, 'en', 'backend', 'core', 'msg', 'CommentsMovedModeration', 'The comments were moved to moderation.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(704, 1, 'en', 'backend', 'core', 'msg', 'CommentsMovedPublished', 'The comments were published.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(705, 1, 'en', 'backend', 'core', 'msg', 'CommentsMovedSpam', 'The comments were marked as spam.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(706, 1, 'en', 'backend', 'core', 'msg', 'CommentsToModerate', '%1$s comment(s) to moderate.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(707, 1, 'en', 'backend', 'core', 'msg', 'ConfigurationError', 'Some settings aren''t configured yet:', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(708, 1, 'en', 'backend', 'core', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the item "%1$s"?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(709, 1, 'en', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Are you sure you want to delete the category "%1$s"?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(710, 1, 'en', 'backend', 'core', 'msg', 'ConfirmMassDelete', 'Are your sure you want to delete this/these item(s)?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(711, 1, 'en', 'backend', 'core', 'msg', 'ConfirmMassSpam', 'Are your sure you want to mark this/these item(s) as spam?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(712, 1, 'en', 'backend', 'core', 'msg', 'DE', 'German', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(713, 1, 'en', 'backend', 'core', 'msg', 'Deleted', 'The item was deleted.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(714, 1, 'en', 'backend', 'core', 'msg', 'DeletedCategory', 'The category "%1$s" was deleted.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(715, 1, 'en', 'backend', 'core', 'msg', 'EditCategory', 'edit category "%1$s"', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(716, 1, 'en', 'backend', 'core', 'msg', 'EditComment', 'edit comment', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(717, 1, 'en', 'backend', 'core', 'msg', 'Edited', 'The item was saved.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(718, 1, 'en', 'backend', 'core', 'msg', 'EditedCategory', 'The category "%1$s" was saved.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(719, 1, 'en', 'backend', 'core', 'msg', 'EditorImagesWithoutAlt', 'There are images without an alt-attribute.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(720, 1, 'en', 'backend', 'core', 'msg', 'EditorInvalidLinks', 'There are invalid links.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(721, 1, 'en', 'backend', 'core', 'msg', 'EN', 'English', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(722, 1, 'en', 'backend', 'core', 'msg', 'ES', 'Spanish', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(723, 1, 'en', 'backend', 'core', 'msg', 'ForgotPassword', 'Forgot password?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(724, 1, 'en', 'backend', 'core', 'msg', 'FR', 'French', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(725, 1, 'en', 'backend', 'core', 'msg', 'HelpAvatar', 'A square picture of your face produces the best results.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(726, 1, 'en', 'backend', 'core', 'msg', 'HelpBlogger', 'Select the file that you exported from <a href="http://blogger.com">Blogger</a>.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(727, 1, 'en', 'backend', 'core', 'msg', 'HelpDrafts', 'Here you can see your draft. These are temporarily versions.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(728, 1, 'en', 'backend', 'core', 'msg', 'HelpEmailFrom', 'E-mails sent from the CMS use these settings.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(729, 1, 'en', 'backend', 'core', 'msg', 'HelpEmailTo', 'Notifications from the CMS are sent here.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(730, 1, 'en', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'eg. http://feeds.feedburner.com/your-website', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(731, 1, 'en', 'backend', 'core', 'msg', 'HelpForgotPassword', 'Below enter your e-mail. You will receive an e-mail containing instructions on how to get a new password.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(732, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaCustom', 'Add your custom metatags here. These will appear in the <code>&lt;head&gt;</code> section of your site.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(733, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaDescription', 'Briefly summarize the content. This summary is shown in the results of search engines.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(734, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'Choose a number of wellthought terms that describe the content.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(735, 1, 'en', 'backend', 'core', 'msg', 'HelpMetaURL', 'Replace the automaticly generated URL by the one you wish.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(736, 1, 'en', 'backend', 'core', 'msg', 'HelpNickname', 'The name you want to be published as (e.g. as the author of an article).', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(737, 1, 'en', 'backend', 'core', 'msg', 'HelpResetPassword', 'Provide your new password.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(738, 1, 'en', 'backend', 'core', 'msg', 'HelpRevisions', 'The last saved versions are kept here. The current version will only be overwritten when you save your changes.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(739, 1, 'en', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Briefly describe what kind of content the RSS feed will contain.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(740, 1, 'en', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Provide a clear title for the RSS feed.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(741, 1, 'en', 'backend', 'core', 'msg', 'HelpSMTPServer', 'Mailserver that should be used for sending e-mails.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(742, 1, 'en', 'backend', 'core', 'msg', 'Imported', 'The data was imported.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(743, 1, 'en', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(744, 1, 'en', 'backend', 'core', 'msg', 'NL', 'Dutch', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(745, 1, 'en', 'backend', 'core', 'msg', 'NoAkismetKey', 'If you want to enable the spam-protection you should <a href="%1$s">configure</a> an Akismet-key.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(746, 1, 'en', 'backend', 'core', 'msg', 'NoComments', 'There are no comments in this category.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(747, 1, 'en', 'backend', 'core', 'msg', 'NoItems', 'There are no items.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(748, 1, 'en', 'backend', 'core', 'msg', 'NoPublishedComments', 'There are no published comments.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(749, 1, 'en', 'backend', 'core', 'msg', 'NoRevisions', 'There are no previous versions yet.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(750, 1, 'en', 'backend', 'core', 'msg', 'NoTags', 'You didn''t use tags yet.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(751, 1, 'en', 'backend', 'core', 'msg', 'NoUsage', 'Not yet used.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(752, 1, 'en', 'backend', 'core', 'msg', 'NowEditing', 'now editing', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(753, 1, 'en', 'backend', 'core', 'msg', 'PasswordResetSuccess', 'Your password has been changed.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(754, 1, 'en', 'backend', 'core', 'msg', 'Redirecting', 'You are being redirected.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(755, 1, 'en', 'backend', 'core', 'msg', 'ResetYourPasswordMailContent', 'Reset your password by clicking the link below. If you didn''t ask for this, you may just ignore this message.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(756, 1, 'en', 'backend', 'core', 'msg', 'ResetYourPasswordMailSubject', 'Change your password', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(757, 1, 'en', 'backend', 'core', 'msg', 'Saved', 'The changes were saved.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(758, 1, 'en', 'backend', 'core', 'msg', 'SavedAsDraft', '"%1$s" saved as draft.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(759, 1, 'en', 'backend', 'core', 'msg', 'UsingADraft', 'You''re using a draft.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(760, 1, 'en', 'backend', 'core', 'msg', 'UsingARevision', 'You''re using an older version. Save to overwrite the current version.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(761, 1, 'nl', 'frontend', 'core', 'act', 'Archive', 'archief', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(762, 1, 'nl', 'frontend', 'core', 'act', 'Category', 'categorie', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(763, 1, 'nl', 'frontend', 'core', 'act', 'Comment', 'reageer', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(764, 1, 'nl', 'frontend', 'core', 'act', 'Comments', 'reacties', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(765, 1, 'nl', 'frontend', 'core', 'act', 'Detail', 'detail', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(766, 1, 'nl', 'frontend', 'core', 'act', 'Rss', 'rss', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(767, 1, 'nl', 'frontend', 'core', 'err', 'AuthorIsRequired', 'Auteur is een verplicht veld.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(768, 1, 'nl', 'frontend', 'core', 'err', 'CommentTimeout', 'Slow down cowboy, er moeten wat tijd tussen iedere reactie zijn.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(769, 1, 'nl', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Er ging iets mis tijdens het verzenden, probeer later opnieuw.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(770, 1, 'nl', 'frontend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(771, 1, 'nl', 'frontend', 'core', 'err', 'EmailIsRequired', 'E-mail is een verplicht veld.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(772, 1, 'nl', 'frontend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(773, 1, 'nl', 'frontend', 'core', 'err', 'InvalidURL', 'Dit is een ongeldige URL.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(774, 1, 'nl', 'frontend', 'core', 'err', 'MessageIsRequired', 'Bericht is een verplicht veld.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(775, 1, 'nl', 'frontend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(776, 1, 'nl', 'frontend', 'core', 'lbl', 'Archive', 'archief', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(777, 1, 'nl', 'frontend', 'core', 'lbl', 'Archives', 'archieven', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(778, 1, 'nl', 'frontend', 'core', 'lbl', 'By', 'door', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(779, 1, 'nl', 'frontend', 'core', 'lbl', 'Category', 'categorie', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(780, 1, 'nl', 'frontend', 'core', 'lbl', 'Categories', 'categorieën', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(781, 1, 'nl', 'frontend', 'core', 'lbl', 'Comment', 'reactie', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(782, 1, 'nl', 'frontend', 'core', 'lbl', 'CommentedOn', 'reageerde op', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(783, 1, 'nl', 'frontend', 'core', 'lbl', 'Comments', 'reacties', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(784, 1, 'nl', 'frontend', 'core', 'lbl', 'Date', 'datum', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(785, 1, 'nl', 'frontend', 'core', 'lbl', 'Email', 'e-mail', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(786, 1, 'nl', 'frontend', 'core', 'lbl', 'GoTo', 'ga naar', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(787, 1, 'nl', 'frontend', 'core', 'lbl', 'GoToPage', 'ga naar pagina', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(788, 1, 'nl', 'frontend', 'core', 'lbl', 'In', 'in', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(789, 1, 'nl', 'frontend', 'core', 'lbl', 'Message', 'bericht', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(790, 1, 'nl', 'frontend', 'core', 'lbl', 'Name', 'naam', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(791, 1, 'nl', 'frontend', 'core', 'lbl', 'NextPage', 'volgende pagina', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(792, 1, 'nl', 'frontend', 'core', 'lbl', 'On', 'op', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(793, 1, 'nl', 'frontend', 'core', 'lbl', 'PreviousPage', 'vorige pagina', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(794, 1, 'nl', 'frontend', 'core', 'lbl', 'RecentComments', 'recente reacties', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(795, 1, 'nl', 'frontend', 'core', 'lbl', 'RequiredField', 'verplicht veld', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(796, 1, 'nl', 'frontend', 'core', 'lbl', 'Send', 'verstuur', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(797, 1, 'nl', 'frontend', 'core', 'lbl', 'Search', 'zoeken', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(798, 1, 'nl', 'frontend', 'core', 'lbl', 'SearchTerm', 'zoekterm', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(799, 1, 'nl', 'frontend', 'core', 'lbl', 'Tags', 'tags', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(800, 1, 'nl', 'frontend', 'core', 'lbl', 'Title', 'titel', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(801, 1, 'nl', 'frontend', 'core', 'lbl', 'Website', 'website', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(802, 1, 'nl', 'frontend', 'core', 'lbl', 'WrittenOn', 'geschreven op', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(803, 1, 'nl', 'frontend', 'core', 'lbl', 'YouAreHere', 'je bent hier', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(804, 1, 'nl', 'frontend', 'core', 'msg', 'Comment', 'reageer', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(805, 1, 'nl', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Uw e-mail werd verzonden.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(806, 1, 'nl', 'frontend', 'core', 'msg', 'ContactSubject', 'E-mail via contactformulier', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(807, 1, 'nl', 'frontend', 'core', 'msg', 'SearchNoItems', 'Er zijn geen resultaten.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(808, 1, 'nl', 'frontend', 'core', 'msg', 'TagsNoItems', 'Er zijn nog geen tags gebruikt.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(809, 1, 'nl', 'frontend', 'core', 'msg', 'WrittenBy', 'geschreven door %1$s', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(810, 1, 'en', 'frontend', 'core', 'act', 'Archive', 'archive', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(811, 1, 'en', 'frontend', 'core', 'act', 'Category', 'category', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(812, 1, 'en', 'frontend', 'core', 'act', 'Comment', 'comment', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(813, 1, 'en', 'frontend', 'core', 'act', 'Comments', 'comments', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(814, 1, 'en', 'frontend', 'core', 'act', 'Detail', 'detail', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(815, 1, 'en', 'frontend', 'core', 'act', 'Rss', 'rss', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(816, 1, 'en', 'frontend', 'core', 'err', 'AuthorIsRequired', 'Author is a required field.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(817, 1, 'en', 'frontend', 'core', 'err', 'CommentTimeout', 'Slow down cowboy, there should be some time between the comments.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(818, 1, 'en', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Something went wrong while trying to send, please try again later.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(819, 1, 'en', 'frontend', 'core', 'err', 'EmailIsInvalid', 'Please provide a valid e-email.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(820, 1, 'en', 'frontend', 'core', 'err', 'EmailIsRequired', 'E-mail is a required field.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(821, 1, 'en', 'frontend', 'core', 'err', 'FormError', 'Something went wrong, please check the marked fields.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(822, 1, 'en', 'frontend', 'core', 'err', 'InvalidURL', 'This is an invalid URL.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(823, 1, 'en', 'frontend', 'core', 'err', 'MessageIsRequired', 'Message is a required field.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(824, 1, 'en', 'frontend', 'core', 'err', 'NameIsRequired', 'Please provide a name.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(825, 1, 'en', 'frontend', 'core', 'lbl', 'Archive', 'archive', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(826, 1, 'en', 'frontend', 'core', 'lbl', 'Archives', 'archives', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(827, 1, 'en', 'frontend', 'core', 'lbl', 'By', 'by', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(828, 1, 'en', 'frontend', 'core', 'lbl', 'Category', 'category', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(829, 1, 'en', 'frontend', 'core', 'lbl', 'Categories', 'categories', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(830, 1, 'en', 'frontend', 'core', 'lbl', 'Comment', 'comment', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(831, 1, 'en', 'frontend', 'core', 'lbl', 'CommentedOn', 'commented on', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(832, 1, 'en', 'frontend', 'core', 'lbl', 'Comments', 'comments', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(833, 1, 'en', 'frontend', 'core', 'lbl', 'Date', 'date', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(834, 1, 'en', 'frontend', 'core', 'lbl', 'Email', 'e-mail', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(835, 1, 'en', 'frontend', 'core', 'lbl', 'GoTo', 'go to', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(836, 1, 'en', 'frontend', 'core', 'lbl', 'GoToPage', 'go to page', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(837, 1, 'en', 'frontend', 'core', 'lbl', 'In', 'in', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(838, 1, 'en', 'frontend', 'core', 'lbl', 'Message', 'message', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(839, 1, 'en', 'frontend', 'core', 'lbl', 'Name', 'name', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(840, 1, 'en', 'frontend', 'core', 'lbl', 'NextPage', 'next page', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(841, 1, 'en', 'frontend', 'core', 'lbl', 'On', 'on', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(842, 1, 'en', 'frontend', 'core', 'lbl', 'PreviousPage', 'previous page', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(843, 1, 'en', 'frontend', 'core', 'lbl', 'RecentComments', 'recent comments', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(844, 1, 'en', 'frontend', 'core', 'lbl', 'RequiredField', 'required field', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(845, 1, 'en', 'frontend', 'core', 'lbl', 'Send', 'send', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(846, 1, 'en', 'frontend', 'core', 'lbl', 'Search', 'search', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(847, 1, 'en', 'frontend', 'core', 'lbl', 'SearchTerm', 'searchterm', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(848, 1, 'en', 'frontend', 'core', 'lbl', 'Tags', 'tags', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(849, 1, 'en', 'frontend', 'core', 'lbl', 'Title', 'title', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(850, 1, 'en', 'frontend', 'core', 'lbl', 'Website', 'website', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(851, 1, 'en', 'frontend', 'core', 'lbl', 'WrittenOn', 'written on', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(852, 1, 'en', 'frontend', 'core', 'lbl', 'YouAreHere', 'you are here', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(853, 1, 'en', 'frontend', 'core', 'msg', 'Comment', 'comment', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(854, 1, 'en', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Your e-mail was sent.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(855, 1, 'en', 'frontend', 'core', 'msg', 'ContactSubject', 'E-mail via contact form.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(856, 1, 'en', 'frontend', 'core', 'msg', 'SearchNoItems', 'There were no results.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(857, 1, 'en', 'frontend', 'core', 'msg', 'TagsNoItems', 'No tags were used.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(858, 1, 'en', 'frontend', 'core', 'msg', 'WrittenBy', 'written by %1$s', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(859, 0, 'nl', 'backend', 'users', 'lbl', 'Add', 'gebruiker toevoegen', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(860, 0, 'nl', 'backend', 'users', 'msg', 'Added', 'De gebruiker "%1$s" werd toegevoegd.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(861, 0, 'nl', 'backend', 'users', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de gebruiker "%1$s" wil verwijderen?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(862, 0, 'nl', 'backend', 'users', 'msg', 'Deleted', 'De gebruiker "%1$s" werd verwijderd.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(863, 0, 'nl', 'backend', 'users', 'msg', 'Edited', 'De instellingen voor "%1$s" werden opgeslagen.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(864, 0, 'nl', 'backend', 'users', 'msg', 'EditUser', 'bewerk gebruiker "%1$s"', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(865, 0, 'nl', 'backend', 'users', 'msg', 'HelpActive', 'Geef deze account toegang tot het CMS.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(866, 0, 'nl', 'backend', 'users', 'msg', 'HelpAPIAccess', 'Geef deze account toegang tot de API.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(867, 0, 'nl', 'backend', 'users', 'msg', 'HelpStrongPassword', 'Sterke wachtwoorden bestaan uit een combinatie van hoofdletters, kleine letters, cijfers en speciale karakters.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(868, 0, 'nl', 'backend', 'users', 'msg', 'Restored', 'De gebruiker "%1$s" werd terug geactiveerd.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(869, 0, 'nl', 'backend', 'users', 'err', 'NonExisting', 'Deze gebruiker bestaat niet.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(870, 0, 'nl', 'backend', 'users', 'err', 'EmailWasDeletedBefore', 'Een gebruiker met dit emailadres werd vroeger verwijderd. <a href="%1$s">Activeer deze gebruiker terug</a>.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(871, 0, 'nl', 'backend', 'users', 'err', 'CantChangeGodsEmail', 'Je kan het emailadres van deze gebruiker niet aanpassen.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(872, 0, 'en', 'backend', 'users', 'lbl', 'Add', 'add user', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(873, 0, 'en', 'backend', 'users', 'msg', 'Added', 'The user "%1$s" was added.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(874, 0, 'en', 'backend', 'users', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the user "%1$s"?', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(875, 0, 'en', 'backend', 'users', 'msg', 'Deleted', 'The user "%1$s" was deleted.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(876, 0, 'en', 'backend', 'users', 'msg', 'Edited', 'The settings for "%1$s" were saved.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(877, 0, 'en', 'backend', 'users', 'msg', 'EditUser', 'edit user "%1$s"', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(878, 0, 'en', 'backend', 'users', 'msg', 'HelpActive', 'Enable CMS access for this account.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(879, 0, 'en', 'backend', 'users', 'msg', 'HelpAPIAccess', 'Enable API access for this account.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(880, 0, 'en', 'backend', 'users', 'msg', 'HelpStrongPassword', 'Strong passwords consist of a combination of capitals, digits, lowercase and special characters.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(881, 0, 'en', 'backend', 'users', 'msg', 'Restored', 'The user "%1$s" is restored.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(882, 0, 'en', 'backend', 'users', 'err', 'CantChangeGodsEmail', 'You can''t change the emailaddres of the GOD-user.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(883, 0, 'en', 'backend', 'users', 'err', 'EmailWasDeletedBefore', 'A user with this emailaddress was deleted. <a href="%1$s">Restore this user</a>.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(884, 0, 'en', 'backend', 'users', 'err', 'NonExisting', 'This user doesn''t exist.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(885, 1, 'nl', 'backend', 'settings', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(886, 1, 'nl', 'backend', 'settings', 'msg', 'HelpAPIKeys', 'Toegangscodes voor gebruikte webservices:', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(887, 1, 'nl', 'backend', 'settings', 'msg', 'HelpDateFormatLong', 'Formaat dat bij de overzichtspagina''s en detailweergaves wordt gebruikt.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(888, 1, 'nl', 'backend', 'settings', 'msg', 'HelpDateFormatShort', 'Dit formaat wordt voornamelijk gebruikt bij tabelweergaves.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(889, 1, 'nl', 'backend', 'settings', 'msg', 'HelpDomains', 'Vul de domeinen in waarop de website te bereiken is (1 domein per regel)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(890, 1, 'nl', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Stuur notificaties van het CMS naar dit e-mailadres.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(891, 1, 'nl', 'backend', 'settings', 'msg', 'HelpFacebookAdminIds', 'Een door komma''s gescheiden lijst met de Facebook-gebruikers hun ID en/of het id van de Facebook-applicatie die de paginas mogen beheren.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(892, 1, 'nl', 'backend', 'settings', 'msg', 'HelpLanguages', 'Duid aan welke talen toegankelijk zijn voor bezoekers', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(893, 1, 'nl', 'backend', 'settings', 'msg', 'HelpRedirectLanguages', 'Duid aan in welke talen mensen op basis van hun browser mogen terechtkomen.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(894, 1, 'nl', 'backend', 'settings', 'msg', 'HelpSendingEmails', 'Je kan e-mails versturen op 2 manieren. Door de ingebouwd mail functie van PHP of via SMTP. We raden je aan om SMTP te gebruiken, aangezien e-mails hierdoor minder snel in de spamfilter zullen terechtkomen.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(895, 1, 'nl', 'backend', 'settings', 'msg', 'HelpScriptsFoot', 'Plaats hier code die onderaan elke pagina geladen moet worden. (bvb. Google Analytics)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(896, 1, 'nl', 'backend', 'settings', 'msg', 'HelpScriptsFootLabel', 'Einde van <code>&lt;body&gt;</code> script(s)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(897, 1, 'nl', 'backend', 'settings', 'msg', 'HelpScriptsHead', 'Plaats hier code die op elke pagina geladen moet worden in de <code>&lt;head&gt;</code>-tag.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(898, 1, 'nl', 'backend', 'settings', 'msg', 'HelpScriptsHeadLabel', '<code>&lt;head&gt;</code> script(s)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(899, 1, 'nl', 'backend', 'settings', 'msg', 'HelpThemes', 'Duid aan welk thema je wil gebruiken.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(900, 1, 'nl', 'backend', 'settings', 'msg', 'HelpTimeFormat', 'Dit formaat wordt gehanteerd bij het weergeven van datums in de frontend.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(901, 1, 'en', 'backend', 'settings', 'msg', 'ConfigurationError', 'Some settings are not yet configured.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(902, 1, 'en', 'backend', 'settings', 'msg', 'HelpAPIKeys', 'Access codes for webservices.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(903, 1, 'en', 'backend', 'settings', 'msg', 'HelpDateFormatLong', 'Format that''s used on overview and detail pages.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(904, 1, 'en', 'backend', 'settings', 'msg', 'HelpDateFormatShort', 'This format is mostly used in table overviews.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(905, 1, 'en', 'backend', 'settings', 'msg', 'HelpDomains', 'Enter the domains on which this website can be reached. (Split domains with linebreaks.)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(906, 1, 'en', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Send CMS notifications to this e-mailaddress.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(907, 1, 'en', 'backend', 'settings', 'msg', 'HelpFacebookAdminIds', 'A comma-separated list of either Facebook user IDs or a Facebook Platform application ID that administers this website.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(908, 1, 'en', 'backend', 'settings', 'msg', 'HelpLanguages', 'Select the languages that are accessible for visitors.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(909, 1, 'en', 'backend', 'settings', 'msg', 'HelpRedirectLanguages', 'Select the languages that people may automatically be redirect to by their browser.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(910, 1, 'en', 'backend', 'settings', 'msg', 'HelpSendingEmails', 'You can send emails in 2 ways. By using PHP''s built-in mail method or via SMTP. We advice you to use SMTP, since this ensures that e-mails are less frequently marked as spam.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(911, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsFoot', 'Paste code that needs to be loaded at the end of the <code>&lt;body&gt;</code> tag here (e.g. Google Analytics).', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(912, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsFootLabel', 'End of <code>&lt;body&gt;</code> script(s)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(913, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsHead', 'Paste code that needs to be loaded in the <code>&lt;head&gt;</code> section here.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(914, 1, 'en', 'backend', 'settings', 'msg', 'HelpScriptsHeadLabel', '<code>&lt;head&gt;</code> script(s)', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(915, 1, 'en', 'backend', 'settings', 'msg', 'HelpThemes', 'Select the theme you wish to use.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(916, 1, 'en', 'backend', 'settings', 'msg', 'HelpTimeFormat', 'This format is used to display dates on the website.', '2010-09-23 11:22:15');
INSERT INTO `locale` VALUES(917, 1, 'nl', 'backend', 'pages', 'err', 'CantBeMoved', 'Pagina kan niet verplaatst worden.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(918, 1, 'nl', 'backend', 'pages', 'err', 'DeleteTemplate', 'Je kan deze template niet verwijderen.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(919, 1, 'nl', 'backend', 'pages', 'err', 'InvalidTemplateSyntax', 'Ongeldige syntax.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(920, 1, 'nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(921, 1, 'nl', 'backend', 'pages', 'lbl', 'EditModuleContent', 'wijzig module inhoud', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(922, 1, 'nl', 'backend', 'pages', 'lbl', 'ExtraTypeBlock', 'module', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(923, 1, 'nl', 'backend', 'pages', 'lbl', 'ExtraTypeWidget', 'widget', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(924, 1, 'nl', 'backend', 'pages', 'lbl', 'Footer', 'navigatie onderaan', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(925, 1, 'nl', 'backend', 'pages', 'lbl', 'MainNavigation', 'hoofdnavigatie', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(926, 1, 'nl', 'backend', 'pages', 'lbl', 'Meta', 'metanavigatie', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(927, 1, 'nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina''s', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(928, 1, 'nl', 'backend', 'pages', 'msg', 'Added', 'De pagina "%1$s" werd toegevoegd.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(929, 1, 'nl', 'backend', 'pages', 'msg', 'AddedTemplate', 'De template "%1$s" werd toegevoegd.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(930, 1, 'nl', 'backend', 'pages', 'msg', 'BlockAttached', 'De module <strong>%1$s</strong> is gekoppeld aan deze sectie.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(931, 1, 'nl', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de pagina "%1$s" wil verwijderen?', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(932, 1, 'nl', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Ben je zeker dat je de template "%1$s" wil verwijderen?', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(933, 1, 'nl', 'backend', 'pages', 'msg', 'Deleted', 'De pagina "%1$s" werd verwijderd.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(934, 1, 'nl', 'backend', 'pages', 'msg', 'DeletedTemplate', 'De template "%1$s" werd verwijderd.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(935, 1, 'nl', 'backend', 'pages', 'msg', 'Edited', 'De pagina "%1$s" werd opgeslagen.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(936, 1, 'nl', 'backend', 'pages', 'msg', 'EditedTemplate', 'De template "%1$s" werd opgeslagen.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(937, 1, 'nl', 'backend', 'pages', 'msg', 'HelpBlockContent', 'Welk soort inhoud wil je hier tonen?', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(938, 1, 'nl', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigatie die (boven het hoofdmenu) op elke pagina staat.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(939, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'De titel die in het menu getoond wordt.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(940, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Zorgt ervoor dat deze pagina de interne PageRank niet beïnvloedt.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(941, 1, 'nl', 'backend', 'pages', 'msg', 'HelpPageTitle', 'De titel die in het browservenster staat (<code>&lt;title&gt;</code>).', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(942, 1, 'nl', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'vb. [1,2],[/,2]', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(943, 1, 'nl', 'backend', 'pages', 'msg', 'HelpTemplateLocation', 'Plaats de templates in de map <code>core/templates</code> van je thema.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(944, 1, 'nl', 'backend', 'pages', 'msg', 'IsAction', 'Deze pagina is een directe subactie.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(945, 1, 'nl', 'backend', 'pages', 'msg', 'MetaNavigation', 'Metanavigatie inschakelen voor deze website.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(946, 1, 'nl', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'Er werd reeds een module gekoppeld aan deze pagina.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(947, 1, 'nl', 'backend', 'pages', 'msg', 'PageIsMoved', 'De pagina "%1$s" werd verplaatst.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(948, 1, 'nl', 'backend', 'pages', 'msg', 'PathToTemplate', 'Pad naar de template', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(949, 1, 'nl', 'backend', 'pages', 'msg', 'RichText', 'Editor', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(950, 1, 'nl', 'backend', 'pages', 'msg', 'TemplateChangeWarning', '<strong>Let op:</strong> de bestaande inhoud zal verloren gaan bij het wijzigen van de template.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(951, 1, 'nl', 'backend', 'pages', 'msg', 'TemplateInUse', 'Deze template is in gebruik, je kan het aantal blokken niet meer aanpassen.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(952, 1, 'nl', 'backend', 'pages', 'msg', 'WidgetAttached', 'De widget <strong>%1$s</strong> is gekoppeld aan deze sectie.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(953, 1, 'en', 'backend', 'pages', 'err', 'CantBeMoved', 'Page can''t be moved.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(954, 1, 'en', 'backend', 'pages', 'err', 'DeletedTemplate', 'You can''t delete this template.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(955, 1, 'en', 'backend', 'pages', 'err', 'InvalidTemplateSyntax', 'Invalid syntax.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(956, 1, 'en', 'backend', 'pages', 'lbl', 'Add', 'add page', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(957, 1, 'en', 'backend', 'pages', 'lbl', 'EditModuleContent', 'edit module content', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(958, 1, 'en', 'backend', 'pages', 'lbl', 'ExtraTypeBlock', 'module', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(959, 1, 'en', 'backend', 'pages', 'lbl', 'ExtraTypeWidget', 'widget', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(960, 1, 'en', 'backend', 'pages', 'lbl', 'Footer', 'bottom navigation', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(961, 1, 'en', 'backend', 'pages', 'lbl', 'MainNavigation', 'main navigation', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(962, 1, 'en', 'backend', 'pages', 'lbl', 'Meta', 'meta navigation', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(963, 1, 'en', 'backend', 'pages', 'lbl', 'Root', 'separate pages', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(964, 1, 'en', 'backend', 'pages', 'msg', 'Added', 'The page "%1$s" was added.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(965, 1, 'en', 'backend', 'pages', 'msg', 'AddedTemplate', 'The template "%1$s" was added.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(966, 1, 'en', 'backend', 'pages', 'msg', 'BlockAttached', 'The module <strong>%1$s</strong> is attached to this section.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(967, 1, 'en', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the page "%1$s"?', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(968, 1, 'en', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Are your sure you want to delete the template "%1$s"?', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(969, 1, 'en', 'backend', 'pages', 'msg', 'Deleted', 'The page "%1$s" was deleted.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(970, 1, 'en', 'backend', 'pages', 'msg', 'DeletedTemplate', 'The template "%1$s" was deleted.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(971, 1, 'en', 'backend', 'pages', 'msg', 'Edited', 'The page "%1$s" was saved.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(972, 1, 'en', 'backend', 'pages', 'msg', 'EditedTemplate', 'The template "%1$s" was saved.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(973, 1, 'en', 'backend', 'pages', 'msg', 'HelpBlockContent', 'What kind of content do you want to show here?', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(974, 1, 'en', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigation (above/below the menu) on every page.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(975, 1, 'en', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'The title that is shown in the menu.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(976, 1, 'en', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Makes sure that this page doesn''t influence the internal PageRank.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(977, 1, 'en', 'backend', 'pages', 'msg', 'HelpPageTitle', 'The title in the browser window (<code>&lt;title&gt;</code>).', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(978, 1, 'en', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'e.g. [1,2],[/,2]', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(979, 1, 'en', 'backend', 'pages', 'msg', 'HelpTemplateLocation', 'Put your templates in the <code>core/templates</code> folder of your theme.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(980, 1, 'en', 'backend', 'pages', 'msg', 'IsAction', 'This page is a direct subaction.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(981, 1, 'en', 'backend', 'pages', 'msg', 'MetaNavigation', 'Enable metanavigation for this website.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(982, 1, 'en', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'A module has already been linked to this page.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(983, 1, 'en', 'backend', 'pages', 'msg', 'PageIsMoved', 'The page "%1$s" was moved.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(984, 1, 'en', 'backend', 'pages', 'msg', 'PathToTemplate', 'Path to template', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(985, 1, 'en', 'backend', 'pages', 'msg', 'RichText', 'Editor', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(986, 1, 'en', 'backend', 'pages', 'msg', 'TemplateChangeWarning', '<strong>Warning:</strong> Existing content will be removed when changing the template.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(987, 1, 'en', 'backend', 'pages', 'msg', 'TemplateInUse', 'This template is in use. You can''t change the number of blocks.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(988, 1, 'en', 'backend', 'pages', 'msg', 'WidgetAttached', 'The widget <strong>%1$s</strong> is attached to this section.', '2010-09-23 11:22:16');
INSERT INTO `locale` VALUES(989, 1, 'nl', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synoniemen zijn verplicht.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(990, 1, 'nl', 'backend', 'search', 'err', 'TermIsRequired', 'De zoekterm is verplicht.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(991, 1, 'nl', 'backend', 'search', 'err', 'TermExists', 'Synoniemen voor deze zoekterm bestaan reeds.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(992, 1, 'nl', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(993, 1, 'nl', 'backend', 'search', 'lbl', 'AddSynonym', 'synoniem toevoegen', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(994, 1, 'nl', 'backend', 'search', 'lbl', 'DeleteSynonym', 'synoniem verwijderen', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(995, 1, 'nl', 'backend', 'search', 'lbl', 'EditSynonym', 'synoniem bewerken', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(996, 1, 'nl', 'backend', 'search', 'lbl', 'ModuleWeight', 'module gewicht', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(997, 1, 'nl', 'backend', 'search', 'lbl', 'SearchedOn', 'gezocht op', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(998, 1, 'nl', 'backend', 'search', 'msg', 'AddedSynonym', 'Het synoniem voor zoekterm "%1$s" werd toegevoegd.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(999, 1, 'nl', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Ben je zeker dat je de synoniemen voor zoekterm "%1$s" wil verwijderen?', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1000, 1, 'nl', 'backend', 'search', 'msg', 'DeletedSynonym', 'Het synoniem voor zoekterm "%1$s" werd verwijderd.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1001, 1, 'nl', 'backend', 'search', 'msg', 'EditedSynonym', 'Het synoniem voor zoekterm "%1$s" werd opgeslagen.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1002, 1, 'nl', 'backend', 'search', 'msg', 'HelpWeight', 'Het standaard gewicht is 1. Als je zoekresultaten van een specifieke module belangrijker vindt, verhoog dan het gewicht. vb. als pagina''s gewicht "3" heeft dan zullen resultaten van deze module 3 keer meer kans hebben om voor te komen in de zoekresultaten.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1003, 1, 'nl', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Definieer de belangrijkheid van iedere module in de zoekresultaten.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1004, 1, 'nl', 'backend', 'search', 'msg', 'IncludeInSearch', 'Opnemen in de zoekresultaten?', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1005, 1, 'nl', 'backend', 'search', 'msg', 'NoStatistics', 'Er zijn nog geen statistieken.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1006, 1, 'nl', 'backend', 'search', 'msg', 'NoSynonyms', 'Er zijn nog geen synoniemen. <a href="%1$s">Voeg het eerste synoniem toe</a>.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1007, 1, 'nl', 'backend', 'search', 'msg', 'NoSynonymsBox', 'Er zijn nog geen synoniemen.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1008, 1, 'en', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synonyms are required.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1009, 1, 'en', 'backend', 'search', 'err', 'TermIsRequired', 'The searchterm is required.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1010, 1, 'en', 'backend', 'search', 'err', 'TermExists', 'Synonyms for this searchterm already exist.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1011, 1, 'en', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1012, 1, 'en', 'backend', 'search', 'lbl', 'AddSynonym', 'add synonym', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1013, 1, 'en', 'backend', 'search', 'lbl', 'DeleteSynonym', 'delete synonym', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1014, 1, 'en', 'backend', 'search', 'lbl', 'EditSynonym', 'edit synonym', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1015, 1, 'en', 'backend', 'search', 'lbl', 'ModuleWeight', 'module weight', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1016, 1, 'en', 'backend', 'search', 'lbl', 'SearchedOn', 'searched on', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1017, 1, 'en', 'backend', 'search', 'msg', 'AddedSynonym', 'The synonym for the searchterm "%1$s" was added.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1018, 1, 'en', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Are you sure you want to delete the synonyms for the searchterm "%1$s"?', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1019, 1, 'en', 'backend', 'search', 'msg', 'DeletedSynonym', 'The synonym for the searchterm "%1$s" was deleted.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1020, 1, 'en', 'backend', 'search', 'msg', 'EditedSynonym', 'The synonym for the searchterm "%1$s" was saved.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1021, 1, 'en', 'backend', 'search', 'msg', 'HelpWeight', 'The default weight is 1. If you want to give search results from a specific module more importance, increase the weight. E.g. if pages has weight "3" then they are 3 times as likely to show up higher in search results.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1022, 1, 'en', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Define the importance of each module in search results here.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1023, 1, 'en', 'backend', 'search', 'msg', 'IncludeInSearch', 'Include in search results?', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1024, 1, 'en', 'backend', 'search', 'msg', 'NoStatistics', 'There are no statistics yet.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1025, 1, 'en', 'backend', 'search', 'msg', 'NoSynonyms', 'There are no synonyms yet. <a href="%1$s">Add the first synonym</a>.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1026, 1, 'en', 'backend', 'search', 'msg', 'NoSynonymsBox', 'There are no synonyms yet.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1027, 1, 'nl', 'backend', 'content_blocks', 'lbl', 'Add', 'inhoudsblok toevoegen', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1028, 1, 'nl', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'bewerk inhoudsblok "%1$s"', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1029, 1, 'nl', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de inhoudsblok "%1$s" wil verwijderen?', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1030, 1, 'nl', 'backend', 'content_blocks', 'msg', 'Added', 'Het inhoudsblok "%1$s" werd toegevoegd.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1031, 1, 'nl', 'backend', 'content_blocks', 'msg', 'Edited', 'Het inhoudsblok "%1$s" werd opgeslagen.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1032, 1, 'nl', 'backend', 'content_blocks', 'msg', 'Deleted', 'Het inhoudsblok "%1$s" werd verwijderd.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1033, 1, 'en', 'backend', 'content_blocks', 'lbl', 'Add', 'add content block', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1034, 1, 'en', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'edit content block "%1$s"', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1035, 1, 'en', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the content block "%1$s"?', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1036, 1, 'en', 'backend', 'content_blocks', 'msg', 'Added', 'The content block "%1$s" was added.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1037, 1, 'en', 'backend', 'content_blocks', 'msg', 'Edited', 'The content block "%1$s" was saved.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1038, 1, 'en', 'backend', 'content_blocks', 'msg', 'Deleted', 'The content block "%1$s" was deleted.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1039, 1, 'nl', 'backend', 'tags', 'msg', 'Edited', 'De tag "%1$s" werd opgeslagen.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1040, 1, 'nl', 'backend', 'tags', 'msg', 'EditTag', 'bewerk tag "%1$s"', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1041, 1, 'nl', 'backend', 'tags', 'msg', 'Deleted', 'De geselecteerde tag(s) werd(en) verwijderd.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1042, 1, 'nl', 'backend', 'tags', 'msg', 'NoItems', 'Er zijn nog geen tags.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1043, 1, 'nl', 'backend', 'tags', 'err', 'NonExisting', 'Deze tag bestaat niet.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1044, 1, 'nl', 'backend', 'tags', 'err', 'NoSelection', 'Er waren geen tags geselecteerd.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1045, 1, 'en', 'backend', 'tags', 'msg', 'Edited', 'The tag "%1$s" was saved.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1046, 1, 'en', 'backend', 'tags', 'msg', 'EditTag', 'edit tag "%1$s"', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1047, 1, 'en', 'backend', 'tags', 'msg', 'Deleted', 'The selected tag(s) was/were deleted.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1048, 1, 'en', 'backend', 'tags', 'msg', 'NoItems', 'There are no tags yet.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1049, 1, 'en', 'backend', 'tags', 'err', 'NonExisting', 'This tag doesn''t exist.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1050, 1, 'en', 'backend', 'tags', 'err', 'NoSelection', 'No tags were selected.', '2010-09-23 11:22:17');
INSERT INTO `locale` VALUES(1051, 1, 'nl', 'backend', 'core', 'msg', 'NoReferrers', 'Er zijn nog geen statistieken van verwijzende sites.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1052, 1, 'nl', 'backend', 'analytics', 'err', 'AnalyseNoSessionToken', 'Er is nog geen Google analytics account gekoppeld. <a href="%1$s">Configureer</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1053, 1, 'nl', 'backend', 'analytics', 'err', 'AnalyseNoTableId', 'Er is nog geen analytics website profiel gekoppeld. <a href="%1$s">Configureer</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1054, 1, 'nl', 'backend', 'analytics', 'err', 'NoSessionToken', 'Er is nog geen Google account gekoppeld.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1055, 1, 'nl', 'backend', 'analytics', 'err', 'NoTableId', 'Er is nog geen website profiel gekoppeld.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1056, 1, 'nl', 'backend', 'analytics', 'lbl', 'AddLandingPage', 'landingspagina toevoegen', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1057, 1, 'nl', 'backend', 'analytics', 'lbl', 'AllStatistics', 'alle statistieken', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1058, 1, 'nl', 'backend', 'analytics', 'lbl', 'AverageTimeOnPage', 'gemiddelde tijd op pagina', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1059, 1, 'nl', 'backend', 'analytics', 'lbl', 'AverageTimeOnSite', 'gemiddelde tijd op site', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1060, 1, 'nl', 'backend', 'analytics', 'lbl', 'BounceRate', 'weigeringspercentage', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1061, 1, 'nl', 'backend', 'analytics', 'lbl', 'Bounces', 'weigeringen', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1062, 1, 'nl', 'backend', 'analytics', 'lbl', 'ChangePeriod', 'periode aanpassen', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1063, 1, 'nl', 'backend', 'analytics', 'lbl', 'ChooseThisAccount', 'kies deze account', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1064, 1, 'nl', 'backend', 'analytics', 'lbl', 'DirectTraffic', 'direct verkeer', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1065, 1, 'nl', 'backend', 'analytics', 'lbl', 'Entrances', 'instappunten', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1066, 1, 'nl', 'backend', 'analytics', 'lbl', 'ExitRate', 'uitstappercentage', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1067, 1, 'nl', 'backend', 'analytics', 'lbl', 'Exits', 'uitstappunten', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1068, 1, 'nl', 'backend', 'analytics', 'lbl', 'GetLiveData', 'haal live gegevens op', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1069, 1, 'nl', 'backend', 'analytics', 'lbl', 'GoogleAnalyticsLink', 'koppeling met Google Analytics', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1070, 1, 'nl', 'backend', 'analytics', 'lbl', 'LinkedAccount', 'gekoppelde account', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1071, 1, 'nl', 'backend', 'analytics', 'lbl', 'LinkedProfile', 'gekoppeld profiel', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1072, 1, 'nl', 'backend', 'analytics', 'lbl', 'NewVisitsPercentage', 'nieuw bezoekpercentage', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1073, 1, 'nl', 'backend', 'analytics', 'lbl', 'PagesPerVisit', 'pagina''s per bezoek', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1074, 1, 'nl', 'backend', 'analytics', 'lbl', 'Pageviews', 'paginaweergaves', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1075, 1, 'nl', 'backend', 'analytics', 'lbl', 'PageviewsByTrafficSources', 'paginaweergaves per verkeersbron', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1076, 1, 'nl', 'backend', 'analytics', 'lbl', 'PercentageOfSiteTotal', 'percentage van sitetotaal', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1077, 1, 'nl', 'backend', 'analytics', 'lbl', 'PeriodStatistics', 'periode statistieken', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1078, 1, 'nl', 'backend', 'analytics', 'lbl', 'Referral', 'verwijzende site', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1079, 1, 'nl', 'backend', 'analytics', 'lbl', 'SearchEngines', 'zoekmachines', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1080, 1, 'nl', 'backend', 'analytics', 'lbl', 'SiteAverage', 'sitegemidddelde', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1081, 1, 'nl', 'backend', 'analytics', 'lbl', 'TimeOnSite', 'bezoekduur', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1082, 1, 'nl', 'backend', 'analytics', 'lbl', 'TopContent', 'belangrijkste inhoud', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1083, 1, 'nl', 'backend', 'analytics', 'lbl', 'TopExitPages', 'belangrijkste uitstappagina''s', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1084, 1, 'nl', 'backend', 'analytics', 'lbl', 'TopKeywords', 'belangrijkste zoekwoorden', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1085, 1, 'nl', 'backend', 'analytics', 'lbl', 'TopLandingPages', 'belangrijkste landingspagina''s', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1086, 1, 'nl', 'backend', 'analytics', 'lbl', 'TopPages', 'belangrijkste pagina''s', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1087, 1, 'nl', 'backend', 'analytics', 'lbl', 'TopReferrers', 'belangrijkste verwijzende sites', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1088, 1, 'nl', 'backend', 'analytics', 'lbl', 'UniquePageviews', 'unieke paginaweergaves', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1089, 1, 'nl', 'backend', 'analytics', 'lbl', 'Views', 'weergaves', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1090, 1, 'nl', 'backend', 'analytics', 'lbl', 'Visits', 'bezoeken', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1091, 1, 'nl', 'backend', 'analytics', 'msg', 'AuthenticateAtGoogle', 'Authentificatie bij Google starten', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1092, 1, 'nl', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkAccount', 'Weet u zeker dat u de koppeling met de account "%1$s" wil verwijderen?<br />Alle opgeslagen statistieken worden dan verwijderd uit het CMS.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1093, 1, 'nl', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkGoogleAccount', 'Weet u zeker dat u de koppeling met uw Google account wilt verwijderen?', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1094, 1, 'nl', 'backend', 'analytics', 'msg', 'GetDataError', 'Er liep iets mis bij het ophalen van de gegevens via Google Analytics. Onze excuses voor het ongemak. Probeer het later nog eens.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1095, 1, 'nl', 'backend', 'analytics', 'msg', 'LinkGoogleAccount', 'Koppel uw Google account aan Fork CMS', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1096, 1, 'nl', 'backend', 'analytics', 'msg', 'LinkWebsiteProfile', 'Koppel een Google Analytics website profiel aan Fork CMS', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1097, 1, 'nl', 'backend', 'analytics', 'msg', 'LoadingData', 'Fork haalt momenteel de gegevens binnen via Google Analytics.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1098, 1, 'nl', 'backend', 'analytics', 'msg', 'NoAccounts', 'Er hangen geen website profielen aan deze Google account. Meld je af bij Google en probeer het met een andere account.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1099, 1, 'nl', 'backend', 'analytics', 'msg', 'NoContent', 'Er zijn nog geen statistieken van inhoud.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1100, 1, 'nl', 'backend', 'analytics', 'msg', 'NoExitPages', 'Er zijn nog geen statistieken van uitstappagina''s', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1101, 1, 'nl', 'backend', 'analytics', 'msg', 'NoKeywords', 'Er zijn nog geen statistieken van zoekwoorden.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1102, 1, 'nl', 'backend', 'analytics', 'msg', 'NoLandingPages', 'Er zijn nog geen statistieken van landingpagina''s.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1103, 1, 'nl', 'backend', 'analytics', 'msg', 'NoPages', 'Er zijn nog geen statistieken van pagina''s.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1104, 1, 'nl', 'backend', 'analytics', 'msg', 'PagesHaveBeenViewedTimes', 'Pagina''s op deze site zijn in totaal %1$s keer bekeken.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1105, 1, 'nl', 'backend', 'analytics', 'msg', 'RefreshedTrafficSources', 'De verkeersbronnen werden vernieuwd.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1106, 1, 'nl', 'backend', 'analytics', 'msg', 'RemoveAccountLink', 'Verwijder de koppeling met uw Google account', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1107, 1, 'nl', 'backend', 'analytics', 'msg', 'RemoveProfileLink', 'Verwijder de koppeling met uw Analytics website profiel', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1108, 1, 'en', 'backend', 'core', 'msg', 'NoReferrers', 'There are no referrers yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1109, 1, 'en', 'backend', 'analytics', 'err', 'AnalyseNoSessionToken', 'There is no link with a Google analytics account yet. <a href="%1$s">Configure</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1110, 1, 'en', 'backend', 'analytics', 'err', 'AnalyseNoTableId', 'There is no link with an analytics website profile yet. <a href="%1$s">Configure</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1111, 1, 'en', 'backend', 'analytics', 'err', 'NoSessionToken', 'There is no link with a Google analytics account yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1112, 1, 'en', 'backend', 'analytics', 'err', 'NoTableId', 'There is no link with an analytics website profile yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1113, 1, 'en', 'backend', 'analytics', 'lbl', 'AddLandingPage', 'add landing page', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1114, 1, 'en', 'backend', 'analytics', 'lbl', 'AllStatistics', 'all statistics', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1115, 1, 'en', 'backend', 'analytics', 'lbl', 'AverageTimeOnPage', 'average time on page', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1116, 1, 'en', 'backend', 'analytics', 'lbl', 'AverageTimeOnSite', 'average time on site', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1117, 1, 'en', 'backend', 'analytics', 'lbl', 'BounceRate', 'bounce rate', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1118, 1, 'en', 'backend', 'analytics', 'lbl', 'Bounces', 'bounces', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1119, 1, 'en', 'backend', 'analytics', 'lbl', 'ChangePeriod', 'change period', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1120, 1, 'en', 'backend', 'analytics', 'lbl', 'ChooseThisAccount', 'choose this account', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1121, 1, 'en', 'backend', 'analytics', 'lbl', 'DirectTraffic', 'direct traffic', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1122, 1, 'en', 'backend', 'analytics', 'lbl', 'Entrances', 'entrances', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1123, 1, 'en', 'backend', 'analytics', 'lbl', 'ExitRate', 'exit rate', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1124, 1, 'en', 'backend', 'analytics', 'lbl', 'Exits', 'exits', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1125, 1, 'en', 'backend', 'analytics', 'lbl', 'GetLiveData', 'collect live data', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1126, 1, 'en', 'backend', 'analytics', 'lbl', 'GoogleAnalyticsLink', 'link to Google Analytics', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1127, 1, 'en', 'backend', 'analytics', 'lbl', 'LinkedAccount', 'linked account', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1128, 1, 'en', 'backend', 'analytics', 'lbl', 'LinkedProfile', 'linked profile', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1129, 1, 'en', 'backend', 'analytics', 'lbl', 'NewVisitsPercentage', 'new visits percentage', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1130, 1, 'en', 'backend', 'analytics', 'lbl', 'PagesPerVisit', 'pages per visit', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1131, 1, 'en', 'backend', 'analytics', 'lbl', 'Pageviews', 'pageviews', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1132, 1, 'en', 'backend', 'analytics', 'lbl', 'PageviewsByTrafficSources', 'pageviews per traffic source', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1133, 1, 'en', 'backend', 'analytics', 'lbl', 'PercentageOfSiteTotal', 'percentage of site total', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1134, 1, 'en', 'backend', 'analytics', 'lbl', 'PeriodStatistics', 'period statistics', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1135, 1, 'en', 'backend', 'analytics', 'lbl', 'Referral', 'referring site', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1136, 1, 'en', 'backend', 'analytics', 'lbl', 'SearchEngines', 'search engines', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1137, 1, 'en', 'backend', 'analytics', 'lbl', 'SiteAverage', 'site average', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1138, 1, 'en', 'backend', 'analytics', 'lbl', 'TimeOnSite', 'time on site', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1139, 1, 'en', 'backend', 'analytics', 'lbl', 'TopContent', 'top content', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1140, 1, 'en', 'backend', 'analytics', 'lbl', 'TopExitPages', 'top exit pages', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1141, 1, 'en', 'backend', 'analytics', 'lbl', 'TopKeywords', 'top keywords', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1142, 1, 'en', 'backend', 'analytics', 'lbl', 'TopLandingPages', 'top landing pages', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1143, 1, 'en', 'backend', 'analytics', 'lbl', 'TopPages', 'top pages', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1144, 1, 'en', 'backend', 'analytics', 'lbl', 'TopReferrers', 'top referrers', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1145, 1, 'en', 'backend', 'analytics', 'lbl', 'UniquePageviews', 'unique pageviews', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1146, 1, 'en', 'backend', 'analytics', 'lbl', 'Views', 'views', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1147, 1, 'en', 'backend', 'analytics', 'lbl', 'Visits', 'visits', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1148, 1, 'en', 'backend', 'analytics', 'msg', 'AuthenticateAtGoogle', 'Start Google authentication', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1149, 1, 'en', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkAccount', 'Are you sure you want to remove the link with the account "%1$s"?<br />All saves statistics will be deleted from the CMS.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1150, 1, 'en', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkGoogleAccount', 'Are you sure you want to remove the link with your Google account?', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1151, 1, 'en', 'backend', 'analytics', 'msg', 'GetDataError', 'Something went wrong while collecting the data from Google Analytics. Our appologies for the inconvenience. Please try again later.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1152, 1, 'en', 'backend', 'analytics', 'msg', 'LinkGoogleAccount', 'Link your Google account to Fork CMS.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1153, 1, 'en', 'backend', 'analytics', 'msg', 'LinkWebsiteProfile', 'Link your Google Analytics website profile to Fork CMS.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1154, 1, 'en', 'backend', 'analytics', 'msg', 'LoadingData', 'Fork is collecting the data from Google Analytics.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1155, 1, 'en', 'backend', 'analytics', 'msg', 'NoAccounts', 'There are no website profiles linked to this Google account. Log off at Google and try with a different account.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1156, 1, 'en', 'backend', 'analytics', 'msg', 'NoContent', 'There is no content yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1157, 1, 'en', 'backend', 'analytics', 'msg', 'NoExitPages', 'There are no exit pages yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1158, 1, 'en', 'backend', 'analytics', 'msg', 'NoKeywords', 'There are no keywords yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1159, 1, 'en', 'backend', 'analytics', 'msg', 'NoLandingPages', 'There are no landing pages yet.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1160, 1, 'en', 'backend', 'analytics', 'msg', 'NoPages', 'There are ni statistics for any pages.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1161, 1, 'en', 'backend', 'analytics', 'msg', 'PagesHaveBeenViewedTimes', 'Pages on this site have been viewed %1$s times.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1162, 1, 'en', 'backend', 'analytics', 'msg', 'RefreshedTrafficSources', 'The traffic sources have been refreshed.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1163, 1, 'en', 'backend', 'analytics', 'msg', 'RemoveAccountLink', 'Remove the link with your Google account', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1164, 1, 'en', 'backend', 'analytics', 'msg', 'RemoveProfileLink', 'Remove the link with your Analytics website profile', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1165, 1, 'nl', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%1$s">Configureer</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1166, 1, 'nl', 'backend', 'blog', 'lbl', 'Add', 'artikel toevoegen', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1167, 1, 'nl', 'backend', 'blog', 'msg', 'Added', 'Het artikel "%1$s" werd toegevoegd.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1168, 1, 'nl', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Reactie op: <a href="%1$s">%2$s</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1169, 1, 'nl', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het artikel "%1$s" wil verwijderen?', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1170, 1, 'nl', 'backend', 'blog', 'msg', 'Deleted', 'De geselecteerde artikels werden verwijderd.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1171, 1, 'nl', 'backend', 'blog', 'msg', 'EditArticle', 'bewerk artikel "%1$s"', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1172, 1, 'en', 'backend', 'blog', 'msg', 'EditComment', 'bewerk reactie', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1173, 1, 'nl', 'backend', 'blog', 'msg', 'Edited', 'Het artikel "%1$s" werd opgeslagen.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1174, 1, 'nl', 'backend', 'blog', 'msg', 'EditedComment', 'De reactie werd opgeslagen.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1175, 1, 'nl', 'backend', 'blog', 'msg', 'HelpMeta', 'Toon de meta informatie van deze blogpost in de RSS feed (categorie, tags, ...)', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1176, 1, 'nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1177, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Maak voor lange artikels een inleiding of samenvatting. Die kan getoond worden op de homepage of het artikeloverzicht.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1178, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1179, 1, 'nl', 'backend', 'blog', 'msg', 'NoItems', 'Er zijn nog geen artikels. <a href="%1$s">Schrijf het eerste artikel</a>.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1180, 1, 'nl', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesFull', 'Aantal items in recente artikels (volledig) widget', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1181, 1, 'nl', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesList', 'Aantal items in recente artikels (lijst) widget', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1182, 1, 'nl', 'frontend', 'core', 'lbl', 'ArticlesInCategory', 'artikels in categorie', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1183, 1, 'nl', 'frontend', 'core', 'lbl', 'InTheCategory', 'in de categorie', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1184, 1, 'nl', 'frontend', 'core', 'lbl', 'SubscribeToTheRSSFeed', 'schrijf je in op de RSS-feed', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1185, 1, 'nl', 'frontend', 'core', 'lbl', 'BlogArchive', 'blogarchief', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1186, 1, 'nl', 'frontend', 'core', 'lbl', 'RecentArticles', 'recente artikels', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1187, 1, 'nl', 'frontend', 'core', 'lbl', 'Wrote', 'schreef', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1188, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1189, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %1$s reacties', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1190, 1, 'nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1191, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1192, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1193, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1194, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoItems', 'Er zijn nog geen blogposts.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1195, 1, 'en', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS description is not yet provided. <a href="%1$s">Configure</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1196, 1, 'en', 'backend', 'blog', 'lbl', 'Add', 'add article', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1197, 1, 'en', 'backend', 'blog', 'msg', 'Added', 'The article "%1$s" was added.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1198, 1, 'en', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Comment on: <a href="%1$s">%2$s</a>', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1199, 1, 'en', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the article "%1$s"?', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1200, 1, 'en', 'backend', 'blog', 'msg', 'Deleted', 'The selected articles were deleted.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1201, 1, 'en', 'backend', 'blog', 'msg', 'EditArticle', 'edit article "%1$s"', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1202, 1, 'en', 'backend', 'blog', 'msg', 'Edited', 'The article "%1$s" was saved.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1203, 1, 'en', 'backend', 'blog', 'msg', 'EditedComment', 'The comment was saved.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1204, 1, 'en', 'backend', 'blog', 'msg', 'HelpMeta', 'Show the meta information for this blogpost in the RSS feed (category, tags, ...)', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1205, 1, 'en', 'backend', 'blog', 'msg', 'HelpPingServices', 'Let various blogservices know when you''ve posted a new article.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1206, 1, 'en', 'backend', 'blog', 'msg', 'HelpSummary', 'Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1207, 1, 'en', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Enable the built-in spamfilter (Akismet) to help avoid spam comments.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1208, 1, 'en', 'backend', 'blog', 'msg', 'NoItems', 'There are no articles yet. <a href="%1$s">Write the first article</a>.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1209, 1, 'en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesFull', 'Number of articles in the recent articles (full) widget', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1210, 1, 'en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesList', 'Number of articles in the recent articles (list) widget', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1211, 1, 'en', 'frontend', 'core', 'lbl', 'ArticlesInCategory', 'articles in category', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1212, 1, 'en', 'frontend', 'core', 'lbl', 'InTheCategory', 'in category', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1213, 1, 'en', 'frontend', 'core', 'lbl', 'SubscribeToTheRSSFeed', 'subscribe to the RSS feed', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1214, 1, 'en', 'frontend', 'core', 'lbl', 'BlogArchive', 'blog archive', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1215, 1, 'en', 'frontend', 'core', 'lbl', 'RecentArticles', 'recent articles', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1216, 1, 'en', 'frontend', 'core', 'lbl', 'Wrote', 'wrote', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1217, 1, 'en', 'frontend', 'core', 'msg', 'BlogNoComments', 'Be the first to comment', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1218, 1, 'en', 'frontend', 'core', 'msg', 'BlogNumberOfComments', '%1$s comments', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1219, 1, 'en', 'frontend', 'core', 'msg', 'BlogOneComment', '1 comment already', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1220, 1, 'en', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Your comment was added.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1221, 1, 'en', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Your comment is awaiting moderation.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1222, 1, 'en', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Your comment was marked as spam.', '2010-09-23 11:22:18');
INSERT INTO `locale` VALUES(1223, 1, 'en', 'frontend', 'core', 'msg', 'BlogNoItems', 'There are no articles yet.', '2010-09-23 11:22:18');

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `keywords_overwrite` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description_overwrite` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title_overwrite` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url_overwrite` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `custom` text CHARACTER SET utf8 COMMENT 'used for custom meta-information',
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Meta-information' AUTO_INCREMENT=73 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` VALUES(1, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(2, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(3, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES(4, 'About', 'N', 'About', 'N', 'About', 'N', 'about', 'N', NULL);
INSERT INTO `meta` VALUES(5, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES(6, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES(7, 'Search', 'N', 'Search', 'N', 'Search', 'N', 'search', 'N', NULL);
INSERT INTO `meta` VALUES(8, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(9, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(10, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES(11, 'Introducing', 'N', 'Introducing', 'N', 'Introducing', 'N', 'introducing', 'N', NULL);
INSERT INTO `meta` VALUES(12, 'Lorem ipsum', 'N', 'Lorem ipsum', 'N', 'Lorem ipsum', 'N', 'lorem-ipsum', 'N', NULL);
INSERT INTO `meta` VALUES(13, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(14, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(15, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(16, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(17, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(28, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(19, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(20, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(21, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(22, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(23, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(24, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(25, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(26, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(27, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(29, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(30, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(31, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(32, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(33, 'Introducing', 'N', 'Introducing', 'N', 'Introducing', 'N', 'introducing', 'N', NULL);
INSERT INTO `meta` VALUES(34, 'Lorem ipsum', 'N', 'Lorem ipsum', 'N', 'Lorem ipsum', 'N', 'lorem-ipsum', 'N', NULL);
INSERT INTO `meta` VALUES(35, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES(36, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(37, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(38, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(39, 'About', 'N', 'About', 'N', 'About', 'N', 'about', 'N', NULL);
INSERT INTO `meta` VALUES(40, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(41, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES(42, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES(43, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES(44, 'Search', 'N', 'Search', 'N', 'Search', 'N', 'search', 'N', NULL);
INSERT INTO `meta` VALUES(45, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES(46, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(47, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(48, 'About us', 'N', 'About us', 'N', 'About us', 'N', 'about-us', 'N', NULL);
INSERT INTO `meta` VALUES(49, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES(50, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(51, 'Test subnav 1', 'N', 'Test subnav 1', 'N', 'Test subnav 1', 'N', 'test-subnav-1', 'N', NULL);
INSERT INTO `meta` VALUES(52, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(53, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(54, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact-2', 'N', NULL);
INSERT INTO `meta` VALUES(55, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(56, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(57, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(58, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(59, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES(60, 'Another page', 'N', 'Another page', 'N', 'Another page', 'N', 'another-page', 'N', NULL);
INSERT INTO `meta` VALUES(61, 'History', 'N', 'History', 'N', 'History', 'N', 'history', 'N', NULL);
INSERT INTO `meta` VALUES(62, 'Another page', 'N', 'Another page', 'N', 'Another page', 'N', 'another-page', 'N', NULL);
INSERT INTO `meta` VALUES(63, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(64, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(65, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(66, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(67, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES(68, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES(69, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES(70, 'Dolor sit amet', 'N', 'Dolor sit amet', 'N', 'Dolor sit amet', 'N', 'dolor-sit-amet', 'N', NULL);
INSERT INTO `meta` VALUES(71, 'Lorem ipsum', 'N', 'Lorem ipsum', 'N', 'Lorem ipsum', 'N', 'lorem-ipsum', 'N', NULL);
INSERT INTO `meta` VALUES(72, 'Dolor sit amet', 'N', 'Dolor sit amet', 'N', 'Dolor sit amet', 'N', 'dolor-sit-amet', 'N', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique module name',
  `description` text COLLATE utf8_unicode_ci,
  `active` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` VALUES('core', 'The Fork CMS core module.', 'Y');
INSERT INTO `modules` VALUES('authentication', 'The module to manage authentication', 'Y');
INSERT INTO `modules` VALUES('dashboard', 'The dashboard containing module specific widgets.', 'Y');
INSERT INTO `modules` VALUES('error', 'The error module, used for displaying errors.', 'Y');
INSERT INTO `modules` VALUES('locale', 'The module to manage your website/cms locale.', 'Y');
INSERT INTO `modules` VALUES('users', 'User management.', 'Y');
INSERT INTO `modules` VALUES('example', 'The example module, used as a reference.', 'Y');
INSERT INTO `modules` VALUES('settings', 'The module to manage your settings.', 'Y');
INSERT INTO `modules` VALUES('pages', 'The module to manage your pages and website structure.', 'Y');
INSERT INTO `modules` VALUES('search', 'The search module.', 'Y');
INSERT INTO `modules` VALUES('contact', 'The contact module.', 'Y');
INSERT INTO `modules` VALUES('content_blocks', 'The content blocks module.', 'Y');
INSERT INTO `modules` VALUES('tags', 'The tags module.', 'Y');
INSERT INTO `modules` VALUES('analytics', 'The analytics module.', 'Y');
INSERT INTO `modules` VALUES('blog', 'The blog module.', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `modules_settings`
--

CREATE TABLE `modules_settings` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`module`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules_settings`
--

INSERT INTO `modules_settings` VALUES('analytics', 'account_name', 's:17:"Sites van Netlash";');
INSERT INTO `modules_settings` VALUES('analytics', 'interval', 's:4:"week";');
INSERT INTO `modules_settings` VALUES('analytics', 'profile_title', 's:13:"www.netlab.be";');
INSERT INTO `modules_settings` VALUES('analytics', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('analytics', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('analytics', 'session_token', 's:45:"1/OFUSw3kbROh3lRDwpkFDxsnGeIUcQjdCNtxwmnfp-rM";');
INSERT INTO `modules_settings` VALUES('analytics', 'table_id', 's:11:"ga:10538702";');
INSERT INTO `modules_settings` VALUES('authentication', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('authentication', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('blog', 'allow_comments', 'b:1;');
INSERT INTO `modules_settings` VALUES('blog', 'default_category_en', 'i:1;');
INSERT INTO `modules_settings` VALUES('blog', 'feedburner_url_en', 's:0:"";');
INSERT INTO `modules_settings` VALUES('blog', 'max_num_revisions', 'i:20;');
INSERT INTO `modules_settings` VALUES('blog', 'moderation', 'b:1;');
INSERT INTO `modules_settings` VALUES('blog', 'overview_num_items', 'i:10;');
INSERT INTO `modules_settings` VALUES('blog', 'ping_services', 'b:1;');
INSERT INTO `modules_settings` VALUES('blog', 'recent_articles_full_num_items', 'i:3;');
INSERT INTO `modules_settings` VALUES('blog', 'recent_articles_list_num_items', 'i:5;');
INSERT INTO `modules_settings` VALUES('blog', 'requires_akismet', 'b:1;');
INSERT INTO `modules_settings` VALUES('blog', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('blog', 'rss_description_en', 's:0:"";');
INSERT INTO `modules_settings` VALUES('blog', 'rss_meta_en', 'b:1;');
INSERT INTO `modules_settings` VALUES('blog', 'rss_title_en', 's:3:"RSS";');
INSERT INTO `modules_settings` VALUES('blog', 'spamfilter', 'b:0;');
INSERT INTO `modules_settings` VALUES('contact', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('contact', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('content_blocks', 'max_num_revisions', 'i:20;');
INSERT INTO `modules_settings` VALUES('content_blocks', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('content_blocks', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('core', 'active_languages', 'a:1:{i:0;s:2:"en";}');
INSERT INTO `modules_settings` VALUES('core', 'akismet_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'date_format_long', 's:7:"l j F Y";');
INSERT INTO `modules_settings` VALUES('core', 'date_format_short', 's:5:"j.n.Y";');
INSERT INTO `modules_settings` VALUES('core', 'date_formats_long', 'a:14:{i:0;s:5:"j F Y";i:1;s:7:"D j F Y";i:2;s:7:"l j F Y";i:3;s:6:"j F, Y";i:4;s:8:"D j F, Y";i:5;s:8:"l j F, Y";i:6;s:5:"d F Y";i:7;s:6:"d F, Y";i:8;s:5:"F j Y";i:9;s:7:"D F j Y";i:10;s:7:"l F j Y";i:11;s:6:"F d, Y";i:12;s:8:"D F d, Y";i:13;s:8:"l F d, Y";}');
INSERT INTO `modules_settings` VALUES('core', 'date_formats_short', 'a:24:{i:0;s:5:"j/n/Y";i:1;s:5:"j-n-Y";i:2;s:5:"j.n.Y";i:3;s:5:"n/j/Y";i:4;s:5:"n/j/Y";i:5;s:5:"n/j/Y";i:6;s:5:"d/m/Y";i:7;s:5:"d-m-Y";i:8;s:5:"d.m.Y";i:9;s:5:"m/d/Y";i:10;s:5:"m-d-Y";i:11;s:5:"m.d.Y";i:12;s:5:"j/n/y";i:13;s:5:"j-n-y";i:14;s:5:"j.n.y";i:15;s:5:"n/j/y";i:16;s:5:"n-j-y";i:17;s:5:"n.j.y";i:18;s:5:"d/m/y";i:19;s:5:"d-m-y";i:20;s:5:"d.m.y";i:21;s:5:"m/d/y";i:22;s:5:"m-d-y";i:23;s:5:"m.d.y";}');
INSERT INTO `modules_settings` VALUES('core', 'default_interface_language', 's:2:"en";');
INSERT INTO `modules_settings` VALUES('core', 'default_language', 's:2:"en";');
INSERT INTO `modules_settings` VALUES('core', 'facebook_admin_ids', 'N;');
INSERT INTO `modules_settings` VALUES('core', 'fork_api_private_key', 's:32:"e72f708881d8a3764c334f0e9e6c89b6";');
INSERT INTO `modules_settings` VALUES('core', 'fork_api_public_key', 's:32:"87207c48925cc854a54b6d84b7b33a36";');
INSERT INTO `modules_settings` VALUES('core', 'google_maps_keky', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'interface_languages', 'a:2:{i:0;s:2:"nl";i:1;s:2:"en";}');
INSERT INTO `modules_settings` VALUES('core', 'languages', 'a:1:{i:0;s:2:"en";}');
INSERT INTO `modules_settings` VALUES('core', 'mailer_from', 'a:2:{s:4:"name";s:8:"Fork CMS";s:5:"email";s:17:"johan@netlash.com";}');
INSERT INTO `modules_settings` VALUES('core', 'mailer_reply_to', 'a:2:{s:4:"name";s:8:"Fork CMS";s:5:"email";s:17:"johan@netlash.com";}');
INSERT INTO `modules_settings` VALUES('core', 'mailer_to', 'a:2:{s:4:"name";s:8:"Fork CMS";s:5:"email";s:17:"johan@netlash.com";}');
INSERT INTO `modules_settings` VALUES('core', 'mailer_type', 's:4:"mail";');
INSERT INTO `modules_settings` VALUES('core', 'max_num_revisions', 'i:20;');
INSERT INTO `modules_settings` VALUES('core', 'ping_services', 'a:2:{s:8:"services";a:3:{i:0;a:3:{s:3:"url";s:27:"http://rpc.weblogs.com/RPC2";s:4:"port";i:80;s:4:"type";s:8:"extended";}i:1;a:3:{s:3:"url";s:30:"http://rpc.pingomatic.com/RPC2";s:4:"port";i:80;s:4:"type";s:8:"extended";}i:2;a:3:{s:3:"url";s:39:"http://blogsearch.google.com/ping/RPC2 ";s:4:"port";i:80;s:4:"type";s:8:"extended";}}s:4:"date";i:1285240934;}');
INSERT INTO `modules_settings` VALUES('core', 'redirect_languages', 'a:1:{i:0;s:2:"en";}');
INSERT INTO `modules_settings` VALUES('core', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('core', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('core', 'site_domains', 'a:1:{i:0;s:11:"fork2.local";}');
INSERT INTO `modules_settings` VALUES('core', 'site_html_footer', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'site_html_header', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'site_title_en', 's:10:"My website";');
INSERT INTO `modules_settings` VALUES('core', 'smtp_password', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'smtp_port', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'smtp_server', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'smtp_username', 's:0:"";');
INSERT INTO `modules_settings` VALUES('core', 'theme', 's:7:"scratch";');
INSERT INTO `modules_settings` VALUES('core', 'time_format', 's:3:"H:i";');
INSERT INTO `modules_settings` VALUES('core', 'time_formats', 'a:4:{i:0;s:3:"H:i";i:1;s:5:"H:i:s";i:2;s:5:"g:i a";i:3;s:5:"g:i A";}');
INSERT INTO `modules_settings` VALUES('dashboard', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('dashboard', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('error', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('error', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('example', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('example', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('locale', 'languages', 'a:5:{i:0;s:2:"de";i:1;s:2:"en";i:2;s:2:"es";i:3;s:2:"fr";i:4;s:2:"nl";}');
INSERT INTO `modules_settings` VALUES('locale', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('locale', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('pages', 'default_template', 'i:6;');
INSERT INTO `modules_settings` VALUES('pages', 'max_num_revisions', 'i:20;');
INSERT INTO `modules_settings` VALUES('pages', 'meta_navigation', 'b:0;');
INSERT INTO `modules_settings` VALUES('pages', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('pages', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('pages', 'template_max_blocks', 'i:6;');
INSERT INTO `modules_settings` VALUES('search', 'overview_num_items', 'i:10;');
INSERT INTO `modules_settings` VALUES('search', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('search', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('search', 'validate_search', 'b:1;');
INSERT INTO `modules_settings` VALUES('settings', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('settings', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('tags', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('tags', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('users', 'date_formats', 'a:4:{i:0;s:5:"j/n/Y";i:1;s:5:"d/m/Y";i:2;s:5:"j F Y";i:3;s:6:"F j, Y";}');
INSERT INTO `modules_settings` VALUES('users', 'default_group', 'i:1;');
INSERT INTO `modules_settings` VALUES('users', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES('users', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES('users', 'time_formats', 'a:4:{i:0;s:3:"H:i";i:1;s:5:"H:i:s";i:2;s:5:"g:i a";i:3;s:5:"g:i A";}');

-- --------------------------------------------------------

--
-- Table structure for table `modules_tags`
--

CREATE TABLE `modules_tags` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL COMMENT 'the real page_id',
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'which user has created this page?',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the parent_id for the page ',
  `template_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the template to use',
  `meta_id` int(11) NOT NULL COMMENT 'linked meta information',
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'language of the content',
  `type` enum('home','root','page','meta','footer','external_alias','internal_alias') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root' COMMENT 'page, header, footer, ...',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `navigation_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'title that will be used in the navigation',
  `navigation_title_overwrite` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'should we override the navigation title',
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'is the page hidden?',
  `status` enum('active','archive','draft') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'is this the active, archive or draft version',
  `publish_on` datetime NOT NULL,
  `data` text COLLATE utf8_unicode_ci COMMENT 'serialized array that may contain type specific parameters',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `allow_move` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `allow_children` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `allow_edit` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `allow_delete` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `no_follow` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `sequence` int(11) NOT NULL,
  `has_extra` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL,
  `extra_ids` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=66 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` VALUES(5, 37, 1, 1, 2, 41, 'en', 'page', 'Contact', 'Contact', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:30:08', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'N', NULL);
INSERT INTO `pages` VALUES(1, 53, 1, 0, 8, 57, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:00:41', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', '6');
INSERT INTO `pages` VALUES(1, 52, 1, 0, 8, 56, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:55:53', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', '6');
INSERT INTO `pages` VALUES(1, 51, 1, 0, 8, 55, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:54:45', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', '6');
INSERT INTO `pages` VALUES(410, 50, 1, 0, 6, 54, 'en', 'root', 'Contact', 'Contact', 'N', 'N', 'active', '2010-09-23 15:51:48', NULL, '2010-09-23 15:51:48', '2010-09-23 15:51:48', 'Y', 'Y', 'Y', 'Y', 'N', 5, 'Y', '10');
INSERT INTO `pages` VALUES(3, 41, 1, 0, 2, 45, 'en', 'footer', 'Disclaimer', 'Disclaimer', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:31:37', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(409, 49, 1, 407, 6, 53, 'en', 'page', 'History', 'History', 'N', 'N', 'archive', '2010-09-23 15:44:41', NULL, '2010-09-23 15:44:41', '2010-09-23 15:50:58', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'N', NULL);
INSERT INTO `pages` VALUES(408, 10, 1, 0, 2, 10, 'en', 'root', 'Tags', 'Tags', 'N', 'N', 'archive', '2010-09-23 11:22:17', NULL, '2010-09-23 11:22:17', '2010-09-23 11:22:17', 'Y', 'Y', 'Y', 'Y', 'N', 1, 'Y', '11');
INSERT INTO `pages` VALUES(2, 36, 1, 0, 2, 40, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:29:10', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(4, 35, 1, 0, 2, 39, 'en', 'meta', 'About', 'About', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:28:58', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(407, 13, 1, 1, 2, 15, 'en', 'page', 'History', 'History', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 12:40:21', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(406, 14, 1, 1, 2, 16, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 12:40:29', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(1, 15, 1, 0, 2, 17, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 12:40:38', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(409, 48, 1, 407, 6, 52, 'en', 'page', 'History', 'History', 'N', 'N', 'archive', '2010-09-23 15:44:41', NULL, '2010-09-23 15:44:41', '2010-09-23 15:50:39', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'N', NULL);
INSERT INTO `pages` VALUES(1, 17, 1, 0, 2, 19, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:41:41', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(409, 47, 1, 407, 6, 51, 'en', 'page', 'Test subnav 1', 'Test subnav 1', 'N', 'N', 'archive', '2010-09-23 15:44:41', NULL, '2010-09-23 15:44:41', '2010-09-23 15:44:41', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'N', NULL);
INSERT INTO `pages` VALUES(406, 19, 1, 1, 2, 21, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:42:35', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(406, 20, 1, 1, 2, 22, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:44:06', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(407, 21, 1, 1, 2, 23, 'en', 'page', 'History', 'History', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:44:30', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(407, 22, 1, 1, 2, 24, 'en', 'page', 'History', 'History', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:45:05', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(407, 23, 1, 1, 2, 25, 'en', 'page', 'History', 'History', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:46:33', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(406, 24, 1, 1, 2, 26, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 13:47:32', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'Y', '1,2,3');
INSERT INTO `pages` VALUES(1, 46, 1, 0, 6, 50, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:42:56', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(406, 45, 1, 1, 7, 49, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:41:30', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'Y', '1,2,3,4,6');
INSERT INTO `pages` VALUES(406, 43, 1, 1, 7, 47, 'en', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:39:56', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(407, 44, 1, 1, 6, 48, 'en', 'page', 'About us', 'About us', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:40:19', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(1, 42, 1, 0, 6, 46, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:39:43', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(1, 33, 1, 0, 2, 37, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:24:07', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(1, 34, 1, 0, 2, 38, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:24:42', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(404, 38, 1, 0, 2, 42, 'en', 'root', '404', '404', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:30:17', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(408, 39, 1, 0, 2, 43, 'en', 'root', 'Tags', 'Tags', 'N', 'N', 'active', '2010-09-23 11:22:17', NULL, '2010-09-23 11:22:17', '2010-09-23 15:30:27', 'Y', 'Y', 'Y', 'Y', 'N', 1, 'Y', '11');
INSERT INTO `pages` VALUES(405, 40, 1, 0, 2, 44, 'en', 'root', 'Search', 'Search', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 15:30:36', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(1, 54, 1, 0, 8, 58, 'en', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:02:22', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', '6');
INSERT INTO `pages` VALUES(1, 55, 1, 0, 8, 59, 'en', 'page', 'Home', 'Home', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:06:03', 'N', 'Y', 'Y', 'N', 'N', 0, 'N', '6');
INSERT INTO `pages` VALUES(411, 56, 1, 407, 6, 60, 'en', 'page', 'Another page', 'Another page', 'N', 'N', 'archive', '2010-09-23 16:06:47', NULL, '2010-09-23 16:06:47', '2010-09-23 16:06:47', 'Y', 'Y', 'Y', 'Y', 'N', 5, 'N', NULL);
INSERT INTO `pages` VALUES(409, 57, 1, 407, 6, 61, 'en', 'page', 'History', 'History', 'N', 'N', 'active', '2010-09-23 15:44:41', NULL, '2010-09-23 15:44:41', '2010-09-23 16:07:37', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'N', NULL);
INSERT INTO `pages` VALUES(411, 58, 1, 407, 6, 62, 'en', 'page', 'Another page', 'Another page', 'N', 'N', 'active', '2010-09-23 16:06:47', NULL, '2010-09-23 16:06:47', '2010-09-23 16:07:47', 'Y', 'Y', 'Y', 'Y', 'N', 5, 'N', NULL);
INSERT INTO `pages` VALUES(2, 59, 1, 0, 6, 63, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:11:30', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(2, 60, 1, 0, 6, 64, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:11:49', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', '9');
INSERT INTO `pages` VALUES(2, 61, 1, 0, 6, 65, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:12:14', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', NULL);
INSERT INTO `pages` VALUES(2, 62, 1, 0, 6, 66, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:12:39', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', '9');
INSERT INTO `pages` VALUES(3, 63, 1, 0, 6, 67, 'en', 'footer', 'Disclaimer', 'Disclaimer', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:13:07', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES(5, 64, 1, 1, 6, 68, 'en', 'page', 'Contact', 'Contact', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:20:38', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'Y', '10');
INSERT INTO `pages` VALUES(2, 65, 1, 0, 6, 69, 'en', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'active', '2010-09-23 11:22:16', NULL, '2010-09-23 11:22:16', '2010-09-23 16:22:30', 'Y', 'Y', 'Y', 'Y', 'N', 0, 'N', '9');

-- --------------------------------------------------------

--
-- Table structure for table `pages_blocks`
--

CREATE TABLE `pages_blocks` (
  `id` int(11) NOT NULL COMMENT 'An ID that will be the same over the revisions.\n',
  `revision_id` int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
  `extra_id` int(11) DEFAULT NULL COMMENT 'The linked extra.',
  `html` text COLLATE utf8_unicode_ci COMMENT 'if this block is HTML this field should contain the real HTML.',
  `status` enum('active','archive','draft') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  KEY `idx_rev_status` (`revision_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pages_blocks`
--

INSERT INTO `pages_blocks` VALUES(13, 57, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 16:07:37', '2010-09-23 16:07:37');
INSERT INTO `pages_blocks` VALUES(19, 58, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus.</p>\r\n<p>Vestibulum in tortor sodales elit sollicitudin  gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat.  Sed ut nisl congue justo pharetra accumsan. Lorem ipsum dolor sit amet,  consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum  tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet  orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed  tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac,  felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus  justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis.  Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit  sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam  erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 16:07:47', '2010-09-23 16:07:47');
INSERT INTO `pages_blocks` VALUES(20, 58, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 16:07:47', '2010-09-23 16:07:47');
INSERT INTO `pages_blocks` VALUES(0, 59, NULL, '', 'active', '2010-09-23 16:11:30', '2010-09-23 16:11:30');
INSERT INTO `pages_blocks` VALUES(1, 59, NULL, '', 'active', '2010-09-23 16:11:30', '2010-09-23 16:11:30');
INSERT INTO `pages_blocks` VALUES(0, 60, NULL, '', 'active', '2010-09-23 16:11:49', '2010-09-23 16:11:49');
INSERT INTO `pages_blocks` VALUES(1, 60, 9, NULL, 'active', '2010-09-23 16:11:49', '2010-09-23 16:11:49');
INSERT INTO `pages_blocks` VALUES(0, 61, NULL, '', 'active', '2010-09-23 16:12:14', '2010-09-23 16:12:14');
INSERT INTO `pages_blocks` VALUES(1, 61, NULL, '', 'active', '2010-09-23 16:12:14', '2010-09-23 16:12:14');
INSERT INTO `pages_blocks` VALUES(0, 62, 9, NULL, 'active', '2010-09-23 16:12:39', '2010-09-23 16:12:39');
INSERT INTO `pages_blocks` VALUES(1, 62, NULL, '', 'active', '2010-09-23 16:12:39', '2010-09-23 16:12:39');
INSERT INTO `pages_blocks` VALUES(0, 63, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 16:13:07', '2010-09-23 16:13:07');
INSERT INTO `pages_blocks` VALUES(1, 63, NULL, '', 'active', '2010-09-23 16:13:07', '2010-09-23 16:13:07');
INSERT INTO `pages_blocks` VALUES(0, 64, NULL, '<p>Want to leave a message?</p>', 'active', '2010-09-23 16:20:38', '2010-09-23 16:20:38');
INSERT INTO `pages_blocks` VALUES(1, 64, 10, NULL, 'active', '2010-09-23 16:20:38', '2010-09-23 16:20:38');
INSERT INTO `pages_blocks` VALUES(0, 65, NULL, '<p>Take a look at all the pages in our website:</p>', 'active', '2010-09-23 16:22:30', '2010-09-23 16:22:30');
INSERT INTO `pages_blocks` VALUES(1, 65, 9, NULL, 'active', '2010-09-23 16:22:30', '2010-09-23 16:22:30');
INSERT INTO `pages_blocks` VALUES(2, 41, NULL, '', 'active', '2010-09-23 15:31:37', '2010-09-23 15:31:37');
INSERT INTO `pages_blocks` VALUES(1, 41, NULL, '', 'active', '2010-09-23 15:31:37', '2010-09-23 15:31:37');
INSERT INTO `pages_blocks` VALUES(0, 41, NULL, '', 'active', '2010-09-23 15:31:37', '2010-09-23 15:31:37');
INSERT INTO `pages_blocks` VALUES(18, 55, 6, NULL, 'active', '2010-09-23 16:06:03', '2010-09-23 16:06:03');
INSERT INTO `pages_blocks` VALUES(19, 56, NULL, '', 'active', '2010-09-23 16:06:47', '2010-09-23 16:06:47');
INSERT INTO `pages_blocks` VALUES(20, 56, NULL, '', 'active', '2010-09-23 16:06:47', '2010-09-23 16:06:47');
INSERT INTO `pages_blocks` VALUES(12, 57, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus.</p>\r\n<p>Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 16:07:37', '2010-09-23 16:07:37');
INSERT INTO `pages_blocks` VALUES(0, 10, 11, '', 'active', '2010-09-23 11:22:17', '2010-09-23 11:22:17');
INSERT INTO `pages_blocks` VALUES(1, 10, NULL, '', 'active', '2010-09-23 11:22:17', '2010-09-23 11:22:17');
INSERT INTO `pages_blocks` VALUES(2, 10, NULL, '', 'active', '2010-09-23 11:22:17', '2010-09-23 11:22:17');
INSERT INTO `pages_blocks` VALUES(3, 10, NULL, '', 'active', '2010-09-23 11:22:17', '2010-09-23 11:22:17');
INSERT INTO `pages_blocks` VALUES(4, 10, NULL, '', 'active', '2010-09-23 11:22:17', '2010-09-23 11:22:17');
INSERT INTO `pages_blocks` VALUES(5, 10, NULL, '', 'active', '2010-09-23 11:22:17', '2010-09-23 11:22:17');
INSERT INTO `pages_blocks` VALUES(1, 39, NULL, '', 'active', '2010-09-23 15:30:27', '2010-09-23 15:30:27');
INSERT INTO `pages_blocks` VALUES(2, 39, NULL, '', 'active', '2010-09-23 15:30:27', '2010-09-23 15:30:27');
INSERT INTO `pages_blocks` VALUES(0, 40, NULL, '', 'active', '2010-09-23 15:30:36', '2010-09-23 15:30:36');
INSERT INTO `pages_blocks` VALUES(1, 40, NULL, '', 'active', '2010-09-23 15:30:36', '2010-09-23 15:30:36');
INSERT INTO `pages_blocks` VALUES(2, 40, NULL, '', 'active', '2010-09-23 15:30:36', '2010-09-23 15:30:36');
INSERT INTO `pages_blocks` VALUES(0, 39, 11, NULL, 'active', '2010-09-23 15:30:27', '2010-09-23 15:30:27');
INSERT INTO `pages_blocks` VALUES(2, 38, NULL, '', 'active', '2010-09-23 15:30:17', '2010-09-23 15:30:17');
INSERT INTO `pages_blocks` VALUES(2, 37, NULL, '', 'active', '2010-09-23 15:30:08', '2010-09-23 15:30:08');
INSERT INTO `pages_blocks` VALUES(0, 38, NULL, '', 'active', '2010-09-23 15:30:17', '2010-09-23 15:30:17');
INSERT INTO `pages_blocks` VALUES(1, 38, NULL, '', 'active', '2010-09-23 15:30:17', '2010-09-23 15:30:17');
INSERT INTO `pages_blocks` VALUES(1, 37, NULL, '', 'active', '2010-09-23 15:30:08', '2010-09-23 15:30:08');
INSERT INTO `pages_blocks` VALUES(0, 37, NULL, '', 'active', '2010-09-23 15:30:08', '2010-09-23 15:30:08');
INSERT INTO `pages_blocks` VALUES(2, 36, NULL, '', 'active', '2010-09-23 15:29:10', '2010-09-23 15:29:10');
INSERT INTO `pages_blocks` VALUES(1, 36, NULL, '', 'active', '2010-09-23 15:29:10', '2010-09-23 15:29:10');
INSERT INTO `pages_blocks` VALUES(0, 36, NULL, '', 'active', '2010-09-23 15:29:10', '2010-09-23 15:29:10');
INSERT INTO `pages_blocks` VALUES(1, 35, NULL, '', 'active', '2010-09-23 15:28:58', '2010-09-23 15:28:58');
INSERT INTO `pages_blocks` VALUES(2, 35, NULL, '', 'active', '2010-09-23 15:28:58', '2010-09-23 15:28:58');
INSERT INTO `pages_blocks` VALUES(0, 35, NULL, '', 'active', '2010-09-23 15:28:58', '2010-09-23 15:28:58');
INSERT INTO `pages_blocks` VALUES(0, 13, NULL, '', 'active', '2010-09-23 12:40:21', '2010-09-23 12:40:21');
INSERT INTO `pages_blocks` VALUES(1, 13, NULL, '', 'active', '2010-09-23 12:40:21', '2010-09-23 12:40:21');
INSERT INTO `pages_blocks` VALUES(2, 13, NULL, '', 'active', '2010-09-23 12:40:21', '2010-09-23 12:40:21');
INSERT INTO `pages_blocks` VALUES(0, 14, NULL, '', 'active', '2010-09-23 12:40:29', '2010-09-23 12:40:29');
INSERT INTO `pages_blocks` VALUES(1, 14, NULL, '', 'active', '2010-09-23 12:40:29', '2010-09-23 12:40:29');
INSERT INTO `pages_blocks` VALUES(2, 14, NULL, '', 'active', '2010-09-23 12:40:29', '2010-09-23 12:40:29');
INSERT INTO `pages_blocks` VALUES(0, 15, NULL, '', 'active', '2010-09-23 12:40:38', '2010-09-23 12:40:38');
INSERT INTO `pages_blocks` VALUES(1, 15, NULL, '', 'active', '2010-09-23 12:40:38', '2010-09-23 12:40:38');
INSERT INTO `pages_blocks` VALUES(2, 15, NULL, '', 'active', '2010-09-23 12:40:38', '2010-09-23 12:40:38');
INSERT INTO `pages_blocks` VALUES(1, 55, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 16:06:03', '2010-09-23 16:06:03');
INSERT INTO `pages_blocks` VALUES(0, 17, NULL, '<p>Hello</p>', 'active', '2010-09-23 13:41:41', '2010-09-23 13:41:41');
INSERT INTO `pages_blocks` VALUES(1, 17, NULL, '', 'active', '2010-09-23 13:41:41', '2010-09-23 13:41:41');
INSERT INTO `pages_blocks` VALUES(2, 17, NULL, '', 'active', '2010-09-23 13:41:41', '2010-09-23 13:41:41');
INSERT INTO `pages_blocks` VALUES(0, 55, NULL, '<h2>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod!</h2>', 'active', '2010-09-23 16:06:03', '2010-09-23 16:06:03');
INSERT INTO `pages_blocks` VALUES(0, 19, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:42:35', '2010-09-23 13:42:35');
INSERT INTO `pages_blocks` VALUES(1, 19, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:42:35', '2010-09-23 13:42:35');
INSERT INTO `pages_blocks` VALUES(2, 19, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:42:35', '2010-09-23 13:42:35');
INSERT INTO `pages_blocks` VALUES(0, 20, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis.</p>\r\n<p>Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:44:06', '2010-09-23 13:44:06');
INSERT INTO `pages_blocks` VALUES(1, 20, NULL, '<h3>Hello, we are ACME</h3>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, <a href="#">mollis  quis, ultricies</a>.</p>', 'active', '2010-09-23 13:44:06', '2010-09-23 13:44:06');
INSERT INTO `pages_blocks` VALUES(2, 20, NULL, '<h3>Order catalog</h3>\r\n<p>Lorem ipsum dolor sit amet, consectetur  adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor.  Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit  amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor.  Sed velit velit, <a href="#">mollis  quis, ultricies</a>.</p>', 'active', '2010-09-23 13:44:06', '2010-09-23 13:44:06');
INSERT INTO `pages_blocks` VALUES(0, 21, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis.</p>\r\n<p>Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo,  sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris  mollis elit sit amet lectus. Vestibulum in tortor sodales elit  sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam  erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id   magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec   interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis   quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit   consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,   vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit   amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.   Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut   nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:44:30', '2010-09-23 13:44:30');
INSERT INTO `pages_blocks` VALUES(1, 21, NULL, '', 'active', '2010-09-23 13:44:30', '2010-09-23 13:44:30');
INSERT INTO `pages_blocks` VALUES(2, 21, NULL, '', 'active', '2010-09-23 13:44:30', '2010-09-23 13:44:30');
INSERT INTO `pages_blocks` VALUES(0, 22, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis.</p>\r\n<p>Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo,  sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris  mollis elit sit amet lectus. Vestibulum in tortor sodales elit  sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam  erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id   magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec   interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis   quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit   consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,   vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit   amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.   Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut   nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:45:05', '2010-09-23 13:45:05');
INSERT INTO `pages_blocks` VALUES(1, 22, NULL, '<h3>Hello, we are ACME</h3>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id   magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec   interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, <a href="#">mollis  quis, ultricies</a>.</p>', 'active', '2010-09-23 13:45:05', '2010-09-23 13:45:05');
INSERT INTO `pages_blocks` VALUES(2, 22, NULL, '<h3>Order catalog</h3>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id   magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec   interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, <a href="#">mollis  quis, ultricies</a>.</p>', 'active', '2010-09-23 13:45:05', '2010-09-23 13:45:05');
INSERT INTO `pages_blocks` VALUES(0, 23, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis.</p>\r\n<p>Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo,  sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris  mollis elit sit amet lectus. Vestibulum in tortor sodales elit  sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam  erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id   magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec   interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis   quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit   consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,   vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit   amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.   Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut   nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 13:46:33', '2010-09-23 13:46:33');
INSERT INTO `pages_blocks` VALUES(1, 23, NULL, '<h3>Hello, we are ACME</h3>\r\n<p>In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, <a href="#">mollis  quis, ultricies</a>.</p>', 'active', '2010-09-23 13:46:33', '2010-09-23 13:46:33');
INSERT INTO `pages_blocks` VALUES(2, 23, NULL, '<h3>Order catalog</h3>\r\n<p>In laoreet orci sit amet sem. In sed metus ac   nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, <a href="#">mollis  quis, ultricies</a>.</p>', 'active', '2010-09-23 13:46:33', '2010-09-23 13:46:33');
INSERT INTO `pages_blocks` VALUES(0, 24, 1, NULL, 'active', '2010-09-23 13:47:32', '2010-09-23 13:47:32');
INSERT INTO `pages_blocks` VALUES(1, 24, 2, NULL, 'active', '2010-09-23 13:47:32', '2010-09-23 13:47:32');
INSERT INTO `pages_blocks` VALUES(2, 24, 3, NULL, 'active', '2010-09-23 13:47:32', '2010-09-23 13:47:32');
INSERT INTO `pages_blocks` VALUES(18, 54, 6, NULL, 'active', '2010-09-23 16:02:22', '2010-09-23 16:02:22');
INSERT INTO `pages_blocks` VALUES(18, 53, 6, NULL, 'active', '2010-09-23 16:00:41', '2010-09-23 16:00:41');
INSERT INTO `pages_blocks` VALUES(0, 54, NULL, '<h2>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod!</h2>', 'active', '2010-09-23 16:02:22', '2010-09-23 16:02:22');
INSERT INTO `pages_blocks` VALUES(1, 54, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor.</p>', 'active', '2010-09-23 16:02:22', '2010-09-23 16:02:22');
INSERT INTO `pages_blocks` VALUES(1, 53, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor.</p>', 'active', '2010-09-23 16:00:41', '2010-09-23 16:00:41');
INSERT INTO `pages_blocks` VALUES(18, 52, 6, NULL, 'active', '2010-09-23 15:55:53', '2010-09-23 15:55:53');
INSERT INTO `pages_blocks` VALUES(0, 53, NULL, '<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc.</h3>', 'active', '2010-09-23 16:00:41', '2010-09-23 16:00:41');
INSERT INTO `pages_blocks` VALUES(1, 52, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor.</p>', 'active', '2010-09-23 15:55:53', '2010-09-23 15:55:53');
INSERT INTO `pages_blocks` VALUES(1, 51, NULL, '', 'active', '2010-09-23 15:54:45', '2010-09-23 15:54:45');
INSERT INTO `pages_blocks` VALUES(18, 51, 6, NULL, 'active', '2010-09-23 15:54:45', '2010-09-23 15:54:45');
INSERT INTO `pages_blocks` VALUES(0, 52, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:55:53', '2010-09-23 15:55:53');
INSERT INTO `pages_blocks` VALUES(14, 50, NULL, '<p>A question? Fill out the contact form!</p>', 'active', '2010-09-23 15:51:48', '2010-09-23 15:51:48');
INSERT INTO `pages_blocks` VALUES(15, 50, 10, NULL, 'active', '2010-09-23 15:51:48', '2010-09-23 15:51:48');
INSERT INTO `pages_blocks` VALUES(0, 51, NULL, '', 'active', '2010-09-23 15:54:45', '2010-09-23 15:54:45');
INSERT INTO `pages_blocks` VALUES(13, 49, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:50:58', '2010-09-23 15:50:58');
INSERT INTO `pages_blocks` VALUES(13, 48, NULL, '<p>&lt;div id="subnavigation"&gt;<br /> {$var|getsubnavigation:''page'':{$page[''id'']}:2}<br /> &amp;nbsp;<br /> &lt;/div&gt;<br /><br /></p>', 'active', '2010-09-23 15:50:39', '2010-09-23 15:50:39');
INSERT INTO `pages_blocks` VALUES(12, 49, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:50:58', '2010-09-23 15:50:58');
INSERT INTO `pages_blocks` VALUES(12, 47, NULL, '<p>X</p>', 'active', '2010-09-23 15:44:41', '2010-09-23 15:44:41');
INSERT INTO `pages_blocks` VALUES(13, 47, NULL, '<p>X</p>', 'active', '2010-09-23 15:44:41', '2010-09-23 15:44:41');
INSERT INTO `pages_blocks` VALUES(12, 48, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:50:39', '2010-09-23 15:50:39');
INSERT INTO `pages_blocks` VALUES(1, 46, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:42:56', '2010-09-23 15:42:56');
INSERT INTO `pages_blocks` VALUES(1, 45, 1, NULL, 'active', '2010-09-23 15:41:30', '2010-09-23 15:41:30');
INSERT INTO `pages_blocks` VALUES(2, 45, 2, NULL, 'active', '2010-09-23 15:41:30', '2010-09-23 15:41:30');
INSERT INTO `pages_blocks` VALUES(9, 45, 3, NULL, 'active', '2010-09-23 15:41:30', '2010-09-23 15:41:30');
INSERT INTO `pages_blocks` VALUES(10, 45, 4, NULL, 'active', '2010-09-23 15:41:30', '2010-09-23 15:41:30');
INSERT INTO `pages_blocks` VALUES(11, 45, 6, NULL, 'active', '2010-09-23 15:41:30', '2010-09-23 15:41:30');
INSERT INTO `pages_blocks` VALUES(0, 46, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:42:56', '2010-09-23 15:42:56');
INSERT INTO `pages_blocks` VALUES(9, 43, NULL, '', 'active', '2010-09-23 15:39:56', '2010-09-23 15:39:56');
INSERT INTO `pages_blocks` VALUES(10, 43, NULL, '', 'active', '2010-09-23 15:39:56', '2010-09-23 15:39:56');
INSERT INTO `pages_blocks` VALUES(11, 43, NULL, '', 'active', '2010-09-23 15:39:56', '2010-09-23 15:39:56');
INSERT INTO `pages_blocks` VALUES(0, 44, NULL, '', 'active', '2010-09-23 15:40:19', '2010-09-23 15:40:19');
INSERT INTO `pages_blocks` VALUES(1, 44, NULL, '', 'active', '2010-09-23 15:40:19', '2010-09-23 15:40:19');
INSERT INTO `pages_blocks` VALUES(0, 45, NULL, '', 'active', '2010-09-23 15:41:30', '2010-09-23 15:41:30');
INSERT INTO `pages_blocks` VALUES(0, 42, NULL, '', 'active', '2010-09-23 15:39:43', '2010-09-23 15:39:43');
INSERT INTO `pages_blocks` VALUES(1, 42, NULL, '', 'active', '2010-09-23 15:39:43', '2010-09-23 15:39:43');
INSERT INTO `pages_blocks` VALUES(0, 43, NULL, '', 'active', '2010-09-23 15:39:56', '2010-09-23 15:39:56');
INSERT INTO `pages_blocks` VALUES(1, 43, NULL, '', 'active', '2010-09-23 15:39:56', '2010-09-23 15:39:56');
INSERT INTO `pages_blocks` VALUES(2, 43, NULL, '', 'active', '2010-09-23 15:39:56', '2010-09-23 15:39:56');
INSERT INTO `pages_blocks` VALUES(0, 33, NULL, '', 'active', '2010-09-23 15:24:07', '2010-09-23 15:24:07');
INSERT INTO `pages_blocks` VALUES(1, 33, NULL, '', 'active', '2010-09-23 15:24:07', '2010-09-23 15:24:07');
INSERT INTO `pages_blocks` VALUES(2, 33, NULL, '', 'active', '2010-09-23 15:24:07', '2010-09-23 15:24:07');
INSERT INTO `pages_blocks` VALUES(0, 34, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'active', '2010-09-23 15:24:42', '2010-09-23 15:24:42');
INSERT INTO `pages_blocks` VALUES(1, 34, NULL, '', 'active', '2010-09-23 15:24:42', '2010-09-23 15:24:42');
INSERT INTO `pages_blocks` VALUES(2, 34, NULL, '', 'active', '2010-09-23 15:24:42', '2010-09-23 15:24:42');

-- --------------------------------------------------------

--
-- Table structure for table `pages_extras`
--

CREATE TABLE `pages_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the extra.',
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` enum('homepage','block','widget') COLLATE utf8_unicode_ci NOT NULL COMMENT 'The type of the block.',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci COMMENT 'A serialized value with the optional parameters',
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Should the extra be shown in the backend?',
  `sequence` int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `pages_extras`
--

INSERT INTO `pages_extras` VALUES(1, 'blog', 'block', 'Blog', NULL, NULL, 'N', 1000);
INSERT INTO `pages_extras` VALUES(2, 'blog', 'widget', 'RecentComments', 'recent_comments', NULL, 'N', 1001);
INSERT INTO `pages_extras` VALUES(3, 'blog', 'widget', 'Categories', 'categories', NULL, 'N', 1002);
INSERT INTO `pages_extras` VALUES(4, 'blog', 'widget', 'Archive', 'archive', NULL, 'N', 1003);
INSERT INTO `pages_extras` VALUES(5, 'blog', 'widget', 'RecentArticlesFull', 'recent_articles_full', NULL, 'N', 1004);
INSERT INTO `pages_extras` VALUES(6, 'blog', 'widget', 'RecentArticlesList', 'recent_articles_list', NULL, 'N', 1005);
INSERT INTO `pages_extras` VALUES(7, 'search', 'block', 'Search', NULL, NULL, 'N', 2000);
INSERT INTO `pages_extras` VALUES(8, 'search', 'widget', 'SearchForm', 'form', NULL, 'N', 2001);
INSERT INTO `pages_extras` VALUES(9, 'pages', 'widget', 'Sitemap', 'sitemap', NULL, 'N', 1);
INSERT INTO `pages_extras` VALUES(10, 'contact', 'block', 'Contact', NULL, NULL, 'N', 6);
INSERT INTO `pages_extras` VALUES(11, 'tags', 'block', 'Tags', NULL, NULL, 'N', 3);

-- --------------------------------------------------------

--
-- Table structure for table `pages_templates`
--

CREATE TABLE `pages_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the template.',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  `num_blocks` int(11) NOT NULL DEFAULT '1' COMMENT 'The number of blocks used in the template.',
  `active` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Is this template active (as in: will it be used).',
  `data` text COLLATE utf8_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `pages_templates`
--

INSERT INTO `pages_templates` VALUES(6, 'Scratch - Default', 'core/layout/templates/default.tpl', 2, 'Y', 'a:3:{s:6:"format";s:7:"[1],[2]";s:5:"names";a:2:{i:0;s:12:"Main content";i:1;s:27:"Module or secondary content";}s:14:"default_extras";a:2:{i:0;s:6:"editor";i:1;s:6:"editor";}}');
INSERT INTO `pages_templates` VALUES(2, 'Default', 'core/layout/templates/default.tpl', 3, 'Y', 'a:3:{s:6:"format";s:3:"[1]";s:5:"names";a:3:{i:0;s:12:"Main Content";i:1;s:16:"Sidebar: block 1";i:2;s:16:"Sidebar: block 2";}s:14:"default_extras";a:3:{i:0;s:6:"editor";i:1;s:6:"editor";i:2;s:6:"editor";}}');
INSERT INTO `pages_templates` VALUES(7, 'Scratch - Two columns', 'core/layout/templates/twocolumns.tpl', 6, 'Y', 'a:3:{s:6:"format";s:31:"[1,1,3],[2,2,4],[2,2,5],[2,2,6]";s:5:"names";a:6:{i:0;s:12:"Main content";i:1;s:27:"Module or secondary content";i:2;s:14:"Sidebar item 1";i:3;s:14:"Sidebar item 2";i:4;s:14:"Sidebar item 3";i:5;s:14:"Sidebar item 4";}s:14:"default_extras";a:6:{i:0;s:6:"editor";i:1;s:6:"editor";i:2;s:6:"editor";i:3;s:6:"editor";i:4;s:6:"editor";i:5;s:6:"editor";}}');
INSERT INTO `pages_templates` VALUES(8, 'Scratch - Home', 'core/layout/templates/home.tpl', 3, 'Y', 'a:3:{s:6:"format";s:11:"[1,1],[2,3]";s:5:"names";a:3:{i:0;s:12:"Main content";i:1;s:27:"Module or secondary content";i:2;s:6:"Widget";}s:14:"default_extras";a:3:{i:0;s:6:"editor";i:1;s:6:"editor";i:2;s:1:"6";}}');

-- --------------------------------------------------------

--
-- Table structure for table `search_index`
--

CREATE TABLE `search_index` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `other_id` int(11) NOT NULL,
  `field` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `active` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`module`,`other_id`,`field`,`language`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Search index';

--
-- Dumping data for table `search_index`
--

INSERT INTO `search_index` VALUES('pages', 1, 'title', 'Home', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 1, 'text', ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod! Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan. ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 2, 'title', 'Sitemap', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 2, 'text', ' Take a look at all the pages in our website: ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 3, 'title', 'Disclaimer', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 3, 'text', ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan. ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 3, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 410, 'title', 'Contact', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 410, 'text', ' A question? Fill out the contact form! ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 410, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 4, 'title', 'About', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 4, 'text', '   ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 5, 'title', 'Contact', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 5, 'text', ' Want to leave a message? ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 404, 'title', '404', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 404, 'text', '   ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 404, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 408, 'title', 'Tags', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 405, 'title', 'Search', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 405, 'text', '   ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 406, 'title', 'Blog', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 406, 'text', '      ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 407, 'title', 'About us', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 407, 'text', '  ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 407, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 406, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('blog', 1, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('blog', 1, 'title', 'Dolor sit amet', 'en', 'Y');
INSERT INTO `search_index` VALUES('blog', 1, 'text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.', 'en', 'Y');
INSERT INTO `search_index` VALUES('blog', 2, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('blog', 2, 'title', 'Lorem ipsum', 'en', 'Y');
INSERT INTO `search_index` VALUES('blog', 2, 'text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 1, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 4, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 2, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 408, 'text', '   ', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 408, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 405, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 411, 'title', 'Another page', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 411, 'text', ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus.\r\nVestibulum in tortor sodales elit sollicitudin  gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat.  Sed ut nisl congue justo pharetra accumsan. Lorem ipsum dolor sit amet,  consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum  tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet  orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed  tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac,  felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus  justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis.  Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit  sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam  erat volutpat. Sed ut nisl congue justo pharetra accumsan. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 411, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 409, 'title', 'History', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 409, 'text', ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus.\r\nVestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id  magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec  interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac  nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis  quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit  consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at,  vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit  amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida.  Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut  nisl congue justo pharetra accumsan.', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 409, 'tags', '', 'en', 'Y');
INSERT INTO `search_index` VALUES('pages', 5, 'tags', '', 'en', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `search_modules`
--

CREATE TABLE `search_modules` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `searchable` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `search_modules`
--

INSERT INTO `search_modules` VALUES('pages', 'Y', 1);
INSERT INTO `search_modules` VALUES('blog', 'Y', 1);

-- --------------------------------------------------------

--
-- Table structure for table `search_statistics`
--

CREATE TABLE `search_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `num_results` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `search_statistics`
--


-- --------------------------------------------------------

--
-- Table structure for table `search_synonyms`
--

CREATE TABLE `search_synonyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `synonym` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`term`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `search_synonyms`
--


-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `timezones`
--

CREATE TABLE `timezones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=454 ;

--
-- Dumping data for table `timezones`
--

INSERT INTO `timezones` VALUES(1, 'Africa/Abidjan');
INSERT INTO `timezones` VALUES(2, 'Africa/Accra');
INSERT INTO `timezones` VALUES(3, 'Africa/Addis_Ababa');
INSERT INTO `timezones` VALUES(4, 'Africa/Algiers');
INSERT INTO `timezones` VALUES(5, 'Africa/Asmara');
INSERT INTO `timezones` VALUES(6, 'Africa/Asmera');
INSERT INTO `timezones` VALUES(7, 'Africa/Bamako');
INSERT INTO `timezones` VALUES(8, 'Africa/Bangui');
INSERT INTO `timezones` VALUES(9, 'Africa/Banjul');
INSERT INTO `timezones` VALUES(10, 'Africa/Bissau');
INSERT INTO `timezones` VALUES(11, 'Africa/Blantyre');
INSERT INTO `timezones` VALUES(12, 'Africa/Brazzaville');
INSERT INTO `timezones` VALUES(13, 'Africa/Bujumbura');
INSERT INTO `timezones` VALUES(14, 'Africa/Cairo');
INSERT INTO `timezones` VALUES(15, 'Africa/Casablanca');
INSERT INTO `timezones` VALUES(16, 'Africa/Ceuta');
INSERT INTO `timezones` VALUES(17, 'Africa/Conakry');
INSERT INTO `timezones` VALUES(18, 'Africa/Dakar');
INSERT INTO `timezones` VALUES(19, 'Africa/Dar_es_Salaam');
INSERT INTO `timezones` VALUES(20, 'Africa/Djibouti');
INSERT INTO `timezones` VALUES(21, 'Africa/Douala');
INSERT INTO `timezones` VALUES(22, 'Africa/El_Aaiun');
INSERT INTO `timezones` VALUES(23, 'Africa/Freetown');
INSERT INTO `timezones` VALUES(24, 'Africa/Gaborone');
INSERT INTO `timezones` VALUES(25, 'Africa/Harare');
INSERT INTO `timezones` VALUES(26, 'Africa/Johannesburg');
INSERT INTO `timezones` VALUES(27, 'Africa/Kampala');
INSERT INTO `timezones` VALUES(28, 'Africa/Khartoum');
INSERT INTO `timezones` VALUES(29, 'Africa/Kigali');
INSERT INTO `timezones` VALUES(30, 'Africa/Kinshasa');
INSERT INTO `timezones` VALUES(31, 'Africa/Lagos');
INSERT INTO `timezones` VALUES(32, 'Africa/Libreville');
INSERT INTO `timezones` VALUES(33, 'Africa/Lome');
INSERT INTO `timezones` VALUES(34, 'Africa/Luanda');
INSERT INTO `timezones` VALUES(35, 'Africa/Lubumbashi');
INSERT INTO `timezones` VALUES(36, 'Africa/Lusaka');
INSERT INTO `timezones` VALUES(37, 'Africa/Malabo');
INSERT INTO `timezones` VALUES(38, 'Africa/Maputo');
INSERT INTO `timezones` VALUES(39, 'Africa/Maseru');
INSERT INTO `timezones` VALUES(40, 'Africa/Mbabane');
INSERT INTO `timezones` VALUES(41, 'Africa/Mogadishu');
INSERT INTO `timezones` VALUES(42, 'Africa/Monrovia');
INSERT INTO `timezones` VALUES(43, 'Africa/Nairobi');
INSERT INTO `timezones` VALUES(44, 'Africa/Ndjamena');
INSERT INTO `timezones` VALUES(45, 'Africa/Niamey');
INSERT INTO `timezones` VALUES(46, 'Africa/Nouakchott');
INSERT INTO `timezones` VALUES(47, 'Africa/Ouagadougou');
INSERT INTO `timezones` VALUES(48, 'Africa/Porto-Novo');
INSERT INTO `timezones` VALUES(49, 'Africa/Sao_Tome');
INSERT INTO `timezones` VALUES(50, 'Africa/Timbuktu');
INSERT INTO `timezones` VALUES(51, 'Africa/Tripoli');
INSERT INTO `timezones` VALUES(52, 'Africa/Tunis');
INSERT INTO `timezones` VALUES(53, 'Africa/Windhoek');
INSERT INTO `timezones` VALUES(54, 'America/Adak');
INSERT INTO `timezones` VALUES(55, 'America/Anchorage');
INSERT INTO `timezones` VALUES(56, 'America/Anguilla');
INSERT INTO `timezones` VALUES(57, 'America/Antigua');
INSERT INTO `timezones` VALUES(58, 'America/Araguaina');
INSERT INTO `timezones` VALUES(59, 'America/Argentina/Buenos_Aires');
INSERT INTO `timezones` VALUES(60, 'America/Argentina/Catamarca');
INSERT INTO `timezones` VALUES(61, 'America/Argentina/ComodRivadavia');
INSERT INTO `timezones` VALUES(62, 'America/Argentina/Cordoba');
INSERT INTO `timezones` VALUES(63, 'America/Argentina/Jujuy');
INSERT INTO `timezones` VALUES(64, 'America/Argentina/La_Rioja');
INSERT INTO `timezones` VALUES(65, 'America/Argentina/Mendoza');
INSERT INTO `timezones` VALUES(66, 'America/Argentina/Rio_Gallegos');
INSERT INTO `timezones` VALUES(67, 'America/Argentina/Salta');
INSERT INTO `timezones` VALUES(68, 'America/Argentina/San_Juan');
INSERT INTO `timezones` VALUES(69, 'America/Argentina/San_Luis');
INSERT INTO `timezones` VALUES(70, 'America/Argentina/Tucuman');
INSERT INTO `timezones` VALUES(71, 'America/Argentina/Ushuaia');
INSERT INTO `timezones` VALUES(72, 'America/Aruba');
INSERT INTO `timezones` VALUES(73, 'America/Asuncion');
INSERT INTO `timezones` VALUES(74, 'America/Atikokan');
INSERT INTO `timezones` VALUES(75, 'America/Atka');
INSERT INTO `timezones` VALUES(76, 'America/Bahia');
INSERT INTO `timezones` VALUES(77, 'America/Barbados');
INSERT INTO `timezones` VALUES(78, 'America/Belem');
INSERT INTO `timezones` VALUES(79, 'America/Belize');
INSERT INTO `timezones` VALUES(80, 'America/Blanc-Sablon');
INSERT INTO `timezones` VALUES(81, 'America/Boa_Vista');
INSERT INTO `timezones` VALUES(82, 'America/Bogota');
INSERT INTO `timezones` VALUES(83, 'America/Boise');
INSERT INTO `timezones` VALUES(84, 'America/Buenos_Aires');
INSERT INTO `timezones` VALUES(85, 'America/Cambridge_Bay');
INSERT INTO `timezones` VALUES(86, 'America/Campo_Grande');
INSERT INTO `timezones` VALUES(87, 'America/Cancun');
INSERT INTO `timezones` VALUES(88, 'America/Caracas');
INSERT INTO `timezones` VALUES(89, 'America/Catamarca');
INSERT INTO `timezones` VALUES(90, 'America/Cayenne');
INSERT INTO `timezones` VALUES(91, 'America/Cayman');
INSERT INTO `timezones` VALUES(92, 'America/Chicago');
INSERT INTO `timezones` VALUES(93, 'America/Chihuahua');
INSERT INTO `timezones` VALUES(94, 'America/Coral_Harbour');
INSERT INTO `timezones` VALUES(95, 'America/Cordoba');
INSERT INTO `timezones` VALUES(96, 'America/Costa_Rica');
INSERT INTO `timezones` VALUES(97, 'America/Cuiaba');
INSERT INTO `timezones` VALUES(98, 'America/Curacao');
INSERT INTO `timezones` VALUES(99, 'America/Danmarkshavn');
INSERT INTO `timezones` VALUES(100, 'America/Dawson');
INSERT INTO `timezones` VALUES(101, 'America/Dawson_Creek');
INSERT INTO `timezones` VALUES(102, 'America/Denver');
INSERT INTO `timezones` VALUES(103, 'America/Detroit');
INSERT INTO `timezones` VALUES(104, 'America/Dominica');
INSERT INTO `timezones` VALUES(105, 'America/Edmonton');
INSERT INTO `timezones` VALUES(106, 'America/Eirunepe');
INSERT INTO `timezones` VALUES(107, 'America/El_Salvador');
INSERT INTO `timezones` VALUES(108, 'America/Ensenada');
INSERT INTO `timezones` VALUES(109, 'America/Fort_Wayne');
INSERT INTO `timezones` VALUES(110, 'America/Fortaleza');
INSERT INTO `timezones` VALUES(111, 'America/Glace_Bay');
INSERT INTO `timezones` VALUES(112, 'America/Godthab');
INSERT INTO `timezones` VALUES(113, 'America/Goose_Bay');
INSERT INTO `timezones` VALUES(114, 'America/Grand_Turk');
INSERT INTO `timezones` VALUES(115, 'America/Grenada');
INSERT INTO `timezones` VALUES(116, 'America/Guadeloupe');
INSERT INTO `timezones` VALUES(117, 'America/Guatemala');
INSERT INTO `timezones` VALUES(118, 'America/Guayaquil');
INSERT INTO `timezones` VALUES(119, 'America/Guyana');
INSERT INTO `timezones` VALUES(120, 'America/Halifax');
INSERT INTO `timezones` VALUES(121, 'America/Havana');
INSERT INTO `timezones` VALUES(122, 'America/Hermosillo');
INSERT INTO `timezones` VALUES(123, 'America/Indiana/Indianapolis');
INSERT INTO `timezones` VALUES(124, 'America/Indiana/Knox');
INSERT INTO `timezones` VALUES(125, 'America/Indiana/Marengo');
INSERT INTO `timezones` VALUES(126, 'America/Indiana/Petersburg');
INSERT INTO `timezones` VALUES(127, 'America/Indiana/Tell_City');
INSERT INTO `timezones` VALUES(128, 'America/Indiana/Vevay');
INSERT INTO `timezones` VALUES(129, 'America/Indiana/Vincennes');
INSERT INTO `timezones` VALUES(130, 'America/Indiana/Winamac');
INSERT INTO `timezones` VALUES(131, 'America/Indianapolis');
INSERT INTO `timezones` VALUES(132, 'America/Inuvik');
INSERT INTO `timezones` VALUES(133, 'America/Iqaluit');
INSERT INTO `timezones` VALUES(134, 'America/Jamaica');
INSERT INTO `timezones` VALUES(135, 'America/Jujuy');
INSERT INTO `timezones` VALUES(136, 'America/Juneau');
INSERT INTO `timezones` VALUES(137, 'America/Kentucky/Louisville');
INSERT INTO `timezones` VALUES(138, 'America/Kentucky/Monticello');
INSERT INTO `timezones` VALUES(139, 'America/Knox_IN');
INSERT INTO `timezones` VALUES(140, 'America/La_Paz');
INSERT INTO `timezones` VALUES(141, 'America/Lima');
INSERT INTO `timezones` VALUES(142, 'America/Los_Angeles');
INSERT INTO `timezones` VALUES(143, 'America/Louisville');
INSERT INTO `timezones` VALUES(144, 'America/Maceio');
INSERT INTO `timezones` VALUES(145, 'America/Managua');
INSERT INTO `timezones` VALUES(146, 'America/Manaus');
INSERT INTO `timezones` VALUES(147, 'America/Marigot');
INSERT INTO `timezones` VALUES(148, 'America/Martinique');
INSERT INTO `timezones` VALUES(149, 'America/Matamoros');
INSERT INTO `timezones` VALUES(150, 'America/Mazatlan');
INSERT INTO `timezones` VALUES(151, 'America/Mendoza');
INSERT INTO `timezones` VALUES(152, 'America/Menominee');
INSERT INTO `timezones` VALUES(153, 'America/Merida');
INSERT INTO `timezones` VALUES(154, 'America/Mexico_City');
INSERT INTO `timezones` VALUES(155, 'America/Miquelon');
INSERT INTO `timezones` VALUES(156, 'America/Moncton');
INSERT INTO `timezones` VALUES(157, 'America/Monterrey');
INSERT INTO `timezones` VALUES(158, 'America/Montevideo');
INSERT INTO `timezones` VALUES(159, 'America/Montreal');
INSERT INTO `timezones` VALUES(160, 'America/Montserrat');
INSERT INTO `timezones` VALUES(161, 'America/Nassau');
INSERT INTO `timezones` VALUES(162, 'America/New_York');
INSERT INTO `timezones` VALUES(163, 'America/Nipigon');
INSERT INTO `timezones` VALUES(164, 'America/Nome');
INSERT INTO `timezones` VALUES(165, 'America/Noronha');
INSERT INTO `timezones` VALUES(166, 'America/North_Dakota/Center');
INSERT INTO `timezones` VALUES(167, 'America/North_Dakota/New_Salem');
INSERT INTO `timezones` VALUES(168, 'America/Ojinaga');
INSERT INTO `timezones` VALUES(169, 'America/Panama');
INSERT INTO `timezones` VALUES(170, 'America/Pangnirtung');
INSERT INTO `timezones` VALUES(171, 'America/Paramaribo');
INSERT INTO `timezones` VALUES(172, 'America/Phoenix');
INSERT INTO `timezones` VALUES(173, 'America/Port-au-Prince');
INSERT INTO `timezones` VALUES(174, 'America/Port_of_Spain');
INSERT INTO `timezones` VALUES(175, 'America/Porto_Acre');
INSERT INTO `timezones` VALUES(176, 'America/Porto_Velho');
INSERT INTO `timezones` VALUES(177, 'America/Puerto_Rico');
INSERT INTO `timezones` VALUES(178, 'America/Rainy_River');
INSERT INTO `timezones` VALUES(179, 'America/Rankin_Inlet');
INSERT INTO `timezones` VALUES(180, 'America/Recife');
INSERT INTO `timezones` VALUES(181, 'America/Regina');
INSERT INTO `timezones` VALUES(182, 'America/Resolute');
INSERT INTO `timezones` VALUES(183, 'America/Rio_Branco');
INSERT INTO `timezones` VALUES(184, 'America/Rosario');
INSERT INTO `timezones` VALUES(185, 'America/Santa_Isabel');
INSERT INTO `timezones` VALUES(186, 'America/Santarem');
INSERT INTO `timezones` VALUES(187, 'America/Santiago');
INSERT INTO `timezones` VALUES(188, 'America/Santo_Domingo');
INSERT INTO `timezones` VALUES(189, 'America/Sao_Paulo');
INSERT INTO `timezones` VALUES(190, 'America/Scoresbysund');
INSERT INTO `timezones` VALUES(191, 'America/Shiprock');
INSERT INTO `timezones` VALUES(192, 'America/St_Barthelemy');
INSERT INTO `timezones` VALUES(193, 'America/St_Johns');
INSERT INTO `timezones` VALUES(194, 'America/St_Kitts');
INSERT INTO `timezones` VALUES(195, 'America/St_Lucia');
INSERT INTO `timezones` VALUES(196, 'America/St_Thomas');
INSERT INTO `timezones` VALUES(197, 'America/St_Vincent');
INSERT INTO `timezones` VALUES(198, 'America/Swift_Current');
INSERT INTO `timezones` VALUES(199, 'America/Tegucigalpa');
INSERT INTO `timezones` VALUES(200, 'America/Thule');
INSERT INTO `timezones` VALUES(201, 'America/Thunder_Bay');
INSERT INTO `timezones` VALUES(202, 'America/Tijuana');
INSERT INTO `timezones` VALUES(203, 'America/Toronto');
INSERT INTO `timezones` VALUES(204, 'America/Tortola');
INSERT INTO `timezones` VALUES(205, 'America/Vancouver');
INSERT INTO `timezones` VALUES(206, 'America/Virgin');
INSERT INTO `timezones` VALUES(207, 'America/Whitehorse');
INSERT INTO `timezones` VALUES(208, 'America/Winnipeg');
INSERT INTO `timezones` VALUES(209, 'America/Yakutat');
INSERT INTO `timezones` VALUES(210, 'America/Yellowknife');
INSERT INTO `timezones` VALUES(211, 'Antarctica/Casey');
INSERT INTO `timezones` VALUES(212, 'Antarctica/Davis');
INSERT INTO `timezones` VALUES(213, 'Antarctica/DumontDUrville');
INSERT INTO `timezones` VALUES(214, 'Antarctica/Mawson');
INSERT INTO `timezones` VALUES(215, 'Antarctica/McMurdo');
INSERT INTO `timezones` VALUES(216, 'Antarctica/Palmer');
INSERT INTO `timezones` VALUES(217, 'Antarctica/Rothera');
INSERT INTO `timezones` VALUES(218, 'Antarctica/South_Pole');
INSERT INTO `timezones` VALUES(219, 'Antarctica/Syowa');
INSERT INTO `timezones` VALUES(220, 'Antarctica/Vostok');
INSERT INTO `timezones` VALUES(221, 'Arctic/Longyearbyen');
INSERT INTO `timezones` VALUES(222, 'Asia/Aden');
INSERT INTO `timezones` VALUES(223, 'Asia/Almaty');
INSERT INTO `timezones` VALUES(224, 'Asia/Amman');
INSERT INTO `timezones` VALUES(225, 'Asia/Anadyr');
INSERT INTO `timezones` VALUES(226, 'Asia/Aqtau');
INSERT INTO `timezones` VALUES(227, 'Asia/Aqtobe');
INSERT INTO `timezones` VALUES(228, 'Asia/Ashgabat');
INSERT INTO `timezones` VALUES(229, 'Asia/Ashkhabad');
INSERT INTO `timezones` VALUES(230, 'Asia/Baghdad');
INSERT INTO `timezones` VALUES(231, 'Asia/Bahrain');
INSERT INTO `timezones` VALUES(232, 'Asia/Baku');
INSERT INTO `timezones` VALUES(233, 'Asia/Bangkok');
INSERT INTO `timezones` VALUES(234, 'Asia/Beirut');
INSERT INTO `timezones` VALUES(235, 'Asia/Bishkek');
INSERT INTO `timezones` VALUES(236, 'Asia/Brunei');
INSERT INTO `timezones` VALUES(237, 'Asia/Calcutta');
INSERT INTO `timezones` VALUES(238, 'Asia/Choibalsan');
INSERT INTO `timezones` VALUES(239, 'Asia/Chongqing');
INSERT INTO `timezones` VALUES(240, 'Asia/Chungking');
INSERT INTO `timezones` VALUES(241, 'Asia/Colombo');
INSERT INTO `timezones` VALUES(242, 'Asia/Dacca');
INSERT INTO `timezones` VALUES(243, 'Asia/Damascus');
INSERT INTO `timezones` VALUES(244, 'Asia/Dhaka');
INSERT INTO `timezones` VALUES(245, 'Asia/Dili');
INSERT INTO `timezones` VALUES(246, 'Asia/Dubai');
INSERT INTO `timezones` VALUES(247, 'Asia/Dushanbe');
INSERT INTO `timezones` VALUES(248, 'Asia/Gaza');
INSERT INTO `timezones` VALUES(249, 'Asia/Harbin');
INSERT INTO `timezones` VALUES(250, 'Asia/Ho_Chi_Minh');
INSERT INTO `timezones` VALUES(251, 'Asia/Hong_Kong');
INSERT INTO `timezones` VALUES(252, 'Asia/Hovd');
INSERT INTO `timezones` VALUES(253, 'Asia/Irkutsk');
INSERT INTO `timezones` VALUES(254, 'Asia/Istanbul');
INSERT INTO `timezones` VALUES(255, 'Asia/Jakarta');
INSERT INTO `timezones` VALUES(256, 'Asia/Jayapura');
INSERT INTO `timezones` VALUES(257, 'Asia/Jerusalem');
INSERT INTO `timezones` VALUES(258, 'Asia/Kabul');
INSERT INTO `timezones` VALUES(259, 'Asia/Kamchatka');
INSERT INTO `timezones` VALUES(260, 'Asia/Karachi');
INSERT INTO `timezones` VALUES(261, 'Asia/Kashgar');
INSERT INTO `timezones` VALUES(262, 'Asia/Kathmandu');
INSERT INTO `timezones` VALUES(263, 'Asia/Katmandu');
INSERT INTO `timezones` VALUES(264, 'Asia/Kolkata');
INSERT INTO `timezones` VALUES(265, 'Asia/Krasnoyarsk');
INSERT INTO `timezones` VALUES(266, 'Asia/Kuala_Lumpur');
INSERT INTO `timezones` VALUES(267, 'Asia/Kuching');
INSERT INTO `timezones` VALUES(268, 'Asia/Kuwait');
INSERT INTO `timezones` VALUES(269, 'Asia/Macao');
INSERT INTO `timezones` VALUES(270, 'Asia/Macau');
INSERT INTO `timezones` VALUES(271, 'Asia/Magadan');
INSERT INTO `timezones` VALUES(272, 'Asia/Makassar');
INSERT INTO `timezones` VALUES(273, 'Asia/Manila');
INSERT INTO `timezones` VALUES(274, 'Asia/Muscat');
INSERT INTO `timezones` VALUES(275, 'Asia/Nicosia');
INSERT INTO `timezones` VALUES(276, 'Asia/Novokuznetsk');
INSERT INTO `timezones` VALUES(277, 'Asia/Novosibirsk');
INSERT INTO `timezones` VALUES(278, 'Asia/Omsk');
INSERT INTO `timezones` VALUES(279, 'Asia/Oral');
INSERT INTO `timezones` VALUES(280, 'Asia/Phnom_Penh');
INSERT INTO `timezones` VALUES(281, 'Asia/Pontianak');
INSERT INTO `timezones` VALUES(282, 'Asia/Pyongyang');
INSERT INTO `timezones` VALUES(283, 'Asia/Qatar');
INSERT INTO `timezones` VALUES(284, 'Asia/Qyzylorda');
INSERT INTO `timezones` VALUES(285, 'Asia/Rangoon');
INSERT INTO `timezones` VALUES(286, 'Asia/Riyadh');
INSERT INTO `timezones` VALUES(287, 'Asia/Saigon');
INSERT INTO `timezones` VALUES(288, 'Asia/Sakhalin');
INSERT INTO `timezones` VALUES(289, 'Asia/Samarkand');
INSERT INTO `timezones` VALUES(290, 'Asia/Seoul');
INSERT INTO `timezones` VALUES(291, 'Asia/Shanghai');
INSERT INTO `timezones` VALUES(292, 'Asia/Singapore');
INSERT INTO `timezones` VALUES(293, 'Asia/Taipei');
INSERT INTO `timezones` VALUES(294, 'Asia/Tashkent');
INSERT INTO `timezones` VALUES(295, 'Asia/Tbilisi');
INSERT INTO `timezones` VALUES(296, 'Asia/Tehran');
INSERT INTO `timezones` VALUES(297, 'Asia/Tel_Aviv');
INSERT INTO `timezones` VALUES(298, 'Asia/Thimbu');
INSERT INTO `timezones` VALUES(299, 'Asia/Thimphu');
INSERT INTO `timezones` VALUES(300, 'Asia/Tokyo');
INSERT INTO `timezones` VALUES(301, 'Asia/Ujung_Pandang');
INSERT INTO `timezones` VALUES(302, 'Asia/Ulaanbaatar');
INSERT INTO `timezones` VALUES(303, 'Asia/Ulan_Bator');
INSERT INTO `timezones` VALUES(304, 'Asia/Urumqi');
INSERT INTO `timezones` VALUES(305, 'Asia/Vientiane');
INSERT INTO `timezones` VALUES(306, 'Asia/Vladivostok');
INSERT INTO `timezones` VALUES(307, 'Asia/Yakutsk');
INSERT INTO `timezones` VALUES(308, 'Asia/Yekaterinburg');
INSERT INTO `timezones` VALUES(309, 'Asia/Yerevan');
INSERT INTO `timezones` VALUES(310, 'Atlantic/Azores');
INSERT INTO `timezones` VALUES(311, 'Atlantic/Bermuda');
INSERT INTO `timezones` VALUES(312, 'Atlantic/Canary');
INSERT INTO `timezones` VALUES(313, 'Atlantic/Cape_Verde');
INSERT INTO `timezones` VALUES(314, 'Atlantic/Faeroe');
INSERT INTO `timezones` VALUES(315, 'Atlantic/Faroe');
INSERT INTO `timezones` VALUES(316, 'Atlantic/Jan_Mayen');
INSERT INTO `timezones` VALUES(317, 'Atlantic/Madeira');
INSERT INTO `timezones` VALUES(318, 'Atlantic/Reykjavik');
INSERT INTO `timezones` VALUES(319, 'Atlantic/South_Georgia');
INSERT INTO `timezones` VALUES(320, 'Atlantic/St_Helena');
INSERT INTO `timezones` VALUES(321, 'Atlantic/Stanley');
INSERT INTO `timezones` VALUES(322, 'Australia/ACT');
INSERT INTO `timezones` VALUES(323, 'Australia/Adelaide');
INSERT INTO `timezones` VALUES(324, 'Australia/Brisbane');
INSERT INTO `timezones` VALUES(325, 'Australia/Broken_Hill');
INSERT INTO `timezones` VALUES(326, 'Australia/Canberra');
INSERT INTO `timezones` VALUES(327, 'Australia/Currie');
INSERT INTO `timezones` VALUES(328, 'Australia/Darwin');
INSERT INTO `timezones` VALUES(329, 'Australia/Eucla');
INSERT INTO `timezones` VALUES(330, 'Australia/Hobart');
INSERT INTO `timezones` VALUES(331, 'Australia/LHI');
INSERT INTO `timezones` VALUES(332, 'Australia/Lindeman');
INSERT INTO `timezones` VALUES(333, 'Australia/Lord_Howe');
INSERT INTO `timezones` VALUES(334, 'Australia/Melbourne');
INSERT INTO `timezones` VALUES(335, 'Australia/North');
INSERT INTO `timezones` VALUES(336, 'Australia/NSW');
INSERT INTO `timezones` VALUES(337, 'Australia/Perth');
INSERT INTO `timezones` VALUES(338, 'Australia/Queensland');
INSERT INTO `timezones` VALUES(339, 'Australia/South');
INSERT INTO `timezones` VALUES(340, 'Australia/Sydney');
INSERT INTO `timezones` VALUES(341, 'Australia/Tasmania');
INSERT INTO `timezones` VALUES(342, 'Australia/Victoria');
INSERT INTO `timezones` VALUES(343, 'Australia/West');
INSERT INTO `timezones` VALUES(344, 'Australia/Yancowinna');
INSERT INTO `timezones` VALUES(345, 'Europe/Amsterdam');
INSERT INTO `timezones` VALUES(346, 'Europe/Andorra');
INSERT INTO `timezones` VALUES(347, 'Europe/Athens');
INSERT INTO `timezones` VALUES(348, 'Europe/Belfast');
INSERT INTO `timezones` VALUES(349, 'Europe/Belgrade');
INSERT INTO `timezones` VALUES(350, 'Europe/Berlin');
INSERT INTO `timezones` VALUES(351, 'Europe/Bratislava');
INSERT INTO `timezones` VALUES(352, 'Europe/Brussels');
INSERT INTO `timezones` VALUES(353, 'Europe/Bucharest');
INSERT INTO `timezones` VALUES(354, 'Europe/Budapest');
INSERT INTO `timezones` VALUES(355, 'Europe/Chisinau');
INSERT INTO `timezones` VALUES(356, 'Europe/Copenhagen');
INSERT INTO `timezones` VALUES(357, 'Europe/Dublin');
INSERT INTO `timezones` VALUES(358, 'Europe/Gibraltar');
INSERT INTO `timezones` VALUES(359, 'Europe/Guernsey');
INSERT INTO `timezones` VALUES(360, 'Europe/Helsinki');
INSERT INTO `timezones` VALUES(361, 'Europe/Isle_of_Man');
INSERT INTO `timezones` VALUES(362, 'Europe/Istanbul');
INSERT INTO `timezones` VALUES(363, 'Europe/Jersey');
INSERT INTO `timezones` VALUES(364, 'Europe/Kaliningrad');
INSERT INTO `timezones` VALUES(365, 'Europe/Kiev');
INSERT INTO `timezones` VALUES(366, 'Europe/Lisbon');
INSERT INTO `timezones` VALUES(367, 'Europe/Ljubljana');
INSERT INTO `timezones` VALUES(368, 'Europe/London');
INSERT INTO `timezones` VALUES(369, 'Europe/Luxembourg');
INSERT INTO `timezones` VALUES(370, 'Europe/Madrid');
INSERT INTO `timezones` VALUES(371, 'Europe/Malta');
INSERT INTO `timezones` VALUES(372, 'Europe/Mariehamn');
INSERT INTO `timezones` VALUES(373, 'Europe/Minsk');
INSERT INTO `timezones` VALUES(374, 'Europe/Monaco');
INSERT INTO `timezones` VALUES(375, 'Europe/Moscow');
INSERT INTO `timezones` VALUES(376, 'Europe/Nicosia');
INSERT INTO `timezones` VALUES(377, 'Europe/Oslo');
INSERT INTO `timezones` VALUES(378, 'Europe/Paris');
INSERT INTO `timezones` VALUES(379, 'Europe/Podgorica');
INSERT INTO `timezones` VALUES(380, 'Europe/Prague');
INSERT INTO `timezones` VALUES(381, 'Europe/Riga');
INSERT INTO `timezones` VALUES(382, 'Europe/Rome');
INSERT INTO `timezones` VALUES(383, 'Europe/Samara');
INSERT INTO `timezones` VALUES(384, 'Europe/San_Marino');
INSERT INTO `timezones` VALUES(385, 'Europe/Sarajevo');
INSERT INTO `timezones` VALUES(386, 'Europe/Simferopol');
INSERT INTO `timezones` VALUES(387, 'Europe/Skopje');
INSERT INTO `timezones` VALUES(388, 'Europe/Sofia');
INSERT INTO `timezones` VALUES(389, 'Europe/Stockholm');
INSERT INTO `timezones` VALUES(390, 'Europe/Tallinn');
INSERT INTO `timezones` VALUES(391, 'Europe/Tirane');
INSERT INTO `timezones` VALUES(392, 'Europe/Tiraspol');
INSERT INTO `timezones` VALUES(393, 'Europe/Uzhgorod');
INSERT INTO `timezones` VALUES(394, 'Europe/Vaduz');
INSERT INTO `timezones` VALUES(395, 'Europe/Vatican');
INSERT INTO `timezones` VALUES(396, 'Europe/Vienna');
INSERT INTO `timezones` VALUES(397, 'Europe/Vilnius');
INSERT INTO `timezones` VALUES(398, 'Europe/Volgograd');
INSERT INTO `timezones` VALUES(399, 'Europe/Warsaw');
INSERT INTO `timezones` VALUES(400, 'Europe/Zagreb');
INSERT INTO `timezones` VALUES(401, 'Europe/Zaporozhye');
INSERT INTO `timezones` VALUES(402, 'Europe/Zurich');
INSERT INTO `timezones` VALUES(403, 'Indian/Antananarivo');
INSERT INTO `timezones` VALUES(404, 'Indian/Chagos');
INSERT INTO `timezones` VALUES(405, 'Indian/Christmas');
INSERT INTO `timezones` VALUES(406, 'Indian/Cocos');
INSERT INTO `timezones` VALUES(407, 'Indian/Comoro');
INSERT INTO `timezones` VALUES(408, 'Indian/Kerguelen');
INSERT INTO `timezones` VALUES(409, 'Indian/Mahe');
INSERT INTO `timezones` VALUES(410, 'Indian/Maldives');
INSERT INTO `timezones` VALUES(411, 'Indian/Mauritius');
INSERT INTO `timezones` VALUES(412, 'Indian/Mayotte');
INSERT INTO `timezones` VALUES(413, 'Indian/Reunion');
INSERT INTO `timezones` VALUES(414, 'Pacific/Apia');
INSERT INTO `timezones` VALUES(415, 'Pacific/Auckland');
INSERT INTO `timezones` VALUES(416, 'Pacific/Chatham');
INSERT INTO `timezones` VALUES(417, 'Pacific/Easter');
INSERT INTO `timezones` VALUES(418, 'Pacific/Efate');
INSERT INTO `timezones` VALUES(419, 'Pacific/Enderbury');
INSERT INTO `timezones` VALUES(420, 'Pacific/Fakaofo');
INSERT INTO `timezones` VALUES(421, 'Pacific/Fiji');
INSERT INTO `timezones` VALUES(422, 'Pacific/Funafuti');
INSERT INTO `timezones` VALUES(423, 'Pacific/Galapagos');
INSERT INTO `timezones` VALUES(424, 'Pacific/Gambier');
INSERT INTO `timezones` VALUES(425, 'Pacific/Guadalcanal');
INSERT INTO `timezones` VALUES(426, 'Pacific/Guam');
INSERT INTO `timezones` VALUES(427, 'Pacific/Honolulu');
INSERT INTO `timezones` VALUES(428, 'Pacific/Johnston');
INSERT INTO `timezones` VALUES(429, 'Pacific/Kiritimati');
INSERT INTO `timezones` VALUES(430, 'Pacific/Kosrae');
INSERT INTO `timezones` VALUES(431, 'Pacific/Kwajalein');
INSERT INTO `timezones` VALUES(432, 'Pacific/Majuro');
INSERT INTO `timezones` VALUES(433, 'Pacific/Marquesas');
INSERT INTO `timezones` VALUES(434, 'Pacific/Midway');
INSERT INTO `timezones` VALUES(435, 'Pacific/Nauru');
INSERT INTO `timezones` VALUES(436, 'Pacific/Niue');
INSERT INTO `timezones` VALUES(437, 'Pacific/Norfolk');
INSERT INTO `timezones` VALUES(438, 'Pacific/Noumea');
INSERT INTO `timezones` VALUES(439, 'Pacific/Pago_Pago');
INSERT INTO `timezones` VALUES(440, 'Pacific/Palau');
INSERT INTO `timezones` VALUES(441, 'Pacific/Pitcairn');
INSERT INTO `timezones` VALUES(442, 'Pacific/Ponape');
INSERT INTO `timezones` VALUES(443, 'Pacific/Port_Moresby');
INSERT INTO `timezones` VALUES(444, 'Pacific/Rarotonga');
INSERT INTO `timezones` VALUES(445, 'Pacific/Saipan');
INSERT INTO `timezones` VALUES(446, 'Pacific/Samoa');
INSERT INTO `timezones` VALUES(447, 'Pacific/Tahiti');
INSERT INTO `timezones` VALUES(448, 'Pacific/Tarawa');
INSERT INTO `timezones` VALUES(449, 'Pacific/Tongatapu');
INSERT INTO `timezones` VALUES(450, 'Pacific/Truk');
INSERT INTO `timezones` VALUES(451, 'Pacific/Wake');
INSERT INTO `timezones` VALUES(452, 'Pacific/Wallis');
INSERT INTO `timezones` VALUES(453, 'Pacific/Yap');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'will be case-sensitive',
  `active` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'is this user active?',
  `deleted` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'is the user deleted?',
  `is_god` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The backend users' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 1, 'johan@netlash.com', '68b63054fa6f6666d4efa5db849f52289634d168', 'Y', 'N', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

CREATE TABLE `users_sessions` (
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

INSERT INTO `users_sessions` VALUES(1, 1, '49cfc9589bb2aed0aff0deae621318db', 'f222f1135996fc8cb02a869b8c33f25ef21bd793', '2010-09-23 16:23:55');
INSERT INTO `users_sessions` VALUES(3, 1, '0ed15942e9da6f9ffacf07a3bc471f1d', 'cf4b2ac329bc10b7a840a4dfa24718aef364f26e', '2010-09-23 13:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `users_settings`
--

CREATE TABLE `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_settings`
--

INSERT INTO `users_settings` VALUES(1, 'nickname', 's:8:"Fork CMS";');
INSERT INTO `users_settings` VALUES(1, 'name', 's:4:"Fork";');
INSERT INTO `users_settings` VALUES(1, 'surname', 's:3:"CMS";');
INSERT INTO `users_settings` VALUES(1, 'interface_language', 's:2:"en";');
INSERT INTO `users_settings` VALUES(1, 'date_format', 's:5:"j F Y";');
INSERT INTO `users_settings` VALUES(1, 'time_format', 's:3:"H:i";');
INSERT INTO `users_settings` VALUES(1, 'datetime_format', 's:9:"j F Y H:i";');
INSERT INTO `users_settings` VALUES(1, 'password_key', 's:13:"4c9b38679d7c1";');
INSERT INTO `users_settings` VALUES(1, 'avatar', 's:7:"god.jpg";');
