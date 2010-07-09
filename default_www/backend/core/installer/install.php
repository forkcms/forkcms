<?php

class ModuleInstaller
{
	/**
	 * Database connection instance
	 *
	 * @var SpoonDatabase
	 */
	protected $db;


	/**
	 * Inserts a new module
	 */
	protected function addModule($name, $description = null)
	{
		// module doesn't exist
		if(!$this->db->getNumRows('SELECT name FROM modules WHERE name = ?;', (string) $name))
		{
			$this->db->insert('modules', array('name' => (string) $name, 'description' => (($description) ? (string) $description : null), 'active' => 'Y'));
		}

		// update module
		else $this->db->update('modules', array('description' => (($description) ? (string) $description : null), 'active' => 'Y'), 'name = ?', (string) $name);
	}


	protected function getSetting($module, $name)
	{
		return unserialize($this->db->getVar('SELECT value FROM modules_settings WHERE module = ? AND name = ?;', array((string) $module, (string) $name)));
	}


	protected function setActionRights($groupId, $module, $action, $level = 7)
	{
		// action doesn't exist
		if(!$this->db->getNumRows('SELECT id FROM groups_rights_actions WHERE group_id = ? AND module = ? AND action = ?;', array((int) $groupId, (string) $module, (string) $action)))
		{
			$this->db->insert('groups_rights_actions', array('group_id' => (int) $groupId, 'module' => (string) $module, 'action' => (string) $action, 'level' => (int) $level));
		}
	}

	protected function setModuleRights($groupId, $module)
	{
		// module doesn't exist
		if(!$this->db->getNumRows('SELECT id FROM groups_rights_modules WHERE group_id = ? AND module = ?;', array((int) $groupId, (string) $module)))
		{
			$this->db->insert('groups_rights_modules', array('group_id' => (int) $groupId, 'module' => (string) $module));
		}
	}


	/**
	 * Imports the sql file
	 *
	 * @return	void
	 */
	protected function importSQL($filename)
	{
		// load the file content and execute it
		$content = trim(SpoonFile::getContent($filename));

		// file actually has content
		if(!empty($content)) $this->db->execute(SpoonFile::getContent($filename));
	}


	/**
	 * Stores a moduel specific setting in the database
	 *
	 * @return	void
	 * @param	string $module
	 * @param	string $name
	 * @param	mixed[optional] $value
	 */
	protected function setSetting($module, $name, $value = null)
	{

		// init vars
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);

		// doens't already exist
		if(!$this->db->getNumRows('SELECT name FROM modules_settings WHERE module = ? AND name = ?;', array($module, $name)))
		{
			// insert setting
			$this->db->insert('modules_settings', array('module' => $module, 'name' => $name, 'value' => $value));
		}
	}
}



class CoreInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages, array $variables)
	{
		// check variables
		if(isset($variables['default_language']) && isset($variables['site_domain']) && isset($variables['spoon_debug_email']) && isset($variables['api_email']) && isset($variables['site_title']))
		{
			// set database instance
			$this->db = $db;

			// load install.sql
			$this->importSQL(PATH_WWW .'/backend/core/installer/install.sql');

			// add 'core' as a module
			$this->addModule('core', 'The Fork CMS core module.');

			/*
			 * We're going to insert a list of module specific settings.
			 */

			// languages settings
			$this->setSetting('core', 'languages', array('de', 'en', 'es', 'fr', 'nl'));
			$this->setSetting('core', 'active_languages', $languages);
			$this->setSetting('core', 'redirect_languages', $languages);
			$this->setSetting('core', 'default_language', $variables['default_language']);
			// --
			$this->setSetting('core', 'interface_languages', array('nl'));
			$this->setSetting('core', 'default_interface_language', array('nl'));

			// other settings
			$this->setSetting('core', 'theme');
			$this->setSetting('core', 'requires_akismet', false);
			$this->setSetting('core', 'askismet_key', '');
			$this->setSetting('core', 'requires_google_maps', false);
			$this->setSetting('core', 'google_maps_keky', '');
			$this->setSetting('core', 'max_num_revisions', 20);
			$this->setSetting('core', 'site_domains', array($variables['site_domain']));
			$this->setSetting('core', 'site_wide_html', '');

			// date & time
			$this->setSetting('core', 'date_format_short', 'j.n.Y');
			$this->setSetting('core', 'date_formats_short', array('j/n/Y', 'j-n-Y', 'j.n.Y', 'n/j/Y', 'n/j/Y', 'n/j/Y', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'm-d-Y', 'm.d.Y', 'j/n/y', 'j-n-y', 'j.n.y', 'n/j/y', 'n-j-y', 'n.j.y', 'd/m/y', 'd-m-y', 'd.m.y', 'm/d/y', 'm-d-y', 'm.d.y'));
			$this->setSetting('core', 'date_format_long', 'l j F Y');
			$this->setSetting('core', 'date_formats_long', array('j F Y', 'D j F Y', 'l j F Y', 'j F, Y', 'D j F, Y', 'l j F, Y', 'd F Y', 'd F, Y', 'F j Y', 'D F j Y', 'l F j Y', 'F d, Y', 'D F d, Y', 'l F d, Y'));
			$this->setSetting('core', 'time_format', 'H:i');
			$this->setSetting('core', 'time_formats', array('H:i', 'H:i:s', 'g:i a', 'g:i A'));

			// e-mail settings
			$this->setSetting('core', 'mailer_from', array('name' => 'Fork CMS', 'email' => $variables['spoon_debug_email']));
			$this->setSetting('core', 'mailer_to', array('name' => 'Fork CMS', 'email' => $variables['spoon_debug_email']));
			$this->setSetting('core', 'mailer_reply_to', array('name' => 'Fork CMS', 'email' => $variables['spoon_debug_email']));
			// @todo davy - deze settings moeten opgevraagd worden in de installer
			$this->setSetting('core', 'smtp_server', 'mail.fork-cms.be');
			$this->setSetting('core', 'smtp_port', 587);
			$this->setSetting('core', 'smpt_username', 'bugs@fork-cms.be');
			$this->setSetting('core', 'smpt_password', 'Jishaik6');

			// ping services
			$this->setSetting('core', 'ping_services', array('services' => array(array('url' => 'http://rpc.weblogs.com/RPC2', 'port' => 80, 'type' => 'extended'),
																			array('url' => 'http://rpc.pingomatic.com/RPC2', 'port' => 80, 'type' => 'extended'),
																			array('url' => 'http://blogsearch.google.com/ping/RPC2', 'port' => 80, 'type' => 'extended')),
															'date' => 1275485024));

			// language specific
			foreach($languages as $language)
			{
				$this->setSetting('core', 'site_title_'. $language, $variables['site_title']);
			}

			/*
			 * We're going to try to install the settings for the api.
			 */
			require_once PATH_LIBRARY .'/external/fork_api.php';

			// create new instance
			$api = new ForkAPI();

			// get the keys
			$keys = $api->coreRequestKeys($variables['site_domain'], $variables['api_email']);

			// ap settings
			$this->setSetting('core', 'fork_api_public_key', $keys['public']);
			$this->setSetting('core', 'fork_api_private_key', $keys['private']);
		}

		// something went wrong
		else throw new SpoonException('Not all required variables were provided.');
	}
}

?>