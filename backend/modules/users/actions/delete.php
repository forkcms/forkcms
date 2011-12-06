<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the delete-action, it will deactivate and mark the user as deleted
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendUsersDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the user exist
		if($this->id !== null && BackendUsersModel::exists($this->id) && BackendAuthentication::getUser()->getUserId() != $this->id)
		{
			parent::execute();

			// get data
			$user = new BackendUser($this->id);

			// God-users can't be deleted
			if($user->isGod()) $this->redirect(BackendModel::createURLForAction('index') . '&error=cant-delete-god');

			// delete item
			BackendUsersModel::delete($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . $user->getSetting('nickname'));
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
