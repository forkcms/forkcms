<?php

namespace Backend\Modules\Blog\DataFixtures;

class LoadBlogPosts
{
    public function load(\SpoonDatabase $database)
    {
        $metaId = $database->insert(
            'meta',
            array(
                'keywords' => 'Blogpost for functional tests',
                'description' => 'Blogpost for functional tests',
                'title' => 'Blogpost for functional tests',
                'url' => 'blogpost-for-functional-tests',
            )
        );

        $categoryId = $database->getVar(
            'SELECT id
             FROM blog_categories
             WHERE title = :title AND language = :language
             LIMIT 1',
            array(
                'title' => 'BlogCategory for tests',
                'language' => 'en',
            )
        );

        $database->insert(
            'blog_posts',
            array(
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
                'num_comments' => 0,
            )
        );

        $database->insert(
            'search_index',
            array(
                'module' => 'Blog',
                'other_id' => 1,
                'field' => 'title',
                'value' => 'Blogpost for functional tests',
                'language' => 'en',
                'active' => 'Y',
            )
        );
    }
}
