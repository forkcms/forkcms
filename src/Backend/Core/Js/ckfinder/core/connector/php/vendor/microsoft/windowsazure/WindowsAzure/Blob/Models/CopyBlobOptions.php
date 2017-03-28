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
 * optional parameters for CopyBlobOptions wrapper
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CopyBlobOptions extends BlobServiceOptions
{

    /**
     * @var AccessCondition
     */
    private $_accessCondition;
    
    /**
     * @var AccessCondition
     */
    private $_sourceAccessCondition;
    
    /**
     * @var array
     */
    private $_metadata;
    
    /**
     * @var string 
     */
    private $_sourceSnapshot;
    
    /**
     * @var string
     */
    private $_leaseId;
    
    /**
     * @var sourceLeaseId
     */
    private $_sourceLeaseId;
  
    /**
     * Gets access condition
     * 
     * @return AccessCondition
     */
    public function getAccessCondition()
    {
        return $this->_accessCondition;
    }
    
    /**
     * Sets access condition
     * 
     * @param AccessCondition $accessCondition value to use.
     * 
     * @return none.
     */
    public function setAccessCondition($accessCondition)
    {
        $this->_accessCondition = $accessCondition;
    }
    
    /**
     * Gets source access condition
     * 
     * @return SourceAccessCondition
     */
    public function getSourceAccessCondition()
    {
        return $this->_sourceAccessCondition;
    }
    
    /**
     * Sets source access condition
     * 
     * @param SourceAccessCondition $sourceAccessCondition value to use.
     * 
     * @return none.
     */
    public function setSourceAccessCondition($sourceAccessCondition)
    {
        $this->_sourceAccessCondition = $sourceAccessCondition;
    }
    
    /**
     * Gets metadata.
     *
     * @return array.
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Sets metadata.
     *
     * @param array $metadata value.
     *
     * @return none.
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }
    
    /**
     * Gets source snapshot. 
     * 
     * @return string
     */
    public function getSourceSnapshot()
    {
        return $this->_sourceSnapshot;
    }
       
    /**
     * Sets source snapshot. 
     * 
     * @param string $sourceSnapshot value.
     * 
     * @return none
     */
    public function setSourceSnapshot($sourceSnapshot)
    {
        $this->_sourceSnapshot = $sourceSnapshot;
    }
   
    /**
     * Gets lease ID.
     *
     * @return string
     */
    public function getLeaseId()
    {
        return $this->_leaseId;
    }

    /**
     * Sets lease ID.
     *
     * @param string $leaseId value.
     * 
     * @return none
     */
    public function setLeaseId($leaseId)
    {
        $this->_leaseId = $leaseId;
    }
    
    /**
     * Gets source lease ID.
     *
     * @return string
     */
    public function getSourceLeaseId()
    {
        return $this->_sourceLeaseId;
    }

    /**
     * Sets source lease ID.
     *
     * @param string $sourceLeaseId value.
     * 
     * @return none
     */
    public function setSourceLeaseId($sourceLeaseId)
    {
        $this->_sourceLeaseId = $sourceLeaseId;
    }
}


