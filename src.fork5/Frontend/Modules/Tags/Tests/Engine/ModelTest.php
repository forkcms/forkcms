<?php

namespace Frontend\Modules\Tags\Tests\Engine;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Search\Engine\Model as SearchModel;
use Frontend\Modules\Pages\Engine\Model as PagesModel;
use Frontend\Modules\Tags\Engine\Model as TagsModel;
use Frontend\Core\Tests\FrontendWebTestCase;

final class ModelTest extends FrontendWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testCallFromInterfaceOnModuleThatDoesNotImplementIt(): void
    {
        $module = 'Search';
        $this->expectException(FrontendException::class);
        $this->expectExceptionMessage(
            'To use the tags module you need
            to implement the FrontendTagsInterface
            in the model of your module
            (' . $module . ').'
        );
        TagsModel::callFromInterface($module, SearchModel::class, 'getIdForTags', null);
    }

    public function testCallFromInterfaceOnModuleThatDoesImplementIt(): void
    {
        $module = 'Pages';
        $pages = TagsModel::callFromInterface($module, PagesModel::class, 'getForTags', [1]);

        self::assertSame($pages[0]['title'], 'Home');
    }

    public function testGettingATagWithTheDefaultLocale(): void
    {
        $tag = TagsModel::get(LoadTagsTags::TAGS_TAG_1_SLUG);
        self::assertTag($tag);
        self::assertSame($tag['url'], LoadTagsTags::TAGS_TAG_1_SLUG);
        self::assertEquals($tag['id'], LoadTagsTags::TAGS_TAG_1_ID);
    }

    public function testGettingATagWithASpecificLocale(): void
    {
        $tag = TagsModel::get(LoadTagsTags::TAGS_TAG_1_SLUG, Locale::fromString('en'));
        self::assertTag($tag);
        self::assertSame($tag['url'], LoadTagsTags::TAGS_TAG_1_SLUG);
        self::assertEquals($tag['id'], LoadTagsTags::TAGS_TAG_1_ID);
        self::assertSame($tag['language'], 'en');
    }

    public function testGetAllTags(): void
    {
        self::assertTag(TagsModel::getAll()[0], ['url', 'name', 'number']);
    }

    public function testGetMostUsed(): void
    {
        self::assertEmpty(TagsModel::getMostUsed(0), 'Most used limit isn\'t respected');
        $mostUsedTags = TagsModel::getMostUsed(2);
        self::assertTag($mostUsedTags[0], ['url', 'name', 'number']);
        self::assertTag($mostUsedTags[1], ['url', 'name', 'number']);
        self::assertTrue($mostUsedTags[0]['number'] >= $mostUsedTags[1]['number'], 'Tags not sorted by usage');
    }

    public function testGetForItemWithDefaultLocale(): void
    {
        $tags = TagsModel::getForItem('Pages', 1);
        self::assertTag($tags[0], ['name', 'full_url', 'url']);
    }

    public function testGetForItemWithSpecificLocale(): void
    {
        $tags = TagsModel::getForItem('Pages', 1, Locale::fromString('en'));
        self::assertTag($tags[0], ['name', 'full_url', 'url']);
    }

    public function testGetForMultipleItemsWithDefaultLocale(): void
    {
        $tags = TagsModel::getForMultipleItems('Pages', [1, 2]);
        self::assertArrayHasKey(1, $tags);
        self::assertArrayHasKey(2, $tags);
        self::assertTag($tags[1][0], ['name', 'other_id', 'url', 'full_url']);
        self::assertTag($tags[2][0], ['name', 'other_id', 'url', 'full_url']);
    }

    public function testGetForMultipleItemsSpecificLocale(): void
    {
        $tags = TagsModel::getForMultipleItems('Pages', [1, 2], Locale::fromString('en'));
        self::assertArrayHasKey(1, $tags);
        self::assertArrayHasKey(2, $tags);
        self::assertTag($tags[1][0], ['name', 'other_id', 'url', 'full_url']);
        self::assertTag($tags[2][0], ['name', 'other_id', 'url', 'full_url']);
    }

    public function testGetIdByUrl(): void
    {
        self::assertSame(LoadTagsTags::TAGS_TAG_1_ID, TagsModel::getIdByUrl(LoadTagsTags::TAGS_TAG_1_SLUG));
        self::assertSame(LoadTagsTags::TAGS_TAG_2_ID, TagsModel::getIdByUrl(LoadTagsTags::TAGS_TAG_2_SLUG));
    }

    public function testGetModulesForTag(): void
    {
        $modules = TagsModel::getModulesForTag(LoadTagsTags::TAGS_TAG_1_ID);
        self::assertSame('Faq', $modules[0]);
        self::assertCount(2, $modules);
        self::assertCount(1, TagsModel::getModulesForTag(LoadTagsTags::TAGS_TAG_2_ID));
    }

    public function testGetName(): void
    {
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, TagsModel::getName(LoadTagsTags::TAGS_TAG_1_ID));
    }

    public function testGetRelatedItemsByTags(): void
    {
        $ids = TagsModel::getRelatedItemsByTags(1, 'Pages', 'Faq');
        self::assertSame('1', $ids[0]);
    }

    public function testGetItemsForTag(): void
    {
        $items = TagsModel::getItemsForTag(LoadTagsTags::TAGS_TAG_1_ID);
        self::assertCount(2, $items);
        self::assertModuleTags($items[1]);
        self::assertSame('Pages', $items[1]['name']);
        self::assertSame('Home', $items[1]['items'][0]['title']);
    }

    public function testGetItemsForTagAndModule(): void
    {
        $items = TagsModel::getItemsForTagAndModule(LoadTagsTags::TAGS_TAG_1_ID, 'Pages');

        self::assertModuleTags($items);
        self::assertSame('Pages', $items['name']);
        self::assertSame('Home', $items['items'][0]['title']);
    }

    public function testGetAllForTag(): void
    {
        self::assertEmpty(TagsModel::getAllForTag('tests', Locale::frontendLanguage()));
        $items = TagsModel::getAllForTag(LoadTagsTags::TAGS_TAG_1_NAME);
        self::assertSame('Faq', $items[0]['module']);
        self::assertSame('1', $items[0]['other_id']);
    }

    private function assertTag(array $tag, array $keys = ['id', 'language', 'name', 'number', 'url']): void
    {
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $tag);
        }
    }

    private function assertModuleTags($items): void
    {
        self::assertArrayHasKey('name', $items);
        self::assertArrayHasKey('label', $items);
        self::assertArrayHasKey('items', $items);
        self::assertTag($items['items'][0], ['id', 'title', 'full_url']);
    }
}
