<?php

/**
 * Installer for the extensions module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class ExtensionsInstaller extends ModuleInstaller
{
	/**
	 * Pre-insert default extras of the default theme.
	 */
	private function insertExtras()
	{
		// insert extra ids
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);
	}

	/**
	 * Insert the templates.
	 */
	private function insertTemplates()
	{
		/*
		 * Fallback templates
		 */

		// build templates
		$templates['core']['default'] = array(
			'theme' => 'core',
			'label' => 'Default',
			'path' => 'core/layout/templates/default.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[main]',
				'names' => array('main')
			))
		);

		$templates['core']['home'] = array(
			'theme' => 'core',
			'label' => 'Home',
			'path' => 'core/layout/templates/home.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[main]',
				'names' => array('main')
			))
		);

		// insert templates
		$this->getDB()->insert('themes_templates', $templates['core']['default']);
		$this->getDB()->insert('themes_templates', $templates['core']['home']);

		/*
		 * Triton templates
		 */

		// search will be installed by default; already link it to this template
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// build templates
		$templates['triton']['default'] = array(
			'theme' => 'triton',
			'label' => 'Default',
			'path' => 'core/layout/templates/default.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[left,main,main,main]',
				'names' => array('main', 'left', 'top', 'advertisement'),
				'default_extras' => array('top' => array($extras['search_form']))
			))
		);

		$templates['triton']['home'] = array(
			'theme' => 'triton',
			'label' => 'Home',
			'path' => 'core/layout/templates/home.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[main,main,main,main],[left,left,right,right]',
				'names' => array('main', 'left', 'right', 'top', 'advertisement'),
				'default_extras' => array('top' => array($extras['search_form']))
			))
		);

		// insert templates
		$this->getDB()->insert('themes_templates', $templates['triton']['default']);
		$this->getDB()->insert('themes_templates', $templates['triton']['home']);
		
		/*
		 * Bootstrap templates
		 */

		// search will be installed by default; already link it to this template
		$extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

		// build templates
		$templates['bootstrap']['default'] = array(
			'theme' => 'bootstrap',
			'label' => 'Default',
			'path' => 'core/layout/templates/default.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[/,/,/,top,/],[/,main,main,main,/]',
				'names' => array('main', 'top'),
				'default_extras' => array('top' => array($extras['search_form']))
			))
		);
		
		$templates['bootstrap']['error'] = array(
			'theme' => 'bootstrap',
			'label' => 'Error',
			'path' => 'core/layout/templates/error.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[/,/,/,top,/],[/,main,main,main,/]',
				'names' => array('main', 'top'),
				'default_extras' => array('top' => array($extras['search_form']))
			))
		);

		$templates['bootstrap']['home'] = array(
			'theme' => 'bootstrap',
			'label' => 'Home',
			'path' => 'core/layout/templates/home.tpl',
			'active' => 'Y',
			'data' => serialize(array(
				'format' => '[/,/,/,top,/],[slideshow,slideshow,slideshow,slideshow,slideshow],[/,features,features,features,/],[/,main,main,main,/]',
				'names' => array('slideshow', 'features', 'main', 'top'),
				'default_extras' => array('top' => array($extras['search_form']))
			))
		);

		// insert templates
		$this->getDB()->insert('themes_templates', $templates['bootstrap']['default']);
		$this->getDB()->insert('themes_templates', $templates['bootstrap']['home']);
		$this->getDB()->insert('themes_templates', $templates['bootstrap']['error']);

		/*
		 * General theme settings
		 */

		// set default theme
		$this->setSetting('core', 'theme', 'bootstrap', true);

		// set default template
		$this->setSetting('pages', 'default_template', $this->getTemplateId('default'));

		// disable meta navigation
		$this->setSetting('pages', 'meta_navigation', false);
	}

	/**
	 * Install the module
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
			'extensions/detail_module',
			'extensions/upload_module'
		));

		// theme navigation
		$navigationThemesId = $this->setNavigation($navigationSettingsId, 'Themes');
		$this->setNavigation($navigationThemesId, 'ThemesSelection', 'extensions/themes', array(
			'extensions/upload_theme',
			'extensions/detail_theme'
		));
		$this->setNavigation($navigationThemesId, 'Templates', 'extensions/theme_templates', array(
			'extensions/add_theme_template',
			'extensions/edit_theme_template'
		));
	}

	/**
	 * Set the rights
	 */
	private function setRights()
	{
		// modules
		$this->setActionRights(1, 'extensions', 'modules');
		$this->setActionRights(1, 'extensions', 'detail_module');
		$this->setActionRights(1, 'extensions', 'install_module');
		$this->setActionRights(1, 'extensions', 'upload_module');

		// themes
		$this->setActionRights(1, 'extensions', 'themes');
		$this->setActionRights(1, 'extensions', 'detail_theme');
		$this->setActionRights(1, 'extensions', 'install_theme');
		$this->setActionRights(1, 'extensions', 'upload_theme');

		// templates
		$this->setActionRights(1, 'extensions', 'theme_templates');
		$this->setActionRights(1, 'extensions', 'add_theme_template');
		$this->setActionRights(1, 'extensions', 'edit_theme_template');
		$this->setActionRights(1, 'extensions', 'delete_theme_template');
	}
}
