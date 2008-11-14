<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			spoon
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */

// current spoon version
define('SPOON_VERSION', '1.0.0');

// default strict setting
if(!defined('SPOON_STRICT')) define('SPOON_STRICT', true);

/** SpoonException class */
require_once 'spoon/errors/exception.php';


/**
 * This class can hold objects in a sort of name based registry to make
 * them easily available throughout your scripts flow
 *
 * @package			spoon
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
final class Spoon
{
	/**
	 * Registry of objects
	 *
	 * @var	array
	 */
	private static $registry = array();


	/**
	 * Dumps the output of a variable in a more readable manner
	 *
	 * @return	string
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
		echo '<pre>'. htmlentities($output, ENT_QUOTES) .'</pre>';

		// return
		if($exit) exit;
	}


	/**
	 * Retrieve the whole registry or the requested item
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
	 * Checks if an object with this name has been registered
	 *
	 * @return	bool
	 * @param	string $name
	 */
	public static function isObjectReference($name)
	{
		return isset(self::$registry[(string) $name]);
	}


	/**
	 * Deletes a given object from the registry
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
	 * Registers a given object under a given name
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