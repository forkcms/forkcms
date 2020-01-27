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

        $this->assertEquals($id, $addedComment['id']);
        $this->assertEquals($commentData['post_id'], $addedComment['post_id']);
        $this->assertEquals($commentData['language'], $addedComment['language']);
        $this->assertEquals($commentData['author'], $addedComment['author']);
        $this->assertEquals($commentData['email'], $addedComment['email']);
        $this->assertEquals($commentData['website'], $addedComment['website']);
        $this->assertEquals($commentData['text'], $addedComment['text']);
        $this->assertEquals($commentData['type'], $addedComment['type']);
        $this->assertEquals($commentData['status'], $addedComment['status']);
        $this->assertEquals($commentData['data'], $addedComment['data']);
        $this->assertEquals(LoadBlogPosts::BLOG_POST_TITLE, $addedComment['post_title']);
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

        $this->assertTrue(Model::existsComment(1));
        $this->assertFalse(Model::existsComment(2));
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

        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_ID, $editedComment['id']);
        $this->assertEquals($commentData['post_id'], $editedComment['post_id']);
        $this->assertEquals($commentData['language'], $editedComment['language']);
        $this->assertEquals($commentData['author'], $editedComment['author']);
        $this->assertEquals($commentData['email'], $editedComment['email']);
        $this->assertEquals($commentData['website'], $editedComment['website']);
        $this->assertEquals($commentData['text'], $editedComment['text']);
        $this->assertEquals($commentData['type'], $editedComment['type']);
        $this->assertEquals($commentData['status'], $editedComment['status']);
        $this->assertEquals($commentData['data'], $editedComment['data']);
        $this->assertEquals(LoadBlogPosts::BLOG_POST_TITLE, $editedComment['post_title']);
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

        $this->assertCount(1, $comments);

        $firstComment = $comments[0];

        $this->assertEquals(1, $firstComment['post_id']);
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['post_id'], $firstComment['post_id']);
        $this->assertEquals(
            (string) strtotime(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['created_on'] . ' UTC'),
            $firstComment['created_on']
        );
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['author'], $firstComment['author']);
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['email'], $firstComment['email']);
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['website'], $firstComment['website']);
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['text'], $firstComment['text']);
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['type'], $firstComment['type']);
        $this->assertEquals(LoadBlogPostComments::BLOG_POST_COMMENT_DATA['status'], $firstComment['status']);
        $this->assertEquals(LoadBlogPosts::BLOG_POST_TITLE, $firstComment['post_title']);
        $this->assertEquals(LoadBlogPosts::BLOG_POST_DATA['language'], $firstComment['post_language']);
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

        $this->assertTrue(Model::existsComment(1));
        Model::deleteComments([1]);
        $this->assertFalse(Model::existsComment(1));
    }

    // categories
    public function testCreateCategory(): void
    {
        $id = Model::insertCategory(
            LoadBlogCategories::BLOG_CATEGORY_DATA,
            LoadBlogCategories::BLOG_CATEGORY_META_DATA
        );
        $createdCategory = Model::getCategory($id);

        $this->assertArrayHasKey('meta_id', $createdCategory);
        $this->assertEquals($id, $createdCategory['id']);
        $this->assertEquals(LoadBlogCategories::BLOG_CATEGORY_DATA['language'], $createdCategory['language']);
        $this->assertEquals(LoadBlogCategories::BLOG_CATEGORY_DATA['title'], $createdCategory['title']);
    }

    public function testIfCategoryExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
            ]
        );
        $this->assertTrue(Model::existsCategory(1));
        $this->assertTrue(Model::existsCategory(2));
        $this->assertFalse(Model::existsCategory(1337));
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
        $this->assertEquals($newCategoryData['id'], $category['id']);
        $this->assertEquals($categoryMetaData['id'], $category['meta_id']);
        $this->assertEquals($newCategoryData['language'], $category['language']);
        $this->assertNotEquals($newCategoryData['title'], $category['title']);

        Model::updateCategory($newCategoryData, $categoryMetaData);

        $updatedCategory = Model::getCategory(LoadBlogCategories::getCategoryId());

        $this->assertEquals($newCategoryData['id'], $updatedCategory['id']);
        $this->assertEquals($categoryMetaData['id'], $updatedCategory['meta_id']);
        $this->assertEquals($newCategoryData['language'], $updatedCategory['language']);
        $this->assertEquals($newCategoryData['title'], $updatedCategory['title']);
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

        $this->assertTrue(Model::existsCategory($id));
        Model::deleteCategory($id);
        $this->assertFalse(Model::existsCategory($id));
    }

    public function testCalculatingCategoryUrl(Client $client): void
    {
        $this->assertEquals(
            LoadBlogCategories::BLOG_CATEGORY_SLUG,
            Model::getUrlForCategory(LoadBlogCategories::BLOG_CATEGORY_SLUG)
        );

        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
            ]
        );

        $this->assertEquals(
            LoadBlogCategories::BLOG_CATEGORY_SLUG . '-2',
            Model::getUrlForCategory(LoadBlogCategories::BLOG_CATEGORY_SLUG)
        );

        // check if the same url is returned when we pass the id
        $this->assertEquals(
            LoadBlogCategories::BLOG_CATEGORY_SLUG,
            Model::getUrlForCategory(
                LoadBlogCategories::BLOG_CATEGORY_SLUG,
                LoadBlogCategories::getCategoryId()
            )
        );
    }

    private function getCategoryData(): array
    {
        return [
            'language' => 'en',
            'title' => 'category title',
        ];
    }

    private function getUpdatedCategoryMetaData(): array
    {
        return [
            'id' => 28,
            'keywords' => 'keywords',
            'description' => 'description',
            'title' => 'meta title',
            'url' => 'meta-url',
        ];
    }
}
