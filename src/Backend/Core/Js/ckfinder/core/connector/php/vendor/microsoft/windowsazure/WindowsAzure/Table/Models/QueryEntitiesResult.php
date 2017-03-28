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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;

/**
 * Holds results of calling queryEntities API
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class QueryEntitiesResult
{
    /**
     * @var Query
     */
    private $_nextRowKey;
    
    /**
     * @var string
     */
    private $_nextPartitionKey;
    
    /**
     * @var array
     */
    private $_entities;
    
    /**
     * Creates new QueryEntitiesResult instance.
     * 
     * @param array $headers  The HTTP response headers.
     * @param array $entities The entities.
     * 
     * @return QueryEntitiesResult
     */
    public static function create($headers, $entities)
    {
        $result  = new QueryEntitiesResult();
        $headers = array_change_key_case($headers);
        $nextPK  = Utilities::tryGetValue(
            $headers, Resources::X_MS_CONTINUATION_NEXTPARTITIONKEY
        );
        $nextRK  = Utilities::tryGetValue(
            $headers, Resources::X_MS_CONTINUATION_NEXTROWKEY
        );
        
        $result->setEntities($entities);
        $result->setNextPartitionKey($nextPK);
        $result->setNextRowKey($nextRK);
        
        return $result;
    }
    
    /**
     * Gets entities.
     * 
     * @return array
     */
    public function getEntities()
    {
        return $this->_entities;
    }
    
    /**
     * Sets entities.
     * 
     * @param array $entities The entities array.
     * 
     * @return none
     */
    public function setEntities($entities)
    {
        $this->_entities = $entities;
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
}


