<?php

namespace Backend\Modules\Authentication\Tests\Action;

use Common\WebTestCase;
use Backend\Core\Engine\Authentication as Authentication;

class LogoutTest extends WebTestCase
{
    public function testLogoutActionActuallyLogsYouOut(): void
    {
        $client = static::createClient();
        $this->login();

        self::assertTrue(Authentication::isLoggedIn());

        $client->request('GET', '/private/en/authentication/logout');
        $client->followRedirect();

        self::assertFalse(Authentication::isLoggedIn());
    }

    public function testLogoutActionRedirectsYouToLoginAfterLoggingOut(): void
    {
        $client = static::createClient();
        $this->login();

        $client->request('GET', '/private/en/authentication/logout');
        $client->followRedirect();

        self::assertContains(
            '/private/en/authentication/index',
            $client->getHistory()->current()->getUri()
        );
    }
}
