<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new user
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendUsersAdd extends BackendBaseActionAdd
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
		// create form
		$this->frm = new BackendForm('add');

		// get the groups
		$groups = BackendGroupsModel::getAll();

		// if there is only one group we can check it so the user isn't bothered with an error for not selecting one
		$checkedGroups = (count($groups) == 1) ? $groups[0]['value'] : null;

		// create elements
		// profile
		$this->frm->addText('email', null, 255);
		$this->frm->addPassword('password', null, 75, 'inputText inputPassword passwordGenerator', 'inputTextError inputPasswordError passwordGenerator')->setAttributes(array('autocomplete' => 'off'));
		$this->frm->addPassword('confirm_password', null, 75)->setAttributes(array('autocomplete' => 'off'));
		$this->frm->addText('name', null, 255);
		$this->frm->addText('surname', null, 255);
		$this->frm->addText('nickname', null, 24);
		$this->frm->addImage('avatar');

		$this->frm->addDropdown('interface_language', BackendLanguage::getInterfaceLanguages(), BackendModel::getModuleSetting('core', 'default_interface_language'));
		$this->frm->addDropdown('date_format', BackendUsersModel::getDateFormats(), BackendAuthentication::getUser()->getSetting('date_format'));
		$this->frm->addDropdown('time_format', BackendUsersModel::getTimeFormats(), BackendAuthentication::getUser()->getSetting('time_format'));
		$this->frm->addDropdown('number_format', BackendUsersModel::getNumberFormats(), BackendAuthentication::getUser()->getSetting('number_format', 'dot_nothing'));

		$this->frm->addDropDown('csv_split_character', BackendUsersModel::getCSVSplitCharacters());
		$this->frm->addDropDown('csv_line_ending', BackendUsersModel::getCSVLineEndings());

		// permissons
		$this->frm->addCheckbox('active', true);
		$this->frm->addCheckbox('api_access', false);
		$this->frm->addMultiCheckbox('groups', $groups, $checkedGroups);
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

			// email is present
			if($this->frm->getField('email')->isFilled(BL::err('EmailIsRequired')))
			{
				// is this an email-address
				if($this->frm->getField('email')->isEmail(BL::err('EmailIsInvalid')))
				{
					// was this emailaddress deleted before
					if(BackendUsersModel::emailDeletedBefore($this->frm->getField('email')->getValue())) $this->frm->getField('email')->addError(sprintf(BL::err('EmailWasDeletedBefore'), BackendModel::createURLForAction('undo_delete', null, null, array('email' => $this->frm->getField('email')->getValue()))));

					else
					{
						// email already exists
						if(BackendUsersModel::existsEmail($this->frm->getField('email')->getValue())) $this->frm->getField('email')->addError(BL::err('EmailAlreadyExists'));
					}
				}
			}

			// required fields
			$this->frm->getField('password')->isFilled(BL::err('PasswordIsRequired'));
			$this->frm->getField('nickname')->isFilled(BL::err('NicknameIsRequired'));
			$this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));
			$this->frm->getField('surname')->isFilled(BL::err('SurnameIsRequired'));
			$this->frm->getField('interface_language')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('date_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('time_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('number_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('groups')->isFilled(BL::err('FieldIsRequired'));
			if($this->frm->getField('password')->isFilled())
			{
				if($this->frm->getField('password')->getValue() !== $this->frm->getField('confirm_password')->getValue()) $this->frm->getField('confirm_password')->addError(BL::err('ValuesDontMatch'));
			}

			// validate avatar
			if($this->frm->getField('avatar')->isFilled())
			{
				// correct extension
				if($this->frm->getField('avatar')->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly')))
				{
					// correct mimetype?
					$this->frm->getField('avatar')->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
				}
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build settings-array
				$settings['nickname'] = $this->frm->getField('nickname')->getValue();
				$settings['name'] = $this->frm->getField('name')->getValue();
				$settings['surname'] = $this->frm->getField('surname')->getValue();
				$settings['interface_language'] = $this->frm->getField('interface_language')->getValue();
				$settings['date_format'] = $this->frm->getField('date_format')->getValue();
				$settings['time_format'] = $this->frm->getField('time_format')->getValue();
				$settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
				$settings['number_format'] = $this->frm->getField('number_format')->getValue();
				$settings['csv_split_character'] = $this->frm->getField('csv_split_character')->getValue();
				$settings['csv_line_ending'] = $this->frm->getField('csv_line_ending')->getValue();
				$settings['password_key'] = uniqid();
				$settings['avatar'] = 'no-avatar.gif';
				$settings['api_access'] = (bool) $this->frm->getField('api_access')->getChecked();

				// get selected groups
				$groups = $this->frm->getField('groups')->getChecked();

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

				// build user-array
				$user['email'] = $this->frm->getField('email')->getValue();
				$user['password'] = BackendAuthentication::getEncryptedString($this->frm->getField('password')->getValue(true), $settings['password_key']);

				// save changes
				$user['id'] = (int) BackendUsersModel::insert($user, $settings);

				// has the user submitted an avatar?
				if($this->frm->getField('avatar')->isFilled())
				{
					// create new filename
					$filename = rand(0,3) . '_' . $user['id'] . '.' . $this->frm->getField('avatar')->getExtension();

					// add into settings to update
					$settings['avatar'] = $filename;

					// resize (128x128)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/backend_users/avatars/128x128/' . $filename, 128, 128, true, false, 100);

					// resize (64x64)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/backend_users/avatars/64x64/' . $filename, 64, 64, true, false, 100);

					// resize (32x32)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/backend_users/avatars/32x32/' . $filename, 32, 32, true, false, 100);
				}

				// update settings (in this case the avatar)
				BackendUsersModel::update($user, $settings);

				// save groups
				BackendGroupsModel::insertMultipleGroups($user['id'], $groups);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $user));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . $settings['nickname'] . '&highlight=row-' . $user['id']);
			}
		}
	}
}
