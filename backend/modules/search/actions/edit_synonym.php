<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit synonym action, it will display a form to edit an existing synonym.
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendSearchEditSynonym extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendSearchModel::existsSynonymById($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exception
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->record = BackendSearchModel::getSynonym($this->id);
	}

	/**
	 * Load the form
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
	 */
	protected function parse()
	{
		parent::parse();

		// assign id, term
		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assign('term', $this->record['term']);
	}

	/**
	 * Validate the form
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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_synonym', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('synonyms') . '&report=edited-synonym&var=' . urlencode($item['term']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
