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
use WindowsAzure\Blob\Models\BlobProperties;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Holds result of GetBlob API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetBlobResult
{
    /**
     * @var BlobProperties
     */
    private $_properties;
    
    /**
     * @var array
     */
    private $_metadata;
    
    /**
     * @var resource
     */
    private $_contentStream;
    
    /**
     * Creates GetBlobResult from getBlob call.
     * 
     * @param array  $headers  The HTTP response headers.
     * @param string $body     The response body.
     * @param array  $metadata The blob metadata.
     * 
     * @return GetBlobResult
     */
    public static function create($headers, $body, $metadata)
    {
        $result = new GetBlobResult();
        $result->setContentStream(Utilities::stringToStream($body));
        $result->setProperties(BlobProperties::create($headers));
        $result->setMetadata(is_null($metadata) ? array() : $metadata);
        
        return $result;
    }
    
    /**
     * Gets blob metadata.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Sets blob metadata.
     *
     * @param string $metadata value.
     * 
     * @return none
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }
    
    /**
     * Gets blob properties.
     *
     * @return BlobProperties
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Sets blob properties.
     *
     * @param BlobProperties $properties value.
     * 
     * @return none
     */
    public function setProperties($properties)
    {
        $this->_properties = $properties;
    }
    
    /**
     * Gets blob contentStream.
     *
     * @return resource
     */
    public function getContentStream()
    {
        return $this->_contentStream;
    }

    /**
     * Sets blob contentStream.
     *
     * @param resource $contentStream The stream handle.
     * 
     * @return none
     */
    public function setContentStream($contentStream)
    {
        $this->_contentStream = $contentStream;
    }
}


