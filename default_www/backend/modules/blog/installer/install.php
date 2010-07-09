<?php

class BlogInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

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
		foreach($languages as $language)
		{
			// add default category
			$defaultCategoryId = $this->addCategory($language, 'Default', 'default');

			// feedburner URL
			$this->setSetting('blog', 'feedburner_url_'. $language, '');

			// insert default category
			$this->setSetting('blog', 'default_category_'. $language, $defaultCategoryId);

			// RSS settings
			$this->setSetting('blog', 'rss_meta_'. $language, true);
			$this->setSetting('blog', 'rss_title_'. $language, 'RSS');
			$this->setSetting('blog', 'rss_description_'. $language, '');
		}

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
	}


	private function addCategory($language, $name, $url)
	{
		return (int) $this->db->insert('blog_categories', array('language' => (string) $language, 'name' => (string) $name, 'url' => (string) $url));
	}
}

?>