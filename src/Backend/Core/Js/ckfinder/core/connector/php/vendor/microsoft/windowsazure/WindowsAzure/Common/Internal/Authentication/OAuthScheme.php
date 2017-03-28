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
 * @package   WindowsAzure\Common\Internal\Authentication
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      http://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Internal\Authentication;
use WindowsAzure\Common\Internal\Authentication\IAuthScheme;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\OAuthRestProxy;
use WindowsAzure\Common\Models\OAuthAccessToken;

/**
 * Provides shared key authentication scheme for OAuth.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Authentication
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      http://github.com/windowsazure/azure-sdk-for-php
 */
class OAuthScheme implements IAuthScheme
{
    /**
     * @var string
     */
    protected $accountName;

    /**
     * @var string
     */
    protected $accountKey;

    /**
     * @var WindowsAzure\Common\Models\OAuthAccessToken
     */
    protected $accessToken;

    /**
     * @var WindowsAzure\Common\Internal\OAuthRestProxy
     */
    protected $oauthService;

    /**
     * @var string
     */
    protected $grantType;

    /**
     * @var string
     */
    protected $scope;

    /**
     * Constructor.
     *
     * @param string                                      $accountName  account name.
     * @param string                                      $accountKey   account
     * secondary key.
     *
     * @param string                                      $grantType    grant type
     * for OAuth request.
     *
     * @param string                                      $scope        scope for
     * OAurh request.
     *
     * @param WindowsAzure\Common\Internal\OAuthRestProxy $oauthService account
     * primary or secondary key.
     */
    public function __construct(
        $accountName,
        $accountKey,
        $grantType,
        $scope,
        $oauthService)
    {
        Validate::isString($accountName, 'accountName');
        Validate::isString($accountKey, 'accountKey');
        Validate::isString($grantType, 'grantType');
        Validate::isString($scope, 'scope');
        Validate::notNull($oauthService, 'oauthService');

        $this->accountName  = $accountName;
        $this->accountKey   = $accountKey;
        $this->grantType    = $grantType;
        $this->scope        = $scope;
        $this->oauthService = $oauthService;
    }

    /**
     * Returns authorization header to be included in the request.
     *
     * @param array  $headers     request headers.
     * @param string $url         reuqest url.
     * @param array  $queryParams query variables.
     * @param string $httpMethod  request http method.
     *
     * @see Specifying the Authorization Header section at
     *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     *
     * @return string
     */
    public function getAuthorizationHeader($headers, $url, $queryParams, $httpMethod)
    {
        if (($this->accessToken == null)
            || ($this->accessToken->getExpiresIn() < time())) {
            $this->accessToken = $this->oauthService->getAccessToken(
                $this->grantType,
                $this->accountName,
                $this->accountKey,
                $this->scope
            );
        }

        return Resources::OAUTH_ACCESS_TOKEN_PREFIX .
            $this->accessToken->getAccessToken();
    }
}

