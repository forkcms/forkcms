<?php

namespace Backend\Modules\Blog\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * In this file we store all generic functions that we will be using in the blog module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT i.hidden, i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.status = ? AND i.language = ?';

    const QRY_DATAGRID_BROWSE_FOR_CATEGORY =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.category_id = ? AND i.status = ? AND i.language = ?';

    const QRY_DATAGRID_BROWSE_CATEGORIES =
        'SELECT i.id, i.title, COUNT(p.id) AS num_items
         FROM blog_categories AS i
         LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND p.status = ? AND p.language = i.language
         WHERE i.language = ?
         GROUP BY i.id';

    const QRY_DATAGRID_BROWSE_COMMENTS =
        'SELECT
             i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.text,
             p.id AS post_id, p.title AS post_title, m.url AS post_url
         FROM blog_comments AS i
         INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
         INNER JOIN meta AS m ON p.meta_id = m.id
         WHERE i.status = ? AND i.language = ? AND p.status = ?
         GROUP BY i.id';

    const QRY_DATAGRID_BROWSE_DRAFTS =
        'SELECT i.id, i.user_id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.num_comments AS comments
         FROM blog_posts AS i
         INNER JOIN
         (
             SELECT MAX(i.revision_id) AS revision_id
             FROM blog_posts AS i
             WHERE i.status = ? AND i.user_id = ? AND i.language = ?
             GROUP BY i.id
         ) AS p
         WHERE i.revision_id = p.revision_id';

    const QRY_DATAGRID_BROWSE_DRAFTS_FOR_CATEGORY =
        'SELECT i.id, i.user_id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.num_comments AS comments
         FROM blog_posts AS i
         INNER JOIN
         (
             SELECT MAX(i.revision_id) AS revision_id
             FROM blog_posts AS i
             WHERE i.category_id = ? AND i.status = ? AND i.user_id = ? AND i.language = ?
             GROUP BY i.id
         ) AS p
         WHERE i.revision_id = p.revision_id';

    const QRY_DATAGRID_BROWSE_RECENT =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QRY_DATAGRID_BROWSE_RECENT_FOR_CATEGORY =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.category_id = ? AND i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QRY_DATAGRID_BROWSE_REVISIONS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM blog_posts AS i
         WHERE i.status = ? AND i.id = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    const QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM blog_posts AS i
         WHERE i.status = ? AND i.id = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings()
    {
        $warnings = array();

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Settings', 'Blog')) {
            // rss title
            if (BackendModel::getModuleSetting('Blog', 'rss_title_' . BL::getWorkingLanguage(), null) == '') {
                $warnings[] = array(
                    'message' => sprintf(
                        BL::err('RSSTitle', 'Blog'),
                        BackendModel::createURLForAction('Settings', 'Blog')
                    )
                );
            }

            // rss description
            if (BackendModel::getModuleSetting('Blog', 'rss_description_' . BL::getWorkingLanguage(), null) == '') {
                $warnings[] = array(
                    'message' => sprintf(
                        BL::err('RSSDescription', 'Blog'),
                        BackendModel::createURLForAction('Settings', 'Blog')
                    )
                );
            }
        }

        return $warnings;
    }

    /**
     * Deletes one or more items
     *
     * @param mixed $ids The ids to delete.
     */
    public static function delete($ids)
    {
        // make sure $ids is an array
        $ids = (array) $ids;

        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($ids), '?');

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get used meta ids
        $metaIds = (array) $db->getColumn(
            'SELECT meta_id
             FROM blog_posts AS p
             WHERE id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?',
            array_merge($ids, array(BL::getWorkingLanguage()))
        );

        // delete meta
        if (!empty($metaIds)) {
            $db->delete('meta', 'id IN (' . implode(',', $metaIds) . ')');
        }

        // delete records
        $db->delete(
            'blog_posts',
            'id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?',
            array_merge($ids, array(BL::getWorkingLanguage()))
        );
        $db->delete(
            'blog_comments',
            'post_id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?',
            array_merge($ids, array(BL::getWorkingLanguage()))
        );

        // delete tags
        foreach ($ids as $id) {
            BackendTagsModel::saveTags($id, '', 'Blog');
        }

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());
    }

    /**
     * Deletes a category
     *
     * @param int $id The id of the category to delete.
     */
    public static function deleteCategory($id)
    {
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // get item
        $item = self::getCategory($id);

        if (!empty($item)) {
            // delete meta
            $db->delete('meta', 'id = ?', array($item['meta_id']));

            // delete category
            $db->delete('blog_categories', 'id = ?', array($id));

            // update category for the posts that might be in this category
            $db->update('blog_posts', array('category_id' => null), 'category_id = ?', array($id));

            // invalidate the cache for blog
            BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());
        }
    }

    /**
     * Checks if it is allowed to delete the a category
     *
     * @param int $id The id of the category.
     * @return bool
     */
    public static function deleteCategoryAllowed($id)
    {
        return !(bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_posts AS i
             WHERE i.category_id = ? AND i.language = ? AND i.status = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage(), 'active')
        );
    }

    /**
     * Deletes one or more comments
     *
     * @param array $ids The id(s) of the items(s) to delete.
     */
    public static function deleteComments($ids)
    {
        // make sure $ids is an array
        $ids = (array) $ids;

        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($ids), '?');

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get ids
        $itemIds = (array) $db->getColumn(
            'SELECT i.post_id
             FROM blog_comments AS i
             WHERE i.id IN (' . implode(', ', $idPlaceHolders) . ')',
            $ids
        );

        // update record
        $db->delete('blog_comments', 'id IN (' . implode(', ', $idPlaceHolders) . ')', $ids);

        // recalculate the comment count
        if (!empty($itemIds)) {
            self::reCalculateCommentCount($itemIds);
        }

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());
    }

    /**
     * Delete all spam
     */
    public static function deleteSpamComments()
    {
        $db = BackendModel::getContainer()->get('database');

        // get ids
        $itemIds = (array) $db->getColumn(
            'SELECT i.post_id
             FROM blog_comments AS i
             WHERE status = ? AND i.language = ?',
            array('spam', BL::getWorkingLanguage())
        );

        // update record
        $db->delete('blog_comments', 'status = ? AND language = ?', array('spam', BL::getWorkingLanguage()));

        // recalculate the comment count
        if (!empty($itemIds)) {
            self::reCalculateCommentCount($itemIds);
        }

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());
    }

    /**
     * Checks if an item exists
     *
     * @param int $id The id of the item to check for existence.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM blog_posts AS i
             WHERE i.id = ? AND i.language = ?',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Checks if a category exists
     *
     * @param int $id The id of the category to check for existence.
     * @return int
     */
    public static function existsCategory($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_categories AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Checks if a comment exists
     *
     * @param int $id The id of the item to check for existence.
     * @return int
     */
    public static function existsComment($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_comments AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The Id of the item to fetch?
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND (i.status = ? OR i.status = ?) AND i.language = ?
             ORDER BY i.revision_id DESC',
            array((int) $id, 'active', 'draft', BL::getWorkingLanguage())
        );
    }

    /**
     * Get the comments
     *
     * @param string $status The type of comments to get.
     * @param int    $limit  The maximum number of items to retrieve.
     * @param int    $offset The offset.
     * @return array
     */
    public static function getAllCommentsForStatus($status, $limit = 30, $offset = 0)
    {
        if ($status !== null) {
            $status = (string) $status;
        }
        $limit = (int) $limit;
        $offset = (int) $offset;

        // no status passed
        if ($status === null) {
            return (array) BackendModel::getContainer()->get('database')->getRecords(
                'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
                 p.id AS post_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
                 FROM blog_comments AS i
                 INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
                 INNER JOIN meta AS m ON p.meta_id = m.id
                 WHERE i.language = ?
                 GROUP BY i.id
                 LIMIT ?, ?',
                array(BL::getWorkingLanguage(), $offset, $limit)
            );
        }

        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
             p.id AS post_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.status = ? AND i.language = ?
             GROUP BY i.id
             LIMIT ?, ?',
            array($status, BL::getWorkingLanguage(), $offset, $limit)
        );
    }

    /**
     * Get all items by a given tag id
     *
     * @param int $tagId The id of the tag.
     * @return array
     */
    public static function getByTag($tagId)
    {
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.title AS name, mt.module
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             INNER JOIN blog_posts AS i ON mt.other_id = i.id
             WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ? AND i.language = ?',
            array('Blog', (int) $tagId, 'active', BL::getWorkingLanguage())
        );

        // overwrite the url
        foreach ($items as &$row) {
            $row['url'] = BackendModel::createURLForAction('Edit', 'Blog', null, array('id' => $row['url']));
        }

        return $items;
    }

    /**
     * Get all categories
     *
     * @param bool $includeCount Include the count?
     * @return array
     */
    public static function getCategories($includeCount = false)
    {
        $db = BackendModel::getContainer()->get('database');

        if ($includeCount) {
            return (array) $db->getPairs(
                'SELECT i.id, CONCAT(i.title, " (", COUNT(p.category_id) ,")") AS title
                 FROM blog_categories AS i
                 LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND i.language = p.language AND p.status = ?
                 WHERE i.language = ?
                 GROUP BY i.id',
                array('active', BL::getWorkingLanguage())
            );
        }

        return (array) $db->getPairs(
            'SELECT i.id, i.title
             FROM blog_categories AS i
             WHERE i.language = ?',
            array(BL::getWorkingLanguage())
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The id of the category to fetch.
     * @return array
     */
    public static function getCategory($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM blog_categories AS i
             WHERE i.id = ? AND i.language = ?',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Get a category id by title
     *
     * @param string $title    The title of the category.
     * @param string $language The language to use, if not provided we will use the working language.
     * @return int
     */
    public static function getCategoryId($title, $language = null)
    {
        $title = (string) $title;
        $language = ($language !== null) ? (string) $language : BL::getWorkingLanguage();

        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM blog_categories AS i
             WHERE i.title = ? AND i.language = ?',
            array($title, $language)
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The Id of the comment to fetch?
     * @return array
     */
    public static function getComment($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on,
             p.id AS post_id, p.title AS post_title, m.url AS post_url
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.id = ? AND p.status = ?
             LIMIT 1',
            array((int) $id, 'active')
        );
    }

    /**
     * Get multiple comments at once
     *
     * @param array $ids The id(s) of the comment(s).
     * @return array
     */
    public static function getComments(array $ids)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT *
             FROM blog_comments AS i
             WHERE i.id IN (' . implode(', ', array_fill(0, count($ids), '?')) . ')',
            $ids
        );
    }

    /**
     * Get a count per comment
     *
     * @return array
     */
    public static function getCommentStatusCount()
    {
        return (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.status, COUNT(i.id)
             FROM blog_comments AS i
             WHERE i.language = ?
             GROUP BY i.status',
            array(BL::getWorkingLanguage())
        );
    }

    /**
     * Get the latest comments for a given type
     *
     * @param string $status The status for the comments to retrieve.
     * @param int    $limit  The maximum number of items to retrieve.
     * @return array
     */
    public static function getLatestComments($status, $limit = 10)
    {
        // get the comments (order by id, this is faster then on date, the higher the id, the more recent
        $comments = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.author, i.text, UNIX_TIMESTAMP(i.created_on) AS created_in,
             p.title, p.language, m.url
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.status = ? AND p.status = ? AND i.language = ?
             ORDER BY i.created_on DESC
             LIMIT ?',
            array((string) $status, 'active', BL::getWorkingLanguage(), (int) $limit)
        );

        // overwrite url
        $baseUrl = BackendModel::getURLForBlock('Blog', 'detail');

        foreach ($comments as &$row) {
            $row['full_url'] = $baseUrl . '/' . $row['url'];
        }

        return $comments;
    }

    /**
     * Get the maximum id
     *
     * @return int
     */
    public static function getMaximumId()
    {
        return (int) BackendModel::getContainer()->get('database')->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1');
    }

    /**
     * Get all data for a given revision
     *
     * @param int $id         The id of the item.
     * @param int $revisionId The revision to get.
     * @return array
     */
    public static function getRevision($id, $revisionId)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND i.revision_id = ?',
            array((int) $id, (int) $revisionId)
        );
    }

    /**
     * Retrieve the unique URL for an item
     *
     * @param string $URL The URL to base on.
     * @param int    $id  The id of the item to ignore.
     * @return string
     */
    public static function getURL($URL, $id = null)
    {
        $URL = (string) $URL;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            // already exists
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM blog_posts AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $URL)
            )
            ) {
                $URL = BackendModel::addNumber($URL);

                return self::getURL($URL);
            }
        } else {
            // current category should be excluded
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM blog_posts AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $URL, $id)
            )
            ) {

                $URL = BackendModel::addNumber($URL);

                return self::getURL($URL, $id);
            }
        }

        return $URL;
    }

    /**
     * Retrieve the unique URL for a category
     *
     * @param string $URL The string whereon the URL will be based.
     * @param int    $id  The id of the category to ignore.
     * @return string
     */
    public static function getURLForCategory($URL, $id = null)
    {
        // redefine URL
        $URL = (string) $URL;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // new category
        if ($id === null) {
            // already exists
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM blog_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $URL)
            )
            ) {
                $URL = BackendModel::addNumber($URL);

                return self::getURLForCategory($URL);
            }
        } else {
            // current category should be excluded
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM blog_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $URL, $id)
            )
            ) {
                $URL = BackendModel::addNumber($URL);

                return self::getURLForCategory($URL, $id);
            }
        }

        return $URL;
    }

    /**
     * Inserts an item into the database
     *
     * @param array $item The data to insert.
     * @return int
     */
    public static function insert(array $item)
    {
        // insert and return the new revision id
        $item['revision_id'] = BackendModel::getContainer()->get('database')->insert('blog_posts', $item);

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());

        // return the new revision id
        return $item['revision_id'];
    }

    /**
     * Inserts a complete post item based on some arrays of data
     *
     * This method's purpose is to be able to insert a post (possibly with all its metadata, tags, and comments)
     * in one method call. As much data as possible has been made optional, to be able to do imports where only
     * fractions of the data we need are known.
     *
     * The item array should have at least a 'title' and a 'text' property other properties are optional.
     * The meta array has only optional properties. You can use these to override the defaults.
     * The tags array is just a list of tagnames as string.
     * The comments array is an array of arrays with comment properties. A comment should have
     * at least 'author', 'email', and 'text' properties.
     *
     * @param array $item     The data to insert.
     * @param array $meta     The metadata to insert.
     * @param array $tags     The tags to connect to this post.
     * @param array $comments The comments attached to this post.
     * @return int
     */
    public static function insertCompletePost($item, $meta = array(), $tags = array(), $comments = array())
    {
        // Build item
        if (!isset($item['id'])) {
            $item['id'] = (int) self::getMaximumId() + 1;
        }
        if (!isset($item['user_id'])) {
            $item['user_id'] = BackendAuthentication::getUser()->getUserId();
        }
        if (!isset($item['hidden'])) {
            $item['hidden'] = 'N';
        }
        if (!isset($item['allow_comments'])) {
            $item['allow_comments'] = 'Y';
        }
        if (!isset($item['num_comments'])) {
            $item['num_comments'] = 0;
        }
        if (!isset($item['status'])) {
            $item['status'] = 'active';
        }
        if (!isset($item['language'])) {
            $item['language'] = BL::getWorkingLanguage();
        }
        if (!isset($item['publish_on'])) {
            $item['publish_on'] = BackendModel::getUTCDate();
        }
        if (!isset($item['created_on'])) {
            $item['created_on'] = BackendModel::getUTCDate();
        }
        if (!isset($item['edited_on'])) {
            $item['edited_on'] = BackendModel::getUTCDate();
        }
        if (!isset($item['category_id'])) {
            $item['category_id'] = 1;
        }
        if (!isset($item['title']) || !isset($item['text'])) {
            throw new Exception('$item should at least have a title and a text property');
        }

        // Set drafts hidden
        if (strtotime((string) $item['publish_on']) > time()) {
            $item['hidden'] = 'Y';
            $item['status'] = 'draft';
        }

        // Build meta
        if (!is_array($meta)) {
            $meta = array();
        }
        if (!isset($meta['keywords'])) {
            $meta['keywords'] = $item['title'];
        }
        if (!isset($meta['keywords_overwrite'])) {
            $meta['keywords_overwrite'] = 'N';
        }
        if (!isset($meta['description'])) {
            $meta['description'] = $item['title'];
        }
        if (!isset($meta['description_overwrite'])) {
            $meta['description_overwrite'] = 'N';
        }
        if (!isset($meta['title'])) {
            $meta['title'] = $item['title'];
        }
        if (!isset($meta['title_overwrite'])) {
            $meta['title_overwrite'] = 'N';
        }
        if (!isset($meta['url'])) {
            $meta['url'] = self::getURL($item['title']);
        }
        if (!isset($meta['url_overwrite'])) {
            $meta['url_overwrite'] = 'N';
        }
        if (!isset($meta['data'])) {
            $meta['data'] = serialize(array('seo_index' => 'index', 'seo_follow' => 'follow'));
        }

        // Write meta to db
        $item['meta_id'] = BackendModel::getContainer()->get('database')->insert('meta', $meta);

        // Write post to db
        $item['revision_id'] = self::insert($item);

        // Any tags?
        if (!empty($tags)) {
            BackendTagsModel::saveTags($item['id'], implode(',', $tags), 'blog');
        }

        // Any comments?
        foreach ($comments as $comment) {
            // We require some fields (author, email, text)
            if (!isset($comment['author']) || !isset($comment['email']) || !isset($comment['text'])) {
                continue;
            }

            // Set some defaults
            if (!isset($comment['language'])) {
                $comment['language'] = BL::getWorkingLanguage();
            }
            if (!isset($comment['created_on'])) {
                $comment['created_on'] = BackendModel::getUTCDate();
            }
            if (!isset($comment['status'])) {
                $comment['status'] = 'published';
            }
            if (!isset($comment['data'])) {
                $comment['data'] = serialize(array('server' => $_SERVER));
            }
            if (!isset($comment['website'])) {
                $comment['website'] = '';
            }

            $comment['post_id'] = $item['id'];
            $comment['data'] = serialize(array('server' => $_SERVER));

            // Insert the comment
            self::insertComment($comment);
        }

        // Return
        return $item['revision_id'];
    }

    /**
     * Inserts a new category into the database
     *
     * @param array $item The data for the category to insert.
     * @param array $meta The metadata for the category to insert.
     * @return int
     */
    public static function insertCategory(array $item, $meta = null)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // meta given?
        if ($meta !== null) {
            $item['meta_id'] = $db->insert('meta', $meta);
        }

        // create category
        $item['id'] = $db->insert('blog_categories', $item);

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());

        // return the id
        return $item['id'];
    }

    /**
     * Inserts a new comment (Taken from FrontendBlogModel)
     *
     * @param array $comment The comment to add.
     * @return int
     */
    public static function insertComment(array $comment)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // insert comment
        $comment['id'] = (int) $db->insert('blog_comments', $comment);

        // recalculate if published
        if ($comment['status'] == 'published') {
            // num comments
            $numComments = (int) BackendModel::getContainer()->get('database')->getVar(
                'SELECT COUNT(i.id) AS comment_count
                 FROM blog_comments AS i
                 INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
                 WHERE i.status = ? AND i.post_id = ? AND i.language = ? AND p.status = ?
                 GROUP BY i.post_id',
                array('published', $comment['post_id'], BL::getWorkingLanguage(), 'active')
            );

            // update num comments
            $db->update('blog_posts', array('num_comments' => $numComments), 'id = ?', $comment['post_id']);
        }

        return $comment['id'];
    }

    /**
     * Recalculate the commentcount
     *
     * @param array $ids The id(s) of the post wherefore the commentcount should be recalculated.
     * @return bool
     */
    public static function reCalculateCommentCount(array $ids)
    {
        // validate
        if (empty($ids)) {
            return false;
        }

        // make unique ids
        $ids = array_unique($ids);

        // get db
        $db = BackendModel::getContainer()->get('database');

        // get counts
        $commentCounts = (array) $db->getPairs(
            'SELECT i.post_id, COUNT(i.id) AS comment_count
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             WHERE i.status = ? AND i.post_id IN (' . implode(',', $ids) . ') AND i.language = ? AND p.status = ?
             GROUP BY i.post_id',
            array('published', BL::getWorkingLanguage(), 'active')
        );

        foreach ($ids as $id) {
            // get count
            $count = (isset($commentCounts[$id])) ? (int) $commentCounts[$id] : 0;

            // update
            $db->update(
                'blog_posts',
                array('num_comments' => $count),
                'id = ? AND language = ?',
                array($id, BL::getWorkingLanguage())
            );
        }

        return true;
    }

    /**
     * Update an existing item
     *
     * @param array $item The new data.
     * @return int
     */
    public static function update(array $item)
    {
        // check if new version is active
        if ($item['status'] == 'active') {
            // archive all older active versions
            BackendModel::getContainer()->get('database')->update(
                'blog_posts',
                array('status' => 'archived'),
                'id = ? AND status = ?',
                array($item['id'], $item['status'])
            );

            // get the record of the exact item we're editing
            $revision = self::getRevision($item['id'], $item['revision_id']);

            // assign values
            $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $revision['created_on']);
            $item['num_comments'] = $revision['num_comments'];

            // if it used to be a draft that we're now publishing, remove drafts
            if ($revision['status'] == 'draft') {
                BackendModel::getContainer()->get('database')->delete(
                    'blog_posts',
                    'id = ? AND status = ?',
                    array($item['id'], $revision['status'])
                );
            }
        }

        // don't want revision id
        unset($item['revision_id']);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::getModuleSetting('Blog', 'max_num_revisions', 20);

        // set type of archive
        $archiveType = ($item['status'] == 'active' ? 'archived' : $item['status']);

        // get revision-ids for items to keep
        $revisionIdsToKeep = (array) BackendModel::getContainer()->get('database')->getColumn(
            'SELECT i.revision_id
             FROM blog_posts AS i
             WHERE i.id = ? AND i.status = ? AND i.language = ?
             ORDER BY i.edited_on DESC
             LIMIT ?',
            array($item['id'], $archiveType, BL::getWorkingLanguage(), $rowsToKeep)
        );

        // delete other revisions
        if (!empty($revisionIdsToKeep)) {
            BackendModel::getContainer()->get('database')->delete(
                'blog_posts',
                'id = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')',
                array($item['id'], $archiveType)
            );
        }

        // insert new version
        $item['revision_id'] = BackendModel::getContainer()->get('database')->insert('blog_posts', $item);

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());

        // return the new revision id
        return $item['revision_id'];
    }

    /**
     * Update an existing category
     *
     * @param array       $item The new data.
     * @param array $meta The new meta-data.
     * @return int
     */
    public static function updateCategory(array $item, $meta = null)
    {
        // get db
        $db = BackendModel::getContainer()->get('database');

        // update category
        $updated = $db->update('blog_categories', $item, 'id = ?', array((int) $item['id']));

        // meta passed?
        if ($meta !== null) {
            // get current category
            $category = self::getCategory($item['id']);

            // update the meta
            $db->update('meta', $meta, 'id = ?', array((int) $category['meta_id']));
        }

        // invalidate the cache for blog
        BackendModel::invalidateFrontendCache('Blog', BL::getWorkingLanguage());

        return $updated;
    }

    /**
     * Update an existing comment
     *
     * @param array $item The new data.
     * @return int
     */
    public static function updateComment(array $item)
    {
        // update category
        return BackendModel::getContainer()->get('database')->update(
            'blog_comments',
            $item,
            'id = ?',
            array((int) $item['id'])
        );
    }

    /**
     * Updates one or more comments' status
     *
     * @param array  $ids    The id(s) of the comment(s) to change the status for.
     * @param string $status The new status.
     */
    public static function updateCommentStatuses($ids, $status)
    {
        // make sure $ids is an array
        $ids = (array) $ids;

        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($ids), '?');

        // get the items and their languages
        $items = (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.post_id, i.language
             FROM blog_comments AS i
             WHERE i.id IN (' . implode(', ', $idPlaceHolders) . ')',
            $ids,
            'post_id'
        );

        // only proceed if there are items
        if (!empty($items)) {
            // get the ids
            $itemIds = array_keys($items);

            // get the unique languages
            $languages = array_unique(array_values($items));

            // update records
            BackendModel::getContainer()->get('database')->execute(
                'UPDATE blog_comments
                 SET status = ?
                 WHERE id IN (' . implode(', ', $idPlaceHolders) . ')',
                array_merge(array((string) $status), $ids)
            );

            // recalculate the comment count
            self::reCalculateCommentCount($itemIds);

            // invalidate the cache for blog
            foreach ($languages as $language) {
                BackendModel::invalidateFrontendCache('Blog', $language);
            }
        }
    }
}
