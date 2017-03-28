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
 * @package   WindowsAzure\MediaServices\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\MediaServices\Internal;
use WindowsAzure\Common\Internal\FilterableService;

/**
 * This interface has all REST APIs provided by Windows Azure for Blob service.
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd135733.aspx
 */
interface IMediaServices extends FilterableService
{
    /**
     * Create new asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset $asset Asset data
     *
     * @return WindowsAzure\MediaServices\Models\Asset Created asset
     */
    public function createAsset($asset);

    /**
     * Get asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return WindowsAzure\MediaServices\Models\Asset
     */
    public function getAsset($asset);

    /**
     * Get asset list
     *
     * @return array
     */
    public function getAssetList();

    /**
     * Get asset locators
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return array
     */
    public function getAssetLocators($asset);

    /**
     * Get parent assets of asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return array
     */
    public function getAssetParentAssets($asset);

    /**
     * Get assetFiles of asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return array
     */
    public function getAssetAssetFileList($asset);

    /**
     * Get storage account of asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return WindowsAzure\MediaServices\Models\StorageAccount
     */
    public function getAssetStorageAccount($asset);

    /**
     * Update asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset $asset New asset data with
     * valid id
     *
     * @return none
     */
    public function updateAsset($asset);

    /**
     * Delete asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return none
     */
    public function deleteAsset($asset);

    /**
     * Create new access policy
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy $accessPolicy Access
     * policy data
     *
     * @return WindowsAzure\MediaServices\Models\AccessPolicy
     */
    public function createAccessPolicy($accessPolicy);

    /**
     * Get AccessPolicy.
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy|string $accessPolicy A
     * AccessPolicy data or AccessPolicy Id
     *
     * @return WindowsAzure\MediaServices\Models\AccessPolicy
     */
    public function getAccessPolicy($accessPolicy);

    /**
     * Get list of AccessPolicies.
     *
     * @return array
     */
    public function getAccessPolicyList();

    /**
     * Delete access policy
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy|string $accessPolicy A
     * Access policy data or access policy Id
     *
     * @return none
     */
    public function deleteAccessPolicy($accessPolicy);

    /**
     * Create new locator
     *
     * @param WindowsAzure\MediaServices\Models\Locator $locator Locator data
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function createLocator($locator);

    /**
     * Get Locator.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocator($locator);

    /**
     * Get Locator access policy.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocatorAccessPolicy($locator);

    /**
     * Get Locator asset.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocatorAsset($locator);

    /**
     * Get list of Locators.
     *
     * @return array
     */
    public function getLocatorList();

    /**
     * Update locator
     *
     * @param WindowsAzure\MediaServices\Models\Locator $locator New locator data
     * with valid id
     *
     * @return none
     */
    public function updateLocator($locator);

    /**
     * Delete locator
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Asset data
     * or asset Id
     *
     * @return none
     */
    public function deleteLocator($locator);

    /**
     * Generate file info for all files in asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return none
     */
    public function createFileInfos($asset);

    /**
     * Get asset file.
     *
     * @param WindowsAzure\MediaServices\Models\AssetFile|string $assetFile AssetFile
     * data or assetFile Id
     *
     * @return WindowsAzure\MediaServices\Models\AssetFile
     */
    public function getAssetFile($assetFile);


    /**
     * Get list of all asset files.
     *
     * @return array
     */
    public function getAssetFileList();

    /**
     * Update asset file
     *
     * @param WindowsAzure\MediaServices\Models\AssetFile $assetFile New AssetFile
     * data
     *
     * @return none
     */
    public function updateAssetFile($assetFile);

    /**
     * Upload asset file to storage.
     *
     * @param WindowsAzure\MediaServices\Models\Locator $locator Write locator for
     * file upload
     *
     * @param string                                    $name    Uploading filename
     * @param string                                    $body    Uploading content
     *
     * @return none
     */
    public function uploadAssetFile($locator, $name, $body);

    /**
     * Create a job.
     *
     * @param WindowsAzure\MediaServices\Models\Job $job         Job data
     * @param array                                 $inputAssets Input assets list
     * @param array                                 $tasks       Performed tasks
     * array (optional)
     *
     * @return array
     */
    public function createJob($job, $inputAssets, $tasks = null);

    /**
     * Get Job.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return WindowsAzure\MediaServices\Models\Job
     */
    public function getJob($job);

    /**
     * Get list of Jobs.
     *
     * @return array
     */
    public function getJobList();

    /**
     * Get status of a job
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return string
     */
    public function getJobStatus($job);

    /**
     * Get job tasks.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return array
     */
    public function getJobTasks($job);


    /**
     * Get job input assets.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return array
     */
    public function getJobInputMediaAssets($job);

    /**
     * Get job output assets.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return array
     */
    public function getJobOutputMediaAssets($job);

    /**
     * Cancel a job
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return none
     */
    public function cancelJob($job);

    /**
     * Delete job
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return none
     */
    public function deleteJob($job);

    /**
     * Get list of tasks.
     *
     * @return array
     */
    public function getTaskList();

    /**
     * Create a job.
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate $jobTemplate   Job
     * template data
     *
     * @param array                                         $taskTemplates Performed
     * tasks template array
     *
     * @return array
     */
    public function createJobTemplate($jobTemplate, $taskTemplates);

    /**
     * Get job template.
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate|string $jobTemplate Job
     * template data or jobTemplate Id
     *
     * @return WindowsAzure\MediaServices\Models\JobTemplate
     */
    public function getJobTemplate($jobTemplate);

    /**
     * Get list of Job Templates.
     *
     * @return array
     */
    public function getJobTemplateList();

    /**
     * Get task templates for job template.
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate|string $jobTemplate Job
     * template data or jobTemplate Id
     *
     * @return array
     */
    public function getJobTemplateTaskTemplateList($jobTemplate);


    /**
     * Delete job template
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate|string $jobTemplate Job
     * template data or job template Id
     *
     * @return none
     */
    public function deleteJobTemplate($jobTemplate);

    /**
     * Get list of task templates.
     *
     * @return array
     */
    public function getTaskTemplateList();

    /**
     * Get list of all media processors asset files
     *
     * @return array
     */
    public function getMediaProcessors();

    /**
     * Get media processor by name with latest version
     *
     * @param string $name Media processor name
     *
     * @return WindowsAzure\MediaServices\Models\JobTemplate\MediaProcessor
     */
    public function getLatestMediaProcessor($name);
}

