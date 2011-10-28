<?php

/**
 * Installer for the groups module
 *
 * @package		installer
 * @subpackage	groups
 *
 * @author		Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @since		2.0
 */
class GroupsInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'settings' as a module
		$this->addModule('groups', 'The module to manage usergroups.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'groups');

		// action rights
		$this->setActionRights(1, 'groups', 'index');
		$this->setActionRights(1, 'groups', 'add');
		$this->setActionRights(1, 'groups', 'edit');
		$this->setActionRights(1, 'groups', 'delete');

		// set navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$this->setNavigation($navigationSettingsId, 'Groups', 'groups/index', array(
			'groups/add',
			'groups/edit'
		), 5);

		// insert admins dashboard sequence
		$this->insertDashboardSequence();
	}


	/**
	 * Insert an empty admin dashboard sequence
	 *
	 * @return	void
	 */
	private function insertDashboardSequence()
	{
		// get db
		$db = $this->getDB();

		// build groupsetting
		$groupSetting['group_id'] = 1;
		$groupSetting['name'] = 'dashboard_sequence';
		$groupSetting['value'] = serialize(array());

		// build usersetting
		$userSetting['user_id'] = 1;
		$userSetting['name'] = 'dashboard_sequence';
		$userSetting['value'] = serialize(array());

		// insert settings
		$db->insert('groups_settings', $groupSetting);
		$db->insert('users_settings', $userSetting);

		// create default dashboard widget
		$analyse = array(
			'column' => 'left',
			'position' => 1,
			'hidden' => false,
			'present' => true
		);

		// insert default dashboard widget
		$this->insertDashboardWidget('settings', 'analyse', $analyse);
	}
}

?>