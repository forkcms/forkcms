<?php

/**
 * This action will delete a membership of a profile in a group.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesDeleteProfileGroup extends BackendBaseActionDelete
{
	/**
	 * Execute the action.
	 *
	 * @return	void
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

			// profile group was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $profileGroup['profile_id'] . '&report=membership-deleted#tabGroups');
		}

		// group does not exists
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>