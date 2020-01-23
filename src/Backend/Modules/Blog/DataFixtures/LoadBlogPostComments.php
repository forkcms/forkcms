<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

class LoadBlogPostComments
{
    public const BLOG_POST_TITLE = 'Blogpost for functional tests';
    public const BLOG_POST_ID = 1;
    public const BLOG_POST_COMMENT_ID = 1;

    public function load(SpoonDatabase $database): void
    {
        $metaId = $database->insert(
            'meta',
            [
                'keywords' => self::BLOG_POST_TITLE,
                'description' => self::BLOG_POST_TITLE,
                'title' => self::BLOG_POST_TITLE,
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
                'num_comments' => 1,
            ]
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

        $database->insert(
            'blog_comments',
            [
                'id' => self::BLOG_POST_COMMENT_ID,
                'post_id' => self::BLOG_POST_ID,
                'language' => 'en',
                'created_on' => '2017-01-01 13:37:00',
                'author' => 'John Doe',
                'email' => 'john@example.org',
                'website' => 'http://example.org',
                'text' => 'Lorem Ipsum',
                'type' => 'comment',
                'status' => 'published',
                'data' => serialize(['server' => ['foo' => 'bar']]),
            ]
        );
    }
}
