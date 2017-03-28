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
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceBus\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\ServiceBus\Models\BrokerProperties;

/**
 * A class representing the brokered message of Windows Azure Service Bus.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class BrokeredMessage
{
    /**
     * The properties of the broker.
     * 
     * @var BrokerProperties
     */
    private $_brokerProperties;

    /**
     * The body of the brokered message.
     * 
     * @var string
     */
    private $_body;

    /**
     * The content type of the brokered message.
     * 
     * @var string
     */
    private $_contentType;

    /**
     * The date of the brokered message.
     * 
     * @var \DateTime 
     */
    private $_date;

    /**
     * The properties of the message that are customized.
     * 
     * @var array
     */
    private $_customProperties; 

    /**
     * Creates a brokered message with specified broker properties. 
     *  
     * @param string $body The body of the message. 
     */
    public function __construct($body = Resources::EMPTY_STRING)
    {
        Validate::isString($body, 'body');
        $this->_body             = $body;
        $this->_brokerProperties = new BrokerProperties();
        $this->_customProperties = array();
    }

    /** 
     * Gets the broker properties.
     *
     * @return BrokerProperties
     */
    public function getBrokerProperties()
    {
        return $this->_brokerProperties;
    }

    /** 
     * Sets the broker properties.
     * 
     * @param BrokerProperties $brokerProperties The properties of broker.
     * 
     * @return none
     */
    public function setBrokerProperties($brokerProperties)
    {
        $this->_brokerProperties = $brokerProperties;
    }
   
    /**
     * Gets the body of the brokered message. 
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    } 
    
    /**
     * Sets the body of the brokered message. 
     * 
     * @param string $body The body of the brokered message.
     *
     * @return none
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    /**
     * Gets the content type of the brokered message. 
     * 
     * @return string
     */
    public function getContentType()
    {
        return $this->_contentType;
    } 

    /**
     * Sets the content type of the brokered message. 
     * 
     * @param string $contentType The content type of 
     * the brokered message. 
     * 
     * @return none
     */ 
    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
    } 

    /**
     * Gets the date of the brokered message.
     * 
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->_date;
    }
    
    /** 
     * Sets the date of the brokered message. 
     * 
     * @param \DateTime $date Sets the date of the brokered message. 
     * 
     * @return none
     */
    public function setDate($date)
    {
        $this->_date = $date;
    }
    
    /**
     * Gets the value of a custom property. 
     *
     * @param string $propertyName The name of the property. 
     * 
     * @return string
     */
    public function getProperty($propertyName)
    {
        Validate::isString($propertyName, 'propertyName');
        return $this->_customProperties[strtolower($propertyName)];
    } 

    /**
     * Sets the value of a custom property. 
     * 
     * @param string $propertyName  The name of the property.
     * @param mixed  $propertyValue The value of the property.
     * 
     * @return none
     */
    public function setProperty($propertyName, $propertyValue)
    {
        Validate::isString($propertyName, 'propertyName');
        Validate::notNull($propertyValue, 'propertyValue');

        $this->_customProperties[strtolower($propertyName)] = $propertyValue;
    }

    /**
     * Gets the custom properties. 
     *
     * @return array 
     */
    public function getProperties()
    {
        return $this->_customProperties;
    }

    /**
     * Gets the delivery count. 
     * 
     * @return integer
     */
    public function getDeliveryCount()
    {
        return $this->_brokerProperties->getDeliveryCount();
    }

    /**
     * Sets the delivery count.
     * 
     * @param integer $deliveryCount The times that the message has been delivered. 
     * 
     * @return none
     */
    public function setDeliveryCount($deliveryCount)
    {
        $this->_brokerProperties->setDeliveryCount($deliveryCount);
    }

    /**
     * Gets the ID of the message. 
     * 
     * @return string 
     */
    public function getMessageId()
    {
        return $this->_brokerProperties->getMessageId();
    }
    
    /**
     * Sets the ID of the message. 
     * 
     * @param string $messageId The ID of the message. 
     *
     * @return none
     */
    public function setMessageId($messageId)
    {
        $this->_brokerProperties->setMessageId($messageId);
    }
    
    /** 
     * Gets the sequence number. 
     * 
     * @return integer
     */
    public function getSequenceNumber()
    {
        return $this->_brokerProperties->getSequenceNumber();
    }

    /**
     * Sets the sequence number. 
     * 
     * @param integer $sequenceNumber The sequence number. 
     * 
     * @return none
     */
    public function setSequenceNumber($sequenceNumber)
    {
        $this->_brokerProperties->setSequenceNumber($sequenceNumber);
    } 

    /** 
     * Gets the time to live. 
     * 
     * @return string
     */
    public function getTimeToLive()
    {
        return $this->_brokerProperties->getTimeToLive();
    }

    /**
     * Sets the time to live. 
     * 
     * @param string $timeToLive The time to live. 
     *
     * @return none
     */ 
    public function setTimeToLive($timeToLive)
    {
        $this->_brokerProperties->setTimeToLive($timeToLive);
    }

    /**
     * Gets the lock token. 
     * 
     * @return string 
     */
    public function getLockToken()
    {
        return $this->_brokerProperties->getLockToken();
    }

    /**
     * Sets the lock token. 
     * 
     * @param string $lockToken The token of the lock. 
     * 
     * @return none
     */
    public function setLockToken($lockToken)
    {
        $this->_brokerProperties->setLockToken($lockToken);
    }

    /**
     * Gets the time of locked until UTC.
     * 
     * @return string
     */
    public function getLockedUntilUtc()
    {
        return $this->_brokerProperties->getLockedUntilUtc();
    }

    /**
     * Sets the time of locked until UTC. 
     * 
     * @param string $lockedUntilUtc The time of locked until UTC.
     * 
     * @return none
     */ 
    public function setLockedUntilUtc($lockedUntilUtc)
    {
        $this->_brokerProperties->setLockedUntilUtc($lockedUntilUtc);
    }

    /**
     * Gets the correlation ID.
     * 
     * @return string 
     */
    public function getCorrelationId()
    {
        return $this->_brokerProperties->getCorrelationId();
    }
    
    /**     
     * Sets the correlation ID.
     * 
     * @param string $correlationId The ID of the correlation.
     * 
     * @return none
     */
    public function setCorrelationId($correlationId)
    {
        $this->_brokerProperties->setCorrelationId($correlationId);
    }

    /**
     * Gets the session ID.
     * 
     * @return string
     */
    public function getSessionId()
    {
        return $this->_brokerProperties->getSessionId();
    }
    
    /**
     * Sets the session ID.
     * 
     * @param string $sessionId The ID of the session. 
     * 
     * @return none
     */
    public function setSessionId($sessionId)
    {
        $this->_brokerProperties->setSessionId($sessionId);
    }

    /**
     * Gets the label.
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->_brokerProperties->getLabel();
    }

    /**
     * Sets the label. 
     * 
     * @param string $label The label of the broker properties. 
     *
     * @return none 
     */
    public function setLabel($label)
    {
        $this->_brokerProperties->setLabel($label);
    }

    /**
     * Gets reply to. 
     * 
     * @return string 
     */
    public function getReplyTo()
    {
        return $this->_brokerProperties->getReplyTo();
    }

    /**
     * Sets the reply to. 
     * 
     * @param string $replyTo The reply to value. 
     *
     * @return none
     */
    public function setReplyTo($replyTo)
    {
        $this->_brokerProperties->setReplyTo($replyTo);
    }

    /**
     * Gets to.     
     * 
     * @return string
     */
    public function getTo()
    {
        return $this->_brokerProperties->getTo();
    } 

    /**
     * Sets the to.
     * 
     * @param string $to to.
     *
     * @return none
     */
    public function setTo($to)
    {
        $this->_brokerProperties->setTo($to);
    }

    /**
     * Gets the scheduled enqueue time. 
     * 
     * @return string
     */
    public function getScheduledEnqueueTimeUtc()
    {
        return $this->_brokerProperties->getScheduledEnqueueTimeUtc();
    }
    
    /**
     * Sets the scheduled enqueue time. 
     * 
     * @param string $scheduledEnqueueTime The date/time of the message.
     *
     * @return none
     */
    public function setScheduledEnqueueTimeUtc($scheduledEnqueueTime)
    {
        $this->_brokerProperties->setScheduledEnqueueTimeUtc($scheduledEnqueueTime);
    }

    /**
     * Gets the reply to session ID. 
     * 
     * @return string 
     */
    public function getReplyToSessionId()
    {
        return $this->_brokerProperties->getReplyToSessionId();
    }

    /**
     * Sets the reply to session ID.
     * 
     * @param string $replyToSessionId The session ID of the reply to recipient. 
     * 
     * @return none
     */
    public function setReplyToSessionId($replyToSessionId)
    {
        $this->_brokerProperties->setReplyToSessionId($replyToSessionId);
    }
    
    /**
     * Gets the message location.
     * 
     * @return string 
     */
    public function getMessageLocation()
    {
        return $this->_brokerProperties->getMessageLocation();
    }

    /**
     * Sets the message location.
     *
     * @param string $messageLocation The location of the message. 
     *
     * @return none
     */
    public function setMessageLocation($messageLocation)
    {
        $this->_brokerProperties->setMessageLocation($messageLocation);
    }
    

    /**
     * Gets the location of the lock.
     * 
     * @return string
     */
    public function getLockLocation()
    {
        return $this->_brokerProperties->getLockLocation();
    }

    /**
     * Sets the location of the lock.
     * 
     * @param string $lockLocation The location of the lock.
     * 
     * @return none
     */
    public function setLockLocation($lockLocation)
    {
        $this->_brokerProperties->setLockLocation($lockLocation);
    }
    
}


