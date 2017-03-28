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
use WindowsAzure\Common\Internal\Utilities;

/**
 * The XML role environment data deserializer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class XmlRoleEnvironmentDataDeserializer
{
    /**
     * Deserializes the role environment data.
     * 
     * @param IInputChannel $inputChannel The input Channel.
     * 
     * @return RoleEnvironmentData
     */
    public function deserialize($inputChannel)
    {
        $document = stream_get_contents($inputChannel);

        $environmentInfo = Utilities::unserialize($document);

        $configurationSettings = $this->_translateConfigurationSettings(
            $environmentInfo
        );

        $localResources  = $this->_translateLocalResources($environmentInfo);
        $currentInstance = $this
            ->_translateCurrentInstance($environmentInfo);        
        $roles           = $this->_translateRoles(
            $environmentInfo,
            $currentInstance,
            $environmentInfo['CurrentInstance']['@attributes']['roleName']
        );

        return new RoleEnvironmentData(
            $environmentInfo['Deployment']['@attributes']['id'],
            $configurationSettings,
            $localResources,
            $currentInstance,
            $roles,
            ($environmentInfo['Deployment']['@attributes']['emulated'] == 'true')
        );
    }
    
    /**
     * Translates the configuration settings.
     * 
     * @param string $environmentInfo The role environment info.
     * 
     * @return array 
     */
    private function _translateConfigurationSettings($environmentInfo)
    {
        $configurationSettings = array();

        $settingsInfo = Utilities::tryGetKeysChainValue(
            $environmentInfo,
            'CurrentInstance',
            'ConfigurationSettings',
            'ConfigurationSetting'
        );
        
        if (!is_null($settingsInfo)) {
            if (array_key_exists('@attributes', $settingsInfo)) {
                $settingsInfo = array(0 => $settingsInfo);
            }

            foreach ($settingsInfo as $settingInfo) {
                $configurationSettings
                    [$settingInfo['@attributes']['name']] = $settingInfo
                        ['@attributes']['value'];
            }
        }

        return $configurationSettings;
    }
    
    /**
     * Translates the local resources.
     * 
     * @param string $environmentInfo The role environment info.
     * 
     * @return array 
     */
    private function _translateLocalResources($environmentInfo)
    {
        $localResourcesMap = array();

        $localResourcesInfo = Utilities::tryGetKeysChainValue(
            $environmentInfo,
            'CurrentInstance',
            'LocalResources',
            'LocalResource'
        );
        
        if (!is_null($localResourcesInfo)) {
            if (array_key_exists('@attributes', $localResourcesInfo)) {
                $localResourcesInfo = array(0 => $localResourcesInfo);
            }

            foreach ($localResourcesInfo as $localResourceInfo) {
                $localResource = new LocalResource(
                    $localResourceInfo['@attributes']['sizeInMB'],
                    $localResourceInfo['@attributes']['name'],
                    $localResourceInfo['@attributes']['path']
                );
                
                $localResourcesMap[$localResource->getName()] = $localResource;
            }
        }

        return $localResourcesMap;
    }
    
    /**
     * Translates the roles.
     * 
     * @param string       $environmentInfo The role environment info.
     * @param RoleInstance $currentInstance The current instance info.
     * @param string       $currentRole     The current role.
     * 
     * @return array
     */
    private function _translateRoles($environmentInfo, $currentInstance,
        $currentRole
    ) {
        $rolesMap = array();

        $rolesInfo = Utilities::tryGetKeysChainValue(
            $environmentInfo,
            'Roles',
            'Role'
        );
        
        if (!is_null($rolesInfo)) {
            if (array_key_exists('@attributes', $rolesInfo)) {
                $rolesInfo = array(0 => $rolesInfo);
            }

            foreach ($rolesInfo as $roleInfo) {
                $roleInstances = $this->_translateRoleInstances($roleInfo);

                if ($roleInfo['@attributes']['name'] == $currentRole) {
                    $roleInstances[$currentInstance->getId()] = $currentInstance;
                }

                $role = new Role($roleInfo['@attributes']['name'], $roleInstances);
                
                foreach ($roleInstances as $instance) {
                    $instance->setRole($role);
                }
                
                $rolesMap[$roleInfo['@attributes']['name']] = $role;
            }
        }
        
        if (!array_key_exists($currentRole, $rolesMap)) {
            $roleInstances                            = array();
            $roleInstances[$currentInstance->getId()] = $currentInstance;

            $singleRole = new Role($currentRole, $roleInstances);
            $currentInstance->setRole($singleRole);

            $rolesMap[$currentRole] = $singleRole;
        }

        return $rolesMap;
    }
    
    /**
     * Translates the role instances.
     * 
     * @param string $instancesInfo The instance info.
     * 
     * @return array
     */
    private function _translateRoleInstances($instancesInfo)
    {
        $roleInstanceMap = array();

        $instances = Utilities::tryGetKeysChainValue(
            $instancesInfo,
            'Instances',
            'Instance'
        );
        
        if (!is_null($instances)) {
            if (array_key_exists('@attributes', $instances)) {
                $instances = array(0 => $instances);
            }

            foreach ($instances as $instanceInfo) {
                $endpoints = $this->_translateRoleInstanceEndpoints(
                    $instanceInfo['Endpoints']['Endpoint']
                );

                $roleInstance = new RoleInstance(
                    $instanceInfo['@attributes']['id'],
                    $instanceInfo['@attributes']['faultDomain'],
                    $instanceInfo['@attributes']['updateDomain'],
                    $endpoints
                );
                
                $roleInstanceMap
                    [$instanceInfo['@attributes']['id']] = $roleInstance;
            }
        }

        return $roleInstanceMap;
    }
    
    /**
     * Translates the role instance endpoints.
     * 
     * @param string $endpointsInfo The endpoints info.
     * 
     * @return array
     */
    private function _translateRoleInstanceEndpoints($endpointsInfo)
    {
        $endpointsMap = array();

        $endpoints = $endpointsInfo;
        if (array_key_exists('@attributes', $endpoints)) {
            $endpoints = array(0 => $endpointsInfo);
        }

        foreach ($endpoints as $endpoint) {
            $roleInstanceEndpoint = new RoleInstanceEndpoint(
                $endpoint['@attributes']['protocol'],
                $endpoint['@attributes']['address'],
                intval($endpoint['@attributes']['port'], 10)
            );
            
            $endpointsMap[$endpoint['@attributes']['name']] = $roleInstanceEndpoint;
        }

        return $endpointsMap;
    }
    
    /**
     * Translates the current instance info.
     * 
     * @param string $environmentInfo The environment info.
     * 
     * @return RoleInstance
     */
    private function _translateCurrentInstance($environmentInfo)
    {
        $endpoints = array();
        
        $endpointsInfo = Utilities::tryGetKeysChainValue(
            $environmentInfo,
            'CurrentInstance',
            'Endpoints',
            'Endpoint'
        );
        
        if (!is_null($endpointsInfo)) {
            $endpoints = $this->_translateRoleInstanceEndpoints($endpointsInfo);
        }

        $currentInstance = new RoleInstance(
            $environmentInfo['CurrentInstance']['@attributes']['id'],
            $environmentInfo['CurrentInstance']['@attributes']['faultDomain'],
            $environmentInfo['CurrentInstance']['@attributes']['updateDomain'],
            $endpoints
        );

        foreach ($currentInstance->getInstanceEndpoints() as $endpoint) {
            $endpoint->setRoleInstance($currentInstance);
        }
        
        return $currentInstance;
    }
}

