<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the resend activation-action. It will resend your activation email.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendProfilesResendActivation extends FrontendBaseBlock
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// profile not logged in
		if(!FrontendProfilesAuthentication::isLoggedIn())
		{
			parent::execute();
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile logged in
		else $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new FrontendForm('resendActivation', null, null, 'resendActivation');

		// create & add elements
		$this->frm->addText('email');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// form was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('resendActivationSuccess', true);

			// hide form
			$this->tpl->assign('resendActivationHideForm', true);
		}

		// parse the form
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validate the form
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
					if(FrontendProfilesModel::existsByEmail($txtEmail->getValue()))
					{
						// get profile id using the filled in email
						$profileId = FrontendProfilesModel::getIdByEmail($txtEmail->getValue());

						// get profile
						$profile = FrontendProfilesModel::get($profileId);

						// must be inactive
						if($profile->getStatus() != FrontendProfilesAuthentication::LOGIN_INACTIVE) $txtEmail->addError(FL::getError('ProfileIsActive'));
					}

					// email no existo
					else $txtEmail->addError(FL::getError('EmailIsInvalid'));
				}
			}

			// valid login
			if($this->frm->isCorrect())
			{
				// activation URL
				$mailValues['activationUrl'] = SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'activate') . '/' . $profile->getSetting('activation_key');

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_resend_activation', array('id' => $profileId));

				// send email
				FrontendMailer::addEmail(
					FL::getMessage('RegisterSubject'),
					FRONTEND_MODULES_PATH . '/profiles/layout/templates/mails/register.tpl',
					$mailValues,
					$profile->getEmail(),
					''
				);

				// redirect
				$this->redirect(SELF . '?sent=true');
			}

			// show errors
			else $this->tpl->assign('resendActivationHasError', true);
		}
	}
}
