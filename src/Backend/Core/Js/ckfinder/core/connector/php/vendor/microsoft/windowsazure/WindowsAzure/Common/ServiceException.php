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
 * @package   WindowsAzure\Common
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common;
use WindowsAzure\Common\Internal\Resources;

/**
 * Fires when the response code is incorrect.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ServiceException extends \LogicException
{
    private $_error;
    private $_reason;
    
    /**
     * Constructor
     *
     * @param string $errorCode status error code.
     * @param string $error     string value of the error code.
     * @param string $reason    detailed message for the error.
     * 
     * @return WindowsAzure\Common\ServiceException
     */
    public function __construct($errorCode, $error = null, $reason = null)
    {
        parent::__construct(
            sprintf(Resources::AZURE_ERROR_MSG, $errorCode, $error, $reason)
        );
        $this->code    = $errorCode;
        $this->_error  = $error;
        $this->_reason = $reason;
    }
    
    /**
     * Gets error text.
     *
     * @return string
     */
    public function getErrorText()
    {
        return $this->_error;
    }
    
    /**
     * Gets detailed error reason.
     *
     * @return string
     */
    public function getErrorReason()
    {
        return $this->_reason;
    }
}


