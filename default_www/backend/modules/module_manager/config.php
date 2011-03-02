<?php

/**
 * BackendModuleManagerConfig
 * This is the configuration-object for the module manager module
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author 		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
final class BackendModuleManagerConfig extends BackendBaseConfig
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