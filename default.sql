DROP TABLE IF EXISTS emails;
CREATE TABLE IF NOT EXISTS emails (
  id int(11) NOT NULL auto_increment,
  to_email varchar(255) collate utf8_unicode_ci NOT NULL,
  to_name varchar(255) collate utf8_unicode_ci default NULL,
  from_email varchar(255) collate utf8_unicode_ci NOT NULL,
  from_name varchar(255) collate utf8_unicode_ci default NULL,
  reply_to_email varchar(255) collate utf8_unicode_ci default NULL,
  reply_to_name varchar(255) collate utf8_unicode_ci default NULL,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  html text collate utf8_unicode_ci NOT NULL,
  plain_text text collate utf8_unicode_ci NOT NULL,
  send_on datetime default NULL,
  created_on datetime NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS groups;
CREATE TABLE IF NOT EXISTS groups (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  parameters text collate utf8_unicode_ci COMMENT 'serialized array containing default user module/action rights',
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO groups VALUES (1, 'admin', NULL);


DROP TABLE IF EXISTS groups_rights_actions;
CREATE TABLE IF NOT EXISTS groups_rights_actions (
  id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL,
  module varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `action` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the action',
  `level` double NOT NULL default '1' COMMENT 'unix type levels 1, 3, 5 and 7',
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO groups_rights_actions VALUES (1, 1, 'dashboard', 'index', 7);
INSERT INTO groups_rights_actions VALUES (3, 1, 'users', 'index', 7);
INSERT INTO groups_rights_actions VALUES (4, 1, 'users', 'edit', 7);
INSERT INTO groups_rights_actions VALUES (5, 1, 'users', 'add', 7);
INSERT INTO groups_rights_actions VALUES (6, 1, 'users', 'delete', 7);
INSERT INTO groups_rights_actions VALUES (8, 1, 'users', 'groups', 7);
INSERT INTO groups_rights_actions VALUES (9, 1, 'users', 'edit_group', 7);
INSERT INTO groups_rights_actions VALUES (10, 1, 'users', 'add_group', 7);
INSERT INTO groups_rights_actions VALUES (16, 1, 'pages', 'index', 7);
INSERT INTO groups_rights_actions VALUES (17, 1, 'pages', 'add', 7);
INSERT INTO groups_rights_actions VALUES (18, 1, 'contentblocks', 'index', 7);
INSERT INTO groups_rights_actions VALUES (19, 1, 'contentblocks', 'add', 7);
INSERT INTO groups_rights_actions VALUES (20, 1, 'pages', 'edit', 7);
INSERT INTO groups_rights_actions VALUES (21, 1, 'contentblocks', 'edit', 7);
INSERT INTO groups_rights_actions VALUES (22, 1, 'settings', 'index', 7);
INSERT INTO groups_rights_actions VALUES (24, 1, 'blog', 'index', 7);
INSERT INTO groups_rights_actions VALUES (25, 1, 'contentblocks', 'delete', 7);
INSERT INTO groups_rights_actions VALUES (26, 1, 'blog', 'categories', 7);
INSERT INTO groups_rights_actions VALUES (27, 1, 'blog', 'comments', 7);
INSERT INTO groups_rights_actions VALUES (28, 1, 'blog', 'settings', 7);
INSERT INTO groups_rights_actions VALUES (29, 1, 'tags', 'autocomplete', 7);
INSERT INTO groups_rights_actions VALUES (30, 1, 'blog', 'add_post', 7);
INSERT INTO groups_rights_actions VALUES (31, 1, 'blog', 'edit_post', 7);
INSERT INTO groups_rights_actions VALUES (32, 1, 'pages', 'get_info', 7);
INSERT INTO groups_rights_actions VALUES (33, 1, 'pages', 'move', 7);
INSERT INTO groups_rights_actions VALUES (34, 1, 'pages', 'delete', 7);
INSERT INTO groups_rights_actions VALUES (35, 1, 'pages', 'save', 7);
INSERT INTO groups_rights_actions VALUES (36, 1, 'blog', 'mass_comment_action', 7);
INSERT INTO groups_rights_actions VALUES (38, 1, 'blog', 'add_category', 7);
INSERT INTO groups_rights_actions VALUES (39, 1, 'blog', 'edit_category', 7);
INSERT INTO groups_rights_actions VALUES (40, 1, 'blog', 'delete_category', 7);
INSERT INTO groups_rights_actions VALUES (41, 1, 'tags', 'index', 7);
INSERT INTO groups_rights_actions VALUES (42, 1, 'tags', 'edit', 7);
INSERT INTO groups_rights_actions VALUES (43, 1, 'blog', 'add', 7);
INSERT INTO groups_rights_actions VALUES (44, 1, 'blog', 'edit', 7);
INSERT INTO groups_rights_actions VALUES (45, 1, 'locale', 'index', 7);
INSERT INTO groups_rights_actions VALUES (46, 1, 'locale', 'edit', 7);
INSERT INTO groups_rights_actions VALUES (47, 1, 'pages', 'templates', 7);
INSERT INTO groups_rights_actions VALUES (48, 1, 'pages', 'add_template', 7);
INSERT INTO groups_rights_actions VALUES (49, 1, 'pages', 'edit_template', 7);
INSERT INTO groups_rights_actions VALUES (50, 1, 'locale', 'add', 7);
INSERT INTO groups_rights_actions VALUES (51, 1, 'blog', 'mass_post_action', 7);
INSERT INTO groups_rights_actions VALUES (52, 1, 'blog', 'delete', 7);
INSERT INTO groups_rights_actions VALUES (53, 1, 'locale', 'mass_action', 7);
INSERT INTO groups_rights_actions VALUES (55, 1, 'blog', 'add_category', 7);
INSERT INTO groups_rights_actions VALUES (56, 1, 'tags', 'mass_action', 7);
INSERT INTO groups_rights_actions VALUES (57, 1, 'example', 'layout', 7);


DROP TABLE IF EXISTS groups_rights_modules;
CREATE TABLE IF NOT EXISTS groups_rights_modules (
  id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL,
  module varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  PRIMARY KEY  (id),
  KEY idx_group_id (group_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO groups_rights_modules VALUES (1, 1, 'dashboard');
INSERT INTO groups_rights_modules VALUES (3, 1, 'users');
INSERT INTO groups_rights_modules VALUES (6, 1, 'pages');
INSERT INTO groups_rights_modules VALUES (7, 1, 'contentblocks');
INSERT INTO groups_rights_modules VALUES (8, 1, 'settings');
INSERT INTO groups_rights_modules VALUES (9, 1, 'blog');
INSERT INTO groups_rights_modules VALUES (10, 1, 'tags');
INSERT INTO groups_rights_modules VALUES (11, 1, 'locale');
INSERT INTO groups_rights_modules VALUES (12, 1, 'example');


DROP TABLE IF EXISTS locale;
CREATE TABLE IF NOT EXISTS locale (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  application varchar(255) collate utf8_unicode_ci NOT NULL,
  module varchar(255) collate utf8_unicode_ci NOT NULL,
  `type` enum('act','err','lbl','msg') collate utf8_unicode_ci NOT NULL default 'lbl',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci,
  edited_on datetime NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO locale VALUES (22, 1, 'nl', 'frontend', 'core', 'act', 'Detail', 'detail', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (23, 1, 'nl', 'frontend', 'core', 'lbl', 'CommentedOn', 'reageerde op', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (24, 1, 'nl', 'frontend', 'core', 'lbl', 'RecentComments', 'recente reacties', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (25, 1, 'nl', 'frontend', 'core', 'lbl', 'YouAreHere', 'je bent hier', '2010-03-10 10:27:50');
INSERT INTO locale VALUES (26, 1, 'nl', 'backend', 'core', 'lbl', 'Edit', 'bewerken', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (27, 1, 'nl', 'backend', 'core', 'lbl', 'Language', 'taal', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (28, 1, 'nl', 'backend', 'core', 'lbl', 'Application', 'applicatie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (29, 1, 'nl', 'backend', 'core', 'lbl', 'Module', 'module', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (30, 1, 'nl', 'backend', 'core', 'lbl', 'Type', 'type', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (31, 1, 'nl', 'backend', 'core', 'lbl', 'Name', 'naam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (32, 1, 'nl', 'backend', 'core', 'lbl', 'Value', 'waarde', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (33, 1, 'nl', 'backend', 'core', 'lbl', 'Settings', 'instellingen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (34, 1, 'nl', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key werd nog niet geconfigureerd.', '2010-02-24 10:35:48');
INSERT INTO locale VALUES (35, 1, 'nl', 'backend', 'core', 'lbl', 'Save', 'opslaan', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (36, 1, 'nl', 'backend', 'core', 'err', 'BlogRSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%1$s">Configureer</a>', '2010-03-12 12:57:37');
INSERT INTO locale VALUES (38, 1, 'nl', 'backend', 'core', 'lbl', 'AddLabel', 'label toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (39, 1, 'nl', 'backend', 'core', 'lbl', 'Active', 'actief', '2010-02-25 14:25:38');
INSERT INTO locale VALUES (40, 1, 'nl', 'backend', 'core', 'lbl', 'ActiveUsers', 'actieve gebruikers', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (41, 1, 'nl', 'backend', 'core', 'lbl', 'Add', 'toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (42, 1, 'nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (43, 1, 'nl', 'backend', 'users', 'lbl', 'Add', 'gebruiker toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (44, 1, 'nl', 'backend', 'core', 'lbl', 'AddCategory', 'categorie toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (45, 1, 'nl', 'backend', 'core', 'lbl', 'AddTemplate', 'template toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (46, 1, 'nl', 'backend', 'core', 'lbl', 'All', 'alle', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (47, 1, 'nl', 'backend', 'blog', 'lbl', 'AllowComments', 'reacties toestaan', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (48, 1, 'nl', 'backend', 'blog', 'lbl', 'AllPosts', 'alle posts', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (49, 1, 'nl', 'backend', 'core', 'lbl', 'Amount', 'aantal', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (50, 1, 'nl', 'backend', 'core', 'lbl', 'APIKey', 'API key', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (51, 1, 'nl', 'backend', 'core', 'lbl', 'APIKeys', 'API keys', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (52, 1, 'nl', 'backend', 'core', 'lbl', 'APIURL', 'API URL', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (53, 1, 'nl', 'backend', 'core', 'lbl', 'Archived', 'gearchiveerd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (54, 1, 'nl', 'backend', 'core', 'lbl', 'At', 'om', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (55, 1, 'nl', 'backend', 'core', 'lbl', 'Avatar', 'avatar', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (56, 1, 'nl', 'backend', 'core', 'lbl', 'Author', 'auteur', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (57, 1, 'nl', 'backend', 'core', 'lbl', 'Back', 'terug', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (58, 1, 'nl', 'backend', 'core', 'lbl', 'Blog', 'blog', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (59, 1, 'nl', 'backend', 'core', 'lbl', 'By', 'door', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (60, 1, 'nl', 'backend', 'core', 'lbl', 'Cancel', 'annuleer', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (61, 1, 'nl', 'backend', 'core', 'lbl', 'Category', 'categorie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (62, 1, 'nl', 'backend', 'core', 'lbl', 'Categories', 'categorieën', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (63, 1, 'nl', 'backend', 'core', 'lbl', 'CheckCommentsForSpam', 'filter reacties op spam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (64, 1, 'nl', 'backend', 'core', 'lbl', 'Comment', 'reactie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (65, 1, 'nl', 'backend', 'core', 'lbl', 'Comments', 'reacties', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (66, 1, 'nl', 'backend', 'core', 'lbl', 'Content', 'inhoud', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (67, 1, 'nl', 'backend', 'pages', 'lbl', 'Core', 'algemeen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (68, 1, 'nl', 'backend', 'core', 'lbl', 'CustomURL', 'aangepaste URL', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (69, 1, 'nl', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (70, 1, 'nl', 'backend', 'core', 'lbl', 'Date', 'datum', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (71, 1, 'nl', 'backend', 'core', 'lbl', 'Default', 'standaard', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (72, 1, 'nl', 'backend', 'core', 'lbl', 'Delete', 'verwijderen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (73, 1, 'nl', 'backend', 'core', 'lbl', 'DeleteTag', 'verwijder deze tag', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (74, 1, 'nl', 'backend', 'core', 'lbl', 'Description', 'beschrijving', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (75, 1, 'nl', 'backend', 'core', 'lbl', 'Developer', 'developer', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (76, 1, 'nl', 'backend', 'core', 'lbl', 'Domains', 'domeinen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (77, 1, 'nl', 'backend', 'core', 'lbl', 'Draft', 'kladversie', '2010-02-14 11:56:30');
INSERT INTO locale VALUES (78, 1, 'nl', 'backend', 'core', 'lbl', 'Dutch', 'nederlands', '2010-03-08 10:08:20');
INSERT INTO locale VALUES (79, 1, 'nl', 'backend', 'core', 'lbl', 'English', 'engels', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (80, 1, 'nl', 'backend', 'core', 'lbl', 'Editor', 'editor', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (81, 1, 'nl', 'backend', 'core', 'lbl', 'EditTemplate', 'template bewerken', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (82, 1, 'nl', 'backend', 'core', 'lbl', 'Email', 'e-mail', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (83, 1, 'nl', 'backend', 'core', 'lbl', 'EmailWebmaster', 'e-mail webmaster', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (84, 1, 'nl', 'backend', 'core', 'lbl', 'Execute', 'uitvoeren', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (85, 1, 'nl', 'backend', 'core', 'lbl', 'Extra', 'extra', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (86, 1, 'nl', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (87, 1, 'nl', 'backend', 'core', 'lbl', 'Footer', 'footer', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (88, 1, 'nl', 'backend', 'core', 'lbl', 'French', 'frans', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (89, 1, 'nl', 'backend', 'core', 'lbl', 'Hidden', 'verborgen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (90, 1, 'nl', 'backend', 'core', 'lbl', 'Home', 'home', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (91, 1, 'nl', 'backend', 'core', 'lbl', 'InterfaceLanguage', 'interface-taal', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (92, 1, 'nl', 'backend', 'core', 'lbl', 'Keywords', 'zoekwoorden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (93, 1, 'nl', 'backend', 'core', 'lbl', 'Languages', 'talen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (94, 1, 'nl', 'backend', 'core', 'lbl', 'LastEditedOn', 'laatst aangepast op', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (95, 1, 'nl', 'backend', 'core', 'lbl', 'LastSave', 'laatst bewaard', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (96, 1, 'nl', 'backend', 'core', 'lbl', 'Loading', 'laden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (97, 1, 'nl', 'backend', 'core', 'lbl', 'Login', 'login', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (98, 1, 'nl', 'backend', 'core', 'lbl', 'Logout', 'afmelden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (99, 1, 'nl', 'backend', 'core', 'lbl', 'MainNavigation', 'hoofdnavigatie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (100, 1, 'nl', 'backend', 'pages', 'lbl', 'Meta', 'meta-navigatie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (101, 1, 'nl', 'backend', 'core', 'lbl', 'MetaCustom', 'meta custom', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (102, 1, 'nl', 'backend', 'core', 'lbl', 'MetaDescription', 'meta-omschrijving', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (103, 1, 'nl', 'backend', 'core', 'lbl', 'MetaInformation', 'meta-informatie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (104, 1, 'nl', 'backend', 'core', 'lbl', 'MetaKeywords', 'sleutelwoorden pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (105, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToModeration', 'verplaats naar moderatie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (106, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToPublished', 'verplaats naar gepubliceerd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (107, 1, 'nl', 'backend', 'core', 'lbl', 'MoveToSpam', 'verplaats naar spam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (108, 1, 'nl', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigatie titel', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (109, 1, 'nl', 'backend', 'core', 'lbl', 'NewPassword', 'nieuw wachtwoord', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (110, 1, 'nl', 'backend', 'core', 'lbl', 'News', 'nieuws', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (111, 1, 'nl', 'backend', 'core', 'lbl', 'Next', 'volgende', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (112, 1, 'nl', 'backend', 'core', 'lbl', 'NextPage', 'volgende pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (113, 1, 'nl', 'backend', 'core', 'lbl', 'Nickname', 'nickname', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (114, 1, 'nl', 'backend', 'core', 'lbl', 'OK', 'ok', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (115, 1, 'nl', 'backend', 'core', 'lbl', 'Page', 'pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (116, 1, 'nl', 'backend', 'core', 'lbl', 'Pages', 'pagina''s', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (117, 1, 'nl', 'backend', 'core', 'lbl', 'Password', 'wachtwoord', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (118, 1, 'nl', 'backend', 'core', 'lbl', 'PageTitle', 'paginatitel', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (119, 1, 'nl', 'backend', 'core', 'lbl', 'Permissions', 'rechten', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (120, 1, 'nl', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (121, 1, 'nl', 'backend', 'blog', 'lbl', 'Posts', 'posts', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (122, 1, 'nl', 'backend', 'core', 'lbl', 'PostsInThisCategory', 'posts in deze categorie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (123, 1, 'nl', 'backend', 'core', 'lbl', 'Preview', 'preview', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (124, 1, 'nl', 'backend', 'core', 'lbl', 'Previous', 'vorige', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (125, 1, 'nl', 'backend', 'core', 'lbl', 'PreviousPage', 'vorige pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (126, 1, 'nl', 'backend', 'core', 'lbl', 'Publish', 'publiceer', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (127, 1, 'nl', 'backend', 'core', 'lbl', 'PublishOn', 'publiceer op', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (128, 1, 'nl', 'backend', 'core', 'lbl', 'Published', 'gepubliceerd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (129, 1, 'nl', 'backend', 'core', 'lbl', 'PublishedOn', 'gepubliceerd op', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (130, 1, 'nl', 'backend', 'core', 'lbl', 'PublishedComments', 'gepubliceerde reacties', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (131, 1, 'nl', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recent aangepast', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (132, 1, 'nl', 'backend', 'core', 'lbl', 'Referrers', 'referrers', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (133, 1, 'nl', 'backend', 'core', 'lbl', 'RepeatPassword', 'herhaal wachtwoord', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (134, 1, 'nl', 'backend', 'core', 'lbl', 'RequiredField', 'verplicht veld', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (135, 1, 'nl', 'backend', 'core', 'lbl', 'Revisions', 'versies', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (136, 1, 'nl', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (137, 1, 'nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina''s', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (138, 1, 'nl', 'backend', 'core', 'lbl', 'Scripts', 'scripts', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (139, 1, 'nl', 'backend', 'core', 'lbl', 'Send', 'verzenden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (140, 1, 'nl', 'backend', 'core', 'lbl', 'Security', 'beveiliging', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (141, 1, 'nl', 'backend', 'core', 'lbl', 'SEO', 'SEO', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (142, 1, 'nl', 'backend', 'core', 'lbl', 'SignIn', 'aanmelden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (143, 1, 'nl', 'backend', 'core', 'lbl', 'SignOut', 'afmelden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (144, 1, 'nl', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (145, 1, 'nl', 'backend', 'core', 'lbl', 'SortAscending', 'sorteerd oplopend', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (146, 1, 'nl', 'backend', 'core', 'lbl', 'SortDescending', 'sorteer aflopend', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (147, 1, 'nl', 'backend', 'core', 'lbl', 'SortedAscending', 'oplopend gesorteerd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (148, 1, 'nl', 'backend', 'core', 'lbl', 'SortedDescending', 'aflopend gesorteerd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (149, 1, 'nl', 'backend', 'core', 'lbl', 'Snippets', 'snippets', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (150, 1, 'nl', 'backend', 'core', 'lbl', 'Spam', 'spam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (151, 1, 'nl', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (152, 1, 'nl', 'backend', 'core', 'lbl', 'Status', 'status', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (153, 1, 'nl', 'backend', 'core', 'lbl', 'Submit', 'verzenden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (154, 1, 'nl', 'backend', 'core', 'lbl', 'Surname', 'achternaam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (155, 1, 'nl', 'backend', 'core', 'lbl', 'Tag', 'tag', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (156, 1, 'nl', 'backend', 'core', 'lbl', 'Tags', 'tags', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (157, 1, 'nl', 'backend', 'core', 'lbl', 'Template', 'template', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (158, 1, 'nl', 'backend', 'core', 'lbl', 'Time', 'tijd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (159, 1, 'nl', 'backend', 'core', 'lbl', 'Title', 'titel', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (160, 1, 'nl', 'backend', 'core', 'lbl', 'Titles', 'titels', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (161, 1, 'nl', 'backend', 'core', 'lbl', 'Update', 'wijzig', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (162, 1, 'nl', 'backend', 'core', 'lbl', 'URL', 'url', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (163, 1, 'nl', 'backend', 'core', 'lbl', 'Userguide', 'gebruikersgids', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (164, 1, 'nl', 'backend', 'core', 'lbl', 'Username', 'gebruikersnaam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (165, 1, 'nl', 'backend', 'core', 'lbl', 'User', 'gebruiker', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (166, 1, 'nl', 'backend', 'core', 'lbl', 'Users', 'gebruikers', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (167, 1, 'nl', 'backend', 'core', 'lbl', 'UseThisVersion', 'gebruik deze versie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (168, 1, 'nl', 'backend', 'core', 'lbl', 'Versions', 'versies', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (169, 1, 'nl', 'backend', 'core', 'lbl', 'Visible', 'zichtbaar', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (170, 1, 'nl', 'backend', 'core', 'lbl', 'WaitingForModeration', 'wachten op moderatie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (171, 1, 'nl', 'backend', 'core', 'lbl', 'Websites', 'websites', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (172, 1, 'nl', 'backend', 'core', 'lbl', 'WebsiteTitle', 'website titel', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (173, 1, 'nl', 'backend', 'core', 'lbl', 'WithSelected', 'met geselecteerde', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (174, 1, 'nl', 'backend', 'pages', 'msg', 'HelpAdd', 'Kies een pagina uit de boomstructuur om deze te bewerken of', '2010-02-23 15:37:46');
INSERT INTO locale VALUES (175, 1, 'nl', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activeer <code>rel="nofollow"</code>', '2010-02-02 09:49:53');
INSERT INTO locale VALUES (176, 1, 'nl', 'backend', 'core', 'msg', 'Added', 'item toegevoegd.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (177, 1, 'nl', 'backend', 'users', 'msg', 'Added', 'Gebruiker %1$s toegevoegd.', '2010-03-12 12:56:53');
INSERT INTO locale VALUES (178, 1, 'nl', 'backend', 'settings', 'msg', 'ApiKeysText', 'Toegangscodes voor gebruikte webservices', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (179, 1, 'nl', 'backend', 'core', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (180, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmDelete', 'Ben je zeker dat je dit item wil verwijderen?', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (181, 1, 'nl', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de pagina ''%1$s'' wil verwijderen?', '2010-03-12 12:59:15');
INSERT INTO locale VALUES (182, 1, 'nl', 'backend', 'users', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de gebruiker ''%1$s'' wil verwijderen?', '2010-03-12 12:59:25');
INSERT INTO locale VALUES (183, 1, 'nl', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Ben je zeker dat je de categorie ''%1$s'' wil verwijderen?', '2010-03-12 12:59:33');
INSERT INTO locale VALUES (184, 1, 'nl', 'backend', 'core', 'msg', 'Deleted', 'Het item werd verwijderd.', '2010-02-24 16:02:52');
INSERT INTO locale VALUES (185, 1, 'nl', 'backend', 'users', 'msg', 'Deleted', 'De gebruiker ''%1$s'' is verwijderd.', '2010-03-12 12:59:42');
INSERT INTO locale VALUES (186, 1, 'nl', 'backend', 'settings', 'msg', 'DomainsText', 'Vul de domeinen in waarop de website te bereiken is (1 domein per regel)', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (187, 1, 'nl', 'backend', 'core', 'msg', 'Edited', 'Wijzigingen opgeslagen.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (188, 1, 'nl', 'backend', 'users', 'msg', 'Edited', 'Wijzigingen voor gebruiker ''%1$s'' opgeslagen.', '2010-03-12 13:00:13');
INSERT INTO locale VALUES (189, 1, 'nl', 'backend', 'core', 'msg', 'ForgotPassword', 'Wachtwoord vergeten?', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (190, 1, 'nl', 'backend', 'core', 'msg', 'ForgotPasswordHelp', 'Vul hieronder je e-mail adres in om een nieuw wachtwoord toegestuurd te krijgen.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (191, 1, 'nl', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Stuur notificaties van het CMS naar dit e-mailadres.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (192, 1, 'nl', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'bijv. http://feeds.feedburner.com/netlog', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (193, 1, 'nl', 'backend', 'pages', 'msg', 'HelpPageTitle', 'De titel die in het venster van de browser staat (<code>&lt;title&gt;</code>).', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (194, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'Als de paginatitel te lang is om in het menu te passen, geef dan een verkorte titel in.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (195, 1, 'nl', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Zorgt ervoor dat deze pagina de interne PageRank niet beïnvloedt.', '2010-03-04 16:40:56');
INSERT INTO locale VALUES (196, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaCustom', 'Laat toe om extra, op maat gemaakte metatags toe te voegen.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (197, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaDescription', 'De pagina-omschrijving die wordt getoond in de resultaten van zoekmachines. Hou het kort en krachtig.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (198, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'De sleutelwoorden (keywords) die deze pagina omschrijven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (199, 1, 'nl', 'backend', 'core', 'msg', 'HelpMetaURL', 'Vervang de automatisch gegenereerde URL door een zelfgekozen URL.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (200, 1, 'nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (201, 1, 'nl', 'backend', 'core', 'msg', 'HelpRevisions', 'De laatst opgeslagen versies worden hier bijgehouden. ''Gebruik deze versie'' opent een vroegere versie. De huidige versie wordt pas overschreven als je opslaat.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (202, 1, 'nl', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Beschrijf bondig wat voor soort inhoud de RSS-feed zal bevatten', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (203, 1, 'nl', 'backend', 'core', 'lbl', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (204, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (205, 1, 'nl', 'backend', 'settings', 'msg', 'LanguagesText', 'Duid aan welke talen toegankelijk zijn voor bezoekers', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (206, 1, 'nl', 'backend', 'core', 'msg', 'LoginFormHelp', 'Vul uw gebruikersnaam en wachtwoord in om u aan te melden.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (207, 1, 'nl', 'backend', 'core', 'lbl', 'LoggedInAs', 'aangemeld als', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (208, 1, 'nl', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (209, 1, 'nl', 'backend', 'pages', 'msg', 'ModuleAttached', 'A module is attached. Go to <a href="{url}">{name}</a> to manage.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (210, 1, 'nl', 'backend', 'core', 'msg', 'NoDrafts', 'Er zijn geen kladversies.', '2010-02-14 11:56:51');
INSERT INTO locale VALUES (211, 1, 'nl', 'backend', 'core', 'msg', 'NoItems', 'Er zijn geen items aanwezig.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (212, 1, 'nl', 'backend', 'core', 'msg', 'NoItemsPublished', 'Er zijn geen items gepubliceerd.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (213, 1, 'nl', 'backend', 'core', 'msg', 'NoItemsScheduled', 'Er zijn geen items gepland.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (214, 1, 'nl', 'backend', 'core', 'msg', 'NoRevisions', 'Er zijn nog geen versies.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (215, 1, 'nl', 'backend', 'core', 'msg', 'NoTags', 'Je hebt nog geen tags ingegeven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (216, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedActionTitle', 'Verboden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (217, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedActionMessage', 'Deze actie is niet toegestaan.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (218, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedModuleTitle', 'Verboden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (219, 1, 'nl', 'backend', 'core', 'msg', 'NotAllowedModuleMessage', 'Deze module is niet toegestaan.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (220, 1, 'nl', 'backend', 'core', 'msg', 'ResetPasswordAndSignIn', 'Resetten en aanmelden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (221, 1, 'nl', 'backend', 'core', 'msg', 'ResetPasswordFormHelp', 'Vul je gewenste, nieuwe wachtwoord in.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (222, 1, 'nl', 'backend', 'core', 'msg', 'Saved', 'De wijzigingen werden opgeslagen.', '2010-02-25 15:09:18');
INSERT INTO locale VALUES (223, 1, 'nl', 'backend', 'settings', 'msg', 'ScriptsText', 'Plaats hier code die op elke pagina geladen moet worden. (bvb. Google Analytics).', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (224, 1, 'nl', 'backend', 'core', 'msg', 'SequenceChanged', 'De volgorde is aangepast.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (225, 1, 'nl', 'backend', 'core', 'msg', 'UsingARevision', 'Je gebruikt een oudere versie!', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (226, 1, 'nl', 'backend', 'core', 'msg', 'VisibleOnSite', 'Zichtbaar op de website?', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (227, 1, 'nl', 'backend', 'pages', 'msg', 'WidgetAttached', 'A widget is attached.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (228, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneDayAgo', '1 dag geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (229, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneHourAgo', '1 uur geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (230, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneMinuteAgo', '1 minuut geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (231, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneMonthAgo', '1 maand geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (232, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneSecondAgo', '1 second geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (233, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneWeekAgo', '1 week geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (234, 1, 'nl', 'backend', 'core', 'msg', 'TimeOneYearAgo', '1 jaar geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (235, 1, 'nl', 'backend', 'core', 'msg', 'TimeDaysAgo', '%1$s dagen geleden', '2010-03-12 13:01:45');
INSERT INTO locale VALUES (236, 1, 'nl', 'backend', 'core', 'msg', 'TimeMinutesAgo', '%1$s minuten geleden', '2010-03-12 13:03:01');
INSERT INTO locale VALUES (237, 1, 'nl', 'backend', 'core', 'msg', 'TimeHoursAgo', '%1$s uren geleden', '2010-03-12 13:02:28');
INSERT INTO locale VALUES (238, 1, 'nl', 'backend', 'core', 'msg', 'TimeMonthsAgo', '%1$s maanden geleden', '2010-03-12 13:03:50');
INSERT INTO locale VALUES (239, 1, 'nl', 'backend', 'core', 'msg', 'TimeSecondsAgo', '%1$s seconden geleden', '2010-03-12 13:04:12');
INSERT INTO locale VALUES (240, 1, 'nl', 'backend', 'core', 'msg', 'TimeWeeksAgo', '%1$s weken geleden', '2010-03-12 13:07:40');
INSERT INTO locale VALUES (241, 1, 'nl', 'backend', 'core', 'msg', 'TimeYearsAgo', '%1$s jaren geleden', '2010-03-12 13:08:21');
INSERT INTO locale VALUES (242, 1, 'nl', 'backend', 'core', 'err', 'BlogRSSTitle', 'Blog RSS titel is nog niet geconfigureerd. <a href="%1$s">Configureer</a>', '2010-03-12 12:58:47');
INSERT INTO locale VALUES (243, 1, 'nl', 'backend', 'core', 'err', 'ContentIsRequired', 'Gelieve inhoud in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (244, 1, 'nl', 'backend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (245, 1, 'nl', 'backend', 'core', 'err', 'EmailIsUnknown', 'Dit emailadres werd niet teruggevonden.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (246, 1, 'nl', 'backend', 'core', 'err', 'FieldIsRequired', 'Dit veld is verplicht.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (247, 1, 'nl', 'backend', 'core', 'err', 'GeneralFormError', 'Er ging iets mis. Kijk de gemarkeerde velden na.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (248, 1, 'nl', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key werd nog niet geconfigureerd.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (249, 1, 'nl', 'backend', 'core', 'err', 'InvalidAPIKey', 'Ongeldige API key.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (250, 1, 'nl', 'backend', 'core', 'err', 'InvalidDomain', 'Gelieve enkel domeinen in te vullen zonder http en www. vb netlash.com', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (251, 1, 'nl', 'backend', 'core', 'err', 'InvalidParameters', 'Ongeldige parameters.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (252, 1, 'nl', 'backend', 'core', 'err', 'InvalidURL', 'Ongeldige URL.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (253, 1, 'nl', 'backend', 'core', 'err', 'InvalidUsernamePasswordCombination', 'De combinatie van gebruikersnaam en wachtwoord is niet correct. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Bent u uw wachtwoord vergeten?</a>', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (254, 1, 'nl', 'backend', 'core', 'err', 'MinimumDimensions', 'Het gekozen bestand moet minimum %1$s<abbr title="pixels">px</abbr> breed en %1$s<abbr title="pixels">px</abbr> hoog zijn.', '2010-03-12 13:00:49');
INSERT INTO locale VALUES (255, 1, 'nl', 'backend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (256, 1, 'nl', 'backend', 'core', 'err', 'NonExisting', 'Dit item bestaat niet.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (257, 1, 'nl', 'backend', 'users', 'err', 'NonExisting', 'De gebruiker bestaat niet.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (258, 1, 'nl', 'backend', 'core', 'err', 'OnlyJPGAndGifAreAllowed', 'Enkel jpg, jpeg en gif zijn toegelaten.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (259, 1, 'nl', 'backend', 'core', 'err', 'PasswordIsRequired', 'Gelieve een wachtwoord in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (260, 1, 'nl', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Gelieve het gewenste wachtwoord te herhalen.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (261, 1, 'nl', 'backend', 'core', 'err', 'PasswordsDoNotMatch', 'De wachtwoorden zijn verschillend, probeer het opnieuw.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (262, 1, 'nl', 'backend', 'core', 'err', 'SomethingWentWrong', 'Er ging iets mis. Probeer later opnieuw.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (263, 1, 'nl', 'backend', 'core', 'err', 'SurnameIsRequired', 'Gelieve een achternaam in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (264, 1, 'nl', 'backend', 'core', 'err', 'TitleIsRequired', 'Gelieve een titel in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (265, 1, 'nl', 'backend', 'core', 'err', 'UsernameIsRequired', 'Gelieve een gebruikersnaam in te geven.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (266, 1, 'nl', 'backend', 'core', 'err', 'UsernameNotAllowed', 'Deze gebruikersnaam is niet toegestaan.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (267, 1, 'nl', 'backend', 'core', 'err', 'URLAlreadyExist', 'Deze URL bestaat al.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (268, 1, 'nl', 'frontend', 'core', 'msg', 'YouAreHere', 'Je bent hier', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (269, 1, 'nl', 'frontend', 'core', 'lbl', 'Required', 'verplicht', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (270, 1, 'nl', 'frontend', 'core', 'msg', 'WroteBy', 'geschreven door %1$s', '2010-03-12 13:08:36');
INSERT INTO locale VALUES (271, 1, 'nl', 'frontend', 'core', 'lbl', 'Comment', 'reactie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (272, 1, 'nl', 'frontend', 'core', 'lbl', 'Category', 'categorie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (273, 1, 'nl', 'frontend', 'core', 'act', 'Comments', 'reacties', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (274, 1, 'nl', 'frontend', 'core', 'lbl', 'Comments', 'reacties', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (275, 1, 'nl', 'frontend', 'core', 'act', 'Comment', 'reactie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (276, 1, 'nl', 'frontend', 'core', 'lbl', 'By', 'door', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (277, 1, 'nl', 'frontend', 'core', 'act', 'React', 'reageer', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (278, 1, 'nl', 'frontend', 'core', 'lbl', 'React', 'reageer', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (279, 1, 'nl', 'frontend', 'core', 'lbl', 'Name', 'naam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (280, 1, 'nl', 'frontend', 'core', 'lbl', 'Email', 'e-mailadres', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (281, 1, 'nl', 'frontend', 'core', 'lbl', 'Website', 'website', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (282, 1, 'nl', 'frontend', 'core', 'lbl', 'Message', 'bericht', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (283, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneDayAgo', '1 dag geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (284, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneHourAgo', '1 uur geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (285, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneMinuteAgo', '1 minuut geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (286, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneMonthAgo', '1 maand geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (287, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneSecondAgo', '1 seconde geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (288, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneWeekAgo', '1 week geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (289, 1, 'nl', 'frontend', 'core', 'msg', 'TimeOneYearAgo', '1 jaar geleden', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (290, 1, 'nl', 'frontend', 'core', 'msg', 'TimeDaysAgo', '%1$s dagen', '2010-03-12 13:02:04');
INSERT INTO locale VALUES (291, 1, 'nl', 'frontend', 'core', 'msg', 'TimeMinutesAgo', '%1$s minuten geleden', '2010-03-12 13:03:16');
INSERT INTO locale VALUES (292, 1, 'nl', 'frontend', 'core', 'msg', 'TimeHoursAgo', '%1$s uren geleden', '2010-03-12 13:02:45');
INSERT INTO locale VALUES (293, 1, 'nl', 'frontend', 'core', 'msg', 'TimeSecondsAgo', '%1$s seconden geleden', '2010-03-12 13:04:27');
INSERT INTO locale VALUES (294, 1, 'nl', 'frontend', 'core', 'msg', 'TimeWeeksAgo', '%1$s weken geleden', '2010-03-12 13:07:52');
INSERT INTO locale VALUES (295, 1, 'nl', 'frontend', 'core', 'msg', 'TimeYearAgo', '%1$s jaren geleden', '2010-03-12 13:08:07');
INSERT INTO locale VALUES (296, 1, 'nl', 'frontend', 'core', 'lbl', 'Tags', 'tags', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (297, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (298, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %1$s reacties', '2010-03-12 12:57:05');
INSERT INTO locale VALUES (299, 1, 'nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (300, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (301, 6, 'nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.', '2010-03-30 13:07:40');
INSERT INTO locale VALUES (302, 1, 'nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (303, 1, 'nl', 'frontend', 'core', 'msg', 'CommentedOn', 'reageerde op', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (306, 1, 'nl', 'frontend', 'core', 'lbl', 'PreviousPage', 'vorige pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (307, 1, 'nl', 'frontend', 'core', 'lbl', 'NextPage', 'volgende pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (308, 1, 'nl', 'frontend', 'core', 'lbl', 'GoToPage', 'ga naar pagina', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (309, 1, 'nl', 'backend', 'blog', 'msg', 'HeaderAdd', 'post toevoegen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (310, 1, 'nl', 'frontend', 'core', 'act', 'Category', 'categorie', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (312, 1, 'nl', 'frontend', 'core', 'act', 'Rss', 'rss', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (313, 1, 'nl', 'backend', 'blog', 'msg', 'HeaderEdit', 'post bewerken', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (314, 1, 'nl', 'frontend', 'core', 'lbl', 'In', 'in', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (315, 1, 'nl', 'frontend', 'core', 'lbl', 'Date', 'datum', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (316, 1, 'nl', 'frontend', 'core', 'lbl', 'Title', 'titel', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (317, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPassword', 'Wijzig je wachtwoord', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (320, 1, 'nl', 'backend', 'core', 'msg', 'EditWithItem', 'Bewerk ''%1$s''', '2010-03-12 13:00:27');
INSERT INTO locale VALUES (321, 1, 'nl', 'backend', 'core', 'msg', 'EditCategoryWithItem', 'Bewerk categorie ''%1$s''', '2010-03-12 12:59:58');
INSERT INTO locale VALUES (322, 1, 'nl', 'backend', 'core', 'lbl', 'General', 'algemeen', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (323, 1, 'nl', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (324, 1, 'nl', 'backend', 'core', 'lbl', 'Summary', 'samenvatting', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (325, 1, 'nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Als je een samenvatting ingeeft, dan zal deze verschijnen in de overzichtspagina''s. Indien niet dan zal het volledige bericht getoond worden.', '2010-03-04 13:53:42');
INSERT INTO locale VALUES (326, 1, 'nl', 'backend', 'core', 'lbl', 'Posts', 'artikels', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (327, 1, 'nl', 'backend', 'core', 'lbl', 'Modules', 'modules', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (328, 1, 'nl', 'backend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.', '2010-02-02 10:09:17');
INSERT INTO locale VALUES (331, 1, 'nl', 'backend', 'core', 'lbl', 'Credentials', 'login gegevens', '2010-02-02 12:54:52');
INSERT INTO locale VALUES (332, 1, 'nl', 'backend', 'core', 'msg', 'HelpStrongPassword', 'Sterke wachtwoorden bestaan uit een combinatie van hoofdletters, kleine letters, cijfers en speciale karakters.', '2010-02-08 17:01:43');
INSERT INTO locale VALUES (333, 1, 'nl', 'backend', 'core', 'lbl', 'None', 'geen', '2010-02-02 12:56:01');
INSERT INTO locale VALUES (334, 1, 'nl', 'backend', 'core', 'lbl', 'Weak', 'zwak', '2010-02-02 12:56:13');
INSERT INTO locale VALUES (335, 1, 'nl', 'backend', 'core', 'lbl', 'Strong', 'sterk', '2010-02-02 12:56:22');
INSERT INTO locale VALUES (336, 1, 'nl', 'backend', 'core', 'lbl', 'ConfirmPassword', 'bevestig wachtwoord', '2010-02-02 12:56:56');
INSERT INTO locale VALUES (337, 1, 'nl', 'backend', 'core', 'lbl', 'PersonalInformation', 'persoonlijke gegevens', '2010-02-02 12:57:15');
INSERT INTO locale VALUES (338, 1, 'nl', 'backend', 'users', 'msg', 'HelpNickname', 'Max. 20 characters. Your nickname will be shown throughout the CMS e.g. this name will show up when you are the author of an item.', '2010-02-02 12:57:46');
INSERT INTO locale VALUES (339, 1, 'nl', 'backend', 'users', 'msg', 'HelpAvatar', 'A square picture of your face works best.', '2010-02-02 12:59:04');
INSERT INTO locale VALUES (340, 1, 'nl', 'backend', 'core', 'lbl', 'InterfacePreferences', 'interface voorkeuren', '2010-02-02 12:59:45');
INSERT INTO locale VALUES (341, 6, 'nl', 'backend', 'core', 'lbl', 'AccountManagement', 'account beheer', '2010-03-30 13:07:32');
INSERT INTO locale VALUES (342, 1, 'nl', 'backend', 'users', 'msg', 'EnableUser', 'Geef deze account toegang tot het CMS.', '2010-02-02 13:09:54');
INSERT INTO locale VALUES (343, 1, 'nl', 'backend', 'core', 'lbl', 'Group', 'groep', '2010-02-02 13:10:04');
INSERT INTO locale VALUES (345, 1, 'nl', 'backend', 'core', 'msg', 'NL', 'Nederlands', '2010-03-08 10:11:12');
INSERT INTO locale VALUES (346, 1, 'nl', 'backend', 'core', 'lbl', 'FR', 'frans', '2010-02-02 16:37:37');
INSERT INTO locale VALUES (347, 1, 'nl', 'backend', 'core', 'msg', 'EN', 'Engels', '2010-03-08 10:11:25');
INSERT INTO locale VALUES (348, 1, 'nl', 'backend', 'core', 'msg', 'ResetSuccess', 'Je wachtwoord werd gewijzigd.', '2010-02-03 09:34:15');
INSERT INTO locale VALUES (349, 1, 'nl', 'backend', 'pages', 'err', 'CantBeMoved', 'Pagina kan niet verplaats worden.', '2010-02-03 09:40:44');
INSERT INTO locale VALUES (350, 1, 'nl', 'backend', 'pages', 'msg', 'PageIsMoved', 'De pagina werd verplaatst.', '2010-02-03 09:41:10');
INSERT INTO locale VALUES (351, 1, 'nl', 'backend', 'core', 'lbl', 'CategoryName', 'categorie naam', '2010-02-03 19:03:50');
INSERT INTO locale VALUES (352, 1, 'nl', 'backend', 'core', 'err', 'AddingCategoryFailed', 'Er ging iets mis', '2010-02-03 19:19:48');
INSERT INTO locale VALUES (353, 1, 'nl', 'backend', 'core', 'err', 'InvalidName', 'Ongeldige naam', '2010-02-03 20:25:00');
INSERT INTO locale VALUES (354, 1, 'nl', 'backend', 'locale', 'err', 'AlreadyExists', 'Deze vertaling bestaat reeds.', '2010-02-25 14:54:46');
INSERT INTO locale VALUES (355, 1, 'nl', 'backend', 'locale', 'err', 'InvalidValue', 'Ongeldige waarde.', '2010-02-03 20:27:31');
INSERT INTO locale VALUES (356, 1, 'nl', 'backend', 'core', 'msg', 'BlogLatestComments', 'Laatste blog reacties', '2010-02-04 12:26:27');
INSERT INTO locale VALUES (357, 1, 'nl', 'backend', 'core', 'lbl', 'AllComments', 'alle reacties', '2010-02-04 12:29:01');
INSERT INTO locale VALUES (358, 1, 'nl', 'backend', 'core', 'msg', 'NoComments', 'Er zijn nog geen reacties.', '2010-02-04 12:29:58');
INSERT INTO locale VALUES (359, 1, 'nl', 'backend', 'core', 'msg', 'BlogCommentsToModerate', '%1$s reactie(s) te modereren.', '2010-03-08 11:01:48');
INSERT INTO locale VALUES (360, 1, 'nl', 'backend', 'core', 'lbl', 'Moderate', 'modereer', '2010-02-04 15:28:54');
INSERT INTO locale VALUES (361, 1, 'nl', 'backend', 'core', 'lbl', 'Analyse', 'analyse', '2010-02-06 19:46:41');
INSERT INTO locale VALUES (362, 1, 'nl', 'backend', 'core', 'err', 'ForkAPIKeys', 'Fork API-keys nog niet geconfigureerd.', '2010-02-06 19:47:46');
INSERT INTO locale VALUES (363, 1, 'nl', 'backend', 'core', 'err', 'DebugModeIsActive', 'Debug-mode is nog actief.', '2010-02-06 19:48:29');
INSERT INTO locale VALUES (364, 1, 'nl', 'backend', 'core', 'err', 'RobotsFileIsNotOK', 'robots.txt is niet correct.', '2010-02-06 19:49:05');
INSERT INTO locale VALUES (365, 1, 'nl', 'backend', 'core', 'lbl', 'Moderation', 'moderatie', '2010-02-06 19:54:24');
INSERT INTO locale VALUES (366, 1, 'nl', 'backend', 'core', 'lbl', 'AllowModeration', 'moderatie toestaan', '2010-02-06 19:55:03');
INSERT INTO locale VALUES (367, 1, 'nl', 'backend', 'core', 'lbl', 'Filter', 'filter', '2010-02-08 19:49:17');
INSERT INTO locale VALUES (368, 1, 'nl', 'backend', 'pages', 'lbl', 'RecentComments', 'recente reacties', '2010-02-08 07:33:27');
INSERT INTO locale VALUES (369, 1, 'nl', 'backend', 'core', 'lbl', 'ContactForm', 'contactformulier', '2010-02-08 07:34:04');
INSERT INTO locale VALUES (370, 1, 'nl', 'backend', 'core', 'lbl', 'Contact', 'contact', '2010-02-08 07:34:57');
INSERT INTO locale VALUES (371, 1, 'nl', 'backend', 'core', 'lbl', 'Translations', 'vertalingen', '2010-02-08 09:13:36');
INSERT INTO locale VALUES (372, 1, 'nl', 'backend', 'core', 'msg', 'AddPage', 'Voeg een pagina toe', '2010-02-08 12:21:25');
INSERT INTO locale VALUES (373, 1, 'nl', 'backend', 'core', 'lbl', 'View', 'bekijk', '2010-02-08 12:49:39');
INSERT INTO locale VALUES (374, 1, 'nl', 'backend', 'blog', 'msg', 'ModerationMoved', 'Reactie verplaatst naar moderatie.', '2010-02-08 12:56:40');
INSERT INTO locale VALUES (375, 1, 'nl', 'backend', 'blog', 'msg', 'ModerationMovedMultiple', 'Reacties verplaatst naar moderatie.', '2010-02-08 12:57:26');
INSERT INTO locale VALUES (376, 1, 'nl', 'backend', 'blog', 'msg', 'PublishedMoved', 'Reactie gepubliceerd', '2010-02-08 13:05:31');
INSERT INTO locale VALUES (377, 1, 'nl', 'backend', 'blog', 'msg', 'PublishedMovedMultiple', 'Reacties gepubliceerd.', '2010-02-08 13:09:56');
INSERT INTO locale VALUES (378, 1, 'nl', 'backend', 'blog', 'msg', 'SpamMoved', 'Reactie gemarkeerd als spam.', '2010-02-08 13:07:37');
INSERT INTO locale VALUES (379, 1, 'nl', 'backend', 'blog', 'msg', 'SpamMovedMultiple', 'Reacties gemarkeerd als spam.', '2010-02-08 13:07:25');
INSERT INTO locale VALUES (380, 1, 'nl', 'backend', 'blog', 'msg', 'DeleteMoved', 'De reactie werd verwijderd.', '2010-02-08 13:08:20');
INSERT INTO locale VALUES (381, 1, 'nl', 'backend', 'blog', 'msg', 'DeleteMovedMultiple', 'De reacties werden verwijderd.', '2010-02-08 13:09:01');
INSERT INTO locale VALUES (382, 1, 'nl', 'backend', 'core', 'lbl', 'Overview', 'overzicht', '2010-02-08 13:48:33');
INSERT INTO locale VALUES (383, 1, 'nl', 'backend', 'core', 'lbl', 'Statistics', 'statistieken', '2010-02-08 14:31:38');
INSERT INTO locale VALUES (384, 1, 'nl', 'backend', 'core', 'lbl', 'Locale', 'Vertalingen', '2010-02-08 14:32:47');
INSERT INTO locale VALUES (389, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseALanguage', 'kies een taal', '2010-02-08 14:53:03');
INSERT INTO locale VALUES (390, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAnApplication', 'kies een applicatie', '2010-02-08 14:54:27');
INSERT INTO locale VALUES (391, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAModule', 'kies een module', '2010-02-08 14:55:18');
INSERT INTO locale VALUES (392, 1, 'nl', 'backend', 'core', 'lbl', 'ChooseAType', 'kies een type', '2010-02-08 14:56:29');
INSERT INTO locale VALUES (393, 1, 'nl', 'backend', 'core', 'lbl', 'Search', 'zoeken', '2010-02-08 14:57:34');
INSERT INTO locale VALUES (394, 1, 'nl', 'backend', 'locale', 'err', 'ModuleHasToBeCore', 'De module moet core zijn voor vertalingen in de frontend.', '2010-02-08 16:18:21');
INSERT INTO locale VALUES (395, 1, 'nl', 'backend', 'core', 'err', 'NoItemsSelected', 'Geen items geselecteerd.', '2010-02-08 17:05:24');
INSERT INTO locale VALUES (396, 1, 'nl', 'backend', 'core', 'msg', 'CategoryAdded', 'De categorie werd toegevoegd.', '2010-02-08 17:15:05');
INSERT INTO locale VALUES (397, 1, 'nl', 'backend', 'core', 'msg', 'UsingADraft', 'Je gebruikt een kladversie.', '2010-02-14 11:57:15');
INSERT INTO locale VALUES (398, 1, 'nl', 'backend', 'core', 'lbl', 'Drafts', 'kladversies', '2010-02-14 11:56:41');
INSERT INTO locale VALUES (399, 1, 'nl', 'backend', 'core', 'msg', 'HelpDrafts', 'Hier kan je jouw kladversie zien. Dit zijn tijdelijke versies.', '2010-02-14 11:57:37');
INSERT INTO locale VALUES (400, 1, 'nl', 'backend', 'core', 'msg', 'SavedAsDraft', '''%1$s'' als kladversie opgeslagen.', '2010-03-12 13:01:09');
INSERT INTO locale VALUES (401, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailSubject', 'Wijzig je wachtwoord', '2010-02-10 10:43:55');
INSERT INTO locale VALUES (402, 1, 'nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailContent', 'Reset je wachtwoord door op de link hieronder te klikken. Indien je niet hier niet om gevraagd hebt hoef je geen actie te ondernemen.', '2010-02-10 10:44:25');
INSERT INTO locale VALUES (403, 1, 'nl', 'backend', 'locale', 'lbl', 'AddTranslation', 'vertaling toevoegen', '2010-02-10 13:02:19');
INSERT INTO locale VALUES (404, 1, 'nl', 'backend', 'locale', 'lbl', 'EditTranslation', 'Vertaling bewerken', '2010-02-10 13:03:21');
INSERT INTO locale VALUES (405, 1, 'nl', 'backend', 'tags', 'lbl', 'EditTag', 'Tag bewerken', '2010-02-10 13:29:30');
INSERT INTO locale VALUES (406, 1, 'nl', 'backend', 'locale', 'msg', 'ValueHelpText', 'De vertaling zelf, bvb. "toevoegen".', '2010-03-08 10:14:25');
INSERT INTO locale VALUES (407, 1, 'nl', 'backend', 'locale', 'msg', 'NameHelpText', 'De Engelstalige referentie naar de vertaling, bvb. "add".', '2010-03-08 10:07:23');
INSERT INTO locale VALUES (408, 1, 'nl', 'backend', 'locale', 'msg', 'AddValueHelpText', 'De vertaling zelf, bvb. "toevoegen".', '2010-03-08 10:14:20');
INSERT INTO locale VALUES (409, 1, 'nl', 'backend', 'locale', 'msg', 'AddNameHelpText', 'De Engelstalige referentie naar de vertaling, bvb. "add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten. Bij voorkeur gebruik je camelCase.', '2010-03-08 10:07:42');
INSERT INTO locale VALUES (410, 1, 'nl', 'backend', 'core', 'lbl', 'UpdateFilter', 'Update filter', '2010-02-10 14:54:38');
INSERT INTO locale VALUES (411, 1, 'nl', 'backend', 'core', 'lbl', 'SaveAsDraft', 'kladversie opslaan', '2010-02-10 21:48:25');
INSERT INTO locale VALUES (412, 1, 'nl', 'frontend', 'core', 'msg', 'TagsNoItems', 'Er zijn nog geen tags gebruikt.', '2010-02-11 19:18:07');
INSERT INTO locale VALUES (413, 1, 'nl', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Reactie op: <a href="%1$s">%2$s</a>', '2010-03-12 12:58:59');
INSERT INTO locale VALUES (414, 1, 'nl', 'frontend', 'core', 'lbl', 'Send', 'verstuur', '2010-02-13 17:26:14');
INSERT INTO locale VALUES (415, 1, 'nl', 'frontend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.', '2010-02-13 17:40:49');
INSERT INTO locale VALUES (416, 1, 'nl', 'frontend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.', '2010-02-13 17:41:19');
INSERT INTO locale VALUES (417, 1, 'nl', 'frontend', 'core', 'err', 'MessageIsRequired', 'Gelieve een bericht in te geven.', '2010-02-13 17:42:11');
INSERT INTO locale VALUES (418, 1, 'nl', 'frontend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.', '2010-02-13 17:46:17');
INSERT INTO locale VALUES (419, 1, 'nl', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Er ging iets mis tijdens het verzenden, probeer later opnieuw.', '2010-02-13 17:52:23');
INSERT INTO locale VALUES (420, 1, 'nl', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Uw mail werd verzonden.', '2010-02-13 23:02:15');
INSERT INTO locale VALUES (421, 1, 'nl', 'frontend', 'core', 'msg', 'ContactSubject', 'Mail via contactformulier', '2010-02-13 23:01:51');
INSERT INTO locale VALUES (422, 1, 'nl', 'backend', 'core', 'lbl', 'EditedOn', 'laatst bewerkt', '2010-02-14 11:53:19');
INSERT INTO locale VALUES (423, 1, 'nl', 'backend', 'core', 'lbl', 'UseThisDraft', 'gebruik deze kladversie', '2010-02-14 11:55:39');
INSERT INTO locale VALUES (424, 1, 'nl', 'backend', 'core', 'lbl', 'MainContent', 'Hoofdinhoud', '2010-02-24 10:02:00');
INSERT INTO locale VALUES (425, 1, 'nl', 'backend', 'core', 'err', 'JavascriptNotEnabled', 'Om Fork CMS te gebruiken moet Javascript geactiveerd zijn in uw browser. Activeer javascript en vernieuw deze pagina.', '2010-02-24 10:22:46');
INSERT INTO locale VALUES (426, 1, 'nl', 'backend', 'core', 'err', 'BrowserNotSupported', '<p>you are using an outdated browser which is not supported by Fork CMS. Use one of the better alternatives</p> 				<ul> 					<li><a href="http://www.microsoft.com/windows/products/winfamily/ie/default.mspx">Internet Explorer *</a>: update to the latest version of the browser you are using now.</li> 					<li><a href="http://www.firefox.com/">Firefox</a>: a very solid browser with many add-on possibilities. The uncrowned fav of the Fork-team.</li> 					<li><a href="http://www.opera.com/">Opera:</a> fast and full-featured.</li> 				</ul>', '2010-03-08 10:30:29');
INSERT INTO locale VALUES (427, 1, 'nl', 'backend', 'core', 'lbl', 'BrowserNotSupported', 'Browser niet ondersteund', '2010-02-24 10:30:24');
INSERT INTO locale VALUES (428, 1, 'nl', 'backend', 'locale', 'msg', 'EditValueHelpText', 'De Engelstalige referentie naar de vertaling, bvb. "add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten. Bij voorkeur gebruik je camelCase.', '2010-03-08 10:07:34');
INSERT INTO locale VALUES (429, 1, 'nl', 'backend', 'core', 'lbl', 'NumbersOfBlocks', 'Aantal blokken', '2010-02-24 16:57:27');
INSERT INTO locale VALUES (430, 1, 'nl', 'backend', 'core', 'lbl', 'Label', 'Label', '2010-02-24 16:57:40');
INSERT INTO locale VALUES (431, 1, 'nl', 'backend', 'core', 'lbl', 'Path', 'pad', '2010-02-24 16:58:05');
INSERT INTO locale VALUES (432, 1, 'nl', 'backend', 'core', 'lbl', 'Layout', 'layout', '2010-02-24 16:58:23');
INSERT INTO locale VALUES (433, 1, 'nl', 'backend', 'core', 'msg', 'IsDefault', 'Standaard?', '2010-02-24 17:01:58');
INSERT INTO locale VALUES (434, 1, 'nl', 'backend', 'core', 'lbl', 'NumberOfBlocks', 'Aantal blokken', '2010-02-24 17:02:48');
INSERT INTO locale VALUES (435, 1, 'nl', 'backend', 'core', 'lbl', 'ContentBlocks', 'inhoudsblokken', '2010-02-25 07:33:58');
INSERT INTO locale VALUES (436, 1, 'nl', 'backend', 'blog', 'msg', 'Delete', 'Het artikel werd verwijderd.', '2010-03-08 10:31:31');
INSERT INTO locale VALUES (437, 1, 'nl', 'backend', 'blog', 'lbl', 'PublishedPosts', 'Gepubliceerde artikels', '2010-02-25 14:35:44');
INSERT INTO locale VALUES (438, 1, 'nl', 'backend', 'blog', 'lbl', 'EditCategory', 'Bewerk categorie', '2010-02-25 14:42:41');
INSERT INTO locale VALUES (439, 1, 'nl', 'backend', 'core', 'lbl', 'DebugMode', 'Debug mode', '2010-02-25 14:48:14');
INSERT INTO locale VALUES (440, 1, 'nl', 'backend', 'core', 'lbl', 'WorkingLanguage', 'Werktaal', '2010-02-25 14:49:51');
INSERT INTO locale VALUES (441, 1, 'nl', 'backend', 'users', 'msg', 'Edit', 'Gebruiker werd bewerkt.', '2010-03-10 14:35:32');
INSERT INTO locale VALUES (442, 1, 'nl', 'backend', 'blog', 'err', 'TextIsRequired', 'Dit veld is verplicht.', '2010-02-25 15:12:32');
INSERT INTO locale VALUES (443, 1, 'nl', 'backend', 'core', 'msg', 'FR', 'Frans', '2010-03-08 10:11:40');
INSERT INTO locale VALUES (444, 1, 'nl', 'backend', 'core', 'msg', 'DE', 'Duits', '2010-03-08 10:10:45');
INSERT INTO locale VALUES (445, 1, 'nl', 'backend', 'core', 'msg', 'ES', 'Spaans', '2010-03-08 10:11:00');
INSERT INTO locale VALUES (447, 1, 'nl', 'backend', 'tags', 'msg', 'Delete', 'De tag(s) werd(en) verwijderd.', '2010-03-09 10:25:54');
INSERT INTO locale VALUES (448, 1, 'nl', 'frontend', 'core', 'msg', 'BlogNoItems', 'Er zijn nog géén blogposts geschreven.', '2010-03-09 10:39:39');
INSERT INTO locale VALUES (449, 1, 'nl', 'backend', 'core', 'lbl', 'Templates', 'Templates', '2010-03-10 14:37:27');
INSERT INTO locale VALUES (450, 5, 'nl', 'backend', 'core', 'lbl', 'ChangePassword', 'Wijzig wachtwoord', '2010-03-10 14:50:04');
INSERT INTO locale VALUES (451, 5, 'nl', 'backend', 'users', 'err', 'ValuesDontMatch', 'De waarden komen niet overeen.', '2010-03-10 14:53:07');
INSERT INTO locale VALUES (452, 1, 'nl', 'backend', 'users', 'err', 'NicknameIsRequired', 'Gelieve een nickname in te geven.', '2010-03-10 15:10:08');
INSERT INTO locale VALUES (453, 1, 'nl', 'backend', 'core', 'msg', 'RedirectLanguagesText', 'Duid aan in welke talen mensen op basis van hun browser mogen terechtkomen.', '2010-03-15 10:43:41');
INSERT INTO locale VALUES (454, 1, 'nl', 'backend', 'core', 'lbl', 'DateFormat', 'datum formaat', '2010-03-15 15:00:06');
INSERT INTO locale VALUES (455, 1, 'nl', 'backend', 'core', 'lbl', 'TimeFormat', 'tijdsformaat', '2010-03-15 15:01:22');
INSERT INTO locale VALUES (456, 1, 'nl', 'backend', 'core', 'err', 'CookiesNotEnabled', 'Om Fork CMS te gebruiken moet cookies geactiveerd zijn in uw browser. Activeer cookies en vernieuw deze pagina.', '2010-03-30 10:35:23');
INSERT INTO locale VALUES (457, 1, 'nl', 'backend', 'core', 'lbl', 'ModuleSettings', 'Module-instellingen', '2010-03-30 11:43:25');
INSERT INTO locale VALUES (458, 1, 'nl', 'backend', 'core', 'lbl', 'GeneralSettings', 'algemene instellingen', '2010-03-30 11:45:55');

DROP TABLE IF EXISTS meta;
CREATE TABLE IF NOT EXISTS meta (
  id int(11) NOT NULL auto_increment,
  keywords varchar(255) collate utf8_unicode_ci NOT NULL,
  keywords_overwrite enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  description varchar(255) collate utf8_unicode_ci NOT NULL,
  description_overwrite enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  title varchar(255) collate utf8_unicode_ci NOT NULL,
  title_overwrite enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  url varchar(255) collate utf8_unicode_ci NOT NULL,
  url_overwrite enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  custom text character set utf8 COMMENT 'used for custom meta-information',
  PRIMARY KEY  (id),
  KEY idx_url (url)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Meta-information';
INSERT INTO meta VALUES (3, 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'Privacy &amp; Disclaimer', 'N', 'privacy-disclaimer', 'N', NULL);
INSERT INTO meta VALUES (2, 'Sitemap', 'N', 'Sitemap', 'N', 'Sitemap', 'N', 'sitemap', 'N', NULL);
INSERT INTO meta VALUES (4, 'Contact', 'N', 'Contact', 'N', 'Contact', 'Y', 'contact', 'N', NULL);
INSERT INTO meta VALUES (5, 'Tags', 'N', 'Tags', 'N', 'Tags', 'N', 'tags', 'N', NULL);
INSERT INTO meta VALUES (1, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);
INSERT INTO meta VALUES (6, '404', 'N', '404', 'N', '404', 'N', '404', 'N', NULL);
INSERT INTO meta VALUES (7, 'Home', 'N', 'Home', 'N', 'Home', 'N', 'home', 'N', NULL);


DROP TABLE IF EXISTS modules;
CREATE TABLE IF NOT EXISTS modules (
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'unique module name',
  description text collate utf8_unicode_ci,
  active enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO modules VALUES ('blog', NULL, 'Y');
INSERT INTO modules VALUES ('pages', 'Manage the pages for this website.', 'Y');
INSERT INTO modules VALUES ('settings', NULL, 'Y');
INSERT INTO modules VALUES ('contentblocks', NULL, 'Y');
INSERT INTO modules VALUES ('statistics', NULL, 'Y');
INSERT INTO modules VALUES ('tags', NULL, 'Y');
INSERT INTO modules VALUES ('news', NULL, 'Y');
INSERT INTO modules VALUES ('users', NULL, 'Y');
INSERT INTO modules VALUES ('locale', NULL, 'Y');
INSERT INTO modules VALUES ('sitemap', NULL, 'Y');
INSERT INTO modules VALUES ('contact', NULL, 'Y');
INSERT INTO modules VALUES ('example', 'Just an example module', 'Y');
INSERT INTO modules VALUES ('core', NULL, 'Y');


DROP TABLE IF EXISTS modules_settings;
CREATE TABLE IF NOT EXISTS modules_settings (
  module varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the module',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text collate utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (module,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO modules_settings VALUES ('contact', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('contact', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('core', 'active_languages', 'a:1:{i:0;s:2:"nl";}');
INSERT INTO modules_settings VALUES ('core', 'default_interface_language', 'N;');
INSERT INTO modules_settings VALUES ('core', 'default_template', 'i:1;');
INSERT INTO modules_settings VALUES ('core', 'email_nl', 'N;');
INSERT INTO modules_settings VALUES ('core', 'fork_api_private_key', 's:32:"29a7c80e4f84408946710f282154a195";');
INSERT INTO modules_settings VALUES ('core', 'fork_api_public_key', 's:32:"f0470a3a3746f2fadeedd83899b1a1fa";');
INSERT INTO modules_settings VALUES ('core', 'languages', 'a:1:{i:0;s:2:"nl";}');
INSERT INTO modules_settings VALUES ('core', 'redirect_languages', 'a:1:{i:0;s:2:"nl";}');
INSERT INTO modules_settings VALUES ('core', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('core', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('core', 'site_domains', 'a:1:{i:0;s:12:"forkng.local";}');
INSERT INTO modules_settings VALUES ('core', 'site_title_nl', 's:14:"Fork Installer";');
INSERT INTO modules_settings VALUES ('core', 'site_wide_html', 's:0:"";');
INSERT INTO modules_settings VALUES ('core', 'template_max_blocks', 'i:5;');
INSERT INTO modules_settings VALUES ('locale', 'languages', 'a:2:{i:0;s:2:"nl";i:1;s:2:"en";}');
INSERT INTO modules_settings VALUES ('locale', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('locale', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('pages', 'maximum_number_of_revisions', 'i:20;');
INSERT INTO modules_settings VALUES ('pages', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('pages', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('settings', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('settings', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('sitemap', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('sitemap', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('users', 'date_formats', 'a:4:{i:0;s:5:"j/n/Y";i:1;s:5:"d/m/Y";i:2;s:5:"j F Y";i:3;s:6:"F j, Y";}');
INSERT INTO modules_settings VALUES ('users', 'requires_akismet', 'b:0;');
INSERT INTO modules_settings VALUES ('users', 'requires_google_maps', 'b:0;');
INSERT INTO modules_settings VALUES ('users', 'time_formats', 'a:4:{i:0;s:3:"H:i";i:1;s:5:"H:i:s";i:2;s:5:"g:i a";i:3;s:5:"g:i A";}');


DROP TABLE IF EXISTS modules_tags;
CREATE TABLE IF NOT EXISTS modules_tags (
  module varchar(255) collate utf8_unicode_ci NOT NULL,
  tag_id int(11) NOT NULL,
  other_id int(11) NOT NULL,
  PRIMARY KEY  (module,tag_id,other_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS pages;
CREATE TABLE IF NOT EXISTS pages (
  id int(11) NOT NULL COMMENT 'the real page_id',
  revision_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL COMMENT 'which user has created this page?',
  parent_id int(11) NOT NULL default '0' COMMENT 'the parent_id for the page ',
  template_id int(11) NOT NULL default '0' COMMENT 'the template to use',
  meta_id int(11) NOT NULL COMMENT 'linked meta information',
  `language` varchar(5) collate utf8_unicode_ci NOT NULL COMMENT 'language of the content',
  `type` enum('home','root','page','meta','footer','external_alias','internal_alias') collate utf8_unicode_ci NOT NULL default 'root' COMMENT 'page, header, footer, ...',
  title varchar(255) collate utf8_unicode_ci NOT NULL,
  navigation_title varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'title that will be used in the navigation',
  navigation_title_overwrite enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'should we override the navigation title',
  hidden enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'is the page hidden?',
  `status` enum('active','archive','draft') collate utf8_unicode_ci NOT NULL default 'active' COMMENT 'is this the active, archive or draft version',
  publish_on datetime NOT NULL,
  `data` text collate utf8_unicode_ci COMMENT 'serialized array that may contain type specific parameters',
  created_on datetime NOT NULL,
  edited_on datetime NOT NULL,
  allow_move enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  allow_children enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  allow_edit enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  allow_delete enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y',
  no_follow enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  sequence int(11) NOT NULL,
  has_extra enum('Y','N') collate utf8_unicode_ci NOT NULL,
  extra_ids varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (revision_id),
  KEY idx_id_status_hidden_language (id,`status`,hidden,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO pages VALUES (3, 3, 1, 0, 1, 3, 'nl', 'footer', 'Privacy &amp; Disclaimer', 'Privacy &amp; Disclaimer', 'N', 'N', 'active', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-01-01 13:37:00', 'Y', 'N', 'Y', 'N', 'Y', 1, 'N', NULL);
INSERT INTO pages VALUES (1, 1, 1, 0, 1, 1, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'archive', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-01-01 13:37:00', 'N', 'Y', 'Y', 'N', 'N', 1, 'N', NULL);
INSERT INTO pages VALUES (4, 4, 1, 1, 1, 4, 'nl', 'page', 'Contact', 'Contact', 'N', 'N', 'active', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-01-01 13:37:00', 'Y', 'Y', 'Y', 'Y', 'N', 8, 'Y', '2');
INSERT INTO pages VALUES (2, 2, 1, 0, 1, 2, 'nl', 'footer', 'Sitemap', 'Sitemap', 'N', 'N', 'active', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-01-01 13:37:00', 'Y', 'N', 'Y', 'N', 'Y', 3, 'Y', '1');
INSERT INTO pages VALUES (5, 5, 1, 0, 1, 5, 'nl', 'root', 'Tags', 'Tags', 'N', 'N', 'active', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-01-01 13:37:00', 'Y', 'N', 'Y', 'N', 'N', 20, 'N', NULL);
INSERT INTO pages VALUES (404, 6, 1, 0, 1, 6, 'nl', 'root', '404', '404', 'N', 'N', 'active', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-01-01 13:37:00', 'N', 'N', 'Y', 'N', 'Y', 21, 'Y', '1');
INSERT INTO pages VALUES (1, 7, 1, 0, 1, 7, 'nl', 'page', 'Home', 'Home', 'N', 'N', 'active', '2010-01-01 13:37:00', NULL, '2010-01-01 13:37:00', '2010-04-09 12:43:10', 'N', 'Y', 'Y', 'N', 'N', 1, 'N', NULL);


DROP TABLE IF EXISTS pages_blocks;
CREATE TABLE IF NOT EXISTS pages_blocks (
  id int(11) NOT NULL COMMENT 'An ID that will be the same over the revisions.\n',
  revision_id int(11) NOT NULL COMMENT 'The ID of the page that contains this block.',
  extra_id int(11) default NULL COMMENT 'The linked extra.',
  html text collate utf8_unicode_ci COMMENT 'if this block is HTML this field should contain the real HTML.',
  `status` enum('active','archive','draft') collate utf8_unicode_ci NOT NULL default 'active',
  created_on datetime NOT NULL,
  edited_on datetime NOT NULL,
  KEY idx_rev_status (revision_id,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO pages_blocks VALUES (1, 1849, NULL, '', 'active', '2010-04-09 11:56:45', '2010-04-09 11:56:45');
INSERT INTO pages_blocks VALUES (2, 1849, NULL, '', 'active', '2010-04-09 11:56:45', '2010-04-09 11:56:45');
INSERT INTO pages_blocks VALUES (3, 1849, NULL, '', 'active', '2010-04-09 11:56:45', '2010-04-09 11:56:45');
INSERT INTO pages_blocks VALUES (4, 1850, NULL, '', 'active', '2010-04-09 11:57:08', '2010-04-09 11:57:08');
INSERT INTO pages_blocks VALUES (5, 1850, 2, NULL, 'active', '2010-04-09 11:57:08', '2010-04-09 11:57:08');
INSERT INTO pages_blocks VALUES (6, 1850, NULL, '', 'active', '2010-04-09 11:57:08', '2010-04-09 11:57:08');
INSERT INTO pages_blocks VALUES (7, 1851, NULL, '', 'active', '2010-04-09 11:57:53', '2010-04-09 11:57:53');
INSERT INTO pages_blocks VALUES (8, 1851, 1, NULL, 'active', '2010-04-09 11:57:53', '2010-04-09 11:57:53');
INSERT INTO pages_blocks VALUES (9, 1851, NULL, '', 'active', '2010-04-09 11:57:53', '2010-04-09 11:57:53');
INSERT INTO pages_blocks VALUES (10, 1852, NULL, '', 'active', '2010-04-09 11:58:27', '2010-04-09 11:58:27');
INSERT INTO pages_blocks VALUES (11, 1852, NULL, '', 'active', '2010-04-09 11:58:27', '2010-04-09 11:58:27');
INSERT INTO pages_blocks VALUES (12, 1852, NULL, '', 'active', '2010-04-09 11:58:27', '2010-04-09 11:58:27');
INSERT INTO pages_blocks VALUES (13, 1853, NULL, '', 'active', '2010-04-09 11:59:30', '2010-04-09 11:59:30');
INSERT INTO pages_blocks VALUES (14, 1853, NULL, '', 'active', '2010-04-09 11:59:30', '2010-04-09 11:59:30');
INSERT INTO pages_blocks VALUES (15, 1853, NULL, '', 'active', '2010-04-09 11:59:30', '2010-04-09 11:59:30');
INSERT INTO pages_blocks VALUES (16, 6, NULL, '<p>De gevraagde pagina is niet gevonden.</p>', 'active', '2010-04-09 12:29:25', '2010-04-09 12:29:25');
INSERT INTO pages_blocks VALUES (17, 6, 1, NULL, 'active', '2010-04-09 12:29:25', '2010-04-09 12:29:25');
INSERT INTO pages_blocks VALUES (18, 6, NULL, '', 'active', '2010-04-09 12:29:25', '2010-04-09 12:29:25');
INSERT INTO pages_blocks VALUES (19, 7, NULL, '', 'active', '2010-04-09 12:43:10', '2010-04-09 12:43:10');
INSERT INTO pages_blocks VALUES (20, 7, NULL, '', 'active', '2010-04-09 12:43:10', '2010-04-09 12:43:10');
INSERT INTO pages_blocks VALUES (21, 7, NULL, '', 'active', '2010-04-09 12:43:10', '2010-04-09 12:43:10');


DROP TABLE IF EXISTS pages_extras;
CREATE TABLE IF NOT EXISTS pages_extras (
  id int(11) NOT NULL auto_increment COMMENT 'Unique ID for the extra.',
  module varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The name of the module this extra belongs to.',
  `type` enum('homepage','block','widget') collate utf8_unicode_ci NOT NULL COMMENT 'The type of the block.',
  label varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The label for this extra. It will be used for displaying purposes.',
  `action` varchar(255) collate utf8_unicode_ci default NULL,
  `data` text collate utf8_unicode_ci COMMENT 'A serialized value with the optional parameters',
  hidden enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'Should the extra be shown in the backend?',
  sequence int(11) NOT NULL COMMENT 'The sequence in the backend.',
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible extras';
INSERT INTO pages_extras VALUES (1, 'sitemap', 'block', 'Sitemap', NULL, NULL, 'N', 1);
INSERT INTO pages_extras VALUES (2, 'contact', 'block', 'ContactForm', NULL, NULL, 'N', 2);
INSERT INTO pages_extras VALUES (3, 'tags', 'block', 'Tags', NULL, NULL, 'N', 3);


DROP TABLE IF EXISTS pages_groups;
CREATE TABLE IF NOT EXISTS pages_groups (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS pages_groups_pages;
CREATE TABLE IF NOT EXISTS pages_groups_pages (
  page_id int(11) NOT NULL,
  group_id int(11) NOT NULL,
  PRIMARY KEY  (page_id,group_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS pages_groups_profiles;
CREATE TABLE IF NOT EXISTS pages_groups_profiles (
  profile_id int(11) NOT NULL,
  group_id int(11) NOT NULL,
  PRIMARY KEY  (profile_id,group_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS pages_templates;
CREATE TABLE IF NOT EXISTS pages_templates (
  id int(11) NOT NULL auto_increment COMMENT 'Unique ID for the template.',
  label varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The label for the template, will be used for displaying purposes.',
  path varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Filename for the template.',
  num_blocks int(11) NOT NULL default '1' COMMENT 'The number of blocks used in the template.',
  active enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT 'Is this template active (as in: will it be used).',
  is_default enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'Is this the default template.',
  `data` text collate utf8_unicode_ci COMMENT 'A serialized array with data that is specific for this template (eg.: names for the blocks).',
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The possible templates';
INSERT INTO pages_templates VALUES (1, 'default', 'core/layout/templates/default.tpl', 3, 'Y', 'N', 'a:2:{s:6:"format";s:14:"[0,1],[2,none]";s:5:"names";a:3:{i:0;s:1:"A";i:1;s:1:"B";i:2;s:1:"C";}}');


DROP TABLE IF EXISTS tags;
CREATE TABLE IF NOT EXISTS tags (
  id int(11) NOT NULL auto_increment,
  `language` varchar(5) collate utf8_unicode_ci NOT NULL,
  tag varchar(255) collate utf8_unicode_ci NOT NULL,
  number int(11) NOT NULL,
  url varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS timezones;
CREATE TABLE IF NOT EXISTS timezones (
  id int(11) NOT NULL auto_increment,
  timezone varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO timezones VALUES (1, 'Africa/Abidjan');
INSERT INTO timezones VALUES (2, 'Africa/Accra');
INSERT INTO timezones VALUES (3, 'Africa/Addis_Ababa');
INSERT INTO timezones VALUES (4, 'Africa/Algiers');
INSERT INTO timezones VALUES (5, 'Africa/Asmara');
INSERT INTO timezones VALUES (6, 'Africa/Asmera');
INSERT INTO timezones VALUES (7, 'Africa/Bamako');
INSERT INTO timezones VALUES (8, 'Africa/Bangui');
INSERT INTO timezones VALUES (9, 'Africa/Banjul');
INSERT INTO timezones VALUES (10, 'Africa/Bissau');
INSERT INTO timezones VALUES (11, 'Africa/Blantyre');
INSERT INTO timezones VALUES (12, 'Africa/Brazzaville');
INSERT INTO timezones VALUES (13, 'Africa/Bujumbura');
INSERT INTO timezones VALUES (14, 'Africa/Cairo');
INSERT INTO timezones VALUES (15, 'Africa/Casablanca');
INSERT INTO timezones VALUES (16, 'Africa/Ceuta');
INSERT INTO timezones VALUES (17, 'Africa/Conakry');
INSERT INTO timezones VALUES (18, 'Africa/Dakar');
INSERT INTO timezones VALUES (19, 'Africa/Dar_es_Salaam');
INSERT INTO timezones VALUES (20, 'Africa/Djibouti');
INSERT INTO timezones VALUES (21, 'Africa/Douala');
INSERT INTO timezones VALUES (22, 'Africa/El_Aaiun');
INSERT INTO timezones VALUES (23, 'Africa/Freetown');
INSERT INTO timezones VALUES (24, 'Africa/Gaborone');
INSERT INTO timezones VALUES (25, 'Africa/Harare');
INSERT INTO timezones VALUES (26, 'Africa/Johannesburg');
INSERT INTO timezones VALUES (27, 'Africa/Kampala');
INSERT INTO timezones VALUES (28, 'Africa/Khartoum');
INSERT INTO timezones VALUES (29, 'Africa/Kigali');
INSERT INTO timezones VALUES (30, 'Africa/Kinshasa');
INSERT INTO timezones VALUES (31, 'Africa/Lagos');
INSERT INTO timezones VALUES (32, 'Africa/Libreville');
INSERT INTO timezones VALUES (33, 'Africa/Lome');
INSERT INTO timezones VALUES (34, 'Africa/Luanda');
INSERT INTO timezones VALUES (35, 'Africa/Lubumbashi');
INSERT INTO timezones VALUES (36, 'Africa/Lusaka');
INSERT INTO timezones VALUES (37, 'Africa/Malabo');
INSERT INTO timezones VALUES (38, 'Africa/Maputo');
INSERT INTO timezones VALUES (39, 'Africa/Maseru');
INSERT INTO timezones VALUES (40, 'Africa/Mbabane');
INSERT INTO timezones VALUES (41, 'Africa/Mogadishu');
INSERT INTO timezones VALUES (42, 'Africa/Monrovia');
INSERT INTO timezones VALUES (43, 'Africa/Nairobi');
INSERT INTO timezones VALUES (44, 'Africa/Ndjamena');
INSERT INTO timezones VALUES (45, 'Africa/Niamey');
INSERT INTO timezones VALUES (46, 'Africa/Nouakchott');
INSERT INTO timezones VALUES (47, 'Africa/Ouagadougou');
INSERT INTO timezones VALUES (48, 'Africa/Porto-Novo');
INSERT INTO timezones VALUES (49, 'Africa/Sao_Tome');
INSERT INTO timezones VALUES (50, 'Africa/Timbuktu');
INSERT INTO timezones VALUES (51, 'Africa/Tripoli');
INSERT INTO timezones VALUES (52, 'Africa/Tunis');
INSERT INTO timezones VALUES (53, 'Africa/Windhoek');
INSERT INTO timezones VALUES (54, 'America/Adak');
INSERT INTO timezones VALUES (55, 'America/Anchorage');
INSERT INTO timezones VALUES (56, 'America/Anguilla');
INSERT INTO timezones VALUES (57, 'America/Antigua');
INSERT INTO timezones VALUES (58, 'America/Araguaina');
INSERT INTO timezones VALUES (59, 'America/Argentina/Buenos_Aires');
INSERT INTO timezones VALUES (60, 'America/Argentina/Catamarca');
INSERT INTO timezones VALUES (61, 'America/Argentina/ComodRivadavia');
INSERT INTO timezones VALUES (62, 'America/Argentina/Cordoba');
INSERT INTO timezones VALUES (63, 'America/Argentina/Jujuy');
INSERT INTO timezones VALUES (64, 'America/Argentina/La_Rioja');
INSERT INTO timezones VALUES (65, 'America/Argentina/Mendoza');
INSERT INTO timezones VALUES (66, 'America/Argentina/Rio_Gallegos');
INSERT INTO timezones VALUES (67, 'America/Argentina/Salta');
INSERT INTO timezones VALUES (68, 'America/Argentina/San_Juan');
INSERT INTO timezones VALUES (69, 'America/Argentina/San_Luis');
INSERT INTO timezones VALUES (70, 'America/Argentina/Tucuman');
INSERT INTO timezones VALUES (71, 'America/Argentina/Ushuaia');
INSERT INTO timezones VALUES (72, 'America/Aruba');
INSERT INTO timezones VALUES (73, 'America/Asuncion');
INSERT INTO timezones VALUES (74, 'America/Atikokan');
INSERT INTO timezones VALUES (75, 'America/Atka');
INSERT INTO timezones VALUES (76, 'America/Bahia');
INSERT INTO timezones VALUES (77, 'America/Barbados');
INSERT INTO timezones VALUES (78, 'America/Belem');
INSERT INTO timezones VALUES (79, 'America/Belize');
INSERT INTO timezones VALUES (80, 'America/Blanc-Sablon');
INSERT INTO timezones VALUES (81, 'America/Boa_Vista');
INSERT INTO timezones VALUES (82, 'America/Bogota');
INSERT INTO timezones VALUES (83, 'America/Boise');
INSERT INTO timezones VALUES (84, 'America/Buenos_Aires');
INSERT INTO timezones VALUES (85, 'America/Cambridge_Bay');
INSERT INTO timezones VALUES (86, 'America/Campo_Grande');
INSERT INTO timezones VALUES (87, 'America/Cancun');
INSERT INTO timezones VALUES (88, 'America/Caracas');
INSERT INTO timezones VALUES (89, 'America/Catamarca');
INSERT INTO timezones VALUES (90, 'America/Cayenne');
INSERT INTO timezones VALUES (91, 'America/Cayman');
INSERT INTO timezones VALUES (92, 'America/Chicago');
INSERT INTO timezones VALUES (93, 'America/Chihuahua');
INSERT INTO timezones VALUES (94, 'America/Coral_Harbour');
INSERT INTO timezones VALUES (95, 'America/Cordoba');
INSERT INTO timezones VALUES (96, 'America/Costa_Rica');
INSERT INTO timezones VALUES (97, 'America/Cuiaba');
INSERT INTO timezones VALUES (98, 'America/Curacao');
INSERT INTO timezones VALUES (99, 'America/Danmarkshavn');
INSERT INTO timezones VALUES (100, 'America/Dawson');
INSERT INTO timezones VALUES (101, 'America/Dawson_Creek');
INSERT INTO timezones VALUES (102, 'America/Denver');
INSERT INTO timezones VALUES (103, 'America/Detroit');
INSERT INTO timezones VALUES (104, 'America/Dominica');
INSERT INTO timezones VALUES (105, 'America/Edmonton');
INSERT INTO timezones VALUES (106, 'America/Eirunepe');
INSERT INTO timezones VALUES (107, 'America/El_Salvador');
INSERT INTO timezones VALUES (108, 'America/Ensenada');
INSERT INTO timezones VALUES (109, 'America/Fort_Wayne');
INSERT INTO timezones VALUES (110, 'America/Fortaleza');
INSERT INTO timezones VALUES (111, 'America/Glace_Bay');
INSERT INTO timezones VALUES (112, 'America/Godthab');
INSERT INTO timezones VALUES (113, 'America/Goose_Bay');
INSERT INTO timezones VALUES (114, 'America/Grand_Turk');
INSERT INTO timezones VALUES (115, 'America/Grenada');
INSERT INTO timezones VALUES (116, 'America/Guadeloupe');
INSERT INTO timezones VALUES (117, 'America/Guatemala');
INSERT INTO timezones VALUES (118, 'America/Guayaquil');
INSERT INTO timezones VALUES (119, 'America/Guyana');
INSERT INTO timezones VALUES (120, 'America/Halifax');
INSERT INTO timezones VALUES (121, 'America/Havana');
INSERT INTO timezones VALUES (122, 'America/Hermosillo');
INSERT INTO timezones VALUES (123, 'America/Indiana/Indianapolis');
INSERT INTO timezones VALUES (124, 'America/Indiana/Knox');
INSERT INTO timezones VALUES (125, 'America/Indiana/Marengo');
INSERT INTO timezones VALUES (126, 'America/Indiana/Petersburg');
INSERT INTO timezones VALUES (127, 'America/Indiana/Tell_City');
INSERT INTO timezones VALUES (128, 'America/Indiana/Vevay');
INSERT INTO timezones VALUES (129, 'America/Indiana/Vincennes');
INSERT INTO timezones VALUES (130, 'America/Indiana/Winamac');
INSERT INTO timezones VALUES (131, 'America/Indianapolis');
INSERT INTO timezones VALUES (132, 'America/Inuvik');
INSERT INTO timezones VALUES (133, 'America/Iqaluit');
INSERT INTO timezones VALUES (134, 'America/Jamaica');
INSERT INTO timezones VALUES (135, 'America/Jujuy');
INSERT INTO timezones VALUES (136, 'America/Juneau');
INSERT INTO timezones VALUES (137, 'America/Kentucky/Louisville');
INSERT INTO timezones VALUES (138, 'America/Kentucky/Monticello');
INSERT INTO timezones VALUES (139, 'America/Knox_IN');
INSERT INTO timezones VALUES (140, 'America/La_Paz');
INSERT INTO timezones VALUES (141, 'America/Lima');
INSERT INTO timezones VALUES (142, 'America/Los_Angeles');
INSERT INTO timezones VALUES (143, 'America/Louisville');
INSERT INTO timezones VALUES (144, 'America/Maceio');
INSERT INTO timezones VALUES (145, 'America/Managua');
INSERT INTO timezones VALUES (146, 'America/Manaus');
INSERT INTO timezones VALUES (147, 'America/Marigot');
INSERT INTO timezones VALUES (148, 'America/Martinique');
INSERT INTO timezones VALUES (149, 'America/Matamoros');
INSERT INTO timezones VALUES (150, 'America/Mazatlan');
INSERT INTO timezones VALUES (151, 'America/Mendoza');
INSERT INTO timezones VALUES (152, 'America/Menominee');
INSERT INTO timezones VALUES (153, 'America/Merida');
INSERT INTO timezones VALUES (154, 'America/Mexico_City');
INSERT INTO timezones VALUES (155, 'America/Miquelon');
INSERT INTO timezones VALUES (156, 'America/Moncton');
INSERT INTO timezones VALUES (157, 'America/Monterrey');
INSERT INTO timezones VALUES (158, 'America/Montevideo');
INSERT INTO timezones VALUES (159, 'America/Montreal');
INSERT INTO timezones VALUES (160, 'America/Montserrat');
INSERT INTO timezones VALUES (161, 'America/Nassau');
INSERT INTO timezones VALUES (162, 'America/New_York');
INSERT INTO timezones VALUES (163, 'America/Nipigon');
INSERT INTO timezones VALUES (164, 'America/Nome');
INSERT INTO timezones VALUES (165, 'America/Noronha');
INSERT INTO timezones VALUES (166, 'America/North_Dakota/Center');
INSERT INTO timezones VALUES (167, 'America/North_Dakota/New_Salem');
INSERT INTO timezones VALUES (168, 'America/Ojinaga');
INSERT INTO timezones VALUES (169, 'America/Panama');
INSERT INTO timezones VALUES (170, 'America/Pangnirtung');
INSERT INTO timezones VALUES (171, 'America/Paramaribo');
INSERT INTO timezones VALUES (172, 'America/Phoenix');
INSERT INTO timezones VALUES (173, 'America/Port-au-Prince');
INSERT INTO timezones VALUES (174, 'America/Port_of_Spain');
INSERT INTO timezones VALUES (175, 'America/Porto_Acre');
INSERT INTO timezones VALUES (176, 'America/Porto_Velho');
INSERT INTO timezones VALUES (177, 'America/Puerto_Rico');
INSERT INTO timezones VALUES (178, 'America/Rainy_River');
INSERT INTO timezones VALUES (179, 'America/Rankin_Inlet');
INSERT INTO timezones VALUES (180, 'America/Recife');
INSERT INTO timezones VALUES (181, 'America/Regina');
INSERT INTO timezones VALUES (182, 'America/Resolute');
INSERT INTO timezones VALUES (183, 'America/Rio_Branco');
INSERT INTO timezones VALUES (184, 'America/Rosario');
INSERT INTO timezones VALUES (185, 'America/Santa_Isabel');
INSERT INTO timezones VALUES (186, 'America/Santarem');
INSERT INTO timezones VALUES (187, 'America/Santiago');
INSERT INTO timezones VALUES (188, 'America/Santo_Domingo');
INSERT INTO timezones VALUES (189, 'America/Sao_Paulo');
INSERT INTO timezones VALUES (190, 'America/Scoresbysund');
INSERT INTO timezones VALUES (191, 'America/Shiprock');
INSERT INTO timezones VALUES (192, 'America/St_Barthelemy');
INSERT INTO timezones VALUES (193, 'America/St_Johns');
INSERT INTO timezones VALUES (194, 'America/St_Kitts');
INSERT INTO timezones VALUES (195, 'America/St_Lucia');
INSERT INTO timezones VALUES (196, 'America/St_Thomas');
INSERT INTO timezones VALUES (197, 'America/St_Vincent');
INSERT INTO timezones VALUES (198, 'America/Swift_Current');
INSERT INTO timezones VALUES (199, 'America/Tegucigalpa');
INSERT INTO timezones VALUES (200, 'America/Thule');
INSERT INTO timezones VALUES (201, 'America/Thunder_Bay');
INSERT INTO timezones VALUES (202, 'America/Tijuana');
INSERT INTO timezones VALUES (203, 'America/Toronto');
INSERT INTO timezones VALUES (204, 'America/Tortola');
INSERT INTO timezones VALUES (205, 'America/Vancouver');
INSERT INTO timezones VALUES (206, 'America/Virgin');
INSERT INTO timezones VALUES (207, 'America/Whitehorse');
INSERT INTO timezones VALUES (208, 'America/Winnipeg');
INSERT INTO timezones VALUES (209, 'America/Yakutat');
INSERT INTO timezones VALUES (210, 'America/Yellowknife');
INSERT INTO timezones VALUES (211, 'Antarctica/Casey');
INSERT INTO timezones VALUES (212, 'Antarctica/Davis');
INSERT INTO timezones VALUES (213, 'Antarctica/DumontDUrville');
INSERT INTO timezones VALUES (214, 'Antarctica/Mawson');
INSERT INTO timezones VALUES (215, 'Antarctica/McMurdo');
INSERT INTO timezones VALUES (216, 'Antarctica/Palmer');
INSERT INTO timezones VALUES (217, 'Antarctica/Rothera');
INSERT INTO timezones VALUES (218, 'Antarctica/South_Pole');
INSERT INTO timezones VALUES (219, 'Antarctica/Syowa');
INSERT INTO timezones VALUES (220, 'Antarctica/Vostok');
INSERT INTO timezones VALUES (221, 'Arctic/Longyearbyen');
INSERT INTO timezones VALUES (222, 'Asia/Aden');
INSERT INTO timezones VALUES (223, 'Asia/Almaty');
INSERT INTO timezones VALUES (224, 'Asia/Amman');
INSERT INTO timezones VALUES (225, 'Asia/Anadyr');
INSERT INTO timezones VALUES (226, 'Asia/Aqtau');
INSERT INTO timezones VALUES (227, 'Asia/Aqtobe');
INSERT INTO timezones VALUES (228, 'Asia/Ashgabat');
INSERT INTO timezones VALUES (229, 'Asia/Ashkhabad');
INSERT INTO timezones VALUES (230, 'Asia/Baghdad');
INSERT INTO timezones VALUES (231, 'Asia/Bahrain');
INSERT INTO timezones VALUES (232, 'Asia/Baku');
INSERT INTO timezones VALUES (233, 'Asia/Bangkok');
INSERT INTO timezones VALUES (234, 'Asia/Beirut');
INSERT INTO timezones VALUES (235, 'Asia/Bishkek');
INSERT INTO timezones VALUES (236, 'Asia/Brunei');
INSERT INTO timezones VALUES (237, 'Asia/Calcutta');
INSERT INTO timezones VALUES (238, 'Asia/Choibalsan');
INSERT INTO timezones VALUES (239, 'Asia/Chongqing');
INSERT INTO timezones VALUES (240, 'Asia/Chungking');
INSERT INTO timezones VALUES (241, 'Asia/Colombo');
INSERT INTO timezones VALUES (242, 'Asia/Dacca');
INSERT INTO timezones VALUES (243, 'Asia/Damascus');
INSERT INTO timezones VALUES (244, 'Asia/Dhaka');
INSERT INTO timezones VALUES (245, 'Asia/Dili');
INSERT INTO timezones VALUES (246, 'Asia/Dubai');
INSERT INTO timezones VALUES (247, 'Asia/Dushanbe');
INSERT INTO timezones VALUES (248, 'Asia/Gaza');
INSERT INTO timezones VALUES (249, 'Asia/Harbin');
INSERT INTO timezones VALUES (250, 'Asia/Ho_Chi_Minh');
INSERT INTO timezones VALUES (251, 'Asia/Hong_Kong');
INSERT INTO timezones VALUES (252, 'Asia/Hovd');
INSERT INTO timezones VALUES (253, 'Asia/Irkutsk');
INSERT INTO timezones VALUES (254, 'Asia/Istanbul');
INSERT INTO timezones VALUES (255, 'Asia/Jakarta');
INSERT INTO timezones VALUES (256, 'Asia/Jayapura');
INSERT INTO timezones VALUES (257, 'Asia/Jerusalem');
INSERT INTO timezones VALUES (258, 'Asia/Kabul');
INSERT INTO timezones VALUES (259, 'Asia/Kamchatka');
INSERT INTO timezones VALUES (260, 'Asia/Karachi');
INSERT INTO timezones VALUES (261, 'Asia/Kashgar');
INSERT INTO timezones VALUES (262, 'Asia/Kathmandu');
INSERT INTO timezones VALUES (263, 'Asia/Katmandu');
INSERT INTO timezones VALUES (264, 'Asia/Kolkata');
INSERT INTO timezones VALUES (265, 'Asia/Krasnoyarsk');
INSERT INTO timezones VALUES (266, 'Asia/Kuala_Lumpur');
INSERT INTO timezones VALUES (267, 'Asia/Kuching');
INSERT INTO timezones VALUES (268, 'Asia/Kuwait');
INSERT INTO timezones VALUES (269, 'Asia/Macao');
INSERT INTO timezones VALUES (270, 'Asia/Macau');
INSERT INTO timezones VALUES (271, 'Asia/Magadan');
INSERT INTO timezones VALUES (272, 'Asia/Makassar');
INSERT INTO timezones VALUES (273, 'Asia/Manila');
INSERT INTO timezones VALUES (274, 'Asia/Muscat');
INSERT INTO timezones VALUES (275, 'Asia/Nicosia');
INSERT INTO timezones VALUES (276, 'Asia/Novokuznetsk');
INSERT INTO timezones VALUES (277, 'Asia/Novosibirsk');
INSERT INTO timezones VALUES (278, 'Asia/Omsk');
INSERT INTO timezones VALUES (279, 'Asia/Oral');
INSERT INTO timezones VALUES (280, 'Asia/Phnom_Penh');
INSERT INTO timezones VALUES (281, 'Asia/Pontianak');
INSERT INTO timezones VALUES (282, 'Asia/Pyongyang');
INSERT INTO timezones VALUES (283, 'Asia/Qatar');
INSERT INTO timezones VALUES (284, 'Asia/Qyzylorda');
INSERT INTO timezones VALUES (285, 'Asia/Rangoon');
INSERT INTO timezones VALUES (286, 'Asia/Riyadh');
INSERT INTO timezones VALUES (287, 'Asia/Saigon');
INSERT INTO timezones VALUES (288, 'Asia/Sakhalin');
INSERT INTO timezones VALUES (289, 'Asia/Samarkand');
INSERT INTO timezones VALUES (290, 'Asia/Seoul');
INSERT INTO timezones VALUES (291, 'Asia/Shanghai');
INSERT INTO timezones VALUES (292, 'Asia/Singapore');
INSERT INTO timezones VALUES (293, 'Asia/Taipei');
INSERT INTO timezones VALUES (294, 'Asia/Tashkent');
INSERT INTO timezones VALUES (295, 'Asia/Tbilisi');
INSERT INTO timezones VALUES (296, 'Asia/Tehran');
INSERT INTO timezones VALUES (297, 'Asia/Tel_Aviv');
INSERT INTO timezones VALUES (298, 'Asia/Thimbu');
INSERT INTO timezones VALUES (299, 'Asia/Thimphu');
INSERT INTO timezones VALUES (300, 'Asia/Tokyo');
INSERT INTO timezones VALUES (301, 'Asia/Ujung_Pandang');
INSERT INTO timezones VALUES (302, 'Asia/Ulaanbaatar');
INSERT INTO timezones VALUES (303, 'Asia/Ulan_Bator');
INSERT INTO timezones VALUES (304, 'Asia/Urumqi');
INSERT INTO timezones VALUES (305, 'Asia/Vientiane');
INSERT INTO timezones VALUES (306, 'Asia/Vladivostok');
INSERT INTO timezones VALUES (307, 'Asia/Yakutsk');
INSERT INTO timezones VALUES (308, 'Asia/Yekaterinburg');
INSERT INTO timezones VALUES (309, 'Asia/Yerevan');
INSERT INTO timezones VALUES (310, 'Atlantic/Azores');
INSERT INTO timezones VALUES (311, 'Atlantic/Bermuda');
INSERT INTO timezones VALUES (312, 'Atlantic/Canary');
INSERT INTO timezones VALUES (313, 'Atlantic/Cape_Verde');
INSERT INTO timezones VALUES (314, 'Atlantic/Faeroe');
INSERT INTO timezones VALUES (315, 'Atlantic/Faroe');
INSERT INTO timezones VALUES (316, 'Atlantic/Jan_Mayen');
INSERT INTO timezones VALUES (317, 'Atlantic/Madeira');
INSERT INTO timezones VALUES (318, 'Atlantic/Reykjavik');
INSERT INTO timezones VALUES (319, 'Atlantic/South_Georgia');
INSERT INTO timezones VALUES (320, 'Atlantic/St_Helena');
INSERT INTO timezones VALUES (321, 'Atlantic/Stanley');
INSERT INTO timezones VALUES (322, 'Australia/ACT');
INSERT INTO timezones VALUES (323, 'Australia/Adelaide');
INSERT INTO timezones VALUES (324, 'Australia/Brisbane');
INSERT INTO timezones VALUES (325, 'Australia/Broken_Hill');
INSERT INTO timezones VALUES (326, 'Australia/Canberra');
INSERT INTO timezones VALUES (327, 'Australia/Currie');
INSERT INTO timezones VALUES (328, 'Australia/Darwin');
INSERT INTO timezones VALUES (329, 'Australia/Eucla');
INSERT INTO timezones VALUES (330, 'Australia/Hobart');
INSERT INTO timezones VALUES (331, 'Australia/LHI');
INSERT INTO timezones VALUES (332, 'Australia/Lindeman');
INSERT INTO timezones VALUES (333, 'Australia/Lord_Howe');
INSERT INTO timezones VALUES (334, 'Australia/Melbourne');
INSERT INTO timezones VALUES (335, 'Australia/North');
INSERT INTO timezones VALUES (336, 'Australia/NSW');
INSERT INTO timezones VALUES (337, 'Australia/Perth');
INSERT INTO timezones VALUES (338, 'Australia/Queensland');
INSERT INTO timezones VALUES (339, 'Australia/South');
INSERT INTO timezones VALUES (340, 'Australia/Sydney');
INSERT INTO timezones VALUES (341, 'Australia/Tasmania');
INSERT INTO timezones VALUES (342, 'Australia/Victoria');
INSERT INTO timezones VALUES (343, 'Australia/West');
INSERT INTO timezones VALUES (344, 'Australia/Yancowinna');
INSERT INTO timezones VALUES (345, 'Europe/Amsterdam');
INSERT INTO timezones VALUES (346, 'Europe/Andorra');
INSERT INTO timezones VALUES (347, 'Europe/Athens');
INSERT INTO timezones VALUES (348, 'Europe/Belfast');
INSERT INTO timezones VALUES (349, 'Europe/Belgrade');
INSERT INTO timezones VALUES (350, 'Europe/Berlin');
INSERT INTO timezones VALUES (351, 'Europe/Bratislava');
INSERT INTO timezones VALUES (352, 'Europe/Brussels');
INSERT INTO timezones VALUES (353, 'Europe/Bucharest');
INSERT INTO timezones VALUES (354, 'Europe/Budapest');
INSERT INTO timezones VALUES (355, 'Europe/Chisinau');
INSERT INTO timezones VALUES (356, 'Europe/Copenhagen');
INSERT INTO timezones VALUES (357, 'Europe/Dublin');
INSERT INTO timezones VALUES (358, 'Europe/Gibraltar');
INSERT INTO timezones VALUES (359, 'Europe/Guernsey');
INSERT INTO timezones VALUES (360, 'Europe/Helsinki');
INSERT INTO timezones VALUES (361, 'Europe/Isle_of_Man');
INSERT INTO timezones VALUES (362, 'Europe/Istanbul');
INSERT INTO timezones VALUES (363, 'Europe/Jersey');
INSERT INTO timezones VALUES (364, 'Europe/Kaliningrad');
INSERT INTO timezones VALUES (365, 'Europe/Kiev');
INSERT INTO timezones VALUES (366, 'Europe/Lisbon');
INSERT INTO timezones VALUES (367, 'Europe/Ljubljana');
INSERT INTO timezones VALUES (368, 'Europe/London');
INSERT INTO timezones VALUES (369, 'Europe/Luxembourg');
INSERT INTO timezones VALUES (370, 'Europe/Madrid');
INSERT INTO timezones VALUES (371, 'Europe/Malta');
INSERT INTO timezones VALUES (372, 'Europe/Mariehamn');
INSERT INTO timezones VALUES (373, 'Europe/Minsk');
INSERT INTO timezones VALUES (374, 'Europe/Monaco');
INSERT INTO timezones VALUES (375, 'Europe/Moscow');
INSERT INTO timezones VALUES (376, 'Europe/Nicosia');
INSERT INTO timezones VALUES (377, 'Europe/Oslo');
INSERT INTO timezones VALUES (378, 'Europe/Paris');
INSERT INTO timezones VALUES (379, 'Europe/Podgorica');
INSERT INTO timezones VALUES (380, 'Europe/Prague');
INSERT INTO timezones VALUES (381, 'Europe/Riga');
INSERT INTO timezones VALUES (382, 'Europe/Rome');
INSERT INTO timezones VALUES (383, 'Europe/Samara');
INSERT INTO timezones VALUES (384, 'Europe/San_Marino');
INSERT INTO timezones VALUES (385, 'Europe/Sarajevo');
INSERT INTO timezones VALUES (386, 'Europe/Simferopol');
INSERT INTO timezones VALUES (387, 'Europe/Skopje');
INSERT INTO timezones VALUES (388, 'Europe/Sofia');
INSERT INTO timezones VALUES (389, 'Europe/Stockholm');
INSERT INTO timezones VALUES (390, 'Europe/Tallinn');
INSERT INTO timezones VALUES (391, 'Europe/Tirane');
INSERT INTO timezones VALUES (392, 'Europe/Tiraspol');
INSERT INTO timezones VALUES (393, 'Europe/Uzhgorod');
INSERT INTO timezones VALUES (394, 'Europe/Vaduz');
INSERT INTO timezones VALUES (395, 'Europe/Vatican');
INSERT INTO timezones VALUES (396, 'Europe/Vienna');
INSERT INTO timezones VALUES (397, 'Europe/Vilnius');
INSERT INTO timezones VALUES (398, 'Europe/Volgograd');
INSERT INTO timezones VALUES (399, 'Europe/Warsaw');
INSERT INTO timezones VALUES (400, 'Europe/Zagreb');
INSERT INTO timezones VALUES (401, 'Europe/Zaporozhye');
INSERT INTO timezones VALUES (402, 'Europe/Zurich');
INSERT INTO timezones VALUES (403, 'Indian/Antananarivo');
INSERT INTO timezones VALUES (404, 'Indian/Chagos');
INSERT INTO timezones VALUES (405, 'Indian/Christmas');
INSERT INTO timezones VALUES (406, 'Indian/Cocos');
INSERT INTO timezones VALUES (407, 'Indian/Comoro');
INSERT INTO timezones VALUES (408, 'Indian/Kerguelen');
INSERT INTO timezones VALUES (409, 'Indian/Mahe');
INSERT INTO timezones VALUES (410, 'Indian/Maldives');
INSERT INTO timezones VALUES (411, 'Indian/Mauritius');
INSERT INTO timezones VALUES (412, 'Indian/Mayotte');
INSERT INTO timezones VALUES (413, 'Indian/Reunion');
INSERT INTO timezones VALUES (414, 'Pacific/Apia');
INSERT INTO timezones VALUES (415, 'Pacific/Auckland');
INSERT INTO timezones VALUES (416, 'Pacific/Chatham');
INSERT INTO timezones VALUES (417, 'Pacific/Easter');
INSERT INTO timezones VALUES (418, 'Pacific/Efate');
INSERT INTO timezones VALUES (419, 'Pacific/Enderbury');
INSERT INTO timezones VALUES (420, 'Pacific/Fakaofo');
INSERT INTO timezones VALUES (421, 'Pacific/Fiji');
INSERT INTO timezones VALUES (422, 'Pacific/Funafuti');
INSERT INTO timezones VALUES (423, 'Pacific/Galapagos');
INSERT INTO timezones VALUES (424, 'Pacific/Gambier');
INSERT INTO timezones VALUES (425, 'Pacific/Guadalcanal');
INSERT INTO timezones VALUES (426, 'Pacific/Guam');
INSERT INTO timezones VALUES (427, 'Pacific/Honolulu');
INSERT INTO timezones VALUES (428, 'Pacific/Johnston');
INSERT INTO timezones VALUES (429, 'Pacific/Kiritimati');
INSERT INTO timezones VALUES (430, 'Pacific/Kosrae');
INSERT INTO timezones VALUES (431, 'Pacific/Kwajalein');
INSERT INTO timezones VALUES (432, 'Pacific/Majuro');
INSERT INTO timezones VALUES (433, 'Pacific/Marquesas');
INSERT INTO timezones VALUES (434, 'Pacific/Midway');
INSERT INTO timezones VALUES (435, 'Pacific/Nauru');
INSERT INTO timezones VALUES (436, 'Pacific/Niue');
INSERT INTO timezones VALUES (437, 'Pacific/Norfolk');
INSERT INTO timezones VALUES (438, 'Pacific/Noumea');
INSERT INTO timezones VALUES (439, 'Pacific/Pago_Pago');
INSERT INTO timezones VALUES (440, 'Pacific/Palau');
INSERT INTO timezones VALUES (441, 'Pacific/Pitcairn');
INSERT INTO timezones VALUES (442, 'Pacific/Ponape');
INSERT INTO timezones VALUES (443, 'Pacific/Port_Moresby');
INSERT INTO timezones VALUES (444, 'Pacific/Rarotonga');
INSERT INTO timezones VALUES (445, 'Pacific/Saipan');
INSERT INTO timezones VALUES (446, 'Pacific/Samoa');
INSERT INTO timezones VALUES (447, 'Pacific/Tahiti');
INSERT INTO timezones VALUES (448, 'Pacific/Tarawa');
INSERT INTO timezones VALUES (449, 'Pacific/Tongatapu');
INSERT INTO timezones VALUES (450, 'Pacific/Truk');
INSERT INTO timezones VALUES (451, 'Pacific/Wake');
INSERT INTO timezones VALUES (452, 'Pacific/Wallis');
INSERT INTO timezones VALUES (453, 'Pacific/Yap');


DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL,
  username varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'username, will be case-sensitive',
  `password` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'will be case-sensitive',
  active enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT 'is this user active?',
  deleted enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N' COMMENT 'is the user deleted?',
  is_god enum('Y','N') collate utf8_unicode_ci NOT NULL default 'N',
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The backend users';


DROP TABLE IF EXISTS users_sessions;
CREATE TABLE IF NOT EXISTS users_sessions (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL,
  session_id varchar(255) collate utf8_unicode_ci NOT NULL,
  secret_key varchar(255) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY idx_session_id_secret_key (session_id(100),secret_key(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS users_settings;
CREATE TABLE IF NOT EXISTS users_settings (
  user_id int(11) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'name of the setting',
  `value` text collate utf8_unicode_ci NOT NULL COMMENT 'serialized value',
  PRIMARY KEY  (user_id,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;