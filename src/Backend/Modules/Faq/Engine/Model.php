<?php

namespace Backend\Modules\Faq\Engine;

use Backend\Modules\Faq\Domain\Category\Category;
use Backend\Core\Language\Locale;
use Backend\Modules\Faq\Domain\Feedback\Feedback;
use Backend\Modules\Faq\Domain\Question\Question;
use App\Domain\ModuleExtra\Type;
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
         FROM FaqQuestion AS i
         WHERE i.locale = ? AND i.category_id = ?
         ORDER BY i.sequence ASC';

    const QUERY_DATAGRID_BROWSE_CATEGORIES =
        'SELECT i.id, i.title, COUNT(p.id) AS num_items, i.sequence
         FROM FaqCategory AS i
         LEFT OUTER JOIN FaqQuestion AS p ON i.id = p.category_id AND p.locale = i.locale
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

        BackendTagsModel::saveTags($id, '', 'Faq');
        BackendModel::get('faq.repository.category')->remove($question);
    }

    public static function deleteCategory(int $id): void
    {
        $category = BackendModel::get('faq.repository.category')->find($id);

        if (!$category instanceof Category) {
            return;
        }

        BackendModel::deleteExtraById($category->getExtraId());
        BackendModel::get('faq.repository.category')->remove($category);
    }

    public static function deleteCategoryAllowed(int $id): bool
    {
        if (!BackendModel::get('fork.settings')->get('Faq', 'allow_multiple_categories', true)
            && self::getCategoryCount() == 1
        ) {
            return false;
        }

        $category = BackendModel::get('faq.repository.category')->find($id);

        return $category->getQuestions()->isEmpty();
    }

    public static function deleteFeedback(int $itemId): void
    {
        $feedback = BackendModel::get('faq.repository.feedback')->find($itemId);
        $feedback->process();

        BackendModel::get('doctrine.orm.default_entity_manager')->flush();
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
        $question = BackendModel::get('faq.repository.question')->findOneBy(
            [
                'id' => $id,
                'locale' => Locale::workingLocale(),
            ]
        );

        return $question instanceof Question;
    }

    public static function existsCategory(int $id): bool
    {
        $category = BackendModel::get('faq.repository.category')->findOneBy(
            [
                'id' => $id,
                'locale' => Locale::workingLocale(),
            ]
        );

        return $category instanceof Category;
    }

    public static function get(int $id): array
    {
        $question = BackendModel::get('faq.repository.question')->find($id);

        return $question->toArray();
    }

    public static function getAllFeedback(int $limit = 5): array
    {
        $feedback = BackendModel::get('faq.repository.feedback')->findAllForWidget($limit);

        return array_map(
            function (Feedback $feedback) {
                return $feedback->toArray();
            },
            $feedback
        );
    }

    public static function getAllFeedbackForQuestion(int $id): array
    {
        $question = BackendModel::get('faq.repository.question')->find($id);

        return $question
            ->getFeedbackItems()
            ->filter(function (Feedback $feedback) {
                return !$feedback->isProcessed();
            })
            ->map(function (Feedback $feedback) {
                return $feedback->toArray();
            })
            ->toArray()
        ;
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
        $questionIds = (array) BackendModel::get('database')->getColumn(
            'SELECT other_id
             FROM modules_tags AS t
             WHERE t.tag_id = :tagId
             AND t.module = :module',
            [
                'tagId' => $tagId,
                'module' => 'Faq',
            ]
        );

        $questions = BackendModel::get('faq.repository.question')->findMultiple(
            $questionIds,
            Locale::workingLocale()
        );

        return array_map(
            function (Question $question) {
                $questionArray = $question->toArray();
                $questionArray['url'] = BackendModel::createUrlForAction(
                    'Edit',
                    'Faq',
                    null,
                    ['id' => $question->getMeta()->getUrl()]
                );
                $questionArray['name'] = $question->getQuestion();
                $questionArray['module'] = 'Faq';

                return $questionArray;
            },
            $questions
        );
    }

    public static function getCategories(bool $includeCount = false): array
    {
        $categories = BackendModel::get('faq.repository.category')->findBy(
            ['locale' => Locale::workingLocale()],
            ['sequence' => 'DESC']
        );

        $categoriesArray = [];
        foreach ($categories as $category) {
            $title = $category->getTitle();
            if ($includeCount) {
                $title .= ' (' . $category->getQuestions()->count() . ')';
            }

            $categoriesArray[$category->getId()] = $title;
        }

        return $categoriesArray;
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
        return BackendModel::get('faq.repository.category')->findCount(Locale::workingLocale());
    }

    public static function getFeedback(int $id): array
    {
        return BackendModel::get('faq.repository.feedback')->find($id)->toArray();
    }

    public static function getMaximumCategorySequence(): int
    {
        return BackendModel::get('faq.repository.category')->findMaximumSequence(
            Locale::workingLocale()
        );
    }

    public static function getMaximumSequence(int $categoryId): int
    {
        $category = BackendModel::get('faq.repository.category')->find($categoryId);

        return BackendModel::get('faq.repository.question')->findMaximumSequence(
            $category,
            Locale::workingLocale()
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
        $url = CommonUri::getUrl($url);

        return BackendModel::get('faq.repository.question')->getUrl(
            $url,
            Locale::workingLocale(),
            $id
        );
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

        return BackendModel::get('faq.repository.category')->getUrl(
            $url,
            Locale::workingLocale(),
            $id
        );
    }

    public static function insert(array $item): int
    {
        $question = new Question(
            Locale::fromString($item['language']),
            BackendModel::get('fork.repository.meta')->find($item['meta_id']),
            BackendModel::get('faq.repository.category')->find($item['category_id']),
            $item['user_id'],
            $item['question'],
            $item['answer'],
            $item['hidden'],
            $item['sequence']
        );

        BackendModel::get('faq.repository.question')->add($question);

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
            Type::widget(),
            'Faq',
            'CategoryList'
        );

        $category = new Category(
            Locale::fromString($item['language']),
            BackendModel::get('fork.repository.meta')->find($item['meta_id']),
            $item['title'],
            $item['sequence']
        );
        $category->setExtraId($extraId);

        BackendModel::get('faq.repository.category')->add($category);

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

    public static function update(array $item): void
    {
        $question = BackendModel::get('faq.repository.question')->find($item['id']);

        if (!$question instanceof Question) {
            return;
        }

        $questionData = $question->getQuestion();
        if (!array_key_exists('question', $item)) {
            $questionData = $item['question'];
        }
        $answer = $question->getAnswer();
        if (!array_key_exists('answer', $item)) {
            $answer = $item['answer'];
        }
        $hidden = $question->isHidden();
        if (!array_key_exists('hidden', $item)) {
            $hidden = $item['hidden'];
        }
        $sequence = $question->getSequence();
        if (!array_key_exists('sequence', $item)) {
            $sequence = $item['sequence'];
        }

        $question->update(
            BackendModel::get('faq.repository.category')->find($item['category_id']),
            $questionData,
            $answer,
            $hidden,
            $sequence
        );

        BackendModel::get('doctrine.orm.default_entity_manager')->flush();
    }

    public static function updateCategory(array $item): void
    {
        $category = BackendModel::get('faq.repository.category')->find($item['id']);

        if (!$category instanceof Category) {
            return;
        }

        $sequence = $category->getSequence();
        if (array_key_exists('sequence', $item)) {
            $sequence = $item['sequence'];
        }

        $category->update($item['title'], $sequence);

        BackendModel::get('doctrine.orm.default_entity_manager')->flush();

        // update extra
        BackendModel::updateExtra(
            $category->getExtraId(),
            'data',
            [
                'id' => $category->getId(),
                'extra_label' => 'Category: ' . $category->getTitle(),
                'language' => $category->getLocale()->getLocale(),
                'edit_url' => BackendModel::createUrlForAction('EditCategory', 'Faq') . '&id=' . $category->getId(),
            ]
        );
    }
}
