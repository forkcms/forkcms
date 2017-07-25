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
use Backend\Core\Language\Language as BL;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * In this file we store all generic functions that we will be using in the blog module
 */
class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT i.hidden, i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.status = ? AND i.language = ?';

    const QUERY_DATAGRID_BROWSE_FOR_CATEGORY =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.category_id = ? AND i.status = ? AND i.language = ?';

    const QUERY_DATAGRID_BROWSE_CATEGORIES =
        'SELECT i.id, i.title, COUNT(p.id) AS num_items
         FROM blog_categories AS i
         LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND p.status = ? AND p.language = i.language
         WHERE i.language = ?
         GROUP BY i.id';

    const QUERY_DATAGRID_BROWSE_COMMENTS =
        'SELECT
             i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.text,
             p.id AS post_id, p.title AS post_title, m.url AS post_url
         FROM blog_comments AS i
         INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
         INNER JOIN meta AS m ON p.meta_id = m.id
         WHERE i.status = ? AND i.language = ? AND p.status = ?
         GROUP BY i.id';

    const QUERY_DATAGRID_BROWSE_DRAFTS =
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

    const QUERY_DATAGRID_BROWSE_DRAFTS_FOR_CATEGORY =
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

    const QUERY_DATAGRID_BROWSE_RECENT =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QUERY_DATAGRID_BROWSE_RECENT_FOR_CATEGORY =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id, i.num_comments AS comments
         FROM blog_posts AS i
         WHERE i.category_id = ? AND i.status = ? AND i.language = ?
         ORDER BY i.edited_on DESC
         LIMIT ?';

    const QUERY_DATAGRID_BROWSE_REVISIONS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM blog_posts AS i
         WHERE i.status = ? AND i.id = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    const QUERY_DATAGRID_BROWSE_SPECIFIC_DRAFTS =
        'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
         FROM blog_posts AS i
         WHERE i.status = ? AND i.id = ? AND i.language = ?
         ORDER BY i.edited_on DESC';

    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings(): array
    {
        $warnings = [];

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Settings', 'Blog')) {
            // rss title
            if (BackendModel::get('fork.settings')->get('Blog', 'rss_title_' . BL::getWorkingLanguage(), null) == '') {
                $warnings[] = [
                    'message' => sprintf(
                        BL::err('RSSTitle', 'Blog'),
                        BackendModel::createUrlForAction('Settings', 'Blog')
                    ),
                ];
            }

            // rss description
            if (BackendModel::get('fork.settings')->get('Blog', 'rss_description_' . BL::getWorkingLanguage(), null) == '') {
                $warnings[] = [
                    'message' => sprintf(
                        BL::err('RSSDescription', 'Blog'),
                        BackendModel::createUrlForAction('Settings', 'Blog')
                    ),
                ];
            }
        }

        return $warnings;
    }

    /**
     * Deletes one or more items
     *
     * @param mixed $ids The ids to delete.
     */
    public static function delete($ids): void
    {
        // make sure $ids is an array
        $ids = (array) $ids;

        // make sure we have elements
        if (empty($ids)) {
            return;
        }

        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an string with an equal amount of questionmarks as ids provided
        $idPlaceHolders = implode(', ', array_fill(0, count($ids), '?'));

        // get database
        $database = BackendModel::getContainer()->get('database');

        // get used meta ids
        $metaIds = (array) $database->getColumn(
            'SELECT meta_id
             FROM blog_posts AS p
             WHERE id IN (' . $idPlaceHolders . ') AND language = ?',
            array_merge($ids, [BL::getWorkingLanguage()])
        );

        // delete meta
        if (!empty($metaIds)) {
            $database->delete('meta', 'id IN (' . implode(',', $metaIds) . ')');
        }

        // delete image files
        $images = $database->getColumn('SELECT image FROM blog_posts WHERE id IN (' . $idPlaceHolders . ')', $ids);

        foreach ($images as $image) {
            BackendModel::deleteThumbnails(FRONTEND_FILES_PATH . '/Blog/images', $image);
        }

        // delete records
        $database->delete(
            'blog_posts',
            'id IN (' . $idPlaceHolders . ') AND language = ?',
            array_merge($ids, [BL::getWorkingLanguage()])
        );
        $database->delete(
            'blog_comments',
            'post_id IN (' . $idPlaceHolders . ') AND language = ?',
            array_merge($ids, [BL::getWorkingLanguage()])
        );

        // delete tags
        foreach ($ids as $id) {
            BackendTagsModel::saveTags($id, '', 'Blog');
        }
    }

    /**
     * Deletes a category
     *
     * @param int $id The id of the category to delete.
     */
    public static function deleteCategory(int $id): void
    {
        $id = (int) $id;
        $database = BackendModel::getContainer()->get('database');

        // get item
        $item = self::getCategory($id);

        if (!empty($item)) {
            // delete meta
            $database->delete('meta', 'id = ?', [$item['meta_id']]);

            // delete category
            $database->delete('blog_categories', 'id = ?', [$id]);

            // update category for the posts that might be in this category
            $database->update('blog_posts', ['category_id' => null], 'category_id = ?', [$id]);
        }
    }

    /**
     * Checks if it is allowed to delete the a category
     *
     * @param int $id The id of the category.
     *
     * @return bool
     */
    public static function deleteCategoryAllowed(int $id): bool
    {
        return !(bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_posts AS i
             WHERE i.category_id = ? AND i.language = ? AND i.status = ?
             LIMIT 1',
            [(int) $id, BL::getWorkingLanguage(), 'active']
        );
    }

    /**
     * Deletes one or more comments
     *
     * @param array $ids The id(s) of the items(s) to delete.
     */
    public static function deleteComments(array $ids): void
    {
        // make sure $ids is an array
        $ids = (array) $ids;

        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($ids), '?');

        // get database
        $database = BackendModel::getContainer()->get('database');

        // get ids
        $itemIds = (array) $database->getColumn(
            'SELECT i.post_id
             FROM blog_comments AS i
             WHERE i.id IN (' . implode(', ', $idPlaceHolders) . ')',
            $ids
        );

        // update record
        $database->delete('blog_comments', 'id IN (' . implode(', ', $idPlaceHolders) . ')', $ids);

        // recalculate the comment count
        if (!empty($itemIds)) {
            self::reCalculateCommentCount($itemIds);
        }
    }

    public static function deleteSpamComments(): void
    {
        $database = BackendModel::getContainer()->get('database');

        // get ids
        $itemIds = (array) $database->getColumn(
            'SELECT i.post_id
             FROM blog_comments AS i
             WHERE status = ? AND i.language = ?',
            ['spam', BL::getWorkingLanguage()]
        );

        // update record
        $database->delete('blog_comments', 'status = ? AND language = ?', ['spam', BL::getWorkingLanguage()]);

        // recalculate the comment count
        if (!empty($itemIds)) {
            self::reCalculateCommentCount($itemIds);
        }
    }

    /**
     * Checks if an item exists
     *
     * @param int $id The id of the item to check for existence.
     *
     * @return bool
     */
    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM blog_posts AS i
             WHERE i.id = ? AND i.language = ?',
            [(int) $id, BL::getWorkingLanguage()]
        );
    }

    public static function existsCategory(int $id): int
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_categories AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            [(int) $id, BL::getWorkingLanguage()]
        );
    }

    public static function existsComment(int $id): int
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_comments AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            [(int) $id, BL::getWorkingLanguage()]
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The Id of the item to fetch?
     *
     * @return array
     */
    public static function get(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND (i.status = ? OR i.status = ?) AND i.language = ?
             ORDER BY i.revision_id DESC',
            [(int) $id, 'active', 'draft', BL::getWorkingLanguage()]
        );
    }

    /**
     * Get the comments
     *
     * @param string $status The type of comments to get.
     * @param int    $limit  The maximum number of items to retrieve.
     * @param int    $offset The offset.
     *
     * @return array
     */
    public static function getAllCommentsForStatus(string $status, int $limit = 30, int $offset = 0): array
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
                [BL::getWorkingLanguage(), $offset, $limit]
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
            [$status, BL::getWorkingLanguage(), $offset, $limit]
        );
    }

    /**
     * Get all items by a given tag id
     *
     * @param int $tagId The id of the tag.
     *
     * @return array
     */
    public static function getByTag(int $tagId): array
    {
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.title AS name, mt.module
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             INNER JOIN blog_posts AS i ON mt.other_id = i.id
             WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ? AND i.language = ?',
            ['Blog', (int) $tagId, 'active', BL::getWorkingLanguage()]
        );

        // overwrite the url
        foreach ($items as &$row) {
            $row['url'] = BackendModel::createUrlForAction('Edit', 'Blog', null, ['id' => $row['url']]);
        }

        return $items;
    }

    /**
     * Get all categories
     *
     * @param bool $includeCount Include the count?
     *
     * @return array
     */
    public static function getCategories(bool $includeCount = false): array
    {
        $database = BackendModel::getContainer()->get('database');

        if ($includeCount) {
            return (array) $database->getPairs(
                'SELECT i.id, CONCAT(i.title, " (", COUNT(p.category_id) ,")") AS title
                 FROM blog_categories AS i
                 LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND i.language = p.language AND p.status = ?
                 WHERE i.language = ?
                 GROUP BY i.id',
                ['active', BL::getWorkingLanguage()]
            );
        }

        return (array) $database->getPairs(
            'SELECT i.id, i.title
             FROM blog_categories AS i
             WHERE i.language = ?',
            [BL::getWorkingLanguage()]
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The id of the category to fetch.
     *
     * @return array
     */
    public static function getCategory($id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM blog_categories AS i
             WHERE i.id = ? AND i.language = ?',
            [(int) $id, BL::getWorkingLanguage()]
        );
    }

    /**
     * Get a category id by title
     *
     * @param string $title    The title of the category.
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return int
     */
    public static function getCategoryId(string $title, string $language = null): int
    {
        $title = (string) $title;
        $language = ($language !== null) ? (string) $language : BL::getWorkingLanguage();

        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id
             FROM blog_categories AS i
             WHERE i.title = ? AND i.language = ?',
            [$title, $language]
        );
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The Id of the comment to fetch?
     *
     * @return array
     */
    public static function getComment($id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on,
             p.id AS post_id, p.title AS post_title, m.url AS post_url
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.id = ? AND p.status = ?
             LIMIT 1',
            [(int) $id, 'active']
        );
    }

    /**
     * Get multiple comments at once
     *
     * @param array $ids The id(s) of the comment(s).
     *
     * @return array
     */
    public static function getComments(array $ids): array
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
    public static function getCommentStatusCount(): array
    {
        return (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT i.status, COUNT(i.id)
             FROM blog_comments AS i
             WHERE i.language = ?
             GROUP BY i.status',
            [BL::getWorkingLanguage()]
        );
    }

    /**
     * Get the latest comments for a given type
     *
     * @param string $status The status for the comments to retrieve.
     * @param int    $limit  The maximum number of items to retrieve.
     *
     * @return array
     */
    public static function getLatestComments(string $status, int $limit = 10): array
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
            [(string) $status, 'active', BL::getWorkingLanguage(), (int) $limit]
        );

        // overwrite url
        $baseUrl = BackendModel::getUrlForBlock('Blog', 'detail');

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
    public static function getMaximumId(): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1');
    }

    /**
     * Get all data for a given revision
     *
     * @param int $id         The id of the item.
     * @param int $revisionId The revision to get.
     *
     * @return array
     */
    public static function getRevision(int $id, int $revisionId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND i.revision_id = ?',
            [(int) $id, (int) $revisionId]
        );
    }

    /**
     * Retrieve the unique URL for an item
     *
     * @param string $url The URL to base on.
     * @param int    $id  The id of the item to ignore.
     *
     * @return string
     */
    public static function getUrl(string $url, int $id = null): string
    {
        $url = (string) $url;

        // get database
        $database = BackendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            // already exists
            if ((bool) $database->getVar(
                'SELECT 1
                 FROM blog_posts AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                [BL::getWorkingLanguage(), $url]
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getUrl($url);
            }
        } else {
            // current category should be excluded
            if ((bool) $database->getVar(
                'SELECT 1
                 FROM blog_posts AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                [BL::getWorkingLanguage(), $url, $id]
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getUrl($url, $id);
            }
        }

        return $url;
    }

    /**
     * Retrieve the unique URL for a category
     *
     * @param string $url The string whereon the URL will be based.
     * @param int    $id  The id of the category to ignore.
     *
     * @return string
     */
    public static function getUrlForCategory($url, int $id = null): string
    {
        // redefine URL
        $url = (string) $url;

        // get database
        $database = BackendModel::getContainer()->get('database');

        // new category
        if ($id === null) {
            // already exists
            if ((bool) $database->getVar(
                'SELECT 1
                 FROM blog_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                [BL::getWorkingLanguage(), $url]
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getUrlForCategory($url);
            }
        } else {
            // current category should be excluded
            if ((bool) $database->getVar(
                'SELECT 1
                 FROM blog_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                [BL::getWorkingLanguage(), $url, $id]
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getUrlForCategory($url, $id);
            }
        }

        return $url;
    }

    /**
     * Inserts an item into the database
     *
     * @param array $item The data to insert.
     *
     * @return int
     */
    public static function insert(array $item): int
    {
        // insert and return the new revision id
        $item['revision_id'] = BackendModel::getContainer()->get('database')->insert('blog_posts', $item);

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
     *
     * @throws Exception
     *
     * @return int
     */
    public static function insertCompletePost(array $item, array $meta = [], $tags = [], $comments = []): int
    {
        // Build item
        if (!isset($item['id'])) {
            $item['id'] = (int) self::getMaximumId() + 1;
        }
        if (!isset($item['user_id'])) {
            $item['user_id'] = BackendAuthentication::getUser()->getUserId();
        }
        if (!isset($item['hidden'])) {
            $item['hidden'] = false;
        }
        if (!isset($item['allow_comments'])) {
            $item['allow_comments'] = true;
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
            $item['hidden'] = true;
            $item['status'] = 'draft';
        }

        // Build meta
        if (!is_array($meta)) {
            $meta = [];
        }
        if (!isset($meta['keywords'])) {
            $meta['keywords'] = $item['title'];
        }
        if (!isset($meta['keywords_overwrite'])) {
            $meta['keywords_overwrite'] = false;
        }
        if (!isset($meta['description'])) {
            $meta['description'] = $item['title'];
        }
        if (!isset($meta['description_overwrite'])) {
            $meta['description_overwrite'] = false;
        }
        if (!isset($meta['title'])) {
            $meta['title'] = $item['title'];
        }
        if (!isset($meta['title_overwrite'])) {
            $meta['title_overwrite'] = false;
        }
        if (!isset($meta['url'])) {
            $meta['url'] = self::getUrl($item['title']);
        }
        if (!isset($meta['url_overwrite'])) {
            $meta['url_overwrite'] = false;
        }
        if (!isset($meta['seo_index'])) {
            $meta['seo_index'] = 'index';
        }
        if (!isset($meta['seo_follow'])) {
            $meta['seo_follow'] = 'follow';
        }

        // Write meta to database
        $item['meta_id'] = BackendModel::getContainer()->get('database')->insert('meta', $meta);

        // Write post to database
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
                $comment['data'] = serialize(['server' => $_SERVER]);
            }
            if (!isset($comment['website'])) {
                $comment['website'] = '';
            }

            $comment['post_id'] = $item['id'];
            $comment['data'] = serialize(['server' => $_SERVER]);

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
     *
     * @return int
     */
    public static function insertCategory(array $item, array $meta = null): int
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // meta given?
        if ($meta !== null) {
            $item['meta_id'] = $database->insert('meta', $meta);
        }

        // create category
        $item['id'] = $database->insert('blog_categories', $item);

        // return the id
        return $item['id'];
    }

    /**
     * Inserts a new comment (Taken from FrontendBlogModel)
     *
     * @param array $comment The comment to add.
     *
     * @return int
     */
    public static function insertComment(array $comment): int
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // insert comment
        $comment['id'] = (int) $database->insert('blog_comments', $comment);

        // recalculate if published
        if ($comment['status'] == 'published') {
            // num comments
            $numComments = (int) BackendModel::getContainer()->get('database')->getVar(
                'SELECT COUNT(i.id) AS comment_count
                 FROM blog_comments AS i
                 INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
                 WHERE i.status = ? AND i.post_id = ? AND i.language = ? AND p.status = ?
                 GROUP BY i.post_id',
                ['published', $comment['post_id'], BL::getWorkingLanguage(), 'active']
            );

            // update num comments
            $database->update('blog_posts', ['num_comments' => $numComments], 'id = ?', $comment['post_id']);
        }

        return $comment['id'];
    }

    /**
     * Recalculate the commentcount
     *
     * @param array $ids The id(s) of the post wherefore the commentcount should be recalculated.
     *
     * @return bool
     */
    public static function reCalculateCommentCount(array $ids): bool
    {
        // validate
        if (empty($ids)) {
            return false;
        }

        // make unique ids
        $ids = array_unique($ids);

        // get database
        $database = BackendModel::getContainer()->get('database');

        // get counts
        $commentCounts = (array) $database->getPairs(
            'SELECT i.post_id, COUNT(i.id) AS comment_count
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             WHERE i.status = ? AND i.post_id IN (' . implode(',', $ids) . ') AND i.language = ? AND p.status = ?
             GROUP BY i.post_id',
            ['published', BL::getWorkingLanguage(), 'active']
        );

        foreach ($ids as $id) {
            // get count
            $count = (isset($commentCounts[$id])) ? (int) $commentCounts[$id] : 0;

            // update
            $database->update(
                'blog_posts',
                ['num_comments' => $count],
                'id = ? AND language = ?',
                [$id, BL::getWorkingLanguage()]
            );
        }

        return true;
    }

    /**
     * Update an existing item
     *
     * @param array $item The new data.
     *
     * @return int
     */
    public static function update(array $item): int
    {
        $database = BackendModel::getContainer()->get('database');
        // check if new version is active
        if ($item['status'] == 'active') {
            // archive all older active versions
            $database->update(
                'blog_posts',
                ['status' => 'archived'],
                'id = ? AND status = ?',
                [$item['id'], $item['status']]
            );

            // get the record of the exact item we're editing
            $revision = self::getRevision($item['id'], $item['revision_id']);

            // assign values
            $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $revision['created_on']);
            $item['num_comments'] = $revision['num_comments'];

            // if it used to be a draft that we're now publishing, remove drafts
            if ($revision['status'] == 'draft') {
                $database->delete(
                    'blog_posts',
                    'id = ? AND status = ?',
                    [$item['id'], $revision['status']]
                );
            }
        }

        // don't want revision id
        unset($item['revision_id']);

        // how many revisions should we keep
        $rowsToKeep = (int) BackendModel::get('fork.settings')->get('Blog', 'max_num_revisions', 20);

        // set type of archive
        $archiveType = ($item['status'] == 'active' ? 'archived' : $item['status']);

        // get revision-ids for items to keep
        $revisionIdsToKeep = (array) $database->getColumn(
            'SELECT i.revision_id
             FROM blog_posts AS i
             WHERE i.id = ? AND i.status = ? AND i.language = ?
             ORDER BY i.edited_on DESC
             LIMIT ?',
            [$item['id'], $archiveType, BL::getWorkingLanguage(), $rowsToKeep]
        );

        // delete other revisions
        if (!empty($revisionIdsToKeep)) {
            // get meta-ids that will be deleted
            $metasIdsToRemove = (array) $database->getColumn(
                'SELECT i.meta_id
                 FROM blog_posts AS i
                 WHERE i.id = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')',
                [$item['id']]
            );

            // get all the images of the revisions that will NOT be deleted
            $imagesToKeep = $database->getColumn(
                'SELECT image FROM blog_posts
                 WHERE id = ? AND revision_id IN (' . implode(', ', $revisionIdsToKeep) . ')',
                [$item['id']]
            );

            // get the images of the revisions that will be deleted
            $imagesOfDeletedRevisions = $database->getColumn(
                'SELECT image FROM blog_posts
                WHERE id = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')',
                [$item['id'], $archiveType]
            );

            // make sure that an image that will be deleted, is not used by a revision that is not to be deleted
            foreach ($imagesOfDeletedRevisions as $imageOfDeletedRevision) {
                if (!in_array($imageOfDeletedRevision, $imagesToKeep)) {
                    BackendModel::deleteThumbnails(FRONTEND_FILES_PATH . '/Blog/images', $imageOfDeletedRevision);
                }
            }

            $database->delete(
                'blog_posts',
                'id = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')',
                [$item['id'], $archiveType]
            );

            if (!empty($metasIdsToRemove)) {
                $database->delete(
                    'meta',
                    'id IN (' . implode(', ', $metasIdsToRemove) . ')'
                );
            }
        }

        // insert new version
        $item['revision_id'] = BackendModel::getContainer()->get('database')->insert('blog_posts', $item);

        // return the new revision id
        return $item['revision_id'];
    }

    /**
     * Update an existing category
     *
     * @param array       $item The new data.
     * @param array $meta The new meta-data.
     *
     * @return int
     */
    public static function updateCategory(array $item, array $meta = null): int
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // update category
        $updated = $database->update('blog_categories', $item, 'id = ?', [(int) $item['id']]);

        // meta passed?
        if ($meta !== null) {
            // get current category
            $category = self::getCategory($item['id']);

            // update the meta
            $database->update('meta', $meta, 'id = ?', [(int) $category['meta_id']]);
        }

        return $updated;
    }

    /**
     * Update an existing comment
     *
     * @param array $item The new data.
     *
     * @return int
     */
    public static function updateComment(array $item): int
    {
        // update category
        return BackendModel::getContainer()->get('database')->update(
            'blog_comments',
            $item,
            'id = ?',
            [(int) $item['id']]
        );
    }

    /**
     * Updates one or more comments' status
     *
     * @param array  $ids    The id(s) of the comment(s) to change the status for.
     * @param string $status The new status.
     */
    public static function updateCommentStatuses(array $ids, string $status): void
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

            // update records
            BackendModel::getContainer()->get('database')->execute(
                'UPDATE blog_comments
                 SET status = ?
                 WHERE id IN (' . implode(', ', $idPlaceHolders) . ')',
                array_merge([(string) $status], $ids)
            );

            // recalculate the comment count
            self::reCalculateCommentCount($itemIds);
        }
    }

    /**
     * Update a page revision without generating a new revision.
     * Needed to add an image to a page.
     *
     * @param $revision_id
     * @param $item
     */
    public static function updateRevision($revision_id, $item): void
    {
        BackendModel::getContainer()->get('database')->update(
            'blog_posts',
            $item,
            'revision_id = ?',
            [$revision_id]
        );
    }
}
