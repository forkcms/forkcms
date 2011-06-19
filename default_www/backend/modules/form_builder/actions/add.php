<?php

/**
 * This is the add-action, it will display a form to create a new item.
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderAdd extends BackendBaseActionAdd
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

		// create elements
		$this->frm->addText('name');
		$this->frm->addDropdown('method', array('database' => BL::getLabel('MethodDatabase'), 'database_email' => BL::getLabel('MethodDatabaseEmail')), 'database_email');
		$this->frm->addText('email');
		$this->frm->addText('identifier', BackendFormBuilderModel::createIdentifier());
		$this->frm->addEditor('success_message');
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

			// shorten the fields
			$txtName = $this->frm->getField('name');
			$txtEmail = $this->frm->getField('email');
			$ddmMethod = $this->frm->getField('method');
			$txtSuccessMessage = $this->frm->getField('success_message');
			$txtIdentifier = $this->frm->getField('identifier');

			// validate fields
			$txtName->isFilled(BL::getError('NameIsRequired'));
			$txtSuccessMessage->isFilled(BL::getError('SuccessMessageIsRequired'));
			if($ddmMethod->isFilled(BL::getError('NameIsRequired')) && $ddmMethod->getValue() == 'database_email') $txtEmail->isEmail(BL::getError('EmailIsRequired'));

			// identifier
			if($txtIdentifier->isFilled())
			{
				// invalid characters
				if(!SpoonFilter::isValidAgainstRegexp('/^[a-zA-Z0-9\.\_\-]+$/', $txtIdentifier->getValue())) $txtIdentifier->setError(BL::getError('InvalidIdentifier'));

				// unique identifier
				elseif(BackendFormBuilderModel::existsIdentifier($txtIdentifier->getValue())) $txtIdentifier->setError(BL::getError('UniqueIdentifier'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build array
				$values['language'] = BL::getWorkingLanguage();
				$values['user_id'] = BackendAuthentication::getUser()->getUserId();
				$values['name'] = $txtName->getValue();
				$values['method'] = $ddmMethod->getValue();
				$values['email'] = ($values['method'] == 'database_email') ? $txtEmail->getValue() : null;
				$values['success_message'] = $txtSuccessMessage->getValue(true);
				$values['identifier'] = ($txtIdentifier->isFilled() ? $txtIdentifier->getValue() : BackendFormBuilderModel::createIdentifier());
				$values['created_on'] = BackendModel::getUTCDate();
				$values['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$id = BackendFormBuilderModel::insert($values);

				// set frontend locale
				FL::setLocale(BL::getWorkingLanguage());

				// create submit button
				$field['form_id'] = $id;
				$field['type'] = 'submit';
				$field['settings'] = serialize(array('values' => ucfirst(FL::getLabel('Send'))));
				BackendFormBuilderModel::insertField($field);

				// everything is saved, so redirect to the editform
				$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $id . '&report=added&var=' . urlencode($values['name']) . '#tabFields');
			}
		}
	}
}

?>