<?php

/**
 * BackendSearchAddSynonym
 * This is the add-action, it will display a form to create a new synonym
 *
 * @package		backend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSearchAddSynonym extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('addItem');

		// create elements
		$this->frm->addText('term', null, 255);
		$this->frm->addText('synonym', null, null, 'inputText synonymBox', 'inputTextError synonymBox');
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate field
			$this->frm->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
			$this->frm->getField('term')->isFilled(BL::err('TermIsRequired'));
			if(BackendSearchModel::existsSynonymByTerm($this->frm->getField('term')->getValue())) $this->frm->getField('term')->addError(BL::err('TermExists'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item = array();
				$item['term'] = $this->frm->getField('term')->getValue();
				$item['synonym'] = $this->frm->getField('synonym')->getValue();
				$item['language'] = BL::getWorkingLanguage();

				// insert the item
				$id = BackendSearchModel::insertSynonym($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('synonyms') . '&report=added-synonym&var=' . urlencode($item['term']) . '&highlight=row-' . $id);
			}
		}
	}
}

?>