<?php

/**
 * Register a profile.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesRegister extends FrontendBaseBlock
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
		// load parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// profile not logged in
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			// load
			$this->loadForm();

			// validate
			$this->validateForm();

			// parse
			$this->parse();
		}

		// just registered so show success message
		elseif($this->URL->getParameter('sent') == true) $this->parse();

		// already logged in, so you can not register
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
		$this->frm = new FrontendForm('register', null, null, 'registerForm');

		// create & add elements
		$this->frm->addText('email');
		$this->frm->addPassword('password', null, null, 'inputText showPasswordInput');
		$this->frm->addCheckbox('show_password');
	}


	/**
	 * Parse the data into the template.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// e-mail was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('registerIsSuccess', true);

			// hide form
			$this->tpl->assign('registerHideForm', true);
		}

		// parse the form
		else $this->frm->parse($this->tpl);
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
			$txtEmail = $this->frm->getField('email');
			$txtPassword = $this->frm->getField('password');

			// check email
			if($txtEmail->isFilled(FL::getError('EmailIsRequired')))
			{
				// valid email?
				if($txtEmail->isEmail(FL::getError('EmailIsInvalid')))
				{
					// email already exists?
					if(FrontendProfilesModel::existsByEmail($txtEmail->getValue()))
					{
						// set error
						$txtEmail->setError(FL::getError('EmailExists'));
					}
				}
			}

			// check password
			$txtPassword->isFilled(FL::getError('PasswordIsRequired'));

			// no errors
			if($this->frm->isCorrect())
			{
				// generate salt
				$salt = FrontendProfilesModel::getRandomString();

				// init values
				$values = array();

				// values
				$values['email'] = $txtEmail->getValue();
				$values['password'] = FrontendProfilesModel::getEncryptedString($txtPassword->getValue(), $salt);
				$values['status'] = 'inactive';
				$values['display_name'] = $txtEmail->getValue();
				$values['registered_on'] = FrontendModel::getUTCDate();

				/*
				 * Add a profile.
				 * We use a try-catch statement to catch errors when more users sign up simultaneously.
				 */
				try
				{
					// insert profile
					$profileId = FrontendProfilesModel::insert($values);

					// use the profile id as url until we have an actual url
					FrontendProfilesModel::update($profileId, array('url' => FrontendProfilesModel::getUrl($profileId)));

					// generate activation key
					$activationKey = FrontendProfilesModel::getEncryptedString($profileId . microtime(), $salt);

					// set settings
					FrontendProfilesModel::setSetting($profileId, 'salt', $salt);
					FrontendProfilesModel::setSetting($profileId, 'activation_key', $activationKey);

					// login
					FrontendProfilesAuthentication::login($profileId);

					// activation URL
					$mailValues['activationUrl'] = SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'activate') . '/' . $activationKey;

					// send email
					FrontendMailer::addEmail(FL::getMessage('RegisterSubject'), FRONTEND_MODULES_PATH . '/profiles/layout/templates/mails/register.tpl', $mailValues, $values['email'], $values['email']);

					// redirect
					$this->redirect(SELF . '?sent=true');
				}

				// catch exceptions
				catch(Exception $e)
				{
					// when debugging we need to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// show error
					$this->tpl->assign('registerHasFormError', true);
				}
			}

			// show errors
			else $this->tpl->assign('registerHasFormError', true);
		}
	}
}

?>
