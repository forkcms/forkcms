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
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Represents one batch operation
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchOperation
{
    /**
     * @var string
     */
    private $_type;
    
    /**
     * @var array
     */
    private $_params;
    
    /**
     * Sets operation type.
     * 
     * @param string $type The operation type. Must be valid type.
     * 
     * @return none
     */
    public function setType($type)
    {
        Validate::isTrue(
            BatchOperationType::isValid($type),
            Resources::INVALID_BO_TYPE_MSG
        );
        
        $this->_type = $type;
    }
    
    /**
     * Gets operation type.
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Adds or sets parameter for the operation.
     * 
     * @param string $name  The param name. Must be valid name.
     * @param mix    $value The param value.
     * 
     * @return none
     */
    public function addParameter($name, $value)
    {
        Validate::isTrue(
            BatchOperationParameterName::isValid($name),
            Resources::INVALID_BO_PN_MSG
        );
        $this->_params[$name] = $value;
    }
    
    /**
     * Gets parameter value and if the name doesn't exist, return null.
     * 
     * @param string $name The parameter name.
     * 
     * @return mix
     */
    public function getParameter($name)
    {
        return Utilities::tryGetValue($this->_params, $name);
    }
}


