<?php

/**
 * Installer for the faq module
 *
 * @package		installer
 * @subpackage	faq
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class FaqInstall extends ModuleInstaller
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
		$this->addModule('faq', 'The faq module.');

		// module rights
		$this->setModuleRights(1, 'faq');

		// action rights
		$this->setActionRights(1, 'faq', 'index');
		$this->setActionRights(1, 'faq', 'add');
		$this->setActionRights(1, 'faq', 'edit');
		$this->setActionRights(1, 'faq', 'delete');
		$this->setActionRights(1, 'faq', 'sequence');
		$this->setActionRights(1, 'faq', 'categories');
		$this->setActionRights(1, 'faq', 'add_category');
		$this->setActionRights(1, 'faq', 'edit_category');
		$this->setActionRights(1, 'faq', 'delete_category');
		$this->setActionRights(1, 'faq', 'sequence_questions');

		// extras
		$this->insertExtra('faq', 'block', 'Faq', 'index', null, 'N', 9001);
		$this->insertExtra('faq', 'block', 'Category', 'category', null, 'N', 9002);

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Faq', 'FAQ');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Questions', 'vragen');
		$this->insertLocale('nl', 'backend', 'faq', 'lbl', 'AddQuestion', 'vraag toevoegen');
		$this->insertLocale('nl', 'backend', 'faq', 'lbl', 'Answer', 'antwoord');
		$this->insertLocale('nl', 'backend', 'faq', 'lbl', 'Question', 'vraag');
		$this->insertLocale('nl', 'backend', 'faq', 'err', 'AnswerIsRequired', 'Het antwoord is verplicht.');
		$this->insertLocale('nl', 'backend', 'faq', 'err', 'CategoryIsRequired', 'Gelieve een categorie te selecteren.');
		$this->insertLocale('nl', 'backend', 'faq', 'err', 'QuestionIsRequired', 'De vraag is verplicht.');
		$this->insertLocale('nl', 'backend', 'faq', 'msg', 'EditQuestion', 'Bewerk vraag "%1$s');
		$this->insertLocale('nl', 'backend', 'faq', 'msg', 'NoQuestionInCategory', 'Er zijn geen vragen in deze categorie.');
		$this->insertLocale('nl', 'backend', 'faq', 'msg', 'NoCategories', 'Er zijn nog geen categorieën, gelieve eerst een <a href="%s$1">categorie toe te voegen</a>.');

		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'NoQuestionsInCategory', 'Er zijn geen vragen in deze categorie.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Faq', 'FAQ');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Questions', 'questions');
		$this->insertLocale('en', 'backend', 'faq', 'lbl', 'Answer', 'answer');
		$this->insertLocale('en', 'backend', 'faq', 'lbl', 'AddQuestion', 'add question');
		$this->insertLocale('en', 'backend', 'faq', 'lbl', 'Question', 'question');
		$this->insertLocale('en', 'backend', 'faq', 'err', 'AnswerIsRequired', 'The answer is required.');
		$this->insertLocale('en', 'backend', 'faq', 'err', 'CategoryIsRequired', 'Please select a category.');
		$this->insertLocale('en', 'backend', 'faq', 'err', 'QuestionIsRequired', 'The question is required.');
		$this->insertLocale('en', 'backend', 'faq', 'msg', 'NoQuestionInCategory', 'There are no questions in this category.');
		$this->insertLocale('en', 'backend', 'faq', 'msg', 'EditQuestion', 'Edit question "%1$s');
		$this->insertLocale('en', 'backend', 'faq', 'msg', 'NoCategories', 'There are no categories yet, please <a href="%s$1">create a category</a> first.');

		$this->insertLocale('en', 'frontend', 'core', 'msg', 'NoQuestionsInCategory', 'There are no questions in this category.');
	}
}

?>