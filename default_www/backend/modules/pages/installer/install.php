<?php

/**
 * Installer for the pages module
 *
 * @package		installer
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class PagesInstall extends ModuleInstaller
{
	/**
	 * Name of the default theme
	 *
	 * @var	string
	 */
	private $defaultTheme = 'triton';


	/**
	 * Class constructor.
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'pages' as a module
		$this->addModule('pages', 'The module to manage your pages and website structure.');

		// import data
		$this->importData();

		// set rights
		$this->setRights();

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}


	/**
	 * Import the data
	 *
	 * @return	void
	 */
	private function importData()
	{
		// insert templates
		$this->insertTemplates();

		// insert required pages
		$this->insertPagesAndExtras();

		// install example data if requested
		if($this->installExample()) $this->installExampleData();
	}


	/**
	 * Insert the pages
	 *
	 * @return	void
	 */
	private function insertPagesAndExtras()
	{
		// insert/get extra ids
		$extras['search'] = $this->insertExtra('search', 'block', 'Search', null, null, 'N', 2000);
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);
		$extras['sitemap_widget_sitemap'] = $this->insertExtra('pages', 'widget', 'Sitemap', 'sitemap', null, 'N', 1);

		// fetch template ids
		$templateIds = $this->getDB()->getPairs('SELECT label, id FROM pages_templates WHERE theme = ?', array($this->defaultTheme));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if pages already exist for this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(id) FROM pages WHERE language = ?', array($language)))
			{
				// insert homepage
				$this->insertPage(array('id' => 1,
										'parent_id' => 0,
										'template_id' => $templateIds['Home'],
										'title' => 'Home',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'N'),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert search page
				$this->insertPage(array('id' => 2,
										'template_id' => $templateIds['Default'],
										'title' => 'Search',
										'type' => 'root',
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $extras['search']),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert sitemap
				$this->insertPage(array('id' => 3,
										'template_id' => $templateIds['Default'],
										'title' => 'Sitemap',
										'type' => 'footer',
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sitemap.txt'),
										array('extra_id' => $extras['sitemap_widget_sitemap']),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert disclaimer
				$this->insertPage(array('id' => 4,
										'template_id' => $templateIds['Default'],
										'title' => 'Disclaimer',
										'type' => 'footer',
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/disclaimer.txt'),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert 404
				$this->insertPage(array('id' => 404,
										'template_id' => $templateIds['Default'],
										'title' => '404',
										'type' => 'root',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'N'),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/404.txt'),
										array('extra_id' => $extras['sitemap_widget_sitemap']),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));
			}
		}
	}


	/**
	 * Insert the templates
	 *
	 * @return	void
	 */
	private function insertTemplates()
	{
		/*
		 * Fallback templates
		 */

		// build templates
		$templates['core']['Default'] = array('theme' => 'core',
												'label' => 'Default',
												'path' => 'core/layout/templates/default.tpl',
												'num_blocks' => 3,
												'active' => 'Y',
												'data' => serialize(array('format' => '[1,2],[1,3]',
																			'names' => array('Main Content', 'Sidebar: block 1', 'Sidebar: block 2'),
																			'default_extras' => array('editor', 'editor', 'editor'))));

		$templates['core']['Home'] = array('theme' => 'core',
											'label' => 'Home',
											'path' => 'core/layout/templates/home.tpl',
											'num_blocks' => 3,
											'active' => 'Y',
											'data' => serialize(array('format' => '[1,2],[1,3]',
																		'names' => array('Main Content', 'Sidebar: block 1', 'Sidebar: block 2'),
																		'default_extras' => array('editor', 'editor', 'editor'))));

		// insert templates
		$templateIds['core']['Default'] = $this->getDB()->insert('pages_templates', $templates['core']['Default']);
		$templateIds['core']['Home'] = $this->getDB()->insert('pages_templates', $templates['core']['Home']);


		/*
		 * Triton templates
		 */

		// search will be installed by default; already link it to this template
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// build templates
		$templates['triton']['Default'] = array('theme' => 'triton',
												'label' => 'Default',
												'path' => 'core/layout/templates/default.tpl',
												'num_blocks' => 10,
												'active' => 'Y',
												'data' => serialize(array('format' => '[/,/,9,9],[/,/,10,10],[/,/,/,/],[5,1,1,1],[6,2,2,2],[7,3,3,3],[8,4,4,4]',
																			'names' => array('Editor', 'Editor', 'Editor', 'Editor', 'Widget', 'Widget', 'Widget', 'Widget', 'Advertisement (468x60)', 'Search'),
																			'default_extras' => array('editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', $extras['search_form']))));

		$templates['triton']['Home'] = array('theme' => 'triton',
												'label' => 'Home',
												'path' => 'core/layout/templates/home.tpl',
												'num_blocks' => 10,
												'active' => 'Y',
												'data' => serialize(array('format' => '[/,/,9,9],[/,/,10,10],[/,/,/,/],[1,1,1,1],[2,2,2,2],[3,3,6,6],[4,4,7,7],[5,5,8,8]',
																			'names' => array('Editor', 'Editor', 'Widget', 'Widget', 'Widget', 'Widget', 'Widget', 'Widget', 'Advertisement (468x60)', 'Search'),
																			'default_extras' => array('editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', $extras['search_form']))));

		// insert templates
		$templateIds['triton']['Default'] = $this->getDB()->insert('pages_templates', $templates['triton']['Default']);
		$templateIds['triton']['Home'] = $this->getDB()->insert('pages_templates', $templates['triton']['Home']);

		/*
		 * General theme settings
		 */

		// set default theme (to be installed next)
		$this->setSetting('core', 'theme', $this->defaultTheme, true);

		// set default template
		$this->setSetting('pages', 'default_template', $templateIds[$this->defaultTheme]['Default']);

		// recalculate num_blocks
		$this->setSetting('pages', 'template_max_blocks', (int) $this->getDB()->getVar('SELECT MAX(num_blocks) FROM pages_templates'), true);

		// disable meta navigation
		$this->setSetting('pages', 'meta_navigation', false);

	}


	/**
	 * Install example data
	 *
	 * @return	void
	 */
	private function installExampleData()
	{
		// insert/get extra ids
		$extras['blog_block'] = $this->insertExtra('blog', 'block', 'Blog', null, null, 'N', 1000);
		$extras['blog_widget_recent_comments'] = $this->insertExtra('blog', 'widget', 'RecentComments', 'recent_comments', null, 'N', 1001);
		$extras['blog_widget_categories'] = $this->insertExtra('blog', 'widget', 'Categories', 'categories', null, 'N', 1002);
		$extras['blog_widget_archive'] = $this->insertExtra('blog', 'widget', 'Archive', 'archive', null, 'N', 1003);
		$extras['blog_widget_recent_articles_full'] = $this->insertExtra('blog', 'widget', 'RecentArticlesFull', 'recent_articles_full', null, 'N', 1004);
		$extras['blog_widget_recent_articles_list'] = $this->insertExtra('blog', 'widget', 'RecentArticlesList', 'recent_articles_list', null, 'N', 1005);
		$extras['search'] = $this->insertExtra('search', 'block', 'Search', null, null, 'N', 2000);
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);
		$extras['sitemap_widget_sitemap'] = $this->insertExtra('pages', 'widget', 'Sitemap', 'sitemap', null, 'N', 1);
		$extras['contact_block'] = $this->insertExtra('contact', 'block', 'Contact', null, 'a:1:{s:3:"url";s:0:"";}', 'N', 6);

		// fetch template ids
		$templateIds = $this->getDB()->getPairs('SELECT label, id FROM pages_templates WHERE theme = ?', array($this->defaultTheme));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if pages already exist for this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(id) FROM pages WHERE language = ? AND id > ?', array($language, 404)))
			{
				// re-insert homepage
				$this->insertPage(array('id' => 1,
										'parent_id' => 0,
										'template_id' => $templateIds['Home'],
										'title' => 'Home',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'N'),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => ''),
										array('extra_id' => $extras['blog_widget_recent_articles_list']),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['blog_widget_recent_comments']),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert blog
				$this->insertPage(array('title' => 'Blog',
										'template_id' => $templateIds['Default'],
										'language' => $language),
										null,
										array('html' => ''),
										array('extra_id' => $extras['blog_block']),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['blog_widget_recent_comments']),
										array('extra_id' => $extras['blog_widget_categories']),
										array('extra_id' => $extras['blog_widget_archive']),
										array('extra_id' => $extras['blog_widget_recent_articles_list']),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert about us page
				$aboutUsId = $this->insertPage(array('template_id' => $templateIds['Default'],
														'title' => 'About us',
														'parent_id' => 1,
														'language' => $language),
														null,
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''),
														array('html' => ''));

				// location
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Location',
										'parent_id' => $aboutUsId,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample2.txt'),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// team
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Team',
										'parent_id' => $aboutUsId,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample2.txt'),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// history
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'History',
										'parent_id' => 1,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample2.txt'),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert contact page
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Contact',
										'parent_id' => 1,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/contact.txt'),
										array('extra_id' => $extras['contact_block']),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));

				// insert lorem ipsum test page
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Lorem ipsum',
										'type' => 'root',
										'language' => $language,
										'hidden' => 'Y',
										'no_follow' => 'Y'),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/lorem_ipsum.txt'),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('html' => ''),
										array('extra_id' => $extras['search_form']));
			}
		}
	}


	/**
	 * Set the rights
	 *
	 * @return	void
	 */
	private function setRights()
	{
		// module rights
		$this->setModuleRights(1, 'pages');

		// action rights
		$this->setActionRights(1, 'pages', 'get_info');
		$this->setActionRights(1, 'pages', 'move');

		$this->setActionRights(1, 'pages', 'index');
		$this->setActionRights(1, 'pages', 'add');
		$this->setActionRights(1, 'pages', 'delete');
		$this->setActionRights(1, 'pages', 'edit');

		$this->setActionRights(1, 'pages', 'templates');
		$this->setActionRights(1, 'pages', 'add_template');
		$this->setActionRights(1, 'pages', 'edit_template');
		$this->setActionRights(1, 'pages', 'delete_template');

		$this->setActionRights(1, 'pages', 'settings');
	}
}

?>