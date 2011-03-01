<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
final class FrontendSearchConfig extends FrontendBaseConfig
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