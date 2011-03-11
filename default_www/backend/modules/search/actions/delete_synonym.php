<?php

/**
 * This action will delete a synonym
 *
 * @package		backend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSearchDeleteSynonym extends BackendBaseActionDelete
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
		if($this->id !== null && BackendSearchModel::existsSynonymById($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get data
			$this->record = (array) BackendSearchModel::getSynonym($this->id);

			// delete item
			BackendSearchModel::deleteSynonym($this->id);

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('synonyms') . '&report=deleted-synonym&var=' . urlencode($this->record['term']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('synonyms') . '&error=non-existing');
	}
}

?>