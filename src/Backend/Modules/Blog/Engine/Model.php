<?php

namespace Backend\Modules\Blog\Engine;

use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Core\Language\Locale;
use Backend\Modules\Blog\Domain\Category\CategoryRepository;
use Backend\Modules\Blog\Domain\Comment\Comment;
use Backend\Modules\Blog\Domain\Category\Category;
use Backend\Modules\Blog\Domain\Comment\CommentRepository;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use ForkCMS\Utility\Thumbnails;
use Common\Doctrine\Entity\Meta;

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
         LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND p.status = ? AND p.language = i.locale
         WHERE i.locale = ?
         GROUP BY i.id';

    const QUERY_DATAGRID_BROWSE_COMMENTS =
        'SELECT
             i.id, UNIX_TIMESTAMP(i.createdOn) AS created_on, i.author, i.text,
             p.id AS post_id, p.title AS post_title, m.url AS post_url
         FROM blog_comments AS i
         INNER JOIN blog_posts AS p ON i.postId = p.id AND i.locale = p.language
         INNER JOIN meta AS m ON p.meta_id = m.id
         WHERE i.status = ? AND i.locale = ? AND p.status = ?
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
            $rssTitle = BackendModel::get('fork.settings')->get(
                'Blog',
                'rss_title_' . BL::getWorkingLanguage(),
                null
            );
            if ($rssTitle == '') {
                $warnings[] = [
                    'message' => sprintf(
                        BL::err('RSSTitle', 'Blog'),
                        BackendModel::createUrlForAction('Settings', 'Blog')
                    ),
                ];
            }

            // rss description
            $rssDescription = BackendModel::get('fork.settings')->get(
                'Blog',
                'rss_description_' . BL::getWorkingLanguage(),
                null
            );
            if ($rssDescription == '') {
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
            BackendModel::get(Thumbnails::class)->delete(FRONTEND_FILES_PATH . '/Blog/images', $image);
        }

        // delete records
        $database->delete(
            'blog_posts',
            'id IN (' . $idPlaceHolders . ') AND language = ?',
            array_merge($ids, [BL::getWorkingLanguage()])
        );
        $database->delete(
            'blog_comments',
            'postId IN (' . $idPlaceHolders . ') AND locale = ?',
            array_merge($ids, [BL::getWorkingLanguage()])
        );

        // delete tags
        foreach ($ids as $id) {
            BackendTagsModel::saveTags($id, '', 'Blog');
        }
    }

    public static function deleteCategory(int $id): void
    {
        $repository = BackendModel::get(CategoryRepository::class);
        $category = $repository->find($id);

        if (!$category instanceof Category) {
            return;
        }

        $repository->remove($category);
    }

    /**
     * Checks if it is allowed to delete the a category
     *
     * @param int $id The id of the category.
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

    public static function deleteComments(array $ids): void
    {
        $repository = BackendModel::get(CommentRepository::class);
        $comments = $repository->findById($ids);

        if (empty($comments)) {
            return;
        }

        $postIdsToRecalculate = array_map(
            function ($comment) {
                return $comment->getPostId();
            },
            $comments
        );

        $repository->deleteMultipleById($ids);

        // recalculate the comment count
        if (!empty($postIdsToRecalculate)) {
            $postIdsToRecalculate = array_unique($postIdsToRecalculate);
            self::reCalculateCommentCount($postIdsToRecalculate);
        }
    }

    public static function deleteSpamComments(): void
    {
        $comments = BackendModel::get('CommentRepository::class')->findBy(
            [
                'status' => 'spam',
                'locale' => BL::getWorkingLanguage(),
            ]
        );

        if (empty($comments)) {
            return;
        }

        $ids = array_map(
            function ($comment) {
                return $comment->getId();
            },
            $comments
        );

        self::deleteComments($ids);
    }

    /**
     * Checks if an item exists
     *
     * @param int $id The id of the item to check for existence.
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

    public static function existsCategory(int $id): bool
    {
        $repository = BackendModel::get(CategoryRepository::class);

        return $repository->find($id) instanceof Category;
    }

    public static function existsComment(int $id): bool
    {
        $repository = BackendModel::get('CommentRepository::class');

        return $repository->find($id) instanceof Comment;
    }

    /**
     * Get all data for a given id
     *
     * @param int $id The Id of the item to fetch?
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
     * @deprecated
     * @param string $status The type of comments to get.
     * @param int $limit The maximum number of items to retrieve.
     * @param int $offset The offset.
     * @return array
     */
    public static function getAllCommentsForStatus(string $status, int $limit = 30, int $offset = 0): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT DISTINCT i.id, UNIX_TIMESTAMP(i.createdOn) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
             p.id AS post_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.postId = p.id AND i.locale = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.status = ? AND i.locale = ?
             LIMIT ?, ?',
            [$status, BL::getWorkingLanguage(), $offset, $limit]
        );
    }

    /**
     * Get all items by a given tag id
     *
     * @param int $tagId The id of the tag.
     * @return array
     */
    public static function getByTag(int $tagId): array
    {
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.title AS name, mt.moduleName AS module
             FROM TagsModuleTag AS mt
             INNER JOIN TagsTag AS t ON mt.tag_id = t.id
             INNER JOIN blog_posts AS i ON mt.moduleId = i.id
             WHERE mt.moduleName = ? AND mt.tag_id = ? AND i.status = ? AND i.language = ?',
            ['Blog', $tagId, 'active', BL::getWorkingLanguage()]
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
     * @return array
     */
    public static function getCategories(bool $includeCount = false): array
    {
        $database = BackendModel::getContainer()->get('database');

        if ($includeCount) {
            return (array) $database->getPairs(
                'SELECT i.id, CONCAT(i.title, " (", COUNT(p.category_id) ,")") AS title
                 FROM blog_categories AS i
                 LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND i.locale = p.language AND p.status = ?
                 WHERE i.locale = ?
                 GROUP BY i.id',
                ['active', BL::getWorkingLanguage()]
            );
        }

        return (array) $database->getPairs(
            'SELECT i.id, i.title
             FROM blog_categories AS i
             WHERE i.locale = ?',
            [BL::getWorkingLanguage()]
        );
    }

    public static function getCategory($id): array
    {
        $category = BackendModel::get(CategoryRepository::class)
                                ->find($id);

        if (!$category instanceof Category) {
            return [];
        }

        return $category->toArray();
    }

    /**
     * @deprecated
     */
    public static function getCategoryId(string $title, string $language = null): int
    {
        $category = BackendModel::get(CategoryRepository::class)
                                ->findOneBy(
                                    [
                                        'title' => $title,
                                        'locale' => Locale::fromString(
                                            $language ?? BL::getWorkingLanguage()
                                        ),
                                    ]
                                );

        if (!$category instanceof Category) {
            return 0;
        }

        return $category->getId();
    }

    public static function getComment(int $id): array
    {
        $comment = BackendModel::get('CommentRepository::class')
                               ->find($id);

        if (!$comment instanceof Comment) {
            return [];
        }

        $commentData = $comment->toArray();

        // I know this is dirty, but as we don't have full entities yet we
        // need to fetch the post separately and inject it into the comment
        // @todo: fix this when there is a POST entity
        $postData = (array) BackendModel::getContainer()->get('database')
            ->getRecord(
                'SELECT p.id AS post_id, p.title AS post_title, m.url AS post_url
                 FROM blog_posts AS p
                 INNER JOIN meta AS m ON p.meta_id = m.id
                 WHERE p.id = ? AND p.status = ? AND p.language = ?
                 LIMIT 1',
                [
                    (int) $comment->getPostId(),
                    'active',
                    $comment->getLocale()->getLocale(),
                ]
            );

        return array_merge(
            $commentData,
            $postData
        );
    }

    public static function getComments(array $ids): array
    {
        $repository = BackendModel::get('CommentRepository::class');

        return array_map(
            function (Comment $comment) {
                $commentData = $comment->toArray();
                // I really don't know why this is returned in a non UNIX-timestamp format
                $commentData['created_on'] = $comment->getCreatedOn()->format('Y-m-d H:i:s');

                return $commentData;
            },
            $repository->findById($ids)
        );
    }

    public static function getCommentStatusCount(): array
    {
        $repository = BackendModel::get('CommentRepository::class');

        return $repository->listCountPerStatus(Locale::workingLocale());
    }

    /**
     * Get the latest comments for a given type
     *
     * @param string $status The status for the comments to retrieve.
     * @param int $limit The maximum number of items to retrieve.
     * @return array
     */
    public static function getLatestComments(string $status, int $limit = 10): array
    {
        $comments = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.author, i.text, UNIX_TIMESTAMP(i.createdOn) AS created_on,
             p.title, p.language, m.url
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.postId = p.id AND i.locale = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.status = ? AND p.status = ? AND i.locale = ?
             ORDER BY i.createdOn DESC
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
     * @param int $id The id of the item.
     * @param int $revisionId The revision to get.
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
     * @param int $id The id of the item to ignore.
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

    public static function getUrlForCategory(string $url, int $id = null): string
    {
        $repository = BackendModel::get(CategoryRepository::class);

        return $repository->getUrl($url, $id);
    }

    /**
     * Inserts an item into the database
     *
     * @param array $item The data to insert.
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
     * This method's purpose is to be able to insert a post (possibly with all its metadata, tags, and comments)
     * in one method call. As much data as possible has been made optional, to be able to do imports where only
     * fractions of the data we need are known.
     * The item array should have at least a 'title' and a 'text' property other properties are optional.
     * The meta array has only optional properties. You can use these to override the defaults.
     * The tags array is just a list of tagnames as string.
     * The comments array is an array of arrays with comment properties. A comment should have
     * at least 'author', 'email', and 'text' properties.
     *
     * @param array $item The data to insert.
     * @param array $meta The metadata to insert.
     * @param array $tags The tags to connect to this post.
     * @param array $comments The comments attached to this post.
     * @throws Exception
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

    public static function insertCategory(array $item, array $meta = null): int
    {
        if ($meta === null) {
            $meta = BackendModel::get('fork.repository.meta')
                                ->find($item['meta_id']);
        } else {
            $meta = new Meta(
                $meta['keywords'],
                $meta['keywords_overwrite'] ?? false,
                $meta['description'],
                $meta['description_overwrite'] ?? false,
                $meta['title'],
                $meta['title_overwrite'] ?? false,
                $meta['url'],
                false
            );
        }

        $category = new Category(
            Locale::fromString($item['language'] ?? $item['locale']),
            $item['title'],
            $meta
        );

        $entityManager = BackendModel::get('doctrine.orm.default_entity_manager');
        $entityManager->persist($category);
        $entityManager->flush($category);

        return $category->getId();
    }

    public static function insertComment(array $data): int
    {
        $comment = new Comment(
            $data['post_id'],
            Locale::fromString($data['language']),
            $data['author'],
            $data['email'],
            $data['text'],
            'comment',
            $data['status'],
            $data['website'],
            $data['data']
        );

        $entityManager = BackendModel::get('doctrine.orm.default_entity_manager');
        $entityManager->persist($comment);
        $entityManager->flush($comment);

        // recalculate if published
        if ($comment->getStatus() === 'published') {
            self::reCalculateCommentCount([$comment->getPostId()]);
        }

        return $comment->getId();
    }

    /**
     * Recalculate the commentcount
     *
     * @param array $ids The id(s) of the post wherefore the commentcount should be recalculated.
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
            'SELECT i.postId as post_id, COUNT(i.id) AS comment_count
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.postId = p.id AND i.locale = p.language
             WHERE i.status = ? AND i.postId IN (' . implode(',', $ids) . ') AND i.locale = ? AND p.status = ?
             GROUP BY i.postId',
            ['published', BL::getWorkingLanguage(), 'active']
        );

        foreach ($ids as $id) {
            // get count
            $count = (int) ($commentCounts[$id] ?? 0);

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
     * @return int
     */
    public static function update(array $item): int
    {
        $database = BackendModel::getContainer()->get('database');

        // get the record of the exact item we're editing
        $revision = self::getRevision($item['id'], $item['revision_id']);

        // assign values
        $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $revision['created_on']);
        $item['num_comments'] = $revision['num_comments'];

        // check if new version is active
        if ($item['status'] === 'active') {
            // archive all older active versions
            $database->update(
                'blog_posts',
                ['status' => 'archived'],
                'id = ? AND status = ?',
                [$item['id'], $item['status']]
            );

            // if it used to be a draft that we're now publishing, remove drafts
            if ($revision['status'] === 'draft') {
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
        $archiveType = ($item['status'] === 'active' ? 'archived' : $item['status']);

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
                if (!in_array($imageOfDeletedRevision, $imagesToKeep, true)) {
                    BackendModel::get(Thumbnails::class)->delete(
                        FRONTEND_FILES_PATH . '/Blog/images',
                        $imageOfDeletedRevision
                    );
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

    public static function updateCategory(array $item, array $meta = null): void
    {
        $category = BackendModel::get(CategoryRepository::class)
                                ->find($item['id']);

        if (!$category instanceof Category) {
            return;
        }

        $metaEntity = $category->getMeta();

        if ($meta !== null) {
            $metaEntity->update(
                $meta['keywords'],
                (isset($meta['keywords_overwrite'])) ? $meta['keywords_overwrite'] : false,
                $meta['description'],
                (isset($meta['description_overwrite'])) ? $meta['description_overwrite'] : false,
                $meta['title'],
                (isset($meta['title_overwrite'])) ? $meta['title_overwrite'] : false,
                $meta['url'],
                false
            );
        }

        $category->update(
            $item['title'],
            $metaEntity
        );

        BackendModel::get('doctrine.orm.default_entity_manager')->flush($category);
    }

    public static function updateComment(array $item): void
    {
        $comment = BackendModel::get('CommentRepository::class')
                               ->find($item['id']);

        if (!$comment instanceof Comment) {
            return;
        }

        $comment->update(
            $item['author'],
            $item['email'],
            $item['text'],
            $item['type'] ?? $comment->getType(),
            $item['status'],
            $item['website'],
            $item['data'] ?? $comment->getData()
        );

        BackendModel::get('doctrine.orm.default_entity_manager')->flush($comment);
    }

    public static function updateCommentStatuses(array $ids, string $status): void
    {
        $repository = BackendModel::get('CommentRepository::class');
        $comments = $repository->findById($ids);

        if (empty($comments)) {
            return;
        }

        $postIdsToRecalculate = [];
        foreach ($comments as $comment) {
            $postIdsToRecalculate[] = $comment->getPostId();
        }

        $repository->updateMultipleStatusById($ids, $status);

        if (empty($postIdsToRecalculate)) {
            return;
        }

        $postIdsToRecalculate = array_unique($postIdsToRecalculate);
        self::reCalculateCommentCount($postIdsToRecalculate);
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
