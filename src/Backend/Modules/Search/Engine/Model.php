<?php

namespace Backend\Modules\Search\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the search module
 */
class Model
{
    const QRY_DATAGRID_BROWSE_SYNONYMS =
        'SELECT i.id, i.term, i.synonym
         FROM search_synonyms AS i
         WHERE i.language = ?';

    const QRY_DATAGRID_BROWSE_STATISTICS =
        'SELECT UNIX_TIMESTAMP(i.time) AS time, i.term, i.data
         FROM search_statistics AS i
         WHERE i.language = ?';

    /**
     * Delete a synonym
     *
     * @param int $id The id of the item we want to delete.
     */
    public static function deleteSynonym(int $id)
    {
        // delete synonym
        BackendModel::getContainer()->get('database')->delete('search_synonyms', 'id = ?', [$id]);

        // invalidate the cache for search
        self::invalidateCache();
    }

    /**
     * Check if a synonym exists
     *
     * @param int $id The id of the item we're looking for.
     *
     * @return bool
     */
    public static function existsSynonymById(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM search_synonyms
             WHERE id = ?
             LIMIT 1',
            [$id]
        );
    }

    /**
     * Check if a synonym exists
     *
     * @param string $term The term we're looking for.
     * @param int $exclude Exclude a certain id.
     *
     * @return bool
     */
    public static function existsSynonymByTerm(string $term, int $exclude = null): bool
    {
        if ($exclude === null) {
            return (bool) BackendModel::getContainer()->get('database')->getVar(
                'SELECT 1
                 FROM search_synonyms
                 WHERE term = ?
                 LIMIT 1',
                [$term]
            );
        }

        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM search_synonyms
             WHERE term = ? AND id != ?
             LIMIT 1',
            [$term, $exclude]
        );
    }

    /**
     * Get modules search settings
     *
     * @return array
     */
    public static function getModuleSettings(): array
    {
        return BackendModel::getContainer()->get('database')->getRecords(
            'SELECT module, searchable, weight
             FROM search_modules',
            [],
            'module'
        );
    }

    /**
     * Get a synonym
     *
     * @param int $id The id of the item we're looking for.
     *
     * @return array
     */
    public static function getSynonym(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT *
             FROM search_synonyms
             WHERE id = ?',
            [$id]
        );
    }

    /**
     * Insert module search settings
     *
     * @param string $module The module wherein will be searched.
     * @param string $searchable Is the module searchable?
     * @param string $weight Weight of this module's results.
     */
    public static function insertModuleSettings(string $module, string $searchable, string $weight)
    {
        // insert or update
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO search_modules (module, searchable, weight)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
            array($module, $searchable, $weight, $searchable, $weight)
        );

        // invalidate the cache for search
        self::invalidateCache();
    }

    /**
     * Insert a synonym
     *
     * @param array $synonym The data to insert in the db.
     *
     * @return int
     */
    public static function insertSynonym(array $synonym): int
    {
        // insert into db
        $id = BackendModel::getContainer()->get('database')->insert('search_synonyms', $synonym);

        // invalidate the cache for search
        self::invalidateCache();

        // return insert id
        return $id;
    }

    /**
     * Invalidate search cache
     */
    public static function invalidateCache()
    {
        $finder = new Finder();
        $filesystem = new Filesystem();
        foreach ($finder->files()->in(FRONTEND_CACHE_PATH . '/Search/') as $file) {
            $filesystem->remove($file->getRealPath());
        }

        // clear the php5.5+ opcode cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Remove an index
     *
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param string $language The language to use.
     */
    public static function removeIndex(string $module, int $otherId, string $language = null)
    {
        if (BackendModel::isModuleInstalled('Search')) {
            return;
        }

        // delete indexes
        BackendModel::getContainer()->get('database')->delete(
            'search_index',
            'module = ? AND other_id = ? AND language = ?',
            array($module, $otherId, $language ?? BL::getWorkingLanguage())
        );

        // invalidate the cache for search
        self::invalidateCache();
    }

    /**
     * Edit an index
     *
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param array $fields A key/value pair of fields to index.
     * @param string $language The frontend language for this entry.
     */
    public static function saveIndex(string $module, int $otherId, array $fields, string $language = null)
    {
        if (BackendModel::isModuleInstalled('Search')) {
            return;
        }

        // no fields?
        if (empty($fields)) {
            return;
        }

        $language = $language ?? BL::getWorkingLanguage();

        // get db
        $db = BackendModel::getContainer()->get('database');

        // insert search index
        foreach ($fields as $field => $value) {
            // reformat value
            $value = strip_tags((string) $value);

            // update search index
            $db->execute(
                'INSERT INTO search_index (module, other_id, language, field, value, active)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?, active = ?',
                array($module, $otherId, $language, (string) $field, $value, 'Y', $value, 'Y')
            );
        }

        // invalidate the cache for search
        self::invalidateCache();
    }

    /**
     * Update a synonym
     *
     * @param array $synonym The data to update in the db.
     */
    public static function updateSynonym(array $synonym)
    {
        // update
        BackendModel::getContainer()->get('database')->update(
            'search_synonyms',
            $synonym,
            'id = ?',
            [$synonym['id']]
        );

        // invalidate the cache for search
        self::invalidateCache();
    }
}
