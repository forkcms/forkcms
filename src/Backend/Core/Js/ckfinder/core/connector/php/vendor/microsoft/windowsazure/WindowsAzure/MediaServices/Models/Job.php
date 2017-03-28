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
 * Represents job object used in media services
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Job
{
    /**
     * The state of the job "queued"
     *
     * @var int
     */
    const STATE_QUEUED = 0;

    /**
     * The state of the job "scheduled"
     *
     * @var int
     */
    const STATE_SCHEDULED = 1;

    /**
     * The state of the job "processing"
     *
     * @var int
     */
    const STATE_PROCESSING = 2;

    /**
     * The state of the job "finished"
     *
     * @var int
     */
    const STATE_FINISHED = 3;

    /**
     * The state of the job "error"
     *
     * @var int
     */
    const STATE_ERROR = 4;

    /**
     * The state of the job "canceled"
     *
     * @var int
     */
    const STATE_CANCELED = 5;

    /**
     * The state of the job "canceling"
     *
     * @var int
     */
    const STATE_CANCELING = 6;

    /**
     * Job id
     *
     * @var string
     */
    private $_id;

    /**
     * State
     *
     * @var int
     */
    private $_state;

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
     * End time
     *
     * @var \DateTime
     */
    private $_endTime;

    /**
     * Priority
     *
     * @var int
     */
    private $_priority;

    /**
     * Running duration
     *
     * @var double
     */
    private $_runningDuration;

    /**
     * Start time
     *
     * @var \DateTime
     */
    private $_startTime;

    /**
     * Template id
     *
     * @var string
     */
    private $_templateId;

    /**
     * Create asset from array
     *
     * @param array $options Array containing values for object properties
     *
     * @return WindowsAzure\MediaServices\Models\Job
     */
    public static function createFromOptions($options)
    {
        $job = new Job();
        $job->fromArray($options);

        return $job;
    }

    /**
     * Fill asset from array
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

        if (isset($options['EndTime'])) {
            Validate::isDateString($options['EndTime'], 'options[EndTime]');
            $this->_endTime = new \DateTime($options['EndTime']);
        }

        if (isset($options['Priority'])) {
            Validate::isInteger($options['Priority'], 'options[Priority]');
            $this->_priority = $options['Priority'];
        }

        if (isset($options['RunningDuration'])) {
            Validate::isDouble(
                $options['RunningDuration'],
                'options[RunningDuration]'
            );
            $this->_runningDuration = $options['RunningDuration'];
        }

        if (isset($options['StartTime'])) {
            Validate::isDateString($options['StartTime'], 'options[StartTime]');
            $this->_startTime = new \DateTime($options['StartTime']);
        }

        if (isset($options['State'])) {
            Validate::isInteger($options['State'], 'options[State]');
            $this->_state = $options['State'];
        }

        if (isset($options['TemplateId'])) {
            Validate::isString($options['TemplateId'], 'options[TemplateId]');
            $this->_templateId = $options['TemplateId'];
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
     * Get "State"
     *
     * @return int
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Get "Job id"
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get "Template id"
     *
     * @return string
     */
    public function getTemplateId()
    {
        return $this->_templateId;
    }

    /**
     * Set "Template id"
     *
     * @param string $value Template id
     *
     * @return none
     */
    public function setTemplateId($value)
    {
        $this->_templateId = $value;
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
     * Get "Running duration"
     *
     * @return double
     */
    public function getRunningDuration()
    {
        return $this->_runningDuration;
    }

    /**
     * Get "Priority"
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Set "Priority"
     *
     * @param int $value Priority
     *
     * @return none
     */
    public function setPriority($value)
    {
        $this->_priority = $value;
    }

    /**
     * Get "End time"
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->_endTime;
    }
}


