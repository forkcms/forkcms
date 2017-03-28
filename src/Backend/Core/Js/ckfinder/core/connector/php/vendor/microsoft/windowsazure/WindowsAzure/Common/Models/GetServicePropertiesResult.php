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
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Models;
use WindowsAzure\Common\Models\ServiceProperties;

/**
 * Result from calling GetQueueProperties REST wrapper.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetServicePropertiesResult
{
    private $_serviceProperties;
    
    /**
     * Creates object from $parsedResponse.
     * 
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return WindowsAzure\Common\Models\GetServicePropertiesResult
     */
    public static function create($parsedResponse)
    {
        $result                     = new GetServicePropertiesResult();
        $result->_serviceProperties = ServiceProperties::create($parsedResponse);
        
        return $result;
    }
    
    /**
     * Gets service properties object.
     * 
     * @return WindowsAzure\Common\Models\ServiceProperties 
     */
    public function getValue()
    {
        return $this->_serviceProperties;
    }
    
    /**
     * Sets service properties object.
     * 
     * @param ServiceProperties $serviceProperties object to use.
     * 
     * @return none 
     */
    public function setValue($serviceProperties)
    {
        $this->_serviceProperties = clone $serviceProperties;
    }
}


