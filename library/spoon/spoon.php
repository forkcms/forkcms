<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */

/*
 * This is the version number for the current version of the
 * Spoon Library.
 */
define('SPOON_VERSION', '1.3.0');

/*
 * This setting will intervene when an exception occures. If enabled the exception will be
 * shown in all its glory. If disabled 'SPOON_DEBUG_MESSAGE' will be displayed instead.
 */
if(!defined('SPOON_DEBUG'))
{
	define('SPOON_DEBUG', true);
}

/*
 * If 'SPOON_DEBUG' is enabled and an exception occures, this message will be
 * displayed.
 */
if(!defined('SPOON_DEBUG_MESSAGE'))
{
	define('SPOON_DEBUG_MESSAGE', 'There seems to be an issue with this page.');
}

/*
 * If 'SPOON_DEBUG' is enabled and an exception occures, an email with the contents of this
 * exception will be emailed to 'SPOON_DEBUG_EMAIL' if it contains a valid email address.
 */
if(!defined('SPOON_DEBUG_EMAIL'))
{
	define('SPOON_DEBUG_EMAIL', '');
}

/*
 * If an exception occures, you can hook into the process that handles this exception
 * and add your own logic. The callback may be a function or static method. If you wish
 * to use a static method define this constant in this way: 'MyClass::myMethod'
 */
if(!defined('SPOON_EXCEPTION_CALLBACK'))
{
	define('SPOON_EXCEPTION_CALLBACK', '');
}

/*
 * Default charset that will be used when a charset needs to be provided to use for
 * certain functions/methods.
 */
if(!defined('SPOON_CHARSET'))
{
	define('SPOON_CHARSET', 'iso-8859-1');
}

/*
 * Should we use the Spoon autoloader to ensure the dependancies are automatically
 * loaded?
 */
if(!defined('SPOON_AUTOLOADER'))
{
	define('SPOON_AUTOLOADER', true);
}

/* SpoonException class */
require_once 'spoon/exception/exception.php';

// check mbstring extension
if(!extension_loaded('mbstring'))
{
	throw new SpoonException('You need to make sure the mbstring extension is loaded.');
}

// attach autoloader
if(SPOON_AUTOLOADER)
{
	spl_autoload_register(array('Spoon', 'autoLoader'));
}

/**
 * This class holds objects/data in a name based registry to make them easily
 * available throughout your application.
 *
 * @package		spoon
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class Spoon
{
	/**
	 * Registry of variables
	 *
	 * @var	array
	 */
	private static $registry = array();


	/**
	 * Spoon autoloader
	 *
	 * @param	string $class	The class that should be loaded.
	 */
	public static function autoLoader($class)
	{
		// redefine class
		$class = strtolower($class);

		// list of classes and their location
		$classes = array();
		$classes['spooncookie'] = 'cookie/cookie.php';
		$classes['spoondatabase'] = 'database/database.php';
		$classes['spoondatagrid'] = 'datagrid/datagrid.php';
		$classes['spoondatagridcolumn'] = 'datagrid/column.php';
		$classes['ispoondatagridpaging'] = 'datagrid/paging.php';
		$classes['spoondatagridpaging'] = 'datagrid/paging.php';
		$classes['spoondatagridsource'] = 'datagrid/source.php';
		$classes['spoondatagridsourcearray'] = 'datagrid/source_array.php';
		$classes['spoondatagridsourcedb'] = 'datagrid/source_db.php';
		$classes['spoondate'] = 'date/date.php';
		$classes['spoondirectory'] = 'directory/directory.php';
		$classes['spoonemail'] = 'email/email.php';
		$classes['spoonemailsmtp'] = 'email/smtp.php';
		$classes['spoonfeedexception'] = 'feed/exception.php';
		$classes['spoonfeedrss'] = 'feed/rss.php';
		$classes['spoonfeedrssitem'] = 'feed/rss_item.php';
		$classes['spoonfeedatomrss'] = 'feed/atom_rss.php';
		$classes['spoonfeedatomrssitem'] = 'feed/atom_rss_item.php';
		$classes['spoonfile'] = 'file/file.php';
		$classes['spoonfilecsv'] = 'file/csv.php';
		$classes['spoonfilter'] = 'filter/filter.php';
		$classes['spoonform'] = 'form/form.php';
		$classes['spoonformattributes'] = 'form/attributes.php';
		$classes['spoonformbutton'] = 'form/button.php';
		$classes['spoonformcheckbox'] = 'form/checkbox.php';
		$classes['spoonformdate'] = 'form/date.php';
		$classes['spoonformdropdown'] = 'form/dropdown.php';
		$classes['spoonformelement'] = 'form/element.php';
		$classes['spoonformfile'] = 'form/file.php';
		$classes['spoonformhidden'] = 'form/hidden.php';
		$classes['spoonformimage'] = 'form/image.php';
		$classes['spoonforminput'] = 'form/input.php';
		$classes['spoonformmulticheckbox'] = 'form/multi_checkbox.php';
		$classes['spoonformpassword'] = 'form/password.php';
		$classes['spoonformradiobutton'] = 'form/radiobutton.php';
		$classes['spoonformtext'] = 'form/text.php';
		$classes['spoonformtextarea'] = 'form/textarea.php';
		$classes['spoonformtime'] = 'form/time.php';
		$classes['spoonhttp'] = 'http/http.php';
		$classes['spoonicalexception'] = 'ical/exception.php';
		$classes['spoonical'] = 'ical/ical.php';
		$classes['spoonicalitem'] = 'ical/ical';
		$classes['Spoonicalevent'] = 'ical/ical';
		$classes['spoonlocale'] = 'locale/locale.php';
		$classes['spoonlog'] = 'log/log.php';
		$classes['spoonrestclient'] = 'rest/client.php';
		$classes['spoonsession'] = 'session/session.php';
		$classes['spoontemplate'] = 'template/template.php';
		$classes['spoontemplatecompiler'] = 'template/compiler.php';
		$classes['spoontemplatemodifiers'] = 'template/modifiers.php';
		$classes['spoonthumbnail'] = 'thumbnail/thumbnail.php';
		$classes['spoonxmlrpcclient'] = 'xmlrpc/client.php';

		// path
		$path = dirname(realpath(__FILE__));

		// does this file exist?
		if(isset($classes[$class]) && file_exists($path . '/' . $classes[$class]))
		{
			require_once $path . '/' . $classes[$class];
		}
	}


	/**
	 * Dumps the output of a variable in a more readable manner.
	 *
	 * @param	mixed $var				The variable to dump.
	 * @param	bool[optional] $exit	Should the code stop here?
	 */
	public static function dump($var, $exit = true)
	{
		ob_start();
		var_dump($var);
		$output = ob_get_clean();

		// no xdebug installed
		if(!extension_loaded('xdebug'))
		{
			$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
			$output = '<pre>' . htmlspecialchars($output, ENT_QUOTES, SPOON_CHARSET) . '</pre>';
		}

		echo $output;
		if($exit) exit;
	}


	/**
	 * Checks if an object with this name is in the registry.
	 *
	 * @return	bool
	 * @param	string $name	The name of the registry item to check for existence.
	 */
	public static function exists($name)
	{
		return isset(self::$registry[(string) $name]);
	}


	/**
	 * Fetch an item from the registry.
	 *
	 * @return	mixed
	 * @param	string $name	The name of the item to fetch.
	 */
	public static function get($name)
	{
		$name = (string) $name;

		if(!isset(self::$registry[$name]))
		{
			throw new SpoonException('No item "' . $name . '" exists in the registry.');
		}

		return self::$registry[$name];
	}


	/**
	 * Retrieve the list of available charsets.
	 *
	 * @return	array
	 */
	public static function getCharsets()
	{
		return array('utf-8', 'iso-8859-1', 'iso-8859-15');
	}


	/**
	 * Are we running in the command line?
	 *
	 * @return	bool
	 */
	public static function inCli()
	{
		return (PHP_SAPI == 'cli');
	}


	/**
	 * Registers a given value under a given name.
	 *
	 * @param	string $name			The name of the value to store.
	 * @param	mixed[optional] $value	The value that needs to be stored.
	 */
	public static function set($name, $value = null)
	{
		// redefine name
		$name = (string) $name;

		// delete item
		if($value === null)
		{
			unset(self::$registry[$name]);
		}

		// add & return its value
		else
		{
			self::$registry[$name] = $value;
			return self::get($name);
		}
	}
}
