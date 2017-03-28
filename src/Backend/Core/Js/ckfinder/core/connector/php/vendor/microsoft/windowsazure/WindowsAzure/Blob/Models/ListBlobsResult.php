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
use WindowsAzure\Blob\Models\Blob;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\InvalidArgumentTypeException;

/**
 * Hold result of calliing listBlobs wrapper.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListBlobsResult
{
    /**
     * @var array
     */
    private $_blobPrefixes;
            
    /**
     * @var array
     */
    private $_blobs;
    
    /**
     * @var string
     */
    private $_delimiter;
    
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
    private $_nextMarker;
    
    /**
     * @var integer
     */
    private $_maxResults;
    
    /**
     * @var string 
     */
    private $_containerName;

    /**
     * Creates ListBlobsResult object from parsed XML response.
     *
     * @param array $parsed XML response parsed into array.
     * 
     * @return ListBlobsResult
     */
    public static function create($parsed)
    {
        $result                 = new ListBlobsResult();
        $result->_containerName = Utilities::tryGetKeysChainValue(
            $parsed,
            Resources::XTAG_ATTRIBUTES,
            Resources::XTAG_CONTAINER_NAME
        );
        $result->_prefix        = Utilities::tryGetValue(
            $parsed, Resources::QP_PREFIX
        );
        $result->_marker        = Utilities::tryGetValue(
            $parsed, Resources::QP_MARKER
        );
        $result->_nextMarker    = Utilities::tryGetValue(
            $parsed, Resources::QP_NEXT_MARKER
        );
        $result->_maxResults    = intval(
            Utilities::tryGetValue($parsed, Resources::QP_MAX_RESULTS, 0)
        );
        $result->_delimiter     = Utilities::tryGetValue(
            $parsed, Resources::QP_DELIMITER
        );
        $result->_blobs         = array();
        $result->_blobPrefixes  = array();
        $rawBlobs               = array();
        $rawBlobPrefixes        = array();
        
        if (   is_array($parsed['Blobs'])
            && array_key_exists('Blob', $parsed['Blobs'])
        ) {
            $rawBlobs = Utilities::getArray($parsed['Blobs']['Blob']);
        }
        
        foreach ($rawBlobs as $value) {
            $blob = new Blob();
            $blob->setName($value['Name']);
            $blob->setUrl($value['Url']);
            $blob->setSnapshot(Utilities::tryGetValue($value, 'Snapshot'));
            $blob->setProperties(
                BlobProperties::create(
                    Utilities::tryGetValue($value, 'Properties')
                )
            );
            $blob->setMetadata(
                Utilities::tryGetValue($value, Resources::QP_METADATA, array())
            );
            
            $result->_blobs[] = $blob;
        }
        
        if (   is_array($parsed['Blobs'])
            && array_key_exists('BlobPrefix', $parsed['Blobs'])
        ) {
            $rawBlobPrefixes = Utilities::getArray($parsed['Blobs']['BlobPrefix']);
        }
        
        foreach ($rawBlobPrefixes as $value) {
            $blobPrefix = new BlobPrefix();
            $blobPrefix->setName($value['Name']);
            
            $result->_blobPrefixes[] = $blobPrefix;
        }
        
        return $result;
    }
    
    /**
     * Gets blobs.
     *
     * @return array
     */
    public function getBlobs()
    {
        return $this->_blobs;
    }
    
    /**
     * Sets blobs.
     *
     * @param array $blobs list of blobs
     * 
     * @return none
     */
    public function setBlobs($blobs)
    {
        $this->_blobs = array();
        foreach ($blobs as $blob) {
            $this->_blobs[] = clone $blob;
        }
    }
    
    /**
     * Gets blobPrefixes.
     *
     * @return array
     */
    public function getBlobPrefixes()
    {
        return $this->_blobPrefixes;
    }
    
    /**
     * Sets blobPrefixes.
     *
     * @param array $blobPrefixes list of blobPrefixes
     * 
     * @return none
     */
    public function setBlobPrefixes($blobPrefixes)
    {
        $this->_blobPrefixes = array();
        foreach ($blobPrefixes as $blob) {
            $this->_blobPrefixes[] = clone $blob;
        }
    }

    /**
     * Gets prefix.
     *
     * @return string
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
     * @return none
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }
    
    /**
     * Gets prefix.
     *
     * @return string
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
     * @return none
     */
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }

    /**
     * Gets marker.
     * 
     * @return string
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
     * @return none
     */
    public function setMarker($marker)
    {
        $this->_marker = $marker;
    }

    /**
     * Gets max results.
     * 
     * @return integer
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
     * @return none
     */
    public function setMaxResults($maxResults)
    {
        $this->_maxResults = $maxResults;
    }

    /**
     * Gets next marker.
     * 
     * @return string
     */
    public function getNextMarker()
    {
        return $this->_nextMarker;
    }

    /**
     * Sets next marker.
     *
     * @param string $nextMarker value.
     * 
     * @return none
     */
    public function setNextMarker($nextMarker)
    {
        $this->_nextMarker = $nextMarker;
    }
    
    /**
     * Gets container name.
     * 
     * @return string
     */
    public function getContainerName()
    {
        return $this->_containerName;
    }

    /**
     * Sets container name.
     *
     * @param string $containerName value.
     * 
     * @return none
     */
    public function setContainerName($containerName)
    {
        $this->_containerName = $containerName;
    }
}