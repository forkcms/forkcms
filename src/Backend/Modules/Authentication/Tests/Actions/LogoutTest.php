<?php

namespace Backend\Modules\Authentication\Tests\Action;

use Common\WebTestCase;
use Backend\Core\Engine\Authentication as Authentication;

class LogoutTest extends WebTestCase
{
    public function testLogoutActionRedirectsYouToLoginAfterLoggingOut(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/authentication/logout');
        $client->followRedirect();

        self::assertContains(
            '/private/en/authentication/index',
            $client->getHistory()->current()->getUri()
        );
    }
}
