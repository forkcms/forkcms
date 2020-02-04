<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Common\WebTestCase;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Authentication;
use Frontend\Modules\Profiles\Tests\DataFixtures\LoadProfiles;
use SpoonDatabase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

final class AuthenticationTest extends WebTestCase
{
    /** @var SpoonDatabase */
    private $database;

    /** @var Session */
    private $session;

    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }

        $client = self::createClient();
        $this->loadFixtures($client, [LoadProfiles::class]);

        $this->database = FrontendModel::get('database');
        $this->session = FrontendModel::getSession();

        // Create a request stack for cookie stuff
        $requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $request->cookies->set('frontend_profile_secret_key', 'NotSoSecret');
        $requestStack->push($request);
        FrontendModel::getContainer()->set('request_stack', $requestStack);
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

    public function testLoggingInMakesUsLoggedIn()
    {
        Authentication::login(1);
        $this->assertTrue(Authentication::isLoggedIn());
    }

    public function testLoggingInCleansUpOldSessions()
    {
        $this->assertEquals('2', $this->database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));

        Authentication::login(1);

        $this->assertFalse((bool) $this->database->getVar('SELECT 1 FROM profiles_sessions WHERE session_id = "1234567890"'));
    }

    public function testLoggingInSetsASessionVariable()
    {
        $this->assertNull(FrontendModel::getSession()->get('frontend_profile_logged_in'));

        Authentication::login(1);

        $this->assertTrue(FrontendModel::getSession()->get('frontend_profile_logged_in'));
    }

    public function testLogginInAddsASessionToTheDatabase()
    {
        $this->assertEquals(
            '0',
            $this->database->getVar(
                'SELECT COUNT(session_id) 
                 FROM profiles_sessions
                 WHERE profile_id = 2'
            )
        );

        Authentication::login(2);

        $this->assertEquals(
            '1',
            $this->database->getVar(
                'SELECT COUNT(session_id) 
                 FROM profiles_sessions
                 WHERE profile_id = 2'
            )
        );
    }

    public function testProfileLastLoginGetsUpdatedWhenLoggingIn()
    {
        $initalLastLogin = $this->database->getVar('SELECT last_login FROM profiles WHERE id = 1');

        Authentication::login(1);

        $newLastLogin = $this->database->getVar('SELECT last_login FROM profiles WHERE id = 1');

        $this->assertLessThan($newLastLogin, $initalLastLogin);
    }

    public function testLogoutDeletesSessionFromDatabase(): void
    {
        $this->database->insert(
            'profiles_sessions',
            [
                'session_id' => $this->session->getId(),
                'profile_id' => 1,
                'secret_key' => 'Fork is da bomb',
                'date' => '1970-01-01 00:00:00',
            ]
        );

        $this->assertTrue(
            (bool) $this->database->getVar(
                'SELECT 1 FROM profiles_sessions WHERE session_id = ?',
                $this->session->getId()
            )
        );

        Authentication::logout();

        $this->assertFalse(
            (bool) $this->database->getVar(
                'SELECT 1 FROM profiles_sessions WHERE session_id = ?',
                $this->session->getId()
            )
        );
    }

    public function testLogoutSetsLoggedInSessionToFalse(): void
    {
        $this->session->set('frontend_profile_logged_in', true);
        $this->assertTrue($this->session->get('frontend_profile_logged_in'));

        Authentication::logout();

        $this->assertFalse($this->session->get('frontend_profile_logged_in'));
    }

    public function testLogoutDeletesSecretKeyCookie(): void
    {
        $cookie = FrontendModel::getContainer()->get('fork.cookie');

        $this->assertTrue($cookie->has('frontend_profile_secret_key'));
        $this->assertEquals('NotSoSecret', $cookie->get('frontend_profile_secret_key'));

        Authentication::logout();

        $this->assertFalse($cookie->has('frontend_profile_secret_key'));
        $this->assertNotEquals('NotSoSecret', $cookie->get('frontend_profile_secret_key'));
    }
}
