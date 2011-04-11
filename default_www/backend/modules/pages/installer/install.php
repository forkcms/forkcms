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

		// set settings
		$this->setSettings();

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

		// install example data if requested
		if($this->installExample()) $this->installExampleData();

		// insert required pages
		else $this->insertPagesAndExtras();
	}


	/**
	 * Insert the pages
	 *
	 * @return	void
	 */
	private function insertPagesAndExtras()
	{
		// insert extra
		$sitemapID = $this->insertExtra('pages', 'widget', 'Sitemap', 'sitemap', null, 'N', 1);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if pages already exist for this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(id)
												FROM pages
												WHERE language = ?',
												array($language)))
			{
				// insert homepage
				$this->insertPage(array('id' => 1,
										'parent_id' => 0,
										'template_id' => 2,
										'title' => 'Home',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'N'));

				// insert sitemap
				$this->insertPage(array('id' => 2,
										'title' => 'Sitemap',
										'type' => 'footer',
										'language' => $language),
									null,
									array('extra_id' => $sitemapID));

				// insert disclaimer
				$this->insertPage(array('id' => 3,
										'title' => 'Disclaimer',
										'type' => 'footer',
										'language' => $language),
									null,
									array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/disclaimer.txt'));

				// insert about
				$this->insertPage(array('id' => 4,
										'title' => 'About',
										'type' => 'meta',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'Y'));

				// insert 404
				$this->insertPage(array('id' => 404,
										'title' => '404',
										'type' => 'root',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'N'),
									null,
									array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/404.txt'),
									array('extra_id' => $sitemapID));

				// insert lorem ipsum test page
				$this->insertPage(array('id' => 404,
										'title' => '404',
										'type' => 'root',
										'language' => $language,
										'allow_move' => 'N',
										'allow_delete' => 'N'),
									null,
									array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/404.txt'),
									array('extra_id' => $sitemapID));

				// insert lorem ipsum test page
				$this->insertPage(array('title' => 'Lorem ipsum',
										'type' => 'root',
										'language' => $language,
										'hidden' => 'Y',
										'no_follow' => 'Y'),
									null,
									array('html' => PATH_WWW . '/backend/modules/pages/installer/data/' . $language . '/lorem_ipsum.txt'),
									array('html' => ''));
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
		// insert template
		try
		{
			$this->getDB()->insert('pages_templates', array('id' => 1, 'label' => 'Default', 'path' => 'core/layout/templates/default.tpl', 'num_blocks' => 3, 'active' => 'Y', 'data' => 'a:3:{s:6:"format";s:11:"[1,2],[1,3]";s:5:"names";a:3:{i:0;s:12:"Main Content";i:1;s:16:"Sidebar: block 1";i:2;s:16:"Sidebar: block 2";}s:14:"default_extras";a:3:{i:0;s:6:"editor";i:1;s:6:"editor";i:2;s:6:"editor";}}'));
			$this->getDB()->insert('pages_templates', array('id' => 2, 'label' => 'Home', 'path' => 'core/layout/templates/home.tpl', 'num_blocks' => 3, 'active' => 'Y', 'data' => 'a:3:{s:6:"format";s:11:"[1,2],[1,3]";s:5:"names";a:3:{i:0;s:12:"Main Content";i:1;s:16:"Sidebar: block 1";i:2;s:16:"Sidebar: block 2";}s:14:"default_extras";a:3:{i:0;s:6:"editor";i:1;s:6:"editor";i:2;s:6:"editor";}}'));
		}
		catch(Exception $e)
		{
			if(substr_count($e->getMessage(), 'Duplicate entry') == 0) throw $e;
		}

		// recalculate num_blocks
		$this->setSetting('pages', 'template_max_blocks', (int) $this->getDB()->getVar('SELECT MAX(num_blocks) FROM pages_templates'), true);
		$this->setSetting('pages', 'meta_navigation', false);
	}


	/**
	 * Install example data
	 *
	 * @return	void
	 */
	private function installExampleData()
	{
		// set theme
		$this->setSetting('core', 'theme', 'triton', true);

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

		// build templates
		$defaultTemplate = array('label' => 'Triton - Default',
								'path' => 'core/layout/templates/default.tpl',
								'num_blocks' => 10,
								'active' => 'Y',
								'data' => serialize(array('format' => '[/,/,9,9],[/,/,10,10],[/,/,/,/],[5,1,1,1],[6,2,2,2],[7,3,3,3],[8,4,4,4]',
															'names' => array('Editor', 'Editor', 'Editor', 'Editor', 'Widget', 'Widget', 'Widget', 'Widget', 'Advertisement (468x60)', 'Search'),
															'default_extras' => array('editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', $extras['search_form']))));
		$homeTemplate = array('label' => 'Triton - Home',
								'path' => 'core/layout/templates/home.tpl',
								'num_blocks' => 10,
								'active' => 'Y',
								'data' => serialize(array('format' => '[/,/,9,9],[/,/,10,10],[/,/,/,/],[1,1,1,1],[2,2,2,2],[3,3,6,6],[4,4,7,7],[5,5,8,8]',
															'names' => array('Editor', 'Editor', 'Widget', 'Widget', 'Widget', 'Widget', 'Widget', 'Widget', 'Advertisement (468x60)', 'Search'),
															'default_extras' => array('editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', 'editor', $extras['search_form']))));

		// insert templates
		$templateIds['default'] = $this->getDB()->insert('pages_templates', $defaultTemplate);
		$templateIds['home'] = $this->getDB()->insert('pages_templates', $homeTemplate);

		// set default template
		$this->setSetting('pages', 'default_template', $templateIds['default']);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if pages already exist for this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(id) FROM pages WHERE language = ?', array($language)))
			{
				// insert homepage
				$this->insertPage(array('id' => 1,
										'parent_id' => 0,
										'template_id' => $templateIds['home'],
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
				$this->insertPage(array('id' => 10,
										'title' => 'Blog',
										'template_id' => $templateIds['default'],
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

				// insert sitemap
				$this->insertPage(array('id' => 2,
										'template_id' => $templateIds['default'],
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
				$this->insertPage(array('id' => 3,
										'template_id' => $templateIds['default'],
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

				// insert about us page
				$aboutUsId = $this->insertPage(array('template_id' => $templateIds['default'],
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
				$this->insertPage(array('template_id' => $templateIds['default'],
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
				$this->insertPage(array('template_id' => $templateIds['default'],
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
				$this->insertPage(array('template_id' => $templateIds['default'],
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
				$this->insertPage(array('template_id' => $templateIds['default'],
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

				// insert search page
				$this->insertPage(array('template_id' => $templateIds['default'],
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

				// insert 404
				$this->insertPage(array('id' => 404,
										'template_id' => $templateIds['default'],
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

				// insert lorem ipsum test page
				$this->insertPage(array('template_id' => $templateIds['default'],
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

		// reset blocks
		$this->setSetting('pages', 'template_max_blocks', (int) $this->getDB()->getVar('SELECT MAX(num_blocks) FROM pages_templates'), true);
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


	/**
	 * Store the settings
	 *
	 * @return	void
	 */
	private function setSettings()
	{
		// general settings
		$this->setSetting('pages', 'template_max_blocks', (int) $this->getDB()->getVar('SELECT MAX(num_blocks) FROM pages_templates'));
		$this->setSetting('pages', 'meta_navigation', true);
		$this->setSetting('pages', 'default_template', 1);
	}
}

?>