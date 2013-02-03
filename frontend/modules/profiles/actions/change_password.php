<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Change the password of the current logged in profile.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendProfilesChangePassword extends FrontendBaseBlock
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
	 */
	public function execute()
	{
		// profile logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			parent::execute();
			$this->getData();
			$this->loadTemplate();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// profile not logged in
		else
		{
			$this->redirect(
				FrontendNavigation::getURLForBlock('profiles', 'login') . '?queryString=' . FrontendNavigation::getURLForBlock('profiles', 'change_password'),
				307
			);
		}
	}

	/**
	 * Get profile data.
	 */
	private function getData()
	{
		// get profile
		$this->profile = FrontendProfilesAuthentication::getProfile();
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		$this->frm = new FrontendForm('updatePassword', null, null, 'updatePasswordForm');
		$this->frm->addPassword('old_password')->setAttributes(array('required' => null));
		$this->frm->addPassword('new_password', null, null, 'inputText showPasswordInput')->setAttributes(array('required' => null));
		$this->frm->addCheckbox('show_password');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// have the settings been saved?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show success message
			$this->tpl->assign('updatePasswordSuccess', true);
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

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_change_password', array('id' => $this->profile->getId()));

				// redirect
				$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('profiles', 'change_password') . '?sent=true');
			}

			// show errors
			else $this->tpl->assign('updatePasswordHasFormError', true);
		}
	}
}
