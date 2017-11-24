<?php

namespace Backend\Modules\Blog\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Backend\Modules\Blog\Engine\Model;
use Common\Doctrine\Entity\Meta;
use Common\WebTestCase;

class ModelPostTest extends WebTestCase
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
                LoadBlogPosts::class,
            ]
        );
    }

    public function testCreatePost(): void
    {
        $postData = [
            'id' => 2,
            'meta_id' => Model::get(1)['meta_id'],
            'category_id' => 1,
            'user_id' => 1,
            'language' => 'en',
            'title' => 'title edited',
            'introduction' => '<p>summary edited</p>',
            'text' => '<p>main content edited</p>',
            'publish_on' => '2017-11-24 09:23:00',
            'created_on' => '2017-11-24 09:24:11',
            'edited_on' => '2017-11-24 09:24:11',
            'hidden' => 1,
            'allow_comments' => false,
            'status' => 'active',
            'num_comments' => 0,
        ];

        $revisionId = Model::insert($postData);
        $createdPost = Model::get($postData['id']);

        $this->assertEquals($revisionId, $createdPost['revision_id']);
        $this->assertEquals($postData['id'], $createdPost['id']);
        $this->assertEquals($postData['category_id'], $createdPost['category_id']);
        $this->assertEquals($postData['user_id'], $createdPost['user_id']);
        $this->assertEquals($postData['meta_id'], $createdPost['meta_id']);
        $this->assertEquals($postData['language'], $createdPost['language']);
        $this->assertEquals($postData['title'], $createdPost['title']);
        $this->assertEquals($postData['introduction'], $createdPost['introduction']);
        $this->assertEquals($postData['text'], $createdPost['text']);
        $this->assertArrayHasKey('image', $createdPost);
        $this->assertEquals($postData['status'], $createdPost['status']);
        $this->assertArrayHasKey('publish_on', $createdPost);
        $this->assertArrayHasKey('created_on', $createdPost);
        $this->assertArrayHasKey('edited_on', $createdPost);
        $this->assertEquals($postData['hidden'], $createdPost['hidden']);
        $this->assertEquals('0', $createdPost['allow_comments']);
        $this->assertEquals($postData['num_comments'], $createdPost['num_comments']);
        $this->assertArrayHasKey('url', $createdPost);
    }

    public function testIfPostExists(): void
    {
        $this->assertTrue(Model::exists(1));
        $this->assertFalse(Model::exists(1337));
    }

    public function testUpdatePost(): void
    {
        $postData = [
            'id' => 1,
            'revision_id' => Model::get(1)['revision_id'],
            'meta_id' => Model::get(1)['meta_id'],
            'category_id' => 1,
            'user_id' => 1,
            'language' => 'en',
            'title' => 'title',
            'introduction' => '<p>summary</p>',
            'text' => '<p>main content</p>',
            'publish_on' => '2017-11-23 09:23:00',
            'created_on' => '2017-11-23 09:24:11',
            'hidden' => 0,
            'allow_comments' => true,
            'num_comments' => 0,
            'status' => 'active',
            'edited_on' => '2017-11-23 09:24:11',
        ];

        $revisionId = Model::update($postData);
        $updatedPost = Model::get(1);

        $this->assertEquals($revisionId, $updatedPost['revision_id']);
        $this->assertEquals($postData['id'], $updatedPost['id']);
        $this->assertEquals($postData['category_id'], $updatedPost['category_id']);
        $this->assertEquals($postData['user_id'], $updatedPost['user_id']);
        $this->assertEquals($postData['meta_id'], $updatedPost['meta_id']);
        $this->assertEquals($postData['language'], $updatedPost['language']);
        $this->assertEquals($postData['title'], $updatedPost['title']);
        $this->assertEquals($postData['introduction'], $updatedPost['introduction']);
        $this->assertEquals($postData['text'], $updatedPost['text']);
        $this->assertArrayHasKey('image', $updatedPost);
        $this->assertEquals($postData['status'], $updatedPost['status']);
        $this->assertArrayHasKey('publish_on', $updatedPost);
        $this->assertArrayHasKey('created_on', $updatedPost);
        $this->assertArrayHasKey('edited_on', $updatedPost);
        $this->assertEquals($postData['hidden'], $updatedPost['hidden']);
        $this->assertEquals('1', $updatedPost['allow_comments']);
        $this->assertEquals($postData['num_comments'], $updatedPost['num_comments']);
        $this->assertArrayHasKey('url', $updatedPost);
    }

    public function testUpdatePostRevision(): void
    {
        Model::updateRevision(
            Model::get(1)['revision_id'],
            [
                'image' => 'foo.jpg',
            ]
        );

        $updatedRevision = Model::get(1);
        $this->assertEquals($updatedRevision['image'], 'foo.jpg');
    }

    public function testDeletePost(): void
    {
        $this->assertTrue(Model::exists(1));
        Model::delete([1]);
        $this->assertFalse(Model::exists(1));
    }

    public function testCalculatingPostUrl(): void
    {
        $this->assertEquals('foo-bar', Model::getUrl('foo-bar'));

        // check if 2 is appended for an existing post
        $this->assertEquals('blogpost-for-functional-tests-2', Model::getUrl('blogpost-for-functional-tests'));

        // check if the same url is returned when we pass the id
        $this->assertEquals('blogpost-for-functional-tests', Model::getUrl('blogpost-for-functional-tests', 1));
    }

    public function testRetrievingMaximumPostId(): void
    {
        $this->assertEquals(1, Model::getMaximumId());
    }
}
