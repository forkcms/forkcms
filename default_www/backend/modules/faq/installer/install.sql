CREATE TABLE IF NOT EXISTS `faq_categories` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `language` varchar(5) NOT NULL,
 `extra_id` int(11) NOT NULL,
 `name` varchar(255) NOT NULL,
 `sequence` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `faq_questions` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `category_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `question` varchar(255) NOT NULL,
 `answer` text NOT NULL,
 `hidden` enum('Y','N') NOT NULL DEFAULT 'N',
 `sequence` int(11) NOT NULL,
 `created_on` datetime NOT NULL,
 PRIMARY KEY (`id`),
 KEY `fk_faq_questions_faq_categories` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;