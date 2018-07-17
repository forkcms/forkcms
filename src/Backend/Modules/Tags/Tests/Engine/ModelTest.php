<?php

namespace Backend\Modules\Tags\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
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

    public function testGetStartsWith(): void
    {

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
