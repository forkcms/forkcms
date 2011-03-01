CREATE TABLE IF NOT EXISTS `search_index` (
 `module` varchar(255) NOT NULL,
 `other_id` int(11) NOT NULL,
 `field` varchar(64) NOT NULL,
 `value` text NOT NULL,
 `language` varchar(5) NOT NULL,
 `active` enum('N','Y') NOT NULL default 'N',
 PRIMARY KEY (`module`,`other_id`,`field`,`language`),
 FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Search index';


CREATE TABLE IF NOT EXISTS `search_modules` (
 `module` varchar(255) NOT NULL,
 `searchable` enum('N','Y') NOT NULL,
 `weight` int(11) NOT NULL,
 PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `search_statistics` (
 `id` int(11) NOT NULL auto_increment,
 `term` varchar(255) NOT NULL,
 `language` varchar(5) NOT NULL,
 `time` datetime NOT NULL,
 `data` text,
 `num_results` int(11) default NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `search_synonyms` (
 `id` int(11) NOT NULL auto_increment,
 `term` varchar(255) NOT NULL,
 `synonym` text NOT NULL,
 `language` varchar(5) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `UNIQUE` (`term`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;