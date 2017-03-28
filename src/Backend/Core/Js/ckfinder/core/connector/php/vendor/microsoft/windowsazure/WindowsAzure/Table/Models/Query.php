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

/**
 * Query to be performed on a table
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Query
{
    /**
     * @var array
     */
    private $_selectFields;
    
    /**
     * @var Filters\Filter
     */
    private $_filter;
    
    /**
     * @var integer
     */
    private $_top;
    
    /**
     * Gets filter.
     *
     * @return Filters\Filter
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * Sets filter.
     *
     * @param Filters\Filter $filter value.
     * 
     * @return none.
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
    }
    
    /**
     * Gets top.
     *
     * @return integer.
     */
    public function getTop()
    {
        return $this->_top;
    }

    /**
     * Sets top.
     *
     * @param integer $top value.
     * 
     * @return none.
     */
    public function setTop($top)
    {
        $this->_top = $top;
    }
    
    /**
     * Adds a field to select fields.
     * 
     * @param string $field The value of the field.
     * 
     * @return none.
     */
    public function addSelectField($field)
    {
        $this->_selectFields[] = $field;
    }
    
    /**
     * Gets selectFields.
     *
     * @return array.
     */
    public function getSelectFields()
    {
        return $this->_selectFields;
    }

    /**
     * Sets selectFields.
     *
     * @param array $selectFields value.
     * 
     * @return none.
     */
    public function setSelectFields($selectFields)
    {
        $this->_selectFields = $selectFields;
    }
}


