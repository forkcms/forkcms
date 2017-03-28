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
 * The runtime kernel.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RuntimeKernel
{
    /**
     * The singleton instance of the runtime kernel.
     * 
     * @var type 
     */
    private static $_theKernel;
    
    /**
     * The current state serializer.
     * 
     * @var type 
     */
    private $_currentStateSerializer;
    
    /**
     * The goal state deserializer.
     * 
     * @var type 
     */
    private $_goalStateDeserializer;
    
    /**
     * The input channel.
     * 
     * @var IInputChannel 
     */
    private $_inputChannel;
    
    /**
     * The output channel.
     * 
     * @var IOutputChannel 
     */
    private $_outputChannel;
    
    /**
     * The runtime current state client.
     * 
     * @var Protocol1RuntimeCurrentStateClient 
     */
    private $_protocol1RuntimeCurrentStateClient;
    
    /**
     * The role environment data deserializer.
     * 
     * @var IRoleEnvironmentDataDeserializer 
     */
    private $_roleEnvironmentDataDeserializer;
    
    /**
     * The runtime goal state client.
     * 
     * @var Protocol1RuntimeGoalStateClient 
     */
    private $_protocol1RuntimeGoalStateClient;
    
    /**
     * The runtime version protocol client.
     * 
     * @var RuntimeVersionProtocolClient 
     */
    private $_runtimeVersionProtocolClient;
    
    /**
     * The runtime version manager.
     * 
     * @var RuntimeVersionManager 
     */
    private $_runtimeVersionManager;

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->_currentStateSerializer = new XmlCurrentStateSerializer();
        $this->_goalStateDeserializer  = new ChunkedGoalStateDeserializer();
        $this->_inputChannel           = new FileInputChannel();
        $this->_outputChannel          = new FileOutputChannel();
        
        $this->_protocol1RuntimeCurrentStateClient = new
            Protocol1RuntimeCurrentStateClient(
                $this->_currentStateSerializer,
                $this->_outputChannel
            );
        
        $this->_roleEnvironmentDataDeserializer = new 
            XmlRoleEnvironmentDataDeserializer();
        
        $this->_protocol1RuntimeGoalStateClient = new
            Protocol1RuntimeGoalStateClient(
                $this->_protocol1RuntimeCurrentStateClient,
                $this->_goalStateDeserializer,
                $this->_roleEnvironmentDataDeserializer,
                $this->_inputChannel
            );
        
        $this->_runtimeVersionProtocolClient = new RuntimeVersionProtocolClient(
            $this->_inputChannel
        );
        
        $this->_runtimeVersionManager = new RuntimeVersionManager(
            $this->_runtimeVersionProtocolClient
        );
    }
    
    /**
     * Gets the current kernel instance.
     * 
     * @param boolean $forceNewInstance Boolean value indicating if a new instance
     * should be obtained even if a previous one exists.
     * 
     * @return RuntimeKernel
     */
    public static function getKernel($forceNewInstance = false)
    {
        if (is_null(self::$_theKernel) || $forceNewInstance) {
            self::$_theKernel = new RuntimeKernel();
        }
        
        return self::$_theKernel;
    }
    
    /**
     * Gets the current state serializer.
     * 
     * @return ICurrentStateSerializer
     */
    public function getCurrentStateSerializer()
    {
        return $this->_currentStateSerializer;
    }
    
    /**
     * Gets the goal state deserializer.
     * 
     * @return IGoalStateDeserializer
     */
    public function getGoalStateDeserializer()
    {
        return $this->_goalStateDeserializer;
    }
    
    /**
     * Gets the input channel.
     *
     * @return IInputChannel
     */
    public function getInputChannel()
    {
        return $this->_inputChannel;
    }
    
    /**
     * Gets the output channel.
     * 
     * @return IOutputChannel
     */
    public function getOutputChannel()
    {
        return $this->_outputChannel;
    }
    
    /**
     * Gets the runtime current state client.
     * 
     * @return Protocol1RuntimeCurrentStateClient
     */
    public function getProtocol1RuntimeCurrentStateClient()
    {
        return $this->_protocol1RuntimeCurrentStateClient;
    }
    
    /**
     * Gets the role environment data deserializer.
     * 
     * @return IRoleEnvironmentDataDeserializer
     */
    public function getRoleEnvironmentDataDeserializer()
    {
        return $this->_roleEnvironmentDataDeserializer;
    }
    
    /**
     * Gets the runtime goal state client.
     * 
     * @return Protocol1RuntimeGoalStateClient
     */
    public function getProtocol1RuntimeGoalStateClient()
    {
        return $this->_protocol1RuntimeGoalStateClient;
    }
    
    /**
     * Gets the runtime version protocol client.
     * 
     * @return RuntimeVersionProtocolClient
     */
    public function getRuntimeVersionProtocolClient()
    {
        return $this->_runtimeVersionProtocolClient;
    }
    
    /**
     * Gets the runtime version manager.
     * 
     * @return RuntimeVersionManager
     */
    public function getRuntimeVersionManager()
    {
        return $this->_runtimeVersionManager;
    }
}

