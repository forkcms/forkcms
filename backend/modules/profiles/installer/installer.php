<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the profiles module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Davy Van Vooren <davy.vanvooren@netlash.com>
 */
class ProfilesInstaller extends ModuleInstaller
{
	/**
	 * Install the module.
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'profiles' as a module
		$this->addModule('profiles');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'profiles');

		// action rights
		$this->setActionRights(1, 'profiles', 'add');
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

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationProfilesId = $this->setNavigation($navigationModulesId, 'Profiles');
		$this->setNavigation($navigationProfilesId, 'Overview', 'profiles/index', array(
			'profiles/add',
			'profiles/edit',
			'profiles/add_profile_group',
			'profiles/edit_profile_group'
		));
		$this->setNavigation($navigationProfilesId, 'Groups', 'profiles/groups', array(
			'profiles/add_group',
			'profiles/edit_group'
		));

		// add extra
		$activateId = $this->insertExtra('profiles', 'block', 'Activate', 'activate', null, 'N', 5000);
		$forgotPasswordId = $this->insertExtra('profiles', 'block', 'ForgotPassword', 'forgot_password', null, 'N', 5001);
		$indexId = $this->insertExtra('profiles', 'block', 'Dashboard', null, null, 'N', 5002);
		$loginId = $this->insertExtra('profiles', 'block', 'Login', 'login', null, 'N', 5003);
		$logoutId = $this->insertExtra('profiles', 'block', 'Logout', 'logout', null, 'N', 5004);
		$changeEmailId = $this->insertExtra('profiles', 'block', 'ChangeEmail', 'change_email', null, 'N', 5005);
		$changePasswordId = $this->insertExtra('profiles', 'block', 'ChangePassword', 'change_password', null, 'N', 5006);
		$settingsId = $this->insertExtra('profiles', 'block', 'Settings', 'settings', null, 'N', 5007);
		$registerId = $this->insertExtra('profiles', 'block', 'Register', 'register', null, 'N', 5008);
		$resetPasswordId = $this->insertExtra('profiles', 'block', 'ResetPassword', 'reset_password', null, 'N', 5008);
		$resendActivationId = $this->insertExtra('profiles', 'block', 'ResendActivation', 'resend_activation', null, 'N', 5009);

		$this->insertExtra('profiles', 'widget', 'LoginBox', 'login_box', null, 'N', 5010);

		// get search widget id
		$searchId = (int) $this->getDB()->getVar('SELECT id FROM modules_extras WHERE module = ? AND action = ?', array('search', 'form'));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// only add pages if profiles isn't linked anywhere
			// @todo refactor me, syntax sucks atm
			if(!(bool) $this->getDB()->getVar(
				'SELECT 1
				 FROM pages AS p
				 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
				 INNER JOIN modules_extras AS e ON e.id = b.extra_id
				 WHERE e.module = ? AND p.language = ?
				 LIMIT 1',
				array('profiles', $language)))
			{
				// activate page
				$this->insertPage(
					array(
						'title' => 'Activate',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $activateId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// forgot password page
				$this->insertPage(
					array(
						'title' => 'Forgot password',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $forgotPasswordId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// reset password page
				$this->insertPage(
					array(
						'title' => 'Reset password',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $resetPasswordId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// resend activation email page
				$this->insertPage(
					array(
						'title' => 'Resend activation e-mail',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $resendActivationId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// login page
				$this->insertPage(
					array(
						'title' => 'Login',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $loginId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// register page
				$this->insertPage(
					array(
						'title' => 'Register',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $registerId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// logout page
				$this->insertPage(
					array(
						'title' => 'Logout',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $logoutId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// index page
				$indexPageId = $this->insertPage(
					array(
						'title' => 'Profile',
						'type' => 'root',
						'language' => $language
					),
					null,
					array('extra_id' => $indexId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// settings page
				$this->insertPage(
					array(
						'title' => 'Profile settings',
						'parent_id' => $indexPageId,
						'language' => $language
					),
					null,
					array('extra_id' => $settingsId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// change email page
				$this->insertPage(
					array(
						'title' => 'Change email',
						'parent_id' => $indexPageId,
						'language' => $language
					),
					null,
					array('extra_id' => $changeEmailId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);

				// change password page
				$this->insertPage(
					array(
						'title' => 'Change password',
						'parent_id' => $indexPageId,
						'language' => $language
					),
					null,
					array('extra_id' => $changePasswordId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);
			}
		}
	}
}
