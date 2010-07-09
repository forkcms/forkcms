<?php

class PagesInstall extends ModuleInstaller
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	SpoonDatabase $db
	 * @param	array $languages
	 */
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;
		$this->languages = $languages;

		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/pages/installer/install.sql');

		// add 'pages' as a module
		$this->addModule('pages', 'The module to manage your pages and website structure.');

		// set rights
		$this->setRights();

		// import data
		$this->importData();

		// set settings
		$this->setSettings();
	}


	private function importData()
	{
		// insert templates
		$this->insertTemplates();

		// insert pages
		$this->insertPages();
	}

	private function insertPages()
	{
		// loop languages
		foreach($this->languages as $language)
		{
			if($this->db->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(1, $language)) == 0)
			{
				// insert meta
				$metaID = $this->db->insert('meta', array('keywords' => 'home', 'keywords_overwrite' => 'N',
															'description' => 'home', 'description_overwrite' => 'N',
															'title' => 'home', 'title_overwrite' => 'N',
															'url' => 'home', 'url_overwrite' => 'N',
															'custom' => null));

				// insert home	@todo	which user should be default?
				$revisionID = $this->db->insert('pages', array('id' => 1,
																'user_id' => 1,
																'parent_id' => 0,
																'template_id' => 1,
																'meta_id' => $metaID,
																'language' => $language,
																'type' => 'page',
																'title' => 'home',
																'navigation_title' => 'home',
																'navigation_title_overwrite' => 'N',
																'hidden' => 'N',
																'status' => 'active',
																'publish_on' => gmdate('Y-m-d H:i:s'),
																'data' => null,
																'created_on' => gmdate('Y-m-d H:i:s'),
																'edited_on' => gmdate('Y-m-d H:i:s'),
																'allow_move' => 'N',
																'allow_children' => 'Y',
																'allow_edit' => 'Y',
																'allow_delete' => 'N',
																'no_follow' => 'N',
																'sequence' => 1,
																'has_extra' => 'N',
																'extra_ids' => null
																));

				// get number of blocks to insert
				$numberOfBlocks = $this->db->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(1));

				// insert blocks
				for($i = 1; $i <= $numberOfBlocks; $i++)
				{
					$this->db->insert('pages_blocks', array('id' => $i,
															'revision_id' => $revisionID,
															'extra_id' => null,
															'html' => '',
															'status' => 'active',
															'created_on' => gmdate('Y-m-d H:i:s'),
															'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
			}

			if($this->db->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(2, $language)) == 0)
			{
				// insert meta
				$metaID = $this->db->insert('meta', array('keywords' => 'sitemap', 'keywords_overwrite' => 'N',
															'description' => 'sitemap', 'description_overwrite' => 'N',
															'title' => 'sitemap', 'title_overwrite' => 'N',
															'url' => 'sitemap', 'url_overwrite' => 'N',
															'custom' => null));

				// insert sitemap	@todo	which user should be default? add widget with sitemap
				$revisionID = $this->db->insert('pages', array('id' => 2,
																'user_id' => 1,
																'parent_id' => 0,
																'template_id' => 2,
																'meta_id' => $metaID,
																'language' => $language,
																'type' => 'footer',
																'title' => 'sitemap',
																'navigation_title' => 'sitemap',
																'navigation_title_overwrite' => 'N',
																'hidden' => 'N',
																'status' => 'active',
																'publish_on' => gmdate('Y-m-d H:i:s'),
																'data' => null,
																'created_on' => gmdate('Y-m-d H:i:s'),
																'edited_on' => gmdate('Y-m-d H:i:s'),
																'allow_move' => 'N',
																'allow_children' => 'Y',
																'allow_edit' => 'Y',
																'allow_delete' => 'N',
																'no_follow' => 'Y',
																'sequence' => 1,
																'has_extra' => 'N',
																'extra_ids' => null
																));

				// get number of blocks to insert
				$numberOfBlocks = $this->db->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numberOfBlocks; $i++)
				{
					$this->db->insert('pages_blocks', array('id' => $i,
															'revision_id' => $revisionID,
															'extra_id' => null,
															'html' => '',
															'status' => 'active',
															'created_on' => gmdate('Y-m-d H:i:s'),
															'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
			}

			if($this->db->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(3, $language)) == 0)
			{
				// insert meta
				$metaID = $this->db->insert('meta', array('keywords' => 'sitemap', 'keywords_overwrite' => 'N',
															'description' => 'sitemap', 'description_overwrite' => 'N',
															'title' => 'sitemap', 'title_overwrite' => 'N',
															'url' => 'sitemap', 'url_overwrite' => 'N',
															'custom' => null));

				// insert disclaimer	@todo	which user should be default? add widget with sitemap
				$revisionID = $this->db->insert('pages', array('id' => 3,
																'user_id' => 1,
																'parent_id' => 0,
																'template_id' => 2,
																'meta_id' => $metaID,
																'language' => $language,
																'type' => 'footer',
																'title' => 'disclaimer',
																'navigation_title' => 'disclaimer',
																'navigation_title_overwrite' => 'N',
																'hidden' => 'N',
																'status' => 'active',
																'publish_on' => gmdate('Y-m-d H:i:s'),
																'data' => null,
																'created_on' => gmdate('Y-m-d H:i:s'),
																'edited_on' => gmdate('Y-m-d H:i:s'),
																'allow_move' => 'N',
																'allow_children' => 'Y',
																'allow_edit' => 'Y',
																'allow_delete' => 'N',
																'no_follow' => 'Y',
																'sequence' => 2,
																'has_extra' => 'N',
																'extra_ids' => null
																));

				// get number of blocks to insert
				$numberOfBlocks = $this->db->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numberOfBlocks; $i++)
				{
					$this->db->insert('pages_blocks', array('id' => $i,
															'revision_id' => $revisionID,
															'extra_id' => null,
															'html' => '',
															'status' => 'active',
															'created_on' => gmdate('Y-m-d H:i:s'),
															'edited_on' => gmdate('Y-m-d H:i:s')
															));
				}
			}

			if($this->db->getNumRows('SELECT id FROM pages WHERE id = ? AND language = ?;', array(404, $language)) == 0)
			{
							// insert meta
				$metaID = $this->db->insert('meta', array('keywords' => '404', 'keywords_overwrite' => 'N',
															'description' => '404', 'description_overwrite' => 'N',
															'title' => '404', 'title_overwrite' => 'N',
															'url' => '404', 'url_overwrite' => 'N',
															'custom' => null));

				// insert disclaimer	@todo	which user should be default? add widget with sitemap
				$revisionID = $this->db->insert('pages', array('id' => 404,
																'user_id' => 1,
																'parent_id' => 0,
																'template_id' => 2,
																'meta_id' => $metaID,
																'language' => $language,
																'type' => 'root',
																'title' => '404',
																'navigation_title' => '404',
																'navigation_title_overwrite' => 'N',
																'hidden' => 'N',
																'status' => 'active',
																'publish_on' => gmdate('Y-m-d H:i:s'),
																'data' => null,
																'created_on' => gmdate('Y-m-d H:i:s'),
																'edited_on' => gmdate('Y-m-d H:i:s'),
																'allow_move' => 'N',
																'allow_children' => 'Y',
																'allow_edit' => 'Y',
																'allow_delete' => 'N',
																'no_follow' => 'Y',
																'sequence' => 2,
																'has_extra' => 'N',
																'extra_ids' => null
																));

				// get number of blocks to insert
				$numberOfBlocks = $this->db->getVar('SELECT num_blocks FROM pages_templates WHERE id = ?;', array(2));

				// insert blocks
				for($i = 1; $i <= $numberOfBlocks; $i++)
				{
					$this->db->insert('pages_blocks', array('id' => $i,
															'revision_id' => $revisionID,
															'extra_id' => null,
															'html' => '',
															'status' => 'active',
															'created_on' => gmdate('Y-m-d H:i:s'),
															'edited_on' => gmdate('Y-m-d H:i:s')
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
			$this->db->insert('pages_templates', array('id' => 1, 'label' => 'home', 'path' => 'core/layout/templates/home.tpl', 'num_blocks' => 3, 'active' => 'Y', 'is_default' => 'N', 'data' => 'a:3:{s:6:"format";s:14:"[1,0],[2,none]";s:5:"names";a:3:{i:0;s:12:"Main Content";i:1;s:7:"Sidebar";i:2;s:10:"Newsletter";}s:5:"types";a:3:{i:0;s:9:"rich_text";i:1;s:9:"rich_text";i:2;s:9:"rich_text";}}'));
		}
		catch(Exception $e)
		{
			if(substr_count($e->getMessage(), 'Duplicate entry') == 0) throw $e;
		}

		try
		{
			$this->db->insert('pages_templates', array('id' => 2, 'label' => 'default', 'path' => 'core/layout/templates/default.tpl', 'num_blocks' => 3, 'active' => 'Y', 'is_default' => 'Y', 'data' => 'a:3:{s:6:"format";s:14:"[0,1],[none,2]";s:5:"names";a:3:{i:0;s:7:"Content";i:1;s:14:"Zijbalk blok 1";i:2;s:14:"Zijbalk blok 2";}s:5:"types";a:3:{i:0;s:9:"rich_text";i:1;s:9:"rich_text";i:2;s:9:"rich_text";}}'));
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
		$this->setSetting('pages', 'template_max_blocks', (int) $this->db->getVar('SELECT MAX(num_blocks) FROM pages_templates;'));
		$this->setSetting('pages', 'has_meta_navigation', true);
		$this->setSetting('pages', 'requires_akismet', false);
		$this->setSetting('pages', 'requires_google_maps', false);
		$this->setSetting('pages', 'default_template', (int) $this->db->getVar('SELECT id FROM pages_templates WHERE is_default = ?;', array('Y')));
	}



	private function insertLocale()
	{
	}


}

?>