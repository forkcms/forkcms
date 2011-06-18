<?php

/**
 * This is the configuration-object.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.0
 */
final class FrontendProfilesConfig extends FrontendBaseConfig
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