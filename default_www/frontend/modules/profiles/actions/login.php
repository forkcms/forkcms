<?php

/**
 * This is the login-action.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesLogin extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;


	/**
	 * Execute.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load parent
		parent::execute();

 		// profile not logged in
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			// load template
			$this->loadTemplate();

			// load
			$this->loadForm();

			// validate
			$this->validateForm();

			// parse
			$this->parse();
		}

		// profile already logged in
		else $this->redirect(SITE_URL);
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new FrontendForm('login', null, null, 'loginForm');

		// create & add elements
		$this->frm->addText('email');
		$this->frm->addPassword('password');
		$this->frm->addCheckbox('remember', true);
	}


	/**
	 * Parse the data into the template.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
	}


	/**
	 * Validate the form.
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// get fields
			$txtEmail = $this->frm->getField('email');
			$txtPassword = $this->frm->getField('password');
			$chkRemember = $this->frm->getField('remember');

			// required fields
			$txtEmail->isFilled(FL::getError('EmailIsRequired'));
			$txtPassword->isFilled(FL::getError('PasswordIsRequired'));

			// both fields filled in
			if($txtEmail->isFilled() && $txtPassword->isFilled())
			{
				// valid email?
				if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
				{
					// get the status for the given login
					$loginStatus = FrontendProfilesAuthentication::getLoginStatus($txtEmail->getValue(), $txtPassword->getValue());

					// valid login?
					if($loginStatus !== FrontendProfilesAuthentication::LOGIN_ACTIVE)
					{
						// get the error string to use
						$errorString = FL::getError('Profiles' . SpoonFilter::toCamelCase($loginStatus) . 'Login');

						// add the error to stack
						$this->frm->addError($errorString);

						// add the error to the template variables
						$this->tpl->assign('loginError', $errorString);
					}
				}
			}

			// valid login
			if($this->frm->isCorrect())
			{
				// get profile id
				$profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

				// login
				FrontendProfilesAuthentication::login($profileId, $chkRemember->getChecked());

				// update salt and password for Dieter's security features
				FrontendProfilesAuthentication::updatePassword($profileId, $txtPassword->getValue());

				// querystring
				$queryString = urldecode(SpoonFilter::getGetValue('queryString', null, SITE_URL));

				// redirect
				$this->redirect($queryString);
			}
		}
	}
}

?>
