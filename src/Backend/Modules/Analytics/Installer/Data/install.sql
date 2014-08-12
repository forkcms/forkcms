CREATE TABLE IF NOT EXISTS `analytics_keywords` (
 `id` int(11) NOT NULL auto_increment,
 `site_id` int(11) NOT NULL,
 `keyword` varchar(255) NOT NULL,
 `entrances` int(11) NOT NULL,
 `date` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `analytics_landing_pages` (
 `id` int(11) NOT NULL auto_increment,
 `site_id` int(11) NOT NULL,
 `page_path` varchar(255) NOT NULL,
 `entrances` int(11) NOT NULL,
 `bounces` int(11) NOT NULL,
 `bounce_rate` varchar(255) NOT NULL,
 `start_date` datetime NOT NULL,
 `end_date` datetime NOT NULL,
 `updated_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `analytics_pages` (
 `id` int(11) NOT NULL auto_increment,
 `site_id` int(11) NOT NULL,
 `page` varchar(255) NOT NULL,
 `date_viewed` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `analytics_referrers` (
 `id` int(11) NOT NULL auto_increment,
 `site_id` int(11) NOT NULL,
 `referrer` varchar(255) NOT NULL,
 `entrances` int(11) NOT NULL,
 `date` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
