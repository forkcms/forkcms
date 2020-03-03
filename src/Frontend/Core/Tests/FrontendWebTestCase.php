<?php

namespace Frontend\Core\Tests;

use Common\WebTestCase;

abstract class FrontendWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }
    }
}
