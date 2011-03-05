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
		$this->importSQL(dirname(__FILE__) . '/install.sql');

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

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synoniemen zijn verplicht.');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'TermIsRequired', 'De zoekterm is verplicht.');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'TermExists', 'Synoniemen voor deze zoekterm bestaan reeds.');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn.');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'AddSynonym', 'synoniem toevoegen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'DeleteSynonym', 'synoniem verwijderen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'EditSynonym', 'synoniem bewerken');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'ItemsForAutocomplete', 'Items in autocomplete (zoekresultaten: suggesties zoekwoorden)');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'ItemsForAutosuggest', 'Items in autosuggest (zoek widget: resultaten)');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'ModuleWeight', 'module gewicht');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'SearchedOn', 'gezocht op');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'AddedSynonym', 'Het synoniem voor zoekterm "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Ben je zeker dat je de synoniemen voor zoekterm "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'DeletedSynonym', 'Het synoniem voor zoekterm "%1$s" werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'EditedSynonym', 'Het synoniem voor zoekterm "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'HelpWeight', 'Het standaard gewicht is 1. Als je zoekresultaten van een specifieke module belangrijker vindt, verhoog dan het gewicht. vb. als pagina\'s gewicht "3" heeft dan zullen resultaten van deze module 3 keer meer kans hebben om voor te komen in de zoekresultaten.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Definieer de belangrijkheid van iedere module in de zoekresultaten.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'IncludeInSearch', 'Opnemen in de zoekresultaten?');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoStatistics', 'Er zijn nog geen statistieken.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoSynonyms', 'Er zijn nog geen synoniemen. <a href="%1$s">Voeg het eerste synoniem toe</a>.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoSynonymsBox', 'Er zijn nog geen synoniemen.');

		$this->insertLocale('nl', 'frontend', 'core', 'err', 'TermIsRequired', 'De zoekterm is verplicht.');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Search', 'zoeken');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synonyms are required.');
		$this->insertLocale('en', 'backend', 'search', 'err', 'TermIsRequired', 'The searchterm is required.');
		$this->insertLocale('en', 'backend', 'search', 'err', 'TermExists', 'Synonyms for this searchterm already exist.');
		$this->insertLocale('en', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'AddSynonym', 'add synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'DeleteSynonym', 'delete synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'EditSynonym', 'edit synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'ItemsForAutocomplete', 'Items in autocomplete (search results: search term suggestions)');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'ItemsForAutosuggest', 'Items in autosuggest (search widget: results)');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'ModuleWeight', 'module weight');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'SearchedOn', 'searched on');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'AddedSynonym', 'The synonym for the searchterm "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Are you sure you want to delete the synonyms for the searchterm "%1$s"?');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'DeletedSynonym', 'The synonym for the searchterm "%1$s" was deleted.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'EditedSynonym', 'The synonym for the searchterm "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'HelpWeight', 'The default weight is 1. If you want to give search results from a specific module more importance, increase the weight. E.g. if pages has weight "3" then they are 3 times as likely to show up higher in search results.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Define the importance of each module in search results here.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'IncludeInSearch', 'Include in search results?');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'NoStatistics', 'There are no statistics yet.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'NoSynonyms', 'There are no synonyms yet. <a href="%1$s">Add the first synonym</a>.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'NoSynonymsBox', 'There are no synonyms yet.');

		$this->insertLocale('en', 'frontend', 'core', 'err', 'TermIsRequired', 'The searchterm is required.');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Search', 'search');
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