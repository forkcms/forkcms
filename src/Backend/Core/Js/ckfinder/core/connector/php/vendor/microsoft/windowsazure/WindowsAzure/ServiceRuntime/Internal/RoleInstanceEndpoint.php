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
 * The role instance endpoint data.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RoleInstanceEndpoint
{
    /**
     * @var RoleInstance
     */
    private $_roleInstance;
    
    /**
     * @var string
     */
    private $_protocol;

    /**
     * @var string
     */
    private $_address;
    
    /**
     * @var string
     */
    private $_port;
    
    /**
     * Constructor
     * 
     * @param string $protocol The protocol.
     * @param string $address  The Address.
     * @param string $port     The Port.
     */
    public function __construct($protocol, $address, $port)
    {
        $this->_protocol = $protocol;
        $this->_address  = $address;
        $this->_port     = $port;
    }
    
    /**
     * Sets the role instance.
     * 
     * @param RoleInstance $roleInstance The role instance.
     * 
     * @return none
     */
    public function setRoleInstance($roleInstance)
    {
        $this->_roleInstance = $roleInstance;
    }
    
    /**
     * Returns the RoleInstance object associated with this endpoint.
     * 
     * @return RoleInstance
     */
    public function getRoleInstance()
    {
        return $this->_roleInstance;
    }
    
    /**
     * Returns the protocol associated with the endpoint.
     * 
     * @return string
     */
    public function getProtocol()
    {
        return $this->_protocol;
    }
    
    /**
     * Return the address.
     * 
     * @return string
     */
    public function getAddress()
    {
        return $this->_address;
    }
    
    /**
     * Return the port.
     * 
     * @return string
     */
    public function getPort()
    {
        return $this->_port;
    }
}

