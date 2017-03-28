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
 * management. For more information about service management connection strings check
 * this page: http://msdn.microsoft.com/en-us/library/windowsazure/gg466228.aspx
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ServiceManagementSettings extends ServiceSettings
{
    /**
     * @var string
     */
    private $_subscriptionId;
    
    /**
     * @var string
     */
    private $_certificatePath;
    
    /**
     * @var string
     */
    private $_endpointUri;
    
    /**
     * Validator for the ServiceManagementEndpoint setting. Must be a valid Uri.
     * 
     * @var array
     */
    private static $_endpointSetting;
    
    /**
     * Validator for the CertificatePath setting. It has to be provided.
     * 
     * @var array
     */
    private static $_certificatePathSetting;
    
    /**
     * Validator for the SubscriptionId setting. It has to be provided.
     * 
     * @var array
     */
    private static $_subscriptionIdSetting;
    
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
        self::$_endpointSetting = self::settingWithFunc(
            Resources::SERVICE_MANAGEMENT_ENDPOINT_NAME,
            Validate::getIsValidUri()
        );
        
        self::$_certificatePathSetting = self::setting(
            Resources::CERTIFICATE_PATH_NAME
        );
        
        self::$_subscriptionIdSetting = self::setting(
            Resources::SUBSCRIPTION_ID_NAME
        );
        
        self::$validSettingKeys[] = Resources::SUBSCRIPTION_ID_NAME;
        self::$validSettingKeys[] = Resources::CERTIFICATE_PATH_NAME;
        self::$validSettingKeys[] = Resources::SERVICE_MANAGEMENT_ENDPOINT_NAME;
    }
    
    /**
     * Creates new service management settings instance.
     * 
     * @param string $subscriptionId  The user provided subscription id.
     * @param string $endpointUri     The service management endpoint uri.
     * @param string $certificatePath The management certificate path.
     */
    public function __construct($subscriptionId, $endpointUri, $certificatePath)
    {
        $this->_certificatePath = $certificatePath;
        $this->_endpointUri     = $endpointUri;
        $this->_subscriptionId  = $subscriptionId;
    }
    
    /**
     * Creates a ServiceManagementSettings object from the given connection string.
     * 
     * @param string $connectionString The storage settings connection string.
     * 
     * @return ServiceManagementSettings 
     */
    public static function createFromConnectionString($connectionString)
    {
        $tokenizedSettings = self::parseAndValidateKeys($connectionString);
        
        $matchedSpecs = self::matchedSpecification(
            $tokenizedSettings,
            self::allRequired(
                self::$_subscriptionIdSetting,
                self::$_certificatePathSetting
            ),
            self::optional(
                self::$_endpointSetting
            )
        );
        if ($matchedSpecs) {
            $endpointUri     = Utilities::tryGetValueInsensitive(
                Resources::SERVICE_MANAGEMENT_ENDPOINT_NAME,
                $tokenizedSettings,
                Resources::SERVICE_MANAGEMENT_URL
            );
            $subscriptionId  = Utilities::tryGetValueInsensitive(
                Resources::SUBSCRIPTION_ID_NAME,
                $tokenizedSettings
            );
            $certificatePath = Utilities::tryGetValueInsensitive(
                Resources::CERTIFICATE_PATH_NAME,
                $tokenizedSettings
            );
            
            return new ServiceManagementSettings(
                $subscriptionId,
                $endpointUri,
                $certificatePath
            );
        }
        
        self::noMatch($connectionString);
    }
    
    /**
     * Gets service management endpoint uri.
     * 
     * @return string
     */
    public function getEndpointUri()
    {
        return $this->_endpointUri;
    }
    
    /**
     * Gets the subscription id.
     * 
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->_subscriptionId;
    }
    
    /**
     * Gets the certificate path.
     * 
     * @return string 
     */
    public function getCertificatePath()
    {
        return $this->_certificatePath;
    }
}


