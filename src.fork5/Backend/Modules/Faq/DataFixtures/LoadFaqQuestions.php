<?php

namespace Backend\Modules\Faq\DataFixtures;

class LoadFaqQuestions
{
    public const FAQ_QUESTION_TITLE = 'Is this a working test?';
    public const FAQ_QUESTION_SLUG = 'is-this-a-working-test';
    public const FAQ_QUESTION_ID = 1;

    public function load(\SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => self::FAQ_QUESTION_TITLE,
                'description' => self::FAQ_QUESTION_TITLE,
                'title' => self::FAQ_QUESTION_TITLE,
                'url' => self::FAQ_QUESTION_SLUG,
            ]
        );

        $database->insert(
            'FaqQuestion',
            [
                'id' => self::FAQ_QUESTION_ID,
                'meta_id' => $metaId,
                'category_id' => LoadFaqCategories::getCategoryId(),
                'userId' => 1,
                'locale' => 'en',
                'question' => self::FAQ_QUESTION_TITLE,
                'answer' => '<p>I hope so.</p>',
                'createdOn' => '2015-02-23 00:00:00',
                'hidden' => false,
                'sequence' => 1,
                'numberOfViews' => 0,
                'numberOfUsefulYes' => 0,
                'numberOfUsefulNo' => 0,
            ]
        );
    }
}
