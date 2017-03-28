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
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

namespace WindowsAzure\MediaServices\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;


/**
 * Entity object properties.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class ContentProperties
{
    /**
     * The undefined content.
     *
     * @var array
     */
    protected $properties;

    /**
     * Creates a AtomProperties instance.
     */
    public function __construct()
    {
    }

    /**
     * Parse an ATOM Properties xml.
     *
     * @param string $xmlContent An XML based string of ATOM Link.
     *
     * @return none
     */
    public function fromXml($xmlContent)
    {
        Validate::notNull($xmlContent, 'xmlContent');

        $this->properties = $this->_fromXmlToArray($xmlContent);

    }

    /**
     * Parse properties recursively
     *
     * @param \SimpleXML $xml XML object to parse
     *
     * @return array
     */
    private function _fromXmlToArray($xml) {
        $result = array();
        $dataNamespace = Resources::DS_XML_NAMESPACE;
        foreach ($xml->children($dataNamespace) as $child) {
            if (count($child->children($dataNamespace)) > 0) {
                $value = array();
                foreach ($child->children($dataNamespace) as $subChild) {
                    if ($subChild->getName() == 'element') {
                        $value[] = $this->_fromXmlToArray($subChild);
                    }
                }
            } else {
                $value = (string)$child;
            }

            $result[$child->getName()] = $value;
        }

        return $result;
    }

    /**
     * Set properties from object.
     *
     * @param object $object Object to serialize
     *
     * @return none
     */
    public function setPropertiesFromObject($object)
    {
        Validate::notNull($object, 'object');

        $reflectionClass = new \ReflectionClass($object);
        $methodArray     = $reflectionClass->getMethods();

        $this->properties = array();
        foreach ($methodArray as $method) {
            if ((strpos($method->name, 'get') === 0)
                && $method->isPublic()
            ) {
                $variableName  = substr($method->name, 3);
                $variableValue = $method->invoke($object);
                if (!empty($variableValue)) {
                    if (is_a($variableValue, '\DateTime')) {
                        $variableValue = $variableValue->format(\DateTime::ATOM);
                    }
                    $this->properties[$variableName] = (string)$variableValue;
                }
            }
        }
    }


    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Writes an XML representing the ATOM properties item.
     *
     * @param \XMLWriter $xmlWriter The xml writer.
     *
     * @return none
     */
    public function writeXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        $xmlWriter->startElementNS(
            'meta',
            Resources::PROPERTIES,
            Resources::DSM_XML_NAMESPACE
        );
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }

    /**
     * Writes the inner XML representing the ATOM properties item.
     *
     * @param \XMLWriter $xmlWriter The xml writer.
     *
     * @return none
     */
    public function writeInnerXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');

        if (is_array($this->properties)) {
            foreach ($this->properties as $key => $value) {
                $xmlWriter->writeElementNS(
                    'data',
                    $key,
                    Resources::DS_XML_NAMESPACE,
                    $value
                );
            }
        }
    }
}

