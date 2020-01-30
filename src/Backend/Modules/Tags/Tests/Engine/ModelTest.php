<?php

namespace Backend\Modules\Tags\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language;
use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Modules\Tags\Engine\Model as TagsModel;
use Backend\Core\Tests\BackendWebTestCase;

final class ModelTest extends BackendWebTestCase
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

    public function testGetStartsWithForDefaultLanguage(): void
    {
        $tags = TagsModel::getStartsWith('te');
        self::assertSame('test', $tags[0]['name']);
        self::assertSame('test', $tags[0]['value']);
    }

    public function testGetStartsWithForLanguage(): void
    {
        $tags = TagsModel::getStartsWith('te', Language::getWorkingLanguage());
        self::assertSame('test', $tags[0]['name']);
        self::assertSame('test', $tags[0]['value']);
        self::assertSame(TagsModel::getStartsWith('te', 'nl'));
    }

    public function testExistsTag(): void
    {
        self::assertTrue(TagsModel::existsTag('test'));
        self::assertFalse(TagsModel::existsTag('non-existing'));
    }

    public function testInsertWithDefaultLanguage(): void
    {
        $name = 'inserted';
        $tagId = TagsModel::insert($name);
        $database = self::createClient()->getContainer()->get('database');
        self::assertSame(
            $tagId,
            (int) $database->getVar(
                'SELECT id FROM tags WHERE tag = ? AND language = ?',
                [$name, Language::getWorkingLanguage()]
            )
        );
    }

    public function testInsertWithSpecificLanguage(): void
    {
        $name = 'inserted';
        $language = 'nl';
        $tagId = TagsModel::insert($name, $language);
        $database = self::createClient()->getContainer()->get('database');
        self::assertSame(
            $tagId,
            (int) $database->getVar(
                'SELECT id FROM tags WHERE tag = ? AND language = ?',
                [$name, $language]
            )
        );
    }

    public function testDeleteSingle(): void
    {
        // check single
        self::assertTrue(TagsModel::exists(1));
        TagsModel::delete(1);
        self::assertFalse(TagsModel::exists(1));
    }

    public function testDeleteMultiple(): void
    {
        self::assertTrue(TagsModel::exists(2));
        self::assertTrue(TagsModel::exists(1));
        TagsModel::delete([2, 1]);
        self::assertFalse(TagsModel::exists(2));
        self::assertFalse(TagsModel::exists(1));
    }

    public function testGetTags(): void
    {
        self::assertSame('most used,test', TagsModel::getTags('Pages', 1));
        self::assertSame(['most used', 'test'], TagsModel::getTags('Pages', 1, 'array'));
        self::assertSame('', TagsModel::getTags('Pages', 1, 'string', 'nl'));
        self::assertSame([], TagsModel::getTags('Pages', 1, 'array', 'nl'));
    }

    public function testGet(): void
    {
        self::assertSame('test', TagsModel::get(1)['name']);
        self::assertSame('most used', TagsModel::get(2)['name']);
    }

    public function testUpdate(): void
    {
        self::assertSame('test', TagsModel::get(1)['name']);
        TagsModel::update(['id' => 1, 'tag' => 'changed']);
        self::assertSame('changed', TagsModel::get(1)['name']);
    }

    public function testSaveTagsAlsoAcceptsAString(): void
    {
        self::assertSame('most used,test', TagsModel::getTags('Pages', 1));
        TagsModel::saveTags(1, 'test,concat', 'Pages');
        self::assertSame('concat,test', TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsUpdatesTheUsedCount(): void
    {
        $database = self::createClient()->getContainer()->get('database');
        $tagCount = function (int $id) use ($database): int {
            return $database->getVar('SELECT number FROM tags WHERE id = ?', $id);
        };
        $originalCountTag1 = $tagCount(1);
        $originalCountTag2 = $tagCount(2);
        TagsModel::saveTags(2, ['test'], 'Pages');
        self::assertSame($originalCountTag1 + 1, $tagCount(1));
        self::assertSame($originalCountTag2 - 1, $tagCount(2));
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
        self::assertSame('most used,test', TagsModel::getTags('Pages', 1));
        TagsModel::saveTags(1, ['test'], 'Pages');
        self::assertSame('test', TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsForOtherLanguage(): void
    {
        self::assertSame('most used,test', TagsModel::getTags('Pages', 1));
        TagsModel::saveTags(1, ['test'], 'Pages', 'nl');
        self::assertSame('most used,test', TagsModel::getTags('Pages', 1));
        self::assertSame('test', TagsModel::getTags('Pages', 1, 'string', 'nl'));
    }

    public function testSaveTagsRemovesUnusedTags(): void
    {
        $id = TagsModel::insert('unused');
        self::assertTrue(TagsModel::exists($id));
        TagsModel::saveTags(420, ['test','most used'], 'Pages');
        self::assertFalse(TagsModel::exists($id));
    }

    public function testGetAll(): void
    {
        $tags = [['name' => 'test'], ['name' => 'most used']];
        self::assertSame($tags, TagsModel::getAll());
        self::assertSame($tags, TagsModel::getAll('en'));
        self::assertSame(TagsModel::getAll('nl'));
    }

    public function testGetUrl(): void
    {
        self::assertSame('test-2', TagsModel::getUrl('test'));
        self::assertSame('test', TagsModel::getUrl('test', 1));
    }

    public function testGetTagNames(): void
    {
        $tags = ['test', 'most used'];
        self::assertSame($tags, TagsModel::getTagNames());
        self::assertSame($tags, TagsModel::getTagNames('en'));
        self::assertSame(TagsModel::getTagNames('nl'));
    }

    public function testExists(): void
    {
        self::assertTrue(TagsModel::exists(1));
        self::assertTrue(TagsModel::exists(2));
        self::assertFalse(TagsModel::exists(99));
        self::assertFalse(TagsModel::exists(9));
    }
}
