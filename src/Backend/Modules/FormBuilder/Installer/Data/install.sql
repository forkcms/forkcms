CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `language` varchar(5) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `method` enum('database','database_email','email') NOT NULL default 'database_email',
  `email` text,
  `success_message` text,
  `identifier` varchar(255) NOT NULL,
  `email_template` VARCHAR(255) DEFAULT "Form.html.twig",
  `email_subject` VARCHAR(255) NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forms_data` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `form_id` int(11) unsigned NOT NULL,
  `session_id` varchar(255) default NULL,
  `sent_on` datetime NOT NULL,
  `data` text COMMENT 'Serialized array with extra information.',
  PRIMARY KEY  (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forms_data_fields` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `data_id` int(11) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `data_id` (`data_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forms_fields` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `form_id` int(11) unsigned NOT NULL,
  `type` enum('textbox','textarea', 'datetime', 'dropdown','checkbox','radiobutton','heading','paragraph','submit') NOT NULL,
  `settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sequence` int(11) NULL,
  PRIMARY KEY  (`id`),
  KEY `sequence` (`sequence`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forms_fields_validation` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `field_id` int(11) unsigned NOT NULL,
  `type` enum('required','email','number','time') NOT NULL,
  `parameter` varchar(255) default NULL COMMENT 'If you want to validate higher then a number, the number would be the parameter',
  `error_message` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
