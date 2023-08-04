<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

final class LoadBlogCategories
{
    public const BLOG_CATEGORY_TITLE = 'Blog Category for tests';
    public const BLOG_CATEGORY_SLUG = 'blog-category-for-tests';
    public const BLOG_CATEGORY_DATA = [
        'locale' => 'en',
        'title' => self::BLOG_CATEGORY_TITLE,
    ];
    public const BLOG_CATEGORY_META_DATA = [
        'keywords' => self::BLOG_CATEGORY_TITLE,
        'description' => self::BLOG_CATEGORY_TITLE,
        'title' => self::BLOG_CATEGORY_TITLE,
        'url' => self::BLOG_CATEGORY_SLUG,
    ];

    /** @var int|null */
    private static $metaId;

    /** @var int|null */
    private static $categoryId;

    public function load(SpoonDatabase $database): void
    {
        self::$metaId = $database->insert(
            'meta',
            self::BLOG_CATEGORY_META_DATA
        );

        $database->insert(
            'blog_categories',
            ['meta_id' => 28, 'id' => 1, 'locale' => 'en', 'title' => 'Default']
        );

        self::$categoryId = $database->insert(
            'blog_categories',
            ['meta_id' => self::$metaId] + self::BLOG_CATEGORY_DATA
        );
    }

    public static function getMetaId(): ?int
    {
        return self::$metaId;
    }

    public static function getCategoryId(): ?int
    {
        return self::$categoryId;
    }
}
