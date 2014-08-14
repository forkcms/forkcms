<?php

namespace Backend\Modules\Faq\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * In this file we store all generic functions that we will be using in the faq module
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
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
    public static function delete($id)
    {
        $question = self::get($id);

        /** @var $db \SpoonDatabase */
        $db = BackendModel::getContainer()->get('database');
        $db->delete('faq_questions', 'id = ?', array((int) $id));
        $db->delete('meta', 'id = ?', array((int) $question['meta_id']));

        BackendTagsModel::saveTags($id, '', 'Faq');
    }

    /**
     * Delete a specific category
     *
     * @param int $id
     */
    public static function deleteCategory($id)
    {
        $db = BackendModel::getContainer()->get('database');
        $item = self::getCategory($id);

        if (!empty($item)) {
            $db->delete('meta', 'id = ?', array($item['meta_id']));
            $db->delete('faq_categories', 'id = ?', array((int) $id));
            $db->update('faq_questions', array('category_id' => null), 'category_id = ?', array((int) $id));

            BackendModel::deleteExtraById($item['extra_id']);
            BackendModel::invalidateFrontendCache('Faq', BL::getWorkingLanguage());
        }
    }

    /**
     * Is the deletion of a category allowed?
     *
     * @param int $id
     * @return bool
     */
    public static function deleteCategoryAllowed($id)
    {
        if (
            !BackendModel::getModuleSetting('Faq', 'allow_multiple_categories', true) &&
            self::getCategoryCount() == 1
        ) {
            return false;
        } else {
            // check if the category contains questions
            return (bool) BackendModel::get('database')->getVar(
                'SELECT 1
                 FROM faq_questions AS i
                 WHERE i.category_id = ? AND i.language = ?
                 LIMIT 1',
                array((int) $id, BL::getWorkingLanguage())
            );
        }
    }

    /**
     * Delete the feedback
     *
     * @param int $itemId
     */
    public static function deleteFeedback($itemId)
    {
        BackendModel::getContainer()->get('database')->update(
            'faq_feedback',
            array('processed' => 'Y'),
            'id = ?',
            (int) $itemId
        );
    }

    /**
     * Does the question exist?
     *
     * @param int $id
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM faq_questions AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Does the category exist?
     *
     * @param int $id
     * @return bool
     */
    public static function existsCategory($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM faq_categories AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch a question
     *
     * @param int $id
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, m.url
             FROM faq_questions AS i
             INNER JOIN meta AS m ON m.id = i.meta_id
             WHERE i.id = ? AND i.language = ?',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetches all the feedback that is available
     *
     * @param int $limit
     * @return array
     */
    public static function getAllFeedback($limit = 5)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.processed = ?
             LIMIT ?',
            array('N', (int) $limit)
        );
    }

    /**
     * Fetches all the feedback for a question
     *
     * @param int $id The question id.
     * @return array
     */
    public static function getAllFeedbackForQuestion($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.question_id = ? AND f.processed = ?',
            array((int) $id, 'N')
        );
    }

    /**
     * Get all items by a given tag id
     *
     * @param int $tagId
     * @return array
     */
    public static function getByTag($tagId)
    {
        $items = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id AS url, i.question AS name, mt.module
             FROM modules_tags AS mt
             INNER JOIN tags AS t ON mt.tag_id = t.id
             INNER JOIN faq_questions AS i ON mt.other_id = i.id
             WHERE mt.module = ? AND mt.tag_id = ? AND i.language = ?',
            array('Faq', (int) $tagId, BL::getWorkingLanguage())
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
     * @return array
     */
    public static function getCategories($includeCount = false)
    {
        $db = BackendModel::getContainer()->get('database');

        if ($includeCount) {
            return (array) $db->getPairs(
                'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
                 FROM faq_categories AS i
                 LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND i.language = p.language
                 WHERE i.language = ?
                 GROUP BY i.id',
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
     * @return array
     */
    public static function getCategory($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*
             FROM faq_categories AS i
             WHERE i.id = ? AND i.language = ?',
            array((int) $id, BL::getWorkingLanguage())
        );
    }

    /**
     * Fetch the category count
     *
     * @return int
     */
    public static function getCategoryCount()
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
     * @return array
     */
    public static function getFeedback($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.id = ?',
            array((int) $id)
        );
    }

    /**
     * Get the maximum sequence for a category
     *
     * @return int
     */
    public static function getMaximumCategorySequence()
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
     * @return int
     */
    public static function getMaximumSequence($id)
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM faq_questions AS i
             WHERE i.category_id = ?',
            array((int) $id)
        );
    }

    /**
     * Retrieve the unique URL for an item
     *
     * @param string $url
     * @param int    $id The id of the item to ignore.
     * @return string
     */
    public static function getURL($url, $id = null)
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
     * @param int    $id The id of the category to ignore.
     * @return string
     */
    public static function getURLForCategory($url, $id = null)
    {
        $url = CommonUri::getUrl((string) $url);
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
        } else {
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
        }

        return $url;
    }

    /**
     * Insert a question in the database
     *
     * @param array $item
     * @return int
     */
    public static function insert(array $item)
    {
        $insertId = BackendModel::getContainer()->get('database')->insert('faq_questions', $item);

        BackendModel::invalidateFrontendCache('Faq', BL::getWorkingLanguage());

        return $insertId;
    }

    /**
     * Insert a category in the database
     *
     * @param array $item
     * @param array $meta The metadata for the category to insert.
     * @return int
     */
    public static function insertCategory(array $item, $meta = null)
    {
        $db = BackendModel::get('database');

        // insert the meta if possible
        if ($meta !== null) {
            $item['meta_id'] = $db->insert('meta', $meta);
        }

        // insert extra
        $item['extra_id'] = BackendModel::insertExtra(
            'widget',
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
                ) . '&id=' . $item['id']
            )
        );

        BackendModel::invalidateFrontendCache('Faq', BL::getWorkingLanguage());

        return $item['id'];
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
        BackendModel::invalidateFrontendCache('Faq', BL::getWorkingLanguage());
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
                'edit_url' => BackendModel::createURLForAction('EditCategory') . '&id=' . $item['id']
            )
        );

        // invalidate faq
        BackendModel::invalidateFrontendCache('Faq', BL::getWorkingLanguage());
    }
}
