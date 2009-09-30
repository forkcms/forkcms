<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		session
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/**
 * This exception is used to handle session related exceptions.
 *
 * @package		session
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonSessionException extends SpoonException {}


/**
 * This class provides some methods for setting, retrieving
 * and manipulating sessions
 *
 * @package		session
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		0.1.1
 */
class SpoonSession
{
	/**
	 * Deletes one or more session variables
	 *
	 * @return	void
	 * @param	mixed $keys
	 */
	public static function delete($keys)
	{
		// redefine
		$keys = (array) $keys;

		// unset these keys
		foreach($keys as $key) unset($_SESSION[$key]);
	}


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
	 * @param	string $key
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
	 * @param	string $key
	 */
	public static function get($key)
	{
		// start session if needed
		if(!session_id()) self::start();


		// redefine key
		$key = (string) $key;

		// fetch key
		if(self::exists((string) $key)) return $_SESSION[(string) $key];

		// key does't exist
		return false;
	}


	/**
	 * Returns the sessionID
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

    	// set key
    	$_SESSION[(string) $key] = $value;
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
}

?>