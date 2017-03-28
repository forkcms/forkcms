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
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal;

/**
 * Holder for default connection string sources used in CloudConfigurationManager.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ConnectionStringSource
{
    /**
     * The list of all sources which comes as default.
     * 
     * @var type 
     */
    private static $_defaultSources;
    
    /**
     * @var boolean
     */
    private static $_isInitialized;
    
    /**
     * Environment variable source name.
     */
    const ENVIRONMENT_SOURCE = 'environment_source';
    
    /**
     * Initializes the default sources.
     * 
     * @return none
     */
    private static function _init()
    {
        if (!self::$_isInitialized) {
            self::$_defaultSources = array(
                self::ENVIRONMENT_SOURCE => array(__CLASS__, 'environmentSource')
            );
            self::$_isInitialized  = true;
        }        
    }
    
    /**
     * Gets a connection string value from the system environment.
     * 
     * @param string $key The connection string name.
     * 
     * @return string
     */
    public static function environmentSource($key)
    {
        Validate::isString($key, 'key');
        
        return getenv($key);
    }
    
    /**
     * Gets list of default sources.
     * 
     * @return array
     */
    public static function getDefaultSources()
    {
        self::_init();
        return self::$_defaultSources;
    }
}


