<?php

namespace Frontend\Modules\Profiles\Tests\Engine;

use Backend\Modules\Profiles\DataFixtures\LoadProfilesProfile;
use Frontend\Core\Tests\FrontendWebTestCase;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Common\WebTestCase;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Authentication;
use SpoonDatabase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

final class AuthenticationTest extends FrontendWebTestCase
{
    /** @var Session */
    private $session;

    /** @var string */
    private const SECRET_COOKIE_KEY = 'NotSoSecret';

    protected function setUp(): void
    {
        parent::setUp();

        $client = $this->getProvidedData()[0];
        $this->loadFixtures($client, [LoadProfilesProfile::class]);

        $this->session = FrontendModel::getSession();

        // Create a request stack for cookie stuff
        $requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $request->cookies->set('frontend_profile_secret_key', self::SECRET_COOKIE_KEY);
        $requestStack->push($request);
        $client->getContainer()->set('request_stack', $requestStack);
    }

    public function testOldSessionCleanUp(Client $client): void
    {
        $database = $this->getDatabase($client);

        self::assertEquals('2', $database->getVar('SELECT COUNT(sessionId) FROM ProfilesSession'));

        Authentication::cleanupOldSessions();

        self::assertEquals('1', $database->getVar('SELECT COUNT(sessionId) FROM ProfilesSession'));
        self::assertFalse(
            (bool) $database->getVar(
                'SELECT 1 FROM ProfilesSession WHERE sessionId = ?',
                LoadProfilesProfile::PROFILES_OLD_SESSION_ID
            )
        );
    }

    public function testGettingLoginStatusForNonExistingUser(): void
    {
        self::assertEquals(Status::invalid(), Authentication::getLoginStatus('non@existe.nt', 'wrong'));
    }

    public function testGettingLoginStatusForUserWithWrongPassword(): void
    {
        self::assertEquals(
            Status::invalid(),
            Authentication::getLoginStatus(LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL, 'wrong')
        );
    }

    public function testGettingLoginStatusForActiveUserWithCorrectPassword(): void
    {
        self::assertEquals(
            Status::active(),
            Authentication::getLoginStatus(
                LoadProfilesProfile::PROFILES_ACTIVE_PROFILE_EMAIL,
                LoadProfilesProfile::PROFILES_PROFILE_PASSWORD
            )
        );
    }

    public function testGettingLoginStatusForInactiveUserWithCorrectPassword(): void
    {
        self::assertEquals(
            Status::inactive(),
            Authentication::getLoginStatus(
                LoadProfilesProfile::PROFILES_INACTIVE_PROFILE_EMAIL,
                LoadProfilesProfile::PROFILES_PROFILE_PASSWORD
            )
        );
    }

    public function testGettingLoginStatusForDeletedUserWithCorrectPassword(): void
    {
        self::assertEquals(
            Status::deleted(),
            Authentication::getLoginStatus(
                LoadProfilesProfile::PROFILES_DELETED_PROFILE_EMAIL,
                LoadProfilesProfile::PROFILES_PROFILE_PASSWORD
            )
        );
    }

    public function testGettingLoginStatusForBlockedUserWithCorrectPassword(): void
    {
        self::assertEquals(
            Status::blocked(),
            Authentication::getLoginStatus(
                LoadProfilesProfile::PROFILES_BLOCKED_PROFILE_EMAIL,
                LoadProfilesProfile::PROFILES_PROFILE_PASSWORD
            )
        );
    }

    public function testLoggingInMakesUsLoggedIn(): void
    {
        Authentication::login(LoadProfilesProfile::getProfileActiveId());
        self::assertTrue(Authentication::isLoggedIn());
    }

    public function testLoggingInCleansUpOldSessions(Client $client): void
    {
        $database = $this->getDatabase($client);

        self::assertEquals('2', $database->getVar('SELECT COUNT(sessionId) FROM ProfilesSession'));

        Authentication::login(LoadProfilesProfile::getProfileActiveId());
        self::assertEquals('2', $database->getVar('SELECT COUNT(session_id) FROM profiles_sessions'));

        self::assertFalse(
            (bool) $database->getVar(
                'SELECT 1 FROM ProfilesSession WHERE sessionId = ?',
                LoadProfilesProfile::PROFILES_OLD_SESSION_ID
            )
        );
    }

    public function testLoggingInSetsASessionVariable(): void
    {
        self::assertNull(FrontendModel::getSession()->get('frontend_profile_logged_in'));

        Authentication::login(LoadProfilesProfile::getProfileActiveId());

        self::assertTrue(FrontendModel::getSession()->get('frontend_profile_logged_in'));
    }

    public function testLoggingInAddsASessionToTheDatabase(Client $client): void
    {
        $database = $this->getDatabase($client);

        self::assertEquals(
            '0',
            $database->getVar(
                'SELECT COUNT(sessionId)
                 FROM ProfilesSession
                 WHERE profile_id = ?',
                LoadProfilesProfile::getProfileInactiveId()
            )
        );

        Authentication::login(LoadProfilesProfile::getProfileInactiveId());

        self::assertEquals(
            '1',
            $database->getVar(
                'SELECT COUNT(sessionId)
                 FROM ProfilesSession
                 WHERE profile_id = ?',
                LoadProfilesProfile::getProfileInactiveId()
            )
        );
    }

    public function testProfileLastLoginGetsUpdatedWhenLoggingIn(Client $client): void
    {
        $database = $this->getDatabase($client);

        $profileId = LoadProfilesProfile::getProfileActiveId();
        $initialLastLogin = $database->getVar('SELECT lastLogin FROM ProfilesProfile WHERE id = ?', $profileId);

        Authentication::login($profileId);

        $newLastLogin = $database->getVar('SELECT lastLogin FROM ProfilesProfile WHERE id = ?', $profileId);

        self::assertLessThan($newLastLogin, $initialLastLogin);
    }

    public function testLogoutDeletesSessionFromDatabase(Client $client): void
    {
        $database = $this->getDatabase($client);

        $database->insert(
            'ProfilesSession',
            [
                'sessionId' => $this->session->getId(),
                'profile_id' => LoadProfilesProfile::getProfileActiveId(),
                'secretKey' => 'Fork is da bomb',
                'date' => '1970-01-01 00:00:00',
            ]
        );

        self::assertTrue(
            (bool) $database->getVar(
                'SELECT 1 FROM ProfilesSession WHERE sessionId = ?',
                $this->session->getId()
            )
        );

        Authentication::logout();

        self::assertFalse(
            (bool) $database->getVar(
                'SELECT 1 FROM ProfilesSession WHERE sessionId = ?',
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

    public function testLogoutDeletesSecretKeyCookie(Client $client): void
    {
        $cookie = $client->getContainer()->get('fork.cookie');

        self::assertTrue($cookie->has('frontend_profile_secret_key'));
        self::assertEquals(self::SECRET_COOKIE_KEY, $cookie->get('frontend_profile_secret_key'));

        Authentication::logout();

        self::assertFalse($cookie->has('frontend_profile_secret_key'));
        self::assertNotEquals(self::SECRET_COOKIE_KEY, $cookie->get('frontend_profile_secret_key'));
    }

    private function getDatabase(Client $client): SpoonDatabase
    {
        return $client->getContainer()->get('database');
    }
}
