<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			session
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonSessionException class */
require_once 'spoon/session/exception.php';


/**
 * This base class provides all the methods used by sessions.
 *
 * @package			session
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
class SpoonSession
{
	/**
	 * Strict setting
	 *
	 * @var	bool
	 */
	private static $strict = SPOON_STRICT;


	/**
	 * Destroys the session
	 *
	 * @return	void
	 */
	public static function destroy()
	{
		if(session_id()) session_destroy();
	}


	/**
	 * Checks if a session variable exists
	 *
	 * @return	bool
	 * @param	string	$key
	 */
	public static function exists($key)
	{
		// start session if needed
		if(!session_id()) self::start();

		// key exists?
		return isset($_SESSION[(string) $key]);
	}


	/**
	 * Gets a variable that was stored in the session
	 *
	 * @return	mixed
	 * @param	string	$key
	 */
	public static function get($key)
	{
		// start session if needed
		if(!session_id()) self::start();

		// redefine key
		$key = (string) $key;

		// fetch key
		if(self::exists($key)) return $_SESSION[$key];

		// key does't exist
		else
		{
			// strict?
			if(self::$strict) throw new SpoonSessionException('This key doesn\'t exists. Key: '.$key);
			return false;
		}
	}


	/**
	 * Retrieve the strict option
	 *
	 * @return	bool
	 */
	public static function getStrict()
	{
		return self::$strict;
	}


	/**
	 * Returns the sessionID
	 *
	 * @example SpoonSession::getSessionId()
	 *
	 * @return	string
	 */
	public static function getSessionId()
	{
		if(!session_id()) self::start();
		return session_id();
	}


	/**
	 * Stores a variable in the session
	 *
	 * @return	void
	 * @param	string $key
	 * @param	mixed $value
	 */
    public static function set($key, $value)
    {
    	// start session if needed
    	if(!session_id()) self::start();

    	// redefine arguments
    	$key = (string) $key;
    	$value = (string) $value;

    	// set key
    	$_SESSION[$key] = $value;
    }


    /**
	 * Starts the session
	 *
	 * @return void
	 */
	public static function start()
	{
		if(!session_id()) @session_start();
	}


	/**
	 * Sets the strict option
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public static function setStrict($on = true)
	{
		self::$strict = (bool) $on;
	}
}

?>