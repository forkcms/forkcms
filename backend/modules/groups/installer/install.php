<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the groups module
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 */
class GroupsInstall extends ModuleInstaller
{
	/**
	 * Install the module
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
	 */
	private function insertDashboardSequence()
	{
		$db = $this->getDB();

		// create standard dashboard sequence
		$sequence = array(
			'settings' => array(
				'analyse' => array(
					'column' => 'left',
					'position' => 1,
					'hidden' => false,
					'present' => true
				)
			),
			'blog' => array(
				'comments' => array(
					'column' => 'middle',
					'position' => 1,
					'hidden' => false,
					'present' => true
				)
			),
			'mailmotor' => array(
				'statistics' => array(
					'column' => 'right',
					'position' => 1,
					'hidden' => false,
					'present' => true
				)
			)
		);

		// build groupsetting
		$groupSetting['group_id'] = 1;
		$groupSetting['name'] = 'dashboard_sequence';
		$groupSetting['value'] = serialize($sequence);

		// build usersetting
		$userSetting['user_id'] = 1;
		$userSetting['name'] = 'dashboard_sequence';
		$userSetting['value'] = serialize($sequence);

		// insert settings
		$db->insert('groups_settings', $groupSetting);
		$db->insert('users_settings', $userSetting);
	}
}
