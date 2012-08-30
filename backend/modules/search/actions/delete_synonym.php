<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a synonym
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class BackendSearchDeleteSynonym extends BackendBaseActionDelete
{
	/**
	 * Execute the action
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

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete_synonym', array('id' => $this->id));

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('synonyms') . '&report=deleted-synonym&var=' . urlencode($this->record['term']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('synonyms') . '&error=non-existing');
	}
}
