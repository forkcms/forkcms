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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Holds result of listBlobBlocks
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListBlobBlocksResult
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
     * @var array
     */
    private $_committedBlocks;
    
    /**
     * @var array
     */
    private $_uncommittedBlocks;
    
    /**
     * Gets block entries from parsed response
     * 
     * @param array  $parsed HTTP response
     * @param string $type   Block type
     * 
     * @return array
     */
    private static function _getEntries($parsed, $type)
    {
        $entries = array();
        
        if (is_array($parsed)) {
            $rawEntries = array();
         
            if (   array_key_exists($type, $parsed)
                &&     is_array($parsed[$type])
                &&     !empty($parsed[$type])
            ) {
                $rawEntries = Utilities::getArray($parsed[$type]['Block']);
            }
            
            foreach ($rawEntries as $value) {
                $entries[base64_decode($value['Name'])] = $value['Size'];
            }
        }
        
        return $entries;
    }
    
    /**
     * Creates ListBlobBlocksResult from given response headers and parsed body
     * 
     * @param array $headers HTTP response headers
     * @param array $parsed  HTTP response body in array representation
     * 
     * @return ListBlobBlocksResult
     */
    public static function create($headers, $parsed)
    {
        $result = new ListBlobBlocksResult();
        $clean  = array_change_key_case($headers);
        
        $result->setETag(Utilities::tryGetValue($clean, Resources::ETAG));
        $date = Utilities::tryGetValue($clean, Resources::LAST_MODIFIED);
        if (!is_null($date)) {
            $date = Utilities::rfc1123ToDateTime($date);
            $result->setLastModified($date);
        }
        $result->setContentLength(
            intval(
                Utilities::tryGetValue($clean, Resources::X_MS_BLOB_CONTENT_LENGTH)
            )
        );
        $result->setContentType(
            Utilities::tryGetValue($clean, Resources::CONTENT_TYPE)
        );
        
        $result->_uncommittedBlocks = self::_getEntries(
            $parsed, 'UncommittedBlocks'
        );
        $result->_committedBlocks   = self::_getEntries($parsed, 'CommittedBlocks');
        
        return $result;
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
     * Gets uncommitted blocks
     * 
     * @return array
     */
    public function getUncommittedBlocks()
    {
        return $this->_uncommittedBlocks;
    }
    
    /**
     * Sets uncommitted blocks
     * 
     * @param array $uncommittedBlocks The uncommitted blocks entries
     * 
     * @return none.
     */
    public function setUncommittedBlocks($uncommittedBlocks)
    {
        $this->_uncommittedBlocks = $uncommittedBlocks;
    }
    
    /**
     * Gets committed blocks
     * 
     * @return array
     */
    public function getCommittedBlocks()
    {
        return $this->_committedBlocks;
    }
    
    /**
     * Sets committed blocks
     * 
     * @param array $committedBlocks The committed blocks entries
     * 
     * @return none.
     */
    public function setCommittedBlocks($committedBlocks)
    {
        $this->_committedBlocks = $committedBlocks;
    }
}


