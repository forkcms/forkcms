<?php

/**
 * This is the configuration-object for the core
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendCoreConfig extends BackendBaseConfig
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
