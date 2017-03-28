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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;

/**
 * The generator class of ATOM library. 
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Generator extends AtomBase
{
    /**
     * The of the generator. 
     *
     * @var string  
     */
    protected $text;

    /**
     * The Uri of the generator. 
     *
     * @var string  
     */
    protected $uri;

    /**
     * The version of the generator.
     *
     * @var string 
     */
    protected $version;

    /** 
     * Creates a generator instance with specified XML string. 
     * 
     * @param string $xmlString A string representing a generator 
     * instance.
     * 
     * @return none
     */
    public static function parseXml($xmlString)
    {
        $generatorXml   = new \SimpleXMLElement($xmlString);
        $generatorArray = (array)$generatorXml;
        $attributes     = $generatorXml->attributes();
        if (!empty($attributes['uri'])) { 
            $this->uri = (string)$attributes['uri'];
        }

        if (!empty($attributes['version'])) {
            $this->version = (string)$attributes['version'];
        }

        $this->text = (string)$generatorXml;  
    }
     
    /** 
     * Creates an ATOM generator instance with specified name.
     *
     * @param string $text The text content of the generator.
     * 
     * @return none
     */
    public function __construct($text = null)
    {
        if (!empty($text)) {
            $this->text = $text;
        }
    }

    /** 
     * Gets the text of the generator. 
     *
     * @return string
     */
    public function getText()
    {   
        return $this->text;
    } 

    /**
     * Sets the text of the generator.
     * 
     * @param string $text The text of the generator.
     * 
     * @return none
     */
    public function setText($text)
    {
        $this->text = $text; 
    }

    /**
     * Gets the URI of the generator. 
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets the URI of the generator. 
     * 
     * @param string $uri The URI of the generator.
     * 
     * @return none
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    
    /**
     * Gets the version of the generator. 
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the version of the generator. 
     * 
     * @param string $version The version of the generator.
     * 
     * @return none
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /** 
     * Writes an XML representing the generator. 
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     * 
     * @return none
     */
    public function writeXml($xmlWriter)
    {
        $xmlWriter->startElementNS(
            'atom',
            Resources::CATEGORY,
            Resources::ATOM_NAMESPACE
        );

        $this->writeOptionalAttribute(
            $xmlWriter,
            'uri', 
            $this->uri
        );

        $this->writeOptionalAttribute(
            $xmlWriter,
            'version', 
            $this->version
        );

        $xmlWriter->writeRaw($this->text);
        $xmlWriter->endElement();
    }
}

