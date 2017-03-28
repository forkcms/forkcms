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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Blob\Models\PageRange;

/**
 * Holds result of calling listPageBlobRanges wrapper
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListPageBlobRangesResult
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
     * @var integer
     */
    private $_contentLength;
    
    /**
     * @var array
     */
    private $_pageRanges;
    
    /**
     * Creates BlobProperties object from $parsed response in array representation
     * 
     * @param array $headers HTTP response headers
     * @param array $parsed  parsed response in array format.
     * 
     * @return ListPageBlobRangesResult
     */
    public static function create($headers, $parsed)
    {
        $result  = new ListPageBlobRangesResult();
        $headers = array_change_key_case($headers);
        
        $date          = $headers[Resources::LAST_MODIFIED];
        $date          = Utilities::rfc1123ToDateTime($date);
        $blobLength    = intval($headers[Resources::X_MS_BLOB_CONTENT_LENGTH]);
        $rawPageRanges = array();
        
        if (!empty($parsed['PageRange'])) {
            $parsed        = array_change_key_case($parsed);
            $rawPageRanges = Utilities::getArray($parsed['pagerange']);
        }
        
        $result->_pageRanges = array();
        foreach ($rawPageRanges as $value) {
            $result->_pageRanges[] = new PageRange(
                intval($value['Start']), intval($value['End'])
            );
        }
        
        $result->setContentLength($blobLength);
        $result->setETag($headers[Resources::ETAG]);
        $result->setLastModified($date);
        
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
        Validate::isString($etag, 'etag');
        $this->_etag = $etag;
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
     * Gets page ranges
     * 
     * @return array
     */
    public function getPageRanges()
    {
        return $this->_pageRanges;
    }
    
    /**
     * Sets page ranges
     * 
     * @param array $pageRanges page ranges to set
     * 
     * @return none
     */
    public function setPageRanges($pageRanges)
    {
        $this->_pageRanges = array();
        foreach ($pageRanges as $pageRange) {
            $this->_pageRanges[] = clone $pageRange;
        }
    }
}


