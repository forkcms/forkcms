<?php

/**
 * BackendModulemanagerModel
 * In this file we store all generic functions that we will be using in the module_manager module
 *
 * @package		backend
 * @subpackage	projects
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
	
	public static function get($module)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM modules AS i
															WHERE i.name = ? LIMIT 1',array((string)$module));
	}
	
	public static function update(array $item,$id)
	{
		$item['active'] = $item['active'] ? 'Y' : 'N';
		BackendModel::getDB(true)->update('modules', $item, 'name = ?', array((string)$id));
	}
	
	public static function updateAction(array $item,$id)
	{
		BackendModel::getDB(true)->update('groups_rights_actions', $item, 'id = ?', array((int)$id));
	}
	
	public static function getActiveModules($active = 'Y',$exclude)
	{
		return (array) BackendModel::getDB()->getRecords('SELECT i.name
															FROM modules AS i
															WHERE i.name NOT IN("'.implode('","',(array)$exclude).'") AND i.active = ?',array((string)$active));
	}
	
	public static function getInstalledModules($exclude)
	{
		return (array) BackendModel::getDB()->getColumn('SELECT i.name
															FROM modules AS i WHERE i.name NOT IN("'.implode('","',(array)$exclude).'")');
	}
	
	public static function getModuleActions($module)
	{
		return (array) BackendModel::getDB()->getRecords('SELECT i.*
															FROM groups_rights_actions AS i
															WHERE i.module = ?',array((string)$module));
	}
	
	public static function getGroupsForDropdown()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT i.id, i.name
															FROM groups AS i');
	}
	
	public static function getModulesForDropdown()
	{
			return (array) BackendModel::getDB()->getPairs('SELECT i.name as id, i.name 
																FROM modules AS i');
	}
	
	public static function exists($module)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.name
														FROM modules AS i
														WHERE i.name = ?',
														array((string) $module));
	}
	
	public static function actionExists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM groups_rights_actions AS i
														WHERE i.id = ?',
														array((int) $id));
	}
	
	public static function getAction($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
														FROM groups_rights_actions AS i
														WHERE i.id = ? LIMIT 1',
														array((int) $id));
	}
	
	public static function rightActionExists($group_id, $action, $module, $level)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM groups_rights_actions AS i
														WHERE i.group_id = ? AND i.module = ? AND i.action = ? AND i.level = ?',
														array((int)$group_id, (string) $module, (string)$action,(int)$level));
	}
	
	public static function insertAction(array $item)
	{
		return BackendModel::getDB(true)->insert('groups_rights_actions', $item);
	}
	
	public static function deleteAction($id)
	{
		BackendModel::getDB()->delete('groups_rights_actions', 'id = ?', array((int)$id));
	}
	
	public static function resetLocale($module)
	{
		$db = BackendModel::getDB();
		
		$db->delete('locale', 'module = ?', array($module));

		self::buildLocale();
	}
	
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
}

?>