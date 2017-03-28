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
 * @package   WindowsAzure\ServiceManagement\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceManagement\Internal;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Base class for all windows azure provided services.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class WindowsAzureService extends Service
{
    /**
     * @var string
     */
    private $_affinityGroup;
    
    /**
     * @var string
     */
    private $_url;
    
    /**
     * Constructs new storage service object.
     * 
     * @param array $sources The list of sources that has the row XML.
     */
    public function __construct($sources = array())
    {
        parent::__construct($sources);
        
        foreach ($sources as $source) {
            $this->setName(
                Utilities::tryGetValue(
                    $source,
                    Resources::XTAG_SERVICE_NAME,
                    $this->getName()
                )
            );
            
            $this->setAffinityGroup(
                Utilities::tryGetValue(
                    $source,
                    Resources::XTAG_AFFINITY_GROUP,
                    $this->getAffinityGroup()
                )
            );
            
            $this->setUrl(
                Utilities::tryGetValue(
                    $source,
                    Resources::XTAG_URL,
                    $this->getUrl()
                )
            );
        }
    }
        
    /**
     * Gets the affinityGroup name.
     * 
     * @return string 
     */
    public function getAffinityGroup()
    {
        return $this->_affinityGroup;
    }
    
    /**
     * Sets the affinityGroup name.
     * 
     * @param string $affinityGroup The affinityGroup name.
     * 
     * @return none
     */
    public function setAffinityGroup($affinityGroup)
    {
        $this->_affinityGroup = $affinityGroup;
    }
    
    /**
     * Gets the url name.
     * 
     * @return string 
     */
    public function getUrl()
    {
        return $this->_url;
    }
    
    /**
     * Sets the url name.
     * 
     * @param string $url The url name.
     * 
     * @return none
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }
    
    /**
     * Converts the current object into ordered array representation.
     * 
     * @return array
     */
    protected function toArray()
    {
        $arr = parent::toArray();
        Utilities::addIfNotEmpty(
            Resources::XTAG_SERVICE_NAME, $this->getName(),
            $arr
        );
        Utilities::addIfNotEmpty(
            Resources::XTAG_AFFINITY_GROUP, $this->getAffinityGroup(),
            $arr
        );

        return $arr;
    }
}