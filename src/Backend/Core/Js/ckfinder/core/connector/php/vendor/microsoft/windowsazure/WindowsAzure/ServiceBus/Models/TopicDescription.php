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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * The description of the topic.  
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      http://msdn.microsoft.com/en-us/library/windowsazure/hh780749
 */
class TopicDescription
{
    /** 
     * The default message time to live.
     * 
     * @var string
     */
    private $_defaultMessageTimeToLive;

    /**
     * The maxizmu size in mega bytes. 
     * 
     * @integer
     */
    private $_maxSizeInMegabytes;

    /**
     * Requires duplicate detection. 
     * 
     * @var boolean 
     */
    private $_requiresDuplicateDetection;

    /** 
     * Duplicate detection history time window. 
     * 
     * @var string
     */
    private $_duplicateDetectionHistoryTimeWindow;
    
    /**
     * Enables batched operations. 
     * 
     * @var boolean
     */
    private $_enableBatchedOperations;
   
    /**
     * The size in bytes. 
     * 
     * @var integer
     */  
    private $_sizeInBytes;

    /**
     * Creates a topic description with default parameters. 
     */
    public function __construct()
    {
    }

    /**
     * Creates a topic description object with specified XML string.
     *
     * @param string $topicDescriptionXml A XML based string describing
     * the topic. 
     *
     * @return TopicDescription
     */
    public static function create($topicDescriptionXml)
    {
        $topicDescription      = new TopicDescription();
        $root                  = simplexml_load_string($topicDescriptionXml);
        $topicDescriptionArray = (array)$root;

        if (array_key_exists('DefaultMessageToLive', $topicDescriptionArray)) {
            $topicDescription->setDefaultMessageToLive(
                (string)$topicDescriptionArray['DefaultMessageToLive']
            );
        }

        if (array_key_exists('MaxSizeInMegabytes', $topicDescriptionArray)) {
            $topicDescription->setMaxSizeInMegabytes(
                (integer)$topicDescriptionArray['MaxSizeInMegabytes']
            );
        }

        if (array_key_exists(
            'RequiresDuplicateDetection', 
            $topicDescriptionArray
        )
        ) {
            $topicDescription->setRequiresDuplicateDetection(
                (boolean)$topicDescriptionArray['RequiresDuplicateDetection']
            );
        }

        if (array_key_exists(
            'DuplicateDetectionHistoryTimeWindow', 
            $topicDescriptionArray
        )
        ) {
            $topicDescription->setDuplicateDetectionHistoryTimeWindow(
                (string)$topicDescriptionArray['DuplicateDetectionHistoryTimeWindow']
            );
        }

        if (array_key_exists(
            'EnableBatchedOperations', 
            $topicDescriptionArray
        )) {
            $topicDescription->setEnableBatchedOperations(
                (boolean)$topicDescriptionArray['EnableBatchedOperations']
            );
        }

        return $topicDescription;
    }

    /**
     * Gets default message time to live.
     *
     * @return string
     */
    public function getDefaultMessageTimeToLive()
    {
        return $this->_defaultMessageTimeToLive;
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
        $this->_defaultMessageTimeToLive = $defaultMessageTimeToLive;
    }

    /**
     * Gets the msax size in mega bytes. 
     * 
     * @return integer
     */
    public function getMaxSizeInMegabytes()
    {
        return $this->_maxSizeInMegabytes;
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
        $this->_maxSizeInMegabytes = $maxSizeInMegabytes;
    }

    /**
     * Gets requires duplicate detection.
     * 
     * @return boolean 
     */
    public function getRequiresDuplicateDetection()
    {
        return $this->_requiresDuplicateDetection;
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
        $this->_requiresDuplicateDetection = $requiresDuplicateDetection;
    }

    /**
     * Gets duplicate detection history time window. 
     * 
     * @return string
     */
    public function getDuplicateDetectionHistoryTimeWindow()
    {
        return $this->_duplicateDetectionHistoryTimeWindow;
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
        $value = $duplicateDetectionHistoryTimeWindow;
        
        $this->_duplicateDetectionHistoryTimeWindow = $value;
    }

    /**
     * Gets enable batched operations. 
     * 
     * @return boolean
     */
    public function getEnableBatchedOperations()
    {
        return $this->_enableBatchedOperations;
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
        $this->_enableBatchedOperations = $enableBatchedOperations;
    }

    /**
     * Gets size in bytes. 
     * 
     * @return integer 
     */
    public function getSizeInBytes()
    {
        return $this->_sizeInBytes;
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
        $this->_sizeInBytes = $sizeInBytes;
    }

}

