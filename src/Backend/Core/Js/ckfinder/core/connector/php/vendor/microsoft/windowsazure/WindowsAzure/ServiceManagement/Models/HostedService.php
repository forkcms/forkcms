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
use WindowsAzure\ServiceManagement\Internal\WindowsAzureService;

/**
 * The hosted service class.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class HostedService extends WindowsAzureService
{
    /**
     * @var array 
     */
    private $_deployments;
    
    /**
     * Constructs new hosted service object.
     */
    public function __construct()
    {
        $sources = func_get_args();
        parent::__construct($sources);
        
        $this->_deployments = array();
        foreach ($sources as $source) {
            $deployments = Utilities::tryGetKeysChainValue(
                $source,
                Resources::XTAG_DEPLOYMENTS,
                Resources::XTAG_DEPLOYMENT
            );
            
            if (!empty($deployments)) {
                $this->_deployments = Utilities::createInstanceList(
                    Utilities::getArray($deployments),
                    'WindowsAzure\ServiceManagement\Models\Deployment'
                );
            }
        }
    }
    
    /**
     * Converts the current object into ordered array representation.
     * 
     * @return array
     */
    protected function toArray()
    {
        $arr     = parent::toArray();
        $order   = array(
            Resources::XTAG_NAMESPACE,
            Resources::XTAG_SERVICE_NAME,
            Resources::XTAG_LABEL,
            Resources::XTAG_DESCRIPTION,
            Resources::XTAG_LOCATION,
            Resources::XTAG_AFFINITY_GROUP
        );
        $ordered = Utilities::orderArray($arr, $order);
        
        return $ordered;
    }
    
    /**
     * Gets the deployments array.
     * 
     * @return array
     */
    public function getDeployments()
    {
        return $this->_deployments;
    }
    
    /**
     * Sets the deployments array.
     * 
     * @param array $deployments The deployments array.
     * 
     * @return none
     */
    public function setDeployments($deployments)
    {
        $this->_deployments = $deployments;
    }
}