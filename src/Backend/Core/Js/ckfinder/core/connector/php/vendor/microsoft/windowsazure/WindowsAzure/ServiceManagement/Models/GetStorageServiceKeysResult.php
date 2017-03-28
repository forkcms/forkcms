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
 * The result of calling getStorageServiceKeys and regenerateStorageServiceKeys API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetStorageServiceKeysResult
{
    /**
     * @var string
     */
    private $_url;
    
    /**
     * @var string
     */
    private $_primary;
    
    /**
     * @var string
     */
    private $_secondary;
    
    /**
     * Creates new GetStorageServiceKeysResult object from parsed response.
     * 
     * @param array $parsed The HTTP parsed response into array representation.
     * 
     * @return GetStorageServiceKeysResult
     */
    public static function create($parsed)
    {
        $result             = new GetStorageServiceKeysResult();
        $keys               = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_STORAGE_SERVICE_KEYS
        );
        $result->_url       = Utilities::tryGetValue($parsed, Resources::XTAG_URL);
        $result->_primary   = Utilities::tryGetValue(
            $keys,
            Resources::XTAG_PRIMARY
        );
        $result->_secondary = Utilities::tryGetValue(
            $keys,
            Resources::XTAG_SECONDARY
        );
        
        return $result;
    }
    
    /**
     * Gets the url.
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }
    
    /**
     * Sets the url.
     * 
     * @param string $url The url.
     * 
     * @return none
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }
    
    /**
     * Gets the primary.
     * 
     * @return string
     */
    public function getPrimary()
    {
        return $this->_primary;
    }
    
    /**
     * Sets the primary.
     * 
     * @param string $primary The primary.
     * 
     * @return none
     */
    public function setPrimary($primary)
    {
        $this->_primary = $primary;
    }
    
    /**
     * Gets the secondary.
     * 
     * @return string
     */
    public function getSecondary()
    {
        return $this->_secondary;
    }
    
    /**
     * Sets the secondary.
     * 
     * @param string $secondary The secondary.
     * 
     * @return none
     */
    public function setSecondary($secondary)
    {
        $this->_secondary = $secondary;
    }
}


