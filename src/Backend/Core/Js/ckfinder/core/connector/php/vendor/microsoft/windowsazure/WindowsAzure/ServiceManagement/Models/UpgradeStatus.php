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
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceManagement\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Holds a deployment upgrade status.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class UpgradeStatus
{
    /**
     * @var string
     */
    private $_upgradeType;
    
    /**
     * @var string
     */
    private $_currentUpgradeDomainState;
    
    /**
     * @var integer
     */
    private $_currentUpgradeDomain;
    
    /**
     * Creates a new UpgradeStatus object from the parsed response.
     * 
     * @param array $parsed The parsed response body in array representation
     * 
     * @return \WindowsAzure\ServiceManagement\Models\UpgradeStatus 
     */
    public static function create($parsed)
    {
        $result                    = new UpgradeStatus();
        $upgradeType               = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_UPGRADE_TYPE
        );
        $currentUpgradeDomainState = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_CURRENT_UPGRADE_DOMAIN_STATE
        );
        $currentUpgradeDomain      = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_CURRENT_UPGRADE_DOMAIN
        );
        
        $result->setCurrentUpgradeDomain(intval($currentUpgradeDomain));
        $result->setCurrentUpgradeDomainState($currentUpgradeDomainState);
        $result->setUpgradeType($upgradeType);
        
        return $result;
    }
    
    /**
     * Gets the deployment upgrade type.
     * 
     * The upgrade type designated for this deployment. Possible values are Auto and
     * Manual.
     * 
     * @return string
     */
    public function getUpgradeType()
    {
        return $this->_upgradeType;
    }
    
    /**
     * Sets the deployment upgrade type.
     * 
     * @param string $upgradeType The deployment upgrade type.
     * 
     * @return none
     */
    public function setUpgradeType($upgradeType)
    {
        $this->_upgradeType = $upgradeType;
    }
    
    /**
     * Gets the deployment current upgrade domain state.
     * 
     * The state of the current upgrade domain. Possible values are Before and 
     * During.
     * 
     * @return string
     */
    public function getCurrentUpgradeDomainState()
    {
        return $this->_currentUpgradeDomainState;
    }
    
    /**
     * Sets the deployment current upgrade domain state.
     * 
     * @param string $currentUpgradeDomainState The deployment current upgrade domain
     * state.
     * 
     * @return none
     */
    public function setCurrentUpgradeDomainState($currentUpgradeDomainState)
    {
        $this->_currentUpgradeDomainState = $currentUpgradeDomainState;
    }
    
    /**
     * Gets the deployment current upgrade domain.
     * 
     * An integer value that identifies the current upgrade domain. Upgrade domains 
     * are identified with a zero-based index: the first upgrade domain has an ID of
     * 0, the second has an ID of 1, and so on.
     * 
     * @return integer
     */
    public function getCurrentUpgradeDomain()
    {
        return $this->_currentUpgradeDomain;
    }
    
    /**
     * Sets the deployment current upgrade domain.
     * 
     * @param integer $currentUpgradeDomain The deployment current upgrade domain.
     * 
     * @return none
     */
    public function setCurrentUpgradeDomain($currentUpgradeDomain)
    {
        $this->_currentUpgradeDomain = $currentUpgradeDomain;
    }
}