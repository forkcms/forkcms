<?php

/**
 * FrontendContentBlocksConfig
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	contentblocks
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
final class FrontendContentBlocksConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'detail';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}

?>