<?php

/**
 * This is the delete-action, it will delete an item.
 *
 * @package		backend
 * @subpackage	groups
 * @actiongroup	management	This is the all-around management action.
 *
 * @author		Jeroen Van den Bossche <jeroenvandenbossche@gmail.com>
 * @since		2.0
 */
class BackendGroupsDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get id
		$this->id = $this->getParameter('id', 'int');

		// group exists and id is not null?
		if($this->id !== null && BackendGroupsModel::exists($this->id))
		{
			// call parent
			parent::execute();

			// get record
			$this->record = BackendGroupsModel::get($this->id);

			// delete group
			BackendGroupsModel::delete($this->id);

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['name']));
		}

		// no item found, redirect to the overview with an error
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>