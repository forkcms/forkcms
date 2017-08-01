<?php

namespace Backend\Modules\Faq\DataFixtures;

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
             FROM faq_categories
             WHERE title = :title AND language = :language
             LIMIT 1',
            [
                'title' => 'Faq for tests',
                'language' => 'en',
            ]
        );

        $database->insert(
            'faq_questions',
            [
                'meta_id' => $metaId,
                'category_id' => $categoryId,
                'user_id' => 1,
                'language' => 'en',
                'question' => 'Is this a working test?',
                'answer' => '<p>I hope so.</p>',
                'created_on' => '2015-02-23 00:00:00',
                'hidden' => false,
                'sequence' => 1,
            ]
        );
    }
}
