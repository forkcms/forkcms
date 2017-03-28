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
 * @package   WindowsAzure\Queue
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Queue;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Http\IHttpClient;
use WindowsAzure\Common\Internal\Http\Url;
use WindowsAzure\Common\Internal\ServiceRestProxy;
use WindowsAzure\Common\Models\GetServicePropertiesResult;
use WindowsAzure\Common\Models\ServiceProperties;
use WindowsAzure\Queue\Internal\IQueue;
use WindowsAzure\Queue\Models\ListQueuesOptions;
use WindowsAzure\Queue\Models\ListQueuesResult;
use WindowsAzure\Queue\Models\CreateQueueOptions;
use WindowsAzure\Queue\Models\QueueServiceOptions;
use WindowsAzure\Queue\Models\GetQueueMetadataResult;
use WindowsAzure\Queue\Models\CreateMessageOptions;
use WindowsAzure\Queue\Models\QueueMessage;
use WindowsAzure\Queue\Models\ListMessagesOptions;
use WindowsAzure\Queue\Models\ListMessagesResult;
use WindowsAzure\Queue\Models\PeekMessagesOptions;
use WindowsAzure\Queue\Models\PeekMessagesResult;
use WindowsAzure\Queue\Models\UpdateMessageResult;

/**
 * This class constructs HTTP requests and receive HTTP responses for queue 
 * service layer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class QueueRestProxy extends ServiceRestProxy implements IQueue
{
    /**
     * Lists all queues in the storage account.
     * 
     * @param ListQueuesOptions $options The optional list queue options.
     * 
     * @return WindowsAzure\Queue\Models\ListQueuesResult
     */
    public function listQueues($options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListQueuesOptions();
        }
        
        $timeout    = $options->getTimeout();
        $maxResults = $options->getMaxResults();
        $include    = $options->getIncludeMetadata();
        $include    = $include ? 'metadata' : null;
        $prefix     = $options->getPrefix();
        $marker     = $options->getMarker();
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_TIMEOUT, $timeout);
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'list');
        $this->addOptionalQueryParam($queryParams, Resources::QP_PREFIX, $prefix);
        $this->addOptionalQueryParam($queryParams, Resources::QP_MARKER, $marker);
        $this->addOptionalQueryParam($queryParams, Resources::QP_INCLUDE, $include);
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $maxResults
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $parsed   = $this->dataSerializer->unserialize($response->getBody());
        
        return ListQueuesResult::create($parsed);
    }

    /**
     * Clears all messages from the queue.
     * 
     * If a queue contains a large number of messages, Clear Messages may time out 
     * before all messages have been deleted. In this case the Queue service will 
     * return status code 500 (Internal Server Error), with the additional error 
     * code OperationTimedOut. If the operation times out, the client should 
     * continue to retry Clear Messages until it succeeds, to ensure that all 
     * messages have been deleted.
     * 
     * @param string              $queueName The name of the queue.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return none
     */
    public function clearMessages($queueName, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages';
        $body        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_NO_CONTENT;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
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
            $statusCode,
            $body
        );
    }

    /**
     * Adds a message to the queue and optionally sets a visibility timeout 
     * for the message.
     * 
     * @param string               $queueName   The name of the queue.
     * @param string               $messageText The message contents.
     * @param CreateMessageOptions $options     The optional parameters.
     * 
     * @return none
     */
    public function createMessage($queueName, $messageText,
        $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::isString($messageText, 'messageText');
        
        $method      = Resources::HTTP_POST;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages';
        $body        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_CREATED;
        $message     = new QueueMessage();
        $message->setMessageText($messageText);
        $body = $message->toXml($this->dataSerializer);
        
        
        if (is_null($options)) {
            $options = new CreateMessageOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );
        
        $visibility = $options->getVisibilityTimeoutInSeconds();
        $timeToLive = $options->getTimeToLiveInSeconds();
        $timeout    = $options->getTimeout();
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_TIMEOUT, $timeout);
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_VISIBILITY_TIMEOUT,
            $visibility
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MESSAGE_TTL,
            $timeToLive
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
     * Creates a new queue under the storage account.
     * 
     * @param string             $queueName The queue name.
     * @param QueueCreateOptions $options   The Optional parameters.
     * 
     * @return none
     */
    public function createQueue($queueName, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName;
        $statusCode  = array(
            Resources::STATUS_CREATED,
            Resources::STATUS_NO_CONTENT
        );
        
        if (is_null($options)) {
            $options = new CreateQueueOptions();
        }

        $metadata = $options->getMetadata();
        $timeout  = $options->getTimeout();
        $headers  = $this->generateMetadataHeaders($metadata);
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_TIMEOUT, $timeout);
        
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
     * Deletes a specified message from the queue.
     * 
     * @param string              $queueName  The name of the queue.
     * @param string              $messageId  The id of the message.
     * @param string              $popReceipt The valid pop receipt value returned
     * from an earlier call to the Get Messages or Update Message operation.
     * @param QueueServiceOptions $options    The optional parameters.
     * 
     * @return none
     */
    public function deleteMessage($queueName, $messageId, $popReceipt, 
        $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::isString($messageId, 'messageId');
        Validate::notNullOrEmpty($messageId, 'messageId');
        Validate::isString($popReceipt, 'popReceipt');
        Validate::notNullOrEmpty($popReceipt, 'popReceipt');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages/' . $messageId;
        $body        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_NO_CONTENT;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_POPRECEIPT,
            $popReceipt
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
     * Deletes a queue.
     * 
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return none
     */
    public function deleteQueue($queueName, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName;
        $statusCode  = Resources::STATUS_NO_CONTENT;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
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
     * Returns queue properties, including user-defined metadata.
     * 
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\GetQueueMetadataResult
     */
    public function getQueueMetadata($queueName, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName;
        $body        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'metadata');
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
        
        $metadata = $this->getMetadataArray($response->getHeader());
        $maxCount = intval(
            $response->getHeader(Resources::X_MS_APPROXIMATE_MESSAGES_COUNT)
        );
        
        return new GetQueueMetadataResult($maxCount, $metadata);
    }

    /**
     * Gets the properties of the Queue service.
     * 
     * @param QueueServiceOptions $options The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\GetServicePropertiesResult
     */
    public function getServiceProperties($options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
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
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $parsed   = $this->dataSerializer->unserialize($response->getBody());
        
        return GetServicePropertiesResult::create($parsed);
    }

    /**
     * Lists all messages in the queue.
     * 
     * @param string              $queueName The queue name.
     * @param ListMessagesOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\ListMessagesResult
     */
    public function listMessages($queueName, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $queueName . '/messages';
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListMessagesOptions();
        }
        
        $messagesCount = $options->getNumberOfMessages();
        $visibility    = $options->getVisibilityTimeoutInSeconds();
        $timeout       = $options->getTimeout();
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_TIMEOUT, $timeout);
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NUM_OF_MESSAGES,
            $messagesCount
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_VISIBILITY_TIMEOUT,
            $visibility
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams,
            $postParams,
            $path, 
            $statusCode
        );

        $parsed = $this->dataSerializer->unserialize($response->getBody());
        
        return ListMessagesResult::create($parsed);
    }

    /**
     * Retrieves a message from the front of the queue, without changing 
     * the message visibility.
     * 
     * @param string              $queueName The queue name.
     * @param PeekMessagesOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\PeekMessagesResult
     */
    public function peekMessages($queueName, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $queueName . '/messages';
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new PeekMessagesOptions();
        }
        
        $messagesCount = $options->getNumberOfMessages();
        $timeout       = $options->getTimeout();
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_PEEK_ONLY, 'true');
        $this->addOptionalQueryParam($queryParams, Resources::QP_TIMEOUT, $timeout);
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_NUM_OF_MESSAGES,
            $messagesCount
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $parsed   = $this->dataSerializer->unserialize($response->getBody());
        
        return PeekMessagesResult::create($parsed);
    }

    /**
     * Sets user-defined metadata on the queue. To delete queue metadata, call 
     * this API without specifying any metadata in $metadata.
     * 
     * @param string              $queueName The queue name.
     * @param array               $metadata  The metadata array.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return none
     */
    public function setQueueMetadata($queueName, $metadata, $options = null)
    {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        $this->validateMetadata($metadata);
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $queueName;
        $statusCode  = Resources::STATUS_NO_CONTENT;
        $body        = Resources::EMPTY_STRING;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'metadata');
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $metadataHeaders = $this->generateMetadataHeaders($metadata);
        $headers         = $metadataHeaders;
        
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
     * Sets the properties of the Queue service.
     * 
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     * 
     * @param array               $serviceProperties The new service properties.
     * @param QueueServiceOptions $options           The optional parameters.  
     * 
     * @return none
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
        $body        = $serviceProperties->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
    
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
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
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
     * Updates the visibility timeout of a message and/or the message contents.
     * 
     * @param string              $queueName                  The queue name.
     * @param string              $messageId                  The id of the message.
     * @param string              $popReceipt                 The valid pop receipt 
     * value returned from an earlier call to the Get Messages or Update Message
     * operation.
     * @param string              $messageText                The message contents.
     * @param int                 $visibilityTimeoutInSeconds Specifies the new 
     * visibility timeout value, in seconds, relative to server time. 
     * The new value must be larger than or equal to 0, and cannot be larger 
     * than 7 days. The visibility timeout of a message cannot be set to a value 
     * later than the expiry time. A message can be updated until it has been 
     * deleted or has expired.
     * @param QueueServiceOptions $options                    The optional 
     * parameters.
     * 
     * @return WindowsAzure\Common\Models\UpdateMessageResult
     */
    public function updateMessage($queueName, $messageId, $popReceipt, $messageText, 
        $visibilityTimeoutInSeconds, $options = null
    ) {
        Validate::isString($queueName, 'queueName');
        Validate::notNullOrEmpty($queueName, 'queueName');
        Validate::isString($messageId, 'messageId');
        Validate::notNullOrEmpty($messageId, 'messageId');
        Validate::isString($popReceipt, 'popReceipt');
        Validate::notNullOrEmpty($popReceipt, 'popReceipt');
        Validate::isString($messageText, 'messageText');
        Validate::isInteger(
            $visibilityTimeoutInSeconds,
            'visibilityTimeoutInSeconds'
        );
        Validate::notNull(
            $visibilityTimeoutInSeconds,
            'visibilityTimeoutInSeconds'
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $queueName . '/messages' . '/' . $messageId;
        $body        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_NO_CONTENT;
        
        if (is_null($options)) {
            $options = new QueueServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_VISIBILITY_TIMEOUT,
            $visibilityTimeoutInSeconds
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_POPRECEIPT,
            $popReceipt
        );
        
        if (!empty($messageText)) {
            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_TYPE,
                Resources::URL_ENCODED_CONTENT_TYPE
            );
        
            $message = new QueueMessage();
            $message->setMessageText($messageText);
            $body = $message->toXml($this->dataSerializer);
        }
        
        $response        = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode, 
            $body
        );
        $popReceipt      = $response->getHeader(Resources::X_MS_POPRECEIPT);
        $timeNextVisible = $response->getHeader(Resources::X_MS_TIME_NEXT_VISIBLE);
        
        $date   = Utilities::rfc1123ToDateTime($timeNextVisible);
        $result = new UpdateMessageResult();
        $result->setPopReceipt($popReceipt);
        $result->setTimeNextVisible($date);
        
        return $result;
    }
}

