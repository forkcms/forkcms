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

    }

    public function testInsert(): void
    {

    }

    public function testDelete(): void
    {

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
}
