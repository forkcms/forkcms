<?php

/**
 * Installer for the contact module
 *
 * @package		installer
 * @subpackage	contact
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
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

		// add extra
		$contactID = $this->insertExtra('contact', 'block', 'Contact', null, null, 'N', 6);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for contact already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($contactID, $language)))
			{
				// insert contact page
				$this->insertPage(array('title' => 'Contact',
										'type' => 'footer',
										'language' => $language),
									null,
									array('extra_id' => $contactID));
			}
		}
	}
}

?>