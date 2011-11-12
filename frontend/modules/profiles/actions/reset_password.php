<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Reset your password using a token received from the forgot_password action.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendProfilesResetPassword extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance.
	 *
	 * @var	FrontendForm
	 */
	private $frm;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// get reset key
		$key = $this->URL->getParameter(0);

		// do we have an reset key?
		if(isset($key))
		{
			// load parent
			parent::execute();

			// load template
			$this->loadTemplate();

			// get profile id
			$profileId = FrontendProfilesModel::getIdBySetting('forgot_password_key', $key);

			// have id?
			if($profileId !== 0)
			{
				// load
				$this->loadForm();

				// validate
				$this->validateForm();
			}

			// invalid key
			elseif($this->URL->getParameter('sent') != 'true') $this->redirect(FrontendNavigation::getURL(404));

			// parse
			$this->parse();
		}

		// no key set
		else $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Load the form.
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new FrontendForm('resetPassword', null, null, 'resetPasswordForm');

		// create & add elements
		$this->frm->addPassword('password', null, null, 'inputText showPasswordInput');
		$this->frm->addCheckbox('show_password');
	}

	/**
	 * Parse the data into the template.
	 */
	private function parse()
	{
		// has the password been saved?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('resetPasswordSuccess', true);

			// hide form
			$this->tpl->assign('resetPasswordHideForm', true);
		}

		// parse the form
		else $this->frm->parse($this->tpl);
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
			$txtPassword = $this->frm->getField('password');

			// field is filled in?
			$txtPassword->isFilled(FL::getError('PasswordIsRequired'));

			// valid
			if($this->frm->isCorrect())
			{
				// get profile id
				$profileId = FrontendProfilesModel::getIdBySetting('forgot_password_key', $this->URL->getParameter(0));

				// remove key (we can only update the password once with this key)
				FrontendProfilesModel::deleteSetting($profileId, 'forgot_password_key');

				// update password
				FrontendProfilesAuthentication::updatePassword($profileId, $txtPassword->getValue());

				// login (check again because we might have logged in in the meanwhile)
				if(!FrontendProfilesAuthentication::isLoggedIn()) FrontendProfilesAuthentication::login($profileId);

				// trigger event
				FrontendModel::triggerEvent('profiles', 'after_reset_password', array('id' => $profileId));

				// redirect
				$this->redirect(FrontendNavigation::getURLForBlock('profiles', 'reset_password') . '/' . $this->URL->getParameter(0) . '?sent=true');
			}

			// show errors
			else $this->tpl->assign('forgotPasswordHasError', true);
		}
	}
}
