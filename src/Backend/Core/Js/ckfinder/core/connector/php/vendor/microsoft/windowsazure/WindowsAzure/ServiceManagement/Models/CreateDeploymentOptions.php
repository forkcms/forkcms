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

/**
 * The optional parameters for createDeployment API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CreateDeploymentOptions
{
    /**
     * Indicates whether to start the deployment immediately after it is created.
     * 
     * @var boolean
     */
    private $_startDeployment;
    
    /**
     * Indicates whether to treat package validation warnings as errors.
     * 
     * @var boolean
     */
    private $_treatWarningsAsErrors;
    
    /**
     * Constructs new CreateDeploymentOptions instance.
     */
    public function __construct()
    {
        $this->_startDeployment       = false;
        $this->_treatWarningsAsErrors = false;
    }
    
    /**
     * Gets start deployment flag.
     * 
     * @return boolean
     */
    public function getStartDeployment()
    {
        return $this->_startDeployment;
    }
    
    /**
     * Sets start deployment flag.
     * 
     * @param boolean $startDeployment Indicates whether to start the deployment 
     * immediately after it is created.
     * 
     * @return none
     */
    public function setStartDeployment($startDeployment)
    {
        Validate::isBoolean($startDeployment, 'startDeployment');
        
        $this->_startDeployment = $startDeployment;
    }
    
    /**
     * Gets treat warnings as errors flag.
     * 
     * @return boolean
     */
    public function getTreatWarningsAsErrors()
    {
        return $this->_treatWarningsAsErrors;
    }
    
    /**
     * Sets treat warnings as errors flag.
     * 
     * @param boolean $treatWarningsAsErrors Indicates whether to treat package 
     * validation warnings as errors.
     * 
     * @return none
     */
    public function setTreatWarningsAsErrors($treatWarningsAsErrors)
    {
        Validate::isBoolean($treatWarningsAsErrors, 'treatWarningsAsErrors');
        
        $this->_treatWarningsAsErrors = $treatWarningsAsErrors;
    }
}