<?php

namespace Backend\Modules\Analytics\GoogleClient;

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

    /**
     * @var string
     */
    private $cacheDir;

    public function __construct(ModulesSettings $modulesSettings, $cacheDir)
    {
        $this->settings = $modulesSettings;
        $this->cacheDir = $cacheDir;
    }

    /**
     * Creates a google client
     *
     * @return Google_Client
     */
    public function createClient()
    {
        $config = new \Google_Config();
        $config->setClassConfig(
            'Google_Cache_File',
            array('directory' => $this->cacheDir)
        );

        $client = new \Google_Client($config);

        // set assertion credentials
        $client->setAssertionCredentials(
            new \Google_Auth_AssertionCredentials(
                $this->settings->get('Analytics', 'email'),
                array('https://www.googleapis.com/auth/analytics.readonly'),
                base64_decode($this->settings->get('Analytics', 'certificate'))
            )
        );

        $client->setAccessType('offline_access');

        return $client;
    }

    public function createAnalyticsService()
    {
        return new Google_Service_Analytics($this->createClient());
    }
}
