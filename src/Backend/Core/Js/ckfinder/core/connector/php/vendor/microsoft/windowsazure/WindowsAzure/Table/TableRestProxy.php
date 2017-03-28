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
 * @package   WindowsAzure\Table
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Http\HttpCallContext;
use WindowsAzure\Common\Models\ServiceProperties;
use WindowsAzure\Common\Internal\ServiceRestProxy;
use WindowsAzure\Common\Models\GetServicePropertiesResult;
use WindowsAzure\Table\Internal\ITable;
use WindowsAzure\Table\Models\TableServiceOptions;
use WindowsAzure\Table\Models\EdmType;
use WindowsAzure\Table\Models\Filters;
use WindowsAzure\Table\Models\Filters\Filter;
use WindowsAzure\Table\Models\Filters\PropertyNameFilter;
use WindowsAzure\Table\Models\Filters\ConstantFilter;
use WindowsAzure\Table\Models\Filters\UnaryFilter;
use WindowsAzure\Table\Models\Filters\BinaryFilter;
use WindowsAzure\Table\Models\Filters\QueryStringFilter;
use WindowsAzure\Table\Models\GetTableResult;
use WindowsAzure\Table\Models\QueryTablesOptions;
use WindowsAzure\Table\Models\QueryTablesResult;
use WindowsAzure\Table\Models\InsertEntityResult;
use WindowsAzure\Table\Models\UpdateEntityResult;
use WindowsAzure\Table\Models\QueryEntitiesOptions;
use WindowsAzure\Table\Models\QueryEntitiesResult;
use WindowsAzure\Table\Models\DeleteEntityOptions;
use WindowsAzure\Table\Models\GetEntityResult;
use WindowsAzure\Table\Models\BatchOperationType;
use WindowsAzure\Table\Models\BatchOperationParameterName;
use WindowsAzure\Table\Models\BatchResult;

/**
 * This class constructs HTTP requests and receive HTTP responses for table
 * service layer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class TableRestProxy extends ServiceRestProxy implements ITable
{
    /**
     * @var Utilities\IAtomReaderWriter
     */
    private $_atomSerializer;
    
    /**
     *
     * @var Utilities\IMimeReaderWriter
     */
    private $_mimeSerializer;
    
    /**
     * Creates contexts for batch operations.
     * 
     * @param array $operations The batch operations array.
     * 
     * @return array
     * 
     * @throws \InvalidArgumentException 
     */
    private function _createOperationsContexts($operations)
    {
        $contexts = array();
        
        foreach ($operations as $operation) {
            $context = null;
            $type    = $operation->getType();
            
            switch ($type) {
            case BatchOperationType::INSERT_ENTITY_OPERATION:
            case BatchOperationType::UPDATE_ENTITY_OPERATION:
            case BatchOperationType::MERGE_ENTITY_OPERATION:
            case BatchOperationType::INSERT_REPLACE_ENTITY_OPERATION:
            case BatchOperationType::INSERT_MERGE_ENTITY_OPERATION:
                $table   = $operation->getParameter(
                    BatchOperationParameterName::BP_TABLE
                );
                $entity  = $operation->getParameter(
                    BatchOperationParameterName::BP_ENTITY
                );
                $context = $this->_getOperationContext($table, $entity, $type);
                break;
        
            case BatchOperationType::DELETE_ENTITY_OPERATION:
                $table        = $operation->getParameter(
                    BatchOperationParameterName::BP_TABLE
                );
                $partitionKey = $operation->getParameter(
                    BatchOperationParameterName::BP_PARTITION_KEY
                );
                $rowKey       = $operation->getParameter(
                    BatchOperationParameterName::BP_ROW_KEY
                );
                $etag         = $operation->getParameter(
                    BatchOperationParameterName::BP_ETAG
                );
                $options      = new DeleteEntityOptions();
                $options->setETag($etag);
                $context = $this->_constructDeleteEntityContext(
                    $table, $partitionKey, $rowKey, $options
                );
                break;

            default:
                throw new \InvalidArgumentException();
            }
            
            $contexts[] = $context;
        }
        
        return $contexts;
    }
    
    /**
     * Creates operation context for the API.
     * 
     * @param string        $table  The table name.
     * @param Models\Entity $entity The entity object.
     * @param string        $type   The API type.
     * 
     * @return WindowsAzure\Common\Internal\Http\HttpCallContext
     * 
     * @throws \InvalidArgumentException 
     */
    private function _getOperationContext($table, $entity, $type)
    {
        switch ($type) {
        case BatchOperationType::INSERT_ENTITY_OPERATION:
        return $this->_constructInsertEntityContext($table, $entity, null);
            
        case BatchOperationType::UPDATE_ENTITY_OPERATION:
        return $this->_constructPutOrMergeEntityContext(
            $table,
            $entity,
            Resources::HTTP_PUT,
            true,
            null
        );
            
        case BatchOperationType::MERGE_ENTITY_OPERATION:
        return $this->_constructPutOrMergeEntityContext(
            $table,
            $entity,
            Resources::HTTP_MERGE,
            true,
            null
        );
            
        case BatchOperationType::INSERT_REPLACE_ENTITY_OPERATION:
        return $this->_constructPutOrMergeEntityContext(
            $table,
            $entity,
            Resources::HTTP_PUT,
            false,
            null
        );
            
        case BatchOperationType::INSERT_MERGE_ENTITY_OPERATION:
        return $this->_constructPutOrMergeEntityContext(
            $table,
            $entity,
            Resources::HTTP_MERGE,
            false,
            null
        );
            
        default:
            throw new \InvalidArgumentException();
        }
    }
    
    /**
     * Creates MIME part body for batch API.
     * 
     * @param array $operations The batch operations.
     * @param array $contexts   The contexts objects.
     * 
     * @return array
     * 
     * @throws \InvalidArgumentException
     */
    private function _createBatchRequestBody($operations, $contexts)
    {
        $mimeBodyParts = array();
        $contentId     = 1;
        $count         = count($operations);
        
        Validate::isTrue(
            count($operations) == count($contexts),
            Resources::INVALID_OC_COUNT_MSG
        );
        
        for ($i = 0; $i < $count; $i++) {
            $operation = $operations[$i];
            $context   = $contexts[$i];
            $type      = $operation->getType();
            
            switch ($type) {
            case BatchOperationType::INSERT_ENTITY_OPERATION:
            case BatchOperationType::UPDATE_ENTITY_OPERATION:
            case BatchOperationType::MERGE_ENTITY_OPERATION:
            case BatchOperationType::INSERT_REPLACE_ENTITY_OPERATION:
            case BatchOperationType::INSERT_MERGE_ENTITY_OPERATION:
                $contentType  = $context->getHeader(Resources::CONTENT_TYPE);
                $body         = $context->getBody();
                $contentType .= ';type=entry';
                $context->addOptionalHeader(Resources::CONTENT_TYPE, $contentType);
                // Use mb_strlen instead of strlen to get the length of the string
                // in bytes instead of the length in chars.
                $context->addOptionalHeader(
                    Resources::CONTENT_LENGTH,
                    strlen($body)
                );
                break;
        
            case BatchOperationType::DELETE_ENTITY_OPERATION:
                break;

            default:
                throw new \InvalidArgumentException();
            }
            
            $context->addOptionalHeader(Resources::CONTENT_ID, $contentId);
            $mimeBodyPart    = $context->__toString();
            $mimeBodyParts[] = $mimeBodyPart;
            $contentId++;
        }
        
        return $this->_mimeSerializer->encodeMimeMultipart($mimeBodyParts);
    }
    
    /**
     * Constructs HTTP call context for deleteEntity API.
     * 
     * @param string                     $table        The name of the table.
     * @param string                     $partitionKey The entity partition key.
     * @param string                     $rowKey       The entity row key.
     * @param Models\DeleteEntityOptions $options      The optional parameters.
     * 
     * @return HttpCallContext
     */
    private function _constructDeleteEntityContext($table, $partitionKey, $rowKey, 
        $options
    ) {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        Validate::isTrue(!is_null($partitionKey), Resources::NULL_TABLE_KEY_MSG);
        Validate::isTrue(!is_null($rowKey), Resources::NULL_TABLE_KEY_MSG);
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_NO_CONTENT;
        $path        = $this->_getEntityPath($table, $partitionKey, $rowKey);
        
        if (is_null($options)) {
            $options = new DeleteEntityOptions();
        }
        
        $etagObj = $options->getETag();
        $ETag    = !is_null($etagObj);
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::IF_MATCH,
            $ETag ? $etagObj : Resources::ASTERISK
        );
        
        $context = new HttpCallContext();
        $context->setHeaders($headers);
        $context->setMethod($method);
        $context->setPath($path);
        $context->setQueryParameters($queryParams);
        $context->addStatusCode($statusCode);
        $context->setUri($this->getUri());
        $context->setBody('');
        
        return $context;
    }
    
    /**
     * Constructs HTTP call context for updateEntity, mergeEntity, 
     * insertOrReplaceEntity and insertOrMergeEntity.
     * 
     * @param string                     $table   The table name.
     * @param Models\Entity              $entity  The entity instance to use.
     * @param string                     $verb    The HTTP method.
     * @param boolean                    $useETag The flag to include etag or not.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return HttpCallContext
     */
    private function _constructPutOrMergeEntityContext($table, $entity, $verb,
        $useETag, $options
    ) {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        Validate::isTrue($entity->isValid($msg), $msg);
        
        $method       = $verb;
        $headers      = array();
        $queryParams  = array();
        $statusCode   = Resources::STATUS_NO_CONTENT;
        $partitionKey = $entity->getPartitionKey();
        $rowKey       = $entity->getRowKey();
        $path         = $this->_getEntityPath($table, $partitionKey, $rowKey);
        $body         = $this->_atomSerializer->getEntity($entity);
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        if ($useETag) {
            $etag         = $entity->getETag();
            $ifMatchValue = is_null($etag) ? Resources::ASTERISK : $etag;
            
            $this->addOptionalHeader($headers, Resources::IF_MATCH, $ifMatchValue);
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        
        $context = new HttpCallContext();
        $context->setBody($body);
        $context->setHeaders($headers);
        $context->setMethod($method);
        $context->setPath($path);
        $context->setQueryParameters($queryParams);
        $context->addStatusCode($statusCode);
        $context->setUri($this->getUri());
        
        return $context;
    }
    
    /**
     * Constructs HTTP call context for insertEntity API.
     * 
     * @param string                     $table   The name of the table.
     * @param Models\Entity              $entity  The table entity.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return HttpCallContext
     */
    private function _constructInsertEntityContext($table, $entity, $options)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        Validate::notNullOrEmpty($entity, 'entity');
        Validate::isTrue($entity->isValid($msg), $msg);
        
        $method      = Resources::HTTP_POST;
        $context     = new HttpCallContext();
        $headers     = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_CREATED;
        $path        = $table;
        $body        = $this->_atomSerializer->getEntity($entity);
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        
        $context->setBody($body);
        $context->setHeaders($headers);
        $context->setMethod($method);
        $context->setPath($path);
        $context->setQueryParameters($queryParams);
        $context->addStatusCode($statusCode);
        $context->setUri($this->getUri());
        
        return $context;
    }
    
    /**
     * Constructs URI path for entity.
     * 
     * @param string $table        The table name.
     * @param string $partitionKey The entity's partition key.
     * @param string $rowKey       The entity's row key.
     * 
     * @return string 
     */
    private function _getEntityPath($table, $partitionKey, $rowKey)
    {
        $encodedPK = $this->_encodeODataUriValue($partitionKey);
        $encodedRK = $this->_encodeODataUriValue($rowKey);
        
        return "$table(PartitionKey='$encodedPK',RowKey='$encodedRK')";
    }
    
    /**
     * Does actual work for update and merge entity APIs.
     * 
     * @param string                     $table   The table name.
     * @param Models\Entity              $entity  The entity instance to use.
     * @param string                     $verb    The HTTP method.
     * @param boolean                    $useETag The flag to include etag or not.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return Models\UpdateEntityResult
     */
    private function _putOrMergeEntityImpl($table, $entity, $verb, $useETag,
        $options
    ) {
        $context = $this->_constructPutOrMergeEntityContext(
            $table,
            $entity,
            $verb,
            $useETag,
            $options
        );
        
        $response = $this->sendContext($context);
        
        return UpdateEntityResult::create($response->getHeader());
    }
 
    /**
     * Builds filter expression
     * 
     * @param Filter $filter The filter object
     * 
     * @return string 
     */
    private function _buildFilterExpression($filter)
    {
        $e = Resources::EMPTY_STRING;
        $this->_buildFilterExpressionRec($filter, $e);
        
        return $e;
    }
    
    /**
     * Builds filter expression
     * 
     * @param Filter $filter The filter object
     * @param string &$e     The filter expression
     * 
     * @return string
     */
    private function _buildFilterExpressionRec($filter, &$e)
    {
        if (is_null($filter)) {
            return;
        }
        
        if ($filter instanceof PropertyNameFilter) {
            $e .= $filter->getPropertyName();
        } else if ($filter instanceof ConstantFilter) {
            $value = $filter->getValue();
            // If the value is null we just append null regardless of the edmType.
            if (is_null($value)) {
                $e .= 'null';
            } else {
                $type = $filter->getEdmType();
                $e   .= EdmType::serializeQueryValue($type, $value);
            }
        } else if ($filter instanceof UnaryFilter) {
            $e .= $filter->getOperator();
            $e .= '(';
            $this->_buildFilterExpressionRec($filter->getOperand(), $e);
            $e .= ')';
        } else if ($filter instanceof Filters\BinaryFilter) {
            $e .= '(';
            $this->_buildFilterExpressionRec($filter->getLeft(), $e);
            $e .= ' ';
            $e .= $filter->getOperator();
            $e .= ' ';
            $this->_buildFilterExpressionRec($filter->getRight(), $e);
            $e .= ')';
        } else if ($filter instanceof QueryStringFilter) {
            $e .= $filter->getQueryString();
        }
        
        return $e;
    }
    
    /**
     * Adds query object to the query parameter array
     * 
     * @param array        $queryParam The URI query parameters 
     * @param Models\Query $query      The query object
     * 
     * @return array 
     */
    private function _addOptionalQuery($queryParam, $query)
    {
        if (!is_null($query)) {
            $selectedFields = $query->getSelectFields();
            if (!empty($selectedFields)) {
                $final = $this->_encodeODataUriValues($selectedFields); 
                
                $this->addOptionalQueryParam(
                    $queryParam,
                    Resources::QP_SELECT,
                    implode(',', $final)
                );
            }
            
            if (!is_null($query->getTop())) {
                $final = strval($this->_encodeODataUriValue($query->getTop()));
                
                $this->addOptionalQueryParam(
                    $queryParam,
                    Resources::QP_TOP,
                    $final
                );
            }
            
            if (!is_null($query->getFilter())) {
                $final = $this->_buildFilterExpression($query->getFilter());
                
                $this->addOptionalQueryParam(
                    $queryParam,
                    Resources::QP_FILTER,
                    $final
                );
            }
        }
        
        return $queryParam;
    }
    
    /**
     * Encodes OData URI values
     * 
     * @param array $values The OData URL values
     * 
     * @return array
     */
    private function _encodeODataUriValues($values)
    {
        $list = array();
        
        foreach ($values as $value) {
            $list[] = $this->_encodeODataUriValue($value);
        }
        
        return $list;
    }
    
    /**
     * Encodes OData URI value
     * 
     * @param string $value The OData URL value
     * 
     * @return string
     */
    private function _encodeODataUriValue($value)
    {
        // Replace each single quote (') with double single quotes ('') not doudle
        // quotes (")
        $value = str_replace('\'', '\'\'', $value);
        
        // Encode the special URL characters
        $value = rawurlencode($value);
        
        return $value;
    }
    
    /**
     * Initializes new TableRestProxy object.
     * 
     * @param IHttpClient       $channel        The HTTP client channel.
     * @param string            $uri            The storage account uri.
     * @param IAtomReaderWriter $atomSerializer The atom serializer.
     * @param IMimeReaderWriter $mimeSerializer The MIME serializer.
     * @param ISerializable     $dataSerializer The data serializer.
     */
    public function __construct($channel, $uri, $atomSerializer, $mimeSerializer, 
        $dataSerializer
    ) {
        parent::__construct(
            $channel,
            $uri,
            Resources::EMPTY_STRING,
            $dataSerializer
        );
        $this->_atomSerializer = $atomSerializer;
        $this->_mimeSerializer = $mimeSerializer;
    }
    
    /**
     * Gets the properties of the Table service.
     * 
     * @param Models\TableServiceOptions $options optional table service options.
     * 
     * @return WindowsAzure\Common\Models\GetServicePropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452238.aspx
     */
    public function getServiceProperties($options = null)
    {
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $context = new HttpCallContext();
        $timeout = $options->getTimeout();
        $context->setMethod(Resources::HTTP_GET);
        $context->addOptionalQueryParameter(Resources::QP_REST_TYPE, 'service');
        $context->addOptionalQueryParameter(Resources::QP_COMP, 'properties');
        $context->addOptionalQueryParameter(Resources::QP_TIMEOUT, $timeout);
        $context->addStatusCode(Resources::STATUS_OK);
        
        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());
        
        return GetServicePropertiesResult::create($parsed);
    }

    /**
     * Sets the properties of the Table service.
     * 
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     * 
     * @param ServiceProperties          $serviceProperties new service properties
     * @param Models\TableServiceOptions $options           optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452240.aspx
     */
    public function setServiceProperties($serviceProperties, $options = null)
    {
        Validate::isTrue(
            $serviceProperties instanceof ServiceProperties,
            Resources::INVALID_SVC_PROP_MSG
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_ACCEPTED;
        $path        = Resources::EMPTY_STRING;
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'service'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'properties'
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        $body = $serviceProperties->toXml($this->dataSerializer);
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode, 
            $body
        );
    }
    
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
    public function queryTables($options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;
        $path        = 'Tables';
        
        if (is_null($options)) {
            $options = new QueryTablesOptions();
        } else if (is_string($options)) {
            $prefix  = $options;
            $options = new QueryTablesOptions();
            $options->setPrefix($prefix);
        } else if ($options instanceof Filter) {
            $filter  = $options;
            $options = new QueryTablesOptions();
            $options->setFilter($filter);
        }
        
        $query   = $options->getQuery();
        $next    = $options->getNextTableName();
        $prefix  = $options->getPrefix();
        $timeout = $options->getTimeout();
        
        if (!empty($prefix)) {
            // Append Max char to end '{' is 1 + 'z' in AsciiTable ==> upperBound 
            // is prefix + '{'
            $prefixFilter = Filter::applyAnd(
                Filter::applyGe(
                    Filter::applyPropertyName('TableName'),
                    Filter::applyConstant($prefix, EdmType::STRING)
                ),
                Filter::applyLe(
                    Filter::applyPropertyName('TableName'),
                    Filter::applyConstant($prefix . '{', EdmType::STRING)
                )
            );
            
            if (is_null($query)) {
                $query = new Models\Query();
            }

            if (is_null($query->getFilter())) {
                // use the prefix filter if the query filter is null
                $query->setFilter($prefixFilter);
            } else {
                // combine and use the prefix filter if the query filter exists
                $combinedFilter = Filter::applyAnd(
                    $query->getFilter(), $prefixFilter
                );
                $query->setFilter($combinedFilter);
            }
        }
        
        $queryParams = $this->_addOptionalQuery($queryParams, $query);
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NEXT_TABLE_NAME,
            $next
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $timeout
        );
        
        // One can specify the NextTableName option to get table entities starting 
        // from the specified name. However, there appears to be an issue in the 
        // Azure Table service where this does not engage on the server unless 
        // $filter appears in the URL. The current behavior is to just ignore the 
        // NextTableName options, which is not expected or easily detectable.
        if (   array_key_exists(Resources::QP_NEXT_TABLE_NAME, $queryParams)
            && !array_key_exists(Resources::QP_FILTER, $queryParams)
        ) {
            $queryParams[Resources::QP_FILTER] = Resources::EMPTY_STRING;
        }
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $tables   = $this->_atomSerializer->parseTableEntries($response->getBody());
        
        return QueryTablesResult::create($response->getHeader(), $tables);
    }
    
    /**
     * Creates new table in the storage account
     * 
     * @param string                     $table   The name of the table.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135729.aspx
     */
    public function createTable($table, $options = null)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        
        $method      = Resources::HTTP_POST;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_CREATED;
        $path        = 'Tables';
        $body        = $this->_atomSerializer->getTable($table);
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode, 
            $body
        );
    }
    
    /**
     * Gets the table.
     * 
     * @param string                     $table   The name of the table.
     * @param Models\TableServiceOptions $options The optional parameters.
     * 
     * @return Models\GetTableResult
     */
    public function getTable($table, $options = null)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;
        $path        = "Tables('$table')";
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        
        return GetTableResult::create($response->getBody(), $this->_atomSerializer);
    }
    
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
    public function deleteTable($table, $options = null)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_NO_CONTENT;
        $path        = "Tables('$table')";
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
    }
    
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
    public function queryEntities($table, $options = null)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;
        $path        = $table;
        
        if (is_null($options)) {
            $options = new QueryEntitiesOptions();
        } else if (is_string($options)) {
            $queryString = $options;
            $options     = new QueryEntitiesOptions();
            $options->setFilter(Filter::applyQueryString($queryString));
        } else if ($options instanceof Filter) {
            $filter  = $options;
            $options = new QueryEntitiesOptions();
            $options->setFilter($filter);
        }
        
        $queryParams = $this->_addOptionalQuery($queryParams, $options->getQuery());
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NEXT_PK,
            $options->getNextPartitionKey()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NEXT_RK,
            $options->getNextRowKey()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        
        if (!is_null($options->getQuery())) {
            $dsHeader   = Resources::DATA_SERVICE_VERSION;
            $maxdsValue = Resources::MAX_DATA_SERVICE_VERSION_VALUE;
            $fields     = $options->getQuery()->getSelectFields();
            $hasSelect  = !empty($fields);
            if ($hasSelect) {
                $this->addOptionalHeader($headers, $dsHeader, $maxdsValue);
            }
        }
        
        $response = $this->send(
            $method,
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );

        $entities = $this->_atomSerializer->parseEntities($response->getBody());
        
        return QueryEntitiesResult::create($response->getHeader(), $entities);
    }
    
    /**
     * Inserts new entity to the table.
     * 
     * @param string                     $table   name of the table.
     * @param Models\Entity              $entity  table entity.
     * @param Models\TableServiceOptions $options optional parameters.
     * 
     * @return Models\InsertEntityResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179433.aspx
     */
    public function insertEntity($table, $entity, $options = null)
    {
        $context = $this->_constructInsertEntityContext($table, $entity, $options);
        
        $response = $this->sendContext($context);
        $body     = $response->getBody();
        $headers  = $response->getHeader();
        
        return InsertEntityResult::create($body, $headers, $this->_atomSerializer);
    }
    
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
    public function insertOrMergeEntity($table, $entity, $options = null)
    {
        return $this->_putOrMergeEntityImpl(
            $table,
            $entity,
            Resources::HTTP_MERGE,
            false, 
            $options
        );
    }
    
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
    public function insertOrReplaceEntity($table, $entity, $options = null)
    {
        return $this->_putOrMergeEntityImpl(
            $table,
            $entity,
            Resources::HTTP_PUT,
            false, 
            $options
        );
    }
    
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
    public function updateEntity($table, $entity, $options = null)
    {
        return $this->_putOrMergeEntityImpl(
            $table,
            $entity,
            Resources::HTTP_PUT,
            true, 
            $options
        );
    }
    
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
    public function mergeEntity($table, $entity, $options = null)
    {
        return $this->_putOrMergeEntityImpl(
            $table,
            $entity,
            Resources::HTTP_MERGE,
            true, 
            $options
        );
    }
    
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
    public function deleteEntity($table, $partitionKey, $rowKey, $options = null)
    {
        $context = $this->_constructDeleteEntityContext(
            $table,
            $partitionKey,
            $rowKey,
            $options
        );
        
        $this->sendContext($context);
    }
    
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
    public function getEntity($table, $partitionKey, $rowKey, $options = null)
    {
        Validate::isString($table, 'table');
        Validate::notNullOrEmpty($table, 'table');
        Validate::isTrue(!is_null($partitionKey), Resources::NULL_TABLE_KEY_MSG);
        Validate::isTrue(!is_null($rowKey), Resources::NULL_TABLE_KEY_MSG);
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;
        $path        = $this->_getEntityPath($table, $partitionKey, $rowKey);
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::XML_ATOM_CONTENT_TYPE
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $context = new HttpCallContext();
        $context->setHeaders($headers);
        $context->setMethod($method);
        $context->setPath($path);
        $context->setQueryParameters($queryParams);
        $context->addStatusCode($statusCode);
        
        $response = $this->sendContext($context);
        $entity   = $this->_atomSerializer->parseEntity($response->getBody());
        $result   = new GetEntityResult();
        $result->setEntity($entity);
        
        return $result;
    }
    
    /**
     * Does batch of operations on the table service.
     * 
     * @param Models\BatchOperations     $batchOperations The operations to apply.
     * @param Models\TableServiceOptions $options         The optional parameters.
     * 
     * @return Models\BatchResult
     */
    public function batch($batchOperations, $options = null)
    {
        Validate::notNullOrEmpty($batchOperations, 'batchOperations');
        
        $method      = Resources::HTTP_POST;
        $operations  = $batchOperations->getOperations();
        $contexts    = $this->_createOperationsContexts($operations);
        $mime        = $this->_createBatchRequestBody($operations, $contexts);
        $body        = $mime['body'];
        $headers     = $mime['headers'];
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_ACCEPTED;
        $path        = '$batch';
        
        if (is_null($options)) {
            $options = new TableServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode, 
            $body
        );
        
        return BatchResult::create(
            $response->getBody(),
            $operations,
            $contexts,
            $this->_atomSerializer,
            $this->_mimeSerializer
        );
    }
}


