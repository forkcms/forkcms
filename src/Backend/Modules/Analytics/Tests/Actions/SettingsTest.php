<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Backend\Core\Language\Language;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class SettingsTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/analytics/settings');
    }

    public function testAnalyticsSettingsWorks(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly($client, '/private/en/analytics/settings', [Language::msg('CertificateHelp', 'Analytics')]);
    }
}
