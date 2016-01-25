<?php

namespace Backend\Modules\Faq\DataFixtures;

class LoadFaqCategories
{
    public function load(\SpoonDatabase $database)
    {
        $metaId = $database->insert(
            'meta',
            array(
                'keywords' => 'Faq for tests',
                'description' => 'Faq for tests',
                'title' => 'Faq for tests',
                'url' => 'faqcategory-for-tests',
            )
        );

        $database->insert(
            'faq_categories',
            array(
                'meta_id' => $metaId,
                'extra_id' => 0,
                'language' => 'en',
                'title' => 'Faq for tests',
                'sequence' => 1,
            )
        );
    }
}
