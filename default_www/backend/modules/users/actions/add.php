<?php

/**
 * BackendUsersAdd
 *
 * This is the add-action, it will display a form to create a new user
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendUsersAdd extends BackendBaseActionAdd
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

		// parse the datagrid
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
		$this->frm->addTextField('username', null, 75);
		$this->frm->addPasswordField('password', null, 75);
		$this->frm->addTextField('nickname', null, 75);
		$this->frm->addTextField('email', null, 255);
		$this->frm->addTextField('name', null, 255);
		$this->frm->addTextField('surname', null, 255);
		$this->frm->addDropDown('interface_language', BackendLanguage::getInterfaceLanguages());
		$this->frm->addImageField('avatar');
		$this->frm->addButton('add', ucfirst(BL::getLabel('Add')), 'submit');
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
				if($this->frm->getField('username')->isAlphaNumeric('{$errOnlyAlphaNumericChars|ucfirst}'))
				{
					// username already exists
					if(BackendUsersModel::existsUsername($this->frm->getField('username')->getValue())) $this->frm->getField('username')->addError('{$errUsernameAlreadyExists}');

					// username doesn't exist
					else
					{
						// some usernames are blacklisted, so don't allow them
						if(in_array($this->frm->getField('username')->getValue(), array('root', 'god', 'netlash'))) $this->frm->getField('username')->addError('{$errUsernameNotAllowed}');
					}
				}
			}

			// required fields
			$this->frm->getField('password')->isFilled(BL::getError('PasswordIsRequired'));
			$this->frm->getField('email')->isEmail(BL::getError('EmailIsInvalid'));
			$this->frm->getField('name')->isFilled(BL::getError('NameIsRequired'));
			$this->frm->getField('surname')->isFilled(BL::getError('SurnameIsRequired'));
			$this->frm->getField('interface_language')->isFilled(BL::getError('InterfaceLanguageIsRequired'));

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
				$aUser['username'] = $this->frm->getField('username')->getValue(true);
				$aUser['password'] = md5($this->frm->getField('password')->getValue(true));

				// build settings-array
				$aSettings = $this->frm->getValues(array('username', 'password'));
				$aSettings['password_key'] = uniqid();

				// save changes
				$aUser['id'] = (int) BackendUsersModel::insert($aUser, $aSettings);

				// does the user submitted an avatar
				if($this->frm->getField('avatar')->isFilled())
				{
					// create new filename
					$fileName = rand(0,3) .'_'. $aUser['id'] .'.'. $this->frm->getField('avatar')->getExtension();

					// add into settings to update
					$aSettings['avatar'] = $fileName;

					// move to new location
					$this->frm->getField('avatar')->moveFile(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName);

					// resize (128x128)
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 128, 128);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/128x128/'. $fileName);

					// resize (64x64)
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 64, 64);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/64x64/'. $fileName);

					// resize (32x32)
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 32, 32);
					$thumbnail->setAllowEnlargement(true);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/32x32/'. $fileName);
				}

				// update settings (in this case the avatar)
				BackendUsersModel::update($aUser, $aSettings);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=add&var='. $aUser['username'] .'&highlight=userid-'. $aUser['id']);
			}
		}
	}
}

?>