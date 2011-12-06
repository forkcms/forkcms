<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to update one or more custom fields (delete, ...)
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorMassCustomFieldAction extends BackendBaseAction
{
	/**
	 * The passed fields
	 *
	 * @var	array
	 */
	private $fields;

	/**
	 * The group record
	 *
	 * @var	array
	 */
	private $group;

	/**
	 * Delete addresses
	 */
	private function deleteCustomFields()
	{
		// set the group fields by flipping the custom fields array for this group
		$groupFields = array_flip($this->group['custom_fields']);

		// group custom fields found
		if(!empty($groupFields))
		{
			// loop the group fields and empty every value
			foreach($groupFields as &$field) $field = '';
		}

		// loop the fields
		foreach($this->fields as $field)
		{
			// check if the passed field is in the group's field list
			if(isset($groupFields[$field]))
			{
				// delete the custom field in CM
				BackendMailmotorCMHelper::deleteCustomField('[' . $field . ']', $this->group['id']);

				// remove the field from the group's field listing
				unset($groupFields[$field]);
			}
		}

		// update custom fields for this group
		BackendMailmotorModel::updateCustomFields($groupFields, $this->group['id']);

		// redirect
		$this->redirect(BackendModel::createURLForAction('custom_fields') . '&group_id=' . $this->group['id'] . '&report=deleted-custom-fields&var=' . $this->group['name']);
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('delete'), '');

		// get passed group ID
		$id = SpoonFilter::getGetValue('group_id', null, 0, 'int');

		// fetch group record
		$this->group = BackendMailmotorModel::getGroup($id);

		// set redirect URL
		$redirectURL = BackendModel::createURLForAction('custom_fields') . '&group_id=' . $this->group['id'];

		// no id's provided
		if(!$action) $this->redirect($redirectURL . '&error=no-action-selected');
		if(!isset($_GET['fields'])) $this->redirect($redirectURL . '&error=no-items-selected');
		if(empty($this->group)) $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');

		// at least one id
		else
		{
			// redefine id's
			$this->fields = (array) $_GET['fields'];

			// evaluate $action, see what action was triggered
			switch($action)
			{
				case 'delete':
					$this->deleteCustomFields();
					break;
			}
		}
	}
}
