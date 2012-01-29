<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the faq module
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class FaqInstaller extends ModuleInstaller
{
	/**
	 * @var	int
	 */
	private $defaultCategoryId;

	/**
	 * Add a category for a language
	 *
	 * @param string $language
	 * @param string $title
	 * @param string $url
	 * @return int
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
	 * Fetch the id of the first category in this language we come across
	 *
	 * @param string $language
	 * @return int
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar(
			'SELECT id
			 FROM faq_categories
			 WHERE language = ?',
			array((string) $language));
	}

	/**
	 * Insert an empty admin dashboard sequence
	 */
	private function insertWidget()
	{
		$feedback = array(
			'column' => 'right',
			'position' => 1,
			'hidden' => false,
			'present' => true
		);

		$this->insertDashboardWidget('faq', 'feedback', $feedback);
	}

	/**
	 * Install the module
	 */
	public function install()
	{
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		$this->addModule('faq');

		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->makeSearchable('faq');
		$this->setModuleRights(1, 'faq');

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
		$this->setActionRights(1, 'faq', 'process_feedback');
		$this->setActionRights(1, 'faq', 'delete_feedback');
		$this->setActionRights(1, 'faq', 'settings');

		$faqId = $this->insertExtra('faq', 'block', 'Faq');
		$this->insertExtra('faq', 'widget', 'MostReadQuestions', 'most_read');
		$this->insertExtra('faq', 'widget', 'AskOwnQuestion', 'own_question');

		$this->setSetting('faq', 'overview_num_items_per_category', 0);
		$this->setSetting('faq', 'most_read_num_items', 0);
		$this->setSetting('faq', 'related_num_items', 0);
		$this->setSetting('faq', 'spamfilter', false);
		$this->setSetting('faq', 'allow_feedback', false);
		$this->setSetting('faq', 'allow_own_question', false);
		$this->setSetting('faq', 'send_email_on_new_feedback', false);

		foreach($this->getLanguages() as $language)
		{
			$this->defaultCategoryId = $this->getCategory($language);

			// no category exists
			if($this->defaultCategoryId == 0)
			{
				$this->defaultCategoryId = $this->addCategory($language, 'Default', 'default');
			}

			// check if a page for blog already exists in this language
			if(!(bool) $this->getDB()->getVar(
				'SELECT COUNT(p.id)
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

		$this->insertWidget();

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationBlogId = $this->setNavigation($navigationModulesId, 'Faq');
		$this->setNavigation($navigationBlogId, 'Questions', 'faq/index', array('faq/add',	'faq/edit'));
		$this->setNavigation($navigationBlogId, 'Categories', 'faq/categories', array('faq/add_category',	'faq/edit_category'));
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Faq', 'faq/settings');
	}
}
