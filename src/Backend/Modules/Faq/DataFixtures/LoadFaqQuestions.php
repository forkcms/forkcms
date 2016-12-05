<?php

namespace Backend\Modules\Faq\DataFixtures;

class LoadFaqQuestions
{
    /**
     * @param \SpoonDatabase $database
     */
    public function load(\SpoonDatabase $database)
    {
        $metaId = $database->insert(
            'meta',
            array(
                'keywords' => 'Is this a working test?',
                'description' => 'Is this a working test?',
                'title' => 'Is this a working test?',
                'url' => 'is-this-a-working-test',
            )
        );

        $categoryId = $database->getVar(
            'SELECT id
             FROM faq_categories
             WHERE title = :title AND language = :language
             LIMIT 1',
            array(
                'title' => 'Faq for tests',
                'language' => 'en',
            )
        );

        $database->insert(
            'faq_questions',
            array(
                'meta_id' => $metaId,
                'category_id' => $categoryId,
                'user_id' => 1,
                'language' => 'en',
                'question' => 'Is this a working test?',
                'answer' => '<p>I hope so.</p>',
                'created_on' => '2015-02-23 00:00:00',
                'hidden' => 'N',
                'sequence' => 1,
            )
        );
    }
}
