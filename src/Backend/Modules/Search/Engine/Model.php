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

use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the search module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
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
    public static function deleteSynonym($id)
    {
        // delete synonym
        BackendModel::getContainer()->get('database')->delete('search_synonyms', 'id = ?', array((int) $id));

        // invalidate the cache for search
        static::invalidateCache();
    }

    /**
     * Check if a synonym exists
     *
     * @param int $id The id of the item we're looking for.
     * @return bool
     */
    public static function existsSynonymById($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM search_synonyms
             WHERE id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Check if a synonym exists
     *
     * @param string $term    The term we're looking for.
     * @param int    $exclude Exclude a certain id.
     * @return bool
     */
    public static function existsSynonymByTerm($term, $exclude = null)
    {
        if ($exclude == null) {
            return (bool) BackendModel::getContainer()->get('database')->getVar(
                'SELECT 1
                 FROM search_synonyms
                 WHERE term = ?
                 LIMIT 1',
                array((string) $term)
            );
        }

        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM search_synonyms
             WHERE term = ? AND id != ?
             LIMIT 1',
            array((string) $term, (int) $exclude)
        );
    }

    /**
     * Get modules search settings
     *
     * @return array
     */
    public static function getModuleSettings()
    {
        return BackendModel::getContainer()->get('database')->getRecords(
            'SELECT module, searchable, weight
             FROM search_modules',
            array(),
            'module'
        );
    }

    /**
     * Get a synonym
     *
     * @param int $id The id of the item we're looking for.
     * @return array
     */
    public static function getSynonym($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT *
             FROM search_synonyms
             WHERE id = ?',
            array((int) $id)
        );
    }

    /**
     * Insert module search settings
     *
     * @param string $module     The module wherein will be searched.
     * @param string $searchable Is the module searchable?
     * @param string $weight     Weight of this module's results.
     */
    public static function insertModuleSettings($module, $searchable, $weight)
    {
        // insert or update
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO search_modules (module, searchable, weight)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
            array($module['module'], $searchable, $weight, $searchable, $weight)
        );

        // invalidate the cache for search
        static::invalidateCache();
    }

    /**
     * Insert a synonym
     *
     * @param array $item The data to insert in the db.
     * @return int
     */
    public static function insertSynonym($item)
    {
        // insert into db
        $id = BackendModel::getContainer()->get('database')->insert('search_synonyms', $item);

        // invalidate the cache for search
        static::invalidateCache();

        // return insert id
        return $id;
    }

    /**
     * Invalidate search cache
     */
    public static function invalidateCache()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        foreach ($finder->files()->in(FRONTEND_CACHE_PATH . '/Search/') as $file) {
            $fs->remove($file->getRealPath());
        }

        // clear the php5.5+ opcode cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Remove an index
     *
     * @param string $module   The module wherein will be searched.
     * @param int    $otherId  The id of the record.
     * @param string $language The language to use.
     */
    public static function removeIndex($module, $otherId, $language = null)
    {
        // module exists?
        if (!in_array('Search', BackendModel::getModules())) {
            return;
        }

        // set language
        if (!$language) {
            $language = BL::getWorkingLanguage();
        }

        // delete indexes
        BackendModel::getContainer()->get('database')->delete(
            'search_index',
            'module = ? AND other_id = ? AND language = ?',
            array((string) $module, (int) $otherId, (string) $language)
        );

        // invalidate the cache for search
        static::invalidateCache();
    }

    /**
     * Edit an index
     *
     * @param string $module   The module wherein will be searched.
     * @param int    $otherId  The id of the record.
     * @param array  $fields   A key/value pair of fields to index.
     * @param string $language The frontend language for this entry.
     */
    public static function saveIndex($module, $otherId, array $fields, $language = null)
    {
        // module exists?
        if (!in_array('Search', BackendModel::getModules())) {
            return;
        }

        // no fields?
        if (empty($fields)) {
            return;
        }

        // set language
        if (!$language) {
            $language = BL::getWorkingLanguage();
        }

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
                array((string) $module, (int) $otherId, (string) $language, (string) $field, $value, 'Y', $value, 'Y')
            );
        }

        // invalidate the cache for search
        static::invalidateCache();
    }

    /**
     * Update a synonym
     *
     * @param array $item The data to update in the db.
     */
    public static function updateSynonym($item)
    {
        // update
        BackendModel::getContainer()->get('database')->update('search_synonyms', $item, 'id = ?', array($item['id']));

        // invalidate the cache for search
        static::invalidateCache();
    }
}
