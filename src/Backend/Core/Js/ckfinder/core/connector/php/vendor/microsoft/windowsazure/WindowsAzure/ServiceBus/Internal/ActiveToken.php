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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\ServiceBus\Internal\WrapAccessTokenResult;
use WindowsAzure\Common\Internal\Utilities;

/**
 * An active WRAP access Token.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class ActiveToken
{
    /** 
     * The WRAP access token result. 
     * 
     * @var WrapAccessTokenResult
     */
    private $_wrapAccessTokenResult;

    /** 
     * When the WRAP access token expires. 
     * 
     * @var \DateTime
     */
    private $_expirationDateTime;

    /**
     * Creates an ActiveToken with specified WRAP 
     * access token result.
     *
     * @param array $wrapAccessTokenResult A WRAP access token result.
     */
    public function __construct($wrapAccessTokenResult)
    {
        $this->_wrapAccessTokenResult = $wrapAccessTokenResult;
    }

    /**
     * Gets WRAP access token.
     *
     * @return WrapAccessTokenResult
     */
    public function getWrapAccessTokenResult()
    {
        return $this->_wrapAccessTokenResult;
    }
    
    /**
     * Sets WRAP access token.
     *
     * @param string $wrapAccessTokenResult The WRAP access token result.
     * 
     * @return none
     */
    public function setWrapAccessTokenResult($wrapAccessTokenResult)
    {
        $this->_wrapAccessTokenResult = $wrapAccessTokenResult;
    }

    /**
     * Gets expiration time.  
     *
     * @return \DateTime
     */
    public function getExpirationDateTime()
    {
        return $this->_expirationDateTime;
    }

    /**
     * Sets expiration time.
     *
     * @param \DateTime $expirationDateTime value.
     * 
     * @return none
     */
    public function setExpirationDateTime($expirationDateTime)
    {
        $this->_expirationDateTime = $expirationDateTime;
    }
}

