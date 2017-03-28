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

/**
 * The retry policy abstract class.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
abstract class RetryPolicy
{
    const DEFAULT_CLIENT_BACKOFF     = 30000;
    const DEFAULT_CLIENT_RETRY_COUNT = 3;
    const DEFAULT_MAX_BACKOFF        = 90000;
    const DEFAULT_MIN_BACKOFF        = 300;
    
    /**
     * Indicates if there should be a retry or not.
     * 
     * @param integer                 $retryCount The retry count.
     * @param \HTTP_Request2_Response $response   The HTTP response object.
     * 
     * @return boolean
     */
    public abstract function shouldRetry($retryCount, $response);
    
    /**
     * Calculates the backoff for the retry policy.
     * 
     * @param integer                 $retryCount The retry count.
     * @param \HTTP_Request2_Response $response   The HTTP response object.
     * 
     * @return integer
     */
    public abstract function calculateBackoff($retryCount, $response);
}


