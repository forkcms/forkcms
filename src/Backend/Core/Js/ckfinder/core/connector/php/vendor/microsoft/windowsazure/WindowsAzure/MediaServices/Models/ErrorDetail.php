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
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\MediaServices\Models;
use WindowsAzure\Common\Internal\Validate;


/**
 * Represents error detail object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ErrorDetail
{
    /**
     * Code
     *
     * @var int
     */
    private $_code;

    /**
     * Message
     *
     * @var string
     */
    private $_message;

    /**
     * Create error detail from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\ErrorDetail
     */
    public static function createFromOptions($options)
    {
        $errorDetail = new ErrorDetail();
        $errorDetail->fromArray($options);

        return $errorDetail;
    }

    /**
     * Fill error detail from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return none
     */
    public function fromArray($options)
    {
        if (isset($options['Code'])) {
            Validate::isInteger($options['Code'], 'options[Code]');
            $this->_code = $options['Code'];
        }

        if (isset($options['Message'])) {
            Validate::isString($options['Message'], 'options[Message]');
            $this->_message = $options['Message'];
        }
    }

    /**
     * Get "Message"
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Get "Code"
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }
}

