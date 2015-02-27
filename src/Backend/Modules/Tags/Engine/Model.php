<?php

namespace Backend\Modules\Tags\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * In this file we store all generic functions that we will be using in the TagsModule
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT i.id, i.tag, i.number AS num_tags
         FROM tags AS i
         WHERE i.language = ?
         GROUP BY i.id';

    /**
     * Delete one or more tags.
     *
     * @param mixed $ids The ids to delete.
     */
    public static function delete($ids)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // make sure $ids is an array
        $ids = (array) $ids;

        // delete tags
        $db->delete('tags', 'id IN (' . implode(',', $ids) . ')');
        $db->delete('modules_tags', 'tag_id IN (' . implode(',', $ids) . ')');
    }

    /**
     * Check if a tag exists.
     *
     * @param int $id The id to check for existence.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM tags AS i
             WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Check if a tag exists
     *
     * @param string $tag The tag to check for existence.
     * @return bool
     */
    public static function existsTag($tag)
    {
        return (BackendModel::getContainer()->get('database')->getVar(
                    'SELECT i.tag FROM tags AS i  WHERE i.tag = ?',
                    array((string) $tag)
                ) != '');
    }

    /**
     * Get tag record.
     *
     * @param int $id The id of the record to get.
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.tag AS name
             FROM tags AS i
             WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Get tags that start with the given string
     *
     * @param string $term            The searchstring.
     * @param string $language        The language to use, if not provided
     *                                use the working language.
     * @return array
     */
    public static function getStartsWith($term, $language = null)
    {
        $language = ($language != null)
            ? (string) $language
            : BL::getWorkingLanguage();

        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.tag AS name, i.tag AS value
             FROM tags AS i
             WHERE i.language = ? AND i.tag LIKE ?
             ORDER BY i.tag ASC',
            array($language, (string) $term . '%')
        );
    }

    /**
     * Get tags for an item
     *
     * @param string $module   The module wherein will be searched.
     * @param int    $otherId  The id of the record.
     * @param string $type     The type of the returnvalue, possible values are: array, string (tags will be joined by ,).
     * @param string $language The language to use, if not provided the working language will be used.
     * @return mixed
     */
    public static function getTags($module, $otherId, $type = 'string', $language = null)
    {
        $module = (string) $module;
        $otherId = (int) $otherId;
        $type = (string) \SpoonFilter::getValue($type, array('string', 'array'), 'string');
        $language = ($language != null) ? (string) $language : BL::getWorkingLanguage();

        // fetch tags
        $tags = (array) BackendModel::getContainer()->get('database')->getColumn(
            'SELECT i.tag
             FROM tags AS i
             INNER JOIN modules_tags AS mt ON i.id = mt.tag_id
             WHERE mt.module = ? AND mt.other_id = ? AND i.language = ?
             ORDER BY i.tag ASC',
            array($module, $otherId, $language)
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
     * @param string $URL The URL to use as a base.
     * @param int    $id  The ID to ignore.
     * @return string
     */
    public static function getURL($URL, $id = null)
    {
        $URL = CommonUri::getUrl((string) $URL);
        $language = BL::getWorkingLanguage();

        // get db
        $db = BackendModel::getContainer()->get('database');

        // no specific id
        if ($id === null) {
            // get number of tags with the specified url
            $number = (int) $db->getVar(
                'SELECT 1
                 FROM tags AS i
                 WHERE i.url = ? AND i.language = ?
                 LIMIT 1',
                array($URL, $language)
            );

            // there are items so, call this method again.
            if ($number != 0) {
                // add a number
                $URL = BackendModel::addNumber($URL);

                // recall this method, but with a new url
                $URL = static::getURL($URL, $id);
            }
        } else {
            // specific id given
            // get number of tags with the specified url
            $number = (int) $db->getVar(
                'SELECT 1
                 FROM tags AS i
                 WHERE i.url = ? AND i.language = ? AND i.id != ?
                 LIMIT 1',
                array($URL, $language, $id)
            );

            // there are items so, call this method again.
            if ($number != 0) {
                // add a number
                $URL = BackendModel::addNumber($URL);

                // recall this method, but with a new url
                $URL = static::getURL($URL, $id);
            }
        }

        return $URL;
    }

    /**
     * Insert a new tag
     *
     * @param string $tag      The data for the tag.
     * @param string $language The language wherein the tag will be inserted,
     *                         if not provided the workinglanguage will be used.
     * @return int
     */
    public static function insert($tag, $language = null)
    {
        $tag = (string) $tag;
        $language = ($language != null) ? (string) $language : BL::getWorkingLanguage();

        // build record
        $item['language'] = $language;
        $item['tag'] = $tag;
        $item['number'] = 0;
        $item['url'] = static::getURL($tag);

        // insert and return id
        return (int) BackendModel::getContainer()->get('database')->insert('tags', $item);
    }

    /**
     * Save the tags
     *
     * @param int    $otherId  The id of the item to tag.
     * @param mixed  $tags     The tags for the item.
     * @param string $module   The module wherein the item is located.
     * @param string $language The language wherein the tags will be inserted,
     *                         if not provided the workinglanguage will be used.
     */
    public static function saveTags($otherId, $tags, $module, $language = null)
    {
        $otherId = (int) $otherId;
        $tags = (is_array($tags)) ? (array) $tags : (string) $tags;
        $module = (string) $module;
        $language = ($language != null) ? (string) $language : BL::getWorkingLanguage();

        // redefine the tags as an array
        if (!is_array($tags)) {
            $tags = (array) explode(',', $tags);
        }

        // make sure the list of tags is unique
        $tags = array_unique($tags);

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get current tags for item
        $currentTags = (array) $db->getPairs(
            'SELECT i.tag, i.id
             FROM tags AS i
             INNER JOIN modules_tags AS mt ON i.id = mt.tag_id
             WHERE mt.module = ? AND mt.other_id = ? AND i.language = ?',
            array($module, $otherId, $language)
        );

        // remove old links
        if (!empty($currentTags)) {
            $db->delete(
                'modules_tags',
                'tag_id IN (' . implode(', ', array_values($currentTags)) . ') AND other_id = ? AND module = ?',
                array($otherId, $module)
            );
        }

        if (!empty($tags)) {
            // loop tags
            foreach ($tags as $key => $tag) {
                // cleanup
                $tag = strtolower(trim($tag));

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
            $tagsAndIds = (array) $db->getPairs(
                'SELECT i.tag, i.id
                 FROM tags AS i
                 WHERE i.tag IN (' . implode(',', $placeholders) . ') AND i.language = ?',
                array_merge($tags, array($language))
            );

            // loop again and create tags that don't already exist
            foreach ($tags as $tag) {
                // doesn' exist yet
                if (!isset($tagsAndIds[$tag])) {
                    // insert tag
                    $tagsAndIds[$tag] = static::insert($tag, $language);
                }
            }

            // init items to insert
            $rowsToInsert = array();

            // loop again
            foreach ($tags as $tag) {
                // get tagId
                $tagId = (int) $tagsAndIds[$tag];

                // not linked before so increment the counter
                if (!isset($currentTags[$tag])) {
                    $db->execute(
                        'UPDATE tags SET number = number + 1 WHERE id = ?',
                        $tagId
                    );
                }

                // add to insert array
                $rowsToInsert[] = array('module' => $module, 'tag_id' => $tagId, 'other_id' => $otherId);
            }

            // insert the rows at once if there are items to insert
            if (!empty($rowsToInsert)) {
                $db->insert('modules_tags', $rowsToInsert);
            }
        }

        // add to search index
        BackendSearchModel::saveIndex($module, $otherId, array('tags' => implode(' ', (array) $tags)), $language);

        // decrement number
        foreach ($currentTags as $tag => $tagId) {
            // if the tag can't be found in the new tags we lower the number of tags by one
            if (array_search($tag, $tags) === false) {
                $db->execute(
                    'UPDATE tags SET number = number - 1 WHERE id = ?',
                    $tagId
                );
            }
        }

        // remove all tags that don't have anything linked
        $db->delete('tags', 'number = ?', 0);
    }

    /**
     * Update a tag
     * Remark: $tag['id'] should be available.
     *
     * @param array $item The new data for the tag.
     */
    public static function update($item)
    {
        return BackendModel::getContainer()->get('database')->update('tags', $item, 'id = ?', $item['id']);
    }
}
