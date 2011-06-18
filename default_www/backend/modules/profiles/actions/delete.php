<?php

/**
 * This action will delete or undelete a profile.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.0
 */
class BackendProfilesDelete extends BackendBaseActionDelete
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
		if($this->id !== null && BackendProfilesModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get profile
			$profile = BackendProfilesModel::get($this->id);

			// already deleted? Prolly want to undo then
			if($profile['status'] === 'deleted')
			{
				// set profile status to active
				BackendProfilesModel::update($this->id, array('status' => 'active'));

				// report
				$report = 'undeleted';
			}

			// profile is active
			else
			{
				// delete profile
				BackendProfilesModel::delete($this->id);

				// report
				$report = 'deleted';
			}

			// redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=profile-' . $report . '&var=' . urlencode($profile['email']) . '&highlight=row-' . $profile['id']);
		}

		// profile does not exists
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>