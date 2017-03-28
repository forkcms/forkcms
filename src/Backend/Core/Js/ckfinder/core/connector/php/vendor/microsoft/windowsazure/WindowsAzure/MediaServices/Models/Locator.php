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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;

/**
 * Represents locator object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Locator
{
    /**
     * Type of Locator - none - The default enumeration value
     *
     * @var int
     */
    const TYPE_NONE = 0;

    /**
     * Type of Locator - SAS - Specifies Shared Access Signature (Sas)
     *
     * @var int
     */
    const TYPE_SAS = 1;

    /**
     * Type of Locator - OnDemandOrigin - Specifies a locator type which refers to
     * an Azure Media Service On Demand Origin streaming endpoint
     *
     * @var int
     */
    const TYPE_ON_DEMAND_ORIGIN = 2;

    /**
     * Locator id
     *
     * @var string
     */
    private $_id;

    /**
     * Name
     *
     * @var string
     */
    private $_name;

    /**
     * Expiration date time
     *
     * @var \DateTime
     */
    private $_expirationDateTime;

    /**
     * Type
     *
     * @var int
     */
    private $_type;

    /**
     * Path
     *
     * @var string
     */
    private $_path;

    /**
     * Base URI
     *
     * @var string
     */
    private $_baseUri;

    /**
     * Content access component
     *
     * @var string
     */
    private $_contentAccessComponent;

    /**
     * Access policy Id
     *
     * @var string
     */
    private $_accessPolicyId;

    /**
     * Asset id
     *
     * @var string
     */
    private $_assetId;

    /**
     * Start time
     *
     * @var \DateTime
     */
    private $_startTime;

    /**
     * Create locator from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public static function createFromOptions($options)
    {
        Validate::notNull($options['AssetId'], 'options[AssetId]');
        Validate::notNull($options['AccessPolicyId'], 'options[AccessPolicyId]');
        Validate::notNull($options['Type'], 'options[Type]');

        $locator = new Locator(
            $options['AssetId'],
            $options['AccessPolicyId'],
            $options['Type']
        );
        $locator->fromArray($options);

        return $locator;
    }

    /**
     * Create locator
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string        $asset        A
     * target asset
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy|string $accessPolicy A
     * target access policy
     *
     * @param int                                                   $type         An
     * enumeration value that describes the type of Locator.
     */
    public function __construct($asset, $accessPolicy, $type)
    {
        $this->_assetId        = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );
        $this->_accessPolicyId = Utilities::getEntityId(
            $accessPolicy,
            'WindowsAzure\MediaServices\Models\AccessPolicy'
        );
        $this->_type           = $type;
    }

    /**
     * Fill locator from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return none
     */
    public function fromArray($options)
    {
        if (isset($options['Id'])) {
            Validate::isString($options['Id'], 'options[Id]');
            $this->_id = $options['Id'];
        }

        if (isset($options['Name'])) {
            Validate::isString($options['Name'], 'options[Name]');
            $this->_name = $options['Name'];
        }

        if (isset($options['ExpirationDateTime'])) {
            Validate::isDateString(
                $options['ExpirationDateTime'],
                'options[ExpirationDateTime]'
            );
            $this->_expirationDateTime = new \DateTime(
                $options['ExpirationDateTime']
            );
        }

        if (isset($options['Type'])) {
            Validate::isInteger($options['Type'], 'options[Type]');
            $this->_type = $options['Type'];
        }

        if (isset($options['Path'])) {
            Validate::isValidUri($options['Path'], 'options[Path]');
            $this->_path = $options['Path'];
        }

        if (isset($options['BaseUri'])) {
            Validate::isValidUri($options['BaseUri'], 'options[BaseUri]');
            $this->_baseUri = $options['BaseUri'];
        }

        if (isset($options['ContentAccessComponent'])) {
            Validate::isString(
                $options['ContentAccessComponent'],
                'options[ContentAccessComponent]'
            );
            $this->_contentAccessComponent = $options['ContentAccessComponent'];
        }

        if (isset($options['AccessPolicyId'])) {
            Validate::isString(
                $options['AccessPolicyId'],
                'options[AccessPolicyId]'
            );
            $this->_accessPolicyId = $options['AccessPolicyId'];
        }

        if (isset($options['AssetId'])) {
            Validate::isString($options['AssetId'], 'options[AssetId]');
            $this->_assetId = $options['AssetId'];
        }

        if (isset($options['StartTime'])) {
            Validate::isDateString($options['StartTime'], 'options[StartTime]');
            $this->_startTime = new \DateTime($options['StartTime']);
        }
    }

    /**
     * Get "Start time"
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->_startTime;
    }

    /**
     * Set "Start time"
     *
     * @param \DateTime $value Start time
     *
     * @return none
     */
    public function setStartTime($value)
    {
        $this->_startTime = $value;
    }

    /**
     * Get "Asset id"
     *
     * @return string
     */
    public function getAssetId()
    {
        return $this->_assetId;
    }

    /**
     * Get "Access policy Id"
     *
     * @return string
     */
    public function getAccessPolicyId()
    {
        return $this->_accessPolicyId;
    }

    /**
     * Get "Content access component"
     *
     * @return string
     */
    public function getContentAccessComponent()
    {
        return $this->_contentAccessComponent;
    }

    /**
     * Get "Base URI"
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->_baseUri;
    }

    /**
     * Get "Path"
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Get "Type"
     *
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set "Type"
     *
     * @param int $value Type
     *
     * @return none
     */
    public function setType($value)
    {
        $this->_type = $value;
    }

    /**
     * Get "Expiration date time"
     *
     * @return \DateTime
     */
    public function getExpirationDateTime()
    {
        return $this->_expirationDateTime;
    }

    /**
     * Set "Expiration date time"
     *
     * @param \DateTime $value Expiration date time
     *
     * @return none
     */
    public function setExpirationDateTime($value)
    {
        $this->_expirationDateTime = $value;
    }

    /**
     * Get "Name"
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set "Name"
     *
     * @param string $value Name
     *
     * @return none
     */
    public function setName($value)
    {
        $this->_name = $value;
    }

    /**
     * Get "Locator id"
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set "Locator id"
     *
     * @param string $value Locator id
     *
     * @return none
     */
    public function setId($value)
    {
        $this->_id = $value;
    }
}

