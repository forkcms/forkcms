<?php

/**
 * BackendException
 * This exception is used to handle backend related exceptions.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendException extends SpoonException
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