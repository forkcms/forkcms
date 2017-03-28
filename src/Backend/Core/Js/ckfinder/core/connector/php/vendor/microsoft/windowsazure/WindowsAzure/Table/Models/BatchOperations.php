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
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;

/**
 * Holds batch operation change set.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchOperations
{
    /**
     * @var array
     */
    private $_operations;

    /**
     * Default constructor. 
     */
    public function __construct()
    {
        $this->_operations = array();
    }
    
    /**
     * Gets the batch operations.
     * 
     * @return array
     */
    public function getOperations()
    {
        return $this->_operations;
    }
    
    /**
     * Sets the batch operations.
     * 
     * @param array $operations The batch operations.
     * 
     * @return none
     */
    public function setOperations($operations)
    {
        $this->_operations = array();
        foreach ($operations as $operation) {
            $this->addOperation($operation);
        }
    }
    
    /**
     * Adds operation to the batch operations.
     * 
     * @param mix $operation The operation to add.
     * 
     * @return none
     */
    public function addOperation($operation)
    {
        Validate::isTrue(
            $operation instanceof BatchOperation,
            Resources::INVALID_BO_TYPE_MSG
        );
        
        $this->_operations[] = $operation;
    }
    
    /**
     * Adds insertEntity operation.
     * 
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     * 
     * @return none
     */
    public function addInsertEntity($table, $entity)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        
        $operation = new BatchOperation();
        $type      = BatchOperationType::INSERT_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }
    
    /**
     * Adds updateEntity operation.
     * 
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     * 
     * @return none
     */
    public function addUpdateEntity($table, $entity)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        
        $operation = new BatchOperation();
        $type      = BatchOperationType::UPDATE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }
    
    /**
     * Adds mergeEntity operation.
     * 
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     * 
     * @return none
     */
    public function addMergeEntity($table, $entity)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        
        $operation = new BatchOperation();
        $type      = BatchOperationType::MERGE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }
    
    /**
     * Adds insertOrReplaceEntity operation.
     * 
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     * 
     * @return none
     */
    public function addInsertOrReplaceEntity($table, $entity)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        
        $operation = new BatchOperation();
        $type      = BatchOperationType::INSERT_REPLACE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }
    
    /**
     * Adds insertOrMergeEntity operation.
     * 
     * @param string $table  The table name.
     * @param Entity $entity The entity instance.
     * 
     * @return none
     */
    public function addInsertOrMergeEntity($table, $entity)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        
        $operation = new BatchOperation();
        $type      = BatchOperationType::INSERT_MERGE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ENTITY, $entity);
        $this->addOperation($operation);
    }
    
    /**
     * Adds deleteEntity operation.
     * 
     * @param string $table        The table name.
     * @param string $partitionKey The entity partition key.
     * @param string $rowKey       The entity row key.
     * @param string $etag         The entity etag.
     * 
     * @return none
     */
    public function addDeleteEntity($table, $partitionKey, $rowKey, $etag = null)
    {
        Validate::isString($table, 'table');
        Validate::isTrue(!is_null($partitionKey), Resources::NULL_TABLE_KEY_MSG);
        Validate::isTrue(!is_null($rowKey), Resources::NULL_TABLE_KEY_MSG);
        
        $operation = new BatchOperation();
        $type      = BatchOperationType::DELETE_ENTITY_OPERATION;
        $operation->setType($type);
        $operation->addParameter(BatchOperationParameterName::BP_TABLE, $table);
        $operation->addParameter(BatchOperationParameterName::BP_ROW_KEY, $rowKey);
        $operation->addParameter(BatchOperationParameterName::BP_ETAG, $etag);
        $operation->addParameter(
            BatchOperationParameterName::BP_PARTITION_KEY,
            $partitionKey
        );
        $this->addOperation($operation);
    }
}


