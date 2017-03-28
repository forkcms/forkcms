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

/**
 * The source class of ATOM library.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Source extends AtomBase
{
    // @codingStandardsIgnoreStart
    
    /**
     * The author the source. 
     * 
     * @var array
     */
    protected $author;

    /**
     * The category of the source. 
     * 
     * @var array
     */
    protected $category;

    /**
     * The contributor of the source. 
     * 
     * @var array
     */
    protected $contributor;

    /**
     * The generator of the source. 
     * 
     * @var Generator
     */
    protected $generator;

    /**
     * The icon of the source. 
     * 
     * @var string
     */
    protected $icon;

    /**
     * The ID of the source. 
     * 
     * @var string
     */
    protected $id;

    /**
     * The link of the source. 
     * 
     * @var AtomLink
     */
    protected $link;

    /**
     * The logo of the source. 
     * 
     * @var string
     */
    protected $logo;

    /**
     * The rights of the source. 
     * 
     * @var string
     */
    protected $rights;

    /**
     * The subtitle of the source. 
     * 
     * @var string
     */
    protected $subtitle;

    /**
     * The title of the source. 
     * 
     * @var string
     */
    protected $title;

    /**
     * The update of the source. 
     * 
     * @var \DateTime
     */
    protected $updated;

    /**
     * The extension element of the source. 
     * 
     * @var string
     */
    protected $extensionElement;

    /**
     * Creates an ATOM FEED object with default parameters. 
     */ 
    public function __construct()
    {   
        $this->attributes  = array();
        $this->category    = array();
        $this->contributor = array();
        $this->author      = array();
    }

    /**
     * Creates a source object with specified XML string. 
     * 
     * @param string $xmlString The XML string representing a source.
     *
     * @return none
     */
    public function parseXml($xmlString)
    {
        $sourceXml   = new \SimpleXMLElement($xmlString);
        $attributes  = $sourceXml->attributes();
        $sourceArray = (array)$sourceXml;

        if (array_key_exists(Resources::AUTHOR, $sourceArray)) {
            $this->content = $this->processAuthorNode($sourceArray);
        }

        if (array_key_exists(Resources::CATEGORY, $sourceArray)) {
            $this->category = $this->processCategoryNode($sourceArray);
        }

        if (array_key_exists(Resources::CONTRIBUTOR, $sourceArray)) {
            $this->contributor = $this->processContributorNode($sourceArray);
        }

        if (array_key_exists('generator', $sourceArray)) {
            $generator = new Generator();
            $generator->setText((string)$sourceArray['generator']->asXML());
            $this->generator = $generator;
        } 

        if (array_key_exists('icon', $sourceArray)) {
            $this->icon = (string)$sourceArray['icon'];
        }

        if (array_key_exists('id', $sourceArray)) {
            $this->id = (string)$sourceArray['id'];
        }

        if (array_key_exists(Resources::LINK, $sourceArray)) {
            $this->link = $this->processLinkNode($sourceArray);
        }

        if (array_key_exists('logo', $sourceArray)) {
            $this->logo = (string)$sourceArray['logo'];
        }

        if (array_key_exists('rights', $sourceArray)) {
            $this->rights = (string)$sourceArray['rights'];
        }

        if (array_key_exists('subtitle', $sourceArray)) {
            $this->subtitle = (string)$sourceArray['subtitle'];
        }

        if (array_key_exists('title', $sourceArray)) {
            $this->title = (string)$sourceArray['title'];
        }

        if (array_key_exists('updated', $sourceArray)) {
            $this->updated = \DateTime::createFromFormat(
                \DateTime::ATOM,
                (string)$sourceArray['updated']
            );
        }
    }

    /**
     * Gets the author of the source. 
     *
     * @return array
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author of the source. 
     *
     * @param array $author An array of authors of the sources. 
     * 
     * @return none
     */
    public function setAuthor($author) 
    {
        $this->author = $author;
    }

    /**
     * Gets the category of the source.
     *  
     * @return array
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category of the source.
     *  
     * @param array $category The category of the source. 
     *
     * @return none
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
   
    /**
     * Gets contributor.
     *
     * @return array
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * Sets contributor.
     * 
     * @param array $contributor The contributors of the source. 
     * 
     * @return none
     */
    public function setContributor($contributor)
    {
        $this->contributor = $contributor;
    }

    /**
     * Gets generator.
     * 
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Sets the generator. 
     * 
     * @param Generator $generator Sets the generator of the source. 
     * 
     * @return none
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * Gets the icon of the source. 
     * 
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Sets the icon of the source. 
     * 
     * @param string $icon The icon of the source. 
     * 
     * @return string   
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Gets the ID of the source. 
     * 
     * @return string   
     */ 
    public function getId()
    {   
        return $this->id;
    }

    /**
     * Sets the ID of the source.
     * 
     * @param string $id The ID of the source. 
     * 
     * @return string   
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the link of the source. 
     * 
     * @return array
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the link of the source. 
     * 
     * @param array $link The link of the source. 
     *
     * @return none
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Gets the logo of the source. 
     * 
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Sets the logo of the source. 
     * 
     * @param string $logo The logo of the source. 
     * 
     * @return none
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * Gets the rights of the source. 
     * 
     * @return string 
     */
    public function getRights()
    {   
        return $this->rights;
    }

    /** 
     * Sets the rights of the source. 
     * 
     * @param string $rights The rights of the source. 
     * 
     * @return none 
     */
    public function setRights($rights)
    {
        $this->rights = $rights;
    }

    /**
     * Gets the sub title.  
     * 
     * @return string 
     */
    public function getSubtitle()
    {   
        return $this->subtitle;
    }

    /**
     * Sets the sub title of the source. 
     *
     * @param string $subtitle Sets the sub title of the source. 
     * 
     * @return none
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Gets the title of the source. 
     *
     * @return string. 
     */
    public function getTitle() 
    {   
        return $this->title;
    }

    /**
     * Sets the title of the source. 
     *
     * @param string $title The title of the source. 
     *
     * @return none
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets the updated. 
     * 
     * @return \DateTime
     */
    public function getUpdated()
    {   
        return $this->updated;
    }

    /**
     * Sets the updated. 
     * 
     * @param \DateTime $updated updated
     * 
     * @return none
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /** 
     * Gets the extension element. 
     * 
     * @return string 
     */
    public function getExtensionElement()
    {   
        return $this->extensionElement;
    }

    /**
     * Sets the extension element. 
     * 
     * @param string $extensionElement The extension element. 
     * 
     * @return none
     */
    public function setExtensionElement($extensionElement)
    {
        $this->extensionElement = $extensionElement;
    }

    /** 
     * Writes an XML representing the source object.
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
            'source', 
            Resources::ATOM_NAMESPACE
        );
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }
    /** 
     * Writes a inner XML representing the source object.
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     * 
     * @return none
     */
    public function writeInnerXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');
        if (!is_null($this->attributes)) {
            if (is_array($this->attributes)) {
                foreach ($this->attributes as $attributeName => $attributeValue) {
                    $xmlWriter->writeAttribute($attributeName, $attributeValue);
                }
            }
        }
         
        if (!is_null($this->author)) {
            Validate::isArray($this->author, Resources::AUTHOR);
            $this->writeArrayItem($xmlWriter, $this->author, Resources::AUTHOR);
        } 

        if (!is_null($this->category)) {
            Validate::isArray($this->category, Resources::CATEGORY);
            $this->writeArrayItem(
                $xmlWriter, 
                $this->category, 
                Resources::CATEGORY
            );
        }

        if (!is_null($this->contributor)) {
            Validate::isArray($this->contributor, Resources::CONTRIBUTOR);
            $this->writeArrayItem(
                $xmlWriter, 
                $this->contributor,
                Resources::CONTRIBUTOR
            );
        }

        if (!is_null($this->generator)) {
            $this->generator->writeXml($xmlWriter);
        } 

        if (!is_null($this->icon)) {
            $xmlWriter->writeElementNS(
                'atom',
                'icon', 
                Resources::ATOM_NAMESPACE,
                $this->icon
            );
        }

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'logo', 
            Resources::ATOM_NAMESPACE,
            $this->logo
        );

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'id', 
            Resources::ATOM_NAMESPACE,
            $this->id
        );

        if (!is_null($this->link)) {
            Validate::isArray($this->link, Resources::LINK);
            $this->writeArrayItem(
                $xmlWriter, 
                $this->link,
                Resources::LINK
            );
        }

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'rights', 
            Resources::ATOM_NAMESPACE,
            $this->rights
        );

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'subtitle', 
            Resources::ATOM_NAMESPACE,
            $this->subtitle
        );

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'title', 
            Resources::ATOM_NAMESPACE,
            $this->title
        );

        if (!is_null($this->updated)) {
            $xmlWriter->writeElementNS(
                'atom',
                'updated', 
                Resources::ATOM_NAMESPACE,
                $this->updated->format(\DateTime::ATOM)
            );
        }
    }
}

// @codingStandardsIgnoreEnd