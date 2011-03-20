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
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

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

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>