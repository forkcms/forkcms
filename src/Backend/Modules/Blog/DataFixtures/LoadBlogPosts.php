<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

class LoadBlogPosts
{
    public const BLOG_POST_TITLE = 'Blogpost for functional tests';
    public const BLOG_POST_SLUG = 'blogpost-for-functional-tests';
    public const BLOG_POST_ID = 1;
    public const BLOG_POST_DATA = [
        'user_id' => 1,
        'language' => 'en',
        'title' => self::BLOG_POST_TITLE,
        'introduction' => '<p>Lorem ipsum dolor sit amet</p>',
        'text' => '<p>Lorem ipsum dolor sit amet</p>',
        'status' => 'active',
        'publish_on' => '2015-02-23 00:00:00',
        'created_on' => '2015-02-23 00:00:00',
        'edited_on' => '2015-02-23 00:00:00',
        'num_comments' => 0,
    ];

    public function load(SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => self::BLOG_POST_TITLE,
                'description' => self::BLOG_POST_TITLE,
                'title' => self::BLOG_POST_TITLE,
                'url' => self::BLOG_POST_SLUG,
            ]
        );

        $categoryId = $database->getVar(
            'SELECT id
             FROM blog_categories
             WHERE title = :title AND locale = :locale
             LIMIT 1',
            [
                'title' => LoadBlogCategories::BLOG_CATEGORY_TITLE,
                'locale' => 'en',
            ]
        );

        $database->insert(
            'blog_posts',
            ['meta_id' => $metaId, 'category_id' => $categoryId, 'id' => self::BLOG_POST_ID] + self::BLOG_POST_DATA
        );

        $database->insert(
            'search_index',
            [
                'module' => 'Blog',
                'other_id' => self::BLOG_POST_ID,
                'field' => 'title',
                'value' => self::BLOG_POST_TITLE,
                'language' => 'en',
                'active' => true,
            ]
        );
    }
}
