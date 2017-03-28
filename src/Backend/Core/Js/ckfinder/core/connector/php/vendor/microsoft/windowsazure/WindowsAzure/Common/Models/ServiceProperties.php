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
use WindowsAzure\Common\Models\Logging;
use WindowsAzure\Common\Models\Metrics;
use WindowsAzure\Common\Internal\Serialization\XmlSerializer;

/**
 * Encapsulates service properties
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ServiceProperties
{
    private $_logging;
    private $_metrics;
    public static $xmlRootName = 'StorageServiceProperties';
    
    /**
     * Creates ServiceProperties object from parsed XML response.
     *
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return WindowsAzure\Common\Models\ServiceProperties.
     */
    public static function create($parsedResponse)
    {
        $result = new ServiceProperties();
        $result->setLogging(Logging::create($parsedResponse['Logging']));
        $result->setMetrics(Metrics::create($parsedResponse['Metrics']));
        
        return $result;
    }
    
    /**
     * Gets logging element.
     *
     * @return WindowsAzure\Common\Models\Logging.
     */
    public function getLogging()
    {
        return $this->_logging;
    }
    
    /**
     * Sets logging element.
     *
     * @param WindowsAzure\Common\Models\Logging $logging new element.
     * 
     * @return none.
     */
    public function setLogging($logging)
    {
        $this->_logging = clone $logging;
    }
    
    /**
     * Gets metrics element.
     *
     * @return WindowsAzure\Common\Models\Metrics.
     */
    public function getMetrics()
    {
        return $this->_metrics;
    }
    
    /**
     * Sets metrics element.
     *
     * @param WindowsAzure\Common\Models\Metrics $metrics new element.
     * 
     * @return none.
     */
    public function setMetrics($metrics)
    {
        $this->_metrics = clone $metrics;
    }
    
    /**
     * Converts this object to array with XML tags
     * 
     * @return array. 
     */
    public function toArray()
    {
        return array(
            'Logging' => !empty($this->_logging) ? $this->_logging->toArray() : null,
            'Metrics' => !empty($this->_metrics) ? $this->_metrics->toArray() : null
        );
    }
    
    /**
     * Converts this current object to XML representation.
     * 
     * @param XmlSerializer $xmlSerializer The XML serializer.
     * 
     * @return string
     */
    public function toXml($xmlSerializer)
    {
        $properties = array(XmlSerializer::ROOT_NAME => self::$xmlRootName);
        
        return $xmlSerializer->serialize($this->toArray(), $properties);
    }
}


