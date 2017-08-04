<?php

namespace Frontend\Modules\Profiles\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;

/**
 * In this file we store all generic functions that we will be using to get and set profile information.
 */
class Profile
{
    /**
     * The display name.
     *
     * @var string
     */
    private $displayName;

    /**
     * The profile email.
     *
     * @var string
     */
    private $email;

    /**
     * The groups this profile belongs to, if any. The keys are the group IDs, the values the HTML-escaped group names.
     *
     * @var array
     */
    protected $groups;

    /**
     * The profile id.
     *
     * @var int
     */
    private $id;

    /**
     * The profile register date (unix timestamp).
     *
     * @var int
     */
    private $registeredOn;

    /**
     * The profile settings.
     *
     * @var array
     */
    private $settings = [];

    /**
     * The profile status.
     *
     * @var string
     */
    private $status;

    /**
     * The profile url.
     *
     * @var string
     */
    private $url;

    public function __construct(int $profileId = null)
    {
        if ($profileId !== null) {
            $this->loadProfile($profileId);
        }
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegisteredOn(): int
    {
        return $this->registeredOn;
    }

    /**
     * Get a profile setting by name.
     *
     * @param string $name Setting name.
     * @param string $defaultValue Default value is used when the setting does not exist.
     *
     * @return mixed
     */
    public function getSetting(string $name, string $defaultValue = null)
    {
        // if settings array does not exist then get it first
        if (empty($this->settings)) {
            $this->settings = $this->getSettings();
        }

        // when setting exists return it
        if (array_key_exists($name, $this->settings)) {
            return $this->settings[$name];
        }

        // if not return default value
        return $defaultValue;
    }

    public function getSettings(): array
    {
        // if settings array does not exist then get it first
        if (empty($this->settings)) {
            $this->settings = FrontendProfilesModel::getSettings($this->getId());
        }

        // return settings
        return $this->settings;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Does this user belong to the group with the given ID?
     *
     * @param int $groupId Group id.
     *
     * @return bool
     */
    public function isInGroup(int $groupId): bool
    {
        return isset($this->groups[$groupId]);
    }

    private function loadProfile(int $profileId): void
    {
        // get profile data
        $profileData = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT p.id, p.email, p.status, p.display_name, p.url, UNIX_TIMESTAMP(p.registered_on) AS registered_on
             FROM profiles AS p
             WHERE p.id = ?',
            $profileId
        );

        // set properties
        $this->setId($profileData['id']);
        $this->setUrl($profileData['url']);
        $this->setEmail($profileData['email']);
        $this->setStatus($profileData['status']);
        $this->setDisplayName($profileData['display_name']);
        $this->setRegisteredOn($profileData['registered_on']);

        // get the groups (only the ones we still have access to)
        $this->groups = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT pg.id, pg.name
             FROM profiles_groups AS pg
             INNER JOIN profiles_groups_rights AS pgr ON pg.id = pgr.group_id
             WHERE pgr.profile_id = :id AND (pgr.expires_on IS NULL OR pgr.expires_on >= NOW())',
            [':id' => $profileId]
        );
    }

    public function loadProfileByUrl(string $url): void
    {
        // get profile data
        $profileData = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT p.id, p.email, p.status, p.display_name, UNIX_TIMESTAMP(p.registered_on) AS registered_on
             FROM profiles AS p
             WHERE p.url = ?',
            $url
        );

        // set properties
        $this->setId($profileData['id']);
        $this->setEmail($profileData['email']);
        $this->setStatus($profileData['status']);
        $this->setDisplayName($profileData['display_name']);
        $this->setRegisteredOn($profileData['registered_on']);

        // get the groups (only the ones we still have access to)
        $this->groups = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT pg.id, pg.name
             FROM profiles_groups AS pg
             INNER JOIN profiles_groups_rights AS pgr ON pg.id = pgr.group_id
             WHERE pgr.profile_id = :id AND (pgr.expires_on IS NULL OR pgr.expires_on >= NOW())',
            [':id' => (int) $this->getId()]
        );

        $this->settings = (array) FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT i.name, i.value
             FROM profiles_settings AS i
             WHERE i.profile_id = ?',
            $this->getId()
        );

        foreach ($this->settings as &$value) {
            $value = unserialize($value);
        }
    }

    public function setDisplayName(string $value): void
    {
        $this->displayName = $value;
    }

    public function setEmail(string $value): void
    {
        $this->email = $value;
    }

    private function setId(int $value): void
    {
        $this->id = $value;
    }

    public function setRegisteredOn(int $value): void
    {
        $this->registeredOn = $value;
    }

    /**
     * @param string $name Setting name.
     * @param mixed $value New setting value.
     */
    public function setSetting(string $name, $value): void
    {
        // make sure we have the current settings in cache
        $this->getSettings();

        // set setting
        FrontendProfilesModel::setSetting($this->getId(), $name, $value);

        // add setting to cache
        $this->settings[$name] = $value;
    }

    /**
     * Insert or update multiple profile settings.
     *
     * @param array $values Settings in key=>value form.
     */
    public function setSettings(array $values): void
    {
        // make sure we have the current settings in cache
        $this->getSettings();

        // set settings
        FrontendProfilesModel::setSettings($this->getId(), $values);

        // add settings to cache
        foreach ($values as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    public function setStatus(string $value): void
    {
        $this->status = $value;
    }

    public function setUrl(string $value): void
    {
        $this->url = $value;
    }

    /**
     * Convert the object into an array for usage in the template
     *
     * @return array
     */
    public function toArray(): array
    {
        $profile = [
            'display_name' => $this->getDisplayName(),
            'registered_on' => $this->getRegisteredOn(),
            'url' => [
                'dashboard' => FrontendNavigation::getUrlForBlock('Profiles'),
                'settings' => FrontendNavigation::getUrlForBlock('Profiles', 'Settings'),
                'url' => $this->getUrl(),
            ],
        ];

        // add settings
        foreach ($this->getSettings() as $key => $value) {
            $profile['settings'][$key] = $value;
        }

        return $profile;
    }
}
