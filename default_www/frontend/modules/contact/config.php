<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	contact
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
final class FrontendContactConfig extends FrontendBaseConfig
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