<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to alter the user-details and settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendUsersEdit extends BackendBaseActionEdit
{
	/**
	 * The authenticated user
	 *
	 * @var	BackendUser
	 */
	private $authenticatedUser;

	/**
	 * The user
	 *
	 * @var	BackendUser
	 */
	private $user;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the user exists
		if($this->id !== null && BackendUsersModel::exists($this->id))
		{
			parent::execute();
			$this->record = (array) BackendUsersModel::get($this->id);
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create user objects
		$this->user = new BackendUser($this->id);
		$this->authenticatedUser = BackendAuthentication::getUser();

		// create form
		$this->frm = new BackendForm('edit');

		// get active groups
		$groups = BackendGroupsModel::getGroupsByUser($this->id);

		// loop through groups and set checked
		foreach($groups as $group) $checkedGroups[] = $group['id'];

		// create elements
		// profile
		$this->frm->addText('email', $this->record['email'], 255);
		if($this->user->isGod()) $this->frm->getField('email')->setAttributes(array('disabled' => 'disabled'));
		$this->frm->addText('name', $this->record['settings']['name'], 255);
		$this->frm->addText('surname', $this->record['settings']['surname'], 255);
		$this->frm->addText('nickname', $this->record['settings']['nickname'], 24);
		$this->frm->addImage('avatar');

		// password
		// check if we're god or same user
		if($this->authenticatedUser->getUserId() == $this->id || $this->authenticatedUser->isGod())
		{
			// allow to set new password
			$this->frm->addPassword('new_password', null, 75);
			$this->frm->addPassword('confirm_password', null, 75);

			// disable autocomplete
			$this->frm->getField('new_password')->setAttributes(array('autocomplete' => 'off'));
			$this->frm->getField('confirm_password')->setAttributes(array('autocomplete' => 'off'));
		}

		// settings
		$this->frm->addDropdown('interface_language', BackendLanguage::getInterfaceLanguages(), $this->record['settings']['interface_language']);
		$this->frm->addDropdown('date_format', BackendUsersModel::getDateFormats(), $this->user->getSetting('date_format'));
		$this->frm->addDropdown('time_format', BackendUsersModel::getTimeFormats(), $this->user->getSetting('time_format'));
		$this->frm->addDropdown('number_format', BackendUsersModel::getNumberFormats(), $this->user->getSetting('number_format', 'dot_nothing'));

		$this->frm->addDropDown('csv_split_character', BackendUsersModel::getCSVSplitCharacters(), $this->user->getSetting('csv_split_character'));
		$this->frm->addDropDown('csv_line_ending', BackendUsersModel::getCSVLineEndings(), $this->user->getSetting('csv_line_ending'));

		// permissions
		$this->frm->addCheckbox('active', ($this->record['active'] == 'Y'));

		// disable active field for current users
		if($this->authenticatedUser->getUserId() == $this->record['id']) $this->frm->getField('active')->setAttribute('disabled', 'disabled');
		$this->frm->addCheckbox('api_access', (isset($this->record['settings']['api_access']) && $this->record['settings']['api_access'] == 'Y'));
		$this->frm->addMultiCheckbox('groups', BackendGroupsModel::getAll(), $checkedGroups);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// reset avatar URL
		if($this->record['settings']['avatar'] != '') $this->record['settings']['avatar'] .= '?time=' . time();

		// only allow deletion of other users
		$this->tpl->assign('showUsersDelete', $this->authenticatedUser->getUserId() != $this->id && BackendAuthentication::isAllowedAction('delete'));

		// assign
		$this->tpl->assign('record', $this->record);
		$this->tpl->assign('id', $this->id);

		// assign that we're god or the same user
		$this->tpl->assign('allowPasswordEdit', ($this->authenticatedUser->getUserId() == $this->id || $this->authenticatedUser->isGod()));

		// check if we need to show the password strength and parse the label
		$this->tpl->assign('showPasswordStrength', ($this->record['settings']['password_strength'] !== 'strong'));
		$this->tpl->assign('passwordStrengthLabel', BL::lbl($this->record['settings']['password_strength']));
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
			$fields = $this->frm->getFields();

			// email is present
			if(!$this->user->isGod())
			{
				if($fields['email']->isFilled(BL::err('EmailIsRequired')))
				{
					// is this an email-address
					if($fields['email']->isEmail(BL::err('EmailIsInvalid')))
					{
						// was this emailaddress deleted before
						if(BackendUsersModel::emailDeletedBefore($fields['email']->getValue()))
						{
							$fields['email']->addError(sprintf(BL::err('EmailWasDeletedBefore'), BackendModel::createURLForAction('undo_delete', null, null, array('email' => $fields['email']->getValue()))));
						}

						// email already exists
						elseif(BackendUsersModel::existsEmail($fields['email']->getValue(), $this->id))
						{
							$fields['email']->addError(BL::err('EmailAlreadyExists'));
						}
					}
				}
			}

			// required fields
			if($this->user->isGod() && $fields['email']->getValue() != '' && $this->user->getEmail() != $fields['email']->getValue()) $fields['email']->addError(BL::err('CantChangeGodsEmail'));
			if(!$this->user->isGod()) $fields['email']->isEmail(BL::err('EmailIsInvalid'));
			$fields['nickname']->isFilled(BL::err('NicknameIsRequired'));
			$fields['name']->isFilled(BL::err('NameIsRequired'));
			$fields['surname']->isFilled(BL::err('SurnameIsRequired'));
			$fields['interface_language']->isFilled(BL::err('FieldIsRequired'));
			$fields['date_format']->isFilled(BL::err('FieldIsRequired'));
			$fields['time_format']->isFilled(BL::err('FieldIsRequired'));
			$fields['number_format']->isFilled(BL::err('FieldIsRequired'));
			$fields['groups']->isFilled(BL::err('FieldIsRequired'));
			if(isset($fields['new_password']) && $fields['new_password']->isFilled())
			{
				if($fields['new_password']->getValue() !== $fields['confirm_password']->getValue()) $fields['confirm_password']->addError(BL::err('ValuesDontMatch'));
			}

			// validate avatar
			if($fields['avatar']->isFilled())
			{
				// correct extension
				if($fields['avatar']->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly')))
				{
					// correct mimetype?
					$fields['avatar']->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
				}

			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build user-array
				$user['id'] = $this->id;
				if(!$this->user->isGod()) $user['email'] = $fields['email']->getValue(true);
				if($this->authenticatedUser->getUserId() != $this->record['id']) $user['active'] = ($fields['active']->isChecked()) ? 'Y' : 'N';

				// build settings-array
				$settings['nickname'] = $fields['nickname']->getValue();
				$settings['name'] = $fields['name']->getValue();
				$settings['surname'] = $fields['surname']->getValue();
				$settings['interface_language'] = $fields['interface_language']->getValue();
				$settings['date_format'] = $fields['date_format']->getValue();
				$settings['time_format'] = $fields['time_format']->getValue();
				$settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
				$settings['number_format'] = $fields['number_format']->getValue();
				$settings['csv_split_character'] = $fields['csv_split_character']->getValue();
				$settings['csv_line_ending'] = $fields['csv_line_ending']->getValue();
				$settings['api_access'] = (bool) $fields['api_access']->getChecked();

				// update password (only if filled in)
				if(isset($fields['new_password']) && $fields['new_password']->isFilled())
				{
					$user['password'] = BackendAuthentication::getEncryptedString($fields['new_password']->getValue(), $this->record['settings']['password_key']);

					// the password has changed
					if($this->record['password'] != $user['password'])
					{
						// save the login timestamp in the user's settings
						$lastPasswordChange = BackendUsersModel::getSetting($user['id'], 'current_password_change');
						$settings['current_password_change'] = time();
						if($lastPasswordChange) $settings['last_password_change'] = $lastPasswordChange;

						// save the password strength
						$passwordStrength = BackendAuthentication::checkPassword($fields['new_password']->getValue());
						$settings['password_strength'] = $passwordStrength;
					}
				}

				// get selected groups
				$groups = $fields['groups']->getChecked();

				// init var
				$newSequence = BackendGroupsModel::getSetting($groups[0], 'dashboard_sequence');

				// loop through groups and collect all dashboard widget sequences
				foreach($groups as $group) $sequences[] = BackendGroupsModel::getSetting($group, 'dashboard_sequence');

				// loop through sequences
				foreach($sequences as $sequence)
				{
					// loop through modules inside a sequence
					foreach($sequence as $moduleKey => $module)
					{
						// loop through widgets inside a module
						foreach($module as $widgetKey => $widget)
						{
							// if widget present set true
							if($widget['present']) $newSequence[$moduleKey][$widgetKey]['present'] = true;
						}
					}
				}

				// add new sequence to settings
				$settings['dashboard_sequence'] = $newSequence;

				// has the user submitted an avatar?
				if($fields['avatar']->isFilled())
				{
					// init vars
					$avatarsPath = FRONTEND_FILES_PATH . '/backend_users/avatars';

					// delete old avatar if it isn't the default-image
					if($this->record['settings']['avatar'] != 'no-avatar.jpg')
					{
						SpoonFile::delete($avatarsPath . '/source/' . $this->record['settings']['avatar']);
						SpoonFile::delete($avatarsPath . '/128x128/' . $this->record['settings']['avatar']);
						SpoonFile::delete($avatarsPath . '/64x64/' . $this->record['settings']['avatar']);
						SpoonFile::delete($avatarsPath . '/32x32/' . $this->record['settings']['avatar']);
					}

					// create new filename
					$filename = rand(0,3) . '_' . $user['id'] . '.' . $fields['avatar']->getExtension();

					// add into settings to update
					$settings['avatar'] = $filename;

					// resize (128x128)
					$fields['avatar']->createThumbnail($avatarsPath . '/128x128/' . $filename, 128, 128, true, false, 100);

					// resize (64x64)
					$fields['avatar']->createThumbnail($avatarsPath . '/64x64/' . $filename, 64, 64, true, false, 100);

					// resize (32x32)
					$fields['avatar']->createThumbnail($avatarsPath . '/32x32/' . $filename, 32, 32, true, false, 100);
				}

				// save changes
				BackendUsersModel::update($user, $settings);

				// save groups
				BackendGroupsModel::insertMultipleGroups($this->id, $groups);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $user));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . $settings['nickname'] . '&highlight=row-' . $user['id']);
			}
		}
	}
}
