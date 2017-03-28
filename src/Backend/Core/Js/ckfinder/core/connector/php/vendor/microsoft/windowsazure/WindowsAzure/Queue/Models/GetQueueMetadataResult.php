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

/**
 * Holds result from calling GetQueueMetadata wrapper
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetQueueMetadataResult
{
    /**
     * Indicates the approximate number of messages in the queue
     * 
     * @var integer 
     */
    private $_approximateMessageCount;
    
    /**
     * A user-defined name/value pair
     * 
     * @var array 
     */
    private $_metadata;
    
    /**
     * Constructor
     * 
     * @param integer $approximateMessageCount Approximate number of queue messages.
     * @param array   $metadata                user defined metadata.
     */
    public function __construct($approximateMessageCount, $metadata)
    {
        $this->_approximateMessageCount = $approximateMessageCount;
        $this->_metadata                = is_null($metadata) ? array() : $metadata;
    }
    
    /**
     * Gets approximate message count.
     * 
     * @return integer
     */
    public function getApproximateMessageCount()
    {
        return $this->_approximateMessageCount;
    }
    
    /**
     * Sets approximate message count.
     * 
     * @param integer $approximateMessageCount value to use.
     * 
     * @return none
     */
    public function setApproximateMessageCount($approximateMessageCount)
    {
        $this->_approximateMessageCount = $approximateMessageCount;
    }
    
    /**
     * Sets metadata.
     * 
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
    
    /**
     * Sets metadata.
     * 
     * @param array $metadata value to use.
     * 
     * @return none
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }

}


