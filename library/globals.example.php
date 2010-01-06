<?php
/**
 * Global configuration options and constants of the FORK CMS
 *
 * @package	Fork
 *
 * @author	Davy Hellemans <davy@netlash.com>
 * @author	Tijs Verkoyen <tijs@netlash.com>
 */

/**
 * Spoon configuration
 */
// should the debug information be shown
define('SPOON_DEBUG', true);
// mailaddress where the exceptions will be mailed to (<tag>-bugs@fork-cms.be)
define('SPOON_DEBUG_EMAIL', '');
// message for the visitors when an exception occur
define('SPOON_DEBUG_MESSAGE', 'Internal error.');


/**
 * Database configuration
 */
// type of connection
define('DB_TYPE', 'mysql');
// database name
define('DB_DATABASE', 'forkng');
// database host
//define('DB_HOSTNAME', 'mysql.local');
define('DB_HOSTNAME', '10.1.11.37');
// database username
define('DB_USERNAME', 'forkng');
// datebase password
define('DB_PASSWORD', 'forkng');


/**
 * Site configuration
 */
// the domain (without http)
define('SITE_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'forkng.local');
// the default title
define('SITE_DEFAULT_TITLE', 'Fork NG');
// the url
define('SITE_URL', 'http://'. SITE_DOMAIN);
// is the site multilanguage?
define('SITE_MULTILANGUAGE', true);


/**
 * Path configuration
 *
 * Depends on the serverlayout. Openminds ftw!
 */
// path to the website itself
define('PATH_WWW', '/Users/tijs/Sites/forkng.local/default_www');
// path to the Spoon library
define('PATH_LIBRARY', dirname(__FILE__));

?>