<?php

/**
 * This is the edit synonym action, it will display a form to edit an existing synonym.
 *
 * @package		backend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSearchEditSynonym extends BackendBaseActionEdit
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

		// does the item exists
		if($this->id !== null && BackendSearchModel::existsSynonymById($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exception
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		$this->record = BackendSearchModel::getSynonym($this->id);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editItem');

		// create elements
		$this->frm->addText('term', $this->record['term'], 255);
		$this->frm->addText('synonym', $this->record['synonym'], null, 'inputText synonymBox', 'inputTextError synonymBox');
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign id, term
		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assign('term', $this->record['term']);
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

			// validate fields
			$this->frm->getField('synonym')->isFilled(BL::err('SynonymIsRequired'));
			$this->frm->getField('term')->isFilled(BL::err('TermIsRequired'));
			if(BackendSearchModel::existsSynonymByTerm($this->frm->getField('term')->getValue(), $this->id)) $this->frm->getField('term')->addError(BL::err('TermExists'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['term'] = $this->frm->getField('term')->getValue();
				$item['synonym'] = $this->frm->getField('synonym')->getValue();
				$item['language'] = BL::getWorkingLanguage();

				// upate the item
				BackendSearchModel::updateSynonym($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('synonyms') . '&report=edited-synonym&var=' . urlencode($item['term']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>