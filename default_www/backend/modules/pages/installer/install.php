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

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// import data
		$this->importData();

		// set rights
		$this->setRights();
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
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// insert sitemap
				$this->insertPage(array('id' => 2,
										'template_id' => $templateIds['Default'],
										'title' => ucfirst($this->getLocale('Sitemap', 'core', $language, 'lbl', 'frontend')),
										'type' => 'footer',
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sitemap.txt'),
										array('extra_id' => $extras['sitemap_widget_sitemap']),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// insert disclaimer
				$this->insertPage(array('id' => 3,
										'template_id' => $templateIds['Default'],
										'title' => ucfirst($this->getLocale('Disclaimer', 'core', $language, 'lbl', 'frontend')),
										'type' => 'footer',
										'language' => $language),
										array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/disclaimer.txt'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

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
										array('extra_id' => $extras['search_form'], 'position' => 'top'));
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
												'active' => 'Y',
												'data' => serialize(array('format' => '[main,right]',
																			'names' => array('main', 'right'))));
												// @todo: default extras

		$templates['core']['Home'] = array('theme' => 'core',
											'label' => 'Home',
											'path' => 'core/layout/templates/home.tpl',
											'active' => 'Y',
											'data' => serialize(array('format' => '[main,right]',
																		'names' => array('main', 'right'))));
											// @todo: default extras

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
												'active' => 'Y',
												'data' => serialize(array('format' => '[/,/,top,top],[/,/,/,/],[left,main,main,main]',
																			'names' => array('main', 'left', 'top'))));
												// @todo: default extras

		$templates['triton']['Home'] = array('theme' => 'triton',
												'label' => 'Home',
												'path' => 'core/layout/templates/home.tpl',
												'active' => 'Y',
												'data' => serialize(array('format' => '[/,/,top,top],[/,/,/,/],[main,main,main,main],[left,left,right,right]',
																			'names' => array('main', 'left', 'right', 'top'))));
												// @todo: default extras

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
										array('extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'left'),
										array('extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'right'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// insert blog
				$this->insertPage(array('title' => 'Blog',
										'template_id' => $templateIds['Default'],
										'language' => $language),
										null,
										array('extra_id' => $extras['blog_block']),
										array('extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'left'),
										array('extra_id' => $extras['blog_widget_categories'], 'position' => 'left'),
										array('extra_id' => $extras['blog_widget_archive'], 'position' => 'left'),
										array('extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'left'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// insert about us page
				$aboutUsId = $this->insertPage(array('template_id' => $templateIds['Default'],
														'title' => ucfirst($this->getLocale('AboutUs', 'core', $language, 'lbl', 'frontend')),
														'parent_id' => 1,
														'language' => $language),
														null,
														array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// location
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => ucfirst($this->getLocale('Location', 'core', $language, 'lbl', 'frontend')),
										'parent_id' => $aboutUsId,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample2.txt'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// team
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Team',
										'parent_id' => $aboutUsId,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample2.txt'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// history
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => ucfirst($this->getLocale('History', 'core', $language, 'lbl', 'frontend')),
										'parent_id' => 1,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample1.txt'),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/sample2.txt'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// insert contact page
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Contact',
										'parent_id' => 1,
										'language' => $language),
										null,
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/contact.txt'),
										array('extra_id' => $extras['contact_block']),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));

				// insert lorem ipsum test page
				$this->insertPage(array('template_id' => $templateIds['Default'],
										'title' => 'Lorem ipsum',
										'type' => 'root',
										'language' => $language,
										'hidden' => 'Y'),
										array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
										array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/lorem_ipsum.txt'),
										array('extra_id' => $extras['search_form'], 'position' => 'top'));
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