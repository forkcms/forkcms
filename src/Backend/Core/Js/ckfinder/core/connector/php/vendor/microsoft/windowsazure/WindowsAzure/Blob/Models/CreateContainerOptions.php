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
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Blob\Models;
use WindowsAzure\Blob\Models\BlobServiceOptions;
use WindowsAzure\Common\Internal\Validate;

/**
 * Optional parameters for createContainer API
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CreateContainerOptions extends BlobServiceOptions
{
    /**
     * @var string 
     */
    private $_publicAccess;
    
    /**
     * @var array
     */
    private $_metadata;
    
    /**
     * Gets container public access.
     * 
     * @return string.
     */
    public function getPublicAccess()
    {
        return $this->_publicAccess;
    }
    
    /**
     * Specifies whether data in the container may be accessed publicly and the level
     * of access. Possible values include: 
     * 1) container: Specifies full public read access for container and blob data.
     *    Clients can enumerate blobs within the container via anonymous request, but
     *    cannot enumerate containers within the storage account.
     * 2) blob: Specifies public read access for blobs. Blob data within this 
     *    container can be read via anonymous request, but container data is not 
     *    available. Clients cannot enumerate blobs within the container via 
     *    anonymous request.
     * If this value is not specified in the request, container data is private to 
     * the account owner.
     * 
     * @param string $publicAccess access modifier for the container
     * 
     * @return none.
     */
    public function setPublicAccess($publicAccess)
    {
        Validate::isString($publicAccess, 'publicAccess');
        $this->_publicAccess = $publicAccess;
    }
    
    /**
     * Gets user defined metadata.
     * 
     * @return array.
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
    
    /**
     * Sets user defined metadata. This metadata should be added without the header
     * prefix (x-ms-meta-*).
     * 
     * @param array $metadata user defined metadata object in array form.
     * 
     * @return none.
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }
    
    /**
     * Adds new metadata element. This element should be added without the header
     * prefix (x-ms-meta-*).
     * 
     * @param string $key   metadata key element.
     * @param string $value metadata value element.
     * 
     * @return none.
     */
    public function addMetadata($key, $value)
    {
        $this->_metadata[$key] = $value;
    }
}


