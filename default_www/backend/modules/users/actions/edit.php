<?php

/**
 * BackendUsersEdit
 *
 * This is the edit-action, it will display a form to alter the user-details and settings
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendUsersEdit extends BackendBaseActionEdit
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

		// does the user exists
		if(BackendUsersModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the user we want to edit
			$this->record = (array) BackendUsersModel::get($this->id);

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the datagrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
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
		$this->frm->addTextField('username', $this->record['username'], 75);
		$this->frm->addPasswordField('new_password', null, 75);
		$this->frm->addPasswordField('confirm_password', null, 75);
		$this->frm->addTextField('nickname', $this->record['settings']['nickname'], 75);
		$this->frm->addTextField('email', $this->record['settings']['email'], 255);
		$this->frm->addTextField('name', $this->record['settings']['name'], 255);
		$this->frm->addTextField('surname', $this->record['settings']['surname'], 255);
		$this->frm->addDropDown('interface_language', BackendLanguage::getInterfaceLanguages(), $this->record['settings']['interface_language']);
		$this->frm->addImageField('avatar');
		$this->frm->addCheckBox('active', ($this->record['active'] == 'Y'));
		$this->frm->addDropDown('group', BackendUsersModel::getGroups(), $this->record['group_id']);
		$this->frm->addButton('edit', BL::getLabel('Edit'), 'submit');

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
		if($this->record['settings']['avatar'] != '') $this->record['settings']['avatar'] .= '?time='. time();

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

			// username is present
			if($this->frm->getField('username')->isFilled(BL::getError('UsernameIsRequired')))
			{
				// only a-z (no spaces) are allowed
				if($this->frm->getField('username')->isAlphaNumeric('{$errOnlyAlphaNumericChars}'))
				{
					// username already exists
					if(BackendUsersModel::existsUsername($this->frm->getField('username')->getValue(), $this->id)) $this->frm->getField('username')->addError('{$errUsernameAlreadyExists}');

					// username doesn't exist
					else
					{
						// some usernames are blacklisted, so don't allow them
						if(in_array($this->frm->getField('username')->getValue(), array('root', 'god', 'netlash'))) $this->frm->getField('username')->addError('{$errUsernameNotAllowed}');
					}
				}
			}

			// required fields
			$this->frm->getField('email')->isEmail(BL::getError('EmailIsInvalid'));
			$this->frm->getField('name')->isFilled(BL::getError('NameIsRequired'));
			$this->frm->getField('surname')->isFilled(BL::getError('SurnameIsRequired'));
			$this->frm->getField('interface_language')->isFilled(BL::getError('InterfaceLanguageIsRequired'));
			if($this->frm->getField('new_password')->isFilled())
			{
				if($this->frm->getField('new_password')->getValue() !== $this->frm->getField('confirm_password')->getValue()) $this->frm->getField('confirm_password')->addError(BL::getError('ValuesDontMatch'));
			}

			// validate avatar
			if($this->frm->getField('avatar')->isFilled())
			{
				// correct extension
				if($this->frm->getField('avatar')->isAllowedExtension(array('jpg', 'jpeg', 'gif'), BL::getError('OnlyJPGAndGifAreAllowed')))
				{
					// correct mimetype?
					$this->frm->getField('avatar')->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg'), BL::getError('OnlyJPGAndGifAreAllowed'));
				}
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build user-array
				$aUser['id'] = $this->id;
				$aUser['username'] = $this->frm->getField('username')->getValue(true);
				if(BackendAuthentication::getUser()->getUserId() != $this->record['id']) $aUser['active'] = ($this->frm->getField('active')->isChecked()) ? 'Y' : 'N';

				// update password (only if filled in)
				if($this->frm->getField('new_password')->isFilled()) $aUser['password'] = BackendAuthentication::getEncryptedString($this->frm->getField('new_password')->getValue(), $this->record['settings']['password_key']);

				// build settings-array
				$aSettings = $this->frm->getValues(array('username', 'password'));

				// is there a file given
				if($this->frm->getField('avatar')->isFilled())
				{
					// delete old avatar if it isn't the default-image
					if($this->record['settings']['avatar'] != 'no-avatar.jpg')
					{
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/source/'. $this->record['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/128x128/'. $this->record['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/64x64/'. $this->record['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/32x32/'. $this->record['settings']['avatar']);
					}

					// create new filename
					$fileName = rand(0,3) .'_'. $aUser['id'] .'.'. $this->frm->getField('avatar')->getExtension();

					// add into settings to update
					$aSettings['avatar'] = $fileName;

					// move to new location
					$this->frm->getField('avatar')->moveFile(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName);

					// resize (128x128)
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 128, 128);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/128x128/'. $fileName);

					// resize (64x64)
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 64, 64);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/64x64/'. $fileName);

					// resize (32x32)
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 32, 32);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/32x32/'. $fileName);
				}

				// save changes
				BackendUsersModel::update($aUser, $aSettings);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=edit&var='. $aUser['username'] .'&highlight=userid-'. $aUser['id']);
			}
		}
	}
}

?>