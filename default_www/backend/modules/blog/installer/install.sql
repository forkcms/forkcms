DROP TABLE IF EXISTS `blog_categories`;
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `blog_comments`;
CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `author` varchar(255) collate utf8_unicode_ci NOT NULL,
  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `website` varchar(255) collate utf8_unicode_ci default NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `type` enum('comment','trackback') collate utf8_unicode_ci NOT NULL default 'comment',
  `status` enum('published','moderation','spam') collate utf8_unicode_ci NOT NULL default 'moderation',
  `data` text collate utf8_unicode_ci COMMENT 'Serialized array with extra data',
  PRIMARY KEY  (`id`),
  KEY `idx_post_id_status` (`post_id`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL COMMENT 'The real post id',
  `revision_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `introduction` text collate utf8_unicode_ci,
  `text` text collate utf8_unicode_ci,
  `status` enum('active','archived','draft') collate utf8_unicode_ci NOT NULL,
  `publish_on` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `hidden` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `allow_comments` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  `num_comments` int(11) NOT NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `idx_status_language_hidden` (`status`,`language`,`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(142, 1, 'nl', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%1$s">Configureer</a>', '2010-07-01 13:54:13'),
(183, 1, 'nl', 'backend', 'blog', 'msg', 'Deleted', 'De geselecteerde artikels werden verwijderd.', '2010-06-22 12:09:16'),
(184, 16, 'nl', 'backend', 'blog', 'msg', 'NoItems', 'Er zijn nog geen artikels. <a href="%1$s">Schrijf het eerste artikel</a>.', '2010-06-23 15:42:39'),
(187, 7, 'nl', 'backend', 'blog', 'lbl', 'Add', 'artikel toevoegen', '2010-06-21 12:52:29'),
(191, 16, 'nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Maak voor lange artikels een inleiding of samenvatting. Die kan getoond worden op de homepage of het artikeloverzicht.', '2010-06-23 15:38:14'),
(215, 1, 'nl', 'backend', 'blog', 'msg', 'Added', 'Het artikel "%1$s" werd toegevoegd.', '2010-06-22 08:20:01'),
(221, 1, 'nl', 'backend', 'blog', 'msg', 'EditArticle', 'bewerk artikel "%1$s"', '2010-06-22 08:51:29'),
(224, 1, 'nl', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het artikel "%1$s" wil verwijderen?', '2010-06-22 08:58:22'),
(225, 16, 'nl', 'backend', 'blog', 'msg', 'Edited', 'Het artikel "%1$s" werd opgeslagen.', '2010-06-23 14:01:48'),
(254, 1, 'nl', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Reactie op: <a href="%1$s">%2$s</a>', '2010-06-22 13:52:29'),
(268, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden.', '2010-06-22 14:32:01'),
(271, 1, 'nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.', '2010-06-22 14:33:27'),
(357, 1, 'nl', 'backend', 'blog', 'msg', 'NumItemsInRecentArticles', 'Aantal items in recente artikels widget', '2010-06-30 09:23:47'),
(424, 1, 'nl', 'backend', 'blog', 'msg', 'HelpMeta', 'Toon de meta informatie van deze blogpost in de RSS feed (categorie, tags, ...)', '2010-07-05 11:53:51');

INSERT INTO `locale` (`id`, `user_id`, `language`, `application`, `module`, `type`, `name`, `value`, `edited_on`) VALUES
(390, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste', '2010-07-01 07:49:40'),
(401, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %1$s reacties', '2010-07-01 07:53:26'),
(402, 1, 'nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie', '2010-07-01 07:54:27'),
(409, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd.', '2010-07-01 08:03:52'),
(410, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.', '2010-07-01 08:04:06'),
(411, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam.', '2010-07-01 08:04:20'),
(450, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoItems', 'Er zijn nog geen blogposts.', '2010-07-06 09:06:48');