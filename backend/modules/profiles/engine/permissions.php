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
	 * The form instance
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
			//$this->loadPermissions();
		}

		$this->loadProfileGroups();
		$this->loadForm();
	}

	protected function loadForm()
	{
		if(!empty($this->groups))
		{
			$this->frm->addCheckbox('is_secured');
			$this->frm->addMultiCheckbox('profile_groups', $this->groups);
		}
	}

	protected function loadProfileGroups()
	{
		$db = BackendModel::getDB();

		$this->groups = (array) $db->getRecords(
			'SELECT id AS value, name AS label FROM profiles_groups',
			array(),
			'value'
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

	public function save($otherId)
	{
		$db = BackendModel::getDB();
		$this->otherId = (int) $otherId;

		// get the groups
		$groups = (array) $this->frm->getField('profile_groups')->getChecked();

		// delete existing permissions
		$db->delete(
			'profiles_groups_permissions',
			'module = ? AND other_id = ?',
			array($this->module, $this->otherId)
		);

		// init the data to insert in the database
		$permission['module'] = $this->module;
		$permission['other_id'] = $this->otherId;

		// insert the new permissions
		foreach($groups as $group)
		{
			$permission['group_id'] = (int) $group;
			$db->insert('profiles_groups_permissions', $permission);
		}
	}
}