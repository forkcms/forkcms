<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete or undelete a profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 */
class BackendProfilesDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action.
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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_reactivate', array('id' => $this->id));

				// redirect
				$this->redirect(BackendModel::createURLForAction('index') . '&report=profile-undeleted&var=' . urlencode($profile['email']) . '&highlight=row-' . $profile['id']);
			}

			// profile is active
			else
			{
				// delete profile
				BackendProfilesModel::delete($this->id);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_delete_profile', array('id' => $this->id));

				// redirect
				$this->redirect(BackendModel::createURLForAction('index') . '&report=profile-deleted&var=' . urlencode($profile['email']) . '&highlight=row-' . $profile['id']);
			}
		}

		// profile does not exists
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
