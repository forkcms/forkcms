CREATE TABLE `mail_to_friend` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `own` text NOT NULL COMMENT 'Serialized data with the users own information',
  `friend` text NOT NULL COMMENT 'Serialized data with the users friends data',
  `message` text NOT NULL,
  `page` varchar(255) NOT NULL default '',
  `language` varchar(5) NOT NULL default '',
  `created_on` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;