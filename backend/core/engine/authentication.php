<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * The class below will handle all authentication stuff. It will handle module-access, action-acces, ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
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
	 */
	public static function cleanupOldSessions()
	{
		// remove all sessions that are invalid (older then 30 min)
		BackendModel::getDB(true)->delete('users_sessions', 'date <= DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
	}

	/**
	 * Returns the encrypted password for a user by giving a email/password
	 * Returns false if no user was found for this user/pass combination
	 *
	 * @param string $email The email.
	 * @param string $password The password.
	 * @return string
	 */
	public static function getEncryptedPassword($email, $password)
	{
		$email = (string) $email;
		$password = (string) $password;

		// fetch user ID by email
		$userId = BackendUsersModel::getIdByEmail($email);

		// check if a user ID was found, return false if no user exists
		if($userId === false) return false;

		// fetch user record
		$user = new BackendUser($userId);
		$key = $user->getSetting('password_key');

		// return the encrypted string
		return (string) self::getEncryptedString($password, $key);
	}

	/**
	 * Returns a string encrypted like sha1(md5($salt) . md5($string))
	 * 	The salt is an optional extra string you can strenghten your encryption with
	 *
	 * @param string $string The string to encrypt.
	 * @param string[optional] $salt The salt to use.
	 * @return string
	 */
	public static function getEncryptedString($string, $salt = null)
	{
		$string = (string) $string;
		$salt = (string) $salt;

		// return the encrypted string
		return (string) sha1(md5($salt) . md5($string));
	}

	/**
	 * Returns the current authenticated user
	 *
	 * @return BackendUser
	 */
	public static function getUser()
	{
		// if the user-object doesn't exist create a new one
		if(self::$user === null) self::$user = new BackendUser();
		return self::$user;
	}

	/**
	 * Is the given action allowed for the current user
	 *
	 * @param string $action The action to check for.
	 * @param string $module The module wherin the action is located.
	 * @return bool
	 */
	public static function isAllowedAction($action = null, $module = null)
	{
		// GOD's rule them all!
		if(self::getUser()->isGod()) return true;

		// always allowed actions (yep, hardcoded, because we don't want other people to fuck up)
		$alwaysAllowed = array(
			'dashboard' => array('index' => 7),
			'core' => array('generate_url' => 7, 'content_css' => 7),
			'error' => array('index' => 7),
			'authentication' => array('index' => 7, 'reset_password' => 7, 'logout' => 7)
		);

		// grab the URL from the reference
		$URL = Spoon::get('url');

		$action = ($action !== null) ? (string) $action : $URL->getAction();
		$module = ($module !== null) ? (string) $module : $URL->getModule();

		// is this action an action that doesn't require authentication?
		if(isset($alwaysAllowed[$module][$action])) return true;

		// we will cache everything
		if(empty(self::$allowedActions))
		{
			// init var
			$db = BackendModel::getDB();

			// get modules
			$modules = BackendModel::getModules();

			// add always allowed
			foreach($alwaysAllowed as $allowedModule => $actions) $modules[] = $allowedModule;

			// get allowed actions
			$allowedActionsRows = (array) $db->getRecords(
				'SELECT gra.module, gra.action, MAX(gra.level) AS level
				 FROM users_sessions AS us
				 INNER JOIN users AS u ON us.user_id = u.id
				 INNER JOIN users_groups AS ug ON u.id = ug.user_id
				 INNER JOIN groups_rights_actions AS gra ON ug.group_id = gra.group_id
				 WHERE us.session_id = ? AND us.secret_key = ?
				 GROUP BY gra.module, gra.action',
				array(SpoonSession::getSessionId(), SpoonSession::get('backend_secret_key'))
			);

			// add all actions and there level
			foreach($allowedActionsRows as $row)
			{
				// add if the module is installed
				if(in_array($row['module'], $modules)) self::$allowedActions[$row['module']][$row['action']] = (int) $row['level'];
			}
		}

		// do we know a level for this action
		if(isset(self::$allowedActions[$module][$action]))
		{
			// is the level greater than zero? aka: do we have access?
			if((int) self::$allowedActions[$module][$action] > 0) return true;
		}

		// fallback
		return false;
	}

	/**
	 * Is the given module allowed for the current user
	 *
	 * @param string $module The module to check for.
	 * @return bool
	 */
	public static function isAllowedModule($module)
	{
		// GOD's rule them all!
		if(self::isLoggedIn() && self::getUser()->isGod()) return true;

		// always allowed modules (yep, hardcoded, because, we don't want other people to fuck up)
		$alwaysAllowed = array('core', 'error', 'authentication');

		// redefine
		$module = (string) $module;

		// is this module a module that doesn't require authentication?
		if(in_array($module, $alwaysAllowed)) return true;

		// do we already know something?
		if(empty(self::$allowedModules))
		{
			// init var
			$db = BackendModel::getDB();

			// get allowed modules
			$allowedModules = $db->getColumn(
				'SELECT DISTINCT grm.module
				 FROM users_sessions AS us
				 INNER JOIN users AS u ON us.user_id = u.id
				 INNER JOIN users_groups AS ug ON u.id = ug.user_id
				 INNER JOIN groups_rights_modules AS grm ON ug.group_id = grm.group_id
				 WHERE us.session_id = ? AND us.secret_key = ?',
				array(SpoonSession::getSessionId(), SpoonSession::get('backend_secret_key'))
			);

			// add all modules
			foreach($allowedModules as $row) self::$allowedModules[$row] = true;
		}

		// not available in our cache
		if(!isset(self::$allowedModules[$module])) return false;

		// return value that was stored in cache
		else return self::$allowedModules[$module];
	}

	/**
	 * Is the current user logged in?
	 *
	 * @return bool
	 */
	public static function isLoggedIn()
	{
		// check if all needed values are set in the session
		// @todo could be written by SpoonSession::get (since that no longer throws exceptions)
		if(SpoonSession::exists('backend_logged_in', 'backend_secret_key') && (bool) SpoonSession::get('backend_logged_in') && (string) SpoonSession::get('backend_secret_key') != '')
		{
			// get database instance
			$db = BackendModel::getDB(true);

			// get the row from the tables
			$sessionData = $db->getRecord(
				'SELECT us.id, us.user_id
				 FROM users_sessions AS us
				 WHERE us.session_id = ? AND us.secret_key = ?
				 LIMIT 1',
				array(SpoonSession::getSessionId(), SpoonSession::get('backend_secret_key'))
			);

			// if we found a matching row, we know the user is logged in, so we update his session
			if($sessionData !== null)
			{
				// update the session in the table
				$db->update('users_sessions', array('date' => BackendModel::getUTCDate()), 'id = ?', (int) $sessionData['id']);

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
	 * Login the user with the given credentials.
	 * Will return a boolean that indicates if the user is logged in.
	 *
	 * @param string $login The users login.
	 * @param string $password The password provided by the user.
	 * @return bool
	 */
	public static function loginUser($login, $password)
	{
		$login = (string) $login;
		$password = (string) $password;

		$db = BackendModel::getDB(true);

		// fetch the encrypted password
		$passwordEncrypted = BackendAuthentication::getEncryptedPassword($login, $password);

		// check in database (is the user active and not deleted, are the email and password correct?)
		$userId = (int) $db->getVar(
			'SELECT u.id
			 FROM users AS u
			 WHERE u.email = ? AND u.password = ? AND u.active = ? AND u.deleted = ?
			 LIMIT 1',
			array($login, $passwordEncrypted, 'Y', 'N')
		);

		// not 0 = valid user!
		if($userId !== 0)
		{
			// cleanup old sessions
			self::cleanupOldSessions();

			// build the session array (will be stored in the database)
			$session = array();
			$session['user_id'] = $userId;
			$session['secret_key'] = BackendAuthentication::getEncryptedString(SpoonSession::getSessionId(), $userId);
			$session['session_id'] = SpoonSession::getSessionId();
			$session['date'] = BackendModel::getUTCDate();

			// insert a new row in the session-table
			$db->insert('users_sessions', $session);

			// store some values in the session
			SpoonSession::set('backend_logged_in', true);
			SpoonSession::set('backend_secret_key', $session['secret_key']);

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

	/**
	 * Logsout the current user
	 */
	public static function logout()
	{
		// remove all rows owned by the current user
		BackendModel::getDB(true)->delete('users_sessions', 'session_id = ?', SpoonSession::getSessionId());

		// reset values. We can't destroy the session because session-data can be used on the site.
		SpoonSession::set('backend_logged_in', false);
		SpoonSession::set('backend_secret_key', '');
	}
}
