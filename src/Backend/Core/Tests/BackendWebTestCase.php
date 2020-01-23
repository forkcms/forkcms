<?php

namespace Backend\Core\Tests;

use Common\WebTestCase;

abstract class BackendWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetDataBase(static::createClient());
    }
}
