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
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Queue\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Holds data for single WindowsAzure queue message.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Queue\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class WindowsAzureQueueMessage
{
    /**
     * GUID value that identifies the message in the queue
     * 
     * @var string
     */
    private $_messageId;
    
    /**
     * insertion date of the message.
     * 
     * @var \DateTime
     */
    private $_insertionDate;
    
    /**
     * expiration date of the message.
     * 
     * @var \DateTime
     */
    private $_expirationDate;
    
    /**
     * The value of PopReceipt is opaque to the client and its only purpose is to 
     * ensure that a message may be deleted with the delete message operation.
     * 
     * @var string
     */
    private $_popReceipt;
    
    /**
     * next visibility time of the message.
     * 
     * @var \DateTime
     */
    private $_timeNextVisible;
    
    /**
     * Dequeues count for this message. Note that this element is returned in the 
     * response body only if the queue was created with version 2009-09-19 of 
     * the Queue service.
     * 
     * @var integer
     */
    private $_dequeueCount;
    
    /**
     * message contents.
     * 
     * @var string
     */
    private $_messageText;
    
    /**
     * Creates WindowsAzureQueueMessage object from parsed XML response of 
     * ListMessages.
     *
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return WindowsAzure\Queue\Models\WindowsAzureQueueMessage.
     */
    public static function createFromListMessages($parsedResponse)
    {
        $timeNextVisible = $parsedResponse['TimeNextVisible'];
        
        $msg  = self::createFromPeekMessages($parsedResponse);
        $date = Utilities::rfc1123ToDateTime($timeNextVisible);
        $msg->setTimeNextVisible($date);
        $msg->setPopReceipt($parsedResponse['PopReceipt']);
        
        return $msg;
    }
    
    /**
     * Creates WindowsAzureQueueMessage object from parsed XML response of
     * PeekMessages.
     *
     * @param array $parsedResponse XML response parsed into array.
     * 
     * @return WindowsAzure\Queue\Models\WindowsAzureQueueMessage.
     */
    public static function createFromPeekMessages($parsedResponse)
    {
        $msg            = new WindowsAzureQueueMessage();
        $expirationDate = $parsedResponse['ExpirationTime'];
        $insertionDate  = $parsedResponse['InsertionTime'];
        
        $msg->setDequeueCount(intval($parsedResponse['DequeueCount']));
        
        $date = Utilities::rfc1123ToDateTime($expirationDate);
        $msg->setExpirationDate($date);
        
        $date = Utilities::rfc1123ToDateTime($insertionDate);
        $msg->setInsertionDate($date);
        
        $msg->setMessageId($parsedResponse['MessageId']);
        $msg->setMessageText($parsedResponse['MessageText']);
        
        return $msg;
    }
    
    /**
     * Gets message text field.
     * 
     * @return string.
     */
    public function getMessageText()
    {
        return $this->_messageText;
    }
    
    /**
     * Sets message text field.
     * 
     * @param string $messageText message contents.
     * 
     * @return none.
     */
    public function setMessageText($messageText)
    {
        $this->_messageText = $messageText;
    }
    
    /**
     * Gets messageId field.
     * 
     * @return integer.
     */
    public function getMessageId()
    {
        return $this->_messageId;
    }
    
    /**
     * Sets messageId field.
     * 
     * @param string $messageId message contents.
     * 
     * @return none.
     */
    public function setMessageId($messageId)
    {
        $this->_messageId = $messageId;
    }
    
    /**
     * Gets insertionDate field.
     * 
     * @return \DateTime.
     */
    public function getInsertionDate()
    {
        return $this->_insertionDate;
    }
    
    /**
     * Sets insertionDate field.
     * 
     * @param \DateTime $insertionDate message contents.
     * 
     * @return none.
     */
    public function setInsertionDate($insertionDate)
    {
        $this->_insertionDate = $insertionDate;
    }
    
    /**
     * Gets expirationDate field.
     * 
     * @return \DateTime.
     */
    public function getExpirationDate()
    {
        return $this->_expirationDate;
    }
    
    /**
     * Sets expirationDate field.
     * 
     * @param \DateTime $expirationDate the expiration date of the message.
     * 
     * @return none.
     */
    public function setExpirationDate($expirationDate)
    {
        $this->_expirationDate = $expirationDate;
    }
    
    /**
     * Gets timeNextVisible field.
     * 
     * @return \DateTime.
     */
    public function getTimeNextVisible()
    {
        return $this->_timeNextVisible;
    }
    
    /**
     * Sets timeNextVisible field.
     * 
     * @param \DateTime $timeNextVisible next visibile time for the message.
     * 
     * @return none.
     */
    public function setTimeNextVisible($timeNextVisible)
    {
        $this->_timeNextVisible = $timeNextVisible;
    }
    
    /**
     * Gets popReceipt field.
     * 
     * @return string.
     */
    public function getPopReceipt()
    {
        return $this->_popReceipt;
    }
    
    /**
     * Sets popReceipt field.
     * 
     * @param string $popReceipt used when deleting the message.
     * 
     * @return none.
     */
    public function setPopReceipt($popReceipt)
    {
        $this->_popReceipt = $popReceipt;
    }
    
    /**
     * Gets dequeueCount field.
     * 
     * @return integer.
     */
    public function getDequeueCount()
    {
        return $this->_dequeueCount;
    }
    
    /**
     * Sets dequeueCount field.
     * 
     * @param integer $dequeueCount number of dequeues for that message.
     * 
     * @return none.
     */
    public function setDequeueCount($dequeueCount)
    {
        $this->_dequeueCount = $dequeueCount;
    }
}


