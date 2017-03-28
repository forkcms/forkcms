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
 * @package   WindowsAzure\ServiceManagement\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceManagement\Internal;
use WindowsAzure\Common\Internal\FilterableService;

/**
 * The Windows Azure service management REST API wrappers.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceManagement\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
interface IServiceManagement extends FilterableService
{
    /**
     * Lists the storage accounts available under the current subscription.
     * 
     * @return ListStorageServicesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460787.aspx
     */
    public function listStorageServices();
    
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
    public function getStorageServiceProperties($name);
    
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
    public function getStorageServiceKeys($name);
    
    
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
    public function regenerateStorageServiceKeys($name, $keyType);
    
    /**
     * Creates a new storage account in Windows Azure.
     * 
     * In the optional parameters either location or affinity group must be provided.
     * Because Create Storage Account is an asynchronous operation, it always returns
     * status code 202 (Accepted). To determine the status code for the operation 
     * once it is complete, call Get Operation Status. The status code is embedded 
     * in the response for this operation; if successful, it will be 
     * status code 200 (OK).
     * 
     * @param string               $name    The storage account name.
     * @param string               $label   Name for the storage
     * account specified as a base64-encoded string. The name may be up to 100
     * characters in length. The name can be used identify the storage account for
     * your tracking purposes.
     * @param CreateServiceOptions $options The optional parameters.
     * 
     * @return AsynchronousOperationResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh264518.aspx 
     */
    public function createStorageService($name, $label, $options);
    
    /**
     * Deletes the specified storage account from Windows Azure.
     * 
     * @param string $name The storage account name.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh264517.aspx 
     */
    public function deleteStorageService($name);
    
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
    public function updateStorageService($name, $options);
    
    /**
     * Lists the affinity groups associated with the specified subscription.
     * 
     * @return ListAffinityGroupsResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460797.aspx
     */
    public function listAffinityGroups();
    
    /**
     * Creates a new affinity group for the specified subscription.
     * 
     * @param string                     $name     The affinity group name.
     * @param string                     $label    A base-64 encoded name for
     * the affinity group. The name can be up to 100 characters in length.
     * @param string                     $location The data center location
     * where the affinity group will be created. To list available locations, use 
     * the listLocations API.
     * @param CreateAffinityGroupOptions $options  The optional parameters.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg715317.aspx
     */
    public function createAffinityGroup($name, $label, $location, $options = null);
    
    /**
     * Deletes an affinity group in the specified subscription.
     * 
     * @param string $name The affinity group name.
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg715314.aspx
     */
    public function deleteAffinityGroup($name);
    
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
    public function updateAffinityGroup($name, $label, $options = null);
    
    /**
     * Returns the system properties associated with the specified affinity group.
     * 
     * @param string $name The affinity group name.
     * 
     * @return GetAffinityGroupPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460789.aspx
     */
    public function getAffinityGroupProperties($name);
    
    /**
     * Lists all of the data center locations that are valid for your subscription.
     * 
     * @return ListLocationsResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/gg441293.aspx
     */
    public function listLocations();
    
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
    public function getOperationStatus($requestInfo);
    
    /**
     * Lists the hosted services available under the current subscription.
     * 
     * @return ListHostedServicesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460781.aspx
     */
    public function listHostedServices();
    
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
    public function createHostedService($name, $label, $options);
    
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
    public function updateHostedService($name, $options);
    
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
    public function deleteHostedService($name);
    
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
    public function getHostedServiceProperties($name, $options = null);
    
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
     * @param string                  $configuration  The base-64 encoded service 
     * configuration file for the deployment.
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
    );
    
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
    public function getDeployment($name, $options);
    
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
    public function swapDeployment($name, $source, $destination);
    
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
    public function deleteDeployment($name, $options);
    
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
     * file contents or file stream,
     * @param ChangeDeploymentConfigurationOptions $options       The optional 
     * parameters.
     * 
     * @return AsynchronousOperationResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee460809.aspx
     */
    public function changeDeploymentConfiguration($name, $configuration, $options);
    
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
    public function updateDeploymentStatus($name, $status, $options);
    
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
     * @param string                   $configuration The base-64 encoded service
     * configuration file for the deployment.
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
    );
    
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
    public function walkUpgradeDomain($name, $upgradeDomain, $options);
    
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
    public function rebootRoleInstance($name, $roleName, $options);
    
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
    public function reimageRoleInstance($name, $roleName, $options);
    
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
    public function rollbackUpdateOrUpgrade($name, $mode, $force, $options);
}