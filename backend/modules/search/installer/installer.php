<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the search module
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class SearchInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'search' as a module
		$this->addModule('search');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

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

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationSearchId = $this->setNavigation($navigationModulesId, 'Search');
		$this->setNavigation($navigationSearchId, 'Statistics', 'search/statistics');
		$this->setNavigation($navigationSearchId, 'Synonyms', 'search/synonyms', array('search/add_synonym', 'search/edit_synonym'));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Search', 'search/settings');

		// add extra's
		$searchId = $this->insertExtra('search', 'block', 'Search', null, 'a:1:{s:3:"url";s:40:"/private/nl/search/statistics?token=true";}', 'N', 2000);
		$this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for search already exists in this language
			// @todo refactor this nasty if statement...
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($searchId, $language)))
			{
				// insert search
				$this->insertPage(
					array(
						'title' => SpoonFilter::ucfirst($this->getLocale('Search', 'core', $language, 'lbl', 'frontend')
					),
					'type' => 'root',
					'language' => $language),
					null,
					array('extra_id' => $searchId, 'position' => 'main')
				);
			}
		}

		// activate search on 'pages'
		$this->searchPages();

		// create module cache path
		if(!SpoonDirectory::exists(PATH_WWW . '/frontend/cache/search')) SpoonDirectory::create(PATH_WWW . '/frontend/cache/search');
	}

	/**
	 * Activate search on pages
	 */
	private function searchPages()
	{
		// make 'pages' searchable
		$this->makeSearchable('pages');

		// get db instance
		$db = $this->getDB();

		// get existing menu items
		$menu = $db->getRecords(
			'SELECT id, revision_id, language, title
			 FROM pages
			 WHERE status = ?',
			array('active')
		);

		// loop menu items
		foreach($menu as $id => $page)
		{
			// get blocks
			$blocks = $db->getColumn('SELECT html FROM pages_blocks WHERE revision_id = ?', array($page['revision_id']));

			// merge blocks content
			$text = strip_tags(implode(' ', $blocks));

			// add page to search index
			$db->execute(
				'INSERT INTO search_index (module, other_id, language, field, value, active)
				 VALUES (?, ?, ?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE value = ?, active = ?',
				array('pages', (int) $page['id'], (string) $page['language'], 'title', $page['title'], 'Y', $page['title'], 'Y')
			);
			$db->execute(
				'INSERT INTO search_index (module, other_id, language, field, value, active)
				 VALUES (?, ?, ?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE value = ?, active = ?',
				array('pages', (int) $page['id'], (string) $page['language'], 'text', $text, 'Y', $text, 'Y')
			);
		}
	}
}
