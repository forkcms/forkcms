<?php

/**
 * This is the profile password-action.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesProfilePassword extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;


	/**
	 * The current profile.
	 *
	 * @var FrontendProfilesProfile
	 */
	private $profile;


	/**
	 * Execute the extra.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// profile logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// load parent
			parent::execute();

			// get data
			$this->getData();

			// load template
			$this->loadTemplate();

			// load
			$this->loadForm();

			// validate
			$this->validateForm();

			// parse
			$this->parse();
		}

		// profile not logged in
		else $this->redirect(FrontendNavigation::getURL(404));
	}


	/**
	 * Get profile data.
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get profile
		$this->profile = FrontendProfilesAuthentication::getProfile();
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new FrontendForm('updatePassword', null, null, 'updatePasswordForm');

		// create & add elements
		$this->frm->addPassword('old_password');
		$this->frm->addPassword('new_password', null, null, 'inputText showPasswordInput');
		$this->frm->addCheckbox('show_password');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// have the settings been saved?
		if($this->URL->getParameter('saved') == 'true')
		{
			// show success message
			$this->tpl->assign('updatePasswordSuccess', true);
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
			// get fields
			$txtOldPassword = $this->frm->getField('old_password');
			$txtNewPassword = $this->frm->getField('new_password');

			// old password filled in?
			if($txtOldPassword->isFilled(FL::getError('PasswordIsRequired')))
			{
				// old password correct?
				if(FrontendProfilesAuthentication::getLoginStatus($this->profile->getEmail(), $txtOldPassword->getValue()) !== FrontendProfilesAuthentication::LOGIN_ACTIVE)
				{
					// set error
					$txtOldPassword->addError(FL::getError('InvalidPassword'));
				}

				// new password filled in?
				$txtNewPassword->isFilled(FL::getError('PasswordIsRequired'));
			}

			// no errors
			if($this->frm->isCorrect())
			{
				// update password
				FrontendProfilesAuthentication::updatePassword($this->profile->getId(), $txtNewPassword->getValue());

				// redirect
				$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'profile_password') . '?saved=true');
			}

			// show errors
			else $this->tpl->assign('updatePasswordHasFormError', true);
		}
	}
}

?>
