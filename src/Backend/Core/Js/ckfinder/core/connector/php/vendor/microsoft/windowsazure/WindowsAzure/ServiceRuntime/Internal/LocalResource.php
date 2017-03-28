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
 * The local resource.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class LocalResource
{
    /**
     * @var int
     */
    private $_maximumSizeInMegabytes;

    /**
     * @var string
     */
    private $_name;
    
    /**
     * @var string
     */
    private $_rootPath;
   
    /**
     * Package accessible constructor.
     * 
     * @param string $maximumSizeInMegabytes Maximum size in megabytes.
     * @param string $name                   The name.
     * @param string $rootPath               The root path.
     */
    public function __construct ($maximumSizeInMegabytes, $name, $rootPath)
    {
        $this->_maximumSizeInMegabytes = $maximumSizeInMegabytes;
        $this->_name                   = $name;
        $this->_rootPath               = $rootPath;
    }
    
    /**
     * Returns the maximum size, in megabytes, allocated for the local storage
     * resource, as defined in the service.
     * 
     * @return int
     */
    public function getMaximumSizeInMegabytes()
    {
        return $this->_maximumSizeInMegabytes;
    }

    /**
     * Returns the name of the local store as declared in the service definition
     * file.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns the full directory path to the local storage resource.
     * 
     * @return string
     */
    public function getRootPath()
    {
        return $this->_rootPath;
    }
}

