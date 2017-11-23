<?php

namespace Backend\Modules\Blog\Tests\Engine;

use Backend\Modules\Blog\DataFixtures\LoadBlogComment;
use Backend\Modules\Blog\Engine\Model;
use Common\WebTestCase;

class ModelCommentTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadBlogComment::class,
            ]
        );
    }

    public function testCreateComment(): void
    {
        $commentData = [
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
        $this->assertEquals('Blogpost for functional tests', $addedComment['post_title']);
    }

    public function testIfCommentExists(): void
    {
        $this->assertTrue(Model::existsComment(1));
        $this->assertFalse(Model::existsComment(2));
    }

    public function testUpdateComment(): void
    {
        $commentData = [
            'id' => 1,
            'post_id' => 1,
            'language' => 'en',
            'created_on' => '2017-01-01 13:37:00',
            'author' => 'John Doe EDIT',
            'email' => 'john@example.org',
            'website' => 'http://example.org',
            'text' => 'Lorem Ipsum EDIT',
            'type' => 'comment',
            'status' => 'published',
            'data' => serialize(['server' => ['foo' => 'bar edit']]),
        ];;
        Model::updateComment($commentData);
        $editedComment = Model::getComment(1);

        $this->assertEquals(1, $editedComment['id']);
        $this->assertEquals($commentData['post_id'], $editedComment['post_id']);
        $this->assertEquals($commentData['language'], $editedComment['language']);
        $this->assertEquals($commentData['author'], $editedComment['author']);
        $this->assertEquals($commentData['email'], $editedComment['email']);
        $this->assertEquals($commentData['website'], $editedComment['website']);
        $this->assertEquals($commentData['text'], $editedComment['text']);
        $this->assertEquals($commentData['type'], $editedComment['type']);
        $this->assertEquals($commentData['status'], $editedComment['status']);
        $this->assertEquals($commentData['data'], $editedComment['data']);
        $this->assertEquals('Blogpost for functional tests', $editedComment['post_title']);
    }

    public function testDeleteComment(): void
    {
        $this->assertTrue(Model::existsComment(1));
        Model::deleteComments([1]);
        $this->assertFalse(Model::existsComment(1));
    }
}
