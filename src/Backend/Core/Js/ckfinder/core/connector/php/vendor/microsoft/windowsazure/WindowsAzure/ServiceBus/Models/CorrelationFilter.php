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
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
namespace WindowsAzure\ServiceBus\Models;
use WindowsAzure\ServiceBus\Internal\Filter;

/**
 * The base class for rule filter.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class CorrelationFilter extends Filter
{
    /**
     * The ID of the correlation.
     *
     * @var string
     */
    private $_correlationId;

    /** 
     * Creates a correlation filter with default parameter. 
     */
    public function __construct()
    {
        parent::__construct();
        $this->attributes['xsi:type'] = 'CorrelationFilter';
    }

    /**
     * Gets the ID of the correlation. 
     *
     * @return string 
     */
    public function getCorrelationId()
    {
        return $this->_correlationId;
    }

    /**
     * Sets the ID of the correlation.
     * 
     * @param string $correlationId The ID of the correlation.
     * 
     * @return none
     */
    public function setCorrelationId($correlationId)
    {
        $this->_correlationId = $correlationId;
    }
}

