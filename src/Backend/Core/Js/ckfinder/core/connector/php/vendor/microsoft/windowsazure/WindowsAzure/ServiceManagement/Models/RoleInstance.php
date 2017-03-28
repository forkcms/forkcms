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
 * Represents a Windows Azure deployment role instance.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RoleInstance
{
    /**
     * @var string
     */
    private $_roleName;
    
    /**
     * @var string
     */
    private $_instanceName;
    
    /**
     * @var string
     */
    private $_instanceStatus;
    
    /**
     * @var integer
     */
    private $_instanceUpgradeDomain;
    
    /**
     * @var integer
     */
    private $_instanceFaultDomain;
    
    /**
     * @var string
     */
    private $_instanceSize;
    
    /**
     * @var string
     */
    private $_instanceStateDetails;
    
    /**
     * @var string
     */
    private $_instanceErrorCode;
    
    /**
     * Creates a new RoleInstance from parsed response body.
     * 
     * @param array $parsed The parsed response body in array representation.
     * 
     * @return RoleInstance
     */
    public static function create($parsed)
    {
        $roleInstance          = new RoleInstance();
        $roleName              = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_ROLE_NAME
        );
        $instanceName          = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_NAME
        );
        $instanceStatus        = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_STATUS
        );
        $instanceUpgradeDomain = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_UPGRADE_DOMAIN
        );
        $instanceFaultDomain   = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_FAULT_DOMAIN
        );
        $instanceSize          = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_SIZE
        );
        $instanceStateDetails  = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_STATE_DETAILS
        );
        $instanceErrorCode     = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_INSTANCE_ERROR_CODE
        );
        
        $roleInstance->setInstanceErrorCode($instanceErrorCode);
        $roleInstance->setInstanceFaultDomain(intval($instanceFaultDomain));
        $roleInstance->setInstanceName($instanceName);
        $roleInstance->setInstanceSize($instanceSize);
        $roleInstance->setInstanceStateDetails($instanceStateDetails);
        $roleInstance->setInstanceStatus($instanceStatus);
        $roleInstance->setInstanceUpgradeDomain(intval($instanceUpgradeDomain));
        $roleInstance->setRoleName($roleName);
        
        return $roleInstance;
    }
    
    /**
     * Gets the role name.
     * 
     * The name of the role.
     * 
     * @return string
     */
    public function getRoleName()
    {
        return $this->_roleName;
    }
    
    /**
     * Sets the role name.
     * 
     * @param string $roleName The role name.
     * 
     * @return none
     */
    public function setRoleName($roleName)
    {
        $this->_roleName = $roleName;
    }
    
    /**
     * Gets the instance name.
     * 
     * The name of the specific role instance (if any).
     * 
     * @return string
     */
    public function getInstanceName()
    {
        return $this->_instanceName;
    }
    
    /**
     * Sets the instance name.
     * 
     * @param string $instanceName The instance name.
     * 
     * @return none
     */
    public function setInstanceName($instanceName)
    {
        $this->_instanceName = $instanceName;
    }
    
    /**
     * Gets the instance status.
     * 
     * The current status of this instance.
     * 
     * @return string
     */
    public function getInstanceStatus()
    {
        return $this->_instanceStatus;
    }
    
    /**
     * Sets the instance status.
     * 
     * @param string $instanceStatus The instance status.
     * 
     * @return none
     */
    public function setInstanceStatus($instanceStatus)
    {
        $this->_instanceStatus = $instanceStatus;
    }
    
    /**
     * Gets the instance upgrade domain.
     * 
     * The upgrade domain that this role instance belongs to. During an upgrade 
     * deployment, all roles in the same upgrade domain are upgraded at the same 
     * time.
     * 
     * @return integer
     */
    public function getInstanceUpgradeDomain()
    {
        return $this->_instanceUpgradeDomain;
    }
    
    /**
     * Sets the instance upgrade domain.
     * 
     * @param integer $instanceUpgradeDomain The instance upgrade domain.
     * 
     * @return none
     */
    public function setInstanceUpgradeDomain($instanceUpgradeDomain)
    {
        $this->_instanceUpgradeDomain = $instanceUpgradeDomain;
    }
    
    /**
     * Gets the instance fault domain.
     * 
     * The fault domain that this role instance belongs to. Role instances in the 
     * same fault domain may be vulnerable to the failure of a single piece of 
     * hardware.
     * 
     * @return integer
     */
    public function getInstanceFaultDomain()
    {
        return $this->_instanceFaultDomain;
    }
    
    /**
     * Sets the instance fault domain.
     * 
     * @param integer $instanceFaultDomain The instance fault domain.
     * 
     * @return none
     */
    public function setInstanceFaultDomain($instanceFaultDomain)
    {
        $this->_instanceFaultDomain = $instanceFaultDomain;
    }
    
    /**
     * Gets the instance size.
     * 
     * The size of the role instance. Possible values are: ExtraSmall, Small, Medium,
     * Large, ExtraLarge.
     * 
     * @return string
     */
    public function getInstanceSize()
    {
        return $this->_instanceSize;
    }
    
    /**
     * Sets the instance size.
     * 
     * @param string $instanceSize The instance size.
     * 
     * @return none
     */
    public function setInstanceSize($instanceSize)
    {
        $this->_instanceSize = $instanceSize;
    }
    
    /**
     * Gets the instance state details.
     * 
     * The instance state is returned as an English human-readable string that, when
     * present, provides a snapshot of the state of the virtual machine at the time
     * the operation was called. For example, when the instance is first being 
     * initialized a "Preparing Windows for first use." could be returned.
     * 
     * @return string
     */
    public function getInstanceStateDetails()
    {
        return $this->_instanceStateDetails;
    }
    
    /**
     * Sets the instance state details.
     * 
     * @param string $instanceStateDetails The instance state details.
     * 
     * @return none
     */
    public function setInstanceStateDetails($instanceStateDetails)
    {
        $this->_instanceStateDetails = $instanceStateDetails;
    }
    
    /**
     * Gets the instance error code.
     * 
     * Error code of the latest role or VM start. For VMRoles the error codes are: 
     * WaitTimeout, VhdTooLarge, AzureInternalError.
     * 
     * For web and worker roles this field returns an error code that can be provided
     * to Windows Azure support to assist in resolution of errors. Typically this
     * field will be empty.
     * 
     * @return string
     */
    public function getInstanceErrorCode()
    {
        return $this->_instanceErrorCode;
    }
    
    /**
     * Sets the instance error code.
     * 
     * @param string $instanceErrorCode The instance error code.
     * 
     * @return none
     */
    public function setInstanceErrorCode($instanceErrorCode)
    {
        $this->_instanceErrorCode = $instanceErrorCode;
    }
}