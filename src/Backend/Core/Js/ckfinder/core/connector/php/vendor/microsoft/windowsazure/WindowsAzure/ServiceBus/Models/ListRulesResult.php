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
use WindowsAzure\ServiceBus\Models\RuleInfo;

/**
 * The result of the list rules request.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class ListRulesResult extends Feed
{
    /**
     * The information of the rule. 
     * 
     * @var array
     */
    private $_ruleInfos;

    /** 
     * Populates the properties with the response from the list rules request.
     * 
     * @param string $response The body of the response of the list rules request. 
     * 
     * @return none
     */
    public function parseXml($response)
    {
        parent::parseXml($response);
        $listRulesResultXml = new \SimpleXMLElement($response);
        $this->_ruleInfos   = array();

        foreach ($listRulesResultXml->entry as $entry) {
            $ruleInfo = new RuleInfo();
            $ruleInfo->parseXml($entry->asXml());
            $this->_ruleInfos[] = $ruleInfo;
        }
    }

    /**
     * Creates a list rules result instance with default parameters. 
     */
    public function __construct()
    {
    }

    /**
     * Gets the information of the rules. 
     * 
     * @return array
     */
    public function getRuleInfos()
    {
        return $this->_ruleInfos;
    }

    /** 
     * Sets the information of the rule. 
     * 
     * @param array $ruleInfos The information of the rule. 
     * 
     * @return none
     */ 
    public function setRuleInfos($ruleInfos)
    {
        $this->_ruleInfos = $ruleInfos;
    }

}

