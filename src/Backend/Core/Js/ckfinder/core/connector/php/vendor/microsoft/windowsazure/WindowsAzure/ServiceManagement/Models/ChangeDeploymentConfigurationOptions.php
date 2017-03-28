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
 * The optional parameters for changeDeploymentConfiguration API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ChangeDeploymentConfigurationOptions extends GetDeploymentOptions
{
    /**
     * Indicates whether to treat package validation warnings as errors.
     * 
     * @var boolean
     */
    private $_treatWarningsAsErrors;
    
    /**
     * If not specified the default value is Auto. If set to Manual, 
     * WalkUpgradeDomain must be called to apply the update. If set to Auto, the 
     * Windows Azure platform will automatically apply the update To each upgrade 
     * domain for the service.
     * 
     * @var string
     */
    private $_mode;
    
    /**
     * Constructs new ChangeDeploymentConfigurationOptions instance.
     */
    public function __construct()
    {
        $this->_treatWarningsAsErrors = false;
    }
    
    /**
     * Gets treat warnings as errors flag.
     * 
     * If not specified the default value is false. If set to true, the update will 
     * be blocked when warnings are encountered.
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
    
    /**
     * Gets change mode.
     * 
     * If not specified the default value is Auto. If set to Manual, 
     * WalkUpgradeDomain must be called to apply the update. If set to Auto, the 
     * Windows Azure platform will automatically apply the update To each upgrade 
     * domain for the service.
     * 
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }
    
    /**
     * Sets mode.
     * 
     * @param string $mode The change mode.
     * 
     * @return none
     */
    public function setMode($mode)
    {
        Validate::isString($mode, 'mode');
        Validate::isTrue(Mode::isValid($mode), Resources::INVALID_CHANGE_MODE_MSG);
        
        $this->_mode = $mode;
    }
}