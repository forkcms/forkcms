<?php

/**
 * This is the configuration-object for the search module
 *
 * @package		backend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
final class BackendSearchConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'statistics';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}

?>