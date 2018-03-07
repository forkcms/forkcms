<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Common\WebTestCase;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Authentication;
use Frontend\Modules\Profiles\Tests\DataFixtures\LoadProfiles;
use SpoonDatabase;

final class AuthenticationTest extends WebTestCase
{
    /** @var SpoonDatabase */
    private $database;

    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }

        $client = self::createClient();
        $this->loadFixtures($client, [LoadProfiles::class]);

        $this->database = FrontendModel::get('database');
    }

    public function testOldSessionCleanUp()
    {
        $this->assertEquals('2', $this->database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));

        Authentication::cleanupOldSessions();

        $this->assertEquals('1', $this->database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));
    }
}
