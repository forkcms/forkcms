<?php

/**
 * BackendMailmotorAddGroup
 * This is the add-action, it will display a form to create a new group
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAddGroup extends BackendBaseActionAdd
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
		$this->frm = new BackendForm('add');

		// add "no default group" option for radiobuttons
		$chkDefaultForLanguageValues[] = array('label' => BL::msg('NoDefault'), 'value' => '0');

		// set default for language radiobutton values
		foreach(BL::getWorkingLanguages() as $key => $value)
		{
			$chkDefaultForLanguageValues[] = array('label' => $value, 'value' => $key);
		}

		// create elements
		$this->frm->addText('name');
		$this->frm->addRadiobutton('default', $chkDefaultForLanguageValues);
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

			// shorten fields
			$txtName = $this->frm->getField('name');
			$rbtDefaultForLanguage = $this->frm->getField('default');

			// validate fields
			if($txtName->isFilled(BL::err('NameIsRequired')))
			{
				// check if the group exists by name
				if(BackendMailmotorModel::existsGroupByName($txtName->getValue())) $txtName->addError(BL::err('GroupAlreadyExists'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['name'] = $txtName->getValue();
				$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
				$item['language'] = $rbtDefaultForLanguage->getValue() === '0' ? null : $rbtDefaultForLanguage->getValue();
				$item['is_default'] = $rbtDefaultForLanguage->getChecked() ? 'Y' : 'N';

				// insert the item
				$id = BackendMailmotorCMHelper::insertGroup($item);

				// check if all default groups were set
				BackendMailmotorModel::checkDefaultGroups();

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=added&var=' . urlencode($item['name']) . '&highlight=id-' . $id);
			}
		}
	}
}

?>