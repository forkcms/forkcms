<?php

/**
 * This is the reset password action, it will display a form that allows the user to reset his/her password.
 *
 * @package		backend
 * @subpackage	authentication
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendAuthenticationResetPassword extends BackendBaseActionAdd
{
	/**
	 * Form instance
	 *
	 * @var	BackendForm
	 */
	protected $frm;


	/**
	 * User email
	 *
	 * @var	$email
	 */
	private $email;


	/**
	 * Reset password key
	 *
	 * @var	$key
	 */
	private $key;


	/**
	 * User record
	 *
	 * @return	array
	 */
	private $user;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// the user email and key provided match
		if(!$this->isUserAllowed()) $this->redirect(BackendModel::createURLForAction('index'));

		// load form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the error
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * The user is allowed on this page
	 *
	 * @return	bool
	 */
	private function isUserAllowed()
	{
		// catch the key and e-mail address from GET
		$this->email = urldecode(SpoonFilter::getGetValue('email', null, ''));
		$this->key = SpoonFilter::getGetValue('key', null, '');

		// if the email or the key aren't set, redirect the user
		if($this->email !== '' && $this->key !== '')
		{
			// fetch the user
			$userId = BackendUsersModel::getIdByEmail($this->email);
			$this->user = new BackendUser($userId);
			$requestTime = $this->user->getSetting('reset_password_timestamp');

			// check if the request was made within 24 hours
			if((time() - $requestTime) > 86400)
			{
				// remove the reset_password_key and reset_password_timestamp usersettings
				BackendUsersModel::deleteResetPasswordSettings($userId);

				// redirect to the login form, with a timeout error
				$this->redirect(BackendModel::createURLForAction('index', null, null, array('reset' => 'timeout')));
			}

			// check if the provided key matches the one in the user record
			if($this->key === $this->user->getSetting('reset_password_key')) return true;
		}

		// if we made it here the user is not allowed to access this page
		return false;
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new BackendForm();

		// create elements and add to the form
		$this->frm->addPassword('backend_new_password');
		$this->frm->addPassword('backend_new_password_repeated');

		$this->frm->getField('backend_new_password')->setAttributes(array('autocomplete' => 'off'));
		$this->frm->getField('backend_new_password_repeated')->setAttributes(array('autocomplete' => 'off'));
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// shorten fields
			$newPassword = $this->frm->getField('backend_new_password');
			$newPasswordRepeated = $this->frm->getField('backend_new_password_repeated');

			// required fields
			$newPassword->isFilled(BL::err('PasswordIsRequired'));
			$newPasswordRepeated->isFilled(BL::err('PasswordRepeatIsRequired'));

			// all fields are ok?
			if($newPassword->isFilled() && $newPasswordRepeated->isFilled())
			{
				// the passwords entered match
				if($newPassword->getValue() !== $newPasswordRepeated->getValue())
				{
					// add error
					$this->frm->addError(BL::err('PasswordsDontMatch'));

					// show error
					$this->tpl->assign('error', BL::err('PasswordsDontMatch'));
				}
			}

			// is the form submitted
			if($this->frm->isCorrect())
			{
				// change the users password
				BackendUsersModel::updatePassword($this->user, $newPassword->getValue());

				// attempt to login the user
				if(!BackendAuthentication::loginUser($this->user->getEmail(), $newPassword->getValue()))
				{
					// redirect to the login form with an error
					$this->redirect(BackendModel::createURLForAction('index', null, null, array('login' => 'failed')));
				}

				// redirect to the login form
				$this->redirect(BackendModel::createUrlForAction('index', 'dashboard', null, array('password_reset' => 'success')));
			}
		}
	}
}

?>