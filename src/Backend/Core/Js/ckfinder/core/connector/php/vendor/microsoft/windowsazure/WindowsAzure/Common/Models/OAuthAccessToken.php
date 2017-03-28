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
 * @package   WindowsAzure\Common\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Models;
use WindowsAzure\Common\Internal\Resources;

/**
 * Holds OAuth access token data.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class OAuthAccessToken
{
    /**
     * Access token itself
     *
     * @var string
     */
    private $_accessToken;

    /**
     * Unix time the access token valid before.
     *
     * @var int
     */
    private $_expiresIn;

    /**
     * Scope of access token
     *
     * @var string.
     */
    private $_scope;

    /**
     * Creates object from $parsedResponse.
     *
     * @param array $parsedResponse JSON response parsed into array.
     *
     * @return WindowsAzure\Common\Models\OAuthAccessToken
     */
    public static function create($parsedResponse)
    {
        $result = new OAuthAccessToken();

        $result->setAccessToken($parsedResponse[Resources::OAUTH_ACCESS_TOKEN]);
        $result->setExpiresIn($parsedResponse[Resources::OAUTH_EXPIRES_IN] + time());
        $result->setScope($parsedResponse[Resources::OAUTH_SCOPE]);

        return $result;
    }

    /**
     * Gets access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }


    /**
     * Sets access token
     *
     * @param string $accessToken OAuth access token
     *
     * @return none
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
    }


    /**
     * Gets expired date of access token in unixdate
     *
     * @return int
     *
     */
    public function getExpiresIn()
    {
        return $this->_expiresIn;
    }


    /**
     * Sets access token expires date
     *
     * @param int $expiresIn OAuth access token expire date
     *
     * @return none
     */
    public function setExpiresIn($expiresIn)
    {
        $this->_expiresIn = $expiresIn;
    }

    /**
     * Gets access token scope
     *
     * @return string
     *
     */
    public function getScope()
    {
        return $this->_scope;
    }


    /**
     * Sets access token scope
     *
     * @param string $scope OAuth access token scope
     *
     * @return none
     */
    public function setScope($scope)
    {
        $this->_scope = $scope;
    }
}


