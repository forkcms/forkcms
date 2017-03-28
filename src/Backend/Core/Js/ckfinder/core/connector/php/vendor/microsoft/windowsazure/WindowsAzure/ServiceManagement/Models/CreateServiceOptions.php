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
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceManagement\Models;
use WindowsAzure\Common\Internal\Validate;

/**
 * Optional parameters for createStorageService API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CreateServiceOptions
{
    /**
     * @var string
     */
    private $_location;
    
    /**
     * @var string
     */
    private $_affinityGroup;
    
    /**
     * @var string
     */
    private $_description;
    
    /**
     * Gets the location.
     * 
     * @return string
     */
    public function getLocation()
    {
        return $this->_location;
    }
    
    /**
     * Sets the location.
     * 
     * @param string $location The location.
     * 
     * @return none
     */
    public function setLocation($location)
    {
        Validate::isString($location, 'location');
        Validate::notNullOrEmpty($location, 'location');
        
        $this->_location = $location;
    }
    
    /**
     * Gets the affinityGroup.
     * 
     * @return string
     */
    public function getAffinityGroup()
    {
        return $this->_affinityGroup;
    }
    
    /**
     * Sets the affinityGroup.
     * 
     * @param string $affinityGroup The affinityGroup.
     * 
     * @return none
     */
    public function setAffinityGroup($affinityGroup)
    {
        Validate::isString($affinityGroup, 'affinityGroup');
        Validate::notNullOrEmpty($affinityGroup, 'affinityGroup');
        
        $this->_affinityGroup = $affinityGroup;
    }
    
    /**
     * Gets the description.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
    
    /**
     * Sets the description.
     * 
     * @param string $description The description.
     * 
     * @return none
     */
    public function setDescription($description)
    {
        Validate::isString($description, 'description');
        
        $this->_description = $description;
    }
}


