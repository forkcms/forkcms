<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fanalytics%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testRedirectToSettingsActionWhenTheAnalyticsModuleIsNotConfigured(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/reset');

        // we should have been redirected to the settings page after the reset
        self::assertContains(
            '/private/en/analytics/settings',
            $client->getHistory()->current()->getUri()
        );
    }
}
