<?php

/**
 * Installer for the faq module
 *
 * @package		installer
 * @subpackage	faq
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.1
 */
class FaqInstall extends ModuleInstaller
{
	/**
	 * Default category id
	 *
	 * @var	int
	 */
	private $defaultCategoryId;


	/**
	 * Add a category for a language
	 *
	 * @return	int
	 * @param	string $language	The language to use.
	 * @param	string $title		The title of the category.
	 * @param	string $url			The URL for the category.
	 */
	private function addCategory($language, $title, $url)
	{
		// build array
		$item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
		$item['language'] = (string) $language;
		$item['title'] = (string) $title;
		$item['sequence'] = 1;

		return (int) $this->getDB()->insert('faq_categories', $item);
	}


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
		$this->addModule('faq', 'The faq module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// make module searchable
		$this->makeSearchable('faq');

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
		$faqId = $this->insertExtra('faq', 'block', 'Faq', null, null, 'N', 5000);
		$this->insertExtra('faq', 'block', 'Category', 'category', null, 'N', 5001);
		$this->insertExtra('faq', 'widget', 'MostReadQuestions', 'most_read', null, 'N', 5002);
		$this->insertExtra('faq', 'widget', 'AskOwnQuestion', 'own_question', null, 'N', 5003);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// fetch current categoryId
			$this->defaultCategoryId = $this->getCategory($language);

			// no category exists
			if($this->defaultCategoryId == 0)
			{
				// add category
				$this->defaultCategoryId = $this->addCategory($language, 'Default', 'default');
			}

			// check if a page for blog already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($faqId, $language)))
			{
				// insert page
				$this->insertPage(array('title' => 'FAQ',
										'language' => $language),
									null,
									array('extra_id' => $faqId));
			}
		}
	}


	/**
	 * Fetch the id of the first category in this language we come across
	 *
	 * @return	int
	 * @param	string $language	The language to use.
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar('SELECT id FROM faq_categories WHERE language = ?', array((string) $language));
	}
}

?>