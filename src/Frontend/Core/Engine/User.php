<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * The class below will handle all stuff relates to users
 */
class User
{
    /**
     * An array that will store all user objects
     *
     * @var array
     */
    private static $cache = [];

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

    /**
     * @param int|null $userId If you provide a userId, the object will be loaded with the data for this user.
     */
    public function __construct(int $userId = null)
    {
        // if a user id is given we will load the user in this object
        if ($userId !== null) {
            $this->loadUser($userId);
        }
    }

    /**
     * Get a backend user
     *
     * @param int $userId The users id in the backend.
     *
     * @return self
     */
    public static function getBackendUser(int $userId): self
    {
        // create new instance if necessary and cache it
        if (!isset(self::$cache[$userId])) {
            self::$cache[$userId] = new self($userId);
        }

        return self::$cache[$userId];
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $setting The name of the setting.
     *
     * @return mixed The stored value, if the setting wasn't found null will be returned
     */
    public function getSetting(string $setting)
    {
        // not set? return null
        if (!isset($this->settings[$setting])) {
            return;
        }

        // return
        return $this->settings[$setting];
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Load the data for the given user
     *
     * @param int $userId The users id in the backend.
     *
     * @throws Exception
     */
    public function loadUser(int $userId): void
    {
        // get database instance
        $database = Model::getContainer()->get('database');

        // get user-data
        $userData = (array) $database->getRecord(
            'SELECT u.id, u.email
             FROM users AS u
             WHERE u.id = ?
             LIMIT 1',
            [$userId]
        );

        // if there is no data we have to destroy this object, I know this isn't a realistic situation
        if (empty($userData)) {
            throw new Exception('The user (' . $userId . ') doesn\'t exist.');
        }

        // set properties
        $this->userId = (int) $userData['id'];
        $this->email = (string) $userData['email'];

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
    }
}
