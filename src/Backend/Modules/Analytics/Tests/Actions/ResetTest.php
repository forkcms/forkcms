<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Common\WebTestCase;

class ResetTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAuthenticationIsNeeded()
    {
        $this->logout();
        $client = static::createClient();

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/reset');

        // we should get redirected to authentication with a reference to the wanted page
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fanalytics%2Freset',
            $client->getHistory()->current()->getUri()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testAfterResetRedirectToSettings()
    {
        $client = static::createClient();
        $this->login();

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/reset');

        // we should have been redirected to the settings page after the reset
        self::assertContains(
            '/private/en/analytics/settings',
            $client->getHistory()->current()->getUri()
        );
    }
}
