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
 * Represents a Windows Azure deployment.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Deployment
{
    /**
     * @var string
     */
    private $_name;
    
    /**
     * @var string
     */
    private $_slot;
    
    /**
     * @var string
     */
    private $_privateId;
    
    /**
     * @var string
     */
    private $_status;
    
    /**
     * @var string
     */
    private $_label;
    
    /**
     * @var string
     */
    private $_configuration;
    
    /**
     * @var array
     */
    private $_roleInstanceList;
    
    /**
     * @var integer
     */
    private $_upgradeDomainCount;
    
    /**
     * @var array
     */
    private $_roleList;
    
    /**
     * @var string
     */
    private $_sdkVersion;
    
    /**
     * @var array
     */
    private $_inputEndpointList;
    
    /**
     * @var boolean
     */
    private $_locked;
    
    /**
     * @var boolean
     */
    private $_rollbackAllowed;
    
    /**
     * @var UpgradeStatus
     */
    private $_upgradeStatus;
    
    /**
     * Creates a new Deployment from parsed response body.
     * 
     * @param array $parsed The parsed response body in array representation.
     * 
     * @return Deployment
     */
    public static function create($parsed)
    {
        $result             = new Deployment();
        $name               = Utilities::tryGetValue($parsed, Resources::XTAG_NAME);
        $label              = Utilities::tryGetValue($parsed, Resources::XTAG_LABEL);
        $url                = Utilities::tryGetValue($parsed, Resources::XTAG_URL);
        $locked             = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_LOCKED
        );
        $rollbackAllowed    = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_ROLLBACK_ALLOWED
        );
        $sdkVersion         = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_SDK_VERSION
        );
        $inputEndpointList  = Utilities::tryGetKeysChainValue(
            $parsed,
            Resources::XTAG_INPUT_ENDPOINT_LIST,
            Resources::XTAG_INPUT_ENDPOINT
        );
        $roleList           = Utilities::tryGetKeysChainValue(
            $parsed,
            Resources::XTAG_ROLE_LIST,
            Resources::XTAG_ROLE
        );
        $roleInstanceList   = Utilities::tryGetKeysChainValue(
            $parsed,
            Resources::XTAG_ROLE_INSTANCE_LIST,
            Resources::XTAG_ROLE_INSTANCE
        );
        $status             = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_STATUS
        );
        $slot               = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_DEPLOYMENT_SLOT
        );
        $privateId          = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_PRIVATE_ID
        );
        $configuration      = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_CONFIGURATION
        );
        $upgradeDomainCount = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_UPGRADE_DOMAIN_COUNT
        );
        $upgradeStatus      = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_UPGRADE_STATUS
        );
        
        $result->setConfiguration($configuration);
        $result->setLabel($label);
        $result->setLocked(Utilities::toBoolean($locked));
        $result->setName($name);
        $result->setPrivateId($privateId);
        $result->setRollbackAllowed(Utilities::toBoolean($rollbackAllowed));
        $result->setSdkVersion($sdkVersion);
        $result->setSlot($slot);
        $result->setStatus($status);
        $result->setUpgradeDomainCount(intval($upgradeDomainCount));
        $result->setUpgradeStatus(UpgradeStatus::create($upgradeStatus));
        $result->setUrl($url);
        $result->setRoleInstanceList(
            Utilities::createInstanceList(
                Utilities::getArray($roleInstanceList),
                'WindowsAzure\ServiceManagement\Models\RoleInstance'
            )
        );
        $result->setRoleList(
            Utilities::createInstanceList(
                Utilities::getArray($roleList),
                'WindowsAzure\ServiceManagement\Models\Role'
            )
        );
        $result->setInputEndpointList(
            Utilities::createInstanceList(
                Utilities::getArray($inputEndpointList),
                'WindowsAzure\ServiceManagement\Models\InputEndpoint'
            )
        );

        return $result;
    }
    
    /**
     * Gets the deployment name.
     * 
     * The user-supplied name for this deployment.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Sets the deployment name.
     * 
     * @param string $name The deployment name.
     * 
     * @return none
     */
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * Gets the deployment slot.
     * 
     * The environment to which the hosted service is deployed, either staging or 
     * production.
     * 
     * @return string
     */
    public function getSlot()
    {
        return $this->_slot;
    }
    
    /**
     * Sets the deployment slot.
     * 
     * @param string $slot The deployment slot.
     * 
     * @return none
     */
    public function setSlot($slot)
    {
        $this->_slot = $slot;
    }
    
    /**
     * Gets the deployment label.
     * 
     * The user-supplied name of the deployment returned as a base-64 encoded string.
     * This name can be used identify the deployment for your tracking purposes.
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }
    
    /**
     * Sets the deployment label.
     * 
     * @param string $label The deployment label.
     * 
     * @return none
     */
    public function setLabel($label)
    {
        $this->_label = $label;
    }
    
    /**
     * Gets the deployment private Id.
     * 
     * A unique identifier generated internally by Windows Azure for this deployment.
     * 
     * @return string
     */
    public function getPrivateId()
    {
        return $this->_privateId;
    }
    
    /**
     * Sets the deployment private Id.
     * 
     * @param string $privateId The deployment privateId.
     * 
     * @return none
     */
    public function setPrivateId($privateId)
    {
        $this->_privateId = $privateId;
    }
    
    /**
     * Gets the deployment status.
     * 
     * The status of the deployment. Possible values are: Running, Suspended, 
     * RunningTransitioning, SuspendedTransitioning, Starting, Suspending, Deploying,
     * Deploying.
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * Sets the deployment status.
     * 
     * @param string $status The deployment status.
     * 
     * @return none
     */
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    /**
     * Gets the deployment url.
     * 
     * The URL used to access the hosted service. 
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }
    
    /**
     * Sets the deployment url.
     * 
     * @param string $url The deployment url.
     * 
     * @return none
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }
    
    /**
     * Gets the deployment configuration.
     * 
     * The base-64 encoded configuration file of the deployment.
     * 
     * @return string
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }
    
    /**
     * Sets the configuration.
     * 
     * @param string $configuration The deployment configuration.
     * 
     * @return none
     */
    public function setConfiguration($configuration)
    {
        $this->_configuration = $configuration;
    }
    
    /**
     * Gets the deployment role instance list.
     * 
     * @return array
     */
    public function getRoleInstanceList()
    {
        return $this->_roleInstanceList;
    }
    
    /**
     * Sets the deployment role instance list.
     * 
     * @param array $roleInstanceList The deployment role instance list.
     * 
     * @return none
     */
    public function setRoleInstanceList($roleInstanceList)
    {
        $this->_roleInstanceList = $roleInstanceList;
    }
    
    /**
     * Gets the deployment upgrade domain count.
     * 
     * @return integer
     */
    public function getUpgradeDomainCount()
    {
        return $this->_upgradeDomainCount;
    }
    
    /**
     * Sets the deployment upgradeDomainCount.
     * 
     * @param integer $upgradeDomainCount The deployment upgrade domain count.
     * 
     * @return none
     */
    public function setUpgradeDomainCount($upgradeDomainCount)
    {
        $this->_upgradeDomainCount = $upgradeDomainCount;
    }
    
    /**
     * Gets the deployment role list.
     * 
     * Contains the provisioning details for the new virtual machine deployment.
     * 
     * @return array
     */
    public function getRoleList()
    {
        return $this->_roleList;
    }
    
    /**
     * Sets the deployment role list.
     * 
     * @param array $roleList The deployment role list.
     * 
     * @return none
     */
    public function setRoleList($roleList)
    {
        $this->_roleList = $roleList;
    }
    
    /**
     * Gets the deployment SDK version.
     * 
     * @return string
     */
    public function getSdkVersion()
    {
        return $this->_sdkVersion;
    }
    
    /**
     * Sets the deployment SDK version.
     * 
     * @param string $sdkVersion The deployment SDK version.
     * 
     * @return none
     */
    public function setSdkVersion($sdkVersion)
    {
        $this->_sdkVersion = $sdkVersion;
    }
    
    /**
     * Gets the deployment input endpoint list.
     * 
     * @return array
     */
    public function getInputEndpointList()
    {
        return $this->_inputEndpointList;
    }
    
    /**
     * Sets the deployment input endpoint list.
     * 
     * @param array $inputEndpointList The deployment input endpoint list.
     * 
     * @return none
     */
    public function setInputEndpointList($inputEndpointList)
    {
        $this->_inputEndpointList = $inputEndpointList;
    }
    
    /**
     * Gets the deployment locked flag.
     * 
     * @return boolean
     */
    public function getLocked()
    {
        return $this->_locked;
    }
    
    /**
     * Sets the deployment locked flag.
     * 
     * @param boolean $locked The deployment locked flag.
     * 
     * @return none
     */
    public function setLocked($locked)
    {
        $this->_locked = $locked;
    }
    
    /**
     * Gets the deployment rollback allowed flag.
     * 
     * @return boolean
     */
    public function getRollbackAllowed()
    {
        return $this->_rollbackAllowed;
    }
    
    /**
     * Sets the deployment rollbackAllowed.
     * 
     * @param boolean $rollbackAllowed The deployment rollback allowed flag.
     * 
     * @return none
     */
    public function setRollbackAllowed($rollbackAllowed)
    {
        $this->_rollbackAllowed = $rollbackAllowed;
    }
    
    /**
     * Gets the deployment upgrade status.
     * 
     * @return UpgradeStatus
     */
    public function getUpgradeStatus()
    {
        return $this->_upgradeStatus;
    }
    
    /**
     * Sets the deployment upgrade status.
     * 
     * @param UpgradeStatus $upgradeStatus The deployment upgrade status.
     * 
     * @return none
     */
    public function setUpgradeStatus($upgradeStatus)
    {
        $this->_upgradeStatus = $upgradeStatus;
    }
}