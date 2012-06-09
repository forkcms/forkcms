<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	session
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * This class provides some methods for setting, retrieving
 * and manipulating sessions
 *
 * @package		spoon
 * @subpackage	session
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		0.1.1
 */
class SpoonSession
{
	/**
	 * Deletes one or more session variables.
	 */
	public static function delete()
	{
		// start session if needed
		if(!session_id()) self::start();

		// loop all arguments
		foreach(func_get_args() as $argument)
		{
			// array element
			if(is_array($argument))
			{
				// loop the keys
				foreach($argument as $key)
				{
					// unset session key
					unset($_SESSION[(string) $key]);
				}
			}

			// other type(s)
			else
			{
				// remove from array
				unset($_SESSION[(string) $argument]);
			}
		}
	}


	/**
	 * Destroys the session.
	 */
	public static function destroy()
	{
		if(session_id())
		{
			session_unset();
			session_destroy();
			$_SESSION = array();
		}
	}


	/**
	 * Checks if a session variable exists.
	 *
	 * @return	bool			If the key(s) exist(s) true, otherwise false.
	 */
	public static function exists()
	{
		// start session if needed
		if(!session_id()) self::start();

		// loop all arguments
		foreach(func_get_args() as $argument)
		{
			// array element
			if(is_array($argument))
			{
				// loop the keys
				foreach($argument as $key)
				{
					// does NOT exist
					if(!isset($_SESSION[(string) $key])) return false;
				}
			}

			// other type(s)
			else
			{
				// does NOT exist
				if(!isset($_SESSION[(string) $argument])) return false;
			}
		}

		return true;
	}


	/**
	 * Gets a variable that was stored in the session.
	 *
	 * @return	mixed			The value that was stored.
	 * @param	string $key		The key of the variable to get.
	 */
	public static function get($key)
	{
		// start session if needed
		if(!session_id()) self::start();

		// redefine key
		$key = (string) $key;

		// fetch key
		if(self::exists((string) $key)) return $_SESSION[(string) $key];

		// key doesn't exist
		return null;
	}


	/**
	 * Returns the sessionID.
	 *
	 * @return	string	The unique session id
	 */
	public static function getSessionId()
	{
		if(!session_id()) self::start();
		return session_id();
	}


	/**
	 * Stores a variable in the session.
	 *
	 * @param	string $key		The key for the variable.
	 * @param	mixed $value	The value to store.
	 */
	public static function set($key, $value)
	{
		// start session if needed
		if(!session_id()) self::start();

		// set key
		$_SESSION[(string) $key] = $value;
	}


	/**
	 * Starts the session.
	 */
	public static function start()
	{
		// session already started?
		if(!session_id())
		{
			// start the session
			return @session_start();
		}

		// if already started
		return true;
	}
}


/**
 * This exception is used to handle session related exceptions.
 *
 * @package		spoon
 * @subpackage	session
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonSessionException extends SpoonException {}
