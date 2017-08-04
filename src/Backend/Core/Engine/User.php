<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;

/**
 * The class below will handle all stuff relates to the current authenticated user
 */
class User
{
    /**
     * The groups
     *
     * @var array
     */
    private $groups = [];

    /**
     * Is the user-object a valid one? As in: is the user authenticated
     *
     * @var bool
     */
    private $isAuthenticated = false;

    /**
     * Is the authenticated user a god?
     *
     * @var bool
     */
    private $isGod = false;

    /**
     * Last timestamp the user logged in
     *
     * @var int
     */
    private $lastLoggedInDate;

    /**
     * The secret key for the user
     *
     * @var string
     */
    private $secretKey;

    /**
     * The session id for the user
     *
     * @var string
     */
    private $sessionId;

    /**
     * All settings
     *
     * @var array
     */
    private $settings = [];

    /**
     * The users id
     *
     * @var int
     */
    private $userId;

    /**
     * The email
     *
     * @var string
     */
    private $email;

    public function __construct(int $userId = null, string $email = null)
    {
        if ($userId !== null) {
            $this->loadUser($userId);
        }
        if ($email !== null) {
            $this->loadUserByEmail($email);
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLastLoggedInDate(): int
    {
        return $this->lastLoggedInDate;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @param string $key The key for the setting to get.
     * @param mixed $defaultValue Default value, will be stored if the setting isn't set.
     *
     * @return mixed
     */
    public function getSetting(string $key, $defaultValue = null)
    {
        // if the value isn't present we should set a default value
        if (!isset($this->settings[$key])) {
            $this->setSetting($key, $defaultValue);
        }

        // return
        return $this->settings[$key];
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function isGod(): bool
    {
        return $this->isGod;
    }

    public function loadUser(int $userId): void
    {
        // get database instance
        $database = BackendModel::getContainer()->get('database');

        // get user-data
        $userData = (array) $database->getRecord(
            'SELECT u.id, u.email, u.is_god, us.session_id, us.secret_key, UNIX_TIMESTAMP(us.date) AS date
             FROM users AS u
             LEFT OUTER JOIN users_sessions AS us ON u.id = us.user_id AND us.session_id = ?
             WHERE u.id = ?
             LIMIT 1',
            [BackendModel::getSession()->getId(), $userId]
        );

        // if there is no data we have to destroy this object, I know this isn't a realistic situation
        if (empty($userData)) {
            throw new Exception('user (' . $userId . ') can\'t be loaded.');
        }

        // set properties
        $this->userId = (int) $userData['id'];
        $this->email = $userData['email'];
        $this->sessionId = $userData['session_id'];
        $this->secretKey = $userData['secret_key'];
        $this->lastLoggedInDate = (int) $userData['date'];
        $this->isAuthenticated = true;
        $this->isGod = (bool) $userData['is_god'];

        $this->loadGroups();

        // get settings
        $settings = (array) $database->getPairs(
            'SELECT us.name, us.value
             FROM users_settings AS us
             WHERE us.user_id = ?',
            [$userId]
        );

        // loop settings and store them in the object
        foreach ($settings as $key => $value) {
            $this->settings[$key] = unserialize($value);
        }

        // nickname available?
        if (!isset($this->settings['nickname']) || $this->settings['nickname'] === '') {
            $this->setSetting('nickname', $this->settings['name'] . ' ' . $this->settings['surname']);
        }
    }

    private function loadGroups(): void
    {
        $this->groups = (array) BackendModel::get('database')->getColumn(
            'SELECT group_id
             FROM users_groups
             WHERE user_id = ?',
            [$this->userId]
        );
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function loadUserByEmail(string $email): void
    {
        $database = BackendModel::getContainer()->get('database');

        $userId = (int) $database->getVar(
            'SELECT u.id
             FROM users AS u
             LEFT OUTER JOIN users_sessions AS us ON u.id = us.user_id AND us.session_id = ?
             WHERE u.email = ?
             LIMIT 1',
            [BackendModel::getSession()->getId(), $email]
        );

        // if there is no data we have to destroy this object, I know this isn't a realistic situation
        if ($userId === 0) {
            throw new Exception('user (' . $email . ') can\'t be loaded.');
        }

        $this->loadUser($userId);
    }

    public function setSetting(string $key, $value): void
    {
        $valueToStore = serialize($value);

        // get database
        $database = BackendModel::getContainer()->get('database');

        // store
        $database->execute(
            'INSERT INTO users_settings(user_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            [$this->getUserId(), $key, $valueToStore, $valueToStore]
        );

        // cache setting
        $this->settings[$key] = $value;
    }
}
