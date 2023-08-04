<?php

namespace Backend\Modules\Blog\Tests\Engine;

use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPostComments;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Backend\Modules\Blog\Engine\Model;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ModelTest extends BackendWebTestCase
{
    // comments
    public function testCreateComment(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $commentData = [
            'post_id' => LoadBlogPosts::BLOG_POST_ID,
            'language' => 'en',
            'created_on' => '2020-01-01 13:37:00',
            'author' => 'Elon Musk',
            'email' => 'elon@example.org',
            'website' => 'http://example.org',
            'text' => 'I really like this CMS',
            'type' => 'comment',
            'status' => 'published',
            'data' => 'a:1:{s:6:"server";a:1:{s:3:"foo";s:3:"bar";}}',
        ];
        $id = Model::insertComment($commentData);

        $addedComment = Model::getComment($id);

        self::assertEquals($id, $addedComment['id']);
        self::assertEquals($commentData['post_id'], $addedComment['post_id']);
        self::assertEquals($commentData['language'], $addedComment['language']);
        self::assertEquals($commentData['author'], $addedComment['author']);
        self::assertEquals($commentData['email'], $addedComment['email']);
        self::assertEquals($commentData['website'], $addedComment['website']);
        self::assertEquals($commentData['text'], $addedComment['text']);
        self::assertEquals($commentData['type'], $addedComment['type']);
        self::assertEquals($commentData['status'], $addedComment['status']);
        self::assertEquals($commentData['data'], $addedComment['data']);
        self::assertEquals(LoadBlogPosts::BLOG_POST_TITLE, $addedComment['post_title']);
    }

    public function testIfCommentExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
                LoadBlogPostComments::class,
            ]
        );

        self::assertTrue(Model::existsComment(1));
        self::assertFalse(Model::existsComment(2));
    }

    public function testUpdateComment(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
                LoadBlogPostComments::class,
            ]
        );

        $commentData = [
            'id' => LoadBlogPostComments::BLOG_POST_COMMENT_ID,
            'post_id' => LoadBlogPosts::BLOG_POST_ID,
            'language' => 'en',
            'created_on' => '2017-01-01 13:37:00',
            'author' => 'John Doe EDIT',
            'email' => 'john@example.org',
            'website' => 'http://example.org',
            'text' => 'Lorem Ipsum EDIT',
            'type' => 'comment',
            'status' => 'published',
            'data' => serialize(['server' => ['foo' => 'bar edit']]),
        ];

        Model::updateComment($commentData);

        $editedComment = Model::getComment(LoadBlogPostComments::BLOG_POST_COMMENT_ID);

        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_ID, $editedComment['id']);
        self::assertEquals($commentData['post_id'], $editedComment['post_id']);
        self::assertEquals($commentData['language'], $editedComment['language']);
        self::assertEquals($commentData['author'], $editedComment['author']);
        self::assertEquals($commentData['email'], $editedComment['email']);
        self::assertEquals($commentData['website'], $editedComment['website']);
        self::assertEquals($commentData['text'], $editedComment['text']);
        self::assertEquals($commentData['type'], $editedComment['type']);
        self::assertEquals($commentData['status'], $editedComment['status']);
        self::assertEquals($commentData['data'], $editedComment['data']);
        self::assertEquals(LoadBlogPosts::BLOG_POST_TITLE, $editedComment['post_title']);
    }

    public function testGettingAllComments(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
                LoadBlogPostComments::class,
            ]
        );

        $comments = Model::getAllCommentsForStatus('published');

        self::assertCount(1, $comments);

        $firstComment = $comments[0];

        self::assertEquals(1, $firstComment['post_id']);
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['postId'], $firstComment['post_id']);
        self::assertEquals(
            (string) strtotime(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['createdOn'] . ' UTC'),
            $firstComment['created_on']
        );
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['author'], $firstComment['author']);
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['email'], $firstComment['email']);
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['website'], $firstComment['website']);
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['text'], $firstComment['text']);
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['type'], $firstComment['type']);
        self::assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['status'], $firstComment['status']);
        self::assertEquals(LoadBlogPosts::BLOG_POST_TITLE, $firstComment['post_title']);
        self::assertEquals(LoadBlogPosts::BLOG_POST_DATA['language'], $firstComment['post_language']);
    }

    public function testDeleteComment(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
                LoadBlogPostComments::class,
            ]
        );

        self::assertTrue(Model::existsComment(1));
        Model::deleteComments([1]);
        self::assertFalse(Model::existsComment(1));
    }

    // categories
    public function testCreateCategory(): void
    {
        $id = Model::insertCategory(
            LoadBlogCategories::BLOG_CATEGORY_DATA,
            LoadBlogCategories::BLOG_CATEGORY_META_DATA
        );
        $createdCategory = Model::getCategory($id);

        self::assertArrayHasKey('meta_id', $createdCategory);
        self::assertEquals($id, $createdCategory['id']);
        self::assertEquals(LoadBlogCategories::BLOG_CATEGORY_DATA['locale'], $createdCategory['language']);
        self::assertEquals(LoadBlogCategories::BLOG_CATEGORY_DATA['title'], $createdCategory['title']);
    }

    public function testIfCategoryExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
            ]
        );
        self::assertTrue(Model::existsCategory(1));
        self::assertTrue(Model::existsCategory(2));
        self::assertFalse(Model::existsCategory(1337));
    }

    public function testUpdateCategory(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
            ]
        );
        $categoryMetaData = [
            'id' => LoadBlogCategories::getMetaId(),
            'keywords' => 'Believe me, I\'ve changed',
            'description' => 'Believe me, I\'ve changed',
            'title' => 'Believe me, I\'ve changed',
            'url' => 'believe-me-i-ve-changed',
        ];

        $newCategoryData = [
            'id' => LoadBlogCategories::getCategoryId(),
            'title' => 'Believe me, I\'ve changed',
            'language' => 'en',
        ];
        $category = Model::getCategory(LoadBlogCategories::getCategoryId());
        self::assertEquals($newCategoryData['id'], $category['id']);
        self::assertEquals($categoryMetaData['id'], $category['meta_id']);
        self::assertEquals($newCategoryData['language'], $category['language']);
        self::assertNotEquals($newCategoryData['title'], $category['title']);

        Model::updateCategory($newCategoryData, $categoryMetaData);

        $updatedCategory = Model::getCategory(LoadBlogCategories::getCategoryId());

        self::assertEquals($newCategoryData['id'], $updatedCategory['id']);
        self::assertEquals($categoryMetaData['id'], $updatedCategory['meta_id']);
        self::assertEquals($newCategoryData['language'], $updatedCategory['language']);
        self::assertEquals($newCategoryData['title'], $updatedCategory['title']);
    }

    public function testDeleteCategory(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
            ]
        );

        $id = LoadBlogCategories::getCategoryId();

        self::assertTrue(Model::existsCategory($id));
        Model::deleteCategory($id);
        self::assertFalse(Model::existsCategory($id));
    }

    public function testCalculatingCategoryUrl(Client $client): void
    {
        self::assertEquals(
            LoadBlogCategories::BLOG_CATEGORY_SLUG,
            Model::getUrlForCategory(LoadBlogCategories::BLOG_CATEGORY_SLUG)
        );

        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
            ]
        );

        self::assertEquals(
            LoadBlogCategories::BLOG_CATEGORY_SLUG . '-2',
            Model::getUrlForCategory(LoadBlogCategories::BLOG_CATEGORY_SLUG)
        );

        // check if the same url is returned when we pass the id
        self::assertEquals(
            LoadBlogCategories::BLOG_CATEGORY_SLUG,
            Model::getUrlForCategory(
                LoadBlogCategories::BLOG_CATEGORY_SLUG,
                LoadBlogCategories::getCategoryId()
            )
        );
    }
}
