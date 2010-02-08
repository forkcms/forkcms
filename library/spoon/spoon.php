<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		spoon
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */

/**
 * This is the version number for the current version of the
 * Spoon Library.
 */
define('SPOON_VERSION', '1.1.5');

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
 * Default charset that will be used when a charset needs to be provided to use for
 * certain functions/methods.
 */
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'iso-8859-1');

/** SpoonException class */
require_once 'spoon/exceptions/exception.php';


/**
 * This class holds objects in a name based registry to make them easily
 * available throughout your application.
 *
 * @package		spoon
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
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
		$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

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
	 * Retrieve the whole registry or the requested item.
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
		}
	}
}

?>