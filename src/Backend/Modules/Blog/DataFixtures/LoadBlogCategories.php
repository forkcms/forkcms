<?php

namespace Backend\Modules\Blog\DataFixtures;

class LoadBlogCategories
{
    public function load(\SpoonDatabase $database)
    {
        $metaId = $database->insert(
            'meta',
            array(
                'keywords' => 'Default',
                'description' => 'Default',
                'title' => 'Default',
                'url' => 'default',
            )
        );

        $database->insert(
            'blog_categories',
            array(
                'meta_id' => $metaId,
                'language' => 'en',
                'title' => 'Default',
            )
        );
    }
}
