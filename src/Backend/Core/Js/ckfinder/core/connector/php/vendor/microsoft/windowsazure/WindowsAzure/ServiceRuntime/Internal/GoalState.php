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
use WindowsAzure\Common\Internal\Validate;

/**
 * The goal state representation.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GoalState
{
    /**
     * @var string
     */
    private $_incarnation;
    
    /**
     * @var string
     */
    private $_expectedState;
    
    /**
     * @var string
     */
    private $_environmentPath;
    
    /**
     * @var string
     */
    private $_deadline;
    
    /**
     * @var string
     */
    private $_currentStateEndpoint;
    
    /**
     * Constructor
     * 
     * @param string    $incarnation          The incarnation.
     * @param string    $expectedState        The expected state.
     * @param string    $environmentPath      The environment path.
     * @param \DateTime $deadline             The deadline.
     * @param string    $currentStateEndpoint The current state endpoint.
     */
    public function __construct ($incarnation, $expectedState, $environmentPath, 
        $deadline, $currentStateEndpoint
    ) {
        Validate::isDate($deadline);
        
        $this->_incarnation          = $incarnation;
        $this->_expectedState        = $expectedState;
        $this->_environmentPath      = $environmentPath;
        $this->_deadline             = $deadline;
        $this->_currentStateEndpoint = $currentStateEndpoint;
    }
    
    /**
     * Gets the incarnation.
     * 
     * @return string
     */
    public function getIncarnation()
    {
        return $this->_incarnation;
    }
    
    /**
     * Gets the expected state.
     * 
     * @return string
     */
    public function getExpectedState()
    {
        return $this->_expectedState;
    }
    
    /**
     * Gets the environment path.
     * 
     * @return string
     */
    public function getEnvironmentPath()
    {
        return $this->_environmentPath;
    }
    
    /**
     * Gets the deadline.
     * 
     * @return string
     */
    public function getDeadline()
    {
        return $this->_deadline;
    }
    
    /**
     * Gets the current state endpoint.
     * 
     * @return string
     */
    public function getCurrentStateEndpoint()
    {
        return $this->_currentStateEndpoint;
    }
}

