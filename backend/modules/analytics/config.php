<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object for the analytics module
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsConfig extends BackendBaseConfig
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

	/**
	 * Check if all required settings have been set
	 *
	 * @param string $module The module.
	 */
	public function __construct($module)
	{
		parent::__construct($module);

		$error = false;
		$action = Spoon::exists('url') ? Spoon::get('url')->getAction() : null;

		// analytics session token
		if(BackendModel::getModuleSetting('analytics', 'session_token') === null) $error = true;

		// analytics table id
		if(BackendModel::getModuleSetting('analytics', 'table_id') === null) $error = true;

		// missing settings, so redirect to the index-page to show a message (except on the index- and settings-page)
		if($error && $action != 'settings' && $action != 'index')
		{
			SpoonHTTP::redirect(BackendModel::createURLForAction('index'));
		}
	}
}
