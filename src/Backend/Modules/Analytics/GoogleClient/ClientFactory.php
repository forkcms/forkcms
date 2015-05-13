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
            self::setAccessToken($client);
        }

        return $client;
    }

    public static function createAnalyticsService()
    {
        return new Google_Service_Analytics(self::createClient());
    }

    private static function setAccessToken(Google_Client $client)
    {
        $client->setAccessToken(Model::getModuleSetting('Analytics', 'token'));

        // if our token is expired, refresh it
        if ($client->isAccesstokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->refreshToken($client->getRefreshToken());
                Model::setModuleSetting('Analytics', 'token', $client->getAccessToken());
            } else {
                // we don't have a refresh token? Let's revoke access.
                // you only receive this refresh token the first time you request a token.
                $client->revokeToken();
                Model::setModuleSetting('Analytics', 'token', null);
            }
        }
    }
}
