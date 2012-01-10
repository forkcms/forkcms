<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Change the settings for the current logged in profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendProfilesSettings extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * The current profile.
	 *
	 * @var FrontendProfilesProfile
	 */
	private $profile;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// profile logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			parent::execute();
			$this->getData();
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Get profile data.
	 */
	private function getData()
	{
		// get profile
		$this->profile = FrontendProfilesAuthentication::getProfile();
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		// gender dropdown values
		$genderValues = array(
			'male' => SpoonFilter::ucfirst(FL::getLabel('Male')),
			'female' => SpoonFilter::ucfirst(FL::getLabel('Female'))
		);

		// birthdate dropdown values
		$days = range(1, 31);
		$months = SpoonLocale::getMonths(FRONTEND_LANGUAGE);
		$years = range(date('Y'), 1900);

		// get settings
		$birthDate = $this->profile->getSetting('birth_date');
		$nameChanges = (int) $this->profile->getSetting('display_name_changes');

		// get day, month and year
		if($birthDate) list($birthYear, $birthMonth, $birthDay) = explode('-', $birthDate);

		// no birth date setting
		else
		{
			$birthDay = '';
			$birthMonth = '';
			$birthYear = '';
		}

		// create the form
		$this->frm = new FrontendForm('updateSettings', null, null, 'updateSettingsForm');

		// create & add elements
		$this->frm->addImage('avatar');
		$this->frm->addText('display_name', $this->profile->getDisplayName());
		$this->frm->addText('first_name', $this->profile->getSetting('first_name'));
		$this->frm->addText('last_name', $this->profile->getSetting('last_name'));
		$this->frm->addText('email', $this->profile->getEmail());
		$this->frm->addText('city', $this->profile->getSetting('city'));
		$this->frm->addDropdown('country', SpoonLocale::getCountries(FRONTEND_LANGUAGE), $this->profile->getSetting('country'));
		$this->frm->addDropdown('gender', $genderValues, $this->profile->getSetting('gender'));
		$this->frm->addDropdown('day', array_combine($days, $days), $birthDay);
		$this->frm->addDropdown('month', $months, $birthMonth);
		$this->frm->addDropdown('year', array_combine($years, $years), (int) $birthYear);

		// set default elements dropdowns
		$this->frm->getField('gender')->setDefaultElement('');
		$this->frm->getField('day')->setDefaultElement('');
		$this->frm->getField('month')->setDefaultElement('');
		$this->frm->getField('year')->setDefaultElement('');
		$this->frm->getField('country')->setDefaultElement('');

		// set email disabled
		$this->frm->getField('email')->setAttribute('disabled', 'disabled');

		// when user exceeded the number of name changes set field disabled
		if($nameChanges >= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES) $this->frm->getField('display_name')->setAttribute('disabled', 'disabled');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// have the settings been saved?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show success message
			$this->tpl->assign('updateSettingsSuccess', true);
		}

		// parse the form
		$this->frm->parse($this->tpl);

		// display name changes
		$this->tpl->assign('maxDisplayNameChanges', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES);
		$this->tpl->assign('displayNameChangesLeft', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES - $this->profile->getSetting('display_name_changes'));
	}

	/**
	 * Validate the form.
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// get fields
			$txtDisplayName = $this->frm->getField('display_name');
			$txtFirstName = $this->frm->getField('first_name');
			$txtLastName = $this->frm->getField('last_name');
			$txtCity = $this->frm->getField('city');
			$ddmCountry = $this->frm->getField('country');
			$ddmGender = $this->frm->getField('gender');
			$ddmDay = $this->frm->getField('day');
			$ddmMonth = $this->frm->getField('month');
			$ddmYear = $this->frm->getField('year');

			// get number of display name changes
			$nameChanges = (int) FrontendProfilesModel::getSetting($this->profile->getId(), 'display_name_changes');

			// has there been a valid display name change request?
			if($this->profile->getDisplayName() !== $txtDisplayName->getValue() && $nameChanges <= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES)
			{
				// display name filled in?
				if($txtDisplayName->isFilled(FL::getError('FieldIsRequired')))
				{
					// display name exists?
					if(FrontendProfilesModel::existsDisplayName($txtDisplayName->getValue(), $this->profile->getId()))
					{
						// set error
						$txtDisplayName->addError(FL::getError('DisplayNameExists'));
					}
				}
			}

			// birthdate is not required but if one is filled we need all
			if($ddmMonth->isFilled() || $ddmDay->isFilled() || $ddmYear->isFilled())
			{
				// valid birth date?
				if(!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue()))
				{
					// set error
					$ddmYear->addError(FL::getError('DateIsInvalid'));
				}
			}

			// no errors
			if($this->frm->isCorrect())
			{
				// init
				$values = array();

				// has there been a valid display name change request?
				if($this->profile->getDisplayName() !== $txtDisplayName->getValue() && $nameChanges <= FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES)
				{
					// get display name value
					$values['display_name'] = $txtDisplayName->getValue();

					// update url based on the new display name
					$values['url'] = FrontendProfilesModel::getUrl($txtDisplayName->getValue(), $this->profile->getId());

					// update display name count
					$this->profile->setSetting('display_name_changes', $nameChanges + 1);
				}

				// update values
				if(!empty($values)) FrontendProfilesModel::update($this->profile->getId(), $values);

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
				$this->profile->setSetting('first_name', $txtFirstName->getValue());
				$this->profile->setSetting('last_name', $txtLastName->getValue());
				$this->profile->setSetting('city', $txtCity->getValue());
				$this->profile->setSetting('country', $ddmCountry->getValue());
				$this->profile->setSetting('gender', $ddmGender->getValue());
				$this->profile->setSetting('birth_date', $birthDate);

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_saved_settings', array('id' => $this->profile->getId()));

				// redirect
				$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'settings') . '?sent=true');
			}

			// show errors
			else $this->tpl->assign('updateSettingsHasFormError', true);
		}
	}
}
