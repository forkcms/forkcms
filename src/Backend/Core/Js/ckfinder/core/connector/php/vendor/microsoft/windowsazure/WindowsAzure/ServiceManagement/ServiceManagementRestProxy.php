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
 * @package   WindowsAzure\ServiceManagement
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceManagement;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\RestProxy;
use WindowsAzure\Common\Internal\Http\HttpCallContext;
use WindowsAzure\Common\Internal\Serialization\XmlSerializer;
use WindowsAzure\ServiceManagement\Internal\IServiceManagement;
use WindowsAzure\ServiceManagement\Models\CreateAffinityGroupOptions;
use WindowsAzure\ServiceManagement\Models\AffinityGroup;
use WindowsAzure\ServiceManagement\Models\ListAffinityGroupsResult;
use WindowsAzure\ServiceManagement\Models\GetAffinityGroupPropertiesResult;
use WindowsAzure\ServiceManagement\Models\ListLocationsResult;
use WindowsAzure\ServiceManagement\Models\StorageService;
use WindowsAzure\ServiceManagement\Models\ListStorageServicesResult;
use WindowsAzure\ServiceManagement\Models\GetOperationStatusResult;
use WindowsAzure\ServiceManagement\Models\AsynchronousOperationResult;
use WindowsAzure\ServiceManagement\Models\UpdateServiceOptions;
use WindowsAzure\ServiceManagement\Models\GetStorageServicePropertiesResult;
use WindowsAzure\ServiceManagement\Models\GetStorageServiceKeysResult;
use WindowsAzure\ServiceManagement\Models\ListHostedServicesResult;
use WindowsAzure\ServiceManagement\Models\HostedService;
use WindowsAzure\ServiceManagement\Models\GetHostedServicePropertiesOptions;
use WindowsAzure\ServiceManagement\Models\GetHostedServicePropertiesResult;
use WindowsAzure\ServiceManagement\Models\DeploymentSlot;
use WindowsAzure\ServiceManagement\Models\CreateDeploymentOptions;
use WindowsAzure\ServiceManagement\Models\GetDeploymentResult;
use WindowsAzure\ServiceManagement\Models\DeploymentStatus;
use WindowsAzure\ServiceManagement\Models\Mode;

/**
 * This class constructs HTTP requests and receive HTTP responses for service
 * management service layer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ServiceManagementRestProxy extends RestProxy
    implements IServiceManagement
{
    /**
     * @var string
     */
    private $_subscriptionId;

    /**
     * Sends an order request for the specified role instance.
     *
     * @param string               $name     The hosted service name.
     * @param string               $roleName The role instance name.
     * @param GetDeploymentOptions $options  The optional parameters.
     * @param string               $order    The order name which is used as value
     * for query parameter 'comp'.
     *
     * @return AsynchronousOperationResult
     */
    private function _sendRoleInstanceOrder($name, $roleName, $options, $order)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($roleName, 'roleName');
        Validate::notNullOrEmpty($roleName, 'roleName');
        Validate::notNullOrEmpty($options, 'options');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getRoleInstancePath($name, $options, $roleName));
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->addQueryParameter(Resources::QP_COMP, $order);
        $context->addHeader(Resources::CONTENT_TYPE, Resources::XML_CONTENT_TYPE);
        $context->addHeader(Resources::CONTENT_LENGTH_NO_SPACE, 0);

        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Constructs URI path for given service management resource.
     *
     * @param string $serviceManagementResource The resource name.
     * @param string $name                      The service name.
     *
     * @return string
     */
    private function _getPath($serviceManagementResource, $name)
    {
        $path = $this->_subscriptionId . '/' . $serviceManagementResource;

        if (!is_null($name)) {
            $path .= '/' . $name;
        }

        return $path;
    }

    /**
     * Constructs URI path for locations.
     *
     * @return string
     */
    private function _getLocationPath()
    {
        return $this->_getPath('locations', null);
    }

    /**
     * Constructs URI path for affinity group.
     *
     * @param string $name The affinity group name.
     *
     * @return string
     */
    private function _getAffinityGroupPath($name = null)
    {
        return $this->_getPath('affinitygroups', $name);
    }

    /**
     * Constructs URI path for storage service.
     *
     * @param string $name The storage service name.
     *
     * @return string
     */
    private function _getStorageServicePath($name = null)
    {
        return $this->_getPath('services/storageservices', $name);
    }

    /**
     * Constructs URI path for hosted service.
     *
     * @param string $name The hosted service name.
     *
     * @return string
     */
    private function _getHostedServicePath($name = null)
    {
        return $this->_getPath('services/hostedservices', $name);
    }

    /**
     * Constructs URI path for deployment slot.
     *
     * @param string $name The hosted service name.
     * @param string $slot The deployment slot name.
     *
     * @return string
     */
    private function _getDeploymentPathUsingSlot($name, $slot)
    {
        $path = "services/hostedservices/$name/deploymentslots";
        return $this->_getPath($path, $slot);
    }

    /**
     * Constructs URI path for deployment slot.
     *
     * @param string $name           The hosted service name.
     * @param string $deploymentName The deployment slot name.
     *
     * @return string
     */
    private function _getDeploymentPathUsingName($name, $deploymentName)
    {
        $path = "services/hostedservices/$name/deployments";
        return $this->_getPath($path, $deploymentName);
    }

    /**
     * Gets role instance path.
     *
     * @param string               $name     The hosted service name.
     * @param GetDeploymentOptions $options  The get deployment options.
     * @param string               $roleName The role instance name.
     *
     * @return string
     */
    private function _getRoleInstancePath($name, $options, $roleName)
    {
        $path = $this->_getDeploymentPath($name, $options) . '/roleinstances';
        return "$path/$roleName";
    }

    /**
     * Gets the deployment URI path using the slot or name.
     *
     * @param string               $name    The hosted service name.
     * @param GetDeploymentOptions $options The optional parameters.
     *
     * @return string
     */
    private function _getDeploymentPath($name, $options)
    {
        $slot           = $options->getSlot();
        $deploymentName = $options->getDeploymentName();
        $path           = null;

        Validate::isTrue(
            !empty($slot) || !empty($deploymentName),
            Resources::INVALID_DEPLOYMENT_LOCATOR_MSG
        );

        if (!empty($slot)) {
            $path = $this->_getDeploymentPathUsingSlot($name, $slot);
        } else {
            $path = $this->_getDeploymentPathUsingName($name, $deploymentName);
        }

        return $path;
    }

    /**
     * Constructs URI path for storage service key.
     *
     * @param string $name The storage service name.
     *
     * @return string
     */
    private function _getStorageServiceKeysPath($name = null)
    {
        return $this->_getPath('services/storageservices', $name) . '/keys';
    }

    /**
     * Constructs URI path for operations.
     *
     * @param string $name The operation resource name.
     *
     * @return string
     */
    private function _getOperationPath($name = null)
    {
        return $this->_getPath('operations', $name);
    }

    /**
     * Constructs request XML including windows azure XML namesoace.
     *
     * @param array  $xmlElements The XML elements associated with their values.
     * @param string $root        The XML root name.
     *
     * @return string
     */
    private function _createRequestXml($xmlElements, $root)
    {
        $requestArray = array(
            Resources::XTAG_NAMESPACE => array(Resources::WA_XML_NAMESPACE => null)
        );

        foreach ($xmlElements as $tagName => $value) {
            if (!empty($value)) {
                $requestArray[$tagName] = $value;
            }
        }

        $properties = array(XmlSerializer::ROOT_NAME => $root);

        return $this->dataSerializer->serialize($requestArray, $properties);
    }

    /**
     * Prepare configuration XML for sending via REST API
     *
     * @param string|resource         $configuration  The configuration file contents
     * or file stream.
     * @return string
     */
    private function _encodeConfiguration($value) {
        $value = is_resource($value) ? stream_get_contents($value) : $value;
        $value = base64_encode($value);

        // Cut the BOM if any. If the xml configuration would start with BOM Azure treats it as invalid XML file.
        if (strpos($value, '77u/') === 0) {
            $value = substr($value, 4);
        }

        return $value;
    }

    /**
     * Initializes new ServiceManagementRestProxy object.
     *
     * @param IHttpClient $channel        The HTTP channel.
     * @param string      $subscriptionId The user subscription id.
     * @param string      $uri            The service URI.
     * @param ISerializer $dataSerializer The data serializer.
     */
    public function __construct($channel, $subscriptionId, $uri, $dataSerializer)
    {
        parent::__construct(
            $channel,
            $dataSerializer,
            $uri
        );
        $this->_subscriptionId = $subscriptionId;
    }

    /**
     * Lists the storage accounts available under the current subscription.
     *
     * @return ListStorageServicesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460787.aspx
     */
    public function listStorageServices()
    {
        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getStorageServicePath());
        $context->addStatusCode(Resources::STATUS_OK);

        $response   = $this->sendContext($context);
        $serialized = $this->dataSerializer->unserialize($response->getBody());

        return ListStorageServicesResult::create($serialized);
    }

    /**
     * Returns the system properties for the specified storage account.
     *
     * These properties include: the address, description, and label of the storage
     * account; and the name of the affinity group to which the service belongs,
     * or its geo-location if it is not part of an affinity group.
     *
     * @param string $name The storage account name.
     *
     * @return GetStorageServicePropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460802.aspx
     */
    public function getStorageServiceProperties($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getStorageServicePath($name));
        $context->addStatusCode(Resources::STATUS_OK);

        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());

        return GetStorageServicePropertiesResult::create($parsed);
    }

    /**
     * Returns the primary and secondary access keys for the specified storage
     * account.
     *
     * @param string $name The storage account name.
     *
     * @return GetStorageServiceKeysResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460785.aspx
     */
    public function getStorageServiceKeys($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getStorageServiceKeysPath($name));
        $context->addStatusCode(Resources::STATUS_OK);

        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());

        return GetStorageServiceKeysResult::create($parsed);
    }

    /**
     * Regenerates the primary or secondary access key for the specified storage
     * account.
     *
     * @param string $name    The storage account name.
     * @param string $keyType Specifies which key to regenerate.
     *
     * @return GetStorageServiceKeysResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460795.aspx
     */
    public function regenerateStorageServiceKeys($name, $keyType)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($keyType, 'keyType');
        Validate::notNullOrEmpty($keyType, 'keyType');

        $body = $this->_createRequestXml(
            array(Resources::XTAG_KEY_TYPE => $keyType),
            Resources::XTAG_REGENERATE_KEYS
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getStorageServiceKeysPath($name));
        $context->addStatusCode(Resources::STATUS_OK);
        $context->addQueryParameter(Resources::QP_ACTION, Resources::QPV_REGENERATE);
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());

        return GetStorageServiceKeysResult::create($parsed);
    }

    /**
     * Creates a new storage account in Windows Azure.
     *
     * In the optional parameters either location or affinity group must be provided.
     * Because Create Storage Account is an asynchronous operation, it always returns
     * status code 202 (Accepted). To determine the status code for the operation
     * once it is complete, call getOperationStatus API. The status code is embedded
     * in the response for this operation; if successful, it will be
     * status code 200 (OK).
     *
     * @param string               $name    The storage account name.
     * @param string               $label   The name for the storage account
     * specified as a base64-encoded string. The name may be up to 100 characters
     * in length. The name can be used identify the storage account for your tracking
     * purposes.
     * @param CreateServiceOptions $options The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh264518.aspx
     */
    public function createStorageService($name, $label, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($label, 'label');
        Validate::notNullOrEmpty($label, 'label');
        Validate::notNullOrEmpty($options, 'options');
        $affinityGroup = $options->getAffinityGroup();
        $location      = $options->getLocation();
        Validate::isTrue(
            !empty($location) || !empty($affinityGroup),
            Resources::INVALID_CREATE_SERVICE_OPTIONS_MSG
        );

        $storageService = new StorageService();
        $storageService->setName($name);
        $storageService->setLabel($label);
        $storageService->setLocation($options->getLocation());
        $storageService->setAffinityGroup($options->getAffinityGroup());
        $storageService->setDescription($options->getDescription());
        $storageService->addSerializationProperty(
            XmlSerializer::ROOT_NAME,
            Resources::XTAG_CREATE_STORAGE_SERVICE_INPUT
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getStorageServicePath());
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->setBody($storageService->serialize($this->dataSerializer));
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Deletes the specified storage account from Windows Azure.
     *
     * @param string $name The storage account name.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh264517.aspx
     */
    public function deleteStorageService($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_DELETE);
        $context->setPath($this->_getStorageServicePath($name));
        $context->addStatusCode(Resources::STATUS_OK);

        $this->sendContext($context);
    }

    /**
     * Updates the label and/or the description for a storage account in Windows
     * Azure.
     *
     * @param string               $name    The storage account name.
     * @param UpdateServiceOptions $options The optional parameters.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh264516.aspx
     */
    public function updateStorageService($name, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        $label       = $options->getLabel();
        $description = $options->getDescription();
        Validate::isTrue(
            !empty($label) || !empty($description),
            Resources::INVALID_UPDATE_SERVICE_OPTIONS_MSG
        );

        $storageService = new StorageService();
        $storageService->setLabel($options->getLabel());
        $storageService->setDescription($options->getDescription());
        $storageService->addSerializationProperty(
            XmlSerializer::ROOT_NAME,
            Resources::XTAG_UPDATE_STORAGE_SERVICE_INPUT
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_PUT);
        $context->setPath($this->_getStorageServicePath($name));
        $context->addStatusCode(Resources::STATUS_OK);
        $context->setBody($storageService->serialize($this->dataSerializer));
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );
        $this->sendContext($context);
    }

    /**
     * Lists the affinity groups associated with the specified subscription.
     *
     * @return ListAffinityGroupsResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460797.aspx
     */
    public function listAffinityGroups()
    {
        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getAffinityGroupPath());
        $context->addStatusCode(Resources::STATUS_OK);

        $response   = $this->sendContext($context);
        $serialized = $this->dataSerializer->unserialize($response->getBody());

        return ListAffinityGroupsResult::create($serialized);
    }

    /**
     * Creates a new affinity group for the specified subscription.
     *
     * @param string                     $name     The affinity group name.
     * @param string                     $label    The base-64 encoded name for the
     * affinity group. The name can be up to 100 characters in length.
     * @param string                     $location The data center location where the
     * affinity group will be created. To list available locations, use the
     * listLocations API.
     * @param CreateAffinityGroupOptions $options  The optional parameters.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg715317.aspx
     */
    public function createAffinityGroup($name, $label, $location, $options = null)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($label, 'label');
        Validate::notNullOrEmpty($label, 'label');
        Validate::isString($location, 'location');
        Validate::notNullOrEmpty($location, 'location');

        if (is_null($options)) {
            $options = new CreateAffinityGroupOptions();
        }

        $affinityGroup = new AffinityGroup();
        $affinityGroup->setName($name);
        $affinityGroup->setLabel($label);
        $affinityGroup->setLocation($location);
        $affinityGroup->setDescription($options->getDescription());
        $affinityGroup->addSerializationProperty(
            XmlSerializer::ROOT_NAME,
            Resources::XTAG_CREATE_AFFINITY_GROUP
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getAffinityGroupPath());
        $context->addStatusCode(Resources::STATUS_CREATED);
        $context->setBody($affinityGroup->serialize($this->dataSerializer));
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $this->sendContext($context);
    }

    /**
     * Deletes an affinity group in the specified subscription.
     *
     * @param string $name The affinity group name.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg715314.aspx
     */
    public function deleteAffinityGroup($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_DELETE);
        $context->setPath($this->_getAffinityGroupPath($name));
        $context->addStatusCode(Resources::STATUS_OK);

        $this->sendContext($context);
    }

    /**
     * Updates the label and/or the description for an affinity group for the
     * specified subscription.
     *
     * @param string                     $name    The affinity group name.
     * @param string                     $label   The affinity group label.
     * @param CreateAffinityGroupOptions $options The optional parameters.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg715316.aspx
     */
    public function updateAffinityGroup($name, $label, $options = null)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($label, 'label');
        Validate::notNullOrEmpty($label, 'label');

        if (is_null($options)) {
            $options = new CreateAffinityGroupOptions();
        }

        $affinityGroup = new AffinityGroup();
        $affinityGroup->setLabel($label);
        $affinityGroup->setDescription($options->getDescription());
        $affinityGroup->addSerializationProperty(
            XmlSerializer::ROOT_NAME,
            Resources::XTAG_UPDATE_AFFINITY_GROUP
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_PUT);
        $context->setPath($this->_getAffinityGroupPath($name));
        $context->addStatusCode(Resources::STATUS_OK);
        $context->setBody($affinityGroup->serialize($this->dataSerializer));
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $this->sendContext($context);
    }

    /**
     * Returns the system properties associated with the specified affinity group.
     *
     * @param string $name The affinity group name.
     *
     * @return GetAffinityGroupPropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460789.aspx
     */
    public function getAffinityGroupProperties($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getAffinityGroupPath($name));
        $context->addStatusCode(Resources::STATUS_OK);

        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());

        return GetAffinityGroupPropertiesResult::create($parsed);
    }

    /**
     * Lists all of the data center locations that are valid for your subscription.
     *
     * @return ListLocationsResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441293.aspx
     */
    public function listLocations()
    {
        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getLocationPath());
        $context->addStatusCode(Resources::STATUS_OK);

        $response   = $this->sendContext($context);
        $serialized = $this->dataSerializer->unserialize($response->getBody());

        return ListLocationsResult::create($serialized);
    }

    /**
     * Returns the status of the specified operation. After calling an asynchronous
     * operation, you can call Get Operation Status to determine whether the
     * operation has succeeded, failed, or is still in progress.
     *
     * @param AsynchronousOperationResult $requestInfo The request information for
     * the REST call you want to track.
     *
     * @return GetOperationStatusResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460783.aspx
     */
    public function getOperationStatus($requestInfo)
    {
        Validate::notNullOrEmpty($requestInfo, 'requestInfo');
        Validate::notNullOrEmpty($requestInfo->getrequestId(), 'requestId');


        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getOperationPath($requestInfo->getrequestId()));
        $context->addStatusCode(Resources::STATUS_OK);

        $response   = $this->sendContext($context);
        $serialized = $this->dataSerializer->unserialize($response->getBody());

        return GetOperationStatusResult::create($serialized);
    }

    /**
     * Lists the hosted services available under the current subscription.
     *
     * @return ListHostedServicesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460781.aspx
     */
    public function listHostedServices()
    {
        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getHostedServicePath());
        $context->addStatusCode(Resources::STATUS_OK);

        $response   = $this->sendContext($context);
        $serialized = $this->dataSerializer->unserialize($response->getBody());

        return ListHostedServicesResult::create($serialized);
    }

    /**
     * Creates a new hosted service in Windows Azure.
     *
     * @param string               $name    The name for the hosted service
     * that is unique within Windows Azure. This name is the DNS prefix name and can
     * be used to access the hosted service.
     * @param string               $label   The name for the hosted service
     * that is base-64 encoded. The name can be used identify the storage account for
     * your tracking purposes.
     * @param CreateServiceOptions $options The optional parameters.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441304.aspx
     */
    public function createHostedService($name, $label, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($label, 'label');
        Validate::notNullOrEmpty($label, 'label');
        Validate::notNullOrEmpty($options, 'options');

        // User have to set affinity group or location.
        $affinityGroup = $options->getAffinityGroup();
        $location      = $options->getLocation();
        Validate::isTrue(
            !empty($location) || !empty($affinityGroup),
            Resources::INVALID_CREATE_SERVICE_OPTIONS_MSG
        );

        $hostedService = new HostedService();
        $hostedService->setName($name);
        $hostedService->setLabel($label);
        $hostedService->setLocation($options->getLocation());
        $hostedService->setAffinityGroup($options->getAffinityGroup());
        $hostedService->setDescription($options->getDescription());
        $hostedService->addSerializationProperty(
            XmlSerializer::ROOT_NAME,
            Resources::XTAG_CREATE_HOSTED_SERVICE
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getHostedServicePath());
        $context->addStatusCode(Resources::STATUS_CREATED);
        $context->setBody($hostedService->serialize($this->dataSerializer));
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $this->sendContext($context);
    }

    /**
     * updates the label and/or the description for a hosted service in Windows
     * Azure.
     *
     * @param string               $name    The name for the hosted service that is
     * unique within Windows Azure.
     * @param UpdateServiceOptions $options The optional parameters.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441303.aspx
     */
    public function updateHostedService($name, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::notNullOrEmpty($options, 'options');
        $label       = $options->getLabel();
        $description = $options->getDescription();
        Validate::isTrue(
            !empty($label) || !empty($description),
            Resources::INVALID_UPDATE_SERVICE_OPTIONS_MSG
        );

        $hostedService = new HostedService();
        $hostedService->setLabel($options->getLabel());
        $hostedService->setDescription($options->getDescription());
        $hostedService->addSerializationProperty(
            XmlSerializer::ROOT_NAME,
            Resources::XTAG_UPDATE_HOSTED_SERVICE
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_PUT);
        $context->setPath($this->_getHostedServicePath($name));
        $context->addStatusCode(Resources::STATUS_OK);
        $context->setBody($hostedService->serialize($this->dataSerializer));
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );
        $this->sendContext($context);
    }

    /**
     * Deletes the specified hosted service from Windows Azure.
     *
     * Before you can delete a hosted service, you must delete any deployments it
     * has. Attempting to delete a hosted service that has deployments results in
     * an error. You can call the deleteDeployment API to delete a hosted service's
     * deployments.
     *
     * @param string $name The name for the hosted service.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441305.aspx
     */
    public function deleteHostedService($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_DELETE);
        $context->setPath($this->_getHostedServicePath($name));
        $context->addStatusCode(Resources::STATUS_OK);

        $this->sendContext($context);
    }

    /**
     * Retrieves system properties for the specified hosted service. These properties
     * include the service name and service type; the name of the affinity group to
     * which the service belongs, or its location if it is not part of an affinity
     * group; and optionally, information on the service's deployments.
     *
     * @param string                            $name    The name for the hosted
     * service.
     * @param GetHostedServicePropertiesOptions $options The optional parameters.
     *
     * @return GetHostedServicePropertiesResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460806.aspx
     */
    public function getHostedServiceProperties($name, $options = null)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');

        if (is_null($options)) {
            $options = new GetHostedServicePropertiesOptions();
        }

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($this->_getHostedServicePath($name));
        $context->addStatusCode(Resources::STATUS_OK);
        $context->addQueryParameter(
            Resources::QP_EMBED_DETAIL,
            Utilities::booleanToString($options->getEmbedDetail())
        );

        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());

        return GetHostedServicePropertiesResult::create($parsed);
    }

    /**
     * Uploads a new service package and creates a new deployment on staging or
     * production.
     *
     * The createDeployment API is an asynchronous operation. To determine whether
     * the management service has finished processing the request, call
     * getOperationStatus API.
     *
     * @param string                  $name           The name for the hosted service
     * that is unique within Windows Azure.
     * @param string                  $deploymentName The name for the deployment.
     * The deployment name must be unique among other deployments for the hosted
     * service.
     * @param string                  $slot           The name of the deployment slot
     * This can be "production" or "staging".
     * @param string                  $packageUrl     The URL that refers to the
     * location of the service package in the Blob service. The service package can
     * be located in a storage account beneath the same subscription.
     * @param string|resource         $configuration  The configuration file contents
     * or file stream.
     * @param string                  $label          The name for the hosted service
     * that is base-64 encoded. The name can be up to 100 characters in length. It is
     * recommended that the label be unique within the subscription. The name can be
     * used identify the hosted service for your tracking purposes.
     * @param CreateDeploymentOptions $options        The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460813.aspx
     */
    public function createDeployment(
        $name,
        $deploymentName,
        $slot,
        $packageUrl,
        $configuration,
        $label,
        $options = null
    ) {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($deploymentName, 'deploymentName');
        Validate::notNullOrEmpty($deploymentName, 'deploymentName');
        Validate::isString($slot, 'slot');
        Validate::notNullOrEmpty($slot, 'slot');
        Validate::isTrue(
            DeploymentSlot::isValid($slot),
            sprintf(Resources::INVALID_SLOT, $slot)
        );
        Validate::isString($packageUrl, 'packageUrl');
        Validate::notNullOrEmpty($packageUrl, 'packageUrl');
        Validate::isString($configuration, 'configuration');
        Validate::notNullOrEmpty($configuration, 'configuration');
        Validate::isString($label, 'label');
        Validate::notNullOrEmpty($label, 'label');

        if (is_null($options)) {
            $options = new CreateDeploymentOptions();
        }

        $configuration = $this->_encodeConfiguration($configuration);

        $startDeployment       = Utilities::booleanToString(
            $options->getStartDeployment()
        );
        $treatWarningsAsErrors = Utilities::booleanToString(
            $options->getTreatWarningsAsErrors()
        );
        $xmlElements           = array(
            Resources::XTAG_NAME                    => $deploymentName,
            Resources::XTAG_PACKAGE_URL             => $packageUrl,
            Resources::XTAG_LABEL                   => $label,
            Resources::XTAG_CONFIGURATION           => $configuration,
            Resources::XTAG_START_DEPLOYMENT        => $startDeployment,
            Resources::XTAG_TREAT_WARNINGS_AS_ERROR => $treatWarningsAsErrors
        );
        $requestXml            = $this->_createRequestXml(
            $xmlElements,
            Resources::XTAG_CREATE_DEPLOYMENT
        );

        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getDeploymentPathUsingSlot($name, $slot));
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->setBody($requestXml);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Returns configuration information, status, and system properties for a
     * deployment.
     *
     * The getDeployment API can be used to retrieve information for a specific
     * deployment or for all deployments in the staging or production environment.
     * If you want to retrieve information about a specific deployment, you must
     * first get the unique name for the deployment. This unique name is part of the
     * response when you make a request to get all deployments in an environment.
     *
     * @param string               $name    The hosted service name.
     * @param GetDeploymentOptions $options The optional parameters.
     *
     * @return GetDeploymentResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460804.aspx
     */
    public function getDeployment($name, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::notNullOrEmpty($options, 'options');

        $context = new HttpCallContext();
        $path    = $this->_getDeploymentPath($name, $options);
        $context->setMethod(Resources::HTTP_GET);
        $context->setPath($path);
        $context->addStatusCode(Resources::STATUS_OK);

        $response = $this->sendContext($context);
        $parsed   = $this->dataSerializer->unserialize($response->getBody());

        return GetDeploymentResult::create($parsed);
    }

    /**
     * Initiates a virtual IP swap between the staging and production deployment
     * environments for a service. If the service is currently running in the staging
     * environment, it will be swapped to the production environment. If it is
     * running in the production environment, it will be swapped to staging.
     *
     * You can swap VIPs only if the number of endpoints specified by the service
     * definition is identical for both deployments. For example, if you add an HTTPS
     * endpoint to a web role that previously exposed only an HTTP endpoint, you
     * cannot upgrade your service using a VIP swap; you must delete your production
     * deployment and redeploy instead. You can obtain information about endpoints
     * that are used by using the Get Deployment operation.
     *
     * @param string $name        The hosted service name.
     * @param string $source      The name of the source deployment.
     * @param string $destination The name of the destination deployment.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460814.aspx
     */
    public function swapDeployment($name, $source, $destination)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($destination, 'destination');
        Validate::notNullOrEmpty($destination, 'destination');
        Validate::isString($source, 'source');
        Validate::notNullOrEmpty($source, 'source');

        $xmlElements = array(
            Resources::XTAG_PRODUCTION        => $destination,
            Resources::XTAG_SOURCE_DEPLOYMENT => $source
        );
        $body        = $this->_createRequestXml($xmlElements, Resources::XTAG_SWAP);
        $context     = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getHostedServicePath($name));
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Deletes the specified deployment.
     *
     * Note that you can delete a deployment either by specifying the deployment
     * environment (staging or production), or by specifying the deployment's unique
     * name.
     *
     * @param string               $name    The hosted service name.
     * @param GetDeploymentOptions $options The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460815.aspx
     */
    public function deleteDeployment($name, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::notNullOrEmpty($options, 'options');

        $context = new HttpCallContext();
        $path    = $this->_getDeploymentPath($name, $options);
        $context->setMethod(Resources::HTTP_DELETE);
        $context->setPath($path);
        $context->addStatusCode(Resources::STATUS_ACCEPTED);

        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Initiates a change to the deployment configuration.
     *
     * Note that you can change a deployment's configuration either by specifying the
     * deployment environment (staging or production), or by specifying the
     * deployment's unique name.
     *
     * @param string                               $name          The hosted service
     * name.
     * @param string|resource                      $configuration The configuration
     * file contents or file stream.
     * @param ChangeDeploymentConfigurationOptions $options       The optional
     * parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460809.aspx
     */
    public function changeDeploymentConfiguration($name, $configuration, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($configuration, 'configuration');
        Validate::notNullOrEmpty($configuration, 'configuration');
        Validate::notNullOrEmpty($options, 'options');

        $configuration = $this->_encodeConfiguration($configuration);
        $warningsTreatment = Utilities::booleanToString(
            $options->getTreatWarningsAsErrors()
        );
        $xmlElements       = array(
            Resources::XTAG_CONFIGURATION           => $configuration,
            Resources::XTAG_TREAT_WARNINGS_AS_ERROR => $warningsTreatment,
            Resources::XTAG_MODE                    => $options->getMode()
        );
        $body              = $this->_createRequestXml(
            $xmlElements,
            Resources::XTAG_CHANGE_CONFIGURATION
        );
        $context           = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getDeploymentPath($name, $options) . '/');
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->addQueryParameter(
            Resources::QP_COMP,
            Resources::QPV_CONFIG
        );
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        assert(Utilities::endsWith($context->getPath(), '/'));
        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Initiates a change in deployment status.
     *
     * Note that you can change deployment status either by specifying the deployment
     * environment (staging or production), or by specifying the deployment's unique
     * name.
     *
     * @param string               $name    The hosted service name.
     * @param string               $status  The change to initiate to the
     * deployment status.
     * Possible values include Running or Suspended.
     * @param GetDeploymentOptions $options The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460808.aspx
     */
    public function updateDeploymentStatus($name, $status, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isTrue(
            DeploymentStatus::isValid($status),
            Resources::INVALID_DEPLOYMENT_STATUS_MSG
        );
        Validate::notNullOrEmpty($options, 'options');

        $body    = $this->_createRequestXml(
            array(Resources::XTAG_STATUS => $status),
            Resources::XTAG_UPDATE_DEPLOYMENT_STATUS
        );
        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getDeploymentPath($name, $options) . '/');
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->addQueryParameter(
            Resources::QP_COMP,
            Resources::QPV_STATUS
        );
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        assert(Utilities::endsWith($context->getPath(), '/'));
        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Initiates an upgrade to a deployment.
     *
     * Note that you can upgrade a deployment either by specifying the deployment
     * environment (staging or production), or by specifying the deployment's unique
     * name.
     *
     * @param string                   $name          The hosted service name.
     * @param string                   $mode          The type of upgrade to initiate
     * If not specified the default value is Auto. If set to Manual,
     * walkUpgradeDomain API must be called to apply the update. If set to Auto, the
     * Windows Azure platform will automatically apply the update to each Upgrade
     * Domain in sequence.
     * @param string                   $packageUrl    The URL that refers to the
     * location of the service package in the Blob service. The service package can
     * be located in a storage account beneath the same subscription.
     * @param string|resource          $configuration The configuration file contents
     * or file stream.
     * @param string                   $label         The name for the hosted service
     * that is base-64 encoded. The name may be up to 100 characters in length.
     * @param boolean                  $force         Specifies whether the rollback
     * should proceed even when it will cause local data to be lost from some role
     * instances. True if the rollback should proceed; otherwise false if the
     * rollback should fail.
     * @param UpgradeDeploymentOptions $options       The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460793.aspx
     */
    public function upgradeDeployment(
        $name,
        $mode,
        $packageUrl,
        $configuration,
        $label,
        $force,
        $options
    ) {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($mode, 'mode');
        Validate::isTrue(Mode::isValid($mode), Resources::INVALID_CHANGE_MODE_MSG);
        Validate::isString($packageUrl, 'packageUrl');
        Validate::notNullOrEmpty($packageUrl, 'packageUrl');
        Validate::isString($configuration, 'configuration');
        Validate::notNullOrEmpty($configuration, 'configuration');
        Validate::isString($label, 'label');
        Validate::notNullOrEmpty($label, 'label');
        Validate::isBoolean($force, 'force');
        Validate::notNullOrEmpty($force, 'force');
        Validate::notNullOrEmpty($options, 'options');

        $configuration = $this->_encodeConfiguration($configuration);

        $xmlElements = array(
            Resources::XTAG_MODE            => $mode,
            Resources::XTAG_PACKAGE_URL     => $packageUrl,
            Resources::XTAG_CONFIGURATION   => $configuration,
            Resources::XTAG_LABEL           => $label,
            Resources::XTAG_ROLE_TO_UPGRADE => $options->getRoleToUpgrade(),
            Resources::XTAG_FORCE           => Utilities::booleanToString($force),
        );
        $body        = $this->_createRequestXml(
            $xmlElements,
            Resources::XTAG_UPGRADE_DEPLOYMENT
        );
        $context     = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getDeploymentPath($name, $options) . '/');
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->addQueryParameter(
            Resources::QP_COMP,
            Resources::QPV_UPGRADE
        );
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        assert(Utilities::endsWith($context->getPath(), '/'));
        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Specifies the next upgrade domain to be walked during manual in-place upgrade
     * or configuration change.
     *
     * Note that you can walk an upgrade domain either by specifying the deployment
     * environment (staging or production), or by specifying the deployment's unique
     * name.
     *
     * @param string               $name          The hosted service name.
     * @param integer              $upgradeDomain The integer value that
     * identifies the upgrade domain to walk. Upgrade domains are identified with a
     * zero-based index: the first upgrade domain has an ID of 0, the second has an
     * ID of 1, and so on.
     * @param GetDeploymentOptions $options       The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460800.aspx
     */
    public function walkUpgradeDomain($name, $upgradeDomain, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isInteger($upgradeDomain, 'upgradeDomain');
        Validate::notNullOrEmpty($options, 'options');

        $body    = $this->_createRequestXml(
            array(Resources::XTAG_UPGRADE_DOMAIN => $upgradeDomain),
            Resources::XTAG_WALK_UPGRADE_DOMAIN
        );
        $context = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getDeploymentPath($name, $options) . '/');
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->addQueryParameter(
            Resources::QP_COMP,
            Resources::QPV_WALK_UPGRADE_DOMAIN
        );
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        assert(Utilities::endsWith($context->getPath(), '/'));
        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }

    /**
     * Requests a reboot of a role instance that is running in a deployment.
     *
     * Note that you can reboot role instance either by specifying the deployment
     * environment (staging or production), or by specifying the deployment's unique
     * name.
     *
     * @param string               $name     The hosted service name.
     * @param string               $roleName The role instance name.
     * @param GetDeploymentOptions $options  The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441298.aspx
     */
    public function rebootRoleInstance($name, $roleName, $options)
    {
        return $this->_sendRoleInstanceOrder(
            $name,
            $roleName,
            $options,
            Resources::QPV_REBOOT
        );
    }

    /**
     * Requests a reimage of a role instance that is running in a deployment.
     *
     * Note that you can reimage role instance either by specifying the deployment
     * environment (staging or production), or by specifying the deployment's unique
     * name.
     *
     * @param string               $name     The hosted service name.
     * @param string               $roleName The role instance name.
     * @param GetDeploymentOptions $options  The optional parameters.
     *
     * @return AsynchronousOperationResult
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441292.aspx
     */
    public function reimageRoleInstance($name, $roleName, $options)
    {
        return $this->_sendRoleInstanceOrder(
            $name,
            $roleName,
            $options,
            Resources::QPV_REIMAGE
        );
    }

    /**
     * Cancels an in progress configuration change (update) or upgrade and returns
     * the deployment to its state before the upgrade or configuration change was
     * started.
     *
     * Note that you can rollback update or upgrade either by specifying the
     * deployment environment (staging or production), or by specifying the
     * deployment's unique name.
     *
     * @param string               $name    The hosted service name.
     * @param string               $mode    Specifies whether the rollback
     * should proceed automatically or not. Auto, The rollback proceeds without
     * further user input. Manual, You must call the walkUpgradeDomain API to apply
     * the rollback to each upgrade domain.
     * @param boolean              $force   Specifies whether the rollback
     * should proceed even when it will cause local data to be lost from some role
     * instances. True if the rollback should proceed; otherwise false if the
     * rollback should fail.
     * @param GetDeploymentOptions $options The optional parameters.
     *
     * @return none
     *
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh403977.aspx
     */
    public function rollbackUpdateOrUpgrade($name, $mode, $force, $options)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        Validate::isString($mode, 'mode');
        Validate::isTrue(Mode::isValid($mode), Resources::INVALID_CHANGE_MODE_MSG);
        Validate::isBoolean($force, 'force');
        Validate::notNullOrEmpty($force, 'force');
        Validate::notNullOrEmpty($options, 'options');

        $xmlElements = array(
            Resources::XTAG_MODE  => $mode,
            Resources::XTAG_FORCE => Utilities::booleanToString($force),
        );
        $body        = $this->_createRequestXml(
            $xmlElements,
            Resources::XTAG_ROLLBACK_UPDATE_OR_UPGRADE
        );
        $context     = new HttpCallContext();
        $context->setMethod(Resources::HTTP_POST);
        $context->setPath($this->_getDeploymentPath($name, $options) . '/');
        $context->addStatusCode(Resources::STATUS_ACCEPTED);
        $context->addQueryParameter(
            Resources::QP_COMP,
            Resources::QPV_ROLLBACK
        );
        $context->setBody($body);
        $context->addHeader(
            Resources::CONTENT_TYPE,
            Resources::XML_CONTENT_TYPE
        );

        assert(Utilities::endsWith($context->getPath(), '/'));
        $response = $this->sendContext($context);

        return AsynchronousOperationResult::create($response->getHeader());
    }
}