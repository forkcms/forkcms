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

/**
 * The result of calling getDeployment API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetDeploymentResult
{
    /**
     * @var Deployment
     */
    private $_deployment;
    
    /**
     * Creates a new GetDeploymentResult from parsed response body.
     * 
     * @param array $parsed The parsed response body in array representation.
     * 
     * @return GetDeploymentResult
     * 
     * @static
     */
    public static function create($parsed)
    {
        $result = new GetDeploymentResult();
        
        $result->setDeployment(Deployment::create($parsed));
        
        return $result;
    }
    
    /**
     * Gets the deployment instance.
     * 
     * @return Deployment
     */
    public function getDeployment()
    {
        return $this->_deployment;
    }
    
    /**
     * Sets the deployment.
     * 
     * @param Deployment $deployment The deployment instance.
     * 
     * @return none
     */
    public function setDeployment($deployment)
    {
        $this->_deployment = $deployment;
    }
}