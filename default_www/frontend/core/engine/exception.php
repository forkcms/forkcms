<?php

/**
 * This exception is used to handle frontend related exceptions.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendException extends SpoonException
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