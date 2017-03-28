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
use WindowsAzure\Blob\Models\ContainerAcl;

/**
 * Holds container ACL
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetContainerAclResult
{
    /**
     * @var ContainerAcl
     */
    private $_containerACL;
    
    /**
     * @var \DateTime
     */
    private $_lastModified;

    /**
     * @var string
     */
    private $_etag;
    
    /**
     * Parses the given array into signed identifiers
     * 
     * @param string    $publicAccess container public access
     * @param string    $etag         container etag
     * @param \DateTime $lastModified last modification date
     * @param array     $parsed       parsed response into array
     * representation
     * 
     * @return none.
     */
    public static function create($publicAccess, $etag, $lastModified, $parsed)
    {
        $result = new GetContainerAclResult();
        $result->setETag($etag);
        $result->setLastModified($lastModified);
        $acl = ContainerAcl::create($publicAccess, $parsed);
        $result->setContainerAcl($acl);
        
        return $result;
    }
    
    /**
     * Gets container ACL
     * 
     * @return ContainerAcl
     */
    public function getContainerAcl()
    {
        return $this->_containerACL;
    }
    
    /**
     * Sets container ACL
     * 
     * @param ContainerAcl $containerACL value.
     * 
     * @return none.
     */
    public function setContainerAcl($containerACL)
    {
        $this->_containerACL = $containerACL;
    }
    
    /**
     * Gets container lastModified.
     *
     * @return \DateTime.
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Sets container lastModified.
     *
     * @param \DateTime $lastModified value.
     *
     * @return none.
     */
    public function setLastModified($lastModified)
    {
        $this->_lastModified = $lastModified;
    }

    /**
     * Gets container etag.
     *
     * @return string.
     */
    public function getETag()
    {
        return $this->_etag;
    }

    /**
     * Sets container etag.
     *
     * @param string $etag value.
     *
     * @return none.
     */
    public function setETag($etag)
    {
        $this->_etag = $etag;
    }
}


