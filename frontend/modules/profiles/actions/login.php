<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the login-action.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();

 		// profile not logged in
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile already logged in
		else
		{
			// querystring
			$queryString = urldecode(SpoonFilter::getGetValue('queryString', null, SITE_URL));

			// redirect
			$this->redirect($queryString);
		}
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('login', null, null, 'loginForm');
		$this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
		$this->frm->addPassword('password')->setAttributes(array('required' => null));
		$this->frm->addCheckbox('remember', true);
	}

	/**
	 * Parse the data into the template.
	 */
	private function parse()
	{
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validate the form.
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
						$errorString = sprintf(FL::getError('Profiles' . SpoonFilter::toCamelCase($loginStatus) . 'Login'), FrontendNavigation::getURLForBlock('profiles', 'resend_activation'));

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

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_logged_in', array('id' => $profileId));

				// querystring
				$queryString = urldecode(SpoonFilter::getGetValue('queryString', null, SITE_URL));

				// redirect
				$this->redirect($queryString);
			}
		}
	}
}
