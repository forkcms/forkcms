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
 * Represents a Windows Azure deployment input endpoint.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class InputEndpoint
{
    /**
     * @var string
     */
    private $_roleName;
    
    /**
     * @var string
     */
    private $_vip;
    
    /**
     * @var string
     */
    private $_port;
    
    /**
     * Creates a new InputEndpoint from parsed response body.
     * 
     * @param array $parsed The parsed response body in array representation.
     * 
     * @return InputEndpoint
     */
    public static function create($parsed)
    {
        $inputEndpoint = new InputEndpoint();
        $vip           = Utilities::tryGetValue($parsed, Resources::XTAG_VIP);
        $port          = Utilities::tryGetValue($parsed, Resources::XTAG_PORT);
        $roleName      = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_ROLE_NAME
        );
        
        $inputEndpoint->setPort($port);
        $inputEndpoint->setRoleName($roleName);
        $inputEndpoint->setVip($vip);
        
        return $inputEndpoint;
    }
    
    /**
     * Gets the input endpoint role name.
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
     * Sets the input endpoint role name.
     * 
     * @param string $roleName The input endpoint role name.
     * 
     * @return none
     */
    public function setRoleName($roleName)
    {
        $this->_roleName = $roleName;
    }
    
    /**
     * Gets the input endpoint VIP.
     * 
     * The virtual IP address that this input endpoint is exposed on.
     * 
     * @return string
     */
    public function getVip()
    {
        return $this->_vip;
    }
    
    /**
     * Sets the input endpoint VIP.
     * 
     * @param string $vip The input endpoint VIP.
     * 
     * @return none
     */
    public function setVip($vip)
    {
        $this->_vip = $vip;
    }
    
    /**
     * Gets the input endpoint port.
     * 
     * The port this input endpoint is exposed on.
     * 
     * @return string
     */
    public function getPort()
    {
        return $this->_port;
    }
    
    /**
     * Sets the input endpoint port.
     * 
     * @param string $port The input endpoint port.
     * 
     * @return none
     */
    public function setPort($port)
    {
        $this->_port = $port;
    }
}