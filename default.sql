-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 08, 2008 at 03:55 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `forkng`
--

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

DROP TABLE IF EXISTS `meta`;
CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL auto_increment,
  `keywords` varchar(255) NOT NULL,
  `keywords_overwrite` enum('Y','N') NOT NULL default 'N',
  `description` varchar(255) NOT NULL,
  `description_overwrite` enum('Y','N') NOT NULL default 'N',
  `pagetitle` varchar(255) NOT NULL,
  `pagetitle_overwrite` enum('Y','N') NOT NULL default 'N',
  `url` varchar(255) NOT NULL,
  `url_overwrite` enum('Y','N') NOT NULL default 'N',
  `custom` text COMMENT 'Used for custom meta-information',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Meta-information';

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` VALUES(1, 'home', 'N', 'home', 'N', 'home', 'N', 'home', 'N', '');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL auto_increment,
  `menu_id` int(11) NOT NULL COMMENT 'The real menu_id',
  `parent_id` int(11) NOT NULL default '0' COMMENT 'The parent_id for the page ',
  `template_id` int(11) NOT NULL default '0' COMMENT 'The template to use',
  `meta_id` int(11) NOT NULL COMMENT 'Meta-information',
  `extra_id` int(11) default NULL COMMENT 'Possible extra',
  `language` varchar(5) NOT NULL COMMENT 'Language of the content',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `navigation_title` varchar(255) NOT NULL COMMENT 'Title that will be used in the navigation',
  `navigation_title_overwrite` enum('Y','N') NOT NULL default 'N' COMMENT 'Should we override the navigation title',
  `sequence` int(11) NOT NULL,
  `hidden` enum('Y','N') NOT NULL default 'N' COMMENT 'Is the page hidden?',
  `active` enum('Y','N') NOT NULL default 'Y' COMMENT 'Is this the active version?',
  `created_by_user` int(11) NOT NULL COMMENT 'Which user has created this page?',
  `created_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `allow_move` enum('Y','N') NOT NULL default 'Y',
  `allow_childs` enum('Y','N') NOT NULL default 'Y',
  `allow_content` enum('Y','N') NOT NULL default 'Y',
  `allow_edit` enum('Y','N') NOT NULL default 'Y',
  `allow_delete` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`id`),
  KEY `fk_pages_meta` (`meta_id`),
  KEY `fk_pages_pages_extra` (`extra_id`),
  KEY `fk_pages_pages_templates` (`template_id`),
  KEY `fk_pages_users` (`created_by_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` VALUES(1, 1, 0, 0, 1, NULL, 'nl', 'Home', '<h2>Fork CMS - SEO inbegrepen</h2>\r\n<p>Fork is het CMS (Content Management System) van het Gentse webbureau <a href="http://www.netlash.com" title="Webdesign Netlash">Netlash</a>.</p>\r\n<p>Netlash'' SEO-kennis (zoekmachine optimalisatie) zit ingebouwd in Fork. Dat wil zeggen dat op <a href="http://www.fork-cms.be" title="Fork CMS">Fork</a> gebaseerde sites automatisch goed scoren in Google. Neem een kijkje in de portfolio om uzelf te overtuigen.</p>\r\n<p>Om het zichzelf en zijn klanten makkelijk te maken, gebruikt Netlash steeds Fork als basis voor een website. Door goed te luisteren naar zowel klanten als (collega-)webdesigners is Fork ge&#235;volutioneerd tot een ideale symbiose tussen de invalshoeken van webmasters en -designers.</p>\r\n<h2>Usability</h2>\r\n<blockquote>\r\n<p>"Make everything as simple as possible, but not simpler." (Albert Einstein)</p>\r\n</blockquote>\r\n<p>Einstein vat daarmee de filosofie achter Fork goed samen. Fork doet niet moeilijk. Het toont een simpele, sobere, intu&#239;tieve interface. Toch biedt het tegelijk ook zeer geavanceerde instellingen. Die geavanceerde instellingen (SEO, versiebeheer, spamfilter, templates) houden zich subtiel op de achtergrond.</p>\r\n<p>Het Netlash team helpt zijn klanten graag met problemen, maar nog liever ontwerpt en bouwt het knappe websites. Dat kon alleen door het CMS van zijn websites zo gebruiksvriendelijk mogelijk te maken. Fork is het antwoord.</p>\r\n<h2>Modulair en to-the-point</h2>\r\n<p>Netlash is een klein en flexibel bedrijf. Die lijn is doorgetrokken naar Fork: geen log, ingewikkeld systeem maar een lichte, flexibele, modulaire oplossing.<br />Fork Core is de motor van Fork. Modules als een multiblog, evenementenkalender, nieuwsbrief, fotoalbum, ... zijn inplugbaar.</p>\r\n<p>Door enkel te tonen wat nodig is, bereikt Fork iets waar designers erg van houden: er is geen <em>clutter.</em></p>', 'Home', 'N', 1, 'N', 'Y', 1, '2008-11-08 15:33:42', '2008-11-08 15:33:42', 'N', 'Y', 'Y', 'Y', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `pages_extra`
--

DROP TABLE IF EXISTS `pages_extra`;
CREATE TABLE IF NOT EXISTS `pages_extra` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` int(11) NOT NULL COMMENT 'The module the extra belongs to',
  `title` varchar(255) NOT NULL COMMENT 'A label that will be used in the backend',
  `location` varchar(255) NOT NULL COMMENT 'The location of the extra',
  `parameters` text COMMENT 'A serialized value with the optional parameters',
  `sequence` int(11) NOT NULL COMMENT 'The sequcnce in the backend (default is the module_id)',
  `hidden` enum('Y','N') NOT NULL default 'N' COMMENT 'Should the extra be shown in the backend?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The possible extras';

--
-- Dumping data for table `pages_extra`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages_templates`
--

DROP TABLE IF EXISTS `pages_templates`;
CREATE TABLE IF NOT EXISTS `pages_templates` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL COMMENT 'A label that will be uses in the backend',
  `location` varchar(255) NOT NULL COMMENT 'The location of the template',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The possible templates';

--
-- Dumping data for table `pages_templates`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL COMMENT 'username, will be case-sensitive',
  `password` varchar(255) NOT NULL COMMENT 'will be case-sensitive',
  `password_raw` varchar(255) NOT NULL COMMENT 'Used when a user forgot his password',
  `active` enum('Y','N') NOT NULL default 'Y' COMMENT 'Is this user active?',
  `deleted` enum('Y','N') NOT NULL default 'N' COMMENT 'Is the user deleted?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='The backend users';

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 'tijs', '1ce08008e914c637cf7659bc6a80720c', 'tijs', 'Y', 'N');
