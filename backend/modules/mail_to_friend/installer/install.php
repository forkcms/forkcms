<?php

/**
 * Installer for the mail_to_friend module
 *
 * @package		installer
 * @subpackage	mail_to_friend
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class MailToFriendInstall extends ModuleInstaller
{
	/**
	 * Execute the installer
	 */
	public function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'mail_to_friend' as a module
		$this->addModule('mail_to_friend', 'The mail_to_friend module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'mail_to_friend');

		// set action rights
		$this->setActionRights(1, 'mail_to_friend', 'index');
		$this->setActionRights(1, 'mail_to_friend', 'detail');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationMailToFriendId = $this->setNavigation($navigationModulesId, 'MailToFriend', 'mail_to_friend/index', array('mail_to_friend/detail'));
	}
}
