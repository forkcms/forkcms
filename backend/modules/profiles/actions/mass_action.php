<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Mass action handler to delete profiles or add them to a specific group.
 *
 * @author Jan Moesen <jan.moesen@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesMassAction extends BackendBaseAction
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('addToGroup', 'delete'), '');
		$ids = (isset($_GET['id'])) ? (array) $_GET['id'] : array();
		$newGroupId = SpoonFilter::getGetValue('newGroup', array_keys(BackendProfilesModel::getGroups()), '');

		// no ids provided
		if(empty($ids)) $this->redirect(BackendModel::createURLForAction('index') . '&error=no-profiles-selected');

		// delete the given profiles
		if($action === 'delete')
		{
			BackendProfilesModel::delete($ids);
			$report = 'deleted';
		}

		// add the profiles to the given group
		elseif($action === 'addToGroup')
		{
			// no group id provided
			if($newGroupId == '') $this->redirect(BackendModel::createURLForAction('index') . '&error=no-group-selected');

			// set new status
			foreach($ids as $id)
			{
				// profile must exist
				if(BackendProfilesModel::exists($id))
				{
					// make sure the user is not already part of this group without an expiration date
					foreach(BackendProfilesModel::getProfileGroups($id) as $existingGroup)
					{
						// if he is, skip to the next user
						if($existingGroup['group_id'] === $newGroupId) continue 2;
					}

					// OK, it's safe to add the user to this group
					BackendProfilesModel::insertProfileGroup(array(
						'profile_id' => $id,
						'group_id' => $newGroupId,
						'starts_on' => BackendModel::getUTCDate()
					));
				}
			}

			// report
			$report = 'added-to-group';
		}

		// unknown action
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=unknown-action');

		// report
		$report = (count($ids) > 1 ? 'profiles-' : 'profile-') . $report;

		// redirect
		$this->redirect(BackendModel::createURLForAction('index', null, null, array(
			'offset' => SpoonFilter::getGetValue('offset', null, ''),
			'order' => SpoonFilter::getGetValue('order', null, ''),
			'sort' => SpoonFilter::getGetValue('sort', null, ''),
			'email' => SpoonFilter::getGetValue('email', null, ''),
			'status' => SpoonFilter::getGetValue('status', null, ''),
			'group' => SpoonFilter::getGetValue('group', null, '')
		)) . '&report=' . $report);
	}
}
