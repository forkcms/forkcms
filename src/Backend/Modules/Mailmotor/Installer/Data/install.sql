CREATE TABLE IF NOT EXISTS `mailmotor_addresses` (
 `email` varchar(255) NOT NULL,
 `source` varchar(255) default NULL,
 `created_on` datetime default NULL,
 PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `mailmotor_addresses_groups` (
 `email` varchar(255) NOT NULL,
 `group_id` int(11) NOT NULL,
 `custom_fields` text,
 `status` enum('subscribed','unsubscribed','inserted') NOT NULL,
 `subscribed_on` datetime default NULL,
 `unsubscribed_on` datetime default NULL,
 PRIMARY KEY (`email`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `mailmotor_campaignmonitor_ids` (
 `cm_id` varchar(50) NOT NULL,
 `type` enum('campaign','list','template') NOT NULL,
 `other_id` int(11) NOT NULL,
 PRIMARY KEY (`type`,`cm_id`,`other_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `mailmotor_campaigns` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(255) NOT NULL,
 `created_on` datetime default NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `mailmotor_groups` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) NULL,
 `name` varchar(255) NOT NULL,
 `custom_fields` text,
 `is_default` enum('N','Y') NOT NULL default 'N',
 `created_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `mailmotor_mailings` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) NOT NULL,
 `template` varchar(255) default NULL,
 `campaign_id` int(11) default NULL,
 `name` varchar(255) default NULL,
 `from_name` varchar(255) default NULL,
 `from_email` varchar(255) default NULL,
 `reply_to_name` varchar(255) default NULL,
 `reply_to_email` varchar(255) default NULL,
 `subject` varchar(255) default NULL,
 `content_html` text,
 `content_plain` text,
 `send_on` datetime default NULL,
 `status` enum('concept','queued','sent') default NULL,
 `created_on` datetime default NULL,
 `edited_on` datetime default NULL,
 PRIMARY KEY (`id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `mailmotor_mailings_groups` (
 `mailing_id` int(11) NOT NULL,
 `group_id` int(11) NOT NULL,
 PRIMARY KEY (`mailing_id`,`group_id`),
 KEY `group_id` (`group_id`),
 KEY `mailing_id` (`mailing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
