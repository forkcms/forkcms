<?php

/**
 * BackendContentBlocksDelete
 * This is the delete-action, it will delete an item.
 *
 * @package		backend
 * @subpackage	content_blocks
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendContentBlocksDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendContentBlocksModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendContentBlocksModel::get($this->id);

			// delete item
			BackendContentBlocksModel::delete($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted&var='. urlencode($this->record['title']));
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>