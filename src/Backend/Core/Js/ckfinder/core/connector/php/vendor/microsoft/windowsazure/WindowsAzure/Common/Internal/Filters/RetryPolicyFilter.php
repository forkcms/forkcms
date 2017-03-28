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
use WindowsAzure\Common\Internal\IServiceFilter;

/**
 * Short description
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RetryPolicyFilter implements IServiceFilter
{
    /**
     * @var RetryPolicy
     */
    private $_retryPolicy;

    /**
     * Initializes new object from RetryPolicyFilter.
     * 
     * @param RetryPolicy $retryPolicy The retry policy object.
     */
    public function __construct($retryPolicy)
    {
        $this->_retryPolicy = $retryPolicy;
    }

    /**
     * Handles the request before sending.
     * 
     * @param \HTTP_Request2 $request The HTTP request.
     * 
     * @return \HTTP_Request2
     */
    public function handleRequest($request)
    {
        return $request;
    }

    /**
     * Handles the response after sending.
     * 
     * @param \HTTP_Request2          $request  The HTTP request.
     * @param \HTTP_Request2_Response $response The HTTP response.
     * 
     * @return \HTTP_Request2_Response
     */
    public function handleResponse($request, $response)
    {
        for ($retryCount = 0;; $retryCount++) {
            $shouldRetry = $this->_retryPolicy->shouldRetry(
                $retryCount,
                $response
            );
            
            if (!$shouldRetry) {
                return $response;
            }
            
            // Backoff for some time according to retry policy
            $backoffTime = $this->_retryPolicy->calculateBackoff(
                $retryCount,
                $response
            );
            sleep($backoffTime * 0.001);
            $response = $request->send(array());
        }
    }
}


