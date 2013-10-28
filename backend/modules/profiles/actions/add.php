<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to add a new profile.
 *
 * @author Davy Van Vooren <davy.vanvooren@netlash.com>
 */
class BackendProfilesAdd extends BackendBaseActionAdd
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * Execute the action.
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
		// gender dropdown values
		$genderValues = array(
			'male' => SpoonFilter::ucfirst(BL::getLabel('Male')),
			'female' => SpoonFilter::ucfirst(BL::getLabel('Female'))
		);

		// birthdate dropdown values
		$days = range(1, 31);
		$months = SpoonLocale::getMonths(BL::getInterfaceLanguage());
		$years = range(date('Y'), 1900);

		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('email');
		$this->frm->addPassword('password');
		$this->frm->addText('display_name');
		$this->frm->addText('first_name');
		$this->frm->addText('last_name');
		$this->frm->addText('city');
		$this->frm->addDropdown('gender', $genderValues);
		$this->frm->addDropdown('day', array_combine($days, $days));
		$this->frm->addDropdown('month', $months);
		$this->frm->addDropdown('year', array_combine($years, $years));
		$this->frm->addDropdown('country', SpoonLocale::getCountries(BL::getInterfaceLanguage()));

		// set default elements dropdowns
		$this->frm->getField('gender')->setDefaultElement('');
		$this->frm->getField('day')->setDefaultElement('');
		$this->frm->getField('month')->setDefaultElement('');
		$this->frm->getField('year')->setDefaultElement('');
		$this->frm->getField('country')->setDefaultElement('');
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

			// get fields
			$txtEmail = $this->frm->getField('email');
			$txtDisplayName = $this->frm->getField('display_name');
			$txtPassword = $this->frm->getField('password');
			$txtFirstName = $this->frm->getField('first_name');
			$txtLastName = $this->frm->getField('last_name');
			$txtCity = $this->frm->getField('city');
			$ddmGender = $this->frm->getField('gender');
			$ddmDay = $this->frm->getField('day');
			$ddmMonth = $this->frm->getField('month');
			$ddmYear = $this->frm->getField('year');
			$ddmCountry = $this->frm->getField('country');

			// email filled in?
			if($txtEmail->isFilled(BL::getError('EmailIsRequired')))
			{
				// valid email?
				if($txtEmail->isEmail(BL::getError('EmailIsInvalid')))
				{
					// email already exists?
					if(BackendProfilesModel::existsByEmail($txtEmail->getValue()))
					{
						// set error
						$txtEmail->addError(BL::getError('EmailExists'));
					}
				}
			}

			// display name filled in?
			if($txtDisplayName->isFilled(BL::getError('DisplayNameIsRequired')))
			{
				// display name already exists?
				if(BackendProfilesModel::existsDisplayName($txtDisplayName->getValue()))
				{
					// set error
					$txtDisplayName->addError(BL::getError('DisplayNameExists'));
				}
			}

			// one of the birthday fields are filled in
			if($ddmDay->isFilled() || $ddmMonth->isFilled() || $ddmYear->isFilled())
			{
				// valid date?
				if(!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue()))
				{
					// set error
					$ddmYear->addError(BL::getError('DateIsInvalid'));
				}
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$values = array(
					'email' => $txtEmail->getValue(),
					'registered_on' => BackendModel::getUTCDate(),
					'display_name' => $txtDisplayName->getValue(),
					'url' => BackendProfilesModel::getUrl($txtDisplayName->getValue())
				);

				$this->id = BackendProfilesModel::insert($values);

				// new password filled in?
				if($txtPassword->isFilled())
				{
					// get new salt
					$salt = BackendProfilesModel::getRandomString();

					// update salt
					BackendProfilesModel::setSetting($this->id, 'salt', $salt);

					// build password
					$values['password'] = BackendProfilesModel::getEncryptedString($txtPassword->getValue(), $salt);
				}

				// update values
				BackendProfilesModel::update($this->id, $values);

				// bday is filled in
				if($ddmYear->isFilled())
				{
					// mysql format
					$birthDate = $ddmYear->getValue() . '-';
					$birthDate .= str_pad($ddmMonth->getValue(), 2, '0', STR_PAD_LEFT) . '-';
					$birthDate .= str_pad($ddmDay->getValue(), 2, '0', STR_PAD_LEFT);
				}

				// not filled in
				else $birthDate = null;

				// update settings
				BackendProfilesModel::setSetting($this->id, 'first_name', $txtFirstName->getValue());
				BackendProfilesModel::setSetting($this->id, 'last_name', $txtLastName->getValue());
				BackendProfilesModel::setSetting($this->id, 'gender', $ddmGender->getValue());
				BackendProfilesModel::setSetting($this->id, 'birth_date', $birthDate);
				BackendProfilesModel::setSetting($this->id, 'city', $txtCity->getValue());
				BackendProfilesModel::setSetting($this->id, 'country', $ddmCountry->getValue());

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $values));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $this->id);
			}
		}
	}
}
