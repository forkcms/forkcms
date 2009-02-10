<?php

/**
 * UsersEdit
 *
 * This is the edit-action, it will display a form to alter the user-details and settings
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class UsersEdit extends BackendBaseActionEdit
{
	/**
	 * Textfields
	 *
	 * @var	SpoonTextField
	 */
	private $txtUsername, $txtNickname, $txtName, $txtSurname, $txtEmail;


	/**
	 * Passwordfield
	 *
	 * @var	SpoonPasswordField
	 */
	private $txtPassword;


	/**
	 * Dropdownmenu
	 *
	 * @var	SpoonDropDown
	 */
	private $ddmInterfaceLanguages;


	/**
	 * Filefield
	 *
	 * @var SpoonFileField
	 */
	private $fileAvatar;


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
			$this->aRecord = (array) BackendUsersModel::get($this->id);

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the datagrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no user found, throw an exceptions, because somebody is fucking with our url
		// @todo	redirect to index with error-message
		else throw new BackendException('Userid ('. $this->id .') doesn\'t exists.');
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new SpoonForm('edit');

		// create elements
		$this->txtUsername = new SpoonTextField('username', $this->aRecord['username'], 255);
		$this->txtPassword = new SpoonPasswordField('password', $this->aRecord['password_raw'], 255);

		$this->txtNickname = new SpoonTextField('nickname', $this->aRecord['settings']['nickname'], 255);
		$this->txtEmail = new SpoonTextField('email', $this->aRecord['settings']['email'], 255);
		$this->txtName = new SpoonTextField('name', $this->aRecord['settings']['name'], 255);
		$this->txtSurname = new SpoonTextField('surname', $this->aRecord['settings']['surname'], 255);

		$this->ddmInterfaceLanguages = new SpoonDropDown('interface_language', BackendLanguage::getInterfaceLanguages(), $this->aRecord['settings']['backend_interface_language']);

		$this->fileAvatar = new SpoonFileField('avatar');

		// add elements
		$this->frm->add($this->txtUsername, $this->txtPassword,
						$this->txtNickname, $this->txtEmail, $this->txtName, $this->txtSurname,
						$this->fileAvatar,
						$this->ddmInterfaceLanguages);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	private function parse()
	{
		// show current avatar
		$this->tpl->assign('avatarImage', FRONTEND_FILES_URL. '/backend_users/avatars/64x64/'. $this->aRecord['settings']['avatar']);

		// assign user-related data
		$this->tpl->assign('nickname', $this->aRecord['settings']['nickname']);
		$this->tpl->assign('id', $this->aRecord['id']);

		// parse the form
		$this->frm->parse($this->tpl);
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
			if($this->txtUsername->isFilled(BL::getError('UsernameIsRequired')))
			{
				// only a-z (no spaces) are allowed
				if($this->txtUsername->isAlphaNumeric('{$errOnlyAlphaNumericChars|ucfirst}'))
				{
					// does the username already exists?
					if(BackendUsersModel::existsUsername($this->txtUsername->getValue(), $this->id)) $this->txtUsername->addError('{$errUsernameAlreadyExists|ucfirst}');

					// the username doesn't exists
					else
					{
						// some usernames are blacklisted, so don't allow them
						if(in_array($this->txtUsername->getValue(), array('root', 'god', 'netlash'))) $this->txtUsername->addError('{$errUsernameNotAllowed|ucfirst}');
					}
				}
			}

			// required fields
			$this->txtPassword->isFilled(BL::getError('PasswordIsRequired'));
			$this->txtEmail->isEmail(BL::getError('EmailIsInvalid'));
			$this->txtName->isFilled(BL::getError('NameIsRequired'));
			$this->txtSurname->isFilled(BL::getError('SurnameIsRequired'));
			$this->ddmInterfaceLanguages->isFilled(BL::getError('InterfaceLanguageIsRequired'));

			// validate fields
			if($this->fileAvatar->isFilled())
			{
				$this->fileAvatar->isAllowedExtension(array('jpg', 'jpeg', 'gif'), BL::getError('OnlyJPGAndGifAreAllowed'));
			}

			// no errors?
			if($this->frm->getCorrect())
			{
				// build user-array
				$aUser['id'] = $this->id;
				$aUser['username'] = $this->txtUsername->getValue(true);
				$aUser['password_raw'] = $this->txtPassword->getValue(true);
				$aUser['password'] = md5($aUser['password_raw']);

				// build settings-array
				$aSettings['nickname'] = $this->txtNickname->getValue();
				$aSettings['email'] = $this->txtEmail->getValue();
				$aSettings['name'] = $this->txtName->getValue();
				$aSettings['surname'] = $this->txtSurname->getValue();
				$aSettings['backend_interface_language'] = $this->ddmInterfaceLanguages->getSelected();

				if($this->fileAvatar->isFilled())
				{
					// delete old avatar if it isn't the default-image
					if($this->aRecord['settings']['avatar'] != 'no-avatar.jpg')
					{
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/source/'. $this->aRecord['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/128x128/'. $this->aRecord['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/64x64/'. $this->aRecord['settings']['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH .'/backend_users_avatars/32x32/'. $this->aRecord['settings']['avatar']);
					}

					// create new filename
					$fileName = rand(0,3) .'_'. $aUser['id'] .'.'. $this->fileAvatar->getExtension();

					// add into settings to update
					$aSettings['avatar'] = $fileName;

					// move to new location
					$this->fileAvatar->moveFile(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName);

					// resize
					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 128, 128);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/128x128/'. $fileName);

					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 64, 64);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/64x64/'. $fileName);

					$thumbnail = new SpoonThumbnail(FRONTEND_FILES_PATH .'/backend_users/avatars/source/'. $fileName, 32, 32);
					$thumbnail->setForceOriginalAspectRatio(false);
					$thumbnail->parseToFile(FRONTEND_FILES_PATH .'/backend_users/avatars/32x32/'. $fileName);
				}

				// save changes
				BackendUsersModel::update($aUser, $aSettings);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'?report=edit&var='. $aUser['username'] .'&hilight=userid-'. $aUser['id']);
			}
		}
	}

}
?>