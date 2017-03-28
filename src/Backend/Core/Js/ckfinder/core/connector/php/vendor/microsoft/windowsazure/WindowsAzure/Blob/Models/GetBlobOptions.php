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
 * Optional parameters for getBlob wrapper
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetBlobOptions extends BlobServiceOptions
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
     * @var AccessCondition
     */
    private $_accessCondition;
    
    /**
     * @var boolean
     */
    private $_computeRangeMD5;
    
    /**
     * @var integer
     */
    private $_rangeStart;
    
    /**
     * @var integer
     */
    private $_rangeEnd;
    
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
     * Gets rangeStart
     * 
     * @return integer
     */
    public function getRangeStart()
    {
        return $this->_rangeStart;
    }
    
    /**
     * Sets rangeStart
     * 
     * @param integer $rangeStart the blob lease id.
     * 
     * @return none
     */
    public function setRangeStart($rangeStart)
    {
        Validate::isInteger($rangeStart, 'rangeStart');
        $this->_rangeStart = $rangeStart;
    }
    
    /**
     * Gets rangeEnd
     * 
     * @return integer
     */
    public function getRangeEnd()
    {
        return $this->_rangeEnd;
    }
    
    /**
     * Sets rangeEnd
     * 
     * @param integer $rangeEnd range end value in bytes
     * 
     * @return none
     */
    public function setRangeEnd($rangeEnd)
    {
        Validate::isInteger($rangeEnd, 'rangeEnd');
        $this->_rangeEnd = $rangeEnd;
    }
    
    /**
     * Gets computeRangeMD5
     * 
     * @return boolean
     */
    public function getComputeRangeMD5()
    {
        return $this->_computeRangeMD5;
    }
    
    /**
     * Sets computeRangeMD5
     * 
     * @param boolean $computeRangeMD5 value
     * 
     * @return none
     */
    public function setComputeRangeMD5($computeRangeMD5)
    {
        Validate::isBoolean($computeRangeMD5);
        $this->_computeRangeMD5 = $computeRangeMD5;
    }
}


