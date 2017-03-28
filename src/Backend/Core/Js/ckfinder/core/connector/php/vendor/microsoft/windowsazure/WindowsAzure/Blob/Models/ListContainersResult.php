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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Blob\Models\Container;

/**
 * Container to hold list container response object.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListContainersResult
{
    /**
     * @var array
     */
    private $_containers;
    
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
    private $_accountName;

    /**
     * Creates ListBlobResult object from parsed XML response.
     *
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return ListBlobResult
     */
    public static function create($parsedResponse)
    {
        $result               = new ListContainersResult();
        $result->_accountName = Utilities::tryGetKeysChainValue(
            $parsedResponse,
            Resources::XTAG_ATTRIBUTES,
            Resources::XTAG_ACCOUNT_NAME
        );
        $result->_prefix      = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_PREFIX
        );
        $result->_marker      = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_MARKER
        );
        $result->_nextMarker  = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_NEXT_MARKER
        );
        $result->_maxResults  = Utilities::tryGetValue(
            $parsedResponse, Resources::QP_MAX_RESULTS
        );
        $result->_containers  = array();
        $rawContainer         = array();
        
        if ( !empty($parsedResponse['Containers']) ) {
            $containersArray = $parsedResponse['Containers']['Container'];
            $rawContainer    = Utilities::getArray($containersArray);
        }
        
        foreach ($rawContainer as $value) {
            $container = new Container();
            $container->setName($value['Name']);
            $container->setUrl($value['Url']);
            $container->setMetadata(
                Utilities::tryGetValue($value, Resources::QP_METADATA, array())
            );
            $properties = new ContainerProperties();
            $date       = $value['Properties']['Last-Modified'];
            $date       = Utilities::rfc1123ToDateTime($date);
            $properties->setLastModified($date);
            $properties->setETag($value['Properties']['Etag']);
            $container->setProperties($properties);
            $result->_containers[] = $container;
        }
        
        return $result;
    }

    /**
     * Sets containers.
     *
     * @param array $containers list of containers.
     * 
     * @return none
     */
    public function setContainers($containers)
    {
        $this->_containers = array();
        foreach ($containers as $container) {
            $this->_containers[] = clone $container;
        }
    }
    
    /**
     * Gets containers.
     *
     * @return array
     */
    public function getContainers()
    {
        return $this->_containers;
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
     * @return string
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
     * Gets account name.
     * 
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }

    /**
     * Sets account name.
     *
     * @param string $accountName value.
     * 
     * @return none
     */
    public function setAccountName($accountName)
    {
        $this->_accountName = $accountName;
    }
}