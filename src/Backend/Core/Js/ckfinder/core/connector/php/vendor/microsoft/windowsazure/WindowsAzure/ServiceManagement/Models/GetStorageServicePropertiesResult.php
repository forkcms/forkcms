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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;

/**
 * The result of calling getStorageServiceProperties API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetStorageServicePropertiesResult
{
    /**
     * @var StorageService
     */
    private $_storageService;
    
    /**
     * Creates GetStorageServicePropertiesResult from parsed response.
     * 
     * @param array $parsed The parsed response in array representation.
     * 
     * @return GetStorageServicePropertiesResult 
     */
    public static function create($parsed)
    {
        $result                  = new GetStorageServicePropertiesResult();
        $properties              = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_STORAGE_SERVICE_PROPERTIES
        );
        $result->_storageService = new StorageService($parsed, $properties);
        
        return $result;
    }
    
    /**
     * Gets the storageService.
     * 
     * @return StorageService
     */
    public function getStorageService()
    {
        return $this->_storageService;
    }
    
    /**
     * Sets the storageService.
     * 
     * @param StorageService $storageService The storageService.
     * 
     * @return none
     */
    public function setStorageService($storageService)
    {
        $this->_storageService = $storageService;
    }
}