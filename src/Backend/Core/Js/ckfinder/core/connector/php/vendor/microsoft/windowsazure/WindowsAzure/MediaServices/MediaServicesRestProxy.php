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
 * @package   WindowsAzure\MediaServices
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\MediaServices;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Http\HttpCallContext;
use WindowsAzure\Common\Models\ServiceProperties;
use WindowsAzure\Common\Internal\ServiceRestProxy;
use WindowsAzure\MediaServices\Internal\IMediaServices;
use WindowsAzure\MediaServices\Models\Asset;
use WindowsAzure\MediaServices\Models\AccessPolicy;
use WindowsAzure\MediaServices\Models\Locator;
use WindowsAzure\MediaServices\Models\AssetFile;
use WindowsAzure\MediaServices\Models\Job;
use WindowsAzure\MediaServices\Models\Task;
use WindowsAzure\MediaServices\Models\MediaProcessor;
use WindowsAzure\MediaServices\Models\JobTemplate;
use WindowsAzure\MediaServices\Models\TaskTemplate;
use WindowsAzure\MediaServices\Models\ContentProperties;
use WindowsAzure\Common\Internal\Atom\Feed;
use WindowsAzure\Common\Internal\Atom\Entry;
use WindowsAzure\Common\Internal\Atom\Content;
use WindowsAzure\Common\Internal\Atom\AtomLink;
use WindowsAzure\Blob\Models\BlobType;
use WindowsAzure\Common\Internal\Http\HttpClient;
use WindowsAzure\Common\Internal\Http\Url;
use WindowsAzure\Common\Internal\Http\BatchRequest;
use WindowsAzure\Common\Internal\Http\BatchResponse;
use WindowsAzure\MediaServices\Models\StorageAccount;

/**
 * This class constructs HTTP requests and receive HTTP responses for media services
 * service layer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\MediaServices
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class MediaServicesRestProxy extends ServiceRestProxy implements IMediaServices
{
    /**
     * Headers used in batch requests
     *
     * @var array
     */
    private $_batchHeaders = array(
        Resources::DATA_SERVICE_VERSION     => Resources::MEDIA_SERVICES_DATA_SERVICE_VERSION_VALUE,
        Resources::MAX_DATA_SERVICE_VERSION => Resources::MEDIA_SERVICES_MAX_DATA_SERVICE_VERSION_VALUE,
        Resources::ACCEPT_HEADER            => Resources::ACCEPT_HEADER_VALUE,
        Resources::CONTENT_TYPE             => Resources::XML_ATOM_CONTENT_TYPE
    );

    /**
     * Sends HTTP request with the specified parameters.
     *
     * @param string $method         HTTP method used in the request
     * @param array  $headers        HTTP headers.
     * @param array  $queryParams    URL query parameters.
     * @param array  $postParameters The HTTP POST parameters.
     * @param string $path           URL path
     * @param int    $statusCode     Expected status code received in the response
     * @param string $body           Request body
     *
     * @return \HTTP_Request2_Response
     */
    protected function send(
        $method,
        $headers,
        $queryParams,
        $postParameters,
        $path,
        $statusCode,
        $body = Resources::EMPTY_STRING
    ) {
        // Add redirect to expected results
        if (!is_array($statusCode)) {
            $statusCode = array($statusCode, );
        }
        array_push($statusCode, Resources::STATUS_MOVED_PERMANENTLY);

        $response = parent::send(
            $method,
            $headers,
            $queryParams,
            $postParameters,
            $path,
            $statusCode,
            $body
        );

        // Set new URI endpoint if we get redirect response and perform query
        if ($response->getStatus() == Resources::STATUS_MOVED_PERMANENTLY) {
            $this->setUri($response->getHeader('location'));
            array_pop($statusCode);

            $response = parent::send(
                $method,
                $headers,
                $queryParams,
                $postParameters,
                $path,
                $statusCode,
                $body
            );
        }


        return $response;
    }

    /**
     * Wraps media services entity with Atom entry
     *
     * @param object $entity Media services entity
     * @param array  $links  AtomLinks to other media services entities
     *
     * @return XML string representing Atom Entry
     */
    protected function wrapAtomEntry($entity, $links = null)
    {
        Validate::notNull($entity, 'entity');

        $properties = new ContentProperties();
        $properties->setPropertiesFromObject($entity);

        $propertyWriter = new \XMLWriter();
        $propertyWriter->openMemory();
        $properties->writeXml($propertyWriter);

        $content = new Content();
        $content->setType(Resources::XML_CONTENT_TYPE);
        $content->setText($propertyWriter->outputMemory());

        $atomEntry = new Entry();
        $atomEntry->setContent($content);

        if ($links) {
            Validate::isArray($links, 'links');

            $atomEntry->setLink($links);
        }

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $atomEntry->writeXml($xmlWriter);

        return $xmlWriter->outputMemory();
    }

    /**
     * Extract media service entity from Atom Entry object
     *
     * @param WindowsAzure\Common\Internal\Atom\Entry $entry Atom Entry containing
     * properties of media services object
     *
     * @return array
     */
    protected function getPropertiesFromAtomEntry($entry)
    {
        Validate::notNull($entry, 'entry');
        Validate::isA($entry, 'WindowsAzure\Common\Internal\Atom\Entry', 'entry');

        $result  = array();
        $content = $entry->getContent();
        if (!empty($content)) {
            $propertiesXml = $content->getXml()->children(
                Resources::DSM_XML_NAMESPACE
            );

            $properties = new ContentProperties();
            $properties->fromXml($propertiesXml);
            $result = $properties->getProperties();
        }

        return $result;
    }

    /**
     * Get array of properties of atom entites passed via feed or single entry
     *
     * @param string $xmlString Atom xml
     *
     * @return array
     */
    protected function getEntryList($xmlString)
    {
        $xml = simplexml_load_string($xmlString);

        if ($xml->getName() == Resources::ENTRY) {
            $entry = new Entry();
            $entry->fromXml($xml);
            $entries = array($entry);
        } else {
            $feed = new Feed();
            $feed->parseXml($xmlString);
            $entries = $feed->getEntry();
        }

        $result = array();
        if (is_array($entries)) {
            foreach ($entries as $entry) {
                $properties = $this->getPropertiesFromAtomEntry($entry);
                if (!empty($properties)) {
                    $result[] = $properties;
                }
            }
        }

        return $result;
    }

    /**
     * Create entity
     *
     * @param object $entity Entity data
     * @param string $path   REST path
     *
     * @return array Created entity data
     */
    private function _createEntity($entity, $path)
    {
        $method      = Resources::HTTP_POST;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_CREATED;
        $body        = $this->wrapAtomEntry($entity);

        $response = $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode,
            $body
        );

        $entry = new Entry();
        $entry->parseXml($response->getBody());
        return $this->getPropertiesFromAtomEntry($entry);
    }

    /**
     * Get entity from Azure
     *
     * @param string $path REST path
     *
     * @return array Entity data
     */
    private function _getEntity($path)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;

        $response = $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode
        );

        $entry = new Entry();
        $entry->parseXml($response->getBody());

        return $this->getPropertiesFromAtomEntry($entry);
    }

    /**
     * Create entity list
     *
     * @param string $path REST path
     *
     * @return array Entities list data
     */
    private function _getEntityList($path)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_OK;

        $response = $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode
        );

        return $this->getEntryList($response->getBody());
    }

    /**
     * Update entity
     *
     * @param object $entity Entity data
     * @param string $path   REST path
     *
     * @return none
     */
    private function _updateEntity($entity, $path)
    {
        $method      = Resources::HTTP_MERGE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_NO_CONTENT;
        $body        = $this->wrapAtomEntry($entity);

        $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode,
            $body
        );
    }

    /**
     * Delete entity
     *
     * @param string $path REST path
     *
     * @return none
     */
    private function _deleteEntity($path)
    {
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_NO_CONTENT;

        $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode
        );
    }

    /**
     * Create new asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset $asset Asset data
     *
     * @return WindowsAzure\MediaServices\Models\Asset Created asset
     */
    public function createAsset($asset)
    {
        Validate::isA($asset, 'WindowsAzure\Mediaservices\Models\Asset', 'asset');

        return Asset::createFromOptions($this->_createEntity($asset, 'Assets'));
    }

    /**
     * Get asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return WindowsAzure\MediaServices\Models\Asset
     */
    public function getAsset($asset)
    {
        $assetId = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );

        return Asset::createFromOptions($this->_getEntity("Assets('{$assetId}')"));
    }

    /**
     * Get asset list
     *
     * @return array
     */
    public function getAssetList()
    {
        $propertyList = $this->_getEntityList("Assets");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Asset::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get asset locators
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return array
     */
    public function getAssetLocators($asset)
    {
        $assetId = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );

        $propertyList = $this->_getEntityList("Assets('{$assetId}')/Locators");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Locator::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get parent assets of asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return array
     */
    public function getAssetParentAssets($asset)
    {
        $assetId = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );

        $propertyList = $this->_getEntityList("Assets('{$assetId}')/ParentAssets");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Asset::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get assetFiles of asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return array
     */
    public function getAssetAssetFileList($asset)
    {
        $assetId = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );

        $propertyList = $this->_getEntityList("Assets('{$assetId}')/Files");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = AssetFile::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get storage account of asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return WindowsAzure\MediaServices\Models\StorageAccount
     */
    public function getAssetStorageAccount($asset)
    {
        $assetId = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );

        return StorageAccount::createFromOptions(
            $this->_getEntity("Assets('{$assetId}')/StorageAccount")
        );
    }

    /**
     * Update asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset $asset New asset data with
     * valid id
     *
     * @return none
     */
    public function updateAsset($asset)
    {
        Validate::isA($asset, 'WindowsAzure\MediaServices\Models\Asset', 'asset');

        $this->_updateEntity($asset, "Assets('{$asset->getId()}')");
    }

    /**
     * Delete asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return none
     */
    public function deleteAsset($asset)
    {
        $assetId = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );

        $this->_deleteEntity("Assets('{$assetId}')");
    }

    /**
     * Create new access policy
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy $accessPolicy Access
     * policy data
     *
     * @return WindowsAzure\MediaServices\Models\AccessPolicy
     */
    public function createAccessPolicy($accessPolicy)
    {
        Validate::isA(
            $accessPolicy,
            'WindowsAzure\Mediaservices\Models\AccessPolicy',
            'accessPolicy'
        );

        return AccessPolicy::createFromOptions(
            $this->_createEntity($accessPolicy, 'AccessPolicies')
        );
    }

    /**
     * Get AccessPolicy.
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy|string $accessPolicy A
     * AccessPolicy data or AccessPolicy Id
     *
     * @return WindowsAzure\MediaServices\Models\AccessPolicy
     */
    public function getAccessPolicy($accessPolicy)
    {
        $accessPolicyId = Utilities::getEntityId(
            $accessPolicy,
            'WindowsAzure\Mediaservices\Models\AccessPolicy'
        );

        return AccessPolicy::createFromOptions(
            $this->_getEntity("AccessPolicies('{$accessPolicyId}')")
        );
    }

    /**
     * Get list of AccessPolicies.
     *
     * @return array
     */
    public function getAccessPolicyList()
    {
        $propertyList = $this->_getEntityList('AccessPolicies');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = AccessPolicy::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Delete access policy
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy|string $accessPolicy A
     * Access policy data or access policy Id
     *
     * @return none
     */
    public function deleteAccessPolicy($accessPolicy)
    {
        $accessPolicyId = Utilities::getEntityId(
            $accessPolicy,
            'WindowsAzure\Mediaservices\Models\AccessPolicy'
        );

        $this->_deleteEntity("AccessPolicies('{$accessPolicyId}')");
    }

    /**
     * Create new locator
     *
     * @param WindowsAzure\MediaServices\Models\Locator $locator Locator data
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function createLocator($locator)
    {
        Validate::isA(
            $locator,
            'WindowsAzure\Mediaservices\Models\Locator',
            'locator'
        );

        return Locator::createFromOptions(
            $this->_createEntity($locator, 'Locators')
        );
    }

    /**
     * Get Locator.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocator($locator)
    {
        $locatorId = Utilities::getEntityId(
            $locator,
            'WindowsAzure\Mediaservices\Models\Locator'
        );

        return Locator::createFromOptions(
            $this->_getEntity("Locators('{$locatorId}')")
        );
    }

    /**
     * Get Locator access policy.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocatorAccessPolicy($locator)
    {
        $locatorId = Utilities::getEntityId(
            $locator,
            'WindowsAzure\Mediaservices\Models\Locator'
        );

        return AccessPolicy::createFromOptions(
            $this->_getEntity("Locators('{$locatorId}')/AccessPolicy")
        );
    }

    /**
     * Get Locator asset.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocatorAsset($locator)
    {
        $locatorId = Utilities::getEntityId(
            $locator,
            'WindowsAzure\Mediaservices\Models\Locator'
        );

        return Asset::createFromOptions(
            $this->_getEntity("Locators('{$locatorId}')/Asset")
        );
    }

    /**
     * Get list of Locators.
     *
     * @return array
     */
    public function getLocatorList()
    {
        $propertyList = $this->_getEntityList('Locators');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Locator::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Update locator
     *
     * @param WindowsAzure\MediaServices\Models\Locator $locator New locator data
     * with valid id
     *
     * @return none
     */
    public function updateLocator($locator)
    {
        Validate::isA(
            $locator,
            'WindowsAzure\MediaServices\Models\Locator',
            'locator'
        );

        $this->_updateEntity($locator, "Locators('{$locator->getId()}')");
    }

    /**
     * Delete locator
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Asset data
     * or asset Id
     *
     * @return none
     */
    public function deleteLocator($locator)
    {
        $locatorId = Utilities::getEntityId(
            $locator,
            'WindowsAzure\Mediaservices\Models\Locator'
        );

        $this->_deleteEntity("Locators('{$locatorId}')");
    }

    /**
     * Generate file info for all files in asset
     *
     * @param WindowsAzure\MediaServices\Models\Asset|string $asset Asset data or
     * asset Id
     *
     * @return none
     */
    public function createFileInfos($asset)
    {
        $assetId        = Utilities::getEntityId(
            $asset,
            'WindowsAzure\MediaServices\Models\Asset'
        );
        $assetIdEncoded = urlencode($assetId);

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = "CreateFileInfos?assetid='{$assetIdEncoded}'";
        $statusCode  = Resources::STATUS_NO_CONTENT;

        $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode
        );
    }

    /**
     * Get asset file.
     *
     * @param WindowsAzure\MediaServices\Models\AssetFile|string $assetFile AssetFile
     *  data or assetFile Id
     *
     * @return WindowsAzure\MediaServices\Models\AssetFile
     */
    public function getAssetFile($assetFile)
    {
        $assetFileId = Utilities::getEntityId(
            $assetFile,
            'WindowsAzure\Mediaservices\Models\AssetFile'
        );

        return AssetFile::createFromOptions(
            $this->_getEntity("Files('{$assetFileId}')")
        );
    }


    /**
     * Get list of all asset files.
     *
     * @return array
     */
    public function getAssetFileList()
    {
        $propertyList = $this->_getEntityList('Files');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = AssetFile::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Update asset file
     *
     * @param WindowsAzure\MediaServices\Models\AssetFile $assetFile New AssetFile
     * data
     *
     * @return none
     */
    public function updateAssetFile($assetFile)
    {
        Validate::isA(
            $assetFile,
            'WindowsAzure\MediaServices\Models\AssetFile',
            'assetFile'
        );

        $this->_updateEntity($assetFile, "Files('{$assetFile->getId()}')");
    }

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
    public function uploadAssetFile($locator, $name, $body)
    {
        Validate::isA(
            $locator,
            'WindowsAzure\Mediaservices\Models\Locator',
            'locator'
        );
        Validate::isString($name, 'name');
        Validate::notNull($body, 'body');

        $method     = Resources::HTTP_PUT;
        $urlFile    = $locator->getBaseUri() . '/' . $name;
        $url        = new Url($urlFile . $locator->getContentAccessComponent());
        $filters    = array();
        $statusCode = Resources::STATUS_CREATED;
        $headers    = array(
            Resources::CONTENT_TYPE   => Resources::BINARY_FILE_TYPE,
            Resources::X_MS_VERSION   => Resources::STORAGE_API_LATEST_VERSION,
            Resources::X_MS_BLOB_TYPE => BlobType::BLOCK_BLOB,
        );

        $httpClient = new HttpClient();
        $httpClient->setMethod($method);
        $httpClient->setHeaders($headers);
        $httpClient->setExpectedStatusCode($statusCode);
        $httpClient->setBody($body);
        $httpClient->send($filters, $url);
    }

    /**
     * Create a job HTTP call context.
     *
     * @param WindowsAzure\MediaServices\Models\Job $job         Job data
     * @param array                                 $inputAssets Input assets list
     *
     * @return WindowsAzure\Common\Internal\Http\HttpCallContext
     */
    private function _getCreateEmptyJobContext($job, $inputAssets)
    {
        Validate::isA($job, 'WindowsAzure\MediaServices\Models\Job', 'job');
        Validate::isArray($inputAssets, 'inputAssets');

        $atomLinks = array();
        foreach ($inputAssets as $inputAsset) {
            Validate::isA(
                $inputAsset,
                'WindowsAzure\MediaServices\Models\Asset',
                'inputAssets'
            );

            $href = urlencode($inputAsset->getId());

            $atomLink = new AtomLink();
            $atomLink->setHref($this->getUri() . "Assets('{$href}')");
            $atomLink->setType(Resources::ATOM_FEED_CONTENT_TYPE);
            $atomLink->setTitle('InputAssets');
            $atomLink->setRel(Resources::MEDIA_SERVICES_INPUT_ASSETS_REL);

            $atomLinks[] = $atomLink;
        }

        $result = new HttpCallContext();
        $result->setMethod(Resources::HTTP_POST);
        $result->setHeaders($this->_batchHeaders);
        $result->setUri($this->getUri());
        $result->setPath('/Jobs');
        $result->setBody($this->wrapAtomEntry($job, $atomLinks));
        $result->addStatusCode(Resources::STATUS_CREATED);

        return $result;
    }


    /**
     * Create task HTTP call context
     *
     * @param WindowsAzure\MediaServices\Models\Task $task Task object to be created
     *
     * @return WindowsAzure\Common\Internal\Http\HttpCallContext
     */
    private function _getCreateTaskContext($task)
    {
        Validate::isA($task, 'WindowsAzure\MediaServices\Models\Task', 'task');

        $result = new HttpCallContext();
        $result->setMethod(Resources::HTTP_POST);
        $result->setHeaders($this->_batchHeaders);
        $result->setUri($this->getUri());
        $result->setPath('/$1/Tasks');
        $result->setBody($this->wrapAtomEntry($task));
        $result->addStatusCode(Resources::STATUS_CREATED);

        return $result;
    }

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
    public function createJob($job, $inputAssets, $tasks = null)
    {
        Validate::isA($job, 'WindowsAzure\MediaServices\Models\Job', 'job');
        Validate::isArray($inputAssets, 'inputAssets');

        $batch = new BatchRequest();
        $batch->appendContext($this->_getCreateEmptyJobContext($job, $inputAssets));

        if ($tasks != null) {
            foreach ($tasks as $task) {
                $batch->appendContext($this->_getCreateTaskContext($task));
            }
        }

        $batch->encode();

        $method      = Resources::HTTP_POST;
        $headers     = $batch->getHeaders();
        $postParams  = array();
        $queryParams = array();
        $path        = '$batch';
        $statusCode  = Resources::STATUS_ACCEPTED;
        $body        = $batch->getBody();

        $response = $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode,
            $body
        );

        $batchResponse = new BatchResponse($response->getBody(), $batch);
        $responses     = $batchResponse->getContexts();
        $jobResponse   = $responses[0];

        $entry = new Entry();
        $entry->parseXml($jobResponse->getBody());
        $properties = $this->getPropertiesFromAtomEntry($entry);

        return Job::createFromOptions($properties);
    }

    /**
     * Get Job.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return WindowsAzure\MediaServices\Models\Job
     */
    public function getJob($job)
    {
        $jobId = Utilities::getEntityId(
            $job,
            'WindowsAzure\Mediaservices\Models\Job'
        );

        return Job::createFromOptions($this->_getEntity("Jobs('{$jobId}')"));
    }

    /**
     * Get list of Jobs.
     *
     * @return array
     */
    public function getJobList()
    {
        $propertyList = $this->_getEntityList('Jobs');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Job::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get status of a job
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return string
     */
    public function getJobStatus($job)
    {
        $jobId        = Utilities::getEntityId(
            $job,
            'WindowsAzure\MediaServices\Models\Job'
        );
        $jobIdEncoded = urlencode($jobId);

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = "Jobs('{$jobIdEncoded}')/State";
        $statusCode  = Resources::STATUS_OK;

        $response = $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode
        );

        return strip_tags($response->getBody());
    }

    /**
     * Get job tasks.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return array
     */
    public function getJobTasks($job)
    {
        $jobId = Utilities::getEntityId(
            $job,
            'WindowsAzure\Mediaservices\Models\Job'
        );

        $propertyList = $this->_getEntityList("Jobs('{$jobId}')/Tasks");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Task::createFromOptions($properties);
        }

        return $result;
    }


    /**
     * Get job input assets.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return array
     */
    public function getJobInputMediaAssets($job)
    {
        $jobId = Utilities::getEntityId(
            $job,
            'WindowsAzure\Mediaservices\Models\Job'
        );

        $propertyList = $this->_getEntityList("Jobs('{$jobId}')/InputMediaAssets");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Asset::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get job output assets.
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return array
     */
    public function getJobOutputMediaAssets($job)
    {
        $jobId = Utilities::getEntityId(
            $job,
            'WindowsAzure\Mediaservices\Models\Job'
        );

        $propertyList = $this->_getEntityList("Jobs('{$jobId}')/OutputMediaAssets");
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Asset::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Cancel a job
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return none
     */
    public function cancelJob($job)
    {
        $jobId        = Utilities::getEntityId(
            $job,
            'WindowsAzure\MediaServices\Models\Job'
        );
        $jobIdEncoded = urlencode($jobId);

        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = "CancelJob?jobid='{$jobIdEncoded}'";
        $statusCode  = Resources::STATUS_NO_CONTENT;

        $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode
        );
    }

    /**
     * Delete job
     *
     * @param WindowsAzure\MediaServices\Models\Job|string $job Job data or job Id
     *
     * @return none
     */
    public function deleteJob($job)
    {
        $jobId = Utilities::getEntityId(
            $job,
            'WindowsAzure\Mediaservices\Models\Job'
        );

        $this->_deleteEntity("Jobs('{$jobId}')");
    }

    /**
     * Get list of tasks.
     *
     * @return array
     */
    public function getTaskList()
    {
        $propertyList = $this->_getEntityList('Tasks');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = Task::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Create a job HTTP call context.
     *
     * @param WindowsAzure\MediaServices\Models\Job $jobTemplate JobTemplate data
     *
     * @return WindowsAzure\Common\Internal\Http\HttpCallContext
     */
    private function _getCreateEmptyJobTemplateContext($jobTemplate)
    {
        Validate::isA(
            $jobTemplate,
            'WindowsAzure\MediaServices\Models\JobTemplate',
            'jobTemplate'
        );

        $result = new HttpCallContext();
        $result->setMethod(Resources::HTTP_POST);
        $result->setHeaders($this->_batchHeaders);
        $result->setUri($this->getUri());
        $result->setPath('/JobTemplates');
        $result->setBody($this->wrapAtomEntry($jobTemplate));
        $result->addStatusCode(Resources::STATUS_CREATED);

        return $result;
    }


    /**
     * Create task template HTTP call context
     *
     * @param WindowsAzure\MediaServices\Models\TaskTemplate $taskTemplate Task
     * template object to be created
     *
     * @return WindowsAzure\Common\Internal\Http\HttpCallContext
     */
    private function _getCreateTaskTemplateContext($taskTemplate)
    {
        Validate::isA(
            $taskTemplate,
            'WindowsAzure\MediaServices\Models\TaskTemplate',
            'taskTemplate'
        );

        $result = new HttpCallContext();
        $result->setMethod(Resources::HTTP_POST);
        $result->setHeaders($this->_batchHeaders);
        $result->setUri($this->getUri());
        $result->setPath('/$1/TaskTemplates');
        $result->setBody($this->wrapAtomEntry($taskTemplate));
        $result->addStatusCode(Resources::STATUS_CREATED);

        return $result;
    }

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
    public function createJobTemplate($jobTemplate, $taskTemplates)
    {
        Validate::isA(
            $jobTemplate,
            'WindowsAzure\MediaServices\Models\JobTemplate',
            'jobTemplate'
        );
        Validate::isArray($taskTemplates, 'taskTemplates');

        $batch = new BatchRequest();
        $batch->appendContext(
            $this->_getCreateEmptyJobTemplateContext($jobTemplate)
        );

        if ($taskTemplates != null) {
            foreach ($taskTemplates as $taskTemplate) {
                $batch->appendContext(
                    $this->_getCreateTaskTemplateContext($taskTemplate)
                );
            }
        }

        $batch->encode();

        $method      = Resources::HTTP_POST;
        $headers     = $batch->getHeaders();
        $postParams  = array();
        $queryParams = array();
        $path        = '$batch';
        $statusCode  = Resources::STATUS_ACCEPTED;
        $body        = $batch->getBody();

        $response = $this->send(
            $method,
            $headers,
            $postParams,
            $queryParams,
            $path,
            $statusCode,
            $body
        );

        $batchResponse       = new BatchResponse($response->getBody(), $batch);
        $responses           = $batchResponse->getContexts();
        $jobTemplateResponse = $responses[0];

        $entry = new Entry();
        $entry->parseXml($jobTemplateResponse->getBody());
        $properties = $this->getPropertiesFromAtomEntry($entry);

        return JobTemplate::createFromOptions($properties);
    }

    /**
     * Get job template.
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate|string $jobTemplate Job
     * template data or jobTemplate Id
     *
     * @return WindowsAzure\MediaServices\Models\JobTemplate
     */
    public function getJobTemplate($jobTemplate)
    {
        $jobTemplateId = Utilities::getEntityId(
            $jobTemplate,
            'WindowsAzure\Mediaservices\Models\JobTemplate'
        );

        return JobTemplate::createFromOptions(
            $this->_getEntity("JobTemplates('{$jobTemplateId}')")
        );
    }

    /**
     * Get list of Job Templates.
     *
     * @return array
     */
    public function getJobTemplateList()
    {
        $propertyList = $this->_getEntityList('JobTemplates');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = JobTemplate::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get task templates for job template.
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate|string $jobTemplate Job
     * template data or jobTemplate Id
     *
     * @return array
     */
    public function getJobTemplateTaskTemplateList($jobTemplate)
    {
        $jobTemplateId = Utilities::getEntityId(
            $jobTemplate,
            'WindowsAzure\Mediaservices\Models\JobTemplate'
        );

        $propertyList = $this->_getEntityList(
            "JobTemplates('{$jobTemplateId}')/TaskTemplates"
        );
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = TaskTemplate::createFromOptions($properties);
        }

        return $result;
    }


    /**
     * Delete job template
     *
     * @param WindowsAzure\MediaServices\Models\JobTemplate|string $jobTemplate Job
     * template data or job template Id
     *
     * @return none
     */
    public function deleteJobTemplate($jobTemplate)
    {
        $jobTemplateId = Utilities::getEntityId(
            $jobTemplate,
            'WindowsAzure\Mediaservices\Models\JobTemplate'
        );

        $this->_deleteEntity("JobTemplates('{$jobTemplateId}')");
    }

    /**
     * Get list of task templates.
     *
     * @return array
     */
    public function getTaskTemplateList()
    {
        $propertyList = $this->_getEntityList('TaskTemplates');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = TaskTemplate::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get list of all media processors asset files
     *
     * @return array
     */
    public function getMediaProcessors()
    {
        $propertyList = $this->_getEntityList('MediaProcessors');
        $result       = array();

        foreach ($propertyList as $properties) {
            $result[] = MediaProcessor::createFromOptions($properties);
        }

        return $result;
    }

    /**
     * Get media processor by name with latest version
     *
     * @param string $name Media processor name
     *
     * @return WindowsAzure\MediaServices\Models\JobTemplate\MediaProcessor
     */
    public function getLatestMediaProcessor($name)
    {
        $mediaProcessors = $this->getMediaProcessors();

        $maxVersion = 0.0;
        $result     = null;
        foreach ($mediaProcessors as $mediaProcessor) {
            if (($mediaProcessor->getName() == $name)
                && ($mediaProcessor->getVersion() > $maxVersion)) {
                $result     = $mediaProcessor;
                $maxVersion = $mediaProcessor->getVersion();
            }
        }

        return $result;
    }
}
