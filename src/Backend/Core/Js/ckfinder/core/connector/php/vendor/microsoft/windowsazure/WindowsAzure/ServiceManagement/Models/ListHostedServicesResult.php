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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;

/**
 * The result of calling listHostedServices API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ListHostedServicesResult
{
    /**
     * @var array
     */
    private $_hostedServices;
    
    /**
     * Creates new ListHostedServicesResult from parsed response body.
     * 
     * @param array $parsed The parsed response body.
     * 
     * @return ListHostedServicesResult
     */
    public static function create($parsed)
    {
        $result                  = new ListHostedServicesResult();
        $result->_hostedServices = array();
        $rowHostedServices       = Utilities::tryGetArray(
            Resources::XTAG_HOSTED_SERVICE,
            $parsed
        );
        
        foreach ($rowHostedServices as $rowHostedService) {
            $properties                = Utilities::tryGetArray(
                Resources::XTAG_HOSTED_SERVICE_PROPERTIES,
                $rowHostedService
            );
            $hostedService             = new HostedService(
                $rowHostedService,
                $properties
            );
            $result->_hostedServices[] = $hostedService;
        }
        
        return $result;
    }
    
    /**
     * Gets hosted services.
     * 
     * @return array
     */
    public function getHostedServices()
    {
        return $this->_hostedServices;
    }
    
    /**
     * Sets hosted services.
     * 
     * @param array $hostedServices The hosted services.
     * 
     * @return none
     */
    public function setHostedServices($hostedServices)
    {
        $this->_hostedServices = $hostedServices;
    }
}