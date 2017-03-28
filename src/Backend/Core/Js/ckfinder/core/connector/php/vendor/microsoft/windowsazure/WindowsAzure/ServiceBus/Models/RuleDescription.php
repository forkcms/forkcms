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
use WindowsAzure\ServiceBus\Internal\Action;
use WindowsAzure\ServiceBus\Internal\Filter;
/**
 *  The description of the rule.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      http://msdn.microsoft.com/en-us/library/windowsazure/hh780753
 */

class RuleDescription
{
    /**
     * The filter of the rule. 
     * 
     * @var Filter
     */
    private $_filter;
    
    /**
     * The action of the rule. 
     * 
     * @var Action
     */
    private $_action;

    /**
     * The name of the rule. 
     * 
     * @var string 
     */
    private $_name;


    /**
     * Creates a rule description instance with default parameters. 
     */
    public function __construct()
    {   
    }

    // @codingStandardsIgnoreStart
    
    /**
     * Creates a rule description instance with specified XML string. 
     * 
     * @param string $ruleDescriptionXml A XML string representing the 
     * rule description.
     * 
     * @return none
     */
    public static function create($ruleDescriptionXml)
    {
        $ruleDescription      = new RuleDescription();
        $root                 = simplexml_load_string(
            $ruleDescriptionXml
        );
        $nameSpaces           = $root->getNameSpaces();
        $ruleDescriptionArray = (array)$root;
        if (array_key_exists('Filter', $ruleDescriptionArray)) {
            $filterItem       = $ruleDescriptionArray['Filter'];
            $filterAttributes = $filterItem->attributes('i', true);
            $filterItemArray  = (array)$filterItem;
            $filterType       = (string)$filterAttributes['type'];
            $filter           = null; 
            switch ($filterType) {
            case 'TrueFilter'  :
                $filter = new TrueFilter();
                break;

            case 'FalseFilter' :
                $filter = new FalseFilter();
                break;

            case 'CorrelationFilter' :
                $filter = new CorrelationFilter();

                if (array_key_exists('CorrelationId', $filterItemArray)) {   
                    $filter->setCorrelationId(
                        (string)$filterItemArray['CorrelationId']
                    );
                }
                break;

            case 'SqlFilter' :
                $filter = new SqlFilter();

                if (array_key_exists('SqlExpression', $filterItemArray)) {   
                    $filter->setSqlExpression(
                        (string)$filterItemArray['SqlExpression']
                    );
                }
                if (array_key_exists('CompatibilityLevel', $filterItemArray)) {
                    $filter->setCompatibilityLevel(
                        (integer)$filterItemArray['CompatibilityLevel']
                    );
                }

                break;
               
            default :
                $filter = new Filter();                
            }

            $ruleDescription->setFilter($filter);
        } 

        if (array_key_exists('Action', $ruleDescriptionArray)) {
            $actionItem       = $ruleDescriptionArray['Action'];
            $actionAttributes = $actionItem->attributes('i', true);
            $actionType       = (string)$actionAttributes['type'];
            $action           = null; 

            switch ($actionType) {
            case 'EmptyRuleAction'  :
                $action = new EmptyRuleAction();
                break;

            case 'SqlRuleAction' :
                $action = new SqlRuleAction();

                if (array_key_exists('SqlExpression', $actionItem)) {   
                    $action->setSqlExpression((string)$actionItem['SqlExpression']);
                }
                break;
               
            default :
                $action = new Action();                
            }

            $ruleDescription->setAction($action);
        } 

        if (array_key_exists('Name', $ruleDescriptionArray)) {
            $ruleDescription->setName((string)$ruleDescriptionArray['Name']);
        } 
       
        return $ruleDescription;
    }
    
    // @codingStandardsIgnoreEnd
    
    /**
     * Gets the filter. 
     *
     * @return Filter
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * Sets the filter of the rule description. 
     * 
     * @param Filter $filter The filter of the rule description. 
     * 
     * @return none
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
    }

    /**
     * Gets the action. 
     *
     * @return Action
     */
    public function getAction()
    {
        return $this->_action;

    }

    /**
     * Sets the action of the rule description. 
     * 
     * @param Action $action The action of the rule description. 
     * 
     * @return none
     */
    public function setAction($action)
    {
        $this->_action = $action;
    }

    /**
     * Gets the name of the rule description. 
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the name of the rule description. 
     * 
     * @param string $name The name of the rule description. 
     * 
     * @return none
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

}