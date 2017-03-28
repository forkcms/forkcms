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
use WindowsAzure\Common\Internal\Validate;

/**
 * The person class of ATOM library.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Person extends AtomBase
{
    /**
     * The name of the person. 
     *
     * @var string  
     */
    protected $name;

    /**
     * The Uri of the person. 
     *
     * @var string  
     */
    protected $uri;

    /**
     * The email of the person.
     *
     * @var string 
     */
    protected $email;
     
    /** 
     * Creates an ATOM person instance with specified name.
     *
     * @param string $name The name of the person.
     */
    public function __construct($name = Resources::EMPTY_STRING)
    {
        $this->name = $name;
    }

    /**
     * Populates the properties with a specified XML string. 
     * 
     * @param string $xmlString An XML based string representing 
     * the Person instance. 
     * 
     * @return none
     */
    public function parseXml($xmlString)
    {
        $personXml   = simplexml_load_string($xmlString);
        $attributes  = $personXml->attributes();
        $personArray = (array)$personXml;

        if (array_key_exists('name', $personArray)) {
            $this->name = (string)$personArray['name'];
        }

        if (array_key_exists('uri', $personArray)) {
            $this->uri = (string)$personArray['uri'];
        }

        if (array_key_exists('email', $personArray)) {
            $this->email = (string)$personArray['email'];
        }
    }

    /** 
     * Gets the name of the person. 
     *
     * @return string
     */
    public function getName()
    {   
        return $this->name;
    } 

    /**
     * Sets the name of the person.
     * 
     * @param string $name The name of the person.
     * 
     * @return none
     */
    public function setName($name)
    {
        $this->name = $name; 
    }

    /**
     * Gets the URI of the person. 
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets the URI of the person. 
     * 
     * @param string $uri The URI of the person.
     * 
     * @return none
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    
    /**
     * Gets the email of the person. 
     * 
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email of the person. 
     * 
     * @param string $email The email of the person.
     * 
     * @return none
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /** 
     * Writes an XML representing the person. 
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
            'person',
            Resources::ATOM_NAMESPACE
        );
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }

    /** 
     * Writes a inner XML representing the person. 
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     * 
     * @return none
     */
    public function writeInnerXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        $xmlWriter->writeElementNS(
            'atom',
            'name', 
            Resources::ATOM_NAMESPACE,
            $this->name
        );

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'uri', 
            Resources::ATOM_NAMESPACE,
            $this->uri
        );

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'email', 
            Resources::ATOM_NAMESPACE,
            $this->email
        );
    }
}

