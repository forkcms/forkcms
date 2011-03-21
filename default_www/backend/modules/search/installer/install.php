<?php

/**
 * Installer for the search module
 *
 * @package		installer
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class SearchInstall extends ModuleInstaller
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

		// add 'search' as a module
		$this->addModule('search', 'The search module.');

		// general settings
		$this->setSetting('search', 'overview_num_items', 10);
		$this->setSetting('search', 'validate_search', true);

		// module rights
		$this->setModuleRights(1, 'search');

		// action rights
		$this->setActionRights(1, 'search', 'add_synonym');
		$this->setActionRights(1, 'search', 'edit_synonym');
		$this->setActionRights(1, 'search', 'delete_synonym');
		$this->setActionRights(1, 'search', 'settings');
		$this->setActionRights(1, 'search', 'statistics');
		$this->setActionRights(1, 'search', 'synonyms');

		// add extra's
		$searchID = $this->insertExtra('search', 'block', 'Search', null, 'a:1:{s:3:"url";s:40:"/private/nl/search/statistics?token=true";}', 'N', 2000);
		$this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for search already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($searchID, $language)))
			{
				// insert search
				$this->insertPage(array('title' => 'Search',
										'type' => 'root',
										'language' => $language),
									null,
									array('extra_id' => $searchID));
			}
		}

		// activate search on 'pages'
		$this->searchPages();

		// create module cache path
		if(!SpoonDirectory::exists(PATH_WWW . '/frontend/cache/search')) SpoonDirectory::create(PATH_WWW . '/frontend/cache/search');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}


	/**
	 * Activate search on pages
	 *
	 * @return	void
	 */
	private function searchPages()
	{
		// make 'pages' searchable
		$this->makeSearchable('pages');

		// get db instance
		$db = $this->getDB();

		// get existing menu items
		$menu = $db->getRecords('SELECT id, revision_id, language, title
									FROM pages
									WHERE status = ?',
									array('active'));

		// loop menu items
		foreach($menu as $id => $page)
		{
			// get blocks
			$blocks = $db->getColumn('SELECT html FROM pages_blocks WHERE revision_id = ?', array($page['revision_id']));

			// merge blocks content
			$text = strip_tags(implode(' ', $blocks));

			// add page to search index
			$db->execute('INSERT INTO search_index (module, other_id, language, field, value, active)
							VALUES (?, ?, ?, ?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?, active = ?', array('pages', (int) $page['id'], (string) $page['language'], 'title', $page['title'], 'Y', $page['title'], 'Y'));
			$db->execute('INSERT INTO search_index (module, other_id, language, field, value, active)
							VALUES (?, ?, ?, ?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?, active = ?', array('pages', (int) $page['id'], (string) $page['language'], 'text', $text, 'Y', $text, 'Y'));
		}
	}
}

?>