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
use WindowsAzure\Common\Internal\Utilities;


/**
 * Represents task template object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class TaskTemplate
{
    /**
     * Task template id
     *
     * @var string
     */
    private $_id;

    /**
     * Configuration
     *
     * @var string
     */
    private $_configuration;

    /**
     * Created
     *
     * @var \DateTime
     */
    private $_created;

    /**
     * Last modified date
     *
     * @var \DateTime
     */
    private $_lastModified;

    /**
     * Description
     *
     * @var string
     */
    private $_description;

    /**
     * Media processor id
     *
     * @var string
     */
    private $_mediaProcessorId;

    /**
     * Name
     *
     * @var string
     */
    private $_name;

    /**
     * Number of input assets
     *
     * @var int
     */
    private $_numberofInputAssets;

    /**
     * Number of output assets
     *
     * @var int
     */
    private $_numberofOutputAssets;

    /**
     * Options
     *
     * @var int
     */
    private $_options;

    /**
     * Encription key id
     *
     * @var string
     */
    private $_encryptionKeyId;

    /**
     * Encryption scheme
     *
     * @var string
     */
    private $_encryptionScheme;

    /**
     * Encryption version
     *
     * @var string
     */
    private $_encryptionVersion;

    /**
     * Initialization vector
     *
     * @var string
     */
    private $_initializationVector;

    /**
     * Create task template from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\TaskTemplate
     */
    public static function createFromOptions($options)
    {
        Validate::notNull(
            $options['NumberofInputAssets'],
            'options[NumberofInputAssets]'
        );
        Validate::notNull(
            $options['NumberofOutputAssets'],
            'options[NumberofOutputAssets]'
        );

        $taskTemplate = new TaskTemplate(
            $options['NumberofInputAssets'],
            $options['NumberofOutputAssets']
        );
        $taskTemplate->fromArray($options);

        return $taskTemplate;
    }

    /**
     * Create task
     *
     * @param int $numberOfInputAssets  Number of input Assets the TaskTemplate
     *                                  must process.
     * @param int $numberOfOutputAssets Number of output Assets the TaskTemplate
     *                                  must process.
     */
    public function __construct($numberOfInputAssets, $numberOfOutputAssets)
    {
        $this->_id                   = 'nb:ttid:UUID:' . Utilities::getGuid();
        $this->_numberofInputAssets  = $numberOfInputAssets;
        $this->_numberofOutputAssets = $numberOfOutputAssets;
    }

    /**
     * Fill task template from array
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

        if (isset($options['Configuration'])) {
            Validate::isString($options['Configuration'], 'options[Configuration]');
            $this->_configuration = $options['Configuration'];
        }

        if (isset($options['Created'])) {
            Validate::isDateString($options['Created'], 'options[Created]');
            $this->_created = new \DateTime($options['Created']);
        }

        if (isset($options['Description'])) {
            Validate::isString($options['Description'], 'options[Description]');
            $this->_description = $options['Description'];
        }

        if (isset($options['LastModified'])) {
            Validate::isDateString(
                $options['LastModified'],
                'options[LastModified]'
            );
            $this->_lastModified = new \DateTime($options['LastModified']);
        }

        if (isset($options['MediaProcessorId'])) {
            Validate::isString(
                $options['MediaProcessorId'],
                'options[MediaProcessorId]'
            );
            $this->_mediaProcessorId = $options['MediaProcessorId'];
        }

        if (isset($options['Name'])) {
            Validate::isString($options['Name'], 'options[Name]');
            $this->_name = $options['Name'];
        }

        if (isset($options['NumberofInputAssets'])) {
            Validate::isInteger(
                $options['NumberofInputAssets'],
                'options[NumberofInputAssets]'
            );
            $this->_numberofInputAssets = $options['NumberofInputAssets'];
        }

        if (isset($options['NumberofOutputAssets'])) {
            Validate::isInteger(
                $options['NumberofOutputAssets'],
                'options[NumberofOutputAssets]'
            );
            $this->_numberofOutputAssets = $options['NumberofOutputAssets'];
        }

        if (isset($options['Options'])) {
            Validate::isInteger($options['Options'], 'options[Options]');
            $this->_options = $options['Options'];
        }

        if (isset($options['EncryptionKeyId'])) {
            Validate::isString(
                $options['EncryptionKeyId'],
                'options[EncryptionKeyId]'
            );
            $this->_encryptionKeyId = $options['EncryptionKeyId'];
        }

        if (isset($options['EncryptionScheme'])) {
            Validate::isString(
                $options['EncryptionScheme'],
                'options[EncryptionScheme]'
            );
            $this->_encryptionScheme = $options['EncryptionScheme'];
        }

        if (isset($options['EncryptionVersion'])) {
            Validate::isString(
                $options['EncryptionVersion'],
                'options[EncryptionVersion]'
            );
            $this->_encryptionVersion = $options['EncryptionVersion'];
        }

        if (isset($options['InitializationVector'])) {
            Validate::isString(
                $options['InitializationVector'],
                'options[InitializationVector]'
            );
            $this->_initializationVector = $options['InitializationVector'];
        }
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
     * Get "Task template id"
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get "Initialization vector"
     *
     * @return string
     */
    public function getInitializationVector()
    {
        return $this->_initializationVector;
    }

    /**
     * Set "Initialization vector"
     *
     * @param string $value Initialization vector
     *
     * @return none
     */
    public function setInitializationVector($value)
    {
        $this->_initializationVector = $value;
    }

    /**
     * Get "Encryption version"
     *
     * @return string
     */
    public function getEncryptionVersion()
    {
        return $this->_encryptionVersion;
    }

    /**
     * Set "Encryption version"
     *
     * @param string $value Encryption version
     *
     * @return none
     */
    public function setEncryptionVersion($value)
    {
        $this->_encryptionVersion = $value;
    }

    /**
     * Get "Encryption scheme"
     *
     * @return string
     */
    public function getEncryptionScheme()
    {
        return $this->_encryptionScheme;
    }

    /**
     * Set "Encryption scheme"
     *
     * @param string $value Encryption scheme
     *
     * @return none
     */
    public function setEncryptionScheme($value)
    {
        $this->_encryptionScheme = $value;
    }

    /**
     * Get "Encription key id"
     *
     * @return string
     */
    public function getEncryptionKeyId()
    {
        return $this->_encryptionKeyId;
    }

    /**
     * Set "Encription key id"
     *
     * @param string $value Encription key id
     *
     * @return none
     */
    public function setEncryptionKeyId($value)
    {
        $this->_encryptionKeyId = $value;
    }

    /**
     * Get "Options"
     *
     * @return int
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get "Media processor id"
     *
     * @return string
     */
    public function getMediaProcessorId()
    {
        return $this->_mediaProcessorId;
    }

    /**
     * Set "Media processor id"
     *
     * @param string $value Media procesot id
     *
     * @return none
     */
    public function setMediaProcessorId($value)
    {
        $this->_mediaProcessorId = $value;
    }

    /**
     * Get "Configuration"
     *
     * @return string
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }

    /**
     * Set "Configuration"
     *
     * @param string $value Configuration
     *
     * @return none
     */
    public function setConfiguration($value)
    {
        $this->_configuration = $value;
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
     * Get "Last modified date"
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->_lastModified;
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
     * Set "Description"
     *
     * @param string $value Description
     *
     * @return none
     */
    public function setDescription($value)
    {
        $this->_description = $value;
    }

    /**
     * Get "Number of output assets"
     *
     * @return int
     */
    public function getNumberofOutputAssets()
    {
        return $this->_numberofOutputAssets;
    }

    /**
     * Set "Number of output assets"
     *
     * @param int $value Number of output assets
     *
     * @return none
     */
    public function setNumberofOutputAssets($value)
    {
        $this->_numberofOutputAssets = $value;
    }

    /**
     * Get "Number of input assets"
     *
     * @return int
     */
    public function getNumberofInputAssets()
    {
        return $this->_numberofInputAssets;
    }

    /**
     * Set "Number of input assets"
     *
     * @param int $value Number of input assets
     *
     * @return none
     */
    public function setNumberofInputAssets($value)
    {
        $this->_numberofInputAssets = $value;
    }
}

