<?php

/**
 * BlogInstall
 * Installer for the blog module
 *
 * @package		installer
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BlogInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/blog/installer/install.sql');

		// add 'blog' as a module
		$this->addModule('blog', 'The blog module.');

		// general settings
		$this->setSetting('blog', 'allow_comments', true);
		$this->setSetting('blog', 'requires_akismet', true);
		$this->setSetting('blog', 'requires_google_maps', false);
		$this->setSetting('blog', 'spamfilter', false);
		$this->setSetting('blog', 'moderation', true);
		$this->setSetting('blog', 'ping_services', true);
		$this->setSetting('blog', 'overview_num_items', 10);
		$this->setSetting('blog', 'recent_articles_num_items', 5);
		$this->setSetting('blog', 'max_num_revisions', 20);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// fetch current categoryId
			$currentCategoryId = $this->getCategory($language);

			// no category exists
			if($currentCategoryId == 0)
			{
				// add default category
				$defaultCategoryId = $this->addCategory($language, 'Default', 'default');

				// insert default category setting
				$this->setSetting('blog', 'default_category_'. $language, $defaultCategoryId, true);
			}

			// category exists
			else
			{
				// current default categoryId
				$currentDefaultCategoryId = $this->getSetting('blog', 'default_category_'. $language);

				// does not exist
				if(!$this->existsCategory($language, $currentDefaultCategoryId))
				{
					// insert default category setting
					$this->setSetting('blog', 'default_category_'. $language, $currentCategoryId, true);
				}
			}

			// feedburner URL
			$this->setSetting('blog', 'feedburner_url_'. $language, '');

			// RSS settings
			$this->setSetting('blog', 'rss_meta_'. $language, true);
			$this->setSetting('blog', 'rss_title_'. $language, 'RSS');
			$this->setSetting('blog', 'rss_description_'. $language, '');
		}

		// make module searchable
		$this->makeSearchable('blog');

		// module rights
		$this->setModuleRights(1, 'blog');

		// action rights
		$this->setActionRights(1, 'blog', 'add_category');
		$this->setActionRights(1, 'blog', 'add');
		$this->setActionRights(1, 'blog', 'categories');
		$this->setActionRights(1, 'blog', 'comments');
		$this->setActionRights(1, 'blog', 'delete_category');
		$this->setActionRights(1, 'blog', 'delete');
		$this->setActionRights(1, 'blog', 'edit_category');
		$this->setActionRights(1, 'blog', 'edit');
		$this->setActionRights(1, 'blog', 'import_blogger');
		$this->setActionRights(1, 'blog', 'index');
		$this->setActionRights(1, 'blog', 'mass_comment_action');
		$this->setActionRights(1, 'blog', 'settings');

		// add extra's
		$this->insertExtra(array('module' => 'blog',
									'type' => 'block',
									'label' => 'Blog',
									'action' => null,
									'data' => null,
									'hidden' => 'N',
									'sequence' => 1000));

		$this->insertExtra(array('module' => 'blog',
									'type' => 'widget',
									'label' => 'RecentComments',
									'action' => 'recent_comments',
									'data' => null,
									'hidden' => 'N',
									'sequence' => 1001));

		$this->insertExtra(array('module' => 'blog',
									'type' => 'widget',
									'label' => 'Categories',
									'action' => 'categories',
									'data' => null,
									'hidden' => 'N',
									'sequence' => 1002));

		$this->insertExtra(array('module' => 'blog',
									'type' => 'widget',
									'label' => 'Archive',
									'action' => 'archive',
									'data' => null,
									'hidden' => 'N',
									'sequence' => 1003));

		$this->insertExtra(array('module' => 'blog',
									'type' => 'widget',
									'label' => 'RecentArticles',
									'action' => 'recent_articles',
									'data' => null,
									'hidden' => 'N',
									'sequence' => 1004));
	}


	/**
	 * Add the default category for a language
	 *
	 * @return	int
	 * @param	string $language
	 * @param	string $name
	 * @param	string $url
	 */
	private function addCategory($language, $name, $url)
	{
		return (int) $this->getDB()->insert('blog_categories', array('language' => (string) $language, 'name' => (string) $name, 'url' => (string) $url));
	}


	/**
	 * Does the category with this id exist within this language.
	 *
	 * @return	bool
	 * @param	string $language
	 * @param	int $id
	 */
	private function existsCategory($language, $id)
	{
		return (bool) $this->getDB()->getNumRows('SELECT id FROM blog_categories WHERE id = ? AND language = ?;', array((int) $id, (string) $language));
	}


	/**
 	 * Fetch the id of the first category in this language we come across
 	 *
 	 * @return	int
 	 * @param	string $language
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar('SELECT id FROM blog_categories WHERE language = ?;', (string) $language);
	}
}

?>