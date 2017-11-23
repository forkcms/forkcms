<?php

namespace Backend\Modules\Blog\Tests\Engine;

use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\Engine\Model;
use Common\WebTestCase;

class ModelCategoryTest extends WebTestCase
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
                LoadBlogCategories::class,
            ]
        );
    }

    public function testCreateCategory(): void
    {
        $categoryData = [
            'language' => 'en',
            'title' => 'category title',
        ];
        $categoryMetaData = [
            'keywords' => 'keywords',
            'description' => 'description',
            'title' => 'meta title',
            'url' => 'meta-url',
        ];

        $id = Model::insertCategory($categoryData, $categoryMetaData);
        $createdCategory = Model::getCategory($id);

        $this->assertArrayHasKey('meta_id', $createdCategory);
        $this->assertEquals($id, $createdCategory['id']);
        $this->assertEquals($categoryData['language'], $createdCategory['language']);
        $this->assertEquals($categoryData['title'], $createdCategory['title']);
    }

    public function testIfCategoryExists(): void
    {
        $this->assertTrue(Model::existsCategory(1));
        $this->assertFalse(Model::existsCategory(1337));
    }

    public function testUpdateCategory(): void
    {
        $categoryData = [
            'id' => 1,
            'language' => 'en',
            'title' => 'category title edited',
        ];
        $categoryMetaData = [
            'id' => Model::getCategory('1')['meta_id'],
            'keywords' => 'keywords',
            'description' => 'description',
            'title' => 'meta title',
            'url' => 'meta-url',
        ];

        Model::updateCategory($categoryData, $categoryMetaData);

        $updatedCategory = Model::getCategory(1);

        $this->assertEquals($categoryData['id'], $updatedCategory['id']);
        $this->assertArrayHasKey('meta_id', $updatedCategory);
        $this->assertEquals($categoryData['language'], $updatedCategory['language']);
        $this->assertEquals($categoryData['title'], $updatedCategory['title']);
    }

    public function testDeleteCategory(): void
    {
        $this->assertTrue(Model::existsCategory(1));
        Model::deleteCategory(1);
        $this->assertFalse(Model::existsCategory(1));
    }

    public function testCalculatingCategoryUrl(): void
    {
        $this->assertEquals('foo-bar', Model::getUrlForCategory('foo-bar'));

        // check if 2 is appended for an existing category
        $this->assertEquals(
            'blogcategory-for-tests-2',
            Model::getUrlForCategory('blogcategory-for-tests')
        );

        // check if the same url is returned when we pass the id
        $this->assertEquals(
            'blogcategory-for-tests',
            Model::getUrlForCategory('blogcategory-for-tests', 1)
        );
    }
}
