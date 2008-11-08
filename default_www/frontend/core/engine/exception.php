<?php
/**
 * This exception is used to handle frontend related exceptions.
 *
 * @package		Frontend
 * @subpackage	Core
 *
 *
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			2.0
 */
class FrontendException extends Exception
{
	/**
	 * Default constructor.
	 *
	 * @return	void
	 * @param	string $message
	 * @param	int[optional] $code
	 */
	public function __construct($message, $code = 0)
	{
		// parent constructor
		parent::__construct((string) $message, (int) $code);
	}
}

?>