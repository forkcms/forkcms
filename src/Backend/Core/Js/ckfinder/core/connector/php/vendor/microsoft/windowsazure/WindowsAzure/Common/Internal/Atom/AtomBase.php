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
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Internal\Atom;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Atom\AtomLink;

/**
 * The base class of ATOM library.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class AtomBase
{
    /**
     * The attributes of the feed.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Creates an ATOM base object with default parameters.
     */
    public function __construct()
    {
        $this->attributes = array();
        $atomlink         = new AtomLink();
    }

    /**
     * Gets the attributes of the ATOM class.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the attributes of the ATOM class.
     *
     * @param array $attributes The attributes of the array.
     *
     * @return array
     */
    public function setAttributes($attributes)
    {
        Validate::isArray($attributes, 'attributes');
        $this->attributes = $attributes;
    }

    /**
     * Sets an attribute to the ATOM object instance.
     *
     * @param string $attributeKey   The key of the attribute.
     * @param mixed  $attributeValue The value of the attribute.
     *
     * @return none
     */
    public function setAttribute($attributeKey, $attributeValue)
    {
        $this->attributes[$attributeKey] = $attributeValue;
    }

    /**
     * Gets an attribute with a specified attribute key.
     *
     * @param string $attributeKey The key of the attribute.
     *
     * @return none
     */
    public function getAttribute($attributeKey)
    {
        return $this->attributes[$attributeKey];
    }

    /**
     * Processes author node.
     *
     * @param array $xmlWriter   The XML writer.
     * @param array $itemArray   An array of item to write.
     * @param array $elementName The name of the element.
     *
     * @return array
     */
    protected function writeArrayItem($xmlWriter, $itemArray, $elementName)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        Validate::isArray($itemArray, 'itemArray');
        Validate::isString($elementName, 'elementName');

        foreach ($itemArray as $itemInstance) {
            $xmlWriter->startElementNS(
                'atom',
                $elementName,
                Resources::ATOM_NAMESPACE
            );
            $itemInstance->writeInnerXml($xmlWriter);
            $xmlWriter->endElement();
        }

    }

    /**
     * Processes author node.
     *
     * @param array $xmlArray An array of simple xml elements.
     *
     * @return array
     */
    protected function processAuthorNode($xmlArray)
    {
        $author     = array();
        $authorItem = $xmlArray[Resources::AUTHOR];

        if (is_array($authorItem)) {
            foreach ($xmlArray[Resources::AUTHOR] as $authorXmlInstance) {
                $authorInstance = new Person();
                $authorInstance->parseXml($authorXmlInstance->asXML());
                $author[] = $authorInstance;
            }
        } else {
            $authorInstance = new Person();
            $authorInstance->parseXml($authorItem->asXML());
            $author[] = $authorInstance;
        }
        return $author;
    }

    /**
     * Processes entry node.
     *
     * @param array $xmlArray An array of simple xml elements.
     *
     * @return array
     */
    protected function processEntryNode($xmlArray)
    {
        $entry     = array();
        $entryItem = $xmlArray[Resources::ENTRY];

        if (is_array($entryItem)) {
            foreach ($xmlArray[Resources::ENTRY] as $entryXmlInstance) {
                $entryInstance = new Entry();
                $entryInstance->fromXml($entryXmlInstance);
                $entry[] = $entryInstance;
            }
        } else {
            $entryInstance = new Entry();
            $entryInstance->fromXml($entryItem);
            $entry[] = $entryInstance;
        }
        return $entry;
    }

    /**
     * Processes category node.
     *
     * @param array $xmlArray An array of simple xml elements.
     *
     * @return array
     */
    protected function processCategoryNode($xmlArray)
    {
        $category     = array();
        $categoryItem = $xmlArray[Resources::CATEGORY];

        if (is_array($categoryItem)) {
            foreach ($xmlArray[Resources::CATEGORY] as $categoryXmlInstance) {
                $categoryInstance = new Category();
                $categoryInstance->parseXml($categoryXmlInstance->asXML());
                $category[] = $categoryInstance;
            }
        } else {
            $categoryInstance = new Category();
            $categoryInstance->parseXml($categoryItem->asXML());
            $category[] = $categoryInstance;
        }
        return $category;
    }

    /**
     * Processes contributor node.
     *
     * @param array $xmlArray An array of simple xml elements.
     *
     * @return array
     */
    protected function processContributorNode($xmlArray)
    {
        $category        = array();
        $contributorItem = $xmlArray[Resources::CONTRIBUTOR];

        if (is_array($contributorItem)) {
            foreach ($xmlArray[Resources::CONTRIBUTOR] as $contributorXmlInstance) {
                $contributorInstance = new Person();
                $contributorInstance->parseXml($contributorXmlInstance->asXML());
                $contributor[] = $contributorInstance;
            }
        } elseif (is_string($contributorItem)) {
            $contributorInstance = new Person();
            $contributorInstance->setName((string)$contributorItem);
            $contributor[] = $contributorInstance;
        } else {
            $contributorInstance = new Person();
            $contributorInstance->parseXml($contributorItem->asXML());
            $contributor[] = $contributorInstance;
        }
        return $contributor;
    }

    /**
     * Processes link node.
     *
     * @param array $xmlArray An array of simple xml elements.
     *
     * @return array
     */
    protected function processLinkNode($xmlArray)
    {
        $link      = array();
        $linkValue = $xmlArray[Resources::LINK];

        if (is_array($linkValue)) {
            foreach ($xmlArray[Resources::LINK] as $linkValueInstance) {
                $linkInstance = new AtomLink();
                $linkInstance->parseXml($linkValueInstance->asXML());
                $link[] = $linkInstance;
            }
        } else {
            $linkInstance = new AtomLink();
            $linkInstance->parseXml($linkValue->asXML());
            $link[] = $linkInstance;
        }

        return $link;
    }

    /**
     * Writes an optional attribute for ATOM.
     *
     * @param \XMLWriter $xmlWriter      The XML writer.
     * @param string     $attributeName  The name of the attribute.
     * @param mixed      $attributeValue The value of the attribute.
     *
     * @return none
     */
    protected function writeOptionalAttribute(
        $xmlWriter,
        $attributeName,
        $attributeValue
    ) {
        Validate::notNull($xmlWriter, 'xmlWriter');
        Validate::isString($attributeName, 'attributeName');

        if (!empty($attributeValue)) {
            $xmlWriter->writeAttribute(
                $attributeName,
                $attributeValue
            );
        }
    }

    /**
     * Writes the optional elements namespaces.
     *
     * @param \XmlWriter $xmlWriter    The XML writer.
     * @param string     $prefix       The prefix.
     * @param string     $elementName  The element name.
     * @param string     $namespace    The namespace name.
     * @param string     $elementValue The element value.
     *
     * @return none
     */
    protected function writeOptionalElementNS(
        $xmlWriter,
        $prefix,
        $elementName,
        $namespace,
        $elementValue
    ) {
        Validate::notNull($xmlWriter, 'xmlWriter');
        Validate::isString($elementName, 'elementName');

        if (!empty($elementValue)) {
            $xmlWriter->writeElementNS(
                $prefix,
                $elementName,
                $namespace,
                $elementValue
            );
        }
    }
}

