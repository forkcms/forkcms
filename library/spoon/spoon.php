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
 * @author 		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		0.1.1
 */

/**
 * This is the version number for the current version of the
 * Spoon Library.
 */
define('SPOON_VERSION', '1.2.0');

/**
 * This setting will intervene when an exception occures. If enabled the exception will be
 * shown in all its glory. If disabled 'SPOON_DEBUG_MESSAGE' will be displayed instead.
 */
if(!defined('SPOON_DEBUG')) define('SPOON_DEBUG', true);

/**
 * If 'SPOON_DEBUG' is enabled and an exception occures, this message will be
 * displayed.
 */
if(!defined('SPOON_DEBUG_MESSAGE')) define('SPOON_DEBUG_MESSAGE', 'There seems to be an issue with this page. The administrator has been notified.');

/**
 * If 'SPOON_DEBUG' is enabled and an exception occures, an email with the contents of this
 * exception will be emailed to 'SPOON_DEBUG_EMAIL' if it contains a valid email address.
 */
if(!defined('SPOON_DEBUG_EMAIL')) define('SPOON_DEBUG_EMAIL', '');

/**
 * If an exception occures, you can hook into the process that handles this exception
 * and add your own logic. The callback may be a function or static method. If you wish
 * to use a static method define this constant in this way: 'MyClass::myMethod'
 */
if(!defined('SPOON_EXCEPTION_CALLBACK')) define('SPOON_EXCEPTION_CALLBACK', '');

/**
 * Default charset that will be used when a charset needs to be provided to use for
 * certain functions/methods.
 */
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'iso-8859-1');

/**
 * Should we use the Spoon autoloader to ensure the dependancies are automatically
 * loaded?
 */
if(!defined('SPOON_AUTOLOADER')) define('SPOON_AUTOLOADER', true);

/** SpoonException class */
require_once 'spoon/exception/exception.php';

// check mbstring extension
if(!extension_loaded('mbstring')) throw new SpoonException('You need to make sure the mbstring extension is loaded.');

// attach autoloader
if(SPOON_AUTOLOADER) spl_autoload_register(array('Spoon', 'autoLoader'));


/**
 * This class holds objects in a name based registry to make them easily
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
	 * Registry of objects
	 *
	 * @var	array
	 */
	private static $registry = array();


	/**
	 * Dumps the output of a variable in a more readable manner.
	 *
	 * @return	void
	 * @param	mixed $var
	 * @param	bool[optional] $exit
	 */
	public static function dump($var, $exit = true)
	{
		// fetch var
		ob_start();
		var_dump($var);
		$output = ob_get_clean();

		// cleanup the output
		$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

		// print
		echo '<pre>'. htmlspecialchars($output, ENT_QUOTES, SPOON_CHARSET) .'</pre>';

		// stop script
		if($exit) exit;
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
	 * Retrieve the whole registry or one specific instance.
	 *
	 * @return	mixed
	 * @param	string[optional] $name
	 */
	public static function getObjectReference($name = null)
	{
		// name defined
		if($name !== null)
		{
			// redefine
			$name = (string) $name;

			// item doesn't exist
			if(!isset(self::$registry[$name])) throw new SpoonException('An item with reference name "'. $name .'" doesn\'t exist in the registry.');

			// item exists
			return self::$registry[$name];
		}

		// whole registry
		return self::$registry;
	}


	/**
	 * Checks if an object with this name has been registered.
	 *
	 * @return	bool
	 * @param	string $name
	 */
	public static function isObjectReference($name)
	{
		return isset(self::$registry[(string) $name]);
	}


	/**
	 * Deletes a given object from the registry.
	 *
	 * @return	void
	 * @param	string $name
	 */
	public static function killObjectReference($name)
	{
		// name
		$name = (string) $name;

		// object doesn't exist
		if(!isset(self::$registry[$name])) throw new SpoonException('The given object "'. $name .'" doesn\'t exist in the registry.');

		// object exists
		unset(self::$registry[$name]);
	}


	/**
	 * Spoon autoloader
	 *
	 * @return	void
	 * @param	string $class
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
		if(isset($classes[$class]) && file_exists($path .'/'. $classes[$class])) require_once $path .'/'. $classes[$class];
	}


	/**
	 * Registers a given object under a given name.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	object $object
	 */
	public static function setObjectReference($name, $object)
	{
		// redefine name
		$name = (string) $name;

		// not an object
		if(!is_object($object)) throw new SpoonException('The given object "'. $name .'" is not an object.');

		// valid object
		else
		{
			// name already exists
			if(isset(self::$registry[$name])) throw new SpoonException('An object by the reference name "'. $name .'" has already been added to the registry.');

			// new item
			self::$registry[$name] = $object;

			// retrieve object
			return self::getObjectReference($name);
		}
	}
}

?>