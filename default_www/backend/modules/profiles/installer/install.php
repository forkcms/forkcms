<?php

/**
 * Installer for the profiles module.
 *
 * @package		installer
 * @subpackage	profiles
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class ProfilesInstall extends ModuleInstaller
{
	/**
	 * Install the module.
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'profiles' as a module
		$this->addModule('profiles', 'The profiles module.');

		// module rights
		$this->setModuleRights(1, 'profiles');

		// action rights
		$this->setActionRights(1, 'profiles', 'add_group');
		$this->setActionRights(1, 'profiles', 'add_profile_group');
		$this->setActionRights(1, 'profiles', 'block');
		$this->setActionRights(1, 'profiles', 'delete_group');
		$this->setActionRights(1, 'profiles', 'delete_profile_group');
		$this->setActionRights(1, 'profiles', 'delete');
		$this->setActionRights(1, 'profiles', 'edit_group');
		$this->setActionRights(1, 'profiles', 'edit_profile_group');
		$this->setActionRights(1, 'profiles', 'edit');
		$this->setActionRights(1, 'profiles', 'groups');
		$this->setActionRights(1, 'profiles', 'index');
		$this->setActionRights(1, 'profiles', 'mass_action');

		// add extra
		$activateId = $this->insertExtra('profiles', 'block', 'Activate', 'activate', null, 'N', 5000);
		$forgotPasswordId = $this->insertExtra('profiles', 'block', 'ForgotPassword', 'forgot_password', null, 'N', 5001);
		$indexId = $this->insertExtra('profiles', 'block', 'Dashboard', null, null, 'N', 5002);
		$loginId = $this->insertExtra('profiles', 'block', 'Login', 'login', null, 'N', 5003);
		$logoutId = $this->insertExtra('profiles', 'block', 'Logout', 'logout', null, 'N', 5004);
		$profileEmailId = $this->insertExtra('profiles', 'block', 'ProfileEmail', 'profile_email', null, 'N', 5005);
		$profilePasswordId = $this->insertExtra('profiles', 'block', 'ProfilePassword', 'profile_password', null, 'N', 5006);
		$profileSettingsId = $this->insertExtra('profiles', 'block', 'ProfileSettings', 'profile_settings', null, 'N', 5007);
		$registerId = $this->insertExtra('profiles', 'block', 'Register', 'register', null, 'N', 5008);
		$resetPasswordId = $this->insertExtra('profiles', 'block', 'ResetPassword', 'reset_password', null, 'N', 5008);
		$resendActivationId = $this->insertExtra('profiles', 'block', 'ResendActivation', 'resend_activation', null, 'N', 5009);

		// get search widget id
		$searchId = (int) $this->getDB()->getVar('SELECT id FROM pages_extras WHERE module = ? AND action = ?', array('search', 'form'));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// only add pages if profiles isnt linked anywhere
			if((int) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												INNER JOIN pages_extras AS e ON e.id = b.extra_id
												WHERE e.module = ? AND p.language = ?', array('profiles', $language)) == 0)
			{
				// activate page
				$this->insertPage(array('title' => 'Activate',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $activateId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// forgot password page
				$this->insertPage(array('title' => 'Forgot password',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $forgotPasswordId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// reset password page
				$this->insertPage(array('title' => 'Reset password',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $resetPasswordId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// resend activation email page
				$this->insertPage(array('title' => 'Resend activation e-mail',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $resendActivationId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// login page
				$this->insertPage(array('title' => 'Login',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $loginId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// register page
				$this->insertPage(array('title' => 'Register',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $registerId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// logout page
				$this->insertPage(array('title' => 'Logout',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $logoutId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// index page
				$indexPageId = $this->insertPage(array('title' => 'Profile',
												'type' => 'root',
												'language' => $language),
												null,
												array('html' => ''),
												array('extra_id' => $indexId),
												array('html' => ''),
												array('html' => ''),
												array('html' => ''),
												array('html' => ''),
												array('html' => ''),
												array('html' => ''),
												array('html' => ''),
												array('extra_id' => $searchId));

				// settings page
				$this->insertPage(array('title' => 'Profile settings',
										'parent_id' => $indexPageId,
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $profileSettingsId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// change email page
				$this->insertPage(array('title' => 'Change email',
										'parent_id' => $indexPageId,
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $profileEmailId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));

				// change password page
				$this->insertPage(array('title' => 'Change password',
										'parent_id' => $indexPageId,
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $profilePasswordId),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $searchId));
			}
		}

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>