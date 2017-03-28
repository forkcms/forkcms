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
require_once 'HTTP/Request2/Response.php';
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Http\HttpClient;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Table\Models\BatchError;
use WindowsAzure\Table\Models\InsertEntityResult;
use WindowsAzure\Table\Models\UpdateEntityResult;

/**
 * Holds results from batch API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchResult
{
    /**
     * Each entry represents change set result.
     * 
     * @var array
     */
    private $_entries;
    
    /**
     * Creates a array of responses from the batch response body.
     * 
     * @param string            $body           The HTTP response body.
     * @param IMimeReaderWriter $mimeSerializer The MIME reader and writer.
     * 
     * @return array
     */
    private static function _constructResponses($body, $mimeSerializer)
    {
        $responses = array();
        $parts     = $mimeSerializer->decodeMimeMultipart($body);
        // Decrease the count of parts to remove the batch response body and just
        // include change sets response body. We may need to undo this action in
        // case that batch response body has useful info.
        $count = count($parts) - 1;

        for ($i = 0; $i < $count; $i++) {
            $lines    = explode("\r\n", $parts[$i]);
            $response = new \HTTP_Request2_Response($lines[0]);
            $j        = 1;
            do {
                $headerLine = $lines[$j++];
                $response->parseHeaderLine($headerLine);
            } while (Resources::EMPTY_STRING != $headerLine);
            $body = implode("\r\n", array_slice($lines, $j));
            $response->appendBody($body);
            $responses[] = $response;
        }
        
        return $responses;
    }
    
    /**
     * Compares between two responses by Content-ID header.
     * 
     * @param \HTTP_Request2_Response $r1 The first response object.
     * @param \HTTP_Request2_Response $r2 The second response object.
     * 
     * @return boolean
     */
    private static function _compareUsingContentId($r1, $r2)
    {
        $h1 = array_change_key_case($r1->getHeader());
        $h2 = array_change_key_case($r2->getHeader());
        $c1 = Utilities::tryGetValue($h1, Resources::CONTENT_ID, 0);
        $c2 = Utilities::tryGetValue($h2, Resources::CONTENT_ID, 0);
        
        return intval($c1) >= intval($c2);
    }


    /**
     * Creates BatchResult object.
     * 
     * @param string            $body           The HTTP response body.
     * @param array             $operations     The batch operations.
     * @param array             $contexts       The batch operations context.
     * @param IAtomReaderWriter $atomSerializer The Atom reader and writer.
     * @param IMimeReaderWriter $mimeSerializer The MIME reader and writer.
     * 
     * @return \WindowsAzure\Table\Models\BatchResult
     * 
     * @throws \InvalidArgumentException 
     */
    public static function create($body, $operations, $contexts, $atomSerializer, 
        $mimeSerializer
    ) {
        $result       = new BatchResult();
        $responses    = self::_constructResponses($body, $mimeSerializer);
        $callbackName = __CLASS__ . '::_compareUsingContentId';
        $count        = count($responses);
        $entries      = array();
        
        // Sort $responses based on Content-ID so they match order of $operations.
        uasort($responses, $callbackName);
        
        for ($i = 0; $i < $count; $i++) {
            $context   = $contexts[$i];
            $response  = $responses[$i];
            $operation = $operations[$i];
            $type      = $operation->getType();
            $body      = $response->getBody();
            $headers   = $response->getHeader();

            try {
                HttpClient::throwIfError(
                    $response->getStatus(),
                    $response->getReasonPhrase(),
                    $response->getBody(),
                    $context->getStatusCodes()
                );
            
                switch ($type) {
                case BatchOperationType::INSERT_ENTITY_OPERATION:
                    $entries[] = InsertEntityResult::create(
                        $body,
                        $headers,
                        $atomSerializer
                    );
                    break;
                case BatchOperationType::UPDATE_ENTITY_OPERATION:
                case BatchOperationType::MERGE_ENTITY_OPERATION:
                case BatchOperationType::INSERT_REPLACE_ENTITY_OPERATION:
                case BatchOperationType::INSERT_MERGE_ENTITY_OPERATION:
                    $entries[] = UpdateEntityResult::create($headers);
                    break;

                case BatchOperationType::DELETE_ENTITY_OPERATION:
                    $entries[] = Resources::BATCH_ENTITY_DEL_MSG;
                    break;

                default:
                    throw new \InvalidArgumentException();
                }
            } catch (ServiceException $e) {
                $entries[] = BatchError::create($e, $response->getHeader());
            }
        }
        $result->setEntries($entries);
        
        return $result;
    }
    
    /**
     * Gets batch call result entries.
     * 
     * @return array
     */
    public function getEntries()
    {
        return $this->_entries;
    }
    
    /**
     * Sets batch call result entries.
     * 
     * @param array $entries The batch call result entries.
     * 
     * @return none
     */
    public function setEntries($entries)
    {
        $this->_entries = $entries;
    }
}


