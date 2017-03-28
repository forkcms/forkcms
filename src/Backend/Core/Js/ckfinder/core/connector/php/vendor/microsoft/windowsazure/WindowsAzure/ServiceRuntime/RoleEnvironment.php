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
 * @package   WindowsAzure\ServiceRuntime
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceRuntime;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\ServiceRuntime\Internal\RuntimeKernel;
use WindowsAzure\ServiceRuntime\Internal\RoleEnvironmentNotAvailableException;
use WindowsAzure\ServiceRuntime\Internal\ChannelNotAvailableException;
use WindowsAzure\ServiceRuntime\Internal\CurrentStatus;
use WindowsAzure\ServiceRuntime\Internal\AcquireCurrentState;
use WindowsAzure\ServiceRuntime\Internal\ReleaseCurrentState;
use WindowsAzure\ServiceRuntime\Internal\RoleInstanceStatus;
use WindowsAzure\ServiceRuntime\Internal\RoleEnvironmentConfigurationSettingChange;
use WindowsAzure\ServiceRuntime\Internal\RoleEnvironmentTopologyChange;

/**
 * Represents the Windows Azure environment in which an instance of a role is 
 * running.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RoleEnvironment
{
    /**
     * Specifies the environment variable that contains the path to the endpoint.
     * 
     * @var string
     */
    const VERSION_ENDPOINT_ENVIRONMENT_NAME = 'WaRuntimeEndpoint';

    /**
     * Specifies the endpoint fixed path.
     * 
     * @var string
     */
    const VERSION_ENDPOINT_FIXED_PATH = '\\\\.\\pipe\\WindowsAzureRuntime';

    /**
     * @var string
     */
    private static $_clientId;
    
    /**
     * @var \DateTime
     */
    private static $_maxDateTime;
    
    /**
     * @var IRuntimeClient
     */
    private static $_runtimeClient;

    /**
     * @var GoalState
     */
    private static $_currentGoalState;

    /**
     * @var RoleEnvironmentData
     */
    private static $_currentEnvironmentData;

    /**
     * @var array
     */
    private static $_changingListeners;

    /**
     * @var array
     */    
    private static $_changedListeners;

    /**
     * @var array
     */
    private static $_stoppingListeners;

    /**
     * @var CurrentState
     */
    private static $_lastState;

    /**
     * @var string
     */
    private static $_versionEndpoint;

    /**
     * @var mix
     */
    private static $_tracking;

    /**
     * Initializes the role environment.
     * 
     * @static
     * 
     * @return none
     */
    public static function init()
    {
        self::$_clientId = uniqid();

        // 2038-01-19 04:14:07 
        self::$_maxDateTime = new \DateTime(
            date(Resources::TIMESTAMP_FORMAT, Resources::INT32_MAX)
        );
        
        self::$_tracking = true;
    }
    
    /**
     * Returns the client identifier.
     * 
     * @static
     * 
     * @return string
     */
    public static function getClientId()
    {
        return self::$_clientId;
    }
    
    /**
     * Initializes the runtime client.
     * 
     * @param bool $keepOpen Boolean value indicating if the connection
     *     should remain open.
     * 
     * @static
     * 
     * @return none
     */
    private static function _initialize($keepOpen = false)
    {
        try {
            if (is_null(self::$_runtimeClient)) {
                self::$_versionEndpoint = getenv(
                    self::VERSION_ENDPOINT_ENVIRONMENT_NAME
                );

                if (self::$_versionEndpoint == false) {
                    self::$_versionEndpoint = self::VERSION_ENDPOINT_FIXED_PATH;
                }

                $kernel = RuntimeKernel::getKernel();
                $kernel->getProtocol1RuntimeGoalStateClient()->setKeepOpen(
                    $keepOpen
                );

                self::$_runtimeClient = $kernel->getRuntimeVersionManager()
                    ->getRuntimeClient(self::$_versionEndpoint);

                self::$_currentGoalState = self::$_runtimeClient
                    ->getCurrentGoalState();

                self::$_currentEnvironmentData = self::$_runtimeClient
                    ->getRoleEnvironmentData();
            } else {
                self::$_currentGoalState = self::$_runtimeClient
                    ->getCurrentGoalState();

                self::$_currentEnvironmentData = self::$_runtimeClient
                    ->getRoleEnvironmentData();
            }
        } catch (ChannelNotAvailableException $ex) {
            throw new RoleEnvironmentNotAvailableException();
        }
    }

    /**
     * Tracks role environment changes raising events as necessary.
     * 
     * This method is blocking and can/should be called in a separate fork.
     * 
     * @static
     * 
     * @return none
     */
    public static function trackChanges()
    {
        self::_initialize(true);

        while (self::$_tracking) {
            $newGoalState = self::$_runtimeClient->getCurrentGoalState();

            switch ($newGoalState->getExpectedState()) {
            case CurrentStatus::STARTED:
                $newIncarnation     = $newGoalState->getIncarnation();
                $currentIncarnation = self::$_currentGoalState->getIncarnation();
                
                if ($newIncarnation > $currentIncarnation) {
                    self::_processGoalStateChange($newGoalState);
                }

                break;
            case CurrentStatus::STOPPED:
                self::_raiseStoppingEvent();

                $stoppedState = new AcquireCurrentState(
                    self::$_clientId,
                    $newGoalState->getIncarnation(),
                    CurrentStatus::STOPPED,
                    self::$_maxDateTime
                );

                self::$_runtimeClient->setCurrentState($stoppedState);
                break;
            }
            
            if (is_int(self::$_tracking)) {
                self::$_tracking--;
            }
        }
    }
    
    /**
     * Processes a goal state change.
     * 
     * @param GoalState $newGoalState The new goal state.
     * 
     * @static
     * 
     * @return none
     */
    private static function _processGoalStateChange($newGoalState)
    {
        $last    = self::$_lastState;
        $changes = self::_calculateChanges(); 
        
        if (count($changes) == 0) {
            self::_acceptLatestIncarnation($newGoalState, $last);
        } else {
            self::_raiseChangingEvent($changes);
            
            self::_acceptLatestIncarnation($newGoalState, $last);
            
            self::$_currentEnvironmentData = self::$_runtimeClient
                ->getRoleEnvironmentData();
            
            self::_raiseChangedEvent($changes);
        }
    }

    /**
     * Accepts the latest incarnation.
     * 
     * @param GoalState    $newGoalState The new goal state.
     * @param CurrentState $last         The last state.
     * 
     * @static
     * 
     * @return none
     */
    private static function _acceptLatestIncarnation($newGoalState, $last)
    {
        if (!is_null($last) && $last instanceof AcquireCurrentState) {
            $acquireState = $last;
            
            $acceptState = new AcquireCurrentState(
                self::$_clientId,
                $newGoalState->getIncarnation(),
                $acquireState->getStatus(),
                $acquireState->getExpiration()
            );
            
            self::$_runtimeClient->setCurrentState($acceptState);
        }
        
        self::$_currentGoalState = $newGoalState;
    }

    /**
     * Raises a stopping event.
     * 
     * @static
     * 
     * @return none
     */
    private static function _raiseStoppingEvent()
    {
        foreach (self::$_stoppingListeners as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Raises a changing event.
     * 
     * @param array $changes The changes.
     * 
     * @static
     * 
     * @return none
     */
    private static function _raiseChangingEvent($changes)
    {
        foreach (self::$_changingListeners as $callback) {
            call_user_func($callback, $changes);
        }
    }
    
    /**
     * Raises a changed event.
     * 
     * @param array $changes The changes.
     * 
     * @static
     * 
     * @return none
     */
    private static function _raiseChangedEvent($changes)
    {
        foreach (self::$_changedListeners as $callback) {
            call_user_func($callback, $changes);
        }
    }
    
    /**
     * Calculates changes.
     *
     * @static
     *
     * @return array
     */
    private static function _calculateChanges()
    {
        $current = self::$_currentEnvironmentData;
        $newData = self::$_runtimeClient->getRoleEnvironmentData();
        
        $changes = self::_calculateConfigurationChanges($current, $newData);

        $currentRoles   = $current->getRoles();
        $newRoles       = $newData->getRoles();
        $changedRoleSet = array();
        
        foreach ($currentRoles as $roleName => $role) {
            if (array_key_exists($roleName, $newRoles)) {
                $currentRole = $currentRoles[$roleName];
                $newRole     = $newRoles[$roleName];

                $currentRoleInstances = $currentRole->getInstances();
                $newRoleInstances     = $newRole->getInstances();
                    
                $changedRoleSet = array_merge(
                    $changedRoleSet,
                    self::_calculateNewRoleInstanceChanges(
                        $role,
                        $currentRoleInstances,
                        $newRoleInstances
                    )
                );
            } else {
                $changedRoleSet[] = $role;
            }
        }
        
        foreach ($newRoles as $roleName => $role) {
            if (array_key_exists($roleName, $currentRoles)) {
                $currentRole = $currentRoles[$roleName];
                $newRole     = $newRoles[$roleName];
                
                $currentRoleInstances = $currentRole->getInstances();
                $newRoleInstances     = $newRole->getInstances();
                    
                $changedRoleSet = array_merge(
                    $changedRoleSet,
                    self::_calculateCurrentRoleInstanceChanges(
                        $role,
                        $currentRoleInstances,
                        $newRoleInstances
                    )
                );
            } else {
                $changedRoleSet[] = $role;
            }
        }
        
        foreach ($changedRoleSet as $role) {
            $changes[] = new RoleEnvironmentTopologyChange($role);
        }
        
        return $changes;
    }
    
    /**
     * Calculates the configuration changes.
     * 
     * @param RoleEnvironmentData $currentRoleEnvironment The current role 
     *     environment data.
     * @param RoleEnvionrmentData $newRoleEnvironment     The new role 
     *     environment data.
     * 
     * @static
     * 
     * @return array
     */
    private static function _calculateConfigurationChanges(
        $currentRoleEnvironment, $newRoleEnvironment
    ) {  
        $changes       = array();
        $currentConfig = $currentRoleEnvironment->getConfigurationSettings();
        $newConfig     = $newRoleEnvironment->getConfigurationSettings();
        
        foreach ($currentConfig as $settingKey => $setting) {
            if (array_key_exists($settingKey, $newConfig)) {
                if ($newConfig[$settingKey] != $currentConfig[$settingKey]) {
                    $changes[] = new RoleEnvironmentConfigurationSettingChange(
                        $settingKey
                    );
                }
            } else {
                $changes[] = new RoleEnvironmentConfigurationSettingChange(
                    $settingKey
                );
            }
        }
        
        foreach ($newConfig as $settingKey => $setting) {
            if (!array_key_exists($settingKey, $currentConfig)) {
                $changes[] = new RoleEnvironmentConfigurationSettingChange(
                    $settingKey
                );
            }
        }
        
        return $changes;
    }
    
    /**
     * Calculates which instances / instance endpoints were added from the current
     * role to the new role.
     * 
     * @param RoleInstance $role                 The current role.
     * @param array        $currentRoleInstances The current role instances.
     * @param array        $newRoleInstances     The new role instances.
     * 
     * @static
     * 
     * @return array
     */
    private static function _calculateNewRoleInstanceChanges(
        $role, $currentRoleInstances, $newRoleInstances
    ) {
        $changedRoleSet = array();
        
        foreach ($currentRoleInstances as $instanceKey => $currentInstance) {
            if (array_key_exists($instanceKey, $newRoleInstances)) {
                $newInstance = $newRoleInstances[$instanceKey];

                $currentUpdateDomain = $currentInstance->getUpdateDomain();
                $newUpdateDomain     = $newInstance->getUpdateDomain();
                $currentFaultDomain  = $currentInstance->getFaultDomain();
                $newFaultDomain      = $newInstance->getFaultDomain();
                
                if ($currentUpdateDomain == $newUpdateDomain
                    && $currentFaultDomain == $newFaultDomain
                ) {
                    $currentInstanceEndpoints = $currentInstance
                        ->getInstanceEndpoints();
                    $newInstanceEndpoints     = $newInstance->getInstanceEndpoints();
                    
                    $changedRoleSet = array_merge(
                        $changedRoleSet,
                        self::_calculateNewRoleInstanceEndpointsChanges(
                            $role,
                            $currentInstanceEndpoints,
                            $newInstanceEndpoints
                        )
                    );
                } else {
                    $changedRoleSet[] = $role;
                }
            } else {
                $changedRoleSet[] = $role;
            }
        }

        return $changedRoleSet;
    }
    
    /**
     * Calculates which endpoints / endpoint were added from the current
     * role to the new role.
     * 
     * @param RoleInstance $role                     The current role.
     * @param array        $currentInstanceEndpoints The current instance endpoints.
     * @param array        $newInstanceEndpoints     The new instance endpoints.
     * 
     * @static
     * 
     * @return array
     */
    private static function _calculateNewRoleInstanceEndpointsChanges(
        $role, $currentInstanceEndpoints, $newInstanceEndpoints
    ) {
        $changedRoleSet = array();

        foreach ($currentInstanceEndpoints as $endpointKey => $currentEndpoint) {
            if (array_key_exists($endpointKey, $newInstanceEndpoints)) {
                $newEndpoint = $newInstanceEndpoints[$endpointKey];

                $currentProtocol = $currentEndpoint->getProtocol();
                $newProtocol     = $newEndpoint->getProtocol();
                $currentAddress  = $currentEndpoint->getAddress();
                $newAddress      = $newEndpoint->getAddress();
                $currentPort     = $currentEndpoint->getPort();
                $newPort         = $newEndpoint->getPort();
                if ($currentProtocol != $newProtocol
                    || $currentAddress != $newAddress
                    || $currentPort != $newPort
                ) {
                    $changedRoleSet[] = $role;
                }
            } else {
                $changedRoleSet[] = $role;
            }
        }

        return $changedRoleSet;
    }
    
    /**
     * Calculates which instances / instance endpoints were removed from the current
     * role to the new role.
     * 
     * @param RoleInstance $role                 The current role.
     * @param array        $currentRoleInstances The current role instances.
     * @param array        $newRoleInstances     The new role instances.
     * 
     * @static
     * 
     * @return array
     */
    private static function _calculateCurrentRoleInstanceChanges(
        $role, $currentRoleInstances, $newRoleInstances
    ) {
        $changedRoleSet = array();
        
        foreach ($newRoleInstances as $instanceKey => $newInstance) {
            if (array_key_exists($instanceKey, $currentRoleInstances)) {
                $currentInstance = $currentRoleInstances[$instanceKey];
                
                $currentUpdateDomain = $currentInstance->getUpdateDomain();
                $newUpdateDomain     = $newInstance->getUpdateDomain();
                $currentFaultDomain  = $currentInstance->getFaultDomain();
                $newFaultDomain      = $newInstance->getFaultDomain();
                
                if ($currentUpdateDomain == $newUpdateDomain
                    && $currentFaultDomain == $newFaultDomain
                ) {
                    $newInstanceEndpoints     = $newInstance
                        ->getInstanceEndpoints();
                    $currentInstanceEndpoints = $currentInstance
                        ->getInstanceEndpoints();

                    $changedRoleSet = array_merge(
                        $changedRoleSet,
                        self::_calculateCurrentRoleInstanceEndpointsChanges(
                            $role,
                            $currentInstanceEndpoints,
                            $newInstanceEndpoints
                        )
                    );
                }
                // Intentionally not adding since if the values are different,
                // it should have already been added
            } else {
                $changedRoleSet[] = $role;
            }
        }

        return $changedRoleSet;
    }
    
    /**
     * Calculates which endpoints / endpoint were removed from the current
     * role to the new role.
     * 
     * @param RoleInstance $role                     The current changed role set.
     * @param array        $currentInstanceEndpoints The current instance endpoints.
     * @param array        $newInstanceEndpoints     The new instance endpoints.
     * 
     * @static
     * 
     * @return array
     */
    private static function _calculateCurrentRoleInstanceEndpointsChanges(
        $role, $currentInstanceEndpoints, $newInstanceEndpoints
    ) {
        $changedRoleSet = array();
        
        foreach ($newInstanceEndpoints as $endpointKey => $newEndpoint) {
            if (!array_key_exists(
                $endpointKey,
                $currentInstanceEndpoints
            )
            ) {
                $changedRoleSet[] = $role;
            }
        }

        return $changedRoleSet;
    }
    
    /**
     * Returns a RoleInstance object that represents the role instance
     * in which this code is currently executing.
     * 
     * @static
     * 
     * @return RoleInstance
     */
    public static function getCurrentRoleInstance()
    {
        self::_initialize();

        return self::$_currentEnvironmentData->getCurrentInstance();
    }

    /**
     * Returns the deployment ID that uniquely identifies the deployment in
     * which this role instance is running.
     * 
     * @static
     * 
     * @return string
     */
    public static function getDeploymentId()
    {
        self::_initialize();

        return self::$_currentEnvironmentData->getDeploymentId();
    }

    /**
     * Indicates whether the role instance is running in the Windows Azure
     * environment.
     * 
     * @static
     * 
     * @return boolean
     */
    public static function isAvailable()
    {
        try {
            self::_initialize();
        } catch (RoleEnvironmentNotAvailableException $ex) {
            return false;
        } catch (ChannelNotAvailableException $ex) {
            return false;
        }

        return self::$_runtimeClient != null;
    }

    /**
     * Indicates whether the role instance is running in the development fabric.
     * 
     * @static
     * 
     * @return boolean
     */
    public static function isEmulated()
    {
        self::_initialize();

        return self::$_currentEnvironmentData->isEmulated();
    }

    /**
     * Returns the set of Role objects defined for your service.
     * 
     * Roles are defined in the service definition file.
     * 
     * @static
     * 
     * @return array
     */
    public static function getRoles()
    {
        self::_initialize();

        return self::$_currentEnvironmentData->getRoles();
    }

    /**
     * Retrieves the settings in the service configuration file.
     * 
     * A role's configuration settings are defined in the service definition 
     * file. Values for configuration settings are set in the service
     * configuration file.
     * 
     * @static
     * 
     * @return array
     */
    public static function getConfigurationSettings()
    {
        self::_initialize();

        return self::$_currentEnvironmentData->getConfigurationSettings();
    }

    /**
     * Retrieves the set of named local storage resources.
     * 
     * @static
     * 
     * @return array
     */
    public static function getLocalResources()
    {
        self::_initialize();

        return self::$_currentEnvironmentData->getLocalResources();
    }

    /**
     * Requests that the current role instance be stopped and restarted.
     * Before the role instance is recycled, the Windows Azure load balancer 
     * takes the role instance out of rotation.
     * 
     * This ensures that no new requests are routed to the instance while it 
     * is restarting.
     * 
     * @static
     * 
     * @return none
     */
    public static function requestRecycle()
    {
        self::_initialize();

        $recycleState = new AcquireCurrentState(
            self::$_clientId,
            self::$_currentGoalState->getIncarnation(),
            CurrentStatus::RECYCLE,
            self::$_maxDateTime
        );

        self::$_runtimeClient->setCurrentState($recycleState);
    }

    /**
     * Sets the status of the role instance.
     * 
     * An instance may indicate that it is in one of two states: Ready or Busy. 
     * If an instance's state is Ready, it is prepared to receive requests from 
     * the load balancer. If the instance's state is Busy, it will not receive
     * requests from the load balancer.
     * 
     * @param RoleInstanceStatus $status        The new role status.
     * @param \DateTime          $expirationUtc The expiration UTC time.
     * 
     * @static
     * 
     * @return none
     */
    public static function setStatus($status, $expirationUtc)
    {
        self::_initialize();

        $currentStatus = CurrentStatus::STARTED;

        switch ($status) {
        case RoleInstanceStatus::BUSY:
            $currentStatus = CurrentStatus::BUSY;
            break;
        case RoleInstanceStatus::READY:
            $currentStatus = CurrentStatus::STARTED;
            break;
        }

        $newState = new AcquireCurrentState(
            self::$_clientId,
            self::$_currentGoalState->getIncarnation(),
            $currentStatus,
            $expirationUtc
        );

        self::$_lastState = $newState;

        self::$_runtimeClient->setCurrentState($newState);
    }

    /**
     * Clears the status of the role instance.
     * 
     * An instance may indicate that it has completed communicating status by 
     * calling this method.
     * 
     * @static
     * 
     * @return none
     */
    public static function clearStatus()
    {
        self::_initialize();

        $newState = new ReleaseCurrentState(self::$_clientId);

        self::$_lastState = $newState;

        self::$_runtimeClient->setCurrentState($newState);
    }

    /**
     * Adds an event listener for the changed event, which occurs
     * after a configuration change has been applied to a role instance.
     * 
     * To listen for events, one should call trackChanges.
     * 
     * @param function $listener The changed listener.
     * 
     * @return none
     */
    public static function addRoleEnvironmentChangedListener($listener)
    {
        self::$_changedListeners[] = $listener;
    }

    /**
     * Removes an event listener for the Changed event.
     * 
     * @param function $listener The changed listener.
     * 
     * @static
     * 
     * @return bool
     */
    public static function removeRoleEnvironmentChangedListener($listener)
    {
        foreach (self::$_changedListeners as $key => $changedListener) {
            if ($changedListener == $listener) {
                unset(self::$_changedListeners[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Adds an event listener for the Changing event, which occurs
     * before a change to the service configuration is applied to the running
     * instances of the role.
     * 
     * Service configuration changes are applied on-the-fly to running role 
     * instances. Configuration changes include changes to the service 
     * configuration changes and changes to the number of instances in the 
     * service.
     * 
     * This event occurs after the new configuration file has been submitted to 
     * Windows Azure but before the changes have been applied to each running 
     * role instance. This event can be cancelled for a given instance to 
     * prevent the configuration change.
     * 
     * Note that cancelling this event causes the instance to be automatically 
     * recycled. When the instance is recycled, the configuration change is 
     * applied when it restarts.
     * 
     * To listen for events, one should call trackChanges.
     * 
     * @param function $listener The changing listener.
     * 
     * @static
     * 
     * @return none
     */
    public static function addRoleEnvironmentChangingListener($listener)
    {
        self::$_changingListeners[] = $listener;
    }

    /** 
     * Removes an event listener for the Changing event.
     * 
     * @param function $listener The changing listener.
     * 
     * @static
     * 
     * @return bool
     */
    public static function removeRoleEnvironmentChangingListener($listener)
    {
        foreach (self::$_changingListeners as $key => $changingListener) {
            if ($changingListener == $listener) {
                unset(self::$_changingListeners[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Adds an event listener for the Stopping event, which occurs
     * when the role is stopping.
     * To listen for events, one should call trackChanges.
     * 
     * @param function $listener The stopping listener.
     * 
     * @static
     * 
     * @return none
     */
    public static function addRoleEnvironmentStoppingListener($listener)
    {
        self::$_stoppingListeners[] = $listener;
    }

    /**
     * Removes an event listener for the Stopping event.
     * 
     * @param function $listener The stopping listener.
     * 
     * @static
     * 
     * @return bool
     */
    public static function removeRoleEnvironmentStoppingListener($listener)
    {
        foreach (self::$_stoppingListeners as $key => $stoppingListener) {
            if ($stoppingListener == $listener) {
                unset(self::$_stoppingListeners[$key]);
                return true;
            }
        }
        
        return false;
    }
}

// Initialize static fields
RoleEnvironment::init();

