-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Feb 15, 2010 at 01:10 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `forkng`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `blog_categories`
-- 

DROP TABLE IF EXISTS `blog_categories`;
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `blog_categories`
-- 

INSERT INTO `blog_categories` VALUES (1, 'nl', 'default', 'default');

-- --------------------------------------------------------

-- 
-- Table structure for table `blog_comments`
-- 

DROP TABLE IF EXISTS `blog_comments`;
CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `author` varchar(255) collate utf8_unicode_ci NOT NULL,
  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `website` varchar(255) collate utf8_unicode_ci default NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `type` enum('comment','trackback') collate utf8_unicode_ci NOT NULL default 'comment',
  `status` enum('published','moderation','spam') collate utf8_unicode_ci NOT NULL default 'moderation',
  `data` text collate utf8_unicode_ci COMMENT 'Serialized array with extra data',
  PRIMARY KEY  (`id`),
  KEY `idx_post_id_status` (`post_id`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62 ;

-- 
-- Dumping data for table `blog_comments`
-- 

INSERT INTO `blog_comments` VALUES (57, 2, '2010-02-14 13:58:33', 'Fork', 'no-reply@fork-cms.be', 'http://fork-cms.be', 'Dit is een voorbeeld van een reactie. Je kan deze beheren via het private-gedeelte.\r\n\r\nZoals je kan zien zorgen we ervoor dat de url die je bezoekers ingeven omgevormd worden naar klikbare links.\r\nBijvoorbeeld:\r\n- een volledige url zoals: http://www.fork-cms.be\r\n- of zonder http: www.fork-cms.be\r\n- zelf de www is niet verplicht: fork-cms.be', 'comment', 'published', 'a:1:{s:6:"server";a:35:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:79:"http://forkng.local/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform";s:11:"HTTP_COOKIE";s:229:"frontend_language=s%3A2%3A%22nl%22%3B; PHPSESSID=5cb6c1ceeba1b7054a66f5111e9c8fe0; backend_interface_language=s%3A2%3A%22nl%22%3B; comment_author=s%3A13%3A%22Tijs+Verkoyen%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"484";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"65053";s:12:"REDIRECT_URL";s:60:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:0:"";s:11:"REQUEST_URI";s:60:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1266155913;s:4:"argv";a:0:{}s:4:"argc";i:0;}}');
INSERT INTO `blog_comments` VALUES (58, 2, '2010-02-14 14:00:36', 'Fork', 'unmoderated@fork-cms.be', 'http://fork-cms.be', 'Als je moderatie hebt aanstaan, dan moet je bij de eerste reactie die een bezoeker heeft eerst je goedkeuring geven.\r\n\r\nZodra de auteur eenmaal goedkeuring heeft gekregen zal zijn reactie onmiddelijk gepubliceerd worden.', 'comment', 'moderation', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:98:"http://forkng.local/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform?comment=moderation";s:11:"HTTP_COOKIE";s:283:"frontend_language=s%3A2%3A%22nl%22%3B; PHPSESSID=5cb6c1ceeba1b7054a66f5111e9c8fe0; backend_interface_language=s%3A2%3A%22nl%22%3B; comment_author=s%3A4%3A%22Fork%22%3B; comment_email=s%3A20%3A%22no-reply%40fork-cms.be%22%3B; comment_website=s%3A18%3A%22http%3A%2F%2Ffork-cms.be%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"337";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"65077";s:21:"REDIRECT_QUERY_STRING";s:18:"comment=moderation";s:12:"REDIRECT_URL";s:60:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:18:"comment=moderation";s:11:"REQUEST_URI";s:79:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform?comment=moderation";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1266156036;s:4:"argv";a:1:{i:0;s:18:"comment=moderation";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (59, 2, '2010-02-14 14:03:36', 'buy Camel cigarettes', 'nwibqw@qtwvwj.com', 'http://www.getinvolvedwinstonsalem.org/camel.html', 'DwjlcLm &lt;a href=&quot;http://www.getinvolvedwinstonsalem.org/camel.html &quot;&gt;buy Camel cigarettes&lt;/a&gt; &lt;a href=&quot;http://www.kingsfeast.com/ &quot;&gt;Cialis&lt;/a&gt; &lt;a href=&quot;http://www.elysiumcache.com/ativan.html &quot;&gt;Ativan&lt;/a&gt; &lt;a href=&quot;http://www.getinvolvedwinstonsalem.org/ &quot;&gt;Cheap Cigarettes&lt;/a&gt; &lt;a href=&quot;http://www.elysiumcache.com/xanax.html &quot;&gt;Xanax&lt;/a&gt; ', 'comment', 'spam', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:98:"http://forkng.local/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform?comment=moderation";s:11:"HTTP_COOKIE";s:286:"frontend_language=s%3A2%3A%22nl%22%3B; PHPSESSID=5cb6c1ceeba1b7054a66f5111e9c8fe0; backend_interface_language=s%3A2%3A%22nl%22%3B; comment_author=s%3A4%3A%22Fork%22%3B; comment_email=s%3A23%3A%22unmoderated%40fork-cms.be%22%3B; comment_website=s%3A18%3A%22http%3A%2F%2Ffork-cms.be%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"607";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"65132";s:21:"REDIRECT_QUERY_STRING";s:18:"comment=moderation";s:12:"REDIRECT_URL";s:60:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:18:"comment=moderation";s:11:"REQUEST_URI";s:79:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform?comment=moderation";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1266156216;s:4:"argv";a:1:{i:0;s:18:"comment=moderation";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (61, 2, '2010-02-14 14:05:40', 'Tijs Verkoyen', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Als we nu eens testen met gravatar, zou dat niet fijn zijn?', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:98:"http://forkng.local/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform?comment=moderation";s:11:"HTTP_COOKIE";s:294:"frontend_language=s%3A2%3A%22nl%22%3B; PHPSESSID=5cb6c1ceeba1b7054a66f5111e9c8fe0; backend_interface_language=s%3A2%3A%22nl%22%3B; comment_author=s%3A13%3A%22Tijs+Verkoyen%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"177";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"65165";s:21:"REDIRECT_QUERY_STRING";s:18:"comment=moderation";s:12:"REDIRECT_URL";s:60:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:18:"comment=moderation";s:11:"REQUEST_URI";s:79:"/nl/blog/detail/de-allereerste-blogpost-op-het-fork-platform?comment=moderation";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1266156340;s:4:"argv";a:1:{i:0;s:18:"comment=moderation";}s:4:"argc";i:1;}}');

-- --------------------------------------------------------

-- 
-- Table structure for table `blog_posts`
-- 

DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL COMMENT 'The real post id',
  `revision_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `introduction` text collate utf8_unicode_ci,
  `text` text collate utf8_unicode_ci,
  `status` enum('active','archived','draft') collate utf8_unicode_ci NOT NULL,
  `publish_on` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `hidden` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `allow_comments` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `num_comments` int(11) NOT NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `blog_posts`
-- 

INSERT INTO `blog_posts` VALUES (1, 2, 1, 1, 548, 'nl', 'Dit is een voorbeeld van een kladversie', '', '<p>Een kladversie van een blogpost zal niet op de site te zien zijn. Dit zorgt ervoor dat je artikels kan voorbereiden.</p>\r\n<p>Zodra je kiest om de kladversie te publiceren zal deze niet meer beschikbaar zijn als kladversie maar als een gepubliceerd artikel.</p>', 'draft', '2010-02-14 13:43:00', '2010-02-14 13:43:40', '2010-02-14 13:43:40', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (2, 3, 1, 1, 551, 'nl', 'De allereerste blogpost op het Fork platform', '', '<p>Dit is je allerleerste blogpost op het <a href="http://fork-cms.be">Fork</a> platform. We willen je alvast bedanken om ons platform te gebruiken en we wensen je er veel plezier mee.</p>\r\n<p>Een blog is naast een krachtige SEO-tool ook de beste manier om je bezoekers op de hoogte te houden van je activiteiten.</p>\r\n<p>Vergeet alvast niet de instelling van je blog te controleren, zoals de naam van je RSS-feed en de omschrijving ervan! Je kan deze instellingen wijzigen via de knop "instellingen" (rechts bovenaan).</p>\r\n<p><strong>Veel plezier!</strong></p>', 'archived', '2010-02-14 13:43:00', '2010-02-14 13:49:48', '2010-02-14 13:49:48', 'N', 'N', 2);
INSERT INTO `blog_posts` VALUES (2, 4, 1, 1, 552, 'nl', 'De allereerste blogpost op het Fork platform', '', '<p>Dit is je allerleerste blogpost op het <a href="http://fork-cms.be">Fork</a> platform. We willen je alvast bedanken om ons platform te gebruiken en we wensen je er veel plezier mee.</p>\r\n<p>Een blog is naast een krachtige SEO-tool ook de beste manier om je bezoekers op de hoogte te houden van je activiteiten.</p>\r\n<p>Vergeet alvast niet de instelling van je blog te controleren, zoals de naam van je RSS-feed en de omschrijving ervan! Je kan deze instellingen wijzigen via de knop "instellingen" (rechts bovenaan).</p>\r\n<p><strong>Veel plezier!</strong></p>', 'archived', '2010-02-14 13:43:00', '2010-02-14 13:49:48', '2010-02-14 13:50:07', 'N', 'N', 2);
INSERT INTO `blog_posts` VALUES (2, 5, 1, 1, 553, 'nl', 'De allereerste blogpost op het Fork platform', '', '<p>Dit is je allerleerste blogpost op het <a href="http://fork-cms.be">Fork</a> platform. We willen je alvast bedanken om ons platform te gebruiken en we wensen je er veel plezier mee.</p>\r\n<p>Een blog is naast een krachtige SEO-tool ook de beste manier om je bezoekers op de hoogte te houden van je activiteiten.</p>\r\n<p>Vergeet alvast niet de instelling van je blog te controleren, zoals de naam van je RSS-feed en de omschrijving ervan! Je kan deze instellingen wijzigen via de knop "instellingen" (rechts bovenaan).</p>\r\n<p><strong>Veel plezier!</strong></p>', 'active', '2010-02-14 13:43:00', '2010-02-14 13:49:48', '2010-02-14 13:53:49', 'N', 'Y', 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `emails`
-- 

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(11) NOT NULL auto_increment,
  `to_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `to_name` varchar(255) collate utf8_unicode_ci default NULL,
  `from_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `from_name` varchar(255) collate utf8_unicode_ci default NULL,
  `reply_to_email` varchar(255) collate utf8_unicode_ci default NULL,
  `reply_to_name` varchar(255) collate utf8_unicode_ci default NULL,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `html` text collate utf8_unicode_ci NOT NULL,
  `plain_text` text collate utf8_unicode_ci NOT NULL,
  `send_on` datetime default NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

-- 
-- Dumping data for table `emails`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `parameters` text collate utf8_unicode_ci COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

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
  `module` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `action` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL default '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=56 ;

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
INSERT INTO `groups_rights_actions` VALUES (18, 1, 'contentblocks', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (19, 1, 'contentblocks', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES (20, 1, 'pages', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (21, 1, 'contentblocks', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (22, 1, 'settings', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (24, 1, 'blog', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (25, 1, 'contentblocks', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES (26, 1, 'blog', 'categories', 7);
INSERT INTO `groups_rights_actions` VALUES (27, 1, 'blog', 'comments', 7);
INSERT INTO `groups_rights_actions` VALUES (28, 1, 'blog', 'settings', 7);
INSERT INTO `groups_rights_actions` VALUES (29, 1, 'tags', 'autocomplete', 7);
INSERT INTO `groups_rights_actions` VALUES (30, 1, 'blog', 'add_post', 7);
INSERT INTO `groups_rights_actions` VALUES (31, 1, 'blog', 'edit_post', 7);
INSERT INTO `groups_rights_actions` VALUES (32, 1, 'pages', 'get_info', 7);
INSERT INTO `groups_rights_actions` VALUES (33, 1, 'pages', 'move', 7);
INSERT INTO `groups_rights_actions` VALUES (34, 1, 'pages', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES (35, 1, 'pages', 'save', 7);
INSERT INTO `groups_rights_actions` VALUES (36, 1, 'blog', 'mass_comment_action', 7);
INSERT INTO `groups_rights_actions` VALUES (38, 1, 'blog', 'add_category', 7);
INSERT INTO `groups_rights_actions` VALUES (39, 1, 'blog', 'edit_category', 7);
INSERT INTO `groups_rights_actions` VALUES (40, 1, 'blog', 'delete_category', 7);
INSERT INTO `groups_rights_actions` VALUES (41, 1, 'tags', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (42, 1, 'tags', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (43, 1, 'blog', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES (44, 1, 'blog', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (45, 1, 'locale', 'index', 7);
INSERT INTO `groups_rights_actions` VALUES (46, 1, 'locale', 'edit', 7);
INSERT INTO `groups_rights_actions` VALUES (47, 1, 'pages', 'templates', 7);
INSERT INTO `groups_rights_actions` VALUES (48, 1, 'pages', 'add_template', 7);
INSERT INTO `groups_rights_actions` VALUES (49, 1, 'pages', 'edit_template', 7);
INSERT INTO `groups_rights_actions` VALUES (50, 1, 'locale', 'add', 7);
INSERT INTO `groups_rights_actions` VALUES (51, 1, 'blog', 'mass_post_action', 7);
INSERT INTO `groups_rights_actions` VALUES (52, 1, 'blog', 'delete', 7);
INSERT INTO `groups_rights_actions` VALUES (53, 1, 'locale', 'mass_action', 7);
INSERT INTO `groups_rights_actions` VALUES (55, 1, 'blog', 'add_category', 7);

-- --------------------------------------------------------

-- 
-- Table structure for table `groups_rights_modules`
-- 

DROP TABLE IF EXISTS `groups_rights_modules`;
CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  PRIMARY KEY  (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

-- 
-- Dumping data for table `groups_rights_modules`
-- 

INSERT INTO `groups_rights_modules` VALUES (1, 1, 'dashboard');
INSERT INTO `groups_rights_modules` VALUES (3, 1, 'users');
INSERT INTO `groups_rights_modules` VALUES (6, 1, 'pages');
INSERT INTO `groups_rights_modules` VALUES (7, 1, 'contentblocks');
INSERT INTO `groups_rights_modules` VALUES (8, 1, 'settings');
INSERT INTO `groups_rights_modules` VALUES (9, 1, 'blog');
INSERT INTO `groups_rights_modules` VALUES (10, 1, 'tags');
INSERT INTO `groups_rights_modules` VALUES (11, 1, 'locale');

-- --------------------------------------------------------

-- 
-- Table structure for table `locale`
-- 

DROP TABLE IF EXISTS `locale`;
CREATE TABLE IF NOT EXISTS `locale` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `application` varchar(255) collate utf8_unicode_ci NOT NULL,
  `module` varchar(255) collate utf8_unicode_ci NOT NULL,
  `type` enum('act','err','lbl','msg') collate utf8_unicode_ci NOT NULL default 'lbl',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=424 ;

-- 
-- Dumping data for table `locale`
-- 

INSERT INTO `locale` VALUES (34, 1, 'nl', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key werd nog niet geconfigureerd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (33, 1, 'nl', 'backend', 'core', 'lbl', 'Settings', 'instellingen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (32, 1, 'nl', 'backend', 'core', 'lbl', 'Value', 'waarde', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (31, 1, 'nl', 'backend', 'core', 'lbl', 'Name', 'naam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (30, 1, 'nl', 'backend', 'core', 'lbl', 'Type', 'type', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (29, 1, 'nl', 'backend', 'core', 'lbl', 'Module', 'module', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (28, 1, 'nl', 'backend', 'core', 'lbl', 'Application', 'applicatie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (27, 1, 'nl', 'backend', 'core', 'lbl', 'Language', 'taal', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (26, 1, 'nl', 'backend', 'core', 'lbl', 'Edit', 'bewerken', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (25, 1, 'nl', 'frontend', 'core', 'lbl', 'YouAreHere', 'je bent hier', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (24, 1, 'nl', 'frontend', 'core', 'lbl', 'RecentComments', 'recente reacties', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (23, 1, 'nl', 'frontend', 'core', 'lbl', 'CommentedOn', 'reageerde op', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (22, 1, 'nl', 'frontend', 'core', 'act', 'Detail', 'detail', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (35, 1, 'nl', 'backend', 'core', 'lbl', 'Save', 'opslaan', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (36, 1, 'nl', 'backend', 'core', 'err', 'BlogRSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%s" class="button"><span><span><span>Configureer</span></span></span></a>', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (38, 1, 'nl', 'backend', 'core', 'lbl', 'AddLabel', 'label toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (39, 1, 'nl', 'backend', 'core', 'lbl', 'Active', 'actief', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (40, 1, 'nl', 'backend', 'core', 'lbl', 'ActiveUsers', 'actieve gebruikers', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (41, 1, 'nl', 'backend', 'core', 'lbl', 'Add', 'toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (42, 1, 'nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (43, 1, 'nl', 'backend', 'users', 'lbl', 'Add', 'gebruiker toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (44, 1, 'nl', 'backend', 'core', 'lbl', 'AddCategory', 'categorie toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (45, 1, 'nl', 'backend', 'core', 'lbl', 'AddTemplate', 'template toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (46, 1, 'nl', 'backend', 'core', 'lbl', 'All', 'alle', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (47, 1, 'nl', 'backend', 'blog', 'lbl', 'AllowComments', 'reacties toestaan', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (48, 1, 'nl', 'backend', 'blog', 'lbl', 'AllPosts', 'alle posts', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (49, 1, 'nl', 'backend', 'core', 'lbl', 'Amount', 'aantal', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (50, 1, 'nl', 'backend', 'core', 'lbl', 'APIKey', 'API key', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (51, 1, 'nl', 'backend', 'core', 'lbl', 'APIKeys', 'API keys', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (52, 1, 'nl', 'backend', 'core', 'lbl', 'APIURL', 'API URL', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (53, 1, 'nl', 'backend', 'core', 'lbl', 'Archived', 'gearchiveerd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (54, 1, 'nl', 'backend', 'core', 'lbl', 'At', 'om', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (55, 1, 'nl', 'backend', 'core', 'lbl', 'Avatar', 'avatar', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (56, 1, 'nl', 'backend', 'core', 'lbl', 'Author', 'auteur', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (57, 1, 'nl', 'backend', 'core', 'lbl', 'Back', 'terug', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (58, 1, 'nl', 'backend', 'core', 'lbl', 'Blog', 'blog', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (59, 1, 'nl', 'backend', 'core', 'lbl', 'By', 'door', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (60, 1, 'nl', 'backend', 'core', 'lbl', 'Cancel', 'annuleer', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (61, 1, 'nl', 'backend', 'core', 'lbl', 'Category', 'categorie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (62, 1, 'nl', 'backend', 'core', 'lbl', 'Categories', 'categorieën', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (63, 1, 'nl', 'backend', 'core', 'lbl', 'CheckCommentsForSpam', 'filter reacties op spam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (64, 1, 'nl', 'backend', 'core', 'lbl', 'Comment', 'reactie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (65, 1, 'nl', 'backend', 'core', 'lbl', 'Comments', 'reacties', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (66, 1, 'nl', 'backend', 'core', 'lbl', 'Content', 'inhoud', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (67, 1, 'nl', 'backend', 'pages', 'lbl', 'Core', 'algemeen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (68, 1, 'nl', 'backend', 'core', 'lbl', 'CustomURL', 'aangepaste URL', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (69, 1, 'nl', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (70, 1, 'nl', 'backend', 'core', 'lbl', 'Date', 'datum', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (71, 1, 'nl', 'backend', 'core', 'lbl', 'Default', 'standaard', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (72, 1, 'nl', 'backend', 'core', 'lbl', 'Delete', 'verwijderen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (73, 1, 'nl', 'backend', 'core', 'lbl', 'DeleteTag', 'verwijder deze tag', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (74, 1, 'nl', 'backend', 'core', 'lbl', 'Description', 'beschrijving', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (75, 1, 'nl', 'backend', 'core', 'lbl', 'Developer', 'developer', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (76, 1, 'nl', 'backend', 'core', 'lbl', 'Domains', 'domeinen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (77, 1, 'nl', 'backend', 'core', 'lbl', 'Draft', 'kladversie', '2010-02-14 11:56:30');
INSERT INTO `locale` VALUES (78, 1, 'nl', 'backend', 'core', 'lbl', 'Dutch', 'Nederlands', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (79, 1, 'nl', 'backend', 'core', 'lbl', 'English', 'engels', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (80, 1, 'nl', 'backend', 'core', 'lbl', 'Editor', 'editor', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (81, 1, 'nl', 'backend', 'core', 'lbl', 'EditTemplate', 'template bewerken', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (82, 1, 'nl', 'backend', 'core', 'lbl', 'Email', 'e-mail', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (83, 1, 'nl', 'backend', 'core', 'lbl', 'EmailWebmaster', 'e-mail webmaster', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (84, 1, 'nl', 'backend', 'core', 'lbl', 'Execute', 'uitvoeren', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (85, 1, 'nl', 'backend', 'core', 'lbl', 'Extra', 'extra', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (86, 1, 'nl', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (87, 1, 'nl', 'backend', 'core', 'lbl', 'Footer', 'footer', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (88, 1, 'nl', 'backend', 'core', 'lbl', 'French', 'frans', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (89, 1, 'nl', 'backend', 'core', 'lbl', 'Hidden', 'verborgen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (90, 1, 'nl', 'backend', 'core', 'lbl', 'Home', 'home', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (91, 1, 'nl', 'backend', 'core', 'lbl', 'InterfaceLanguage', 'interface-taal', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (92, 1, 'nl', 'backend', 'core', 'lbl', 'Keywords', 'zoekwoorden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (93, 1, 'nl', 'backend', 'core', 'lbl', 'Languages', 'talen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (94, 1, 'nl', 'backend', 'core', 'lbl', 'LastEditedOn', 'laatst aangepast op', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (95, 1, 'nl', 'backend', 'core', 'lbl', 'LastSave', 'laatst bewaard', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (96, 1, 'nl', 'backend', 'core', 'lbl', 'Loading', 'laden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (97, 1, 'nl', 'backend', 'core', 'lbl', 'Login', 'login', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (98, 1, 'nl', 'backend', 'core', 'lbl', 'Logout', 'afmelden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (99, 1, 'nl', 'backend', 'core', 'lbl', 'MainNavigation', 'hoofdnavigatie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (100, 1, 'nl', 'backend', 'pages', 'lbl', 'Meta', 'meta-navigatie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (101, 1, 'nl', 'backend', 'core', 'lbl', 'MetaCustom', 'meta custom', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (102, 1, 'nl', 'backend', 'core', 'lbl', 'MetaDescription', 'meta-omschrijving', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (103, 1, 'nl', 'backend', 'core', 'lbl', 'MetaInformation', 'meta-informatie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (104, 1, 'nl', 'backend', 'core', 'lbl', 'MetaKeywords', 'sleutelwoorden pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (105, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToModeration', 'verplaats naar moderatie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (106, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToPublished', 'verplaats naar gepubliceerd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (107, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToSpam', 'verplaats naar spam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (108, 1, 'nl', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigatie titel', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (109, 1, 'nl', 'backend', 'core', 'lbl', 'NewPassword', 'nieuw wachtwoord', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (110, 1, 'nl', 'backend', 'core', 'lbl', 'News', 'nieuws', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (111, 1, 'nl', 'backend', 'core', 'lbl', 'Next', 'volgende', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (112, 1, 'nl', 'backend', 'core', 'lbl', 'NextPage', 'volgende pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (113, 1, 'nl', 'backend', 'core', 'lbl', 'Nickname', 'nickname', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (114, 1, 'nl', 'backend', 'core', 'lbl', 'OK', 'ok', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (115, 1, 'nl', 'backend', 'core', 'lbl', 'Page', 'pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (116, 1, 'nl', 'backend', 'core', 'lbl', 'Pages', 'pagina''s', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (117, 1, 'nl', 'backend', 'core', 'lbl', 'Password', 'wachtwoord', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (118, 1, 'nl', 'backend', 'core', 'lbl', 'PageTitle', 'paginatitel', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (119, 1, 'nl', 'backend', 'core', 'lbl', 'Permissions', 'rechten', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (120, 1, 'nl', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (121, 1, 'nl', 'backend', 'blog', 'lbl', 'Posts', 'posts', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (122, 1, 'nl', 'backend', 'core', 'lbl', 'PostsInThisCategory', 'posts in deze categorie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (123, 1, 'nl', 'backend', 'core', 'lbl', 'Preview', 'preview', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (124, 1, 'nl', 'backend', 'core', 'lbl', 'Previous', 'vorige', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (125, 1, 'nl', 'backend', 'core', 'lbl', 'PreviousPage', 'vorige pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (126, 1, 'nl', 'backend', 'core', 'lbl', 'Publish', 'publiceer', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (127, 1, 'nl', 'backend', 'core', 'lbl', 'PublishOn', 'publiceer op', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (128, 1, 'nl', 'backend', 'core', 'lbl', 'Published', 'gepubliceerd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (129, 1, 'nl', 'backend', 'core', 'lbl', 'PublishedOn', 'gepubliceerd op', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (130, 1, 'nl', 'backend', 'core', 'lbl', 'PublishedComments', 'gepubliceerde reacties', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (131, 1, 'nl', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recent aangepast', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (132, 1, 'nl', 'backend', 'core', 'lbl', 'Referrers', 'referrers', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (133, 1, 'nl', 'backend', 'core', 'lbl', 'RepeatPassword', 'herhaal wachtwoord', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (134, 1, 'nl', 'backend', 'core', 'lbl', 'RequiredField', 'verplicht veld', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (135, 1, 'nl', 'backend', 'core', 'lbl', 'Revisions', 'versies', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (136, 1, 'nl', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (137, 1, 'nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina''s', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (138, 1, 'nl', 'backend', 'core', 'lbl', 'Scripts', 'scripts', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (139, 1, 'nl', 'backend', 'core', 'lbl', 'Send', 'verzenden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (140, 1, 'nl', 'backend', 'core', 'lbl', 'Security', 'beveiliging', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (141, 1, 'nl', 'backend', 'core', 'lbl', 'SEO', 'SEO', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (142, 1, 'nl', 'backend', 'core', 'lbl', 'SignIn', 'aanmelden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (143, 1, 'nl', 'backend', 'core', 'lbl', 'SignOut', 'afmelden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (144, 1, 'nl', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (145, 1, 'nl', 'backend', 'core', 'lbl', 'SortAscending', 'sorteerd oplopend', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (146, 1, 'nl', 'backend', 'core', 'lbl', 'SortDescending', 'sorteer aflopend', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (147, 1, 'nl', 'backend', 'core', 'lbl', 'SortedAscending', 'oplopend gesorteerd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (148, 1, 'nl', 'backend', 'core', 'lbl', 'SortedDescending', 'aflopend gesorteerd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (149, 1, 'nl', 'backend', 'core', 'lbl', 'Snippets', 'contentblocks', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (150, 1, 'nl', 'backend', 'core', 'lbl', 'Spam', 'spam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (151, 1, 'nl', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (152, 1, 'nl', 'backend', 'core', 'lbl', 'Status', 'status', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (153, 1, 'nl', 'backend', 'core', 'lbl', 'Submit', 'verzenden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (154, 1, 'nl', 'backend', 'core', 'lbl', 'Surname', 'achternaam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (155, 1, 'nl', 'backend', 'core', 'lbl', 'Tag', 'tag', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (156, 1, 'nl', 'backend', 'core', 'lbl', 'Tags', 'tags', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (157, 1, 'nl', 'backend', 'core', 'lbl', 'Template', 'template', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (158, 1, 'nl', 'backend', 'core', 'lbl', 'Time', 'tijd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (159, 1, 'nl', 'backend', 'core', 'lbl', 'Title', 'titel', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (160, 1, 'nl', 'backend', 'core', 'lbl', 'Titles', 'titels', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (161, 1, 'nl', 'backend', 'core', 'lbl', 'Update', 'wijzig', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (162, 1, 'nl', 'backend', 'core', 'lbl', 'URL', 'url', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (163, 1, 'nl', 'backend', 'core', 'lbl', 'Userguide', 'gebruikersgids', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (164, 1, 'nl', 'backend', 'core', 'lbl', 'Username', 'gebruikersnaam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (165, 1, 'nl', 'backend', 'core', 'lbl', 'User', 'gebruiker', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (166, 1, 'nl', 'backend', 'core', 'lbl', 'Users', 'gebruikers', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (167, 1, 'nl', 'backend', 'core', 'lbl', 'UseThisVersion', 'gebruik deze versie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (168, 1, 'nl', 'backend', 'core', 'lbl', 'Versions', 'versies', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (169, 1, 'nl', 'backend', 'core', 'lbl', 'Visible', 'zichtbaar', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (170, 1, 'nl', 'backend', 'core', 'lbl', 'WaitingForModeration', 'wachten op moderatie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (171, 1, 'nl', 'backend', 'core', 'lbl', 'Websites', 'websites', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (172, 1, 'nl', 'backend', 'core', 'lbl', 'WebsiteTitle', 'website titel', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (173, 1, 'nl', 'backend', 'core', 'lbl', 'WithSelected', 'met geselecteerde', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (174, 1, 'nl', 'backend', 'pages', 'msg', 'HelpAdd', '&larr; Kies een pagina uit de boomstructuur om deze te bewerken of', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (175, 1, 'nl', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activeer <code>rel="nofollow"</code>', '2010-02-02 09:49:53');
INSERT INTO `locale` VALUES (176, 1, 'nl', 'backend', 'core', 'msg', 'Added', 'item toegevoegd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (177, 1, 'nl', 'backend', 'users', 'msg', 'Added', 'Gebruiker ''%s'' toegevoegd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (178, 1, 'nl', 'backend', 'settings', 'msg', 'ApiKeysText', 'Toegangscodes voor gebruikte webservices', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (179, 1, 'nl', 'backend', 'core', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (180, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmDelete', 'Ben je zeker dat je dit item wil verwijderen?', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (181, 1, 'nl', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de pagina ''%s'' wil verwijderen?', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (182, 1, 'nl', 'backend', 'users', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de gebruiker ''%s'' wil verwijderen?', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (183, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Ben je zeker dat je de categorie ''%s'' wil verwijderen?', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (184, 1, 'nl', 'backend', 'core', 'msg', 'Deleted', 'Het item is verwijderd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (185, 1, 'nl', 'backend', 'users', 'msg', 'Deleted', 'De gebruiker ''%s'' is verwijderd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (186, 1, 'nl', 'backend', 'settings', 'msg', 'DomainsText', 'Vul de domeinen in waarop de website te bereiken is (1 domein per regel)', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (187, 1, 'nl', 'backend', 'core', 'msg', 'Edited', 'Wijzigingen opgeslagen.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (188, 1, 'nl', 'backend', 'users', 'msg', 'Edited', 'Wijzigingen voor gebruiker ''%s'' opgeslagen.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (189, 1, 'nl', 'backend', 'core', 'msg', 'ForgotPassword', 'Wachtwoord vergeten?', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (190, 1, 'nl', 'backend', 'core', 'msg', 'ForgotPasswordHelp', 'Vul hieronder je e-mail adres in om een nieuw wachtwoord toegestuurd te krijgen.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (191, 1, 'nl', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Stuur notificaties van het CMS naar dit e-mailadres.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (192, 1, 'nl', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'bijv. http://feeds.feedburner.com/netlog', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (193, 1, 'nl', 'backend', 'pages', 'msg', 'HelpPageTitle', 'De titel die in het venster van de browser staat (<code>&lt;title&gt;</code>).', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (194, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'Als de paginatitel te lang is om in het menu te passen, geef dan een verkorte titel in.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (195, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Zorgt ervoor dat deze pagina de interne PageRank niet be', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (196, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaCustom', 'Laat toe om extra, op maat gemaakte metatags toe te voegen.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (197, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaDescription', 'De pagina-omschrijving die wordt getoond in de resultaten van zoekmachines. Hou het kort en krachtig.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (198, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'De sleutelwoorden (keywords) die deze pagina omschrijven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (199, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaURL', 'Vervang de automatisch gegenereerde URL door een zelfgekozen URL.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (200, 1, 'nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (201, 1, 'nl', 'backend', 'core', 'msg', 'HelpRevisions', 'De laatst opgeslagen versies worden hier bijgehouden. ''Gebruik deze versie'' opent een vroegere versie. De huidige versie wordt pas overschreven als je opslaat.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (202, 1, 'nl', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Beschrijf bondig wat voor soort inhoud de RSS-feed zal bevatten', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (203, 1, 'nl', 'backend', 'core', 'lbl', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (204, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (205, 1, 'nl', 'backend', 'settings', 'msg', 'LanguagesText', 'Duid aan welke talen toegankelijk zijn voor bezoekers', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (206, 1, 'nl', 'backend', 'core', 'msg', 'LoginFormHelp', 'Vul uw gebruikersnaam en wachtwoord in om u aan te melden.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (207, 1, 'nl', 'backend', 'core', 'lbl', 'LoggedInAs', 'aangemeld als', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (208, 1, 'nl', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (209, 1, 'nl', 'backend', 'pages', 'msg', 'ModuleAttached', 'A module is attached. Go to <a href="{url}">{name}</a> to manage.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (210, 1, 'nl', 'backend', 'core', 'msg', 'NoDrafts', 'Er zijn geen kladversies.', '2010-02-14 11:56:51');
INSERT INTO `locale` VALUES (211, 1, 'nl', 'backend', 'core', 'msg', 'NoItems', 'Er zijn geen items aanwezig.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (212, 1, 'nl', 'backend', 'core', 'msg', 'NoItemsPublished', 'Er zijn geen items gepubliceerd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (213, 1, 'nl', 'backend', 'core', 'msg', 'NoItemsScheduled', 'Er zijn geen items gepland.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (214, 1, 'nl', 'backend', 'core', 'msg', 'NoRevisions', 'Er zijn nog geen versies.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (215, 1, 'nl', 'backend', 'core', 'msg', 'NoTags', 'Je hebt nog geen tags ingegeven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (216, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedActionTitle', 'Verboden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (217, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedActionMessage', 'Deze actie is niet toegestaan.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (218, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedModuleTitle', 'Verboden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (219, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedModuleMessage', 'Deze module is niet toegestaan.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (220, 1, 'nl', 'backend', 'core', 'msg', 'ResetPasswordAndSignIn', 'Resetten en aanmelden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (221, 1, 'nl', 'backend', 'core', 'msg', 'ResetPasswordFormHelp', 'Vul je gewenste, nieuwe wachtwoord in.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (222, 1, 'nl', 'backend', 'core', 'msg', 'Saved', 'De wijzigingen zijn opgeslagen.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (223, 1, 'nl', 'backend', 'settings', 'msg', 'ScriptsText', 'Plaats hier code die op elke pagina geladen moet worden. (bvb. Google Analytics).', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (224, 1, 'nl', 'backend', 'core', 'msg', 'SequenceChanged', 'De volgorde is aangepast.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (225, 1, 'nl', 'backend', 'core', 'msg', 'UsingARevision', 'Je gebruikt een oudere versie!', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (226, 1, 'nl', 'backend', 'core', 'msg', 'VisibleOnSite', 'Zichtbaar op de website?', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (227, 1, 'nl', 'backend', 'pages', 'msg', 'WidgetAttached', 'A widget is attached.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (228, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneDayAgo', '1 dag geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (229, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneHourAgo', '1 uur geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (230, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneMinuteAgo', '1 minuut geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (231, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneMonthAgo', '1 maand geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (232, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneSecondAgo', '1 second geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (233, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneWeekAgo', '1 week geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (234, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneYearAgo', '1 jaar geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (235, 1, 'nl', 'backend', 'core', 'msg', 'TimeDaysAgo', '%s dagen geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (236, 1, 'nl', 'backend', 'core', 'msg', 'TimeMinutesAgo', '%s minuten geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (237, 1, 'nl', 'backend', 'core', 'msg', 'TimeHoursAgo', '%s uren geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (238, 1, 'nl', 'backend', 'core', 'msg', 'TimeMonthsAgo', '%s maanden geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (239, 1, 'nl', 'backend', 'core', 'msg', 'TimeSecondsAgo', '%s seconden geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (240, 1, 'nl', 'backend', 'core', 'msg', 'TimeWeeksAgo', '%s weken geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (241, 1, 'nl', 'backend', 'core', 'msg', 'TimeYearsAgo', '%s jaren geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (242, 1, 'nl', 'backend', 'core', 'err', 'BlogRSSTitle', 'Blog RSS titel is nog niet geconfigureerd. <a href="%s" class="button"><span><span><span>Configureer</span></span></span></a>', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (243, 1, 'nl', 'backend', 'core', 'err', 'ContentIsRequired', 'Gelieve inhoud in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (244, 1, 'nl', 'backend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (245, 1, 'nl', 'backend', 'core', 'err', 'EmailIsUnknown', 'Dit emailadres werd niet teruggevonden.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (246, 1, 'nl', 'backend', 'core', 'err', 'FieldIsRequired', 'Dit veld is verplicht.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (247, 1, 'nl', 'backend', 'core', 'err', 'GeneralFormError', 'Er ging iets mis. Kijk de gemarkeerde velden na.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (248, 1, 'nl', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key werd nog niet geconfigureerd.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (249, 1, 'nl', 'backend', 'core', 'err', 'InvalidAPIKey', 'Ongeldige API key.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (250, 1, 'nl', 'backend', 'core', 'err', 'InvalidDomain', 'Gelieve enkel domeinen in te vullen zonder http en www. vb netlash.com', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (251, 1, 'nl', 'backend', 'core', 'err', 'InvalidParameters', 'Ongeldige parameters.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (252, 1, 'nl', 'backend', 'core', 'err', 'InvalidURL', 'Ongeldige URL.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (253, 1, 'nl', 'backend', 'core', 'err', 'InvalidUsernamePasswordCombination', 'De combinatie van gebruikersnaam en wachtwoord is niet correct. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Bent u uw wachtwoord vergeten?</a>', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (254, 1, 'nl', 'backend', 'core', 'err', 'MinimumDimensions', 'Het gekozen bestand moet minimum %s<abbr title="pixels">px</abbr> breed en %s<abbr title="pixels">px</abbr> hoog zijn.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (255, 1, 'nl', 'backend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (256, 1, 'nl', 'backend', 'core', 'err', 'NonExisting', 'Dit item bestaat niet.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (257, 1, 'nl', 'backend', 'users', 'err', 'NonExisting', 'De gebruiker bestaat niet.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (258, 1, 'nl', 'backend', 'core', 'err', 'OnlyJPGAndGifAreAllowed', 'Enkel jpg, jpeg en gif zijn toegelaten.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (259, 1, 'nl', 'backend', 'core', 'err', 'PasswordIsRequired', 'Gelieve een wachtwoord in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (260, 1, 'nl', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Gelieve het gewenste wachtwoord te herhalen.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (261, 1, 'nl', 'backend', 'core', 'err', 'PasswordsDoNotMatch', 'De wachtwoorden zijn verschillend, probeer het opnieuw.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (262, 1, 'nl', 'backend', 'core', 'err', 'SomethingWentWrong', 'Er ging iets mis. Probeer later opnieuw.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (263, 1, 'nl', 'backend', 'core', 'err', 'SurnameIsRequired', 'Gelieve een achternaam in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (264, 1, 'nl', 'backend', 'core', 'err', 'TitleIsRequired', 'Gelieve een titel in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (265, 1, 'nl', 'backend', 'core', 'err', 'UsernameIsRequired', 'Gelieve een gebruikersnaam in te geven.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (266, 1, 'nl', 'backend', 'core', 'err', 'UsernameNotAllowed', 'Deze gebruikersnaam is niet toegestaan.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (267, 1, 'nl', 'backend', 'core', 'err', 'URLAlreadyExist', 'Deze URL bestaat al.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (268, 1, 'nl', 'frontend', 'core', 'msg', 'YouAreHere', 'Je bent hier', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (269, 1, 'nl', 'frontend', 'core', 'lbl', 'Required', 'verplicht', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (270, 1, 'nl', 'frontend', 'core', 'msg', 'WroteBy', 'geschreven door %s', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (271, 1, 'nl', 'frontend', 'core', 'lbl', 'Comment', 'reactie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (272, 1, 'nl', 'frontend', 'core', 'lbl', 'Category', 'categorie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (273, 1, 'nl', 'frontend', 'core', 'act', 'Comments', 'reacties', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (274, 1, 'nl', 'frontend', 'core', 'lbl', 'Comments', 'reacties', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (275, 1, 'nl', 'frontend', 'core', 'act', 'Comment', 'reactie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (276, 1, 'nl', 'frontend', 'core', 'lbl', 'By', 'door', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (277, 1, 'nl', 'frontend', 'core', 'act', 'React', 'reageer', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (278, 1, 'nl', 'frontend', 'core', 'lbl', 'React', 'reageer', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (279, 1, 'nl', 'frontend', 'core', 'lbl', 'Name', 'naam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (280, 1, 'nl', 'frontend', 'core', 'lbl', 'Email', 'e-mailadres', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (281, 1, 'nl', 'frontend', 'core', 'lbl', 'Website', 'website', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (282, 1, 'nl', 'frontend', 'core', 'lbl', 'Message', 'bericht', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (283, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneDayAgo', '1 dag geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (284, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneHourAgo', '1 uur geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (285, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneMinuteAgo', '1 minuut geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (286, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneMonthAgo', '1 maand geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (287, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneSecondAgo', '1 seconde geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (288, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneWeekAgo', '1 week geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (289, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneYearAgo', '1 jaar geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (290, 1, 'nl', 'frontend', 'core', 'msg', 'TimeDaysAgo', '%s dagen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (291, 1, 'nl', 'frontend', 'core', 'msg', 'TimeMinutesAgo', '%s minuten geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (292, 1, 'nl', 'frontend', 'core', 'msg', 'TimeHoursAgo', '%s uren geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (293, 1, 'nl', 'frontend', 'core', 'msg', 'TimeSecondsAgo', '%s seconden geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (294, 1, 'nl', 'frontend', 'core', 'msg', 'TimeWeeksAgo', '%s weken geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (295, 1, 'nl', 'frontend', 'core', 'msg', 'TimeYearAgo', '%s jaren geleden', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (296, 1, 'nl', 'frontend', 'core', 'lbl', 'Tags', 'tags', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (297, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (298, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %s reacties', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (299, 1, 'nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (300, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (301, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.', '2010-02-08 12:41:59');
INSERT INTO `locale` VALUES (302, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (303, 1, 'nl', 'frontend', 'core', 'msg', 'CommentedOn', 'reageerde op', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (306, 1, 'nl', 'frontend', 'core', 'lbl', 'PreviousPage', 'vorige pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (307, 1, 'nl', 'frontend', 'core', 'lbl', 'NextPage', 'volgende pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (308, 1, 'nl', 'frontend', 'core', 'lbl', 'GoToPage', 'ga naar pagina', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (309, 1, 'nl', 'backend', 'blog', 'msg', 'HeaderAdd', 'post toevoegen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (310, 1, 'nl', 'frontend', 'core', 'act', 'Category', 'categorie', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (312, 1, 'nl', 'frontend', 'core', 'act', 'Rss', 'rss', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (313, 1, 'nl', 'backend', 'blog', 'msg', 'HeaderEdit', 'post bewerken', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (314, 1, 'nl', 'frontend', 'core', 'lbl', 'In', 'in', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (315, 1, 'nl', 'frontend', 'core', 'lbl', 'Date', 'datum', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (316, 1, 'nl', 'frontend', 'core', 'lbl', 'Title', 'titel', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (317, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPassword', 'Wijzig je wachtwoord', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (384, 1, 'nl', 'backend', 'core', 'lbl', 'Locale', 'Vertalingen', '2010-02-08 14:32:47');
INSERT INTO `locale` VALUES (320, 1, 'nl', 'backend', 'core', 'msg', 'EditWithItem', 'Bewerk &quot;%s&quot;', '2010-02-10 13:30:35');
INSERT INTO `locale` VALUES (321, 1, 'nl', 'backend', 'core', 'msg', 'EditCategoryWithItem', 'Bewerk categorie &quot;%s&quot;', '2010-02-10 13:30:22');
INSERT INTO `locale` VALUES (322, 1, 'nl', 'backend', 'core', 'lbl', 'General', 'algemeen', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (323, 1, 'nl', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (324, 1, 'nl', 'backend', 'core', 'lbl', 'Summary', 'samenvatting', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (325, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Als je een samenvatting ingeeft, dan zal deze verschijnen in de overzichtspagina''s. Indien niet dan zal de volledige post getoond worden', '2010-02-02 13:26:16');
INSERT INTO `locale` VALUES (326, 1, 'nl', 'backend', 'core', 'lbl', 'Posts', 'artikels', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (327, 1, 'nl', 'backend', 'core', 'lbl', 'Modules', 'modules', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (328, 1, 'nl', 'backend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.', '2010-02-02 10:09:17');
INSERT INTO `locale` VALUES (383, 1, 'nl', 'backend', 'core', 'lbl', 'Statistics', 'statistieken', '2010-02-08 14:31:38');
INSERT INTO `locale` VALUES (341, 1, 'nl', 'backend', 'core', 'lbl', 'AccountManagement', 'account beheer', '2010-02-10 13:39:11');
INSERT INTO `locale` VALUES (331, 1, 'nl', 'backend', 'core', 'lbl', 'Credentials', 'login gegevens', '2010-02-02 12:54:52');
INSERT INTO `locale` VALUES (332, 1, 'nl', 'backend', 'core', 'msg', 'HelpStrongPassword', 'Sterke wachtwoorden bestaan uit een combinatie van hoofdletters, kleine letters, cijfers en speciale karakters.', '2010-02-08 17:01:43');
INSERT INTO `locale` VALUES (333, 1, 'nl', 'backend', 'core', 'lbl', 'None', 'geen', '2010-02-02 12:56:01');
INSERT INTO `locale` VALUES (334, 1, 'nl', 'backend', 'core', 'lbl', 'Weak', 'zwak', '2010-02-02 12:56:13');
INSERT INTO `locale` VALUES (335, 1, 'nl', 'backend', 'core', 'lbl', 'Strong', 'sterk', '2010-02-02 12:56:22');
INSERT INTO `locale` VALUES (336, 1, 'nl', 'backend', 'core', 'lbl', 'ConfirmPassword', 'bevestig wachtwoord', '2010-02-02 12:56:56');
INSERT INTO `locale` VALUES (337, 1, 'nl', 'backend', 'core', 'lbl', 'PersonalInformation', 'persoonlijke gegevens', '2010-02-02 12:57:15');
INSERT INTO `locale` VALUES (338, 1, 'nl', 'backend', 'users', 'msg', 'HelpNickname', 'Max. 20 characters. Your nickname will be shown throughout the CMS e.g. this name will show up when you are the author of an item.', '2010-02-02 12:57:46');
INSERT INTO `locale` VALUES (339, 1, 'nl', 'backend', 'users', 'msg', 'HelpAvatar', 'A square picture of your face works best.', '2010-02-02 12:59:04');
INSERT INTO `locale` VALUES (340, 1, 'nl', 'backend', 'core', 'lbl', 'InterfacePreferences', 'interface voorkeuren', '2010-02-02 12:59:45');
INSERT INTO `locale` VALUES (342, 1, 'nl', 'backend', 'users', 'msg', 'EnableUser', 'Geef deze account toegang tot het CMS.', '2010-02-02 13:09:54');
INSERT INTO `locale` VALUES (343, 1, 'nl', 'backend', 'core', 'lbl', 'Group', 'groep', '2010-02-02 13:10:04');
INSERT INTO `locale` VALUES (344, 1, 'nl', 'backend', 'blog', 'msg', 'HarHar', 'test post test', '2010-02-02 13:26:10');
INSERT INTO `locale` VALUES (345, 1, 'nl', 'backend', 'core', 'lbl', 'NL', 'nederlands', '2010-02-02 16:37:15');
INSERT INTO `locale` VALUES (346, 1, 'nl', 'backend', 'core', 'lbl', 'FR', 'frans', '2010-02-02 16:37:37');
INSERT INTO `locale` VALUES (347, 1, 'nl', 'backend', 'core', 'lbl', 'EN', 'engels', '2010-02-02 16:37:54');
INSERT INTO `locale` VALUES (348, 1, 'nl', 'backend', 'core', 'msg', 'ResetSuccess', 'Je wachtwoord werd gewijzigd.', '2010-02-03 09:34:15');
INSERT INTO `locale` VALUES (349, 1, 'nl', 'backend', 'pages', 'err', 'CantBeMoved', 'Pagina kan niet verplaats worden.', '2010-02-03 09:40:44');
INSERT INTO `locale` VALUES (350, 1, 'nl', 'backend', 'pages', 'msg', 'PageIsMoved', 'De pagina werd verplaatst.', '2010-02-03 09:41:10');
INSERT INTO `locale` VALUES (351, 1, 'nl', 'backend', 'core', 'lbl', 'CategoryName', 'categorie naam', '2010-02-03 19:03:50');
INSERT INTO `locale` VALUES (352, 1, 'nl', 'backend', 'core', 'err', 'AddingCategoryFailed', 'Er ging iets mis', '2010-02-03 19:19:48');
INSERT INTO `locale` VALUES (353, 1, 'nl', 'backend', 'core', 'err', 'InvalidName', 'Ongeldige naam', '2010-02-03 20:25:00');
INSERT INTO `locale` VALUES (354, 1, 'nl', 'backend', 'locale', 'err', 'AlreadyExists', 'Al aanwezig in de database.', '2010-02-03 20:26:10');
INSERT INTO `locale` VALUES (355, 1, 'nl', 'backend', 'locale', 'err', 'InvalidValue', 'Ongeldige waarde.', '2010-02-03 20:27:31');
INSERT INTO `locale` VALUES (356, 1, 'nl', 'backend', 'core', 'msg', 'BlogLatestComments', 'Laatste blog reacties', '2010-02-04 12:26:27');
INSERT INTO `locale` VALUES (357, 1, 'nl', 'backend', 'core', 'lbl', 'AllComments', 'alle reacties', '2010-02-04 12:29:01');
INSERT INTO `locale` VALUES (358, 1, 'nl', 'backend', 'core', 'msg', 'NoComments', 'Er zijn nog geen reacties.', '2010-02-04 12:29:58');
INSERT INTO `locale` VALUES (359, 1, 'nl', 'backend', 'core', 'msg', 'BlogCommentsToModerate', '%s reactie(s) te modereren.', '2010-02-04 15:27:05');
INSERT INTO `locale` VALUES (360, 1, 'nl', 'backend', 'core', 'lbl', 'Moderate', 'modereer', '2010-02-04 15:28:54');
INSERT INTO `locale` VALUES (361, 1, 'nl', 'backend', 'core', 'lbl', 'Analyse', 'analyse', '2010-02-06 19:46:41');
INSERT INTO `locale` VALUES (362, 1, 'nl', 'backend', 'core', 'err', 'ForkAPIKeys', 'Fork API-keys nog niet geconfigureerd.', '2010-02-06 19:47:46');
INSERT INTO `locale` VALUES (363, 1, 'nl', 'backend', 'core', 'err', 'DebugModeIsActive', 'Debug-mode is nog actief.', '2010-02-06 19:48:29');
INSERT INTO `locale` VALUES (364, 1, 'nl', 'backend', 'core', 'err', 'RobotsFileIsNotOK', 'robots.txt is niet correct.', '2010-02-06 19:49:05');
INSERT INTO `locale` VALUES (365, 1, 'nl', 'backend', 'core', 'lbl', 'Moderation', 'moderatie', '2010-02-06 19:54:24');
INSERT INTO `locale` VALUES (366, 1, 'nl', 'backend', 'core', 'lbl', 'AllowModeration', 'moderatie toestaan', '2010-02-06 19:55:03');
INSERT INTO `locale` VALUES (367, 1, 'nl', 'backend', 'core', 'lbl', 'Filter', 'filter', '2010-02-08 19:49:17');
INSERT INTO `locale` VALUES (368, 1, 'nl', 'backend', 'pages', 'lbl', 'RecentComments', 'recente reacties', '2010-02-08 07:33:27');
INSERT INTO `locale` VALUES (369, 1, 'nl', 'backend', 'core', 'lbl', 'ContactForm', 'contactformulier', '2010-02-08 07:34:04');
INSERT INTO `locale` VALUES (370, 1, 'nl', 'backend', 'core', 'lbl', 'Contact', 'contact', '2010-02-08 07:34:57');
INSERT INTO `locale` VALUES (371, 1, 'nl', 'backend', 'core', 'lbl', 'Translations', 'vertalingen', '2010-02-08 09:13:36');
INSERT INTO `locale` VALUES (372, 1, 'nl', 'backend', 'core', 'msg', 'AddPage', 'Voeg een pagina toe', '2010-02-08 12:21:25');
INSERT INTO `locale` VALUES (382, 1, 'nl', 'backend', 'core', 'lbl', 'Overview', 'overzicht', '2010-02-08 13:48:33');
INSERT INTO `locale` VALUES (373, 1, 'nl', 'backend', 'core', 'lbl', 'View', 'bekijk', '2010-02-08 12:49:39');
INSERT INTO `locale` VALUES (374, 1, 'nl', 'backend', 'blog', 'msg', 'ModerationMoved', 'Reactie verplaatst naar moderatie.', '2010-02-08 12:56:40');
INSERT INTO `locale` VALUES (375, 1, 'nl', 'backend', 'blog', 'msg', 'ModerationMovedMultiple', 'Reacties verplaatst naar moderatie.', '2010-02-08 12:57:26');
INSERT INTO `locale` VALUES (376, 1, 'nl', 'backend', 'blog', 'msg', 'PublishedMoved', 'Reactie gepubliceerd', '2010-02-08 13:05:31');
INSERT INTO `locale` VALUES (377, 1, 'nl', 'backend', 'blog', 'msg', 'PublishedMovedMultiple', 'Reacties gepubliceerd.', '2010-02-08 13:09:56');
INSERT INTO `locale` VALUES (378, 1, 'nl', 'backend', 'blog', 'msg', 'SpamMoved', 'Reactie gemarkeerd als spam.', '2010-02-08 13:07:37');
INSERT INTO `locale` VALUES (379, 1, 'nl', 'backend', 'blog', 'msg', 'SpamMovedMultiple', 'Reacties gemarkeerd als spam.', '2010-02-08 13:07:25');
INSERT INTO `locale` VALUES (380, 1, 'nl', 'backend', 'blog', 'msg', 'DeleteMoved', 'De reactie werd verwijderd.', '2010-02-08 13:08:20');
INSERT INTO `locale` VALUES (381, 1, 'nl', 'backend', 'blog', 'msg', 'DeleteMovedMultiple', 'De reacties werden verwijderd.', '2010-02-08 13:09:01');
INSERT INTO `locale` VALUES (390, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAnApplication', 'kies een applicatie', '2010-02-08 14:54:27');
INSERT INTO `locale` VALUES (389, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseALanguage', 'kies een taal', '2010-02-08 14:53:03');
INSERT INTO `locale` VALUES (391, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAModule', 'kies een module', '2010-02-08 14:55:18');
INSERT INTO `locale` VALUES (392, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAType', 'kies een type', '2010-02-08 14:56:29');
INSERT INTO `locale` VALUES (393, 1, 'nl', 'backend', 'core', 'lbl', 'Search', 'zoeken', '2010-02-08 14:57:34');
INSERT INTO `locale` VALUES (394, 1, 'nl', 'backend', 'locale', 'err', 'ModuleHasToBeCore', 'De module moet core zijn voor vertalingen in de frontend.', '2010-02-08 16:18:21');
INSERT INTO `locale` VALUES (395, 1, 'nl', 'backend', 'core', 'err', 'NoItemsSelected', 'Geen items geselecteerd.', '2010-02-08 17:05:24');
INSERT INTO `locale` VALUES (396, 1, 'nl', 'backend', 'core', 'msg', 'CategoryAdded', 'De categorie werd toegevoegd.', '2010-02-08 17:15:05');
INSERT INTO `locale` VALUES (397, 1, 'nl', 'backend', 'core', 'msg', 'UsingADraft', 'Je gebruikt een kladversie.', '2010-02-14 11:57:15');
INSERT INTO `locale` VALUES (398, 1, 'nl', 'backend', 'core', 'lbl', 'Drafts', 'kladversies', '2010-02-14 11:56:41');
INSERT INTO `locale` VALUES (399, 1, 'nl', 'backend', 'core', 'msg', 'HelpDrafts', 'Hier kan je jouw kladversie zien. Dit zijn tijdelijke versies.', '2010-02-14 11:57:37');
INSERT INTO `locale` VALUES (400, 1, 'nl', 'backend', 'core', 'msg', 'SavedAsDraft', '%s als kladversie opgeslagen.', '2010-02-14 11:57:02');
INSERT INTO `locale` VALUES (401, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailSubject', 'Wijzig je wachtwoord', '2010-02-10 10:43:55');
INSERT INTO `locale` VALUES (402, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailContent', 'Reset je wachtwoord door op de link hieronder te klikken. Indien je niet hier niet om gevraagd hebt hoef je geen actie te ondernemen.', '2010-02-10 10:44:25');
INSERT INTO `locale` VALUES (403, 1, 'nl', 'backend', 'locale', 'lbl', 'AddTranslation', 'vertaling toevoegen', '2010-02-10 13:02:19');
INSERT INTO `locale` VALUES (404, 1, 'nl', 'backend', 'locale', 'lbl', 'EditTranslation', 'Vertaling bewerken', '2010-02-10 13:03:21');
INSERT INTO `locale` VALUES (405, 1, 'nl', 'backend', 'tags', 'lbl', 'EditTag', 'Tag bewerken', '2010-02-10 13:29:30');
INSERT INTO `locale` VALUES (406, 1, 'nl', 'backend', 'locale', 'msg', 'ValueHelpTxt', 'De vertaling zelf, bvb. "toevoegen".', '2010-02-10 13:42:05');
INSERT INTO `locale` VALUES (407, 1, 'nl', 'backend', 'locale', 'msg', 'NameHelpTxt', 'De Engelstalige referentie naar de vertaling, bvb. "add".', '2010-02-10 13:42:50');
INSERT INTO `locale` VALUES (408, 1, 'nl', 'backend', 'locale', 'msg', 'AddValueHelpTxt', 'De vertaling zelf, bvb. "toevoegen".', '2010-02-10 13:59:50');
INSERT INTO `locale` VALUES (409, 1, 'nl', 'backend', 'locale', 'msg', 'AddNameHelpTxt', 'De Engelstalige referentie naar de vertaling, bvb. "add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten. Bij voorkeur gebruik je camelCase.', '2010-02-10 13:59:49');
INSERT INTO `locale` VALUES (410, 1, 'nl', 'backend', 'core', 'lbl', 'UpdateFilter', 'Update filter', '2010-02-10 14:54:38');
INSERT INTO `locale` VALUES (411, 1, 'nl', 'backend', 'core', 'lbl', 'SaveAsDraft', 'kladversie opslaan', '2010-02-10 21:48:25');
INSERT INTO `locale` VALUES (412, 1, 'nl', 'frontend', 'core', 'msg', 'TagsNoItems', 'Er zijn nog geen tags gebruikt.', '2010-02-11 19:18:07');
INSERT INTO `locale` VALUES (413, 1, 'nl', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Reactie op: <a href="%s">%s</a>', '2010-02-11 20:11:39');
INSERT INTO `locale` VALUES (414, 1, 'nl', 'frontend', 'core', 'lbl', 'Send', 'verstuur', '2010-02-13 17:26:14');
INSERT INTO `locale` VALUES (415, 1, 'nl', 'frontend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.', '2010-02-13 17:40:49');
INSERT INTO `locale` VALUES (416, 1, 'nl', 'frontend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.', '2010-02-13 17:41:19');
INSERT INTO `locale` VALUES (417, 1, 'nl', 'frontend', 'core', 'err', 'MessageIsRequired', 'Gelieve een bericht in te geven.', '2010-02-13 17:42:11');
INSERT INTO `locale` VALUES (418, 1, 'nl', 'frontend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.', '2010-02-13 17:46:17');
INSERT INTO `locale` VALUES (419, 1, 'nl', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Er ging iets mis tijdens het verzenden, probeer later opnieuw.', '2010-02-13 17:52:23');
INSERT INTO `locale` VALUES (420, 1, 'nl', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Uw mail werd verzonden.', '2010-02-13 23:02:15');
INSERT INTO `locale` VALUES (421, 1, 'nl', 'frontend', 'core', 'msg', 'ContactSubject', 'Mail via contactformulier', '2010-02-13 23:01:51');
INSERT INTO `locale` VALUES (422, 1, 'nl', 'backend', 'core', 'lbl', 'EditedOn', 'laatst bewerkt', '2010-02-14 11:53:19');
INSERT INTO `locale` VALUES (423, 1, 'nl', 'backend', 'core', 'lbl', 'UseThisDraft', 'gebruik deze kladversie', '2010-02-14 11:55:39');

-- --------------------------------------------------------

-- 
-- Table structure for table `meta`
-- 

DROP TABLE IF EXISTS `meta`;
CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL auto_increment,
  `keywords` varchar(255) collate utf8_unicode_ci NOT NULL,
  `keywords_overwrite` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description_overwrite` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `title_overwrite` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  `url_overwrite` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `custom` text character set utf8 COMMENT 'used for custom meta-information',
  PRIMARY KEY  (`id`),
  KEY `idx_url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Meta-information' AUTO_INCREMENT=554 ;

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
INSERT INTO `meta` VALUES (19, 'Titel', 'N', 'Titel', 'N', 'Titel', 'N', 'titel', 'N', NULL);
INSERT INTO `meta` VALUES (20, 'Titel', 'N', 'Titel', 'N', 'Titel', 'N', 'titel-2', 'N', NULL);
INSERT INTO `meta` VALUES (21, 'Titel', 'N', 'Titel', 'N', 'Titel', 'N', 'titel-3', 'N', NULL);
INSERT INTO `meta` VALUES (22, 'Titel', 'N', 'Titel', 'N', 'Titel', 'N', 'titel-4', 'N', NULL);
INSERT INTO `meta` VALUES (23, 'Title', 'N', 'Title', 'N', 'Title', 'N', 'title', 'N', NULL);
INSERT INTO `meta` VALUES (24, 'Titel', 'N', 'Titel', 'N', 'Titel', 'N', 'titel-5', 'N', NULL);
INSERT INTO `meta` VALUES (25, 'ARF', 'N', 'ARF', 'N', 'ARF', 'N', 'arf', 'N', NULL);
INSERT INTO `meta` VALUES (26, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (27, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (28, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (29, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (30, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (31, 'subsubpage', 'N', 'subsubpage', 'N', 'subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (32, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (33, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (34, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (35, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (36, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (37, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (38, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (39, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (40, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (41, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (42, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (43, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (44, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (45, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (46, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (47, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (48, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (49, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (50, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (51, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (52, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (53, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (54, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (55, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (56, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (57, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (58, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (59, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (60, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (61, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (62, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (63, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (64, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (65, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (66, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (67, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (68, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (69, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (70, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (71, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (72, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (73, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (74, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (75, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (76, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (77, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (78, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (79, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (80, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (81, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (82, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (83, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (84, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (85, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (86, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (87, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (88, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (89, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (90, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (91, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (92, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (93, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (94, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (95, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (96, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (97, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (98, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (99, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (100, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (101, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (102, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (103, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (104, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (105, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (106, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (107, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (108, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (109, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (110, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (111, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (112, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (113, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (114, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (115, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (116, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (117, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (118, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (119, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (120, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (121, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (122, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (123, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (124, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (125, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (126, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (127, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (128, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (129, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (130, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (131, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (132, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (133, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (134, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (135, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (136, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (137, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (138, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (139, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (140, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (141, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (142, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (143, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (144, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (145, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (146, 'Subsubpage', 'N', 'Subsubpage', 'N', 'Subsubpage', 'N', 'subsubpage', 'N', NULL);
INSERT INTO `meta` VALUES (147, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (148, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (149, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (177, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (151, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (152, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (153, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (154, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (155, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (156, 'Disclaimer', 'N', 'Disclaimer', 'N', 'Disclaimer', 'N', 'disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (170, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (171, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (172, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (173, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (176, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (162, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (163, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (164, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (165, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (166, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (167, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (175, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (178, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (179, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (180, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (181, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (182, 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'Ik ben meta....', 'N', 'ik-ben-meta', 'N', NULL);
INSERT INTO `meta` VALUES (184, 'Over ons', 'N', 'Over ons', 'N', 'Over ons', 'N', 'over-ons', 'N', NULL);
INSERT INTO `meta` VALUES (185, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-amp-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (245, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (187, '1900-1910', 'N', '1900-1910', 'N', '1900-1910', 'N', '1900-1910', 'N', NULL);
INSERT INTO `meta` VALUES (188, '1910-1920', 'N', '1910-1920', 'N', '1910-1920', 'N', '1910-1920', 'N', NULL);
INSERT INTO `meta` VALUES (189, '1920-1930', 'N', '1920-1930', 'N', '1920-1930', 'N', '1920-1930', 'N', NULL);
INSERT INTO `meta` VALUES (193, 'Metanav test 1', 'N', 'Metanav test 1', 'N', 'Metanav test 1', 'N', 'metanav-test-1', 'N', NULL);
INSERT INTO `meta` VALUES (192, '1930-1940', 'N', '1930-1940', 'N', '1930-1940', 'N', '1930-1941', 'N', NULL);
INSERT INTO `meta` VALUES (194, 'Metanav test 2', 'N', 'Metanav test 2', 'N', 'Metanav test 2', 'N', 'metanav-test-2', 'N', NULL);
INSERT INTO `meta` VALUES (195, 'Metanav test 3', 'N', 'Metanav test 3', 'N', 'Metanav test 3', 'N', 'metanav-test-3', 'N', NULL);
INSERT INTO `meta` VALUES (196, 'Metanav test 4', 'N', 'Metanav test 4', 'N', 'Metanav test 4', 'N', 'metanav-test-4', 'N', NULL);
INSERT INTO `meta` VALUES (197, '19e eeuw', 'N', '19e eeuw', 'N', '19e eeuw', 'N', '19e-eeuw', 'N', NULL);
INSERT INTO `meta` VALUES (198, '20e eeuw', 'N', '20e eeuw', 'N', '20e eeuw', 'N', '20e-eeuw', 'N', NULL);
INSERT INTO `meta` VALUES (199, '21e eeuw', 'N', '21e eeuw', 'N', '21e eeuw', 'N', '21e-eeuw', 'N', NULL);
INSERT INTO `meta` VALUES (200, '1940-1950', 'N', '1940-1950', 'N', '1940-1950', 'N', '1940-1950', 'N', NULL);
INSERT INTO `meta` VALUES (201, '1950-1960', 'N', '1950-1960', 'N', '1950-1960', 'N', '1950-1960', 'N', NULL);
INSERT INTO `meta` VALUES (202, '1960-1970', 'N', '1960-1970', 'N', '1960-1970', 'N', '1960-1970', 'N', NULL);
INSERT INTO `meta` VALUES (203, '1970-1980', 'N', '1970-1980', 'N', '1970-1980', 'N', '1970-1980', 'N', NULL);
INSERT INTO `meta` VALUES (204, '1990-1999', 'N', '1990-1999', 'N', '1990-1999', 'N', '1990-1999', 'N', NULL);
INSERT INTO `meta` VALUES (205, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (206, '2000', 'N', '2000', 'N', '2000', 'N', '2000', 'N', NULL);
INSERT INTO `meta` VALUES (207, '2001', 'N', '2001', 'N', '2001', 'N', '2001', 'N', NULL);
INSERT INTO `meta` VALUES (208, '2002', 'N', '2002', 'N', '2002', 'N', '2002', 'N', NULL);
INSERT INTO `meta` VALUES (209, '2003', 'N', '2003', 'N', '2003', 'N', '2003', 'N', NULL);
INSERT INTO `meta` VALUES (210, 'Januari', 'N', 'Januari', 'N', 'Januari', 'N', 'januari', 'N', NULL);
INSERT INTO `meta` VALUES (211, 'Februari', 'N', 'Februari', 'N', 'Februari', 'N', 'februari', 'N', NULL);
INSERT INTO `meta` VALUES (212, '1', 'N', '1', 'N', '1', 'N', '1', 'N', NULL);
INSERT INTO `meta` VALUES (213, '2', 'N', '2', 'N', '2', 'N', '2', 'N', NULL);
INSERT INTO `meta` VALUES (214, '01AM', 'N', '01AM', 'N', '01AM', 'N', '01am', 'N', NULL);
INSERT INTO `meta` VALUES (215, '02AM', 'N', '02AM', 'N', '02AM', 'N', '02am', 'N', NULL);
INSERT INTO `meta` VALUES (216, '60', 'N', '60', 'N', '60', 'N', '60', 'N', NULL);
INSERT INTO `meta` VALUES (217, 'Historisch feit in een lange titel', 'N', 'Historisch feit in een lange titel', 'N', 'Historisch feit in een lange titel', 'N', 'historisch-feit-in-een-lange-titel', 'N', NULL);
INSERT INTO `meta` VALUES (506, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (244, 'Over ons', 'N', 'Over ons', 'N', 'Over ons', 'N', 'over-ons', 'N', NULL);
INSERT INTO `meta` VALUES (219, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (220, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (221, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (222, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (238, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (239, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (240, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (251, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (246, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (247, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (248, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-amp-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (249, 'Metanav test 1', 'N', 'Metanav test 1', 'N', 'Metanav test 1', 'N', 'metanav-test-1', 'N', NULL);
INSERT INTO `meta` VALUES (250, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (252, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (505, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (504, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (266, 'Dit is een blogpost', 'N', 'Dit is een blogpost', 'N', 'Dit is een blogpost', 'N', 'dit-is-een-blogpost', 'N', NULL);
INSERT INTO `meta` VALUES (267, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (268, 'tewsafdfas', 'N', 'tewsafdfas', 'N', 'tewsafdfas', 'N', 'tewsafdfas', 'N', NULL);
INSERT INTO `meta` VALUES (503, 'Metanav test 1', 'N', 'Metanav test 1', 'N', 'Metanav test 1', 'N', 'metanav-test-1', 'N', NULL);
INSERT INTO `meta` VALUES (281, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (552, 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'de-allereerste-blogpost-op-het-fork-platform', 'N', NULL);
INSERT INTO `meta` VALUES (280, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (551, 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'de-allereerste-blogpost-op-het-fork-platform', 'N', NULL);
INSERT INTO `meta` VALUES (523, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (522, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (302, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (301, 'Hupsakeee kakak', 'N', 'Hupsakeee kakak', 'N', 'Hupsakeee kakak', 'N', 'hupsakeee-kakak', 'N', NULL);
INSERT INTO `meta` VALUES (303, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (304, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (305, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (306, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (307, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (309, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (310, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (326, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (327, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (330, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (331, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (332, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (333, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (334, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (335, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (336, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (337, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (338, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (339, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (340, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (341, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (342, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (343, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (344, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (345, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (346, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (347, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (348, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (349, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (350, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (351, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (352, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (353, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (354, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (355, 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', 'dsdsq', 'N', NULL);
INSERT INTO `meta` VALUES (377, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (379, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (381, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (507, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (508, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (394, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (395, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (396, 'Hupsakeee swdsqdsq', 'N', 'Hupsakeee swdsqdsq', 'N', 'Hupsakeee swdsqdsq', 'N', 'hupsakeee-swdsqdsq', 'N', NULL);
INSERT INTO `meta` VALUES (397, 'Hupsakeee swdsqdsq', 'N', 'Hupsakeee swdsqdsq', 'N', 'Hupsakeee swdsqdsq', 'N', 'hupsakeee-swdsqdsq', 'N', NULL);
INSERT INTO `meta` VALUES (398, 'Hupsakeee swdsqdsq', 'N', 'Hupsakeee swdsqdsq', 'N', 'Hupsakeee swdsqdsq', 'N', 'hupsakeee-swdsqdsq', 'N', NULL);
INSERT INTO `meta` VALUES (399, 'Hupsakeee maar dan als draft', 'N', 'Hupsakeee maar dan als draft', 'N', 'Hupsakeee maar dan als draft', 'N', 'hupsakeee-maar-dan-als-draft', 'N', NULL);
INSERT INTO `meta` VALUES (400, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (401, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (402, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (403, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (404, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (405, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (406, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (407, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (408, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (409, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (410, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (411, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (412, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (413, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (414, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (415, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (416, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (417, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (418, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (419, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (420, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (421, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (422, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (423, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (424, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (425, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (426, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (427, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (428, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (429, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (430, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (431, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (432, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (433, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (434, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (435, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (436, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (437, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (438, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (439, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (440, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (441, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (442, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (443, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (444, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (445, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (446, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (447, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (448, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (449, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (450, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (451, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (452, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (453, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (454, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (455, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (456, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (457, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (458, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (459, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (460, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (461, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (462, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (463, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (464, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (465, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (466, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (467, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (468, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (469, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (470, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (471, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (472, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (473, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (474, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (475, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (476, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (477, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (478, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (479, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (480, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (481, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (482, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (483, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (484, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (485, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (486, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (487, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (488, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (489, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (490, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (491, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (492, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (493, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (494, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (495, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (496, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (497, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO `meta` VALUES (498, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO `meta` VALUES (499, 'Over ons', 'N', 'Over ons', 'N', 'Over ons', 'N', 'over-ons', 'N', NULL);
INSERT INTO `meta` VALUES (500, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (509, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (510, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (511, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (512, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (513, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (514, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (515, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (516, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (517, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (518, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (519, 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'Hupsakeee', 'N', 'hupsakeee', 'N', NULL);
INSERT INTO `meta` VALUES (525, 'Subpage', 'N', 'Subpage', 'N', 'Subpage', 'N', 'subpage', 'N', NULL);
INSERT INTO `meta` VALUES (527, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (528, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES (529, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES (530, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES (531, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (532, 'Contact', 'N', 'Contact', 'N', 'Contact', 'N', 'contact', 'N', NULL);
INSERT INTO `meta` VALUES (550, 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'de-allereerste-blogpost-op-het-fork-platform', 'N', NULL);
INSERT INTO `meta` VALUES (534, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO `meta` VALUES (535, 'DRAFT', 'N', 'DRAFT', 'N', 'DRAFT', 'N', 'draft', 'N', NULL);
INSERT INTO `meta` VALUES (536, 'DRAFT', 'N', 'DRAFT', 'N', 'DRAFT', 'N', 'draft-2', 'N', NULL);
INSERT INTO `meta` VALUES (537, 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'dit-is-een-draft', 'N', NULL);
INSERT INTO `meta` VALUES (538, 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'dit-is-een-draft', 'N', NULL);
INSERT INTO `meta` VALUES (539, 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'dit-is-een-draft', 'N', NULL);
INSERT INTO `meta` VALUES (540, 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'dit-is-een-draft', 'N', NULL);
INSERT INTO `meta` VALUES (541, 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'dit-is-een-draft', 'N', NULL);
INSERT INTO `meta` VALUES (542, 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'DIT IS EEN DRAFT', 'N', 'dit-is-een-draft', 'N', NULL);
INSERT INTO `meta` VALUES (549, 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'de-allereerste-blogpost-op-het-fork-platform', 'N', NULL);
INSERT INTO `meta` VALUES (548, 'Dit is een voorbeeld van een kladversie', 'N', 'Dit is een voorbeeld van een kladversie', 'N', 'Dit is een voorbeeld van een kladversie', 'N', 'dit-is-een-voorbeeld-van-een-kladversie', 'N', NULL);
INSERT INTO `meta` VALUES (553, 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'De allereerste blogpost op het Fork platform', 'N', 'de-allereerste-blogpost-op-het-fork-platform', 'N', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `modules`
-- 

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'unique module name',
  `description` text collate utf8_unicode_ci,
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `modules`
-- 

INSERT INTO `modules` VALUES ('blog', NULL, 'Y');
INSERT INTO `modules` VALUES ('pages', 'Manage the pages for this website.', 'Y');
INSERT INTO `modules` VALUES ('settings', NULL, 'Y');
INSERT INTO `modules` VALUES ('contentblocks', NULL, 'Y');
INSERT INTO `modules` VALUES ('statistics', NULL, 'Y');
INSERT INTO `modules` VALUES ('tags', NULL, 'Y');
INSERT INTO `modules` VALUES ('news', NULL, 'Y');
INSERT INTO `modules` VALUES ('users', NULL, 'Y');
INSERT INTO `modules` VALUES ('locale', NULL, 'Y');
INSERT INTO `modules` VALUES ('sitemap', NULL, 'Y');
INSERT INTO `modules` VALUES ('contact', NULL, 'Y');

-- --------------------------------------------------------

-- 
-- Table structure for table `modules_settings`
-- 

DROP TABLE IF EXISTS `modules_settings`;
CREATE TABLE IF NOT EXISTS `modules_settings` (
  `module` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text collate utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`module`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `modules_settings`
-- 

INSERT INTO `modules_settings` VALUES ('blog', 'allow_comments', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'default_category_nl', 'i:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'feedburner_url_nl', 'N;');
INSERT INTO `modules_settings` VALUES ('blog', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('blog', 'moderation', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'ping_services', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'requires_akismet', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('blog', 'rss_description_nl', 's:34:"Volg het reilen en zeilen van Fork";');
INSERT INTO `modules_settings` VALUES ('blog', 'rss_title_nl', 's:9:"Fork blog";');
INSERT INTO `modules_settings` VALUES ('blog', 'spamfilter', 'b:1;');
INSERT INTO `modules_settings` VALUES ('contact', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('contact', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('core', 'active_languages', 'a:1:{i:0;s:2:"nl";}');
INSERT INTO `modules_settings` VALUES ('core', 'akismet_key', 's:12:"41eadca08459";');
INSERT INTO `modules_settings` VALUES ('core', 'default_category', 'i:1;');
INSERT INTO `modules_settings` VALUES ('core', 'default_language', 's:2:"nl";');
INSERT INTO `modules_settings` VALUES ('core', 'default_template', 'i:1;');
INSERT INTO `modules_settings` VALUES ('core', 'email_nl', 's:18:"forkng@fork-cms.be";');
INSERT INTO `modules_settings` VALUES ('core', 'fork_api_private_key', 's:32:"58e93416a90fcc5df0d381f50cccf9ae";');
INSERT INTO `modules_settings` VALUES ('core', 'fork_api_public_key', 's:32:"c0afc6354ba2ca77e92acde7eb6d75f8";');
INSERT INTO `modules_settings` VALUES ('core', 'google_maps_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'interface_languages', 'a:1:{i:0;s:2:"nl";}');
INSERT INTO `modules_settings` VALUES ('core', 'languages', 'a:3:{i:0;s:2:"nl";i:1;s:2:"fr";i:2;s:2:"en";}');
INSERT INTO `modules_settings` VALUES ('core', 'mailer_from', 'a:2:{i:0;s:20:"no-reply@fork-cms.be";i:1;s:4:"Fork";}');
INSERT INTO `modules_settings` VALUES ('core', 'mailer_reply_to', 'a:2:{i:0;s:20:"no-reply@fork-cms.be";i:1;s:4:"Fork";}');
INSERT INTO `modules_settings` VALUES ('core', 'mailer_to', 'a:2:{i:0;s:20:"no-reply@fork-cms.be";i:1;s:4:"Fork";}');
INSERT INTO `modules_settings` VALUES ('core', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('core', 'ping_services', 'a:2:{s:8:"services";a:3:{i:0;a:3:{s:3:"url";s:27:"http://rpc.weblogs.com/RPC2";s:4:"port";i:80;s:4:"type";s:8:"extended";}i:1;a:3:{s:3:"url";s:30:"http://rpc.pingomatic.com/RPC2";s:4:"port";i:80;s:4:"type";s:8:"extended";}i:2;a:3:{s:3:"url";s:39:"http://blogsearch.google.com/ping/RPC2 ";s:4:"port";i:80;s:4:"type";s:8:"extended";}}s:4:"date";i:1264968052;}');
INSERT INTO `modules_settings` VALUES ('core', 'site_domains', 'a:1:{i:0;s:12:"forkng.local";}');
INSERT INTO `modules_settings` VALUES ('core', 'site_title_', 's:2:"nl";');
INSERT INTO `modules_settings` VALUES ('core', 'site_title_en', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('core', 'site_title_fr', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('core', 'site_title_nl', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('core', 'site_wide_html', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_password', 's:8:"Jishaik6";');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_port', 'i:587;');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_server', 's:16:"mail.fork-cms.be";');
INSERT INTO `modules_settings` VALUES ('core', 'smtp_username', 's:16:"bugs@fork-cms.be";');
INSERT INTO `modules_settings` VALUES ('core', 'template_max_blocks', 'i:5;');
INSERT INTO `modules_settings` VALUES ('core', 'website_title_nl', 's:7:"Fork NG";');
INSERT INTO `modules_settings` VALUES ('locale', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('locale', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('news', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('news', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('pages', 'maximum_number_of_revisions', 'i:20;');
INSERT INTO `modules_settings` VALUES ('pages', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('pages', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('settings', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('settings', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('sitemap', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('sitemap', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('contentblocks', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('contentblocks', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('contentblocks', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('statistics', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('statistics', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('tags', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('tags', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('users', 'default_group', 'i:1;');
INSERT INTO `modules_settings` VALUES ('users', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('users', 'requires_google_maps', 'b:0;');

-- --------------------------------------------------------

-- 
-- Table structure for table `modules_tags`
-- 

DROP TABLE IF EXISTS `modules_tags`;
CREATE TABLE IF NOT EXISTS `modules_tags` (
  `module` varchar(255) collate utf8_unicode_ci NOT NULL,
  `tag_id` int(11) NOT NULL,
  `other_id` int(11) NOT NULL,
  PRIMARY KEY  (`module`,`tag_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `modules_tags`
-- 

INSERT INTO `modules_tags` VALUES ('blog', 1, 1);
INSERT INTO `modules_tags` VALUES ('blog', 2, 2);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1795 ;

-- 
-- Dumping data for table `pages`
-- 

INSERT INTO `pages` VALUES (1008, 1507, 1, 1015, 1, 188, 'nl', 'page', '1910-1920', '1910-1920', 'N', 'N', 'active', '2010-01-20 15:57:12', NULL, '2010-01-20 15:57:12', '2010-01-20 15:57:12', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'N', NULL);
INSERT INTO `pages` VALUES (1009, 1508, 1, 1015, 1, 189, 'nl', 'page', '1920-1930', '1920-1930', 'N', 'N', 'active', '2010-01-20 15:57:22', NULL, '2010-01-20 15:57:22', '2010-01-20 15:57:22', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'N', NULL);
INSERT INTO `pages` VALUES (1011, 1511, 1, 1015, 1, 192, 'nl', 'page', '1930-1940', '1930-1940', 'N', 'N', 'active', '2010-01-20 15:57:47', NULL, '2010-01-20 15:57:47', '2010-01-20 15:57:47', 'Y', 'Y', 'Y', 'Y', 'N', 7, 'N', NULL);
INSERT INTO `pages` VALUES (1012, 1513, 1, 1000, 1, 194, 'nl', 'meta', 'Metanav test 2', 'Metanav test 2', 'N', 'N', 'active', '2010-01-20 16:05:11', NULL, '2010-01-20 16:05:11', '2010-01-20 16:05:11', 'Y', 'Y', 'Y', 'Y', 'N', 13, 'N', NULL);
INSERT INTO `pages` VALUES (1013, 1514, 1, 1000, 1, 195, 'nl', 'meta', 'Metanav test 3', 'Metanav test 3', 'N', 'N', 'active', '2010-01-20 16:05:22', NULL, '2010-01-20 16:05:22', '2010-01-20 16:05:22', 'Y', 'Y', 'Y', 'Y', 'N', 14, 'N', NULL);
INSERT INTO `pages` VALUES (1014, 1515, 1, 1000, 1, 196, 'nl', 'meta', 'Metanav test 4', 'Metanav test 4', 'N', 'N', 'active', '2010-01-20 16:05:32', NULL, '2010-01-20 16:05:32', '2010-01-20 16:05:32', 'Y', 'Y', 'Y', 'Y', 'N', 15, 'N', NULL);
INSERT INTO `pages` VALUES (1007, 1516, 1, 1015, 1, 197, 'nl', 'page', '19e eeuw', '1900-1910', 'N', 'N', 'active', '2010-01-20 15:57:05', NULL, '2010-01-20 15:57:05', '2010-01-20 16:12:02', 'Y', 'Y', 'Y', 'Y', 'N', 5, 'N', NULL);
INSERT INTO `pages` VALUES (1015, 1517, 1, 1002, 1, 198, 'nl', 'page', '20e eeuw', '20e eeuw', 'N', 'N', 'active', '2010-01-20 16:12:12', NULL, '2010-01-20 16:12:12', '2010-01-20 16:12:12', 'Y', 'Y', 'Y', 'Y', 'N', 4, 'N', NULL);
INSERT INTO `pages` VALUES (1016, 1518, 1, 1002, 1, 199, 'nl', 'page', '21e eeuw', '21e eeuw', 'N', 'N', 'active', '2010-01-20 16:12:22', NULL, '2010-01-20 16:12:22', '2010-01-20 16:12:22', 'Y', 'Y', 'Y', 'Y', 'N', 8, 'N', NULL);
INSERT INTO `pages` VALUES (1017, 1519, 1, 1015, 1, 200, 'nl', 'page', '1940-1950', '1940-1950', 'N', 'N', 'active', '2010-01-20 16:33:24', NULL, '2010-01-20 16:33:24', '2010-01-20 16:33:24', 'Y', 'Y', 'Y', 'Y', 'N', 8, 'N', NULL);
INSERT INTO `pages` VALUES (1018, 1520, 1, 1015, 1, 201, 'nl', 'page', '1950-1960', '1950-1960', 'N', 'N', 'active', '2010-01-20 16:33:32', NULL, '2010-01-20 16:33:32', '2010-01-20 16:33:32', 'Y', 'Y', 'Y', 'Y', 'N', 9, 'N', NULL);
INSERT INTO `pages` VALUES (1019, 1521, 1, 1015, 1, 202, 'nl', 'page', '1960-1970', '1960-1970', 'N', 'N', 'active', '2010-01-20 16:33:38', NULL, '2010-01-20 16:33:38', '2010-01-20 16:33:38', 'Y', 'Y', 'Y', 'Y', 'N', 10, 'N', NULL);
INSERT INTO `pages` VALUES (1020, 1522, 1, 1015, 1, 203, 'nl', 'page', '1970-1980', '1970-1980', 'N', 'N', 'active', '2010-01-20 16:33:45', NULL, '2010-01-20 16:33:45', '2010-01-20 16:33:45', 'Y', 'Y', 'Y', 'Y', 'N', 11, 'N', NULL);
INSERT INTO `pages` VALUES (1021, 1523, 1, 1016, 1, 204, 'nl', 'page', '1990-1999', '1990-1999', 'N', 'N', 'active', '2010-01-20 16:33:51', NULL, '2010-01-20 16:33:51', '2010-01-20 16:33:51', 'Y', 'Y', 'Y', 'Y', 'N', 9, 'N', NULL);
INSERT INTO `pages` VALUES (1023, 1525, 1, 1022, 1, 206, 'nl', 'page', '2000', '2000', 'N', 'N', 'active', '2010-01-20 16:35:25', NULL, '2010-01-20 16:35:25', '2010-01-20 16:35:25', 'Y', 'Y', 'Y', 'Y', 'N', 7, 'N', NULL);
INSERT INTO `pages` VALUES (1024, 1526, 1, 1022, 1, 207, 'nl', 'page', '2001', '2001', 'N', 'N', 'active', '2010-01-20 16:35:29', NULL, '2010-01-20 16:35:29', '2010-01-20 16:35:29', 'Y', 'Y', 'Y', 'Y', 'N', 9, 'N', NULL);
INSERT INTO `pages` VALUES (1025, 1527, 1, 1022, 1, 208, 'nl', 'page', '2002', '2002', 'N', 'N', 'active', '2010-01-20 16:35:36', NULL, '2010-01-20 16:35:36', '2010-01-20 16:35:36', 'Y', 'Y', 'Y', 'Y', 'N', 10, 'N', NULL);
INSERT INTO `pages` VALUES (1026, 1528, 1, 1022, 1, 209, 'nl', 'page', '2003', '2003', 'N', 'N', 'active', '2010-01-20 16:35:41', NULL, '2010-01-20 16:35:41', '2010-01-20 16:35:41', 'Y', 'Y', 'Y', 'Y', 'N', 11, 'N', NULL);
INSERT INTO `pages` VALUES (1027, 1529, 1, 1026, 1, 210, 'nl', 'page', 'Januari', 'Januari', 'N', 'N', 'active', '2010-01-20 16:35:46', NULL, '2010-01-20 16:35:46', '2010-01-20 16:35:46', 'Y', 'Y', 'Y', 'Y', 'N', 11, 'N', NULL);
INSERT INTO `pages` VALUES (1028, 1530, 1, 1026, 1, 211, 'nl', 'page', 'Februari', 'Februari', 'N', 'N', 'active', '2010-01-20 16:35:53', NULL, '2010-01-20 16:35:53', '2010-01-20 16:35:53', 'Y', 'Y', 'Y', 'Y', 'N', 13, 'N', NULL);
INSERT INTO `pages` VALUES (1030, 1532, 1, 1027, 1, 213, 'nl', 'page', '2', '2', 'N', 'N', 'active', '2010-01-20 16:36:07', NULL, '2010-01-20 16:36:07', '2010-01-20 16:36:07', 'Y', 'Y', 'Y', 'Y', 'N', 20, 'N', NULL);
INSERT INTO `pages` VALUES (1029, 1533, 1, 1027, 1, 214, 'nl', 'page', '01AM', '1', 'N', 'N', 'active', '2010-01-20 16:36:02', NULL, '2010-01-20 16:36:02', '2010-01-20 16:38:30', 'Y', 'Y', 'Y', 'Y', 'N', 16, 'N', NULL);
INSERT INTO `pages` VALUES (1031, 1534, 1, 1029, 1, 215, 'nl', 'page', '02AM', '02AM', 'N', 'N', 'active', '2010-01-20 16:38:36', NULL, '2010-01-20 16:38:36', '2010-01-20 16:38:36', 'Y', 'Y', 'Y', 'Y', 'N', 18, 'N', NULL);
INSERT INTO `pages` VALUES (1032, 1535, 1, 1026, 1, 216, 'nl', 'page', '60', '60', 'N', 'N', 'active', '2010-01-20 16:39:12', NULL, '2010-01-20 16:39:12', '2010-01-20 16:39:12', 'Y', 'Y', 'Y', 'Y', 'N', 11, 'N', NULL);
INSERT INTO `pages` VALUES (1033, 1536, 1, 1031, 1, 217, 'nl', 'page', 'Historisch feit in een lange titel', 'Historisch feit in een lange titel', 'N', 'N', 'active', '2010-01-20 16:39:50', NULL, '2010-01-20 16:39:50', '2010-01-20 16:39:50', 'Y', 'Y', 'Y', 'Y', 'N', 20, 'N', NULL);
INSERT INTO `pages` VALUES (1022, 1540, 1, 1015, 2, 221, 'nl', 'page', '2000-2010', '2000-2010', 'N', 'N', 'active', '2010-01-20 16:34:50', NULL, '2010-01-20 16:34:50', '2010-01-26 14:14:40', 'Y', 'Y', 'Y', 'Y', 'N', 12, 'N', NULL);
INSERT INTO `pages` VALUES (1034, 1574, 6, 0, 1, 265, 'nl', 'root', 'dsfasdf', 'dsfasdf', 'N', 'N', 'active', '2010-01-28 14:03:54', NULL, '2010-01-28 14:03:54', '2010-01-28 14:03:54', 'Y', 'Y', 'Y', 'Y', 'N', 18, 'N', NULL);
INSERT INTO `pages` VALUES (3, 1777, 1, 0, 1, 504, 'nl', 'footer', 'Privacy &amp; Disclaimer', 'Disclaimer', 'N', 'N', 'active', '2010-01-11 14:02:49', NULL, '2010-01-11 14:02:49', '2010-02-09 21:18:10', 'Y', 'Y', 'Y', 'Y', 'Y', 1, 'N', '2');
INSERT INTO `pages` VALUES (1000, 1776, 1, 0, 2, 503, 'nl', 'meta', 'Metanav test 1', 'Ik ben meta....', 'N', 'N', 'active', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-02-09 21:18:05', 'Y', 'Y', 'Y', 'Y', 'N', 13, 'N', NULL);
INSERT INTO `pages` VALUES (1, 1771, 1, 0, 1, 498, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'active', '2010-01-11 09:41:16', NULL, '2010-01-11 07:02:26', '2010-02-09 21:15:35', 'N', 'Y', 'Y', 'N', 'N', 1, 'N', '7,8');
INSERT INTO `pages` VALUES (404, 1794, 1, 0, 1, 534, 'nl', 'root', '404', '404', 'N', 'N', 'active', '2010-01-11 15:03:17', NULL, '2010-01-11 15:03:17', '2010-02-13 22:05:28', 'N', 'Y', 'Y', 'N', 'N', 17, 'N', '8');
INSERT INTO `pages` VALUES (1044, 1770, 1, 0, 1, 497, 'nl', 'root', 'Tags', 'Tags', 'N', 'N', 'active', '2010-02-09 20:29:46', NULL, '2010-02-09 20:29:46', '2010-02-09 21:15:02', 'Y', 'Y', 'Y', 'Y', 'N', 26, 'Y', '9');
INSERT INTO `pages` VALUES (1005, 1772, 1, 1, 1, 499, 'nl', 'page', 'Over ons', 'Over ons', 'N', 'N', 'active', '2010-01-20 15:55:27', NULL, '2010-01-20 15:55:27', '2010-02-09 21:15:42', 'Y', 'Y', 'Y', 'Y', 'N', 3, 'N', NULL);
INSERT INTO `pages` VALUES (1045, 1793, 1, 1, 1, 532, 'nl', 'page', 'Contact', 'Contact', 'N', 'N', 'active', '2010-02-13 16:57:04', NULL, '2010-02-13 16:57:04', '2010-02-13 17:13:58', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'Y', '6');
INSERT INTO `pages` VALUES (404, 1782, 1, 0, 1, 513, 'nl', 'root', '404', '404', 'N', 'N', 'archive', '2010-01-11 15:03:17', NULL, '2010-01-11 15:03:17', '2010-02-10 10:48:36', 'N', 'Y', 'Y', 'N', 'N', 17, 'N', NULL);
INSERT INTO `pages` VALUES (2, 1792, 1, 0, 1, 531, 'nl', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'active', '2010-01-11 14:02:56', NULL, '2010-01-11 14:02:56', '2010-02-13 17:13:37', 'Y', 'Y', 'Y', 'Y', 'Y', 3, 'Y', '5');
INSERT INTO `pages` VALUES (1003, 1784, 1, 1, 1, 515, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'active', '2010-01-18 20:31:59', NULL, '2010-01-18 20:31:59', '2010-02-10 10:59:50', 'Y', 'Y', 'Y', 'Y', 'N', 2, 'Y', '1,2');
INSERT INTO `pages` VALUES (1045, 1791, 1, 1, 1, 530, 'nl', 'page', 'Contact', 'Contact', 'N', 'N', 'archive', '2010-02-13 16:57:04', NULL, '2010-02-13 16:57:04', '2010-02-13 17:11:54', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'N', NULL);
INSERT INTO `pages` VALUES (1045, 1790, 1, 1, 1, 529, 'nl', 'page', 'Contact', 'Contact', 'N', 'N', 'archive', '2010-02-13 16:57:04', NULL, '2010-02-13 16:57:04', '2010-02-13 17:11:44', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'N', NULL);
INSERT INTO `pages` VALUES (1002, 1787, 1, 1, 2, 525, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'active', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-02-11 16:14:32', 'Y', 'Y', 'Y', 'Y', 'N', 18, 'N', NULL);
INSERT INTO `pages` VALUES (2, 1788, 1, 0, 1, 527, 'nl', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-01-11 14:02:56', NULL, '2010-01-11 14:02:56', '2010-02-13 16:56:09', 'Y', 'Y', 'Y', 'Y', 'Y', 3, 'Y', '5');
INSERT INTO `pages` VALUES (1045, 1789, 1, 1, 1, 528, 'nl', 'page', 'Contact', 'Contact', 'N', 'N', 'archive', '2010-02-13 16:57:04', NULL, '2010-02-13 16:57:04', '2010-02-13 16:57:04', 'Y', 'Y', 'Y', 'Y', 'N', 6, 'Y', '6');

-- --------------------------------------------------------

-- 
-- Table structure for table `pages_blocks`
-- 

DROP TABLE IF EXISTS `pages_blocks`;
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

-- 
-- Dumping data for table `pages_blocks`
-- 

INSERT INTO `pages_blocks` VALUES (1, 1466, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:28:27', '2010-01-12 16:28:27');
INSERT INTO `pages_blocks` VALUES (2, 1466, NULL, '<p>BB</p>', 'active', '2010-01-12 16:28:27', '2010-01-12 16:28:27');
INSERT INTO `pages_blocks` VALUES (3, 1466, NULL, '<p>CC</p>', 'active', '2010-01-12 16:28:27', '2010-01-12 16:28:27');
INSERT INTO `pages_blocks` VALUES (4, 1466, NULL, '<p>DD</p>', 'active', '2010-01-12 16:28:27', '2010-01-12 16:28:27');
INSERT INTO `pages_blocks` VALUES (5, 1466, NULL, '<p>EE</p>', 'active', '2010-01-12 16:28:27', '2010-01-12 16:28:27');
INSERT INTO `pages_blocks` VALUES (1, 1467, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:29:27', '2010-01-12 16:29:27');
INSERT INTO `pages_blocks` VALUES (2, 1467, NULL, '<p>BB</p>', 'active', '2010-01-12 16:29:27', '2010-01-12 16:29:27');
INSERT INTO `pages_blocks` VALUES (3, 1467, NULL, '<p>CC</p>', 'active', '2010-01-12 16:29:27', '2010-01-12 16:29:27');
INSERT INTO `pages_blocks` VALUES (6, 1468, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:30:08', '2010-01-12 16:30:08');
INSERT INTO `pages_blocks` VALUES (7, 1468, NULL, '<p>BB</p>', 'active', '2010-01-12 16:30:08', '2010-01-12 16:30:08');
INSERT INTO `pages_blocks` VALUES (8, 1468, NULL, '<p>CC</p>', 'active', '2010-01-12 16:30:08', '2010-01-12 16:30:08');
INSERT INTO `pages_blocks` VALUES (9, 1468, NULL, '<p>DD</p>', 'active', '2010-01-12 16:30:08', '2010-01-12 16:30:08');
INSERT INTO `pages_blocks` VALUES (10, 1468, NULL, '<p>EE</p>', 'active', '2010-01-12 16:30:08', '2010-01-12 16:30:08');
INSERT INTO `pages_blocks` VALUES (40, 1496, NULL, '', 'archive', '2010-01-18 21:48:18', '2010-01-18 21:48:18');
INSERT INTO `pages_blocks` VALUES (39, 1496, NULL, '<p>ddddd</p>', 'archive', '2010-01-18 21:48:18', '2010-01-18 21:48:18');
INSERT INTO `pages_blocks` VALUES (14, 1470, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:30:50', '2010-01-12 16:30:50');
INSERT INTO `pages_blocks` VALUES (15, 1470, NULL, '<p>BB</p>', 'active', '2010-01-12 16:30:50', '2010-01-12 16:30:50');
INSERT INTO `pages_blocks` VALUES (16, 1470, NULL, '<p>CC</p>', 'active', '2010-01-12 16:30:50', '2010-01-12 16:30:50');
INSERT INTO `pages_blocks` VALUES (17, 1471, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:31:07', '2010-01-12 16:31:07');
INSERT INTO `pages_blocks` VALUES (18, 1471, NULL, '<p>BB</p>', 'active', '2010-01-12 16:31:07', '2010-01-12 16:31:07');
INSERT INTO `pages_blocks` VALUES (19, 1471, NULL, '<p>CC</p>', 'active', '2010-01-12 16:31:07', '2010-01-12 16:31:07');
INSERT INTO `pages_blocks` VALUES (1, 1472, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:33:00', '2010-01-12 16:33:00');
INSERT INTO `pages_blocks` VALUES (2, 1472, NULL, '<p>BB</p>', 'active', '2010-01-12 16:33:00', '2010-01-12 16:33:00');
INSERT INTO `pages_blocks` VALUES (3, 1472, NULL, '<p>CC</p>', 'active', '2010-01-12 16:33:00', '2010-01-12 16:33:00');
INSERT INTO `pages_blocks` VALUES (14, 1473, NULL, '<p>AA</p>', 'archive', '2010-01-12 16:33:13', '2010-01-12 16:33:13');
INSERT INTO `pages_blocks` VALUES (15, 1473, NULL, '<p>BB</p>', 'active', '2010-01-12 16:33:13', '2010-01-12 16:33:13');
INSERT INTO `pages_blocks` VALUES (16, 1473, NULL, '<p>CC</p>', 'active', '2010-01-12 16:33:13', '2010-01-12 16:33:13');
INSERT INTO `pages_blocks` VALUES (14, 1474, 1, NULL, 'archive', '2010-01-12 18:47:15', '2010-01-12 18:47:15');
INSERT INTO `pages_blocks` VALUES (15, 1474, 2, NULL, 'active', '2010-01-12 18:47:15', '2010-01-12 18:47:15');
INSERT INTO `pages_blocks` VALUES (16, 1474, 3, NULL, 'active', '2010-01-12 18:47:15', '2010-01-12 18:47:15');
INSERT INTO `pages_blocks` VALUES (14, 1475, 1, NULL, 'archive', '2010-01-12 18:47:39', '2010-01-12 18:47:39');
INSERT INTO `pages_blocks` VALUES (15, 1475, 2, NULL, 'active', '2010-01-12 18:47:39', '2010-01-12 18:47:39');
INSERT INTO `pages_blocks` VALUES (16, 1475, 3, NULL, 'active', '2010-01-12 18:47:39', '2010-01-12 18:47:39');
INSERT INTO `pages_blocks` VALUES (40, 1490, NULL, '', 'archive', '2010-01-14 18:15:23', '2010-01-14 18:15:23');
INSERT INTO `pages_blocks` VALUES (39, 1490, NULL, '', 'archive', '2010-01-14 18:15:23', '2010-01-14 18:15:23');
INSERT INTO `pages_blocks` VALUES (1, 1489, NULL, '<p>AA</p>', 'active', '2010-01-14 18:11:30', '2010-01-14 18:11:30');
INSERT INTO `pages_blocks` VALUES (2, 1489, NULL, '<p>BB</p>', 'active', '2010-01-14 18:11:30', '2010-01-14 18:11:30');
INSERT INTO `pages_blocks` VALUES (3, 1489, NULL, '<p>CC</p>', 'active', '2010-01-14 18:11:30', '2010-01-14 18:11:30');
INSERT INTO `pages_blocks` VALUES (45, 1492, NULL, '<p>BB</p>', 'archive', '2010-01-18 19:26:49', '2010-01-18 19:26:49');
INSERT INTO `pages_blocks` VALUES (44, 1492, NULL, '<p>AA</p>', 'archive', '2010-01-18 19:26:49', '2010-01-18 19:26:49');
INSERT INTO `pages_blocks` VALUES (43, 1490, NULL, '', 'active', '2010-01-14 18:15:23', '2010-01-14 18:15:23');
INSERT INTO `pages_blocks` VALUES (42, 1490, NULL, '', 'archive', '2010-01-14 18:15:23', '2010-01-14 18:15:23');
INSERT INTO `pages_blocks` VALUES (41, 1490, NULL, '', 'archive', '2010-01-14 18:15:23', '2010-01-14 18:15:23');
INSERT INTO `pages_blocks` VALUES (6, 1481, NULL, '<p>AA</p>', 'active', '2010-01-14 16:59:19', '2010-01-14 16:59:19');
INSERT INTO `pages_blocks` VALUES (7, 1481, NULL, '<p>BB</p>', 'active', '2010-01-14 16:59:19', '2010-01-14 16:59:19');
INSERT INTO `pages_blocks` VALUES (8, 1481, NULL, '<p>CC</p>', 'active', '2010-01-14 16:59:19', '2010-01-14 16:59:19');
INSERT INTO `pages_blocks` VALUES (9, 1481, NULL, '<p>DD</p>', 'active', '2010-01-14 16:59:19', '2010-01-14 16:59:19');
INSERT INTO `pages_blocks` VALUES (10, 1481, NULL, '<p>EE</p>', 'active', '2010-01-14 16:59:19', '2010-01-14 16:59:19');
INSERT INTO `pages_blocks` VALUES (29, 1484, NULL, '<p>AA</p>', 'archive', '2010-01-14 17:02:02', '2010-01-14 17:02:02');
INSERT INTO `pages_blocks` VALUES (30, 1484, NULL, '<p>BB</p>', 'active', '2010-01-14 17:02:02', '2010-01-14 17:02:02');
INSERT INTO `pages_blocks` VALUES (31, 1484, NULL, '<p>CC</p>', 'active', '2010-01-14 17:02:02', '2010-01-14 17:02:02');
INSERT INTO `pages_blocks` VALUES (32, 1484, NULL, '<p>DD</p>', 'active', '2010-01-14 17:02:02', '2010-01-14 17:02:02');
INSERT INTO `pages_blocks` VALUES (33, 1484, NULL, '<p>EE</p>', 'active', '2010-01-14 17:02:02', '2010-01-14 17:02:02');
INSERT INTO `pages_blocks` VALUES (29, 1485, NULL, '<p>AA</p>', 'archive', '2010-01-14 17:02:12', '2010-01-14 17:02:12');
INSERT INTO `pages_blocks` VALUES (30, 1485, NULL, '<p>BB</p>', 'active', '2010-01-14 17:02:12', '2010-01-14 17:02:12');
INSERT INTO `pages_blocks` VALUES (31, 1485, NULL, '<p>CC</p>', 'active', '2010-01-14 17:02:12', '2010-01-14 17:02:12');
INSERT INTO `pages_blocks` VALUES (32, 1485, NULL, '<p>DD</p>', 'active', '2010-01-14 17:02:12', '2010-01-14 17:02:12');
INSERT INTO `pages_blocks` VALUES (33, 1485, NULL, '<p>EE</p>', 'active', '2010-01-14 17:02:12', '2010-01-14 17:02:12');
INSERT INTO `pages_blocks` VALUES (29, 1486, NULL, '<p>AA</p>', 'archive', '2010-01-14 17:02:51', '2010-01-14 17:02:51');
INSERT INTO `pages_blocks` VALUES (30, 1486, NULL, '<p>BB</p>', 'active', '2010-01-14 17:02:51', '2010-01-14 17:02:51');
INSERT INTO `pages_blocks` VALUES (31, 1486, NULL, '<p>CC</p>', 'active', '2010-01-14 17:02:51', '2010-01-14 17:02:51');
INSERT INTO `pages_blocks` VALUES (32, 1486, NULL, '<p>DD</p>', 'active', '2010-01-14 17:02:51', '2010-01-14 17:02:51');
INSERT INTO `pages_blocks` VALUES (33, 1486, NULL, '<p>EE</p>', 'active', '2010-01-14 17:02:51', '2010-01-14 17:02:51');
INSERT INTO `pages_blocks` VALUES (43, 1496, NULL, '', 'active', '2010-01-18 21:48:18', '2010-01-18 21:48:18');
INSERT INTO `pages_blocks` VALUES (42, 1496, NULL, '<p>ddddddd</p>', 'archive', '2010-01-18 21:48:18', '2010-01-18 21:48:18');
INSERT INTO `pages_blocks` VALUES (41, 1496, NULL, '', 'active', '2010-01-18 21:48:18', '2010-01-18 21:48:18');
INSERT INTO `pages_blocks` VALUES (41, 1495, NULL, '', 'archive', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (46, 1492, NULL, '<p>CC</p>', 'active', '2010-01-18 19:26:49', '2010-01-18 19:26:49');
INSERT INTO `pages_blocks` VALUES (42, 1495, NULL, '<p>ddddddd</p>', 'archive', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (43, 1495, NULL, '', 'active', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (39, 1495, NULL, '', 'archive', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (40, 1495, NULL, '', 'archive', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (47, 1494, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-18 21:31:59', '2010-01-18 21:31:59');
INSERT INTO `pages_blocks` VALUES (48, 1494, 1, NULL, 'active', '2010-01-18 21:31:59', '2010-01-18 21:31:59');
INSERT INTO `pages_blocks` VALUES (49, 1494, 2, NULL, 'active', '2010-01-18 21:31:59', '2010-01-18 21:31:59');
INSERT INTO `pages_blocks` VALUES (40, 1497, NULL, '', 'archive', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (39, 1497, NULL, '<p>ddddd</p>', 'archive', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (43, 1497, NULL, '', 'active', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (42, 1497, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (41, 1497, NULL, '', 'active', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (40, 1498, NULL, '', 'archive', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (39, 1498, NULL, '<p>ddddd</p>', 'archive', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (43, 1498, NULL, '', 'active', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (42, 1498, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (41, 1498, NULL, '', 'active', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (40, 1499, NULL, '', 'archive', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (39, 1499, NULL, '<p>ddddd</p>', 'archive', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (43, 1499, NULL, '', 'active', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (42, 1499, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (41, 1499, NULL, '', 'active', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (40, 1500, NULL, '', 'archive', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (39, 1500, NULL, '<p>ddddd</p>', 'archive', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (43, 1500, NULL, '', 'active', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (42, 1500, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (41, 1500, NULL, '', 'active', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (40, 1501, NULL, '', 'archive', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (39, 1501, NULL, '<p>ddddd</p>', 'archive', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (43, 1501, NULL, '', 'active', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (42, 1501, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (41, 1501, NULL, '', 'active', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (53, 1503, NULL, '', 'archive', '2010-01-20 15:55:27', '2010-01-20 15:55:27');
INSERT INTO `pages_blocks` VALUES (54, 1503, NULL, '', 'active', '2010-01-20 15:55:27', '2010-01-20 15:55:27');
INSERT INTO `pages_blocks` VALUES (55, 1503, NULL, '', 'active', '2010-01-20 15:55:27', '2010-01-20 15:55:27');
INSERT INTO `pages_blocks` VALUES (14, 1504, 1, NULL, 'archive', '2010-01-20 15:55:55', '2010-01-20 15:55:55');
INSERT INTO `pages_blocks` VALUES (15, 1504, 2, NULL, 'active', '2010-01-20 15:55:55', '2010-01-20 15:55:55');
INSERT INTO `pages_blocks` VALUES (16, 1504, 3, NULL, 'active', '2010-01-20 15:55:55', '2010-01-20 15:55:55');
INSERT INTO `pages_blocks` VALUES (157, 1564, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-27 15:14:45', '2010-01-27 15:14:45');
INSERT INTO `pages_blocks` VALUES (59, 1506, NULL, '', 'archive', '2010-01-20 15:57:05', '2010-01-20 15:57:05');
INSERT INTO `pages_blocks` VALUES (60, 1506, NULL, '', 'active', '2010-01-20 15:57:05', '2010-01-20 15:57:05');
INSERT INTO `pages_blocks` VALUES (61, 1506, NULL, '', 'active', '2010-01-20 15:57:05', '2010-01-20 15:57:05');
INSERT INTO `pages_blocks` VALUES (62, 1507, NULL, '', 'active', '2010-01-20 15:57:12', '2010-01-20 15:57:12');
INSERT INTO `pages_blocks` VALUES (63, 1507, NULL, '', 'active', '2010-01-20 15:57:12', '2010-01-20 15:57:12');
INSERT INTO `pages_blocks` VALUES (64, 1507, NULL, '', 'active', '2010-01-20 15:57:12', '2010-01-20 15:57:12');
INSERT INTO `pages_blocks` VALUES (65, 1508, NULL, '', 'active', '2010-01-20 15:57:22', '2010-01-20 15:57:22');
INSERT INTO `pages_blocks` VALUES (66, 1508, NULL, '', 'active', '2010-01-20 15:57:22', '2010-01-20 15:57:22');
INSERT INTO `pages_blocks` VALUES (67, 1508, NULL, '', 'active', '2010-01-20 15:57:22', '2010-01-20 15:57:22');
INSERT INTO `pages_blocks` VALUES (42, 1512, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 16:05:02', '2010-01-20 16:05:02');
INSERT INTO `pages_blocks` VALUES (43, 1512, NULL, '', 'active', '2010-01-20 16:05:02', '2010-01-20 16:05:02');
INSERT INTO `pages_blocks` VALUES (39, 1512, NULL, '<p>ddddd</p>', 'archive', '2010-01-20 16:05:02', '2010-01-20 16:05:02');
INSERT INTO `pages_blocks` VALUES (40, 1512, NULL, '', 'active', '2010-01-20 16:05:02', '2010-01-20 16:05:02');
INSERT INTO `pages_blocks` VALUES (71, 1511, NULL, '', 'active', '2010-01-20 15:57:47', '2010-01-20 15:57:47');
INSERT INTO `pages_blocks` VALUES (72, 1511, NULL, '', 'active', '2010-01-20 15:57:47', '2010-01-20 15:57:47');
INSERT INTO `pages_blocks` VALUES (73, 1511, NULL, '', 'active', '2010-01-20 15:57:47', '2010-01-20 15:57:47');
INSERT INTO `pages_blocks` VALUES (41, 1512, NULL, '', 'active', '2010-01-20 16:05:02', '2010-01-20 16:05:02');
INSERT INTO `pages_blocks` VALUES (74, 1513, NULL, '', 'active', '2010-01-20 16:05:11', '2010-01-20 16:05:11');
INSERT INTO `pages_blocks` VALUES (75, 1513, NULL, '', 'active', '2010-01-20 16:05:11', '2010-01-20 16:05:11');
INSERT INTO `pages_blocks` VALUES (76, 1513, NULL, '', 'active', '2010-01-20 16:05:11', '2010-01-20 16:05:11');
INSERT INTO `pages_blocks` VALUES (77, 1514, NULL, '', 'active', '2010-01-20 16:05:22', '2010-01-20 16:05:22');
INSERT INTO `pages_blocks` VALUES (78, 1514, NULL, '', 'active', '2010-01-20 16:05:22', '2010-01-20 16:05:22');
INSERT INTO `pages_blocks` VALUES (79, 1514, NULL, '', 'active', '2010-01-20 16:05:22', '2010-01-20 16:05:22');
INSERT INTO `pages_blocks` VALUES (80, 1515, NULL, '', 'active', '2010-01-20 16:05:32', '2010-01-20 16:05:32');
INSERT INTO `pages_blocks` VALUES (81, 1515, NULL, '', 'active', '2010-01-20 16:05:32', '2010-01-20 16:05:32');
INSERT INTO `pages_blocks` VALUES (82, 1515, NULL, '', 'active', '2010-01-20 16:05:32', '2010-01-20 16:05:32');
INSERT INTO `pages_blocks` VALUES (59, 1516, NULL, '', 'active', '2010-01-20 16:12:02', '2010-01-20 16:12:02');
INSERT INTO `pages_blocks` VALUES (60, 1516, NULL, '', 'active', '2010-01-20 16:12:02', '2010-01-20 16:12:02');
INSERT INTO `pages_blocks` VALUES (61, 1516, NULL, '', 'active', '2010-01-20 16:12:02', '2010-01-20 16:12:02');
INSERT INTO `pages_blocks` VALUES (83, 1517, NULL, '', 'active', '2010-01-20 16:12:12', '2010-01-20 16:12:12');
INSERT INTO `pages_blocks` VALUES (84, 1517, NULL, '', 'active', '2010-01-20 16:12:12', '2010-01-20 16:12:12');
INSERT INTO `pages_blocks` VALUES (85, 1517, NULL, '', 'active', '2010-01-20 16:12:12', '2010-01-20 16:12:12');
INSERT INTO `pages_blocks` VALUES (86, 1518, NULL, '', 'active', '2010-01-20 16:12:22', '2010-01-20 16:12:22');
INSERT INTO `pages_blocks` VALUES (87, 1518, NULL, '', 'active', '2010-01-20 16:12:22', '2010-01-20 16:12:22');
INSERT INTO `pages_blocks` VALUES (88, 1518, NULL, '', 'active', '2010-01-20 16:12:22', '2010-01-20 16:12:22');
INSERT INTO `pages_blocks` VALUES (89, 1519, NULL, '', 'active', '2010-01-20 16:33:24', '2010-01-20 16:33:24');
INSERT INTO `pages_blocks` VALUES (90, 1519, NULL, '', 'active', '2010-01-20 16:33:24', '2010-01-20 16:33:24');
INSERT INTO `pages_blocks` VALUES (91, 1519, NULL, '', 'active', '2010-01-20 16:33:24', '2010-01-20 16:33:24');
INSERT INTO `pages_blocks` VALUES (92, 1520, NULL, '', 'active', '2010-01-20 16:33:32', '2010-01-20 16:33:32');
INSERT INTO `pages_blocks` VALUES (93, 1520, NULL, '', 'active', '2010-01-20 16:33:32', '2010-01-20 16:33:32');
INSERT INTO `pages_blocks` VALUES (94, 1520, NULL, '', 'active', '2010-01-20 16:33:32', '2010-01-20 16:33:32');
INSERT INTO `pages_blocks` VALUES (95, 1521, NULL, '', 'active', '2010-01-20 16:33:38', '2010-01-20 16:33:38');
INSERT INTO `pages_blocks` VALUES (96, 1521, NULL, '', 'active', '2010-01-20 16:33:38', '2010-01-20 16:33:38');
INSERT INTO `pages_blocks` VALUES (97, 1521, NULL, '', 'active', '2010-01-20 16:33:38', '2010-01-20 16:33:38');
INSERT INTO `pages_blocks` VALUES (98, 1522, NULL, '', 'active', '2010-01-20 16:33:45', '2010-01-20 16:33:45');
INSERT INTO `pages_blocks` VALUES (99, 1522, NULL, '', 'active', '2010-01-20 16:33:45', '2010-01-20 16:33:45');
INSERT INTO `pages_blocks` VALUES (100, 1522, NULL, '', 'active', '2010-01-20 16:33:45', '2010-01-20 16:33:45');
INSERT INTO `pages_blocks` VALUES (101, 1523, NULL, '', 'active', '2010-01-20 16:33:51', '2010-01-20 16:33:51');
INSERT INTO `pages_blocks` VALUES (102, 1523, NULL, '', 'active', '2010-01-20 16:33:51', '2010-01-20 16:33:51');
INSERT INTO `pages_blocks` VALUES (103, 1523, NULL, '', 'active', '2010-01-20 16:33:51', '2010-01-20 16:33:51');
INSERT INTO `pages_blocks` VALUES (104, 1524, NULL, '', 'archive', '2010-01-20 16:34:50', '2010-01-20 16:34:50');
INSERT INTO `pages_blocks` VALUES (105, 1524, NULL, '', 'active', '2010-01-20 16:34:50', '2010-01-20 16:34:50');
INSERT INTO `pages_blocks` VALUES (106, 1524, NULL, '', 'active', '2010-01-20 16:34:50', '2010-01-20 16:34:50');
INSERT INTO `pages_blocks` VALUES (107, 1525, NULL, '', 'active', '2010-01-20 16:35:25', '2010-01-20 16:35:25');
INSERT INTO `pages_blocks` VALUES (108, 1525, NULL, '', 'active', '2010-01-20 16:35:25', '2010-01-20 16:35:25');
INSERT INTO `pages_blocks` VALUES (109, 1525, NULL, '', 'active', '2010-01-20 16:35:25', '2010-01-20 16:35:25');
INSERT INTO `pages_blocks` VALUES (110, 1526, NULL, '', 'active', '2010-01-20 16:35:29', '2010-01-20 16:35:29');
INSERT INTO `pages_blocks` VALUES (111, 1526, NULL, '', 'active', '2010-01-20 16:35:29', '2010-01-20 16:35:29');
INSERT INTO `pages_blocks` VALUES (112, 1526, NULL, '', 'active', '2010-01-20 16:35:29', '2010-01-20 16:35:29');
INSERT INTO `pages_blocks` VALUES (113, 1527, NULL, '', 'active', '2010-01-20 16:35:36', '2010-01-20 16:35:36');
INSERT INTO `pages_blocks` VALUES (114, 1527, NULL, '', 'active', '2010-01-20 16:35:36', '2010-01-20 16:35:36');
INSERT INTO `pages_blocks` VALUES (115, 1527, NULL, '', 'active', '2010-01-20 16:35:36', '2010-01-20 16:35:36');
INSERT INTO `pages_blocks` VALUES (116, 1528, NULL, '', 'active', '2010-01-20 16:35:41', '2010-01-20 16:35:41');
INSERT INTO `pages_blocks` VALUES (117, 1528, NULL, '', 'active', '2010-01-20 16:35:41', '2010-01-20 16:35:41');
INSERT INTO `pages_blocks` VALUES (118, 1528, NULL, '', 'active', '2010-01-20 16:35:41', '2010-01-20 16:35:41');
INSERT INTO `pages_blocks` VALUES (119, 1529, NULL, '', 'active', '2010-01-20 16:35:46', '2010-01-20 16:35:46');
INSERT INTO `pages_blocks` VALUES (120, 1529, NULL, '', 'active', '2010-01-20 16:35:46', '2010-01-20 16:35:46');
INSERT INTO `pages_blocks` VALUES (121, 1529, NULL, '', 'active', '2010-01-20 16:35:46', '2010-01-20 16:35:46');
INSERT INTO `pages_blocks` VALUES (122, 1530, NULL, '', 'active', '2010-01-20 16:35:53', '2010-01-20 16:35:53');
INSERT INTO `pages_blocks` VALUES (123, 1530, NULL, '', 'active', '2010-01-20 16:35:53', '2010-01-20 16:35:53');
INSERT INTO `pages_blocks` VALUES (124, 1530, NULL, '', 'active', '2010-01-20 16:35:53', '2010-01-20 16:35:53');
INSERT INTO `pages_blocks` VALUES (125, 1531, NULL, '', 'archive', '2010-01-20 16:36:02', '2010-01-20 16:36:02');
INSERT INTO `pages_blocks` VALUES (126, 1531, NULL, '', 'active', '2010-01-20 16:36:02', '2010-01-20 16:36:02');
INSERT INTO `pages_blocks` VALUES (127, 1531, NULL, '', 'active', '2010-01-20 16:36:02', '2010-01-20 16:36:02');
INSERT INTO `pages_blocks` VALUES (128, 1532, NULL, '', 'active', '2010-01-20 16:36:07', '2010-01-20 16:36:07');
INSERT INTO `pages_blocks` VALUES (129, 1532, NULL, '', 'active', '2010-01-20 16:36:07', '2010-01-20 16:36:07');
INSERT INTO `pages_blocks` VALUES (130, 1532, NULL, '', 'active', '2010-01-20 16:36:07', '2010-01-20 16:36:07');
INSERT INTO `pages_blocks` VALUES (125, 1533, NULL, '', 'active', '2010-01-20 16:38:30', '2010-01-20 16:38:30');
INSERT INTO `pages_blocks` VALUES (126, 1533, NULL, '', 'active', '2010-01-20 16:38:30', '2010-01-20 16:38:30');
INSERT INTO `pages_blocks` VALUES (127, 1533, NULL, '', 'active', '2010-01-20 16:38:30', '2010-01-20 16:38:30');
INSERT INTO `pages_blocks` VALUES (131, 1534, NULL, '', 'active', '2010-01-20 16:38:36', '2010-01-20 16:38:36');
INSERT INTO `pages_blocks` VALUES (132, 1534, NULL, '', 'active', '2010-01-20 16:38:36', '2010-01-20 16:38:36');
INSERT INTO `pages_blocks` VALUES (133, 1534, NULL, '', 'active', '2010-01-20 16:38:36', '2010-01-20 16:38:36');
INSERT INTO `pages_blocks` VALUES (134, 1535, NULL, '', 'active', '2010-01-20 16:39:12', '2010-01-20 16:39:12');
INSERT INTO `pages_blocks` VALUES (135, 1535, NULL, '', 'active', '2010-01-20 16:39:12', '2010-01-20 16:39:12');
INSERT INTO `pages_blocks` VALUES (136, 1535, NULL, '', 'active', '2010-01-20 16:39:12', '2010-01-20 16:39:12');
INSERT INTO `pages_blocks` VALUES (137, 1536, NULL, '', 'active', '2010-01-20 16:39:50', '2010-01-20 16:39:50');
INSERT INTO `pages_blocks` VALUES (138, 1536, NULL, '', 'active', '2010-01-20 16:39:50', '2010-01-20 16:39:50');
INSERT INTO `pages_blocks` VALUES (139, 1536, NULL, '', 'active', '2010-01-20 16:39:50', '2010-01-20 16:39:50');
INSERT INTO `pages_blocks` VALUES (16, 1777, NULL, '', 'active', '2010-02-09 21:18:10', '2010-02-09 21:18:10');
INSERT INTO `pages_blocks` VALUES (104, 1538, NULL, '', 'archive', '2010-01-26 14:14:17', '2010-01-26 14:14:17');
INSERT INTO `pages_blocks` VALUES (105, 1538, NULL, '', 'active', '2010-01-26 14:14:17', '2010-01-26 14:14:17');
INSERT INTO `pages_blocks` VALUES (106, 1538, NULL, '', 'active', '2010-01-26 14:14:17', '2010-01-26 14:14:17');
INSERT INTO `pages_blocks` VALUES (146, 1538, NULL, '', 'active', '2010-01-26 14:14:17', '2010-01-26 14:14:17');
INSERT INTO `pages_blocks` VALUES (147, 1538, NULL, '', 'active', '2010-01-26 14:14:17', '2010-01-26 14:14:17');
INSERT INTO `pages_blocks` VALUES (104, 1539, NULL, '', 'archive', '2010-01-26 14:14:29', '2010-01-26 14:14:29');
INSERT INTO `pages_blocks` VALUES (105, 1539, NULL, '', 'active', '2010-01-26 14:14:29', '2010-01-26 14:14:29');
INSERT INTO `pages_blocks` VALUES (106, 1539, NULL, '', 'active', '2010-01-26 14:14:29', '2010-01-26 14:14:29');
INSERT INTO `pages_blocks` VALUES (146, 1539, NULL, '', 'active', '2010-01-26 14:14:29', '2010-01-26 14:14:29');
INSERT INTO `pages_blocks` VALUES (147, 1539, NULL, '', 'active', '2010-01-26 14:14:29', '2010-01-26 14:14:29');
INSERT INTO `pages_blocks` VALUES (104, 1540, NULL, '', 'active', '2010-01-26 14:14:40', '2010-01-26 14:14:40');
INSERT INTO `pages_blocks` VALUES (105, 1540, NULL, '', 'active', '2010-01-26 14:14:40', '2010-01-26 14:14:40');
INSERT INTO `pages_blocks` VALUES (106, 1540, NULL, '', 'active', '2010-01-26 14:14:40', '2010-01-26 14:14:40');
INSERT INTO `pages_blocks` VALUES (146, 1540, NULL, '<p>Wat een kouwe kak.</p>', 'active', '2010-01-26 14:14:40', '2010-01-26 14:14:40');
INSERT INTO `pages_blocks` VALUES (147, 1540, NULL, '', 'active', '2010-01-26 14:14:40', '2010-01-26 14:14:40');
INSERT INTO `pages_blocks` VALUES (47, 1541, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-27 12:29:45', '2010-01-27 12:29:45');
INSERT INTO `pages_blocks` VALUES (48, 1541, 1, NULL, 'active', '2010-01-27 12:29:45', '2010-01-27 12:29:45');
INSERT INTO `pages_blocks` VALUES (49, 1541, 2, NULL, 'active', '2010-01-27 12:29:45', '2010-01-27 12:29:45');
INSERT INTO `pages_blocks` VALUES (162, 1779, NULL, '', 'active', '2010-02-09 21:18:29', '2010-02-09 21:18:29');
INSERT INTO `pages_blocks` VALUES (160, 1779, NULL, '', 'archive', '2010-02-09 21:18:29', '2010-02-09 21:18:29');
INSERT INTO `pages_blocks` VALUES (161, 1779, NULL, '', 'active', '2010-02-09 21:18:29', '2010-02-09 21:18:29');
INSERT INTO `pages_blocks` VALUES (19, 1778, NULL, '', 'active', '2010-02-09 21:18:18', '2010-02-09 21:18:18');
INSERT INTO `pages_blocks` VALUES (18, 1778, 5, NULL, 'active', '2010-02-09 21:18:18', '2010-02-09 21:18:18');
INSERT INTO `pages_blocks` VALUES (17, 1778, NULL, '<p>De weg kwijt?</p>', 'archive', '2010-02-09 21:18:18', '2010-02-09 21:18:18');
INSERT INTO `pages_blocks` VALUES (47, 1557, NULL, '<p>Intro tekst over blog</p>', 'active', '2010-01-27 14:57:15', '2010-01-27 14:57:15');
INSERT INTO `pages_blocks` VALUES (48, 1557, 1, NULL, 'active', '2010-01-27 14:57:15', '2010-01-27 14:57:15');
INSERT INTO `pages_blocks` VALUES (49, 1557, 2, NULL, 'active', '2010-01-27 14:57:15', '2010-01-27 14:57:15');
INSERT INTO `pages_blocks` VALUES (157, 1559, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-27 15:01:21', '2010-01-27 15:01:21');
INSERT INTO `pages_blocks` VALUES (158, 1559, 1, NULL, 'archive', '2010-01-27 15:01:21', '2010-01-27 15:01:21');
INSERT INTO `pages_blocks` VALUES (159, 1559, 2, NULL, 'active', '2010-01-27 15:01:21', '2010-01-27 15:01:21');
INSERT INTO `pages_blocks` VALUES (158, 1570, 1, NULL, 'archive', '2010-01-27 15:29:40', '2010-01-27 15:29:40');
INSERT INTO `pages_blocks` VALUES (157, 1570, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-27 15:29:40', '2010-01-27 15:29:40');
INSERT INTO `pages_blocks` VALUES (159, 1570, 2, NULL, 'active', '2010-01-27 15:29:40', '2010-01-27 15:29:40');
INSERT INTO `pages_blocks` VALUES (53, 1563, NULL, '', 'archive', '2010-01-27 15:13:55', '2010-01-27 15:13:55');
INSERT INTO `pages_blocks` VALUES (54, 1563, NULL, '', 'active', '2010-01-27 15:13:55', '2010-01-27 15:13:55');
INSERT INTO `pages_blocks` VALUES (55, 1563, NULL, '', 'active', '2010-01-27 15:13:55', '2010-01-27 15:13:55');
INSERT INTO `pages_blocks` VALUES (158, 1564, 1, NULL, 'archive', '2010-01-27 15:14:45', '2010-01-27 15:14:45');
INSERT INTO `pages_blocks` VALUES (159, 1564, 2, NULL, 'active', '2010-01-27 15:14:45', '2010-01-27 15:14:45');
INSERT INTO `pages_blocks` VALUES (29, 1565, NULL, '<p>AA</p>', 'archive', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (30, 1565, NULL, '<p>BB</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (31, 1565, NULL, '<p>CC</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (32, 1565, NULL, '<p>DD</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (33, 1565, NULL, '<p>EE</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (45, 1566, NULL, '<p>BB</p>', 'archive', '2010-01-27 15:28:23', '2010-01-27 15:28:23');
INSERT INTO `pages_blocks` VALUES (44, 1566, NULL, '<p>AA</p>', 'archive', '2010-01-27 15:28:23', '2010-01-27 15:28:23');
INSERT INTO `pages_blocks` VALUES (46, 1566, NULL, '<p>CC</p>', 'active', '2010-01-27 15:28:23', '2010-01-27 15:28:23');
INSERT INTO `pages_blocks` VALUES (14, 1567, 1, NULL, 'archive', '2010-01-27 15:28:50', '2010-01-27 15:28:50');
INSERT INTO `pages_blocks` VALUES (15, 1567, 2, NULL, 'active', '2010-01-27 15:28:50', '2010-01-27 15:28:50');
INSERT INTO `pages_blocks` VALUES (16, 1567, 3, NULL, 'active', '2010-01-27 15:28:50', '2010-01-27 15:28:50');
INSERT INTO `pages_blocks` VALUES (42, 1568, NULL, '<p>ddddddd</p>', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (43, 1568, NULL, '', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (39, 1568, NULL, '<p>ddddd</p>', 'archive', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (40, 1568, NULL, '', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (41, 1568, NULL, '', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (17, 1569, NULL, '<p>AA</p>', 'archive', '2010-01-27 15:29:06', '2010-01-27 15:29:06');
INSERT INTO `pages_blocks` VALUES (18, 1569, NULL, '<p>BB</p>', 'active', '2010-01-27 15:29:06', '2010-01-27 15:29:06');
INSERT INTO `pages_blocks` VALUES (19, 1569, NULL, '<p>CC</p>', 'active', '2010-01-27 15:29:06', '2010-01-27 15:29:06');
INSERT INTO `pages_blocks` VALUES (160, 1571, NULL, '', 'archive', '2010-01-27 15:41:09', '2010-01-27 15:41:09');
INSERT INTO `pages_blocks` VALUES (161, 1571, NULL, '', 'active', '2010-01-27 15:41:09', '2010-01-27 15:41:09');
INSERT INTO `pages_blocks` VALUES (162, 1571, NULL, '', 'active', '2010-01-27 15:41:09', '2010-01-27 15:41:09');
INSERT INTO `pages_blocks` VALUES (14, 1777, NULL, '', 'active', '2010-02-09 21:18:10', '2010-02-09 21:18:10');
INSERT INTO `pages_blocks` VALUES (15, 1777, 2, NULL, 'active', '2010-02-09 21:18:10', '2010-02-09 21:18:10');
INSERT INTO `pages_blocks` VALUES (43, 1776, NULL, '', 'active', '2010-02-09 21:18:05', '2010-02-09 21:18:05');
INSERT INTO `pages_blocks` VALUES (163, 1574, NULL, '', 'active', '2010-01-28 14:03:54', '2010-01-28 14:03:54');
INSERT INTO `pages_blocks` VALUES (164, 1574, NULL, '', 'active', '2010-01-28 14:03:54', '2010-01-28 14:03:54');
INSERT INTO `pages_blocks` VALUES (165, 1574, NULL, '', 'active', '2010-01-28 14:03:54', '2010-01-28 14:03:54');
INSERT INTO `pages_blocks` VALUES (17, 1575, NULL, '<p>De weg kwijt?</p>', 'archive', '2010-01-28 14:40:06', '2010-01-28 14:40:06');
INSERT INTO `pages_blocks` VALUES (18, 1575, 5, NULL, 'active', '2010-01-28 14:40:06', '2010-01-28 14:40:06');
INSERT INTO `pages_blocks` VALUES (19, 1575, NULL, '', 'active', '2010-01-28 14:40:06', '2010-01-28 14:40:06');
INSERT INTO `pages_blocks` VALUES (41, 1776, NULL, '', 'active', '2010-02-09 21:18:05', '2010-02-09 21:18:05');
INSERT INTO `pages_blocks` VALUES (42, 1776, NULL, '<p>ddddddd</p>', 'active', '2010-02-09 21:18:05', '2010-02-09 21:18:05');
INSERT INTO `pages_blocks` VALUES (40, 1776, NULL, '', 'active', '2010-02-09 21:18:05', '2010-02-09 21:18:05');
INSERT INTO `pages_blocks` VALUES (39, 1776, NULL, '<p>ddddd</p>', 'active', '2010-02-09 21:18:05', '2010-02-09 21:18:05');
INSERT INTO `pages_blocks` VALUES (14, 1578, NULL, '', 'archive', '2010-01-29 13:21:58', '2010-01-29 13:21:58');
INSERT INTO `pages_blocks` VALUES (15, 1578, 2, NULL, 'active', '2010-01-29 13:21:58', '2010-01-29 13:21:58');
INSERT INTO `pages_blocks` VALUES (16, 1578, 3, NULL, 'active', '2010-01-29 13:21:58', '2010-01-29 13:21:58');
INSERT INTO `pages_blocks` VALUES (14, 1579, NULL, '', 'archive', '2010-01-29 13:22:16', '2010-01-29 13:22:16');
INSERT INTO `pages_blocks` VALUES (15, 1579, 2, NULL, 'active', '2010-01-29 13:22:16', '2010-01-29 13:22:16');
INSERT INTO `pages_blocks` VALUES (16, 1579, 3, NULL, 'active', '2010-01-29 13:22:16', '2010-01-29 13:22:16');
INSERT INTO `pages_blocks` VALUES (45, 1580, NULL, '<p>BB</p>', 'archive', '2010-02-01 09:27:44', '2010-02-01 09:27:44');
INSERT INTO `pages_blocks` VALUES (44, 1580, 3, NULL, 'archive', '2010-02-01 09:27:44', '2010-02-01 09:27:44');
INSERT INTO `pages_blocks` VALUES (46, 1580, NULL, '<p>CC</p>', 'active', '2010-02-01 09:27:44', '2010-02-01 09:27:44');
INSERT INTO `pages_blocks` VALUES (45, 1581, 5, NULL, 'archive', '2010-02-01 09:28:10', '2010-02-01 09:28:10');
INSERT INTO `pages_blocks` VALUES (44, 1581, NULL, '<p>BB</p>', 'archive', '2010-02-01 09:28:10', '2010-02-01 09:28:10');
INSERT INTO `pages_blocks` VALUES (46, 1581, NULL, '<p>CC</p>', 'active', '2010-02-01 09:28:10', '2010-02-01 09:28:10');
INSERT INTO `pages_blocks` VALUES (45, 1582, NULL, '<p>Welkom op de homepage van ForkNG</p>', 'archive', '2010-02-01 10:21:53', '2010-02-01 10:21:53');
INSERT INTO `pages_blocks` VALUES (44, 1582, 7, NULL, 'archive', '2010-02-01 10:21:53', '2010-02-01 10:21:53');
INSERT INTO `pages_blocks` VALUES (46, 1582, NULL, '<p>CC</p>', 'active', '2010-02-01 10:21:53', '2010-02-01 10:21:53');
INSERT INTO `pages_blocks` VALUES (45, 1583, NULL, '<p>Welkom op de homepage van ForkNG</p>', 'archive', '2010-02-01 10:22:24', '2010-02-01 10:22:24');
INSERT INTO `pages_blocks` VALUES (44, 1583, 7, NULL, 'archive', '2010-02-01 10:22:24', '2010-02-01 10:22:24');
INSERT INTO `pages_blocks` VALUES (46, 1583, NULL, '<p>De laatste block (c)</p>', 'active', '2010-02-01 10:22:24', '2010-02-01 10:22:24');
INSERT INTO `pages_blocks` VALUES (45, 1584, NULL, '<p>Welkom op de homepage van ForkNG</p>', 'archive', '2010-02-01 11:31:24', '2010-02-01 11:31:24');
INSERT INTO `pages_blocks` VALUES (44, 1584, 7, NULL, 'archive', '2010-02-01 11:31:24', '2010-02-01 11:31:24');
INSERT INTO `pages_blocks` VALUES (46, 1584, 8, NULL, 'active', '2010-02-01 11:31:24', '2010-02-01 11:31:24');
INSERT INTO `pages_blocks` VALUES (45, 1585, NULL, '<p>Welkom op de homepage van ForkNG</p>', 'archive', '2010-02-01 16:37:20', '2010-02-01 16:37:20');
INSERT INTO `pages_blocks` VALUES (44, 1585, 7, NULL, 'archive', '2010-02-01 16:37:20', '2010-02-01 16:37:20');
INSERT INTO `pages_blocks` VALUES (46, 1585, 8, NULL, 'active', '2010-02-01 16:37:20', '2010-02-01 16:37:20');
INSERT INTO `pages_blocks` VALUES (45, 1587, NULL, '<p>Welkom op de homepage van ForkNG</p>', 'archive', '2010-02-03 08:14:39', '2010-02-03 08:14:39');
INSERT INTO `pages_blocks` VALUES (44, 1587, 7, NULL, 'archive', '2010-02-03 08:14:39', '2010-02-03 08:14:39');
INSERT INTO `pages_blocks` VALUES (46, 1587, 8, NULL, 'active', '2010-02-03 08:14:39', '2010-02-03 08:14:39');
INSERT INTO `pages_blocks` VALUES (158, 1588, 1, NULL, 'active', '2010-02-03 08:14:46', '2010-02-03 08:14:46');
INSERT INTO `pages_blocks` VALUES (157, 1588, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-02-03 08:14:46', '2010-02-03 08:14:46');
INSERT INTO `pages_blocks` VALUES (159, 1588, 2, NULL, 'active', '2010-02-03 08:14:46', '2010-02-03 08:14:46');
INSERT INTO `pages_blocks` VALUES (14, 1604, NULL, '', 'archive', '2010-02-03 10:26:46', '2010-02-03 10:26:46');
INSERT INTO `pages_blocks` VALUES (15, 1604, 2, NULL, 'active', '2010-02-03 10:26:46', '2010-02-03 10:26:46');
INSERT INTO `pages_blocks` VALUES (16, 1604, NULL, '', 'active', '2010-02-03 10:26:46', '2010-02-03 10:26:46');
INSERT INTO `pages_blocks` VALUES (17, 1605, NULL, '<p>De weg kwijt?</p>', 'archive', '2010-02-03 10:27:15', '2010-02-03 10:27:15');
INSERT INTO `pages_blocks` VALUES (18, 1605, 5, NULL, 'active', '2010-02-03 10:27:15', '2010-02-03 10:27:15');
INSERT INTO `pages_blocks` VALUES (19, 1605, NULL, '', 'active', '2010-02-03 10:27:15', '2010-02-03 10:27:15');
INSERT INTO `pages_blocks` VALUES (45, 1608, NULL, '<p>Welkom op de homepage van ForkNG</p>', 'archive', '2010-02-03 14:28:42', '2010-02-03 14:28:42');
INSERT INTO `pages_blocks` VALUES (44, 1608, 7, NULL, 'archive', '2010-02-03 14:28:42', '2010-02-03 14:28:42');
INSERT INTO `pages_blocks` VALUES (46, 1608, 8, NULL, 'active', '2010-02-03 14:28:42', '2010-02-03 14:28:42');
INSERT INTO `pages_blocks` VALUES (45, 1609, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>', 'archive', '2010-02-06 16:00:39', '2010-02-06 16:00:39');
INSERT INTO `pages_blocks` VALUES (44, 1609, 7, NULL, 'archive', '2010-02-06 16:00:39', '2010-02-06 16:00:39');
INSERT INTO `pages_blocks` VALUES (46, 1609, 8, NULL, 'active', '2010-02-06 16:00:39', '2010-02-06 16:00:39');
INSERT INTO `pages_blocks` VALUES (45, 1610, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'archive', '2010-02-06 16:11:20', '2010-02-06 16:11:20');
INSERT INTO `pages_blocks` VALUES (44, 1610, 7, NULL, 'archive', '2010-02-06 16:11:20', '2010-02-06 16:11:20');
INSERT INTO `pages_blocks` VALUES (46, 1610, 8, NULL, 'active', '2010-02-06 16:11:20', '2010-02-06 16:11:20');
INSERT INTO `pages_blocks` VALUES (45, 1611, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'archive', '2010-02-08 07:39:01', '2010-02-08 07:39:01');
INSERT INTO `pages_blocks` VALUES (44, 1611, 7, NULL, 'archive', '2010-02-08 07:39:01', '2010-02-08 07:39:01');
INSERT INTO `pages_blocks` VALUES (46, 1611, 8, NULL, 'active', '2010-02-08 07:39:01', '2010-02-08 07:39:01');
INSERT INTO `pages_blocks` VALUES (45, 1612, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'archive', '2010-02-08 08:35:27', '2010-02-08 08:35:27');
INSERT INTO `pages_blocks` VALUES (44, 1612, 7, NULL, 'archive', '2010-02-08 08:35:27', '2010-02-08 08:35:27');
INSERT INTO `pages_blocks` VALUES (46, 1612, 8, NULL, 'active', '2010-02-08 08:35:27', '2010-02-08 08:35:27');
INSERT INTO `pages_blocks` VALUES (45, 1655, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'archive', '2010-02-08 10:56:20', '2010-02-08 10:56:20');
INSERT INTO `pages_blocks` VALUES (44, 1655, 7, NULL, 'archive', '2010-02-08 10:56:20', '2010-02-08 10:56:20');
INSERT INTO `pages_blocks` VALUES (46, 1655, 8, NULL, 'active', '2010-02-08 10:56:20', '2010-02-08 10:56:20');
INSERT INTO `pages_blocks` VALUES (45, 1657, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'archive', '2010-02-08 12:00:38', '2010-02-08 12:00:38');
INSERT INTO `pages_blocks` VALUES (44, 1657, 7, NULL, 'archive', '2010-02-08 12:00:38', '2010-02-08 12:00:38');
INSERT INTO `pages_blocks` VALUES (46, 1657, 8, NULL, 'active', '2010-02-08 12:00:38', '2010-02-08 12:00:38');
INSERT INTO `pages_blocks` VALUES (45, 1659, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'active', '2010-02-08 12:40:53', '2010-02-08 12:40:53');
INSERT INTO `pages_blocks` VALUES (44, 1659, 7, NULL, 'archive', '2010-02-08 12:40:53', '2010-02-08 12:40:53');
INSERT INTO `pages_blocks` VALUES (46, 1659, 8, NULL, 'active', '2010-02-08 12:40:53', '2010-02-08 12:40:53');
INSERT INTO `pages_blocks` VALUES (160, 1780, NULL, '', 'archive', '2010-02-09 21:32:05', '2010-02-09 21:32:05');
INSERT INTO `pages_blocks` VALUES (161, 1780, NULL, '', 'active', '2010-02-09 21:32:05', '2010-02-09 21:32:05');
INSERT INTO `pages_blocks` VALUES (162, 1780, NULL, '', 'active', '2010-02-09 21:32:05', '2010-02-09 21:32:05');
INSERT INTO `pages_blocks` VALUES (160, 1781, NULL, '', 'archive', '2010-02-09 22:23:53', '2010-02-09 22:23:53');
INSERT INTO `pages_blocks` VALUES (161, 1781, NULL, '', 'active', '2010-02-09 22:23:53', '2010-02-09 22:23:53');
INSERT INTO `pages_blocks` VALUES (162, 1781, NULL, '', 'active', '2010-02-09 22:23:53', '2010-02-09 22:23:53');
INSERT INTO `pages_blocks` VALUES (160, 1672, NULL, '', 'archive', '2010-02-08 16:49:35', '2010-02-08 16:49:35');
INSERT INTO `pages_blocks` VALUES (161, 1672, NULL, '', 'active', '2010-02-08 16:49:35', '2010-02-08 16:49:35');
INSERT INTO `pages_blocks` VALUES (162, 1672, NULL, '', 'active', '2010-02-08 16:49:35', '2010-02-08 16:49:35');
INSERT INTO `pages_blocks` VALUES (214, 1757, 9, NULL, 'archive', '2010-02-09 21:07:59', '2010-02-09 21:07:59');
INSERT INTO `pages_blocks` VALUES (215, 1757, NULL, '', 'active', '2010-02-09 21:07:59', '2010-02-09 21:07:59');
INSERT INTO `pages_blocks` VALUES (216, 1758, NULL, '', 'active', '2010-02-09 21:08:01', '2010-02-09 21:08:01');
INSERT INTO `pages_blocks` VALUES (214, 1758, 9, NULL, 'active', '2010-02-09 21:08:01', '2010-02-09 21:08:01');
INSERT INTO `pages_blocks` VALUES (217, 1762, 9, NULL, 'archive', '2010-02-09 21:10:12', '2010-02-09 21:10:12');
INSERT INTO `pages_blocks` VALUES (218, 1762, NULL, '', 'active', '2010-02-09 21:10:12', '2010-02-09 21:10:12');
INSERT INTO `pages_blocks` VALUES (219, 1763, NULL, '', 'active', '2010-02-09 21:10:14', '2010-02-09 21:10:14');
INSERT INTO `pages_blocks` VALUES (217, 1763, 9, NULL, 'archive', '2010-02-09 21:10:14', '2010-02-09 21:10:14');
INSERT INTO `pages_blocks` VALUES (221, 1767, NULL, '', 'active', '2010-02-09 21:12:19', '2010-02-09 21:12:19');
INSERT INTO `pages_blocks` VALUES (222, 1767, NULL, '', 'active', '2010-02-09 21:12:19', '2010-02-09 21:12:19');
INSERT INTO `pages_blocks` VALUES (220, 1768, 9, NULL, 'archive', '2010-02-09 21:12:46', '2010-02-09 21:12:46');
INSERT INTO `pages_blocks` VALUES (221, 1768, NULL, '', 'active', '2010-02-09 21:12:46', '2010-02-09 21:12:46');
INSERT INTO `pages_blocks` VALUES (222, 1768, NULL, '', 'active', '2010-02-09 21:12:46', '2010-02-09 21:12:46');
INSERT INTO `pages_blocks` VALUES (220, 1769, 9, NULL, 'archive', '2010-02-09 21:14:53', '2010-02-09 21:14:53');
INSERT INTO `pages_blocks` VALUES (221, 1769, NULL, '', 'active', '2010-02-09 21:14:53', '2010-02-09 21:14:53');
INSERT INTO `pages_blocks` VALUES (222, 1769, NULL, '', 'active', '2010-02-09 21:14:53', '2010-02-09 21:14:53');
INSERT INTO `pages_blocks` VALUES (220, 1770, 9, NULL, 'active', '2010-02-09 21:15:02', '2010-02-09 21:15:02');
INSERT INTO `pages_blocks` VALUES (221, 1770, NULL, '', 'active', '2010-02-09 21:15:02', '2010-02-09 21:15:02');
INSERT INTO `pages_blocks` VALUES (222, 1770, NULL, '', 'active', '2010-02-09 21:15:02', '2010-02-09 21:15:02');
INSERT INTO `pages_blocks` VALUES (44, 1771, 7, NULL, 'active', '2010-02-09 21:15:35', '2010-02-09 21:15:35');
INSERT INTO `pages_blocks` VALUES (45, 1771, NULL, '<p>Welkom op de <a href="/frontend/files/userfiles/files/svn-book.pdf">homepage</a> van ForkNG</p>\r\n<p><img src="/frontend/files/userfiles/images/me_128x128.jpg" alt="" width="128" height="128" /></p>', 'active', '2010-02-09 21:15:35', '2010-02-09 21:15:35');
INSERT INTO `pages_blocks` VALUES (216, 1756, NULL, '', 'active', '2010-02-09 21:07:32', '2010-02-09 21:07:32');
INSERT INTO `pages_blocks` VALUES (214, 1756, 9, NULL, 'archive', '2010-02-09 21:07:32', '2010-02-09 21:07:32');
INSERT INTO `pages_blocks` VALUES (216, 1757, NULL, '', 'active', '2010-02-09 21:07:59', '2010-02-09 21:07:59');
INSERT INTO `pages_blocks` VALUES (215, 1758, NULL, '', 'active', '2010-02-09 21:08:01', '2010-02-09 21:08:01');
INSERT INTO `pages_blocks` VALUES (55, 1772, NULL, '', 'active', '2010-02-09 21:15:42', '2010-02-09 21:15:42');
INSERT INTO `pages_blocks` VALUES (54, 1772, NULL, '', 'active', '2010-02-09 21:15:42', '2010-02-09 21:15:42');
INSERT INTO `pages_blocks` VALUES (46, 1771, 8, NULL, 'active', '2010-02-09 21:15:35', '2010-02-09 21:15:35');
INSERT INTO `pages_blocks` VALUES (219, 1761, NULL, '', 'active', '2010-02-09 21:10:09', '2010-02-09 21:10:09');
INSERT INTO `pages_blocks` VALUES (217, 1761, 9, NULL, 'archive', '2010-02-09 21:10:09', '2010-02-09 21:10:09');
INSERT INTO `pages_blocks` VALUES (219, 1762, NULL, '', 'active', '2010-02-09 21:10:12', '2010-02-09 21:10:12');
INSERT INTO `pages_blocks` VALUES (218, 1763, NULL, '', 'active', '2010-02-09 21:10:14', '2010-02-09 21:10:14');
INSERT INTO `pages_blocks` VALUES (219, 1764, NULL, '', 'active', '2010-02-09 21:10:16', '2010-02-09 21:10:16');
INSERT INTO `pages_blocks` VALUES (217, 1764, 9, NULL, 'active', '2010-02-09 21:10:16', '2010-02-09 21:10:16');
INSERT INTO `pages_blocks` VALUES (220, 1767, 9, NULL, 'archive', '2010-02-09 21:12:19', '2010-02-09 21:12:19');
INSERT INTO `pages_blocks` VALUES (213, 1752, NULL, '', 'active', '2010-02-09 21:05:09', '2010-02-09 21:05:09');
INSERT INTO `pages_blocks` VALUES (159, 1773, 2, NULL, 'active', '2010-02-09 21:15:47', '2010-02-09 21:15:47');
INSERT INTO `pages_blocks` VALUES (212, 1752, NULL, '', 'active', '2010-02-09 21:05:09', '2010-02-09 21:05:09');
INSERT INTO `pages_blocks` VALUES (211, 1752, 9, NULL, 'archive', '2010-02-09 21:05:09', '2010-02-09 21:05:09');
INSERT INTO `pages_blocks` VALUES (213, 1753, NULL, '', 'active', '2010-02-09 21:05:13', '2010-02-09 21:05:13');
INSERT INTO `pages_blocks` VALUES (211, 1753, 9, NULL, 'active', '2010-02-09 21:05:13', '2010-02-09 21:05:13');
INSERT INTO `pages_blocks` VALUES (158, 1773, 1, NULL, 'active', '2010-02-09 21:15:47', '2010-02-09 21:15:47');
INSERT INTO `pages_blocks` VALUES (215, 1756, NULL, '', 'active', '2010-02-09 21:07:32', '2010-02-09 21:07:32');
INSERT INTO `pages_blocks` VALUES (157, 1773, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-02-09 21:15:47', '2010-02-09 21:15:47');
INSERT INTO `pages_blocks` VALUES (53, 1772, NULL, '', 'active', '2010-02-09 21:15:42', '2010-02-09 21:15:42');
INSERT INTO `pages_blocks` VALUES (218, 1761, NULL, '', 'active', '2010-02-09 21:10:09', '2010-02-09 21:10:09');
INSERT INTO `pages_blocks` VALUES (218, 1764, NULL, '', 'active', '2010-02-09 21:10:16', '2010-02-09 21:10:16');
INSERT INTO `pages_blocks` VALUES (212, 1753, NULL, '', 'active', '2010-02-09 21:05:13', '2010-02-09 21:05:13');
INSERT INTO `pages_blocks` VALUES (160, 1782, NULL, '', 'archive', '2010-02-10 10:48:36', '2010-02-10 10:48:36');
INSERT INTO `pages_blocks` VALUES (161, 1782, NULL, '', 'active', '2010-02-10 10:48:36', '2010-02-10 10:48:36');
INSERT INTO `pages_blocks` VALUES (162, 1782, NULL, '', 'active', '2010-02-10 10:48:36', '2010-02-10 10:48:36');
INSERT INTO `pages_blocks` VALUES (157, 1783, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-02-10 10:58:32', '2010-02-10 10:58:32');
INSERT INTO `pages_blocks` VALUES (158, 1783, 1, NULL, 'active', '2010-02-10 10:58:32', '2010-02-10 10:58:32');
INSERT INTO `pages_blocks` VALUES (159, 1783, 2, NULL, 'active', '2010-02-10 10:58:32', '2010-02-10 10:58:32');
INSERT INTO `pages_blocks` VALUES (157, 1784, 1, NULL, 'active', '2010-02-10 10:59:50', '2010-02-10 10:59:50');
INSERT INTO `pages_blocks` VALUES (158, 1784, NULL, '', 'active', '2010-02-10 10:59:50', '2010-02-10 10:59:50');
INSERT INTO `pages_blocks` VALUES (159, 1784, 2, NULL, 'active', '2010-02-10 10:59:50', '2010-02-10 10:59:50');
INSERT INTO `pages_blocks` VALUES (29, 1785, NULL, '<p>AA</p>', 'archive', '2010-02-10 11:00:35', '2010-02-10 11:00:35');
INSERT INTO `pages_blocks` VALUES (30, 1785, NULL, '<p>BB</p>', 'active', '2010-02-10 11:00:35', '2010-02-10 11:00:35');
INSERT INTO `pages_blocks` VALUES (31, 1785, NULL, '<p>CC</p>', 'active', '2010-02-10 11:00:35', '2010-02-10 11:00:35');
INSERT INTO `pages_blocks` VALUES (32, 1785, NULL, '<p>DD</p>', 'active', '2010-02-10 11:00:35', '2010-02-10 11:00:35');
INSERT INTO `pages_blocks` VALUES (33, 1785, NULL, '<p>EE</p>', 'active', '2010-02-10 11:00:35', '2010-02-10 11:00:35');
INSERT INTO `pages_blocks` VALUES (29, 1786, NULL, '<p>AA</p>', 'archive', '2010-02-10 12:38:19', '2010-02-10 12:38:19');
INSERT INTO `pages_blocks` VALUES (30, 1786, NULL, '<p>BB</p>', 'active', '2010-02-10 12:38:19', '2010-02-10 12:38:19');
INSERT INTO `pages_blocks` VALUES (31, 1786, NULL, '<p>CC</p>', 'active', '2010-02-10 12:38:19', '2010-02-10 12:38:19');
INSERT INTO `pages_blocks` VALUES (32, 1786, NULL, '<p>DD</p>', 'active', '2010-02-10 12:38:19', '2010-02-10 12:38:19');
INSERT INTO `pages_blocks` VALUES (33, 1786, NULL, '<p>EE</p>', 'active', '2010-02-10 12:38:19', '2010-02-10 12:38:19');
INSERT INTO `pages_blocks` VALUES (29, 1787, NULL, '<p>AA</p>', 'active', '2010-02-11 16:14:32', '2010-02-11 16:14:32');
INSERT INTO `pages_blocks` VALUES (30, 1787, NULL, '<p>BB</p>', 'active', '2010-02-11 16:14:32', '2010-02-11 16:14:32');
INSERT INTO `pages_blocks` VALUES (31, 1787, NULL, '<p>CC</p>', 'active', '2010-02-11 16:14:32', '2010-02-11 16:14:32');
INSERT INTO `pages_blocks` VALUES (32, 1787, NULL, '<p>DD</p>', 'active', '2010-02-11 16:14:32', '2010-02-11 16:14:32');
INSERT INTO `pages_blocks` VALUES (33, 1787, NULL, '<p>EE</p>', 'active', '2010-02-11 16:14:32', '2010-02-11 16:14:32');
INSERT INTO `pages_blocks` VALUES (17, 1788, NULL, '<p>De weg kwijt?</p>', 'archive', '2010-02-13 16:56:09', '2010-02-13 16:56:09');
INSERT INTO `pages_blocks` VALUES (18, 1788, 5, NULL, 'active', '2010-02-13 16:56:09', '2010-02-13 16:56:09');
INSERT INTO `pages_blocks` VALUES (19, 1788, NULL, '', 'active', '2010-02-13 16:56:09', '2010-02-13 16:56:09');
INSERT INTO `pages_blocks` VALUES (223, 1789, NULL, '', 'archive', '2010-02-13 16:57:04', '2010-02-13 16:57:04');
INSERT INTO `pages_blocks` VALUES (224, 1789, 6, NULL, 'active', '2010-02-13 16:57:04', '2010-02-13 16:57:04');
INSERT INTO `pages_blocks` VALUES (225, 1789, NULL, '', 'active', '2010-02-13 16:57:04', '2010-02-13 16:57:04');
INSERT INTO `pages_blocks` VALUES (223, 1790, NULL, '', 'archive', '2010-02-13 17:11:44', '2010-02-13 17:11:44');
INSERT INTO `pages_blocks` VALUES (224, 1790, NULL, '', 'active', '2010-02-13 17:11:44', '2010-02-13 17:11:44');
INSERT INTO `pages_blocks` VALUES (225, 1790, NULL, '', 'active', '2010-02-13 17:11:44', '2010-02-13 17:11:44');
INSERT INTO `pages_blocks` VALUES (223, 1791, NULL, '', 'archive', '2010-02-13 17:11:54', '2010-02-13 17:11:54');
INSERT INTO `pages_blocks` VALUES (224, 1791, NULL, '', 'active', '2010-02-13 17:11:54', '2010-02-13 17:11:54');
INSERT INTO `pages_blocks` VALUES (225, 1791, NULL, '', 'active', '2010-02-13 17:11:54', '2010-02-13 17:11:54');
INSERT INTO `pages_blocks` VALUES (17, 1792, NULL, '<p>De weg kwijt?</p>', 'active', '2010-02-13 17:13:37', '2010-02-13 17:13:37');
INSERT INTO `pages_blocks` VALUES (18, 1792, 5, NULL, 'active', '2010-02-13 17:13:37', '2010-02-13 17:13:37');
INSERT INTO `pages_blocks` VALUES (19, 1792, NULL, '', 'active', '2010-02-13 17:13:37', '2010-02-13 17:13:37');
INSERT INTO `pages_blocks` VALUES (223, 1793, NULL, '', 'active', '2010-02-13 17:13:58', '2010-02-13 17:13:58');
INSERT INTO `pages_blocks` VALUES (224, 1793, 6, NULL, 'active', '2010-02-13 17:13:58', '2010-02-13 17:13:58');
INSERT INTO `pages_blocks` VALUES (225, 1793, NULL, '', 'active', '2010-02-13 17:13:58', '2010-02-13 17:13:58');
INSERT INTO `pages_blocks` VALUES (160, 1794, NULL, '', 'active', '2010-02-13 22:05:28', '2010-02-13 22:05:28');
INSERT INTO `pages_blocks` VALUES (161, 1794, 8, NULL, 'active', '2010-02-13 22:05:28', '2010-02-13 22:05:28');
INSERT INTO `pages_blocks` VALUES (162, 1794, NULL, '', 'active', '2010-02-13 22:05:28', '2010-02-13 22:05:28');

-- --------------------------------------------------------

-- 
-- Table structure for table `pages_extras`
-- 

DROP TABLE IF EXISTS `pages_extras`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `pages_extras`
-- 

INSERT INTO `pages_extras` VALUES (1, 'blog', 'block', 'Blog', NULL, NULL, 'N', 1000);
INSERT INTO `pages_extras` VALUES (2, 'blog', 'widget', 'RecentComments', 'recent_comments', NULL, 'N', 1001);
INSERT INTO `pages_extras` VALUES (8, 'contentblocks', 'widget', 'Snippets', 'detail', 'a:2:{s:11:"extra_label";s:24:"Dit is de tweede widget.";s:2:"id";i:2;}', 'N', 2002);
INSERT INTO `pages_extras` VALUES (5, 'sitemap', 'block', 'Sitemap', NULL, NULL, 'N', 3);
INSERT INTO `pages_extras` VALUES (6, 'contact', 'block', 'ContactForm', NULL, NULL, 'N', 2);
INSERT INTO `pages_extras` VALUES (7, 'contentblocks', 'widget', 'Snippets', 'detail', 'a:2:{s:11:"extra_label";s:32:"Een eerste snippet, maar bewerkt";s:2:"id";i:1;}', 'N', 2001);
INSERT INTO `pages_extras` VALUES (9, 'tags', 'block', 'Tags', NULL, NULL, 'N', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `pages_groups`
-- 

DROP TABLE IF EXISTS `pages_groups`;
CREATE TABLE IF NOT EXISTS `pages_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `label` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  `path` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  `num_blocks` int(11) NOT NULL default '1' COMMENT 'The number of blocks used in the template.',
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
  `is_default` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'Is this the default template.',
  `data` text collate utf8_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates' AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `pages_templates`
-- 

INSERT INTO `pages_templates` VALUES (1, 'home', 'core/layout/templates/home.tpl', 3, 'Y', 'N', 'a:2:{s:6:"format";s:14:"[0,1],[2,none]";s:5:"names";a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}}');
INSERT INTO `pages_templates` VALUES (2, 'content', 'core/layout/templates/index.tpl', 5, 'Y', 'Y', 'a:2:{s:6:"format";s:39:"[0,1],[2,3:selected,none],[4,none,none]";s:5:"names";a:5:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;s:1:"d";i:4;s:1:"e";}}');

-- --------------------------------------------------------

-- 
-- Table structure for table `profiles`
-- 

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) collate utf8_unicode_ci NOT NULL,
  `password` varchar(255) collate utf8_unicode_ci NOT NULL,
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  `blocked` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `registered_on` datetime NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
  `session_id` varchar(255) collate utf8_unicode_ci NOT NULL,
  `secret_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`profile_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `profiles_settings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `contentblocks`
-- 

DROP TABLE IF EXISTS `contentblocks`;
CREATE TABLE IF NOT EXISTS `contentblocks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` varchar(10) collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `content` text collate utf8_unicode_ci,
  `hidden` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `status` enum('active','archived') collate utf8_unicode_ci NOT NULL default 'active',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sometimes we need editable parts in the templates, this modu' AUTO_INCREMENT=32 ;

-- 
-- Dumping data for table `contentblocks`
-- 

INSERT INTO `contentblocks` VALUES (1, 28, 1, 'nl', 'Een eerste snippet', '<p>Dit is de eerste snippet...</p>\r\n<p>De inhoud van dit item kan aan meerdere pagina''s gekoppeld worden.</p>', 'N', 'archived', '2010-02-01 10:20:47', '2010-02-01 10:20:47');
INSERT INTO `contentblocks` VALUES (1, 29, 1, 'nl', 'Een eerste snippet, maar bewerkt', '<p>Dit is de eerste snippet...</p>\r\n<p>De inhoud van dit item kan aan meerdere pagina''s gekoppeld worden.</p>', 'N', 'archived', '2010-02-01 10:20:47', '2010-02-01 11:30:17');
INSERT INTO `contentblocks` VALUES (2, 30, 1, 'nl', 'Dit is de tweede widget.', '<p>Deze gaan we verwijderen</p>', 'N', 'active', '2010-02-01 11:31:03', '2010-02-01 11:31:03');
INSERT INTO `contentblocks` VALUES (1, 31, 1, 'nl', 'Een eerste snippet, maar bewerkt', '<p>Dit is de eerste snippet...</p>\r\n<p>De inhoud van dit item kan aan meerdere pagina''s gekoppeld worden.</p>', 'N', 'active', '2010-02-01 10:20:47', '2010-02-08 16:41:08');

-- --------------------------------------------------------

-- 
-- Table structure for table `tags`
-- 

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `tags`
-- 

INSERT INTO `tags` VALUES (1, 'nl', 'kladversie', 1, 'kladversie');
INSERT INTO `tags` VALUES (2, 'nl', 'voorbeeld', 1, 'voorbeeld');

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `username` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'username, will be case-sensitive',
  `password` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'will be case-sensitive',
  `active` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT 'is this user active?',
  `deleted` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'is the user deleted?',
  `is_god` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The backend users' AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 1, 'tijs', 'b45a8670f0f9d65aef1512516b97c12f43c62fcd', 'Y', 'N', 'Y');
INSERT INTO `users` VALUES (6, 1, 'dave', '12961055474222b28d6885c3622e06879edc47b3', 'Y', 'N', 'N');
INSERT INTO `users` VALUES (5, 1, 'Wolfr', 'ae2b1fca515949e5d54fb22b8ed95575', 'Y', 'N', 'N');
INSERT INTO `users` VALUES (7, 1, 'bauffman', '6a0711fc48e846b7af71d99b4b01a1e33d9e0d3c', 'Y', 'N', 'N');
INSERT INTO `users` VALUES (9, 1, 'pim', 'c0a320562a03c5c23df5ad930f397bfa643d8c07', 'N', 'Y', 'N');

-- --------------------------------------------------------

-- 
-- Table structure for table `users_sessions`
-- 

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) collate utf8_unicode_ci NOT NULL,
  `secret_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_session_id_secret_key` (`session_id`(100),`secret_key`(100))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=51 ;

-- 
-- Dumping data for table `users_sessions`
-- 

INSERT INTO `users_sessions` VALUES (48, 1, 'dc0608842bc778f876e8e3c1c79fdf02', 'f31821ce1bab693ad2a05b029312724e1e9696ce', '2010-02-14 20:36:32');
INSERT INTO `users_sessions` VALUES (50, 1, '1ad5fd080eeb9bf3e601b96da65d4b51', '714fa806555bf0869ba2628c3feba477b212b822', '2010-02-14 21:13:26');
INSERT INTO `users_sessions` VALUES (49, 1, '1ad5fd080eeb9bf3e601b96da65d4b51', '714fa806555bf0869ba2628c3feba477b212b822', '2010-02-14 20:47:33');

-- --------------------------------------------------------

-- 
-- Table structure for table `users_settings`
-- 

DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE IF NOT EXISTS `users_settings` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text collate utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `users_settings`
-- 

INSERT INTO `users_settings` VALUES (1, 'avatar', 's:7:"1_1.jpg";');
INSERT INTO `users_settings` VALUES (1, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (1, 'date_long_format', 's:11:"d/m/Y H:i:s";');
INSERT INTO `users_settings` VALUES (1, 'edit', 's:8:"bewerken";');
INSERT INTO `users_settings` VALUES (1, 'email', 's:16:"tijs@verkoyen.eu";');
INSERT INTO `users_settings` VALUES (1, 'form', 's:4:"edit";');
INSERT INTO `users_settings` VALUES (1, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (1, 'name', 's:4:"Tijs";');
INSERT INTO `users_settings` VALUES (1, 'nickname', 's:13:"Tijs Verkoyen";');
INSERT INTO `users_settings` VALUES (1, 'surname', 's:8:"Verkoyen";');
INSERT INTO `users_settings` VALUES (3, 'avatar', 's:13:"no-avatar.gif";');
INSERT INTO `users_settings` VALUES (0, 'nickname', 'N;');
INSERT INTO `users_settings` VALUES (1, 'password_key', 's:13:"4b557b2d8aef1";');
INSERT INTO `users_settings` VALUES (0, 'password_key', 'N;');
INSERT INTO `users_settings` VALUES (2, 'form', 's:3:"add";');
INSERT INTO `users_settings` VALUES (2, 'nickname', 's:5:"Wolfr";');
INSERT INTO `users_settings` VALUES (2, 'email', 's:22:"johan.ronsse@gmail.com";');
INSERT INTO `users_settings` VALUES (2, 'name', 's:5:"Johan";');
INSERT INTO `users_settings` VALUES (2, 'surname', 's:6:"Ronsse";');
INSERT INTO `users_settings` VALUES (2, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (2, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (2, 'password_key', 's:13:"4b60476c1a6cf";');
INSERT INTO `users_settings` VALUES (3, 'form', 's:3:"add";');
INSERT INTO `users_settings` VALUES (3, 'nickname', 's:4:"test";');
INSERT INTO `users_settings` VALUES (3, 'email', 's:12:"test@test.be";');
INSERT INTO `users_settings` VALUES (3, 'name', 's:4:"test";');
INSERT INTO `users_settings` VALUES (3, 'surname', 's:4:"test";');
INSERT INTO `users_settings` VALUES (3, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (3, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (3, 'password_key', 's:13:"4b604872ed340";');
INSERT INTO `users_settings` VALUES (4, 'form', 's:3:"add";');
INSERT INTO `users_settings` VALUES (4, 'nickname', 's:5:"test2";');
INSERT INTO `users_settings` VALUES (4, 'email', 's:22:"johan.ronsse@gmail.com";');
INSERT INTO `users_settings` VALUES (4, 'name', 's:5:"test2";');
INSERT INTO `users_settings` VALUES (4, 'surname', 's:5:"test2";');
INSERT INTO `users_settings` VALUES (4, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (4, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (4, 'password_key', 's:13:"4b604df98f72b";');
INSERT INTO `users_settings` VALUES (4, 'avatar', 's:13:"no-avatar.gif";');
INSERT INTO `users_settings` VALUES (5, 'form', 's:3:"add";');
INSERT INTO `users_settings` VALUES (5, 'nickname', 's:0:"";');
INSERT INTO `users_settings` VALUES (5, 'email', 's:24:"wolf@wolfslittlestore.be";');
INSERT INTO `users_settings` VALUES (5, 'name', 's:5:"Johan";');
INSERT INTO `users_settings` VALUES (5, 'surname', 's:6:"Ronsse";');
INSERT INTO `users_settings` VALUES (5, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (5, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (5, 'password_key', 's:13:"4b6053661b24a";');
INSERT INTO `users_settings` VALUES (5, 'avatar', 's:13:"no-avatar.gif";');
INSERT INTO `users_settings` VALUES (6, 'form', 's:4:"edit";');
INSERT INTO `users_settings` VALUES (6, 'nickname', 's:4:"dave";');
INSERT INTO `users_settings` VALUES (6, 'email', 's:16:"dave@netlash.com";');
INSERT INTO `users_settings` VALUES (6, 'name', 's:4:"Dave";');
INSERT INTO `users_settings` VALUES (6, 'surname', 's:4:"Lens";');
INSERT INTO `users_settings` VALUES (6, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (6, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (6, 'password_key', 's:13:"4b6056c2f37ef";');
INSERT INTO `users_settings` VALUES (6, 'avatar', 's:7:"1_6.jpg";');
INSERT INTO `users_settings` VALUES (7, 'form', 's:4:"edit";');
INSERT INTO `users_settings` VALUES (7, 'nickname', 's:8:"Bauffman";');
INSERT INTO `users_settings` VALUES (6, 'edit', 's:8:"bewerken";');
INSERT INTO `users_settings` VALUES (6, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (7, 'email', 's:16:"davy@netlash.com";');
INSERT INTO `users_settings` VALUES (7, 'name', 's:4:"Davy";');
INSERT INTO `users_settings` VALUES (7, 'surname', 's:9:"Hellemans";');
INSERT INTO `users_settings` VALUES (7, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (7, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (7, 'password_key', 's:13:"4b6147a83e80f";');
INSERT INTO `users_settings` VALUES (7, 'avatar', 's:7:"2_7.jpg";');
INSERT INTO `users_settings` VALUES (7, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (7, 'date_long_format', 's:11:"d/m/Y H:i:s";');
INSERT INTO `users_settings` VALUES (7, 'edit', 's:8:"bewerken";');
INSERT INTO `users_settings` VALUES (6, 'date_long_format', 's:11:"d/m/Y H:i:s";');
INSERT INTO `users_settings` VALUES (0, 'avatar', 'N;');
INSERT INTO `users_settings` VALUES (6, 'new_password', 's:0:"";');
INSERT INTO `users_settings` VALUES (6, 'confirm_password', 's:0:"";');
INSERT INTO `users_settings` VALUES (6, 'active', 'b:1;');
INSERT INTO `users_settings` VALUES (6, 'group', 's:1:"1";');
INSERT INTO `users_settings` VALUES (8, 'form', 's:3:"add";');
INSERT INTO `users_settings` VALUES (8, 'confirm_password', 's:8:"internet";');
INSERT INTO `users_settings` VALUES (8, 'nickname', 's:14:"Joris Verkoyen";');
INSERT INTO `users_settings` VALUES (8, 'email', 's:17:"joris@verkoyen.eu";');
INSERT INTO `users_settings` VALUES (8, 'name', 's:5:"Joris";');
INSERT INTO `users_settings` VALUES (8, 'surname', 's:8:"Verkoyen";');
INSERT INTO `users_settings` VALUES (8, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (8, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (8, 'password_key', 's:13:"4b682e27d18a8";');
INSERT INTO `users_settings` VALUES (8, 'avatar', 's:13:"no-avatar.gif";');
INSERT INTO `users_settings` VALUES (8, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (9, 'form', 's:3:"add";');
INSERT INTO `users_settings` VALUES (9, 'confirm_password', 's:8:"internet";');
INSERT INTO `users_settings` VALUES (9, 'nickname', 's:3:"Pim";');
INSERT INTO `users_settings` VALUES (9, 'email', 's:15:"pim@verkoyen.eu";');
INSERT INTO `users_settings` VALUES (9, 'name', 's:3:"Pim";');
INSERT INTO `users_settings` VALUES (9, 'surname', 's:8:"Verkoyen";');
INSERT INTO `users_settings` VALUES (9, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (9, 'active', 'b:1;');
INSERT INTO `users_settings` VALUES (9, 'group', 's:1:"1";');
INSERT INTO `users_settings` VALUES (9, 'add', 's:19:"Gebruiker toevoegen";');
INSERT INTO `users_settings` VALUES (9, 'password_key', 's:13:"4b682f7bd409a";');
INSERT INTO `users_settings` VALUES (9, 'avatar', 's:13:"no-avatar.gif";');
INSERT INTO `users_settings` VALUES (9, 'backend_interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (1, 'new_password', 's:0:"";');
INSERT INTO `users_settings` VALUES (1, 'confirm_password', 's:0:"";');
INSERT INTO `users_settings` VALUES (1, 'active', 'b:0;');
INSERT INTO `users_settings` VALUES (1, 'group', 's:1:"1";');
INSERT INTO `users_settings` VALUES (5, 'reset_password_key', 's:40:"16f980f3d9b31424204b3acd079adcfd0308f1d1";');
INSERT INTO `users_settings` VALUES (5, 'reset_password_timestamp', 'i:1265617245;');
INSERT INTO `users_settings` VALUES (1, 'reset_password_key', 's:40:"532027a5518142e5773b842e2a24a9baaf4e170a";');
INSERT INTO `users_settings` VALUES (1, 'reset_password_timestamp', 'i:1265834358;');
