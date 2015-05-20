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
    public function createClient()
    {
        // create the instance
        $client = new Google_Client();
        $client->setAuthConfig(
            $this->settings->get('Analytics', 'secret_file')
        );
        $client->setRedirectUri(
            SITE_URL . strtok(Model::createURLForAction('Settings', 'Analytics'), '?')
        );
        $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

        // set the access token if we have one
        if ($this->settings->get('Analytics', 'token') !== null) {
            $this->setAccessToken($client);
        }

        return $client;
    }

    public function createAnalyticsService()
    {
        return new Google_Service_Analytics($this->createClient());
    }

    private function setAccessToken(Google_Client $client)
    {
        $client->setAccessToken($this->settings->get('Analytics', 'token'));

        // if our token is expired, refresh it
        if ($client->isAccesstokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->refreshToken($client->getRefreshToken());
                $this->settings->set('Analytics', 'token', $client->getAccessToken());
            } else {
                // we don't have a refresh token? Let's revoke access.
                // you only receive this refresh token the first time you request a token.
                $client->revokeToken();
                $this->settings->set('Analytics', 'token', null);
            }
        }
    }
}
