<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
final class FrontendFormBuilderConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'form';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}

?>