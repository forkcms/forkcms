<?php

/**
 * This is the profile email-action.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesProfileEmail extends FrontendBaseBlock
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
		$this->frm = new FrontendForm('updateEmail', null, null, 'updateEmailForm');

		// create & add elements
		$this->frm->addPassword('password');
		$this->frm->addText('email', $this->profile->getEmail());
	}


	/**
	 * Parse the data into the template.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// have the settings been saved?
		if($this->URL->getParameter('saved') == 'true')
		{
			// show success message
			$this->tpl->assign('updateEmailSuccess', true);
		}

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
			$txtPassword = $this->frm->getField('password');
			$txtEmail = $this->frm->getField('email');

			// password filled in?
			if($txtPassword->isFilled(FL::getError('PasswordIsRequired')))
			{
				// password correct?
				if(FrontendProfilesAuthentication::getLoginStatus($this->profile->getEmail(), $txtPassword->getValue()) !== FrontendProfilesAuthentication::LOGIN_ACTIVE)
				{
					// set error
					$txtPassword->addError(FL::getError('InvalidPassword'));
				}

				// email filled in?
				if($txtEmail->isFilled(FL::getError('EmailIsRequired')))
				{
					// valid email?
					if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
					{
						// email already exists?
						if(FrontendProfilesModel::existsByEmail($txtEmail->getValue(), $this->profile->getId()))
						{
							// set error
							$txtEmail->setError(FL::getError('EmailExists'));
						}
					}
				}
			}

			// no errors
			if($this->frm->isCorrect())
			{
				// update email
				FrontendProfilesModel::update($this->profile->getId(), array('email' => $txtEmail->getValue()));

				// redirect
				$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'profile_email') . '?saved=true');
			}

			// show errors
			else $this->tpl->assign('updateEmailHasFormError', true);
		}
	}
}

?>
