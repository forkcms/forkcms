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
 * Optional parameters for listBlobs API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListBlobsOptions extends BlobServiceOptions
{
    /**
     * @var string
     */
    private $_prefix;
    
    /**
     * @var string
     */
    private $_marker;
    
    /**
     * @var string
     */
    private $_delimiter;
    
    /**
     * @var integer
     */
    private $_maxResults;
    
    /**
     * @var boolean
     */
    private $_includeMetadata;
    
    /**
     * @var boolean
     */
    private $_includeSnapshots;
    
    /**
     * @var boolean
     */
    private $_includeUncommittedBlobs;

    /**
     * Gets prefix.
     *
     * @return string.
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Sets prefix.
     *
     * @param string $prefix value.
     * 
     * @return none.
     */
    public function setPrefix($prefix)
    {
        Validate::isString($prefix, 'prefix');
        $this->_prefix = $prefix;
    }
    
    /**
     * Gets delimiter.
     *
     * @return string.
     */
    public function getDelimiter()
    {
        return $this->_delimiter;
    }

    /**
     * Sets prefix.
     *
     * @param string $delimiter value.
     * 
     * @return none.
     */
    public function setDelimiter($delimiter)
    {
        Validate::isString($delimiter, 'delimiter');
        $this->_delimiter = $delimiter;
    }

    /**
     * Gets marker.
     * 
     * @return string.
     */
    public function getMarker()
    {
        return $this->_marker;
    }

    /**
     * Sets marker.
     *
     * @param string $marker value.
     * 
     * @return none.
     */
    public function setMarker($marker)
    {
        Validate::isString($marker, 'marker');
        $this->_marker = $marker;
    }

    /**
     * Gets max results.
     * 
     * @return integer.
     */
    public function getMaxResults()
    {
        return $this->_maxResults;
    }

    /**
     * Sets max results.
     *
     * @param integer $maxResults value.
     * 
     * @return none.
     */
    public function setMaxResults($maxResults)
    {
        Validate::isInteger($maxResults, 'maxResults');
        $this->_maxResults = $maxResults;
    }

    /**
     * Indicates if metadata is included or not.
     * 
     * @return boolean.
     */
    public function getIncludeMetadata()
    {
        return $this->_includeMetadata;
    }

    /**
     * Sets the include metadata flag.
     *
     * @param bool $includeMetadata value.
     * 
     * @return none.
     */
    public function setIncludeMetadata($includeMetadata)
    {
        Validate::isBoolean($includeMetadata);
        $this->_includeMetadata = $includeMetadata;
    }
    
    /**
     * Indicates if snapshots is included or not.
     * 
     * @return boolean.
     */
    public function getIncludeSnapshots()
    {
        return $this->_includeSnapshots;
    }

    /**
     * Sets the include snapshots flag.
     *
     * @param bool $includeSnapshots value.
     * 
     * @return none.
     */
    public function setIncludeSnapshots($includeSnapshots)
    {
        Validate::isBoolean($includeSnapshots);
        $this->_includeSnapshots = $includeSnapshots;
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
}


