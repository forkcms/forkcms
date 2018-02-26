<?php

namespace ForkCMS\Tests\Backend\Modules\Faq\Engine;

use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Modules\Faq\Engine\Model;
use ForkCMS\Tests\WebTestCase;

final class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures($client);
    }

    public function testInsertingFaqCategory(): void
    {
        $categoryId = $this->addCategory();

        $categoryData = $this->getCategoryData();
        $addedCategory = Model::getCategory($categoryId);

        $this->assertEquals($categoryId, $addedCategory['id']);
        $this->assertEquals($categoryData['language'], $addedCategory['language']);
        $this->assertArrayHasKey('meta_id', $addedCategory);
        $this->assertEquals($categoryData['title'], $addedCategory['title']);
        $this->assertEquals($categoryData['sequence'], $addedCategory['sequence']);
    }

    public function testIfCategoryExists(): void
    {
        $categoryId = $this->addCategory();

        $this->assertTrue(Model::existsCategory($categoryId));
        $this->assertFalse(Model::existsCategory(99));
    }

    public function testGeneratingCategoryUrl(): void
    {
        // new url
        $this->assertEquals('new-url', Model::getUrlForCategory('new-url'));

        $categoryId = $this->addCategory();

        // existing url, "2" is should be appended
        $this->assertEquals('test-category-2', Model::getUrlForCategory('test-category'));
        // existing url with id
        $this->assertEquals('test-category', Model::getUrlForCategory('test-category', $categoryId));
    }

    public function testEditCategory(): void
    {
        $categoryId = $this->addCategory();

        $categoryData = $this->getUpdateCategoryData();
        $categoryMetaData = $this->getUpdatedCategoryMetaData();

        // update meta, there doesn't seems to be a function for this?
        BackendModel::get('database')->update('meta', $categoryMetaData, 'id = ?', [$categoryMetaData['id']]);

        Model::updateCategory($categoryData);

        $editedCategory = Model::getCategory($categoryData['id']);

        $this->assertEquals($categoryId, $editedCategory['id']);
        $this->assertEquals($categoryMetaData['id'], $editedCategory['meta_id']);
        $this->assertEquals($categoryData['language'], $editedCategory['language']);
        $this->assertEquals($categoryData['title'], $editedCategory['title']);
    }

    public function testDeleteCategory(): void
    {
        $categoryId = $this->addCategory();

        $this->assertTrue(Model::existsCategory($categoryId));
        Model::deleteCategory(1);
        $this->assertFalse(Model::existsCategory($categoryId));
    }

    private function addCategory(): int
    {
        $categoryData = $this->getCategoryData();
        $categoryMetaData = $this->getCategoryMetaData();

        return Model::insertCategory($categoryData, $categoryMetaData);
    }

    public function getCategoryData(): array
    {
        return [
            'language' => 'en',
            'title' => 'Test category',
            'sequence' => 1,
        ];
    }

    private function getCategoryMetaData(): array
    {
        return [
            'keywords' => 'Test category',
            'description' => 'Test category',
            'title' => 'Test category',
            'url' => 'test-category',
        ];
    }

    private function getUpdateCategoryData()
    {
        return [
            'id' => 1,
            'language' => 'en',
            'title' => 'Test edit category'
        ];
    }

    private function getUpdatedCategoryMetaData()
    {
        return [
            'id' => 28,
            'keywords' => 'Test edit category',
            'description' => 'Test edit category',
            'title' => 'Test edit category',
            'url' => 'test-edit-category',
        ];
    }
}
