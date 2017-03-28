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
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceBus\Internal;
use WindowsAzure\Common\Internal\FilterableService;

/**
 * This class constructs HTTP requests and receive HTTP responses for Service Bus.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
interface IServiceBus extends FilterableService
{
    /**
     * Sends a brokered message. 
     * 
     * @param type $path            The path to send message. 
     * @param type $brokeredMessage The brokered message. 
     *
     * @throws Exception 
     * @return none
     */
    public function sendMessage($path, $brokeredMessage);

    /**
     * Sends a queue message. 
     * 
     * @param string           $queueName       The name of the queue to send 
     * message.
     * @param \BrokeredMessage $brokeredMessage The brokered message. 
     *
     * @throws Exception 
     * @return none
     */
    public function sendQueueMessage($queueName, $brokeredMessage);
    
    /**
     * Receives a queue message. 
     * 
     * @param string                  $queueName              The name of the
     * queue. 
     * @param \ReceivedMessageOptions $receivedMessageOptions The options to 
     * receive the message. 
     *
     * @throws Exception 
     * @return none
     */
    public function receiveQueueMessage($queueName, $receivedMessageOptions);

    /**
     * Receives a message. 
     * 
     * @param string                  $path                  The path of the 
     * message. 
     * @param \ReceivedMessageOptions $receiveMessageOptions The options to 
     * receive the message. 
     *
     * @throws Exception 
     * @return none
     */
    public function receiveMessage($path, $receiveMessageOptions);

    /**
     * Sends a brokered message to a specified topic. 
     * 
     * @param string           $topicName       The name of the topic. 
     * @param \BrokeredMessage $brokeredMessage The brokered message. 
     *
     * @throws Exception 
     * @return none
     */
    public function sendTopicMessage($topicName, $brokeredMessage);

    /**
     * Receives a subscription message. 
     * 
     * @param string                 $topicName             The name of the 
     * topic.
     * @param string                 $subscriptionName      The name of the 
     * subscription.
     * @param \ReceiveMessageOptions $receiveMessageOptions The options to 
     * receive the subscription message. 
     *
     * @throws Exception 
     * @return none
     */
    public function receiveSubscriptionMessage(
        $topicName, 
        $subscriptionName, 
        $receiveMessageOptions
    );

    /**
     * Unlocks a brokered message. 
     * 
     * @param \BrokeredMessage $brokeredMessage The brokered message. 
     *
     * @throws Exception 
     * @return none
     */
    public function unlockMessage($brokeredMessage);
    
    /**
     * Deletes a brokered message. 
     * 
     * @param \BrokeredMessage $brokeredMessage The borkered message.
     *
     * @throws Exception 
     * @return none
     */
    public function deleteMessage($brokeredMessage);
   
    /**
     * Creates a queue with specified queue info. 
     * 
     * @param \QueueInfo $queueInfo The information of the queue.
     *
     * @throws Exception 
     * @return none
     */
    public function createQueue($queueInfo);

    /**
     * Deletes a queue. 
     * 
     * @param string $queuePath The path of the queue.
     *
     * @throws Exception 
     * @return none
     */
    public function deleteQueue($queuePath);

    /**
     * Gets a queue with specified path. 
     * 
     * @param string $queuePath The path of the queue.
     *
     * @throws Exception 
     * @return none
     */
    public function getQueue($queuePath);

    /**
     * Lists a queue. 
     * 
     * @param \ListQueueOptions $listQueueOptions The options to list the 
     * queues.
     *
     * @throws Exception 
     * @return none
     */
    public function listQueues($listQueueOptions);

    /**
     * Creates a topic with specified topic info.  
     * 
     * @param \TopicInfo $topicInfo The information of the topic. 
     *
     * @throws Exception 
     * @return none
     */
    public function createTopic($topicInfo);

    /**
     * Deletes a topic with specified topic path. 
     * 
     * @param string $topicPath The path of the topic.
     *
     * @throws Exception 
     * @return none
     */
    public function deleteTopic($topicPath);
    
    /**
     * Gets a topic. 
     * 
     * @param string $topicPath The path of the topic.
     *
     * @throws Exception 
     * @return none
     */
    public function getTopic($topicPath); 
    
    /**
     * Lists topics. 
     * 
     * @param \ListTopicsOptions $listTopicsOptions The options to list 
     * the topics. 
     *
     * @throws Exception 
     * @return none 
     */
    public function listTopics($listTopicsOptions); 

    /**
     * Creates a subscription with specified topic path and 
     * subscription info. 
     * 
     * @param string            $topicPath        The path of the topic.
     * @param \SubscriptionInfo $subscriptionInfo The information of the 
     * subscription.
     *
     * @throws Exception 
     * @return none
     */
    public function createSubscription($topicPath, $subscriptionInfo); 

    /**
     * Deletes a subscription. 
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     *
     * @throws Exception 
     * @return none
     */
    public function deleteSubscription($topicPath, $subscriptionName); 
    
    /**
     * Gets a subscription. 
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     *
     * @throws Exception 
     * @return none 
     */
    public function getSubscription($topicPath, $subscriptionName); 

    /**
     * Lists subscriptions. 
     * 
     * @param string                   $topicPath                The path of 
     * the topic.
     * @param ListSubscriptionsOptions $listSubscriptionsOptions The options
     * to list the subscriptions. 
     *
     * @throws Exception 
     * @return none
     */
    public function listSubscriptions($topicPath, $listSubscriptionsOptions); 

    /**
     * Creates a rule. 
     * 
     * @param string    $topicPath        The path of the topic.
     * @param string    $subscriptionName The name of the subscription. 
     * @param \RuleInfo $ruleInfo         The info of the rule.
     *
     * @throws Exception 
     * @return none
     */
    public function createRule($topicPath, $subscriptionName, $ruleInfo);

    /**
     * Deletes a rule. 
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     * @param string $ruleName         The name of the rule.
     *
     * @throws Exception 
     * @return none
     */
    public function deleteRule($topicPath, $subscriptionName, $ruleName); 

    /**
     * Gets a rule. 
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     * @param string $ruleName         The name of the rule.
     *
     * @throws Exception 
     * @return none
     */
    public function getRule($topicPath, $subscriptionName, $ruleName); 

    /**
     * Lists rules. 
     * 
     * @param string            $topicPath        The path of the topic.
     * @param string            $subscriptionName The name of the subscription.
     * @param \ListRulesOptions $listRulesOptions The options to list the rules.
     *
     * @throws Exception 
     * @return none
     */
    public function listRules($topicPath, $subscriptionName, $listRulesOptions); 
    
}
