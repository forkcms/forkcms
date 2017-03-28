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
 * Optional parameters for setBlobProperties wrapper
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class SetBlobPropertiesOptions extends BlobServiceOptions
{
    /**
     * @var BlobProperties
     */
    private $_blobProperties;
    
    /**
     * @var string
     */
    private $_leaseId;
    
    /**
     * @var string
     */
    private $_sequenceNumberAction;
    
    /**
     * @var AccessCondition
     */
    private $_accessCondition;
    
    /**
     * Creates a new SetBlobPropertiesOptions with a specified BlobProperties 
     * instance.
     * 
     * @param BlobProperties $blobProperties The blob properties instance.
     */
    public function __construct($blobProperties = null)
    {
        $this->_blobProperties = is_null($blobProperties) 
                                 ? new BlobProperties() : clone $blobProperties;
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
     * Gets blob sequenceNumber.
     *
     * @return integer.
     */
    public function getSequenceNumber()
    {
        return $this->_blobProperties->getSequenceNumber();
    }

    /**
     * Sets blob sequenceNumber.
     *
     * @param integer $sequenceNumber value.
     *
     * @return none.
     */
    public function setSequenceNumber($sequenceNumber)
    {
        $this->_blobProperties->setSequenceNumber($sequenceNumber);
    }
    
    /**
     * Gets lease Id for the blob
     * 
     * @return string
     */
    public function getSequenceNumberAction()
    {
        return $this->_sequenceNumberAction;
    }
    
    /**
     * Sets lease Id for the blob
     * 
     * @param string $sequenceNumberAction action.
     * 
     * @return none
     */
    public function setSequenceNumberAction($sequenceNumberAction)
    {
        $this->_sequenceNumberAction = $sequenceNumberAction;
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
     * Gets blob blobContentLength.
     *
     * @return integer.
     */
    public function getBlobContentLength()
    {
        return $this->_blobProperties->getContentLength();
    }

    /**
     * Sets blob blobContentLength.
     *
     * @param integer $blobContentLength value.
     *
     * @return none.
     */
    public function setBlobContentLength($blobContentLength)
    {
        $this->_blobProperties->setContentLength($blobContentLength);
    }
    
    /**
     * Gets blob ContentType.
     *
     * @return string.
     */
    public function getBlobContentType()
    {
        return $this->_blobProperties->getContentType();
    }

    /**
     * Sets blob ContentType.
     *
     * @param string $blobContentType value.
     *
     * @return none.
     */
    public function setBlobContentType($blobContentType)
    {
        $this->_blobProperties->setContentType($blobContentType);
    }
    
    /**
     * Gets blob ContentEncoding.
     *
     * @return string.
     */
    public function getBlobContentEncoding()
    {
        return $this->_blobProperties->getContentEncoding();
    }

    /**
     * Sets blob ContentEncoding.
     *
     * @param string $blobContentEncoding value.
     *
     * @return none.
     */
    public function setBlobContentEncoding($blobContentEncoding)
    {
        $this->_blobProperties->setContentEncoding($blobContentEncoding);
    }
    
    /**
     * Gets blob ContentLanguage.
     *
     * @return string.
     */
    public function getBlobContentLanguage()
    {
        return $this->_blobProperties->getContentLanguage();
    }

    /**
     * Sets blob ContentLanguage.
     *
     * @param string $blobContentLanguage value.
     *
     * @return none.
     */
    public function setBlobContentLanguage($blobContentLanguage)
    {
        $this->_blobProperties->setContentLanguage($blobContentLanguage);
    }
    
    /**
     * Gets blob ContentMD5.
     *
     * @return string.
     */
    public function getBlobContentMD5()
    {
        return $this->_blobProperties->getContentMD5();
    }

    /**
     * Sets blob ContentMD5.
     *
     * @param string $blobContentMD5 value.
     *
     * @return none.
     */
    public function setBlobContentMD5($blobContentMD5)
    {
        $this->_blobProperties->setContentMD5($blobContentMD5);
    }
    
    /**
     * Gets blob cache control.
     *
     * @return string.
     */
    public function getBlobCacheControl()
    {
        return $this->_blobProperties->getCacheControl();
    }
    
    /**
     * Sets blob cacheControl.
     *
     * @param string $blobCacheControl value to use.
     * 
     * @return none.
     */
    public function setBlobCacheControl($blobCacheControl)
    {
        $this->_blobProperties->setCacheControl($blobCacheControl);
    }
}