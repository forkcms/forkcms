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
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Queue\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Queue\Models\Queue;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Container to hold list queue response object.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListQueuesResult
{
    private $_queues;
    private $_prefix;
    private $_marker;
    private $_nextMarker;
    private $_maxResults;
    private $_accountName;

    /**
     * Creates ListQueuesResult object from parsed XML response.
     *
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return WindowsAzure\Queue\Models\ListQueuesResult.
     */
    public static function create($parsedResponse)
    {
        $result               = new ListQueuesResult();
        $result->_accountName = Utilities::tryGetKeysChainValue(
            $parsedResponse,
            Resources::XTAG_ATTRIBUTES,
            Resources::XTAG_ACCOUNT_NAME
        );
        $result->_prefix      = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_PREFIX
        );
        $result->_marker      = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_MARKER
        );
        $result->_nextMarker  = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_NEXT_MARKER
        );
        $result->_maxResults  = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_MAX_RESULTS
        );
        $result->_queues      = array();
        $rawQueues            = array();
        
        if ( !empty($parsedResponse['Queues']) ) {
            $rawQueues = Utilities::getArray($parsedResponse['Queues']['Queue']);
        }
        
        foreach ($rawQueues as $value) {
            $queue    = new Queue($value['Name'], $value['Url']);
            $metadata = Utilities::tryGetValue($value, Resources::QP_METADATA);
            $queue->setMetadata(is_null($metadata) ? array() : $metadata);
            $result->_queues[] = $queue;
        }
        
        return $result;
    }

    /**
     * Gets queues.
     *
     * @return array.
     */
    public function getQueues()
    {
        return $this->_queues;
    }
    
    /**
     * Sets queues.
     *
     * @param array $queues list of queues
     * 
     * @return none.
     */
    public function setQueues($queues)
    {
        $this->_queues = array();
        foreach ($queues as $queue) {
            $this->_queues[] = clone $queue;
        }
    }

    /**
     * Gets prefix.
     *
     * @return string.
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Sets prefix.
     *
     * @param string $prefix value.
     * 
     * @return none.
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    /**
     * Gets marker.
     * 
     * @return string.
     */
    public function getMarker()
    {
        return $this->_marker;
    }

    /**
     * Sets marker.
     *
     * @param string $marker value.
     * 
     * @return none.
     */
    public function setMarker($marker)
    {
        $this->_marker = $marker;
    }

    /**
     * Gets max results.
     * 
     * @return string.
     */
    public function getMaxResults()
    {
        return $this->_maxResults;
    }

    /**
     * Sets max results.
     *
     * @param string $maxResults value.
     * 
     * @return none.
     */
    public function setMaxResults($maxResults)
    {
        $this->_maxResults = $maxResults;
    }

    /**
     * Gets next marker.
     * 
     * @return string.
     */
    public function getNextMarker()
    {
        return $this->_nextMarker;
    }

    /**
     * Sets next marker.
     *
     * @param string $nextMarker value.
     * 
     * @return none.
     */
    public function setNextMarker($nextMarker)
    {
        $this->_nextMarker = $nextMarker;
    }
    
    /**
     * Gets account name.
     * 
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }

    /**
     * Sets account name.
     *
     * @param string $accountName value.
     * 
     * @return none
     */
    public function setAccountName($accountName)
    {
        $this->_accountName = $accountName;
    }
}