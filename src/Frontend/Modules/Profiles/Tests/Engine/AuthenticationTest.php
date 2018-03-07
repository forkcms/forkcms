<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Common\WebTestCase;
use DateTime;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Authentication;
use SpoonDatabase;

final class AuthenticationTest extends WebTestCase
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

    public function testOldSessionCleanUp()
    {
        $dateWithinAMonthAgo = new DateTime('-1 week');
        $dateOverAMonthAgo = new DateTime('-2 months');

        /** @var SpoonDatabase $database */
        $database = FrontendModel::get('database');

        $database->insert(
            'profiles_sessions',
            [
                [
                    'session_id' => '0123456789',
                    'profile_id' => 1,
                    'secret_key' => 'NotSoSecretNowIsIt',
                    'date' => $dateWithinAMonthAgo->format('Y-m-d H:i:s'),
                ],
                [
                    'session_id' => '1234567890',
                    'profile_id' => 1,
                    'secret_key' => 'WeNeedToTalk',
                    'date' => $dateOverAMonthAgo->format('Y-m-d H:i:s'),
                ],
            ]
        );

        $this->assertEquals('2', $database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));

        Authentication::cleanupOldSessions();

        $this->assertEquals('1', $database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));
    }
}
