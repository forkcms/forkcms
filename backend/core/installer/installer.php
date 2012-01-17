<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * The base-class for the installer
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
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
	 * The default extras that have to be added to every page.
	 *
	 * @var array
	 */
	private $defaultExtras;

	/**
	 * The frontend language(s)
	 *
	 * @var array
	 */
	private $languages = array();

	/**
	 * The backend language(s)
	 *
	 * @var array
	 */
	private $interfaceLanguages = array();

	/**
	 * Cached modules
	 *
	 * @var	array
	 */
	private static $modules = array();

	/**
	 * The variables passed by the installer
	 *
	 * @var array
	 */
	private $variables = array();

	/**
	 * The warnings thrown during the install
	 *
	 * @var array
	 */
	private $warnings = array();

	/**
	 * @param SpoonDatabase $db The database-connection.
	 * @param array $languages The selected frontend languages.
	 * @param array $interfaceLanguages The selected backend languages.
	 * @param bool[optional] $example Should example data be installed.
	 * @param array[optional] $variables The passed variables.
	 */
	public function __construct(SpoonDatabase $db, array $languages, array $interfaceLanguages, $example = false, array $variables = array())
	{
		$this->db = $db;
		$this->languages = $languages;
		$this->interfaceLanguages = $interfaceLanguages;
		$this->example = (bool) $example;
		$this->variables = $variables;
	}

	/**
	 * Adds a default extra to the stack of extras
	 *
	 * @param int $extraId The extra id to add to every page.
	 * @param string $position The position to put the default extra.
	 */
	protected function addDefaultExtra($extraId, $position)
	{
		$this->defaultExtras[] = array('id' => $extraId, 'position' => $position);
	}

	/**
	 * Inserts a new module
	 *
	 * @param string $name The name of the module.
	 */
	protected function addModule($name)
	{
		$name = (string) $name;

		// module does not yet exists
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(name) FROM modules WHERE name = ?', $name))
		{
			// build item
			$item = array(
				'name' => $name,
				'installed_on' => gmdate('Y-m-d H:i:s'));

			// insert module
			$this->getDB()->insert('modules', $item);
		}

		// activate and update description
		else $this->getDB()->update('modules', array('installed_on' => gmdate('Y-m-d H:i:s')), 'name = ?', $name);
	}

	/**
	 * Add a search index
	 *
	 * @param string $module The module wherin will be searched.
	 * @param int $otherId The id of the record.
	 * @param  array $fields A key/value pair of fields to index.
	 * @param string[optional] $language The frontend language for this entry.
	 */
	protected function addSearchIndex($module, $otherId, array $fields, $language)
	{
		// get db
		$db = $this->getDB();

		// validate cache
		if(empty(self::$modules))
		{
			// get all modules
			self::$modules = (array) $db->getColumn('SELECT m.name FROM modules AS m');
		}

		// module exists?
		if(!in_array('search', self::$modules)) return;

		// no fields?
		if(empty($fields)) return;

		// insert search index
		foreach($fields as $field => $value)
		{
			// reformat value
			$value = strip_tags((string) $value);

			// insert in db
			$db->execute(
				'INSERT INTO search_index (module, other_id, language, field, value, active)
				 VALUES (?, ?, ?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE value = ?, active = ?',
				array((string) $module, (int) $otherId, (string) $language, (string) $field, $value, 'Y', $value, 'Y')
			);
		}

		// invalidate the cache for search
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/search/') as $file)
		{
			SpoonFile::delete(FRONTEND_CACHE_PATH . '/search/' . $file);
		}
	}

	/**
	 * Adds a warning to the stack of warnings
	 *
	 * @param string $message The message that needs to be displayed.
	 */
	public function addWarning($message)
	{
		$this->warnings[] = array('message' => $message);
	}

	/**
	 * Method that will be overriden by the specific installers
	 */
	protected function execute()
	{
		// just a placeholder
	}

	/**
	 * Get the database-handle
	 *
	 * @return SpoonDatabase
	 */
	protected function getDB()
	{
		return $this->db;
	}

	/**
	 * Get the default extras.
	 *
	 * @return array
	 */
	public function getDefaultExtras()
	{
		return $this->defaultExtras;
	}

	/**
	 * Get the default user
	 *
	 * @return int
	 */
	protected function getDefaultUserID()
	{
		try
		{
			// fetch default user id
			return (int) $this->getDB()->getVar(
				'SELECT id
				 FROM users
				 WHERE is_god = ? AND active = ? AND deleted = ?
				 ORDER BY id ASC',
				array('Y', 'Y', 'N')
			);
		}

		catch(Exception $e)
		{
			return 1;
		}
	}

	/**
	 * Get the selected cms interface languages
	 */
	protected function getInterfaceLanguages()
	{
		return $this->interfaceLanguages;
	}

	/**
	 * Get the selected languages
	 */
	protected function getLanguages()
	{
		return $this->languages;
	}

	/**
	 * Get a locale item.
	 *
	 * @param string $name
	 * @param string[optional] $module
	 * @param string[optional] $language The language abbreviation.
	 * @param string[optional] $type The type of locale.
	 * @param string[optional] $application
	 * @return mixed
	 */
	protected function getLocale($name, $module = 'core', $language = 'en', $type = 'lbl', $application = 'backend')
	{
		$translation = (string) $this->getDB()->getVar(
			'SELECT value
			 FROM locale
			 WHERE name = ? AND module = ? AND language = ? AND type = ? AND application = ?',
			array((string) $name, (string) $module, (string) $language, (string) $type, (string) $application)
		);

		return ($translation != '') ? $translation : $name;
	}

	/**
	 * Get a setting
	 *
	 * @param string $module The name of the module.
	 * @param string $name The name of the setting.
	 * @return mixed
	 */
	protected function getSetting($module, $name)
	{
		return unserialize($this->getDB()->getVar(
			'SELECT value
			 FROM modules_settings
			 WHERE module = ? AND name = ?',
			array((string) $module, (string) $name))
		);
	}

	/**
	 * Get the id of the requested template of the active theme.
	 *
	 * @param string $template
	 * @param string[optional] $theme
	 * @return int
	 */
	protected function getTemplateId($template, $theme = null)
	{
		// no theme set = default theme
		if($theme === null) $theme = $this->getSetting('core', 'theme');

		// return best matching template id
		return (int) $this->getDB()->getVar(
			'SELECT id FROM themes_templates
			 WHERE theme = ?
			 ORDER BY path LIKE ? DESC, id ASC
			 LIMIT 1',
			array((string) $theme, '%' . (string) $template . '%'));
	}

	/**
	 * Get a variable
	 *
	 * @param string $name
	 * @return mixed
	 */
	protected function getVariable($name)
	{
		return (!isset($this->variables[$name])) ? null : $this->variables[$name];
	}

	/**
	 * Get all warnings
	 *
	 * @return array
	 */
	public function getWarnings()
	{
		return $this->warnings;
	}

	/**
	 * Imports the locale XML file
	 *
	 * @param string $filename The full path for the XML-file.
	 * @param bool[optional] $overwriteConflicts Should we overwrite when there is a conflict?
	 */
	protected function importLocale($filename, $overwriteConflicts = false)
	{
		$filename = (string) $filename;
		$overwriteConflicts = (bool) $overwriteConflicts;

		// load the file content and execute it
		$content = trim(SpoonFile::getContent($filename));

		// file actually has content
		if(!empty($content))
		{
			// load xml
			$xml = @simplexml_load_file($filename);

			// import if it's valid xml
			if($xml !== false)
			{
				// import locale
				require_once BACKEND_MODULES_PATH . '/locale/engine/model.php';
				BackendLocaleModel::importXML($xml, $overwriteConflicts, $this->getLanguages(), $this->getInterfaceLanguages(), $this->getDefaultUserID(), gmdate('Y-m-d H:i:s'));
			}
		}
	}

	/**
	 * Imports the sql file
	 *
	 * @param string $filename The full path for the SQL-file.
	 */
	protected function importSQL($filename)
	{
		// load the file content and execute it
		$content = trim(SpoonFile::getContent($filename));

		// file actually has content
		if(!empty($content))
		{
			/**
			 * Some versions of PHP can't handle multiple statements at once, so split them
			 * We know this isn't the best solution, but we couldn't find a beter way.
			 */
			$queries = preg_split("/;(\r)?\n/", $content);

			// loop queries and execute them
			foreach($queries as $query) $this->getDB()->execute($query);
		}
	}

	/**
	 * Insert a dashboard widget
	 *
	 * @param array $module
	 * @param array $widget
	 * @param array $data
	 */
	protected function insertDashboardWidget($module, $widget, $data)
	{
		// get db
		$db = $this->getDB();

		// fetch current settings
		$groupSettings = (array) $db->getRecords('SELECT * FROM groups_settings WHERE name = ?', array('dashboard_sequence'));
		$userSettings = (array) $db->getRecords('SELECT * FROM users_settings WHERE name = ?', array('dashboard_sequence'));

		// loop group settings
		foreach($groupSettings as $settings)
		{
			// unserialize data
			$settings['value'] = unserialize($settings['value']);

			// add new widget
			$settings['value'][$module][$widget] = $data;

			// re-serialize value
			$settings['value'] = serialize($settings['value']);

			// update in db
			$db->update('groups_settings', $settings, 'group_id = ? AND name = ?', array($settings['group_id'], $settings['name']));
		}

		// loop user settings
		foreach($userSettings as $settings)
		{
			// unserialize data
			$settings['value'] = unserialize($settings['value']);

			// add new widget
			$settings['value'][$module][$widget] = $data;

			// re-serialize value
			$settings['value'] = serialize($settings['value']);

			// update in db
			$db->update('users_settings', $settings, 'user_id = ? AND name = ?', array($settings['user_id'], $settings['name']));
		}
	}

	/**
	 * Insert an extra
	 *
	 * @param string $module The module for the extra.
	 * @param string $type The type, possible values are: homepage, widget, block.
	 * @param string $label The label for the extra.
	 * @param string[optional] $action The action.
	 * @param string[optional] $data Optional data, will be passed in the extra.
	 * @param bool[optional] $hidden Is this extra hidden?
	 * @param int[optional] $sequence The sequence for the extra.
	 * @return int
	 */
	protected function insertExtra($module, $type, $label, $action = null, $data = null, $hidden = false, $sequence = null)
	{
		// no sequence set
		if(is_null($sequence))
		{
			// set next sequence number for this module
			$sequence = $this->getDB()->getVar('SELECT MAX(sequence) + 1 FROM modules_extras WHERE module = ?', array((string) $module));

			// this is the first extra for this module: generate new 1000-series
			if(is_null($sequence)) $sequence = $sequence = $this->getDB()->getVar('SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM modules_extras');
		}

		$module = (string) $module;
		$type = (string) $type;
		$label = (string) $label;
		$action = !is_null($action) ? (string) $action : null;
		$data = !is_null($data) ? (string) $data : null;
		$hidden = $hidden && $hidden !== 'N' ? 'Y' : 'N';
		$sequence = (int) $sequence;

		// build item
		$item = array(
			'module' => $module,
			'type' => $type,
			'label' => $label,
			'action' => $action,
			'data' => $data,
			'hidden' => $hidden,
			'sequence' => $sequence
		);

		// build query
		$query = 'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND label = ?';
		$parameters = array($item['module'], $item['type'], $item['label']);

		// data parameter must match
		if($data !== null)
		{
			$query .= ' AND data = ?';
			$parameters[] = $data;
		}

		// we need a nullio
		else $query .= ' AND data IS NULL';

		// get id (if its already exists)
		$extraId = (int) $this->getDB()->getVar($query, $parameters);

		// doesn't already exist
		if($extraId === 0)
		{
			// insert extra and return id
			return (int) $this->getDB()->insert('modules_extras', $item);
		}

		// exists so return id
		return $extraId;
	}

	/**
	 * Insert a meta item
	 *
	 * @param string $keywords The keyword of the item.
	 * @param string $description A description of the item.
	 * @param string $title The page title for the item.
	 * @param string $url The unique URL.
	 * @param bool[optional] $keywordsOverwrite Should the keywords be overwritten?
	 * @param bool[optional] $descriptionOverwrite Should the descriptions be overwritten?
	 * @param bool[optional] $titleOverwrite Should the pagetitle be overwritten?
	 * @param bool[optional] $urlOverwrite Should the URL be overwritten?
	 * @param string[optional] $custom Any custom meta-data.
	 * @param array[optional] $data Any custom meta-data.
	 * @return int
	 */
	protected function insertMeta($keywords, $description, $title, $url, $keywordsOverwrite = false, $descriptionOverwrite = false, $titleOverwrite = false, $urlOverwrite = false, $custom = null, $data = null)
	{
		$item = array(
			'keywords' => (string) $keywords,
			'keywords_overwrite' => ($keywordsOverwrite && $keywordsOverwrite !== 'N' ? 'Y' : 'N'),
			'description' => (string) $description,
			'description_overwrite' => ($descriptionOverwrite && $descriptionOverwrite !== 'N' ? 'Y' : 'N'),
			'title' => (string) $title,
			'title_overwrite' => ($titleOverwrite && $titleOverwrite !== 'N' ? 'Y' : 'N'),
			'url' => SpoonFilter::urlise((string) $url, SPOON_CHARSET),
			'url_overwrite' => ($urlOverwrite && $urlOverwrite !== 'N' ? 'Y' : 'N'),
			'custom' => (!is_null($custom) ? (string) $custom : null),
			'data' => (!is_null($data)) ? serialize($data) : null
		);

		return (int) $this->getDB()->insert('meta', $item);
	}

	/**
	 * Insert a page
	 *
	 * @param array $revision An array with the revision data.
	 * @param array[optional] $meta The meta-data.
	 * @param array[optional] $block The blocks.
	 */
	protected function insertPage(array $revision, array $meta = null, array $block = null)
	{
		$revision = (array) $revision;
		$meta = (array) $meta;

		// deactive previous revisions
		if(isset($revision['id']) && isset($revision['language'])) $this->getDB()->update('pages', array('status' => 'archive'), 'id = ? AND language = ?', array($revision['id'], $revision['language']));

		// build revision
		if(!isset($revision['language'])) throw new SpoonException('language is required for installing pages');
		if(!isset($revision['title'])) throw new SpoonException('title is required for installing pages');
		if(!isset($revision['id'])) $revision['id'] = (int) $this->getDB()->getVar('SELECT MAX(id) + 1 FROM pages WHERE language = ?', array($revision['language']));
		if(!$revision['id']) $revision['id'] = 1;
		if(!isset($revision['user_id'])) $revision['user_id'] = $this->getDefaultUserID();
		if(!isset($revision['template_id'])) $revision['template_id'] = $this->getTemplateId('default');
		if(!isset($revision['type'])) $revision['type'] = 'page';
		if(!isset($revision['parent_id'])) $revision['parent_id'] = ($revision['type'] == 'page' ? 1 : 0);
		if(!isset($revision['navigation_title'])) $revision['navigation_title'] = $revision['title'];
		if(!isset($revision['navigation_title_overwrite'])) $revision['navigation_title_overwrite'] = 'N';
		if(!isset($revision['hidden'])) $revision['hidden'] = 'N';
		if(!isset($revision['status'])) $revision['status'] = 'active';
		if(!isset($revision['publish_on'])) $revision['publish_on'] = gmdate('Y-m-d H:i:s');
		if(!isset($revision['created_on'])) $revision['created_on'] = gmdate('Y-m-d H:i:s');
		if(!isset($revision['edited_on'])) $revision['edited_on'] = gmdate('Y-m-d H:i:s');
		if(!isset($revision['data'])) $revision['data'] = null;
		if(!isset($revision['allow_move'])) $revision['allow_move'] = 'Y';
		if(!isset($revision['allow_children'])) $revision['allow_children'] = 'Y';
		if(!isset($revision['allow_edit'])) $revision['allow_edit'] = 'Y';
		if(!isset($revision['allow_delete'])) $revision['allow_delete'] = 'Y';
		if(!isset($revision['sequence'])) $revision['sequence'] = (int) $this->getDB()->getVar('SELECT MAX(sequence) + 1 FROM pages WHERE language = ? AND parent_id = ? AND type = ?', array($revision['language'], $revision['parent_id'], $revision['type']));

		// meta needs to be inserted
		if(!isset($revision['meta_id']))
		{
			// build meta
			if(!isset($meta['keywords'])) $meta['keywords'] = $revision['title'];
			if(!isset($meta['keywords_overwrite'])) $meta['keywords_overwrite'] = false;
			if(!isset($meta['description'])) $meta['description'] = $revision['title'];
			if(!isset($meta['description_overwrite'])) $meta['description_overwrite'] = false;
			if(!isset($meta['title'])) $meta['title'] = $revision['title'];
			if(!isset($meta['title_overwrite'])) $meta['title_overwrite'] = false;
			if(!isset($meta['url'])) $meta['url'] = $revision['title'];
			if(!isset($meta['url_overwrite'])) $meta['url_overwrite'] = false;
			if(!isset($meta['custom'])) $meta['custom'] = null;
			if(!isset($meta['data'])) $meta['data'] = null;

			// insert meta
			$revision['meta_id'] = $this->insertMeta($meta['keywords'], $meta['description'], $meta['title'], $meta['url'], $meta['keywords_overwrite'], $meta['description_overwrite'], $meta['title_overwrite'], $meta['url_overwrite'], $meta['custom'], $meta['data']);
		}

		// insert page
		$revision['revision_id'] = $this->getDB()->insert('pages', $revision);

		// array of positions and linked blocks (will be used to automatically set block sequence)
		$positions = array();

		// loop blocks: get arguments (this function has a variable length argument list, to allow multiple blocks to be added)
		for($i = 0; $i < func_num_args() - 2; $i++)
		{
			// get block
			$block = @func_get_arg($i + 2);
			if($block === false) $block = array();
			else $block = (array) $block;

			// save block position
			if(!isset($block['position'])) $block['position'] = 'main';
			$positions[$block['position']][] = $block;

			// build block
			if(!isset($block['revision_id'])) $block['revision_id'] = $revision['revision_id'];
			if(!isset($block['html'])) $block['html'] = '';
			elseif(SpoonFile::exists($block['html'])) $block['html'] = SpoonFile::getContent($block['html']);
			if(!isset($block['created_on'])) $block['created_on'] = gmdate('Y-m-d H:i:s');
			if(!isset($block['edited_on'])) $block['edited_on'] = gmdate('Y-m-d H:i:s');
			if(!isset($block['extra_id'])) $block['extra_id'] = null;
			if(!isset($block['visible'])) $block['visible'] = 'Y';
			if(!isset($block['sequence'])) $block['sequence'] = count($positions[$block['position']]) - 1;

			$this->getDB()->insert('pages_blocks', $block);
		}

		// return page id
		return $revision['id'];
	}

	/**
	 * Should example data be installed
	 *
	 * @return bool
	 */
	protected function installExample()
	{
		return $this->example;
	}

	/**
	 * Make a module searchable
	 *
	 * @param string $module The module to make searchable.
	 * @param bool[optional] $searchable Enable/disable search for this module by default?
	 * @param int[optional] $weight Set default search weight for this module.
	 */
	protected function makeSearchable($module, $searchable = true, $weight = 1)
	{
		$module = (string) $module;
		$searchable = $searchable && $searchable !== 'N' ? 'Y' : 'N';
		$weight = (int) $weight;

		// make module searchable
		$this->getDB()->execute(
			'INSERT INTO search_modules (module, searchable, weight) VALUES (?, ?, ?)
			 ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
			array($module, $searchable, $weight, $searchable, $weight)
		);
	}

	/**
	 * Set the rights for an action
	 *
	 * @param int $groupId The group wherefor the rights will be set.
	 * @param string $module The module wherin the action appears.
	 * @param string $action The action wherefor the rights have to set.
	 * @param int[optional] $level The leve, default is 7 (max).
	 */
	protected function setActionRights($groupId, $module, $action, $level = 7)
	{
		$groupId = (int) $groupId;
		$module = (string) $module;
		$action = (string) $action;
		$level = (int) $level;

		// check if the action already exists
		$exists = (bool) $this->getDB()->getVar(
			'SELECT COUNT(id)
			 FROM groups_rights_actions
			 WHERE group_id = ? AND module = ? AND action = ?',
			array($groupId, $module, $action)
		);

		// action doesn't exist
		if(!$exists)
		{
			// build item
			$item = array('group_id' => $groupId,
							'module' => $module,
							'action' => $action,
							'level' => $level);

			$this->getDB()->insert('groups_rights_actions', $item);
		}
	}

	/**
	 * Sets the rights for a module
	 *
	 * @param int $groupId The group wherefor the rights will be set.
	 * @param string $module The module too set the rights for.
	 */
	protected function setModuleRights($groupId, $module)
	{
		$groupId = (int) $groupId;
		$module = (string) $module;

		// module doesn't exist
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(id)
											FROM groups_rights_modules
											WHERE group_id = ? AND module = ?',
											array((int) $groupId, (string) $module)))
		{
			$item = array(
				'group_id' => $groupId,
				'module' => $module
			);

			$this->getDB()->insert('groups_rights_modules', $item);
		}
	}

	/**
	 * Set a new navigation item.
	 *
	 * @param int $parentId Id of the navigation item under we should add this.
	 * @param string $label Label for the item.
	 * @param string[optional] $url Url for the item. If ommitted the first child is used.
	 * @param array[optional] $selectedFor Set selected when these actions are active.
	 * @param int[optional] $sequence Sequence to use for this item.
	 * @return int
	 */
	protected function setNavigation($parentId, $label, $url = '', array $selectedFor = null, $sequence = null)
	{
		$parentId = (int) $parentId;
		$label = (string) $label;
		$url = (string) $url;
		$selectedFor = ($selectedFor !== null && is_array($selectedFor)) ? serialize($selectedFor) : null;

		// no custom sequence
		if($sequence === null)
		{
			// get maximum sequence for this parent
			$maxSequence = (int) $this->getDB()->getVar('SELECT MAX(sequence) FROM backend_navigation WHERE parent_id = ?', $parentId);

			// add at the end
			$sequence = $maxSequence + 1;
		}

		// a custom sequence was set
		else $sequence = (int) $sequence;

		// get the id for this url
		$id = (int) $this->getDB()->getVar(
			'SELECT id
			 FROM backend_navigation
			 WHERE parent_id = ? AND label = ? AND url = ?',
			array($parentId, $label, $url)
		);

		// doesn't exist yet, add it
		if($id === 0)
		{
			return $this->getDB()->insert('backend_navigation', array(
				'parent_id' => $parentId,
				'label' => $label,
				'url' => $url,
				'selected_for' => $selectedFor,
				'sequence' => $sequence)
			);
		}

		// already exists so return current id
		return $id;
	}

	/**
	 * Stores a module specific setting in the database.
	 *
	 * @param string $module The module wherefore the setting will be set.
	 * @param string $name The name of the setting.
	 * @param mixed[optional] $value The optional value.
	 * @param bool[optional] $overwrite Overwrite no matter what.
	 */
	protected function setSetting($module, $name, $value = null, $overwrite = false)
	{
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);
		$overwrite = (bool) $overwrite;

		if($overwrite)
		{
			$this->getDB()->execute(
				'INSERT INTO modules_settings (module, name, value)
				 VALUES (?, ?, ?)
				 ON DUPLICATE KEY UPDATE value = ?',
				array($module, $name, $value, $value)
			);
		}

		// don't overwrite
		else
		{
			// check if this setting already exists
			$exists = (bool) $this->getDB()->getVar(
				'SELECT COUNT(name)
				 FROM modules_settings
				 WHERE module = ? AND name = ?',
				array($module, $name)
			);

			// does not yet exist
			if(!$exists)
			{
				// build item
				$item = array(
					'module' => $module,
					'name' => $name,
					'value' => $value
				);

				$this->getDB()->insert('modules_settings', $item);
			}
		}
	}
}

/**
 * Installer for the core
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class CoreInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// validate variables
		if($this->getVariable('default_language') === null) throw new SpoonException('Default frontend language is not provided.');
		if($this->getVariable('default_interface_language') === null) throw new SpoonException('Default backend language is not provided.');
		if($this->getVariable('site_domain') === null) throw new SpoonException('Site domain is not provided.');
		if($this->getVariable('spoon_debug_email') === null) throw new SpoonException('Spoon debug email is not provided.');
		if($this->getVariable('api_email') === null) throw new SpoonException('API email is not provided.');
		if($this->getVariable('site_title') === null) throw new SpoonException('Site title is not provided.');

		// import SQL
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add core modules
		$this->addModule('core');
		$this->addModule('authentication');
		$this->addModule('dashboard');
		$this->addModule('error');

		$this->setRights();
		$this->setSettings();

		// add core navigation
		$this->setNavigation(null, 'Dashboard', 'dashboard/index', null, 1);
		$this->setNavigation(null, 'Modules', null, null, 3);
	}

	/**
	 * Set the rights
	 */
	private function setRights()
	{
		$this->setModuleRights(1, 'dashboard');

		$this->setActionRights(1, 'dashboard', 'index');
		$this->setActionRights(1, 'dashboard', 'alter_sequence');
	}

	/**
	 * Store the settings
	 */
	private function setSettings()
	{
		// languages settings
		$this->setSetting('core', 'languages', $this->getLanguages(), true);
		$this->setSetting('core', 'active_languages', $this->getLanguages(), true);
		$this->setSetting('core', 'redirect_languages', $this->getLanguages(), true);
		$this->setSetting('core', 'default_language', $this->getVariable('default_language'), true);
		$this->setSetting('core', 'interface_languages', $this->getInterfaceLanguages(), true);
		$this->setSetting('core', 'default_interface_language', $this->getVariable('default_interface_language'), true);

		// other settings
		$this->setSetting('core', 'theme');
		$this->setSetting('core', 'akismet_key', '');
		$this->setSetting('core', 'google_maps_key', '');
		$this->setSetting('core', 'max_num_revisions', 20);
		$this->setSetting('core', 'site_domains', array($this->getVariable('site_domain')));
		$this->setSetting('core', 'site_html_header', '');
		$this->setSetting('core', 'site_html_footer', '');

		// date & time
		$this->setSetting('core', 'date_format_short', 'j.n.Y');
		$this->setSetting('core', 'date_formats_short', array('j/n/Y', 'j-n-Y', 'j.n.Y', 'n/j/Y', 'n/j/Y', 'n/j/Y', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'm-d-Y', 'm.d.Y', 'j/n/y', 'j-n-y', 'j.n.y', 'n/j/y', 'n-j-y', 'n.j.y', 'd/m/y', 'd-m-y', 'd.m.y', 'm/d/y', 'm-d-y', 'm.d.y'));
		$this->setSetting('core', 'date_format_long', 'l j F Y');
		$this->setSetting('core', 'date_formats_long', array('j F Y', 'D j F Y', 'l j F Y', 'j F, Y', 'D j F, Y', 'l j F, Y', 'd F Y', 'd F, Y', 'F j Y', 'D F j Y', 'l F j Y', 'F d, Y', 'D F d, Y', 'l F d, Y'));
		$this->setSetting('core', 'time_format', 'H:i');
		$this->setSetting('core', 'time_formats', array('H:i', 'H:i:s', 'g:i a', 'g:i A'));

		// number formats
		$this->setSetting('core', 'number_format', 'dot_nothing');
		$this->setSetting('core', 'number_formats', array('comma_nothing' => '10000,25', 'dot_nothing' => '10000.25', 'dot_comma' => '10,000.25', 'comma_dot' => '10.000,25', 'dot_space' => '10000.25', 'comma_space' => '10 000,25'));

		// e-mail settings
		$this->setSetting('core', 'mailer_from', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));
		$this->setSetting('core', 'mailer_to', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));
		$this->setSetting('core', 'mailer_reply_to', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));

		// stmp settings
		$this->setSetting('core', 'smtp_server', $this->getVariable('smtp_server'));
		$this->setSetting('core', 'smtp_port', $this->getVariable('smtp_port'));
		$this->setSetting('core', 'smtp_username', $this->getVariable('smtp_username'));
		$this->setSetting('core', 'smtp_password', $this->getVariable('smtp_password'));

		// default titles
		$siteTitles = array('en' => 'My website',
							'cn' => '我的网站',
							'nl' => 'Mijn website',
							'fr' => 'Mon site web',
							'de' => 'Meine Webseite',
							'hu' => 'Hhonlapom',
							'it' => 'Il mio sito web',
							'ru' => 'мой сайт',
							'es' => 'Mi sitio web');

		// language specific
		foreach($this->getLanguages() as $language)
		{
			// set title
			$this->setSetting('core', 'site_title_' . $language, (isset($siteTitles[$language])) ? $siteTitles[$language] : $this->getVariable('site_title'));
		}

		/*
		 * We're going to try to install the settings for the api.
		 */
		require_once PATH_LIBRARY . '/external/fork_api.php';

		// create new instance
		$api = new ForkAPI();

		try
		{
			// get the keys
			$keys = $api->coreRequestKeys($this->getVariable('site_domain'), $this->getVariable('api_email'));

			// api settings
			$this->setSetting('core', 'fork_api_public_key', $keys['public']);
			$this->setSetting('core', 'fork_api_private_key', $keys['private']);

			// set keys
			$api->setPublicKey($keys['public']);
			$api->setPrivateKey($keys['private']);

			// get services
			$services = (array) $api->pingGetServices();

			// set services
			if(!empty($services)) $this->setSetting('core', 'ping_services', array('services' => $services, 'date' => time()));
		}

		// catch exceptions
		catch(Exception $e)
		{
			// we don't need those keys.
		}

		// ckfinder
		$this->setSetting('core', 'ckfinder_license_name', 'Fork CMS');
		$this->setSetting('core', 'ckfinder_license_key', 'QJH2-32UV-6VRM-V6Y7-A91J-W26Z-3F8R');
	}
}
