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
use WindowsAzure\Common\Internal\Resources;

/**
 * The runtime version manager.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class RuntimeVersionManager
{
    /**
     * The protocol client.
     * 
     * @var RuntimeVersionProtocolClient
     */
    private $_protocolClient;
    
    /**
     * The supported versions list.
     * 
     * @var array
     */
    private $_supportedVersionList;
    
    /**
     * Constructor
     * 
     * @param RuntimeVersionProtocolClient $protocolClient The runtime version 
     * protocol client.
     */
    public function __construct($protocolClient)
    {
        $this->_protocolClient = $protocolClient;
        
        $this->_supportedVersionList = array();
        array_push(
            $this->_supportedVersionList,
            new Protocol1RuntimeClientFactory()
        );
    }
    
    /**
     * Gets the runtime client.
     * 
     * @param string $versionEndpoint The endpoint's version.
     *
     * @return Protocol1RuntimeClient
     */
    public function getRuntimeClient($versionEndpoint)
    {
        $versionMap = $this->_protocolClient->getVersionMap($versionEndpoint);
        
        foreach ($this->_supportedVersionList as $factory) {
            if (array_key_exists($factory->getVersion(), $versionMap)) {
                return $factory->createRuntimeClient(
                    $versionMap[$factory->getVersion()]
                );
            }
        }
        
        throw new \RuntimeException(Resources::INVALID_VERSION_MSG);
    }
}

