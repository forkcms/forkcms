<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
final class FrontendPagesConfig extends FrontendBaseConfig
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