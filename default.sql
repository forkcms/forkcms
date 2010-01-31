-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 31, 2010 at 01:58 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `blog_categories`
-- 

INSERT INTO `blog_categories` VALUES (1, 'nl', 'test', 'test');
INSERT INTO `blog_categories` VALUES (2, 'nl', 'uw moeder heeft géén viskes gebákken.', 'uw-moeder-heeft-geen-viskes-gebakken');
INSERT INTO `blog_categories` VALUES (3, 'nl', 'en ik kan al terug sáven!', 'en-ik-kan-al-terug-saven');
INSERT INTO `blog_categories` VALUES (5, 'nl', 'haha', 'haha');
INSERT INTO `blog_categories` VALUES (7, 'nl', 'development', 'development');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=55 ;

-- 
-- Dumping data for table `blog_comments`
-- 

INSERT INTO `blog_comments` VALUES (13, 3, '2010-01-12 16:27:08', 'Bauffman', 'erik@bauffman.be', 'http://blog.bauffman.be', 'Wat een zinloze discussie over terminologie eigenlijk... Ik maak trouwens sowieso nooit een volledig ontwerp in Photoshop of Illustrator. Mijn mock-ups maak ik in illustrator (betere measure-tools dan Photoshop imo) en dan ga ik rechtstreeks naar de browser. Van slicen is er dus niet echt sprake.\r\n\r\nIk mis in het artikel eigenlijk één belangrijk punt: probeer je klant zo te begeleiden dat hij je problemen voorschotelt, eerder dan oplossingen. Verder een goeie round-up, al kan geen enkel designproces in strikte stappen opgedeeld worden natuurlijk.', 'comment', 'moderation', NULL);
INSERT INTO `blog_comments` VALUES (16, 3, '2010-01-12 16:27:08', 'Bauffman', 'erik@bauffman.be', 'http://blog.bauffman.be', 'Wat een zinloze discussie over terminologie eigenlijk... Ik maak trouwens sowieso nooit een volledig ontwerp in Photoshop of Illustrator. Mijn mock-ups maak ik in illustrator (betere measure-tools dan Photoshop imo) en dan ga ik rechtstreeks naar de browser. Van slicen is er dus niet echt sprake.\r\n\r\nIk mis in het artikel eigenlijk één belangrijk punt: probeer je klant zo te begeleiden dat hij je problemen voorschotelt, eerder dan oplossingen. Verder een goeie round-up, al kan geen enkel designproces in strikte stappen opgedeeld worden natuurlijk.', 'comment', 'published', NULL);
INSERT INTO `blog_comments` VALUES (45, 3, '2010-01-29 13:38:36', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Praten met de klant? Oké, maar dan maak je wel iets voor de klant.\r\n\r\nMoet je geen websites maken voor de klanten van de klant en dus onderzoeken wat die doen en willen?\r\n\r\nVolgens mij kan je geen goede informatiearchitectuur maken, zonder dat aspect grondig te onderzoeken. Zoals Michel zegt, geldt dat eerder voor de wat grotere websites.\r\n\r\nNuttig artikel over informatiearchitectuur is misschien: http://usability-blog.be/informatiearchitectuur-wat-waarom-hoe/ ', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:58:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs";s:11:"HTTP_COOKIE";s:191:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984";s:18:"HTTP_CACHE_CONTROL";s:9:"max-age=0";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"621";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51298";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:0:"";s:11:"REQUEST_URI";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264768716;s:4:"argv";a:0:{}s:4:"argc";i:0;}}');
INSERT INTO `blog_comments` VALUES (46, 3, '2010-01-29 13:49:44', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Praten met de klant? Oké, maar dan maak je wel iets voor de klant. Moet je geen websites maken voor de klanten van de klant en dus onderzoeken wat die doen en willen? Volgens mij kan je geen goede informatiearchitectuur maken, zonder dat aspect grondig te onderzoeken. Zoals Michel zegt, geldt dat eerder voor de wat grotere websites. Nuttig artikel over informatiearchitectuur is misschien: http://usability-blog.be/informatiearchitectuur-wat-waarom-hoe/\r\n\r\nMaar dan nu nr2', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:71:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"HTTP_COOKIE";s:191:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"614";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51364";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264769384;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (47, 3, '2010-01-29 13:49:58', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Praten met de klant? Oké, maar dan maak je wel iets voor de klant. Moet je geen websites maken voor de klanten van de klant en dus onderzoeken wat die doen en willen? Volgens mij kan je geen goede informatiearchitectuur maken, zonder dat aspect grondig te onderzoeken. Zoals Michel zegt, geldt dat eerder voor de wat grotere websites. Nuttig artikel over informatiearchitectuur is misschien: http://usability-blog.be/informatiearchitectuur-wat-waarom-hoe/\r\n\r\nMaar dan nu nr2', 'comment', 'published', 'a:1:{s:6:"server";a:37:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:71:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"HTTP_COOKIE";s:191:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984";s:18:"HTTP_CACHE_CONTROL";s:9:"max-age=0";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"614";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51364";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264769398;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (48, 3, '2010-01-29 13:51:04', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Praten met de klant? Oké, maar dan maak je wel iets voor de klant. Moet je geen websites maken voor de klanten van de klant en dus onderzoeken wat die doen en willen? Volgens mij kan je geen goede informatiearchitectuur maken, zonder dat aspect grondig te onderzoeken. Zoals Michel zegt, geldt dat eerder voor de wat grotere websites. Nuttig artikel over informatiearchitectuur is misschien: http://usability-blog.be/informatiearchitectuur-wat-waarom-hoe/\r\n\r\nMaar dan nu nr2', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:18:"HTTP_CACHE_CONTROL";s:9:"max-age=0";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"614";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51373";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264769464;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (49, 3, '2010-01-29 13:51:47', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Praten met de klant? Oké, maar dan maak je wel iets voor de klant. Moet je geen websites maken voor de klanten van de klant en dus onderzoeken wat die doen en willen? Volgens mij kan je geen goede informatiearchitectuur maken, zonder dat aspect grondig te onderzoeken. Zoals Michel zegt, geldt dat eerder voor de wat grotere websites. Nuttig artikel over informatiearchitectuur is misschien: http://usability-blog.be/informatiearchitectuur-wat-waarom-hoe/\r\n\r\nMaar dan nu nr2', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:18:"HTTP_CACHE_CONTROL";s:9:"max-age=0";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"614";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51379";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264769507;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (50, 3, '2010-01-29 13:54:36', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Praten met de klant? Oké, maar dan maak je wel iets voor de klant. Moet je geen websites maken voor de klanten van de klant en dus onderzoeken wat die doen en willen? Volgens mij kan je geen goede informatiearchitectuur maken, zonder dat aspect grondig te onderzoeken. Zoals Michel zegt, geldt dat eerder voor de wat grotere websites. Nuttig artikel over informatiearchitectuur is misschien: http://usability-blog.be/informatiearchitectuur-wat-waarom-hoe/\r\n\r\nMaar dan nu nr2', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:18:"HTTP_CACHE_CONTROL";s:9:"max-age=0";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"614";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51385";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264769676;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (51, 3, '2010-01-29 14:00:24', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', '♪♪♪♪♪♪♪♪♪♪♪\r\nblablabla', 'comment', 'published', 'a:1:{s:6:"server";a:35:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:58:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"219";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51748";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:0:"";s:11:"REQUEST_URI";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264770024;s:4:"argv";a:0:{}s:4:"argc";i:0;}}');
INSERT INTO `blog_comments` VALUES (52, 3, '2010-01-29 14:02:48', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', '♪♪♪♪♪♪♪\r\n♪\r\n♪\r\n\r\n\r\n♪\r\n♪\r\n\r\n\r\n♪\r\n♪♪♪♪\r\n♪♪', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:71:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"333";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51756";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264770168;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (53, 3, '2010-01-29 14:03:39', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'Hallo, ik ben dave.be', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:71:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"128";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51764";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264770219;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');
INSERT INTO `blog_comments` VALUES (54, 3, '2010-01-29 14:04:45', 'Tijs', 'tijs@verkoyen.eu', 'http://blog.verkoyen.eu', 'hallo dave.be/mekker?homo=true\r\n\r\nmekker.be\r\n\r\nmekker.mekker\r\n\r\nhttp://mongool.idioot\r\n\r\nhttp://mongool.nu\r\n\r\n', 'comment', 'published', 'a:1:{s:6:"server";a:36:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:12:"forkng.local";s:15:"HTTP_USER_AGENT";s:95:"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7";s:11:"HTTP_ACCEPT";s:63:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";s:20:"HTTP_ACCEPT_LANGUAGE";s:2:"en";s:20:"HTTP_ACCEPT_ENCODING";s:12:"gzip,deflate";s:19:"HTTP_ACCEPT_CHARSET";s:30:"ISO-8859-1,utf-8;q=0.7,*;q=0.7";s:15:"HTTP_KEEP_ALIVE";s:3:"300";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:12:"HTTP_REFERER";s:71:"http://forkng.local/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"HTTP_COOKIE";s:346:"frontend_language=s%3A2%3A%22nl%22%3B; cookie_id=s%3A32%3A%22b4554d36e5aa3a8143eb7f6c5d69386d%22%3B; backend_interface_language=s%3A2%3A%22nl%22%3B; PHPSESSID=f50f3bdb395715e4bedab411cd048984; comment_author=s%3A4%3A%22Tijs%22%3B; comment_email=s%3A16%3A%22tijs%40verkoyen.eu%22%3B; comment_website=s%3A23%3A%22http%3A%2F%2Fblog.verkoyen.eu%22%3B";s:12:"CONTENT_TYPE";s:33:"application/x-www-form-urlencoded";s:14:"CONTENT_LENGTH";s:3:"273";s:4:"PATH";s:71:"/bin:/sbin:/usr/bin:/usr/sbin:/usr/libexec:/System/Library/CoreServices";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:12:"forkng.local";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:9:"127.0.0.1";s:13:"DOCUMENT_ROOT";s:42:"/Users/tijs/Sites/forkng.local/default_www";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:52:"/Users/tijs/Sites/forkng.local/default_www/index.php";s:11:"REMOTE_PORT";s:5:"51773";s:21:"REDIRECT_QUERY_STRING";s:12:"comment=true";s:12:"REDIRECT_URL";s:39:"/nl/blog/detail/afblijven-t-is-van-tijs";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:12:"comment=true";s:11:"REQUEST_URI";s:52:"/nl/blog/detail/afblijven-t-is-van-tijs?comment=true";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:12:"REQUEST_TIME";i:1264770284;s:4:"argv";a:1:{i:0;s:12:"comment=true";}s:4:"argc";i:1;}}');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=69 ;

-- 
-- Dumping data for table `blog_posts`
-- 

INSERT INTO `blog_posts` VALUES (1, 60, 2, 1, 279, 'nl', 'Dit is een blogpost', '', '<p>dfgsdf</p>', 'archived', '2010-01-27 18:16:00', '2010-01-28 14:19:29', '2010-01-29 10:22:58', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (3, 41, 1, 1, 273, 'nl', 'Afblijven, &#039;t is van Tijs', '<p>Ik ben de inhoud</p>', '<p>Ik ben de inhoud</p>', 'archived', '2010-01-28 16:44:00', '2010-01-28 16:45:41', '2010-01-28 17:16:48', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (3, 42, 1, 1, 273, 'nl', 'Afblijven, &#039;t is van Tijs', '<p>Ik ben de inhoud</p>', '<p>Ik ben de inhoud</p>', 'archived', '2010-01-28 16:44:00', '2010-01-28 16:45:41', '2010-01-28 17:17:20', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (3, 43, 1, 1, 273, 'nl', 'Afblijven, &#039;t is van Tijs', '<p>Ik ben de inhoud</p>', '<p>Ik ben de inhoud</p>', 'archived', '2010-01-28 16:44:00', '2010-01-28 16:45:41', '2010-01-28 17:17:21', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (3, 44, 1, 1, 275, 'nl', 'Afblijven, &#039;t is van Tijs', '<p>Ik ben de inhoud</p>', '<p>Ik ben de inhoud</p>', 'archived', '2010-01-28 16:44:00', '2010-01-28 16:45:41', '2010-01-28 17:20:03', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (3, 45, 1, 1, 273, 'nl', 'Afblijven, &#039;t is van Tijs', '<p>Ik ben de inhoud</p>', '<p>Ik ben de inhoud</p>', 'archived', '2010-01-28 16:44:00', '2010-01-28 16:45:41', '2010-01-28 17:21:48', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (1, 62, 2, 1, 279, 'nl', 'Dit is een blogpost', '', '<p>dfgsdf</p>', 'archived', '2010-01-27 18:16:00', '2010-01-28 14:19:29', '2010-01-29 10:32:54', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (1, 63, 2, 1, 279, 'nl', 'Dit is een blogpost', '<p>sdfasdfs</p>', '<p>dfgsdf</p>', 'archived', '2010-01-27 18:16:00', '2010-01-28 14:19:29', '2010-01-29 10:33:03', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (1, 64, 2, 1, 279, 'nl', 'Dit is een blogpost', '', '<p>dfgsdf</p>', 'active', '2010-01-27 18:16:00', '2010-01-28 14:19:29', '2010-01-29 10:33:49', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (1, 61, 2, 1, 279, 'nl', 'Dit is een blogpost', '<p>sdfsadf</p>', '<p>dfgsdf</p>', 'archived', '2010-01-27 18:16:00', '2010-01-28 14:19:29', '2010-01-29 10:31:53', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (3, 65, 1, 1, 273, 'nl', 'Afblijven, &#039;t is van Tijs', '<p>Ik ben de inhoud</p>', '<p>Ik ben de inhoud</p>', 'active', '2010-01-28 16:44:00', '2010-01-28 16:45:41', '2010-01-29 12:56:37', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (1, 59, 2, 1, 279, 'nl', 'Dit is een blogpost', '<p>dsafdsf</p>', '<p>dfgsdf</p>', 'archived', '2010-01-27 18:16:00', '2010-01-28 14:19:29', '2010-01-29 10:19:19', 'N', 'Y', 0);
INSERT INTO `blog_posts` VALUES (4, 4, 1, 1, 282, 'nl', 'nr 3', '', '<p>ddd</p>', 'active', '2010-01-30 12:25:00', '2010-01-30 12:26:01', '2010-01-30 12:26:01', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (5, 5, 1, 1, 283, 'nl', 'nr4', '', '<p>nr4</p>', 'active', '2010-01-30 12:26:00', '2010-01-30 12:26:15', '2010-01-30 12:26:15', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (6, 6, 1, 1, 284, 'nl', 'nr 5', '', '<p>5</p>', 'active', '2010-01-30 12:28:00', '2010-01-30 12:28:14', '2010-01-30 12:28:14', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (7, 7, 1, 1, 285, 'nl', 'nr 6', '', '<p>dd</p>', 'active', '2010-01-30 12:28:00', '2010-01-30 12:28:26', '2010-01-30 12:28:26', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (8, 8, 1, 1, 286, 'nl', 'nr 7', '', '<p>7</p>', 'active', '2010-01-30 12:28:00', '2010-01-30 12:28:37', '2010-01-30 12:28:37', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (9, 9, 1, 1, 287, 'nl', 'nr 8', '', '<p>8</p>', 'active', '2010-01-30 12:28:00', '2010-01-30 12:28:48', '2010-01-30 12:28:48', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (10, 10, 1, 1, 288, 'nl', 'nr 9', '', '<p>9</p>', 'active', '2010-01-30 12:28:00', '2010-01-30 12:29:02', '2010-01-30 12:29:02', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (11, 11, 1, 1, 289, 'nl', 'nr 10', '', '<p>10</p>', 'active', '2010-01-30 12:29:00', '2010-01-30 12:29:18', '2010-01-30 12:29:18', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (12, 12, 1, 1, 290, 'nl', 'nr 11', '', '<p>11</p>', 'active', '2010-01-30 12:29:00', '2010-01-30 12:29:31', '2010-01-30 12:29:31', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (13, 13, 1, 1, 292, 'nl', 'nr 12', '', '<p>12</p>', 'active', '2010-01-30 12:30:00', '2010-01-30 12:30:11', '2010-01-30 12:30:11', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (14, 14, 1, 1, 293, 'nl', 'nr 13', '', '<p>13</p>', 'active', '2010-01-30 12:30:00', '2010-01-30 12:30:26', '2010-01-30 12:30:26', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (15, 15, 1, 1, 294, 'nl', 'nr 14', '', '<p>14</p>', 'active', '2010-01-30 12:30:00', '2010-01-30 12:30:37', '2010-01-30 12:30:37', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (16, 16, 1, 1, 295, 'nl', 'nr 15', '', '<p>15</p>', 'active', '2010-01-30 12:30:00', '2010-01-30 12:30:47', '2010-01-30 12:30:47', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (17, 17, 1, 1, 296, 'nl', 'nr 16', '', '<p>16</p>', 'active', '2010-01-30 12:59:00', '2010-01-30 13:00:03', '2010-01-30 13:00:03', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (18, 18, 1, 1, 297, 'nl', 'nr 17', '', '<p>17</p>', 'archived', '2010-01-30 13:00:00', '2010-01-30 13:00:13', '2010-01-30 13:00:13', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (19, 19, 1, 1, 298, 'nl', 'nr 18', '', '<p>18</p>', 'active', '2010-01-30 13:00:00', '2010-01-30 13:00:22', '2010-01-30 13:00:22', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (20, 20, 1, 1, 299, 'nl', 'nr 19', '', '<p>19</p>', 'active', '2010-01-30 13:00:00', '2010-01-30 13:00:36', '2010-01-30 13:00:36', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (21, 21, 1, 1, 300, 'nl', 'nr 20', '', '<p>20</p>', 'active', '2010-01-30 13:00:00', '2010-01-30 13:00:47', '2010-01-30 13:00:47', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (18, 66, 1, 1, 297, 'nl', 'nr 17', '', '<ul>\r\n<li>een <a href="/nl/blog">interne</a> link</li>\r\n<li>een <a href="http://www.netlash.com">externe</a> link</li>\r\n<li>een eigen afbeelding<br /><img src="/files/backend_users/avatars/source/no-avatar.jpg" alt="" /></li>\r\n<li>een extern afbeelding<br /><img src="http://static.netlash.be/modules/core/layout/images/logo.gif" alt="" width="138" height="68" /></li>\r\n</ul>', 'archived', '2010-01-30 13:00:00', '2010-01-30 13:00:13', '2010-01-30 14:18:57', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (18, 67, 1, 1, 297, 'nl', 'nr 17', '', '<ul>\r\n<li>een <a href="/nl/blog">interne</a> link</li>\r\n<li>een <a href="http://www.netlash.com">externe</a> link</li>\r\n<li>een eigen afbeelding<br /><img src="/frontend/files/backend_users/avatars/source/no-avatar.jpg" alt="" /></li>\r\n<li>een extern afbeelding<br /><img src="http://static.netlash.be/modules/core/layout/images/logo.gif" alt="" width="138" height="68" /></li>\r\n</ul>', 'archived', '2010-01-30 13:00:00', '2010-01-30 13:00:13', '2010-01-30 14:20:24', 'N', 'N', 0);
INSERT INTO `blog_posts` VALUES (18, 68, 1, 1, 297, 'nl', 'nr 17', '', '<ul>\r\n<li>een <a href="/nl/blog">interne</a> link</li>\r\n<li>een <a href="http://www.netlash.com">externe</a> link</li>\r\n<li>een eigen afbeelding<br /><img src="/frontend/files/backend_users/avatars/source/no-avatar.jpg" alt="" /></li>\r\n<li>een extern afbeelding<br /><img src="http://static.netlash.be/modules/core/layout/images/logo.gif" alt="" width="138" height="68" /></li>\r\n</ul>', 'active', '2010-01-30 13:00:00', '2010-01-30 13:00:13', '2010-01-31 12:44:00', 'N', 'N', 0);

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
  `send_on` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=54 ;

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

-- --------------------------------------------------------

-- 
-- Table structure for table `groups_rights_modules`
-- 

DROP TABLE IF EXISTS `groups_rights_modules`;
CREATE TABLE IF NOT EXISTS `groups_rights_modules` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `module` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

-- 
-- Dumping data for table `groups_rights_modules`
-- 

INSERT INTO `groups_rights_modules` VALUES (1, 1, 'dashboard');
INSERT INTO `groups_rights_modules` VALUES (3, 1, 'users');
INSERT INTO `groups_rights_modules` VALUES (6, 1, 'pages');
INSERT INTO `groups_rights_modules` VALUES (7, 1, 'snippets');
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
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `application` varchar(255) collate utf8_unicode_ci NOT NULL,
  `module` varchar(255) collate utf8_unicode_ci NOT NULL,
  `type` enum('act','err','lbl','msg') collate utf8_unicode_ci NOT NULL default 'lbl',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=326 ;

-- 
-- Dumping data for table `locale`
-- 

INSERT INTO `locale` VALUES (34, 'nl', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key werd nog niet geconfigureerd.');
INSERT INTO `locale` VALUES (33, 'nl', 'backend', 'core', 'lbl', 'Settings', 'instellingen');
INSERT INTO `locale` VALUES (32, 'nl', 'backend', 'core', 'lbl', 'Value', 'waarde');
INSERT INTO `locale` VALUES (31, 'nl', 'backend', 'core', 'lbl', 'Name', 'naam');
INSERT INTO `locale` VALUES (30, 'nl', 'backend', 'core', 'lbl', 'Type', 'type');
INSERT INTO `locale` VALUES (29, 'nl', 'backend', 'core', 'lbl', 'Module', 'module');
INSERT INTO `locale` VALUES (28, 'nl', 'backend', 'core', 'lbl', 'Application', 'applicatie');
INSERT INTO `locale` VALUES (27, 'nl', 'backend', 'core', 'lbl', 'Language', 'taal');
INSERT INTO `locale` VALUES (26, 'nl', 'backend', 'core', 'lbl', 'Edit', 'bewerken');
INSERT INTO `locale` VALUES (25, 'nl', 'frontend', 'core', 'lbl', 'YouAreHere', 'je bent hier');
INSERT INTO `locale` VALUES (24, 'nl', 'frontend', 'core', 'lbl', 'RecentComments', 'recente reacties');
INSERT INTO `locale` VALUES (23, 'nl', 'frontend', 'core', 'lbl', 'CommentedOn', 'reageerde op');
INSERT INTO `locale` VALUES (22, 'nl', 'frontend', 'core', 'act', 'Detail', 'detail');
INSERT INTO `locale` VALUES (35, 'nl', 'backend', 'core', 'lbl', 'Save', 'opslaan');
INSERT INTO `locale` VALUES (36, 'nl', 'backend', 'core', 'err', 'BlogRSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%s" class="button"><span><span><span>Configureer</span></span></span></a>');
INSERT INTO `locale` VALUES (37, 'nl', 'backend', 'core', 'lbl', 'Locale', 'locale');
INSERT INTO `locale` VALUES (38, 'nl', 'backend', 'core', 'lbl', 'AddLabel', 'label toevoegen');
INSERT INTO `locale` VALUES (39, 'nl', 'backend', 'core', 'lbl', 'Active', 'actief');
INSERT INTO `locale` VALUES (40, 'nl', 'backend', 'core', 'lbl', 'ActiveUsers', 'actieve gebruikers');
INSERT INTO `locale` VALUES (41, 'nl', 'backend', 'core', 'lbl', 'Add', 'toevoegen');
INSERT INTO `locale` VALUES (42, 'nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen');
INSERT INTO `locale` VALUES (43, 'nl', 'backend', 'users', 'lbl', 'Add', 'gebruiker toevoegen');
INSERT INTO `locale` VALUES (44, 'nl', 'backend', 'core', 'lbl', 'AddCategory', 'categorie toevoegen');
INSERT INTO `locale` VALUES (45, 'nl', 'backend', 'core', 'lbl', 'AddTemplate', 'template toevoegen');
INSERT INTO `locale` VALUES (46, 'nl', 'backend', 'core', 'lbl', 'All', 'alle');
INSERT INTO `locale` VALUES (47, 'nl', 'backend', 'blog', 'lbl', 'AllowComments', 'reacties toestaan');
INSERT INTO `locale` VALUES (48, 'nl', 'backend', 'blog', 'lbl', 'AllPosts', 'alle posts');
INSERT INTO `locale` VALUES (49, 'nl', 'backend', 'core', 'lbl', 'Amount', 'aantal');
INSERT INTO `locale` VALUES (50, 'nl', 'backend', 'core', 'lbl', 'APIKey', 'API key');
INSERT INTO `locale` VALUES (51, 'nl', 'backend', 'core', 'lbl', 'APIKeys', 'API keys');
INSERT INTO `locale` VALUES (52, 'nl', 'backend', 'core', 'lbl', 'APIURL', 'API URL');
INSERT INTO `locale` VALUES (53, 'nl', 'backend', 'core', 'lbl', 'Archived', 'gearchiveerd');
INSERT INTO `locale` VALUES (54, 'nl', 'backend', 'core', 'lbl', 'At', 'om');
INSERT INTO `locale` VALUES (55, 'nl', 'backend', 'core', 'lbl', 'Avatar', 'avatar');
INSERT INTO `locale` VALUES (56, 'nl', 'backend', 'core', 'lbl', 'Author', 'auteur');
INSERT INTO `locale` VALUES (57, 'nl', 'backend', 'core', 'lbl', 'Back', 'terug');
INSERT INTO `locale` VALUES (58, 'nl', 'backend', 'core', 'lbl', 'Blog', 'blog');
INSERT INTO `locale` VALUES (59, 'nl', 'backend', 'core', 'lbl', 'By', 'door');
INSERT INTO `locale` VALUES (60, 'nl', 'backend', 'core', 'lbl', 'Cancel', 'annuleer');
INSERT INTO `locale` VALUES (61, 'nl', 'backend', 'core', 'lbl', 'Category', 'categorie');
INSERT INTO `locale` VALUES (62, 'nl', 'backend', 'core', 'lbl', 'Categories', 'categorieën');
INSERT INTO `locale` VALUES (63, 'nl', 'backend', 'core', 'lbl', 'CheckCommentsForSpam', 'filter reacties op spam');
INSERT INTO `locale` VALUES (64, 'nl', 'backend', 'core', 'lbl', 'Comment', 'reactie');
INSERT INTO `locale` VALUES (65, 'nl', 'backend', 'core', 'lbl', 'Comments', 'reacties');
INSERT INTO `locale` VALUES (66, 'nl', 'backend', 'core', 'lbl', 'Content', 'inhoud');
INSERT INTO `locale` VALUES (67, 'nl', 'backend', 'pages', 'lbl', 'Core', 'algemeen');
INSERT INTO `locale` VALUES (68, 'nl', 'backend', 'core', 'lbl', 'CustomURL', 'aangepaste URL');
INSERT INTO `locale` VALUES (69, 'nl', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard');
INSERT INTO `locale` VALUES (70, 'nl', 'backend', 'core', 'lbl', 'Date', 'datum');
INSERT INTO `locale` VALUES (71, 'nl', 'backend', 'core', 'lbl', 'Default', 'standaard');
INSERT INTO `locale` VALUES (72, 'nl', 'backend', 'core', 'lbl', 'Delete', 'verwijderen');
INSERT INTO `locale` VALUES (73, 'nl', 'backend', 'core', 'lbl', 'DeleteTag', 'verwijder deze tag');
INSERT INTO `locale` VALUES (74, 'nl', 'backend', 'core', 'lbl', 'Description', 'beschrijving');
INSERT INTO `locale` VALUES (75, 'nl', 'backend', 'core', 'lbl', 'Developer', 'developer');
INSERT INTO `locale` VALUES (76, 'nl', 'backend', 'core', 'lbl', 'Domains', 'domeinen');
INSERT INTO `locale` VALUES (77, 'nl', 'backend', 'core', 'lbl', 'Draft', 'draft');
INSERT INTO `locale` VALUES (78, 'nl', 'backend', 'core', 'lbl', 'Dutch', 'Nederlands');
INSERT INTO `locale` VALUES (79, 'nl', 'backend', 'core', 'lbl', 'English', 'engels');
INSERT INTO `locale` VALUES (80, 'nl', 'backend', 'core', 'lbl', 'Editor', 'editor');
INSERT INTO `locale` VALUES (81, 'nl', 'backend', 'core', 'lbl', 'EditTemplate', 'template bewerken');
INSERT INTO `locale` VALUES (82, 'nl', 'backend', 'core', 'lbl', 'Email', 'e-mail');
INSERT INTO `locale` VALUES (83, 'nl', 'backend', 'core', 'lbl', 'EmailWebmaster', 'e-mail webmaster');
INSERT INTO `locale` VALUES (84, 'nl', 'backend', 'core', 'lbl', 'Execute', 'uitvoeren');
INSERT INTO `locale` VALUES (85, 'nl', 'backend', 'core', 'lbl', 'Extra', 'extra');
INSERT INTO `locale` VALUES (86, 'nl', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL');
INSERT INTO `locale` VALUES (87, 'nl', 'backend', 'core', 'lbl', 'Footer', 'footer');
INSERT INTO `locale` VALUES (88, 'nl', 'backend', 'core', 'lbl', 'French', 'frans');
INSERT INTO `locale` VALUES (89, 'nl', 'backend', 'core', 'lbl', 'Hidden', 'verborgen');
INSERT INTO `locale` VALUES (90, 'nl', 'backend', 'core', 'lbl', 'Home', 'home');
INSERT INTO `locale` VALUES (91, 'nl', 'backend', 'core', 'lbl', 'InterfaceLanguage', 'interface-taal');
INSERT INTO `locale` VALUES (92, 'nl', 'backend', 'core', 'lbl', 'Keywords', 'zoekwoorden');
INSERT INTO `locale` VALUES (93, 'nl', 'backend', 'core', 'lbl', 'Languages', 'talen');
INSERT INTO `locale` VALUES (94, 'nl', 'backend', 'core', 'lbl', 'LastEditedOn', 'laatst aangepast op');
INSERT INTO `locale` VALUES (95, 'nl', 'backend', 'core', 'lbl', 'LastSave', 'laatst bewaard');
INSERT INTO `locale` VALUES (96, 'nl', 'backend', 'core', 'lbl', 'Loading', 'laden');
INSERT INTO `locale` VALUES (97, 'nl', 'backend', 'core', 'lbl', 'Login', 'login');
INSERT INTO `locale` VALUES (98, 'nl', 'backend', 'core', 'lbl', 'Logout', 'afmelden');
INSERT INTO `locale` VALUES (99, 'nl', 'backend', 'core', 'lbl', 'MainNavigation', 'hoofdnavigatie');
INSERT INTO `locale` VALUES (100, 'nl', 'backend', 'pages', 'lbl', 'Meta', 'meta-navigatie');
INSERT INTO `locale` VALUES (101, 'nl', 'backend', 'core', 'lbl', 'MetaCustom', 'meta custom');
INSERT INTO `locale` VALUES (102, 'nl', 'backend', 'core', 'lbl', 'MetaDescription', 'meta-omschrijving');
INSERT INTO `locale` VALUES (103, 'nl', 'backend', 'core', 'lbl', 'MetaInformation', 'meta-informatie');
INSERT INTO `locale` VALUES (104, 'nl', 'backend', 'core', 'lbl', 'MetaKeywords', 'sleutelwoorden pagina');
INSERT INTO `locale` VALUES (105, 'nl', 'backend', 'core', 'lbl', 'MoveToModeration', 'verplaats naar moderatie');
INSERT INTO `locale` VALUES (106, 'nl', 'backend', 'core', 'lbl', 'MoveToPublished', 'verplaats naar gepubliceerd');
INSERT INTO `locale` VALUES (107, 'nl', 'backend', 'core', 'lbl', 'MoveToSpam', 'verplaats naar spam');
INSERT INTO `locale` VALUES (108, 'nl', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigatie titel');
INSERT INTO `locale` VALUES (109, 'nl', 'backend', 'core', 'lbl', 'NewPassword', 'nieuw wachtwoord');
INSERT INTO `locale` VALUES (110, 'nl', 'backend', 'core', 'lbl', 'News', 'nieuws');
INSERT INTO `locale` VALUES (111, 'nl', 'backend', 'core', 'lbl', 'Next', 'volgende');
INSERT INTO `locale` VALUES (112, 'nl', 'backend', 'core', 'lbl', 'NextPage', 'volgende pagina');
INSERT INTO `locale` VALUES (113, 'nl', 'backend', 'core', 'lbl', 'Nickname', 'nickname');
INSERT INTO `locale` VALUES (114, 'nl', 'backend', 'core', 'lbl', 'OK', 'ok');
INSERT INTO `locale` VALUES (115, 'nl', 'backend', 'core', 'lbl', 'Page', 'pagina');
INSERT INTO `locale` VALUES (116, 'nl', 'backend', 'core', 'lbl', 'Pages', 'pagina''s');
INSERT INTO `locale` VALUES (117, 'nl', 'backend', 'core', 'lbl', 'Password', 'wachtwoord');
INSERT INTO `locale` VALUES (118, 'nl', 'backend', 'core', 'lbl', 'PageTitle', 'paginatitel');
INSERT INTO `locale` VALUES (119, 'nl', 'backend', 'core', 'lbl', 'Permissions', 'rechten');
INSERT INTO `locale` VALUES (120, 'nl', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices');
INSERT INTO `locale` VALUES (121, 'nl', 'backend', 'blog', 'lbl', 'Posts', 'posts');
INSERT INTO `locale` VALUES (122, 'nl', 'backend', 'core', 'lbl', 'PostsInThisCategory', 'posts in deze categorie');
INSERT INTO `locale` VALUES (123, 'nl', 'backend', 'core', 'lbl', 'Preview', 'preview');
INSERT INTO `locale` VALUES (124, 'nl', 'backend', 'core', 'lbl', 'Previous', 'vorige');
INSERT INTO `locale` VALUES (125, 'nl', 'backend', 'core', 'lbl', 'PreviousPage', 'vorige pagina');
INSERT INTO `locale` VALUES (126, 'nl', 'backend', 'core', 'lbl', 'Publish', 'publiceer');
INSERT INTO `locale` VALUES (127, 'nl', 'backend', 'core', 'lbl', 'PublishOn', 'publiceer op');
INSERT INTO `locale` VALUES (128, 'nl', 'backend', 'core', 'lbl', 'Published', 'gepubliceerd');
INSERT INTO `locale` VALUES (129, 'nl', 'backend', 'core', 'lbl', 'PublishedOn', 'gepubliceerd op');
INSERT INTO `locale` VALUES (130, 'nl', 'backend', 'core', 'lbl', 'PublishedComments', 'gepubliceerde reacties');
INSERT INTO `locale` VALUES (131, 'nl', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recent aangepast');
INSERT INTO `locale` VALUES (132, 'nl', 'backend', 'core', 'lbl', 'Referrers', 'referrers');
INSERT INTO `locale` VALUES (133, 'nl', 'backend', 'core', 'lbl', 'RepeatPassword', 'herhaal wachtwoord');
INSERT INTO `locale` VALUES (134, 'nl', 'backend', 'core', 'lbl', 'RequiredField', 'verplicht veld');
INSERT INTO `locale` VALUES (135, 'nl', 'backend', 'core', 'lbl', 'Revisions', 'versies');
INSERT INTO `locale` VALUES (136, 'nl', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed');
INSERT INTO `locale` VALUES (137, 'nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina''s');
INSERT INTO `locale` VALUES (138, 'nl', 'backend', 'core', 'lbl', 'Scripts', 'scripts');
INSERT INTO `locale` VALUES (139, 'nl', 'backend', 'core', 'lbl', 'Send', 'verzenden');
INSERT INTO `locale` VALUES (140, 'nl', 'backend', 'core', 'lbl', 'Security', 'beveiliging');
INSERT INTO `locale` VALUES (141, 'nl', 'backend', 'core', 'lbl', 'SEO', 'SEO');
INSERT INTO `locale` VALUES (142, 'nl', 'backend', 'core', 'lbl', 'SignIn', 'aanmelden');
INSERT INTO `locale` VALUES (143, 'nl', 'backend', 'core', 'lbl', 'SignOut', 'afmelden');
INSERT INTO `locale` VALUES (144, 'nl', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap');
INSERT INTO `locale` VALUES (145, 'nl', 'backend', 'core', 'lbl', 'SortAscending', 'sorteerd oplopend');
INSERT INTO `locale` VALUES (146, 'nl', 'backend', 'core', 'lbl', 'SortDescending', 'sorteer aflopend');
INSERT INTO `locale` VALUES (147, 'nl', 'backend', 'core', 'lbl', 'SortedAscending', 'oplopend gesorteerd');
INSERT INTO `locale` VALUES (148, 'nl', 'backend', 'core', 'lbl', 'SortedDescending', 'aflopend gesorteerd');
INSERT INTO `locale` VALUES (149, 'nl', 'backend', 'core', 'lbl', 'Snippets', 'snippets');
INSERT INTO `locale` VALUES (150, 'nl', 'backend', 'core', 'lbl', 'Spam', 'spam');
INSERT INTO `locale` VALUES (151, 'nl', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter');
INSERT INTO `locale` VALUES (152, 'nl', 'backend', 'core', 'lbl', 'Status', 'status');
INSERT INTO `locale` VALUES (153, 'nl', 'backend', 'core', 'lbl', 'Submit', 'verzenden');
INSERT INTO `locale` VALUES (154, 'nl', 'backend', 'core', 'lbl', 'Surname', 'achternaam');
INSERT INTO `locale` VALUES (155, 'nl', 'backend', 'core', 'lbl', 'Tag', 'tag');
INSERT INTO `locale` VALUES (156, 'nl', 'backend', 'core', 'lbl', 'Tags', 'tags');
INSERT INTO `locale` VALUES (157, 'nl', 'backend', 'core', 'lbl', 'Template', 'template');
INSERT INTO `locale` VALUES (158, 'nl', 'backend', 'core', 'lbl', 'Time', 'tijd');
INSERT INTO `locale` VALUES (159, 'nl', 'backend', 'core', 'lbl', 'Title', 'titel');
INSERT INTO `locale` VALUES (160, 'nl', 'backend', 'core', 'lbl', 'Titles', 'titels');
INSERT INTO `locale` VALUES (161, 'nl', 'backend', 'core', 'lbl', 'Update', 'wijzig');
INSERT INTO `locale` VALUES (162, 'nl', 'backend', 'core', 'lbl', 'URL', 'url');
INSERT INTO `locale` VALUES (163, 'nl', 'backend', 'core', 'lbl', 'Userguide', 'gebruikersgids');
INSERT INTO `locale` VALUES (164, 'nl', 'backend', 'core', 'lbl', 'Username', 'gebruikersnaam');
INSERT INTO `locale` VALUES (165, 'nl', 'backend', 'core', 'lbl', 'User', 'gebruiker');
INSERT INTO `locale` VALUES (166, 'nl', 'backend', 'core', 'lbl', 'Users', 'gebruikers');
INSERT INTO `locale` VALUES (167, 'nl', 'backend', 'core', 'lbl', 'UseThisVersion', 'gebruik deze versie');
INSERT INTO `locale` VALUES (168, 'nl', 'backend', 'core', 'lbl', 'Versions', 'versies');
INSERT INTO `locale` VALUES (169, 'nl', 'backend', 'core', 'lbl', 'Visible', 'zichtbaar');
INSERT INTO `locale` VALUES (170, 'nl', 'backend', 'core', 'lbl', 'WaitingForModeration', 'wachten op moderatie');
INSERT INTO `locale` VALUES (171, 'nl', 'backend', 'core', 'lbl', 'Websites', 'websites');
INSERT INTO `locale` VALUES (172, 'nl', 'backend', 'core', 'lbl', 'WebsiteTitle', 'website titel');
INSERT INTO `locale` VALUES (173, 'nl', 'backend', 'core', 'lbl', 'WithSelected', 'met geselecteerde');
INSERT INTO `locale` VALUES (174, 'nl', 'backend', 'pages', 'msg', 'HelpAdd', '&larr; Kies een pagina uit de boomstructuur om deze te bewerken of');
INSERT INTO `locale` VALUES (175, 'nl', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activeer <code>rel="nofollow"</code>');
INSERT INTO `locale` VALUES (176, 'nl', 'backend', 'core', 'msg', 'Added', 'item toegevoegd.');
INSERT INTO `locale` VALUES (177, 'nl', 'backend', 'users', 'msg', 'Added', 'Gebruiker ''%s'' toegevoegd.');
INSERT INTO `locale` VALUES (178, 'nl', 'backend', 'settings', 'msg', 'ApiKeysText', 'Toegangscodes voor gebruikte webservices');
INSERT INTO `locale` VALUES (179, 'nl', 'backend', 'core', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:');
INSERT INTO `locale` VALUES (180, 'nl', 'backend', 'core', 'msg', 'ConfirmDelete', 'Ben je zeker dat je dit item wil verwijderen?');
INSERT INTO `locale` VALUES (181, 'nl', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de pagina ''%s'' wil verwijderen?');
INSERT INTO `locale` VALUES (182, 'nl', 'backend', 'users', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de gebruiker ''%s'' wil verwijderen?');
INSERT INTO `locale` VALUES (183, 'nl', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Ben je zeker dat je de categorie ''%s'' wil verwijderen?');
INSERT INTO `locale` VALUES (184, 'nl', 'backend', 'core', 'msg', 'Deleted', 'Het item is verwijderd.');
INSERT INTO `locale` VALUES (185, 'nl', 'backend', 'users', 'msg', 'Deleted', 'De gebruiker ''%s'' is verwijderd.');
INSERT INTO `locale` VALUES (186, 'nl', 'backend', 'settings', 'msg', 'DomainsText', 'Vul de domeinen in waarop de website te bereiken is (1 domein per regel)');
INSERT INTO `locale` VALUES (187, 'nl', 'backend', 'core', 'msg', 'Edited', 'Wijzigingen opgeslagen.');
INSERT INTO `locale` VALUES (188, 'nl', 'backend', 'users', 'msg', 'Edited', 'Wijzigingen voor gebruiker ''%s'' opgeslagen.');
INSERT INTO `locale` VALUES (189, 'nl', 'backend', 'core', 'msg', 'ForgotPassword', 'Wachtwoord vergeten?');
INSERT INTO `locale` VALUES (190, 'nl', 'backend', 'core', 'msg', 'ForgotPasswordHelp', 'Vul hieronder je e-mail adres in om een nieuw wachtwoord toegestuurd te krijgen.');
INSERT INTO `locale` VALUES (191, 'nl', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Stuur notificaties van het CMS naar dit e-mailadres.');
INSERT INTO `locale` VALUES (192, 'nl', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'bijv. http://feeds.feedburner.com/netlog');
INSERT INTO `locale` VALUES (193, 'nl', 'backend', 'pages', 'msg', 'HelpPageTitle', 'De titel die in het venster van de browser staat (<code>&lt;title&gt;</code>).');
INSERT INTO `locale` VALUES (194, 'nl', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'Als de paginatitel te lang is om in het menu te passen, geef dan een verkorte titel in.');
INSERT INTO `locale` VALUES (195, 'nl', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Zorgt ervoor dat deze pagina de interne PageRank niet be');
INSERT INTO `locale` VALUES (196, 'nl', 'backend', 'core', 'msg', 'HelpMetaCustom', 'Laat toe om extra, op maat gemaakte metatags toe te voegen.');
INSERT INTO `locale` VALUES (197, 'nl', 'backend', 'core', 'msg', 'HelpMetaDescription', 'De pagina-omschrijving die wordt getoond in de resultaten van zoekmachines. Hou het kort en krachtig.');
INSERT INTO `locale` VALUES (198, 'nl', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'De sleutelwoorden (keywords) die deze pagina omschrijven.');
INSERT INTO `locale` VALUES (199, 'nl', 'backend', 'core', 'msg', 'HelpMetaURL', 'Vervang de automatisch gegenereerde URL door een zelfgekozen URL.');
INSERT INTO `locale` VALUES (200, 'nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.');
INSERT INTO `locale` VALUES (201, 'nl', 'backend', 'core', 'msg', 'HelpRevisions', 'De laatst opgeslagen versies worden hier bijgehouden. ''Gebruik deze versie'' opent een vroegere versie. De huidige versie wordt pas overschreven als je opslaat.');
INSERT INTO `locale` VALUES (202, 'nl', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Beschrijf bondig wat voor soort inhoud de RSS-feed zal bevatten');
INSERT INTO `locale` VALUES (203, 'nl', 'backend', 'core', 'lbl', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed');
INSERT INTO `locale` VALUES (204, 'nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden');
INSERT INTO `locale` VALUES (205, 'nl', 'backend', 'settings', 'msg', 'LanguagesText', 'Duid aan welke talen toegankelijk zijn voor bezoekers');
INSERT INTO `locale` VALUES (206, 'nl', 'backend', 'core', 'msg', 'LoginFormHelp', 'Vul uw gebruikersnaam en wachtwoord in om u aan te melden.');
INSERT INTO `locale` VALUES (207, 'nl', 'backend', 'core', 'lbl', 'LoggedInAs', 'aangemeld als');
INSERT INTO `locale` VALUES (208, 'nl', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!');
INSERT INTO `locale` VALUES (209, 'nl', 'backend', 'pages', 'msg', 'ModuleAttached', 'A module is attached. Go to <a href="{url}">{name}</a> to manage.');
INSERT INTO `locale` VALUES (210, 'nl', 'backend', 'core', 'msg', 'NoDrafts', 'Er zijn geen drafts.');
INSERT INTO `locale` VALUES (211, 'nl', 'backend', 'core', 'msg', 'NoItems', 'Er zijn geen items aanwezig.');
INSERT INTO `locale` VALUES (212, 'nl', 'backend', 'core', 'msg', 'NoItemsPublished', 'Er zijn geen items gepubliceerd.');
INSERT INTO `locale` VALUES (213, 'nl', 'backend', 'core', 'msg', 'NoItemsScheduled', 'Er zijn geen items gepland.');
INSERT INTO `locale` VALUES (214, 'nl', 'backend', 'core', 'msg', 'NoRevisions', 'Er zijn nog geen versies.');
INSERT INTO `locale` VALUES (215, 'nl', 'backend', 'core', 'msg', 'NoTags', 'Je hebt nog geen tags ingegeven.');
INSERT INTO `locale` VALUES (216, 'nl', 'backend', 'core', 'msg', 'NotAllowedActionTitle', 'Verboden');
INSERT INTO `locale` VALUES (217, 'nl', 'backend', 'core', 'msg', 'NotAllowedActionMessage', 'Deze actie is niet toegestaan.');
INSERT INTO `locale` VALUES (218, 'nl', 'backend', 'core', 'msg', 'NotAllowedModuleTitle', 'Verboden');
INSERT INTO `locale` VALUES (219, 'nl', 'backend', 'core', 'msg', 'NotAllowedModuleMessage', 'Deze module is niet toegestaan.');
INSERT INTO `locale` VALUES (220, 'nl', 'backend', 'core', 'msg', 'ResetPasswordAndSignIn', 'Resetten en aanmelden');
INSERT INTO `locale` VALUES (221, 'nl', 'backend', 'core', 'msg', 'ResetPasswordFormHelp', 'Vul je gewenste, nieuwe wachtwoord in.');
INSERT INTO `locale` VALUES (222, 'nl', 'backend', 'core', 'msg', 'Saved', 'De wijzigingen zijn opgeslagen.');
INSERT INTO `locale` VALUES (223, 'nl', 'backend', 'settings', 'msg', 'ScriptsText', 'Plaats hier code die op elke pagina geladen moet worden. (bvb. Google Analytics).');
INSERT INTO `locale` VALUES (224, 'nl', 'backend', 'core', 'msg', 'SequenceChanged', 'De volgorde is aangepast.');
INSERT INTO `locale` VALUES (225, 'nl', 'backend', 'core', 'msg', 'UsingARevision', 'Je gebruikt een oudere versie!');
INSERT INTO `locale` VALUES (226, 'nl', 'backend', 'core', 'msg', 'VisibleOnSite', 'Zichtbaar op de website?');
INSERT INTO `locale` VALUES (227, 'nl', 'backend', 'pages', 'msg', 'WidgetAttached', 'A widget is attached.');
INSERT INTO `locale` VALUES (228, 'nl', 'backend', 'core', 'msg', 'TimeOneDayAgo', '1 dag geleden');
INSERT INTO `locale` VALUES (229, 'nl', 'backend', 'core', 'msg', 'TimeOneHourAgo', '1 uur geleden');
INSERT INTO `locale` VALUES (230, 'nl', 'backend', 'core', 'msg', 'TimeOneMinuteAgo', '1 minuut geleden');
INSERT INTO `locale` VALUES (231, 'nl', 'backend', 'core', 'msg', 'TimeOneMonthAgo', '1 maand geleden');
INSERT INTO `locale` VALUES (232, 'nl', 'backend', 'core', 'msg', 'TimeOneSecondAgo', '1 second geleden');
INSERT INTO `locale` VALUES (233, 'nl', 'backend', 'core', 'msg', 'TimeOneWeekAgo', '1 week geleden');
INSERT INTO `locale` VALUES (234, 'nl', 'backend', 'core', 'msg', 'TimeOneYearAgo', '1 jaar geleden');
INSERT INTO `locale` VALUES (235, 'nl', 'backend', 'core', 'msg', 'TimeDaysAgo', '%s dagen geleden');
INSERT INTO `locale` VALUES (236, 'nl', 'backend', 'core', 'msg', 'TimeMinutesAgo', '%s minuten geleden');
INSERT INTO `locale` VALUES (237, 'nl', 'backend', 'core', 'msg', 'TimeHoursAgo', '%s uren geleden');
INSERT INTO `locale` VALUES (238, 'nl', 'backend', 'core', 'msg', 'TimeMonthsAgo', '%s maanden geleden');
INSERT INTO `locale` VALUES (239, 'nl', 'backend', 'core', 'msg', 'TimeSecondsAgo', '%s seconden geleden');
INSERT INTO `locale` VALUES (240, 'nl', 'backend', 'core', 'msg', 'TimeWeeksAgo', '%s weken geleden');
INSERT INTO `locale` VALUES (241, 'nl', 'backend', 'core', 'msg', 'TimeYearsAgo', '%s jaren geleden');
INSERT INTO `locale` VALUES (242, 'nl', 'backend', 'core', 'err', 'BlogRSSTitle', 'Blog RSS titel is nog niet geconfigureerd. <a href="%s" class="button"><span><span><span>Configureer</span></span></span></a>');
INSERT INTO `locale` VALUES (243, 'nl', 'backend', 'core', 'err', 'ContentIsRequired', 'Gelieve inhoud in te geven.');
INSERT INTO `locale` VALUES (244, 'nl', 'backend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.');
INSERT INTO `locale` VALUES (245, 'nl', 'backend', 'core', 'err', 'EmailIsUnknown', 'Dit emailadres werd niet teruggevonden.');
INSERT INTO `locale` VALUES (246, 'nl', 'backend', 'core', 'err', 'FieldIsRequired', 'Dit veld is verplicht.');
INSERT INTO `locale` VALUES (247, 'nl', 'backend', 'core', 'err', 'GeneralFormError', 'Er ging iets mis. Kijk de gemarkeerde velden na.');
INSERT INTO `locale` VALUES (248, 'nl', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key werd nog niet geconfigureerd.');
INSERT INTO `locale` VALUES (249, 'nl', 'backend', 'core', 'err', 'InvalidAPIKey', 'Ongeldige API key.');
INSERT INTO `locale` VALUES (250, 'nl', 'backend', 'core', 'err', 'InvalidDomain', 'Gelieve enkel domeinen in te vullen zonder http en www. vb netlash.com');
INSERT INTO `locale` VALUES (251, 'nl', 'backend', 'core', 'err', 'InvalidParameters', 'Ongeldige parameters.');
INSERT INTO `locale` VALUES (252, 'nl', 'backend', 'core', 'err', 'InvalidURL', 'Ongeldige URL.');
INSERT INTO `locale` VALUES (253, 'nl', 'backend', 'core', 'err', 'InvalidUsernamePasswordCombination', 'De combinatie van gebruikersnaam en wachtwoord is niet correct. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Bent u uw wachtwoord vergeten?</a>');
INSERT INTO `locale` VALUES (254, 'nl', 'backend', 'core', 'err', 'MinimumDimensions', 'Het gekozen bestand moet minimum %s<abbr title="pixels">px</abbr> breed en %s<abbr title="pixels">px</abbr> hoog zijn.');
INSERT INTO `locale` VALUES (255, 'nl', 'backend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.');
INSERT INTO `locale` VALUES (256, 'nl', 'backend', 'core', 'err', 'NonExisting', 'Dit item bestaat niet.');
INSERT INTO `locale` VALUES (257, 'nl', 'backend', 'users', 'err', 'NonExisting', 'De gebruiker bestaat niet.');
INSERT INTO `locale` VALUES (258, 'nl', 'backend', 'core', 'err', 'OnlyJPGAndGifAreAllowed', 'Enkel jpg, jpeg en gif zijn toegelaten.');
INSERT INTO `locale` VALUES (259, 'nl', 'backend', 'core', 'err', 'PasswordIsRequired', 'Gelieve een wachtwoord in te geven.');
INSERT INTO `locale` VALUES (260, 'nl', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Gelieve het gewenste wachtwoord te herhalen.');
INSERT INTO `locale` VALUES (261, 'nl', 'backend', 'core', 'err', 'PasswordsDoNotMatch', 'De wachtwoorden zijn verschillend, probeer het opnieuw.');
INSERT INTO `locale` VALUES (262, 'nl', 'backend', 'core', 'err', 'SomethingWentWrong', 'Er ging iets mis. Probeer later opnieuw.');
INSERT INTO `locale` VALUES (263, 'nl', 'backend', 'core', 'err', 'SurnameIsRequired', 'Gelieve een achternaam in te geven.');
INSERT INTO `locale` VALUES (264, 'nl', 'backend', 'core', 'err', 'TitleIsRequired', 'Gelieve een titel in te geven.');
INSERT INTO `locale` VALUES (265, 'nl', 'backend', 'core', 'err', 'UsernameIsRequired', 'Gelieve een gebruikersnaam in te geven.');
INSERT INTO `locale` VALUES (266, 'nl', 'backend', 'core', 'err', 'UsernameNotAllowed', 'Deze gebruikersnaam is niet toegestaan.');
INSERT INTO `locale` VALUES (267, 'nl', 'backend', 'core', 'err', 'URLAlreadyExist', 'Deze URL bestaat al.');
INSERT INTO `locale` VALUES (268, 'nl', 'frontend', 'core', 'msg', 'YouAreHere', 'Je bent hier');
INSERT INTO `locale` VALUES (269, 'nl', 'frontend', 'core', 'lbl', 'Required', 'verplicht');
INSERT INTO `locale` VALUES (270, 'nl', 'frontend', 'core', 'msg', 'WroteBy', 'geschreven door %s');
INSERT INTO `locale` VALUES (271, 'nl', 'frontend', 'core', 'lbl', 'Comment', 'reactie');
INSERT INTO `locale` VALUES (272, 'nl', 'frontend', 'core', 'lbl', 'Category', 'categorie');
INSERT INTO `locale` VALUES (273, 'nl', 'frontend', 'core', 'act', 'Comments', 'reacties');
INSERT INTO `locale` VALUES (274, 'nl', 'frontend', 'core', 'lbl', 'Comments', 'reacties');
INSERT INTO `locale` VALUES (275, 'nl', 'frontend', 'core', 'act', 'Comment', 'reactie');
INSERT INTO `locale` VALUES (276, 'nl', 'frontend', 'core', 'lbl', 'By', 'door');
INSERT INTO `locale` VALUES (277, 'nl', 'frontend', 'core', 'act', 'React', 'reageer');
INSERT INTO `locale` VALUES (278, 'nl', 'frontend', 'core', 'lbl', 'React', 'reageer');
INSERT INTO `locale` VALUES (279, 'nl', 'frontend', 'core', 'lbl', 'Name', 'naam');
INSERT INTO `locale` VALUES (280, 'nl', 'frontend', 'core', 'lbl', 'Email', 'e-mailadres');
INSERT INTO `locale` VALUES (281, 'nl', 'frontend', 'core', 'lbl', 'Website', 'website');
INSERT INTO `locale` VALUES (282, 'nl', 'frontend', 'core', 'lbl', 'Message', 'bericht');
INSERT INTO `locale` VALUES (283, 'nl', 'frontend', 'core', 'msg', 'TimeOneDayAgo', '1 dag geleden');
INSERT INTO `locale` VALUES (284, 'nl', 'frontend', 'core', 'msg', 'TimeOneHourAgo', '1 uur geleden');
INSERT INTO `locale` VALUES (285, 'nl', 'frontend', 'core', 'msg', 'TimeOneMinuteAgo', '1 minuut geleden');
INSERT INTO `locale` VALUES (286, 'nl', 'frontend', 'core', 'msg', 'TimeOneMonthAgo', '1 maand geleden');
INSERT INTO `locale` VALUES (287, 'nl', 'frontend', 'core', 'msg', 'TimeOneSecondAgo', '1 seconde geleden');
INSERT INTO `locale` VALUES (288, 'nl', 'frontend', 'core', 'msg', 'TimeOneWeekAgo', '1 week geleden');
INSERT INTO `locale` VALUES (289, 'nl', 'frontend', 'core', 'msg', 'TimeOneYearAgo', '1 jaar geleden');
INSERT INTO `locale` VALUES (290, 'nl', 'frontend', 'core', 'msg', 'TimeDaysAgo', '%s dagen');
INSERT INTO `locale` VALUES (291, 'nl', 'frontend', 'core', 'msg', 'TimeMinutesAgo', '%s minuten geleden');
INSERT INTO `locale` VALUES (292, 'nl', 'frontend', 'core', 'msg', 'TimeHoursAgo', '%s uren geleden');
INSERT INTO `locale` VALUES (293, 'nl', 'frontend', 'core', 'msg', 'TimeSecondsAgo', '%s seconden geleden');
INSERT INTO `locale` VALUES (294, 'nl', 'frontend', 'core', 'msg', 'TimeWeeksAgo', '%s weken geleden');
INSERT INTO `locale` VALUES (295, 'nl', 'frontend', 'core', 'msg', 'TimeYearAgo', '%s jaren geleden');
INSERT INTO `locale` VALUES (296, 'nl', 'frontend', 'core', 'lbl', 'Tags', 'tags');
INSERT INTO `locale` VALUES (297, 'nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste');
INSERT INTO `locale` VALUES (298, 'nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %s reacties');
INSERT INTO `locale` VALUES (299, 'nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie');
INSERT INTO `locale` VALUES (300, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd');
INSERT INTO `locale` VALUES (301, 'nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.');
INSERT INTO `locale` VALUES (302, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam');
INSERT INTO `locale` VALUES (303, 'nl', 'frontend', 'core', 'msg', 'CommentedOn', 'reageerde op');
INSERT INTO `locale` VALUES (306, 'nl', 'frontend', 'core', 'lbl', 'PreviousPage', 'vorige pagina');
INSERT INTO `locale` VALUES (307, 'nl', 'frontend', 'core', 'lbl', 'NextPage', 'volgende pagina');
INSERT INTO `locale` VALUES (308, 'nl', 'frontend', 'core', 'lbl', 'GoToPage', 'ga naar pagina');
INSERT INTO `locale` VALUES (309, 'nl', 'backend', 'blog', 'msg', 'HeaderAdd', 'post toevoegen');
INSERT INTO `locale` VALUES (310, 'nl', 'frontend', 'core', 'act', 'Category', 'categorie');
INSERT INTO `locale` VALUES (312, 'nl', 'frontend', 'core', 'act', 'Rss', 'rss');
INSERT INTO `locale` VALUES (313, 'nl', 'backend', 'blog', 'msg', 'HeaderEdit', 'post bewerken');
INSERT INTO `locale` VALUES (314, 'nl', 'frontend', 'core', 'lbl', 'In', 'in');
INSERT INTO `locale` VALUES (315, 'nl', 'frontend', 'core', 'lbl', 'Date', 'datum');
INSERT INTO `locale` VALUES (316, 'nl', 'frontend', 'core', 'lbl', 'Title', 'titel');
INSERT INTO `locale` VALUES (317, 'nl', 'backend', 'core', 'msg', 'ResetYourPassword', 'Wijzig je wachtwoord');
INSERT INTO `locale` VALUES (318, 'nl', 'backend', 'core', 'lbl', 'Index', 'overzicht');
INSERT INTO `locale` VALUES (319, 'nl', 'backend', 'blog', 'lbl', 'Index', 'posts');
INSERT INTO `locale` VALUES (320, 'nl', 'backend', 'core', 'msg', 'EditWithItem', 'Bewerk <em>&quot;%s&quot;</em>');
INSERT INTO `locale` VALUES (321, 'nl', 'backend', 'core', 'msg', 'EditCategoryWithItem', 'Bewerk categorie <em>&quot;%s&quot;</em>');
INSERT INTO `locale` VALUES (322, 'nl', 'backend', 'core', 'lbl', 'General', 'algemeen');
INSERT INTO `locale` VALUES (323, 'nl', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed');
INSERT INTO `locale` VALUES (324, 'nl', 'backend', 'core', 'lbl', 'Summary', 'samenvatting');
INSERT INTO `locale` VALUES (325, 'nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Als je een samenvatting ingeeft, dan zal deze verschijnen in de overzichtspagina''s. Indien niet dan zal de volledige post getoond worden');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Meta-information' AUTO_INCREMENT=301 ;

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
INSERT INTO `meta` VALUES (183, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
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
INSERT INTO `meta` VALUES (243, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (244, 'Over ons', 'N', 'Over ons', 'N', 'Over ons', 'N', 'over-ons', 'N', NULL);
INSERT INTO `meta` VALUES (219, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (220, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (221, '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', '2000-2010', 'N', NULL);
INSERT INTO `meta` VALUES (222, 'Blog', 'N', 'Blog', 'N', 'Blog', 'N', 'blog', 'N', NULL);
INSERT INTO `meta` VALUES (223, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (224, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (225, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (226, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (227, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (228, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (229, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (230, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (231, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (232, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (233, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (234, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (235, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (236, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (237, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
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
INSERT INTO `meta` VALUES (253, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (254, 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'Ik ben een pagina', 'N', 'ik-ben-een-pagina', 'N', NULL);
INSERT INTO `meta` VALUES (266, 'Dit is een blogpost', 'N', 'Dit is een blogpost', 'N', 'Dit is een blogpost', 'N', 'dit-is-een-blogpost', 'N', NULL);
INSERT INTO `meta` VALUES (267, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO `meta` VALUES (268, 'tewsafdfas', 'N', 'tewsafdfas', 'N', 'tewsafdfas', 'N', 'tewsafdfas', 'N', NULL);
INSERT INTO `meta` VALUES (271, 'Testpagina', 'N', 'Testpagina', 'N', 'Testpagina', 'N', 'testpagina', 'N', NULL);
INSERT INTO `meta` VALUES (272, 'Testpagina', 'N', 'Testpagina', 'N', 'Testpagina', 'N', 'testpagina', 'N', NULL);
INSERT INTO `meta` VALUES (273, 'Afblijven, &#039;t is van Tijs', 'N', 'Afblijven, &#039;t is van Tijs', 'N', 'Afblijven, &#039;t is van Tijs', 'N', 'afblijven-t-is-van-tijs', 'N', NULL);
INSERT INTO `meta` VALUES (281, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (275, 'Afblijven, &#039;t is van Tijs', 'N', 'Afblijven, &#039;t is van Tijs', 'N', 'Afblijven, &#039;t is van Tijs', 'N', 'afblijven-039t-is-van-tijs-2', 'N', NULL);
INSERT INTO `meta` VALUES (280, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO `meta` VALUES (279, 'Dit is een blogpost', 'N', 'Dit is een blogpost', 'N', 'Dit is een blogpost', 'N', 'dit-is-een-blogpost-2', 'N', NULL);
INSERT INTO `meta` VALUES (282, 'nr 3', 'N', 'nr 3', 'N', 'nr 3', 'N', 'nr-3', 'N', NULL);
INSERT INTO `meta` VALUES (283, 'nr4', 'N', 'nr4', 'N', 'nr4', 'N', 'nr4', 'N', NULL);
INSERT INTO `meta` VALUES (284, 'nr 5', 'N', 'nr 5', 'N', 'nr 5', 'N', 'nr-5', 'N', NULL);
INSERT INTO `meta` VALUES (285, 'nr 6', 'N', 'nr 6', 'N', 'nr 6', 'N', 'nr-6', 'N', NULL);
INSERT INTO `meta` VALUES (286, 'nr 7', 'N', 'nr 7', 'N', 'nr 7', 'N', 'nr-7', 'N', NULL);
INSERT INTO `meta` VALUES (287, 'nr 8', 'N', 'nr 8', 'N', 'nr 8', 'N', 'nr-8', 'N', NULL);
INSERT INTO `meta` VALUES (288, 'nr 9', 'N', 'nr 9', 'N', 'nr 9', 'N', 'nr-9', 'N', NULL);
INSERT INTO `meta` VALUES (289, 'nr 10', 'N', 'nr 10', 'N', 'nr 10', 'N', 'nr-10', 'N', NULL);
INSERT INTO `meta` VALUES (290, 'nr 11', 'N', 'nr 11', 'N', 'nr 11', 'N', 'nr-11', 'N', NULL);
INSERT INTO `meta` VALUES (292, 'nr 12', 'N', 'nr 12', 'N', 'nr 12', 'N', 'nr-12', 'N', NULL);
INSERT INTO `meta` VALUES (293, 'nr 13', 'N', 'nr 13', 'N', 'nr 13', 'N', 'nr-13', 'N', NULL);
INSERT INTO `meta` VALUES (294, 'nr 14', 'N', 'nr 14', 'N', 'nr 14', 'N', 'nr-14', 'N', NULL);
INSERT INTO `meta` VALUES (295, 'nr 15', 'N', 'nr 15', 'N', 'nr 15', 'N', 'nr-15', 'N', NULL);
INSERT INTO `meta` VALUES (296, 'nr 16', 'N', 'nr 16', 'N', 'nr 16', 'N', 'nr-16', 'N', NULL);
INSERT INTO `meta` VALUES (297, 'nr 17', 'N', 'nr 17', 'N', 'nr 17', 'N', 'nr-21', 'N', NULL);
INSERT INTO `meta` VALUES (298, 'nr 18', 'N', 'nr 18', 'N', 'nr 18', 'N', 'nr-18', 'N', NULL);
INSERT INTO `meta` VALUES (299, 'nr 19', 'N', 'nr 19', 'N', 'nr 19', 'N', 'nr-19', 'N', NULL);
INSERT INTO `meta` VALUES (300, 'nr 20', 'N', 'nr 20', 'N', 'nr 20', 'N', 'nr-20', 'N', NULL);

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
INSERT INTO `modules` VALUES ('snippets', NULL, 'Y');
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
INSERT INTO `modules_settings` VALUES ('blog', 'default_category', 'i:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'default_category_nl', 'i:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'feedburner_url_nl', 's:34:"http://feeds.feedburner.com/netlog";');
INSERT INTO `modules_settings` VALUES ('blog', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('blog', 'moderation', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'ping_services', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'requires_akismet', 'b:1;');
INSERT INTO `modules_settings` VALUES ('blog', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('blog', 'rss_description_nl', 's:35:"Volg onze blog via jouw RSS-reader.";');
INSERT INTO `modules_settings` VALUES ('blog', 'rss_title_LANGUAGE_ABBREVIATION', 'N;');
INSERT INTO `modules_settings` VALUES ('blog', 'rss_title_nl', 's:12:"Fork NG blog";');
INSERT INTO `modules_settings` VALUES ('blog', 'site_title_nl', 'N;');
INSERT INTO `modules_settings` VALUES ('blog', 'spamfilter', 'b:1;');
INSERT INTO `modules_settings` VALUES ('contact', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('contact', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('core', 'active_languages', 'a:3:{i:0;s:2:"nl";i:1;s:2:"fr";i:2;s:2:"en";}');
INSERT INTO `modules_settings` VALUES ('core', 'akismet_key', 's:12:"41eadca08459";');
INSERT INTO `modules_settings` VALUES ('core', 'default_category', 'i:1;');
INSERT INTO `modules_settings` VALUES ('core', 'default_language', 's:2:"nl";');
INSERT INTO `modules_settings` VALUES ('core', 'default_template', 'i:1;');
INSERT INTO `modules_settings` VALUES ('core', 'email_nl', 's:18:"forkng@fork-cms.be";');
INSERT INTO `modules_settings` VALUES ('core', 'fork_api_private_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'fork_api_public_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'google_maps_key', 's:0:"";');
INSERT INTO `modules_settings` VALUES ('core', 'languages', 'a:3:{i:0;s:2:"nl";i:1;s:2:"fr";i:2;s:2:"en";}');
INSERT INTO `modules_settings` VALUES ('core', 'mailer_from', 'a:2:{i:0;s:20:"no-reply@fork-cms.be";i:1;s:4:"Fork";}');
INSERT INTO `modules_settings` VALUES ('core', 'mailer_reply_to', 'a:2:{i:0;s:20:"no-reply@fork-cms.be";i:1;s:4:"Fork";}');
INSERT INTO `modules_settings` VALUES ('core', 'mailer_to', 'a:2:{i:0;s:20:"no-reply@fork-cms.be";i:1;s:4:"Fork";}');
INSERT INTO `modules_settings` VALUES ('core', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('core', 'site_domains', 'a:1:{i:0;s:12:"forkng.local";}');
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
INSERT INTO `modules_settings` VALUES ('snippets', 'maximum_number_of_revisions', 'i:5;');
INSERT INTO `modules_settings` VALUES ('snippets', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('snippets', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('statistics', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('statistics', 'requires_google_maps', 'b:0;');
INSERT INTO `modules_settings` VALUES ('tags', 'requires_akismet', 'b:0;');
INSERT INTO `modules_settings` VALUES ('tags', 'requires_google_maps', 'b:0;');
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

INSERT INTO `modules_tags` VALUES ('blog', 4, 2);
INSERT INTO `modules_tags` VALUES ('blog', 4, 3);
INSERT INTO `modules_tags` VALUES ('blog', 13, 1);
INSERT INTO `modules_tags` VALUES ('blog', 14, 1);
INSERT INTO `modules_tags` VALUES ('blog', 15, 3);
INSERT INTO `modules_tags` VALUES ('pages', 4, 4);
INSERT INTO `modules_tags` VALUES ('pages', 4, 1034);
INSERT INTO `modules_tags` VALUES ('pages', 6, 7);
INSERT INTO `modules_tags` VALUES ('pages', 12, 1034);

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
  `sequence` int(11) NOT NULL,
  `has_extra` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `idx_id_status_hidden_language` (`id`,`status`,`hidden`,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1580 ;

-- 
-- Dumping data for table `pages`
-- 

INSERT INTO `pages` VALUES (1002, 1483, 1, 1, 2, 164, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-14 17:01:05', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1002, 1484, 1, 1, 2, 165, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-14 17:02:02', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1002, 1485, 1, 1, 2, 166, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-14 17:02:12', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1002, 1486, 1, 1, 2, 167, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-14 17:02:51', 'Y', 'Y', 'Y', 'Y', 7, 'N');
INSERT INTO `pages` VALUES (1003, 1494, 1, 1, 1, 175, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-18 21:31:59', 'Y', 'Y', 'Y', 'Y', 5, 'N');
INSERT INTO `pages` VALUES (1, 1472, 1, 0, 1, 153, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-01-11 17:41:16', NULL, '2010-01-11 15:02:26', '2010-01-12 16:33:00', 'N', 'Y', 'Y', 'N', 1, 'N');
INSERT INTO `pages` VALUES (1002, 1482, 1, 1, 2, 163, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-14 17:00:28', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1002, 1481, 1, 1, 2, 162, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-14 16:59:19', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (3, 1475, 1, 0, 1, 156, 'nl', 'meta', 'Disclaimer', 'Disclaimer', 'N', 'N', 'archive', '2010-01-11 15:02:49', NULL, '2010-01-11 15:02:49', '2010-01-12 18:47:39', 'Y', 'Y', 'Y', 'Y', 11, 'N');
INSERT INTO `pages` VALUES (1000, 1426, 1, 0, 1, 107, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-12 15:50:01', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (404, 1427, 1, 0, 1, 108, 'nl', 'root', '404', '404', 'N', 'N', 'archive', '2010-01-11 15:03:17', NULL, '2010-01-11 15:03:17', '2010-01-12 15:50:11', 'Y', 'Y', 'Y', 'Y', 16, 'N');
INSERT INTO `pages` VALUES (1, 1492, 1, 0, 1, 173, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-01-11 17:41:16', NULL, '2010-01-11 15:02:26', '2010-01-18 19:26:49', 'N', 'Y', 'Y', 'N', 1, 'N');
INSERT INTO `pages` VALUES (1, 1491, 1, 0, 1, 172, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-01-11 17:41:16', NULL, '2010-01-11 15:02:26', '2010-01-18 19:24:20', 'N', 'Y', 'Y', 'N', 1, 'N');
INSERT INTO `pages` VALUES (2, 1471, 1, 0, 1, 152, 'nl', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-01-11 15:02:56', NULL, '2010-01-11 15:02:56', '2010-01-12 16:31:07', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1000, 1495, 1, 0, 2, 176, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-18 21:48:05', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1000, 1496, 1, 0, 2, 177, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-18 21:48:18', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1002, 1468, 1, 1, 2, 149, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'N', 'archive', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-12 16:30:08', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1000, 1490, 1, 0, 2, 171, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-14 18:15:23', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1, 1489, 1, 0, 1, 170, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-01-11 17:41:16', NULL, '2010-01-11 15:02:26', '2010-01-14 18:11:30', 'N', 'Y', 'Y', 'N', 1, 'N');
INSERT INTO `pages` VALUES (1000, 1497, 1, 0, 2, 178, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-20 10:13:23', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1000, 1498, 1, 0, 2, 179, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-20 10:53:08', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1000, 1499, 1, 0, 2, 180, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-20 10:53:35', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1000, 1500, 1, 0, 2, 181, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-20 10:56:10', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1000, 1501, 1, 0, 2, 182, 'nl', 'meta', 'Ik ben meta....', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-20 10:56:28', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1004, 1502, 1, 1, 1, 183, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-20 15:51:30', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (3, 1504, 1, 0, 1, 185, 'nl', 'meta', 'Privacy &amp; Disclaimer', 'Disclaimer', 'N', 'N', 'archive', '2010-01-11 15:02:49', NULL, '2010-01-11 15:02:49', '2010-01-20 15:55:55', 'Y', 'Y', 'Y', 'Y', 11, 'N');
INSERT INTO `pages` VALUES (1005, 1503, 1, 1, 1, 184, 'nl', 'page', 'Over ons', 'Over ons', 'N', 'N', 'archive', '2010-01-20 15:55:27', NULL, '2010-01-20 15:55:27', '2010-01-20 15:55:27', 'Y', 'Y', 'Y', 'Y', 3, 'N');
INSERT INTO `pages` VALUES (1003, 1564, 1, 1, 1, 245, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-27 15:14:45', 'Y', 'Y', 'Y', 'Y', 5, 'N');
INSERT INTO `pages` VALUES (1007, 1506, 1, 1006, 1, 187, 'nl', 'page', '1900-1910', '1900-1910', 'N', 'N', 'archive', '2010-01-20 15:57:05', NULL, '2010-01-20 15:57:05', '2010-01-20 15:57:05', 'Y', 'Y', 'Y', 'Y', 13, 'N');
INSERT INTO `pages` VALUES (1008, 1507, 1, 1015, 1, 188, 'nl', 'page', '1910-1920', '1910-1920', 'N', 'N', 'active', '2010-01-20 15:57:12', NULL, '2010-01-20 15:57:12', '2010-01-20 15:57:12', 'Y', 'Y', 'Y', 'Y', 6, 'N');
INSERT INTO `pages` VALUES (1009, 1508, 1, 1015, 1, 189, 'nl', 'page', '1920-1930', '1920-1930', 'N', 'N', 'active', '2010-01-20 15:57:22', NULL, '2010-01-20 15:57:22', '2010-01-20 15:57:22', 'Y', 'Y', 'Y', 'Y', 6, 'N');
INSERT INTO `pages` VALUES (1011, 1511, 1, 1015, 1, 192, 'nl', 'page', '1930-1940', '1930-1940', 'N', 'N', 'active', '2010-01-20 15:57:47', NULL, '2010-01-20 15:57:47', '2010-01-20 15:57:47', 'Y', 'Y', 'Y', 'Y', 7, 'N');
INSERT INTO `pages` VALUES (1000, 1512, 1, 0, 2, 193, 'nl', 'meta', 'Metanav test 1', 'Ik ben meta....', 'N', 'N', 'archive', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-20 16:05:02', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1012, 1513, 1, 1000, 1, 194, 'nl', 'meta', 'Metanav test 2', 'Metanav test 2', 'N', 'N', 'active', '2010-01-20 16:05:11', NULL, '2010-01-20 16:05:11', '2010-01-20 16:05:11', 'Y', 'Y', 'Y', 'Y', 13, 'N');
INSERT INTO `pages` VALUES (1013, 1514, 1, 1000, 1, 195, 'nl', 'meta', 'Metanav test 3', 'Metanav test 3', 'N', 'N', 'active', '2010-01-20 16:05:22', NULL, '2010-01-20 16:05:22', '2010-01-20 16:05:22', 'Y', 'Y', 'Y', 'Y', 14, 'N');
INSERT INTO `pages` VALUES (1014, 1515, 1, 1000, 1, 196, 'nl', 'meta', 'Metanav test 4', 'Metanav test 4', 'N', 'N', 'active', '2010-01-20 16:05:32', NULL, '2010-01-20 16:05:32', '2010-01-20 16:05:32', 'Y', 'Y', 'Y', 'Y', 15, 'N');
INSERT INTO `pages` VALUES (1007, 1516, 1, 1015, 1, 197, 'nl', 'page', '19e eeuw', '1900-1910', 'N', 'N', 'active', '2010-01-20 15:57:05', NULL, '2010-01-20 15:57:05', '2010-01-20 16:12:02', 'Y', 'Y', 'Y', 'Y', 5, 'N');
INSERT INTO `pages` VALUES (1015, 1517, 1, 1002, 1, 198, 'nl', 'page', '20e eeuw', '20e eeuw', 'N', 'N', 'active', '2010-01-20 16:12:12', NULL, '2010-01-20 16:12:12', '2010-01-20 16:12:12', 'Y', 'Y', 'Y', 'Y', 4, 'N');
INSERT INTO `pages` VALUES (1016, 1518, 1, 1002, 1, 199, 'nl', 'page', '21e eeuw', '21e eeuw', 'N', 'N', 'active', '2010-01-20 16:12:22', NULL, '2010-01-20 16:12:22', '2010-01-20 16:12:22', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1017, 1519, 1, 1015, 1, 200, 'nl', 'page', '1940-1950', '1940-1950', 'N', 'N', 'active', '2010-01-20 16:33:24', NULL, '2010-01-20 16:33:24', '2010-01-20 16:33:24', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1018, 1520, 1, 1015, 1, 201, 'nl', 'page', '1950-1960', '1950-1960', 'N', 'N', 'active', '2010-01-20 16:33:32', NULL, '2010-01-20 16:33:32', '2010-01-20 16:33:32', 'Y', 'Y', 'Y', 'Y', 9, 'N');
INSERT INTO `pages` VALUES (1019, 1521, 1, 1015, 1, 202, 'nl', 'page', '1960-1970', '1960-1970', 'N', 'N', 'active', '2010-01-20 16:33:38', NULL, '2010-01-20 16:33:38', '2010-01-20 16:33:38', 'Y', 'Y', 'Y', 'Y', 10, 'N');
INSERT INTO `pages` VALUES (1020, 1522, 1, 1015, 1, 203, 'nl', 'page', '1970-1980', '1970-1980', 'N', 'N', 'active', '2010-01-20 16:33:45', NULL, '2010-01-20 16:33:45', '2010-01-20 16:33:45', 'Y', 'Y', 'Y', 'Y', 11, 'N');
INSERT INTO `pages` VALUES (1021, 1523, 1, 1015, 1, 204, 'nl', 'page', '1990-1999', '1990-1999', 'N', 'N', 'active', '2010-01-20 16:33:51', NULL, '2010-01-20 16:33:51', '2010-01-20 16:33:51', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1022, 1524, 1, 1016, 1, 205, 'nl', 'page', '2000-2010', '2000-2010', 'N', 'N', 'archive', '2010-01-20 16:34:50', NULL, '2010-01-20 16:34:50', '2010-01-20 16:34:50', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1023, 1525, 1, 1022, 1, 206, 'nl', 'page', '2000', '2000', 'N', 'N', 'active', '2010-01-20 16:35:25', NULL, '2010-01-20 16:35:25', '2010-01-20 16:35:25', 'Y', 'Y', 'Y', 'Y', 7, 'N');
INSERT INTO `pages` VALUES (1024, 1526, 1, 1022, 1, 207, 'nl', 'page', '2001', '2001', 'N', 'N', 'active', '2010-01-20 16:35:29', NULL, '2010-01-20 16:35:29', '2010-01-20 16:35:29', 'Y', 'Y', 'Y', 'Y', 9, 'N');
INSERT INTO `pages` VALUES (1025, 1527, 1, 1022, 1, 208, 'nl', 'page', '2002', '2002', 'N', 'N', 'active', '2010-01-20 16:35:36', NULL, '2010-01-20 16:35:36', '2010-01-20 16:35:36', 'Y', 'Y', 'Y', 'Y', 10, 'N');
INSERT INTO `pages` VALUES (1026, 1528, 1, 1022, 1, 209, 'nl', 'page', '2003', '2003', 'N', 'N', 'active', '2010-01-20 16:35:41', NULL, '2010-01-20 16:35:41', '2010-01-20 16:35:41', 'Y', 'Y', 'Y', 'Y', 11, 'N');
INSERT INTO `pages` VALUES (1027, 1529, 1, 1026, 1, 210, 'nl', 'page', 'Januari', 'Januari', 'N', 'N', 'active', '2010-01-20 16:35:46', NULL, '2010-01-20 16:35:46', '2010-01-20 16:35:46', 'Y', 'Y', 'Y', 'Y', 11, 'N');
INSERT INTO `pages` VALUES (1028, 1530, 1, 1026, 1, 211, 'nl', 'page', 'Februari', 'Februari', 'N', 'N', 'active', '2010-01-20 16:35:53', NULL, '2010-01-20 16:35:53', '2010-01-20 16:35:53', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (1029, 1531, 1, 1027, 1, 212, 'nl', 'page', '1', '1', 'N', 'N', 'archive', '2010-01-20 16:36:02', NULL, '2010-01-20 16:36:02', '2010-01-20 16:36:02', 'Y', 'Y', 'Y', 'Y', 16, 'N');
INSERT INTO `pages` VALUES (1030, 1532, 1, 1027, 1, 213, 'nl', 'page', '2', '2', 'N', 'N', 'active', '2010-01-20 16:36:07', NULL, '2010-01-20 16:36:07', '2010-01-20 16:36:07', 'Y', 'Y', 'Y', 'Y', 20, 'N');
INSERT INTO `pages` VALUES (1029, 1533, 1, 1027, 1, 214, 'nl', 'page', '01AM', '1', 'N', 'N', 'active', '2010-01-20 16:36:02', NULL, '2010-01-20 16:36:02', '2010-01-20 16:38:30', 'Y', 'Y', 'Y', 'Y', 16, 'N');
INSERT INTO `pages` VALUES (1031, 1534, 1, 1029, 1, 215, 'nl', 'page', '02AM', '02AM', 'N', 'N', 'active', '2010-01-20 16:38:36', NULL, '2010-01-20 16:38:36', '2010-01-20 16:38:36', 'Y', 'Y', 'Y', 'Y', 18, 'N');
INSERT INTO `pages` VALUES (1032, 1535, 1, 1031, 1, 216, 'nl', 'page', '60', '60', 'N', 'N', 'active', '2010-01-20 16:39:12', NULL, '2010-01-20 16:39:12', '2010-01-20 16:39:12', 'Y', 'Y', 'Y', 'Y', 19, 'N');
INSERT INTO `pages` VALUES (1033, 1536, 1, 1031, 1, 217, 'nl', 'page', 'Historisch feit in een lange titel', 'Historisch feit in een lange titel', 'N', 'N', 'active', '2010-01-20 16:39:50', NULL, '2010-01-20 16:39:50', '2010-01-20 16:39:50', 'Y', 'Y', 'Y', 'Y', 20, 'N');
INSERT INTO `pages` VALUES (1004, 1562, 1, 1, 1, 243, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 15:13:43', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1022, 1538, 1, 1016, 2, 219, 'nl', 'page', '2000-2010', '2000-2010', 'N', 'N', 'archive', '2010-01-20 16:34:50', NULL, '2010-01-20 16:34:50', '2010-01-26 14:14:17', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1022, 1539, 1, 1016, 2, 220, 'nl', 'page', '2000-2010', '2000-2010', 'N', 'N', 'archive', '2010-01-20 16:34:50', NULL, '2010-01-20 16:34:50', '2010-01-26 14:14:29', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1022, 1540, 1, 1016, 2, 221, 'nl', 'page', '2000-2010', '2000-2010', 'N', 'N', 'active', '2010-01-20 16:34:50', NULL, '2010-01-20 16:34:50', '2010-01-26 14:14:40', 'Y', 'Y', 'Y', 'Y', 8, 'N');
INSERT INTO `pages` VALUES (1003, 1541, 1, 1, 1, 222, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-27 12:29:45', 'Y', 'Y', 'Y', 'Y', 5, 'N');
INSERT INTO `pages` VALUES (1004, 1542, 1, 1, 1, 223, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:17:53', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1543, 1, 1, 1, 224, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:21:09', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1544, 1, 1, 1, 225, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:38:53', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1545, 1, 1, 1, 226, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:48:11', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1546, 1, 1, 1, 227, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:48:27', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1547, 1, 1, 1, 228, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:48:35', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1548, 1, 1, 1, 229, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:48:49', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1549, 1, 1, 1, 230, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:49:17', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1550, 1, 1, 1, 231, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:49:45', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1551, 1, 1, 1, 232, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:49:53', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1552, 1, 1, 1, 233, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:50:45', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1553, 1, 1, 1, 234, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:50:50', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1554, 1, 1, 1, 235, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:53:54', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1555, 1, 1, 1, 236, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:54:26', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1556, 1, 1, 1, 237, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 14:56:59', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1003, 1557, 1, 1, 1, 238, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-27 14:57:15', 'Y', 'Y', 'Y', 'Y', 5, 'N');
INSERT INTO `pages` VALUES (1003, 1558, 1, 1, 1, 239, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-27 15:00:56', 'Y', 'Y', 'Y', 'Y', 5, 'Y');
INSERT INTO `pages` VALUES (1003, 1559, 1, 1, 1, 240, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'archive', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-27 15:01:21', 'Y', 'Y', 'Y', 'Y', 5, 'N');
INSERT INTO `pages` VALUES (1003, 1570, 1, 1, 1, 251, 'nl', 'page', 'Blog', 'Blog', 'N', 'N', 'active', '2010-01-18 21:31:59', NULL, '2010-01-18 21:31:59', '2010-01-27 15:29:40', 'Y', 'Y', 'Y', 'Y', 5, 'Y');
INSERT INTO `pages` VALUES (404, 1571, 1, 0, 1, 252, 'nl', 'root', '404', '404', 'N', 'N', 'active', '2010-01-11 15:03:17', NULL, '2010-01-11 15:03:17', '2010-01-27 15:41:09', 'N', 'Y', 'Y', 'N', 16, 'N');
INSERT INTO `pages` VALUES (1005, 1563, 1, 1, 1, 244, 'nl', 'page', 'Over ons', 'Over ons', 'N', 'N', 'active', '2010-01-20 15:55:27', NULL, '2010-01-20 15:55:27', '2010-01-27 15:13:55', 'Y', 'Y', 'Y', 'Y', 3, 'N');
INSERT INTO `pages` VALUES (1002, 1565, 1, 1, 2, 246, 'nl', 'page', 'Subpage', 'Subpage', 'N', 'Y', 'active', '2010-01-11 15:09:33', NULL, '2010-01-11 15:09:33', '2010-01-27 15:14:52', 'Y', 'Y', 'Y', 'Y', 7, 'N');
INSERT INTO `pages` VALUES (1, 1566, 1, 0, 1, 247, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'active', '2010-01-11 17:41:16', NULL, '2010-01-11 15:02:26', '2010-01-27 15:28:23', 'N', 'Y', 'Y', 'N', 1, 'N');
INSERT INTO `pages` VALUES (3, 1567, 1, 0, 1, 248, 'nl', 'meta', 'Privacy &amp; Disclaimer', 'Disclaimer', 'N', 'N', 'archive', '2010-01-11 15:02:49', NULL, '2010-01-11 15:02:49', '2010-01-27 15:28:50', 'Y', 'Y', 'Y', 'Y', 11, 'Y');
INSERT INTO `pages` VALUES (1000, 1568, 1, 0, 2, 249, 'nl', 'meta', 'Metanav test 1', 'Ik ben meta....', 'N', 'N', 'active', '2010-01-11 16:55:23', NULL, '2010-01-11 16:55:23', '2010-01-27 15:28:59', 'Y', 'Y', 'Y', 'Y', 12, 'N');
INSERT INTO `pages` VALUES (2, 1569, 1, 0, 1, 250, 'nl', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'archive', '2010-01-11 15:02:56', NULL, '2010-01-11 15:02:56', '2010-01-27 15:29:06', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1572, 1, 1, 1, 253, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 15:52:09', 'Y', 'Y', 'Y', 'Y', 2, 'Y');
INSERT INTO `pages` VALUES (1004, 1573, 1, 1, 1, 254, 'nl', 'page', 'Ik ben een pagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-27 16:57:02', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1034, 1574, 6, 0, 1, 265, 'nl', 'root', 'dsfasdf', 'dsfasdf', 'N', 'N', 'active', '2010-01-28 14:03:54', NULL, '2010-01-28 14:03:54', '2010-01-28 14:03:54', 'Y', 'Y', 'Y', 'Y', 17, 'N');
INSERT INTO `pages` VALUES (2, 1575, 1, 0, 1, 267, 'nl', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'active', '2010-01-11 15:02:56', NULL, '2010-01-11 15:02:56', '2010-01-28 14:40:06', 'Y', 'Y', 'Y', 'Y', 2, 'Y');
INSERT INTO `pages` VALUES (1004, 1576, 1, 1, 1, 271, 'nl', 'page', 'Testpagina', 'Ik ben een pagina', 'N', 'N', 'archive', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-28 16:41:28', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (1004, 1577, 1, 1, 1, 272, 'nl', 'page', 'Testpagina', 'Testpagina', 'Y', 'N', 'active', '2010-01-20 15:51:30', NULL, '2010-01-20 15:51:30', '2010-01-28 16:41:52', 'Y', 'Y', 'Y', 'Y', 2, 'N');
INSERT INTO `pages` VALUES (3, 1578, 1, 0, 1, 280, 'nl', 'meta', 'Privacy &amp; Disclaimer', 'Disclaimer', 'N', 'N', 'archive', '2010-01-11 15:02:49', NULL, '2010-01-11 15:02:49', '2010-01-29 13:21:58', 'Y', 'Y', 'Y', 'Y', 11, 'N');
INSERT INTO `pages` VALUES (3, 1579, 1, 0, 1, 281, 'nl', 'meta', 'Privacy &amp; Disclaimer', 'Disclaimer', 'N', 'N', 'active', '2010-01-11 15:02:49', NULL, '2010-01-11 15:02:49', '2010-01-29 13:22:16', 'Y', 'Y', 'Y', 'Y', 11, 'N');

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
INSERT INTO `pages_blocks` VALUES (39, 1496, NULL, '<p>ddddd</p>', 'active', '2010-01-18 21:48:18', '2010-01-18 21:48:18');
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
INSERT INTO `pages_blocks` VALUES (39, 1490, NULL, '', 'active', '2010-01-14 18:15:23', '2010-01-14 18:15:23');
INSERT INTO `pages_blocks` VALUES (1, 1489, NULL, '<p>AA</p>', 'active', '2010-01-14 18:11:30', '2010-01-14 18:11:30');
INSERT INTO `pages_blocks` VALUES (2, 1489, NULL, '<p>BB</p>', 'active', '2010-01-14 18:11:30', '2010-01-14 18:11:30');
INSERT INTO `pages_blocks` VALUES (3, 1489, NULL, '<p>CC</p>', 'active', '2010-01-14 18:11:30', '2010-01-14 18:11:30');
INSERT INTO `pages_blocks` VALUES (45, 1492, NULL, '<p>BB</p>', 'archive', '2010-01-18 19:26:49', '2010-01-18 19:26:49');
INSERT INTO `pages_blocks` VALUES (44, 1492, NULL, '<p>AA</p>', 'active', '2010-01-18 19:26:49', '2010-01-18 19:26:49');
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
INSERT INTO `pages_blocks` VALUES (39, 1495, NULL, '', 'active', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (40, 1495, NULL, '', 'archive', '2010-01-18 21:48:05', '2010-01-18 21:48:05');
INSERT INTO `pages_blocks` VALUES (47, 1494, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-18 21:31:59', '2010-01-18 21:31:59');
INSERT INTO `pages_blocks` VALUES (48, 1494, 1, NULL, 'active', '2010-01-18 21:31:59', '2010-01-18 21:31:59');
INSERT INTO `pages_blocks` VALUES (49, 1494, 2, NULL, 'active', '2010-01-18 21:31:59', '2010-01-18 21:31:59');
INSERT INTO `pages_blocks` VALUES (40, 1497, NULL, '', 'archive', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (39, 1497, NULL, '<p>ddddd</p>', 'active', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (43, 1497, NULL, '', 'active', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (42, 1497, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (41, 1497, NULL, '', 'active', '2010-01-20 10:13:23', '2010-01-20 10:13:23');
INSERT INTO `pages_blocks` VALUES (40, 1498, NULL, '', 'archive', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (39, 1498, NULL, '<p>ddddd</p>', 'active', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (43, 1498, NULL, '', 'active', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (42, 1498, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (41, 1498, NULL, '', 'active', '2010-01-20 10:53:08', '2010-01-20 10:53:08');
INSERT INTO `pages_blocks` VALUES (40, 1499, NULL, '', 'archive', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (39, 1499, NULL, '<p>ddddd</p>', 'active', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (43, 1499, NULL, '', 'active', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (42, 1499, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (41, 1499, NULL, '', 'active', '2010-01-20 10:53:35', '2010-01-20 10:53:35');
INSERT INTO `pages_blocks` VALUES (40, 1500, NULL, '', 'archive', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (39, 1500, NULL, '<p>ddddd</p>', 'active', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (43, 1500, NULL, '', 'active', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (42, 1500, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (41, 1500, NULL, '', 'active', '2010-01-20 10:56:10', '2010-01-20 10:56:10');
INSERT INTO `pages_blocks` VALUES (40, 1501, NULL, '', 'archive', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (39, 1501, NULL, '<p>ddddd</p>', 'active', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (43, 1501, NULL, '', 'active', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (42, 1501, NULL, '<p>ddddddd</p>', 'archive', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (41, 1501, NULL, '', 'active', '2010-01-20 10:56:28', '2010-01-20 10:56:28');
INSERT INTO `pages_blocks` VALUES (50, 1502, NULL, '', 'active', '2010-01-20 15:51:30', '2010-01-20 15:51:30');
INSERT INTO `pages_blocks` VALUES (51, 1502, NULL, '', 'active', '2010-01-20 15:51:30', '2010-01-20 15:51:30');
INSERT INTO `pages_blocks` VALUES (52, 1502, NULL, '', 'active', '2010-01-20 15:51:30', '2010-01-20 15:51:30');
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
INSERT INTO `pages_blocks` VALUES (39, 1512, NULL, '<p>ddddd</p>', 'active', '2010-01-20 16:05:02', '2010-01-20 16:05:02');
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
INSERT INTO `pages_blocks` VALUES (156, 1562, NULL, '', 'archive', '2010-01-27 15:13:43', '2010-01-27 15:13:43');
INSERT INTO `pages_blocks` VALUES (155, 1562, NULL, '', 'active', '2010-01-27 15:13:43', '2010-01-27 15:13:43');
INSERT INTO `pages_blocks` VALUES (154, 1562, NULL, '', 'active', '2010-01-27 15:13:43', '2010-01-27 15:13:43');
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
INSERT INTO `pages_blocks` VALUES (148, 1548, NULL, '', 'archive', '2010-01-27 14:48:49', '2010-01-27 14:48:49');
INSERT INTO `pages_blocks` VALUES (149, 1548, NULL, '', 'active', '2010-01-27 14:48:49', '2010-01-27 14:48:49');
INSERT INTO `pages_blocks` VALUES (150, 1548, NULL, '', 'active', '2010-01-27 14:48:49', '2010-01-27 14:48:49');
INSERT INTO `pages_blocks` VALUES (148, 1549, NULL, '', 'active', '2010-01-27 14:49:17', '2010-01-27 14:49:17');
INSERT INTO `pages_blocks` VALUES (149, 1549, NULL, '', 'active', '2010-01-27 14:49:17', '2010-01-27 14:49:17');
INSERT INTO `pages_blocks` VALUES (150, 1549, NULL, '', 'active', '2010-01-27 14:49:17', '2010-01-27 14:49:17');
INSERT INTO `pages_blocks` VALUES (151, 1551, NULL, '', 'archive', '2010-01-27 14:49:53', '2010-01-27 14:49:53');
INSERT INTO `pages_blocks` VALUES (152, 1551, NULL, '', 'active', '2010-01-27 14:49:53', '2010-01-27 14:49:53');
INSERT INTO `pages_blocks` VALUES (153, 1551, NULL, '', 'active', '2010-01-27 14:49:53', '2010-01-27 14:49:53');
INSERT INTO `pages_blocks` VALUES (151, 1552, NULL, '', 'archive', '2010-01-27 14:50:45', '2010-01-27 14:50:45');
INSERT INTO `pages_blocks` VALUES (152, 1552, NULL, '', 'active', '2010-01-27 14:50:45', '2010-01-27 14:50:45');
INSERT INTO `pages_blocks` VALUES (153, 1552, NULL, '', 'active', '2010-01-27 14:50:45', '2010-01-27 14:50:45');
INSERT INTO `pages_blocks` VALUES (151, 1553, NULL, '', 'archive', '2010-01-27 14:50:50', '2010-01-27 14:50:50');
INSERT INTO `pages_blocks` VALUES (152, 1553, NULL, '', 'active', '2010-01-27 14:50:50', '2010-01-27 14:50:50');
INSERT INTO `pages_blocks` VALUES (153, 1553, NULL, '', 'active', '2010-01-27 14:50:50', '2010-01-27 14:50:50');
INSERT INTO `pages_blocks` VALUES (151, 1554, NULL, '', 'active', '2010-01-27 14:53:54', '2010-01-27 14:53:54');
INSERT INTO `pages_blocks` VALUES (152, 1554, NULL, '', 'active', '2010-01-27 14:53:54', '2010-01-27 14:53:54');
INSERT INTO `pages_blocks` VALUES (153, 1554, NULL, '', 'active', '2010-01-27 14:53:54', '2010-01-27 14:53:54');
INSERT INTO `pages_blocks` VALUES (154, 1556, NULL, '', 'archive', '2010-01-27 14:56:59', '2010-01-27 14:56:59');
INSERT INTO `pages_blocks` VALUES (155, 1556, NULL, '', 'active', '2010-01-27 14:56:59', '2010-01-27 14:56:59');
INSERT INTO `pages_blocks` VALUES (156, 1556, NULL, '', 'archive', '2010-01-27 14:56:59', '2010-01-27 14:56:59');
INSERT INTO `pages_blocks` VALUES (47, 1557, NULL, '<p>Intro tekst over blog</p>', 'active', '2010-01-27 14:57:15', '2010-01-27 14:57:15');
INSERT INTO `pages_blocks` VALUES (48, 1557, 1, NULL, 'active', '2010-01-27 14:57:15', '2010-01-27 14:57:15');
INSERT INTO `pages_blocks` VALUES (49, 1557, 2, NULL, 'active', '2010-01-27 14:57:15', '2010-01-27 14:57:15');
INSERT INTO `pages_blocks` VALUES (157, 1559, NULL, '<p>Intro tekst over blog</p>', 'archive', '2010-01-27 15:01:21', '2010-01-27 15:01:21');
INSERT INTO `pages_blocks` VALUES (158, 1559, 1, NULL, 'active', '2010-01-27 15:01:21', '2010-01-27 15:01:21');
INSERT INTO `pages_blocks` VALUES (159, 1559, 2, NULL, 'active', '2010-01-27 15:01:21', '2010-01-27 15:01:21');
INSERT INTO `pages_blocks` VALUES (158, 1570, 1, NULL, 'active', '2010-01-27 15:29:40', '2010-01-27 15:29:40');
INSERT INTO `pages_blocks` VALUES (157, 1570, NULL, '<p>Intro tekst over blog</p>', 'active', '2010-01-27 15:29:40', '2010-01-27 15:29:40');
INSERT INTO `pages_blocks` VALUES (159, 1570, 2, NULL, 'active', '2010-01-27 15:29:40', '2010-01-27 15:29:40');
INSERT INTO `pages_blocks` VALUES (53, 1563, NULL, '', 'active', '2010-01-27 15:13:55', '2010-01-27 15:13:55');
INSERT INTO `pages_blocks` VALUES (54, 1563, NULL, '', 'active', '2010-01-27 15:13:55', '2010-01-27 15:13:55');
INSERT INTO `pages_blocks` VALUES (55, 1563, NULL, '', 'active', '2010-01-27 15:13:55', '2010-01-27 15:13:55');
INSERT INTO `pages_blocks` VALUES (158, 1564, 1, NULL, 'active', '2010-01-27 15:14:45', '2010-01-27 15:14:45');
INSERT INTO `pages_blocks` VALUES (159, 1564, 2, NULL, 'active', '2010-01-27 15:14:45', '2010-01-27 15:14:45');
INSERT INTO `pages_blocks` VALUES (29, 1565, NULL, '<p>AA</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (30, 1565, NULL, '<p>BB</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (31, 1565, NULL, '<p>CC</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (32, 1565, NULL, '<p>DD</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (33, 1565, NULL, '<p>EE</p>', 'active', '2010-01-27 15:14:52', '2010-01-27 15:14:52');
INSERT INTO `pages_blocks` VALUES (45, 1566, NULL, '<p>BB</p>', 'active', '2010-01-27 15:28:23', '2010-01-27 15:28:23');
INSERT INTO `pages_blocks` VALUES (44, 1566, NULL, '<p>AA</p>', 'active', '2010-01-27 15:28:23', '2010-01-27 15:28:23');
INSERT INTO `pages_blocks` VALUES (46, 1566, NULL, '<p>CC</p>', 'active', '2010-01-27 15:28:23', '2010-01-27 15:28:23');
INSERT INTO `pages_blocks` VALUES (14, 1567, 1, NULL, 'archive', '2010-01-27 15:28:50', '2010-01-27 15:28:50');
INSERT INTO `pages_blocks` VALUES (15, 1567, 2, NULL, 'active', '2010-01-27 15:28:50', '2010-01-27 15:28:50');
INSERT INTO `pages_blocks` VALUES (16, 1567, 3, NULL, 'active', '2010-01-27 15:28:50', '2010-01-27 15:28:50');
INSERT INTO `pages_blocks` VALUES (42, 1568, NULL, '<p>ddddddd</p>', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (43, 1568, NULL, '', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (39, 1568, NULL, '<p>ddddd</p>', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (40, 1568, NULL, '', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (41, 1568, NULL, '', 'active', '2010-01-27 15:28:59', '2010-01-27 15:28:59');
INSERT INTO `pages_blocks` VALUES (17, 1569, NULL, '<p>AA</p>', 'archive', '2010-01-27 15:29:06', '2010-01-27 15:29:06');
INSERT INTO `pages_blocks` VALUES (18, 1569, NULL, '<p>BB</p>', 'active', '2010-01-27 15:29:06', '2010-01-27 15:29:06');
INSERT INTO `pages_blocks` VALUES (19, 1569, NULL, '<p>CC</p>', 'active', '2010-01-27 15:29:06', '2010-01-27 15:29:06');
INSERT INTO `pages_blocks` VALUES (160, 1571, NULL, '', 'active', '2010-01-27 15:41:09', '2010-01-27 15:41:09');
INSERT INTO `pages_blocks` VALUES (161, 1571, NULL, '', 'active', '2010-01-27 15:41:09', '2010-01-27 15:41:09');
INSERT INTO `pages_blocks` VALUES (162, 1571, NULL, '', 'active', '2010-01-27 15:41:09', '2010-01-27 15:41:09');
INSERT INTO `pages_blocks` VALUES (156, 1572, NULL, '', 'archive', '2010-01-27 15:52:09', '2010-01-27 15:52:09');
INSERT INTO `pages_blocks` VALUES (155, 1572, NULL, '', 'active', '2010-01-27 15:52:09', '2010-01-27 15:52:09');
INSERT INTO `pages_blocks` VALUES (154, 1572, NULL, '', 'active', '2010-01-27 15:52:09', '2010-01-27 15:52:09');
INSERT INTO `pages_blocks` VALUES (156, 1573, NULL, '', 'archive', '2010-01-27 16:57:02', '2010-01-27 16:57:02');
INSERT INTO `pages_blocks` VALUES (155, 1573, NULL, '', 'active', '2010-01-27 16:57:02', '2010-01-27 16:57:02');
INSERT INTO `pages_blocks` VALUES (154, 1573, NULL, '', 'active', '2010-01-27 16:57:02', '2010-01-27 16:57:02');
INSERT INTO `pages_blocks` VALUES (163, 1574, NULL, '', 'active', '2010-01-28 14:03:54', '2010-01-28 14:03:54');
INSERT INTO `pages_blocks` VALUES (164, 1574, NULL, '', 'active', '2010-01-28 14:03:54', '2010-01-28 14:03:54');
INSERT INTO `pages_blocks` VALUES (165, 1574, NULL, '', 'active', '2010-01-28 14:03:54', '2010-01-28 14:03:54');
INSERT INTO `pages_blocks` VALUES (17, 1575, NULL, '<p>De weg kwijt?</p>', 'active', '2010-01-28 14:40:06', '2010-01-28 14:40:06');
INSERT INTO `pages_blocks` VALUES (18, 1575, 5, NULL, 'active', '2010-01-28 14:40:06', '2010-01-28 14:40:06');
INSERT INTO `pages_blocks` VALUES (19, 1575, NULL, '', 'active', '2010-01-28 14:40:06', '2010-01-28 14:40:06');
INSERT INTO `pages_blocks` VALUES (156, 1576, NULL, '', 'archive', '2010-01-28 16:41:28', '2010-01-28 16:41:28');
INSERT INTO `pages_blocks` VALUES (155, 1576, NULL, '', 'active', '2010-01-28 16:41:28', '2010-01-28 16:41:28');
INSERT INTO `pages_blocks` VALUES (154, 1576, NULL, '', 'active', '2010-01-28 16:41:28', '2010-01-28 16:41:28');
INSERT INTO `pages_blocks` VALUES (156, 1577, NULL, '', 'active', '2010-01-28 16:41:52', '2010-01-28 16:41:52');
INSERT INTO `pages_blocks` VALUES (155, 1577, NULL, '', 'active', '2010-01-28 16:41:52', '2010-01-28 16:41:52');
INSERT INTO `pages_blocks` VALUES (154, 1577, NULL, '', 'active', '2010-01-28 16:41:52', '2010-01-28 16:41:52');
INSERT INTO `pages_blocks` VALUES (14, 1578, NULL, '', 'archive', '2010-01-29 13:21:58', '2010-01-29 13:21:58');
INSERT INTO `pages_blocks` VALUES (15, 1578, 2, NULL, 'active', '2010-01-29 13:21:58', '2010-01-29 13:21:58');
INSERT INTO `pages_blocks` VALUES (16, 1578, 3, NULL, 'active', '2010-01-29 13:21:58', '2010-01-29 13:21:58');
INSERT INTO `pages_blocks` VALUES (14, 1579, NULL, '', 'active', '2010-01-29 13:22:16', '2010-01-29 13:22:16');
INSERT INTO `pages_blocks` VALUES (15, 1579, 2, NULL, 'active', '2010-01-29 13:22:16', '2010-01-29 13:22:16');
INSERT INTO `pages_blocks` VALUES (16, 1579, 3, NULL, 'active', '2010-01-29 13:22:16', '2010-01-29 13:22:16');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras' AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `pages_extras`
-- 

INSERT INTO `pages_extras` VALUES (1, 'blog', 'block', 'Blog', NULL, NULL, 'N', 100);
INSERT INTO `pages_extras` VALUES (2, 'blog', 'widget', 'RecentComments', 'recent_comments', NULL, 'N', 101);
INSERT INTO `pages_extras` VALUES (3, 'snippets', 'widget', 'Snippets', 'detail', 'a:1:{s:11:"extra_label";s:4:"nr 1";}', 'N', 200);
INSERT INTO `pages_extras` VALUES (4, 'news', 'block', 'News', NULL, NULL, 'N', 300);
INSERT INTO `pages_extras` VALUES (5, 'sitemap', 'block', 'Sitemap', NULL, NULL, 'N', 1000);
INSERT INTO `pages_extras` VALUES (6, 'contact', 'block', 'Contact', NULL, NULL, 'N', 1);

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

INSERT INTO `pages_templates` VALUES (1, 'home', 'core/layout/templates/home.tpl', 3, 'Y', 'N', 'a:2:{s:5:"names";a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}s:6:"format";s:14:"[0,1],[2,none]";}');
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
-- Table structure for table `snippets`
-- 

DROP TABLE IF EXISTS `snippets`;
CREATE TABLE IF NOT EXISTS `snippets` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sometimes we need editable parts in the templates, this modu' AUTO_INCREMENT=27 ;

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
INSERT INTO `snippets` VALUES (2, 13, 1, 'nl', 'snipper de snip', '<p>Hier de inhoud van mijn magnifieke snippet</p>', 'N', 'archived', '2009-10-21 14:16:26', '2009-11-19 09:08:01');
INSERT INTO `snippets` VALUES (1, 14, 1, 'nl', 'test', '<p>test</p>', 'N', 'archived', '2009-10-21 14:04:25', '2009-12-02 15:55:11');
INSERT INTO `snippets` VALUES (4, 15, 1, 'nl', 'dikke test', '<p>test</p>', 'N', 'archived', '2009-12-16 13:12:48', '2009-12-16 13:12:48');
INSERT INTO `snippets` VALUES (4, 16, 1, 'nl', 'dikke test', '<p>test</p>', 'N', 'archived', '2009-12-16 13:12:48', '2009-12-16 13:12:56');
INSERT INTO `snippets` VALUES (1, 17, 1, 'nl', 'test', '<p>test</p>', 'N', 'active', '2009-10-21 14:04:25', '2009-12-23 18:25:48');
INSERT INTO `snippets` VALUES (2, 18, 1, 'nl', 'snipper de snip', '<p>Hier de inhoud van mijn magnifieke snippet</p>', 'N', 'active', '2009-10-21 14:16:26', '2010-01-06 16:47:11');
INSERT INTO `snippets` VALUES (4, 19, 1, 'nl', 'dikke test', '<p>test</p>', 'N', 'active', '2009-12-16 13:12:48', '2010-01-06 18:26:34');
INSERT INTO `snippets` VALUES (5, 20, 1, 'nl', 'fasdfasdf', '<p>asdfasdf</p>', 'N', 'active', '2010-01-11 18:08:57', '2010-01-11 18:08:57');
INSERT INTO `snippets` VALUES (6, 21, 1, 'nl', 'asdf asdfsad', '<p>asdfasdf</p>', 'N', 'archived', '2010-01-11 18:09:04', '2010-01-11 18:09:04');
INSERT INTO `snippets` VALUES (7, 22, 1, 'nl', 'sadfasdfasdf', '<p>asdfasdf asdfasdf</p>', 'N', 'active', '2010-01-11 18:09:12', '2010-01-11 18:09:12');
INSERT INTO `snippets` VALUES (6, 23, 1, 'nl', 'asdf asdfsad', '<p>asdfasdf</p>', 'N', 'archived', '2010-01-11 18:09:04', '2010-01-15 11:06:21');
INSERT INTO `snippets` VALUES (6, 24, 1, 'nl', 'asdf asdfsad', '<p>asdfasdf</p>', 'N', 'archived', '2010-01-11 18:09:04', '2010-01-15 11:21:23');
INSERT INTO `snippets` VALUES (6, 25, 1, 'nl', 'asdf asdfsad', '<p>asdfasdf</p>', 'N', 'active', '2010-01-11 18:09:04', '2010-01-27 16:45:09');
INSERT INTO `snippets` VALUES (8, 26, 1, 'nl', 'Test!', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id magna. Proin euismod vestibulum tortor. Vestibulum eget nisl. Donec interdum quam at nunc. In laoreet orci sit amet sem. In sed metus ac nunc blandit ultricies. Maecenas sed tortor. Sed velit velit, mollis quis, ultricies tincidunt, dictum ac, felis. Integer hendrerit consectetur libero. Duis sem. Mauris tellus justo, sollicitudin at, vehicula eget, auctor vel, odio. Proin mattis. Mauris mollis elit sit amet lectus. Vestibulum in tortor sodales elit sollicitudin gravida. Integer scelerisque sollicitudin velit. Aliquam erat volutpat. Sed ut nisl congue justo pharetra accumsan.</p>', 'N', 'active', '2010-01-28 17:25:28', '2010-01-28 17:25:28');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `tags`
-- 

INSERT INTO `tags` VALUES (6, 'nl', 'meta', 1, 'meta');
INSERT INTO `tags` VALUES (4, 'nl', 'developer', 4, 'developer');
INSERT INTO `tags` VALUES (15, 'nl', 'afblijven', 1, 'afblijven');
INSERT INTO `tags` VALUES (14, 'nl', 'test2', 1, 'test2');
INSERT INTO `tags` VALUES (12, 'nl', 'test', 1, 'test');
INSERT INTO `tags` VALUES (13, 'nl', 'test1', 1, 'test1');
INSERT INTO `tags` VALUES (16, 'nl', 'arf', 0, 'arf');

-- --------------------------------------------------------

-- 
-- Table structure for table `test`
-- 

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL auto_increment,
  `naam` varchar(255) collate utf8_unicode_ci NOT NULL,
  `waarde` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1000 ;

-- 
-- Dumping data for table `test`
-- 

INSERT INTO `test` VALUES (1, 'hahahahhah 1', 'schimmelfuckers 1');
INSERT INTO `test` VALUES (2, 'hahahahhah 2', 'schimmelfuckers 2');
INSERT INTO `test` VALUES (3, 'hahahahhah 3', 'schimmelfuckers 3');
INSERT INTO `test` VALUES (4, 'hahahahhah 4', 'schimmelfuckers 4');
INSERT INTO `test` VALUES (5, 'hahahahhah 5', 'schimmelfuckers 5');
INSERT INTO `test` VALUES (6, 'hahahahhah 6', 'schimmelfuckers 6');
INSERT INTO `test` VALUES (7, 'hahahahhah 7', 'schimmelfuckers 7');
INSERT INTO `test` VALUES (8, 'hahahahhah 8', 'schimmelfuckers 8');
INSERT INTO `test` VALUES (9, 'hahahahhah 9', 'schimmelfuckers 9');
INSERT INTO `test` VALUES (10, 'hahahahhah 10', 'schimmelfuckers 10');
INSERT INTO `test` VALUES (11, 'hahahahhah 11', 'schimmelfuckers 11');
INSERT INTO `test` VALUES (12, 'hahahahhah 12', 'schimmelfuckers 12');
INSERT INTO `test` VALUES (13, 'hahahahhah 13', 'schimmelfuckers 13');
INSERT INTO `test` VALUES (14, 'hahahahhah 14', 'schimmelfuckers 14');
INSERT INTO `test` VALUES (15, 'hahahahhah 15', 'schimmelfuckers 15');
INSERT INTO `test` VALUES (16, 'hahahahhah 16', 'schimmelfuckers 16');
INSERT INTO `test` VALUES (17, 'hahahahhah 17', 'schimmelfuckers 17');
INSERT INTO `test` VALUES (18, 'hahahahhah 18', 'schimmelfuckers 18');
INSERT INTO `test` VALUES (19, 'hahahahhah 19', 'schimmelfuckers 19');
INSERT INTO `test` VALUES (20, 'hahahahhah 20', 'schimmelfuckers 20');
INSERT INTO `test` VALUES (21, 'hahahahhah 21', 'schimmelfuckers 21');
INSERT INTO `test` VALUES (22, 'hahahahhah 22', 'schimmelfuckers 22');
INSERT INTO `test` VALUES (23, 'hahahahhah 23', 'schimmelfuckers 23');
INSERT INTO `test` VALUES (24, 'hahahahhah 24', 'schimmelfuckers 24');
INSERT INTO `test` VALUES (25, 'hahahahhah 25', 'schimmelfuckers 25');
INSERT INTO `test` VALUES (26, 'hahahahhah 26', 'schimmelfuckers 26');
INSERT INTO `test` VALUES (27, 'hahahahhah 27', 'schimmelfuckers 27');
INSERT INTO `test` VALUES (28, 'hahahahhah 28', 'schimmelfuckers 28');
INSERT INTO `test` VALUES (29, 'hahahahhah 29', 'schimmelfuckers 29');
INSERT INTO `test` VALUES (30, 'hahahahhah 30', 'schimmelfuckers 30');
INSERT INTO `test` VALUES (31, 'hahahahhah 31', 'schimmelfuckers 31');
INSERT INTO `test` VALUES (32, 'hahahahhah 32', 'schimmelfuckers 32');
INSERT INTO `test` VALUES (33, 'hahahahhah 33', 'schimmelfuckers 33');
INSERT INTO `test` VALUES (34, 'hahahahhah 34', 'schimmelfuckers 34');
INSERT INTO `test` VALUES (35, 'hahahahhah 35', 'schimmelfuckers 35');
INSERT INTO `test` VALUES (36, 'hahahahhah 36', 'schimmelfuckers 36');
INSERT INTO `test` VALUES (37, 'hahahahhah 37', 'schimmelfuckers 37');
INSERT INTO `test` VALUES (38, 'hahahahhah 38', 'schimmelfuckers 38');
INSERT INTO `test` VALUES (39, 'hahahahhah 39', 'schimmelfuckers 39');
INSERT INTO `test` VALUES (40, 'hahahahhah 40', 'schimmelfuckers 40');
INSERT INTO `test` VALUES (41, 'hahahahhah 41', 'schimmelfuckers 41');
INSERT INTO `test` VALUES (42, 'hahahahhah 42', 'schimmelfuckers 42');
INSERT INTO `test` VALUES (43, 'hahahahhah 43', 'schimmelfuckers 43');
INSERT INTO `test` VALUES (44, 'hahahahhah 44', 'schimmelfuckers 44');
INSERT INTO `test` VALUES (45, 'hahahahhah 45', 'schimmelfuckers 45');
INSERT INTO `test` VALUES (46, 'hahahahhah 46', 'schimmelfuckers 46');
INSERT INTO `test` VALUES (47, 'hahahahhah 47', 'schimmelfuckers 47');
INSERT INTO `test` VALUES (48, 'hahahahhah 48', 'schimmelfuckers 48');
INSERT INTO `test` VALUES (49, 'hahahahhah 49', 'schimmelfuckers 49');
INSERT INTO `test` VALUES (50, 'hahahahhah 50', 'schimmelfuckers 50');
INSERT INTO `test` VALUES (51, 'hahahahhah 51', 'schimmelfuckers 51');
INSERT INTO `test` VALUES (52, 'hahahahhah 52', 'schimmelfuckers 52');
INSERT INTO `test` VALUES (53, 'hahahahhah 53', 'schimmelfuckers 53');
INSERT INTO `test` VALUES (54, 'hahahahhah 54', 'schimmelfuckers 54');
INSERT INTO `test` VALUES (55, 'hahahahhah 55', 'schimmelfuckers 55');
INSERT INTO `test` VALUES (56, 'hahahahhah 56', 'schimmelfuckers 56');
INSERT INTO `test` VALUES (57, 'hahahahhah 57', 'schimmelfuckers 57');
INSERT INTO `test` VALUES (58, 'hahahahhah 58', 'schimmelfuckers 58');
INSERT INTO `test` VALUES (59, 'hahahahhah 59', 'schimmelfuckers 59');
INSERT INTO `test` VALUES (60, 'hahahahhah 60', 'schimmelfuckers 60');
INSERT INTO `test` VALUES (61, 'hahahahhah 61', 'schimmelfuckers 61');
INSERT INTO `test` VALUES (62, 'hahahahhah 62', 'schimmelfuckers 62');
INSERT INTO `test` VALUES (63, 'hahahahhah 63', 'schimmelfuckers 63');
INSERT INTO `test` VALUES (64, 'hahahahhah 64', 'schimmelfuckers 64');
INSERT INTO `test` VALUES (65, 'hahahahhah 65', 'schimmelfuckers 65');
INSERT INTO `test` VALUES (66, 'hahahahhah 66', 'schimmelfuckers 66');
INSERT INTO `test` VALUES (67, 'hahahahhah 67', 'schimmelfuckers 67');
INSERT INTO `test` VALUES (68, 'hahahahhah 68', 'schimmelfuckers 68');
INSERT INTO `test` VALUES (69, 'hahahahhah 69', 'schimmelfuckers 69');
INSERT INTO `test` VALUES (70, 'hahahahhah 70', 'schimmelfuckers 70');
INSERT INTO `test` VALUES (71, 'hahahahhah 71', 'schimmelfuckers 71');
INSERT INTO `test` VALUES (72, 'hahahahhah 72', 'schimmelfuckers 72');
INSERT INTO `test` VALUES (73, 'hahahahhah 73', 'schimmelfuckers 73');
INSERT INTO `test` VALUES (74, 'hahahahhah 74', 'schimmelfuckers 74');
INSERT INTO `test` VALUES (75, 'hahahahhah 75', 'schimmelfuckers 75');
INSERT INTO `test` VALUES (76, 'hahahahhah 76', 'schimmelfuckers 76');
INSERT INTO `test` VALUES (77, 'hahahahhah 77', 'schimmelfuckers 77');
INSERT INTO `test` VALUES (78, 'hahahahhah 78', 'schimmelfuckers 78');
INSERT INTO `test` VALUES (79, 'hahahahhah 79', 'schimmelfuckers 79');
INSERT INTO `test` VALUES (80, 'hahahahhah 80', 'schimmelfuckers 80');
INSERT INTO `test` VALUES (81, 'hahahahhah 81', 'schimmelfuckers 81');
INSERT INTO `test` VALUES (82, 'hahahahhah 82', 'schimmelfuckers 82');
INSERT INTO `test` VALUES (83, 'hahahahhah 83', 'schimmelfuckers 83');
INSERT INTO `test` VALUES (84, 'hahahahhah 84', 'schimmelfuckers 84');
INSERT INTO `test` VALUES (85, 'hahahahhah 85', 'schimmelfuckers 85');
INSERT INTO `test` VALUES (86, 'hahahahhah 86', 'schimmelfuckers 86');
INSERT INTO `test` VALUES (87, 'hahahahhah 87', 'schimmelfuckers 87');
INSERT INTO `test` VALUES (88, 'hahahahhah 88', 'schimmelfuckers 88');
INSERT INTO `test` VALUES (89, 'hahahahhah 89', 'schimmelfuckers 89');
INSERT INTO `test` VALUES (90, 'hahahahhah 90', 'schimmelfuckers 90');
INSERT INTO `test` VALUES (91, 'hahahahhah 91', 'schimmelfuckers 91');
INSERT INTO `test` VALUES (92, 'hahahahhah 92', 'schimmelfuckers 92');
INSERT INTO `test` VALUES (93, 'hahahahhah 93', 'schimmelfuckers 93');
INSERT INTO `test` VALUES (94, 'hahahahhah 94', 'schimmelfuckers 94');
INSERT INTO `test` VALUES (95, 'hahahahhah 95', 'schimmelfuckers 95');
INSERT INTO `test` VALUES (96, 'hahahahhah 96', 'schimmelfuckers 96');
INSERT INTO `test` VALUES (97, 'hahahahhah 97', 'schimmelfuckers 97');
INSERT INTO `test` VALUES (98, 'hahahahhah 98', 'schimmelfuckers 98');
INSERT INTO `test` VALUES (99, 'hahahahhah 99', 'schimmelfuckers 99');
INSERT INTO `test` VALUES (100, 'hahahahhah 100', 'schimmelfuckers 100');
INSERT INTO `test` VALUES (101, 'hahahahhah 101', 'schimmelfuckers 101');
INSERT INTO `test` VALUES (102, 'hahahahhah 102', 'schimmelfuckers 102');
INSERT INTO `test` VALUES (103, 'hahahahhah 103', 'schimmelfuckers 103');
INSERT INTO `test` VALUES (104, 'hahahahhah 104', 'schimmelfuckers 104');
INSERT INTO `test` VALUES (105, 'hahahahhah 105', 'schimmelfuckers 105');
INSERT INTO `test` VALUES (106, 'hahahahhah 106', 'schimmelfuckers 106');
INSERT INTO `test` VALUES (107, 'hahahahhah 107', 'schimmelfuckers 107');
INSERT INTO `test` VALUES (108, 'hahahahhah 108', 'schimmelfuckers 108');
INSERT INTO `test` VALUES (109, 'hahahahhah 109', 'schimmelfuckers 109');
INSERT INTO `test` VALUES (110, 'hahahahhah 110', 'schimmelfuckers 110');
INSERT INTO `test` VALUES (111, 'hahahahhah 111', 'schimmelfuckers 111');
INSERT INTO `test` VALUES (112, 'hahahahhah 112', 'schimmelfuckers 112');
INSERT INTO `test` VALUES (113, 'hahahahhah 113', 'schimmelfuckers 113');
INSERT INTO `test` VALUES (114, 'hahahahhah 114', 'schimmelfuckers 114');
INSERT INTO `test` VALUES (115, 'hahahahhah 115', 'schimmelfuckers 115');
INSERT INTO `test` VALUES (116, 'hahahahhah 116', 'schimmelfuckers 116');
INSERT INTO `test` VALUES (117, 'hahahahhah 117', 'schimmelfuckers 117');
INSERT INTO `test` VALUES (118, 'hahahahhah 118', 'schimmelfuckers 118');
INSERT INTO `test` VALUES (119, 'hahahahhah 119', 'schimmelfuckers 119');
INSERT INTO `test` VALUES (120, 'hahahahhah 120', 'schimmelfuckers 120');
INSERT INTO `test` VALUES (121, 'hahahahhah 121', 'schimmelfuckers 121');
INSERT INTO `test` VALUES (122, 'hahahahhah 122', 'schimmelfuckers 122');
INSERT INTO `test` VALUES (123, 'hahahahhah 123', 'schimmelfuckers 123');
INSERT INTO `test` VALUES (124, 'hahahahhah 124', 'schimmelfuckers 124');
INSERT INTO `test` VALUES (125, 'hahahahhah 125', 'schimmelfuckers 125');
INSERT INTO `test` VALUES (126, 'hahahahhah 126', 'schimmelfuckers 126');
INSERT INTO `test` VALUES (127, 'hahahahhah 127', 'schimmelfuckers 127');
INSERT INTO `test` VALUES (128, 'hahahahhah 128', 'schimmelfuckers 128');
INSERT INTO `test` VALUES (129, 'hahahahhah 129', 'schimmelfuckers 129');
INSERT INTO `test` VALUES (130, 'hahahahhah 130', 'schimmelfuckers 130');
INSERT INTO `test` VALUES (131, 'hahahahhah 131', 'schimmelfuckers 131');
INSERT INTO `test` VALUES (132, 'hahahahhah 132', 'schimmelfuckers 132');
INSERT INTO `test` VALUES (133, 'hahahahhah 133', 'schimmelfuckers 133');
INSERT INTO `test` VALUES (134, 'hahahahhah 134', 'schimmelfuckers 134');
INSERT INTO `test` VALUES (135, 'hahahahhah 135', 'schimmelfuckers 135');
INSERT INTO `test` VALUES (136, 'hahahahhah 136', 'schimmelfuckers 136');
INSERT INTO `test` VALUES (137, 'hahahahhah 137', 'schimmelfuckers 137');
INSERT INTO `test` VALUES (138, 'hahahahhah 138', 'schimmelfuckers 138');
INSERT INTO `test` VALUES (139, 'hahahahhah 139', 'schimmelfuckers 139');
INSERT INTO `test` VALUES (140, 'hahahahhah 140', 'schimmelfuckers 140');
INSERT INTO `test` VALUES (141, 'hahahahhah 141', 'schimmelfuckers 141');
INSERT INTO `test` VALUES (142, 'hahahahhah 142', 'schimmelfuckers 142');
INSERT INTO `test` VALUES (143, 'hahahahhah 143', 'schimmelfuckers 143');
INSERT INTO `test` VALUES (144, 'hahahahhah 144', 'schimmelfuckers 144');
INSERT INTO `test` VALUES (145, 'hahahahhah 145', 'schimmelfuckers 145');
INSERT INTO `test` VALUES (146, 'hahahahhah 146', 'schimmelfuckers 146');
INSERT INTO `test` VALUES (147, 'hahahahhah 147', 'schimmelfuckers 147');
INSERT INTO `test` VALUES (148, 'hahahahhah 148', 'schimmelfuckers 148');
INSERT INTO `test` VALUES (149, 'hahahahhah 149', 'schimmelfuckers 149');
INSERT INTO `test` VALUES (150, 'hahahahhah 150', 'schimmelfuckers 150');
INSERT INTO `test` VALUES (151, 'hahahahhah 151', 'schimmelfuckers 151');
INSERT INTO `test` VALUES (152, 'hahahahhah 152', 'schimmelfuckers 152');
INSERT INTO `test` VALUES (153, 'hahahahhah 153', 'schimmelfuckers 153');
INSERT INTO `test` VALUES (154, 'hahahahhah 154', 'schimmelfuckers 154');
INSERT INTO `test` VALUES (155, 'hahahahhah 155', 'schimmelfuckers 155');
INSERT INTO `test` VALUES (156, 'hahahahhah 156', 'schimmelfuckers 156');
INSERT INTO `test` VALUES (157, 'hahahahhah 157', 'schimmelfuckers 157');
INSERT INTO `test` VALUES (158, 'hahahahhah 158', 'schimmelfuckers 158');
INSERT INTO `test` VALUES (159, 'hahahahhah 159', 'schimmelfuckers 159');
INSERT INTO `test` VALUES (160, 'hahahahhah 160', 'schimmelfuckers 160');
INSERT INTO `test` VALUES (161, 'hahahahhah 161', 'schimmelfuckers 161');
INSERT INTO `test` VALUES (162, 'hahahahhah 162', 'schimmelfuckers 162');
INSERT INTO `test` VALUES (163, 'hahahahhah 163', 'schimmelfuckers 163');
INSERT INTO `test` VALUES (164, 'hahahahhah 164', 'schimmelfuckers 164');
INSERT INTO `test` VALUES (165, 'hahahahhah 165', 'schimmelfuckers 165');
INSERT INTO `test` VALUES (166, 'hahahahhah 166', 'schimmelfuckers 166');
INSERT INTO `test` VALUES (167, 'hahahahhah 167', 'schimmelfuckers 167');
INSERT INTO `test` VALUES (168, 'hahahahhah 168', 'schimmelfuckers 168');
INSERT INTO `test` VALUES (169, 'hahahahhah 169', 'schimmelfuckers 169');
INSERT INTO `test` VALUES (170, 'hahahahhah 170', 'schimmelfuckers 170');
INSERT INTO `test` VALUES (171, 'hahahahhah 171', 'schimmelfuckers 171');
INSERT INTO `test` VALUES (172, 'hahahahhah 172', 'schimmelfuckers 172');
INSERT INTO `test` VALUES (173, 'hahahahhah 173', 'schimmelfuckers 173');
INSERT INTO `test` VALUES (174, 'hahahahhah 174', 'schimmelfuckers 174');
INSERT INTO `test` VALUES (175, 'hahahahhah 175', 'schimmelfuckers 175');
INSERT INTO `test` VALUES (176, 'hahahahhah 176', 'schimmelfuckers 176');
INSERT INTO `test` VALUES (177, 'hahahahhah 177', 'schimmelfuckers 177');
INSERT INTO `test` VALUES (178, 'hahahahhah 178', 'schimmelfuckers 178');
INSERT INTO `test` VALUES (179, 'hahahahhah 179', 'schimmelfuckers 179');
INSERT INTO `test` VALUES (180, 'hahahahhah 180', 'schimmelfuckers 180');
INSERT INTO `test` VALUES (181, 'hahahahhah 181', 'schimmelfuckers 181');
INSERT INTO `test` VALUES (182, 'hahahahhah 182', 'schimmelfuckers 182');
INSERT INTO `test` VALUES (183, 'hahahahhah 183', 'schimmelfuckers 183');
INSERT INTO `test` VALUES (184, 'hahahahhah 184', 'schimmelfuckers 184');
INSERT INTO `test` VALUES (185, 'hahahahhah 185', 'schimmelfuckers 185');
INSERT INTO `test` VALUES (186, 'hahahahhah 186', 'schimmelfuckers 186');
INSERT INTO `test` VALUES (187, 'hahahahhah 187', 'schimmelfuckers 187');
INSERT INTO `test` VALUES (188, 'hahahahhah 188', 'schimmelfuckers 188');
INSERT INTO `test` VALUES (189, 'hahahahhah 189', 'schimmelfuckers 189');
INSERT INTO `test` VALUES (190, 'hahahahhah 190', 'schimmelfuckers 190');
INSERT INTO `test` VALUES (191, 'hahahahhah 191', 'schimmelfuckers 191');
INSERT INTO `test` VALUES (192, 'hahahahhah 192', 'schimmelfuckers 192');
INSERT INTO `test` VALUES (193, 'hahahahhah 193', 'schimmelfuckers 193');
INSERT INTO `test` VALUES (194, 'hahahahhah 194', 'schimmelfuckers 194');
INSERT INTO `test` VALUES (195, 'hahahahhah 195', 'schimmelfuckers 195');
INSERT INTO `test` VALUES (196, 'hahahahhah 196', 'schimmelfuckers 196');
INSERT INTO `test` VALUES (197, 'hahahahhah 197', 'schimmelfuckers 197');
INSERT INTO `test` VALUES (198, 'hahahahhah 198', 'schimmelfuckers 198');
INSERT INTO `test` VALUES (199, 'hahahahhah 199', 'schimmelfuckers 199');
INSERT INTO `test` VALUES (200, 'hahahahhah 200', 'schimmelfuckers 200');
INSERT INTO `test` VALUES (201, 'hahahahhah 201', 'schimmelfuckers 201');
INSERT INTO `test` VALUES (202, 'hahahahhah 202', 'schimmelfuckers 202');
INSERT INTO `test` VALUES (203, 'hahahahhah 203', 'schimmelfuckers 203');
INSERT INTO `test` VALUES (204, 'hahahahhah 204', 'schimmelfuckers 204');
INSERT INTO `test` VALUES (205, 'hahahahhah 205', 'schimmelfuckers 205');
INSERT INTO `test` VALUES (206, 'hahahahhah 206', 'schimmelfuckers 206');
INSERT INTO `test` VALUES (207, 'hahahahhah 207', 'schimmelfuckers 207');
INSERT INTO `test` VALUES (208, 'hahahahhah 208', 'schimmelfuckers 208');
INSERT INTO `test` VALUES (209, 'hahahahhah 209', 'schimmelfuckers 209');
INSERT INTO `test` VALUES (210, 'hahahahhah 210', 'schimmelfuckers 210');
INSERT INTO `test` VALUES (211, 'hahahahhah 211', 'schimmelfuckers 211');
INSERT INTO `test` VALUES (212, 'hahahahhah 212', 'schimmelfuckers 212');
INSERT INTO `test` VALUES (213, 'hahahahhah 213', 'schimmelfuckers 213');
INSERT INTO `test` VALUES (214, 'hahahahhah 214', 'schimmelfuckers 214');
INSERT INTO `test` VALUES (215, 'hahahahhah 215', 'schimmelfuckers 215');
INSERT INTO `test` VALUES (216, 'hahahahhah 216', 'schimmelfuckers 216');
INSERT INTO `test` VALUES (217, 'hahahahhah 217', 'schimmelfuckers 217');
INSERT INTO `test` VALUES (218, 'hahahahhah 218', 'schimmelfuckers 218');
INSERT INTO `test` VALUES (219, 'hahahahhah 219', 'schimmelfuckers 219');
INSERT INTO `test` VALUES (220, 'hahahahhah 220', 'schimmelfuckers 220');
INSERT INTO `test` VALUES (221, 'hahahahhah 221', 'schimmelfuckers 221');
INSERT INTO `test` VALUES (222, 'hahahahhah 222', 'schimmelfuckers 222');
INSERT INTO `test` VALUES (223, 'hahahahhah 223', 'schimmelfuckers 223');
INSERT INTO `test` VALUES (224, 'hahahahhah 224', 'schimmelfuckers 224');
INSERT INTO `test` VALUES (225, 'hahahahhah 225', 'schimmelfuckers 225');
INSERT INTO `test` VALUES (226, 'hahahahhah 226', 'schimmelfuckers 226');
INSERT INTO `test` VALUES (227, 'hahahahhah 227', 'schimmelfuckers 227');
INSERT INTO `test` VALUES (228, 'hahahahhah 228', 'schimmelfuckers 228');
INSERT INTO `test` VALUES (229, 'hahahahhah 229', 'schimmelfuckers 229');
INSERT INTO `test` VALUES (230, 'hahahahhah 230', 'schimmelfuckers 230');
INSERT INTO `test` VALUES (231, 'hahahahhah 231', 'schimmelfuckers 231');
INSERT INTO `test` VALUES (232, 'hahahahhah 232', 'schimmelfuckers 232');
INSERT INTO `test` VALUES (233, 'hahahahhah 233', 'schimmelfuckers 233');
INSERT INTO `test` VALUES (234, 'hahahahhah 234', 'schimmelfuckers 234');
INSERT INTO `test` VALUES (235, 'hahahahhah 235', 'schimmelfuckers 235');
INSERT INTO `test` VALUES (236, 'hahahahhah 236', 'schimmelfuckers 236');
INSERT INTO `test` VALUES (237, 'hahahahhah 237', 'schimmelfuckers 237');
INSERT INTO `test` VALUES (238, 'hahahahhah 238', 'schimmelfuckers 238');
INSERT INTO `test` VALUES (239, 'hahahahhah 239', 'schimmelfuckers 239');
INSERT INTO `test` VALUES (240, 'hahahahhah 240', 'schimmelfuckers 240');
INSERT INTO `test` VALUES (241, 'hahahahhah 241', 'schimmelfuckers 241');
INSERT INTO `test` VALUES (242, 'hahahahhah 242', 'schimmelfuckers 242');
INSERT INTO `test` VALUES (243, 'hahahahhah 243', 'schimmelfuckers 243');
INSERT INTO `test` VALUES (244, 'hahahahhah 244', 'schimmelfuckers 244');
INSERT INTO `test` VALUES (245, 'hahahahhah 245', 'schimmelfuckers 245');
INSERT INTO `test` VALUES (246, 'hahahahhah 246', 'schimmelfuckers 246');
INSERT INTO `test` VALUES (247, 'hahahahhah 247', 'schimmelfuckers 247');
INSERT INTO `test` VALUES (248, 'hahahahhah 248', 'schimmelfuckers 248');
INSERT INTO `test` VALUES (249, 'hahahahhah 249', 'schimmelfuckers 249');
INSERT INTO `test` VALUES (250, 'hahahahhah 250', 'schimmelfuckers 250');
INSERT INTO `test` VALUES (251, 'hahahahhah 251', 'schimmelfuckers 251');
INSERT INTO `test` VALUES (252, 'hahahahhah 252', 'schimmelfuckers 252');
INSERT INTO `test` VALUES (253, 'hahahahhah 253', 'schimmelfuckers 253');
INSERT INTO `test` VALUES (254, 'hahahahhah 254', 'schimmelfuckers 254');
INSERT INTO `test` VALUES (255, 'hahahahhah 255', 'schimmelfuckers 255');
INSERT INTO `test` VALUES (256, 'hahahahhah 256', 'schimmelfuckers 256');
INSERT INTO `test` VALUES (257, 'hahahahhah 257', 'schimmelfuckers 257');
INSERT INTO `test` VALUES (258, 'hahahahhah 258', 'schimmelfuckers 258');
INSERT INTO `test` VALUES (259, 'hahahahhah 259', 'schimmelfuckers 259');
INSERT INTO `test` VALUES (260, 'hahahahhah 260', 'schimmelfuckers 260');
INSERT INTO `test` VALUES (261, 'hahahahhah 261', 'schimmelfuckers 261');
INSERT INTO `test` VALUES (262, 'hahahahhah 262', 'schimmelfuckers 262');
INSERT INTO `test` VALUES (263, 'hahahahhah 263', 'schimmelfuckers 263');
INSERT INTO `test` VALUES (264, 'hahahahhah 264', 'schimmelfuckers 264');
INSERT INTO `test` VALUES (265, 'hahahahhah 265', 'schimmelfuckers 265');
INSERT INTO `test` VALUES (266, 'hahahahhah 266', 'schimmelfuckers 266');
INSERT INTO `test` VALUES (267, 'hahahahhah 267', 'schimmelfuckers 267');
INSERT INTO `test` VALUES (268, 'hahahahhah 268', 'schimmelfuckers 268');
INSERT INTO `test` VALUES (269, 'hahahahhah 269', 'schimmelfuckers 269');
INSERT INTO `test` VALUES (270, 'hahahahhah 270', 'schimmelfuckers 270');
INSERT INTO `test` VALUES (271, 'hahahahhah 271', 'schimmelfuckers 271');
INSERT INTO `test` VALUES (272, 'hahahahhah 272', 'schimmelfuckers 272');
INSERT INTO `test` VALUES (273, 'hahahahhah 273', 'schimmelfuckers 273');
INSERT INTO `test` VALUES (274, 'hahahahhah 274', 'schimmelfuckers 274');
INSERT INTO `test` VALUES (275, 'hahahahhah 275', 'schimmelfuckers 275');
INSERT INTO `test` VALUES (276, 'hahahahhah 276', 'schimmelfuckers 276');
INSERT INTO `test` VALUES (277, 'hahahahhah 277', 'schimmelfuckers 277');
INSERT INTO `test` VALUES (278, 'hahahahhah 278', 'schimmelfuckers 278');
INSERT INTO `test` VALUES (279, 'hahahahhah 279', 'schimmelfuckers 279');
INSERT INTO `test` VALUES (280, 'hahahahhah 280', 'schimmelfuckers 280');
INSERT INTO `test` VALUES (281, 'hahahahhah 281', 'schimmelfuckers 281');
INSERT INTO `test` VALUES (282, 'hahahahhah 282', 'schimmelfuckers 282');
INSERT INTO `test` VALUES (283, 'hahahahhah 283', 'schimmelfuckers 283');
INSERT INTO `test` VALUES (284, 'hahahahhah 284', 'schimmelfuckers 284');
INSERT INTO `test` VALUES (285, 'hahahahhah 285', 'schimmelfuckers 285');
INSERT INTO `test` VALUES (286, 'hahahahhah 286', 'schimmelfuckers 286');
INSERT INTO `test` VALUES (287, 'hahahahhah 287', 'schimmelfuckers 287');
INSERT INTO `test` VALUES (288, 'hahahahhah 288', 'schimmelfuckers 288');
INSERT INTO `test` VALUES (289, 'hahahahhah 289', 'schimmelfuckers 289');
INSERT INTO `test` VALUES (290, 'hahahahhah 290', 'schimmelfuckers 290');
INSERT INTO `test` VALUES (291, 'hahahahhah 291', 'schimmelfuckers 291');
INSERT INTO `test` VALUES (292, 'hahahahhah 292', 'schimmelfuckers 292');
INSERT INTO `test` VALUES (293, 'hahahahhah 293', 'schimmelfuckers 293');
INSERT INTO `test` VALUES (294, 'hahahahhah 294', 'schimmelfuckers 294');
INSERT INTO `test` VALUES (295, 'hahahahhah 295', 'schimmelfuckers 295');
INSERT INTO `test` VALUES (296, 'hahahahhah 296', 'schimmelfuckers 296');
INSERT INTO `test` VALUES (297, 'hahahahhah 297', 'schimmelfuckers 297');
INSERT INTO `test` VALUES (298, 'hahahahhah 298', 'schimmelfuckers 298');
INSERT INTO `test` VALUES (299, 'hahahahhah 299', 'schimmelfuckers 299');
INSERT INTO `test` VALUES (300, 'hahahahhah 300', 'schimmelfuckers 300');
INSERT INTO `test` VALUES (301, 'hahahahhah 301', 'schimmelfuckers 301');
INSERT INTO `test` VALUES (302, 'hahahahhah 302', 'schimmelfuckers 302');
INSERT INTO `test` VALUES (303, 'hahahahhah 303', 'schimmelfuckers 303');
INSERT INTO `test` VALUES (304, 'hahahahhah 304', 'schimmelfuckers 304');
INSERT INTO `test` VALUES (305, 'hahahahhah 305', 'schimmelfuckers 305');
INSERT INTO `test` VALUES (306, 'hahahahhah 306', 'schimmelfuckers 306');
INSERT INTO `test` VALUES (307, 'hahahahhah 307', 'schimmelfuckers 307');
INSERT INTO `test` VALUES (308, 'hahahahhah 308', 'schimmelfuckers 308');
INSERT INTO `test` VALUES (309, 'hahahahhah 309', 'schimmelfuckers 309');
INSERT INTO `test` VALUES (310, 'hahahahhah 310', 'schimmelfuckers 310');
INSERT INTO `test` VALUES (311, 'hahahahhah 311', 'schimmelfuckers 311');
INSERT INTO `test` VALUES (312, 'hahahahhah 312', 'schimmelfuckers 312');
INSERT INTO `test` VALUES (313, 'hahahahhah 313', 'schimmelfuckers 313');
INSERT INTO `test` VALUES (314, 'hahahahhah 314', 'schimmelfuckers 314');
INSERT INTO `test` VALUES (315, 'hahahahhah 315', 'schimmelfuckers 315');
INSERT INTO `test` VALUES (316, 'hahahahhah 316', 'schimmelfuckers 316');
INSERT INTO `test` VALUES (317, 'hahahahhah 317', 'schimmelfuckers 317');
INSERT INTO `test` VALUES (318, 'hahahahhah 318', 'schimmelfuckers 318');
INSERT INTO `test` VALUES (319, 'hahahahhah 319', 'schimmelfuckers 319');
INSERT INTO `test` VALUES (320, 'hahahahhah 320', 'schimmelfuckers 320');
INSERT INTO `test` VALUES (321, 'hahahahhah 321', 'schimmelfuckers 321');
INSERT INTO `test` VALUES (322, 'hahahahhah 322', 'schimmelfuckers 322');
INSERT INTO `test` VALUES (323, 'hahahahhah 323', 'schimmelfuckers 323');
INSERT INTO `test` VALUES (324, 'hahahahhah 324', 'schimmelfuckers 324');
INSERT INTO `test` VALUES (325, 'hahahahhah 325', 'schimmelfuckers 325');
INSERT INTO `test` VALUES (326, 'hahahahhah 326', 'schimmelfuckers 326');
INSERT INTO `test` VALUES (327, 'hahahahhah 327', 'schimmelfuckers 327');
INSERT INTO `test` VALUES (328, 'hahahahhah 328', 'schimmelfuckers 328');
INSERT INTO `test` VALUES (329, 'hahahahhah 329', 'schimmelfuckers 329');
INSERT INTO `test` VALUES (330, 'hahahahhah 330', 'schimmelfuckers 330');
INSERT INTO `test` VALUES (331, 'hahahahhah 331', 'schimmelfuckers 331');
INSERT INTO `test` VALUES (332, 'hahahahhah 332', 'schimmelfuckers 332');
INSERT INTO `test` VALUES (333, 'hahahahhah 333', 'schimmelfuckers 333');
INSERT INTO `test` VALUES (334, 'hahahahhah 334', 'schimmelfuckers 334');
INSERT INTO `test` VALUES (335, 'hahahahhah 335', 'schimmelfuckers 335');
INSERT INTO `test` VALUES (336, 'hahahahhah 336', 'schimmelfuckers 336');
INSERT INTO `test` VALUES (337, 'hahahahhah 337', 'schimmelfuckers 337');
INSERT INTO `test` VALUES (338, 'hahahahhah 338', 'schimmelfuckers 338');
INSERT INTO `test` VALUES (339, 'hahahahhah 339', 'schimmelfuckers 339');
INSERT INTO `test` VALUES (340, 'hahahahhah 340', 'schimmelfuckers 340');
INSERT INTO `test` VALUES (341, 'hahahahhah 341', 'schimmelfuckers 341');
INSERT INTO `test` VALUES (342, 'hahahahhah 342', 'schimmelfuckers 342');
INSERT INTO `test` VALUES (343, 'hahahahhah 343', 'schimmelfuckers 343');
INSERT INTO `test` VALUES (344, 'hahahahhah 344', 'schimmelfuckers 344');
INSERT INTO `test` VALUES (345, 'hahahahhah 345', 'schimmelfuckers 345');
INSERT INTO `test` VALUES (346, 'hahahahhah 346', 'schimmelfuckers 346');
INSERT INTO `test` VALUES (347, 'hahahahhah 347', 'schimmelfuckers 347');
INSERT INTO `test` VALUES (348, 'hahahahhah 348', 'schimmelfuckers 348');
INSERT INTO `test` VALUES (349, 'hahahahhah 349', 'schimmelfuckers 349');
INSERT INTO `test` VALUES (350, 'hahahahhah 350', 'schimmelfuckers 350');
INSERT INTO `test` VALUES (351, 'hahahahhah 351', 'schimmelfuckers 351');
INSERT INTO `test` VALUES (352, 'hahahahhah 352', 'schimmelfuckers 352');
INSERT INTO `test` VALUES (353, 'hahahahhah 353', 'schimmelfuckers 353');
INSERT INTO `test` VALUES (354, 'hahahahhah 354', 'schimmelfuckers 354');
INSERT INTO `test` VALUES (355, 'hahahahhah 355', 'schimmelfuckers 355');
INSERT INTO `test` VALUES (356, 'hahahahhah 356', 'schimmelfuckers 356');
INSERT INTO `test` VALUES (357, 'hahahahhah 357', 'schimmelfuckers 357');
INSERT INTO `test` VALUES (358, 'hahahahhah 358', 'schimmelfuckers 358');
INSERT INTO `test` VALUES (359, 'hahahahhah 359', 'schimmelfuckers 359');
INSERT INTO `test` VALUES (360, 'hahahahhah 360', 'schimmelfuckers 360');
INSERT INTO `test` VALUES (361, 'hahahahhah 361', 'schimmelfuckers 361');
INSERT INTO `test` VALUES (362, 'hahahahhah 362', 'schimmelfuckers 362');
INSERT INTO `test` VALUES (363, 'hahahahhah 363', 'schimmelfuckers 363');
INSERT INTO `test` VALUES (364, 'hahahahhah 364', 'schimmelfuckers 364');
INSERT INTO `test` VALUES (365, 'hahahahhah 365', 'schimmelfuckers 365');
INSERT INTO `test` VALUES (366, 'hahahahhah 366', 'schimmelfuckers 366');
INSERT INTO `test` VALUES (367, 'hahahahhah 367', 'schimmelfuckers 367');
INSERT INTO `test` VALUES (368, 'hahahahhah 368', 'schimmelfuckers 368');
INSERT INTO `test` VALUES (369, 'hahahahhah 369', 'schimmelfuckers 369');
INSERT INTO `test` VALUES (370, 'hahahahhah 370', 'schimmelfuckers 370');
INSERT INTO `test` VALUES (371, 'hahahahhah 371', 'schimmelfuckers 371');
INSERT INTO `test` VALUES (372, 'hahahahhah 372', 'schimmelfuckers 372');
INSERT INTO `test` VALUES (373, 'hahahahhah 373', 'schimmelfuckers 373');
INSERT INTO `test` VALUES (374, 'hahahahhah 374', 'schimmelfuckers 374');
INSERT INTO `test` VALUES (375, 'hahahahhah 375', 'schimmelfuckers 375');
INSERT INTO `test` VALUES (376, 'hahahahhah 376', 'schimmelfuckers 376');
INSERT INTO `test` VALUES (377, 'hahahahhah 377', 'schimmelfuckers 377');
INSERT INTO `test` VALUES (378, 'hahahahhah 378', 'schimmelfuckers 378');
INSERT INTO `test` VALUES (379, 'hahahahhah 379', 'schimmelfuckers 379');
INSERT INTO `test` VALUES (380, 'hahahahhah 380', 'schimmelfuckers 380');
INSERT INTO `test` VALUES (381, 'hahahahhah 381', 'schimmelfuckers 381');
INSERT INTO `test` VALUES (382, 'hahahahhah 382', 'schimmelfuckers 382');
INSERT INTO `test` VALUES (383, 'hahahahhah 383', 'schimmelfuckers 383');
INSERT INTO `test` VALUES (384, 'hahahahhah 384', 'schimmelfuckers 384');
INSERT INTO `test` VALUES (385, 'hahahahhah 385', 'schimmelfuckers 385');
INSERT INTO `test` VALUES (386, 'hahahahhah 386', 'schimmelfuckers 386');
INSERT INTO `test` VALUES (387, 'hahahahhah 387', 'schimmelfuckers 387');
INSERT INTO `test` VALUES (388, 'hahahahhah 388', 'schimmelfuckers 388');
INSERT INTO `test` VALUES (389, 'hahahahhah 389', 'schimmelfuckers 389');
INSERT INTO `test` VALUES (390, 'hahahahhah 390', 'schimmelfuckers 390');
INSERT INTO `test` VALUES (391, 'hahahahhah 391', 'schimmelfuckers 391');
INSERT INTO `test` VALUES (392, 'hahahahhah 392', 'schimmelfuckers 392');
INSERT INTO `test` VALUES (393, 'hahahahhah 393', 'schimmelfuckers 393');
INSERT INTO `test` VALUES (394, 'hahahahhah 394', 'schimmelfuckers 394');
INSERT INTO `test` VALUES (395, 'hahahahhah 395', 'schimmelfuckers 395');
INSERT INTO `test` VALUES (396, 'hahahahhah 396', 'schimmelfuckers 396');
INSERT INTO `test` VALUES (397, 'hahahahhah 397', 'schimmelfuckers 397');
INSERT INTO `test` VALUES (398, 'hahahahhah 398', 'schimmelfuckers 398');
INSERT INTO `test` VALUES (399, 'hahahahhah 399', 'schimmelfuckers 399');
INSERT INTO `test` VALUES (400, 'hahahahhah 400', 'schimmelfuckers 400');
INSERT INTO `test` VALUES (401, 'hahahahhah 401', 'schimmelfuckers 401');
INSERT INTO `test` VALUES (402, 'hahahahhah 402', 'schimmelfuckers 402');
INSERT INTO `test` VALUES (403, 'hahahahhah 403', 'schimmelfuckers 403');
INSERT INTO `test` VALUES (404, 'hahahahhah 404', 'schimmelfuckers 404');
INSERT INTO `test` VALUES (405, 'hahahahhah 405', 'schimmelfuckers 405');
INSERT INTO `test` VALUES (406, 'hahahahhah 406', 'schimmelfuckers 406');
INSERT INTO `test` VALUES (407, 'hahahahhah 407', 'schimmelfuckers 407');
INSERT INTO `test` VALUES (408, 'hahahahhah 408', 'schimmelfuckers 408');
INSERT INTO `test` VALUES (409, 'hahahahhah 409', 'schimmelfuckers 409');
INSERT INTO `test` VALUES (410, 'hahahahhah 410', 'schimmelfuckers 410');
INSERT INTO `test` VALUES (411, 'hahahahhah 411', 'schimmelfuckers 411');
INSERT INTO `test` VALUES (412, 'hahahahhah 412', 'schimmelfuckers 412');
INSERT INTO `test` VALUES (413, 'hahahahhah 413', 'schimmelfuckers 413');
INSERT INTO `test` VALUES (414, 'hahahahhah 414', 'schimmelfuckers 414');
INSERT INTO `test` VALUES (415, 'hahahahhah 415', 'schimmelfuckers 415');
INSERT INTO `test` VALUES (416, 'hahahahhah 416', 'schimmelfuckers 416');
INSERT INTO `test` VALUES (417, 'hahahahhah 417', 'schimmelfuckers 417');
INSERT INTO `test` VALUES (418, 'hahahahhah 418', 'schimmelfuckers 418');
INSERT INTO `test` VALUES (419, 'hahahahhah 419', 'schimmelfuckers 419');
INSERT INTO `test` VALUES (420, 'hahahahhah 420', 'schimmelfuckers 420');
INSERT INTO `test` VALUES (421, 'hahahahhah 421', 'schimmelfuckers 421');
INSERT INTO `test` VALUES (422, 'hahahahhah 422', 'schimmelfuckers 422');
INSERT INTO `test` VALUES (423, 'hahahahhah 423', 'schimmelfuckers 423');
INSERT INTO `test` VALUES (424, 'hahahahhah 424', 'schimmelfuckers 424');
INSERT INTO `test` VALUES (425, 'hahahahhah 425', 'schimmelfuckers 425');
INSERT INTO `test` VALUES (426, 'hahahahhah 426', 'schimmelfuckers 426');
INSERT INTO `test` VALUES (427, 'hahahahhah 427', 'schimmelfuckers 427');
INSERT INTO `test` VALUES (428, 'hahahahhah 428', 'schimmelfuckers 428');
INSERT INTO `test` VALUES (429, 'hahahahhah 429', 'schimmelfuckers 429');
INSERT INTO `test` VALUES (430, 'hahahahhah 430', 'schimmelfuckers 430');
INSERT INTO `test` VALUES (431, 'hahahahhah 431', 'schimmelfuckers 431');
INSERT INTO `test` VALUES (432, 'hahahahhah 432', 'schimmelfuckers 432');
INSERT INTO `test` VALUES (433, 'hahahahhah 433', 'schimmelfuckers 433');
INSERT INTO `test` VALUES (434, 'hahahahhah 434', 'schimmelfuckers 434');
INSERT INTO `test` VALUES (435, 'hahahahhah 435', 'schimmelfuckers 435');
INSERT INTO `test` VALUES (436, 'hahahahhah 436', 'schimmelfuckers 436');
INSERT INTO `test` VALUES (437, 'hahahahhah 437', 'schimmelfuckers 437');
INSERT INTO `test` VALUES (438, 'hahahahhah 438', 'schimmelfuckers 438');
INSERT INTO `test` VALUES (439, 'hahahahhah 439', 'schimmelfuckers 439');
INSERT INTO `test` VALUES (440, 'hahahahhah 440', 'schimmelfuckers 440');
INSERT INTO `test` VALUES (441, 'hahahahhah 441', 'schimmelfuckers 441');
INSERT INTO `test` VALUES (442, 'hahahahhah 442', 'schimmelfuckers 442');
INSERT INTO `test` VALUES (443, 'hahahahhah 443', 'schimmelfuckers 443');
INSERT INTO `test` VALUES (444, 'hahahahhah 444', 'schimmelfuckers 444');
INSERT INTO `test` VALUES (445, 'hahahahhah 445', 'schimmelfuckers 445');
INSERT INTO `test` VALUES (446, 'hahahahhah 446', 'schimmelfuckers 446');
INSERT INTO `test` VALUES (447, 'hahahahhah 447', 'schimmelfuckers 447');
INSERT INTO `test` VALUES (448, 'hahahahhah 448', 'schimmelfuckers 448');
INSERT INTO `test` VALUES (449, 'hahahahhah 449', 'schimmelfuckers 449');
INSERT INTO `test` VALUES (450, 'hahahahhah 450', 'schimmelfuckers 450');
INSERT INTO `test` VALUES (451, 'hahahahhah 451', 'schimmelfuckers 451');
INSERT INTO `test` VALUES (452, 'hahahahhah 452', 'schimmelfuckers 452');
INSERT INTO `test` VALUES (453, 'hahahahhah 453', 'schimmelfuckers 453');
INSERT INTO `test` VALUES (454, 'hahahahhah 454', 'schimmelfuckers 454');
INSERT INTO `test` VALUES (455, 'hahahahhah 455', 'schimmelfuckers 455');
INSERT INTO `test` VALUES (456, 'hahahahhah 456', 'schimmelfuckers 456');
INSERT INTO `test` VALUES (457, 'hahahahhah 457', 'schimmelfuckers 457');
INSERT INTO `test` VALUES (458, 'hahahahhah 458', 'schimmelfuckers 458');
INSERT INTO `test` VALUES (459, 'hahahahhah 459', 'schimmelfuckers 459');
INSERT INTO `test` VALUES (460, 'hahahahhah 460', 'schimmelfuckers 460');
INSERT INTO `test` VALUES (461, 'hahahahhah 461', 'schimmelfuckers 461');
INSERT INTO `test` VALUES (462, 'hahahahhah 462', 'schimmelfuckers 462');
INSERT INTO `test` VALUES (463, 'hahahahhah 463', 'schimmelfuckers 463');
INSERT INTO `test` VALUES (464, 'hahahahhah 464', 'schimmelfuckers 464');
INSERT INTO `test` VALUES (465, 'hahahahhah 465', 'schimmelfuckers 465');
INSERT INTO `test` VALUES (466, 'hahahahhah 466', 'schimmelfuckers 466');
INSERT INTO `test` VALUES (467, 'hahahahhah 467', 'schimmelfuckers 467');
INSERT INTO `test` VALUES (468, 'hahahahhah 468', 'schimmelfuckers 468');
INSERT INTO `test` VALUES (469, 'hahahahhah 469', 'schimmelfuckers 469');
INSERT INTO `test` VALUES (470, 'hahahahhah 470', 'schimmelfuckers 470');
INSERT INTO `test` VALUES (471, 'hahahahhah 471', 'schimmelfuckers 471');
INSERT INTO `test` VALUES (472, 'hahahahhah 472', 'schimmelfuckers 472');
INSERT INTO `test` VALUES (473, 'hahahahhah 473', 'schimmelfuckers 473');
INSERT INTO `test` VALUES (474, 'hahahahhah 474', 'schimmelfuckers 474');
INSERT INTO `test` VALUES (475, 'hahahahhah 475', 'schimmelfuckers 475');
INSERT INTO `test` VALUES (476, 'hahahahhah 476', 'schimmelfuckers 476');
INSERT INTO `test` VALUES (477, 'hahahahhah 477', 'schimmelfuckers 477');
INSERT INTO `test` VALUES (478, 'hahahahhah 478', 'schimmelfuckers 478');
INSERT INTO `test` VALUES (479, 'hahahahhah 479', 'schimmelfuckers 479');
INSERT INTO `test` VALUES (480, 'hahahahhah 480', 'schimmelfuckers 480');
INSERT INTO `test` VALUES (481, 'hahahahhah 481', 'schimmelfuckers 481');
INSERT INTO `test` VALUES (482, 'hahahahhah 482', 'schimmelfuckers 482');
INSERT INTO `test` VALUES (483, 'hahahahhah 483', 'schimmelfuckers 483');
INSERT INTO `test` VALUES (484, 'hahahahhah 484', 'schimmelfuckers 484');
INSERT INTO `test` VALUES (485, 'hahahahhah 485', 'schimmelfuckers 485');
INSERT INTO `test` VALUES (486, 'hahahahhah 486', 'schimmelfuckers 486');
INSERT INTO `test` VALUES (487, 'hahahahhah 487', 'schimmelfuckers 487');
INSERT INTO `test` VALUES (488, 'hahahahhah 488', 'schimmelfuckers 488');
INSERT INTO `test` VALUES (489, 'hahahahhah 489', 'schimmelfuckers 489');
INSERT INTO `test` VALUES (490, 'hahahahhah 490', 'schimmelfuckers 490');
INSERT INTO `test` VALUES (491, 'hahahahhah 491', 'schimmelfuckers 491');
INSERT INTO `test` VALUES (492, 'hahahahhah 492', 'schimmelfuckers 492');
INSERT INTO `test` VALUES (493, 'hahahahhah 493', 'schimmelfuckers 493');
INSERT INTO `test` VALUES (494, 'hahahahhah 494', 'schimmelfuckers 494');
INSERT INTO `test` VALUES (495, 'hahahahhah 495', 'schimmelfuckers 495');
INSERT INTO `test` VALUES (496, 'hahahahhah 496', 'schimmelfuckers 496');
INSERT INTO `test` VALUES (497, 'hahahahhah 497', 'schimmelfuckers 497');
INSERT INTO `test` VALUES (498, 'hahahahhah 498', 'schimmelfuckers 498');
INSERT INTO `test` VALUES (499, 'hahahahhah 499', 'schimmelfuckers 499');
INSERT INTO `test` VALUES (500, 'hahahahhah 500', 'schimmelfuckers 500');
INSERT INTO `test` VALUES (501, 'hahahahhah 501', 'schimmelfuckers 501');
INSERT INTO `test` VALUES (502, 'hahahahhah 502', 'schimmelfuckers 502');
INSERT INTO `test` VALUES (503, 'hahahahhah 503', 'schimmelfuckers 503');
INSERT INTO `test` VALUES (504, 'hahahahhah 504', 'schimmelfuckers 504');
INSERT INTO `test` VALUES (505, 'hahahahhah 505', 'schimmelfuckers 505');
INSERT INTO `test` VALUES (506, 'hahahahhah 506', 'schimmelfuckers 506');
INSERT INTO `test` VALUES (507, 'hahahahhah 507', 'schimmelfuckers 507');
INSERT INTO `test` VALUES (508, 'hahahahhah 508', 'schimmelfuckers 508');
INSERT INTO `test` VALUES (509, 'hahahahhah 509', 'schimmelfuckers 509');
INSERT INTO `test` VALUES (510, 'hahahahhah 510', 'schimmelfuckers 510');
INSERT INTO `test` VALUES (511, 'hahahahhah 511', 'schimmelfuckers 511');
INSERT INTO `test` VALUES (512, 'hahahahhah 512', 'schimmelfuckers 512');
INSERT INTO `test` VALUES (513, 'hahahahhah 513', 'schimmelfuckers 513');
INSERT INTO `test` VALUES (514, 'hahahahhah 514', 'schimmelfuckers 514');
INSERT INTO `test` VALUES (515, 'hahahahhah 515', 'schimmelfuckers 515');
INSERT INTO `test` VALUES (516, 'hahahahhah 516', 'schimmelfuckers 516');
INSERT INTO `test` VALUES (517, 'hahahahhah 517', 'schimmelfuckers 517');
INSERT INTO `test` VALUES (518, 'hahahahhah 518', 'schimmelfuckers 518');
INSERT INTO `test` VALUES (519, 'hahahahhah 519', 'schimmelfuckers 519');
INSERT INTO `test` VALUES (520, 'hahahahhah 520', 'schimmelfuckers 520');
INSERT INTO `test` VALUES (521, 'hahahahhah 521', 'schimmelfuckers 521');
INSERT INTO `test` VALUES (522, 'hahahahhah 522', 'schimmelfuckers 522');
INSERT INTO `test` VALUES (523, 'hahahahhah 523', 'schimmelfuckers 523');
INSERT INTO `test` VALUES (524, 'hahahahhah 524', 'schimmelfuckers 524');
INSERT INTO `test` VALUES (525, 'hahahahhah 525', 'schimmelfuckers 525');
INSERT INTO `test` VALUES (526, 'hahahahhah 526', 'schimmelfuckers 526');
INSERT INTO `test` VALUES (527, 'hahahahhah 527', 'schimmelfuckers 527');
INSERT INTO `test` VALUES (528, 'hahahahhah 528', 'schimmelfuckers 528');
INSERT INTO `test` VALUES (529, 'hahahahhah 529', 'schimmelfuckers 529');
INSERT INTO `test` VALUES (530, 'hahahahhah 530', 'schimmelfuckers 530');
INSERT INTO `test` VALUES (531, 'hahahahhah 531', 'schimmelfuckers 531');
INSERT INTO `test` VALUES (532, 'hahahahhah 532', 'schimmelfuckers 532');
INSERT INTO `test` VALUES (533, 'hahahahhah 533', 'schimmelfuckers 533');
INSERT INTO `test` VALUES (534, 'hahahahhah 534', 'schimmelfuckers 534');
INSERT INTO `test` VALUES (535, 'hahahahhah 535', 'schimmelfuckers 535');
INSERT INTO `test` VALUES (536, 'hahahahhah 536', 'schimmelfuckers 536');
INSERT INTO `test` VALUES (537, 'hahahahhah 537', 'schimmelfuckers 537');
INSERT INTO `test` VALUES (538, 'hahahahhah 538', 'schimmelfuckers 538');
INSERT INTO `test` VALUES (539, 'hahahahhah 539', 'schimmelfuckers 539');
INSERT INTO `test` VALUES (540, 'hahahahhah 540', 'schimmelfuckers 540');
INSERT INTO `test` VALUES (541, 'hahahahhah 541', 'schimmelfuckers 541');
INSERT INTO `test` VALUES (542, 'hahahahhah 542', 'schimmelfuckers 542');
INSERT INTO `test` VALUES (543, 'hahahahhah 543', 'schimmelfuckers 543');
INSERT INTO `test` VALUES (544, 'hahahahhah 544', 'schimmelfuckers 544');
INSERT INTO `test` VALUES (545, 'hahahahhah 545', 'schimmelfuckers 545');
INSERT INTO `test` VALUES (546, 'hahahahhah 546', 'schimmelfuckers 546');
INSERT INTO `test` VALUES (547, 'hahahahhah 547', 'schimmelfuckers 547');
INSERT INTO `test` VALUES (548, 'hahahahhah 548', 'schimmelfuckers 548');
INSERT INTO `test` VALUES (549, 'hahahahhah 549', 'schimmelfuckers 549');
INSERT INTO `test` VALUES (550, 'hahahahhah 550', 'schimmelfuckers 550');
INSERT INTO `test` VALUES (551, 'hahahahhah 551', 'schimmelfuckers 551');
INSERT INTO `test` VALUES (552, 'hahahahhah 552', 'schimmelfuckers 552');
INSERT INTO `test` VALUES (553, 'hahahahhah 553', 'schimmelfuckers 553');
INSERT INTO `test` VALUES (554, 'hahahahhah 554', 'schimmelfuckers 554');
INSERT INTO `test` VALUES (555, 'hahahahhah 555', 'schimmelfuckers 555');
INSERT INTO `test` VALUES (556, 'hahahahhah 556', 'schimmelfuckers 556');
INSERT INTO `test` VALUES (557, 'hahahahhah 557', 'schimmelfuckers 557');
INSERT INTO `test` VALUES (558, 'hahahahhah 558', 'schimmelfuckers 558');
INSERT INTO `test` VALUES (559, 'hahahahhah 559', 'schimmelfuckers 559');
INSERT INTO `test` VALUES (560, 'hahahahhah 560', 'schimmelfuckers 560');
INSERT INTO `test` VALUES (561, 'hahahahhah 561', 'schimmelfuckers 561');
INSERT INTO `test` VALUES (562, 'hahahahhah 562', 'schimmelfuckers 562');
INSERT INTO `test` VALUES (563, 'hahahahhah 563', 'schimmelfuckers 563');
INSERT INTO `test` VALUES (564, 'hahahahhah 564', 'schimmelfuckers 564');
INSERT INTO `test` VALUES (565, 'hahahahhah 565', 'schimmelfuckers 565');
INSERT INTO `test` VALUES (566, 'hahahahhah 566', 'schimmelfuckers 566');
INSERT INTO `test` VALUES (567, 'hahahahhah 567', 'schimmelfuckers 567');
INSERT INTO `test` VALUES (568, 'hahahahhah 568', 'schimmelfuckers 568');
INSERT INTO `test` VALUES (569, 'hahahahhah 569', 'schimmelfuckers 569');
INSERT INTO `test` VALUES (570, 'hahahahhah 570', 'schimmelfuckers 570');
INSERT INTO `test` VALUES (571, 'hahahahhah 571', 'schimmelfuckers 571');
INSERT INTO `test` VALUES (572, 'hahahahhah 572', 'schimmelfuckers 572');
INSERT INTO `test` VALUES (573, 'hahahahhah 573', 'schimmelfuckers 573');
INSERT INTO `test` VALUES (574, 'hahahahhah 574', 'schimmelfuckers 574');
INSERT INTO `test` VALUES (575, 'hahahahhah 575', 'schimmelfuckers 575');
INSERT INTO `test` VALUES (576, 'hahahahhah 576', 'schimmelfuckers 576');
INSERT INTO `test` VALUES (577, 'hahahahhah 577', 'schimmelfuckers 577');
INSERT INTO `test` VALUES (578, 'hahahahhah 578', 'schimmelfuckers 578');
INSERT INTO `test` VALUES (579, 'hahahahhah 579', 'schimmelfuckers 579');
INSERT INTO `test` VALUES (580, 'hahahahhah 580', 'schimmelfuckers 580');
INSERT INTO `test` VALUES (581, 'hahahahhah 581', 'schimmelfuckers 581');
INSERT INTO `test` VALUES (582, 'hahahahhah 582', 'schimmelfuckers 582');
INSERT INTO `test` VALUES (583, 'hahahahhah 583', 'schimmelfuckers 583');
INSERT INTO `test` VALUES (584, 'hahahahhah 584', 'schimmelfuckers 584');
INSERT INTO `test` VALUES (585, 'hahahahhah 585', 'schimmelfuckers 585');
INSERT INTO `test` VALUES (586, 'hahahahhah 586', 'schimmelfuckers 586');
INSERT INTO `test` VALUES (587, 'hahahahhah 587', 'schimmelfuckers 587');
INSERT INTO `test` VALUES (588, 'hahahahhah 588', 'schimmelfuckers 588');
INSERT INTO `test` VALUES (589, 'hahahahhah 589', 'schimmelfuckers 589');
INSERT INTO `test` VALUES (590, 'hahahahhah 590', 'schimmelfuckers 590');
INSERT INTO `test` VALUES (591, 'hahahahhah 591', 'schimmelfuckers 591');
INSERT INTO `test` VALUES (592, 'hahahahhah 592', 'schimmelfuckers 592');
INSERT INTO `test` VALUES (593, 'hahahahhah 593', 'schimmelfuckers 593');
INSERT INTO `test` VALUES (594, 'hahahahhah 594', 'schimmelfuckers 594');
INSERT INTO `test` VALUES (595, 'hahahahhah 595', 'schimmelfuckers 595');
INSERT INTO `test` VALUES (596, 'hahahahhah 596', 'schimmelfuckers 596');
INSERT INTO `test` VALUES (597, 'hahahahhah 597', 'schimmelfuckers 597');
INSERT INTO `test` VALUES (598, 'hahahahhah 598', 'schimmelfuckers 598');
INSERT INTO `test` VALUES (599, 'hahahahhah 599', 'schimmelfuckers 599');
INSERT INTO `test` VALUES (600, 'hahahahhah 600', 'schimmelfuckers 600');
INSERT INTO `test` VALUES (601, 'hahahahhah 601', 'schimmelfuckers 601');
INSERT INTO `test` VALUES (602, 'hahahahhah 602', 'schimmelfuckers 602');
INSERT INTO `test` VALUES (603, 'hahahahhah 603', 'schimmelfuckers 603');
INSERT INTO `test` VALUES (604, 'hahahahhah 604', 'schimmelfuckers 604');
INSERT INTO `test` VALUES (605, 'hahahahhah 605', 'schimmelfuckers 605');
INSERT INTO `test` VALUES (606, 'hahahahhah 606', 'schimmelfuckers 606');
INSERT INTO `test` VALUES (607, 'hahahahhah 607', 'schimmelfuckers 607');
INSERT INTO `test` VALUES (608, 'hahahahhah 608', 'schimmelfuckers 608');
INSERT INTO `test` VALUES (609, 'hahahahhah 609', 'schimmelfuckers 609');
INSERT INTO `test` VALUES (610, 'hahahahhah 610', 'schimmelfuckers 610');
INSERT INTO `test` VALUES (611, 'hahahahhah 611', 'schimmelfuckers 611');
INSERT INTO `test` VALUES (612, 'hahahahhah 612', 'schimmelfuckers 612');
INSERT INTO `test` VALUES (613, 'hahahahhah 613', 'schimmelfuckers 613');
INSERT INTO `test` VALUES (614, 'hahahahhah 614', 'schimmelfuckers 614');
INSERT INTO `test` VALUES (615, 'hahahahhah 615', 'schimmelfuckers 615');
INSERT INTO `test` VALUES (616, 'hahahahhah 616', 'schimmelfuckers 616');
INSERT INTO `test` VALUES (617, 'hahahahhah 617', 'schimmelfuckers 617');
INSERT INTO `test` VALUES (618, 'hahahahhah 618', 'schimmelfuckers 618');
INSERT INTO `test` VALUES (619, 'hahahahhah 619', 'schimmelfuckers 619');
INSERT INTO `test` VALUES (620, 'hahahahhah 620', 'schimmelfuckers 620');
INSERT INTO `test` VALUES (621, 'hahahahhah 621', 'schimmelfuckers 621');
INSERT INTO `test` VALUES (622, 'hahahahhah 622', 'schimmelfuckers 622');
INSERT INTO `test` VALUES (623, 'hahahahhah 623', 'schimmelfuckers 623');
INSERT INTO `test` VALUES (624, 'hahahahhah 624', 'schimmelfuckers 624');
INSERT INTO `test` VALUES (625, 'hahahahhah 625', 'schimmelfuckers 625');
INSERT INTO `test` VALUES (626, 'hahahahhah 626', 'schimmelfuckers 626');
INSERT INTO `test` VALUES (627, 'hahahahhah 627', 'schimmelfuckers 627');
INSERT INTO `test` VALUES (628, 'hahahahhah 628', 'schimmelfuckers 628');
INSERT INTO `test` VALUES (629, 'hahahahhah 629', 'schimmelfuckers 629');
INSERT INTO `test` VALUES (630, 'hahahahhah 630', 'schimmelfuckers 630');
INSERT INTO `test` VALUES (631, 'hahahahhah 631', 'schimmelfuckers 631');
INSERT INTO `test` VALUES (632, 'hahahahhah 632', 'schimmelfuckers 632');
INSERT INTO `test` VALUES (633, 'hahahahhah 633', 'schimmelfuckers 633');
INSERT INTO `test` VALUES (634, 'hahahahhah 634', 'schimmelfuckers 634');
INSERT INTO `test` VALUES (635, 'hahahahhah 635', 'schimmelfuckers 635');
INSERT INTO `test` VALUES (636, 'hahahahhah 636', 'schimmelfuckers 636');
INSERT INTO `test` VALUES (637, 'hahahahhah 637', 'schimmelfuckers 637');
INSERT INTO `test` VALUES (638, 'hahahahhah 638', 'schimmelfuckers 638');
INSERT INTO `test` VALUES (639, 'hahahahhah 639', 'schimmelfuckers 639');
INSERT INTO `test` VALUES (640, 'hahahahhah 640', 'schimmelfuckers 640');
INSERT INTO `test` VALUES (641, 'hahahahhah 641', 'schimmelfuckers 641');
INSERT INTO `test` VALUES (642, 'hahahahhah 642', 'schimmelfuckers 642');
INSERT INTO `test` VALUES (643, 'hahahahhah 643', 'schimmelfuckers 643');
INSERT INTO `test` VALUES (644, 'hahahahhah 644', 'schimmelfuckers 644');
INSERT INTO `test` VALUES (645, 'hahahahhah 645', 'schimmelfuckers 645');
INSERT INTO `test` VALUES (646, 'hahahahhah 646', 'schimmelfuckers 646');
INSERT INTO `test` VALUES (647, 'hahahahhah 647', 'schimmelfuckers 647');
INSERT INTO `test` VALUES (648, 'hahahahhah 648', 'schimmelfuckers 648');
INSERT INTO `test` VALUES (649, 'hahahahhah 649', 'schimmelfuckers 649');
INSERT INTO `test` VALUES (650, 'hahahahhah 650', 'schimmelfuckers 650');
INSERT INTO `test` VALUES (651, 'hahahahhah 651', 'schimmelfuckers 651');
INSERT INTO `test` VALUES (652, 'hahahahhah 652', 'schimmelfuckers 652');
INSERT INTO `test` VALUES (653, 'hahahahhah 653', 'schimmelfuckers 653');
INSERT INTO `test` VALUES (654, 'hahahahhah 654', 'schimmelfuckers 654');
INSERT INTO `test` VALUES (655, 'hahahahhah 655', 'schimmelfuckers 655');
INSERT INTO `test` VALUES (656, 'hahahahhah 656', 'schimmelfuckers 656');
INSERT INTO `test` VALUES (657, 'hahahahhah 657', 'schimmelfuckers 657');
INSERT INTO `test` VALUES (658, 'hahahahhah 658', 'schimmelfuckers 658');
INSERT INTO `test` VALUES (659, 'hahahahhah 659', 'schimmelfuckers 659');
INSERT INTO `test` VALUES (660, 'hahahahhah 660', 'schimmelfuckers 660');
INSERT INTO `test` VALUES (661, 'hahahahhah 661', 'schimmelfuckers 661');
INSERT INTO `test` VALUES (662, 'hahahahhah 662', 'schimmelfuckers 662');
INSERT INTO `test` VALUES (663, 'hahahahhah 663', 'schimmelfuckers 663');
INSERT INTO `test` VALUES (664, 'hahahahhah 664', 'schimmelfuckers 664');
INSERT INTO `test` VALUES (665, 'hahahahhah 665', 'schimmelfuckers 665');
INSERT INTO `test` VALUES (666, 'hahahahhah 666', 'schimmelfuckers 666');
INSERT INTO `test` VALUES (667, 'hahahahhah 667', 'schimmelfuckers 667');
INSERT INTO `test` VALUES (668, 'hahahahhah 668', 'schimmelfuckers 668');
INSERT INTO `test` VALUES (669, 'hahahahhah 669', 'schimmelfuckers 669');
INSERT INTO `test` VALUES (670, 'hahahahhah 670', 'schimmelfuckers 670');
INSERT INTO `test` VALUES (671, 'hahahahhah 671', 'schimmelfuckers 671');
INSERT INTO `test` VALUES (672, 'hahahahhah 672', 'schimmelfuckers 672');
INSERT INTO `test` VALUES (673, 'hahahahhah 673', 'schimmelfuckers 673');
INSERT INTO `test` VALUES (674, 'hahahahhah 674', 'schimmelfuckers 674');
INSERT INTO `test` VALUES (675, 'hahahahhah 675', 'schimmelfuckers 675');
INSERT INTO `test` VALUES (676, 'hahahahhah 676', 'schimmelfuckers 676');
INSERT INTO `test` VALUES (677, 'hahahahhah 677', 'schimmelfuckers 677');
INSERT INTO `test` VALUES (678, 'hahahahhah 678', 'schimmelfuckers 678');
INSERT INTO `test` VALUES (679, 'hahahahhah 679', 'schimmelfuckers 679');
INSERT INTO `test` VALUES (680, 'hahahahhah 680', 'schimmelfuckers 680');
INSERT INTO `test` VALUES (681, 'hahahahhah 681', 'schimmelfuckers 681');
INSERT INTO `test` VALUES (682, 'hahahahhah 682', 'schimmelfuckers 682');
INSERT INTO `test` VALUES (683, 'hahahahhah 683', 'schimmelfuckers 683');
INSERT INTO `test` VALUES (684, 'hahahahhah 684', 'schimmelfuckers 684');
INSERT INTO `test` VALUES (685, 'hahahahhah 685', 'schimmelfuckers 685');
INSERT INTO `test` VALUES (686, 'hahahahhah 686', 'schimmelfuckers 686');
INSERT INTO `test` VALUES (687, 'hahahahhah 687', 'schimmelfuckers 687');
INSERT INTO `test` VALUES (688, 'hahahahhah 688', 'schimmelfuckers 688');
INSERT INTO `test` VALUES (689, 'hahahahhah 689', 'schimmelfuckers 689');
INSERT INTO `test` VALUES (690, 'hahahahhah 690', 'schimmelfuckers 690');
INSERT INTO `test` VALUES (691, 'hahahahhah 691', 'schimmelfuckers 691');
INSERT INTO `test` VALUES (692, 'hahahahhah 692', 'schimmelfuckers 692');
INSERT INTO `test` VALUES (693, 'hahahahhah 693', 'schimmelfuckers 693');
INSERT INTO `test` VALUES (694, 'hahahahhah 694', 'schimmelfuckers 694');
INSERT INTO `test` VALUES (695, 'hahahahhah 695', 'schimmelfuckers 695');
INSERT INTO `test` VALUES (696, 'hahahahhah 696', 'schimmelfuckers 696');
INSERT INTO `test` VALUES (697, 'hahahahhah 697', 'schimmelfuckers 697');
INSERT INTO `test` VALUES (698, 'hahahahhah 698', 'schimmelfuckers 698');
INSERT INTO `test` VALUES (699, 'hahahahhah 699', 'schimmelfuckers 699');
INSERT INTO `test` VALUES (700, 'hahahahhah 700', 'schimmelfuckers 700');
INSERT INTO `test` VALUES (701, 'hahahahhah 701', 'schimmelfuckers 701');
INSERT INTO `test` VALUES (702, 'hahahahhah 702', 'schimmelfuckers 702');
INSERT INTO `test` VALUES (703, 'hahahahhah 703', 'schimmelfuckers 703');
INSERT INTO `test` VALUES (704, 'hahahahhah 704', 'schimmelfuckers 704');
INSERT INTO `test` VALUES (705, 'hahahahhah 705', 'schimmelfuckers 705');
INSERT INTO `test` VALUES (706, 'hahahahhah 706', 'schimmelfuckers 706');
INSERT INTO `test` VALUES (707, 'hahahahhah 707', 'schimmelfuckers 707');
INSERT INTO `test` VALUES (708, 'hahahahhah 708', 'schimmelfuckers 708');
INSERT INTO `test` VALUES (709, 'hahahahhah 709', 'schimmelfuckers 709');
INSERT INTO `test` VALUES (710, 'hahahahhah 710', 'schimmelfuckers 710');
INSERT INTO `test` VALUES (711, 'hahahahhah 711', 'schimmelfuckers 711');
INSERT INTO `test` VALUES (712, 'hahahahhah 712', 'schimmelfuckers 712');
INSERT INTO `test` VALUES (713, 'hahahahhah 713', 'schimmelfuckers 713');
INSERT INTO `test` VALUES (714, 'hahahahhah 714', 'schimmelfuckers 714');
INSERT INTO `test` VALUES (715, 'hahahahhah 715', 'schimmelfuckers 715');
INSERT INTO `test` VALUES (716, 'hahahahhah 716', 'schimmelfuckers 716');
INSERT INTO `test` VALUES (717, 'hahahahhah 717', 'schimmelfuckers 717');
INSERT INTO `test` VALUES (718, 'hahahahhah 718', 'schimmelfuckers 718');
INSERT INTO `test` VALUES (719, 'hahahahhah 719', 'schimmelfuckers 719');
INSERT INTO `test` VALUES (720, 'hahahahhah 720', 'schimmelfuckers 720');
INSERT INTO `test` VALUES (721, 'hahahahhah 721', 'schimmelfuckers 721');
INSERT INTO `test` VALUES (722, 'hahahahhah 722', 'schimmelfuckers 722');
INSERT INTO `test` VALUES (723, 'hahahahhah 723', 'schimmelfuckers 723');
INSERT INTO `test` VALUES (724, 'hahahahhah 724', 'schimmelfuckers 724');
INSERT INTO `test` VALUES (725, 'hahahahhah 725', 'schimmelfuckers 725');
INSERT INTO `test` VALUES (726, 'hahahahhah 726', 'schimmelfuckers 726');
INSERT INTO `test` VALUES (727, 'hahahahhah 727', 'schimmelfuckers 727');
INSERT INTO `test` VALUES (728, 'hahahahhah 728', 'schimmelfuckers 728');
INSERT INTO `test` VALUES (729, 'hahahahhah 729', 'schimmelfuckers 729');
INSERT INTO `test` VALUES (730, 'hahahahhah 730', 'schimmelfuckers 730');
INSERT INTO `test` VALUES (731, 'hahahahhah 731', 'schimmelfuckers 731');
INSERT INTO `test` VALUES (732, 'hahahahhah 732', 'schimmelfuckers 732');
INSERT INTO `test` VALUES (733, 'hahahahhah 733', 'schimmelfuckers 733');
INSERT INTO `test` VALUES (734, 'hahahahhah 734', 'schimmelfuckers 734');
INSERT INTO `test` VALUES (735, 'hahahahhah 735', 'schimmelfuckers 735');
INSERT INTO `test` VALUES (736, 'hahahahhah 736', 'schimmelfuckers 736');
INSERT INTO `test` VALUES (737, 'hahahahhah 737', 'schimmelfuckers 737');
INSERT INTO `test` VALUES (738, 'hahahahhah 738', 'schimmelfuckers 738');
INSERT INTO `test` VALUES (739, 'hahahahhah 739', 'schimmelfuckers 739');
INSERT INTO `test` VALUES (740, 'hahahahhah 740', 'schimmelfuckers 740');
INSERT INTO `test` VALUES (741, 'hahahahhah 741', 'schimmelfuckers 741');
INSERT INTO `test` VALUES (742, 'hahahahhah 742', 'schimmelfuckers 742');
INSERT INTO `test` VALUES (743, 'hahahahhah 743', 'schimmelfuckers 743');
INSERT INTO `test` VALUES (744, 'hahahahhah 744', 'schimmelfuckers 744');
INSERT INTO `test` VALUES (745, 'hahahahhah 745', 'schimmelfuckers 745');
INSERT INTO `test` VALUES (746, 'hahahahhah 746', 'schimmelfuckers 746');
INSERT INTO `test` VALUES (747, 'hahahahhah 747', 'schimmelfuckers 747');
INSERT INTO `test` VALUES (748, 'hahahahhah 748', 'schimmelfuckers 748');
INSERT INTO `test` VALUES (749, 'hahahahhah 749', 'schimmelfuckers 749');
INSERT INTO `test` VALUES (750, 'hahahahhah 750', 'schimmelfuckers 750');
INSERT INTO `test` VALUES (751, 'hahahahhah 751', 'schimmelfuckers 751');
INSERT INTO `test` VALUES (752, 'hahahahhah 752', 'schimmelfuckers 752');
INSERT INTO `test` VALUES (753, 'hahahahhah 753', 'schimmelfuckers 753');
INSERT INTO `test` VALUES (754, 'hahahahhah 754', 'schimmelfuckers 754');
INSERT INTO `test` VALUES (755, 'hahahahhah 755', 'schimmelfuckers 755');
INSERT INTO `test` VALUES (756, 'hahahahhah 756', 'schimmelfuckers 756');
INSERT INTO `test` VALUES (757, 'hahahahhah 757', 'schimmelfuckers 757');
INSERT INTO `test` VALUES (758, 'hahahahhah 758', 'schimmelfuckers 758');
INSERT INTO `test` VALUES (759, 'hahahahhah 759', 'schimmelfuckers 759');
INSERT INTO `test` VALUES (760, 'hahahahhah 760', 'schimmelfuckers 760');
INSERT INTO `test` VALUES (761, 'hahahahhah 761', 'schimmelfuckers 761');
INSERT INTO `test` VALUES (762, 'hahahahhah 762', 'schimmelfuckers 762');
INSERT INTO `test` VALUES (763, 'hahahahhah 763', 'schimmelfuckers 763');
INSERT INTO `test` VALUES (764, 'hahahahhah 764', 'schimmelfuckers 764');
INSERT INTO `test` VALUES (765, 'hahahahhah 765', 'schimmelfuckers 765');
INSERT INTO `test` VALUES (766, 'hahahahhah 766', 'schimmelfuckers 766');
INSERT INTO `test` VALUES (767, 'hahahahhah 767', 'schimmelfuckers 767');
INSERT INTO `test` VALUES (768, 'hahahahhah 768', 'schimmelfuckers 768');
INSERT INTO `test` VALUES (769, 'hahahahhah 769', 'schimmelfuckers 769');
INSERT INTO `test` VALUES (770, 'hahahahhah 770', 'schimmelfuckers 770');
INSERT INTO `test` VALUES (771, 'hahahahhah 771', 'schimmelfuckers 771');
INSERT INTO `test` VALUES (772, 'hahahahhah 772', 'schimmelfuckers 772');
INSERT INTO `test` VALUES (773, 'hahahahhah 773', 'schimmelfuckers 773');
INSERT INTO `test` VALUES (774, 'hahahahhah 774', 'schimmelfuckers 774');
INSERT INTO `test` VALUES (775, 'hahahahhah 775', 'schimmelfuckers 775');
INSERT INTO `test` VALUES (776, 'hahahahhah 776', 'schimmelfuckers 776');
INSERT INTO `test` VALUES (777, 'hahahahhah 777', 'schimmelfuckers 777');
INSERT INTO `test` VALUES (778, 'hahahahhah 778', 'schimmelfuckers 778');
INSERT INTO `test` VALUES (779, 'hahahahhah 779', 'schimmelfuckers 779');
INSERT INTO `test` VALUES (780, 'hahahahhah 780', 'schimmelfuckers 780');
INSERT INTO `test` VALUES (781, 'hahahahhah 781', 'schimmelfuckers 781');
INSERT INTO `test` VALUES (782, 'hahahahhah 782', 'schimmelfuckers 782');
INSERT INTO `test` VALUES (783, 'hahahahhah 783', 'schimmelfuckers 783');
INSERT INTO `test` VALUES (784, 'hahahahhah 784', 'schimmelfuckers 784');
INSERT INTO `test` VALUES (785, 'hahahahhah 785', 'schimmelfuckers 785');
INSERT INTO `test` VALUES (786, 'hahahahhah 786', 'schimmelfuckers 786');
INSERT INTO `test` VALUES (787, 'hahahahhah 787', 'schimmelfuckers 787');
INSERT INTO `test` VALUES (788, 'hahahahhah 788', 'schimmelfuckers 788');
INSERT INTO `test` VALUES (789, 'hahahahhah 789', 'schimmelfuckers 789');
INSERT INTO `test` VALUES (790, 'hahahahhah 790', 'schimmelfuckers 790');
INSERT INTO `test` VALUES (791, 'hahahahhah 791', 'schimmelfuckers 791');
INSERT INTO `test` VALUES (792, 'hahahahhah 792', 'schimmelfuckers 792');
INSERT INTO `test` VALUES (793, 'hahahahhah 793', 'schimmelfuckers 793');
INSERT INTO `test` VALUES (794, 'hahahahhah 794', 'schimmelfuckers 794');
INSERT INTO `test` VALUES (795, 'hahahahhah 795', 'schimmelfuckers 795');
INSERT INTO `test` VALUES (796, 'hahahahhah 796', 'schimmelfuckers 796');
INSERT INTO `test` VALUES (797, 'hahahahhah 797', 'schimmelfuckers 797');
INSERT INTO `test` VALUES (798, 'hahahahhah 798', 'schimmelfuckers 798');
INSERT INTO `test` VALUES (799, 'hahahahhah 799', 'schimmelfuckers 799');
INSERT INTO `test` VALUES (800, 'hahahahhah 800', 'schimmelfuckers 800');
INSERT INTO `test` VALUES (801, 'hahahahhah 801', 'schimmelfuckers 801');
INSERT INTO `test` VALUES (802, 'hahahahhah 802', 'schimmelfuckers 802');
INSERT INTO `test` VALUES (803, 'hahahahhah 803', 'schimmelfuckers 803');
INSERT INTO `test` VALUES (804, 'hahahahhah 804', 'schimmelfuckers 804');
INSERT INTO `test` VALUES (805, 'hahahahhah 805', 'schimmelfuckers 805');
INSERT INTO `test` VALUES (806, 'hahahahhah 806', 'schimmelfuckers 806');
INSERT INTO `test` VALUES (807, 'hahahahhah 807', 'schimmelfuckers 807');
INSERT INTO `test` VALUES (808, 'hahahahhah 808', 'schimmelfuckers 808');
INSERT INTO `test` VALUES (809, 'hahahahhah 809', 'schimmelfuckers 809');
INSERT INTO `test` VALUES (810, 'hahahahhah 810', 'schimmelfuckers 810');
INSERT INTO `test` VALUES (811, 'hahahahhah 811', 'schimmelfuckers 811');
INSERT INTO `test` VALUES (812, 'hahahahhah 812', 'schimmelfuckers 812');
INSERT INTO `test` VALUES (813, 'hahahahhah 813', 'schimmelfuckers 813');
INSERT INTO `test` VALUES (814, 'hahahahhah 814', 'schimmelfuckers 814');
INSERT INTO `test` VALUES (815, 'hahahahhah 815', 'schimmelfuckers 815');
INSERT INTO `test` VALUES (816, 'hahahahhah 816', 'schimmelfuckers 816');
INSERT INTO `test` VALUES (817, 'hahahahhah 817', 'schimmelfuckers 817');
INSERT INTO `test` VALUES (818, 'hahahahhah 818', 'schimmelfuckers 818');
INSERT INTO `test` VALUES (819, 'hahahahhah 819', 'schimmelfuckers 819');
INSERT INTO `test` VALUES (820, 'hahahahhah 820', 'schimmelfuckers 820');
INSERT INTO `test` VALUES (821, 'hahahahhah 821', 'schimmelfuckers 821');
INSERT INTO `test` VALUES (822, 'hahahahhah 822', 'schimmelfuckers 822');
INSERT INTO `test` VALUES (823, 'hahahahhah 823', 'schimmelfuckers 823');
INSERT INTO `test` VALUES (824, 'hahahahhah 824', 'schimmelfuckers 824');
INSERT INTO `test` VALUES (825, 'hahahahhah 825', 'schimmelfuckers 825');
INSERT INTO `test` VALUES (826, 'hahahahhah 826', 'schimmelfuckers 826');
INSERT INTO `test` VALUES (827, 'hahahahhah 827', 'schimmelfuckers 827');
INSERT INTO `test` VALUES (828, 'hahahahhah 828', 'schimmelfuckers 828');
INSERT INTO `test` VALUES (829, 'hahahahhah 829', 'schimmelfuckers 829');
INSERT INTO `test` VALUES (830, 'hahahahhah 830', 'schimmelfuckers 830');
INSERT INTO `test` VALUES (831, 'hahahahhah 831', 'schimmelfuckers 831');
INSERT INTO `test` VALUES (832, 'hahahahhah 832', 'schimmelfuckers 832');
INSERT INTO `test` VALUES (833, 'hahahahhah 833', 'schimmelfuckers 833');
INSERT INTO `test` VALUES (834, 'hahahahhah 834', 'schimmelfuckers 834');
INSERT INTO `test` VALUES (835, 'hahahahhah 835', 'schimmelfuckers 835');
INSERT INTO `test` VALUES (836, 'hahahahhah 836', 'schimmelfuckers 836');
INSERT INTO `test` VALUES (837, 'hahahahhah 837', 'schimmelfuckers 837');
INSERT INTO `test` VALUES (838, 'hahahahhah 838', 'schimmelfuckers 838');
INSERT INTO `test` VALUES (839, 'hahahahhah 839', 'schimmelfuckers 839');
INSERT INTO `test` VALUES (840, 'hahahahhah 840', 'schimmelfuckers 840');
INSERT INTO `test` VALUES (841, 'hahahahhah 841', 'schimmelfuckers 841');
INSERT INTO `test` VALUES (842, 'hahahahhah 842', 'schimmelfuckers 842');
INSERT INTO `test` VALUES (843, 'hahahahhah 843', 'schimmelfuckers 843');
INSERT INTO `test` VALUES (844, 'hahahahhah 844', 'schimmelfuckers 844');
INSERT INTO `test` VALUES (845, 'hahahahhah 845', 'schimmelfuckers 845');
INSERT INTO `test` VALUES (846, 'hahahahhah 846', 'schimmelfuckers 846');
INSERT INTO `test` VALUES (847, 'hahahahhah 847', 'schimmelfuckers 847');
INSERT INTO `test` VALUES (848, 'hahahahhah 848', 'schimmelfuckers 848');
INSERT INTO `test` VALUES (849, 'hahahahhah 849', 'schimmelfuckers 849');
INSERT INTO `test` VALUES (850, 'hahahahhah 850', 'schimmelfuckers 850');
INSERT INTO `test` VALUES (851, 'hahahahhah 851', 'schimmelfuckers 851');
INSERT INTO `test` VALUES (852, 'hahahahhah 852', 'schimmelfuckers 852');
INSERT INTO `test` VALUES (853, 'hahahahhah 853', 'schimmelfuckers 853');
INSERT INTO `test` VALUES (854, 'hahahahhah 854', 'schimmelfuckers 854');
INSERT INTO `test` VALUES (855, 'hahahahhah 855', 'schimmelfuckers 855');
INSERT INTO `test` VALUES (856, 'hahahahhah 856', 'schimmelfuckers 856');
INSERT INTO `test` VALUES (857, 'hahahahhah 857', 'schimmelfuckers 857');
INSERT INTO `test` VALUES (858, 'hahahahhah 858', 'schimmelfuckers 858');
INSERT INTO `test` VALUES (859, 'hahahahhah 859', 'schimmelfuckers 859');
INSERT INTO `test` VALUES (860, 'hahahahhah 860', 'schimmelfuckers 860');
INSERT INTO `test` VALUES (861, 'hahahahhah 861', 'schimmelfuckers 861');
INSERT INTO `test` VALUES (862, 'hahahahhah 862', 'schimmelfuckers 862');
INSERT INTO `test` VALUES (863, 'hahahahhah 863', 'schimmelfuckers 863');
INSERT INTO `test` VALUES (864, 'hahahahhah 864', 'schimmelfuckers 864');
INSERT INTO `test` VALUES (865, 'hahahahhah 865', 'schimmelfuckers 865');
INSERT INTO `test` VALUES (866, 'hahahahhah 866', 'schimmelfuckers 866');
INSERT INTO `test` VALUES (867, 'hahahahhah 867', 'schimmelfuckers 867');
INSERT INTO `test` VALUES (868, 'hahahahhah 868', 'schimmelfuckers 868');
INSERT INTO `test` VALUES (869, 'hahahahhah 869', 'schimmelfuckers 869');
INSERT INTO `test` VALUES (870, 'hahahahhah 870', 'schimmelfuckers 870');
INSERT INTO `test` VALUES (871, 'hahahahhah 871', 'schimmelfuckers 871');
INSERT INTO `test` VALUES (872, 'hahahahhah 872', 'schimmelfuckers 872');
INSERT INTO `test` VALUES (873, 'hahahahhah 873', 'schimmelfuckers 873');
INSERT INTO `test` VALUES (874, 'hahahahhah 874', 'schimmelfuckers 874');
INSERT INTO `test` VALUES (875, 'hahahahhah 875', 'schimmelfuckers 875');
INSERT INTO `test` VALUES (876, 'hahahahhah 876', 'schimmelfuckers 876');
INSERT INTO `test` VALUES (877, 'hahahahhah 877', 'schimmelfuckers 877');
INSERT INTO `test` VALUES (878, 'hahahahhah 878', 'schimmelfuckers 878');
INSERT INTO `test` VALUES (879, 'hahahahhah 879', 'schimmelfuckers 879');
INSERT INTO `test` VALUES (880, 'hahahahhah 880', 'schimmelfuckers 880');
INSERT INTO `test` VALUES (881, 'hahahahhah 881', 'schimmelfuckers 881');
INSERT INTO `test` VALUES (882, 'hahahahhah 882', 'schimmelfuckers 882');
INSERT INTO `test` VALUES (883, 'hahahahhah 883', 'schimmelfuckers 883');
INSERT INTO `test` VALUES (884, 'hahahahhah 884', 'schimmelfuckers 884');
INSERT INTO `test` VALUES (885, 'hahahahhah 885', 'schimmelfuckers 885');
INSERT INTO `test` VALUES (886, 'hahahahhah 886', 'schimmelfuckers 886');
INSERT INTO `test` VALUES (887, 'hahahahhah 887', 'schimmelfuckers 887');
INSERT INTO `test` VALUES (888, 'hahahahhah 888', 'schimmelfuckers 888');
INSERT INTO `test` VALUES (889, 'hahahahhah 889', 'schimmelfuckers 889');
INSERT INTO `test` VALUES (890, 'hahahahhah 890', 'schimmelfuckers 890');
INSERT INTO `test` VALUES (891, 'hahahahhah 891', 'schimmelfuckers 891');
INSERT INTO `test` VALUES (892, 'hahahahhah 892', 'schimmelfuckers 892');
INSERT INTO `test` VALUES (893, 'hahahahhah 893', 'schimmelfuckers 893');
INSERT INTO `test` VALUES (894, 'hahahahhah 894', 'schimmelfuckers 894');
INSERT INTO `test` VALUES (895, 'hahahahhah 895', 'schimmelfuckers 895');
INSERT INTO `test` VALUES (896, 'hahahahhah 896', 'schimmelfuckers 896');
INSERT INTO `test` VALUES (897, 'hahahahhah 897', 'schimmelfuckers 897');
INSERT INTO `test` VALUES (898, 'hahahahhah 898', 'schimmelfuckers 898');
INSERT INTO `test` VALUES (899, 'hahahahhah 899', 'schimmelfuckers 899');
INSERT INTO `test` VALUES (900, 'hahahahhah 900', 'schimmelfuckers 900');
INSERT INTO `test` VALUES (901, 'hahahahhah 901', 'schimmelfuckers 901');
INSERT INTO `test` VALUES (902, 'hahahahhah 902', 'schimmelfuckers 902');
INSERT INTO `test` VALUES (903, 'hahahahhah 903', 'schimmelfuckers 903');
INSERT INTO `test` VALUES (904, 'hahahahhah 904', 'schimmelfuckers 904');
INSERT INTO `test` VALUES (905, 'hahahahhah 905', 'schimmelfuckers 905');
INSERT INTO `test` VALUES (906, 'hahahahhah 906', 'schimmelfuckers 906');
INSERT INTO `test` VALUES (907, 'hahahahhah 907', 'schimmelfuckers 907');
INSERT INTO `test` VALUES (908, 'hahahahhah 908', 'schimmelfuckers 908');
INSERT INTO `test` VALUES (909, 'hahahahhah 909', 'schimmelfuckers 909');
INSERT INTO `test` VALUES (910, 'hahahahhah 910', 'schimmelfuckers 910');
INSERT INTO `test` VALUES (911, 'hahahahhah 911', 'schimmelfuckers 911');
INSERT INTO `test` VALUES (912, 'hahahahhah 912', 'schimmelfuckers 912');
INSERT INTO `test` VALUES (913, 'hahahahhah 913', 'schimmelfuckers 913');
INSERT INTO `test` VALUES (914, 'hahahahhah 914', 'schimmelfuckers 914');
INSERT INTO `test` VALUES (915, 'hahahahhah 915', 'schimmelfuckers 915');
INSERT INTO `test` VALUES (916, 'hahahahhah 916', 'schimmelfuckers 916');
INSERT INTO `test` VALUES (917, 'hahahahhah 917', 'schimmelfuckers 917');
INSERT INTO `test` VALUES (918, 'hahahahhah 918', 'schimmelfuckers 918');
INSERT INTO `test` VALUES (919, 'hahahahhah 919', 'schimmelfuckers 919');
INSERT INTO `test` VALUES (920, 'hahahahhah 920', 'schimmelfuckers 920');
INSERT INTO `test` VALUES (921, 'hahahahhah 921', 'schimmelfuckers 921');
INSERT INTO `test` VALUES (922, 'hahahahhah 922', 'schimmelfuckers 922');
INSERT INTO `test` VALUES (923, 'hahahahhah 923', 'schimmelfuckers 923');
INSERT INTO `test` VALUES (924, 'hahahahhah 924', 'schimmelfuckers 924');
INSERT INTO `test` VALUES (925, 'hahahahhah 925', 'schimmelfuckers 925');
INSERT INTO `test` VALUES (926, 'hahahahhah 926', 'schimmelfuckers 926');
INSERT INTO `test` VALUES (927, 'hahahahhah 927', 'schimmelfuckers 927');
INSERT INTO `test` VALUES (928, 'hahahahhah 928', 'schimmelfuckers 928');
INSERT INTO `test` VALUES (929, 'hahahahhah 929', 'schimmelfuckers 929');
INSERT INTO `test` VALUES (930, 'hahahahhah 930', 'schimmelfuckers 930');
INSERT INTO `test` VALUES (931, 'hahahahhah 931', 'schimmelfuckers 931');
INSERT INTO `test` VALUES (932, 'hahahahhah 932', 'schimmelfuckers 932');
INSERT INTO `test` VALUES (933, 'hahahahhah 933', 'schimmelfuckers 933');
INSERT INTO `test` VALUES (934, 'hahahahhah 934', 'schimmelfuckers 934');
INSERT INTO `test` VALUES (935, 'hahahahhah 935', 'schimmelfuckers 935');
INSERT INTO `test` VALUES (936, 'hahahahhah 936', 'schimmelfuckers 936');
INSERT INTO `test` VALUES (937, 'hahahahhah 937', 'schimmelfuckers 937');
INSERT INTO `test` VALUES (938, 'hahahahhah 938', 'schimmelfuckers 938');
INSERT INTO `test` VALUES (939, 'hahahahhah 939', 'schimmelfuckers 939');
INSERT INTO `test` VALUES (940, 'hahahahhah 940', 'schimmelfuckers 940');
INSERT INTO `test` VALUES (941, 'hahahahhah 941', 'schimmelfuckers 941');
INSERT INTO `test` VALUES (942, 'hahahahhah 942', 'schimmelfuckers 942');
INSERT INTO `test` VALUES (943, 'hahahahhah 943', 'schimmelfuckers 943');
INSERT INTO `test` VALUES (944, 'hahahahhah 944', 'schimmelfuckers 944');
INSERT INTO `test` VALUES (945, 'hahahahhah 945', 'schimmelfuckers 945');
INSERT INTO `test` VALUES (946, 'hahahahhah 946', 'schimmelfuckers 946');
INSERT INTO `test` VALUES (947, 'hahahahhah 947', 'schimmelfuckers 947');
INSERT INTO `test` VALUES (948, 'hahahahhah 948', 'schimmelfuckers 948');
INSERT INTO `test` VALUES (949, 'hahahahhah 949', 'schimmelfuckers 949');
INSERT INTO `test` VALUES (950, 'hahahahhah 950', 'schimmelfuckers 950');
INSERT INTO `test` VALUES (951, 'hahahahhah 951', 'schimmelfuckers 951');
INSERT INTO `test` VALUES (952, 'hahahahhah 952', 'schimmelfuckers 952');
INSERT INTO `test` VALUES (953, 'hahahahhah 953', 'schimmelfuckers 953');
INSERT INTO `test` VALUES (954, 'hahahahhah 954', 'schimmelfuckers 954');
INSERT INTO `test` VALUES (955, 'hahahahhah 955', 'schimmelfuckers 955');
INSERT INTO `test` VALUES (956, 'hahahahhah 956', 'schimmelfuckers 956');
INSERT INTO `test` VALUES (957, 'hahahahhah 957', 'schimmelfuckers 957');
INSERT INTO `test` VALUES (958, 'hahahahhah 958', 'schimmelfuckers 958');
INSERT INTO `test` VALUES (959, 'hahahahhah 959', 'schimmelfuckers 959');
INSERT INTO `test` VALUES (960, 'hahahahhah 960', 'schimmelfuckers 960');
INSERT INTO `test` VALUES (961, 'hahahahhah 961', 'schimmelfuckers 961');
INSERT INTO `test` VALUES (962, 'hahahahhah 962', 'schimmelfuckers 962');
INSERT INTO `test` VALUES (963, 'hahahahhah 963', 'schimmelfuckers 963');
INSERT INTO `test` VALUES (964, 'hahahahhah 964', 'schimmelfuckers 964');
INSERT INTO `test` VALUES (965, 'hahahahhah 965', 'schimmelfuckers 965');
INSERT INTO `test` VALUES (966, 'hahahahhah 966', 'schimmelfuckers 966');
INSERT INTO `test` VALUES (967, 'hahahahhah 967', 'schimmelfuckers 967');
INSERT INTO `test` VALUES (968, 'hahahahhah 968', 'schimmelfuckers 968');
INSERT INTO `test` VALUES (969, 'hahahahhah 969', 'schimmelfuckers 969');
INSERT INTO `test` VALUES (970, 'hahahahhah 970', 'schimmelfuckers 970');
INSERT INTO `test` VALUES (971, 'hahahahhah 971', 'schimmelfuckers 971');
INSERT INTO `test` VALUES (972, 'hahahahhah 972', 'schimmelfuckers 972');
INSERT INTO `test` VALUES (973, 'hahahahhah 973', 'schimmelfuckers 973');
INSERT INTO `test` VALUES (974, 'hahahahhah 974', 'schimmelfuckers 974');
INSERT INTO `test` VALUES (975, 'hahahahhah 975', 'schimmelfuckers 975');
INSERT INTO `test` VALUES (976, 'hahahahhah 976', 'schimmelfuckers 976');
INSERT INTO `test` VALUES (977, 'hahahahhah 977', 'schimmelfuckers 977');
INSERT INTO `test` VALUES (978, 'hahahahhah 978', 'schimmelfuckers 978');
INSERT INTO `test` VALUES (979, 'hahahahhah 979', 'schimmelfuckers 979');
INSERT INTO `test` VALUES (980, 'hahahahhah 980', 'schimmelfuckers 980');
INSERT INTO `test` VALUES (981, 'hahahahhah 981', 'schimmelfuckers 981');
INSERT INTO `test` VALUES (982, 'hahahahhah 982', 'schimmelfuckers 982');
INSERT INTO `test` VALUES (983, 'hahahahhah 983', 'schimmelfuckers 983');
INSERT INTO `test` VALUES (984, 'hahahahhah 984', 'schimmelfuckers 984');
INSERT INTO `test` VALUES (985, 'hahahahhah 985', 'schimmelfuckers 985');
INSERT INTO `test` VALUES (986, 'hahahahhah 986', 'schimmelfuckers 986');
INSERT INTO `test` VALUES (987, 'hahahahhah 987', 'schimmelfuckers 987');
INSERT INTO `test` VALUES (988, 'hahahahhah 988', 'schimmelfuckers 988');
INSERT INTO `test` VALUES (989, 'hahahahhah 989', 'schimmelfuckers 989');
INSERT INTO `test` VALUES (990, 'hahahahhah 990', 'schimmelfuckers 990');
INSERT INTO `test` VALUES (991, 'hahahahhah 991', 'schimmelfuckers 991');
INSERT INTO `test` VALUES (992, 'hahahahhah 992', 'schimmelfuckers 992');
INSERT INTO `test` VALUES (993, 'hahahahhah 993', 'schimmelfuckers 993');
INSERT INTO `test` VALUES (994, 'hahahahhah 994', 'schimmelfuckers 994');
INSERT INTO `test` VALUES (995, 'hahahahhah 995', 'schimmelfuckers 995');
INSERT INTO `test` VALUES (996, 'hahahahhah 996', 'schimmelfuckers 996');
INSERT INTO `test` VALUES (997, 'hahahahhah 997', 'schimmelfuckers 997');
INSERT INTO `test` VALUES (998, 'hahahahhah 998', 'schimmelfuckers 998');
INSERT INTO `test` VALUES (999, 'hahahahhah 999', 'schimmelfuckers 999');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The backend users' AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 1, 'tijs', 'b45a8670f0f9d65aef1512516b97c12f43c62fcd', 'Y', 'N', 'Y');
INSERT INTO `users` VALUES (6, 1, 'dave', '12961055474222b28d6885c3622e06879edc47b3', 'Y', 'N', 'N');
INSERT INTO `users` VALUES (5, 1, 'Wolfr', 'ae2b1fca515949e5d54fb22b8ed95575', 'Y', 'N', 'N');
INSERT INTO `users` VALUES (7, 1, 'bauffman', '6a0711fc48e846b7af71d99b4b01a1e33d9e0d3c', 'Y', 'N', 'N');

-- --------------------------------------------------------

-- 
-- Table structure for table `users_sessions`
-- 

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `session_id` varchar(255) collate utf8_unicode_ci NOT NULL,
  `secret_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=102 ;

-- 
-- Dumping data for table `users_sessions`
-- 

INSERT INTO `users_sessions` VALUES (101, 1, '', 'a2b2cfa049b11da03b8f87939a9060e0', 'e8d02910860caec10b2447aa587f4d7f15aa11a5', '2010-01-31 13:56:41');

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
INSERT INTO `users_settings` VALUES (1, 'email', 's:16:"tijs@netlash.com";');
INSERT INTO `users_settings` VALUES (1, 'form', 's:4:"edit";');
INSERT INTO `users_settings` VALUES (1, 'interface_language', 's:2:"nl";');
INSERT INTO `users_settings` VALUES (1, 'name', 's:4:"Tijs";');
INSERT INTO `users_settings` VALUES (1, 'nickname', 's:4:"Tijs";');
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
INSERT INTO `users_settings` VALUES (1, 'reset_password_key', 's:40:"dd5eb2224e67796c02b167807def3d7c7071236f";');
INSERT INTO `users_settings` VALUES (0, 'avatar', 'N;');
INSERT INTO `users_settings` VALUES (1, 'reset_password_timestamp', 'i:1264937756;');
