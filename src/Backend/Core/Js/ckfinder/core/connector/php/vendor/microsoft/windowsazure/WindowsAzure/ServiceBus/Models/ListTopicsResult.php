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

use WindowsAzure\Common\Internal\Atom\Feed;
use WindowsAzure\Common\Internal\Atom\Entry;

/**
 * The result of a list topics request. 
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class ListTopicsResult extends Feed
{
    /**
     * Gets the information of the topic. 
     * 
     * @var array
     */ 
    private $_topicInfos;

    /**
     * Populates the properties with a the response from the list topics request.
     * 
     * @param string $response The body of the response of the list topics request. 
     * 
     * @return none
     */ 
    public function parseXml($response)
    {
        parent::parseXml($response);
        $listTopicsResultXml = new \SimpleXMLElement($response);
        $this->_topicInfos   = array();
        foreach ($listTopicsResultXml->entry as $entry) {
            $topicInfo = new TopicInfo();   
            $topicInfo->parseXml($entry->asXml());
            $this->_topicInfos[] = $topicInfo;
        } 
    }

    /**
     * Creates a list topics result with default parameters. 
     */
    public function __construct()
    {
    }

    /**
     * Gets the information of the topic. 
     *  
     * @return array
     */
    public function getTopicInfos()
    {
        return $this->_topicInfos;
    }

    /**
     * Sets the topic information.
     *
     * @param array $topicInfos The information of the topics. 
     * 
     * @return none
     */
    public function setTopicInfos($topicInfos)
    {
        $this->_topicInfos = $topicInfos;
    }

}

