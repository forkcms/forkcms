<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderEdit extends BackendBaseActionEdit
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
		if($this->id !== null && BackendFormBuilderModel::exists($this->id))
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

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		$this->record = BackendFormBuilderModel::get($this->id);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addDropdown('method', array('database' => BL::getLabel('MethodDatabase'), 'database_email' => BL::getLabel('MethodDatabaseEmail')), $this->record['method']);
		$this->frm->addText('email', $this->record['email']);
		$this->frm->addText('identifier', $this->record['identifier']);
		$this->frm->addEditor('success_message', $this->record['success_message']);

		// textfield dialog
		$this->frm->addText('textbox_label');
		$this->frm->addText('textbox_value');
		$this->frm->addCheckbox('textbox_required');
		$this->frm->addText('textbox_required_error_message');
		$this->frm->addDropdown('textbox_validation', array('' => '', 'email' => BL::getLabel('Email'), 'numeric' => BL::getLabel('Numeric')));
		$this->frm->addText('textbox_validation_parameter');
		$this->frm->addText('textbox_error_message');

		// textarea dialog
		$this->frm->addText('textarea_label');
		$this->frm->addTextarea('textarea_value');
		$this->frm->getField('textarea_value')->setAttribute('cols', 30);
		$this->frm->addCheckbox('textarea_required');
		$this->frm->addText('textarea_required_error_message');
		$this->frm->addDropdown('textarea_validation', array('' => ''));
		$this->frm->addText('textarea_validation_parameter');
		$this->frm->addText('textarea_error_message');

		// dropdown dialog
		$this->frm->addText('dropdown_label');
		$this->frm->addText('dropdown_values');
		$this->frm->addDropdown('dropdown_default_value', array('' => ''))->setAttribute('rel', 'dropDownValues');
		$this->frm->addCheckbox('dropdown_required');
		$this->frm->addText('dropdown_required_error_message');

		// radiobutton dialog
		$this->frm->addText('radiobutton_label');
		$this->frm->addText('radiobutton_values');
		$this->frm->addDropdown('radiobutton_default_value', array('' => ''))->setAttribute('rel', 'radioButtonValues');
		$this->frm->addCheckbox('radiobutton_required');
		$this->frm->addText('radiobutton_required_error_message');

		// checkbox dialog
		$this->frm->addText('checkbox_label');
		$this->frm->addText('checkbox_values');
		$this->frm->addDropdown('checkbox_default_value', array('' => ''))->setAttribute('rel', 'checkBoxValues');
		$this->frm->addCheckbox('checkbox_required');
		$this->frm->addText('checkbox_required_error_message');

		// heading dialog
		$this->frm->addText('heading');

		// paragraph dialog
		$this->frm->addTextarea('paragraph');
		$this->frm->getField('paragraph')->setAttribute('cols', 30);

		// submit dialog
		$this->frm->addText('submit');
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// fields
		$this->parseFields();

		// call parent
		parent::parse();

		// assign fields
		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assign('name', $this->record['name']);

		// parse error messages
		$this->parseErrorMessages();
	}


	/**
	 * Parse the default error messages
	 *
	 * @return	void
	 */
	private function parseErrorMessages()
	{
		// set frontend locale
		FL::setLocale(BL::getWorkingLanguage());

		// assign error messages
		$this->tpl->assign('errors', BackendFormBuilderModel::getErrors());
	}


	/**
	 * Parse the fields
	 *
	 * @return	void
	 */
	private function parseFields()
	{
		// init
		$fieldsHTML = array();

		// get fields
		$fields = BackendFormBuilderModel::getFields($this->id);

		// loop fields
		foreach($fields as $field)
		{
			// submit button
			if($field['type'] == 'submit')
			{
				// assign
				$this->tpl->assign('submitId', $field['id']);

				// add field
				$btn = $this->frm->addButton('submit_field', SpoonFilter::htmlspecialcharsDecode($field['settings']['values']), 'button');
				$btn->setAttribute('disabled', 'disabled');

				// skip
				continue;
			}

			// parse field to html
			$fieldsHTML[]['field'] = FormBuilderHelper::parseField($field);
		}

		// assign iteration
		$this->tpl->assign('fields', $fieldsHTML);
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
				elseif(BackendFormBuilderModel::existsIdentifier($txtIdentifier->getValue(), $this->id)) $txtIdentifier->setError(BL::getError('UniqueIdentifier'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build array
				$values['name'] = $txtName->getValue();
				$values['method'] = $ddmMethod->getValue();
				$values['email'] = ($ddmMethod->getValue() == 'database_email') ? $txtEmail->getValue() : null;
				$values['success_message'] = $txtSuccessMessage->getValue(true);
				$values['identifier'] = ($txtIdentifier->isFilled() ? $txtIdentifier->getValue() : BackendFormBuilderModel::createIdentifier());
				$values['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$id = (int) BackendFormBuilderModel::update($this->id, $values);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($values['name']) . '&highlight=row-' . $id);
			}
		}
	}
}

?>