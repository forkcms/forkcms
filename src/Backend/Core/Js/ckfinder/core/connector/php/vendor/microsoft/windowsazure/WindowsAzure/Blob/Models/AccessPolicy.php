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
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Blob\Models;

use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;

/**
 * Holds container access policy elements
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class AccessPolicy
{
    /**
     * @var string
     */
    private $_start;
    
    /**
     * @var \DateTime
     */
    private $_expiry;
    
    /**
     * @var \DateTime
     */
    private $_permission;
    
    /**
     * Gets start.
     *
     * @return \DateTime.
     */
    public function getStart()
    {
        return $this->_start;
    }

    /**
     * Sets start.
     *
     * @param \DateTime $start value.
     * 
     * @return none.
     */
    public function setStart($start)
    {
        Validate::isDate($start);
        $this->_start = $start;
    }
    
    /**
     * Gets expiry.
     *
     * @return \DateTime.
     */
    public function getExpiry()
    {
        return $this->_expiry;
    }

    /**
     * Sets expiry.
     *
     * @param \DateTime $expiry value.
     * 
     * @return none.
     */
    public function setExpiry($expiry)
    {
        Validate::isDate($expiry);
        $this->_expiry = $expiry;
    }
    
    /**
     * Gets permission.
     *
     * @return string.
     */
    public function getPermission()
    {
        return $this->_permission;
    }

    /**
     * Sets permission.
     *
     * @param string $permission value.
     * 
     * @return none.
     */
    public function setPermission($permission)
    {
        $this->_permission = $permission;
    }
    
    /**
     * Converts this current object to XML representation.
     * 
     * @return array.
     */
    public function toArray()
    {
        $array = array();
        
        $array['Start']      = Utilities::convertToEdmDateTime($this->_start);
        $array['Expiry']     = Utilities::convertToEdmDateTime($this->_expiry);
        $array['Permission'] = $this->_permission;
        
        return $array;
    }
}


