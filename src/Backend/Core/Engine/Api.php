<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Api\V1\Engine\Api as BaseAPI;

use Backend\Core\Engine\Model as BackendModel;

use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Api
{
    /**
     * Add an Apple device to a user.
     *
     * @param string $token The token of the device.
     * @param string $email The emailaddress for the user to link the device to.
     */
    public static function appleAddDevice($token, $email)
    {
        if (BaseAPI::isAuthorized()) {
            $token = str_replace(' ', '', (string) $token);

            // validate
            if ($token == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No token-parameter provided.'));
            }
            if ($email == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No email-parameter provided.'));
            }

            // we should tell the ForkAPI that we registered a device
            $publicKey = BackendModel::getModuleSetting('Core', 'fork_api_public_key', '');
            $privateKey = FrontendModel::getModuleSetting('Core', 'fork_api_private_key', '');

            // validate keys
            if ($publicKey == '' || $privateKey == '') {
                BaseAPI::output(
                    BaseAPI::BAD_REQUEST,
                    array('message' => 'Invalid key for the Fork API, configure them in the backend.')
                );
            }

            try {
                // load user
                $user = new User(null, $email);

                // get current tokens
                $tokens = (array) $user->getSetting('apple_device_token');

                // not already in array?
                if (!in_array($token, $tokens)) {
                    $tokens[] = $token;
                }

                // require the class
                require_once PATH_LIBRARY . '/external/fork_api.php';

                // create instance
                $forkAPI = new \ForkAPI($publicKey, $privateKey);

                // make the call
                $forkAPI->appleRegisterDevice($token);

                // store
                if (!empty($tokens)) {
                    $user->setSetting('apple_device_token', $tokens);
                }
            } catch (Exception $e) {
                BaseAPI::output(BaseAPI::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
            }
        }
    }

    /**
     * Remove an Apple device from a user.
     *
     * @param string $token The token of the device.
     * @param string $email The emailaddress for the user to link the device to.
     */
    public static function appleRemoveDevice($token, $email)
    {
        if (BaseAPI::isAuthorized()) {
            // redefine
            $token = str_replace(' ', '', (string) $token);

            // validate
            if ($token == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No token-parameter provided.'));
            }
            if ($email == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No email-parameter provided.'));
            }

            try {
                // load user
                $user = new User(null, $email);

                // get current tokens
                $tokens = (array) $user->getSetting('apple_device_token');

                // not already in array?
                $index = array_search($token, $tokens);

                if ($index !== false) {
                    // remove from array
                    unset($tokens[$index]);

                    // save it
                    $user->setSetting('apple_device_token', $tokens);
                }
            } catch (Exception $e) {
                BaseAPI::output(BaseAPI::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
            }
        }
    }

    /**
     * Get the API-key for a user.
     *
     * @param string $email    The emailaddress for the user.
     * @param string $password The password for the user.
     * @return array
     */
    public static function getAPIKey($email, $password)
    {
        $email = (string) $email;
        $password = (string) $password;

        // validate
        if ($email == '') {
            BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No email-parameter provided.'));
        }
        if ($password == '') {
            BaseAPI::output(
                BaseAPI::BAD_REQUEST,
                array('message' => 'No password-parameter provided.')
            );
        }

        // load user
        try {
            $user = new User(null, $email);
        } catch (Exception $e) {
            BaseAPI::output(BaseAPI::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
        }

        // validate password
        if (!Authentication::loginUser($email, $password)) {
            BaseAPI::output(
                BaseAPI::FORBIDDEN,
                array('message' => 'Can\'t authenticate you.')
            );
        } else {
            // does the user have access?
            if ($user->getSetting('api_access', false) == false) {
                BaseAPI::output(
                    BaseAPI::FORBIDDEN,
                    array('message' => 'Your account isn\'t allowed to use the API. Contact an administrator.')
                );
            } else {
                // create the key if needed
                if ($user->getSetting('api_key', null) == null) {
                    $user->setSetting('api_key', uniqid());
                }

                // return the key
                return array('api_key' => $user->getSetting('api_key'));
            }
        }
    }

    /**
     * Get info about the site.
     *
     * @return array
     */
    public static function getInfo()
    {
        if (BaseAPI::isAuthorized()) {
            $info = array();

            // get all languages
            $languages = Language::getActiveLanguages();
            $default = BackendModel::getModuleSetting('Core', 'default_language', SITE_DEFAULT_LANGUAGE);

            // loop languages
            foreach ($languages as $language) {
                // create array
                $var = array();

                // set attributes
                $var['language']['@attributes']['language'] = $language;
                if ($language == $default) {
                    $var['language']['@attributes']['is_default'] = 'true';
                }

                // set attributes
                $var['language']['title'] = BackendModel::getModuleSetting('Core', 'site_title_' . $language);
                $var['language']['url'] = SITE_URL . '/' . $language;

                // add
                $info['languages'][] = $var;
            }

            return $info;
        }
    }

    /**
     * Add a Microsoft device to a user.
     *
     * @param string $uri   The uri of the channel opened for the device.
     * @param string $email The emailaddress for the user to link the device to.
     */
    public static function microsoftAddDevice($uri, $email)
    {
        if (BaseAPI::isAuthorized()) {
            // redefine
            $uri = (string) $uri;

            // validate
            if ($uri == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No uri-parameter provided.'));
            }
            if ($email == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No email-parameter provided.'));
            }

            // we should tell the ForkAPI that we registered a device
            $publicKey = BackendModel::getModuleSetting('Core', 'fork_api_public_key', '');
            $privateKey = FrontendModel::getModuleSetting('Core', 'fork_api_private_key', '');

            // validate keys
            if ($publicKey == '' || $privateKey == '') {
                BaseAPI::output(
                    BaseAPI::BAD_REQUEST,
                    array('message' => 'Invalid key for the Fork API, configure them in the backend.')
                );
            }

            try {
                // load user
                $user = new User(null, $email);

                // get current uris
                $uris = (array) $user->getSetting('microsoft_channel_uri');

                // not already in array?
                if (!in_array($uri, $uris)) {
                    $uris[] = $uri;
                }

                // require the class
                require_once PATH_LIBRARY . '/external/fork_api.php';

                // create instance
                $forkAPI = new \ForkAPI($publicKey, $privateKey);

                // make the call
                $forkAPI->microsoftRegisterDevice($uris);

                // store
                if (!empty($uris)) {
                    $user->setSetting('microsoft_channel_uri', $uris);
                }
            } catch (Exception $e) {
                BaseAPI::output(BaseAPI::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
            }
        }
    }

    /**
     * Remove a device from a user.
     *
     * @param string $uri   The uri of the channel opened for the device.
     * @param string $email The emailaddress for the user to link the device to.
     */
    public static function microsoftRemoveDevice($uri, $email)
    {
        if (BaseAPI::isAuthorized()) {
            // redefine
            $uri = (string) $uri;

            // validate
            if ($uri == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No uri-parameter provided.'));
            }
            if ($email == '') {
                BaseAPI::output(BaseAPI::BAD_REQUEST, array('message' => 'No email-parameter provided.'));
            }

            try {
                // load user
                $user = new User(null, $email);

                // get current uris
                $uris = (array) $user->getSetting('microsoft_channel_uri');

                // not already in array?
                $index = array_search($uri, $uris);

                if ($index !== false) {
                    // remove from array
                    unset($uris[$index]);

                    // save it
                    $user->setSetting('microsoft_channel_uri', $uris);
                }
            } catch (Exception $e) {
                BaseAPI::output(BaseAPI::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
            }
        }
    }
}
