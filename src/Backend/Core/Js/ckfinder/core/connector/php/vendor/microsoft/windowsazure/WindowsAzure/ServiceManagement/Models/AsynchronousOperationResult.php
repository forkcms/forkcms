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
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * The result of an asynchronous operation.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class AsynchronousOperationResult
{
    /**
     * @var string
     */
    private $_requestId;
    
    /**
     * Creates new AsynchronousOperationResult from response HTTP headers.
     * 
     * @param array $headers The HTTP response headers array.
     * 
     * @return AsynchronousOperationResult 
     */
    public static function create($headers)
    {
        $result             = new AsynchronousOperationResult();
        $result->_requestId = Utilities::tryGetValue(
            $headers,
            Resources::X_MS_REQUEST_ID
        );
        
        return $result;
    }
    
    /**
     * Gets the requestId.
     * 
     * @return string
     */
    public function getrequestId()
    {
        return $this->_requestId;
    }
    
    /**
     * Sets the requestId.
     * 
     * @param string $requestId The request Id of the asynchronous operation.
     * 
     * @return none
     */
    public function setrequestId($requestId)
    {
        $this->_requestId = $requestId;
    }
}


