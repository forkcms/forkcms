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
 * Represents job template object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class JobTemplate
{
    /**
     * The type of JobTemplate "system level"
     *
     * @var int
     */
    const TYPE_SYSTEM_LEVEL = 0;

    /**
     * The type of JobTemplate "account level"
     *
     * @var int
     */
    const TYPE_ACCOUNT_LEVEL = 1;

    /**
     * Job template id
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
     * Job template body
     *
     * @var string
     */
    private $_jobTemplateBody;

    /**
     * Number of input assets
     *
     * @var int
     */
    private $_numberofInputAssets;

    /**
     * Template type
     *
     * @var int
     */
    private $_templateType;

    /**
     * Create asset from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\JobTemplate
     */
    public static function createFromOptions($options)
    {
        Validate::notNull($options['JobTemplateBody'], 'options[JobTemplateBody]');

        $jobTemplate = new JobTemplate(
            $options['JobTemplateBody'],
            $options['TemplateType']
        );
        $jobTemplate->fromArray($options);

        return $jobTemplate;
    }

    /**
     * Create job template
     *
     * @param string $jobTemplateBody Job template XML body.
     * @param string $templateType    Template type default to AccountLevel.
     */
    public function __construct(
        $jobTemplateBody,
        $templateType = JobTemplate::TYPE_ACCOUNT_LEVEL)
    {
        $this->_jobTemplateBody = $jobTemplateBody;
        $this->_templateType    = $templateType;
    }

    /**
     * Fill job template from array
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

        if (isset($options['JobTemplateBody'])) {
            Validate::isString(
                $options['JobTemplateBody'],
                'options[JobTemplateBody]'
            );
            $this->_jobTemplateBody = $options['JobTemplateBody'];
        }

        if (isset($options['NumberofInputAssets'])) {
            Validate::isInteger(
                $options['NumberofInputAssets'],
                'options[NumberofInputAssets]'
            );
            $this->_numberofInputAssets = $options['NumberofInputAssets'];
        }

        if (isset($options['TemplateType'])) {
            Validate::isInteger($options['TemplateType'], 'options[TemplateType]');
            $this->_templateType = $options['TemplateType'];
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
     * Get "job template id"
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get "Template type"
     *
     * @return int
     */
    public function getTemplateType()
    {
        return $this->_templateType;
    }

    /**
     * Set "Template type"
     *
     * @param int $value Template type
     *
     * @return none
     */
    public function setTemplateType($value)
    {
        $this->_templateType = $value;
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

    /**
     * Get "Job template body"
     *
     * @return string
     */
    public function getJobTemplateBody()
    {
        return $this->_jobTemplateBody;
    }

    /**
     * Set "Job template body"
     *
     * @param string $value Job template body
     *
     * @return none
     */
    public function setJobTemplateBody($value)
    {
        $this->_jobTemplateBody = $value;
    }
}


