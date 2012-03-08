<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class represents the permissions-object.
 * It will handle some stuff in the backend, concerning the permissions of the profiles
 * in the frontend.
 *
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 */
class BackendProfilesPermissions
{
	/**
	 * The form instance.
	 *
	 * @var	BackendForm
	 */
	protected $frm;

	/**
	 * Is the profiles module installed?
	 *
	 * @var bool
	 */
	protected $isInstalled;

	/**
	 * The module.
	 *
	 * @var string
	 */
	protected $module;

	/**
	 * The other id.
	 *
	 * @var int
	 */
	protected $otherId;

	/**
	 * The permssion for the current item.
	 *
	 * @var array
	 */
	protected $permissions = array();

	/**
	 * @param BackendForm $form An instance of Backendform, the elements will be parsed in here.
	 * @param string $module
	 * @param int $otherId[optional]
	 */
	public function __construct(BackendForm $form, $module, $otherId = null)
	{
		// is the profiles module installed?
		$this->isInstalled = in_array('profiles', BackendModel::getModules());

		if($this->isInstalled)
		{
			// set the module
			$this->module = (string) $module;

			// set the form
			$this->frm = $form;

			if($otherId !== null)
			{
				// set the other id
				$this->otherId = (int) $otherId;

				// load the existing permissions
				$this->loadPermissions();
			}

			$this->loadProfileGroups();
			$this->loadForm();
		}
	}

	protected function loadForm()
	{
		$this->frm->addCheckbox('is_secured', $this->permissions['is_secured']);
		$this->frm->addCheckbox('show_in_navigation', $this->permissions['show_in_navigation']);

		if(!empty($this->groups))
		{
			$this->frm->addCheckbox('for_profile_groups', is_array($this->permissions['groups']));
			$this->frm->addMultiCheckbox('profile_groups', $this->groups, $this->permissions['groups']);
		}
	}

	/**
	 * Load the current permissions for the module and otherId.
	 */
	protected function loadPermissions()
	{
		$db = BackendModel::getDB();

		$data = $db->getVar(
			'SELECT i.data
			 FROM profiles_groups_permissions AS i
			 WHERE i.module = ? AND i.other_id = ?',
			array($this->module, $this->otherId)
		);

		// anything found?
		if($data !== null)
		{
			$this->permissions = unserialize($data);
		}

		// nothing found, create default values
		else
		{
			$this->permissions['is_secured'] = false;
			$this->permissions['groups'] = null;
			$this->permissions['show_in_navigation'] = false;
		}
	}

	/**
	 * Load all the available profile groups.
	 */
	protected function loadProfileGroups()
	{
		$db = BackendModel::getDB();

		$this->groups = (array) $db->getRecords(
			'SELECT id AS value, name AS label FROM profiles_groups
			 ORDER BY label',
			array(),
			'value'
		);
	}

	/**
	 * Save the permissions.
	 *
	 * @param int $otherId The other id that should be used for saving the permissions.
	 */
	public function save($otherId)
	{
		if($this->isInstalled)
		{
			$db = BackendModel::getDB();
			$this->otherId = (int) $otherId;

			// build the permissions
			$permission['module'] = $this->module;
			$permission['other_id'] = $this->otherId;
			$permission['data']['is_secured'] = $this->frm->getField('is_secured')->getChecked();
			$permission['data']['show_in_navigation'] = $this->frm->getField('show_in_navigation')->getChecked();
			$permission['data']['groups'] = null;

			// get the groups
			if(!empty($this->groups) && $this->frm->getField('for_profile_groups')->getChecked())
			{
				$permission['data']['groups'] = (array) $this->frm->getField('profile_groups')->getChecked();
			}

			$permission['data'] = serialize($permission['data']);

			// insert the permission, or update if it already exists
			$db->execute(
				'INSERT INTO profiles_groups_permissions(module, other_id, data)
				 VALUES(:module, :other_id, :data)
				 ON DUPLICATE KEY UPDATE data = :data',
				$permission
			);
		}
	}

	public function validate()
	{
		if($this->isInstalled && $this->frm->isSubmitted())
		{
			if(!empty($this->groups) && $this->frm->getField('for_profile_groups')->getChecked())
			{
				if($this->frm->getField('profile_groups')->isFilled(BL::err('ProfileGroupsIsRequired')));
			}
		}
	}
}