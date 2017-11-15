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

    public function testInsertingComment(): void
    {
        $client = self::createClient();
        $this->loadFixtures($client);
        $this->insertBlogpost();

        $commentData = $this->getCommentData();

        Model::insertComment($commentData);

        $addedComment = Model::getComment(1);

        self::assertEquals(1, $addedComment['id']);
        self::assertEquals($commentData['post_id'], $addedComment['post_id']);
        self::assertEquals($commentData['language'], $addedComment['language']);
        self::assertEquals($commentData['author'], $addedComment['author']);
        self::assertEquals($commentData['email'], $addedComment['email']);
        self::assertEquals($commentData['website'], $addedComment['website']);
        self::assertEquals($commentData['text'], $addedComment['text']);
        self::assertEquals($commentData['type'], $addedComment['type']);
        self::assertEquals($commentData['status'], $addedComment['status']);
        self::assertEquals($commentData['data'], $addedComment['data']);
        self::assertEquals(
            $this->getBlogpostData()['title'],
            $addedComment['post_title']
        );
    }

    public function testCommentExists(): void
    {
        self::assertEquals(true, Model::existsComment(1));
        self::assertEquals(false, Model::existsComment(2));
    }

    public function testEditingComment(): void
    {
        $commentData = $this->getUpdatedCommentData();

        Model::updateComment($commentData);

        $editedComment = Model::getComment(1);

        self::assertEquals(1, $editedComment['id']);
        self::assertEquals($commentData['post_id'], $editedComment['post_id']);
        self::assertEquals(
            $commentData['language'],
            $editedComment['language']
        );
        self::assertEquals($commentData['author'], $editedComment['author']);
        self::assertEquals($commentData['email'], $editedComment['email']);
        self::assertEquals($commentData['website'], $editedComment['website']);
        self::assertEquals($commentData['text'], $editedComment['text']);
        self::assertEquals($commentData['type'], $editedComment['type']);
        self::assertEquals($commentData['status'], $editedComment['status']);
        self::assertEquals($commentData['data'], $editedComment['data']);
        self::assertEquals(
            $this->getBlogpostData()['title'],
            $editedComment['post_title']
        );
    }

    public function testDeletingComment(): void
    {
        self::assertTrue(Model::existsComment(1));
        Model::deleteComments([1]);
        self::assertFalse(Model::existsComment(1));
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
}
