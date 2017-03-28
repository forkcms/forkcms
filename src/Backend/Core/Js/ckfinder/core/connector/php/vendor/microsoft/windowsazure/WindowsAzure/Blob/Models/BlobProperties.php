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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Represents blob properties
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BlobProperties
{
    /**
     * @var \DateTime
     */
    private $_lastModified;
    
    /**
     * @var string
     */
    private $_etag;
    
    /**
     * @var string
     */
    private $_contentType;
    
    /**
     * @var integer
     */
    private $_contentLength;
    
    /**
     * @var string
     */
    private $_contentEncoding;
    
    /**
     * @var string
     */
    private $_contentLanguage;
    
    /**
     * @var string
     */
    private $_contentMD5;
    
    /**
     * @var string
     */
    private $_contentRange;
    
    /**
     * @var string
     */
    private $_cacheControl;
    
    /**
     * @var string
     */
    private $_blobType;
    
    /**
     * @var string
     */
    private $_leaseStatus;
    
    /**
     * @var integer
     */
    private $_sequenceNumber;
    
    /**
     * Creates BlobProperties object from $parsed response in array representation
     * 
     * @param array $parsed parsed response in array format.
     * 
     * @return BlobProperties
     */
    public static function create($parsed)
    {
        $result = new BlobProperties();
        $clean  = array_change_key_case($parsed);
        
        $date = Utilities::tryGetValue($clean, Resources::LAST_MODIFIED);
        $result->setBlobType(Utilities::tryGetValue($clean, 'blobtype'));
        $result->setContentLength(intval($clean[Resources::CONTENT_LENGTH]));
        $result->setETag(Utilities::tryGetValue($clean, Resources::ETAG));
        
        if (!is_null($date)) {
            $date = Utilities::rfc1123ToDateTime($date);
            $result->setLastModified($date);
        }
        
        $result->setLeaseStatus(Utilities::tryGetValue($clean, 'leasestatus'));
        $result->setLeaseStatus(
            Utilities::tryGetValue(
                $clean, Resources::X_MS_LEASE_STATUS, $result->getLeaseStatus()
            )
        );
        $result->setSequenceNumber(
            intval(
                Utilities::tryGetValue($clean, Resources::X_MS_BLOB_SEQUENCE_NUMBER)
            )
        );
        $result->setContentRange(
            Utilities::tryGetValue($clean, Resources::CONTENT_RANGE)
        );
        $result->setCacheControl(
            Utilities::tryGetValue($clean, Resources::CACHE_CONTROL)
        );
        $result->setBlobType(
            Utilities::tryGetValue(
                $clean, Resources::X_MS_BLOB_TYPE, $result->getBlobType()
            )
        );
        $result->setContentEncoding(
            Utilities::tryGetValue($clean, Resources::CONTENT_ENCODING)
        );
        $result->setContentLanguage(
            Utilities::tryGetValue($clean, Resources::CONTENT_LANGUAGE)
        );
        $result->setContentMD5(
            Utilities::tryGetValue($clean, Resources::CONTENT_MD5)
        );
        $result->setContentType(
            Utilities::tryGetValue($clean, Resources::CONTENT_TYPE)
        );
        
        return $result;
    }
    
    /**
     * Makes deep copy from the current object.
     * 
     * @return BlobProperties
     */
    public function __clone()
    {
        $this->_blobType        = $this->_blobType;
        $this->_cacheControl    = $this->_cacheControl;
        $this->_contentEncoding = $this->_contentEncoding;
        $this->_contentLanguage = $this->_contentLanguage;
        $this->_contentLength   = $this->_contentLength;
        $this->_contentMD5      = $this->_contentMD5;
        $this->_contentRange    = $this->_contentRange;
        $this->_contentType     = $this->_contentType;
        $this->_etag            = $this->_etag;
        $this->_lastModified    = $this->_lastModified;
        $this->_leaseStatus     = $this->_leaseStatus;
        $this->_sequenceNumber  = $this->_sequenceNumber;
    }

    /**
     * Gets blob lastModified.
     *
     * @return \DateTime.
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Sets blob lastModified.
     *
     * @param \DateTime $lastModified value.
     *
     * @return none.
     */
    public function setLastModified($lastModified)
    {
        Validate::isDate($lastModified);
        $this->_lastModified = $lastModified;
    }

    /**
     * Gets blob etag.
     *
     * @return string.
     */
    public function getETag()
    {
        return $this->_etag;
    }

    /**
     * Sets blob etag.
     *
     * @param string $etag value.
     *
     * @return none.
     */
    public function setETag($etag)
    {
        $this->_etag = $etag;
    }
    
    /**
     * Gets blob contentType.
     *
     * @return string.
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Sets blob contentType.
     *
     * @param string $contentType value.
     *
     * @return none.
     */
    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
    }
    
    /**
     * Gets blob contentRange.
     *
     * @return string.
     */
    public function getContentRange()
    {
        return $this->_contentRange;
    }

    /**
     * Sets blob contentRange.
     *
     * @param string $contentRange value.
     *
     * @return none.
     */
    public function setContentRange($contentRange)
    {
        $this->_contentRange = $contentRange;
    }
    
    /**
     * Gets blob contentLength.
     *
     * @return integer.
     */
    public function getContentLength()
    {
        return $this->_contentLength;
    }

    /**
     * Sets blob contentLength.
     *
     * @param integer $contentLength value.
     *
     * @return none.
     */
    public function setContentLength($contentLength)
    {
        Validate::isInteger($contentLength, 'contentLength');
        $this->_contentLength = $contentLength;
    }
    
    /**
     * Gets blob contentEncoding.
     *
     * @return string.
     */
    public function getContentEncoding()
    {
        return $this->_contentEncoding;
    }

    /**
     * Sets blob contentEncoding.
     *
     * @param string $contentEncoding value.
     *
     * @return none.
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->_contentEncoding = $contentEncoding;
    }
    
    /**
     * Gets blob contentLanguage.
     *
     * @return string.
     */
    public function getContentLanguage()
    {
        return $this->_contentLanguage;
    }

    /**
     * Sets blob contentLanguage.
     *
     * @param string $contentLanguage value.
     *
     * @return none.
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->_contentLanguage = $contentLanguage;
    }
    
    /**
     * Gets blob contentMD5.
     *
     * @return string.
     */
    public function getContentMD5()
    {
        return $this->_contentMD5;
    }

    /**
     * Sets blob contentMD5.
     *
     * @param string $contentMD5 value.
     *
     * @return none.
     */
    public function setContentMD5($contentMD5)
    {
        $this->_contentMD5 = $contentMD5;
    }
    
    /**
     * Gets blob cacheControl.
     *
     * @return string.
     */
    public function getCacheControl()
    {
        return $this->_cacheControl;
    }

    /**
     * Sets blob cacheControl.
     *
     * @param string $cacheControl value.
     *
     * @return none.
     */
    public function setCacheControl($cacheControl)
    {
        $this->_cacheControl = $cacheControl;
    }
    
    /**
     * Gets blob blobType.
     *
     * @return string.
     */
    public function getBlobType()
    {
        return $this->_blobType;
    }

    /**
     * Sets blob blobType.
     *
     * @param string $blobType value.
     *
     * @return none.
     */
    public function setBlobType($blobType)
    {
        $this->_blobType = $blobType;
    }
    
    /**
     * Gets blob leaseStatus.
     *
     * @return string.
     */
    public function getLeaseStatus()
    {
        return $this->_leaseStatus;
    }

    /**
     * Sets blob leaseStatus.
     *
     * @param string $leaseStatus value.
     *
     * @return none.
     */
    public function setLeaseStatus($leaseStatus)
    {
        $this->_leaseStatus = $leaseStatus;
    }
    
    /**
     * Gets blob sequenceNumber.
     *
     * @return int.
     */
    public function getSequenceNumber()
    {
        return $this->_sequenceNumber;
    }

    /**
     * Sets blob sequenceNumber.
     *
     * @param int $sequenceNumber value.
     *
     * @return none.
     */
    public function setSequenceNumber($sequenceNumber)
    {
        Validate::isInteger($sequenceNumber, 'sequenceNumber');
        $this->_sequenceNumber = $sequenceNumber;
    }
}


