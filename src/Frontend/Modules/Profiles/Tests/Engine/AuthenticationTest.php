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

        $this->assertFalse((bool) $this->database->getVar('SELECT 1 FROM profiles_sessions WHERE session_id = "1234567890"'));
    }

    public function testGettingLoginStatusForNonExistingUser()
    {
        $this->assertEquals('invalid', Authentication::getLoginStatus('non@existe.nt', 'wrong'));
    }

    public function testGettingLoginStatusForUserWithWrongPassword()
    {
        $this->assertEquals('invalid', Authentication::getLoginStatus('test-active@fork-cms.com', 'wrong'));
    }

    public function testGettingLoginStatusForActiveUserWithCorrectPassword()
    {
        $this->assertEquals('active', Authentication::getLoginStatus('test-active@fork-cms.com', 'forkcms'));
    }

    public function testGettingLoginStatusForInactiveUserWithCorrectPassword()
    {
        $this->assertEquals('inactive', Authentication::getLoginStatus('test-inactive@fork-cms.com', 'forkcms'));
    }

    public function testGettingLoginStatusForDeletedUserWithCorrectPassword()
    {
        $this->assertEquals('deleted', Authentication::getLoginStatus('test-deleted@fork-cms.com', 'forkcms'));
    }

    public function testGettingLoginStatusForBlockedUserWithCorrectPassword()
    {
        $this->assertEquals('blocked', Authentication::getLoginStatus('test-blocked@fork-cms.com', 'forkcms'));
    }
}
