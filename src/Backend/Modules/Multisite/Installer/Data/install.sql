CREATE TABLE IF NOT EXISTS `sites` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The domain for this site',
 `is_active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Whether or not this site is active',
 `is_viewable` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Whether or not this site is viewable',
 `is_main_site` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'The main site can be used as fallback.',
 `prefix` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'prefix to use for development purposes, one puts the prefix in the url, and the system will try to match it',
 PRIMARY KEY (`id`),
 KEY `idx_sites_domain` (`domain`),
 KEY `idx_sites_is_active` (`is_active`),
 KEY `idx_sites_is_viewable` (`is_viewable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sites_languages` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `site_id` int(11) NOT NULL COMMENT 'ID of the site this language is for.',
 `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Language for a specific site.',
 `is_active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Whether or not this language is active',
 `is_viewable` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Whether or not this language is viewable',
 PRIMARY KEY (`id`),
 KEY `idx_site_languages_site_id` (`site_id`),
 KEY `idx_site_languages_language` (`language`),
 KEY `idx_site_languages_is_active` (`is_active`),
 KEY `idx_site_languages_is_viewable` (`is_viewable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
