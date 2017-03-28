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
 * The result of calling listStorageServices API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListStorageServicesResult
{
    /**
     * @var array
     */
    private $_storageServices;
    
    /**
     * Creates new ListStorageServicesResult from parsed response body.
     * 
     * @param array $parsed The parsed response body.
     * 
     * @return ListStorageServicesResult
     */
    public static function create($parsed)
    {
        $result             = new ListStorageServicesResult();
        $rowStorageServices = Utilities::tryGetArray(
            Resources::XTAG_STORAGE_SERVICE,
            $parsed
        );
        
        foreach ($rowStorageServices as $rowStorageService) {
            $result->_storageServices[] = new StorageService($rowStorageService);
        }
        
        return $result;
    }
    
    /**
     * Gets storage accounts.
     * 
     * @return array
     */
    public function getStorageServices()
    {
        return $this->_storageServices;
    }
    
    /**
     * Sets storage accounts.
     * 
     * @param array $storageServices The storage accounts.
     * 
     * @return none
     */
    public function setStorageServices($storageServices)
    {
        $this->_storageServices = $storageServices;
    }
}