<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendFormBuilderAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('add');
		$this->frm->addText('name');
		$this->frm->addDropdown('method', array('database' => BL::getLabel('MethodDatabase'), 'database_email' => BL::getLabel('MethodDatabaseEmail')), 'database_email');
		$this->frm->addText('email');
		$this->frm->addText('identifier', BackendFormBuilderModel::createIdentifier());
		$this->frm->addEditor('success_message');
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// shorten the fields
			$txtName = $this->frm->getField('name');
			$txtEmail = $this->frm->getField('email');
			$ddmMethod = $this->frm->getField('method');
			$txtSuccessMessage = $this->frm->getField('success_message');
			$txtIdentifier = $this->frm->getField('identifier');

			$emailAddresses = (array) explode(',', $txtEmail->getValue());

			// validate fields
			$txtName->isFilled(BL::getError('NameIsRequired'));
			$txtSuccessMessage->isFilled(BL::getError('SuccessMessageIsRequired'));
			if($ddmMethod->isFilled(BL::getError('NameIsRequired')) && $ddmMethod->getValue() == 'database_email')
			{
				$error = false;

				// check the addresses
				foreach($emailAddresses as $address)
				{
					$address = trim($address);

					if(!SpoonFilter::isEmail($address))
					{
						$error = true;
						break;
					}
				}

				// add error
				if($error) $txtEmail->addError(BL::getError('EmailIsInvalid'));
			}

			// identifier
			if($txtIdentifier->isFilled())
			{
				// invalid characters
				if(!SpoonFilter::isValidAgainstRegexp('/^[a-zA-Z0-9\.\_\-]+$/', $txtIdentifier->getValue())) $txtIdentifier->setError(BL::getError('InvalidIdentifier'));

				// unique identifier
				elseif(BackendFormBuilderModel::existsIdentifier($txtIdentifier->getValue())) $txtIdentifier->setError(BL::getError('UniqueIdentifier'));
			}

			if($this->frm->isCorrect())
			{
				// build array
				$values['language'] = BL::getWorkingLanguage();
				$values['user_id'] = BackendAuthentication::getUser()->getUserId();
				$values['name'] = $txtName->getValue();
				$values['method'] = $ddmMethod->getValue();
				$values['email'] = ($ddmMethod->getValue() == 'database_email') ? serialize($emailAddresses) : null;
				$values['success_message'] = $txtSuccessMessage->getValue(true);
				$values['identifier'] = ($txtIdentifier->isFilled() ? $txtIdentifier->getValue() : BackendFormBuilderModel::createIdentifier());
				$values['created_on'] = BackendModel::getUTCDate();
				$values['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$id = BackendFormBuilderModel::insert($values);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $values));

				// set frontend locale
				FL::setLocale(BL::getWorkingLanguage());

				// create submit button
				$field['form_id'] = $id;
				$field['type'] = 'submit';
				$field['settings'] = serialize(array('values' => SpoonFilter::ucfirst(FL::getLabel('Send'))));
				BackendFormBuilderModel::insertField($field);

				// everything is saved, so redirect to the editform
				$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $id . '&report=added&var=' . urlencode($values['name']) . '#tabFields');
			}
		}
	}
}
