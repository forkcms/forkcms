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
 * in the frontend. @todo: better explanation?
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
	 * The current permissions.
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
		// @todo check if the profiles module is installed and active.

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

	/**
	 * @todo: doc
	 */
	private function isSecured()
	{
		$db = BackendModel::getDB();

		$allowed = $db->getVar(
			'SELECT i.allowed
			 FROM profiles_groups_permissions AS i
			 WHERE i.module = ? AND i.other_id = ? AND i.group_id = ?',
			array($this->module, $this->otherId, 0)
		);

		if($allowed == null)
		{
			return false;
		}

		else
		{
			return $allowed == 'Y';
		}
	}

	/**
	 * @todo: doc
	 */
	private function insertPermission($groupId, $allowed)
	{
		$db = BackendModel::getDB();

		$permission['module'] = $this->module;
		$permission['other_id'] = $this->otherId;
		$permission['group_id'] = (int) $groupId;
		$permission['allowed'] = $allowed ? 'Y' : 'N';

		$db->insert('profiles_groups_permissions', $permission);
	}

	protected function loadForm()
	{
		if(!empty($this->groups))
		{
			$this->frm->addCheckbox('is_secured', $this->isSecured());
			$this->frm->addMultiCheckbox('profile_groups', $this->groups, $this->permissions);
		}
	}

	/**
	 * Load the current permissions for the module and otherId.
	 */
	protected function loadPermissions()
	{
		$db = BackendModel::getDB();

		$this->permissions = (array) $db->getColumn(
			'SELECT i.group_id
			 FROM profiles_groups_permissions AS i
			 WHERE i.module = ? AND i.other_id = ? AND i.allowed = ?',
			array($this->module, $this->otherId, 'Y')
		);
	}

	/**
	 * Load all the available profile groups.
	 */
	protected function loadProfileGroups()
	{
		$db = BackendModel::getDB();

		$this->groups = (array) $db->getRecords(
			'SELECT id AS value, name AS label FROM profiles_groups',
			array(),
			'value'
		);
	}

	public function save($otherId)
	{
		$db = BackendModel::getDB();
		$this->otherId = (int) $otherId;

		// get the groups
		if($this->frm->getField('is_secured')->getChecked())
		{
			$allowedGroups = (array) $this->frm->getField('profile_groups')->getChecked();
		}

		else
		{
			$allowedGroups = array();
		}

		// delete existing permissions
		$db->delete(
			'profiles_groups_permissions',
			'module = ? AND other_id = ?',
			array($this->module, $this->otherId)
		);

		// insert the new permissions
		foreach($this->groups as $group)
		{
			$this->insertPermission(
				$group['value'],
				in_array($group['value'], $allowedGroups)
			);
		}

		/*
		 * Insert a permission for the "null-group". By doing this, the item will still have
		 * the correct security settings, even after the profile groups are deleted.
		 */
		$this->insertPermission(
			null,
			$this->frm->getField('is_secured')->getChecked()
		);
	}

	public function validate()
	{
		if($this->frm->isSubmitted())
		{
			if($this->frm->getField('is_secured')->getChecked())
			{
				if($this->frm->getField('profile_groups')->isFilled(BL::err('FieldIsRequired')));
			}
		}
	}
}