CREATE TABLE IF NOT EXISTS `search_index` (
  `module` varchar(255) NOT NULL,
  `other_id` int(11) NOT NULL,
  `field` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `language` varchar(5) NOT NULL,
  `active` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY (`module`,`other_id`,`field`,`language`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Search index';


CREATE TABLE IF NOT EXISTS `search_modules` (
  `module` varchar(255) NOT NULL,
  `searchable` enum('Y','N') NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `search_statistics` (
  `id` int(11) NOT NULL auto_increment,
  `term` varchar(255) NOT NULL,
  `language` varchar(5) NOT NULL,
  `time` datetime NOT NULL,
  `data` text,
  `num_results` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `search_synonyms` (
  `id` int(11) NOT NULL auto_increment,
  `term` varchar(255) NOT NULL,
  `synonym` text NOT NULL,
  `language` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UNIQUE` (`term`,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `locale` (`user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(1, 'nl', 'backend', 'search', 'lbl', 'SearchedOn', 'gezocht op', '2010-07-08 13:53:39'),
(1, 'nl', 'backend', 'search', 'lbl', 'Term', 'zoekterm', '2010-07-08 13:51:31'),
(1, 'nl', 'backend', 'search', 'lbl', 'IP', 'IP', '2010-07-08 13:51:41'),
(1, 'nl', 'backend', 'search', 'lbl', 'Referrer', 'referrer', '2010-07-08 13:51:58'),
(1, 'nl', 'backend', 'search', 'lbl', 'AddSynonym', 'synoniem toevoegen', '2010-07-08 13:55:46'),
(1, 'nl', 'backend', 'search', 'lbl', 'Synonym', 'synoniem', '2010-07-08 13:56:06'),
(1, 'nl', 'backend', 'search', 'lbl', 'EditSynonym', 'synoniem bewerken', '2010-07-08 13:57:14'),
(1, 'nl', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Ben je zeker dat je de synoniemen voor zoekterm "%1$s" wil verwijderen?', '2010-07-08 14:19:15'),
(1, 'nl', 'backend', 'search', 'msg', 'AddedSynonym', 'De synoniemen voor zoekterm "%1$s" werden toegevoegd.', '2010-07-08 15:07:04'),
(1, 'nl', 'backend', 'search', 'msg', 'EditedSynonym', 'De synoniemen voor zoekterm "%1$s" werden opgeslagen.', '2010-07-08 15:07:35'),
(1, 'nl', 'backend', 'search', 'msg', 'DeletedSynonym', 'De synoniemen voor zoekterm "%1$s" werden verwijderd.', '2010-07-08 15:07:56'),
(1, 'nl', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn', '2010-07-08 15:12:03'),
(1, 'nl', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synoniemen zijn verplicht', '2010-07-08 15:12:31'),
(1, 'nl', 'backend', 'search', 'err', 'TermIsRequired', 'De zoekterm is verplicht', '2010-07-08 15:12:44'),
(1, 'nl', 'backend', 'search', 'err', 'TermExists', 'Synoniemen voor deze zoekterm bestaan reeds', '2010-07-08 15:13:07');


INSERT INTO `locale` (`user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(1, 'nl', 'frontend', 'core', 'lbl', 'SearchTerm', 'zoekterm', '2010-07-08 13:42:04'),
(1, 'nl', 'frontend', 'core', 'lbl', 'Search', 'zoeken', '2010-07-08 13:42:24'),
(1, 'nl', 'frontend', 'core', 'err', 'TermIsRequired', 'Gelieve uw zoekterm in te vullen', '2010-07-08 13:44:09'),
(1, 'nl', 'backend', 'core', 'msg', 'NoSynonyms', 'Er zijn nog geen synoniemen', '2010-07-08 13:48:25'),
(1, 'nl', 'backend', 'core', 'lbl', 'DeleteSynonym', 'synoniem verwijderen', '2010-07-08 13:48:42'),
(1, 'nl', 'backend', 'core', 'lbl', 'Search', 'zoeken', '2010-07-08 13:50:05'),
(1, 'nl', 'backend', 'core', 'lbl', 'Synonyms', 'synoniemen', '2010-07-08 13:54:30');