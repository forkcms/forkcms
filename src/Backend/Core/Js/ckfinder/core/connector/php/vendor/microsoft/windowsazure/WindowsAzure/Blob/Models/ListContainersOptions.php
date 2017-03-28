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
use WindowsAzure\Blob\Models\BlobServiceOptions;
use \WindowsAzure\Common\Internal\Validate;

/**
 * Options for listBlobs API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListContainersOptions extends BlobServiceOptions
{
    /**
     * Filters the results to return only containers whose name begins with the 
     * specified prefix.
     * 
     * @var string
     */
    private $_prefix;
    
    /**
     * Identifies the portion of the list to be returned with the next list operation
     * The operation returns a marker value within the 
     * response body if the list returned was not complete. The marker value may 
     * then be used in a subsequent call to request the next set of list items.
     * The marker value is opaque to the client.
     * 
     * @var string
     */
    private $_marker;
    
    /**
     * Specifies the maximum number of containers to return. If the request does not
     * specify maxresults, or specifies a value greater than 5,000, the server will
     * return up to 5,000 items. If the parameter is set to a value less than or
     * equal to zero, the server will return status code 400 (Bad Request).
     * 
     * @var string
     */
    private $_maxResults;
    
    /**
     * Include this parameter to specify that the container's metadata be returned
     * as part of the response body.
     * 
     * @var bool
     */
    private $_includeMetadata;

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
     * @return string.
     */
    public function getMaxResults()
    {
        return $this->_maxResults;
    }

    /**
     * Sets max results.
     *
     * @param string $maxResults value.
     * 
     * @return none.
     */
    public function setMaxResults($maxResults)
    {
        Validate::isString($maxResults, 'maxResults');
        $this->_maxResults = $maxResults;
    }

    /**
     * Indicates if metadata is included or not.
     * 
     * @return string.
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
}


