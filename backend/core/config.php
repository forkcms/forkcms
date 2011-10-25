<?php

/**
 * This is the configuration-object for the core
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Matthias Mullie <matthias@mullie.eu>
 * @since		2.0
 */
final class BackendCoreConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}

?>