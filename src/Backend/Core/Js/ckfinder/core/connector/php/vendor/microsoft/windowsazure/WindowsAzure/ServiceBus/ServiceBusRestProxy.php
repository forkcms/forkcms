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
 * @package   WindowsAzure\ServiceBus
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceBus;
use WindowsAzure\Common\Internal\ServiceRestProxy;
use WindowsAzure\Common\Internal\Http\HttpCallContext;
use WindowsAzure\Common\Internal\Serialization\XmlSerializer;
use WindowsAzure\Common\Internal\Atom\Content;
use WindowsAzure\Common\Internal\Atom\Entry;
use WindowsAzure\Common\Internal\Atom\Feed;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\ServiceBus\Internal\IServiceBus;
use WindowsAzure\ServiceBus\Models\BrokeredMessage;
use WindowsAzure\ServiceBus\Models\BrokerProperties;
use WindowsAzure\ServiceBus\Models\ListQueuesOptions;
use WindowsAzure\ServiceBus\Models\ListQueuesResult;
use WindowsAzure\ServiceBus\Models\ListSubscriptionsOptions;
use WindowsAzure\ServiceBus\Models\ListSubscriptionsResult;
use WindowsAzure\ServiceBus\Models\ListTopicsOptions;
use WindowsAzure\ServiceBus\Models\ListTopicsResult;
use WindowsAzure\ServiceBus\Models\ListRulesOptions;
use WindowsAzure\ServiceBus\Models\ListRulesResult;
use WindowsAzure\ServiceBus\Models\ListOptions;
use WindowsAzure\ServiceBus\Models\QueueDescription;
use WindowsAzure\ServiceBus\Models\QueueInfo;
use WindowsAzure\ServiceBus\Models\ReceiveMessageOptions;
use WindowsAzure\ServiceBus\Models\RuleDescription;
use WindowsAzure\ServiceBus\Models\RuleInfo;
use WindowsAzure\ServiceBus\Models\SubscriptionDescription;
use WindowsAzure\ServiceBus\Models\SubscriptionInfo;
use WindowsAzure\ServiceBus\Models\TopicDescription;
use WindowsAzure\ServiceBus\Models\TopicInfo;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;

/**
 * This class constructs HTTP requests and receive HTTP responses 
 * for Service Bus.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class ServiceBusRestProxy extends ServiceRestProxy implements IServiceBus
{
    /**
     * Creates a ServiceBusRestProxy with specified parameter. 
     * 
     * @param IHttpClient $channel        The channel to communicate. 
     * @param string      $uri            The URI of Service Bus service.
     * @param ISerializer $dataSerializer The serializer of the Service Bus.
     *
     * @return none
     */
    public function __construct($channel, $uri, $dataSerializer)
    {
        parent::__construct(
            $channel, 
            $uri, 
            Resources::EMPTY_STRING, 
            $dataSerializer
        );
    }
    
    /**
     * Sends a brokered message. 
     * 
     * Queues:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780775
     *
     * Topic Subscriptions:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780786
     * 
     * @param type $path            The path to send message. 
     * @param type $brokeredMessage The brokered message. 
     *
     * @return none
     */
    public function sendMessage($path, $brokeredMessage)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_POST);
        $httpCallContext->addStatusCode(Resources::STATUS_CREATED);
        $httpCallContext->setPath($path);
        $contentType = $brokeredMessage->getContentType();

        if (!is_null($contentType)) {
            $httpCallContext->addHeader(
                Resources::CONTENT_TYPE,
                $contentType
            );
        }
        
        $brokerProperties = $brokeredMessage->getBrokerProperties();
        if (!is_null($brokerProperties)) {
            $httpCallContext->addHeader(
                Resources::BROKER_PROPERTIES,
                $brokerProperties->toString()
            );
        } 
        $customProperties = $brokeredMessage->getProperties();

        if (!empty($customProperties)) {
            foreach ($customProperties as $key => $value) {
                $value = json_encode($value);
                $httpCallContext->addHeader($key, $value);
                    
            }
        }

        $httpCallContext->setBody($brokeredMessage->getBody());
        $this->sendContext($httpCallContext);
    }

    /**
     * Sends a queue message. 
     * 
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780775
     *
     * @param string          $queueName       The name of the queue.
     * @param BrokeredMessage $brokeredMessage The brokered message. 
     *
     * @return none
     */
    public function sendQueueMessage($queueName, $brokeredMessage)
    {
        $path = sprintf(Resources::SEND_MESSAGE_PATH, $queueName);
        $this->sendMessage($path, $brokeredMessage);
    }
    
    /**
     * Receives a queue message. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780735
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780756 
     * 
     * @param string                $queueName             The name of the
     * queue. 
     * @param ReceiveMessageOptions $receiveMessageOptions The options to 
     * receive the message.
     *
     * @return BrokeredMessage
     */
    public function receiveQueueMessage($queueName, $receiveMessageOptions = null)
    {
        $queueMessagePath = sprintf(Resources::RECEIVE_MESSAGE_PATH, $queueName);
        return $this->receiveMessage(
            $queueMessagePath, 
            $receiveMessageOptions
        );
    }

    // @codingStandardsIgnoreStart
    
    /**
     * Receives a message. 
     * 
     * Queues:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780735
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780756 
     *
     * Topic Subscriptions:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780722
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780770
     *
     * @param string                 $path                  The path of the 
     * message. 
     * @param ReceivedMessageOptions $receiveMessageOptions The options to 
     * receive the message. 
     * 
     * @return BrokeredMessage
     */
    public function receiveMessage($path, $receiveMessageOptions = null)
    {
        if (is_null($receiveMessageOptions)) {
            $receiveMessageOptions = new ReceiveMessageOptions();
        } 

        $httpCallContext = new HttpCallContext();
        $httpCallContext->setPath($path);
        $httpCallContext->addStatusCode(Resources::STATUS_CREATED);
        $httpCallContext->addStatusCode(Resources::STATUS_NO_CONTENT);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $timeout = $receiveMessageOptions->getTimeout();
        if (!is_null($timeout)) {
            $httpCallContext->addQueryParameter('timeout', $timeout);
        }

        if ($receiveMessageOptions->getIsReceiveAndDelete()) {
            $httpCallContext->setMethod(Resources::HTTP_DELETE);
        } else if ($receiveMessageOptions->getIsPeekLock()) {
            $httpCallContext->setMethod(Resources::HTTP_POST);
        } else {
            throw new \InvalidArgumentException(
                Resources::INVALID_RECEIVE_MODE_MSG
            );
        }

        $response = $this->sendContext($httpCallContext);
        if ($response->getStatus() === Resources::STATUS_NO_CONTENT) {
            $brokeredMessage = null;
        } else {
            $responseHeaders  = $response->getHeader(); 
            $brokerProperties = new BrokerProperties();

            if (array_key_exists('brokerproperties', $responseHeaders)) {
                $brokerProperties = BrokerProperties::create(
                    $responseHeaders['brokerproperties']
                );
            }

            if (array_key_exists('location', $responseHeaders)) {
                $brokerProperties->setLockLocation($responseHeaders['location']);
            }

            $brokeredMessage = new BrokeredMessage();
            $brokeredMessage->setBrokerProperties($brokerProperties);
        
            if (array_key_exists(Resources::CONTENT_TYPE, $responseHeaders)) {
                $brokeredMessage->setContentType(
                    $responseHeaders[Resources::CONTENT_TYPE]
                );
            }

            if (array_key_exists('Date', $responseHeaders)) {
                $brokeredMessage->setDate($responseHeaders['Date']);
            }

            $brokeredMessage->setBody($response->getBody());

            foreach (array_keys($responseHeaders) as $headerKey) {
                $value        = $responseHeaders[$headerKey];
                $decodedValue = json_decode($value);
                if (is_scalar($decodedValue)) {
                    $brokeredMessage->setProperty(
                        $headerKey, 
                        $decodedValue
                    );
                }

            }
        }
        
        return $brokeredMessage; 
    }
    
    // @codingStandardsIgnoreEnd

    /**
     * Sends a brokered message to a specified topic. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780786
     * 
     * @param string          $topicName       The name of the topic. 
     * @param BrokeredMessage $brokeredMessage The brokered message. 
     *
     * @return none
     */
    public function sendTopicMessage($topicName, $brokeredMessage)
    {
        $topicMessagePath = sprintf(Resources::SEND_MESSAGE_PATH, $topicName);
        $this->sendMessage($topicMessagePath, $brokeredMessage);
    } 

    /**
     * Receives a subscription message. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780722
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780770
     * 
     * @param string                $topicName             The name of the 
     * topic.
     * @param string                $subscriptionName      The name of the 
     * subscription.
     * @param ReceiveMessageOptions $receiveMessageOptions The options to 
     * receive the subscription message. 
     *
     * @return BrokeredMessage 
     */
    public function receiveSubscriptionMessage(
        $topicName, 
        $subscriptionName, 
        $receiveMessageOptions = null
    ) {
        $messagePath = sprintf(
            Resources::RECEIVE_SUBSCRIPTION_MESSAGE_PATH, 
            $topicName,
            $subscriptionName
        );

        $brokeredMessage = $this->receiveMessage(
            $messagePath,
            $receiveMessageOptions
        );

        return $brokeredMessage;
    }

    /**
     * Unlocks a brokered message. 
     *
     * Queues:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780723
     * 
     * Topic Subscriptions:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780737
     * 
     * @param BrokeredMessage $brokeredMessage The brokered message. 
     *
     * @return none
     */
    public function unlockMessage($brokeredMessage)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_PUT);
        $lockLocation      = $brokeredMessage->getLockLocation();
        $lockLocationArray = parse_url($lockLocation);
        $lockLocationPath  = Resources::EMPTY_STRING;

        if (array_key_exists(Resources::PHP_URL_PATH, $lockLocationArray)) {
            $lockLocationPath = $lockLocationArray[Resources::PHP_URL_PATH];
            $lockLocationPath = preg_replace(
                '@^\/@', 
                Resources::EMPTY_STRING, 
                $lockLocationPath
            );
        } 

        $httpCallContext->setPath($lockLocationPath);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $this->sendContext($httpCallContext);
    }
    
    /**
     * Deletes a brokered message. 
     *
     * Queues:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780767
     * 
     * Topic Subscriptions:
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780768
     * 
     * @param BrokeredMessage $brokeredMessage The brokered message.
     *
     * @return none
     */
    public function deleteMessage($brokeredMessage)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_DELETE);
        $lockLocation      = $brokeredMessage->getLockLocation();
        $lockLocationArray = parse_url($lockLocation);
        $lockLocationPath  = Resources::EMPTY_STRING;

        if (array_key_exists(Resources::PHP_URL_PATH, $lockLocationArray)) {
            $lockLocationPath = $lockLocationArray[Resources::PHP_URL_PATH];
            $lockLocationPath = preg_replace(
                '@^\/@', 
                Resources::EMPTY_STRING, 
                $lockLocationPath
            );
        }
        
        if (empty($lockLocationPath)) {
            throw new \InvalidArgumentException(
                Resources::MISSING_LOCK_LOCATION_MSG
            );
        }
        $httpCallContext->setPath($lockLocationPath);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $this->sendContext($httpCallContext);
    }
   
    /**
     * Creates a queue with a specified queue information. 
     * 
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780716
     *
     * @param QueueInfo $queueInfo The information of the queue.
     *
     * @return QueueInfo
     */
    public function createQueue($queueInfo)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_PUT);
        $httpCallContext->setPath($queueInfo->getTitle());
        $httpCallContext->addHeader(
            Resources::CONTENT_TYPE,
            Resources::ATOM_ENTRY_CONTENT_TYPE
        );
        $httpCallContext->addStatusCode(Resources::STATUS_CREATED);
        
        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $queueInfo->writeXml($xmlWriter); 
        $body = $xmlWriter->outputMemory();
        $httpCallContext->setBody($body);

        $response  = $this->sendContext($httpCallContext);
        $queueInfo = new QueueInfo();
        $queueInfo->parseXml($response->getBody());
        return $queueInfo;
    } 

    /**
     * Deletes a queue. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780747
     * 
     * @param string $queuePath The path of the queue.
     *
     * @return none
     */
    public function deleteQueue($queuePath)
    {
        Validate::isString($queuePath, 'queuePath');
        Validate::notNullOrEmpty($queuePath, 'queuePath');
        
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_DELETE);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $httpCallContext->setPath($queuePath);
        
        $this->sendContext($httpCallContext);
    }

    /**
     * Gets a queue with specified path. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780764
     * 
     * @param string $queuePath The path of the queue.
     *
     * @return QueueInfo
     */
    public function getQueue($queuePath)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setPath($queuePath);
        $httpCallContext->setMethod(Resources::HTTP_GET);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $response  = $this->sendContext($httpCallContext);
        $queueInfo = new QueueInfo();
        $queueInfo->parseXml($response->getBody());
        return $queueInfo;
    }

    /**
     * Lists a queue. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780759
     * 
     * @param ListQueuesOptions $listQueuesOptions The options to list the 
     * queues.
     *
     * @return ListQueuesResult;
     */
    public function listQueues($listQueuesOptions = null)
    {
        $response = $this->_listOptions(
            $listQueuesOptions, 
            Resources::LIST_QUEUES_PATH
        );

        $listQueuesResult = new ListQueuesResult();
        $listQueuesResult->parseXml($response->getBody());
        return $listQueuesResult;
    }

    /**
     * The base method of all the list operations. 
     * 
     * @param ListOptions $listOptions The options for list operation. 
     * @param string      $path        The path of the list operation.
     *
     * @return none 
     */
    private function _listOptions($listOptions, $path)
    {
        if (is_null($listOptions)) {
            $listOptions = new ListOptions();
        }

        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_GET);
        $httpCallContext->setPath($path);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $top  = $listOptions->getTop();
        $skip = $listOptions->getSkip();

        if (!empty($top)) {
            $httpCallContext->addQueryParameter(Resources::QP_TOP, $top);
        } 

        if (!empty($skip)) { 
            $httpCallContext->addQueryParameter(Resources::QP_SKIP, $skip);
        }

        return $this->sendContext($httpCallContext);
    }

    /**
     * Creates a topic with specified topic info.  
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780728
     * 
     * @param TopicInfo $topicInfo The information of the topic. 
     *
     * @return TopicInfo
     */
    public function createTopic($topicInfo)
    {
        Validate::notNullOrEmpty($topicInfo, 'topicInfo');
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_PUT);
        $httpCallContext->setPath($topicInfo->getTitle());
        $httpCallContext->addHeader(
            Resources::CONTENT_TYPE,
            Resources::ATOM_ENTRY_CONTENT_TYPE
        );
        $httpCallContext->addStatusCode(Resources::STATUS_CREATED);

        $topicDescriptionXml = XmlSerializer::objectSerialize(
            $topicInfo->getTopicDescription(),
            'TopicDescription'
        );

        $entry   = new Entry();
        $content = new Content($topicDescriptionXml);
        $content->setType(Resources::XML_CONTENT_TYPE);
        $entry->setContent($content); 

        $entry->setAttribute(
            Resources::XMLNS,
            Resources::SERVICE_BUS_NAMESPACE
        );

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $entry->writeXml($xmlWriter); 
        $httpCallContext->setBody($xmlWriter->outputMemory());

        $response  = $this->sendContext($httpCallContext);
        $topicInfo = new TopicInfo();
        $topicInfo->parseXml($response->getBody());
        return $topicInfo;
    } 

    /**
     * Deletes a topic with specified topic path.
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780721
     * 
     * @param string $topicPath The path of the topic.
     *
     * @return none
     */
    public function deleteTopic($topicPath)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_DELETE);
        $httpCallContext->setPath($topicPath);     
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        
        $this->sendContext($httpCallContext);
    }
    
    /**
     * Gets a topic.
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780769 
     * 
     * @param string $topicPath The path of the topic.
     *
     * @return TopicInfo
     */
    public function getTopic($topicPath) 
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_GET);
        $httpCallContext->setPath($topicPath);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $response  = $this->sendContext($httpCallContext);
        $topicInfo = new TopicInfo();
        $topicInfo->parseXml($response->getBody());
        return $topicInfo; 
    }
    
    /**
     * Lists topics. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780744
     * 
     * @param ListTopicsOptions $listTopicsOptions The options to list 
     * the topics. 
     *
     * @return ListTopicsResults
     */
    public function listTopics($listTopicsOptions = null) 
    {
        $response = $this->_listOptions(
            $listTopicsOptions, 
            Resources::LIST_TOPICS_PATH
        );

        $listTopicsResult = new ListTopicsResult();
        $listTopicsResult->parseXml($response->getBody());
        return $listTopicsResult;
    }

    /**
     * Creates a subscription with specified topic path and 
     * subscription info. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780748
     * 
     * @param string           $topicPath        The path of
     * the topic.
     * @param SubscriptionInfo $subscriptionInfo The information
     * of the subscription.
     *
     * @return SubscriptionInfo
     */
    public function createSubscription($topicPath, $subscriptionInfo) 
    {
        $httpCallContext = new HttpCallContext(); 
        $httpCallContext->setMethod(Resources::HTTP_PUT);
        $subscriptionPath = sprintf(
            Resources::SUBSCRIPTION_PATH, 
            $topicPath,
            $subscriptionInfo->getTitle()
        );
        $httpCallContext->setPath($subscriptionPath);
        $httpCallContext->addHeader(
            Resources::CONTENT_TYPE,    
            Resources::ATOM_ENTRY_CONTENT_TYPE
        );
        $httpCallContext->addStatusCode(Resources::STATUS_CREATED);

        $subscriptionDescriptionXml = XmlSerializer::objectSerialize(
            $subscriptionInfo->getSubscriptionDescription(),
            'SubscriptionDescription'
        );

        $entry   = new Entry();
        $content = new Content($subscriptionDescriptionXml);
        $content->setType(Resources::XML_CONTENT_TYPE);
        $entry->setContent($content);

        $entry->setAttribute(
            Resources::XMLNS,
            Resources::SERVICE_BUS_NAMESPACE
        );

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $entry->writeXml($xmlWriter); 
        $httpCallContext->setBody($xmlWriter->outputMemory());

        $response         = $this->sendContext($httpCallContext);
        $subscriptionInfo = new SubscriptionInfo();
        $subscriptionInfo->parseXml($response->getBody());
        return $subscriptionInfo;
    }

    /**
     * Deletes a subscription. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780740
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     *
     * @return none
     */
    public function deleteSubscription($topicPath, $subscriptionName) 
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_DELETE);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $subscriptionPath = sprintf(
            Resources::SUBSCRIPTION_PATH,
            $topicPath,
            $subscriptionName
        );
        $httpCallContext->setPath($subscriptionPath);
        $this->sendContext($httpCallContext);
    }
    
    /**
     * Gets a subscription. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780741
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     *
     * @return SubscriptionInfo
     */
    public function getSubscription($topicPath, $subscriptionName) 
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_GET);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $subscriptionPath = sprintf(
            Resources::SUBSCRIPTION_PATH,
            $topicPath,
            $subscriptionName
        );
        $httpCallContext->setPath($subscriptionPath);
        $response         = $this->sendContext($httpCallContext);
        $subscriptionInfo = new SubscriptionInfo();
        $subscriptionInfo->parseXml($response->getBody()); 
        return $subscriptionInfo;
    }

    /**
     * Lists subscription. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780766
     * 
     * @param string                   $topicPath                The path of 
     * the topic.
     * @param ListSubscriptionsOptions $listSubscriptionsOptions The options
     * to list the subscription. 
     *
     * @return ListSubscriptionsResult
     */
    public function listSubscriptions(
        $topicPath, 
        $listSubscriptionsOptions = null
    ) {

        $listSubscriptionsPath   = sprintf(
            Resources::LIST_SUBSCRIPTIONS_PATH, 
            $topicPath
        );
        $response                = $this->_listOptions(
            $listSubscriptionsOptions, 
            $listSubscriptionsPath
        );
        $listSubscriptionsResult = new ListSubscriptionsResult();
        $listSubscriptionsResult->parseXml($response->getBody());
        return $listSubscriptionsResult; 
    }

    /**
     * Creates a rule. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780774
     * 
     * @param string   $topicPath        The path of the topic.
     * @param string   $subscriptionName The name of the subscription. 
     * @param RuleInfo $ruleInfo         The information of the rule.
     *
     * @return RuleInfo
     */
    public function createRule($topicPath, $subscriptionName, $ruleInfo)
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_PUT);
        $httpCallContext->addStatusCode(Resources::STATUS_CREATED);
        $httpCallContext->addHeader(
            Resources::CONTENT_TYPE,
            Resources::ATOM_ENTRY_CONTENT_TYPE
        );
        $rulePath = sprintf(
            Resources::RULE_PATH,
            $topicPath,
            $subscriptionName,
            $ruleInfo->getTitle()
        );

        $ruleDescriptionXml = XmlSerializer::objectSerialize(
            $ruleInfo->getRuleDescription(),
            'RuleDescription'
        );

        $entry   = new Entry();
        $content = new Content($ruleDescriptionXml);
        $content->setType(Resources::XML_CONTENT_TYPE);
        $entry->setContent($content);

        $entry->setAttribute(
            Resources::XMLNS,
            Resources::SERVICE_BUS_NAMESPACE
        );

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $entry->writeXml($xmlWriter); 
        $httpCallContext->setBody($xmlWriter->outputMemory());

        $httpCallContext->setPath($rulePath);
        $response = $this->sendContext($httpCallContext);
        $ruleInfo = new ruleInfo();
        $ruleInfo->parseXml($response->getBody()); 
        return $ruleInfo;
    }

    /**
     * Deletes a rule. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780751
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     * @param string $ruleName         The name of the rule.
     *
     * @return none
     */
    public function deleteRule($topicPath, $subscriptionName, $ruleName) 
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $httpCallContext->setMethod(Resources::HTTP_DELETE);
        $rulePath = sprintf(
            Resources::RULE_PATH,
            $topicPath,
            $subscriptionName,
            $ruleName
        );
        $httpCallContext->setPath($rulePath);
        $this->sendContext($httpCallContext);
    }

    /**
     * Gets a rule. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780772
     * 
     * @param string $topicPath        The path of the topic.
     * @param string $subscriptionName The name of the subscription.
     * @param string $ruleName         The name of the rule.
     *
     * @return RuleInfo
     */
    public function getRule($topicPath, $subscriptionName, $ruleName) 
    {
        $httpCallContext = new HttpCallContext();
        $httpCallContext->setMethod(Resources::HTTP_GET);
        $httpCallContext->addStatusCode(Resources::STATUS_OK);
        $rulePath = sprintf(
            Resources::RULE_PATH,
            $topicPath,
            $subscriptionName,
            $ruleName
        );
        $httpCallContext->setPath($rulePath);
        $response = $this->sendContext($httpCallContext);
        $ruleInfo = new RuleInfo();
        $ruleInfo->parseXml($response->getBody());
        return $ruleInfo;
    }

    /**
     * Lists rules. 
     *
     * @link http://msdn.microsoft.com/en-us/library/windowsazure/hh780732
     * 
     * @param string           $topicPath        The path of the topic.
     * @param string           $subscriptionName The name of the subscription.
     * @param ListRulesOptions $listRulesOptions The options to list the rules.
     *
     * @return ListRuleResult
     */
    public function listRules(
        $topicPath, 
        $subscriptionName, 
        $listRulesOptions = null
    ) {
        $listRulesPath = sprintf(
            Resources::LIST_RULES_PATH,
            $topicPath,
            $subscriptionName
        );

        $response = $this->_listOptions(
            $listRulesOptions, 
            $listRulesPath
        );

        $listRulesResult = new ListRulesResult();
        $listRulesResult->parseXml($response->getBody());
        return $listRulesResult;
    }

}
