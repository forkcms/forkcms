<?php

namespace Frontend\Modules\Faq\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendUrl;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

/**
 * In this file we store all generic functions that we will be using in the faq module
 */
class Model implements FrontendTagsInterface
{
    public static function get(string $url): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, m.url, c.title AS category_title, m2.url AS category_url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN faq_categories AS c ON i.category_id = c.id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE m.url = ? AND i.language = ? AND i.hidden = ?
             ORDER BY i.sequence',
            [$url, LANGUAGE, false]
        );
    }

    /**
     * @param int $categoryId
     * @param int $limit
     * @param int|int[] $excludeIds
     *
     * @return array
     */
    public static function getAllForCategory(int $categoryId, int $limit = null, $excludeIds = null): array
    {
        $excludeIds = empty($excludeIds) ? [0] : (array) $excludeIds;

        // get items
        if ($limit !== null) {
            $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
                'SELECT i.*, m.url
                 FROM faq_questions AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.category_id = ? AND i.language = ? AND i.hidden = ?
                 AND i.id NOT IN (' . implode(',', $excludeIds) . ')
             ORDER BY i.sequence
             LIMIT ?',
                [$categoryId, LANGUAGE, false, $limit]
            );
        } else {
            $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
                'SELECT i.*, m.url
                 FROM faq_questions AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.category_id = ? AND i.language = ? AND i.hidden = ?
                 AND i.id NOT IN (' . implode(',', $excludeIds) . ')
             ORDER BY i.sequence',
                [$categoryId, LANGUAGE, false]
            );
        }

        // init var
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        // build the item urls
        foreach ($items as &$item) {
            $item['full_url'] = $link . '/' . $item['url'];
        }

        return $items;
    }

    public static function getCategories(): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*, m.url
             FROM faq_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ?
             ORDER BY i.sequence',
            [LANGUAGE]
        );

        // init var
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Category');

        // build the item url
        foreach ($items as &$item) {
            $item['full_url'] = $link . '/' . $item['url'];
        }

        return $items;
    }

    public static function getCategory(string $url): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, m.url
             FROM faq_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ? AND i.language = ?
             ORDER BY i.sequence',
            [$url, LANGUAGE]
        );
    }

    public static function getCategoryById(int $id): array
    {
        return (array) FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, m.url
             FROM faq_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.id = ? AND i.language = ?
             ORDER BY i.sequence',
            [$id, LANGUAGE]
        );
    }

    /**
     * Fetch the list of tags for a list of items
     *
     * @param array $ids
     *
     * @return array
     */
    public static function getForTags(array $ids): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.question AS title, m.url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.hidden = ? AND i.id IN (' . implode(',', $ids) . ')
             ORDER BY i.question',
            [false]
        );

        if (!empty($items)) {
            $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

            // build the item urls
            foreach ($items as &$row) {
                $row['full_url'] = $link . '/' . $row['url'];
            }
        }

        return $items;
    }

    /**
     * Get the id of an item by the full URL of the current page.
     * Selects the proper part of the full URL to get the item's id from the database.
     *
     * @param FrontendUrl $url
     *
     * @return int
     */
    public static function getIdForTags(FrontendUrl $url): int
    {
        $itemUrl = (string) $url->getParameter(1);

        return self::get($itemUrl)['id'];
    }

    public static function getMostRead(int $limit): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.*, m.url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.num_views > 0 AND i.language = ? AND i.hidden = ?
             ORDER BY (i.num_usefull_yes + i.num_usefull_no) DESC
             LIMIT ?',
            [LANGUAGE, false, $limit]
        );

        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');
        foreach ($items as &$item) {
            $item['full_url'] = $link . '/' . $item['url'];
        }

        return $items;
    }

    public static function getFaqsForCategory(int $categoryId): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.category_id, i.question, i.hidden, i.sequence, m.url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ? AND i.category_id = ?
             ORDER BY i.sequence ASC',
            [LANGUAGE, $categoryId]
        );

        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        foreach ($items as &$item) {
            $item['full_url'] = $link . '/' . $item['url'];
        }

        return $items;
    }

    /**
     * Get related items based on tags
     *
     * @param int $questionId
     * @param int $limit
     *
     * @return array
     */
    public static function getRelated(int $questionId, int $limit = 5): array
    {
        $relatedIDs = (array) FrontendTagsModel::getRelatedItemsByTags((int) $questionId, 'Faq', 'Faq');

        // there are no items, so return an empty array
        if (empty($relatedIDs)) {
            return [];
        }

        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.question, m.url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ? AND i.hidden = ? AND i.id IN(' . implode(',', $relatedIDs) . ')
             ORDER BY i.question
             LIMIT ?',
            [LANGUAGE, false, $limit],
            'id'
        );

        foreach ($items as &$row) {
            $row['full_url'] = $link . '/' . $row['url'];
        }

        return $items;
    }

    public static function increaseViewCount(int $questionId): void
    {
        FrontendModel::getContainer()->get('database')->execute(
            'UPDATE faq_questions SET num_views = num_views + 1 WHERE id = ?',
            [$questionId]
        );
    }

    public static function saveFeedback(array $feedback): void
    {
        $feedback['created_on'] = FrontendModel::getUTCDate();
        unset($feedback['sentOn']);

        FrontendModel::getContainer()->get('database')->insert('faq_feedback', $feedback);
    }

    /**
     * Parse the search results for this module
     *
     * Note: a module's search function should always:
     *        - accept an array of entry id's
     *        - return only the entries that are allowed to be displayed, with their array's index being the entry's id
     *
     *
     * @param array $ids
     *
     * @return array
     */
    public static function search(array $ids): array
    {
        $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.question AS title, i.answer AS text, m.url,
             c.title AS category_title, m2.url AS category_url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             INNER JOIN faq_categories AS c ON c.id = i.category_id
             INNER JOIN meta AS m2 ON c.meta_id = m2.id
             WHERE i.hidden = ? AND i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
            [false, LANGUAGE],
            'id'
        );

        // prepare items for search
        $detailUrl = FrontendNavigation::getUrlForBlock('Faq', 'Detail');
        foreach ($items as &$item) {
            $item['full_url'] = $detailUrl . '/' . $item['url'];
        }

        return $items;
    }

    /**
     * Update the usefulness of the feedback
     *
     * @param int $id
     * @param bool $useful
     * @param bool|null $previousFeedback
     */
    public static function updateFeedback(int $id, bool $useful, bool $previousFeedback = null): void
    {
        // feedback hasn't changed so don't update the counters
        if ($previousFeedback !== null && $useful == $previousFeedback) {
            return;
        }

        $database = FrontendModel::getContainer()->get('database');

        // update counter with current feedback (increase)
        if ($useful) {
            $database->execute(
                'UPDATE faq_questions
                 SET num_usefull_yes = num_usefull_yes + 1
                 WHERE id = ?',
                [$id]
            );
        } else {
            $database->execute(
                'UPDATE faq_questions
                 SET num_usefull_no = num_usefull_no + 1
                 WHERE id = ?',
                [$id]
            );
        }

        // update counter with previous feedback (decrease)
        if ($previousFeedback) {
            $database->execute(
                'UPDATE faq_questions
                 SET num_usefull_yes = num_usefull_yes - 1
                 WHERE id = ?',
                [$id]
            );
        } elseif ($previousFeedback !== null) {
            $database->execute(
                'UPDATE faq_questions
                 SET num_usefull_no = num_usefull_no - 1
                 WHERE id = ?',
                [$id]
            );
        }
    }
}
