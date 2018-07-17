<?php

namespace Frontend\Modules\Tags\Tests\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Tags\Engine\Model;
use Common\WebTestCase;

final class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }

        $client = self::createClient();
        $this->loadFixtures($client);
    }
}
