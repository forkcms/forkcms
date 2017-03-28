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
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Models;
use WindowsAzure\Table\Models\EdmType;
use WindowsAzure\Common\Internal\Validate;

/**
 * Represents entity property.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Property
{
    /**
     * @var string
     */
    private $_edmType;
    
    /**
     * @var mix
     */
    private $_value;
    
    /**
     * Gets the type of the property.
     * 
     * @return string
     */
    public function getEdmType()
    {
        return $this->_edmType;
    }
    
    /**
     * Sets the value of the property.
     * 
     * @param string $edmType The property type.
     * 
     * @return none
     */
    public function setEdmType($edmType)
    {
        EdmType::isValid($edmType);
        $this->_edmType = $edmType;
    }
    
    /**
     * Gets the value of the property.
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Sets the property value.
     * 
     * @param mix $value The value of property.
     * 
     * @return none
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }
}


