<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			errors
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** exceptionHandler */
require_once 'spoon/errors/handler.php';


/**
 * This is the default spoon exception, that extends the default php exception
 *
 * @package			errors
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonException extends Exception
{
	/**
	 * Exception name
	 *
	 * @var	string
	 */
	protected $name;


	/**
	 * String to obfuscate
	 *
	 * @var	array
	 */
	protected $obfuscate = array();


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $message
	 * @param	int[optional] $code
	 * @param	mixed[optional] $obfuscate
	 */
	public function __construct($message, $code = 0, $obfuscate = null)
	{
		// parent constructor
		parent::__construct((string) $message, $code);

		// set name
		$this->name = get_class($this);

		// obfuscating?
		if($obfuscate !== null) $this->obfuscate = (array) $obfuscate;
	}


	/**
	 * Retrieve the name of this exception
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Return an array of elements that need to be obfuscated
	 *
	 * @return	array
	 */
	public function getObfuscate()
	{
		return $this->obfuscate;
	}
}

?>