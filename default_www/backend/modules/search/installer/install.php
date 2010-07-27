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
		$this->insertExtra(array('module' => 'search',
									'type' => 'block',
									'label' => 'Search',
									'action' => null,
									'data' => null,
									'hidden' => 'N',
									'sequence' => 3000));

		// make 'pages' searchable
		$this->makeSearchable('pages');

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
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Referrer', 'referrer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Search', 'zoeken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Synonyms', 'synoniemen');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Search', 'zoeken');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'SearchTerm', 'zoekterm');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'SearchNoItems', 'Er zijn geen resultaten.');

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
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Referrer', 'referrer');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Search', 'search');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Synonyms', 'synonyms');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Search', 'search');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'SearchTerm', 'searchterm');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'SearchNoItems', 'There were no results.');
	}
}

?>