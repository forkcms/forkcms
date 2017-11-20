<?php

namespace Backend\Modules\Faq\Engine;

use Backend\Modules\Faq\Domain\Category\Category;
use Backend\Core\Language\Locale;
use Backend\Modules\Faq\Domain\Question\Question;
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
    const QUERY_DATAGRID_BROWSE =
        'SELECT i.id, i.category_id, i.question, i.hidden, i.sequence
         FROM faq_questions AS i
         WHERE i.locale = ? AND i.category_id = ?
         ORDER BY i.sequence ASC';

    const QUERY_DATAGRID_BROWSE_CATEGORIES =
        'SELECT i.id, i.title, COUNT(p.id) AS num_items, i.sequence
         FROM faq_categories AS i
         LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND p.locale = i.locale
         WHERE i.locale = ?
         GROUP BY i.id
         ORDER BY i.sequence ASC';

    /**
     * Delete a question
     *
     * @param int $id
     */
    public static function delete(int $id): void
    {
        $question = BackendModel::get('faq.repository.question')->find($id);

        $entityManager = BackendModel::getEntityManager();

        BackendTagsModel::saveTags($id, '', 'Faq');
        $entityManager->remove($question);
        $entityManager->flush();
    }

    public static function deleteCategory(int $id): void
    {
        $category = BackendModel::get('faq.repository.category')->find($id);

        if (!$category instanceof Category) {
            return;
        }

        $entityManager = BackendModel::getEntityManager();

        BackendModel::deleteExtraById($category->getExtraId());
        $entityManager->remove($category);
        $entityManager->flush();
    }

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
                 WHERE i.category_id = ? AND i.locale = ?
                 LIMIT 1',
            [$id, BL::getWorkingLanguage()]
        );
    }

    public static function deleteFeedback(int $itemId): void
    {
        BackendModel::getContainer()->get('database')->update(
            'faq_feedback',
            ['processed' => true, 'edited_on' => \SpoonDate::getDate('Y-m-d H:i:s')],
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
             WHERE i.id = ? AND i.locale = ?
             LIMIT 1',
            [$id, BL::getWorkingLanguage()]
        );
    }

    public static function existsCategory(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM faq_categories AS i
             WHERE i.id = ? AND i.locale = ?
             LIMIT 1',
            [$id, BL::getWorkingLanguage()]
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
        $question = BackendModel::get('faq.repository.question')->find($id);

        return $question->toArray();
    }

    public static function getAllFeedback(int $limit = 5): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.processed = ?
             LIMIT ?',
            [false, $limit]
        );
    }

    public static function getAllFeedbackForQuestion(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.question_id = ? AND f.processed = ?',
            [$id, false]
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
             WHERE mt.module = ? AND mt.tag_id = ? AND i.locale = ?',
            ['Faq', $tagId, BL::getWorkingLanguage()]
        );

        foreach ($items as &$row) {
            $row['url'] = BackendModel::createUrlForAction('Edit', 'Faq', null, ['id' => $row['url']]);
        }

        return $items;
    }

    public static function getCategories(bool $includeCount = false): array
    {
        $database = BackendModel::getContainer()->get('database');

        if ($includeCount) {
            return (array) $database->getPairs(
                'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
                 FROM faq_categories AS i
                 LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND i.locale = p.locale
                 WHERE i.locale = ?
                 GROUP BY i.id
                 ORDER BY i.sequence',
                [BL::getWorkingLanguage()]
            );
        }

        return (array) $database->getPairs(
            'SELECT i.id, i.title
             FROM faq_categories AS i
             WHERE i.locale = ?',
            [BL::getWorkingLanguage()]
        );
    }

    public static function getCategory(int $id): array
    {
        $category = BackendModel::get('faq.repository.category')->find($id);

        if (!$category instanceof Category) {
            return [];
        }

        return $category->toArray();
    }

    public static function getCategoryCount(): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT COUNT(i.id)
             FROM faq_categories AS i
             WHERE i.locale = ?',
            [BL::getWorkingLanguage()]
        );
    }

    public static function getFeedback(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT f.*
             FROM faq_feedback AS f
             WHERE f.id = ?',
            [$id]
        );
    }

    public static function getMaximumCategorySequence(): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM faq_categories AS i
             WHERE i.locale = ?',
            [BL::getWorkingLanguage()]
        );
    }

    /**
     * Get the max sequence id for a question belonging to a category
     *
     * @param int $categoryId
     *
     * @return int
     */
    public static function getMaximumSequence(int $categoryId): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM faq_questions AS i
             WHERE i.category_id = ?',
            [$categoryId]
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
    public static function getUrl(string $url, int $id = null): string
    {
        $url = CommonUri::getUrl((string) $url);
        $database = BackendModel::get('database');

        // new item
        if ($id === null) {
            if ((bool) $database->getVar(
                'SELECT 1
                 FROM faq_questions AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.locale = ? AND m.url = ?
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
                 FROM faq_questions AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.locale = ? AND m.url = ? AND i.id != ?
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
     * @param string $url
     * @param int $id The id of the category to ignore.
     *
     * @return string
     */
    public static function getUrlForCategory(string $url, int $id = null): string
    {
        $url = CommonUri::getUrl($url);
        $database = BackendModel::get('database');

        // new category
        if ($id === null) {
            if ((bool) $database->getVar(
                'SELECT 1
                 FROM faq_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.locale = ? AND m.url = ?
                 LIMIT 1',
                [BL::getWorkingLanguage(), $url]
            )
            ) {
                $url = BackendModel::addNumber($url);

                return self::getUrlForCategory($url);
            }

            return $url;
        }

        // current category should be excluded
        if ((bool) $database->getVar(
            'SELECT 1
                 FROM faq_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.locale = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
            [BL::getWorkingLanguage(), $url, $id]
        )
        ) {
            $url = BackendModel::addNumber($url);

            return self::getUrlForCategory($url, $id);
        }

        return $url;
    }

    public static function insert(array $item): int
    {
        $question = new Question(
            Locale::fromString($item['language']),
            $item['meta']->getMetaEntity(),
            BackendModel::get('faq.repository.category')->find($item['category_id']),
            $item['user_id'],
            $item['question'],
            $item['answer'],
            $item['hidden'],
            $item['sequence']
        );

        $entityManager = BackendModel::getEntityManager();
        $entityManager->persist($question);
        $entityManager->flush();

        return $question->getId();
    }

    public static function insertCategory(array $item, array $meta = null): int
    {
        // insert the meta if possible
        if ($meta !== null) {
            $item['meta_id'] = BackendModel::get('database')->insert('meta', $meta);
        }

        // insert extra
        $extraId = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'Faq',
            'CategoryList'
        );

        $category = new Category(
            Locale::fromString($item['language']),
            $item['meta']->getMetaEntity(),
            $item['title'],
            $item['sequence']
        );
        $category->setExtraId($extraId);

        $entityManager = BackendModel::getEntityManager();
        $entityManager->persist($category);
        $entityManager->flush();

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $extraId,
            'data',
            [
                'id' => $category->getId(),
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Category', 'Faq')) . ': ' . $category->getTitle(),
                'language' => $category->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction(
                    'EditCategory',
                    'Faq',
                    $category->getLocale()->getLocale()
                ) . '&id=' . $category->getId(),
            ]
        );

        return $category->getId();
    }

    /**
     * Update a certain question
     *
     * @param array $item
     */
    public static function update(array $item): void
    {
        $question = BackendModel::get('faq.repository.question')->find($item['id']);

        if (!$question instanceof Question) {
            return;
        }

        $question->update(
            BackendModel::get('faq.repository.category')->find($item['category_id']),
            $item['question'],
            $item['answer'],
            $item['hidden']
        );

        BackendModel::getEntityManager()->flush();
    }

    public static function updateCategory(array $item): void
    {
        $category = BackendModel::get('faq.repository.category')->find($item['id']);

        if (!$category instanceof Category) {
            return;
        }

        $category->update($item['title']);

        BackendModel::getEntityManager()->flush();

        // update extra
        BackendModel::updateExtra(
            $category->getExtraId(),
            'data',
            [
                'id' => $category->getId(),
                'extra_label' => 'Category: ' . $category->getTitle(),
                'language' => $category->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction('EditCategory') . '&id=' . $category->getId(),
            ]
        );
    }
}
