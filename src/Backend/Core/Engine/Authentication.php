<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * The class below will handle all authentication stuff. It will handle module-access, action-access, ...
 */
class Authentication
{
    /**
     * All allowed modules
     *
     * @var array
     */
    private static $allowedActions = [];

    /**
     * All allowed modules
     *
     * @var array
     */
    private static $allowedModules = [];

    /**
     * A user object for the current authenticated user
     *
     * @var User
     */
    private static $user;

    /**
     * Check the strength of the password
     *
     * @param string $password The password.
     *
     * @return string
     */
    public static function checkPassword(string $password): string
    {
        return PasswordStrengthChecker::checkPassword($password);
    }

    /**
     * Cleanup sessions for the current user and sessions that are invalid
     */
    public static function cleanupOldSessions(): void
    {
        // remove all sessions that are invalid (older then 30 min)
        BackendModel::get('database')->delete('users_sessions', 'date <= DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
    }

    /**
     * Encrypt the password with PHP password_hash function.
     *
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify the password with PHP password_verify function.
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public static function verifyPassword(string $email, string $password): bool
    {
        $encryptedPassword = BackendUsersModel::getEncryptedPassword($email);

        return password_verify($password, $encryptedPassword);
    }

    /**
     * Returns a string encrypted like sha1(md5($salt) . md5($string))
     *    The salt is an optional extra string you can strengthen your encryption with
     *
     * @param string $string The string to encrypt.
     * @param string $salt The salt to use.
     *
     * @return string
     */
    public static function getEncryptedString(string $string, string $salt = null): string
    {
        return (string) sha1(md5($salt) . md5($string));
    }

    /**
     * Returns the current authenticated user
     *
     * @return User
     */
    public static function getUser(): User
    {
        // if the user-object doesn't exist create a new one
        if (self::$user === null) {
            self::$user = new User();
        }

        return self::$user;
    }

    public static function getAllowedActions(): array
    {
        if (!empty(self::$allowedActions)) {
            return self::$allowedActions;
        }

        $allowedActionsRows = (array) BackendModel::get('database')->getRecords(
            'SELECT gra.module, gra.action, MAX(gra.level) AS level
            FROM users_sessions AS us
            INNER JOIN users AS u ON us.user_id = u.id
            INNER JOIN users_groups AS ug ON u.id = ug.user_id
            INNER JOIN groups_rights_actions AS gra ON ug.group_id = gra.group_id
            WHERE us.session_id = ? AND us.secret_key = ?
            GROUP BY gra.module, gra.action',
            [BackendModel::getSession()->getId(), BackendModel::getSession()->get('backend_secret_key')]
        );

        // add all actions and their level
        $modules = BackendModel::getModules();
        foreach ($allowedActionsRows as $row) {
            // add if the module is installed
            if (in_array($row['module'], $modules, true)) {
                self::$allowedActions[$row['module']][$row['action']] = (int) $row['level'];
            }
        }

        return self::$allowedActions;
    }

    /**
     * Is the given action allowed for the current user
     *
     * @param string $action The action to check for.
     * @param string $module The module wherein the action is located.
     *
     * @return bool
     */
    public static function isAllowedAction(string $action = null, string $module = null): bool
    {
        $alwaysAllowed = self::getAlwaysAllowed();

        // The url should only be taken from the container if the action and or module isn't set
        // This way we can use the command also in the a console command
        $action = $action ?: BackendModel::get('url')->getAction();
        $module = \SpoonFilter::toCamelCase($module ?: BackendModel::get('url')->getModule());

        // is this action an action that doesn't require authentication?
        if (isset($alwaysAllowed[$module][$action])) {
            return true;
        }

        // users that aren't logged in can only access always allowed items
        if (!self::isLoggedIn()) {
            return false;
        }

        // module exists and God user is enough to be allowed
        if (in_array($module, BackendModel::getModules(), true) && self::getUser()->isGod()) {
            return true;
        }

        $allowedActions = self::getAllowedActions();

        // do we know a level for this action
        if (isset($allowedActions[$module][$action])) {
            // is the level greater than zero? aka: do we have access?
            if ((int) $allowedActions[$module][$action] > 0) {
                return true;
            }
        }

        return false;
    }

    private static function getAlwaysAllowed(): array
    {
        return [
            'Core' => ['GenerateUrl' => 7, 'ContentCss' => 7, 'Templates' => 7],
            'Error' => ['Index' => 7],
            'Authentication' => ['Index' => 7, 'ResetPassword' => 7, 'Logout' => 7],
        ];
    }

    /**
     * Is the given module allowed for the current user
     *
     * @param string $module The module to check for.
     *
     * @return bool
     */
    public static function isAllowedModule(string $module): bool
    {
        $modules = BackendModel::getModules();
        $alwaysAllowed = array_keys(self::getAlwaysAllowed());
        $module = \SpoonFilter::toCamelCase($module);

        // is this module a module that doesn't require user level authentication?
        if (in_array($module, $alwaysAllowed, true)) {
            return true;
        }

        // users that aren't logged in can only access always allowed items
        if (!self::isLoggedIn()) {
            return false;
        }

        // module is active and God user, good enough
        if (in_array($module, $modules, true) && self::getUser()->isGod()) {
            return true;
        }

        // do we already know something?
        if (empty(self::$allowedModules)) {
            $database = BackendModel::get('database');

            // get allowed modules
            $allowedModules = (array) $database->getColumn(
                'SELECT DISTINCT grm.module
                 FROM users_sessions AS us
                 INNER JOIN users AS u ON us.user_id = u.id
                 INNER JOIN users_groups AS ug ON u.id = ug.user_id
                 INNER JOIN groups_rights_modules AS grm ON ug.group_id = grm.group_id
                 WHERE us.session_id = ? AND us.secret_key = ?',
                [BackendModel::getSession()->getId(), BackendModel::getSession()->get('backend_secret_key')]
            );

            foreach ($allowedModules as $row) {
                self::$allowedModules[$row] = true;
            }
        }

        return isset(self::$allowedModules[$module]) ?? false;
    }

    /**
     * Is the current user logged in?
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        if (BackendModel::getContainer()->has('logged_in')) {
            return (bool) BackendModel::getContainer()->get('logged_in');
        }

        // check if all needed values are set in the session
        if (!(bool) BackendModel::getSession()->get('backend_logged_in')
            || (string) BackendModel::getSession()->get('backend_secret_key') === '') {
            self::logout();

            return false;
        }

        $database = BackendModel::get('database');

        // get the row from the tables
        $sessionData = $database->getRecord(
            'SELECT us.id, us.user_id
             FROM users_sessions AS us
             WHERE us.session_id = ? AND us.secret_key = ?
             LIMIT 1',
            [BackendModel::getSession()->getId(), BackendModel::getSession()->get('backend_secret_key')]
        );

        // if we found a matching row, we know the user is logged in, so we update his session
        if ($sessionData !== null) {
            // update the session in the table
            $database->update(
                'users_sessions',
                ['date' => BackendModel::getUTCDate()],
                'id = ?',
                (int) $sessionData['id']
            );

            // create a user object, it will handle stuff related to the current authenticated user
            self::$user = new User($sessionData['user_id']);

            // the user is logged on
            BackendModel::getContainer()->set('logged_in', true);

            return true;
        }

        self::logout();

        return false;
    }

    /**
     * Login the user with the given credentials.
     * Will return a boolean that indicates if the user is logged in.
     *
     * @param string $login The users login.
     * @param string $password The password provided by the user.
     *
     * @return bool
     */
    public static function loginUser(string $login, string $password): bool
    {
        $database = BackendModel::get('database');

        // check password
        if (!static::verifyPassword($login, $password)) {
            return false;
        }

        // check in database (is the user active and not deleted, are the email and password correct?)
        $userId = (int) $database->getVar(
            'SELECT u.id
             FROM users AS u
             WHERE u.email = ? AND u.active = ? AND u.deleted = ?
             LIMIT 1',
            [$login, true, false]
        );

        if ($userId === 0) {
            // userId 0 will not exist, so it means that this isn't a valid combination
            // reset values for invalid users. We can't destroy the session
            // because session-data can be used on the site.
            self::logout();

            return false;
        }

        // cleanup old sessions
        self::cleanupOldSessions();

        // build the session array (will be stored in the database)
        $session = [
            'user_id' => $userId,
            'secret_key' => static::getEncryptedString(BackendModel::getSession()->getId(), $userId),
            'session_id' => BackendModel::getSession()->getId(),
            'date' => BackendModel::getUTCDate(),
        ];

        // insert a new row in the session-table
        $database->insert('users_sessions', $session);

        // store some values in the session
        BackendModel::getSession()->set('backend_logged_in', true);
        BackendModel::getSession()->set('backend_secret_key', $session['secret_key']);

        // update/instantiate the value for the logged_in container.
        BackendModel::getContainer()->set('logged_in', true);
        self::$user = new User($userId);

        return true;
    }

    /**
     * Logout the current user
     */
    public static function logout(): void
    {
        // remove all rows owned by the current user
        BackendModel::get('database')->delete('users_sessions', 'session_id = ?', BackendModel::getSession()->getId());

        // reset values. We can't destroy the session because session-data can be used on the site.
        BackendModel::getSession()->set('backend_logged_in', false);
        BackendModel::getSession()->set('backend_secret_key', '');
        BackendModel::getSession()->set('csrf_token', '');
    }

    /**
     * Reset our class to make sure no contamination from previous
     * authentications persists. This signifies a deeper issue with
     * this class. Solving the issue would be preferable to introducting
     * another method. This currently only exists to serve the test.
     */
    public static function tearDown(): void
    {
        self::$allowedActions = [];
        self::$allowedModules = [];
        self::$user = null;
    }
}
