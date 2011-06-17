<?php

/**
 * This action will toggle the block status a profile.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesBlock extends BackendBaseActionDelete
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

			// get item
			$profile = BackendProfilesModel::get($this->id);

			// already blocked? Prolly want to unblock then
			if($profile['status'] === 'blocked')
			{
				// set profile status to active
				BackendProfilesModel::update($this->id, array('status' => 'active'));

				// report
				$report = 'unblocked';
			}

			// block profile
			else
			{
				// delete profile session that may be active
				BackendProfilesModel::deleteSession($this->id);

				// set profile status to blocked
				BackendProfilesModel::update($this->id, array('status' => 'blocked'));

				// report
				$report = 'blocked';
			}

			// redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=profile-' . $report . '&var=' . urlencode($profile['email']) . '&highlight=row-' . $this->id);
		}

		// profile does not exists
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>