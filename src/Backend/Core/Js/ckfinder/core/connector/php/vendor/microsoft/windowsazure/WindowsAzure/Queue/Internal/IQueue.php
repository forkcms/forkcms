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
 * @package   WindowsAzure\Queue\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Queue\Internal;
use WindowsAzure\Common\Internal\FilterableService;

/**
 * This interface has all REST APIs provided by Windows Azure for queue service
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd179363.aspx
 */
interface IQueue extends FilterableService
{
    /**
     * Gets the properties of the Queue service.
     * 
     * @param QueueServiceOptions $options The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\GetServicePropertiesResult
     */
    public function getServiceProperties($options = null);

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
    public function setServiceProperties($serviceProperties, $options = null);

    /**
     * Creates a new queue under the storage account.
     * 
     * @param string             $queueName The queue name.
     * @param QueueCreateOptions $options   The optional queue create options.
     * 
     * @return none
     */
    public function createQueue($queueName, $options = null);

    /**
     * Deletes a queue.
     * 
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return none
     */
    public function deleteQueue($queueName, $options);

    /**
     * Lists all queues in the storage account.
     * 
     * @param ListQueuesOptions $options The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\ListQueuesResult
     */
    public function listQueues($options = null);

    /**
     * Returns queue properties, including user-defined metadata.
     * 
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\GetQueueMetadataResult
     */
    public function getQueueMetadata($queueName, $options = null);

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
    public function setQueueMetadata($queueName, $metadata, $options = null);

    /**
     * Adds a message to the queue and optionally sets a visibility timeout 
     * for the message.
     * 
     * @param string               $queueName   The queue name.
     * @param string               $messageText The message contents.
     * @param CreateMessageOptions $options     The optional parameters.
     * 
     * @return none
     */
    public function createMessage($queueName, $messageText, $options = null);

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
    );

    /**
     * Deletes a specified message from the queue.
     * 
     * @param string              $queueName  The queue name.
     * @param string              $messageId  The id of the message.
     * @param string              $popReceipt The valid pop receipt value returned
     * from an earlier call to the Get Messages or Update Message operation.
     * @param QueueServiceOptions $options    The optional parameters.
     * 
     * @return none
     */
    public function deleteMessage($queueName, $messageId, $popReceipt, 
        $options = null
    );

    /**
     * Lists all messages in the queue.
     * 
     * @param string              $queueName The queue name.
     * @param ListMessagesOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\ListMessagesResult
     */
    public function listMessages($queueName, $options = null);

    /**
     * Retrieves a message from the front of the queue, without changing 
     * the message visibility.
     * 
     * @param string              $queueName The queue name.
     * @param PeekMessagesOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\PeekMessagesResult
     */
    public function peekMessages($queueName, $options = null);

    /**
     * Clears all messages from the queue.
     * 
     * @param string              $queueName The queue name.
     * @param QueueServiceOptions $options   The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\PeekMessagesResult
     */
    public function clearMessages($queueName, $options = null);
}

