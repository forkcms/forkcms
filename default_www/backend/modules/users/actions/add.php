<?php

/**
 * UsersAdd
 *
 * This is the add-action, it will display a form to create a new user
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class UsersAdd extends BackendBaseActionAdd
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
		$this->frm = new SpoonForm('add');

		// create elements
		$this->txtUsername = new SpoonTextField('username', null, 255);
		$this->txtPassword = new SpoonPasswordField('password', null, 255);

		$this->txtNickname = new SpoonTextField('nickname', null, 255);
		$this->txtEmail = new SpoonTextField('email', null, 255);
		$this->txtName = new SpoonTextField('name', null, 255);
		$this->txtSurname = new SpoonTextField('surname', null, 255);

		$this->ddmInterfaceLanguages = new SpoonDropDown('interface_language', BackendLanguage::getInterfaceLanguages());

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
					if(BackendUsersModel::existsUsername($this->txtUsername->getValue())) $this->txtUsername->addError('{$errUsernameAlreadyExists|ucfirst}');

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
				$aUser['username'] = $this->txtUsername->getValue(true);
				$aUser['password_raw'] = $this->txtPassword->getValue(true);
				$aUser['password'] = md5($aUser['password_raw']);

				// build settings-array
				$aSettings['nickname'] = $this->txtNickname->getValue();
				$aSettings['email'] = $this->txtEmail->getValue();
				$aSettings['name'] = $this->txtName->getValue();
				$aSettings['surname'] = $this->txtSurname->getValue();
				$aSettings['avatar'] = 'no-avatar.jpg';
				$aSettings['backend_interface_language'] = $this->ddmInterfaceLanguages->getSelected();

				// save changes
				$aUser['id'] = (int) BackendUsersModel::insert($aUser, $aSettings);


				if($this->fileAvatar->isFilled())
				{
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

				// update settings (in this case the avatar)
				BackendUsersModel::update($aUser, $aSettings);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'?report=add&var='. $aUser['username'] .'&hilight=userid-'. $aUser['id']);
			}
		}
	}

}
?>