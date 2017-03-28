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
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceBus\Models;
use WindowsAzure\Common\Internal\Atom\Entry;
use WindowsAzure\Common\Internal\Atom\Content;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Serialization\XmlSerializer;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\ServiceBus\Models\TopicDescription;

/**
 * The description of a topic.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class TopicInfo extends Entry
{
    /**
     * The entry of the topic info. 
     * 
     * @var Entry
     */
    private $_entry;

    /**
     * The description of the topics. 
     *
     * @var TopicDescription
     */
    private $_topicDescription;

    /**
     * Creates a TopicInfo with specified parameters.
     *
     * @param string           $title            The name of the topic.
     * @param TopicDescription $topicDescription The description of the 
     * topic.
     */
    public function __construct(
        $title = Resources::EMPTY_STRING, 
        $topicDescription = null
    ) {
        Validate::isString($title, 'title');
        if (is_null($topicDescription)) {
            $topicDescription = new TopicDescription();
        }
        $this->title             = $title;
        $this->_topicDescription = $topicDescription;
        $this->_entry            = new Entry();
        $this->_entry->setTitle($title);
        $this->_entry->setAttribute(
            Resources::XMLNS,
            Resources::SERVICE_BUS_NAMESPACE
        );
    }
    
    /**
     * Populates properties with a specified XML string. 
     * 
     * @param string $xmlString An XML string representing the topic information. 
     * 
     * @return none
     */
    public function parseXml($xmlString)
    {
        $this->_entry->parseXml($xmlString);
        $content = $this->_entry->getContent();
        if (is_null($content)) {
            $this->_topicDescription = null;
        } else {
            $this->_topicDescription = TopicDescription::create($content->getText());
        }
    }

    /**
     * Writes an XML string.
     * 
     * @param \XMLWriter $xmlWriter The XML writer.
     *
     * @return string 
     */
    public function writeXml($xmlWriter)
    {
        $content = null;
        if (!is_null($this->_topicDescription)) {
            $content = new Content();
            $content->setText(
                XmlSerializer::objectSerialize(
                    $this->_topicDescription,
                    'TopicDescription'
                )
            );
            $content->setType(Resources::XML_CONTENT_TYPE);
        }
        $this->_entry->setContent($content);
        $this->_entry->writeXml($xmlWriter);
    }

    /**
     * Gets the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_entry->getTitle();
    }

    /**
     * Sets the title.
     *
     * @param string $title The title of the queue info.
     *
     * @return none
     */
    public function setTitle($title)
    {
        $this->_entry->setTitle($title);
    }

    /**
     * Gets the entry. 
     * 
     * @return Entry
     */
    public function getEntry()
    {
        return $this->_entry;
    }

    /**
     * Sets the entry.
     *
     * @param Entry $entry The entry of the queue info.
     *
     * @return none
     */
    public function setEntry($entry)
    {
        $this->_entry = $entry;
    }

    /**
     * Gets the descriptions of the topic. 
     * 
     * @return TopicDescription
     */
    public function getTopicDescription()
    {
        return $this->_topicDescription;
    }

    /** 
     * Sets the descriptions of the topic. 
     * 
     * @param TopicDescription $topicDescription The description of the topic. 
     * 
     * @return none
     */
    public function setTopicDescription($topicDescription)
    {
        $this->_topicDescription = $topicDescription;
    }

    /**
     * Gets default message time to live.
     *
     * @return string
     */
    public function getDefaultMessageTimeToLive()
    {
        return $this->_topicDescription->getDefaultMessageTimeToLive();
    }
    
    /**
     * Sets the default message to live.
     *
     * @param string $defaultMessageTimeToLive The default message time to live.
     * 
     * @return none
     */
    public function setDefaultMessageTimeToLive($defaultMessageTimeToLive)
    {
        $this->_topicDescription->setDefaultMessageTimeToLive(
            $defaultMessageTimeToLive
        );
    }

    /**
     * Gets the msax size in mega bytes. 
     * 
     * @return integer
     */
    public function getMaxSizeInMegabytes()
    {
        return $this->_topicDescription->getMaxSizeInMegabytes();
    }

    /**
     * Sets max size in mega bytes. 
     * 
     * @param integer $maxSizeInMegabytes The maximum size in mega bytes. 
     * 
     * @return none
     */
    public function setMaxSizeInMegabytes($maxSizeInMegabytes)
    {
        $this->_topicDescription->setmaxSizeInMegabytes($maxSizeInMegabytes);
    }

    /**
     * Gets requires duplicate detection.
     * 
     * @return boolean 
     */
    public function getRequiresDuplicateDetection()
    {
        return $this->_topicDescription->getRequiresDuplicateDetection();
    }

    /**
     * Sets requires duplicate detection. 
     * 
     * @param boolean $requiresDuplicateDetection Sets requires duplicate detection.
     *
     * @return none
     */
    public function setRequiresDuplicateDetection($requiresDuplicateDetection)
    {
        $this->_topicDescription->setrequiresDuplicateDetection(
            $requiresDuplicateDetection
        );
    }

    /**
     * Gets duplicate detection history time window. 
     * 
     * @return string
     */
    public function getDuplicateDetectionHistoryTimeWindow()
    {
        return $this->_topicDescription->getDuplicateDetectionHistoryTimeWindow();
    }

    /**
     * Sets duplicate detection history time window. 
     * 
     * @param string $duplicateDetectionHistoryTimeWindow The duplicate 
     * detection history time window.
     *
     * @return none
     */
    public function setDuplicateDetectionHistoryTimeWindow(
        $duplicateDetectionHistoryTimeWindow
    ) {
        $this->_topicDescription->setduplicateDetectionHistoryTimeWindow(
            $duplicateDetectionHistoryTimeWindow
        );
    }

    /**
     * Gets enable batched operations. 
     * 
     * @return boolean
     */
    public function getEnableBatchedOperations()
    {
        return $this->_topicDescription->getEnableBatchedOperations();
    }

    /**
     * Sets enable batched operations.
     * 
     * @param boolean $enableBatchedOperations Enables batched operations.
     * 
     * @return none
     */
    public function setEnableBatchedOperations($enableBatchedOperations)
    {
        $this->_topicDescription->setenableBatchedOperations(
            $enableBatchedOperations
        );
    }

    /**
     * Gets size in bytes. 
     * 
     * @return integer 
     */
    public function getSizeInBytes()
    {
        return $this->_topicDescription->getSizeInBytes();
    }

    /** 
     * Sets size in bytes.
     * 
     * @param integer $sizeInBytes The size in bytes. 
     *
     * @return none
     */
    public function setSizeInBytes($sizeInBytes)
    {
        $this->_topicDescription->setSizeInBytes($sizeInBytes);
    }

}
