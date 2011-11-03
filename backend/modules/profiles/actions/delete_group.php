<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a profile group.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesDeleteGroup extends BackendBaseActionDelete
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendProfilesModel::existsGroup($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get group
			$group = BackendProfilesModel::getGroup($this->id);

			// delete group
			BackendProfilesModel::deleteGroup($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete_group', array('id' => $this->id));

			// group was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('groups') . '&report=deleted&var=' . urlencode($group['name']));
		}

		// group does not exists
		else $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
	}
}
