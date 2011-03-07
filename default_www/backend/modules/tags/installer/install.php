<?php

/**
 * Installer for the tags module
 *
 * @package		installer
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
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
		$this->importSQL(dirname(__FILE__) . '/install.sql');

		// add 'blog' as a module
		$this->addModule('tags', 'The tags module.');

		// module rights
		$this->setModuleRights(1, 'tags');

		// action rights
		$this->setActionRights(1, 'tags', 'autocomplete');
		$this->setActionRights(1, 'tags', 'edit');
		$this->setActionRights(1, 'tags', 'index');
		$this->setActionRights(1, 'tags', 'mass_action');

		// add extra
		$tagsID = $this->insertExtra('tags', 'block', 'Tags', null, null, 'N', 30);
		$this->insertExtra('tags', 'widget', 'TagCloud', 'tagcloud', null, 'N', 31);
		$this->insertExtra('tags', 'widget', 'Related', 'related', null, 'N', 32);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for tags already exists in this language
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
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Blog', 'blog');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'ItemsWithTag', 'items met tag "%1$s"');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Pages', 'pagina\'s');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Related', 'gerelateerd');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'ToTagsOverview', 'naar het tags overzicht');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Related', 'gerelateerd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'TagCloud', 'tag-cloud');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'Edited', 'De tag "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'EditTag', 'bewerk tag "%1$s"');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'Deleted', 'De geselecteerde tag(s) werd(en) verwijderd.');
		$this->insertLocale('nl', 'backend', 'tags', 'msg', 'NoItems', 'Er zijn nog geen tags.');
		$this->insertLocale('nl', 'backend', 'tags', 'err', 'NonExisting', 'Deze tag bestaat niet.');
		$this->insertLocale('nl', 'backend', 'tags', 'err', 'NoSelection', 'Er waren geen tags geselecteerd.');

		// insert locale (en)
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Blog', 'blog');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'ItemsWithTag', 'items with tag "%1$s"');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Pages', 'pages');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Related', 'related');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'ToTagsOverview', 'to tags overview');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Related', 'related');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'TagCloud', 'tagcloud');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'Edited', 'The tag "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'EditTag', 'edit tag "%1$s"');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'Deleted', 'The selected tag(s) was/were deleted.');
		$this->insertLocale('en', 'backend', 'tags', 'msg', 'NoItems', 'There are no tags yet.');
		$this->insertLocale('en', 'backend', 'tags', 'err', 'NonExisting', 'This tag doesn\'t exist.');
		$this->insertLocale('en', 'backend', 'tags', 'err', 'NoSelection', 'No tags were selected.');
	}
}

?>