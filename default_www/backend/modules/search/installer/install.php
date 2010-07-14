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

		// insert locale
		$this->insertLocale('nl', 'backend', 'search', 'err', 'SynonymIsRequired', 'Synoniemen zijn verplicht');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'TermIsRequired', 'De zoekterm is verplicht');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'TermExists', 'Synoniemen voor deze zoekterm bestaan reeds');
		$this->insertLocale('nl', 'backend', 'search', 'err', 'WeightNotNumeric', 'Het gewicht moet numeriek zijn');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'AddSynonym', 'synoniem toevoegen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'DeleteSynonym', 'synoniem verwijderen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'EditSynonym', 'synoniem bewerken');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'IP', 'IP');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'SearchedOn', 'gezocht op');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'Synonym', 'synoniem');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'Synonyms', 'synoniemen');
		$this->insertLocale('nl', 'backend', 'search', 'lbl', 'Term', 'term');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'ConfirmDeleteSynonym', 'Ben je zeker dat je de synoniemen voor zoekterm "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoStatistics', 'Er zijn nog geen statistieken.');
		$this->insertLocale('nl', 'backend', 'search', 'msg', 'NoSynonyms', 'Er zijn nog geen synoniemen. <a href="%1$s">Voeg het eerste synoniem toe</a>.');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Referrer', 'referrer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Search', 'zoeken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Synonyms', 'synoniemen');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Search', 'zoeken');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'SearchTerm', 'zoekterm');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'SearchNoItems', 'Er werden geen resultaten gevonden.');


	}
}

?>