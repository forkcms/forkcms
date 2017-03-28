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
 * Holds optional parameters for queryEntities API
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class QueryEntitiesOptions extends TableServiceOptions
{
    /**
     * @var Query
     */
    private $_query;
    
    /**
     * @var string
     */
    private $_nextPartitionKey;
    
    /**
     * @var string
     */
    private $_nextRowKey;
    
    /**
     * Constructs new QueryEntitiesOptions object.
     */
    public function __construct()
    {
        $this->_query = new Query();
    }
    
    /**
     * Gets query.
     * 
     * @return Query
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * Sets query.
     * 
     * You can either sets the whole query *or* use the individual query functions
     * like (setTop).
     * 
     * @param string $query The query instance.
     * 
     * @return none
     */
    public function setQuery($query)
    {
        $this->_query = $query;
    }
    
    /**
     * Gets entity next partition key.
     *
     * @return string
     */
    public function getNextPartitionKey()
    {
        return $this->_nextPartitionKey;
    }

    /**
     * Sets entity next partition key.
     *
     * @param string $nextPartitionKey The entity next partition key value.
     *
     * @return none
     */
    public function setNextPartitionKey($nextPartitionKey)
    {
        $this->_nextPartitionKey = $nextPartitionKey;
    }
    
    /**
     * Gets entity next row key.
     *
     * @return string
     */
    public function getNextRowKey()
    {
        return $this->_nextRowKey;
    }

    /**
     * Sets entity next row key.
     *
     * @param string $nextRowKey The entity next row key value.
     *
     * @return none
     */
    public function setNextRowKey($nextRowKey)
    {
        $this->_nextRowKey = $nextRowKey;
    }
    
    /**
     * Gets filter.
     *
     * @return Filters\Filter
     */
    public function getFilter()
    {
        return $this->_query->getFilter();
    }

    /**
     * Sets filter.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     * 
     * @param Filters\Filter $filter value.
     * 
     * @return none.
     */
    public function setFilter($filter)
    {
        $this->_query->setFilter($filter);
    }
    
    /**
     * Gets top.
     *
     * @return integer.
     */
    public function getTop()
    {
        return $this->_query->getTop();
    }

    /**
     * Sets top.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     * 
     * @param integer $top value.
     * 
     * @return none.
     */
    public function setTop($top)
    {
        $this->_query->setTop($top);
    }
    
    /**
     * Adds a field to select fields.
     * 
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     * 
     * @param string $field The value of the field.
     * 
     * @return none.
     */
    public function addSelectField($field)
    {
        $this->_query->addSelectField($field);
    }
    
    /**
     * Gets selectFields.
     *
     * @return array.
     */
    public function getSelectFields()
    {
        return $this->_query->getSelectFields();
    }

    /**
     * Sets selectFields.
     *
     * You can either use this individual function or use setQuery to set the whole
     * query object.
     * 
     * @param array $selectFields value.
     * 
     * @return none.
     */
    public function setSelectFields($selectFields)
    {
        $this->_query->setSelectFields($selectFields);
    }
}


