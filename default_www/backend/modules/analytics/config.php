<?php

/**
 * This is the configuration-object for the analytics module
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
final class BackendAnalyticsConfig extends BackendBaseConfig
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
	 * @return	void
	 * @param	string $module		The module.
	 */
	public function __construct($module)
	{
		// parent construct
		parent::__construct($module);

		// init
		$error = false;
		$action = Spoon::exists('url') ? Spoon::get('url')->getAction() : null;

		// analytics session token
		if(BackendModel::getModuleSetting('analytics', 'session_token') === null) $error = true;

		// analytics table id
		if(BackendModel::getModuleSetting('analytics', 'table_id') === null) $error = true;

		// missing settings, so redirect to the index-page to show a message (except on the index- and settings-page)
		if($error && $action != 'settings' && $action != 'index') SpoonHTTP::redirect(BackendModel::createURLForAction('index'));
	}
}

?>