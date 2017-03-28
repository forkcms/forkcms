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
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceRuntime\Internal;

/**
 * The role environment data.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RoleEnvironmentData
{
    /**
     * @var string
     */
    private $_deploymentId;
    
    /**
     * @var array
     */
    private $_configurationSettings;

    /**
     * @var array
     */
    private $_localResources;
    
    /**
     * @var RoleInstance
     */
    private $_currentInstance;
    
    /**
     * @var array
     */
    private $_roles;
    
    /**
     * @var boolean
     */
    private $_isEmulated;

    /**
     * Constructor
     * 
     * @param string       $deploymentId          The deployment identifier.
     * @param array        $configurationSettings The configuration settings.
     * @param array        $localResources        The local resources.
     * @param RoleInstance $currentInstance       The current instance information.
     * @param array        $roles                 The instance roles.
     * @param boolean      $isEmulated            Boolean value indicating if
     *     the instance is running in the emulator.
     */
    public function __construct($deploymentId, $configurationSettings, 
        $localResources, $currentInstance, $roles, $isEmulated
    ) {
        $this->_deploymentId          = $deploymentId;
        $this->_configurationSettings = $configurationSettings;
        $this->_localResources        = $localResources;
        $this->_currentInstance       = $currentInstance;
        $this->_roles                 = $roles;
        $this->_isEmulated            = $isEmulated;
    }
    
    /**
     * Gets the configuration settings.
     * 
     * @return array
     */
    function getConfigurationSettings()
    {
        return $this->_configurationSettings;
    }
    
    /**
     * Gets the local resources.
     * 
     * @return array
     */
    function getLocalResources()
    {
        return $this->_localResources;
    }
    
    /**
     * Gets the current instance.
     * 
     * @return RoleInstance
     */
    function getCurrentInstance()
    {
        return $this->_currentInstance;
    }
    
    /**
     * Gets the roles.
     * 
     * @return array
     */
    function getRoles()
    {
        return $this->_roles;
    }
    
    /**
     * Gets the deployment identifier.
     * 
     * @return string
     */
    function getDeploymentId()
    {
        return $this->_deploymentId;
    }
    
    /**
     * Indicates if it is emulated.
     * 
     * @return boolean
     */
    function isEmulated()
    {
        return $this->_isEmulated;
    }
}

