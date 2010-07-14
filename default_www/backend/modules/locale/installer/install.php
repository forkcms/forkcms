<?php

/**
 * LocaleInstall
 * Installer for the locale module
 *
 * @package		installer
 * @subpackage	locale
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class LocaleInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/locale/installer/install.sql');

		// add 'locale' as a module
		$this->addModule('locale', 'The module to manage your website/cms locale.');

		// general settings
		$this->setSetting('locale', 'languages', array('de', 'en', 'es', 'fr', 'nl'));
		$this->setSetting('locale', 'requires_akismet', false);
		$this->setSetting('locale', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'locale');

		// action rights
		$this->setActionRights(1, 'locale', 'add');
		$this->setActionRights(1, 'locale', 'analyse');
		$this->setActionRights(1, 'locale', 'edit');
		$this->setActionRights(1, 'locale', 'index');
		$this->setActionRights(1, 'locale', 'mass_action');

		// insert locale for backend locale
		$this->insertLocale('nl', 'backend', 'locale', 'err', 'AlreadyExists', 'Deze vertaling bestaat reeds.');
		$this->insertLocale('nl', 'backend', 'locale', 'err', 'ModuleHasToBeCore', 'De module moet core zijn voor vertalingen in de frontend.');
		$this->insertLocale('nl', 'backend', 'locale', 'err', 'NoSelection', 'Er waren geen vertalingen geselecteerd.');
		$this->insertLocale('nl', 'backend', 'locale', 'lbl', 'Add', 'vertaling toevoegen');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'Added', 'De vertaling "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'Deleted', 'De geselecteerde vertalingen werden verwijderd.');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'Edited', 'De vertaling "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'EditTranslation', 'bewerk vertaling "%1$s"');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'HelpAddName', 'De Engelstalige referentie naar de vertaling, bvb. "Add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten.');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'HelpAddValue', 'De vertaling zelf, bvb. "toevoegen".');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'HelpEditName', 'De Engelstalige referentie naar de vertaling, bvb. "Add". Deze waarde moet beginnen met een hoofdletter en mag geen spaties bevatten.');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'HelpEditValue', 'De vertaling zelf, bvb. "toevoegen".');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'HelpName', 'De Engelstalige referentie naar de vertaling, bvb. "Add".');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'HelpValue', 'De vertaling zelf, bvb. "toevoegen".');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'NoItems', 'Er zijn nog geen vertalingen. <a href="%1$s">Voeg de eerste vertaling toe</a>.');
		$this->insertLocale('nl', 'backend', 'locale', 'msg', 'NoItemsAnalyse', 'Er werden geen ontbrekende vertalingen gevonden.');

		// insert locale for backend core
		$this->insertLocale('nl', 'backend', 'core', 'err', 'ActionNotAllowed', 'Je hebt onvoldoende rechten voor deze actie.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'AddingCategoryFailed', 'Er ging iets mis.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'AkismetKey', 'Akismet API-key werd nog niet geconfigureerd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'AlphaNumericCharactersOnly', 'Enkel alfanumerieke karakters zijn toegestaan.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'BrowserNotSupported', '<p>Je gebruikt een verouderde browser die niet ondersteund wordt door Fork CMS. Gebruik een van de volgende goeie alternatieven:</p><ul><li><a href="http://www.microsoft.com/windows/products/winfamily/ie/default.mspx">Internet Explorer *</a>: update naar de nieuwe versie van Internet Explorer.</li><li><a href="http://www.firefox.com/">Firefox</a>: een zeer goeie browser met veel gratis extensies.</li><li><a href="http://www.opera.com/">Opera:</a> Snel en met vele functionaliteiten.</li></ul>');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'CookiesNotEnabled', 'Om Fork CMS te gebruiken moet cookies geactiveerd zijn in uw browser. Activeer cookies en vernieuw deze pagina.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'DateIsInvalid', 'Ongeldige datum.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'DebugModeIsActive', 'Debug-mode is nog actief.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'EmailAlreadyExists', 'Dit e-mailadres is al in gebruik.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'EmailIsRequired', 'Gelieve een e-mailadres in te geven.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'EmailIsUnknown', 'Dit e-mailadres zit niet in onze database.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'FieldIsRequired', 'Dit veld is verplicht.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'ForkAPIKeys', 'Fork API-keys nog niet geconfigureerd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'GoogleMapsKey', 'Google maps API-key werd nog niet geconfigureerd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'InvalidAPIKey', 'Ongeldige API key.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'InvalidDomain', 'Ongeldig domein.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'InvalidEmailPasswordCombination', 'De combinatie van e-mail en wachtwoord is niet correct. <a href="#" rel="forgotPasswordHolder" class="toggleBalloon">Bent u uw wachtwoord vergeten?</a>');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'InvalidName', 'Ongeldige naam.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'InvalidURL', 'Ongeldige URL.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'InvalidValue', 'Ongeldige waarde.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'JavascriptNotEnabled', 'Om Fork CMS te gebruiken moet Javascript geactiveerd zijn in uw browser. Activeer javascript en vernieuw deze pagina.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'JPGAndGIFOnly', 'Enkel jpg en gif bestanden zijn toegelaten.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'ModuleNotAllowed', 'Je hebt onvoldoende rechten voor deze module.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'NicknameIsRequired', 'Gelieve een publicatienaam in te geven.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'NoCommentsSelected', 'Er waren geen reacties geselecteerd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'NoModuleLinked', 'Er is nog geen module gekoppeld.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'NonExisting', 'Dit item bestaat niet.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'NoSelection', 'Er waren geen items geselecteerd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'PasswordIsRequired', 'Gelieve een wachtwoord in te geven.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'PasswordRepeatIsRequired', 'Gelieve het gewenste wachtwoord te herhalen.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'PasswordsDontMatch', 'De wachtwoorden zijn verschillend, probeer het opnieuw.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'RobotsFileIsNotOK', 'robots.txt is niet correct.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'RSSTitle', 'Blog RSS titel is nog niet ingevuld. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'SettingsForkAPIKeys', 'De Fork API-keys zijn niet goed geconfigureerd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'SomethingWentWrong', 'Er liep iets fout.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'SurnameIsRequired', 'Gelieve een achternaam in te geven.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'TimeIsInvalid', 'Ongeldige tijd.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'TitleIsRequired', 'Geef een titel in.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'URLAlreadyExists', 'Deze URL bestaat reeds.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'ValuesDontMatch', 'De waarden komen niet overeen.');
		$this->insertLocale('nl', 'backend', 'core', 'err', 'XMLFilesOnly', 'Enkel xml bestanden zijn toegelaten.');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AccountManagement', 'account beheer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Active', 'actief');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Add', 'toevoegen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AddCategory', 'categorie toevoegen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AddTemplate', 'template toevoegen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AllComments', 'alle reacties');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AllowComments', 'reacties toestaan');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Amount', 'aantal');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Analyse', 'analyse');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'APIKey', 'API key');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'APIKeys', 'API keys');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'APIURL', 'API URL');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Application', 'applicatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Archive', 'archief');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Archived', 'gearchiveerd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Articles', 'artikels');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'At', 'om');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Author', 'auteur');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Avatar', 'avatar');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Back', 'terug');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Backend', 'backend');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Block', 'blok');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Blog', 'blog');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'BrowserNotSupported', 'browser niet ondersteund');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'By', 'door');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Cancel', 'annuleer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Categories', 'categorieën');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Category', 'categorie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChangePassword', 'wijzig wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChooseALanguage', 'kies een taal');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChooseAModule', 'kies een module');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChooseAnApplication', 'kies een applicatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChooseATemplate', 'kies een template');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChooseAType', 'kies een type');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ChooseContent', 'kies inhoud');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Comment', 'reactie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Comments', 'reacties');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ConfirmPassword', 'bevestig wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Contact', 'contact');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ContactForm', 'contactformulier');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Content', 'inhoud');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ContentBlocks', 'inhoudsblokken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Core', 'core');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'CustomURL', 'aangepaste URL');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Dashboard', 'dashboard');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Date', 'datum');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'DateAndTime', 'Datum en tijd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'DateFormat', 'formaat datums');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Dear', 'beste');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'DebugMode', 'debug mode');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Default', 'standaard');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Delete', 'verwijderen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'DeleteThisTag', 'verwijder deze tag');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Description', 'beschrijving');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Developer', 'developer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Domains', 'domeinen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Draft', 'kladversie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Drafts', 'kladversies');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Edit', 'wijzigen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'EditedOn', 'bewerkt op');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Editor', 'editor');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'EditTemplate', 'template wijzigen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Email', 'e-mail');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'EnableModeration', 'moderatie inschakelen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Example', 'voorbeeld');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Execute', 'uitvoeren');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ExtraMetaTags', 'extra metatags');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'FeedburnerURL', 'feedburner URL');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'File', 'bestand');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Filename', 'bestandsnaam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'FilterCommentsForSpam', 'filter reacties op spam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'From', 'van');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Frontend', 'frontend');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'General', 'algemeen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'GeneralSettings', 'algemene instellingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'GoToPage', 'ga naar pagina');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Group', 'groep');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Hidden', 'verborgen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Home', 'home');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Import', 'importeer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Interface', 'interface');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'InterfacePreferences', 'voorkeuren interface');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ItemsPerPage', 'items per pagina');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Keywords', 'sleutelwoorden');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Label', 'label');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Language', 'taal');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Languages', 'talen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'LastEditedOn', 'laatst bewerkt op');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'LastSaved', 'laatst opgeslagen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'LatestComments', 'laatste reacties');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Layout', 'layout');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Loading', 'loading');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Locale', 'locale');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'LoginCredentials', 'login gegevens');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'LongDateFormat', 'lange datumformaat');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MainContent', 'hoofdinhoud');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Meta', 'meta');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MetaInformation', 'meta-informatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MetaNavigation', 'metanavigatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Moderate', 'modereer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Moderation', 'moderatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Module', 'module');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Modules', 'modules');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ModuleSettings', 'module-instellingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MoveToModeration', 'verplaats naar moderatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MoveToPublished', 'verplaats naar gepubliceerd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MoveToSpam', 'verplaats naar spam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Name', 'naam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'NavigationTitle', 'navigatietitel');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'NewPassword', 'nieuw wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'News', 'nieuws');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Next', 'volgende');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'NextPage', 'volgende pagina');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Nickname', 'publicatienaam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'None', 'geen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'NoTheme', 'geen thema');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'NumberOfBlocks', 'aantal blokken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'OK', 'OK');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Page', 'pagina');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Pages', 'pagina\'s');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PageTitle', 'paginatitel');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Pagination', 'paginering');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Password', 'wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Permissions', 'rechten');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PersonalInformation', 'persoonlijke gegevens');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PingBlogServices', 'ping blogservices');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Port', 'poort');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Preview', 'preview');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Previous', 'vorige');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PreviousPage', 'vorige pagina');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PreviousVersions', 'vorige versies');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Profile', 'profiel');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Publish', 'publiceer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Published', 'gepubliceerd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PublishedArticles', 'gepubliceerde artikels');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PublishedOn', 'gepubliceerd op');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'PublishOn', 'publiceer op');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RecentArticles', 'recente artikels');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RecentComments', 'recente reacties');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RecentlyEdited', 'recent bewerkt');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ReferenceCode', 'referentiecode');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RepeatPassword', 'herhaal wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ReplyTo', 'reply-to');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RequiredField', 'verplicht veld');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ResetAndSignIn', 'resetten en aanmelden');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ResetYourPassword', 'reset je wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RSSFeed', 'RSS feed');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Save', 'opslaan');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SaveDraft', 'kladversie opslaan');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Scripts', 'scripts');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Send', 'verzenden');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SEO', 'SEO');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Server', 'server');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Settings', 'instellingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ShortDateFormat', 'korte datumformaat');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SignIn', 'Log in');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SignOut', 'afmelden');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Sitemap', 'sitemap');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SMTP', 'SMTP');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SortAscending', 'sorteer oplopend');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SortDescending', 'sorteer aflopend');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SortedAscending', 'oplopend gesorteerd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SortedDescending', 'aflopend gesorteerd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Spam', 'spam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SpamFilter', 'spamfilter');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Statistics', 'statistieken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Status', 'status');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Strong', 'sterk');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Summary', 'samenvatting');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Surname', 'achternaam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Tags', 'tags');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Template', 'template');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Templates', 'templates');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Themes', 'thema\'s');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'TimeFormat', 'formaat tijd');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Title', 'titel');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Titles', 'titels');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'To', 'aan');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Translation', 'vertaling');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Translations', 'vertalingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Type', 'type');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'UpdateFilter', 'filter updaten');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'URL', 'URL');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'UsedIn', 'gebruikt in');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Userguide', 'userguide');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Username', 'gebruikersnaam');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Users', 'gebruikers');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'UseThisDraft', 'gebruik deze kladversie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'UseThisVersion', 'laad deze versie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Value', 'waarde');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'View', 'bekijken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'VisibleOnSite', 'Zichtbaar op de website');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'VisitWebsite', 'bezoek website');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WaitingForModeration', 'wachten op moderatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Weak', 'zwak');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WebmasterEmail', 'e-mailadres webmaster');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WebsiteTitle', 'titel website');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WebsiteWorkingLanguage', 'werktaal website');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WhichModule', 'welke module');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WhichWidget', 'welke widget');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Widget', 'widget');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'WithSelected', 'met geselecteerde');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ActivateNoFollow', 'Activeer <code>rel="nofollow"</code>');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'Added', 'Het item werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'AddedCategory', 'De categorie "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ClickToEdit', 'Klik om te wijzigen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentDeleted', 'De reactie werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentMovedModeration', 'De reactie werd verplaatst naar moderatie.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentMovedPublished', 'De reactie werd gepubliceerd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentMovedSpam', 'De reactie werd gemarkeerd als spam.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentsDeleted', 'De reacties werden verwijderd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentsMovedModeration', 'De reacties werden verplaatst naar moderatie.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentsMovedPublished', 'De reacties werden gepubliceerd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentsMovedSpam', 'De reacties werden gemarkeerd als spam.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'CommentsToModerate', '%1$s reactie(s) te modereren.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het item "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ConfirmDeleteCategory', 'Ben je zeker dat je deze categorie "%1$s" wil verwijderen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ConfirmMassDelete', 'Ben je zeker dat je deze item(s) wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ConfirmMassSpam', 'Ben je zeker dat je deze item(s) wil markeren als spam?');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'DE', 'Duits');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'Deleted', 'Het item werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'DeletedCategory', 'De categorie "%1$s" werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'EditCategory', 'bewerk categorie "%1$s"');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'Edited', 'Het item werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'EditedCategory', 'De categorie "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'EN', 'Engels');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ES', 'Spaans');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ForgotPassword', 'Wachtwoord vergeten?');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'FR', 'Frans');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpAvatar', 'Een vierkante foto van je gezicht geeft het beste resultaat.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpBlogger', 'Selecteer het bestand dat u heeft geëxporteerd van <a href="http://blogger.com">Blogger</a>.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpDrafts', 'Hier kan je jouw kladversie zien. Dit zijn tijdelijke versies.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpEmailFrom', 'E-mails verzonden vanuit het CMS gebruiken deze instellingen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpEmailTo', 'Notificaties van het CMS worden hiernaar verstuurd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpFeedburnerURL', 'bijv. http://feeds.feedburner.com/jouw-website');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpForgotPassword', 'Vul hieronder je e-mail adres in. Je krijgt een e-mail met instructies hoe je een nieuw wachtwoord instelt.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpMetaCustom', 'Voeg extra, op maat gemaakte metatags toe.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpMetaDescription', 'Vat de inhoud kort samen. Deze samenvatting wordt getoond in de resultaten van zoekmachines.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpMetaKeywords', 'Kies een aantal goed gekozen termen die de inhoud omschrijven.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpMetaURL', 'Vervang de automatisch gegenereerde URL door een zelfgekozen URL.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpNickname', 'De naam waaronder je wilt publiceren (bijvoorbeeld als auteur van een blogartikel).');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpResetPassword', 'Vul je gewenste, nieuwe wachtwoord in.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpRevisions', 'De laatst opgeslagen versies worden hier bijgehouden. De huidige versie wordt pas overschreven als je opslaat.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpRSSDescription', 'Beschrijf bondig wat voor soort inhoud de RSS-feed zal bevatten.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpRSSTitle', 'Geef een duidelijke titel aan de RSS-feed');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'HelpSMTPServer', 'Mailserver die wordt gebruikt voor het versturen van e-mails.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'LoginFormForgotPasswordSuccess', '<strong>Mail sent.</strong> Please check your inbox!');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NL', 'Nederlands');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoComments', 'Er zijn geen reacties in deze categorie.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoItems', 'Er zijn geen items.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoPublishedComments', 'Er zijn geen gepubliceerde reacties.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoRevisions', 'Er zijn nog geen vorige versies.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoTags', 'Je hebt nog geen tags ingegeven.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoUsage', 'Nog niet gebruikt.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'PasswordResetSuccess', 'Je wachtwoord werd gewijzigd.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailContent', 'Reset je wachtwoord door op de link hieronder te klikken. Indien je niet hier niet om gevraagd hebt hoef je geen actie te ondernemen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'ResetYourPasswordMailSubject', 'Wijzig je wachtwoord');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'Saved', 'De wijzigingen werden opgeslagen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'SavedAsDraft', '"%1$s" als kladversie opgeslagen.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'UsingADraft', 'Je gebruikt een kladversie.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'UsingARevision', 'Je hebt een oudere versie geladen.');

		// insert locale for frontend core
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Archive', 'archief');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Category', 'categorie');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Comment', 'reageer');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Comments', 'reacties');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Detail', 'detail');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Rss', 'rss');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'AuthorIsRequired', 'Auteur is een verplicht veld.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'EmailIsInvalid', 'Gelieve een geldig emailadres in te geven.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'EmailIsRequired', 'E-mail is een verplicht veld.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'FormError', 'Er ging iets mis, kijk de gemarkeerde velden na.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'InvalidURL', 'Dit is een ongeldige URL.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'MessageIsRequired', 'Bericht is een verplicht veld.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'NameIsRequired', 'Gelieve een naam in te geven.');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Archive', 'archief');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'By', 'door');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Category', 'categorie');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Comment', 'reactie');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'CommentedOn', 'reageerde op');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Comments', 'reacties');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Date', 'datum');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Email', 'e-mail');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'GoTo', 'ga naar');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'GoToPage', 'ga naar pagina');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'In', 'in');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Message', 'bericht');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Name', 'naam');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'NextPage', 'volgende pagina');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'On', 'op');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'PreviousPage', 'vorige pagina');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'RecentComments', 'recente reacties');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'RequiredField', 'verplicht veld');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Send', 'verstuur');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Tags', 'tags');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Title', 'titel');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Website', 'website');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'WrittenOn', 'geschreven op');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'YouAreHere', 'je bent hier');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'Comment', 'reageer');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'TagsNoItems', 'Er zijn nog geen tags gebruikt.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'WrittenBy', 'geschreven door %1$s');
	}
}

?>