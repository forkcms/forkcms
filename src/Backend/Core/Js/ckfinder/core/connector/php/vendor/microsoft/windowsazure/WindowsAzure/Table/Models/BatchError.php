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
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Models;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\ServiceException;

/**
 * Represents an error returned from call to batch API.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchError
{
    /**
     * @var WindowsAzure\Common\ServiceException 
     */
    private $_error;
    
    /**
     * @var integer
     */
    private $_contentId;
    
    /**
     * Creates BatchError object.
     * 
     * @param WindowsAzure\Common\ServiceException $error   The error object.
     * @param array                                $headers The response headers.
     * 
     * @return \WindowsAzure\Table\Models\BatchError 
     */
    public static function create($error, $headers)
    {
        Validate::isTrue(
            $error instanceof ServiceException,
            Resources::INVALID_EXC_OBJ_MSG
        );
        Validate::isArray($headers, 'headers');
        
        $result = new BatchError();
        $clean  = array_change_key_case($headers);
        
        $result->setError($error);
        $contentId = Utilities::tryGetValue($clean, Resources::CONTENT_ID);
        $result->setContentId(is_null($contentId) ? null : intval($contentId));
        
        return $result;
    }
    
    /**
     * Gets the error.
     * 
     * @return WindowsAzure\Common\ServiceException
     */
    public function getError()
    {
        return $this->_error;
    }
    
    /**
     * Sets the error.
     * 
     * @param WindowsAzure\Common\ServiceException $error The error object.
     * 
     * @return none
     */
    public function setError($error)
    {
        $this->_error = $error;
    }
    
    /**
     * Gets the contentId.
     * 
     * @return integer
     */
    public function getContentId()
    {
        return $this->_contentId;
    }
    
    /**
     * Sets the contentId.
     * 
     * @param integer $contentId The contentId object.
     * 
     * @return none
     */
    public function setContentId($contentId)
    {
        $this->_contentId = $contentId;
    }
}


