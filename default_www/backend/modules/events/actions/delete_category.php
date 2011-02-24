<?php

/**
 * This action will delete a category
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsDeleteCategory extends BackendBaseActionDelete
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
		if($this->id !== null && BackendEventsModel::existsCategory($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get data
			$this->record = (array) BackendEventsModel::getCategory($this->id);

			// delete item
			BackendEventsModel::deleteCategory($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('categories') .'&report=deleted-category&var='. urlencode($this->record['name']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') .'&error=non-existing');
	}
}

?>