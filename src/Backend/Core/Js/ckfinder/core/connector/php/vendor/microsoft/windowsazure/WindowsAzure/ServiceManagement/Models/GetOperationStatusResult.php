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
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceManagement\Models;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\ServiceException;

/**
 * The result of calling getOperationStatus API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class GetOperationStatusResult
{
    /**
     * @var string
     */
    private $_id;
    
    /**
     * @var string
     */
    private $_status;
    
    /**
     * @var string
     */
    private $_httpStatusCode;
    
    /**
     * @var ServiceException
     */
    private $_error;
    
    /**
     * Creates GetOperationStatusResult object from parsed response.
     * 
     * @param array $parsed The parsed response.
     * 
     * @return GetOperationStatusResult
     */
    public static function create($parsed)
    {
        $result = new GetOperationStatusResult();
        
        $result->_id             = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_ID
        );
        $result->_status         = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_STATUS
        );
        $result->_httpStatusCode = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_HTTP_STATUS_CODE
        );
        $error                   = Utilities::tryGetValue(
            $parsed,
            Resources::XTAG_ERROR
        );
        
        if (!empty($error)) {
            $code    = Utilities::tryGetValue($error, Resources::XTAG_CODE);
            $message = Utilities::tryGetValue($error, Resources::XTAG_MESSAGE);
            
            $result->_error = new ServiceException($code, $message);
        }        
        
        return $result;
    }
    
    /**
     * Gets the id.
     * 
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Sets the id.
     * 
     * @param string $id The id.
     * 
     * @return none
     */
    public function setId($id)
    {
        $this->_id = $id;
    }
    
    /**
     * Gets the status.
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * Sets the status.
     * 
     * @param string $status The status.
     * 
     * @return none
     */
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    /**
     * Gets the httpStatusCode.
     * 
     * @return string
     */
    public function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }
    
    /**
     * Sets the httpStatusCode.
     * 
     * @param string $httpStatusCode The httpStatusCode.
     * 
     * @return none
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->_httpStatusCode = $httpStatusCode;
    }
    
    /**
     * Gets the error.
     * 
     * @return ServiceException
     */
    public function getError()
    {
        return $this->_error;
    }
    
    /**
     * Sets the error.
     * 
     * @param ServiceException $error The error.
     * 
     * @return none
     */
    public function setError($error)
    {
        $this->_error = $error;
    }
}


