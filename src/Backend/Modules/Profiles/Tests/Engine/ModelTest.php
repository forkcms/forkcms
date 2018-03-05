<?php

namespace Backend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\Engine\Model;
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

    public function testPasswordGetsEncrypted(): void
    {
        $encryptedPassword = Model::encryptPassword($this->getPassword());

        $this->assertTrue(password_verify($this->getPassword(), $encryptedPassword));
    }

    public function getPassword(): string
    {
        return 'forkcms';
    }
}
