<?php

namespace Backend\Modules\Faq\DataFixtures;

class LoadFaqCategories
{
    public const FAQ_CATEGORY_TITLE = 'Blog Category for tests';
    public const FAQ_CATEGORY_SLUG = 'blog-category-for-tests';

    public const FAQ_CATEGORY_DATA = [
        'locale' => 'en',
        'title' => self::FAQ_CATEGORY_TITLE,
        'sequence' => 1,
    ];
    public const FAQ_CATEGORY_META_DATA = [
        'keywords' => self::FAQ_CATEGORY_TITLE,
        'description' => self::FAQ_CATEGORY_TITLE,
        'title' => self::FAQ_CATEGORY_TITLE,
        'url' => self::FAQ_CATEGORY_SLUG,
    ];

    /** @var int|null */
    private static $metaId;

    /** @var int|null */
    private static $categoryId;

    public function load(\SpoonDatabase $database): void
    {
        self::$metaId = $database->insert(
            'meta',
            self::FAQ_CATEGORY_META_DATA
        );

        $extraId = $database->insert(
            'PagesModuleExtra',
            [
                'module' => 'Faq',
                'type' => 'widget',
                'label' => 'CategoryList',
                'action' => 'CategoryList',
                'data' => null,
                'hidden' => 0,
                'sequence' => 5012,
            ]
        );

        self::$categoryId = $database->insert(
            'FaqCategory',
            ['meta_id' => self::$metaId, 'extraId' => $extraId] + self::FAQ_CATEGORY_DATA
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
