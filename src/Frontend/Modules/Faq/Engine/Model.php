<?php

namespace Frontend\Modules\Faq\Engine;

use Backend\Modules\Faq\Domain\Category\Category;
use Backend\Modules\Faq\Domain\Feedback\Feedback;
use Backend\Modules\Faq\Domain\Question\Question;
use Doctrine\ORM\NoResultException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendUrl;
use App\Component\Locale\FrontendLocale;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

/**
 * In this file we store all generic functions that we will be using in the faq module
 */
class Model implements FrontendTagsInterface
{
    public static function get(string $url): array
    {
        try {
            return FrontendModel::get('faq.repository.question')->findOneByUrl(
                $url,
                FrontendLocale::frontendLanguage()
            )->toArray();
        } catch (NoResultException $exception) {
            return [];
        }
    }

    /**
     * @param int $categoryId
     * @param int $limit
     * @param int|int[] $excludeIds
     *
     * @return array
     */
    public static function getAllForCategory(int $categoryId, int $limit = null, array $excludeIds = array()): array
    {
        $questions = FrontendModel::get('faq.repository.question')->findByCategory(
            FrontendModel::get('faq.repository.category')->find($categoryId),
            FrontendLocale::frontendLanguage(),
            $limit,
            $excludeIds
        );
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        return array_map(
            function (Question $question) use ($link) {
                $questionArray = $question->toArray();
                $questionArray['full_url'] = $link . '/' . $question->getMeta()->getUrl();

                return $questionArray;
            },
            $questions
        );
    }

    public static function getCategories(): array
    {
        $categories = FrontendModel::get('faq.repository.category')->findBy(
            ['locale' => FrontendLocale::frontendLanguage()],
            ['sequence' => 'DESC']
        );
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Category');

        return array_map(
            function (Category $category) use ($link) {
                $categoryArray = $category->toArray();
                $categoryArray['full_url'] = $link . '/' . $category->getMeta()->getUrl();

                return $categoryArray;
            },
            $categories
        );
    }

    public static function getCategory(string $url): array
    {
        try {
            return FrontendModel::get('faq.repository.category')->findOneByUrl(
                $url,
                FrontendLocale::frontendLanguage()
            )->toArray();
        } catch (NoResultException $exception) {
            return [];
        }
    }

    public static function getCategoryById(int $id): array
    {
        return FrontendModel::get('faq.repository.category')->findOne(
            [
                'id' => $id,
                'locale' => FrontendLocale::frontendLanguage()
            ]
        )->toArray();
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
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        $questions = FrontendModel::get('faq.repository.question')->findMultiple(
            $ids,
            FrontendLocale::frontendLanguage()
        );

        return array_map(
            function (Question $question) use ($link) {
                $questionArray = $question->toArray();
                $questionArray['full_url'] = $link . '/' . $question->getMeta()->getUrl();

                return $questionArray;
            },
            $questions
        );
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

        try {
            return FrontendModel::get('faq.repository.question')->findOneByUrl(
                $itemUrl,
                FrontendLocale::frontendLanguage()
            )->getId();
        } catch (NoResultException $exception) {
            return 0;
        }
    }

    public static function getMostRead(int $limit): array
    {
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        return array_map(
            function (Question $question) use ($link) {
                $questionArray = $question->toArray();
                $questionArray['full_url'] = $link . '/' . $question->getMeta()->getUrl();

                return $questionArray;
            },
            FrontendModel::get('faq.repository.question')->findMostRead(
                $limit,
                FrontendLocale::frontendLanguage()
            )
        );
    }

    public static function getFaqsForCategory(int $categoryId): array
    {
        $category = FrontendModel::get('faq.repository.category')->find($categoryId);
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        $questions = FrontendModel::get('faq.repository.question')->findByCategory(
            $category,
            FrontendLocale::frontendLanguage()
        );

        return array_map(
            function (Question $question) use ($link) {
                $questionArray = $question->toArray();
                $questionArray['full_url'] = $link . '/' . $question->getMeta()->getUrl();

                return $questionArray;
            },
            $questions
        );
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
        $relatedIDs = FrontendTagsModel::getRelatedItemsByTags($questionId, 'Faq', 'Faq');

        // there are no items, so return an empty array
        if (empty($relatedIDs)) {
            return [];
        }

        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        $questions = FrontendModel::get('faq.repository.question')->findMultiple(
            $relatedIDs,
            FrontendLocale::frontendLanguage()
        );

        return array_map(
            function (Question $question) use ($link) {
                $questionArray = $question->toArray();
                $questionArray['title'] = $question->getQuestion();
                $questionArray['full_url'] = $link . '/' . $question->getMeta()->getUrl();

                return $questionArray;
            },
            $questions
        );
    }

    public static function increaseViewCount(int $questionId): void
    {
        $question = FrontendModel::get('faq.repository.question')->find($questionId);
        $question->increaseViewCount();

        FrontendModel::get('doctrine.orm.default_entity_manager')->flush();
    }

    public static function saveFeedback(array $feedback): void
    {
        $feedback = new Feedback(
            FrontendModel::get('faq.repository.question')->find($feedback['question_id']),
            $feedback['text']
        );

        FrontendModel::get('faq.repository.feedback')->add($feedback);
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
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        $questions = FrontendModel::get('faq.repository.question')->findMultiple(
            $ids,
            FrontendLocale::frontendLanguage()
        );
        $questions = array_map(
            function (Question $question) use ($link) {
                $questionArray = $question->toArray();
                $questionArray['full_url'] = $link . '/' . $question->getMeta()->getUrl();

                return $questionArray;
            },
            $questions
        );

        // make sure the search array is indexed by id
        $questionsArray = [];
        foreach ($questions as $question) {
            $questionsArray[$question['id']] = $question;
        }

        return $questionsArray;
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

        $question = FrontendModel::get('faq.repository.question')->find($id);

        // update counter with current feedback (increase)
        if ($useful) {
            $question->increaseUsefulYesCount();
        } else {
            $question->increaseUsefulNoCount();
        }

        // update counter with previous feedback (decrease)
        if ($previousFeedback) {
            $question->decreaseUsefulYesCount();
        } elseif ($previousFeedback !== null) {
            $question->decreaseUsefulNoCount();
        }

        FrontendModel::get('doctrine.orm.default_entity_manager')->flush();
    }
}
