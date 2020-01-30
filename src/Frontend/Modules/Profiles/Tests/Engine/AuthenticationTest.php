<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Frontend\Core\Tests\FrontendWebTestCase;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Authentication;
use Frontend\Modules\Profiles\Tests\DataFixtures\LoadProfiles;
use SpoonDatabase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

final class AuthenticationTest extends FrontendWebTestCase
{
    /** @var SpoonDatabase */
    private $database;

    /** @var Session */
    private $session;

    protected function setUp(): void
    {
        parent::setUp();

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
        self::assertEquals('2', $this->database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));

        Authentication::cleanupOldSessions();

        self::assertFalse((bool) $this->database->getVar('SELECT 1 FROM profiles_sessions WHERE session_id = "1234567890"'));
    }

    public function testGettingLoginStatusForNonExistingUser()
    {
        self::assertEquals('invalid', Authentication::getLoginStatus('non@existe.nt', 'wrong'));
    }

    public function testGettingLoginStatusForUserWithWrongPassword()
    {
        self::assertEquals('invalid', Authentication::getLoginStatus('test-active@fork-cms.com', 'wrong'));
    }

    public function testGettingLoginStatusForActiveUserWithCorrectPassword()
    {
        self::assertEquals('active', Authentication::getLoginStatus('test-active@fork-cms.com', 'forkcms'));
    }

    public function testGettingLoginStatusForInactiveUserWithCorrectPassword()
    {
        self::assertEquals('inactive', Authentication::getLoginStatus('test-inactive@fork-cms.com', 'forkcms'));
    }

    public function testGettingLoginStatusForDeletedUserWithCorrectPassword()
    {
        self::assertEquals('deleted', Authentication::getLoginStatus('test-deleted@fork-cms.com', 'forkcms'));
    }

    public function testGettingLoginStatusForBlockedUserWithCorrectPassword()
    {
        self::assertEquals('blocked', Authentication::getLoginStatus('test-blocked@fork-cms.com', 'forkcms'));
    }

    public function testLoggingInMakesUsLoggedIn()
    {
        Authentication::login(1);
        self::assertTrue(Authentication::isLoggedIn());
    }

    public function testLoggingInCleansUpOldSessions()
    {
        self::assertEquals('2', $this->database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));

        Authentication::login(1);

        self::assertFalse((bool) $this->database->getVar('SELECT 1 FROM profiles_sessions WHERE session_id = "1234567890"'));
    }

    public function testLoggingInSetsASessionVariable()
    {
        self::assertNull(FrontendModel::getSession()->get('frontend_profile_logged_in'));

        Authentication::login(1);

        self::assertTrue(FrontendModel::getSession()->get('frontend_profile_logged_in'));
    }

    public function testLogginInAddsASessionToTheDatabase()
    {
        self::assertEquals(
            '0',
            $this->database->getVar(
                'SELECT COUNT(session_id)
                 FROM profiles_sessions
                 WHERE profile_id = 2'
            )
        );

        Authentication::login(2);

        self::assertEquals(
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

        self::assertLessThan($newLastLogin, $initalLastLogin);
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

        self::assertTrue(
            (bool) $this->database->getVar(
                'SELECT 1 FROM profiles_sessions WHERE session_id = ?',
                $this->session->getId()
            )
        );

        Authentication::logout();

        self::assertFalse(
            (bool) $this->database->getVar(
                'SELECT 1 FROM profiles_sessions WHERE session_id = ?',
                $this->session->getId()
            )
        );
    }

    public function testLogoutSetsLoggedInSessionToFalse(): void
    {
        $this->session->set('frontend_profile_logged_in', true);
        self::assertTrue($this->session->get('frontend_profile_logged_in'));

        Authentication::logout();

        self::assertFalse($this->session->get('frontend_profile_logged_in'));
    }

    public function testLogoutDeletesSecretKeyCookie(): void
    {
        $cookie = FrontendModel::getContainer()->get('fork.cookie');

        self::assertTrue($cookie->has('frontend_profile_secret_key'));
        self::assertEquals('NotSoSecret', $cookie->get('frontend_profile_secret_key'));

        Authentication::logout();

        self::assertFalse($cookie->has('frontend_profile_secret_key'));
        self::assertNotEquals('NotSoSecret', $cookie->get('frontend_profile_secret_key'));
    }
}
