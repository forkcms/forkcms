<?php

/**
 * ContactInstall
 * Installer for the contact module
 *
 * @package		installer
 * @subpackage	contact
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ContactInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// add 'contact' as a module
		$this->addModule('contact', 'The contact module.');

		// general settings
		$this->setSetting('contact', 'requires_akismet', false);
		$this->setSetting('contact', 'requires_google_maps', false);

		// insert locale (nl)
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Er ging iets mis tijdens het verzenden, probeer later opnieuw.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Uw e-mail werd verzonden.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'ContactSubject', 'E-mail via contactformulier');

		// insert locale (en)
		$this->insertLocale('en', 'frontend', 'core', 'err', 'ContactErrorWhileSending', 'Something went wrong while trying to send, please try again later.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'ContactMessageSent', 'Your e-mail was sent.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'ContactSubject', 'E-mail via contact form.');
	}
}

?>