<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

class LoadBlogPosts
{
    public const BLOG_POST_TITLE = 'Blogpost for functional tests';
    public const BLOG_POST_ID = 1;

    public function load(SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => 'Blogpost for functional tests',
                'description' => 'Blogpost for functional tests',
                'title' => 'Blogpost for functional tests',
                'url' => 'blogpost-for-functional-tests',
            ]
        );

        $categoryId = $database->getVar(
            'SELECT id
             FROM blog_categories
             WHERE title = :title AND language = :language
             LIMIT 1',
            [
                'title' => 'BlogCategory for tests',
                'language' => 'en',
            ]
        );

        $database->insert(
            'blog_posts',
            [
                'id' => self::BLOG_POST_ID,
                'meta_id' => $metaId,
                'category_id' => $categoryId,
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
            ]
        );

        $database->insert(
            'search_index',
            [
                'module' => 'Blog',
                'other_id' => 1,
                'field' => 'title',
                'value' => 'Blogpost for functional tests',
                'language' => 'en',
                'active' => true,
            ]
        );
    }
}
