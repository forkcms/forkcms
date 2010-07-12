<?php

/**
 * ModuleInstaller
 * The base-class for the installer
 *
 * @package		installer
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ModuleInstaller
{
	/**
	 * Database connection instance
	 *
	 * @var SpoonDatabase
	 */
	private $db;


	/**
	 * The active languages
	 *
	 * @var	array
	 */
	private $languages = array();


	/**
	 * The variables passed by the installer
	 *
	 * @var	array
	 */
	private $variables = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	SpoonDatabase $db	The database-connection.
	 * @param	array $languages	The selected languages
	 * @param	array $variables	The passed variables
	 */
	public function __construct(SpoonDatabase $db, array $languages, array $variables = array())
	{
		// set DB
		$this->db = $db;
		$this->languages = $languages;
		$this->variables = $variables;

		// call the execute method
		$this->execute();
	}


	/**
	 * Inserts a new module
	 *
	 * @return	void
	 * @param	string $name					The name of the module
	 * @param	string[optional] $description	A description for the module
	 */
	protected function addModule($name, $description = null)
	{
		// redefine
		$name = (string) $name;
		$description = (string) $description;

		// insert or update
		$this->getDB()->execute('INSERT INTO modules(name, description, active)
									VALUES(:name, :description, :active)
									ON DUPLICATE KEY UPDATE description = :description, active = :active',
									array('name' => $name, 'description' => (($description) ? $description : null), 'active' => 'Y'));
	}


	/**
	 * Method that will be overriden by the specific installers
	 *
	 * @return void
	 */
	protected function execute()
	{
		// just a placeholder
	}


	/**
	 * Get the database-handle
	 *
	 * @return	SpoonDatabase
	 */
	protected function getDB()
	{
		return $this->db;
	}


	/**
	 * Get the default user
	 *
	 * @return	int
	 */
	protected function getDefaultUserID()
	{
		return (int) $this->getDB()->getVar('SELECT id
												FROM users
												WHERE is_god = ? AND active =? AND deleted = ?
												ORDER BY id ASC;',
												array('Y', 'Y', 'N'));
	}


	/**
	 * Get the selected languages
	 *
	 * @return	void
	 */
	protected function getLanguages()
	{
		return $this->languages;
	}


	/**
	 * Get a setting
	 *
	 * @return	mixed
	 * @param	string $module	The name of the module.
	 * @param	string $name	The name of the setting.
	 */
	protected function getSetting($module, $name)
	{
		return unserialize($this->getDB()->getVar('SELECT value
													FROM modules_settings
													WHERE module = ? AND name = ?;',
													array((string) $module, (string) $name)));
	}


	/**
	 * Get a variable
	 *
	 * @return	mixed
	 * @param	string $name		The name of the variable
	 */
	protected function getVariable($name)
	{
		// is the variable available?
		if(!isset($this->variables[$name])) return null;

		// return the real value
		return $this->variables[$name];
	}


	/**
	 * Imports the sql file
	 *
	 * @return	void
	 * @param	string $filename	The full path for the SQL-file
	 */
	protected function importSQL($filename)
	{
		// load the file content and execute it
		$content = trim(SpoonFile::getContent($filename));

		// file actually has content
		if(!empty($content)) $this->getDB()->execute(SpoonFile::getContent($filename));
	}


	/**
	 * Insert an extra
	 *
	 * @return	int
	 * @param	array $extra	The extra
	 */
	protected function insertExtra(array $item)
	{
		return (int) $this->getDB()->insert('pages_extras', $item);
	}


	/**
	 * Insert a meta item
	 *
	 * @return	int
	 * @param	array $item		The meta item
	 */
	protected function insertMeta($item)
	{
		return (int) $this->getDB()->insert('meta', $item);
	}


	/**
	 * Set the rights for an action
	 *
	 * @return	void
	 * @param	int $groupId			The group wherefor the rights will be set.
	 * @param	string $module			The module wherin the action appears.
	 * @param	string $action			The action wherefor the rights have to set.
	 * @param	int[optional] $level	The leve, default is 7 (max).
	 */
	protected function setActionRights($groupId, $module, $action, $level = 7)
	{
		// redefine
		$groupId = (int) $groupId;
		$module = (string) $module;
		$action = (string) $action;
		$level = (int) $level;

		// action doesn't exist
		if($this->getDB()->getNumRows('SELECT id
										FROM groups_rights_actions
										WHERE group_id = ? AND module = ? AND action = ?;',
										array($groupId, $module,$action)) == 0)
		{
			// insert
			$this->getDB()->insert('groups_rights_actions', array('group_id' => $groupId,
																'module' => $module,
																'action' => $action,
																'level' => $level));
		}
	}


	/**
	 * Sets the rights for a module
	 *
	 * @return	void
	 * @param	int $groupId		The group wherefor the rights will be set.
	 * @param	string $module		The module too set the rights for.
	 */
	protected function setModuleRights($groupId, $module)
	{
		// redefine
		$groupId = (int) $groupId;
		$module = (string) $module;

		// module doesn't exist
		if($this->getDB()->getNumRows('SELECT id
										FROM groups_rights_modules
										WHERE group_id = ? AND module = ?;',
										array((int) $groupId, (string) $module)) == 0)
		{
			$this->getDB()->insert('groups_rights_modules', array('group_id' => $groupId,
																'module' => $module));
		}
	}


	/**
	 * Stores a module specific setting in the database.
	 *
	 * @return	void
	 * @param	string $module				The module wherefore the setting will be set.
	 * @param	string $name				The name of the setting.
	 * @param	mixed[optional] $value		The optional value.
	 * @param	bool[optional] $overwrite	Overwrite no matter what.
	 */
	protected function setSetting($module, $name, $value = null, $overwrite = false)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);
		$overwrite = (bool) $overwrite;

		// doens't already exist
		if($this->getDB()->getNumRows('SELECT name
										FROM modules_settings
										WHERE module = ? AND name = ?;',
										array($module, $name)) == 0)
		{
			// insert setting
			$this->getDB()->insert('modules_settings', array('module' => $module,
																'name' => $name,
																'value' => $value));
		}

		// overwrite
		elseif($overwrite)
		{
			// insert setting
			$this->getDB()->execute("INSERT INTO modules_settings (module, name, value) VALUES (?, ?, ?)
									ON DUPLICATE KEY UPDATE value = ?;", array($module, $name, $value, $value));
		}
	}
}


/**
 * CoreInstall
 * Installer for the core
 *
 * @package		installer
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class CoreInstall extends ModuleInstaller
{
	/**
	 * Installe the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// validate variables
		if($this->getVariable('default_language') === null) throw new SpoonException('Not all required variables were provided.');
		if($this->getVariable('site_domain') === null) throw new SpoonException('Not all required variables were provided.');
		if($this->getVariable('spoon_debug_email') === null) throw new SpoonException('Not all required variables were provided.');
		if($this->getVariable('api_email') === null) throw new SpoonException('Not all required variables were provided.');
		if($this->getVariable('site_title') === null) throw new SpoonException('Not all required variables were provided.');

		// import SQL
		$this->importSQL(PATH_WWW .'/backend/core/installer/install.sql');

		// add core modules
		$this->addModule('core', 'The Fork CMS core module.');
		$this->addModule('authentication', 'The module to manage authentication');
		$this->addModule('dashboard', 'The dashboard containing module specific widgets.');
		$this->addModule('error', 'The error module, used for displaying errors.');

		// set rights
		$this->setRights();

		// set settings
		$this->setSettings();
	}


	/**
	 * Set the rights
	 *
	 * @return	void
	 */
	private function setRights()
	{
		// module rights
		$this->setModuleRights(1, 'dashboard');

		// action rights
		$this->setActionRights(1, 'dashboard', 'index');
	}


	/**
	 * Store the settings
	 *
	 * @return	void
	 */
	private function setSettings()
	{
		// languages settings
		$this->setSetting('core', 'languages', $this->getLanguages());
		$this->setSetting('core', 'active_languages', $this->getLanguages());
		$this->setSetting('core', 'redirect_languages', $this->getLanguages());
		$this->setSetting('core', 'default_language', $this->getVariable('default_language'));
		$this->setSetting('core', 'interface_languages', array('nl'));
		$this->setSetting('core', 'default_interface_language', array('nl'));

		// other settings
		$this->setSetting('core', 'theme');
		$this->setSetting('core', 'requires_akismet', false);
		$this->setSetting('core', 'askismet_key', '');
		$this->setSetting('core', 'requires_google_maps', false);
		$this->setSetting('core', 'google_maps_keky', '');
		$this->setSetting('core', 'max_num_revisions', 20);
		$this->setSetting('core', 'site_domains', array($this->getVariable('site_domain')));
		$this->setSetting('core', 'site_wide_html', '');

		// date & time
		$this->setSetting('core', 'date_format_short', 'j.n.Y');
		$this->setSetting('core', 'date_formats_short', array('j/n/Y', 'j-n-Y', 'j.n.Y', 'n/j/Y', 'n/j/Y', 'n/j/Y', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'm-d-Y', 'm.d.Y', 'j/n/y', 'j-n-y', 'j.n.y', 'n/j/y', 'n-j-y', 'n.j.y', 'd/m/y', 'd-m-y', 'd.m.y', 'm/d/y', 'm-d-y', 'm.d.y'));
		$this->setSetting('core', 'date_format_long', 'l j F Y');
		$this->setSetting('core', 'date_formats_long', array('j F Y', 'D j F Y', 'l j F Y', 'j F, Y', 'D j F, Y', 'l j F, Y', 'd F Y', 'd F, Y', 'F j Y', 'D F j Y', 'l F j Y', 'F d, Y', 'D F d, Y', 'l F d, Y'));
		$this->setSetting('core', 'time_format', 'H:i');
		$this->setSetting('core', 'time_formats', array('H:i', 'H:i:s', 'g:i a', 'g:i A'));

		// e-mail settings
		$this->setSetting('core', 'mailer_from', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));
		$this->setSetting('core', 'mailer_to', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));
		$this->setSetting('core', 'mailer_reply_to', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));

		// stmp settings
		$this->setSetting('core', 'smtp_server', $this->getVariable('smtp_server'));
		$this->setSetting('core', 'smtp_port', $this->getVariable('smtp_port'));
		$this->setSetting('core', 'smpt_username', $this->getVariable('smtp_username'));
		$this->setSetting('core', 'smpt_password', $this->getVariable('smtp_password'));

		// language specific
		foreach($this->getLanguages() as $language)
		{
			$this->setSetting('core', 'site_title_'. $language, $this->getVariable('site_title'));
		}

		/*
		 * We're going to try to install the settings for the api.
		 */
		require_once PATH_LIBRARY .'/external/fork_api.php';

		// create new instance
		$api = new ForkAPI();

		// get the keys
		$keys = $api->coreRequestKeys($this->getVariable('site_domain'), $this->getVariable('api_email'));

		// ap settings
		$this->setSetting('core', 'fork_api_public_key', $keys['public']);
		$this->setSetting('core', 'fork_api_private_key', $keys['private']);

		// set keys
		$api->setPublicKey($keys['public']);
		$api->setPrivateKey($keys['private']);

		// get services
		$services = (array) $api->pingGetServices();

		// set services
		if(!empty($services)) $this->setSetting('core', 'ping_services', array('services' => $services, 'date' => time()));

		// general settings
		$this->setSetting('dashboard', 'requires_akismet', false);
		$this->setSetting('dashboard', 'requires_google_maps', false);
	}
}

?>