<?php

/**
 * This action will delete a profile group.
 *
 * @package		backend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendProfilesDeleteGroup extends BackendBaseActionDelete
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
		if($this->id !== null && BackendProfilesModel::existsGroup($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get group
			$group = BackendProfilesModel::getGroup($this->id);

			// delete group
			BackendProfilesModel::deleteGroup($this->id);

			// group was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('groups') . '&report=deleted&var=' . urlencode($group['name']));
		}

		// group does not exists
		else $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
	}
}

?>