<?php

/**
 * The configuration-object for the testimonials module.
 *
 * @package		backend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
final class BackendTestimonialsConfig extends BackendBaseConfig
{
	/**
	 * The default action.
	 *
	 * @var  string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions.
	 *
	 * @var  array
	 */
	protected $disabledActions = array();


	/**
	 * The disabled AJAX actions.
	 *
	 * @var  array
	 */
	protected $disabledAJAXActions = array();
}

?>