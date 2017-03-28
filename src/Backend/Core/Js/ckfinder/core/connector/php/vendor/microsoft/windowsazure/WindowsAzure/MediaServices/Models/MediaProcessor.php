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
 * Represents task historical event object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class MediaProcessor
{
    /**
     * id
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
     * Description
     *
     * @var string
     */
    private $_description;

    /**
     * SKU
     *
     * @var string
     */
    private $_sku;

    /**
     * Vendor
     *
     * @var string
     */
    private $_vendor;

    /**
     * Version
     *
     * @var string
     */
    private $_version;

    /**
     * Create media processor from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\MediaProcessor
     */
    public static function createFromOptions($options)
    {
        $mediaProcessor = new MediaProcessor();
        $mediaProcessor->fromArray($options);

        return $mediaProcessor;
    }

    /**
     * Create media processor
     */
    public function __construct()
    {
    }

    /**
     * Fill media processor from array
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

        if (isset($options['Description'])) {
            Validate::isString($options['Description'], 'options[Description]');
            $this->_description = $options['Description'];
        }

        if (isset($options['Sku'])) {
            Validate::isString($options['Sku'], 'options[Sku]');
            $this->_sku = $options['Sku'];
        }

        if (isset($options['Vendor'])) {
            Validate::isString($options['Vendor'], 'options[Vendor]');
            $this->_vendor = $options['Vendor'];
        }

        if (isset($options['Version'])) {
            Validate::isString($options['Version'], 'options[Version]');
            $this->_version = $options['Version'];
        }

    }

    /**
     * Get "Version"
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get "Vendor"
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->_vendor;
    }

    /**
     * Get "SKU"
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_sku;
    }

    /**
     * Get "Description"
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
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
     * Get "id"
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }
}

