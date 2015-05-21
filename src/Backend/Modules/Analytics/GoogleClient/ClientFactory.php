<?php

namespace Backend\Modules\Analytics\GoogleClient;

use Backend\Core\Engine\Model;
use Google_Client;
use Google_Service_Analytics;
use Common\ModulesSettings;

/**
 * Factory to easily create google client instances
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class ClientFactory
{
    /**
     * @var ModulesSettings
     */
    private $settings;

    public function __construct(ModulesSettings $modulesSettings)
    {
        $this->settings = $modulesSettings;
    }

    /**
     * Creates a google client
     *
     * @return Google_Client
     */
    public static function createClient()
    {
        $client = new \Google_Client();

        // set assertion credentials
        $client->setAssertionCredentials(
            new \Google_Auth_AssertionCredentials(
                $this->settings->get('Analytics', 'email'),
                array('https://www.googleapis.com/auth/analytics.readonly'),
                base64_decode($this->settings->get('Analytics', 'certificate'))
            )
        );

        // other settings
        $client->setClientId($this->settings->get('Analytics', 'client_id'));
        $client->setAccessType('offline_access');

        return $client;
    }

    public static function createAnalyticsService()
    {
        return new Google_Service_Analytics(self::createClient());
    }
}
