<?php

/**
 * Installer for the form_builder module
 *
 * @package		installer
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FormBuilderInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW . '/backend/modules/form_builder/installer/data/install.sql');

		// add as a module
		$this->addModule('form_builder', 'The module to create and manage forms.');

		// module rights
		$this->setModuleRights(1, 'form_builder');

		// action rights
		$this->setActionRights(1, 'form_builder', 'add');
		$this->setActionRights(1, 'form_builder', 'edit');
		$this->setActionRights(1, 'form_builder', 'delete');
		$this->setActionRights(1, 'form_builder', 'index');
		$this->setActionRights(1, 'form_builder', 'data');
		$this->setActionRights(1, 'form_builder', 'data_details');
		$this->setActionRights(1, 'form_builder', 'mass_data_action');
		$this->setActionRights(1, 'form_builder', 'get_field');
		$this->setActionRights(1, 'form_builder', 'delete_field');
		$this->setActionRights(1, 'form_builder', 'save_field');
		$this->setActionRights(1, 'form_builder', 'sequence');

		// backend (nl)
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'FormBuilder', 'formbuilder');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'ErrorMessageIsRequired', 'Gelieve een foutmelding in te geven.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'IdentifierExists', 'Deze identifier bestaat reeds.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'InvalidIdentifier', 'Gelieve een geldige identifier op te geven (enkel . - _ en alphanumerieke karakters)');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'LabelIsRequired', 'Gelieve een label in te geven.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'SuccessMessageIsRequired', 'Gelieve een succes boodschap in te geven.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'UniqueIdentifier', 'Deze identifier is al in gebruik.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'ValueIsRequired', 'Gelieve een waarde in te geven.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Add', 'formulier toevoegen');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'AddFields', 'velden toevoegen');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'BackToData', 'terug naar inzendingen');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Basic', 'basis');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Checkbox', 'selectievakje');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'DefaultValue', 'standaardwaarde');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Details', 'details');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Drag', 'versleep');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Dropdown', 'keuzemenu');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'EditForm', 'bewerk formulier "%1$s"');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'ErrorMessage', 'foutmelding');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Extra', 'extra');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Fields', 'velden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'FormData', 'inzendingen van "%1$s"');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'FormElements', 'formulier elementen');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Heading', 'hoofding');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Identifier', 'identifier');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Method', 'methode');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'MethodDatabase', 'opslaan in de database');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'MethodDatabaseEmail', 'opslaan in de database en email verzenden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'MinutesAgo', '%1$s minuten geleden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Numeric', 'numeriek');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'OneMinuteAgo', '1 minuut geleden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'OneSecondAgo', '1 seconde geleden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Paragraph', 'paragraaf');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Parameter', 'argument');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Preview', 'preview');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Properties', 'eigenschappen');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Radiobutton', 'keuzerondje');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Recipient', 'bestemmeling');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Required', 'vereist');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'SecondsAgo', '%1$s seconden geleden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'SenderInformation', 'afzender informatie');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'SentOn', 'verstuurd op');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'SubmitButton', 'verzendknop');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'SuccessMessage', 'succes boodschap');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Textarea', 'tekstgebied');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Textbox', 'tekstveld');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'TextElements', 'tekst elementen');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Today', 'vandaag');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Validation', 'validatie');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Values', 'waarden');
		$this->insertLocale('nl', 'backend', 'form_builder', 'lbl', 'Yesterday', 'gisteren');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'Added', 'Het formulier "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het formulier "%1$s" en al zijn inzendingen wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'ConfirmDeleteData', 'Ben je zeker dat je deze inzending wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'Deleted', 'Het formulier "%1$s" werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'Edited', 'Het formulier "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'HelpIdentifier', 'De identifier wordt in de URL geplaatst na het succesvol opslaan van formulier.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'ImportantImmediateUpdate', '<strong>Belangrijk</strong>: aanpassingen die je hier maakt worden onmiddelijk opgeslaan.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'ItemDeleted', 'Inzending verwijderd.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'ItemsDeleted', 'Inzendingen verwijderd.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'NoData', 'Er zijn nog geen inzendingen.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'NoFields', 'Er zijn nog geen velden.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'NoItems', 'Er zijn nog geen formulieren.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'NoValues', 'Er zijn nog geen waarden.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'OneSentForm', '1 inzending');
		$this->insertLocale('nl', 'backend', 'form_builder', 'msg', 'SentForms', '%1$s inzendingen');

		// frontend (nl)
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'FieldIsRequired', 'Dit veld is verplicht.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'FormTimeout', 'Slow down cowboy, er moeten wat tijd tussen iedere inzending zijn.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'NumericCharactersOnly', 'Enkel numerieke karakters zijn toegestaan.');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Content', 'content');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'IP', 'IP');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'SenderInformation', 'afzender informatie');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'SentOn', 'verstuurd op');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'FormBuilderSubject', 'Nieuwe inzending voor formulier "%1$s".');

		// backend (en)
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'FormBuilder', 'formbuilder');
		$this->insertLocale('en', 'backend', 'form_builder', 'err', 'ErrorMessageIsRequired', 'Please provide an error message.');
		$this->insertLocale('nl', 'backend', 'form_builder', 'err', 'IdentifierExists', 'This identifier already exists.');
		$this->insertLocale('en', 'backend', 'form_builder', 'err', 'InvalidIdentifier', 'Please provide a valid identifier. (only . - _ and alphanumeric characters)');
		$this->insertLocale('en', 'backend', 'form_builder', 'err', 'LabelIsRequired', 'Please provide a label.');
		$this->insertLocale('en', 'backend', 'form_builder', 'err', 'SuccessMessageIsRequired', 'Please provide a success message.');
		$this->insertLocale('en', 'backend', 'form_builder', 'err', 'UniqueIdentifier', 'This identifier is already in use.');
		$this->insertLocale('en', 'backend', 'form_builder', 'err', 'ValueIsRequired', 'Please provide a value.');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Add', 'add form');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'AddFields', 'add fields');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'BackToData', 'back to submissions');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Basic', 'basic');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Checkbox', 'checkbox');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'DefaultValue', 'default value');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Details', 'details');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Drag', 'move');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Dropdown', 'dropdown');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'EditForm', 'edit form "%1$s"');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'ErrorMessage', 'error mesage');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Extra', 'extra');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Fields', 'fields');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'FormData', 'submissions for "%1$s"');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'FormElements', 'form elements');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Heading', 'heading');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Identifier', 'identifier');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Method', 'method');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'MethodDatabase', 'save in the database');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'MethodDatabaseEmail', 'save in the database and send email');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'MinutesAgo', '%1$s minutes ago');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Numeric', 'numeric');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'OneMinuteAgo', '1 minute ago');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'OneSecondAgo', '1 second ago');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Paragraph', 'paragraph');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Parameter', 'parameter');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Preview', 'preview');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Properties', 'properties');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Radiobutton', 'radiobutton');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Recipient', 'recipient');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Required', 'required');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'SecondsAgo', '%1$s seconds ago');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'SenderInformation', 'sender information');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'SentOn', 'sent on');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'SubmitButton', 'send button');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'SuccessMessage', 'success message');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Textarea', 'textarea');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Textbox', 'textbox');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'TextElements', 'text elements');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Today', 'today');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Validation', 'validation');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Values', 'values');
		$this->insertLocale('en', 'backend', 'form_builder', 'lbl', 'Yesterday', 'yesterday');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'Added', 'The form "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the form "%1$s" and all its submissons?');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'ConfirmDeleteData', 'Are you sure you want to delete this submission?');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'Deleted', 'The form "%1$s" was removed.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'Edited', 'The form "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'HelpIdentifier', 'The identifier is placed in the URL after successfully submitting a form.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'ImportantImmediateUpdate', '<strong>Important</strong>: modifications made here are immediately saved.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'ItemDeleted', 'Submission removed.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'ItemsDeleted', 'Submissions removed.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'NoData', 'There are no submissions yet.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'NoFields', 'There are no fields yet.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'NoItems', 'There are no forms yet.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'NoValues', 'There are no values yet.');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'OneSentForm', '1 submission');
		$this->insertLocale('en', 'backend', 'form_builder', 'msg', 'SentForms', '%1$s submissions');

		// frontend (en)
		$this->insertLocale('en', 'frontend', 'core', 'err', 'FieldIsRequired', 'This field is required.');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'FormTimeout', 'Slow down cowboy, there needs to be some time between each submission.');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'NumericCharactersOnly', 'Only numeric characters are allowed.');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Content', 'content');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'IP', 'IP');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'SenderInformation', 'sender information');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'SentOn', 'sent on');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'FormBuilderSubject', 'New submission for form "%1$s".');

		// frontend (fr)
		$this->insertLocale('fr', 'frontend', 'core', 'err', 'FieldIsRequired', 'Ce champ est obligatoire.');
		$this->insertLocale('fr', 'frontend', 'core', 'err', 'FormTimeout', 'Ralentissez cow-boy, il faut un certain temps entre chaque soumission.');
		$this->insertLocale('fr', 'frontend', 'core', 'err', 'NumericCharactersOnly', 'Seuls les caractères numériques sont acceptés.');
		$this->insertLocale('fr', 'frontend', 'core', 'lbl', 'Content', 'content');
		$this->insertLocale('fr', 'frontend', 'core', 'lbl', 'IP', 'IP');
		$this->insertLocale('fr', 'frontend', 'core', 'lbl', 'SenderInformation', 'informations sur l\'expéditeur');
		$this->insertLocale('fr', 'frontend', 'core', 'lbl', 'SentOn', 'envoyé le');
		$this->insertLocale('fr', 'frontend', 'core', 'msg', 'FormBuilderSubject', 'Nouvelle soumission pour la forme "%1$s".');
	}
}

?>