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
 * The result of creating Blob snapshot. 
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CreateBlobSnapshotResult
{
    /**
     * A DateTime value which uniquely identifies the snapshot. 
     * @var string
     */
    private $_snapshot;
            
    /**
     * The ETag for the destination blob. 
     * @var string
     */
    private $_etag;
    
    /**
     * The date/time that the copy operation to the destination blob completed. 
     * @var \DateTime
     */
    private $_lastModified;
    
    /**
     * Creates CreateBlobSnapshotResult object from the response of the 
     * create Blob snapshot request.
     * 
     * @param array $headers The HTTP response headers in array representation.
     * 
     * @return CreateBlobSnapshotResult
     */
    public static function create($headers)
    {
        $result                 = new CreateBlobSnapshotResult();
        $headerWithLowerCaseKey = array_change_key_case($headers);
        
        $result->setETag($headerWithLowerCaseKey[Resources::ETAG]);
        
        $result->setLastModified(
            Utilities::rfc1123ToDateTime(
                $headerWithLowerCaseKey[Resources::LAST_MODIFIED]
            )
        );
        
        $result->setSnapshot($headerWithLowerCaseKey[Resources::X_MS_SNAPSHOT]);
        
        return $result;
    }
    
    /**
     * Gets snapshot. 
     *
     * @return string
     */
    public function getSnapshot()
    {
        return $this->_snapshot;
    }
    
    /**
     * Sets snapshot.
     * 
     * @param string $snapshot value.
     *
     * @return none
     */
    public function setSnapshot($snapshot)
    {
        $this->_snapshot = $snapshot;
    }
    
    /**
     * Gets ETag.
     * 
     * @return string
     */
    public function getETag()
    {
        return $this->_etag;
    }

    /**
     * Sets ETag.
     *
     * @param string $etag value.
     *
     * @return none
     */
    public function setETag($etag)
    {
        $this->_etag = $etag;
    }
    
    /**
     * Gets blob lastModified.
     *
     * @return \DateTime
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
     * @return none
     */
    public function setLastModified($lastModified)
    {
        $this->_lastModified = $lastModified;
    }
}


