<?php

/**
 * Global configuration options and constants of the FORK CMS
 *
 * @package	Fork
 *
 * @author	Davy Hellemans <davy@netlash.com>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Matthias Mullie <matthias@mullie.eu>
 */

/**
 * Spoon configuration
 */
// should the debug information be shown
define('SPOON_DEBUG', '<debug-mode>');
// mailaddress where the exceptions will be mailed to (<tag>-bugs@fork-cms.be)
define('SPOON_DEBUG_EMAIL', '<spoon-debug-email>');
// message for the visitors when an exception occur
define('SPOON_DEBUG_MESSAGE', 'Internal error.');
// default charset used in spoon.
define('SPOON_CHARSET', 'utf-8');


/**
 * Fork configuration
 */
// version of Fork
define('FORK_VERSION', '3.2.2');


/**
 * Database configuration
 */
// type of connection
define('DB_TYPE', 'mysql');
// database name
define('DB_DATABASE', '<database-name>');
// database host
define('DB_HOSTNAME', '<database-hostname>');
// database port
define('DB_PORT', '<database-port>');
// database username
define('DB_USERNAME', '<database-username>');
// datebase password
define('DB_PASSWORD', '<database-password>');


/**
 * Site configuration
 */
// the protocol
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PROTOCOL']) ? (strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https') : 'http');
// the domain (without http(s))
define('SITE_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '<site-domain>');
// the default title
define('SITE_DEFAULT_TITLE', '<site-default-title>');
// the url
define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);
// is the site multilanguage?
define('SITE_MULTILANGUAGE', '<site-multilanguage>');
// default action group tag
define('ACTION_GROUP_TAG', '<action-group-tag>');
// default action rights level
define('ACTION_RIGHTS_LEVEL', '<action-rights-level>');


/**
 * Path configuration
 *
 * Depends on the server layout
 */
// path to the website itself
define('PATH_WWW', '<path-www>');
// path to the library
define('PATH_LIBRARY', '<path-library>');
