<?php

namespace Common;

use Psr\Cache\CacheItemPoolInterface;
use SpoonDatabase;

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
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @param SpoonDatabase $database
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(SpoonDatabase $database, CacheItemPoolInterface $cache)
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

        /*
         * Instead of invalidating the cache, we could also fetch existing
         * settings, update them & re-store them to cache. That would save
         * us the next query to repopulate the cache.
         * However, there could be race conditions where 2 concurrent
         * requests write at the same time and one ends up overwriting the
         * other (unless we do a CAS, but PSR-6 doesn't support that)
         * Clearing cache will be safe: in the case of concurrent requests
         * & cache being regenerated while the other is being saved, it will
         * be cleared again after saving the new setting!
         */
        $this->cache->deleteItems(array('settings'));
    }

    /**
     * Deletes a module setting
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

        /*
         * Instead of invalidating the cache, we could also fetch existing
         * settings, update them & re-store them to cache. That would save
         * us the next query to repopulate the cache.
         * However, there could be race conditions where 2 concurrent
         * requests write at the same time and one ends up overwriting the
         * other (unless we do a CAS, but PSR-6 doesn't support that)
         * Clearing cache will be safe: in the case of concurrent requests
         * & cache being regenerated while the other is being saved, it will
         * be cleared again after saving the new setting!
         */
        $this->cache->deleteItems(array('settings'));
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
        $item = $this->cache->getItem('settings');
        if ($item->isHit()) {
            return $item->get();
        }

        $settings = $this->getAllSettingsFromDatabase();
        $item->set($settings);
        $this->cache->save($item);

        return $settings;
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

        // loop settings & unserialize the values
        $groupedSettings = array();
        foreach ($settings as $row) {
            $groupedSettings[$row['module']][$row['name']] = unserialize(
                $row['value']
            );
        }

        return $groupedSettings;
    }
}
