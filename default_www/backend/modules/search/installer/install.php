<?php

/**
 * SearchInstall
 * Installer for the search module
 *
 * @package		installer
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
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
		$this->importSQL(PATH_WWW .'/backend/modules/search/installer/install.sql');

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
		$searchBlock = $this->insertExtra('search', 'block', 'Search', null, null, 'N', 2000);
		$this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if the Search page doesn't exist
			if((int) $this->getDB()->getVar('SELECT COUNT(id) FROM pages WHERE id = ? AND language = ?', array(5, $language)) == 0) // @todo: dit moet eigenlijk zoeken op 'bestaat pagina met dit block al' ipv op id (ok, da klopt nu wel door de search order enzo, maar als er ooit ene tussenkomt, of deze pagina wordt ooit verwijderd en er wordt een neiuwe taal geinstalleerd, dan wordt search opnieuw geinstalleerd voor andere talen ook)
			{
				// insert disclaimer
				$this->insertPage(array('id' => 5,
										'title' => 'Search',
										'type' => 'root',
										'language' => $language,
										'allow_move' => 'Y',
										'allow_delete' => 'Y'),
									null,
									array('extra_id' => $searchBlock));
			}
		}

		// activate search on 'pages'
		$this->searchPages();

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synoniemen zijn verplicht.');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'TermIsRequired', 'De zoekterm is verplicht.');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'TermExists', 'Synoniemen voor deze zoekterm bestaan reeds.');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn.');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'AddSynonym', 'synoniem toevoegen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'DeleteSynonym', 'synoniem verwijderen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'EditSynonym', 'synoniem bewerken');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'IP', 'IP');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'ModuleWeight', 'module gewicht');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'SearchedOn', 'gezocht op');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'Synonym', 'synoniem');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'Synonyms', 'synoniemen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'Term', 'term');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Ben je zeker dat je de synoniemen voor zoekterm "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'HelpWeight', 'Het standaard gewicht is 1. Als je zoekresultaten van een specifieke module belangrijker vindt, verhoog dan het gewicht. vb.  als pagina\'s gewicht "3" heeft dan zullen resultaten van deze module 3 keer meer kans hebben om voor te komen in de zoekresultaten.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Definieer de belangrijkheid van iedere module in de zoekresultaten.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'IncludeInSearch', 'Opnemen in de zoekresultaten?');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoStatistics', 'Er zijn nog geen statistieken.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoSynonyms', 'Er zijn nog geen synoniemen. <a href="%1$s">Voeg het eerste synoniem toe</a>.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoSynonymsBox', 'Er zijn nog geen synoniemen.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synonyms are required.');
		$this->insertLocale('en', 'backend', 'search', 'err', 'TermIsRequired', 'The searchterm is required.');
		$this->insertLocale('en', 'backend', 'search', 'err', 'TermExists', 'Synonyms for this searchterm already exist.');
		$this->insertLocale('en', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'AddSynonym', 'add synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'DeleteSynonym', 'delete synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'EditSynonym', 'edit synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'IP', 'IP');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'ModuleWeight', 'module weight');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'SearchedOn', 'searched on');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'Synonym', 'synonym');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'Synonyms', 'synonyms');
		$this->insertLocale('en', 'backend', 'search', 'lbl', 'Term', 'term');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Are you sure you want to delete the synonyms for the searchterm "%1$s"?');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'HelpWeight', 'The default weight is 1. If you want to give search results from a specific module more importance, increase the weight. E.g. if pages has weight "3" then they are 3 times as likely to show up higher in search results.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'HelpWeightGeneral', 'Define the importance of each module in search results here.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'IncludeInSearch', 'Include in search results?');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'NoStatistics', 'There are no statistics yet.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'NoSynonyms', 'There are no synonyms yet. <a href="%1$s">Add the first synonym</a>.');
		$this->insertLocale('en', 'backend', 'search', 'msg', 'NoSynonymsBox', 'There are no synonyms yet.');
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
		$menu = $db->retrieve('SELECT id, revision_id, language, title FROM pages WHERE status = ?', array('active'));

		// loop menu items
		foreach($menu as $id => $page)
		{
			// get blocks
			$blocks = $db->getColumn('SELECT html FROM pages_blocks WHERE revision_id = ?', array($page['revision_id']));

			// merge blocks content
			$text = strip_tags(implode(' ', $blocks));

			// add page to search index
			$db->execute('INSERT INTO search_index (module, other_id, language, field, value, active) VALUES (?, ?, ?, ?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?, active = ?', array('pages', (int) $page['id'], (string) $page['language'], 'title', $page['title'], 'Y', $page['title'], 'Y'));
			$db->execute('INSERT INTO search_index (module, other_id, language, field, value, active) VALUES (?, ?, ?, ?, ?, ?)
							ON DUPLICATE KEY UPDATE value = ?, active = ?', array('pages', (int) $page['id'], (string) $page['language'], 'text', $text, 'Y', $text, 'Y'));
		}
	}
}

?>