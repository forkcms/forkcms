<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit an existing profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendProfilesEdit extends BackendBaseActionEdit
{
	/**
	 * Info about the current profile.
	 *
	 * @var array
	 */
	private $profile;

	/**
	 * Groups data grid.
	 *
	 * @var	BackendDataGrid
	 */
	private $dgGroups;

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist?
		if($this->id !== null && BackendProfilesModel::exists($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadGroups();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, redirect to index, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the profile data.
	 */
	private function getData()
	{
		// get general info
		$this->profile = BackendProfilesModel::get($this->id);
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

		// get settings
		$birthDate = BackendProfilesModel::getSetting($this->id, 'birth_date');

		// get day, month and year
		if($birthDate) list($birthYear, $birthMonth, $birthDay) = explode('-', $birthDate);

		// no birth date setting
		else
		{
			$birthDay = '';
			$birthMonth = '';
			$birthYear = '';
		}

		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('email', $this->profile['email']);
		$this->frm->addPassword('password');
		$this->frm->addText('display_name', $this->profile['display_name']);
		$this->frm->addText('first_name', BackendProfilesModel::getSetting($this->id, 'first_name'));
		$this->frm->addText('last_name', BackendProfilesModel::getSetting($this->id, 'last_name'));
		$this->frm->addText('city', BackendProfilesModel::getSetting($this->id, 'city'));
		$this->frm->addDropdown('gender', $genderValues, BackendProfilesModel::getSetting($this->id, 'gender'));
		$this->frm->addDropdown('day', array_combine($days, $days), $birthDay);
		$this->frm->addDropdown('month', $months, $birthMonth);
		$this->frm->addDropdown('year', array_combine($years, $years), (int) $birthYear);
		$this->frm->addDropdown('country', SpoonLocale::getCountries(BL::getInterfaceLanguage()), BackendProfilesModel::getSetting($this->id, 'country'));

		// set default elements dropdowns
		$this->frm->getField('gender')->setDefaultElement('');
		$this->frm->getField('day')->setDefaultElement('');
		$this->frm->getField('month')->setDefaultElement('');
		$this->frm->getField('year')->setDefaultElement('');
		$this->frm->getField('country')->setDefaultElement('');
	}

	/**
	 * Load the data grid with groups.
	 */
	private function loadGroups()
	{
		// create the data grid
		$this->dgGroups = new BackendDataGridDB(BackendProfilesModel::QRY_DATAGRID_BROWSE_PROFILE_GROUPS, array($this->profile['id']));

		// sorting columns
		$this->dgGroups->setSortingColumns(array('group_name'), 'group_name');

		// disable paging
		$this->dgGroups->setPaging(false);

		// set column function
		$this->dgGroups->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[expires_on]'), 'expires_on', true);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit_profile_group'))
		{
			// set column URLs
			$this->dgGroups->setColumnURL('group_name', BackendModel::createURLForAction('edit_profile_group') . '&amp;id=[id]&amp;profile_id=' . $this->id);

			// edit column
			$this->dgGroups->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_profile_group') . '&amp;id=[id]&amp;profile_id=' . $this->id, BL::getLabel('Edit'));
		}
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('profile', $this->profile);

		// parse data grids
		$this->tpl->assign('dgGroups', ($this->dgGroups->getNumResults() != 0) ? $this->dgGroups->getContent() : false);

		// show delete or undelete button?
		if($this->profile['status'] === 'deleted') $this->tpl->assign('deleted', true);

		// show block or unblock button?
		if($this->profile['status'] === 'blocked') $this->tpl->assign('blocked', true);
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
					if(BackendProfilesModel::existsByEmail($txtEmail->getValue(), $this->id))
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
				if(BackendProfilesModel::existsDisplayName($txtDisplayName->getValue(), $this->id))
				{
					// set error
					$txtDisplayName->addError(BL::getError('DisplayNameExists'));
				}
			}

			// one of the bday fields are filled in
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
				$values['email'] = $txtEmail->getValue();

				// only update if display name changed
				if($txtDisplayName->getValue() != $this->profile['display_name'])
				{
					$values['display_name'] = $txtDisplayName->getValue();
					$values['url'] = BackendProfilesModel::getUrl($txtDisplayName->getValue(), $this->id);
				}

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
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $values));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=saved&var=' . urlencode($values['email']) . '&highlight=row-' . $this->id);
			}
		}
	}
}
