<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

class LoadBlogCategories
{
    public function load(SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => 'BlogCategory for tests',
                'description' => 'BlogCategory for tests',
                'title' => 'BlogCategory for tests',
                'url' => 'blogcategory-for-tests',
            ]
        );

        $database->insert(
            'blog_categories',
            [
                'meta_id' => $metaId,
                'language' => 'en',
                'title' => 'BlogCategory for tests',
            ]
        );
    }
}
