<?php

namespace Backend\Modules\Tags\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language;
use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Modules\Tags\Engine\Model as TagsModel;
use Common\WebTestCase;

final class ModelTest extends WebTestCase
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
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testGetStartsWithForDefaultLanguage(): void
    {
        $tags = TagsModel::getStartsWith('te');
        $this->assertSame('test', $tags[0]['name']);
        $this->assertSame('test', $tags[0]['value']);
    }

    public function testGetStartsWithForLanguage(): void
    {
        $tags = TagsModel::getStartsWith('te', Language::getWorkingLanguage());
        $this->assertSame('test', $tags[0]['name']);
        $this->assertSame('test', $tags[0]['value']);
        $this->assertEmpty(TagsModel::getStartsWith('te', 'nl'));
    }

    public function testExistsTag(): void
    {
        $this->assertTrue(TagsModel::existsTag('test'));
        $this->assertFalse(TagsModel::existsTag('non-existing'));
    }

    public function testInsertWithDefaultLanguage(): void
    {
        $name = 'inserted';
        $tagId = TagsModel::insert($name);
        $database = self::createClient()->getContainer()->get('database');
        $this->assertSame(
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
        $this->assertSame(
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
        $this->assertTrue(TagsModel::exists(1));
        TagsModel::delete(1);
        $this->assertFalse(TagsModel::exists(1));
    }

    public function testDeleteMultiple(): void
    {
        $this->assertTrue(TagsModel::exists(2));
        $this->assertTrue(TagsModel::exists(1));
        TagsModel::delete([2, 1]);
        $this->assertFalse(TagsModel::exists(2));
        $this->assertFalse(TagsModel::exists(1));
    }

    public function testGetTags(): void
    {
        $this->assertSame('most used,test', TagsModel::getTags('Pages', 1));
        $this->assertSame(['most used', 'test'], TagsModel::getTags('Pages', 1, 'array'));
        $this->assertSame('', TagsModel::getTags('Pages', 1, 'string', 'nl'));
        $this->assertSame([], TagsModel::getTags('Pages', 1, 'array', 'nl'));
    }

    public function testGet(): void
    {
        $this->assertSame('test', TagsModel::get(1)['name']);
        $this->assertSame('most used', TagsModel::get(2)['name']);
    }

    public function testUpdate(): void
    {
        $this->assertSame('test', TagsModel::get(1)['name']);
        TagsModel::update(['id' => 1, 'tag' => 'changed']);
        $this->assertSame('changed', TagsModel::get(1)['name']);
    }

    public function testSaveTagsAlsoAcceptsAString(): void
    {
        $this->assertSame('most used,test', TagsModel::getTags('Pages', 1));
        TagsModel::saveTags(1, 'test,concat', 'Pages');
        $this->assertSame('concat,test', TagsModel::getTags('Pages', 1));
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
        $this->assertSame($originalCountTag1 + 1, $tagCount(1));
        $this->assertSame($originalCountTag2 - 1, $tagCount(2));
    }

    public function testSaveTagsFiltersOutDuplicates(): void
    {
        TagsModel::saveTags(1, ['page', 'Page', 'pAgE', 'page '], 'Pages');
        $this->assertSame('page', TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsCreatesNewTagsIfNeeded(): void
    {
        $tag = 'New kid in town';
        $this->assertFalse(TagsModel::existsTag($tag));
        TagsModel::saveTags(1, [$tag], 'Pages');
        $this->assertTrue(TagsModel::existsTag($tag));
    }

    public function testSaveTagsReplacesOldLinks(): void
    {
        $this->assertSame('most used,test', TagsModel::getTags('Pages', 1));
        TagsModel::saveTags(1, ['test'], 'Pages');
        $this->assertSame('test', TagsModel::getTags('Pages', 1));
    }

    public function testSaveTagsRemovesUnusedTags(): void
    {
        $id = TagsModel::insert('unused');
        $this->assertTrue(TagsModel::exists($id));
        TagsModel::saveTags(420, ['test','most used'], 'Pages');
        $this->assertFalse(TagsModel::exists($id));
    }

    public function testGetAll(): void
    {
        $tags = [['name' => 'test'], ['name' => 'most used']];
        $this->assertSame($tags, TagsModel::getAll());
        $this->assertSame($tags, TagsModel::getAll('en'));
        $this->assertEmpty(TagsModel::getAll('nl'));
    }

    public function testGetUrl(): void
    {
        $this->assertSame('test-2', TagsModel::getUrl('test'));
        $this->assertSame('test', TagsModel::getUrl('test', 1));
    }

    public function testGetTagNames(): void
    {
        $tags = ['test', 'most used'];
        $this->assertSame($tags, TagsModel::getTagNames());
        $this->assertSame($tags, TagsModel::getTagNames('en'));
        $this->assertEmpty(TagsModel::getTagNames('nl'));
    }

    public function testExists(): void
    {
        $this->assertTrue(TagsModel::exists(1));
        $this->assertTrue(TagsModel::exists(2));
        $this->assertFalse(TagsModel::exists(99));
        $this->assertFalse(TagsModel::exists(9));
    }
}
