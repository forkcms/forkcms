<?php

namespace ForkCMS\Utility;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

/**
 * Akismet class
 *
 * @author Tijs Verkoyen <php-akismet@verkoyen.eu>
 * @author Jacob van Dam <jacob@jvdict.nl>
 * @version 2.0.0
 * @copyright Copyright (c) Tijs Verkoyen. All rights reserved.
 * @license BSD License
 */
class Akismet
{
    // internal constant to enable/disable debugging
    const DEBUG = false;

    // url for the api
    const API_URL = 'https://rest.akismet.com';

    // version of the api
    const API_VERSION = '1.1';

    // current version
    const VERSION = '2.0.0';

    /**
     * The key for the API
     * @var string
     */
    private string $apiKey;

    /**
     * The timeout
     * @var int
     */
    private int $timeOut = 60;

    /**
     * The user agent
     * @var string
     */
    private string $userAgent;

    /**
     * The url
     * @var string
     */
    private string $url;

    /**
     * Default constructor
     * Creates an instance of the Akismet Class.
     *
     * @param string $apiKey API key being verified for use with the API.
     * @param string $url The front page or home URL of the instance making
     *                       the request. For a blog or wiki this would be the
     *                       front page. Note: Must be a full URI, including
     *                       http://.
     */
    public function __construct(string $apiKey, string $url)
    {
        $this->setApiKey($apiKey);
        $this->setUrl($url);
    }

    /**
     * Make the call
     * @param string $url URL to call.
     * @param array $parameters The parameters to pass.
     * @param bool $authenticate Should we authenticate?
     * @return string
     * @throws \Exception
     */
    private function doCall(string $url, array $parameters = [], bool $authenticate = true)
    {
        // add key in front of url
        if ($authenticate) {
            // get api key
            $apiKey = $this->getApiKey();

            // validate apiKey
            if ($apiKey == '') {
                throw new \Exception('Invalid API-key');
            }
        }

        // add url into the parameters
        $parameters['blog'] = $this->getUrl();

        $client = new Client([
            'base_uri' => self::API_URL . '/' . self::API_VERSION . '/',
            'timeout' => self::getTimeOut(),
        ]);

        try {
            $response = $client->post($url, [
                'form_params' => $parameters,
                'headers' => [
                    'User-Agent' => $this->getUserAgent(),
                ],
            ]);
        } catch (ClientException|ServerException $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * Get the API-key that will be used
     *
     * @return string
     */
    private function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the timeout that will be used
     *
     * @return int
     */
    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    /**
     * Get the url of the instance making the request
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the useragent that will be used.
     * Our version will be prepended to yours. It will look like:
     * "PHP Akismet/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return 'PHP Akismet/' . self::VERSION . ' ' . $this->userAgent;
    }

    /**
     * Set API key that has to be used
     *
     * @param string $apiKey API key to use.
     */
    private function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Set the timeout
     * After this time the request will stop. You should handle any errors
     * triggered by this.
     *
     * @param int $seconds The timeout in seconds.
     */
    public function setTimeOut(int $seconds)
    {
        $this->timeOut = $seconds;
    }

    /**
     * Set the url of the instance making the request
     * @param string $url The URL making the request.
     */
    private function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like:
     * "PHP Akismet/<version> <your-user-agent>"
     *
     * @param string $userAgent The user-agent, it should look like:
     *                          <app-name>/<app-version>.
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
    }

    // api methods

    /**
     * Verifies the key
     * @return bool if the key is valid it will return true, otherwise false
     *              will be returned.
     * @throws \Exception
     */
    public function verifyKey(): bool
    {
        // possible answers
        $aPossibleResponses = ['valid', 'invalid'];

        // build parameters
        $aParameters['key'] = $this->getApiKey();

        // make the call
        $response = $this->doCall('verify-key', $aParameters, false);

        // validate response
        if (!in_array($response, $aPossibleResponses)) {
            throw new \Exception($response, 400);
        }

        // valid key
        return $response == 'valid';
    }

    /**
     * Check if the comment is spam or not
     * This is basically the core of everything. This call takes a number of
     * arguments and characteristics about the submitted content and then
     * returns a thumbs up or thumbs down.
     * Almost everything is optional, but performance can drop dramatically if
     * you exclude certain elements.
     * REMARK: If you are having trouble triggering you can send
     * "viagra-test-123" as the author and it will trigger a true response,
     * always.
     *
     * @param string $content The content that was submitted.
     * @param string|null $author The name.
     * @param string|null $email The email address.
     * @param string|null $url The URL.
     * @param string|null $permalink The permanent location of the entry
     *                                    the comment was submitted to.
     * @param string|null $type The type, can be blank, comment,
     *                                    trackback, pingback, or a made up
     *                                    value like "registration".
     * @return bool If the comment is spam true will be
     *                                    returned, otherwise false.
     * @throws \Exception
     */
    public function isSpam(
        string $content,
        string $author = null,
        string $email = null,
        string $url = null,
        string $permalink = null,
        string $type = null
    ): bool {
        // possible answers
        $possibleResponses = ['true', 'false'];

        // get stuff from the $_SERVER-array
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = (string)$_SERVER['HTTP_USER_AGENT'];
        } else {
            $userAgent = '';
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = (string)$_SERVER['HTTP_REFERER'];
        } else {
            $referrer = '';
        }

        // build parameters
        $parameters = [];
        $parameters['user_ip'] = $ip;
        $parameters['user_agent'] = $userAgent;
        if ($referrer) {
            $parameters['referrer'] = $referrer;
        }
        if ($permalink) {
            $parameters['permalink'] = $permalink;
        }
        if ($type) {
            $parameters['comment_type'] = $type;
        }
        if ($author) {
            $parameters['comment_author'] = $author;
        }
        if ($email) {
            $parameters['comment_author_email'] = $email;
        }
        if ($url) {
            $parameters['comment_author_url'] = $url;
        }
        $parameters['comment_content'] = $content;

        // add all stuff from $_SERVER
        foreach ($_SERVER as $key => $value) {
            // keys to ignore
            $keysToIgnore = [
                'HTTP_COOKIE',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED_HOST',
                'HTTP_MAX_FORWARDS',
                'HTTP_X_FORWARDED_SERVER',
                'REDIRECT_STATUS',
                'SERVER_PORT',
                'PATH',
                'DOCUMENT_ROOT',
                'SERVER_ADMIN',
                'QUERY_STRING',
                'PHP_SELF',
                'argv',
                'argc',
                'SCRIPT_FILENAME',
                'SCRIPT_NAME',
            ];

            // add to parameters if not in ignore list
            if (!in_array($key, $keysToIgnore)) {
                $parameters[$key] = $value;
            }
        }

        // make the call
        $response = $this->doCall('comment-check', $parameters);

        // validate response
        if (!in_array($response, $possibleResponses)) {
            throw new \Exception($response, 400);
        }

        return $response == 'true';
    }

    /**
     * Submit ham to Akismet
     * This call is intended for the marking of false positives, things that
     * were incorrectly marked as spam.
     * @param string $userIp The address of the comment submitter.
     * @param string $userAgent The agent information.
     * @param string $content The content that was submitted.
     * @param string|null $author The name of the author.
     * @param string|null $email The email address.
     * @param string|null $url The URL.
     * @param string|null $permalink The permanent location of the entry
     *                                    the comment was submitted to.
     * @param string|null $type The type, can be blank, comment,
     *                                    trackback, pingback, or a made up
     *                                    value like "registration".
     * @param string|null $referrer The content of the HTTP_REFERER
     *                                    header should be sent here.
     * @param array $others Extra data (the variables from
     *                                    $_SERVER).
     * @return bool If everything went fine true will be
     *                                    returned, otherwise an exception
     *                                    will be triggered.
     * @throws \Exception
     */
    public function submitHam(
        string $userIp,
        string $userAgent,
        string $content,
        string $author = null,
        string $email = null,
        string $url = null,
        string $permalink = null,
        string $type = null,
        string $referrer = null,
        array  $others = []
    ): bool {
        // possible answers
        $possibleResponses = ['Thanks for making the web a better place.'];

        // build parameters
        $parameters = [];
        $parameters['user_ip'] = $userIp;
        $parameters['user_agent'] = $userAgent;

        if ($referrer) {
            $parameters['referrer'] = $referrer;
        }

        if ($permalink) {
            $parameters['permalink'] = $permalink;
        }

        if ($type) {
            $parameters['comment_type'] = $type;
        }
        if ($author) {
            $parameters['comment_author'] = $author;
        }
        if ($email) {
            $parameters['comment_author_email'] = $email;
        }
        if ($url) {
            $parameters['comment_author_url'] = $url;
        }
        $parameters['comment_content'] = $content;

        // add other parameters
        foreach ($others as $key => $value) {
            $parameters[$key] = $value;
        }

        // make the call
        $response = $this->doCall('submit-ham', $parameters);

        // validate response
        if (in_array($response, $possibleResponses)) {
            return true;
        }

        // fallback
        throw new \Exception($response);
    }

    /**
     * Submit spam to Akismet
     * This call is for submitting comments that weren't marked as spam but
     * should have been.
     * @param string $userIp The address of the comment submitter.
     * @param string $userAgent The agent information.
     * @param string $content The content that was submitted.
     * @param string|null $author The name of the author.
     * @param string|null $email The email address.
     * @param string|null $url The URL.
     * @param string|null $permalink The permanent location of the entry
     *                                    the comment was submitted to.
     * @param string|null $type The type, can be blank, comment,
     *                                    trackback, pingback, or a made up
     *                                    value like "registration".
     * @param string|null $referrer The content of the HTTP_REFERER
     *                                    header should be sent here.
     * @param array $others Extra data (the variables from
     *                                    $_SERVER).
     * @return bool If everything went fine true will be
     *                                    returned, otherwise an exception
     *                                    will be triggered.
     * @throws \Exception
     */
    public function submitSpam(
        string $userIp,
        string $userAgent,
        string $content,
        string $author = null,
        string $email = null,
        string $url = null,
        string $permalink = null,
        string $type = null,
        string $referrer = null,
        array  $others = []
    ): bool {
        // possible answers
        $possibleResponses = ['Thanks for making the web a better place.'];

        // build parameters
        $parameters['user_ip'] = $userIp;
        $parameters['user_agent'] = $userAgent;
        if ($referrer) {
            $parameters['referrer'] = $referrer;
        }
        if ($permalink) {
            $parameters['permalink'] = $permalink;
        }
        if ($type) {
            $parameters['comment_type'] = $type;
        }
        if ($author) {
            $parameters['comment_author'] = $author;
        }
        if ($email) {
            $parameters['comment_author_email'] = $email;
        }
        if ($url) {
            $parameters['comment_author_url'] = $url;
        }
        $parameters['comment_content'] = $content;

        // add other parameters
        foreach ($others as $key => $value) {
            $parameters[$key] = $value;
        }

        // make the call
        $response = $this->doCall('submit-spam', $parameters);

        // validate response
        if (in_array($response, $possibleResponses)) {
            return true;
        }

        // fallback
        throw new \Exception($response);
    }
}
