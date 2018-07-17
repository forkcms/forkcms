<?php

namespace Backend\Modules\Tags\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
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
        $this->loadFixtures($client);


        BackendModel::get('database')->execute(
            'INSERT INTO `modules_tags` (`module`, `tag_id`, `other_id`)
            VALUES
                (\'Pages\', 1, 1),
                (\'Pages\', 2, 2),
                (\'Pages\', 2, 3),
                (\'Pages\', 2, 404),
                (\'Pages\', 2, 405),
                (\'Pages\', 2, 406),
                (\'Faq\', 1, 1)'
        );
        BackendModel::get('database')->execute(
            'INSERT INTO `tags` (`id`, `language`, `tag`, `number`, `url`)
            VALUES
                (1, \'en\', \'test\', 1, \'test\'),
                (2, \'en\', \'most used\', 5, \'most-used\')'
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
