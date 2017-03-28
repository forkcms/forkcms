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
 * An implementation for the protocol runtime goal state client.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Protocol1RuntimeGoalStateClient implements IRuntimeGoalStateClient
{
    /**
     * @var Protocol1RuntimeCurrentStateClient
     */
    private $_currentStateClient;

    /**
     * @var IGoalStateDeserializer
     */
    private $_goalStateDeserializer;

    /**
     * @var IGoalStateDeserializer
     */
    private $_roleEnvironmentDeserializer;

    /**
     * @var IInputChannel
     */
    private $_inputChannel;

    /**
     * @var string
     */
    private $_endpoint;

    /**
     * @var CurrentGoalState
     */
    private $_currentGoalState;

    /**
     * @var RoleEnvironmentData
     */
    private $_currentEnvironmentData;

    /**
     * @var bool
     */
    private $_keepOpen;

    /**
     * Constructor
     * 
     * @param Protocol1RuntimeCurrentStateClient $currentStateClient          The
     *      current state client.
     * @param IGoalStateDeserializer             $goalStateDeserializer       The
     *      goal state deserializer.
     * @param IRoleEnvironmentDeserializer       $roleEnvironmentDeserializer The
     *      role environment deserializer.
     * @param IInputChannel                      $inputChannel                The
     *      input channel.
     */
    public function __construct($currentStateClient, $goalStateDeserializer,
        $roleEnvironmentDeserializer, $inputChannel
    ) {
        $this->_currentStateClient          = $currentStateClient;
        $this->_goalStateDeserializer       = $goalStateDeserializer;
        $this->_roleEnvironmentDeserializer = $roleEnvironmentDeserializer;
        $this->_inputChannel                = $inputChannel;

        $this->_listeners = array();

        $this->_currentGoalState       = null;
        $this->_currentEnvironmentData = null;
        $this->_keepOpen               = false;
    }

    /**
     * Gets the current goal state.
     * 
     * @return GoalState
     */
    public function getCurrentGoalState()
    {
        $this->_ensureGoalStateRetrieved();

        return $this->_currentGoalState;
    }

    /**
     * Gets the role environment data.
     * 
     * @return RoleEnvironmentData
     */
    public function getRoleEnvironmentData()
    {
        $this->_ensureGoalStateRetrieved();

        if (is_null($this->_currentEnvironmentData)) {
            $current = $this->_currentGoalState;

            if (is_null($current->getEnvironmentPath())) {
                throw new RoleEnvironmentNotAvailableException(
                    'No role environment data for the current goal state'
                );
            }

            $environmentStream = $this->_inputChannel->getInputStream(
                $current->getEnvironmentPath()
            );

            $this->_currentEnvironmentData = $this->_roleEnvironmentDeserializer
                ->deserialize($environmentStream);
        }

        return $this->_currentEnvironmentData;
    }

    /**
     * Sets the endpoint.
     *
     * @param string $endpoint Sets the endpoint.
     * 
     * @return none
     */
    public function setEndpoint($endpoint)
    {
        $this->_endpoint = $endpoint;
    }

    /**
     * Gets the endpoint.
     * 
     * @return string
     */
    public function getEndpoint()
    {
        return $this->_endpoint;
    }

    /**
     * Sets the keep open state.
     *
     * @param string $keepOpen Sets the keep open state.
     * 
     * @return none
     */
    public function setKeepOpen($keepOpen)
    {
        $this->_keepOpen = $keepOpen;
    }

    /**
     * Gets the keep open state.
     * 
     * @return bool
     */
    public function getKeepOpen()
    {
        return $this->_keepOpen;
    }

    /**
     * Ensures that the goal state is retrieved.
     * 
     * @return none
     */
    private function _ensureGoalStateRetrieved()
    {
        if (is_null($this->_currentGoalState) || !$this->_keepOpen) {
            $inputStream = $this->_inputChannel->getInputStream($this->_endpoint);
            $this->_goalStateDeserializer->initialize($inputStream);
        }

        $goalState = $this->_goalStateDeserializer->deserialize();
        if (is_null($goalState)) {
            return;
        }

        $this->_currentGoalState = $goalState;

        if (!is_null($goalState->getEnvironmentPath())) {
            $this->_currentEnvironmentData = null;
        }

        $this->_currentStateClient->setEndpoint(
            $this->_currentGoalState->getCurrentStateEndpoint()
        );

        if (!$this->_keepOpen) {
            $this->_inputChannel->closeInputStream();
        }
    }
}


