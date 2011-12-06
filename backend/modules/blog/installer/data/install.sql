CREATE TABLE IF NOT EXISTS `blog_categories` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `blog_comments` (
 `id` int(11) NOT NULL auto_increment,
 `post_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `created_on` datetime NOT NULL,
 `author` varchar(255) NOT NULL,
 `email` varchar(255) NOT NULL,
 `website` text,
 `text` text NOT NULL,
 `type` enum('comment','trackback') NOT NULL default 'comment',
 `status` enum('published','moderation','spam') NOT NULL default 'moderation',
 `data` text COMMENT 'Serialized array with extra data',
 PRIMARY KEY (`id`),
 KEY `idx_post_id_status` (`post_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `blog_posts` (
 `id` int(11) NOT NULL COMMENT 'The real post id',
 `revision_id` int(11) NOT NULL auto_increment,
 `category_id` int(11) NOT NULL,
 `user_id` int(11) NOT NULL,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `introduction` text,
 `text` text,
 `image` varchar(255),
 `status` enum('active','archived','draft') NOT NULL,
 `publish_on` datetime NOT NULL,
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 `hidden` enum('N','Y') NOT NULL default 'N',
 `allow_comments` enum('N','Y') NOT NULL default 'N',
 `num_comments` int(11) NOT NULL,
 PRIMARY KEY (`revision_id`),
 KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
