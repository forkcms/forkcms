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
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;

/**
 * The parameters to get a deployment.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetDeploymentOptions
{
    /**
     * @var string
     */
    private $_slot;
    
    /**
     * @var string
     */
    private $_deploymentName;
    
    
    /**
     * Gets the deployment slot.
     * 
     * @return string
     */
    public function getSlot()
    {
        return $this->_slot;
    }
    
    /**
     * Sets the deployment slot.
     * 
     * @param string $slot The deployment slot name.
     * 
     * @return none
     */
    public function setSlot($slot)
    {
        Validate::isString($slot, 'slot');
        Validate::notNullOrEmpty($slot, 'slot');
        Validate::isTrue(
            DeploymentSlot::isValid($slot),
            sprintf(Resources::INVALID_SLOT, $slot)
        );
                
        $this->_slot = $slot;
    }
    
    /**
     * Gets the deployment name.
     * 
     * @return string
     */
    public function getDeploymentName()
    {
        return $this->_deploymentName;
    }
    
    /**
     * Sets the deployment name.
     * 
     * @param string $deploymentName The deployment name.
     * 
     * @return none
     */
    public function setDeploymentName($deploymentName)
    {
        Validate::isString($deploymentName, 'deploymentName');
        Validate::notNullOrEmpty($deploymentName, 'deploymentName');
                
        $this->_deploymentName = $deploymentName;
    }
}