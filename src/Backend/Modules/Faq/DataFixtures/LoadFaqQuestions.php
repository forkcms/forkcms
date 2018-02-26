<?php

namespace ForkCMS\Backend\Modules\Faq\DataFixtures;

class LoadFaqQuestions
{
    public function load(\SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => 'Is this a working test?',
                'description' => 'Is this a working test?',
                'title' => 'Is this a working test?',
                'url' => 'is-this-a-working-test',
            ]
        );

        $categoryId = $database->getVar(
            'SELECT id
             FROM FaqCategory
             WHERE title = :title AND locale = :locale
             LIMIT 1',
            [
                'title' => 'Faq for tests',
                'locale' => 'en',
            ]
        );

        $database->insert(
            'FaqQuestion',
            [
                'meta_id' => $metaId,
                'category_id' => $categoryId,
                'userId' => 1,
                'locale' => 'en',
                'question' => 'Is this a working test?',
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
