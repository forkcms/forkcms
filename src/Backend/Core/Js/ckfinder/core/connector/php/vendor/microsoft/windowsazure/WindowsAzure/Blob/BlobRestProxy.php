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
 * @package   WindowsAzure\Blob
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Blob;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Models\ServiceProperties;
use WindowsAzure\Common\Internal\ServiceRestProxy;
use WindowsAzure\Blob\Internal\IBlob;
use WindowsAzure\Blob\Models\BlobServiceOptions;
use WindowsAzure\Common\Models\GetServicePropertiesResult;
use WindowsAzure\Blob\Models\ListContainersOptions;
use WindowsAzure\Blob\Models\ListContainersResult;
use WindowsAzure\Blob\Models\CreateContainerOptions;
use WindowsAzure\Blob\Models\GetContainerPropertiesResult;
use WindowsAzure\Blob\Models\GetContainerACLResult;
use WindowsAzure\Blob\Models\SetContainerMetadataOptions;
use WindowsAzure\Blob\Models\DeleteContainerOptions;
use WindowsAzure\Blob\Models\ListBlobsOptions;
use WindowsAzure\Blob\Models\ListBlobsResult;
use WindowsAzure\Blob\Models\BlobType;
use WindowsAzure\Blob\Models\Block;
use WindowsAzure\Blob\Models\CreateBlobOptions;
use WindowsAzure\Blob\Models\BlobProperties;
use WindowsAzure\Blob\Models\GetBlobPropertiesOptions;
use WindowsAzure\Blob\Models\GetBlobPropertiesResult;
use WindowsAzure\Blob\Models\SetBlobPropertiesOptions;
use WindowsAzure\Blob\Models\SetBlobPropertiesResult;
use WindowsAzure\Blob\Models\GetBlobMetadataOptions;
use WindowsAzure\Blob\Models\GetBlobMetadataResult;
use WindowsAzure\Blob\Models\SetBlobMetadataOptions;
use WindowsAzure\Blob\Models\SetBlobMetadataResult;
use WindowsAzure\Blob\Models\GetBlobOptions;
use WindowsAzure\Blob\Models\GetBlobResult;
use WindowsAzure\Blob\Models\DeleteBlobOptions;
use WindowsAzure\Blob\Models\LeaseMode;
use WindowsAzure\Blob\Models\AcquireLeaseOptions;
use WindowsAzure\Blob\Models\AcquireLeaseResult;
use WindowsAzure\Blob\Models\CreateBlobPagesOptions;
use WindowsAzure\Blob\Models\CreateBlobPagesResult;
use WindowsAzure\Blob\Models\PageWriteOption;
use WindowsAzure\Blob\Models\ListPageBlobRangesOptions;
use WindowsAzure\Blob\Models\ListPageBlobRangesResult;
use WindowsAzure\Blob\Models\CreateBlobBlockOptions;
use WindowsAzure\Blob\Models\CommitBlobBlocksOptions;
use WindowsAzure\Blob\Models\BlockList;
use WindowsAzure\Blob\Models\ListBlobBlocksOptions;
use WindowsAzure\Blob\Models\ListBlobBlocksResult;
use WindowsAzure\Blob\Models\CopyBlobOptions;
use WindowsAzure\Blob\Models\CreateBlobSnapshotOptions;
use WindowsAzure\Blob\Models\CreateBlobSnapshotResult;
use WindowsAzure\Blob\Models\PageRange;
use WindowsAzure\Blob\Models\CopyBlobResult;
use WindowsAzure\Blob\Models\BreakLeaseResult;

/**
 * This class constructs HTTP requests and receive HTTP responses for blob
 * service layer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BlobRestProxy extends ServiceRestProxy implements IBlob
{
    /**
     * @var int Defaults to 32MB
     */
    private $_SingleBlobUploadThresholdInBytes = 33554432 ;

    /**
     * Get the value for SingleBlobUploadThresholdInBytes
     *
     * @return int
     */
    public function getSingleBlobUploadThresholdInBytes()
    {
        return $this->_SingleBlobUploadThresholdInBytes;
    }

    /**
     * Set the value for SingleBlobUploadThresholdInBytes, Max 64MB
     *
     * @param int $val The max size to send as a single blob block
     *
     * @return none
     */
    public function setSingleBlobUploadThresholdInBytes($val)
    {
        if ($val > 67108864) {
            // What should the proper action here be?
            $val = 67108864;
        } elseif ($val < 1) {
            // another spot that could use looking at
            $val = 33554432;
        }
        $this->_SingleBlobUploadThresholdInBytes = $val;
    }

    /**
     * Gets the copy blob source name with specified parameters. 
     * 
     * @param string                 $containerName The name of the container. 
     * @param string                 $blobName      The name of the blob.
     * @param Models\CopyBlobOptions $options       The optional parameters.
     *
     * @return string 
     */
    private function _getCopyBlobSourceName($containerName, $blobName, $options)
    {
        $sourceName  = '/' . $this->getAccountName();
        $sourceName .= '/' . $this->_createPath($containerName, $blobName);

        if (!is_null($options->getSourceSnapshot())) {
            $sourceName .= '?snapshot=' . $options->getSourceSnapshot();
        }

        return $sourceName;
    }
    
    /**
     * Creates URI path for blob.
     * 
     * @param string $container The container name.
     * @param string $blob      The blob name.
     * 
     * @return string
     */
    private function _createPath($container, $blob)
    {
        $encodedBlob = urlencode($blob);
        // Unencode the forward slashes to match what the server expects.
        $encodedBlob = str_replace('%2F', '/', $encodedBlob);
        // Unencode the backward slashes to match what the server expects.
        $encodedBlob = str_replace('%5C', '/', $encodedBlob);
        // Re-encode the spaces (encoded as space) to the % encoding.
        $encodedBlob = str_replace('+', '%20', $encodedBlob);
        
        // Empty container means accessing default container
        if (empty($container)) {
            return $encodedBlob;
        } else {
            return $container . '/' . $encodedBlob;
        }
    }
    
    /**
     * Creates GetBlobPropertiesResult from headers array.
     * 
     * @param array $headers The HTTP response headers array.
     * 
     * @return GetBlobPropertiesResult
     */
    private function _getBlobPropertiesResultFromResponse($headers)
    {
        $result     = new GetBlobPropertiesResult();
        $properties = new BlobProperties();
        $d          = $headers[Resources::LAST_MODIFIED];
        $bType      = $headers[Resources::X_MS_BLOB_TYPE];
        $cLength    = intval($headers[Resources::CONTENT_LENGTH]);
        $lStatus    = Utilities::tryGetValue($headers, Resources::X_MS_LEASE_STATUS);
        $cType      = Utilities::tryGetValue($headers, Resources::CONTENT_TYPE);
        $cMD5       = Utilities::tryGetValue($headers, Resources::CONTENT_MD5);
        $cEncoding  = Utilities::tryGetValue($headers, Resources::CONTENT_ENCODING);
        $cLanguage  = Utilities::tryGetValue($headers, Resources::CONTENT_LANGUAGE);
        $cControl   = Utilities::tryGetValue($headers, Resources::CACHE_CONTROL);
        $etag       = $headers[Resources::ETAG];
        $metadata   = $this->getMetadataArray($headers);
        
        if (array_key_exists(Resources::X_MS_BLOB_SEQUENCE_NUMBER, $headers)) {
            $sNumber = intval($headers[Resources::X_MS_BLOB_SEQUENCE_NUMBER]);
            $properties->setSequenceNumber($sNumber);
        }
        
        $properties->setBlobType($bType);
        $properties->setCacheControl($cControl);
        $properties->setContentEncoding($cEncoding);
        $properties->setContentLanguage($cLanguage);
        $properties->setContentLength($cLength);
        $properties->setContentMD5($cMD5);
        $properties->setContentType($cType);
        $properties->setETag($etag);
        $properties->setLastModified(Utilities::rfc1123ToDateTime($d));
        $properties->setLeaseStatus($lStatus);
        
        $result->setProperties($properties);
        $result->setMetadata($metadata);
        
        return $result;
    }
    
    /**
     * Helper method for getContainerProperties and getContainerMetadata.
     * 
     * @param string                    $container The container name.
     * @param Models\BlobServiceOptions $options   The optional parameters.
     * @param string                    $operation The operation string. Should be
     * 'metadata' to get metadata.
     * 
     * @return Models\GetContainerPropertiesResult
     */
    private function _getContainerPropertiesImpl($container, $options = null,
        $operation = null
    ) {
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            $operation
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );

        $result   = new GetContainerPropertiesResult();
        $metadata = $this->getMetadataArray($response->getHeader());
        $date     = $response->getHeader(Resources::LAST_MODIFIED);
        $date     = Utilities::rfc1123ToDateTime($date);
        $result->setETag($response->getHeader(Resources::ETAG));
        $result->setMetadata($metadata);
        $result->setLastModified($date);
        
        return $result;
    }
    
    /**
     * Adds optional create blob headers.
     * 
     * @param CreateBlobOptions $options The optional parameters.
     * @param array             $headers The HTTP request headers.
     * 
     * @return array
     */
    private function _addCreateBlobOptionalHeaders($options, $headers)
    {
        $contentType         = $options->getContentType();
        $metadata            = $options->getMetadata();
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        
        if (!is_null($contentType)) {
            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_TYPE,
                $options->getContentType()
            );
        } else {
            $this->addOptionalHeader(
                $headers,
                Resources::CONTENT_TYPE,
                Resources::BINARY_FILE_TYPE
            );
        }
        $headers = $this->addMetadataHeaders($headers, $metadata);
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_ENCODING,
            $options->getContentEncoding()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_LANGUAGE,
            $options->getContentLanguage()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CACHE_CONTROL,
            $options->getCacheControl()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $leaseId
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_TYPE,
            $blobContentType
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_ENCODING,
            $blobContentEncoding
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LANGUAGE,
            $blobContentLanguage
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_MD5,
            $blobContentMD5
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CACHE_CONTROL,
            $blobCacheControl
        );
        
        return $headers;
    }
    
    /**
     * Adds Range header to the headers array.
     * 
     * @param array   $headers The HTTP request headers.
     * @param integer $start   The start byte.
     * @param integer $end     The end byte.
     * 
     * @return array
     */
    private function _addOptionalRangeHeader($headers, $start, $end)
    {
        if (!is_null($start) || !is_null($end)) {
            $range      = $start . '-' . $end;
            $rangeValue = 'bytes=' . $range;
            $this->addOptionalHeader($headers, Resources::RANGE, $rangeValue);
        }
        
        return $headers;
    }

    /**
     * Does the actual work for leasing a blob.
     * 
     * @param string             $leaseAction     The lease action string.
     * @param string             $container       The container name.
     * @param string             $blob            The blob to lease name.
     * @param string             $leaseId         The existing lease id.
     * @param BlobServiceOptions $options         The optional parameters.
     * @param AccessCondition    $accessCondition The access conditions.
     * 
     * @return array
     */
    private function _putLeaseImpl($leaseAction, $container, $blob, $leaseId, 
        $options, $accessCondition = null
    ) {
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::EMPTY_STRING;
        
        switch ($leaseAction) {
        case LeaseMode::ACQUIRE_ACTION:
            $statusCode = Resources::STATUS_CREATED;
            break;
        case LeaseMode::RENEW_ACTION:
            $statusCode = Resources::STATUS_OK;
            break;
        case LeaseMode::RELEASE_ACTION:
            $statusCode = Resources::STATUS_OK;
            break;
        case LeaseMode::BREAK_ACTION:
            $statusCode = Resources::STATUS_ACCEPTED;
            break;
        default:
            throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
        }
        
        if (!is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $accessCondition
        );

        $this->addOptionalHeader($headers, Resources::X_MS_LEASE_ID, $leaseId);
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ACTION,
            $leaseAction
        );
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'lease');
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );
        
        return $response->getHeader();
    }
    
    /**
     * Does actual work for create and clear blob pages.
     * 
     * @param string                 $action    Either clear or create.
     * @param string                 $container The container name.
     * @param string                 $blob      The blob name.
     * @param PageRange              $range     The page ranges.
     * @param string|resource        $content   The content stream.
     * @param CreateBlobPagesOptions $options   The optional parameters.
     * 
     * @return CreateBlobPagesResult
     */
    private function _updatePageBlobPagesImpl($action, $container, $blob, $range,
        $content, $options = null
    ) {
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isString($container, 'container');
        Validate::isTrue(
            $range instanceof PageRange,
            sprintf(
                Resources::INVALID_PARAM_MSG,
                'range',
                get_class(new PageRange())
            )
        );
        Validate::isTrue(
            is_string($content) || is_resource($content),
            sprintf(Resources::INVALID_PARAM_MSG, 'content', 'string|resource')
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_CREATED;
        // If read file failed for any reason it will throw an exception.
        $body = is_resource($content) ? stream_get_contents($content) : $content;
        
        if (is_null($options)) {
            $options = new CreateBlobPagesOptions();
        }
        
        $headers = $this->_addOptionalRangeHeader(
            $headers, $range->getStart(), $range->getEnd()
        );
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_PAGE_WRITE,
            $action
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );
        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'page');
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode, 
            $body
        );
        
        return CreateBlobPagesResult::create($response->getHeader());
    }
    
    /**
     * Gets the properties of the Blob service.
     * 
     * @param Models\BlobServiceOptions $options The optional parameters.
     * 
     * @return WindowsAzure\Common\Models\GetServicePropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
     */
    public function getServiceProperties($options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'service'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'properties'
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );
        $parsed   = $this->dataSerializer->unserialize($response->getBody());
        
        return GetServicePropertiesResult::create($parsed);
    }

    /**
     * Sets the properties of the Blob service.
     * 
     * It's recommended to use getServiceProperties, alter the returned object and
     * then use setServiceProperties with this altered object.
     * 
     * @param ServiceProperties         $serviceProperties The service properties.
     * @param Models\BlobServiceOptions $options           The optional parameters.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
     */
    public function setServiceProperties($serviceProperties, $options = null)
    {
        Validate::isTrue(
            $serviceProperties instanceof ServiceProperties,
            Resources::INVALID_SVC_PROP_MSG
        );
                
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $statusCode  = Resources::STATUS_ACCEPTED;
        $path        = Resources::EMPTY_STRING;
        $body        = $serviceProperties->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
    
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'service'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'properties'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode, 
            $body
        );
    }
    
    /**
     * Lists all of the containers in the given storage account.
     * 
     * @param Models\ListContainersOptions $options The optional parameters.
     * 
     * @return WindowsAzure\Blob\Models\ListContainersResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179352.aspx
     */
    public function listContainers($options = null)
    {
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListContainersOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'list'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_PREFIX,
            $options->getPrefix()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MARKER,
            $options->getMarker()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $options->getMaxResults()
        );
        $isInclude = $options->getIncludeMetadata();
        $isInclude = $isInclude ? 'metadata' : null;
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_INCLUDE,
            $isInclude
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );

        $parsed = $this->dataSerializer->unserialize($response->getBody());
        
        return ListContainersResult::create($parsed);
    }
    
    /**
     * Creates a new container in the given storage account.
     * 
     * @param string                        $container The container name.
     * @param Models\CreateContainerOptions $options   The optional parameters.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     */
    public function createContainer($container, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::notNullOrEmpty($container, 'container');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array(Resources::QP_REST_TYPE => 'container');
        $path        = $container;
        $statusCode  = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateContainerOptions();
        }

        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_PUBLIC_ACCESS,
            $options->getPublicAccess()
        );
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
    }
    
    /**
     * Creates a new container in the given storage account.
     * 
     * @param string                        $container The container name.
     * @param Models\DeleteContainerOptions $options   The optional parameters.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179408.aspx
     */
    public function deleteContainer($container, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::notNullOrEmpty($container, 'container');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_ACCEPTED;
        
        if (is_null($options)) {
            $options = new DeleteContainerOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );
    }
    
    /**
     * Returns all properties and metadata on the container.
     * 
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\GetContainerPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179370.aspx
     */
    public function getContainerProperties($container, $options = null)
    {
        return $this->_getContainerPropertiesImpl($container, $options);
    }
    
    /**
     * Returns only user-defined metadata for the specified container.
     * 
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\GetContainerPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691976.aspx 
     */
    public function getContainerMetadata($container, $options = null)
    {
        return $this->_getContainerPropertiesImpl($container, $options, 'metadata');
    }
    
    /**
     * Gets the access control list (ACL) and any container-level access policies 
     * for the container.
     * 
     * @param string                    $container The container name.
     * @param Models\BlobServiceOptions $options   The optional parameters.
     * 
     * @return Models\GetContainerAclResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
     */
    public function getContainerAcl($container, $options = null)
    {
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );
        
        $access       = $response->getHeader(Resources::X_MS_BLOB_PUBLIC_ACCESS);
        $etag         = $response->getHeader(Resources::ETAG);
        $modified     = $response->getHeader(Resources::LAST_MODIFIED);
        $modifiedDate = Utilities::convertToDateTime($modified);
        $parsed       = $this->dataSerializer->unserialize($response->getBody());
                
        return GetContainerAclResult::create($access, $etag, $modifiedDate, $parsed);
    }
    
    /**
     * Sets the ACL and any container-level access policies for the container.
     * 
     * @param string                    $container name
     * @param Models\ContainerAcl       $acl       access control list for container
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
     */
    public function setContainerAcl($container, $acl, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::notNullOrEmpty($acl, 'acl');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        $body        = $acl->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'acl'
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_PUBLIC_ACCESS,
            $acl->getPublicAccess()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );

        $this->send(
            $method,    
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode, 
            $body
        );
    }
    
    /**
     * Sets metadata headers on the container.
     * 
     * @param string                             $container name
     * @param array                              $metadata  metadata key/value pair.
     * @param Models\SetContainerMetadataOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
     */
    public function setContainerMetadata($container, $metadata, $options = null)
    {
        Validate::isString($container, 'container');
        $this->validateMetadata($metadata);
        
        $method      = Resources::HTTP_PUT;
        $headers     = $this->generateMetadataHeaders($metadata);
        $postParams  = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new SetContainerMetadataOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );

        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
    }
    
    /**
     * Lists all of the blobs in the given container.
     * 
     * @param string                  $container The container name.
     * @param Models\ListBlobsOptions $options   The optional parameters.
     * 
     * @return Models\ListBlobsResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
     */
    public function listBlobs($container, $options = null)
    {
        Validate::isString($container, 'container');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListBlobsOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_REST_TYPE,
            'container'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'list'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_PREFIX,
            str_replace('\\', '/', $options->getPrefix())
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MARKER,
            $options->getMarker()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_DELIMITER,
            $options->getDelimiter()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_MAX_RESULTS,
            $options->getMaxResults()
        );
        
        $includeMetadata         = $options->getIncludeMetadata();
        $includeSnapshots        = $options->getIncludeSnapshots();
        $includeUncommittedBlobs = $options->getIncludeUncommittedBlobs();
        
        $includeValue = $this->groupQueryValues(
            array(
                $includeMetadata ? 'metadata' : null, 
                $includeSnapshots ? 'snapshots' : null, 
                $includeUncommittedBlobs ? 'uncommittedblobs' : null
            )
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_INCLUDE,
            $includeValue
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams,
            $postParams,
            $path, 
            $statusCode
        );

        $parsed = $this->dataSerializer->unserialize($response->getBody());
        
        return ListBlobsResult::create($parsed);
    }
    
    /**
     * Creates a new page blob. Note that calling createPageBlob to create a page
     * blob only initializes the blob.
     * To add content to a page blob, call createBlobPages method.
     * 
     * @param string                   $container The container name.
     * @param string                   $blob      The blob name.
     * @param integer                  $length    Specifies the maximum size for the
     * page blob, up to 1 TB. The page blob size must be aligned to a 512-byte 
     * boundary.
     * @param Models\CreateBlobOptions $options   The optional parameters.
     * 
     * @return CopyBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createPageBlob($container, $blob, $length, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isInteger($length, 'length');
        Validate::notNull($length, 'length');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateBlobOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_TYPE,
            BlobType::PAGE_BLOB
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LENGTH,
            $length
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_SEQUENCE_NUMBER,
            $options->getSequenceNumber()
        );
        $headers = $this->_addCreateBlobOptionalHeaders($options, $headers);
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );
        
        return CopyBlobResult::create($response->getHeader());
    }
    
    /**
     * Creates a new block blob or updates the content of an existing block blob.
     * 
     * Updating an existing block blob overwrites any existing metadata on the blob.
     * Partial updates are not supported with createBlockBlob the content of the
     * existing blob is overwritten with the content of the new blob. To perform a
     * partial update of the content o  f a block blob, use the createBlockList
     * method.
     * Note that the default content type is application/octet-stream.
     * 
     * @param string                   $container The name of the container.
     * @param string                   $blob      The name of the blob.
     * @param string|resource          $content   The content of the blob.
     * @param Models\CreateBlobOptions $options   The optional parameters.
     * 
     * @return CopyBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createBlockBlob($container, $blob, $content, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isTrue(
            is_string($content) || is_resource($content),
            sprintf(Resources::INVALID_PARAM_MSG, 'content', 'string|resource')
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $bodySize    = 0;
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateBlobOptions();
        }
        
        if (is_resource($content)) {
            $cStat = fstat($content);
            // if the resource is a remote file, $cStat will be false
            if ($cStat) {
                $bodySize = $cStat['size'];
            }
        } else {
            $bodySize = strlen($content);
        }

        // if we have a size we can try to one shot this, else failsafe on block upload
        if ($bodySize && $bodySize <= $this->_SingleBlobUploadThresholdInBytes) {
            $headers = $this->_addCreateBlobOptionalHeaders($options, $headers);
            
            $this->addOptionalHeader(
                $headers,
                Resources::X_MS_BLOB_TYPE,
                BlobType::BLOCK_BLOB
            );
            $this->addOptionalQueryParam(
                $queryParams,
                Resources::QP_TIMEOUT,
                $options->getTimeout()
            );

            // If read file failed for any reason it will throw an exception.
            $body = is_resource($content) ? stream_get_contents($content) : $content;

            $response = $this->send(
                $method, 
                $headers, 
                $queryParams, 
                $postParams,
                $path, 
                $statusCode,
                $body
            );
        } else {
            // This is for large or failsafe upload
            $end       = 0;
            $counter   = 0;
            $body      = '';
            $blockIds  = array();
            // if threshold is lower than 4mb, honor threshold, else use 4mb
            $blockSize = ($this->_SingleBlobUploadThresholdInBytes < 4194304) ? $this->_SingleBlobUploadThresholdInBytes : 4194304;
            while(!$end) {
                if (is_resource($content)) {
                    $body = fread($content, $blockSize);
                    if (feof($content)) {
                        $end = 1;
                    }
                } else {
                    if (strlen($content) <= $blockSize) {
                        $body = $content;
                        $end = 1;
                    } else {
                        $body = substr($content, 0, $blockSize);
                        $content = substr_replace($content, '', 0, $blockSize);
                    }
                }
                $block = new Block();
                $block->setBlockId(base64_encode(str_pad($counter++, '0', 6)));
                $block->setType('Uncommitted');
                array_push($blockIds, $block);
                $this->createBlobBlock($container, $blob, $block->getBlockId(), $body);
            }
            $response = $this->commitBlobBlocks($container, $blob, $blockIds, $options);
        }
        return CopyBlobResult::create($response->getHeader());
    }
    
    /**
     * Clears a range of pages from the blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\PageRange              $range     Can be up to the value of the
     * blob's full size. Note that ranges must be aligned to 512 (0-511, 512-1023)
     * @param Models\CreateBlobPagesOptions $options   optional parameters
     * 
     * @return Models\CreateBlobPagesResult.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function clearBlobPages($container, $blob, $range, $options = null)
    {
        return $this->_updatePageBlobPagesImpl(
            PageWriteOption::CLEAR_OPTION,
            $container,
            $blob,
            $range,
            Resources::EMPTY_STRING,
            $options
        );
    }
    
    /**
     * Creates a range of pages to a page blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\PageRange              $range     Can be up to 4 MB in size
     * Note that ranges must be aligned to 512 (0-511, 512-1023)
     * @param string                        $content   the blob contents.
     * @param Models\CreateBlobPagesOptions $options   optional parameters
     * 
     * @return Models\CreateBlobPagesResult.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function createBlobPages($container, $blob, $range, $content,
        $options = null
    ) {
        return $this->_updatePageBlobPagesImpl(
            PageWriteOption::UPDATE_OPTION,
            $container,
            $blob,
            $range,
            $content,
            $options
        );
    }
    
    /**
     * Creates a new block to be committed as part of a block blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param string                        $blockId   must be less than or equal to 
     * 64 bytes in size. For a given blob, the length of the value specified for the
     * blockid parameter must be the same size for each block.
     * @param string                        $content   the blob block contents
     * @param Models\CreateBlobBlockOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135726.aspx
     */
    public function createBlobBlock($container, $blob, $blockId, $content,
        $options = null
    ) {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isString($blockId, 'blockId');
        Validate::notNullOrEmpty($blockId, 'blockId');
        Validate::isTrue(
            is_string($content) || is_resource($content),
            sprintf(Resources::INVALID_PARAM_MSG, 'content', 'string|resource')
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_CREATED;
        $body        = $content;
        
        if (is_null($options)) {
            $options = new CreateBlobBlockOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_MD5,
            $options->getContentMD5()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            Resources::URL_ENCODED_CONTENT_TYPE
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'block'
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_BLOCKID,
            base64_encode($blockId)
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode,
            $body
        );
        
        return CopyBlobResult::create($response->getHeader());
    }
    
    /**
     * This method writes a blob by specifying the list of block IDs that make up the
     * blob. In order to be written as part of a blob, a block must have been 
     * successfully written to the server in a prior createBlobBlock method.
     * 
     * You can call Put Block List to update a blob by uploading only those blocks 
     * that have changed, then committing the new and existing blocks together. 
     * You can do this by specifying whether to commit a block from the committed 
     * block list or from the uncommitted block list, or to commit the most recently
     * uploaded version of the block, whichever list it may belong to.
     * 
     * @param string                         $container The container name.
     * @param string                         $blob      The blob name.
     * @param Models\BlockList|array         $blockList The block entries.
     * @param Models\CommitBlobBlocksOptions $options   The optional parameters.
     * 
     * @return CopyBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx 
     */
    public function commitBlobBlocks($container, $blob, $blockList, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        Validate::isTrue(
            $blockList instanceof BlockList || is_array($blockList),
            sprintf(
                Resources::INVALID_PARAM_MSG,
                'blockList',
                get_class(new BlockList())
            )
        );
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_CREATED;
        $isArray     = is_array($blockList);
        $blockList   = $isArray ? BlockList::create($blockList) : $blockList;
        $body        = $blockList->toXml($this->dataSerializer);
        
        if (is_null($options)) {
            $options = new CommitBlobBlocksOptions();
        }
        
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        $contentType         = Resources::URL_ENCODED_CONTENT_TYPE;
        
        $metadata = $options->getMetadata();
        $headers  = $this->generateMetadataHeaders($metadata);
        $headers  = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $leaseId
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CACHE_CONTROL,
            $blobCacheControl
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_TYPE,
            $blobContentType
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_ENCODING,
            $blobContentEncoding
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LANGUAGE,
            $blobContentLanguage
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_MD5,
            $blobContentMD5
        );
        $this->addOptionalHeader(
            $headers,
            Resources::CONTENT_TYPE,
            $contentType
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'blocklist'
        );
        
        return $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode, 
            $body
        );
    }
    
    /**
     * Retrieves the list of blocks that have been uploaded as part of a block blob.
     * 
     * There are two block lists maintained for a blob:
     * 1) Committed Block List: The list of blocks that have been successfully 
     *    committed to a given blob with commitBlobBlocks.
     * 2) Uncommitted Block List: The list of blocks that have been uploaded for a 
     *    blob using Put Block (REST API), but that have not yet been committed. 
     *    These blocks are stored in Windows Azure in association with a blob, but do
     *    not yet form part of the blob.
     * 
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param Models\ListBlobBlocksOptions $options   optional parameters
     * 
     * @return Models\ListBlobBlocksResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179400.aspx
     */
    public function listBlobBlocks($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListBlobBlocksOptions();
        }
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_BLOCK_LIST_TYPE,
            $options->getBlockListType()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'blocklist'
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams,
            $path, 
            $statusCode
        );

        $parsed = $this->dataSerializer->unserialize($response->getBody());
        
        return ListBlobBlocksResult::create($response->getHeader(), $parsed);
    }
    
    /**
     * Returns all properties and metadata on the blob.
     * 
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\GetBlobPropertiesOptions $options   optional parameters
     * 
     * @return Models\GetBlobPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179394.aspx
     */
    public function getBlobProperties($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_HEAD;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new GetBlobPropertiesOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        
        return $this->_getBlobPropertiesResultFromResponse($response->getHeader());
    }
    
    /**
     * Returns all properties and metadata on the blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\GetBlobMetadataOptions $options   optional parameters
     * 
     * @return Models\GetBlobMetadataResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179350.aspx
     */
    public function getBlobMetadata($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_HEAD;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new GetBlobMetadataOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $metadata = $this->getMetadataArray($response->getHeader());
        
        return GetBlobMetadataResult::create($response->getHeader(), $metadata);
    }
    
    /**
     * Returns a list of active page ranges for a page blob. Active page ranges are 
     * those that have been populated with data.
     * 
     * @param string                           $container name of the container
     * @param string                           $blob      name of the blob
     * @param Models\ListPageBlobRangesOptions $options   optional parameters
     * 
     * @return Models\ListPageBlobRangesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691973.aspx
     */
    public function listPageBlobRanges($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $queryParams = array();
        $postParams  = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListPageBlobRangesOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $headers = $this->_addOptionalRangeHeader(
            $headers, $options->getRangeStart(), $options->getRangeEnd()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'pagelist'
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $parsed   = $this->dataSerializer->unserialize($response->getBody());
        
        return ListPageBlobRangesResult::create($response->getHeader(), $parsed);
    }
    
    /**
     * Sets system properties defined for a blob.
     * 
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\SetBlobPropertiesOptions $options   optional parameters
     * 
     * @return Models\SetBlobPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691966.aspx
     */
    public function setBlobProperties($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new SetBlobPropertiesOptions();
        }
        
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentLength   = $options->getBlobContentLength();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        $sNumberAction       = $options->getSequenceNumberAction();
        $sNumber             = $options->getSequenceNumber();
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $leaseId
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CACHE_CONTROL,
            $blobCacheControl
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_TYPE,
            $blobContentType
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_ENCODING,
            $blobContentEncoding
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LANGUAGE,
            $blobContentLanguage
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_LENGTH,
            $blobContentLength
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_CONTENT_MD5,
            $blobContentMD5
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_SEQUENCE_NUMBER_ACTION,
            $sNumberAction
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_BLOB_SEQUENCE_NUMBER,
            $sNumber
        );

        $this->addOptionalQueryParam($queryParams, Resources::QP_COMP, 'properties');
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        
        return SetBlobPropertiesResult::create($response->getHeader());
    }
    
    /**
     * Sets metadata headers on the blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param array                         $metadata  key/value pair representation
     * @param Models\SetBlobMetadataOptions $options   optional parameters
     * 
     * @return Models\SetBlobMetadataResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179414.aspx
     */
    public function setBlobMetadata($container, $blob, $metadata, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        $this->validateMetadata($metadata);
        
        $method      = Resources::HTTP_PUT;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new SetBlobMetadataOptions();
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        $headers = $this->addMetadataHeaders($headers, $metadata);
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_COMP,
            'metadata'
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        
        return SetBlobMetadataResult::create($response->getHeader());
    }
    
    /**
     * Reads or downloads a blob from the system, including its metadata and 
     * properties.
     * 
     * @param string                $container name of the container
     * @param string                $blob      name of the blob
     * @param Models\GetBlobOptions $options   optional parameters
     * 
     * @return Models\GetBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function getBlob($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        
        $method      = Resources::HTTP_GET;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = array(
            Resources::STATUS_OK,
            Resources::STATUS_PARTIAL_CONTENT
        );
        
        if (is_null($options)) {
            $options = new GetBlobOptions();
        }
        
        $getMD5  = $options->getComputeRangeMD5();
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        $headers = $this->_addOptionalRangeHeader(
            $headers, $options->getRangeStart(), $options->getRangeEnd()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_RANGE_GET_CONTENT_MD5,
            $getMD5 ? 'true' : null
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_SNAPSHOT,
            $options->getSnapshot()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
        $metadata = $this->getMetadataArray($response->getHeader());
        
        return GetBlobResult::create(
            $response->getHeader(),
            $response->getBody(),
            $metadata
        );
    }
    
    /**
     * Deletes a blob or blob snapshot.
     * 
     * Note that if the snapshot entry is specified in the $options then only this
     * blob snapshot is deleted. To delete all blob snapshots, do not set Snapshot 
     * and just set getDeleteSnaphotsOnly to true.
     * 
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param Models\DeleteBlobOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     */
    public function deleteBlob($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method      = Resources::HTTP_DELETE;
        $headers     = array();
        $postParams  = array();
        $queryParams = array();
        $path        = $this->_createPath($container, $blob);
        $statusCode  = Resources::STATUS_ACCEPTED;
        
        if (is_null($options)) {
            $options = new DeleteBlobOptions();
        }
        
        if (is_null($options->getSnapshot())) {
            $delSnapshots = $options->getDeleteSnaphotsOnly() ? 'only' : 'include';
            $this->addOptionalHeader(
                $headers,
                Resources::X_MS_DELETE_SNAPSHOTS,
                $delSnapshots
            );
        } else {
            $this->addOptionalQueryParam(
                $queryParams,
                Resources::QP_SNAPSHOT,
                $options->getSnapshot()
            );
        }
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, $options->getAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $statusCode
        );
    }
    
    /**
     * Creates a snapshot of a blob.
     * 
     * @param string                           $container The name of the container.
     * @param string                           $blob      The name of the blob.
     * @param Models\CreateBlobSnapshotOptions $options   The optional parameters.
     * 
     * @return Models\CreateBlobSnapshotResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
     */
    public function createBlobSnapshot($container, $blob, $options = null)
    {
        Validate::isString($container, 'container');
        Validate::isString($blob, 'blob');
        Validate::notNullOrEmpty($blob, 'blob');
        
        $method             = Resources::HTTP_PUT;
        $headers            = array();
        $postParams         = array();
        $queryParams        = array();
        $path               = $this->_createPath($container, $blob);
        $expectedStatusCode = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateBlobSnapshotOptions();
        }  
        
        $queryParams[Resources::QP_COMP] = 'snapshot';
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );

        $headers = $this->addOptionalAccessConditionHeader(
            $headers,
            $options->getAccessCondition()
        );
        $headers = $this->addMetadataHeaders($headers, $options->getMetadata());
        $this->addOptionalHeader(
            $headers,
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $path, 
            $expectedStatusCode
        );
        
        return CreateBlobSnapshotResult::create($response->getHeader());
    }
    
    /**
     * Copies a source blob to a destination blob within the same storage account.
     * 
     * @param string                 $destinationContainer name of the destination 
     * container
     * @param string                 $destinationBlob      name of the destination 
     * blob
     * @param string                 $sourceContainer      name of the source 
     * container
     * @param string                 $sourceBlob           name of the source
     * blob
     * @param Models\CopyBlobOptions $options              optional parameters
     * 
     * @return CopyBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd894037.aspx
     */
    public function copyBlob(
        $destinationContainer, 
        $destinationBlob,
        $sourceContainer, 
        $sourceBlob, 
        $options = null
    ) {

        $method              = Resources::HTTP_PUT;
        $headers             = array();
        $postParams          = array();
        $queryParams         = array();
        $destinationBlobPath = $this->_createPath(
            $destinationContainer,
            $destinationBlob
        );
        $statusCode          = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CopyBlobOptions();
        }
        
        $this->addOptionalQueryParam(
            $queryParams,
            Resources::QP_TIMEOUT,
            $options->getTimeout()
        );
        
        $sourceBlobPath = $this->_getCopyBlobSourceName(
            $sourceContainer, 
            $sourceBlob,
            $options
        );
        
        $headers = $this->addOptionalAccessConditionHeader(
            $headers, 
            $options->getAccessCondition()
        );
        
        $headers = $this->addOptionalSourceAccessConditionHeader(
            $headers,
            $options->getSourceAccessCondition()
        );
        
        $this->addOptionalHeader(
            $headers, 
            Resources::X_MS_COPY_SOURCE, 
            $sourceBlobPath
        );
        
        $headers = $this->addMetadataHeaders($headers, $options->getMetadata());
        
        $this->addOptionalHeader(
            $headers, 
            Resources::X_MS_LEASE_ID,
            $options->getLeaseId()
        );
        
        $this->addOptionalHeader(
            $headers, 
            Resources::X_MS_SOURCE_LEASE_ID,
            $options->getSourceLeaseId()
        );
        
        $response = $this->send(
            $method, 
            $headers, 
            $queryParams, 
            $postParams, 
            $destinationBlobPath, 
            $statusCode
        );
        
        return CopyBlobResult::create($response->getHeader());
    }
        
    /**
     * Establishes an exclusive one-minute write lock on a blob. To write to a locked
     * blob, a client must provide a lease ID.
     * 
     * @param string                     $container name of the container
     * @param string                     $blob      name of the blob
     * @param Models\AcquireLeaseOptions $options   optional parameters
     * 
     * @return Models\AcquireLeaseResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function acquireLease($container, $blob, $options = null)
    {
        $headers = $this->_putLeaseImpl(
            LeaseMode::ACQUIRE_ACTION,
            $container,
            $blob,
            null /* leaseId */,
            is_null($options) ? new AcquireLeaseOptions() : $options,
            is_null($options) ? null : $options->getAccessCondition()
        );
        
        return AcquireLeaseResult::create($headers);
    }
    
    /**
     * Renews an existing lease
     * 
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\AcquireLeaseResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function renewLease($container, $blob, $leaseId, $options = null)
    {
        $headers = $this->_putLeaseImpl(
            LeaseMode::RENEW_ACTION,
            $container,
            $blob,
            $leaseId,
            is_null($options) ? new BlobServiceOptions() : $options
        );
        
        return AcquireLeaseResult::create($headers);
    }
    
    /**
     * Frees the lease if it is no longer needed so that another client may 
     * immediately acquire a lease against the blob.
     * 
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function releaseLease($container, $blob, $leaseId, $options = null)
    {
        $this->_putLeaseImpl(
            LeaseMode::RELEASE_ACTION,
            $container,
            $blob,
            $leaseId,
            is_null($options) ? new BlobServiceOptions() : $options
        );
    }
    
    /**
     * Ends the lease but ensure that another client cannot acquire a new lease until
     * the current lease period has expired.
     * 
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return BreakLeaseResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function breakLease($container, $blob, $options = null)
    {
        $headers = $this->_putLeaseImpl(
            LeaseMode::BREAK_ACTION,
            $container,
            $blob,
            null,
            is_null($options) ? new BlobServiceOptions() : $options
        );
        
        return BreakLeaseResult::create($headers);
    }
}
