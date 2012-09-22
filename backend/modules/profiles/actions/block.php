<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will toggle the block status a profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesBlock extends BackendBaseActionDelete
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

			// get item
			$profile = BackendProfilesModel::get($this->id);

			// already blocked? Prolly want to unblock then
			if($profile['status'] === 'blocked')
			{
				// set profile status to active
				BackendProfilesModel::update($this->id, array('status' => 'active'));

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_unblock', array('id' => $this->id));

				// redirect
				$this->redirect(BackendModel::createURLForAction('index') . '&report=profile-unblocked&var=' . urlencode($profile['email']) . '&highlight=row-' . $this->id);
			}

			// block profile
			else
			{
				// delete profile session that may be active
				BackendProfilesModel::deleteSession($this->id);

				// set profile status to blocked
				BackendProfilesModel::update($this->id, array('status' => 'blocked'));

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_block', array('id' => $this->id));

				// redirect
				$this->redirect(BackendModel::createURLForAction('index') . '&report=profile-blocked&var=' . urlencode($profile['email']) . '&highlight=row-' . $this->id);
			}
		}

		// profile does not exists
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
