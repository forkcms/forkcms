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
 * Optional parameters for list messages wrapper.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListMessagesOptions extends QueueServiceOptions
{
    /**
     * A nonzero integer value that specifies the number of messages to retrieve 
     * from the queue, up to a maximum of 32. If fewer are visible, 
     * the visible messages are returned. By default, a single message is retrieved 
     * from the queue with this operation.
     * 
     * @var integer
     */
    private $_numberOfMessages;
    
    /**
     * Specifies the new visibility timeout value, in seconds, 
     * relative to server time. The new value must be larger than or equal to 
     * 1 second, and cannot be larger than 7 days, or larger than 2 hours on 
     * REST protocol versions prior to version 2011-08-18. 
     * The visibility timeout of a message can be set to a value later than the 
     * expiry time.
     * 
     * @var integer
     */
    private $_visibilityTimeoutInSeconds;
    
    /**
     * Gets visibilityTimeoutInSeconds field.
     * 
     * @return integer
     */
    public function getVisibilityTimeoutInSeconds()
    {
        return $this->_visibilityTimeoutInSeconds;
    }
    
    /**
     * Sets visibilityTimeoutInSeconds field.
     * 
     * @param integer $visibilityTimeoutInSeconds value to use.
     * 
     * @return none
     */
    public function setVisibilityTimeoutInSeconds($visibilityTimeoutInSeconds)
    {
        $this->_visibilityTimeoutInSeconds = $visibilityTimeoutInSeconds;
    }
    
    /**
     * Gets numberOfMessages field.
     * 
     * @return integer
     */
    public function getNumberOfMessages()
    {
        return $this->_numberOfMessages;
    }
    
    /**
     * Sets numberOfMessages field.
     * 
     * @param integer $numberOfMessages value to use.
     * 
     * @return none
     */
    public function setNumberOfMessages($numberOfMessages)
    {
        $this->_numberOfMessages = $numberOfMessages;
    }
}


