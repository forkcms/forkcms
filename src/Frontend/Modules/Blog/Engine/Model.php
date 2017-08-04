<?php

namespace Frontend\Modules\Blog\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Mailer\Message;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendUrl;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

/**
 * In this file we store all generic functions that we will be using in the blog module
 */
class Model implements FrontendTagsInterface
{
    public static function get(string $url): array
    {
        $return = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.revision_id, i.language, i.title, i.introduction, i.text,
             c.title AS category_title, m2.url AS category_url, i.image,
             UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id,
             i.allow_comments,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.custom AS meta_custom,
             m.url,
             m.data AS meta_data, m.seo_follow AS meta_seo_follow, m.seo_index AS meta_seo_index
             FROM blog_posts AS i
             INNER JOIN blog_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ? AND m.url = ?
             LIMIT 1',
            ['active', LANGUAGE, false, FrontendModel::getUTCDate('Y-m-d H:i'), $url]
        );

        // unserialize
        if (isset($return['meta_data'])) {
            $return['meta_data'] = @unserialize($return['meta_data']);
        }

        // image?
        if (isset($return['image'])) {
            $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Blog/images', true);

            foreach ($folders as $folder) {
                $return['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $return['image'];
            }
        }

        // return
        return $return;
    }

    public static function getAll(int $limit = 10, int $offset = 0): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.revision_id, i.language, i.title, i.introduction, i.text, i.num_comments AS comments_count,
             c.title AS category_title, m2.url AS category_url, i.image,
             UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.allow_comments,
             m.url
             FROM blog_posts AS i
             INNER JOIN blog_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ?
             ORDER BY i.publish_on DESC, i.id DESC
             LIMIT ?, ?',
            [
                'active',
                LANGUAGE,
                false,
                FrontendModel::getUTCDate('Y-m-d H:i'),
                $offset,
                $limit,
            ],
            'id'
        );

        // no results?
        if (empty($items)) {
            return [];
        }

        // init var
        $link = FrontendNavigation::getUrlForBlock('Blog', 'Detail');
        $categoryLink = FrontendNavigation::getUrlForBlock('Blog', 'Category');
        $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Blog/images', true);

        // loop
        foreach ($items as $key => $row) {
            // URLs
            $items[$key]['full_url'] = $link . '/' . $row['url'];
            $items[$key]['category_full_url'] = $categoryLink . '/' . $row['category_url'];

            // comments
            if ($row['comments_count'] > 0) {
                $items[$key]['comments'] = true;
            }
            if ($row['comments_count'] > 1) {
                $items[$key]['comments_multiple'] = true;
            }

            // allow comments as boolean
            $items[$key]['allow_comments'] = (bool) $row['allow_comments'];

            // reset allow comments
            if (!FrontendModel::get('fork.settings')->get('Blog', 'allow_comments')) {
                $items[$key]['allow_comments'] = false;
            }

            // image?
            if (isset($row['image'])) {
                foreach ($folders as $folder) {
                    $items[$key]['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['image'];
                }
            }
        }

        // get all tags
        $tags = FrontendTagsModel::getForMultipleItems('Blog', array_keys($items));

        // loop tags and add to correct item
        foreach ($tags as $postId => $data) {
            if (isset($items[$postId])) {
                $items[$postId]['tags'] = $data;
            }
        }

        // return
        return $items;
    }

    public static function getAllCategories(): array
    {
        $return = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, m.data AS meta_data,
                 m.seo_follow AS meta_seo_follow, m.seo_index AS meta_seo_index
             FROM blog_categories AS c
             INNER JOIN blog_posts AS i ON c.id = i.category_id AND c.language = i.language
             INNER JOIN meta AS m ON c.meta_id = m.id
             WHERE c.language = ? AND i.status = ? AND i.hidden = ? AND i.publish_on <= ?
             GROUP BY c.id',
            [LANGUAGE, 'active', false, FrontendModel::getUTCDate('Y-m-d H:i')],
            'id'
        );

        // loop items and unserialize
        foreach ($return as &$row) {
            if (isset($row['meta_data'])) {
                $row['meta_data'] = @unserialize($row['meta_data']);
            }
        }

        return $return;
    }

    public static function getAllComments(int $limit = 10, int $offset = 0): array
    {
        $comments = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.text,
             p.id AS post_id, p.title AS post_title, m.url AS post_url, i.email
             FROM blog_comments AS i
             INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
             INNER JOIN meta AS m ON p.meta_id = m.id
             WHERE i.status = ? AND i.language = ?
             GROUP BY i.id
             ORDER BY i.created_on DESC
             LIMIT ?, ?',
            ['published', LANGUAGE, $offset, $limit]
        );

        // loop comments and create gravatar id
        foreach ($comments as &$row) {
            $row['author'] = htmlspecialchars($row['author']);
            $row['text'] = htmlspecialchars($row['text']);
            $row['gravatar_id'] = md5($row['email']);
        }

        return $comments;
    }

    public static function getAllCount(): int
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM blog_posts AS i
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ?',
            ['active', LANGUAGE, false, FrontendModel::getUTCDate('Y-m-d H:i')]
        );
    }

    public static function getAllForCategory(string $categoryUrl, int $limit = 10, int $offset = 0): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.revision_id, i.language, i.title, i.introduction, i.text, i.num_comments AS comments_count,
             c.title AS category_title, m2.url AS category_url, i.image,
             UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.allow_comments,
             m.url
             FROM blog_posts AS i
             INNER JOIN blog_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ? AND m2.url = ?
             ORDER BY i.publish_on DESC
             LIMIT ?, ?',
            [
                'active',
                LANGUAGE,
                false,
                FrontendModel::getUTCDate('Y-m-d H:i'),
                $categoryUrl,
                $offset,
                $limit,
            ],
            'id'
        );

        // no results?
        if (empty($items)) {
            return [];
        }

        // init var
        $link = FrontendNavigation::getUrlForBlock('Blog', 'Detail');
        $categoryLink = FrontendNavigation::getUrlForBlock('Blog', 'Category');
        $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Blog/images', true);

        // loop
        foreach ($items as $key => $row) {
            // URLs
            $items[$key]['full_url'] = $link . '/' . $row['url'];
            $items[$key]['category_full_url'] = $categoryLink . '/' . $row['category_url'];

            // comments
            if ($row['comments_count'] > 0) {
                $items[$key]['comments'] = true;
            }
            if ($row['comments_count'] > 1) {
                $items[$key]['comments_multiple'] = true;
            }

            // allow comments as boolean
            $items[$key]['allow_comments'] = (bool) $row['allow_comments'];

            // reset allow comments
            if (!FrontendModel::get('fork.settings')->get('Blog', 'allow_comments')) {
                $items[$key]['allow_comments'] = false;
            }

            // image?
            if (isset($row['image'])) {
                foreach ($folders as $folder) {
                    $items[$key]['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] .
                                                                  '/' . $row['image'];
                }
            }
        }

        // get all tags
        $tags = FrontendTagsModel::getForMultipleItems('Blog', array_keys($items));

        // loop tags and add to correct item
        foreach ($tags as $postId => $data) {
            $items[$postId]['tags'] = $data;
        }

        // return
        return $items;
    }

    public static function getAllForCategoryCount(string $url): int
    {
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM blog_posts AS i
             INNER JOIN blog_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m ON c.meta_id = m.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ? AND m.url = ?',
            ['active', LANGUAGE, false, FrontendModel::getUTCDate('Y-m-d H:i'), $url]
        );
    }

    /**
     * Get all items between a start and end-date
     *
     * @param int $start The start date as a UNIX-timestamp.
     * @param int $end The end date as a UNIX-timestamp.
     * @param int $limit The number of items to get.
     * @param int $offset The offset.
     *
     * @return array
     */
    public static function getAllForDateRange(int $start, int $end, int $limit = 10, int $offset = 0): array
    {
        // get the items
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.revision_id, i.language, i.title, i.introduction, i.text, i.num_comments AS comments_count,
             c.title AS category_title, m2.url AS category_url, i.image,
             UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.allow_comments,
             m.url
             FROM blog_posts AS i
             INNER JOIN blog_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on BETWEEN ? AND ?
             ORDER BY i.publish_on DESC
             LIMIT ?, ?',
            [
                'active',
                LANGUAGE,
                false,
                FrontendModel::getUTCDate('Y-m-d H:i', $start),
                FrontendModel::getUTCDate('Y-m-d H:i', $end),
                $offset,
                $limit,
            ],
            'id'
        );

        // no results?
        if (empty($items)) {
            return [];
        }

        // init var
        $link = FrontendNavigation::getUrlForBlock('Blog', 'Detail');
        $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Blog/images', true);

        // loop
        foreach ($items as $key => $row) {
            // URLs
            $items[$key]['full_url'] = $link . '/' . $row['url'];

            // comments
            if ($row['comments_count'] > 0) {
                $items[$key]['comments'] = true;
            }
            if ($row['comments_count'] > 1) {
                $items[$key]['comments_multiple'] = true;
            }

            // allow comments as boolean
            $items[$key]['allow_comments'] = (bool) $row['allow_comments'];

            // reset allow comments
            if (!FrontendModel::get('fork.settings')->get('Blog', 'allow_comments')) {
                $items[$key]['allow_comments'] = false;
            }

            // image?
            if (isset($row['image'])) {
                foreach ($folders as $folder) {
                    $items[$key]['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] .
                                                                  '/' . $row['image'];
                }
            }
        }

        // get all tags
        $tags = FrontendTagsModel::getForMultipleItems('Blog', array_keys($items));

        // loop tags and add to correct item
        foreach ($tags as $postId => $data) {
            $items[$postId]['tags'] = $data;
        }

        // return
        return $items;
    }

    /**
     * Get the number of items in a date range
     *
     * @param int $start The start date as a UNIX-timestamp.
     * @param int $end The end date as a UNIX-timestamp.
     *
     * @return int
     */
    public static function getAllForDateRangeCount(int $start, int $end): int
    {
        // return the number of items
        return (int) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT COUNT(i.id)
             FROM blog_posts AS i
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on BETWEEN ? AND ?',
            [
                'active',
                LANGUAGE,
                false,
                FrontendModel::getUTCDate('Y-m-d H:i:s', $start),
                FrontendModel::getUTCDate('Y-m-d H:i:s', $end),
            ]
        );
    }

    /**
     * Get the statistics for the archive
     *
     * @return array
     */
    public static function getArchiveNumbers(): array
    {
        // grab stats
        $numbers = FrontendModel::getContainer()->get('database')->getPairs(
            'SELECT DATE_FORMAT(i.publish_on, "%Y%m") AS month, COUNT(i.id)
             FROM blog_posts AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ?
             GROUP BY month',
            ['active', LANGUAGE, false, FrontendModel::getUTCDate('Y-m-d H:i')]
        );

        $stats = [];
        $link = FrontendNavigation::getUrlForBlock('Blog', 'Archive');
        $firstYear = (int) date('Y');
        $lastYear = 0;

        // loop the numbers
        foreach ($numbers as $key => $count) {
            $year = mb_substr($key, 0, 4);
            $month = mb_substr($key, 4, 2);

            // reset
            if ($year < $firstYear) {
                $firstYear = $year;
            }
            if ($year > $lastYear) {
                $lastYear = $year;
            }

            // generate timestamp
            $timestamp = gmmktime(00, 00, 00, $month, 01, $year);

            // initialize if needed
            if (!isset($stats[$year])) {
                $stats[$year] = [
                    'url' => $link . '/' . $year,
                    'label' => $year,
                    'total' => 0,
                    'months' => null,
                ];
            }

            // increment the total
            $stats[$year]['total'] += (int) $count;
            $stats[$year]['months'][$key] = [
                'url' => $link . '/' . $year . '/' . $month,
                'label' => $timestamp,
                'total' => $count,
            ];
        }

        // loop years
        for ($i = $firstYear; $i <= $lastYear; ++$i) {
            // year missing
            if (!isset($stats[$i])) {
                $stats[$i] = ['url' => null, 'label' => $i, 'total' => 0, 'months' => null];
            }
        }

        // sort
        krsort($stats);

        // reset stats
        foreach ($stats as &$row) {
            // remove url for empty years
            if ($row['total'] == 0) {
                $row['url'] = null;
            }

            // any months?
            if (!empty($row['months'])) {
                // sort months
                ksort($row['months']);
            }
        }

        // return
        return $stats;
    }

    public static function getComments(int $blogPostId): array
    {
        // get the comments
        $comments = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT c.id, UNIX_TIMESTAMP(c.created_on) AS created_on, c.text, c.data,
             c.author, c.email, c.website
             FROM blog_comments AS c
             WHERE c.post_id = ? AND c.status = ? AND c.language = ?
             ORDER BY c.id ASC',
            [$blogPostId, 'published', LANGUAGE]
        );

        // loop comments and create gravatar id
        foreach ($comments as &$row) {
            $row['author'] = htmlspecialchars($row['author']);
            $row['text'] = htmlspecialchars($row['text']);
            $row['gravatar_id'] = md5($row['email']);
        }

        // return
        return $comments;
    }

    /**
     * Fetch the list of tags for a list of items
     *
     * @param array $blogPostIds The ids of the items to grab.
     *
     * @return array
     */
    public static function getForTags(array $blogPostIds): array
    {
        // fetch items
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.title, i.image, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.status = ? AND i.hidden = ? AND i.id IN (' . implode(',', $blogPostIds) . ') AND i.publish_on <= ?
             ORDER BY i.publish_on DESC',
            ['active', false, FrontendModel::getUTCDate('Y-m-d H:i')]
        );

        // has items
        if (!empty($items)) {
            // init var
            $link = FrontendNavigation::getUrlForBlock('Blog', 'Detail');
            $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Blog/images', true);

            // reset url
            foreach ($items as &$row) {
                $row['full_url'] = $link . '/' . $row['url'];

                // image?
                if (isset($row['image'])) {
                    foreach ($folders as $folder) {
                        $row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] .
                                                              '/' . $row['image'];
                    }
                }
            }
        }

        // return
        return $items;
    }

    /**
     * Get the id of an item by the full URL of the current page.
     * Selects the proper part of the full URL to get the item's id from the database.
     *
     * @param FrontendUrl $url The current URL.
     *
     * @return int
     */
    public static function getIdForTags(FrontendUrl $url): int
    {
        // select the proper part of the full URL
        $itemUrl = (string) $url->getParameter(1);

        // return the item
        return self::get($itemUrl)['id'];
    }

    /**
     * Get an array with the previous and the next post
     *
     * @param int $blogPostId The id of the current item.
     *
     * @return array
     */
    public static function getNavigation(int $blogPostId): array
    {
        // get database
        $database = FrontendModel::getContainer()->get('database');

        // get date for current item
        $date = (string) $database->getVar(
            'SELECT i.publish_on
             FROM blog_posts AS i
             WHERE i.id = ? AND i.status = ?',
            [$blogPostId, 'active']
        );

        // validate
        if ($date == '') {
            return [];
        }

        // init var
        $navigation = [];
        $detailLink = FrontendNavigation::getUrlForBlock('Blog', 'Detail') . '/';

        // get previous post
        $navigation['previous'] = $database->getRecord(
            'SELECT i.id, i.title, CONCAT(?, m.url) AS url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.id != ? AND i.status = ? AND i.hidden = ? AND i.language = ? AND
                ((i.publish_on = ? AND i.id < ?) OR i.publish_on < ?)
             ORDER BY i.publish_on DESC, i.id DESC
             LIMIT 1',
            [$detailLink, $blogPostId, 'active', false, LANGUAGE, $date, $blogPostId, $date]
        );

        // get next post
        $navigation['next'] = $database->getRecord(
            'SELECT i.id, i.title, CONCAT(?, m.url) AS url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.id != ? AND i.status = ? AND i.hidden = ? AND i.language = ? AND
                ((i.publish_on = ? AND i.id > ?) OR (i.publish_on > ? AND i.publish_on <= ?))
             ORDER BY i.publish_on ASC, i.id ASC
             LIMIT 1',
            [
                $detailLink,
                $blogPostId,
                'active',
                false,
                LANGUAGE,
                $date,
                $blogPostId,
                $date,
                FrontendModel::getUTCDate('Y-m-d H:i'),
            ]
        );

        // if empty, unset it
        if (empty($navigation['previous'])) {
            unset($navigation['previous']);
        }
        if (empty($navigation['next'])) {
            unset($navigation['next']);
        }

        // return
        return $navigation;
    }

    public static function getRecentComments(int $limit = 5): array
    {
        // init var
        $return = [];

        // get comments
        $comments = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT c.id, c.author, c.website, c.email, UNIX_TIMESTAMP(c.created_on) AS created_on, c.text,
             i.id AS post_id, i.title AS post_title,
             m.url AS post_url
             FROM blog_comments AS c
             INNER JOIN blog_posts AS i ON c.post_id = i.id AND c.language = i.language
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE c.status = ? AND i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ?
             ORDER BY c.id DESC
             LIMIT ?',
            ['published', 'active', LANGUAGE, false, FrontendModel::getUTCDate('Y-m-d H:i'), $limit]
        );

        // validate
        if (empty($comments)) {
            return $return;
        }

        // get link
        $link = FrontendNavigation::getUrlForBlock('Blog', 'Detail');

        // loop comments
        foreach ($comments as &$row) {
            // add some URLs
            $row['post_full_url'] = $link . '/' . $row['post_url'];
            $row['full_url'] = $link . '/' . $row['post_url'] . '#comment-' . $row['id'];
            $row['gravatar_id'] = md5($row['email']);
        }

        return $comments;
    }

    public static function getRelated(int $blogPostId, int $limit = 5): array
    {
        // get the related IDs
        $relatedIDs = (array) FrontendTagsModel::getRelatedItemsByTags($blogPostId, 'Blog', 'Blog', $limit);

        // no items
        if (empty($relatedIDs)) {
            return [];
        }

        // get link
        $link = FrontendNavigation::getUrlForBlock('Blog', 'Detail');

        // get items
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.title, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ? AND i.id IN(' .
            implode(',', $relatedIDs) . ')
             ORDER BY i.publish_on DESC, i.id DESC
             LIMIT ?',
            ['active', LANGUAGE, false, FrontendModel::getUTCDate('Y-m-d H:i'), $limit],
            'id'
        );

        // loop items
        foreach ($items as &$row) {
            $row['full_url'] = $link . '/' . $row['url'];
        }

        return $items;
    }

    public static function getRevision(string $url, int $revisionId): array
    {
        $return = (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.id, i.revision_id, i.language, i.title, i.introduction, i.text, i.image,
             c.title AS category_title, m2.url AS category_url,
             UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id,
             i.allow_comments,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.custom AS meta_custom,
             m.url,
             m.data AS meta_data, m.seo_follow AS meta_seo_follow, m.seo_index AS meta_seo_index
             FROM blog_posts AS i
             INNER JOIN blog_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE i.language = ? AND i.revision_id = ? AND m.url = ?
             LIMIT 1',
            [LANGUAGE, $revisionId, $url]
        );

        // unserialize
        if (isset($return['meta_data'])) {
            $return['meta_data'] = @unserialize($return['meta_data']);
        }

        // image?
        if (isset($return['image'])) {
            $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Blog/images', true);

            foreach ($folders as $folder) {
                $return['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $return['image'];
            }
        }

        // return
        return $return;
    }

    public static function insertComment(array $comment): int
    {
        // get database
        $database = FrontendModel::getContainer()->get('database');

        // insert comment
        $comment['id'] = (int) $database->insert('blog_comments', $comment);

        // recalculate if published
        if ($comment['status'] == 'published') {
            // num comments
            $numComments = (int) FrontendModel::getContainer()->get('database')->getVar(
                'SELECT COUNT(i.id) AS comment_count
                 FROM blog_comments AS i
                 INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
                 WHERE i.status = ? AND i.post_id = ? AND i.language = ? AND p.status = ?
                 GROUP BY i.post_id',
                ['published', $comment['post_id'], LANGUAGE, 'active']
            );

            // update num comments
            $database->update('blog_posts', ['num_comments' => $numComments], 'id = ?', $comment['post_id']);
        }

        return $comment['id'];
    }

    /**
     * Get moderation status for an author
     *
     * @param string $author The name for the author.
     * @param string $email The email address for the author.
     *
     * @return bool
     */
    public static function isModerated(string $author, string $email): bool
    {
        return (bool) FrontendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM blog_comments AS c
             WHERE c.status = ? AND c.author = ? AND c.email = ?
             LIMIT 1',
            ['published', $author, $email]
        );
    }

    /**
     * Notify the admin
     *
     * @param array $comment The comment that was submitted.
     */
    public static function notifyAdmin(array $comment): void
    {
        // don't notify admin in case of spam
        if ($comment['status'] == 'spam') {
            return;
        }

        // get settings
        $notifyByMailOnComment = FrontendModel::get('fork.settings')->get(
            'Blog',
            'notify_by_email_on_new_comment',
            false
        );
        $notifyByMailOnCommentToModerate = FrontendModel::get('fork.settings')->get(
            'Blog',
            'notify_by_email_on_new_comment_to_moderate',
            false
        );

        // create URLs
        $url = SITE_URL . FrontendNavigation::getUrlForBlock('Blog', 'Detail') . '/' .
               $comment['post_url'] . '#comment-' . $comment['id'];
        $backendUrl = SITE_URL . FrontendNavigation::getBackendUrlForBlock('comments', 'Blog') . '#tabModeration';

        // notify on all comments
        if ($notifyByMailOnComment) {
            $variables = [];

            // comment to moderate
            if ($comment['status'] == 'moderation') {
                $variables['message'] = vsprintf(
                    FL::msg('BlogEmailNotificationsNewCommentToModerate'),
                    [$comment['author'], $url, $comment['post_title'], $backendUrl]
                );
            } elseif ($comment['status'] == 'published') {
                // comment was published
                $variables['message'] = vsprintf(
                    FL::msg('BlogEmailNotificationsNewComment'),
                    [$comment['author'], $url, $comment['post_title']]
                );
            }

            $to = FrontendModel::get('fork.settings')->get('Core', 'mailer_to');
            $from = FrontendModel::get('fork.settings')->get('Core', 'mailer_from');
            $replyTo = FrontendModel::get('fork.settings')->get('Core', 'mailer_reply_to');
            $message = Message::newInstance(FL::msg('NotificationSubject'))
                ->setFrom([$from['email'] => $from['name']])
                ->setTo([$to['email'] => $to['name']])
                ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                ->parseHtml(
                    '/Core/Layout/Templates/Mails/Notification.html.twig',
                    $variables,
                    true
                )
            ;
            FrontendModel::get('mailer')->send($message);
        } elseif ($notifyByMailOnCommentToModerate && $comment['status'] == 'moderation') {
            // only notify on new comments to moderate and if the comment is one to moderate
            // set variables
            $variables = [];
            $variables['message'] = vsprintf(
                FL::msg('BlogEmailNotificationsNewCommentToModerate'),
                [$comment['author'], $url, $comment['post_title'], $backendUrl]
            );

            $to = FrontendModel::get('fork.settings')->get('Core', 'mailer_to');
            $from = FrontendModel::get('fork.settings')->get('Core', 'mailer_from');
            $replyTo = FrontendModel::get('fork.settings')->get('Core', 'mailer_reply_to');
            $message = Message::newInstance(FL::msg('NotificationSubject'))
                ->setFrom([$from['email'] => $from['name']])
                ->setTo([$to['email'] => $to['name']])
                ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                ->parseHtml(
                    '/Core/Layout/Templates/Mails/Notification.html.twig',
                    $variables,
                    true
                )
            ;
            FrontendModel::get('mailer')->send($message);
        }
    }

    /**
     * Parse the search results for this module
     *
     * Note: a module's search function should always:
     *        - accept an array of entry id's
     *        - return only the entries that are allowed to be displayed, with their array's index being the entry's id
     *
     *
     * @param array $ids The ids of the found results.
     *
     * @return array
     */
    public static function search(array $ids): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.title, i.introduction, i.text, m.url
             FROM blog_posts AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.status = ? AND i.hidden = ? AND i.language = ? AND i.publish_on <= ? AND i.id IN (' .
            implode(',', $ids) . ')',
            ['active', false, LANGUAGE, date('Y-m-d H:i')],
            'id'
        );

        // prepare items for search
        $detailUrl = FrontendNavigation::getUrlForBlock('Blog', 'Detail');
        foreach ($items as &$item) {
            $item['full_url'] = $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }
}
