<?php

namespace Backend\Modules\Faq\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModuleExtraType;
use Common\Uri as CommonUri;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * In this file we store all generic functions that we will be using in the faq module
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT i.id, i.category_id, i.question, i.hidden, i.sequence
         FROM faq_questions AS i
         WHERE i.language = ? AND i.category_id = ?
         ORDER BY i.sequence ASC';

    const QRY_DATAGRID_BROWSE_CATEGORIES =
        'SELECT i.id, i.title, COUNT(p.id) AS num_items, i.sequence
         FROM faq_categories AS i
         LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND p.language = i.language
         WHERE i.language = ?
         GROUP BY i.id
         ORDER BY i.sequence ASC';

    /**
     * Delete a question
     *
     * @param int $id
     */
    public static function delete(int $id)
    {
        $question = self::get($id);

        /** @var $db \SpoonDatabase */
        $db = BackendModel::getContainer()->get('database');
        $db->delete('faq_questions', 'id = ?', array($id));
        $db->delete('meta', 'id = ?', array((int) $question['meta_id']));

        BackendTagsModel::saveTags($id, '', 'Faq');
    }

    /**
     * Delete a specific category
     *
     * @param int $id
     */
    public static function deleteCategory(int $id)
    {
        $db = BackendModel::getContainer()->get('database');
        $item = self::getCategory($id);

        if (empty($item)) {
            return;
        }

        $db->delete('meta', 'id = ?', array($item['meta_id']));
        $db->delete('faq_categories', 'id = ?', array($id));
        $db->update('faq_questions', array('category_id' => null), 'category_id = ?', array($id));

        BackendModel::deleteExtraById($item['extra_id']);
    }

    /**
     * Is the deletion of a category allowed?
     *
     * @param int $id
     *
     * @return bool
     */
    public static function deleteCategoryAllowed(int $id): bool
    {
        if (!BackendModel::get('fork.settings')->get('Faq', 'allow_multiple_categories', true)
            && self::getCategoryCount() == 1
        ) {
            return false;
        }

        // check if the category does not contain questions
        return !(bool) BackendModel::get('database')->getVar(
            'SELECT 1
                 FROM faq_questions AS i
                 WHERE i.category_id = ? AND i.language = ?
                 LIMIT 1',
            array($id, BL::getWorkingLanguage())
        );
    }

    /**
     * Delete the feedback
     *
     * @param int $itemId
     */
    public static function deleteFeedback(int $itemId)
    {
        BackendModel::getContainer()->get('database')->update(
            'faq_feedback',
            array('processed' => 'Y', 'edited_on' => \SpoonDate::getDate('Y-m-d H:i:s')),
            'id = ?',
            $itemId
        );
    }

    /**
     * Does the question exist?
     *
     * @param int $id
     *
     * @return bool
     */
    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM faq_questions AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array($id, BL::getWorkingLanguage())
        );
    }

    /**
     * Does the category exist?
     *
     * @param int $id
     *
     * @return bool
     */
    public static function existsCategory(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM faq_categories AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array($id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch a question
     *
     * @param int $id
     *
     * @return array
     */
    public static function get(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, m.url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND i.language = ?',
            array($id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetches all the feedback that is available
     *
     * @param int $limit
     *
     * @return array
     */
    public static function getAllFeedback(int $limit = 5): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.processed = ?
             LIMIT ?',
            array('N', $limit)
        );
    }

    /**
     * Fetches all the feedback for a question
     *
     * @param int $id The question id.
     *
     * @return array
     */
    public static function getAllFeedbackForQuestion(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.question_id = ? AND f.processed = ?',
            array($id, 'N')
        );
    }

    /**
     * Get all items by a given tag id
     *
     * @param int $tagId
     *
     * @return array
     */
    public static function getByTag(int $tagId): array
    {
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.question AS name, mt.module
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             INNER JOIN faq_questions AS i ON mt.other_id = i.id
             WHERE mt.module = ? AND mt.tag_id = ? AND i.language = ?',
            array('Faq', $tagId, BL::getWorkingLanguage())
        );

        foreach ($items as &$row) {
            $row['url'] = BackendModel::createURLForAction('Edit', 'Faq', null, array('id' => $row['url']));
        }

        return $items;
    }

    /**
     * Get all the categories
     *
     * @param bool $includeCount
     *
     * @return array
     */
    public static function getCategories(bool $includeCount = false): array
    {
        $db = BackendModel::getContainer()->get('database');

        if ($includeCount) {
            return (array) $db->getPairs(
                'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
                 FROM faq_categories AS i
                 LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND i.language = p.language
                 WHERE i.language = ?
                 GROUP BY i.id
                 ORDER BY i.sequence',
                array(BL::getWorkingLanguage())
            );
        }

        return (array) $db->getPairs(
            'SELECT i.id, i.title
             FROM faq_categories AS i
             WHERE i.language = ?',
            array(BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch a category
     *
     * @param int $id
     *
     * @return array
     */
    public static function getCategory(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, m.url
             FROM faq_categories AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND i.language = ?',
            array($id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch the category count
     *
     * @return int
     */
    public static function getCategoryCount(): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT COUNT(i.id)
             FROM faq_categories AS i
             WHERE i.language = ?',
            array(BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch the feedback item
     *
     * @param int $id
     *
     * @return array
     */
    public static function getFeedback(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.id = ?',
            array($id)
        );
    }

    /**
     * Get the maximum sequence for a category
     *
     * @return int
     */
    public static function getMaximumCategorySequence(): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM faq_categories AS i
             WHERE i.language = ?',
            array(BL::getWorkingLanguage())
        );
    }

    /**
     * Get the max sequence id for a category
     *
     * @param int $id The category id.
     *
     * @return int
     */
    public static function getMaximumSequence(int $id): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM faq_questions AS i
             WHERE i.category_id = ?',
            array($id)
        );
    }

    /**
     * Retrieve the unique URL for an item
     *
     * @param string $url
     * @param int $id The id of the item to ignore.
     *
     * @return string
     */
    public static function getURL(string $url, int $id = null): string
    {
        $url = CommonUri::getUrl((string) $url);
        $db = BackendModel::get('database');

        // new item
        if ($id === null) {
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM faq_questions AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $url)
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getURL($url);
            }
        } else {
            // current category should be excluded
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM faq_questions AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $url, $id)
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getURL($url, $id);
            }
        }

        return $url;
    }

    /**
     * Retrieve the unique URL for a category
     *
     * @param string $url
     * @param int $id The id of the category to ignore.
     *
     * @return string
     */
    public static function getURLForCategory(string $url, int $id = null): string
    {
        $url = CommonUri::getUrl($url);
        $db = BackendModel::get('database');

        // new category
        if ($id === null) {
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM faq_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                array(BL::getWorkingLanguage(), $url)
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getURLForCategory($url);
            }

            return $url;
        }

        // current category should be excluded
        if ((bool) $db->getVar(
            'SELECT 1
                 FROM faq_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
            array(BL::getWorkingLanguage(), $url, $id)
        )
        ) {
            $url = BackendModel::addNumber($url);

            return self::getURLForCategory($url, $id);
        }

        return $url;
    }

    /**
     * Insert a question in the database
     *
     * @param array $item
     *
     * @return int
     */
    public static function insert(array $item): int
    {
        $insertId = BackendModel::getContainer()->get('database')->insert('faq_questions', $item);

        return $insertId;
    }

    /**
     * Insert a category in the database
     *
     * @param array $item
     * @param array $meta The metadata for the category to insert.
     *
     * @return int
     */
    public static function insertCategory(array $item, array $meta = null): int
    {
        $db = BackendModel::get('database');

        // insert the meta if possible
        if ($meta !== null) {
            $item['meta_id'] = $db->insert('meta', $meta);
        }

        // insert extra
        $item['extra_id'] = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Faq',
            'CategoryList'
        );

        $item['id'] = $db->insert('faq_categories', $item);

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            array(
                'id' => $item['id'],
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Category', 'Faq')) . ': ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createURLForAction(
                    'EditCategory',
                    'Faq',
                    $item['language']
                ) . '&id=' . $item['id'],
            )
        );

        return (int) $item['id'];
    }

    /**
     * Update a certain question
     *
     * @param array $item
     */
    public static function update(array $item)
    {
        BackendModel::getContainer()->get('database')->update(
            'faq_questions',
            $item,
            'id = ?',
            array((int) $item['id'])
        );
    }

    /**
     * Update a certain category
     *
     * @param array $item
     */
    public static function updateCategory(array $item)
    {
        // update faq category
        BackendModel::getContainer()->get('database')->update('faq_categories', $item, 'id = ?', array($item['id']));

        // update extra
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            array(
                'id' => $item['id'],
                'extra_label' => 'Category: ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createURLForAction('EditCategory') . '&id=' . $item['id'],
            )
        );
    }
}
