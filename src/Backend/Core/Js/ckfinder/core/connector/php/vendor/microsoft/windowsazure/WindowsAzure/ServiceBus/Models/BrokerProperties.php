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
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;

/**
 * The properties of the broker of a brokered message. 
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class BrokerProperties
{
    /**
     * The correlation ID.
     *
     * @var string
     */
    private $_correlationId;

    /**
     * The session ID.
     *
     * @var string
     */
    private $_sessionId;

    /**
     * The delivery count.
     *
     * @var integer
     */
    private $_deliveryCount;

    /**
     * The locked until time.
     *
     * @var \DateTime
     */
    private $_lockedUntilUtc;

    /**
     * The lock token.
     *
     * @var string
     */
    private $_lockToken;

    /**
     * The message Id.
     *
     * @var string
     */
    private $_messageId;

    /**
     * The label.
     *
     * @var string
     */
    private $_label;    

    /**
     * The reply to.
     *
     * @var string
     */
    private $_replyTo;

    /**
     * The sequence number.
     *
     * @var string
     */
    private $_sequenceNumber;

    /**
     * The time to live.
     *
     * @var float
     */
    private $_timeToLive;

    /**
     * The to. 
     *
     * @var string
     */
    private $_to;

    /**
     * The scheduled enqueue time. 
     *
     * @var \DateTime
     */
    private $_scheduledEnqueueTimeUtc;

    /**
     * The reply to session ID.
     *
     * @var string
     */
    private $_replyToSessionId;

    /**
     * The location of the message.
     *
     * @var string
     */
    private $_messageLocation;

    /**
     * The location of the lock.
     *
     * @var string
     */
    private $_lockLocation;
 

    /**
     * Creates a broker properties instance with default parameters. 
     */
    public function __construct()
    {
    }

    // @codingStandardsIgnoreStart
    
    /**
     * Creates a broker properties instance with specified JSON message.  
     *
     * @param string $brokerPropertiesJson A JSON message representing a 
     * broker properties.
     * 
     * @return none
     */
    public static function create($brokerPropertiesJson)
    {        
        Validate::isString($brokerPropertiesJson, 'brokerPropertiesJson');

        $brokerProperties = new BrokerProperties();

        $brokerPropertiesArray = (array)json_decode($brokerPropertiesJson);
        
        if (array_key_exists('CorrelationId', $brokerPropertiesArray)) {
            $brokerProperties->setCorrelationId(
                $brokerPropertiesArray['CorrelationId']
            );
        }

        if (array_key_exists('SessionId', $brokerPropertiesArray)) {
            $brokerProperties->setSessionId(
                $brokerPropertiesArray['SessionId']
            );
        }

        if (array_key_exists('DeliveryCount', $brokerPropertiesArray)) {
            $brokerProperties->setDeliveryCount(
                $brokerPropertiesArray['DeliveryCount']
            );
        }

        if (array_key_exists('LockedUntilUtc', $brokerPropertiesArray)) {
            $brokerProperties->setLockedUntilUtc(
                \DateTime::createFromFormat(
                    Resources::AZURE_DATE_FORMAT,
                    $brokerPropertiesArray['LockedUntilUtc']
                )
            );
        }

        if (array_key_exists('LockToken', $brokerPropertiesArray)) {
            $brokerProperties->setLockToken(
                $brokerPropertiesArray['LockToken']
            );
        }

        if (array_key_exists('MessageId', $brokerPropertiesArray)) {
            $brokerProperties->setMessageId(
                $brokerPropertiesArray['MessageId']
            );
        }

        if (array_key_exists('Label', $brokerPropertiesArray)) {
            $brokerProperties->setLabel($brokerPropertiesArray['Label']);
        }

        if (array_key_exists('ReplyTo', $brokerPropertiesArray)) {
            $brokerProperties->setReplyTo($brokerPropertiesArray['ReplyTo']);
        }

        if (array_key_exists('SequenceNumber', $brokerPropertiesArray)) {
            $brokerProperties->setSequenceNumber(
                $brokerPropertiesArray['SequenceNumber']
            );
        }

        if (array_key_exists('TimeToLive', $brokerPropertiesArray)) {
            $brokerProperties->setTimeToLive(
                doubleval($brokerPropertiesArray['TimeToLive'])
            );
        }

        if (array_key_exists('To', $brokerPropertiesArray)) {
            $brokerProperties->setTo($brokerPropertiesArray['To']);
        }

        if (array_key_exists(
            'ScheduledEnqueueTimeUtc', 
            $brokerPropertiesArray
        )
        ) {
            $brokerProperties->setScheduledEnqueueTimeUtc(
                \DateTime::createFromFormat(
                    Resources::AZURE_DATE_FORMAT,
                    $brokerPropertiesArray['ScheduledEnqueueTimeUtc']
                )
            );
        }

        if (array_key_exists('ReplyToSessionId', $brokerPropertiesArray)) {
            $brokerProperties->setReplyToSessionId(
                $brokerPropertiesArray['ReplyToSessionId']
            );
        }

        if (array_key_exists('MessageLocation', $brokerPropertiesArray)) {
            $brokerProperties->setMessageLocation(
                $brokerPropertiesArray['MessageLocation']
            );
        }

        if (array_key_exists('LockLocation', $brokerPropertiesArray)) {
            $brokerProperties->setLockLocation(
                $brokerPropertiesArray['LockLocation']
            );
        }

        return $brokerProperties;
    }
    
    // @codingStandardsIgnoreEnd

    /**
     * Gets the correlation ID. 
     *
     * @return string 
     */
    public function getCorrelationId()
    {
        return $this->_correlationId;
    }

    /**
     * Sets the correlation ID.
     * 
     * @param string $correlationId The correlation ID. 
     * 
     * @return none
     */ 
    public function setCorrelationId($correlationId)
    {
        $this->_correlationId = $correlationId;
    }

    /**
     * Gets the session ID. 
     * 
     * @return string 
     */
    public function getSessionId()
    {
        return $this->_sessionId;
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
        $this->_sessionId = $sessionId;
    }

    /** 
     * Gets the delivery count. 
     * 
     * @return integer
     */
    public function getDeliveryCount()
    {
        return $this->_deliveryCount;
    }

    /** 
     * Sets the delivery count. 
     * 
     * @param integer $deliveryCount The count of the delivery. 
     * 
     * @return none
     */
    public function setDeliveryCount($deliveryCount)
    {
        $this->_deliveryCount = $deliveryCount;
    }

    /**
     * Gets the locked until time. 
     * 
     * @return string
     */
    public function getLockedUntilUtc()
    {
        return $this->_lockedUntilUtc;
    }

    /**
     * Sets the locked until time. 
     * 
     * @param string $lockedUntilUtc The locked until time. 
     * 
     * @return none
     */
    public function setLockedUntilUtc($lockedUntilUtc)
    {
        $this->_lockedUntilUtc = $lockedUntilUtc;
    }

    /** 
     * Gets lock token. 
     * 
     * @return string. 
     */
    public function getLockToken()
    {
        return $this->_lockToken;
    }

    /**
     * Sets the lock token. 
     * 
     * @param string $lockToken The lock token. 
     * 
     * @return none
     */
    public function setLockToken($lockToken)
    {
        $this->_lockToken = $lockToken;
    }

    /**
     * Gets the message ID. 
     * 
     * @return string
     */
    public function getMessageId()
    {
        return $this->_messageId;
    }

    /**
     * Sets the message ID.
     * 
     * @param string $messageId The ID of the message. 
     * 
     * @return none
     */
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
    }

    /**
     * Gets the label. 
     * 
     * @return string 
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Sets the label. 
     * 
     * @param string $label The label of the broker property. 
     * 
     * @return none
     */
    public function setLabel($label)
    {
        $this->_label = $label;
    }

    /**
     * Gets the reply to. 
     * 
     * @return string 
     */
    public function getReplyTo()
    {
        return $this->_replyTo;
    }

    /** 
     * Sets the reply to. 
     *
     * @param string $replyTo The reply to. 
     * 
     * @return none
     */
    public function setReplyTo($replyTo)
    {
        $this->_replyTo = $replyTo;
    }

    /**
     * Gets the sequence number. 
     * 
     * @return integer
     */
    public function getSequenceNumber()
    {
        return $this->_sequenceNumber;
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
        $this->_sequenceNumber = $sequenceNumber;
    }

    /**
     * Gets time to live. 
     * 
     * @return string 
     */
    public function getTimeToLive()
    {
        return $this->_timeToLive;
    }

    /**
     * Sets time to live. 
     * 
     * @param string $timeToLive The time to live. 
     * 
     * @return none
     */
    public function setTimeToLive($timeToLive)
    {
        $this->_timeToLive = $timeToLive;
    }

    /** 
     * Gets to. 
     * 
     * @return string 
     */
    public function getTo()
    {
        return $this->_to;
    }

    /** 
     * Sets to. 
     * 
     * @param string $to To.
     * 
     * @return none
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }

    /**
     * Gets scheduled enqueue time UTC. 
     * 
     * @return string
     */
    public function getScheduledEnqueueTimeUtc()
    {
        return $this->_scheduledEnqueueTimeUtc;
    }

    /**
     * Sets scheduled enqueue time UTC. 
     * 
     * @param string $scheduledEnqueueTimeUtc The scheduled enqueue time. 
     * 
     * @return none
     */
    public function setScheduledEnqueueTimeUtc($scheduledEnqueueTimeUtc)
    {
        $this->_scheduledEnqueueTimeUtc = $scheduledEnqueueTimeUtc;
    }

    /** 
     * Gets reply to session ID. 
     * 
     * @return string
     */
    public function getReplyToSessionId()
    {
        return $this->_replyToSessionId;
    }

    /**
     * Sets reply to session. 
     * 
     * @param string $replyToSessionId reply to session. 
     * 
     * @return none
     */
    public function setReplyToSessionId($replyToSessionId)
    {
        $this->_replyToSessionId = $replyToSessionId;
    }

    /**
     * Gets message location. 
     * 
     * @return string 
     */
    public function getMessageLocation()
    {
        return $this->_messageLocation;
    }

    /** 
     * Sets the location of the message. 
     * 
     * @param string $messageLocation The location of the message. 
     * 
     * @return none
     */
    public function setMessageLocation($messageLocation)
    {
        $this->_messageLocation = $messageLocation;
    }

    /** 
     * Gets the location of the lock. 
     * 
     * @return string 
     */
    public function getLockLocation()
    {
        return $this->_lockLocation;
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
        $this->_lockLocation = $lockLocation;
    }
    
    /**
     * Gets a string representing the broker property. 
     *
     * @return string
     */
    public function toString()
    {
        $value = array();
        
        $this->setValueArrayString(
            $value, 
            'CorrelationId',
            $this->_correlationId
        );

        $this->setValueArrayString(
            $value, 
            'SessionId',
            $this->_sessionId
        );

        $this->setValueArrayInt(
            $value, 
            'DeliveryCount',
            $this->_deliveryCount
        );

        $this->setValueArrayDateTime(
            $value, 
            'LockedUntilUtc',
            $this->_lockedUntilUtc
        );

        $this->setValueArrayString(
            $value,
            'LockToken',
            $this->_lockToken
        );

        $this->setValueArrayString(
            $value,
            'MessageId',
            $this->_messageId
        );

        $this->setValueArrayString(
            $value,
            'Label',
            $this->_label
        );

        $this->setValueArrayString(
            $value,
            'ReplyTo',
            $this->_replyTo
        );

        $this->setValueArrayString(
            $value,
            'SequenceNumber',
            $this->_sequenceNumber
        );

        $this->setValueArrayFloat(
            $value,
            'TimeToLive',
            $this->_timeToLive
        );

        $this->setValueArrayString(
            $value,
            'To',
            $this->_to
        );

        $this->setValueArrayDateTime(
            $value,
            'ScheduledEnqueueTimeUtc',
            $this->_scheduledEnqueueTimeUtc
        );

        $this->setValueArrayString(
            $value,
            'ReplyToSessionId',
            $this->_replyToSessionId
        );

        $this->setValueArrayString(
            $value,
            'MessageLocation',
            $this->_messageLocation
        );

        $this->setValueArrayString(
            $value,
            'LockLocation',
            $this->_lockLocation
        );

        $result = json_encode($value);
        return $result; 
    }    

    /** 
     * Sets a string in an array. 
     *
     * @param array  &$valueArray The array of a set of values. 
     * @param string $key         The key of the key value pair. 
     * @param string $value       The value of the key value pair. 
     * 
     * @return none
     */
    public function setValueArrayString(&$valueArray, $key, $value)
    {
        Validate::isArray($valueArray, 'valueArray');
        Validate::isString($key, 'key');

        if (!empty($value)) {
            Validate::isString($value, 'value');
            $valueArray[$key] = $value;
        }
    }

    /** 
     * Sets an integer value in an array. 
     *
     * @param array  &$valueArray The array of a set of values. 
     * @param string $key         The key of the key value pair. 
     * @param int    $value       The value of the key value pair. 
     * 
     * @return none
     */
    public function setValueArrayInt(&$valueArray, $key, $value)
    {
        Validate::isArray($valueArray, 'valueArray');
        Validate::isString($key, 'key');

        if (!empty($value)) {
            Validate::isInteger($value, 'value');
            $valueArray[$key] = $value;
        }
    }

    /**
     * Sets a float value in an array. 
     *
     * @param array  &$valueArray The array of a set of values. 
     * @param string $key         The key of the key value pair. 
     * @param float  $value       The value of the key value pair. 
     * 
     * @return none
     */
    public function setValueArrayFloat(&$valueArray, $key, $value)
    {
        Validate::isArray($valueArray, 'valueArray');
        Validate::isString($key, 'key');

        if (!empty($value)) {
            Validate::isDouble($value, 'value');
            $valueArray[$key] = (double)$value;
        }
    }

    /**
     * Sets a DateTime value in an array. 
     *
     * @param array     &$valueArray The array of a set of values. 
     * @param string    $key         The key of the key value pair. 
     * @param \DateTime $value       The value of the key value pair. 
     * 
     * @return none
     */
    public function setValueArrayDateTime(&$valueArray, $key, $value)
    {
        Validate::isArray($valueArray, 'valueArray');
        Validate::isString($key, 'key');

        if (!empty($value)) {
            Validate::isDate($value, 'value');
            $valueArray[$key] = gmdate(
                Resources::AZURE_DATE_FORMAT, 
                $value->getTimestamp()
            );
        }
    }
}