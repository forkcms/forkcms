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
 * @package   WindowsAzure\Common\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Models;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Holds elements of queue properties metrics field.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Metrics
{
    /**
     * The version of Storage Analytics to configure
     * 
     * @var string
     */
    private $_version;
    
    /**
     * Indicates whether metrics is enabled for the storage service
     * 
     * @var bool
     */
    private $_enabled;
    
    /**
     * Indicates whether a retention policy is enabled for the storage service
     * 
     * @var bool
     */
    private $_includeAPIs;
    
    /**
     * @var WindowsAzure\Common\Models\RetentionPolicy
     */
    private $_retentionPolicy;
    
    /**
     * Creates object from $parsedResponse.
     * 
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return WindowsAzure\Common\Models\Metrics
     */
    public static function create($parsedResponse)
    {
        $result = new Metrics();
        $result->setVersion($parsedResponse['Version']);
        $result->setEnabled(Utilities::toBoolean($parsedResponse['Enabled']));
        if ($result->getEnabled()) {
            $result->setIncludeAPIs(
                Utilities::toBoolean($parsedResponse['IncludeAPIs'])
            );
        }
        $result->setRetentionPolicy(
            RetentionPolicy::create($parsedResponse['RetentionPolicy'])
        );
        
        return $result;
    }
    
    /**
     * Gets retention policy
     * 
     * @return WindowsAzure\Common\Models\RetentionPolicy
     *  
     */
    public function getRetentionPolicy()
    {
        return $this->_retentionPolicy;
    }
    
    /**
     * Sets retention policy
     * 
     * @param RetentionPolicy $policy object to use
     * 
     * @return none.
     */
    public function setRetentionPolicy($policy)
    {
        $this->_retentionPolicy = $policy;
    }
    
    /**
     * Gets include APIs.
     * 
     * @return bool. 
     */
    public function getIncludeAPIs()
    {
        return $this->_includeAPIs;
    }
    
    /**
     * Sets include APIs.
     * 
     * @param $bool $includeAPIs value to use.
     * 
     * @return none. 
     */
    public function setIncludeAPIs($includeAPIs)
    {
        $this->_includeAPIs = $includeAPIs;
    }
    
    /**
     * Gets enabled.
     * 
     * @return bool. 
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }
    
    /**
     * Sets enabled.
     * 
     * @param bool $enabled value to use.
     * 
     * @return none. 
     */
    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;
    }
    
    /**
     * Gets version
     * 
     * @return string.
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * Sets version
     * 
     * @param string $version new value.
     * 
     * @return none.
     */
    public function setVersion($version)
    {
        $this->_version = $version;
    }
    
    /**
     * Converts this object to array with XML tags
     * 
     * @return array. 
     */
    public function toArray()
    {
        $array = array(
            'Version' => $this->_version,
            'Enabled' => Utilities::booleanToString($this->_enabled)
        );
        if ($this->_enabled) {
            $array['IncludeAPIs'] = Utilities::booleanToString($this->_includeAPIs);
        }
        $array['RetentionPolicy'] = !empty($this->_retentionPolicy)
            ? $this->_retentionPolicy->toArray()
            : null;
        
        return $array;
    }
}


