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
 * This link defines a reference from an entry or feed to a Web resource.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class AtomLink extends AtomBase
{
    /**
     * The undefined content. 
     *
     * @var string  
     */
    protected $undefinedContent;

    /**
     * The HREF of the link. 
     *
     * @var string  
     */
    protected $href;

    /**
     * The rel attribute of the link.
     *
     * @var string
     */
    protected $rel;

    /**
     * The media type of the link. 
     *
     * @var string 
     */
    protected $type;

    /**
     * The language of HREF.
     * 
     * @var string 
     */
    protected $hreflang;

    /**
     * The titile of the link. 
     * 
     * @var string 
     */ 
    protected $title;

    /**
     * The length of the link. 
     * 
     * @var integer 
     */
    protected $length;
     
    /** 
     * Creates a AtomLink instance with specified text.
     */
    public function __construct()
    {
    }

    /**
     * Parse an ATOM Link xml. 
     * 
     * @param string $xmlString an XML based string of ATOM Link.
     * 
     * @return none
     */ 
    public function parseXml($xmlString)
    {
        Validate::notNull($xmlString, 'xmlString');
        Validate::isString($xmlString, 'xmlString');
        $atomLinkXml = simplexml_load_string($xmlString);
        $attributes  = $atomLinkXml->attributes();

        if (!empty($attributes['href'])) {
            $this->href = (string)$attributes['href'];
        }

        if (!empty($attributes['rel'])) {
            $this->rel = (string)$attributes['rel'];
        }

        if (!empty($attributes['type'])) {
            $this->type = (string)$attributes['type'];
        }

        if (!empty($attributes['hreflang'])) {
            $this->hreflang = (string)$attributes['hreflang'];
        }

        if (!empty($attributes['title'])) {
            $this->title = (string)$attributes['title'];
        }

        if (!empty($attributes['length'])) {
            $this->length = (integer)$attributes['length'];
        }

        $undefinedContent = (string)$atomLinkXml;
        if (empty($undefinedContent)) {
            $this->undefinedContent = null;
        } else {
            $this->undefinedContent = (string)$atomLinkXml;
        }
    }

    /** 
     * Gets the href of the link. 
     *
     * @return string
     */
    public function getHref()
    {   
        return $this->href;
    } 

    /**
     * Sets the href of the link.
     * 
     * @param string $href The href of the link.
     *
     * @return none
     */
    public function setHref($href)
    {
        $this->href = $href; 
    }

    /**
     * Gets the rel of the atomLink. 
     * 
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Sets the rel of the link. 
     * 
     * @param string $rel The rel of the atomLink.
     *
     * @return none
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
    }

    /**
     * Gets the type of the link. 
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type of the link.
     * 
     * @param string $type The type of the link. 
     *
     * @return none
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Gets the language of the href. 
     * 
     * @return string 
     */
    public function getHrefLang()
    {
        return $this->hrefLang;
    }

    /**
     * Sets the language of the href. 
     * 
     * @param string $hrefLang The language of the href.
     *
     * @return none
     */
    public function setHrefLang($hrefLang)
    {
        $this->hrefLang = $hrefLang;
    }

    /** 
     * Gets the title of the link. 
     * 
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title of the link. 
     * 
     * @param string $title The title of the link. 
     *
     * @return none
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the length of the link. 
     * 
     * @return string 
     */
    public function getLength() 
    {
        return $this->length;
    }

    /**
     * Sets the length of the link. 
     * 
     * @param string $length The length of the link. 
     *
     * @return none
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**     
     * Gets the undefined content. 
     *
     * @return string 
     */
    public function getUndefinedContent()
    {
        return $this->undefinedContent;
    }

    /**
     * Sets the undefined content. 
     * 
     * @param string $undefinedContent The undefined content. 
     *
     * @return none
     */
    public function setUndefinedContent($undefinedContent)
    {
        $this->undefinedContent = $undefinedContent;
    }

    /** 
     * Writes an XML representing the ATOM link item.
     * 
     * @param \XMLWriter $xmlWriter The xml writer.
     *
     * @return none
     */
    public function writeXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        $xmlWriter->startElementNS(
            'atom', 
            Resources::LINK, 
            Resources::ATOM_NAMESPACE
        );
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }

    /** 
     * Writes the inner XML representing the ATOM link item.
     * 
     * @param \XMLWriter $xmlWriter The xml writer.
     * 
     * @return none
     */
    public function writeInnerXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        
        $this->writeOptionalAttribute($xmlWriter, 'href', $this->href);
        $this->writeOptionalAttribute($xmlWriter, 'rel', $this->rel);
        $this->writeOptionalAttribute($xmlWriter, 'type', $this->type);
        $this->writeOptionalAttribute($xmlWriter, 'hreflang', $this->hreflang);
        $this->writeOptionalAttribute($xmlWriter, 'title', $this->title);
        $this->writeOptionalAttribute($xmlWriter, 'length', $this->length);

        if (!empty($this->undefinedContent)) {
            $xmlWriter->writeRaw($this->undefinedContent);
        }

    }
}

