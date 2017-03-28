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
 * The exponential retry policy.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ExponentialRetryPolicy extends RetryPolicy
{
    /**
     * @var integer
     */
    private $_deltaBackoffIntervalInMs;
    
    /**
     * @var integer
     */
    private $_maximumAttempts;
    
    /**
     * @var integer
     */
    private $_resolvedMaxBackoff;
    
    /**
     * @var integer
     */
    private $_resolvedMinBackoff;
    
    /**
     * @var array
     */
    private $_retryableStatusCodes;
    
    /**
     * Initializes new object from ExponentialRetryPolicy.
     * 
     * @param array   $retryableStatusCodes The retryable status codes.
     * @param integer $deltaBackoff         The backoff time delta.
     * @param integer $maximumAttempts      The number of max attempts.
     */
    public function __construct($retryableStatusCodes, 
        $deltaBackoff = parent::DEFAULT_CLIENT_BACKOFF, 
        $maximumAttempts = parent::DEFAULT_CLIENT_RETRY_COUNT
    ) {
        $this->_deltaBackoffIntervalInMs = $deltaBackoff;
        $this->_maximumAttempts          = $maximumAttempts;
        $this->_resolvedMaxBackoff       = parent::DEFAULT_MAX_BACKOFF;
        $this->_resolvedMinBackoff       = parent::DEFAULT_MIN_BACKOFF;
        $this->_retryableStatusCodes     = $retryableStatusCodes;
        sort($retryableStatusCodes);
    }
    
    /**
     * Indicates if there should be a retry or not.
     * 
     * @param integer                 $retryCount The retry count.
     * @param \HTTP_Request2_Response $response   The HTTP response object.
     * 
     * @return boolean
     */
    public function shouldRetry($retryCount, $response)
    {
        if (  $retryCount >= $this->_maximumAttempts
            || array_search($response->getStatus(), $this->_retryableStatusCodes)
            || is_null($response)     
        ) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Calculates the backoff for the retry policy.
     * 
     * @param integer                 $retryCount The retry count.
     * @param \HTTP_Request2_Response $response   The HTTP response object.
     * 
     * @return integer
     */
    public function calculateBackoff($retryCount, $response)
    {
        // Calculate backoff Interval between 80% and 120% of the desired
        // backoff, multiply by 2^n -1 for
        // exponential
        $incrementDelta   = (int) (pow(2, $retryCount) - 1);
        $boundedRandDelta = (int) ($this->_deltaBackoffIntervalInMs * 0.8)
            + mt_rand(
                0,
                (int) ($this->_deltaBackoffIntervalInMs * 1.2)
                - (int) ($this->_deltaBackoffIntervalInMs * 0.8)
            );
        $incrementDelta  *= $boundedRandDelta;

        // Enforce max / min backoffs
        return min(
            $this->_resolvedMinBackoff + $incrementDelta,
            $this->_resolvedMaxBackoff
        );
    }
}


