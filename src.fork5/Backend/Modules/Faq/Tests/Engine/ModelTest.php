<?php

namespace Backend\Modules\Faq\Tests\Engine;

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

        self::assertEquals($categoryId, $addedCategory['id']);
        self::assertEquals(LoadFaqCategories::FAQ_CATEGORY_DATA['locale'], $addedCategory['language']);
        self::assertArrayHasKey('meta_id', $addedCategory);
        self::assertEquals(LoadFaqCategories::FAQ_CATEGORY_DATA['title'], $addedCategory['title']);
        self::assertEquals(LoadFaqCategories::FAQ_CATEGORY_DATA['sequence'], $addedCategory['sequence']);
    }

    public function testIfCategoryExists(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
            ]
        );

        self::assertTrue(Model::existsCategory(LoadFaqCategories::getCategoryId()));
        self::assertFalse(Model::existsCategory(99));
    }

    public function testGeneratingCategoryUrl(Client $client): void
    {
        // new url
        self::assertEquals(
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
        self::assertEquals(
            LoadFaqCategories::FAQ_CATEGORY_SLUG . '-2',
            Model::getUrlForCategory(LoadFaqCategories::FAQ_CATEGORY_SLUG)
        );
        // existing url with id
        self::assertEquals(
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
        $client->getContainer()->get('database')->update('meta', $categoryMetaData, 'id = ?', [$categoryMetaData['id']]);

        Model::updateCategory($categoryData);

        $editedCategory = Model::getCategory($categoryData['id']);

        self::assertEquals($categoryData['id'], $editedCategory['id']);
        self::assertEquals($categoryMetaData['id'], $editedCategory['meta_id']);
        self::assertEquals($categoryData['language'], $editedCategory['language']);
        self::assertEquals($categoryData['title'], $editedCategory['title']);
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
        self::assertTrue(Model::existsCategory($id));
        Model::deleteCategory($id);
        self::assertFalse(Model::existsCategory($id));
    }
}
