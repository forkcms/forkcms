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
use WindowsAzure\Common\Internal\Resources;

/**
 * Represents the settings used to sign and access a request against the service 
 * bus.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ServiceBusSettings extends ServiceSettings
{
    /**
     * @var string
     */
    private $_serviceBusEndpointUri;
    
    /**
     * @var string
     */
    private $_wrapEndpointUri;
    
    /**
     * @var string
     */
    private $_wrapName;
    
    /**
     * @var string
     */
    private $_wrapPassword;
    
    /**
     * @var string
     */
    private $_namespace;
    
    /**
     * Validator for the SharedSecretValue setting. It has to be provided.
     * 
     * @var array
     */
    private static $_wrapPasswordSetting;
    
    /**
     * Validator for the SharedSecretIssuer setting. It has to be provided.
     * 
     * @var array
     */
    private static $_wrapNameSetting;
    
    /**
     * Validator for the Endpoint setting. Must be a valid Uri.
     * 
     * @var array
     */
    private static $_serviceBusEndpointSetting;
    
    /**
     * Validator for the StsEndpoint setting. Must be a valid Uri.
     * 
     * @var array
     */
    private static $_wrapEndpointUriSetting;
    
    /**
     * @var boolean
     */
    protected static $isInitialized = false;
    
    /**
     * Holds the expected setting keys.
     * 
     * @var array
     */
    protected static $validSettingKeys = array();
    
    /**
     * Initializes static members of the class.
     * 
     * @return none
     */
    protected static function init()
    {
        self::$_serviceBusEndpointSetting = self::settingWithFunc(
            Resources::SERVICE_BUS_ENDPOINT_NAME,
            Validate::getIsValidUri()
        );
        
        self::$_wrapNameSetting = self::setting(
            Resources::SHARED_SECRET_ISSUER_NAME
        );
        
        self::$_wrapPasswordSetting = self::setting(
            Resources::SHARED_SECRET_VALUE_NAME
        );
        
        self::$_wrapEndpointUriSetting = self::settingWithFunc(
            Resources::STS_ENDPOINT_NAME,
            Validate::getIsValidUri()
        );
        
        self::$validSettingKeys[] = Resources::SERVICE_BUS_ENDPOINT_NAME;
        self::$validSettingKeys[] = Resources::SHARED_SECRET_ISSUER_NAME;
        self::$validSettingKeys[] = Resources::SHARED_SECRET_VALUE_NAME;
        self::$validSettingKeys[] = Resources::STS_ENDPOINT_NAME;
    }
    
    /**
     * Creates new Service Bus settings instance.
     * 
     * @param string $serviceBusEndpoint The Service Bus endpoint uri.
     * @param string $namespace          The service namespace.
     * @param string $wrapName           The wrap name.
     * @param string $wrapPassword       The wrap password.
     */
    public function __construct(
        $serviceBusEndpoint,
        $namespace,
        $wrapEndpointUri,
        $wrapName,
        $wrapPassword
    ) {
        $this->_namespace             = $namespace;
        $this->_serviceBusEndpointUri = $serviceBusEndpoint;
        $this->_wrapEndpointUri       = $wrapEndpointUri;
        $this->_wrapName              = $wrapName;
        $this->_wrapPassword          = $wrapPassword;
    }
    
    /**
     * Creates a ServiceBusSettings object from the given connection string.
     * 
     * @param string $connectionString The storage settings connection string.
     * 
     * @return ServiceBusSettings 
     */
    public static function createFromConnectionString($connectionString)
    {
        $tokenizedSettings = self::parseAndValidateKeys($connectionString);
        
        $matchedSpecs = self::matchedSpecification(
            $tokenizedSettings,
            self::allRequired(
                self::$_serviceBusEndpointSetting,
                self::$_wrapNameSetting,
                self::$_wrapPasswordSetting
            ),
            self::optional(self::$_wrapEndpointUriSetting)
        );
        
        if ($matchedSpecs) {
            $endpoint = Utilities::tryGetValueInsensitive(
                Resources::SERVICE_BUS_ENDPOINT_NAME,
                $tokenizedSettings
            );
            
            // Parse the namespace part from the URI
            $namespace   = explode('.', parse_url($endpoint, PHP_URL_HOST));
            $namespace   = $namespace[0];
            $wrapEndpointUri = Utilities::tryGetValueInsensitive(
                Resources::STS_ENDPOINT_NAME,
                $tokenizedSettings,
                sprintf(Resources::WRAP_ENDPOINT_URI_FORMAT, $namespace)
            );
            $issuerName  = Utilities::tryGetValueInsensitive(
                Resources::SHARED_SECRET_ISSUER_NAME,
                $tokenizedSettings
            );
            $issuerValue = Utilities::tryGetValueInsensitive(
                Resources::SHARED_SECRET_VALUE_NAME,
                $tokenizedSettings
            );
            
            return new ServiceBusSettings(
                $endpoint,
                $namespace,
                $wrapEndpointUri,
                $issuerName,
                $issuerValue
            );
        }
        
        self::noMatch($connectionString);
    }
    
    /**
     * Gets the Service Bus endpoint URI.
     * 
     * @return string
     */
    public function getServiceBusEndpointUri()
    {
        return $this->_serviceBusEndpointUri;
    }
    
    /**
     * Gets the wrap endpoint URI.
     * 
     * @return string
     */
    public function getWrapEndpointUri()
    {
        return $this->_wrapEndpointUri;
    }
    
    /**
     * Gets the wrap name.
     * 
     * @return string
     */
    public function getWrapName()
    {
        return $this->_wrapName;
    }
    
    /**
     * Gets the wrap password.
     * 
     * @return string
     */
    public function getWrapPassword()
    {
        return $this->_wrapPassword;
    }
    
    /**
     * Gets the namespace name.
     * 
     * @return string 
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }
}


