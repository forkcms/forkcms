CREATE TABLE IF NOT EXISTS `events` (
 `id` int(11) NOT NULL COMMENT 'The real event id',
 `revision_id` int(11) NOT NULL auto_increment,
 `category_id` int(11) NOT NULL,
 `user_id` int(11) NOT NULL,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `starts_on` datetime NOT NULL,
 `ends_on` datetime DEFAULT NULL,
 `introduction` text,
 `text` text,
 `status` enum('active','archived','draft') NOT NULL,
 `publish_on` datetime NOT NULL,
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 `hidden` enum('Y','N') NOT NULL DEFAULT 'N',
 `allow_comments` enum('Y','N') NOT NULL DEFAULT 'N',
 `num_comments` int(11) NOT NULL,
 `allow_subscriptions` enum('N','Y') NOT NULL DEFAULT 'N',
 `max_subscriptions` int(11) DEFAULT NULL,
 `num_subscriptions` int(11) DEFAULT NULL,
 PRIMARY KEY (`revision_id`),
 KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `events_categories` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `events_comments` (
 `id` int(11) NOT NULL auto_increment,
 `event_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `created_on` datetime NOT NULL,
 `author` varchar(255) NOT NULL,
 `email` varchar(255) NOT NULL,
 `website` varchar(255) DEFAULT NULL,
 `text` text NOT NULL,
 `type` enum('comment','trackback') NOT NULL DEFAULT 'comment',
 `status` enum('published','moderation','spam') NOT NULL DEFAULT 'moderation',
 `data` text COMMENT 'Serialized array with extra data',
 PRIMARY KEY (`id`),
 KEY `idx_id_status` (`event_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `events_subscriptions` (
 `id` int(11) NOT NULL auto_increment,
 `event_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `created_on` datetime NOT NULL,
 `author` varchar(255) NOT NULL,
 `email` varchar(255) NOT NULL,
 `num_attendees` int(11) NOT NULL DEFAULT '1',
 `status` enum('published','moderation','spam') NOT NULL DEFAULT 'moderation',
 `data` text COMMENT 'Serialized array with extra data',
 PRIMARY KEY (`id`),
 KEY `idx_id_status` (`event_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;