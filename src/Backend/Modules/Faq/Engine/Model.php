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
use Backend\Modules\Faq\Entity\Category;
use Backend\Modules\Faq\Entity\Question;

/**
 * In this file we store all generic functions that we will be using in the faq module
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class Model
{
    const CATEGORY_ENTITY_CLASS = 'Backend\Modules\Faq\Entity\Category';
    const QUESTION_ENTITY_CLASS = 'Backend\Modules\Faq\Entity\Question';
    const FEEDBACK_ENTITY_CLASS = 'Backend\Modules\Faq\Entity\Feedback';

    /**
     * Delete a question
     *
     * @param Question $question
     */
    public static function delete(Question $question)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->remove($question);
        $em->flush();

        BackendTagsModel::saveTags($question->getId(), '', 'Faq');
    }

    /**
     * Delete a specific category
     *
     * @param Category $category
     */
    public static function deleteCategory(Category $category)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');

        if (!empty($category)) {
            BackendModel::deleteExtraById($category->getExtraId());

            $em->remove($category);
            $em->flush();

            BackendModel::invalidateFrontendCache('Faq', $category->getLanguage());
        }
    }

    /**
     * Is the deletion of a category allowed?
     *
     * @param  Category $category
     * @return bool
     */
    public static function deleteCategoryAllowed(Category $category)
    {
        if (
            !BackendModel::getModuleSetting('Faq', 'allow_multiple_categories', true) &&
            self::getCategoryCount() == 1
        ) {
            return false;
        } else {
            // check if the category does not contain questions
            return count($category->getQuestions()) === 0;
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
            array('processed' => 'Y', 'edited_on' => \SpoonDate::getDate('Y-m-d H:i:s')),
            'id = ?',
            (int) $itemId
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
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository(self::QUESTION_ENTITY_CLASS)
            ->findOneBy(
                array(
                    'id'       => $id,
                    'language' => BL::getWorkingLanguage(),
                )
            )
        ;
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
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $categories = $em
            ->getRepository(self::CATEGORY_ENTITY_CLASS)
            ->findByLanguage(BL::getWorkingLanguage())
        ;

        $pairs = array();
        foreach ($categories as $category) {
            $pairs[$category->getId()] = $category->getTitle();

            if ($includeCount) {
                $pairs[$category->getId()] .= ' (' . count($category->getQuestions()) . ')';
            }
        }

        return $pairs;
    }

    /**
     * Fetch a category
     *
     * @param int $id
     * @return array
     */
    public static function getCategory($id)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository(self::CATEGORY_ENTITY_CLASS)
            ->findOneBy(
                array(
                    'id'       => $id,
                    'language' => BL::getWorkingLanguage(),
                )
            )
        ;
    }

    /**
     * Fetch the category count
     *
     * @return int
     */
    public static function getCategoryCount()
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        return (int) $em
            ->getRepository(self::CATEGORY_ENTITY_CLASS)
            ->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->where('i.language = :language')
            ->setParameter('language', BL::getWorkingLanguage())
            ->getQuery()
            ->getSingleScalarResult()
        ;
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
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $maxCategory = $em
            ->getRepository(self::CATEGORY_ENTITY_CLASS)
            ->findOneBy(
                array('language' => BL::getWorkingLanguage()),
                array('sequence' => 'DESC')
            )
        ;

        return empty($maxCategory) ? 0 : $maxCategory->getSequence();
    }

    /**
     * Get the max sequence id for a category
     *
     * @param int $id The category id.
     * @return int
     */
    public static function getMaximumSequence($id)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $maxQuestion = $em
            ->getRepository(self::QUESTION_ENTITY_CLASS)
            ->findOneBy(
                array('category' => self::getCategory($id)),
                array('sequence' => 'DESC')
            )
        ;

        return empty($maxQuestion) ? 0 : $maxQuestion->getSequence();
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
        $em = BackendModel::get('doctrine.orm.entity_manager');

        $items = $em
            ->getRepository(self::QUESTION_ENTITY_CLASS)
            ->findByUrl($url, BL::getWorkingLanguage(), $id)
        ;
        if (count($items) !== 0) {
            $url = BackendModel::addNumber($url);

            return self::getURL($url);
        }

        return $url;
    }

    /**
     * Retrieve the unique URL for a category
     *
     * @todo  Rework this method to use doctrine
     * @param string $url
     * @param int    $id The id of the category to ignore.
     * @return string
     */
    public static function getURLForCategory($url, $id = null)
    {
        $url = CommonUri::getUrl((string) $url);
        $em = BackendModel::get('doctrine.orm.entity_manager');

        $items = $em
            ->getRepository(self::CATEGORY_ENTITY_CLASS)
            ->findByUrl($url, BL::getWorkingLanguage(), $id)
        ;
        if (count($items) !== 0) {
            $url = BackendModel::addNumber($url);

            return self::getURL($url);
        }

        return $url;
    }

    /**
     * Insert a question in the database
     *
     * @param Question $question
     * @return int
     */
    public static function insert(Question $question)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($question);
        $em->flush();

        BackendModel::invalidateFrontendCache('Faq', $question->getLanguage());

        return $question->getid();
    }

    /**
     * Insert a category in the database
     *
     * @param array $category
     * @return int
     */
    public static function insertCategory(Category $category)
    {
        // insert extra
        $category->setExtraId(BackendModel::insertExtra(
            'widget',
            'Faq',
            'CategoryList'
        ));

        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($category);
        $em->flush();

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $category->getExtraId(),
            'data',
            array(
                'id' => $category->getId(),
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Category', 'Faq')) . ': ' . $category->getTitle(),
                'language' => $category->getLanguage(),
                'edit_url' => BackendModel::createURLForAction(
                    'EditCategory',
                    'Faq',
                    $category->getLanguage()
                ) . '&id=' . $category->getId()
            )
        );

        BackendModel::invalidateFrontendCache('Faq', $category->getLanguage());

        return $category->getId();
    }

    /**
     * Update a certain question
     *
     * @param Question $question
     */
    public static function update(Question $question)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->flush();

        BackendModel::invalidateFrontendCache('Faq', BL::getWorkingLanguage());
    }

    /**
     * Update a certain category
     *
     * @param array $item
     */
    public static function updateCategory(Category $category)
    {
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->flush();

        // update extra
        BackendModel::updateExtra(
            $category->getExtraId(),
            'data',
            array(
                'id' => $category->getId(),
                'extra_label' => 'Category: ' . $category->getTitle(),
                'language' => $category->getLanguage(),
                'edit_url' => BackendModel::createURLForAction('EditCategory') . '&id=' . $category->getId()
            )
        );

        // invalidate faq
        BackendModel::invalidateFrontendCache('Faq', $category->getLanguage());
    }
}
