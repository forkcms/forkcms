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

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'pages', 'err', 'CantBeMoved', 'Pagina kan niet verplaatst worden.');
		$this->insertLocale('nl', 'backend', 'pages', 'err', 'DeleteTemplate', 'Je kan deze template niet verwijderen.');
		$this->insertLocale('nl', 'backend', 'pages', 'err', 'HomeCantHaveBlocks', 'Je kan geen modules aan de homepage koppelen.');
		$this->insertLocale('nl', 'backend', 'pages', 'err', 'InvalidTemplateSyntax', 'Ongeldige syntax.');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'EditModuleContent', 'wijzig module inhoud');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'ExtraTypeBlock', 'module');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'ExtraTypeWidget', 'widget');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Footer', 'footer navigatie');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'MainNavigation', 'hoofdnavigatie');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Meta', 'metanavigatie');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina\'s');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'Added', 'De pagina "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'AddedTemplate', 'De template "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'BlockAttached', 'De module <strong>%1$s</strong> is gekoppeld aan deze sectie.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de pagina "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Ben je zeker dat je de template "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'Deleted', 'De pagina "%1$s" werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'DeletedTemplate', 'De template "%1$s" werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'Edited', 'De pagina "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'EditedTemplate', 'De template "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpBlockContent', 'Welk soort inhoud wil je hier tonen?');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigatie die (boven het hoofdmenu) op elke pagina staat.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'De titel die in het menu getoond wordt.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Zorgt ervoor dat deze pagina de interne PageRank niet beïnvloedt.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'vb. [1,2],[/,2]');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpTemplateLocation', 'Plaats de templates in de map <code>core/templates</code> van je thema.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'IsAction', 'Gebruik deze pagina als module-actie.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'MetaNavigation', 'Metanavigatie inschakelen voor deze website.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'Er werd reeds een module gekoppeld aan deze pagina.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'PageIsMoved', 'De pagina "%1$s" werd verplaatst.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'PathToTemplate', 'Pad naar de template');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'RichText', 'Editor');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'TemplateChangeWarning', '<strong>Let op:</strong> de bestaande inhoud zal verloren gaan bij het wijzigen van de template.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'TemplateInUse', 'Deze template is in gebruik, je kan het aantal blokken niet meer aanpassen.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'WidgetAttached', 'De widget <strong>%1$s</strong> is gekoppeld aan deze sectie.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'pages', 'err', 'CantBeMoved', 'Page can\'t be moved.');
		$this->insertLocale('en', 'backend', 'pages', 'err', 'DeletedTemplate', 'You can\'t delete this template.');
		$this->insertLocale('en', 'backend', 'pages', 'err', 'HomeCantHaveBlocks', 'You can\'t link a module to the homepage.');
		$this->insertLocale('en', 'backend', 'pages', 'err', 'InvalidTemplateSyntax', 'Invalid syntax.');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Add', 'add page');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'EditModuleContent', 'edit module content');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'ExtraTypeBlock', 'module');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'ExtraTypeWidget', 'widget');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Footer', 'footer navigation');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'MainNavigation', 'main navigation');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Meta', 'meta navigation');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Root', 'single pages');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Added', 'The page "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'AddedTemplate', 'The template "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'BlockAttached', 'The module <strong>%1$s</strong> is attached to this section.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the page "%1$s"?');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Are your sure you want to delete the template "%1$s"?');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Deleted', 'The page "%1$s" was deleted.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'DeletedTemplate', 'The template "%1$s" was deleted.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Edited', 'The page "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'EditedTemplate', 'The template "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpBlockContent', 'What kind of content do you want to show here?');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigation (above/below the menu) on every page.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'The title that is shown in the menu.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Makes sure that this page doesn\'t influence the internal PageRank.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'e.g. [1,2],[/,2]');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpTemplateLocation', 'Put your templates in the <code>core/templates</code> folder of your theme.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'IsAction', 'Use this page as a module action.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'MetaNavigation', 'Enable metanavigation for this website.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'A module has already been linked to this page.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'PageIsMoved', 'The page "%1$s" was moved.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'PathToTemplate', 'Path to template');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'RichText', 'Editor');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'TemplateChangeWarning', '<strong>Warning:</strong> Existing content will be deleted when changing the template.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'TemplateInUse', 'This template is in use. You can\'t change the number of blocks.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'WidgetAttached', 'The widget <strong>%1$s</strong> is attached to this section.');
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