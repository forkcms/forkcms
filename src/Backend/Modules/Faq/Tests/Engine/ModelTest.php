<?php

namespace Backend\Modules\Faq\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\DataFixtures\LoadFaqCategories;
use Backend\Modules\Faq\Engine\Model;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class ModelTest extends BackendWebTestCase
{
    public function testInsertingFaqCategory(): void
    {
        $categoryId = Model::insertCategory(
            LoadFaqCategories::FAQ_CATEGORY_DATA,
            LoadFaqCategories::FAQ_CATEGORY_META_DATA
        );

        $addedCategory = Model::getCategory($categoryId);

        $this->assertEquals($categoryId, $addedCategory['id']);
        $this->assertEquals(LoadFaqCategories::FAQ_CATEGORY_DATA['language'], $addedCategory['language']);
        $this->assertArrayHasKey('meta_id', $addedCategory);
        $this->assertEquals(LoadFaqCategories::FAQ_CATEGORY_DATA['title'], $addedCategory['title']);
        $this->assertEquals(LoadFaqCategories::FAQ_CATEGORY_DATA['sequence'], $addedCategory['sequence']);
    }

    public function testIfCategoryExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
            ]
        );

        $this->assertTrue(Model::existsCategory(LoadFaqCategories::getCategoryId()));
        $this->assertFalse(Model::existsCategory(99));
    }

    public function testGeneratingCategoryUrl(Client $client): void
    {
        // new url
        $this->assertEquals(
            LoadFaqCategories::FAQ_CATEGORY_SLUG,
            Model::getUrlForCategory(LoadFaqCategories::FAQ_CATEGORY_SLUG)
        );

        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
            ]
        );

        // existing url, "2" is should be appended
        $this->assertEquals(
            LoadFaqCategories::FAQ_CATEGORY_SLUG . '-2',
            Model::getUrlForCategory(LoadFaqCategories::FAQ_CATEGORY_SLUG)
        );
        // existing url with id
        $this->assertEquals(
            LoadFaqCategories::FAQ_CATEGORY_SLUG,
            Model::getUrlForCategory(LoadFaqCategories::FAQ_CATEGORY_SLUG, LoadFaqCategories::getCategoryId())
        );
    }

    public function testEditCategory(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
            ]
        );

        $categoryData = [
            'id' => LoadFaqCategories::getCategoryId(),
            'language' => 'en',
            'title' => 'Test edit category',
            'extra_id' => 39,
        ];

        $categoryMetaData = [
            'id' => LoadFaqCategories::getMetaId(),
            'keywords' => 'Test edit category',
            'description' => 'Test edit category',
            'title' => 'Test edit category',
            'url' => 'test-edit-category',
        ];

        // update meta, there doesn't seems to be a function for this?
        BackendModel::get('database')->update('meta', $categoryMetaData, 'id = ?', [$categoryMetaData['id']]);

        Model::updateCategory($categoryData);

        $editedCategory = Model::getCategory($categoryData['id']);

        $this->assertEquals($categoryData['id'], $editedCategory['id']);
        $this->assertEquals($categoryMetaData['id'], $editedCategory['meta_id']);
        $this->assertEquals($categoryData['language'], $editedCategory['language']);
        $this->assertEquals($categoryData['title'], $editedCategory['title']);
    }

    public function testDeleteCategory(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
            ]
        );

        $id = LoadFaqCategories::getCategoryId();
        $this->assertTrue(Model::existsCategory($id));
        Model::deleteCategory($id);
        $this->assertFalse(Model::existsCategory($id));
    }
}
