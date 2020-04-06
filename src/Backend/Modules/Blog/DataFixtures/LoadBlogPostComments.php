<?php

namespace Backend\Modules\Blog\DataFixtures;

use SpoonDatabase;

class LoadBlogPostComments
{
    public const BLOG_POST_COMMENT_ID = 1;
    public const BLOG_POST_COMMENT_DATA = [
        'postId' => LoadBlogPosts::BLOG_POST_ID,
        'locale' => 'en',
        'createdOn' => '2017-01-01 13:37:00',
        'author' => 'John Doe',
        'email' => 'john@example.org',
        'website' => 'http://example.org',
        'text' => 'Lorem Ipsum',
        'type' => 'comment',
        'status' => 'published',
        'data' => 'a:1:{s:6:"server";a:1:{s:3:"foo";s:3:"bar";}}',
    ];

    public function load(SpoonDatabase $database): void
    {
        $database->update(
            'blog_posts',
            ['num_comments' => 1],
            'id = ?',
            LoadBlogPosts::BLOG_POST_ID
        );

        $database->insert(
            'blog_comments',
            ['id' => self::BLOG_POST_COMMENT_ID] + self::BLOG_POST_COMMENT_DATA
        );
    }
}
