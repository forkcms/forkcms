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
 * The feed class of ATOM library.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Feed extends AtomBase
{
    // @codingStandardsIgnoreStart
    
    /**
     * The entry of the feed. 
     * 
     * @var array
     */
    protected $entry;

    /**
     * the author of the feed. 
     * 
     * @var array 
     */
    protected $author;

    /**
     * The category of the feed. 
     * 
     * @var array 
     */
    protected $category;

    /**
     * The contributor of the feed. 
     * 
     * @var array 
     */
    protected $contributor;

    /**
     * The generator of the feed. 
     * 
     * @var Generator
     */
    protected $generator;

    /**
     * The icon of the feed. 
     * 
     * @var string
     */
    protected $icon;

    /**
     * The ID of the feed. 
     * 
     * @var string
     */
    protected $id;

    /**
     * The link of the feed. 
     * 
     * @var array
     */
    protected $link;

    /**
     * The logo of the feed. 
     * 
     * @var string
     */
    protected $logo;

    /**
     * The rights of the feed. 
     * 
     * @var string
     */
    protected $rights;

    /**
     * The subtitle of the feed. 
     * 
     * @var string
     */
    protected $subtitle;

    /**
     * The title of the feed. 
     * 
     * @var string
     */
    protected $title;

    /**
     * The update of the feed. 
     * 
     * @var \DateTime
     */
    protected $updated;

    /**
     * The extension element of the feed. 
     * 
     * @var string
     */
    protected $extensionElement;

    /**
     * Creates an ATOM FEED object with default parameters. 
     */ 
    public function __construct()
    {   
        $this->attributes = array();
    }

    /**
     * Creates a feed object with specified XML string. 
     *
     * @param string $xmlString An XML string representing the feed object.
     *
     * @return none
     */
    public function parseXml($xmlString)
    {
        $feedXml    = simplexml_load_string($xmlString);
        $attributes = $feedXml->attributes();
        $feedArray  = (array)$feedXml;
        if (!empty($attributes)) {
            $this->attributes = (array)$attributes;
        }

        if (array_key_exists('author', $feedArray)) {
            $this->author = $this->processAuthorNode($feedArray);
        }

        if (array_key_exists('entry', $feedArray)) {
            $this->entry = $this->processEntryNode($feedArray);
        }

        if (array_key_exists('category', $feedArray)) {
            $this->category = $this->processCategoryNode($feedArray);
        }

        if (array_key_exists('contributor', $feedArray)) {
            $this->contributor = $this->processContributorNode($feedArray);
        }

        if (array_key_exists('generator', $feedArray)) {
            $generator      = new Generator();
            $generatorValue = $feedArray['generator'];
            if (is_string($generatorValue)) {
                $generator->setText($generatorValue);
            } else {
                $generator->parseXml($generatorValue->asXML());
            }
                
            $this->generator = $generator;
        } 

        if (array_key_exists('icon', $feedArray)) {
            $this->icon = (string)$feedArray['icon'];
        }

        if (array_key_exists('id', $feedArray)) {
            $this->id = (string)$feedArray['id'];
        }

        if (array_key_exists('link', $feedArray)) {
            $this->link = $this->processLinkNode($feedArray);
        }

        if (array_key_exists('logo', $feedArray)) {
            $this->logo = (string)$feedArray['logo'];
        }

        if (array_key_exists('rights', $feedArray)) {
            $this->rights = (string)$feedArray['rights'];
        }

        if (array_key_exists('subtitle', $feedArray)) {
            $this->subtitle = (string)$feedArray['subtitle'];
        }

        if (array_key_exists('title', $feedArray)) {
            $this->title = (string)$feedArray['title'];
        }

        if (array_key_exists('updated', $feedArray)) {
            $this->updated = \DateTime::createFromFormat(
                \DateTime::ATOM,
                (string)$feedArray['updated']
            );
        }
    }

    /**
     * Gets the attributes of the feed. 
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the attributes of the feed. 
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
     * Adds an attribute to the feed object instance. 
     * 
     * @param string $attributeKey   The key of the attribute. 
     * @param mixed  $attributeValue The value of the attribute.
     *
     * @return none
     */
    public function addAttribute($attributeKey, $attributeValue)
    {
        $this->attributes[$attributeKey] = $attributeValue;
    }   

    /**
     * Gets the author of the feed. 
     * 
     * @return Person 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author of the feed. 
     * 
     * @param Person $author The author of the feed. 
     *
     * @return none
     */ 
    public function setAuthor($author)
    {
        Validate::isArray($author, 'author');
        $person = new Person();
        foreach ($author as $authorInstance) {
            Validate::isInstanceOf($authorInstance, $person, 'author'); 
        }
        $this->author = $author;
    }

    /**
     * Gets the category of the feed.
     *  
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category of the feed.
     *  
     * @param Category $category The category of the feed. 
     * 
     * @return none
     */
    public function setCategory($category)
    {
        Validate::isArray($category, 'category');
        $categoryClassInstance = new Category();
        foreach ($category as $categoryInstance) {
            Validate::isInstanceOf(
                $categoryInstance, 
                $categoryClassInstance, 
                'category'
            );
        }
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
     * @param string $contributor The contributor of the feed. 
     * 
     * @return none
     */
    public function setContributor($contributor)
    {
        Validate::isArray($contributor, 'contributor');
        $person = new Person();
        foreach ($contributor as $contributorInstance) {
            Validate::isInstanceOf($contributorInstance, $person, 'contributor'); 
        }
        $this->contributor = $contributor;
    }

    /**
     * Gets generator.
     * 
     * @return string 
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Sets the generator. 
     * 
     * @param string $generator Sets the generator of the feed. 
     * 
     * @return none
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * Gets the icon of the feed. 
     * 
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Sets the icon of the feed. 
     * 
     * @param string $icon The icon of the feed. 
     * 
     * @return none
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Gets the ID of the feed. 
     * 
     * @return string   
     */ 
    public function getId()
    {   
        return $this->id;
    }

    /**
     * Sets the ID of the feed.
     * 
     * @param string $id The ID of the feed. 
     * 
     * @return none
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the link of the feed. 
     * 
     * @return array
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the link of the feed. 
     * 
     * @param array $link The link of the feed. 
     * 
     * @return none
     */
    public function setLink($link)
    {
        Validate::isArray($link, 'link');
        $this->link = $link;
    }

    /**
     * Gets the logo of the feed. 
     * 
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Sets the logo of the feed. 
     * 
     * @param string $logo The logo of the feed. 
     * 
     * @return none
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * Gets the rights of the feed. 
     * 
     * @return string 
     */
    public function getRights()
    {   
        return $this->rights;
    }

    /** 
     * Sets the rights of the feed. 
     * 
     * @param string $rights The rights of the feed. 
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
     * Sets the sub title of the feed. 
     *
     * @param string $subtitle Sets the sub title of the feed. 
     * 
     * @return none
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Gets the title of the feed. 
     *
     * @return string. 
     */
    public function getTitle() 
    {   
        return $this->title;
    }

    /**
     * Sets the title of the feed. 
     *
     * @param string $title The title of the feed. 
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
        Validate::isInstanceOf($updated, new \DateTime(), 'updated');
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
     * Gets the entry of the feed. 
     * 
     * @return Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Sets the entry of the feed.
     * 
     * @param Entry $entry The entry of the feed. 
     * 
     * @return none
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /** 
     * Writes an XML representing the feed object.
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     * 
     * @return none 
     */
    public function writeXml($xmlWriter)
    {
        Validate::notNull($xmlWriter, 'xmlWriter');

        $xmlWriter->startElementNS('atom', 'feed', Resources::ATOM_NAMESPACE);
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }

    /** 
     * Writes an XML representing the feed object.
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
                foreach (
                    $this->attributes 
                    as $attributeName => $attributeValue
                ) {
                    $xmlWriter->writeAttribute($attributeName, $attributeValue);
                }
            }
        }
         
        if (!is_null($this->author)) {
            $this->writeArrayItem(
                $xmlWriter,
                $this->author,
                Resources::AUTHOR
            );
        } 

        if (!is_null($this->category)) {
            $this->writeArrayItem(
                $xmlWriter,
                $this->category,
                Resources::CATEGORY
            );
        }

        if (!is_null($this->contributor)) {
            $this->writeArrayItem(
                $xmlWriter,
                $this->contributor,
                Resources::CONTRIBUTOR
            );
        }

        if (!is_null($this->generator)) {
            $this->generator->writeXml($xmlWriter);
        } 

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom', 
            'icon', 
            Resources::ATOM_NAMESPACE,
            $this->icon
        );

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