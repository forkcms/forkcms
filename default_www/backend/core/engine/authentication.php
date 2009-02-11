<?php

/**
 * BackendAuthentication
 *
 * The class below will handle all authentication stuff. It will handle module-access, action-acces, ...
 *
 * @package		backend
 * @subpackage	authentication
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendAuthentication
{
	/**
	 * All allowed modules
	 *
	 * @var	array
	 */
	private static $allowedActions = array();


	/**
	 * All allowed modules
	 *
	 * @var	array
	 */
	private static $allowedModules = array();


	/**
	 * A userobject for the current authenticated user
	 *
	 * @var	BackendUser
	 */
	private static $user;


	/**
	 * Cleanup sessions for the current user and sessions that are invalid
	 *
	 * @return	void
	 */
	public static function cleanupOlderSessions()
	{
		// init var
		$db = BackendModel::getDB();

		// remove all sessions that are invalid (older then 30min)
		$db->delete('users_sessions', 'date <= DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
	}


	/**
	 * Returns the current authenticated user
	 *
	 * @return	BackendUser
	 */
	public static function getUser()
	{
		// if the user-object doesn't exists create a new one
		if(self::$user === null) self::$user = new BackendUser();

		// return the object
		return self::$user;
	}


	/**
	 * Is the given action allowed for the current user
	 *
	 * @return	bool
	 * @param	string $module
	 */
	public static function isAllowedAction($action, $module)
	{
		// always allowed actions (yep, hardcoded, because we don't want other people to fuck up)
		$aAlwaysAllowed = array('error' => array('index'), 'authentication' => array('index', 'logout'));

		// redefine
		$action = (string) $action;
		$module = (string) $module;

		// is this action an action that doesn't require authentication?
		if(isset($aAlwaysAllowed[$module]) && in_array($action, $aAlwaysAllowed[$module])) return true;

		// we will cache everything
		if(empty(self::$allowedActions))
		{
			// init var
			$db = BackendModel::getDB();

			// get allowed actions
			$aAllowedActions = (array) $db->retrieve('SELECT gra.module, gra.action, gra.level
														FROM users_sessions AS us
														INNER JOIN users AS u ON us.user_id = u.id
														INNER JOIN groups_rights_actions AS gra ON u.group_id = gra.group_id
														WHERE us.session_id = ? AND us.secret_key = ?;',
														array(SpoonSession::getSessionId(), SpoonSession::get('backend_secret_key')));

			// add all actions and there level
			foreach($aAllowedActions as $row) self::$allowedActions[$row['module']][$row['action']] = (int) $row['level'];
		}

		// do we know a level for this action
		if(isset(self::$allowedActions[$module][$action]))
		{
			// is the level greather then zero? aka: do we have access?
			if(self::$allowedActions[$module][$action] > 0) return true;
		}

		// fallback
		return false;
	}


	/**
	 * Is the given module allowed for the current user
	 *
	 * @return	bool
	 * @param	string $module
	 */
	public static function isAllowedModule($module)
	{
		// always allowed modules (yep, hardcoded, because, we don't want other people to fuck up)
		$aAlwaysAllowed = array('error', 'authentication');

		// redefine
		$module = (string) $module;

		// is this module a module that doesn't require authentication?
		if(in_array($module, $aAlwaysAllowed)) return true;

		// do we already know something?
		if(empty(self::$allowedModules))
		{
			// init var
			$db = BackendModel::getDB();

			// get allowed modules
			$aAllowedModules = $db->getColumn('SELECT grm.module
												FROM users_sessions AS us
												INNER JOIN users AS u ON us.user_id = u.id
												INNER JOIN groups_rights_modules AS grm ON u.group_id = grm.group_id
												WHERE us.session_id = ? AND us.secret_key = ?;',
												array(SpoonSession::getSessionId(), SpoonSession::get('backend_secret_key')));

			// add all modules
			foreach($aAllowedModules as $row) self::$allowedModules[$row] = true;
		}

		// return result
		return (bool) (!isset(self::$allowedModules[$module])) ? false : self::$allowedModules[$module];
	}



	/**
	 * Is the current user logged in?
	 *
	 * @return	bool
	 */
	public static function isLoggedIn()
	{
		// check if all needed values are set in the session
		if(SpoonSession::exists('backend_logged_in') && (bool) SpoonSession::get('backend_logged_in') && SpoonSession::exists('backend_secret_key') && (string) SpoonSession::get('backend_secret_key') != '')
		{
			// get database instance
			$db = BackendModel::getDB();

			// get the row from the tables
			$sessionData = $db->getRecord('SELECT us.id, us.user_id
											FROM users_sessions AS us
											WHERE us.session_id = ? AND us.secret_key = ?
											LIMIT 1;',
											array(SpoonSession::getSessionId(), SpoonSession::get('backend_secret_key')));

			// if we found a matching row we know the user is logged in, so we update his session
			if($sessionData !== null)
			{
				// update the session in the table
				$db->update('users_sessions', array('date' => date('Y-m-d H:i:s')), 'id = ?', (int) $sessionData['id']);

				// create a user object, it will handle stuff related to the current authenticated user
				self::$user = new BackendUser($sessionData['user_id']);

				// the user is logged on
				return true;
			}

			// no data found, so fuck up the session, will be handled later on in the code
			else SpoonSession::set('backend_logged_in', false);
		}

		// no data found, so fuck up the session, will be handled later on in the code
		else SpoonSession::set('backend_logged_in', false);

		// reset values for invalid users. We can't destroy the session because session-data can be used on the site.
		if((bool) SpoonSession::get('backend_logged_in') === false)
		{
			// reset some values
			SpoonSession::set('backend_logged_in', false);
			SpoonSession::set('backend_secret_key', '');

			// return result
			return false;
		}
	}


	/**
	 * Logsout the current user
	 *
	 * @return	void
	 */
	public static function logout()
	{
		// init var
		$db = BackendModel::getDB();

		// remove all rows owned by the current user
		$db->delete('users_sessions', 'session_id = ?', SpoonSession::getSessionId());

		// reset values. We can't destroy the session because session-data can be used on the site.
		SpoonSession::set('backend_logged_in', false);
		SpoonSession::set('backend_secret_key', '');
	}


	/**
	 * Login the user with the given credentials.
	 * Will return a boolean that indicates if the user is logged in.
	 *
	 * @return	bool
	 * @param	string $login
	 * @param	string $password
	 */
	public static function loginUser($login, $password)
	{
		// redefine
		$login = (string) $login;
		$password = (string) $password;

		// init vars
		$db = BackendModel::getDB();

		// check in database (is the user active and not deleted, is the username and password correct?)
		$userId = (int) $db->getVar('SELECT u.id
										FROM users AS u
										WHERE u.username = ? AND u.password = ? AND u.active = ? AND u.deleted = ?
										LIMIT 1;',
										array($login, md5($password), 'Y', 'N'));

		// not 0, a valid user!
		if($userId !== 0)
		{
			// cleanup old sessions
			BackendAuthentication::cleanupOlderSessions();

			// build the session array (will be stored in the database)
			$aSession = array();
			$aSession['user_id'] = $userId;
			$aSession['secret_key'] = md5(md5($userId) . md5(SpoonSession::getSessionId()));
			$aSession['session_id'] = SpoonSession::getSessionId();
			$aSession['date'] = date('Y-m-d H:i:s');

			// insert a new row in the session-table
			$db->insert('users_sessions', $aSession);

			// store some values in the session
			SpoonSession::set('backend_logged_in', true);
			SpoonSession::set('backend_secret_key', $aSession['secret_key']);

			// return result
			return true;
		}

		// userId 0 will not exist, so it means that this isn't a valid combination
		else
		{
			// reset values for invalid users. We can't destroy the session because session-data can be used on the site.
			SpoonSession::set('backend_logged_in', false);
			SpoonSession::set('backend_secret_key', '');

			// return result
			return false;
		}
	}
}

?>