<?php

/**
 * This is the forgotPassword-action.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesForgotPassword extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;


	/**
	 * Execute the extra.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// only for guests
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			// load parent
			parent::execute();

			// load template
			$this->loadTemplate();

			// load
			$this->loadForm();

			// validate
			$this->validateForm();

			// parse
			$this->parse();
		}

		// already logged in, redirect to settings
		else $this->redirect(FrontendNavigation::getURLForBlock('profiles', 'profile_settings'));
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new FrontendForm('forgotPassword', null, null, 'forgotPasswordForm');

		// create & add elements
		$this->frm->addText('email');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// e-mail was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('forgotPasswordSuccess', true);

			// hide form
			$this->tpl->assign('forgotPasswordHideForm', true);
		}

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
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// get field
			$txtEmail = $this->frm->getField('email');

			// field is filled in?
			if($txtEmail->isFilled(FL::getError('EmailIsRequired')))
			{
				// valid email?
				if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
				{
					// email exists?
					if(!FrontendProfilesModel::existsByEmail($txtEmail->getValue()))
					{
						$txtEmail->addError(FL::getError('EmailIsUnknown'));
					}
				}
			}

			// valid login
			if($this->frm->isCorrect())
			{
				// get profile id
				$profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

				// generate forgot password key
				$key = FrontendProfilesModel::getEncryptedString($profileId . microtime(), FrontendProfilesModel::getRandomString());

				// insert forgot password key
				FrontendProfilesModel::setSetting($profileId, 'forgot_password_key', $key);

				// reset url
				$mailValues['resetUrl'] = SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'reset_password') . '/' . $key;
				$mailValues['firstName'] = FrontendProfilesModel::getSetting($profileId, 'first_name');
				$mailValues['lastName'] = FrontendProfilesModel::getSetting($profileId, 'last_name');

				// send email
				FrontendMailer::addEmail(FL::getMessage('ForgotPasswordSubject'), FRONTEND_MODULES_PATH . '/profiles/layout/templates/mails/forgot_password.tpl', $mailValues, $txtEmail->getValue(), $txtEmail->getValue());

				// redirect
				$this->redirect(SELF . '?sent=true');
			}

			// show errors
			else $this->tpl->assign('forgotPasswordHasError', true);
		}
	}
}

?>
