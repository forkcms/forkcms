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
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
namespace WindowsAzure\ServiceBus\Internal;
use WindowsAzure\Common\Internal\Resources;

/**
 * The base class for rule filter.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Filter
{
    /** 
     * The attributes of the filter. 
     *
     * @var array
     */ 
    protected $attributes;

    /**
     * Creates a filter with default parameters. 
     */
    public function __construct()
    {
        $this->attributes              = array();
        $this->attributes['xmlns:xsi'] = Resources::XSI_XML_NAMESPACE;
    }

    /**
     * Creates a Filter with specifed XML based string. 
     * 
     * @param string $filterXmlString An XML based filter string. 
     *
     * @return Filter
     */
    public static function create($filterXmlString)
    {
        $filterXml  = simplexml_load_string($filterXmlString);
        $attributes = (array)$filterXml->attributes();

        if (array_key_exists('i:type', $attributes)) {
            $type = (string)$attributes['i:type'];
            if ($type === 'TrueFilter') {
                return new TrueFilter();
            }

            if ($type === 'FalseFilter') {
                return new FalseFilter();
            }

            return new Filter();
        }
    }

    /**
     * Gets the attributes. 
     *
     * @return array
     */ 
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets an attribute. 
     *
     * @param string $key   The key of the attribute.
     * @param string $value The value of the attribute.
     * 
     * @return none
     */
    protected function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }   

}

