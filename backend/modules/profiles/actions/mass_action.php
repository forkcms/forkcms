<?php

/**
 * Mass action handler to delete profiles or add them to a specific group.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesMassAction extends BackendBaseAction
{
	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('addToGroup', 'delete'), '');
		$ids = (isset($_GET['id'])) ? (array) $_GET['id'] : array();
		$newGroupId = SpoonFilter::getGetValue('newGroup', array_keys(BackendProfilesModel::getGroups()), '');

		// at least one id
		if(!empty($ids))
		{
			// delete the given profiles
			if($action === 'delete')
			{
				BackendProfilesModel::delete($ids);
				$report = 'deleted';
			}

			// add the profiles to the given group
			elseif($action === 'addToGroup')
			{
				// for which we need a group of course
				if($newGroupId != '')
				{
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

				// no group id provided
				else $this->redirect(BackendModel::createURLForAction('index') . '&error=no-group-selected');
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

		// no id's provided
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=no-profiles-selected');
	}
}

?>
