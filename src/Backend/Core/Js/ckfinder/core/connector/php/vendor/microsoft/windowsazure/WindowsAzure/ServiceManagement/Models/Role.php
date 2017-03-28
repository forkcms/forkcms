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
 * Represents a Windows Azure deployment role.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Role
{
    /**
     * @var string
     */
    private $_roleName;
    
    /**
     * @var string
     */
    private $_osVersion;
    
    /**
     * Creates a new Role from parsed response body.
     * 
     * @param array $parsed The parsed response body in array representation.
     * 
     * @return Role
     */
    public static function create($parsed)
    {
        $role      = new Role();
        $roleName  = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_ROLE_NAME
        );
        $osVersion = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_OS_VERSION
        );
        
        $role->setOsVersion($osVersion);
        $role->setRoleName($roleName);
        
        return $role;
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
     * Gets the role OS version.
     * 
     * The version of the Windows Azure Guest Operating System on which this role's
     * instances are running.
     * 
     * @return string
     */
    public function getOsVersion()
    {
        return $this->_osVersion;
    }
    
    /**
     * Sets the role OS version.
     * 
     * @param string $osVersion The role OS version.
     * 
     * @return none
     */
    public function setOsVersion($osVersion)
    {
        $this->_osVersion = $osVersion;
    }
}