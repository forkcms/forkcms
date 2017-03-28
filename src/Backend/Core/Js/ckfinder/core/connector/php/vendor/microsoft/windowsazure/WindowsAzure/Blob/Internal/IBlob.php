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
 * @package   WindowsAzure\Blob\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Blob\Internal;
use WindowsAzure\Common\Internal\FilterableService;

/**
 * This interface has all REST APIs provided by Windows Azure for Blob service.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 * @see       http://msdn.microsoft.com/en-us/library/windowsazure/dd135733.aspx
 */
interface IBlob extends FilterableService
{
    /**
    * Gets the properties of the Blob service.
    * 
    * @param Models\BlobServiceOptions $options optional blob service options.
    * 
    * @return WindowsAzure\Common\Models\GetServicePropertiesResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
    */
    public function getServiceProperties($options = null);

    /**
    * Sets the properties of the Blob service.
    * 
    * @param ServiceProperties         $serviceProperties new service properties
    * @param Models\BlobServiceOptions $options           optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
    */
    public function setServiceProperties($serviceProperties, $options = null);

    /**
    * Lists all of the containers in the given storage account.
    * 
    * @param Models\ListContainersOptions $options optional parameters
    * 
    * @return WindowsAzure\Blob\Models\ListContainersResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179352.aspx
    */
    public function listContainers($options = null);

    /**
    * Creates a new container in the given storage account.
    * 
    * @param string                        $container name
    * @param Models\CreateContainerOptions $options   optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
    */
    public function createContainer($container, $options = null);

    /**
    * Creates a new container in the given storage account.
    * 
    * @param string                        $container name
    * @param Models\DeleteContainerOptions $options   optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179408.aspx
    */
    public function deleteContainer($container, $options = null);

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
    public function getContainerProperties($container, $options = null);

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
    public function getContainerMetadata($container, $options = null);

    /**
    * Gets the access control list (ACL) and any container-level access policies 
    * for the container.
    * 
    * @param string                    $container name
    * @param Models\BlobServiceOptions $options   optional parameters
    * 
    * @return Models\GetContainerAclResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
    */
    public function getContainerAcl($container, $options = null);

    /**
    * Sets the ACL and any container-level access policies for the container.
    * 
    * @param string                    $container name
    * @param Models\ContainerAcl       $acl       access control list for container
    * @param Models\BlobServiceOptions $options   optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
    */
    public function setContainerAcl($container, $acl, $options = null);

    /**
    * Sets metadata headers on the container.
    * 
    * @param string                             $container name
    * @param array                              $metadata  metadata key/value pair.
    * @param Models\SetContainerMetadataOptions $options   optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
    */
    public function setContainerMetadata($container, $metadata, $options = null);

    /**
    * Lists all of the blobs in the given container.
    * 
    * @param string                  $container name
    * @param Models\ListBlobsOptions $options   optional parameters
    * 
    * @return Models\ListBlobsResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
    */
    public function listBlobs($container, $options = null);

    /**
    * Creates a new page blob. Note that calling createPageBlob to create a page
    * blob only initializes the blob.
    * To add content to a page blob, call createBlobPages method.
    * 
    * @param string                   $container name of the container
    * @param string                   $blob      name of the blob
    * @param int                      $length    specifies the maximum size for the
    * page blob, up to 1 TB. The page blob size must be aligned to a 512-byte 
    * boundary.
    * @param Models\CreateBlobOptions $options   optional parameters
    * 
    * @return CopyBlobResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
    */
    public function createPageBlob($container, $blob, $length, $options = null);

    /**
    * Creates a new block blob or updates the content of an existing block blob.
    * Updating an existing block blob overwrites any existing metadata on the blob.
    * Partial updates are not supported with createBlockBlob; the content of the
    * existing blob is overwritten with the content of the new blob. To perform a
    * partial update of the content of a block blob, use the createBlockList method.
    * 
    * @param string                   $container name of the container
    * @param string                   $blob      name of the blob
    * @param string                   $content   content of the blob
    * @param Models\CreateBlobOptions $options   optional parameters
    * 
    * @return CopyBlobResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
    */
    public function createBlockBlob($container, $blob, $content, $options = null);

    /**
    * Clears a range of pages from the blob.
    * 
    * @param string                        $container name of the container
    * @param string                        $blob      name of the blob
    * @param Models\PageRange              $range     Can be up to the value of the
    * blob's full size.
    * @param Models\CreateBlobPagesOptions $options   optional parameters
    * 
    * @return Models\CreateBlobPagesResult.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
    */
    public function clearBlobPages($container, $blob, $range, $options = null);

    /**
    * Creates a range of pages to a page blob.
    * 
    * @param string                        $container name of the container
    * @param string                        $blob      name of the blob
    * @param Models\PageRange              $range     Can be up to 4 MB in size
    * @param string                        $content   the blob contents
    * @param Models\CreateBlobPagesOptions $options   optional parameters
    * 
    * @return Models\CreateBlobPagesResult.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
    */
    public function createBlobPages($container, $blob, $range, $content,
        $options = null
    );

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
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135726.aspx
    */
    public function createBlobBlock($container, $blob, $blockId, $content,
        $options = null
    );

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
    * @param string                         $container name of the container
    * @param string                         $blob      name of the blob
    * @param Models\BlockList               $blockList the block list entries
    * @param Models\CommitBlobBlocksOptions $options   optional parameters
    * 
    * @return none.
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx 
    */
    public function commitBlobBlocks($container, $blob, $blockList, $options = null);

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
    public function listBlobBlocks($container, $blob, $options = null);

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
    public function getBlobProperties($container, $blob, $options = null);

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
    public function getBlobMetadata($container, $blob, $options = null);

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
    public function listPageBlobRanges($container, $blob, $options = null);

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
    public function setBlobProperties($container, $blob, $options = null);

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
    public function setBlobMetadata($container, $blob, $metadata, $options = null);

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
    public function getBlob($container, $blob, $options = null);

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
    public function deleteBlob($container, $blob, $options = null);

    /**
    * Creates a snapshot of a blob.
    * 
    * @param string                           $container name of the container
    * @param string                           $blob      name of the blob
    * @param Models\CreateBlobSnapshotOptions $options   optional parameters
    * 
    * @return Models\CreateBlobSnapshotResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
    */
    public function createBlobSnapshot($container, $blob, $options = null);

    /**
    * Copies a source blob to a destination blob within the same storage account.
    * 
    * @param string                 $destinationContainer name of container
    * @param string                 $destinationBlob      name of blob
    * @param string                 $sourceContainer      name of container
    * @param string                 $sourceBlob           name of blob
    * @param Models\CopyBlobOptions $options              optional parameters
    * 
    * @return CopyBlobResult
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd894037.aspx
    */
    public function copyBlob($destinationContainer, $destinationBlob,
        $sourceContainer, $sourceBlob, $options = null
    );

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
    public function acquireLease($container, $blob, $options = null);

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
    public function renewLease($container, $blob, $leaseId, $options = null);

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
    public function releaseLease($container, $blob, $leaseId, $options = null);

    /**
    * Ends the lease but ensure that another client cannot acquire a new lease until
    * the current lease period has expired.
    * 
    * @param string                    $container name of the container
    * @param string                    $blob      name of the blob
    * @param Models\BlobServiceOptions $options   optional parameters
    * 
    * @return none
    * 
    * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
    */
    public function breakLease($container, $blob, $options = null);
}


