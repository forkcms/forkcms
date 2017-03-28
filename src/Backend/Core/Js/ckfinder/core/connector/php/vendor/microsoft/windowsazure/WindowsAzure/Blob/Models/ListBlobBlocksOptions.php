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
use WindowsAzure\Common\Internal\Validate;

/**
 * Optional parameters for listBlobBlock wrapper
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListBlobBlocksOptions extends BlobServiceOptions
{
    /**
     * @var string
     */
    private $_leaseId;
    
    /**
     * @var string
     */
    private $_snapshot;
    
    /**
     * @var boolean
     */
    private $_includeUncommittedBlobs;
    
    /**
     * @var boolean
     */
    private $_includeCommittedBlobs;
    
    /**
     * Holds result of list type. You can access it by this order:
     * $_listType[$this->_includeUncommittedBlobs][$this->_includeCommittedBlobs]
     * 
     * @var array
     */
    private static $_listType;
    
    /**
     * Constructs the static variable $listType.
     */
    public function __construct()
    {
        self::$_listType[true][true]   = 'all';
        self::$_listType[true][false]  = 'uncommitted';
        self::$_listType[false][true]  = 'committed';
        self::$_listType[false][false] = 'all';
        
        $this->_includeUncommittedBlobs = false;
        $this->_includeCommittedBlobs   = false;    
    }
    
    /**
     * Gets lease Id for the blob
     * 
     * @return string
     */
    public function getLeaseId()
    {
        return $this->_leaseId;
    }
    
    /**
     * Sets lease Id for the blob
     * 
     * @param string $leaseId the blob lease id.
     * 
     * @return none
     */
    public function setLeaseId($leaseId)
    {
        $this->_leaseId = $leaseId;
    }
    
    /**
     * Gets blob snapshot.
     *
     * @return string.
     */
    public function getSnapshot()
    {
        return $this->_snapshot;
    }

    /**
     * Sets blob snapshot.
     *
     * @param string $snapshot value.
     * 
     * @return none.
     */
    public function setSnapshot($snapshot)
    {
        $this->_snapshot = $snapshot;
    }
    
    /**
     * Sets the include uncommittedBlobs flag.
     *
     * @param bool $includeUncommittedBlobs value.
     * 
     * @return none.
     */
    public function setIncludeUncommittedBlobs($includeUncommittedBlobs)
    {
        Validate::isBoolean($includeUncommittedBlobs);
        $this->_includeUncommittedBlobs = $includeUncommittedBlobs;
    }
    
    /**
     * Indicates if uncommittedBlobs is included or not.
     * 
     * @return boolean.
     */
    public function getIncludeUncommittedBlobs()
    {
        return $this->_includeUncommittedBlobs;
    }
    
    /**
     * Sets the include committedBlobs flag.
     *
     * @param bool $includeCommittedBlobs value.
     * 
     * @return none.
     */
    public function setIncludeCommittedBlobs($includeCommittedBlobs)
    {
        Validate::isBoolean($includeCommittedBlobs);
        $this->_includeCommittedBlobs = $includeCommittedBlobs;
    }
    
    /**
     * Indicates if committedBlobs is included or not.
     * 
     * @return boolean.
     */
    public function getIncludeCommittedBlobs()
    {
        return $this->_includeCommittedBlobs;
    }
    
    /**
     * Gets block list type.
     * 
     * @return string
     */
    public function getBlockListType()
    {
        $includeUncommitted = $this->_includeUncommittedBlobs;
        $includeCommitted   = $this->_includeCommittedBlobs;
        
        return self::$_listType[$includeUncommitted][$includeCommitted];
    }
}


