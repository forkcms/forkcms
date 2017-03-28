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
 * The Entry class of ATOM standard.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Atom
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class Entry extends AtomBase
{
    // @codingStandardsIgnoreStart

    /**
     * The author of the entry.
     *
     * @var Person
     */
    protected $author;

    /**
     * The category of the entry.
     *
     * @var array
     */
    protected $category;

    /**
     * The content of the entry.
     *
     * @var string
     */
    protected $content;

    /**
     * The contributor of the entry.
     *
     * @var string
     */
    protected $contributor;

    /**
     * An unqiue ID representing the entry.
     *
     * @var string
     */
    protected $id;

    /**
     * The link of the entry.
     *
     * @var string
     */
    protected $link;

    /**
     * Is the entry published.
     *
     * @var boolean
     */
    protected $published;

    /**
     * The copy right of the entry.
     *
     * @var string
     */
    protected $rights;

    /**
     * The source of the entry.
     *
     * @var string
     */
    protected $source;

    /**
     * The summary of the entry.
     *
     * @var string
     */
    protected $summary;

    /**
     * The title of the entry.
     *
     * @var string
     */
    protected $title;

    /**
     * Is the entry updated.
     *
     * @var \DateTime
     */
    protected $updated;

    /**
     * The extension element of the entry.
     *
     * @var string
     */
    protected $extensionElement;

    /**
     * Creates an ATOM Entry instance with default parameters.
     */
    public function __construct()
    {
        $this->attributes = array();
    }

    /**
     * Populate the properties of an ATOM Entry instance with specified XML..
     *
     * @param string $xmlString A string representing an ATOM entry instance.
     *
     * @return none
     */
    public function parseXml($xmlString)
    {
        Validate::notNull($xmlString, 'xmlString');

        $this->fromXml(simplexml_load_string($xmlString));
    }

    /**
     * Creates an ATOM ENTRY instance with specified simpleXML object
     *
     * @param \SimpleXMLElement $entryXml xml element of ATOM ENTRY
     *
     * @return none
     */
    public function fromXml($entryXml) {
        Validate::notNull($entryXml, 'entryXml');
        Validate::isA($entryXml, '\SimpleXMLElement', 'entryXml');

        $this->attributes = (array)$entryXml->attributes();
        $entryArray       = (array)$entryXml;

        if (array_key_exists(Resources::AUTHOR, $entryArray)) {
            $this->author = $this->processAuthorNode($entryArray);
        }

        if (array_key_exists(Resources::CATEGORY, $entryArray)) {
            $this->category = $this->processCategoryNode($entryArray);
        }

        if (array_key_exists('content', $entryArray)) {
            $content = new Content();
            $content->fromXml($entryArray['content']);
            $this->content = $content;
        }

        if (array_key_exists(Resources::CONTRIBUTOR, $entryArray)) {
            $this->contributor = $this->processContributorNode($entryArray);
        }

        if (array_key_exists('id', $entryArray)) {
            $this->id = (string)$entryArray['id'];
        }

        if (array_key_exists(Resources::LINK, $entryArray)) {
            $this->link = $this->processLinkNode($entryArray);
        }

        if (array_key_exists('published', $entryArray)) {
            $this->published = $entryArray['published'];
        }

        if (array_key_exists('rights', $entryArray)) {
            $this->rights = $entryArray['rights'];
        }

        if (array_key_exists('source', $entryArray)) {
            $source = new Source();
            $source->parseXml($entryArray['source']->asXML());
            $this->source = $source;
        }

        if (array_key_exists('title', $entryArray)) {
            $this->title = $entryArray['title'];
        }

        if (array_key_exists('updated', $entryArray)) {
            $this->updated = \DateTime::createFromFormat(
                \DateTime::ATOM,
                (string)$entryArray['updated']
            );
        }
    }

    /**
     * Gets the author of the entry.
     *
     * @return Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author of the entry.
     *
     * @param Person $author The author of the entry.
     *
     * @return none
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Gets the category.
     *
     * @return array
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category.
     *
     * @param string $category The category of the entry.
     *
     * @return none
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Gets the content.
     *
     * @return Content.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the content.
     *
     * @param Content $content Sets the content of the entry.
     *
     * @return none
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Gets the contributor.
     *
     * @return string
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * Sets the contributor.
     *
     * @param string $contributor The contributor of the entry.
     *
     * @return none
     */
    public function setContributor($contributor)
    {
        $this->contributor = $contributor;
    }

    /**
     * Gets the ID of the entry.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the ID of the entry.
     *
     * @param string $id The id of the entry.
     *
     * @return none
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the link of the entry.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the link of the entry.
     *
     * @param string $link The link of the entry.
     *
     * @return none
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Gets published of the entry.
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Sets published of the entry.
     *
     * @param boolean $published Is the entry published.
     *
     * @return none
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * Gets the rights of the entry.
     *
     * @return string
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * Sets the rights of the entry.
     *
     * @param string $rights The rights of the entry.
     *
     * @return none
     */
    public function setRights($rights)
    {
        $this->rights = $rights;
    }

    /**
     * Gets the source of the entry.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the source of the entry.
     *
     * @param string $source The source of the entry.
     *
     * @return none
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Gets the summary of the entry.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Sets the summary of the entry.
     *
     * @param string $summary The summary of the entry.
     *
     * @return none
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Gets the title of the entry.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title of the entry.
     *
     * @param string $title The title of the entry.
     *
     * @return none
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Gets updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Sets updated
     *
     * @param \DateTime $updated updated.
     *
     * @return none
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Gets extension element.
     *
     * @return string
     */
    public function getExtensionElement()
    {
        return $this->extensionElement;
    }

    /**
     * Sets extension element.
     *
     * @param string $extensionElement The extension element of the entry.
     *
     * @return none
     */
    public function setExtensionElement($extensionElement)
    {
        $this->extensionElement = $extensionElement;
    }

    /**
     * Writes a inner XML string representing the entry.
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
            Resources::ENTRY,
            Resources::ATOM_NAMESPACE
        );
        $this->writeInnerXml($xmlWriter);
        $xmlWriter->endElement();
    }

    /**
     * Writes a inner XML string representing the entry.
     *
     * @param \XMLWriter $xmlWriter The XML writer.
     *
     * @return none
     */
    public function writeInnerXml($xmlWriter)
    {
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

        if (!is_null($this->content)) {
            $this->content->writeXml($xmlWriter);
        }

        if (!is_null($this->contributor)) {
            $this->writeArrayItem(
                $xmlWriter,
                $this->contributor,
                Resources::CONTRIBUTOR
            );
        }

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
            'published',
            Resources::ATOM_NAMESPACE,
            $this->published
        );

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
            'source',
            Resources::ATOM_NAMESPACE,
            $this->source
        );

        $this->writeOptionalElementNS(
            $xmlWriter,
            'atom',
            'summary',
            Resources::ATOM_NAMESPACE,
            $this->summary
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