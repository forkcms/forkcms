<?php

namespace Backend\Modules\Tags\Tests\Engine;

use Backend\Core\Language\Language;
use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Modules\Tags\Engine\Model as TagsModel;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class ModelTest extends BackendWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures(
            $this->getProvidedData()[0],
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testGetStartsWithForDefaultLanguage(): void
    {
        $tags = TagsModel::getStartsWith('te');
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, $tags[0]['name']);
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, $tags[0]['value']);
    }

    public function testGetStartsWithForLanguage(): void
    {
        $tags = TagsModel::getStartsWith('te', Language::getWorkingLanguage());
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, $tags[0]['name']);
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, $tags[0]['value']);
        self::assertEmpty(TagsModel::getStartsWith('te', 'nl'));
    }

    public function testExistsTag(): void
    {
        self::assertTrue(TagsModel::existsTag(LoadTagsTags::TAGS_TAG_1_NAME));
        self::assertFalse(TagsModel::existsTag('non-existing'));
    }

    public function testInsertWithDefaultLanguage(Client $client): void
    {
        $name = 'inserted';
        $tagId = TagsModel::insert($name);
        $database = $client->getContainer()->get('database');
        self::assertSame(
            $tagId,
            (int) $database->getVar(
                'SELECT id FROM TagsTag WHERE tag = ? AND locale = ?',
                [$name, Language::getWorkingLanguage()]
            )
        );
    }

    public function testInsertWithSpecificLanguage(Client $client): void
    {
        $name = 'inserted';
        $language = 'nl';
        $tagId = TagsModel::insert($name, $language);
        $database = $client->getContainer()->get('database');
        self::assertSame(
            $tagId,
            (int) $database->getVar(
                'SELECT id FROM TagsTag WHERE tag = ? AND locale = ?',
                [$name, $language]
            )
        );
    }

    public function testDeleteSingle(): void
    {
        // check single
        self::assertTrue(TagsModel::exists(LoadTagsTags::TAGS_TAG_1_ID));
        TagsModel::delete(LoadTagsTags::TAGS_TAG_1_ID);
        self::assertFalse(TagsModel::exists(LoadTagsTags::TAGS_TAG_1_ID));
    }

    public function testDeleteMultiple(): void
    {
        self::assertTrue(TagsModel::exists(LoadTagsTags::TAGS_TAG_2_ID));
        self::assertTrue(TagsModel::exists(LoadTagsTags::TAGS_TAG_1_ID));
        TagsModel::delete([LoadTagsTags::TAGS_TAG_2_ID, LoadTagsTags::TAGS_TAG_1_ID]);
        self::assertFalse(TagsModel::exists(LoadTagsTags::TAGS_TAG_2_ID));
        self::assertFalse(TagsModel::exists(LoadTagsTags::TAGS_TAG_1_ID));
    }

    public function testGetTags(): void
    {
        self::assertSame(
            implode(',', [LoadTagsTags::TAGS_TAG_2_NAME, LoadTagsTags::TAGS_TAG_1_NAME]),
            TagsModel::getTags('Pages', 1)
        );
        self::assertSame(
            [LoadTagsTags::TAGS_TAG_2_NAME, LoadTagsTags::TAGS_TAG_1_NAME],
            TagsModel::getTags('Pages', 1, 'array')
        );
        self::assertSame('', TagsModel::getTags('Pages', 1, 'string', 'nl'));
        self::assertSame([], TagsModel::getTags('Pages', 1, 'array', 'nl'));
    }

    public function testGet(): void
    {
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, TagsModel::get(LoadTagsTags::TAGS_TAG_1_ID)['name']);
        self::assertSame(LoadTagsTags::TAGS_TAG_2_NAME, TagsModel::get(LoadTagsTags::TAGS_TAG_2_ID)['name']);
    }

    public function testUpdate(): void
    {
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, TagsModel::get(LoadTagsTags::TAGS_TAG_1_ID)['name']);
        TagsModel::update(['id' => LoadTagsTags::TAGS_TAG_1_ID, 'tag' => 'changed']);
        self::assertSame('changed', TagsModel::get(LoadTagsTags::TAGS_TAG_1_ID)['name']);
    }

    public function testSaveTagsAlsoAcceptsAString(): void
    {
        self::assertSame(
            implode(',', [LoadTagsTags::TAGS_TAG_2_NAME, LoadTagsTags::TAGS_TAG_1_NAME]),
            TagsModel::getTags('Pages', 1)
        );
        TagsModel::saveTags(1, implode(',', ['concat', LoadTagsTags::TAGS_TAG_1_NAME]), 'Pages');
        self::assertSame(implode(',', ['concat', LoadTagsTags::TAGS_TAG_1_NAME]), TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsUpdatesTheUsedCount(Client $client): void
    {
        $database = $client->getContainer()->get('database');
        $tagCount = static function (int $id) use ($database): int {
            return $database->getVar('SELECT numberOfTimesLinked FROM TagsTag WHERE id = ?', $id);
        };
        $originalCountTag1 = $tagCount(LoadTagsTags::TAGS_TAG_1_ID);
        $originalCountTag2 = $tagCount(LoadTagsTags::TAGS_TAG_2_ID);
        TagsModel::saveTags(2, [LoadTagsTags::TAGS_TAG_1_NAME], 'Pages');
        self::assertSame($originalCountTag1 + 1, $tagCount(LoadTagsTags::TAGS_TAG_1_ID));
        self::assertSame($originalCountTag2 - 1, $tagCount(LoadTagsTags::TAGS_TAG_2_ID));
    }

    public function testSaveTagsFiltersOutDuplicates(): void
    {
        TagsModel::saveTags(1, ['page', 'Page', 'pAgE', 'page '], 'Pages');
        self::assertSame('page', TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsCreatesNewTagsIfNeeded(): void
    {
        $tag = 'New kid in town';
        self::assertFalse(TagsModel::existsTag($tag));
        TagsModel::saveTags(1, [$tag], 'Pages');
        self::assertTrue(TagsModel::existsTag($tag));
    }

    public function testSaveTagsReplacesOldLinks(): void
    {
        self::assertSame(
            implode(',', [LoadTagsTags::TAGS_TAG_2_NAME, LoadTagsTags::TAGS_TAG_1_NAME]),
            TagsModel::getTags('Pages', 1)
        );
        TagsModel::saveTags(1, [LoadTagsTags::TAGS_TAG_1_NAME], 'Pages');
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsForOtherLanguage(): void
    {
        self::assertSame(
            implode(',', [LoadTagsTags::TAGS_TAG_2_NAME, LoadTagsTags::TAGS_TAG_1_NAME]),
            TagsModel::getTags('Pages', 1)
        );
        TagsModel::saveTags(1, [LoadTagsTags::TAGS_TAG_1_NAME], 'Pages', 'nl');
        self::assertSame(
            implode(',', [LoadTagsTags::TAGS_TAG_2_NAME, LoadTagsTags::TAGS_TAG_1_NAME]),
            TagsModel::getTags('Pages', 1)
        );
        self::assertSame(LoadTagsTags::TAGS_TAG_1_NAME, TagsModel::getTags('Pages', 1, 'string', 'nl'));
    }

    public function testSaveTagsRemovesUnusedTags(): void
    {
        $id = TagsModel::insert('unused');
        self::assertTrue(TagsModel::exists($id));
        TagsModel::saveTags(420, [LoadTagsTags::TAGS_TAG_1_NAME, LoadTagsTags::TAGS_TAG_2_NAME], 'Pages');
        self::assertFalse(TagsModel::exists($id));
    }

    public function testGetAll(): void
    {
        $tags = [['name' => LoadTagsTags::TAGS_TAG_1_NAME], ['name' => LoadTagsTags::TAGS_TAG_2_NAME]];
        self::assertSame($tags, TagsModel::getAll());
        self::assertSame($tags, TagsModel::getAll('en'));
        self::assertEmpty(TagsModel::getAll('nl'));
    }

    public function testGetUrl(): void
    {
        self::assertSame(LoadTagsTags::TAGS_TAG_1_SLUG . '-2', TagsModel::getUrl(LoadTagsTags::TAGS_TAG_1_NAME));
        self::assertSame(LoadTagsTags::TAGS_TAG_1_SLUG, TagsModel::getUrl(LoadTagsTags::TAGS_TAG_1_NAME, 1));
    }

    public function testGetTagNames(): void
    {
        $tags = [LoadTagsTags::TAGS_TAG_1_NAME, LoadTagsTags::TAGS_TAG_2_NAME];
        self::assertSame($tags, TagsModel::getTagNames());
        self::assertSame($tags, TagsModel::getTagNames('en'));
        self::assertEmpty(TagsModel::getTagNames('nl'));
    }

    public function testExists(): void
    {
        self::assertTrue(TagsModel::exists(LoadTagsTags::TAGS_TAG_1_ID));
        self::assertTrue(TagsModel::exists(LoadTagsTags::TAGS_TAG_2_ID));
        self::assertFalse(TagsModel::exists(99));
        self::assertFalse(TagsModel::exists(9));
    }
}
