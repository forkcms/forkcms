<?php

/**
 * Installer for the extensions module
 *
 * @package		installer
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.6.6
 */
class ExtensionsInstaller extends ModuleInstaller
{
	/**
	 * Pre-insert default extras of the default theme.
	 *
	 * @return void
	 */
	private function insertExtras()
	{
		// insert extra ids
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);
	}


	/**
	 * Insert the templates.
	 *
	 * @return	void
	 */
	private function insertTemplates()
	{
		/*
		 * Fallback templates
		 */

		// build templates
		$templates['core']['default'] = array('theme' => 'core',
												'label' => 'Default',
												'path' => 'core/layout/templates/default.tpl',
												'active' => 'Y',
												'data' => serialize(array('format' => '[main]',
																			'names' => array('main'))));

		$templates['core']['home'] = array('theme' => 'core',
											'label' => 'Home',
											'path' => 'core/layout/templates/home.tpl',
											'active' => 'Y',
											'data' => serialize(array('format' => '[main]',
																		'names' => array('main'))));

		// insert templates
		$this->getDB()->insert('themes_templates', $templates['core']['default']);
		$this->getDB()->insert('themes_templates', $templates['core']['home']);


		/*
		 * Triton templates
		 */

		// search will be installed by default; already link it to this template
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// build templates
		$templates['triton']['default'] = array('theme' => 'triton',
												'label' => 'Default',
												'path' => 'core/layout/templates/default.tpl',
												'active' => 'Y',
												'data' => serialize(array('format' => '[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[left,main,main,main]',
																			'names' => array('main', 'left', 'top', 'advertisement'),
																			'default_extras' => array('top' => array($extras['search_form'])))));

		$templates['triton']['home'] = array('theme' => 'triton',
												'label' => 'Home',
												'path' => 'core/layout/templates/home.tpl',
												'active' => 'Y',
												'data' => serialize(array('format' => '[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[main,main,main,main],[left,left,right,right]',
																			'names' => array('main', 'left', 'right', 'top', 'advertisement'),
																			'default_extras' => array('top' => array($extras['search_form'])))));

		// insert templates
		$this->getDB()->insert('themes_templates', $templates['triton']['default']);
		$this->getDB()->insert('themes_templates', $templates['triton']['home']);

		/*
		 * General theme settings
		 */

		// set default theme
		$this->setSetting('core', 'theme', 'triton', true);

		// set default template
		$this->setSetting('pages', 'default_template', $this->getTemplateId('default'));

		// disable meta navigation
		$this->setSetting('pages', 'meta_navigation', false);
	}


	/**
	 * Install the module
	 *
	 * @return	void
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'extensions' as a module
		$this->addModule('extensions');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// insert extras
		$this->insertExtras();

		// insert templates
		$this->insertTemplates();

		// module rights
		$this->setModuleRights(1, 'extensions');

		// set rights
		$this->setRights();

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Overview', 'extensions/modules', array(
			'extensions/module_detail',
			'extensions/module_upload'
		));

		// theme navigation
		$navigationThemesId = $this->setNavigation($navigationSettingsId, 'Themes');
		$this->setNavigation($navigationThemesId, 'ThemesSelection', 'extensions/themes');
		$this->setNavigation($navigationThemesId, 'Templates', 'extensions/theme_templates', array(
			'extensions/add_theme_template',
			'extensions/edit_theme_template'
		));
	}


	/**
	 * Set the rights
	 *
	 * @return	void
	 */
	private function setRights()
	{

		// action rights
		$this->setActionRights(1, 'extensions', 'module_detail');
		$this->setActionRights(1, 'extensions', 'module_install');
		$this->setActionRights(1, 'extensions', 'module_upload');
		$this->setActionRights(1, 'extensions', 'modules');

		$this->setActionRights(1, 'extensions', 'themes');

		$this->setActionRights(1, 'pages', 'theme_templates');
		$this->setActionRights(1, 'pages', 'add_theme_template');
		$this->setActionRights(1, 'pages', 'edit_theme_template');
		$this->setActionRights(1, 'pages', 'delete_theme_template');
	}
}

?>