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
 * Represents access policy object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class AccessPolicy
{
    /**
     * The access rights the client has when interacting with the Asset. (None)
     *
     * @var int
     */
    const PERMISSIONS_NONE = 0;

    /**
     * The access rights the client has when interacting with the Asset. (Read)
     *
     * @var int
     */
    const PERMISSIONS_READ = 1;

    /**
     * The access rights the client has when interacting with the Asset.
     * (Write)
     *
     * @var int
     */
    const PERMISSIONS_WRITE = 2;

    /**
     * The access rights the client has when interacting with the Asset.
     * (Delete)
     *
     * @var int
     */
    const PERMISSIONS_DELETE = 4;

    /**
     * The access rights the client has when interacting with the Asset. (List)
     *
     * @var int
     */
    const PERMISSIONS_LIST = 8;

    /**
     * Access policy id
     *
     * @var string
     */
    private $_id;

    /**
     * Created
     *
     * @var \DateTime
     */
    private $_created;

    /**
     * Last modified
     *
     * @var \DateTime
     */
    private $_lastModified;

    /**
     * Name
     *
     * @var string
     */
    private $_name;

    /**
     * Duration in minutes
     *
     * @var double
     */
    private $_durationInMinutes;

    /**
     * Permissions
     *
     * @var int
     */
    private $_permissions;

    /**
     * Create access policy from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\AccessPolicy
     */
    public static function createFromOptions($options)
    {
        Validate::notNull($options['Name'], 'options[Name]');

        $accessPolicy = new AccessPolicy($options['Name']);
        $accessPolicy->fromArray($options);

        return $accessPolicy;
    }

    /**
     * Create access policy
     *
     * @param string $name Entity name
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Fill access policy from array
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

        if (isset($options['Created'])) {
            Validate::isDateString($options['Created'], 'options[Created]');
            $this->_created = new \DateTime($options['Created']);
        }

        if (isset($options['LastModified'])) {
            Validate::isDateString(
                $options['LastModified'],
                'options[LastModified]'
            );
            $this->_lastModified = new \DateTime($options['LastModified']);
        }

        if (isset($options['Name'])) {
            Validate::isString($options['Name'], 'options[Name]');
            $this->_name = $options['Name'];
        }

        if (isset($options['DurationInMinutes'])) {
            Validate::isDouble(
                $options['DurationInMinutes'],
                'options[DurationInMinutes]'
            );
            $this->_durationInMinutes = $options['DurationInMinutes'];
        }

        if (isset($options['Permissions'])) {
            Validate::isInteger(
                $options['Permissions'],
                'options[Permissions]'
            );
            $this->_permissions = $options['Permissions'];
        }
    }

    /**
     * Get "Permissions"
     *
     * @return int
     */
    public function getPermissions()
    {
        return $this->_permissions;
    }

    /**
     * Set "Permissions"
     *
     * @param int $value Permissions
     *
     * @return none
     */
    public function setPermissions($value)
    {
        $this->_permissions = $value;
    }

    /**
     * Get "Duration in minutes"
     *
     * @return double
     */
    public function getDurationInMinutes()
    {
        return $this->_durationInMinutes;
    }

    /**
     * Set "Duration in minutes"
     *
     * @param double $value Duration in minutes
     *
     * @return none
     */
    public function setDurationInMinutes($value)
    {
        $this->_durationInMinutes = $value;
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
     * Get "Last modified"
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Get "Created"
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->_created;
    }

    /**
     * Get "Access policy id"
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }
}


