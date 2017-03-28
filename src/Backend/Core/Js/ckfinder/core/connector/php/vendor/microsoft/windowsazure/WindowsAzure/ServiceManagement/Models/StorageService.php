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
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceManagement\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\ServiceManagement\Internal\WindowsAzureService;

/**
 * The storage service class.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class StorageService extends WindowsAzureService
{
    /**
     * @var string
     */
    private $_blobEndpointUri;
    
    /**
     * @var string
     */
    private $_queueEndpointUri;
    
    /**
     * @var string
     */
    private $_tableEndpointUri;
    
    /**
     * @var string
     */
    private $_status;
    
    /**
     * Constructs new storage service object.
     */
    public function __construct()
    {
        $sources = func_get_args();
        parent::__construct($sources);
        
        foreach ($sources as $source) {
            $this->setStatus(
                Utilities::tryGetValue(
                    $source,
                    Resources::XTAG_STATUS,
                    $this->getStatus()
                )
            );
            
            $endpoints = Utilities::tryGetKeysChainValue(
                $source,
                Resources::XTAG_ENDPOINTS,
                Resources::XTAG_ENDPOINT
            );
            $this->setBlobEndpointUri(Utilities::tryGetValue($endpoints, 0));
            $this->setQueueEndpointUri(Utilities::tryGetValue($endpoints, 1));
            $this->setTableEndpointUri(Utilities::tryGetValue($endpoints, 2));
        }
    }
    
    /**
     * Converts the current object into ordered array representation.
     * 
     * @return array
     */
    protected function toArray()
    {
        $arr     = parent::toArray();
        $order   = array(
            Resources::XTAG_NAMESPACE,
            Resources::XTAG_SERVICE_NAME,
            Resources::XTAG_DESCRIPTION,
            Resources::XTAG_LABEL,
            Resources::XTAG_AFFINITY_GROUP,
            Resources::XTAG_LOCATION
        );
        $ordered = Utilities::orderArray($arr, $order);
        
        return $ordered;
    }
    
    /**
     * Gets the status.
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * Sets the status.
     * 
     * @param string $status The status.
     * 
     * @return none
     */
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    /**
     * Gets storage service blob endpoint uri.
     * 
     * @return string
     */
    public function getBlobEndpointUri()
    {
        return $this->_blobEndpointUri;
    }
    
    /**
     * Gets storage service queue endpoint uri.
     * 
     * @return string
     */
    public function getQueueEndpointUri()
    {
        return $this->_queueEndpointUri;
    }

    /**
     * Gets storage service table endpoint uri.
     * 
     * @return string
     */
    public function getTableEndpointUri()
    {
        return $this->_tableEndpointUri;
    }
    
    /**
     * Gets storage service blob endpoint uri.
     * 
     * @param string $blobEndpointUri The endpoint URI.
     * 
     * @return string
     */
    public function setBlobEndpointUri($blobEndpointUri)
    {
        $this->_blobEndpointUri = $blobEndpointUri;
    }
    
    /**
     * Gets storage service queue endpoint uri.
     * 
     * @param string $queueEndpointUri The endpoint URI.
     * 
     * @return string
     */
    public function setQueueEndpointUri($queueEndpointUri)
    {
        $this->_queueEndpointUri = $queueEndpointUri;
    }

    /**
     * Gets storage service table endpoint uri.
     * 
     * @param string $tableEndpointUri The endpoint URI.
     * 
     * @return string
     */
    public function setTableEndpointUri($tableEndpointUri)
    {
        $this->_tableEndpointUri = $tableEndpointUri;
    }
}