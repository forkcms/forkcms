<?php

namespace Frontend\Modules\Faq\Engine;

use Backend\Modules\Faq\Domain\Category\Category;
use Backend\Modules\Faq\Domain\Category\CategoryRepository;
use Backend\Modules\Faq\Domain\Feedback\Feedback;
use Backend\Modules\Faq\Domain\Feedback\FeedbackRepository;
use Backend\Modules\Faq\Domain\Question\Question;
use Backend\Modules\Faq\Domain\Question\QuestionRepository;
use Doctrine\ORM\NoResultException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url as FrontendUrl;
use Frontend\Core\Language\Locale;
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
            return FrontendModel::get(QuestionRepository::class)->findOneByUrl(
                $url,
                Locale::frontendLanguage()
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
        $questions = FrontendModel::get(QuestionRepository::class)->findByCategory(
            FrontendModel::get(CategoryRepository::class)->find($categoryId),
            Locale::frontendLanguage(),
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
        $categories = FrontendModel::get(CategoryRepository::class)->findBy(
            ['locale' => Locale::frontendLanguage()],
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
            return FrontendModel::get(CategoryRepository::class)->findOneByUrl(
                $url,
                Locale::frontendLanguage()
            )->toArray();
        } catch (NoResultException $exception) {
            return [];
        }
    }

    public static function getCategoryById(int $id): array
    {
        return FrontendModel::get(CategoryRepository::class)->findOneBy(
            [
                'id' => $id,
                'locale' => Locale::frontendLanguage()
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

        $questions = FrontendModel::get(QuestionRepository::class)->findMultiple(
            $ids,
            Locale::frontendLanguage()
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
            return FrontendModel::get(QuestionRepository::class)->findOneByUrl(
                $itemUrl,
                Locale::frontendLanguage()
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
            FrontendModel::get(QuestionRepository::class)->findMostRead(
                $limit,
                Locale::frontendLanguage()
            )
        );
    }

    public static function getFaqsForCategory(int $categoryId): array
    {
        $category = FrontendModel::get(CategoryRepository::class)->find($categoryId);
        $link = FrontendNavigation::getUrlForBlock('Faq', 'Detail');

        $questions = FrontendModel::get(QuestionRepository::class)->findByCategory(
            $category,
            Locale::frontendLanguage()
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

        $questions = FrontendModel::get(QuestionRepository::class)->findMultiple(
            $relatedIDs,
            Locale::frontendLanguage()
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
        $question = FrontendModel::get(QuestionRepository::class)->find($questionId);
        $question->increaseViewCount();

        FrontendModel::get('doctrine.orm.default_entity_manager')->flush();
    }

    public static function saveFeedback(array $feedback): void
    {
        $feedback = new Feedback(
            FrontendModel::get(QuestionRepository::class)->find($feedback['question_id']),
            $feedback['text']
        );

        FrontendModel::get(FeedbackRepository::class)->add($feedback);
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

        $questions = FrontendModel::get(QuestionRepository::class)->findMultiple(
            $ids,
            Locale::frontendLanguage()
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

        $question = FrontendModel::get(QuestionRepository::class)->find($id);

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
