<?php

/**
 * This is the edit-action, it will display a form to alter the user-details and settings
 *
 * @package		backend
 * @subpackage	users
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendUsersEdit extends BackendBaseActionEdit
{
	/**
	 * The user
	 *
	 * @var	BackendUser
	 */
	private $user;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the user exists
		if($this->id !== null && BackendUsersModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the user we want to edit
			$this->record = (array) BackendUsersModel::get($this->id);

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create user object
		$this->user = new BackendUser($this->id);

		// create form
		$this->frm = new BackendForm('edit');

		// get active groups
		$groups = BackendGroupsModel::getGroupsByUser($this->id);

		// loop through groups and set checked
		foreach($groups as $group) $checkedGroups[] = $group['id'];

		// create elements
		$this->frm->addText('email', $this->record['email'], 255);
		if($this->user->isGod()) $this->frm->getField('email')->setAttributes(array('disabled' => 'disabled'));
		$this->frm->addPassword('new_password', null, 75);
		$this->frm->addPassword('confirm_password', null, 75);
		$this->frm->addText('nickname', $this->record['settings']['nickname'], 24);
		$this->frm->addText('name', $this->record['settings']['name'], 255);
		$this->frm->addText('surname', $this->record['settings']['surname'], 255);
		$this->frm->addDropdown('interface_language', BackendLanguage::getInterfaceLanguages(), $this->record['settings']['interface_language']);
		$this->frm->addDropdown('date_format', BackendUsersModel::getDateFormats(), $this->user->getSetting('date_format'));
		$this->frm->addDropdown('time_format', BackendUsersModel::getTimeFormats(), $this->user->getSetting('time_format'));
		$this->frm->addDropdown('number_format', BackendUsersModel::getNumberFormats(), $this->user->getSetting('number_format', 'dot_nothing'));
		$this->frm->addImage('avatar');
		$this->frm->addCheckbox('api_access', (isset($this->record['settings']['api_access']) && $this->record['settings']['api_access'] == 'Y'));
		$this->frm->addCheckbox('active', ($this->record['active'] == 'Y'));
		$this->frm->addMultiCheckbox('groups', BackendGroupsModel::getAll(), $checkedGroups);

		// disable autocomplete
		$this->frm->getField('new_password')->setAttributes(array('autocomplete' => 'off'));
		$this->frm->getField('confirm_password')->setAttributes(array('autocomplete' => 'off'));

		// disable active field for current users
		if(BackendAuthentication::getUser()->getUserId() == $this->record['id']) $this->frm->getField('active')->setAttribute('disabled', 'disabled');
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// reset avatar URL
		if($this->record['settings']['avatar'] != '') $this->record['settings']['avatar'] .= '?time=' . time();

		// only allow deletion of other users
		if(BackendAuthentication::getUser()->getUserId() != $this->id) $this->tpl->assign('deleteAllowed', true);

		// assign
		$this->tpl->assign('record', $this->record);
		$this->tpl->assign('id', $this->id);
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

			// email is present
			if(!$this->user->isGod())
			{
				if($this->frm->getField('email')->isFilled(BL::err('EmailIsRequired')))
				{
					// is this an email-address
					if($this->frm->getField('email')->isEmail(BL::err('EmailIsInvalid')))
					{
						// was this emailaddress deleted before
						if(BackendUsersModel::emailDeletedBefore($this->frm->getField('email')->getValue()))
						{
							$this->frm->getField('email')->addError(sprintf(BL::err('EmailWasDeletedBefore'), BackendModel::createURLForAction('undo_delete', null, null, array('email' => $this->frm->getField('email')->getValue()))));
						}

						// email already exists
						elseif(BackendUsersModel::existsEmail($this->frm->getField('email')->getValue(), $this->id))
						{
							$this->frm->getField('email')->addError(BL::err('EmailAlreadyExists'));
						}
					}
				}
			}

			// required fields
			if($this->user->isGod() && $this->frm->getField('email')->getValue() != '' && $this->user->getEmail() != $this->frm->getField('email')->getValue()) $this->frm->getField('email')->addError(BL::err('CantChangeGodsEmail'));
			if(!$this->user->isGod()) $this->frm->getField('email')->isEmail(BL::err('EmailIsInvalid'));
			$this->frm->getField('nickname')->isFilled(BL::err('NicknameIsRequired'));
			$this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));
			$this->frm->getField('surname')->isFilled(BL::err('SurnameIsRequired'));
			$this->frm->getField('interface_language')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('date_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('time_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('number_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('groups')->isFilled(BL::err('FieldIsRequired'));
			if($this->frm->getField('new_password')->isFilled())
			{
				if($this->frm->getField('new_password')->getValue() !== $this->frm->getField('confirm_password')->getValue()) $this->frm->getField('confirm_password')->addError(BL::err('ValuesDontMatch'));
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
				// build user-array
				$user['id'] = $this->id;
				if(!$this->user->isGod()) $user['email'] = $this->frm->getField('email')->getValue(true);
				if(BackendAuthentication::getUser()->getUserId() != $this->record['id']) $user['active'] = ($this->frm->getField('active')->isChecked()) ? 'Y' : 'N';

				// update password (only if filled in)
				if($this->frm->getField('new_password')->isFilled()) $user['password'] = BackendAuthentication::getEncryptedString($this->frm->getField('new_password')->getValue(), $this->record['settings']['password_key']);

				// build settings-array
				$settings['nickname'] = $this->frm->getField('nickname')->getValue();
				$settings['name'] = $this->frm->getField('name')->getValue();
				$settings['surname'] = $this->frm->getField('surname')->getValue();
				$settings['interface_language'] = $this->frm->getField('interface_language')->getValue();
				$settings['date_format'] = $this->frm->getField('date_format')->getValue();
				$settings['time_format'] = $this->frm->getField('time_format')->getValue();
				$settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
				$settings['number_format'] = $this->frm->getField('number_format')->getValue();
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

				// has the user submitted an avatar?
				if($this->frm->getField('avatar')->isFilled())
				{
					// delete old avatar if it isn't the default-image
					if($this->record['settings']['avatar'] != 'no-avatar.jpg')
					{
						SpoonFile::delete(FRONTEND_FILES_PATH . '/backend_users/avatars/source/' . $this->record['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/backend_users/avatars/128x128/' . $this->record['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/backend_users/avatars/64x64/' . $this->record['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/backend_users/avatars/32x32/' . $this->record['settings']['avatar']);
					}

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

				// save changes
				BackendUsersModel::update($user, $settings);

				// save groups
				BackendGroupsModel::insertMultipleGroups($this->id, $groups);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . $settings['nickname'] . '&highlight=row-' . $user['id']);
			}
		}
	}
}

?>