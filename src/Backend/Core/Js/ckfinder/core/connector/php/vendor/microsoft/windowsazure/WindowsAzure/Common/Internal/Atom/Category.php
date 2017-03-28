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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;

/**
 * The category class of the ATOM standard.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Category extends AtomBase
{
    /**
     * The term of the category. 
     *
     * @var string  
     */
    protected $term;

    /**
     * The scheme of the category. 
     *
     * @var string  
     */
    protected $scheme;

    /**
     * The label of the category. 
     * 
     * @var string 
     */ 
    protected $label;

    /**
     * The undefined content of the category. 
     *  
     * @var string 
     */
    protected $undefinedContent;
     
    /** 
     * Creates a Category instance with specified text.
     *
     * @param string $undefinedContent The undefined content of the category.
     *
     * @return none
     */
    public function __construct($undefinedContent = Resources::EMPTY_STRING)
    {
        $this->undefinedContent = $undefinedContent;
    }

    /**
     * Creates an ATOM Category instance with specified xml string. 
     * 
     * @param string $xmlString an XML based string of ATOM CONTENT.
     * 
     * @return none
     */ 
    public function parseXml($xmlString)
    {
        Validate::notNull($xmlString, 'xmlString');
        Validate::isString($xmlString, 'xmlString');
        $categoryXml = simplexml_load_string($xmlString);
        $attributes  = $categoryXml->attributes();
        if (!empty($attributes['term'])) {
            $this->term = (string)$attributes['term'];
        }

        if (!empty($attributes['scheme'])) {
            $this->scheme = (string)$attributes['scheme'];
        }

        if (!empty($attributes['label'])) {
            $this->label = (string)$attributes['label'];
        }

        $this->undefinedContent =(string)$categoryXml;
    }

    /** 
     * Gets the term of the category. 
     *
     * @return string
     */
    public function getTerm()
    {   
        return $this->term;
    } 

    /**
     * Sets the term of the category.
     * 
     * @param string $term The term of the category.
     * 
     * @return none
     */
    public function setTerm($term)
    {
        $this->term = $term; 
    }

    /**
     * Gets the scheme of the category. 
     * 
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets the scheme of the category. 
     * 
     * @param string $scheme The scheme of the category.
     * 
     * @return none
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Gets the label of the category. 
     *
     * @return string The label. 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the label of the category. 
     * 
     * @param string $label The label of the category. 
     * 
     * @return none
     */ 
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Gets the undefined content of the category. 
     * 
     * @return string
     */
    public function getUndefinedContent()
    {
        return $this->undefinedContent;
    }

    /**
     * Sets the undefined content of the category. 
     * 
     * @param string $undefinedContent The undefined content of the category. 
     *
     * @return none
     */
    public function setUndefinedContent($undefinedContent)
    {
        $this->undefinedContent = $undefinedContent;
    }
    
    /** 
     * Writes an XML representing the category. 
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     * 
     * @return none
     */
    public function writeXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        $xmlWriter->startElementNS(
            'atom', 
            'category',
            Resources::ATOM_NAMESPACE
        );
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }

    /** 
     * Writes an XML representing the category. 
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     * 
     * @return none
     */
    public function writeInnerXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        $this->writeOptionalAttribute(
            $xmlWriter,
            'term', 
            $this->term
        );

        $this->writeOptionalAttribute(
            $xmlWriter,
            'scheme', 
            $this->scheme
        );

        $this->writeOptionalAttribute(
            $xmlWriter,
            'label', 
            $this->label
        );
             
        if (!empty($this->undefinedContent)) {
            $xmlWriter->WriteRaw($this->undefinedContent);
        }

    }
}

