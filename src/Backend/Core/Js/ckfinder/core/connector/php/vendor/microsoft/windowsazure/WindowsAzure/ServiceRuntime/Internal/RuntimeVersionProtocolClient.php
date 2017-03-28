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
use WindowsAzure\Common\Internal\Utilities;

/**
 * The runtime version protocol client.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RuntimeVersionProtocolClient
{
    /**
     * The input channel.
     * 
     * @var IInputChannel
     */
    private $_inputChannel;
    
    /**
     * Constructor
     * 
     * @param IInputChannel $inputChannel The input channel.
     */
    public function __construct($inputChannel)
    {
        $this->_inputChannel = $inputChannel;
    }
    
    /**
     * Gets the version map.
     * 
     * @param string $connectionPath The connection path.
     * 
     * @return array
     */
    public function getVersionMap($connectionPath)
    {
        $versions = array();
       
        $input    = $this->_inputChannel->getInputStream($connectionPath);
        $contents = stream_get_contents($input);

        $discoveryInfo = Utilities::unserialize($contents);
        
        $endpoints = $discoveryInfo['RuntimeServerEndpoints']
            ['RuntimeServerEndpoint'];

        if (array_key_exists('@attributes', $endpoints)) {
            $endpoints   = array();
            $endpoints[] = $discoveryInfo
                ['RuntimeServerEndpoints']['RuntimeServerEndpoint'];
        }
        
        foreach ($endpoints as $endpoint) {
            $versions[$endpoint['@attributes']['version']] = $endpoint
                ['@attributes']['path'];
        }

        return $versions;
    }
}

