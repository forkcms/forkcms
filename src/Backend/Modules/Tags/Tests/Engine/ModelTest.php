<?php

namespace Backend\Modules\Tags\Tests\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model;
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
    }
}
