<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the blog module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BlogInstaller extends ModuleInstaller
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
	 * @param string $language The language to use.
	 * @param string $title The title of the category.
	 * @param string $url The URL for the category.
	 * @return int
	 */
	private function addCategory($language, $title, $url)
	{
		$item = array();
		$item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
		$item['language'] = (string) $language;
		$item['title'] = (string) $title;

		return (int) $this->getDB()->insert('blog_categories', $item);
	}

	/**
	 * Fetch the id of the first category in this language we come across
	 *
	 * @param string $language The language to use.
	 * @return int
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar('SELECT id FROM blog_categories WHERE language = ?', array((string) $language));
	}

	/**
	 * Insert an empty admin dashboard sequence
	 */
	private function insertWidget()
	{
		$comments = array(
			'column' => 'right',
			'position' => 1,
			'hidden' => false,
			'present' => true
		);

		$this->insertDashboardWidget('blog', 'comments', $comments);
	}

	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'blog' as a module
		$this->addModule('blog');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// general settings
		$this->setSetting('blog', 'allow_comments', true);
		$this->setSetting('blog', 'requires_akismet', true);
		$this->setSetting('blog', 'spamfilter', false);
		$this->setSetting('blog', 'moderation', true);
		$this->setSetting('blog', 'ping_services', true);
		$this->setSetting('blog', 'overview_num_items', 10);
		$this->setSetting('blog', 'recent_articles_full_num_items', 3);
		$this->setSetting('blog', 'recent_articles_list_num_items', 5);
		$this->setSetting('blog', 'max_num_revisions', 20);

		$this->makeSearchable('blog');

		// module rights
		$this->setModuleRights(1, 'blog');

		// action rights
		$this->setActionRights(1, 'blog', 'add_category');
		$this->setActionRights(1, 'blog', 'add');
		$this->setActionRights(1, 'blog', 'categories');
		$this->setActionRights(1, 'blog', 'comments');
		$this->setActionRights(1, 'blog', 'delete_category');
		$this->setActionRights(1, 'blog', 'delete_spam');
		$this->setActionRights(1, 'blog', 'delete');
		$this->setActionRights(1, 'blog', 'edit_category');
		$this->setActionRights(1, 'blog', 'edit_comment');
		$this->setActionRights(1, 'blog', 'edit');
		$this->setActionRights(1, 'blog', 'import_blogger');
		$this->setActionRights(1, 'blog', 'index');
		$this->setActionRights(1, 'blog', 'mass_comment_action');
		$this->setActionRights(1, 'blog', 'settings');

		// insert dashboard widget
		$this->insertWidget();

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationBlogId = $this->setNavigation($navigationModulesId, 'Blog');
		$this->setNavigation($navigationBlogId, 'Articles', 'blog/index', array('blog/add',	'blog/edit', 'blog/import_blogger'));
		$this->setNavigation($navigationBlogId, 'Comments', 'blog/comments', array('blog/edit_comment'));
		$this->setNavigation($navigationBlogId, 'Categories', 'blog/categories', array('blog/add_category',	'blog/edit_category'));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Blog', 'blog/settings');

		// add extra's
		$blogId = $this->insertExtra('blog', 'block', 'Blog', null, null, 'N', 1000);
		$this->insertExtra('blog', 'widget', 'RecentComments', 'recent_comments', null, 'N', 1001);
		$this->insertExtra('blog', 'widget', 'Categories', 'categories', null, 'N', 1002);
		$this->insertExtra('blog', 'widget', 'Archive', 'archive', null, 'N', 1003);
		$this->insertExtra('blog', 'widget', 'RecentArticlesFull', 'recent_articles_full', null, 'N', 1004);
		$this->insertExtra('blog', 'widget', 'RecentArticlesList', 'recent_articles_list', null, 'N', 1005);

		// get search extra id
		$searchId = (int) $this->getDB()->getVar(
			'SELECT id FROM modules_extras
			 WHERE module = ? AND type = ? AND action = ?',
			array('search', 'widget', 'form')
		);

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

			// feedburner URL
			$this->setSetting('blog', 'feedburner_url_' . $language, '');

			// RSS settings
			$this->setSetting('blog', 'rss_meta_' . $language, true);
			$this->setSetting('blog', 'rss_title_' . $language, 'RSS');
			$this->setSetting('blog', 'rss_description_' . $language, '');

			// check if a page for blog already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($blogId, $language)))
			{
				$this->insertPage(
					array('title' => 'Blog', 'language' => $language),
					null,
					array('extra_id' => $blogId, 'position' => 'main'),
					array('extra_id' => $searchId, 'position' => 'top')
				);
			}

			if($this->installExample())
			{
				$this->installExampleData($language);
			}
		}
	}

	/**
	 * Install example data
	 *
	 * @param string $language The language to use.
	 */
	private function installExampleData($language)
	{
		// get db instance
		$db = $this->getDB();

		// check if blogposts already exist in this language
		if(!(bool) $db->getVar('SELECT COUNT(id) FROM blog_posts WHERE language = ?', array($language)))
		{
			// insert sample blogpost 1
			$db->insert('blog_posts', array(
				'id' => 1,
				'category_id' => $this->defaultCategoryId,
				'user_id' => $this->getDefaultUserID(),
				'meta_id' => $this->insertMeta('Nunc sediam est', 'Nunc sediam est', 'Nunc sediam est', 'nunc-sediam-est'),
				'language' => $language,
				'title' => 'Nunc sediam est',
				'introduction' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
				'text' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
				'status' => 'active',
				'publish_on' => gmdate('Y-m-d H:i:00'),
				'created_on' => gmdate('Y-m-d H:i:00'),
				'edited_on' => gmdate('Y-m-d H:i:00'),
				'hidden' => 'N',
				'allow_comments' => 'Y',
				'num_comments' => '3'
			));

			// insert sample blogpost 2
			$db->insert('blog_posts', array(
				'id' => 2,
				'category_id' => $this->defaultCategoryId,
				'user_id' => $this->getDefaultUserID(),
				'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
				'language' => $language,
				'title' => 'Lorem ipsum',
				'introduction' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
				'text' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
				'status' => 'active',
				'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
				'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
				'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
				'hidden' => 'N',
				'allow_comments' => 'Y',
				'num_comments' => '0'
			));

			// insert example comment 1
			$db->insert('blog_comments', array(
				'post_id' => 1,
				'language' => $language,
				'created_on' => gmdate('Y-m-d H:i:00'),
				'author' => 'Matthias Mullie',
				'email' => 'forkcms-sample@mullie.eu',
				'website' => 'http://www.mullie.eu',
				'text' => 'cool!',
				'type' => 'comment',
				'status' => 'published',
				'data' => null
			));

			// insert example comment 2
			$db->insert('blog_comments', array(
				'post_id' => 1,
				'language' => $language,
				'created_on' => gmdate('Y-m-d H:i:00'),
				'author' => 'Davy Hellemans',
				'email' => 'forkcms-sample@spoon-library.com',
				'website' => 'http://www.spoon-library.com',
				'text' => 'awesome!',
				'type' => 'comment',
				'status' => 'published',
				'data' => null
			));

			// insert example comment 3
			$db->insert('blog_comments', array(
				'post_id' => 1,
				'language' => $language,
				'created_on' => gmdate('Y-m-d H:i:00'),
				'author' => 'Tijs Verkoyen',
				'email' => 'forkcms-sample@sumocoders.be',
				'website' => 'http://www.sumocoders.be',
				'text' => 'wicked!',
				'type' => 'comment',
				'status' => 'published',
				'data' => null
			));
		}
	}
}
