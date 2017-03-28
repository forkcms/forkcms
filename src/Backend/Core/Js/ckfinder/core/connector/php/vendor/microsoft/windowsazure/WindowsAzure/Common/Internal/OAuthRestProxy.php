<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Internal;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\ServiceRestProxy;
use WindowsAzure\Common\Models\OAuthAccessToken;
use WindowsAzure\Common\Internal\Serialization\JsonSerializer;

/**
 * OAuth rest proxy.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class OAuthRestProxy extends ServiceRestProxy
{
    /**
     * Initializes new OAuthRestProxy object.
     *
     * @param IHttpClient $channel The HTTP client used to send HTTP requests.
     * @param string      $uri     The storage account uri.
     */
    public function __construct($channel, $uri)
    {
        parent::__construct(
            $channel,
            $uri,
            Resources::EMPTY_STRING,
            new JsonSerializer()
        );
    }


    /**
     * Get OAuth access token.
     *
     * @param string $grantType    OAuth request grant_type field value.
     * @param string $clientId     OAuth request clent_id field value.
     * @param string $clientSecret OAuth request clent_secret field value.
     * @param string $scope        OAuth request scope field value.
     *
     * @return WindowsAzure\Common\Internal\Models\OAuthAccessToken
     */
    public function getAccessToken($grantType, $clientId, $clientSecret, $scope)
    {
        $method         = Resources::HTTP_POST;
        $headers        = array();
        $queryParams    = array();
        $postParameters = array();
        $statusCode     = Resources::STATUS_OK;

        $postParameters = $this->addPostParameter(
            $postParameters,
            Resources::OAUTH_GRANT_TYPE,
            $grantType
        );

        $postParameters = $this->addPostParameter(
            $postParameters,
            Resources::OAUTH_CLIENT_ID,
            $clientId
        );

        $postParameters = $this->addPostParameter(
            $postParameters,
            Resources::OAUTH_CLIENT_SECRET,
            $clientSecret
        );

        $postParameters = $this->addPostParameter(
            $postParameters,
            Resources::OAUTH_SCOPE,
            $scope
        );

        $response = $this->send(
            $method,
            $headers,
            $queryParams,
            $postParameters,
            Resources::EMPTY_STRING,
            $statusCode
        );

        return OAuthAccessToken::create(
            $this->dataSerializer->unserialize($response->getBody())
        );
    }
}


