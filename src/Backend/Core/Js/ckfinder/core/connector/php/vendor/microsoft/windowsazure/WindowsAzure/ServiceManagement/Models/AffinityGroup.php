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
use WindowsAzure\ServiceManagement\Internal\Service;

/**
 * The affinity group class.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class AffinityGroup extends Service
{
    /**
     * Constructs new affinity group object.
     */
    public function __construct()
    {
        $sources = func_get_args();
        parent::__construct($sources);
        
        foreach ($sources as $source) {
            $this->setName(
                Utilities::tryGetValue(
                    $source,
                    Resources::XTAG_NAME,
                    $this->getName()
                )
            );
        }
    }
    
    /**
     * Converts the current object into ordered array representation.
     * 
     * @return array
     */
    protected function toArray()
    {
        $arr   = parent::toArray();
        $order = array(
            Resources::XTAG_NAMESPACE,
            Resources::XTAG_NAME,
            Resources::XTAG_LABEL,
            Resources::XTAG_DESCRIPTION,
            Resources::XTAG_LOCATION
        );
        Utilities::addIfNotEmpty(Resources::XTAG_NAME, $this->getName(), $arr);
        $ordered = Utilities::orderArray($arr, $order);
        
        return $ordered;
    }
}


