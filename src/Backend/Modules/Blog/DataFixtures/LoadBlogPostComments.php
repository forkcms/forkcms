<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

class LoadBlogPostComments
{
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
                'id' => 1,
                'meta_id' => $metaId,
                'category_id' => $categoryId,
                'user_id' => 1,
                'language' => 'en',
                'title' => 'Blogpost for functional tests',
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
            'blog_comments',
            [
                'post_id' => 1,
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
