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
 * The role instance data.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
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
    private $_id;
    
    /**
     * @var integer
     */
    private $_faultDomain;

    /**
     * @var integer
     */
    private $_updateDomain;
    
    /**
     * @var array
     */
    private $_endpoints;
    
    /**
     * @var Role
     */
    private $_role;
   
    /**
     * Constructor
     * 
     * @param string  $id           The identifier.
     * @param integer $faultDomain  The fault domain.
     * @param integer $updateDomain The update domain.
     * @param array   $endpoints    The endpoints.
     */
    public function __construct($id, $faultDomain, $updateDomain, $endpoints)
    {
        $this->_id           = $id;
        $this->_faultDomain  = $faultDomain;
        $this->_updateDomain = $updateDomain;
        $this->_endpoints    = $endpoints;
    }
    
    /**
     * Returns the ID of this instance.
     * 
     * The returned ID is unique to the application domain of the role's 
     * instance. If an instance is terminated and has been configured to 
     * restart automatically, the restarted instance will have the same ID 
     * as the terminated instance.
     * 
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Returns an integer value that indicates the fault domain in which this
     * instance resides.
     * 
     * @return integer
     */
    public function getFaultDomain()
    {
        return $this->_faultDomain;
    }
    
    /**
     * Returns an integer value that indicates the update domain in which this
     * instance resides.
     * 
     * @return integer
     */
    public function getUpdateDomain()
    {
        return $this->_updateDomain;
    }
    
    /**
     * Returns the Role object associated with this instance.
     * 
     * @return Role
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Sets the Role object associated with this instance.
     * 
     * @param Role $role The role object.
     * 
     * @return Role
     */
    public function setRole($role)
    {
        $this->_role = $role;
    }
    
    /**
     * Returns the set of endpoints associated with this role instance.
     * 
     * @return array
     */
    public function getInstanceEndpoints()
    {
        return $this->_endpoints;
    }
}

