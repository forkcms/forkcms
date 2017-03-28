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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\ConnectionStringSource;

/**
 * Configuration manager for accessing Windows Azure settings.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class CloudConfigurationManager
{
    /**
     * @var boolean
     */
    private static $_isInitialized = false;
    
    /**
     * The list of connection string sources.
     * 
     * @var array
     */
    private static $_sources;
    
    /**
     * Restrict users from creating instances from this class
     */
    private function __construct()
    {
        
    }
    
    /**
     * Initializes the connection string source providers.
     * 
     * @return none 
     */
    private static function _init()
    {
        if (!self::$_isInitialized) {
            self::$_sources = array();
            
            // Get list of default connection string sources.
            $default = ConnectionStringSource::getDefaultSources();
            foreach ($default as $name => $provider) {
                self::$_sources[$name] = $provider;
            }
            
            self::$_isInitialized = true;
        }
    }
    
    /**
     * Gets a connection string from all available sources.
     * 
     * @param string $key The connection string key name.
     * 
     * @return string If the key does not exist return null.
     */
    public static function getConnectionString($key)
    {
        Validate::isString($key, 'key');
        
        self::_init();
        $value = null;
        
        foreach (self::$_sources as $source) {
            $value = call_user_func_array($source, array($key));
            
            if (!empty($value)) {
                break;
            }
        }
        
        return $value;
    }
    
    /**
     * Registers a new connection string source provider. If the source to get 
     * registered is a default source, only the name of the source is required.
     * 
     * @param string   $name     The source name.
     * @param callable $provider The source callback.
     * @param boolean  $prepend  When true, the $provider is processed first when 
     * calling getConnectionString. When false (the default) the $provider is 
     * processed after the existing callbacks.
     * 
     * @return none
     */
    public static function registerSource($name, $provider = null, $prepend = false)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        
        self::_init();
        $default = ConnectionStringSource::getDefaultSources();
        
        // Try to get callback if the user is trying to register a default source.
        $provider = Utilities::tryGetValue($default, $name, $provider);
        
        Validate::notNullOrEmpty($provider, 'callback');
        
        if ($prepend) {
            self::$_sources = array_merge(
                array($name => $provider),
                self::$_sources
            );
            
        } else {
            self::$_sources[$name] = $provider;
        }
    }
    
    /**
     * Unregisters a connection string source.
     * 
     * @param string $name The source name.
     * 
     * @return callable
     */
    public static function unregisterSource($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        
        self::_init();
        
        $sourceCallback = Utilities::tryGetValue(self::$_sources, $name);
        
        if (!is_null($sourceCallback)) {
            unset(self::$_sources[$name]);
        }
        
        return $sourceCallback;
    }
}