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
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceRuntime\Internal;

/**
 * The file input channel.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class FileInputChannel implements IInputChannel
{
    // @codingStandardsIgnoreStart
    
    /**
     * @var Resource
     */
    private $_inputStream;
    
    /**
     * Gets the input stream.
     * 
     * @param string $name The input stream path.
     * 
     * @return none
     */
    public function getInputStream($name)
    {
        $this->_inputStream = @fopen($name, 'r');
        if ($this->_inputStream) {
            return $this->_inputStream;
        } else {
            throw new ChannelNotAvailableException();
        }
    }
    
    /**
     * Closes the input stream.
     * 
     * @return none
     */
    public function closeInputStream() 
    {
        if (!is_null($this->_inputStream)) {
            fclose($this->_inputStream);
            $this->_inputStream = null;
        }
    }
    
    // @codingStandardsIgnoreEnd
}

