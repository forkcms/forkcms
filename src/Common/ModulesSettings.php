<?php

namespace Common;

use SpoonDatabase;
use Common\Cache\Cache;

/**
 * This is our module settings class
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class ModulesSettings
{
    /**
     * @var SpoonDatabase
     */
    private $database;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param SpoonDatabase $database
     * @param Cache $cache
     */
    public function __construct(SpoonDatabase $database, Cache $cache)
    {
        $this->database = $database;
        $this->cache = $cache;
    }

    /**
     * Get a module setting
     *
     * @param  string $module       The module wherefore a setting has to be retrieved.
     * @param  string $key          The name of the setting to be retrieved.
     * @param  mixed  $defaultValue A fallback value
     * @return mixed
     */
    public function get($module, $key, $defaultValue = null)
    {
        $settings = $this->getSettings();

        if (isset($settings[$module][$key])) {
            return $settings[$module][$key];
        }

        return $defaultValue;
    }

    /**
     * Store a module setting
     *
     * @param string $module The module wherefore a setting has to be stored.
     * @param string $key    The name of the setting.
     * @param mixed  $value  The value to save
     */
    public function set($module, $key, $value)
    {
        $valueToStore = serialize($value);

        $this->database->execute(
            'INSERT INTO modules_settings(module, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            array($module, $key, $valueToStore, $valueToStore)
        );

        $settings = $this->getSettings();

        if (!isset($settings[$module])) {
            $settings[$module] = array();
        }
        $settings[$module][$key] = $value;

        $this->cache->cache('settings', $settings);
    }

    /**
     * Delets a module setting
     *
     * @param string $module
     * @param string $key
     */
    public function delete($module, $key)
    {
        $this->database->delete(
            'modules_settings',
            'module = :module and name = :name',
            array(
                'module' => $module,
                'name' => $key,
            )
        );

        $settings = $this->getSettings();

        if (isset($settings[$module][$key])) {
            unset($settings[$module][$key]);
            $this->cache->cache('settings', $settings);
        }
    }

    /**
     * Get all module settings for a module
     *
     * @param  string $module The module wherefore a setting has to be retrieved.
     * @return array
     */
    public function getForModule($module)
    {
        $settings = $this->getSettings();

        if (isset($settings[$module])) {
            return $settings[$module];
        }

        return array();
    }

    /**
     * Fetches all the settings
     *
     * @return array
     */
    private function getSettings()
    {
        if (!$this->cache->isCached('settings')) {
            $this->cache->cache('settings', $this->getAllSettingsFromDatabase());
        }

        return $this->cache->getFromCache('settings');
    }

    /**
     * Reads all the settings from the database and groups them by module
     *
     * @return array
     */
    private function getAllSettingsFromDatabase()
    {
        // fetch settings
        $settings = (array) $this->database->getRecords(
            'SELECT ms.module, ms.name, ms.value
             FROM modules_settings AS ms
             INNER JOIN modules AS m ON ms.module = m.name'
        );

        // loop settings and cache them, also unserialize the values
        $groupedSettings = array();
        foreach ($settings as $row) {
            $groupedSettings[$row['module']][$row['name']] = unserialize(
                $row['value']
            );
        }

        return $groupedSettings;
    }
}
