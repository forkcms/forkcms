<?php

/**
 * BackendAuthenticationIndex
 * This is the index-action (default), it will display the login screen
 *
 * @package		backend
 * @subpackage	authentication
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendAuthenticationIndex extends BackendBaseActionIndex
{
	/**
	 * Form instances
	 *
	 * @var	BackendForm
	 */
	private $frm, $frmForgotPassword;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// check if the user is really logged on
		if(BackendAuthentication::getUser()->isAuthenticated())
		{
			// get the redirect-URL from the URL
			$redirectURL = $this->getParameter('querystring');

			// if there isn't a redirect URL we will redirect to the dashboard
			if($redirectURL === null) $redirectURL = BackendModel::createUrlForAction(null, 'dashboard');

			// redirect to the correct URL (URL the user was looking for or fallback)
			$this->redirect($redirectURL);
		}

		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load form
		$this->load();

		// validate the form
		$this->validate();

		// parse the error
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function load()
	{
		// create the login form
		$this->frm = new BackendForm();

		// create elements and add to the form
		$this->frm->addText('backend_email');
		$this->frm->addPassword('backend_password');

		// create form for forgot password
		$this->frmForgotPassword = new BackendForm('forgotPassword');

		// create elements and add to the form
		$this->frmForgotPassword->addText('backend_email');
	}


	/**
	 * Parse the action into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
		$this->frmForgotPassword->parse($this->tpl);
	}


	/**
	 * Validate the forms
	 *
	 * @return	void
	 */
	private function validate()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// redefine fields
			$txtEmail = $this->frm->getField('backend_email');
			$txtPassword = $this->frm->getField('backend_password');

			// required fields
			$txtEmail->isFilled(BL::getError('EmailIsRequired'));
			$txtPassword->isFilled(BL::getError('PasswordIsRequired'));

			// all fields are ok?
			if($txtEmail->isFilled() && $txtPassword->isFilled() && $this->frm->getToken() == $this->frm->getField('form_token')->getValue())
			{
				// try to login the user
				if(!BackendAuthentication::loginUser($txtEmail->getValue(), $txtPassword->getValue()))
				{
					// add error
					$this->frm->addError('invalid login');

					// show error
					$this->tpl->assign('hasError', true);
				}
			}

			// no errors in the form?
			if($this->frm->isCorrect())
			{
				// get the redirect-URL from the URL
				$redirectURL = $this->getParameter('querystring');

				// if there isn't a redirect URL we will redirect to the dashboard
				if($redirectURL === null) $redirectURL = BackendModel::createUrlForAction(null, 'dashboard');

				// redirect to the correct URL (URL the user was looking for or fallback)
				$this->redirect($redirectURL);
			}
		}

		// is the form submitted
		if($this->frmForgotPassword->isSubmitted())
		{
			// backend email
			$email = $this->frmForgotPassword->getField('backend_email')->getValue();

			// required fields
			if($this->frmForgotPassword->getField('backend_email')->isEmail(BL::getError('EmailIsInvalid')))
			{
				// check if there is a user with the given emailaddress
				if(!BackendUsersModel::existsEmail($email)) $this->frmForgotPassword->getField('backend_email')->addError(BL::getError('EmailIsUnknown'));
			}

			// no errors in the form?
			if($this->frmForgotPassword->isCorrect())
			{
				// generate the key for the reset link and fetch the user ID for this email
				$key = BackendAuthentication::getEncryptedString($email, uniqid());

				// insert the key and the timestamp into the user settings
				$userId = BackendUsersModel::getIdByEmail($email);
				$user = new BackendUser($userId);
				$user->setSetting('reset_password_key', $key);
				$user->setSetting('reset_password_timestamp', time());

				// variables to parse in the e-mail
				$variables['resetLink'] = SITE_URL . BackendModel::createURLForAction('reset_password') .'&email='. $email .'&key='. $key;

				// send e-mail to user
				BackendMailer::addEmail(ucfirst(BL::getMessage('ResetYourPasswordMailSubject')), BACKEND_MODULE_PATH .'/layout/templates/mails/reset_password.tpl', $variables, $email);

				// clear post-values
				$_POST['backend_email'] = '';

				// show success message
				$this->tpl->assign('isForgotPasswordSuccess', true);

				// show form
				$this->tpl->assign('showForm', true);
			}

			// errors?
			else
			{
				// show form
				$this->tpl->assign('showForm', true);
			}
		}

	}
}

?>