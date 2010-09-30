<?php

/**
 * TagsInstall
 * Installer for the tags module
 *
 * @package		installer
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class TagsInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/tags/installer/install.sql');

		// add 'blog' as a module
		$this->addModule('tags', 'The tags module.');

		// general settings
		$this->setSetting('tags', 'requires_akismet', false);
		$this->setSetting('tags', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'tags');

		// action rights
		$this->setActionRights(1, 'tags', 'autocomplete');
		$this->setActionRights(1, 'tags', 'edit');
		$this->setActionRights(1, 'tags', 'index');
		$this->setActionRights(1, 'tags', 'mass_action');

		// add extra
		$tagsID = $this->insertExtra('tags', 'block', 'Tags', null, null, 'N', 3);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for contact already exists in this language
			if((int) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?', array($tagsID, $language)) == 0)
			{
				// insert contact page
				$this->insertPage(array('title' => 'Tags',
										'type' => 'root',
										'language' => $language),
									null,
									array('extra_id' => $tagsID));
			}
		}



		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'Edited', 'De tag "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'EditTag', 'bewerk tag "%1$s"');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'Deleted', 'De geselecteerde tag(s) werd(en) verwijderd.');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'NoItems', 'Er zijn nog geen tags.');
		$this->insertLocale('nl', 'backend', 'tags', 'err', 'NonExisting', 'Deze tag bestaat niet.');
		$this->insertLocale('nl', 'backend', 'tags', 'err', 'NoSelection', 'Er waren geen tags geselecteerd.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'Edited', 'The tag "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'EditTag', 'edit tag "%1$s"');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'Deleted', 'The selected tag(s) was/were deleted.');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'NoItems', 'There are no tags yet.');
		$this->insertLocale('en', 'backend', 'tags', 'err', 'NonExisting', 'This tag doesn\'t exist.');
		$this->insertLocale('en', 'backend', 'tags', 'err', 'NoSelection', 'No tags were selected.');
	}
}

?>