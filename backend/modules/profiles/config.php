<?php

/**
 * This is the configuration-object for the profiles module.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
final class BackendProfilesConfig extends BackendBaseConfig
{
	/**
	 * The default action.
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions.
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}

?>