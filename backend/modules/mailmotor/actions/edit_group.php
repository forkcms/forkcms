<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit a group
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorEditGroup extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if(BackendMailmotorModel::existsGroup($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendMailmotorModel::getGroup($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// add "no default group" option for radiobuttons
		$chkDefaultForLanguageValues[] = array('label' => BL::msg('NoDefault'), 'value' => '0');

		// set default for language radiobutton values
		foreach(BL::getWorkingLanguages() as $key => $value)
		{
			$chkDefaultForLanguageValues[] = array('label' => $value, 'value' => $key);
		}

		// create elements
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addRadiobutton('default', $chkDefaultForLanguageValues, $this->record['language']);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('group', $this->record);
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

			// shorten fields
			$txtName = $this->frm->getField('name');
			$rbtDefaultForLanguage = $this->frm->getField('default');

			// validate fields
			if($txtName->isFilled(BL::err('NameIsRequired')))
			{
				if($txtName->getValue() != $this->record['name'] && BackendMailmotorModel::existsGroupByName($txtName->getValue())) $txtName->addError(BL::err('GroupAlreadyExists'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['name'] = $txtName->getValue();
				$item['language'] = $rbtDefaultForLanguage->getValue() === '0' ? null : $rbtDefaultForLanguage->getValue();
				$item['is_default'] = $rbtDefaultForLanguage->getChecked() ? 'Y' : 'N';

				// update the item
				BackendMailmotorCMHelper::updateGroup($item);

				// check if all default groups were set
				BackendMailmotorModel::checkDefaultGroups();

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_group', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=edited&var=' . urlencode($item['name']) . '&highlight=id-' . $item['id']);
			}
		}
	}
}
