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
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Internal;
use WindowsAzure\Common\Internal\FilterableService;

/**
 * This interface has all REST APIs provided by Windows Azure for Table service.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd179423.aspx
 */
interface ITable extends FilterableService
{
    /**
    * Gets the properties of the Table service.
    * 
    * @param Models\TableServiceOptions $options optional table service options.
    * 
    * @return WindowsAzure\Common\Models\GetServicePropertiesResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452238.aspx
    */
    public function getServiceProperties($options = null);

    /**
    * Sets the properties of the Table service.
    * 
    * @param ServiceProperties          $serviceProperties new service properties
    * @param Models\TableServiceOptions $options           optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452240.aspx
    */
    public function setServiceProperties($serviceProperties, $options = null);
    
    /**
     * Quries tables in the given storage account.
     * 
     * @param Models\QueryTablesOptions|string|Models\Filter $options Could be
     * optional parameters, table prefix or filter to apply.
     * 
     * @return Models\QueryTablesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179405.aspx
     */
    public function queryTables($options = null);
    
    /**
     * Creates new table in the storage account
     * 
     * @param string                     $table   The name of the table.
     * @param Models\TableServiceOptions $options optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135729.aspx
     */
    public function createTable($table, $options = null);
    
    /**
     * Gets the table.
     * 
     * @param string                     $table   The The name of the table..
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return Models\GetTableResult
     */
    public function getTable($table, $options = null);
    
    /**
     * Deletes the specified table and any data it contains.
     * 
     * @param string                     $table   The name of the table.
     * @param Models\TableServiceOptions $options optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179387.aspx
     */
    public function deleteTable($table, $options = null);
    
    /**
     * Quries entities for the given table name
     * 
     * @param string                                           $table   The name of
     * the table.
     * @param Models\QueryEntitiesOptions|string|Models\Filter $options Coule be
     * optional parameters, query string or filter to apply.
     * 
     * @return Models\QueryEntitiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179421.aspx
     */
    public function queryEntities($table, $options = null);
    
    /**
     * Inserts new entity to the table
     * 
     * @param string                     $table   name of the table
     * @param Models\Entity              $entity  table entity
     * @param Models\TableServiceOptions $options optional parameters
     * 
     * @return Models\InsertEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179433.aspx
     */
    public function insertEntity($table, $entity, $options = null);
    
    /**
     * Updates an existing entity or inserts a new entity if it does not exist in the
     * table.
     * 
     * @param string                     $table   name of the table
     * @param Models\Entity              $entity  table entity
     * @param Models\TableServiceOptions $options optional parameters
     * 
     * @return Models\UpdateEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452241.aspx
     */
    public function insertOrMergeEntity($table, $entity, $options = null);
    
    /**
     * Replaces an existing entity or inserts a new entity if it does not exist in
     * the table.
     * 
     * @param string                     $table   name of the table
     * @param Models\Entity              $entity  table entity
     * @param Models\TableServiceOptions $options optional parameters
     * 
     * @return Models\UpdateEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452242.aspx
     */
    public function insertOrReplaceEntity($table, $entity, $options = null);
    
    /**
     * Updates an existing entity in a table. The Update Entity operation replaces 
     * the entire entity and can be used to remove properties.
     * 
     * @param string                     $table   The table name.
     * @param Models\Entity              $entity  The table entity.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return Models\UpdateEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179427.aspx
     */
    public function updateEntity($table, $entity, $options = null);
    
    /**
     * Updates an existing entity by updating the entity's properties. This operation
     * does not replace the existing entity, as the updateEntity operation does.
     * 
     * @param string                     $table   The table name.
     * @param Models\Entity              $entity  The table entity.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return Models\UpdateEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179392.aspx
     */
    public function mergeEntity($table, $entity, $options = null);
    
    /**
     * Deletes an existing entity in a table.
     * 
     * @param string                     $table        The name of the table.
     * @param string                     $partitionKey The entity partition key.
     * @param string                     $rowKey       The entity row key.
     * @param Models\DeleteEntityOptions $options      The optional parameters.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135727.aspx
     */
    public function deleteEntity($table, $partitionKey, $rowKey, $options = null);
    
    /**
     * Gets table entity.
     * 
     * @param string                     $table        The name of the table.
     * @param string                     $partitionKey The entity partition key.
     * @param string                     $rowKey       The entity row key.
     * @param Models\TableServiceOptions $options      The optional parameters.
     * 
     * @return Models\GetEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179421.aspx
     */
    public function getEntity($table, $partitionKey, $rowKey, $options = null);
    
    /**
     * Does batch of operations on given table service.
     * 
     * @param BatchOperations            $operations the operations to apply
     * @param Models\TableServiceOptions $options    optional parameters
     * 
     * @return Models\BatchResult
     */
    public function batch($operations, $options = null);
}


