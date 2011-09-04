<?php

/**
 * BackendModulemanagerModel
 * In this file we store all generic functions that we will be using in the module_manager module
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerModel
{
	
	const QRY_DATAGRID_BROWSE_ACTIONS = 'SELECT i.id, i.action, g.name as group_name, i.level
										FROM groups_rights_actions AS i
										INNER JOIN groups as g ON i.group_id = g.id
										WHERE i.module = ?';


	/**
	 * Check if an action exists
	 *
	 * @return	boolean
	 * @param	int $id	The id of the action.
	 */
	public static function actionExists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM groups_rights_actions AS i
														WHERE i.id = ?',
														array((int) $id));
	}


	/**
	 * Build the locale
	 *
	 * @return	void
	 */
	public static function buildLocale()
	{
		$db = BackendModel::getDB();
		
		// loop all the languages
		foreach(BackendLanguage::getActiveLanguages() as $language)
		{
			// get applications
			$applications = $db->getColumn('SELECT DISTINCT application
													FROM locale
													WHERE language = ?',
													array((string) $language));
			// loop applications
			foreach((array) $applications as $application)
			{
				// build application locale cache
				BackendLocaleModel::buildCache($language ,$application);
			}
		}
	}


	/**
	 * Delete a module
	 *
	 * @return	void
	 * @param	string $module		The name of the module.
	 */
	public static function delete($module)
	{
		$db = BackendModel::getDB();
		$module = (string) $module;

		$db->delete('modules', 'name = ?', array($module));
		$db->delete('modules_settings', 'module = ?', array($module));
		$db->delete('groups_rights_actions', 'module = ?', array($module));
		$db->delete('groups_rights_modules', 'module = ?', array($module));
		$db->delete('modules_tags', 'module = ?', array($module));

		BackendModel::deleteExtra($module);

		self::resetLocale($module);
	}


	/**
	 * Insert the action in the database
	 *
	 * @return	void
	 * @param	int $id		The id of the action.
	 */
	public static function deleteAction($id)
	{
		BackendModel::getDB()->delete('groups_rights_actions', 'id = ?', array((int) $id));
	}


	/**
	 * Check if a module exists
	 *
	 * @return	boolean
	 * @param	string $module	The module name.
	 */
	public static function exists($module)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.name
														FROM modules AS i
														WHERE i.name = ?',
														array((string) $module));
	}


	/**
	 * Get the module
	 *
	 * @return	array
	 * @param	string $module	The module name.
	 */
	public static function get($module)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM modules AS i
															WHERE i.name = ? LIMIT 1',array((string) $module));
	}


	/**
	 * Get an action
	 *
	 * @return	array
	 * @param	int $id	The id of the action.
	 */
	public static function getAction($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
														FROM groups_rights_actions AS i
														WHERE i.id = ? LIMIT 1',
														array((int) $id));
	}


	/**
	 * Get all active modules
	 *
	 * @return	array
	 * @param	array $exclude				An array of modules to exclude.
	 * @param	string[optional] $active	An array of data.
	 */
	public static function getActiveModules($exclude, $active = 'Y')
	{
		return (array) BackendModel::getDB()->getRecords('SELECT i.name
															FROM modules AS i
															WHERE i.name NOT IN("' . implode('","',(array) $exclude) . '") AND i.active = ?', array((string) $active));
	}


	/**
	 * Get all the right groups for a dropdown.
	 *
	 * @return	array
	 */
	public static function getGroupsForDropdown()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT i.id, i.name
															FROM groups AS i');
	}


	/**
	 * Get all installed modules
	 *
	 * @return	array
	 * @param	array $exclude		An array of modules to exclude.
	 */
	public static function getInstalledModules($exclude)
	{
		return (array) BackendModel::getDB()->getColumn('SELECT i.name
															FROM modules AS i WHERE i.name NOT IN("' . implode('","',(array) $exclude) . '")');
	}


	/**
	 * Get the missing moddule actions
	 *
	 * @return	array
	 * @param	string $module	The module name.
	 */
	public static function getMissingActions($module)
	{
		$module_actions_path = BACKEND_MODULES_PATH . '/' . $module . '/actions';
		$action_files = SpoonFile::getlist($module_actions_path, '/(.*).php/');
		
		$module_ajax_path = BACKEND_MODULES_PATH . '/' . $module . '/ajax';
		$ajax_files = SpoonFile::getlist($module_ajax_path, '/(.*).php/');
		
		$module_actions_array = array();
		$actions_from_database = BackendModulemanagerModel::getModuleActions($module);
		
		
		foreach($action_files as $file)
		{
			$path = $module_ajax_path . '/' . $file;
			$fileInfo = SpoonFile::getInfo($path);
			$module_actions_array[] = array('file' => $file, 'path' => $path, 'action' => $fileInfo['name']);
		}

		foreach($ajax_files as $file)
		{
			$path = $module_ajax_path . '/' . $file;
			$fileInfo = SpoonFile::getInfo($path);
			$module_actions_array[] = array('file' => $file, 'path' => $path, 'action' => $fileInfo['name']);
		}

		foreach($module_actions_array as $key => $existing_action_file)
		{

			if(in_array($existing_action_file['action'],$actions_from_database))
			{
				unset($module_actions_array[$key]);
			}
		}
		
		return $module_actions_array;
	}


	/**
	 * Get all the actions of a module
	 *
	 * @return	array
	 * @param	string $module	The module name.
	 */
	public static function getModuleActions($module)
	{
		return (array) BackendModel::getDB()->getColumn('SELECT i.action
															FROM groups_rights_actions AS i
															WHERE i.module = ?',array((string) $module));
	}


	/**
	 * Get all the modules groups for a dropdown.
	 *
	 * @return	array
	 */
	public static function getModulesForDropdown()
	{
			return (array) BackendModel::getDB()->getPairs('SELECT i.name AS id, i.name 
																FROM modules AS i');
	}


	/**
	 * Insert the action in the database
	 *
	 * @return	int
	 * @param	array $item		The data that need to be inserted.
	 */
	public static function insertAction(array $item)
	{
		return BackendModel::getDB(true)->insert('groups_rights_actions', $item);
	}


	/**
	 * Deletes and rebuilds the locale
	 *
	 * @return	void
	 * @param	string $module		The name of the module.
	 */
	public static function resetLocale($module)
	{
		$db = BackendModel::getDB();
		
		$db->delete('locale', 'module = ?', array($module));

		self::buildLocale();
	}


	/**
	 * Check if the action exists for a specific group, module and level
	 *
	 * @return	boolean
	 * @param	int $group_id	The id of the group.
	 * @param	string $action	The name of the actions.
	 * @param	string $module	The name of the module.
	 * @param	int $level		The level of the action.
	 */
	public static function rightActionExists($group_id, $action, $module, $level)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM groups_rights_actions AS i
														WHERE i.group_id = ? AND i.module = ? AND i.action = ? AND i.level = ?',
														array((int) $group_id, (string) $module, (string) $action,(int) $level));
	}


	/**
	 * Update the module
	 *
	 * @return	id
	 * @param	array $item		An array of data.
	 */
	public static function update(array $item)
	{
		$item['active'] = $item['active'] ? 'Y' : 'N';
		BackendModel::getDB(true)->update('modules', $item, 'name = ?', array($item['name']));
	}


	/**
	 * Update the action
	 *
	 * @return	id
	 * @param	array $item		An array of data.
	 */
	public static function updateAction(array $item)
	{
		BackendModel::getDB(true)->update('groups_rights_actions', $item, 'id = ?', array( (int) $item['id']));
	}
	

}

?>