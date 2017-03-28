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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * The result of calling getAffinityGroupProperties API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetAffinityGroupPropertiesResult
{
    /**
     * @var AffinityGroup
     */
    private $_affinityGroup;
    
    /**
     * @var array
     */
    private $_hostedServices;
    
    /**
     * @var array
     */
    private $_storageServices;
    
    /**
     * Creates GetAffinityGroupPropertiesResult from parsed response into array.
     * 
     * @param array $parsed The parsed HTTP response body.
     * 
     * @return GetAffinityGroupPropertiesResult 
     */
    public static function create($parsed)
    {
        $result          = new GetAffinityGroupPropertiesResult();
        $hostedServices  = Utilities::tryGetArray(
            Resources::XTAG_HOSTED_SERVICES,
            $parsed
        );
        $storageServices = Utilities::tryGetArray(
            Resources::XTAG_STORAGE_SERVICES,
            $parsed
        );
        
        $result->_affinityGroup = new AffinityGroup($parsed);
        
        foreach ($hostedServices as $value) {
            $service                   = new HostedService($value);
            $result->_hostedServices[] = $service;
        }
        
        foreach ($storageServices as $value) {
            $service                    = new StorageService($value);
            $result->_storageServices[] = $service;
        }
        
        return $result;
    }
    
    /**
     * Gets the affinityGroup.
     * 
     * @return AffinityGroup 
     */
    public function getAffinityGroup()
    {
        return $this->_affinityGroup;
    }
    
    /**
     * Sets the affinityGroup.
     * 
     * @param AffinityGroup $affinityGroup The affinityGroup.
     * 
     * @return none
     */
    public function setAffinityGroup($affinityGroup)
    {
        $this->_affinityGroup = $affinityGroup;
    }
    
    /**
     * Gets the hostedServices.
     * 
     * @return array 
     */
    public function getHostedServices()
    {
        return $this->_hostedServices;
    }
    
    /**
     * Sets the hostedServices.
     * 
     * @param array $hostedServices The hostedServices.
     * 
     * @return none
     */
    public function setHostedServices($hostedServices)
    {
        $this->_hostedServices = $hostedServices;
    }
    
    /**
     * Gets the storageServices.
     * 
     * @return array 
     */
    public function getStorageServices()
    {
        return $this->_storageServices;
    }
    
    /**
     * Sets the storageServices.
     * 
     * @param array $storageServices The storageServices.
     * 
     * @return none
     */
    public function setStorageServices($storageServices)
    {
        $this->_storageServices = $storageServices;
    }
}


