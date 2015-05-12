<?php

namespace Backend\Modules\Analytics\GoogleClient;

use Backend\Core\Engine\Model;
use Google_Client;
use Google_Service_Analytics;

/**
 * Factory to easily create google client instances
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class ClientFactory
{
    /**
     * Creates a google client
     *
     * @return Google_Client
     */
    public static function createClient()
    {
        // create the instance
        $client = new Google_Client();
        $client->setAuthConfigFile(
            BACKEND_CACHE_PATH . '/Analytics/'
            . Model::getModuleSetting('Analytics', 'secret_file')
        );
        $client->setRedirectUri(
            'http://localhost' . strtok(Model::createURLForAction('Settings', 'Analytics'), '?')
        );
        $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

        // set the access token if we have one
        if (Model::getModuleSetting('Analytics', 'token') !== null) {
            $client->setAccessToken(Model::getModuleSetting('Analytics', 'token'));
        }

        return $client;
    }

    public static function createAnalyticsService()
    {
        return new Google_Service_Analytics(self::createClient());
    }
}
