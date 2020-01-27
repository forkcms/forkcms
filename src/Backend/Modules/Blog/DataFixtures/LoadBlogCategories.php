<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

final class LoadBlogCategories
{
    public const BLOG_CATEGORY_TITLE = 'BlogCategory for tests';
    public const BLOG_CATEGORY_SLUG = 'BlogCategory for tests';
    public const BLOG_CATEGORY_ID = 2;
    public const BLOG_CATEGORY_DATA = [
        'language' => 'en',
        'title' => self::BLOG_CATEGORY_TITLE,
    ];

    public function load(SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => self::BLOG_CATEGORY_TITLE,
                'description' => self::BLOG_CATEGORY_TITLE,
                'title' => self::BLOG_CATEGORY_TITLE,
                'url' => self::BLOG_CATEGORY_SLUG,
            ]
        );

        $database->insert(
            'blog_categories',
            ['meta_id' => $metaId, 'id' => self::BLOG_CATEGORY_ID] + self::BLOG_CATEGORY_DATA
        );
    }
}
