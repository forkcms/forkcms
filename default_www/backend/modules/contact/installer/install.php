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

		// add extra
		$contactID = $this->insertExtra('contact', 'block', 'Contact', null, null, 'N', 6);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// insert contact page
			$this->insertPage(array('id' => 6,
									'title' => 'Contact',
									'type' => 'footer',
									'language' => $language,
									'allow_move' => 'Y',
									'allow_delete' => 'Y'),
								null,
								array('extra_id' => $contactID));
		}
	}
}

?>