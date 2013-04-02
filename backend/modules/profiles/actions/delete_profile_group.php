<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a membership of a profile in a group.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesDeleteProfileGroup extends BackendBaseActionDelete
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendProfilesModel::existsProfileGroup($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get profile group
			$profileGroup = BackendProfilesModel::getProfileGroup($this->id);

			// delete profile group
			BackendProfilesModel::deleteProfileGroup($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_profile_delete_from_group', array('id' => $this->id));

			// profile group was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $profileGroup['profile_id'] . '&report=membership-deleted#tabGroups');
		}

		// group does not exists
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
