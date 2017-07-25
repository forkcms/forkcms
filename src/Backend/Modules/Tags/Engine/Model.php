<?php

namespace Backend\Modules\Tags\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * In this file we store all generic functions that we will be using in the TagsModule
 */
class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT i.id, i.tag, i.number AS num_tags
         FROM tags AS i
         WHERE i.language = ?
         GROUP BY i.id';

    /**
     * Delete one or more tags.
     *
     * @param int|int[] $ids The ids to delete.
     */
    public static function delete($ids): void
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // make sure $ids is an array
        $ids = (array) $ids;

        // delete tags
        $database->delete('tags', 'id IN (' . implode(',', $ids) . ')');
        $database->delete('modules_tags', 'tag_id IN (' . implode(',', $ids) . ')');
    }

    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM tags AS i
             WHERE i.id = ?',
            [$id]
        );
    }

    public static function existsTag(string $tag): bool
    {
        return (BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.tag FROM tags AS i  WHERE i.tag = ?',
            [$tag]
        ) != '');
    }

    public static function get(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.tag AS name
             FROM tags AS i
             WHERE i.id = ?',
            [$id]
        );
    }

    public static function getAll(string $language = null): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.tag AS name
             FROM tags AS i
             WHERE i.language = ?',
            [$language ?? BL::getWorkingLanguage()]
        );
    }

    /**
     * Get tags that start with the given string
     *
     * @param string $term The searchstring.
     * @param string $language The language to use, if not provided use the working language.
     *
     * @return array
     */
    public static function getStartsWith(string $term, string $language = null): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.tag AS name, i.tag AS value
             FROM tags AS i
             WHERE i.language = ? AND i.tag LIKE ?
             ORDER BY i.tag ASC',
            [$language ?? BL::getWorkingLanguage(), $term . '%']
        );
    }

    /**
     * Get tags for an item
     *
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param string $type The type of the returnvalue, possible values are: array, string (tags will be joined by ,).
     * @param string $language The language to use, if not provided the working language will be used.
     *
     * @return mixed
     */
    public static function getTags(string $module, int $otherId, string $type = 'string', string $language = null)
    {
        $type = (string) \SpoonFilter::getValue($type, ['string', 'array'], 'string');

        // fetch tags
        $tags = (array) BackendModel::getContainer()->get('database')->getColumn(
            'SELECT i.tag
             FROM tags AS i
             INNER JOIN modules_tags AS mt ON i.id = mt.tag_id
             WHERE mt.module = ? AND mt.other_id = ? AND i.language = ?
             ORDER BY i.tag ASC',
            [$module, $otherId, $language ?? BL::getWorkingLanguage()]
        );

        // return as an imploded string
        if ($type == 'string') {
            return implode(',', $tags);
        }

        // return as array
        return $tags;
    }

    /**
     * Get a unique URL for a tag
     *
     * @param string $url The URL to use as a base.
     * @param int|null $id The ID to ignore.
     *
     * @return string
     */
    public static function getUrl(string $url, int $id = null): string
    {
        $url = CommonUri::getUrl($url);
        $language = BL::getWorkingLanguage();

        // get database
        $database = BackendModel::getContainer()->get('database');

        // no specific id
        if ($id === null) {
            // get number of tags with the specified url
            $number = (int) $database->getVar(
                'SELECT 1
                 FROM tags AS i
                 WHERE i.url = ? AND i.language = ?
                 LIMIT 1',
                [$url, $language]
            );

            // there are items so, call this method again.
            if ($number != 0) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new url
                $url = self::getUrl($url, $id);
            }
        } else {
            // specific id given
            // get number of tags with the specified url
            $number = (int) $database->getVar(
                'SELECT 1
                 FROM tags AS i
                 WHERE i.url = ? AND i.language = ? AND i.id != ?
                 LIMIT 1',
                [$url, $language, $id]
            );

            // there are items so, call this method again.
            if ($number != 0) {
                // add a number
                $url = BackendModel::addNumber($url);

                // recall this method, but with a new url
                $url = self::getUrl($url, $id);
            }
        }

        return $url;
    }

    /**
     * Insert a new tag
     *
     * @param string $tag The data for the tag.
     * @param string $language The language wherein the tag will be inserted,
     *                         if not provided the workinglanguage will be used.
     *
     * @return int
     */
    public static function insert(string $tag, string $language = null): int
    {
        return (int) BackendModel::getContainer()->get('database')->insert(
            'tags',
            [
                'language' => $language ?? BL::getWorkingLanguage(),
                'tag' => $tag,
                'number' => 0,
                'url' => self::getUrl($tag),
            ]
        );
    }

    /**
     * Save the tags
     *
     * @param int $otherId The id of the item to tag.
     * @param mixed $tags The tags for the item.
     * @param string $module The module wherein the item is located.
     * @param string|null $language The language wherein the tags will be inserted,
     *                         if not provided the workinglanguage will be used.
     */
    public static function saveTags(int $otherId, $tags, string $module, string $language = null)
    {
        $language = $language ?? BL::getWorkingLanguage();

        // redefine the tags as an array
        if (!is_array($tags)) {
            $tags = (array) explode(',', (string) $tags);
        }

        // make sure the list of tags contains only unique and non-empty elements
        $tags = array_filter(array_unique($tags));

        // get database
        $database = BackendModel::getContainer()->get('database');

        // get current tags for item
        $currentTags = (array) $database->getPairs(
            'SELECT i.tag, i.id
             FROM tags AS i
             INNER JOIN modules_tags AS mt ON i.id = mt.tag_id
             WHERE mt.module = ? AND mt.other_id = ? AND i.language = ?',
            [$module, $otherId, $language]
        );

        // remove old links
        if (!empty($currentTags)) {
            $database->delete(
                'modules_tags',
                'tag_id IN (' . implode(', ', array_values($currentTags)) . ') AND other_id = ? AND module = ?',
                [$otherId, $module]
            );
        }

        if (!empty($tags)) {
            // loop tags
            foreach ($tags as $key => $tag) {
                // cleanup
                $tag = mb_strtolower(trim($tag));

                // unset if the tag is empty
                if ($tag == '') {
                    unset($tags[$key]);
                } else {
                    $tags[$key] = $tag;
                }
            }

            // don't do a regular implode, mysql injection might be possible
            $placeholders = array_fill(0, count($tags), '?');

            // get tag ids
            $tagsAndIds = (array) $database->getPairs(
                'SELECT i.tag, i.id
                 FROM tags AS i
                 WHERE i.tag IN (' . implode(',', $placeholders) . ') AND i.language = ?',
                array_merge($tags, [$language])
            );

            // loop again and create tags that don't already exist
            foreach ($tags as $tag) {
                // doesn' exist yet
                if (!isset($tagsAndIds[$tag])) {
                    // insert tag
                    $tagsAndIds[$tag] = self::insert($tag, $language);
                }
            }

            // init items to insert
            $rowsToInsert = [];

            // loop again
            foreach ($tags as $tag) {
                // get tagId
                $tagId = (int) $tagsAndIds[$tag];

                // not linked before so increment the counter
                if (!isset($currentTags[$tag])) {
                    $database->execute(
                        'UPDATE tags SET number = number + 1 WHERE id = ?',
                        $tagId
                    );
                }

                // add to insert array
                $rowsToInsert[] = ['module' => $module, 'tag_id' => $tagId, 'other_id' => $otherId];
            }

            // insert the rows at once if there are items to insert
            if (!empty($rowsToInsert)) {
                $database->insert('modules_tags', $rowsToInsert);
            }
        }

        // add to search index
        BackendSearchModel::saveIndex($module, $otherId, ['tags' => implode(' ', (array) $tags)], $language);

        // decrement number
        foreach ($currentTags as $tag => $tagId) {
            // if the tag can't be found in the new tags we lower the number of tags by one
            if (array_search($tag, $tags) === false) {
                $database->execute(
                    'UPDATE tags SET number = number - 1 WHERE id = ?',
                    $tagId
                );
            }
        }

        // remove all tags that don't have anything linked
        $database->delete('tags', 'number = ?', 0);
    }

    /**
     * Update a tag
     * Remark: $tag['id'] should be available.
     *
     * @param array $tag The new data for the tag.
     *
     * @return int
     */
    public static function update(array $tag): int
    {
        return BackendModel::getContainer()->get('database')->update('tags', $tag, 'id = ?', $tag['id']);
    }
}
