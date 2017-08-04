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
    const QUERY_DATAGRID_BROWSE_SYNONYMS =
        'SELECT i.id, i.term, i.synonym
         FROM search_synonyms AS i
         WHERE i.language = ?';

    const QUERY_DATAGRID_BROWSE_STATISTICS =
        'SELECT UNIX_TIMESTAMP(i.time) AS time, i.term, i.data
         FROM search_statistics AS i
         WHERE i.language = ?';

    public static function deleteSynonym(int $synonymId): void
    {
        // delete synonym
        BackendModel::getContainer()->get('database')->delete('search_synonyms', 'id = ?', [$synonymId]);

        // invalidate the cache for search
        self::invalidateCache();
    }

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

    public static function existsSynonymByTerm(string $searchTerm, int $excludedId = null): bool
    {
        if ($excludedId === null) {
            return (bool) BackendModel::getContainer()->get('database')->getVar(
                'SELECT 1
                 FROM search_synonyms
                 WHERE term = ?
                 LIMIT 1',
                [$searchTerm]
            );
        }

        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM search_synonyms
             WHERE term = ? AND id != ?
             LIMIT 1',
            [$searchTerm, $excludedId]
        );
    }

    public static function getModuleSettings(): array
    {
        return BackendModel::getContainer()->get('database')->getRecords(
            'SELECT module, searchable, weight
             FROM search_modules',
            [],
            'module'
        );
    }

    public static function getSynonym(int $synonymId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT *
             FROM search_synonyms
             WHERE id = ?',
            [$synonymId]
        );
    }

    /**
     * Insert module search settings
     *
     * @param string $module The module wherein will be searched.
     * @param string $searchable Is the module searchable?
     * @param string $weight Weight of this module's results.
     */
    public static function insertModuleSettings(string $module, string $searchable, string $weight): void
    {
        // insert or update
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO search_modules (module, searchable, weight)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
            [$module, $searchable, $weight, $searchable, $weight]
        );

        // invalidate the cache for search
        self::invalidateCache();
    }

    public static function insertSynonym(array $synonym): int
    {
        // insert into database
        $id = BackendModel::getContainer()->get('database')->insert('search_synonyms', $synonym);

        // invalidate the cache for search
        self::invalidateCache();

        // return insert id
        return $id;
    }

    public static function invalidateCache(): void
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
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param string $language The language to use.
     */
    public static function removeIndex(string $module, int $otherId, string $language = null): void
    {
        if (BackendModel::isModuleInstalled('Search')) {
            return;
        }

        // delete indexes
        BackendModel::getContainer()->get('database')->delete(
            'search_index',
            'module = ? AND other_id = ? AND language = ?',
            [$module, $otherId, $language ?? BL::getWorkingLanguage()]
        );

        // invalidate the cache for search
        self::invalidateCache();
    }

    /**
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param array $fields A key/value pair of fields to index.
     * @param string $language The frontend language for this entry.
     */
    public static function saveIndex(string $module, int $otherId, array $fields, string $language = null): void
    {
        if (!BackendModel::isModuleInstalled('Search')) {
            return;
        }

        // no fields?
        if (empty($fields)) {
            return;
        }

        $language = $language ?? BL::getWorkingLanguage();

        // get database
        $database = BackendModel::getContainer()->get('database');

        // insert search index
        foreach ($fields as $field => $value) {
            // reformat value
            $value = strip_tags((string) $value);

            // update search index
            $database->execute(
                'INSERT INTO search_index (module, other_id, language, field, value, active)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?, active = ?',
                [$module, $otherId, $language, (string) $field, $value, true, $value, true]
            );
        }

        // invalidate the cache for search
        self::invalidateCache();
    }

    /**
     * @param array $synonym The data to update in the database.
     */
    public static function updateSynonym(array $synonym): void
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
