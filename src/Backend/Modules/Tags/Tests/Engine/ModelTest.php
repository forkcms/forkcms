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
        $this->assertTrue($this->checkIfTagExists(1));
        TagsModel::delete(1);
        $this->assertFalse($this->checkIfTagExists(1));
    }

    public function testDeleteMultiple(): void
    {
        $this->assertTrue($this->checkIfTagExists(2));
        $this->assertTrue($this->checkIfTagExists(1));
        TagsModel::delete([2, 1]);
        $this->assertFalse($this->checkIfTagExists(2));
        $this->assertFalse($this->checkIfTagExists(1));
    }

    public function testGetTags(): void
    {

    }

    public function testGet(): void
    {

    }

    public function testUpdate(): void
    {

    }

    public function testSaveTags(): void
    {

    }

    public function testGetAll(): void
    {

    }

    public function testGetUrl(): void
    {

    }

    public function testGetTagNames(): void
    {

    }

    public function testExists(): void
    {

    }

    private function checkIfTagExists(int $id): bool
    {
        $database = self::createClient()->getContainer()->get('database');

        return (bool) $database->getVar('SELECT 1 FROM tags WHERE id = ?', $id);
    }
}
