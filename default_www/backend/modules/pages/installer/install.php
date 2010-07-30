<?php

/**
 * PagesInstall
 * Installer for the pages module
 *
 * @package		installer
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class PagesInstall extends ModuleInstaller
{
	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	SpoonDatabase $db
	 * @param	array $languages
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/pages/installer/data/install.sql');

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
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Add', 'pagina toevoegen');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Footer', 'navigatie onderaan');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'MainNavigation', 'hoofdnavigatie');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Meta', 'metanavigatie');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'Root', 'losse pagina\'s');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'Added', 'De pagina "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'AddedTemplate', 'De template "%1$s" werd toegevoegd.');
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
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpPageTitle', 'De titel die in het browservenster staat (<code>&lt;title&gt;</code>).');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'e.g. [0,1],[2,none]');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'MetaNavigation', 'Metanavigatie inschakelen voor deze website.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'Er werd reeds een module gekoppeld aan deze pagina.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'PageIsMoved', 'De pagina "%1$s" werd verplaatst.');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'RichText', 'Editor');
		$this->insertLocale('nl', 'backend', 'pages', 'msg', 'TemplateInUse', 'Deze template is in gebruik, je kan het aantal blokken niet meer aanpassen.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'pages', 'err', 'CantBeMoved', 'Page can\'t be moved.');
		$this->insertLocale('en', 'backend', 'pages', 'err', 'DeletedTemplate', 'You can\'t delete this template.');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Add', 'add page');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Footer', 'bottom navigation');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'MainNavigation', 'main navigation');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Meta', 'meta navigation');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'Root', 'separate pages');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Added', 'The page "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'AddedTemplate', 'The template "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the page "%1$s"?');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'ConfirmDeleteTemplate', 'Are your sure you want to delete the template "%1$s"?');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Deleted', 'The page "%1$s" was deleted.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'DeletedTemplate', 'The template "%1$s" was deleted.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Edited', 'The page "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'Edited', 'The template "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpBlockContent', 'What kind of content do you want to show here?');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpMetaNavigation', 'Extra topnavigation (above/below the menu) on every page.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpNavigationTitle', 'The title that is shown in the menu.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpNoFollow', 'Makes sure that this page doesn\'t influence the internal PageRank.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpPageTitle', 'The title in the browser window (<code>&lt;title&gt;</code>).');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'HelpTemplateFormat', 'e.g. [0,1],[2,none]');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'MetaNavigation', 'Enable metanavigation for this website.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'ModuleBlockAlreadyLinked', 'A module has already been linked to this page.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'PageIsMoved', 'The page "%1$s" was moved.');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'RichText', 'Editor');
		$this->insertLocale('en', 'backend', 'pages', 'msg', 'TemplateInUse', 'This template is in use. You can\'t change the number of blocks.');
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

		// insert pages
		$this->insertPagesAndExtras();
	}


	/**
	 * Insert the pages
	 *
	 * @return	void
	 */
	private function insertPagesAndExtras()
	{
		// @todo insert contact page

		// @todo insert extra for contact page

		// insert extras
		$sitemapID = $this->insertExtra(array('module' => 'pages',
												'type' => 'widget',
												'label' => 'Sitemap',
												'action' => 'sitemap',
												'data' => null,
												'hidden' => 'N',
												'sequence' => 1));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if the homepage doesn't exist
			if($this->getDB()->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(1, $language)) == 0)
			{
				// insert meta
				$metaID = $this->getDB()->insert('meta', array('keywords' => 'Home', 'keywords_overwrite' => 'N',
																'description' => 'Home', 'description_overwrite' => 'N',
																'title' => 'Home', 'title_overwrite' => 'N',
																'url' => 'home', 'url_overwrite' => 'N',
																'custom' => null
															));

				// insert home
				$revisionID = $this->getDB()->insert('pages', array('id' => 1, 'user_id' => $this->getDefaultUserID(), 'parent_id' => 0, 'template_id' => 1, 'type' => 'page',
																	'meta_id' => $metaID, 'language' => $language,
																	'title' => 'Home',
																	'navigation_title' => 'home', 'navigation_title_overwrite' => 'N',
																	'hidden' => 'N', 'status' => 'active',
																	'publish_on' => gmdate('Y-m-d H:i:s'), 'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s'),
																	'data' => null,
																	'allow_move' => 'N', 'allow_children' => 'Y', 'allow_edit' => 'Y', 'allow_delete' => 'N',
																	'no_follow' => 'N',
																	'sequence' => 1,
																	'has_extra' => 'N', 'extra_ids' => null
																));

				// get number of blocks to insert
				$numBlocks = $this->getDB()->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(1));

				// insert blocks
				for($i = 1; $i <= $numBlocks; $i++)
				{
					$this->getDB()->insert('pages_blocks', array('id' => $i, 'revision_id' => $revisionID, 'status' => 'active',
																	'extra_id' => null, 'html' => '',
																	'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s')
																));
				}
			}

			// check if the sitemap page doesn't exist
			if($this->getDB()->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(2, $language)) == 0)
			{
				// insert meta
				$metaID = $this->getDB()->insert('meta', array('keywords' => 'Sitemap', 'keywords_overwrite' => 'N',
																'description' => 'Sitemap', 'description_overwrite' => 'N',
																'title' => 'Sitemap', 'title_overwrite' => 'N',
																'url' => 'sitemap', 'url_overwrite' => 'N',
																'custom' => null
															));

				// insert sitemap	@todo	add widget with sitemap
				$revisionID = $this->getDB()->insert('pages', array('id' => 2, 'user_id' => $this->getDefaultUserID(), 'parent_id' => 0, 'template_id' => 2, 'type' => 'footer',
																	'meta_id' => $metaID, 'language' => $language,
																	'title' => 'Sitemap',
																	'navigation_title' => 'Sitemap', 'navigation_title_overwrite' => 'N',
																	'hidden' => 'N', 'status' => 'active',
																	'publish_on' => gmdate('Y-m-d H:i:s'), 'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s'),
																	'data' => null,
																	'allow_move' => 'N', 'allow_children' => 'Y', 'allow_edit' => 'Y', 'allow_delete' => 'N',
																	'no_follow' => 'Y',
																	'sequence' => 1,
																	'has_extra' => 'N', 'extra_ids' => $sitemapID
																));

				// get number of blocks to insert
				$numBlocks = $this->getDB()->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numBlocks; $i++)
				{
					$extraId = null;
					if($i == 2) $extraId = $sitemapID;

					$this->getDB()->insert('pages_blocks', array('id' => $i, 'revision_id' => $revisionID, 'status' => 'active',
															'extra_id' => $extraId, 'html' => '',
															'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
			}

			// check if the disclaimerpage doesn't exist
			if($this->getDB()->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(3, $language)) == 0)
			{
				// insert meta
				$metaID = $this->getDB()->insert('meta', array('keywords' => 'Disclaimer', 'keywords_overwrite' => 'N',
																'description' => 'Disclaimer', 'description_overwrite' => 'N',
																'title' => 'Disclaimer', 'title_overwrite' => 'N',
																'url' => 'disclaimer', 'url_overwrite' => 'N',
																'custom' => null
															));

				// insert disclaimer
				$revisionID = $this->getDB()->insert('pages', array('id' => 3, 'user_id' => $this->getDefaultUserID(), 'parent_id' => 0, 'template_id' => 2, 'type' => 'footer',
																	'meta_id' => $metaID, 'language' => $language,
																	'title' => 'Disclaimer',
																	'navigation_title' => 'Disclaimer', 'navigation_title_overwrite' => 'N',
																	'hidden' => 'N', 'status' => 'active',
																	'publish_on' => gmdate('Y-m-d H:i:s'), 'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s'),
																	'data' => null,
																	'allow_move' => 'N', 'allow_children' => 'Y', 'allow_edit' => 'Y', 'allow_delete' => 'N',
																	'no_follow' => 'Y',
																	'sequence' => 2,
																	'has_extra' => 'N', 'extra_ids' => null
																));

				// get number of blocks to insert
				$numBlocks = $this->getDB()->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numBlocks; $i++)
				{
					$this->getDB()->insert('pages_blocks', array('id' => $i, 'revision_id' => $revisionID, 'status' => 'active',
															'extra_id' => null, 'html' => '',
															'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
			}

			// check if the about page doesn't exist
			if($this->getDB()->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(4, $language)) == 0)
			{
				// insert meta
				$metaID = $this->getDB()->insert('meta', array('keywords' => 'About', 'keywords_overwrite' => 'N',
																'description' => 'About', 'description_overwrite' => 'N',
																'title' => 'About', 'title_overwrite' => 'N',
																'url' => 'about', 'url_overwrite' => 'N',
																'custom' => null
															));

				// insert about
				$revisionID = $this->getDB()->insert('pages', array('id' => 4, 'user_id' => $this->getDefaultUserID(), 'parent_id' => 0, 'template_id' => 2, 'type' => 'meta',
																	'meta_id' => $metaID, 'language' => $language,
																	'title' => 'About',
																	'navigation_title' => 'About', 'navigation_title_overwrite' => 'N',
																	'hidden' => 'N', 'status' => 'active',
																	'publish_on' => gmdate('Y-m-d H:i:s'), 'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s'),
																	'data' => null,
																	'allow_move' => 'N', 'allow_children' => 'Y', 'allow_edit' => 'Y', 'allow_delete' => 'N',
																	'no_follow' => 'Y',
																	'sequence' => 2,
																	'has_extra' => 'N', 'extra_ids' => null
																));

				// get number of blocks to insert
				$numBlocks = $this->getDB()->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numBlocks; $i++)
				{
					$this->getDB()->insert('pages_blocks', array('id' => $i, 'revision_id' => $revisionID, 'status' => 'active',
															'extra_id' => null, 'html' => '',
															'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
			}

			// check if the 404 page doesn't exist
			if($this->getDB()->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(404, $language)) == 0)
			{
							// insert meta
				$metaID = $this->getDB()->insert('meta', array('keywords' => '404', 'keywords_overwrite' => 'N',
																'description' => '404', 'description_overwrite' => 'N',
																'title' => '404', 'title_overwrite' => 'N',
																'url' => '404', 'url_overwrite' => 'N',
																'custom' => null
															));

				// insert disclaimer
				$revisionID = $this->getDB()->insert('pages', array('id' => 404, 'user_id' => $this->getDefaultUserID(), 'parent_id' => 0, 'template_id' => 2, 'type' => 'root',
																	'meta_id' => $metaID,
																	'language' => $language,
																	'title' => '404',
																	'navigation_title' => '404', 'navigation_title_overwrite' => 'N',
																	'hidden' => 'N', 'status' => 'active',
																	'publish_on' => gmdate('Y-m-d H:i:s'), 'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s'),
																	'data' => null,
																	'allow_move' => 'N', 'allow_children' => 'Y', 'allow_edit' => 'Y', 'allow_delete' => 'N',
																	'no_follow' => 'Y',
																	'sequence' => 2,
																	'has_extra' => 'N', 'extra_ids' => null
																));

				// get number of blocks to insert
				$numBlocks = $this->getDB()->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numBlocks; $i++)
				{
					$this->getDB()->insert('pages_blocks', array('id' => $i, 'revision_id' => $revisionID, 'status' => 'active',
															'extra_id' => null, 'html' => '',
															'created_on' => gmdate('Y-m-d H:i:s'), 'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
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
		// insert home template
		try
		{
			$this->getDB()->insert('pages_templates', array('id' => 1, 'label' => 'home', 'path' => 'core/layout/templates/home.tpl', 'num_blocks' => 3, 'active' => 'Y', 'data' => 'a:3:{s:6:"format";s:14:"[1,0],[2,none]";s:5:"names";a:3:{i:0;s:12:"Main Content";i:1;s:7:"Sidebar";i:2;s:10:"Newsletter";}s:5:"types";a:3:{i:0;s:9:"rich_text";i:1;s:9:"rich_text";i:2;s:9:"rich_text";}}'));
		}
		catch(Exception $e)
		{
			if(substr_count($e->getMessage(), 'Duplicate entry') == 0) throw $e;
		}

		try
		{
			$this->getDB()->insert('pages_templates', array('id' => 2, 'label' => 'default', 'path' => 'core/layout/templates/default.tpl', 'num_blocks' => 3, 'active' => 'Y', 'data' => 'a:3:{s:6:"format";s:14:"[0,1],[none,2]";s:5:"names";a:3:{i:0;s:7:"Content";i:1;s:14:"Zijbalk blok 1";i:2;s:14:"Zijbalk blok 2";}s:5:"types";a:3:{i:0;s:9:"rich_text";i:1;s:9:"rich_text";i:2;s:9:"rich_text";}}'));
		}
		catch(Exception $e)
		{
			if(substr_count($e->getMessage(), 'Duplicate entry') == 0) throw $e;
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


	/**
	 * Store the settings
	 *
	 * @return	void
	 */
	private function setSettings()
	{
		// general settings
		$this->setSetting('pages', 'template_max_blocks', (int) $this->getDB()->getVar('SELECT MAX(num_blocks) FROM pages_templates;'));
		$this->setSetting('pages', 'meta_navigation', true);
		$this->setSetting('pages', 'requires_akismet', false);
		$this->setSetting('pages', 'requires_google_maps', false);
		$this->setSetting('pages', 'default_template', 2);
	}
}

?>