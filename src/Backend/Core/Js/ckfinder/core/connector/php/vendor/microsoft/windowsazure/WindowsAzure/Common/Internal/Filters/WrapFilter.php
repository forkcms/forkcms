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
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal\Filters;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\IServiceFilter;
use WindowsAzure\Common\Internal\Authentication\SharedKeyAuthScheme;
use WindowsAzure\Common\Internal\Authentication\TableSharedKeyLiteAuthScheme;
use WindowsAzure\Common\Internal\InvalidArgumentTypeException;
use WindowsAzure\ServiceBus\Internal\WrapTokenManager;


/**
 * Adds WRAP authentication header to the http request object.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class WrapFilter implements IServiceFilter
{
    /**
     * @var WrapTokenManager
     */
    private $_wrapTokenManager;

    /**
     * Creates a WrapFilter with specified WRAP parameters.
     *
     * @param string $wrapUri       The URI of the WRAP service. 
     * @param string $wrapUsername  The user name of the WRAP account.
     * @param string $wrapPassword  The password of the WRAP account.
     * @param IWrap  $wrapRestProxy The WRAP service REST proxy.
     */
    public function __construct(
        $wrapUri, 
        $wrapUsername, 
        $wrapPassword,
        $wrapRestProxy
    ) {
        $this->_wrapTokenManager = new WrapTokenManager(
            $wrapUri, 
            $wrapUsername, 
            $wrapPassword,
            $wrapRestProxy
        );
    }

    /**
     * Adds WRAP authentication header to the request headers.
     *
     * @param HttpClient $request HTTP channel object.
     * 
     * @return \HTTP_Request2
     */
    public function handleRequest($request)
    {
        Validate::notNull($request, 'request');
        $wrapAccessToken = $this->_wrapTokenManager->getAccessToken(
            $request->getUrl()
        );
        
        $authorization = sprintf(
            Resources::WRAP_AUTHORIZATION,
            $wrapAccessToken
        );
        
        $request->setHeader(Resources::AUTHENTICATION, $authorization);

        return $request;
    }

    /**
     * Returns the original response.
     *
     * @param HttpClient              $request  A HTTP channel object.
     * @param \HTTP_Request2_Response $response A HTTP response object.
     * 
     * @return \HTTP_Request2_Response
     */
    public function handleResponse($request, $response) 
    {
        return $response;
    }
}


