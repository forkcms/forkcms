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

use WindowsAzure\Common\Internal\Atom\Entry;
use WindowsAzure\Common\Internal\Atom\Content;
use WindowsAzure\Common\Internal\Serialization\XmlSerializer;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\ServiceBus\Models\SubscriptionDescription;

/**
 * The information of a subscription.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class SubscriptionInfo extends Entry
{
    /**
     * The entry of the subscription info.
     *
     * @var Entry 
     */
    private $_entry;

    /** 
     * The description of the subscription. 
     * 
     * @var SubscriptionDescription
     */
    private $_subscriptionDescription;

    /**
     * Creates a SubscriptionInfo instance with specified parameters.
     *
     * @param string                  $title                   The title of 
     * the subscription.
     * @param SubscriptionDescription $subscriptionDescription The description 
     * of the subscription.
     */
    public function __construct(
        $title = Resources::EMPTY_STRING, 
        $subscriptionDescription = null
    ) {
        Validate::isString($title, 'title');
        if (is_null($subscriptionDescription)) {
            $subscriptionDescription = new SubscriptionDescription();
        }
        $this->_subscriptionDescription = $subscriptionDescription;
        $this->_entry                   = new Entry();
        $this->_entry->setTitle($title);
        $this->_entry->setAttribute(
            Resources::XMLNS,
            Resources::SERVICE_BUS_NAMESPACE
        );
    }

    /**
     * Populates the properties of the subscription info instance with a XML string. 
     * 
     * @param string $entryXml A XML string representing a subscription 
     * information instance.
     * 
     * @return none
     */
    public function parseXml($entryXml)
    {
        $this->_entry->parseXml($entryXml);
        $content = $this->_entry->getContent();
        if (is_null($content)) {
            $this->_subscriptionDescription = null;
        } else {
            $this->_subscriptionDescription = SubscriptionDescription::create(
                $content->getText()
            );
        }
    }

    /**
     * Writes XML based on the subscription information. 
     * 
     * @param XMLWriter $xmlWriter The XML writer. 
     * 
     * @return none
     */
    public function writeXml($xmlWriter)
    {
        $content = null;
        if (!is_null($this->_subscriptionDescription)) {
            $content = new Content();
            $content->setText(
                XmlSerializer::objectSerialize(
                    $this->_subscriptionDescription,
                    'SubscriptionDescription'
                )
            );
        }
        $this->_entry->setContent($content);
        $this->_entry->writeXml($xmlWriter);
    }

    /**
     * Gets the entry of the subscription info.
     *
     * @return Entry
     */
    public function getEntry()
    {
        return $this->_entry;
    }

    /**
     * Sets the entry of the subscription info.
     * 
     * @param Entry $entry The entry of the subscription info.
     *
     * @return none
     */
    public function setEntry($entry)
    {
        $this->_entry = $entry;
    }
    
    /**
     * Gets the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_entry->getTitle();
    }

    /**
     * Sets the title.
     *
     * @param string $title The title of the queue info.
     *
     * @return none
     */
    public function setTitle($title)
    {
        $this->_entry->setTitle($title);
    }

    /**
     * Gets the subscription description. 
     *
     * @return none
     */
    public function getSubscriptionDescription()
    {
        return $this->_subscriptionDescription;
    }

    /**
     * Sets the subscription description. 
     * 
     * @param string $subscriptionDescription The description of 
     * the subscription. 
     * 
     * @return none
     */
    public function setSubscriptionDescription($subscriptionDescription)
    {
        $this->_subscriptionDescription = $subscriptionDescription;
    }
    
    /**
     * Gets the lock duration.
     *
     * @return integer
     */
    public function getLockDuration()
    {
        return $this->_subscriptionDescription->getLockDuration();
    }
    
    /**
     * Sets the lock duration.
     *
     * @param string $lockDuration The duration of the lock. 
     * 
     * @return none
     */
    public function setLockDuration($lockDuration)
    {
        $this->_subscriptionDescription->setlockDuration($lockDuration);
    }

    /**
     * Gets requires session.
     * 
     * @return boolean
     */
    public function getRequiresSession()
    {
        return $this->_subscriptionDescription->getRequiresSession();
    }

    /**
     * Sets the requires session.
     * 
     * @param boolean $requiresSession The requires session. 
     * 
     * @return none
     */
    public function setRequiresSession($requiresSession)
    {
        $this->_subscriptionDescription->setRequiresSession($requiresSession);
    }

    /**
     * Gets default message time to live. 
     * 
     * @return string 
     */
    public function getDefaultMessageTimeToLive()
    {
        return $this->_subscriptionDescription->getDefaultMessageTimeToLive();
    }

    /**
     * Sets default message time to live. 
     * 
     * @param string $defaultMessageTimeToLive The default message time to live. 
     * 
     * @return none
     */
    public function setDefaultMessageTimeToLive($defaultMessageTimeToLive)
    {   
        $this->_subscriptionDescription->setDefaultMessageTimeToLive(
            $defaultMessageTimeToLive
        );
    }

    /**
     * Gets dead lettering on message expiration. 
     * 
     * @return string
     */
    public function getDeadLetteringOnMessageExpiration()
    {
        $subscriptionDesc = $this->_subscriptionDescription;
        return $subscriptionDesc->getDeadLetteringOnMessageExpiration();
    }

    /** 
     * Sets dead lettering on message expiration.
     * 
     * @param string $deadLetteringOnMessageExpiration The dead lettering 
     * on message expiration.
     * 
     * @return none
     */
    public function setDeadLetteringOnMessageExpiration(
        $deadLetteringOnMessageExpiration
    ) {
        $this->_subscriptionDescription->setDeadLetteringOnMessageExpiration(
            $deadLetteringOnMessageExpiration
        );
    }

    /**
     * Gets dead lettering on filter evaluation exceptions. 
     * 
     * @return string 
     */
    public function getDeadLetteringOnFilterEvaluationExceptions()
    {
        $subscriptionDesc = $this->_subscriptionDescription;
        return $subscriptionDesc->getDeadLetteringOnFilterEvaluationExceptions();
    }

    /**
     * Sets dead lettering on filter evaluation exceptions. 
     * 
     * @param string $deadLetteringOnFilterEvaluationExceptions Sets dead lettering 
     * on filter evaluation exceptions. 
     * 
     * @return none
     */
    public function setDeadLetteringOnFilterEvaluationExceptions(
        $deadLetteringOnFilterEvaluationExceptions
    ) {
        $subscriptionDesc = $this->_subscriptionDescription;
        $subscriptionDesc->setdeadLetteringOnFilterEvaluationExceptions(
            $deadLetteringOnFilterEvaluationExceptions
        );
    }

    /**
     * Gets the default rule description. 
     * 
     * @return RuleDescription 
     */
    public function getDefaultRuleDescription()
    {
        return $this->_subscriptionDescription->getDefaultRuleDescription();
    }

    /**
     * Sets the default rule description.
     * 
     * @param string $defaultRuleDescription The default rule description. 
     *
     * @return none
     */
    public function setDefaultRuleDescription($defaultRuleDescription)
    {
        $this->_subscriptionDescription->setDefaultRuleDescription(
            $defaultRuleDescription
        );
    }

    /**
     * Gets the count of the message. 
     * 
     * @return integer
     */
    public function getMessageCount()
    {
        return $this->_subscriptionDescription->getMessageCount();
    } 

    /**
     * Sets the count of the message.
     * 
     * @param string $messageCount The count of the message. 
     *
     * @return none
     */
    public function setMessageCount($messageCount)
    {
        $this->_subscriptionDescription->setMessageCount($messageCount);
    }

    /**
     * Gets maximum delivery count.
     * 
     * @return integer
     */
    public function getMaxDeliveryCount()
    {
        return $this->_subscriptionDescription->getMaxDeliveryCount();
    }

    /**
     * Sets maximum delivery count. 
     * 
     * @param integer $maxDeliveryCount The maximum delivery count. 
     *
     * @return none
     */
    public function setMaxDeliveryCount($maxDeliveryCount)
    {
        $this->_subscriptionDescription->setmaxDeliveryCount($maxDeliveryCount);
    }

    /**
     * Gets enable batched operations. 
     * 
     * @return boolean
     */
    public function getEnableBatchedOperations()
    {
        return $this->_subscriptionDescription->getEnableBatchedOperations();
    }

    /**
     * Sets enable batched operations. 
     * 
     * @param boolean $enableBatchedOperations Enable batched operations. 
     * 
     * @return none
     */
    public function setEnableBatchedOperations($enableBatchedOperations)
    {
        $this->_subscriptionDescription->setEnableBatchedOperations(
            $enableBatchedOperations
        );
    }
}
