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
 * Holds optional parameters for createMessage wrapper.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CreateMessageOptions extends QueueServiceOptions
{
    /**
     * If specified, the request must be made using an x-ms-version 
     * of 2011-08-18 or newer. If not specified, the default value is 0. 
     * Specifies the new visibility timeout value, in seconds, relative to server 
     * time. The new value must be larger than or equal to 0, and cannot be 
     * larger than 7 days. The visibility timeout of a message cannot be set to a 
     * value later than the expiry time. visibilitytimeout should be set to a 
     * value smaller than the time-to-live value.
     * 
     * @var integer
     */
    private $_visibilityTimeoutInSeconds;
    
    /**
     * Specifies the time-to-live interval for the message, in seconds. 
     * The maximum time-to-live allowed is 7 days. If this parameter is omitted, 
     * the default time-to-live is 7 days.
     * 
     * @var integer
     */
    private $_timeToLiveInSeconds;
    
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
     * Gets timeToLiveInSeconds field.
     * 
     * @return integer
     */
    public function getTimeToLiveInSeconds()
    {
        return $this->_timeToLiveInSeconds;
    }
    
    /**
     * Sets timeToLiveInSeconds field.
     * 
     * @param integer $timeToLiveInSeconds value to use.
     * 
     * @return none
     */
    public function setTimeToLiveInSeconds($timeToLiveInSeconds)
    {
        $this->_timeToLiveInSeconds = $timeToLiveInSeconds;
    }
}


