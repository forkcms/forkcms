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

        self::$categoryId = $database->insert(
            'blog_categories',
            ['meta_id' => self::$metaId, 'id' => self::BLOG_CATEGORY_ID] + self::BLOG_CATEGORY_DATA
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
