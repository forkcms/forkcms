<?php

namespace Backend\Modules\Blog\Tests\Engine;

use Backend\Modules\Blog\Engine\Model;
use Common\Doctrine\Entity\Meta;
use Common\WebTestCase;

class ModelTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }
    }

    // comments
    public function testCreateComment(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);
        $this->insertBlogpost();

        $commentData = $this->getCommentData();

        Model::insertComment($commentData);

        $addedComment = Model::getComment(1);

        $this->assertEquals(1, $addedComment['id']);
        $this->assertEquals($commentData['post_id'], $addedComment['post_id']);
        $this->assertEquals(
            $commentData['language'],
            $addedComment['language']
        );
        $this->assertEquals($commentData['author'], $addedComment['author']);
        $this->assertEquals($commentData['email'], $addedComment['email']);
        $this->assertEquals($commentData['website'], $addedComment['website']);
        $this->assertEquals($commentData['text'], $addedComment['text']);
        $this->assertEquals($commentData['type'], $addedComment['type']);
        $this->assertEquals($commentData['status'], $addedComment['status']);
        $this->assertEquals($commentData['data'], $addedComment['data']);
        $this->assertEquals(
            $this->getBlogpostData()['title'],
            $addedComment['post_title']
        );
    }

    public function testIfCommentExists(): void
    {
        $this->assertTrue(Model::existsComment(1));
        $this->assertFalse(Model::existsComment(2));
    }

    public function testUpdateComment(): void
    {
        $commentData = $this->getUpdatedCommentData();

        Model::updateComment($commentData);

        $editedComment = Model::getComment(1);

        $this->assertEquals(1, $editedComment['id']);
        $this->assertEquals($commentData['post_id'], $editedComment['post_id']);
        $this->assertEquals(
            $commentData['language'],
            $editedComment['language']
        );
        $this->assertEquals($commentData['author'], $editedComment['author']);
        $this->assertEquals($commentData['email'], $editedComment['email']);
        $this->assertEquals($commentData['website'], $editedComment['website']);
        $this->assertEquals($commentData['text'], $editedComment['text']);
        $this->assertEquals($commentData['type'], $editedComment['type']);
        $this->assertEquals($commentData['status'], $editedComment['status']);
        $this->assertEquals($commentData['data'], $editedComment['data']);
        $this->assertEquals(
            $this->getBlogpostData()['title'],
            $editedComment['post_title']
        );
    }

    public function testDeleteComment(): void
    {
        $this->assertTrue(Model::existsComment(1));
        Model::deleteComments([1]);
        $this->assertFalse(Model::existsComment(1));
    }

    private function getCommentData(): array
    {
        return [
            'post_id' => $this->getBlogpostData()['id'],
            'language' => 'en',
            'created_on' => '2017-01-01 13:37:00',
            'author' => 'John Doe',
            'email' => 'john@example.org',
            'website' => 'http://example.org',
            'text' => 'Lorem Ipsum',
            'type' => 'comment',
            'status' => 'published',
            'data' => serialize(['server' => ['foo' => 'bar']]),
        ];
    }

    private function getUpdatedCommentData(): array
    {
        return [
            'id' => 1,
            'post_id' => $this->getBlogpostData()['id'],
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
    }

    private function insertBlogPost(): void
    {
        Model::insert(
            $this->getBlogpostData()
        );
    }

    private function getBlogpostData(): array
    {
        return [
            'id' => 1,
            'meta_id' => 1,
            'category_id' => 1,
            'user_id' => 1,
            'language' => 'en',
            'title' => 'Blog Title',
            'introduction' => 'Intro',
            'text' => 'Text',
            'publish_on' => '2017-01-01 13:37:00',
            'created_on' => '2017-01-01 13:37:00',
            'edited_on' => '2017-01-01 13:37:00',
            'hidden' => 0,
            'allow_comments' => 1,
            'num_comments' => 0,
            'status' => 'active',
        ];
    }

    // categories
    public function testCreateCategory(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);

        $categoryData = $this->getCategoryData();
        $categoryMetaData = $this->getCategoryMetaData();
        $id = Model::insertCategory($categoryData, $categoryMetaData);
        $createdCategory = Model::getCategory($id);

        $this->assertArrayHasKey('meta_id', $createdCategory);
        $this->assertEquals($id, $createdCategory['id']);
        $this->assertEquals(
            $categoryData['language'],
            $createdCategory['language']
        );
        $this->assertEquals($categoryData['title'], $createdCategory['title']);
    }

    public function testIfCategoryExists(): void
    {
        $this->assertTrue(Model::existsCategory(1));
        $this->assertFalse(Model::existsCategory(1337));
    }

    public function testUpdateCategory(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);

        $categoryData = $this->getUpdateCategoryData();
        $categoryMetaData = $this->getUpdatedCategoryMetaData();

        Model::updateCategory($categoryData, $categoryMetaData);

        $updatedCategory = Model::getCategory(1);

        $this->assertEquals($categoryData['id'], $updatedCategory['id']);
        $this->assertArrayHasKey('meta_id', $updatedCategory);
        $this->assertEquals($categoryData['language'], $updatedCategory['language']);
        $this->assertEquals($categoryData['title'], $updatedCategory['title']);
    }

    public function testDeleteCategory(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);

        $id = Model::insertCategory(
            $this->getCategoryData(),
            $this->getCategoryMetaData()
        );

        $this->assertTrue(Model::existsCategory($id));
        Model::deleteCategory($id);
        $this->assertFalse(Model::existsCategory($id));
    }

    public function testCalculatingCategoryUrl(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);

        $this->assertEquals('foo-bar', Model::getUrlForCategory('foo-bar'));

        // check if 2 is appended for an existing category
        $id = Model::insertCategory(
            $this->getCategoryData(),
            $this->getCategoryMetaData()
        );
        $this->assertEquals('meta-url-2', Model::getUrlForCategory('meta-url'));

        // check if the same url is returned when we pass the id
        $this->assertEquals('meta-url', Model::getUrlForCategory('meta-url', $id));
    }

    private function getCategoryData(): array
    {
        return [
            'language' => 'en',
            'title' => 'category title',
        ];
    }

    private function getCategoryMetaData(): array
    {
        return [
            'keywords' => 'keywords',
            'description' => 'description',
            'title' => 'meta title',
            'url' => 'meta-url',
        ];
    }

    private function getUpdateCategoryData(): array
    {
        return [
            'id' => 1,
            'language' => 'en',
            'title' => 'category title edited',
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
