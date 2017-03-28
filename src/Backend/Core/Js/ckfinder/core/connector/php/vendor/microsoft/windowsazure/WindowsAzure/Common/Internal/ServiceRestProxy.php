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
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\RestProxy;
use WindowsAzure\Common\Internal\Http\Url;
use WindowsAzure\Common\Internal\Http\HttpCallContext;

/**
 * Base class for all services rest proxies.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ServiceRestProxy extends RestProxy
{
    /**
     * @var string
     */
    private $_accountName;

    /**
     * Initializes new ServiceRestProxy object.
     *
     * @param IHttpClient $channel        The HTTP client used to send HTTP requests.
     * @param string      $uri            The storage account uri.
     * @param string      $accountName    The name of the account.
     * @param ISerializer $dataSerializer The data serializer.
     */
    public function __construct($channel, $uri, $accountName, $dataSerializer)
    {
        parent::__construct($channel, $dataSerializer, $uri);
        $this->_accountName = $accountName;
    }

    /**
     * Gets the account name. 
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }
    
    /**
     * Sends HTTP request with the specified HTTP call context.
     * 
     * @param WindowsAzure\Common\Internal\Http\HttpCallContext $context The HTTP 
     * call context.
     * 
     * @return \HTTP_Request2_Response
     */
    protected function sendContext($context)
    {
        $context->setUri($this->getUri());
        return parent::sendContext($context);
    }
    
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
        $context = new HttpCallContext();
        $context->setBody($body);
        $context->setHeaders($headers);
        $context->setMethod($method);
        $context->setPath($path);
        $context->setQueryParameters($queryParams);
        $context->setPostParameters($postParameters);
        
        if (is_array($statusCode)) {
            $context->setStatusCodes($statusCode);
        } else {
            $context->addStatusCode($statusCode);
        }
        
        return $this->sendContext($context);
    }

    
    /**
     * Adds optional header to headers if set
     * 
     * @param array           $headers         The array of request headers.
     * @param AccessCondition $accessCondition The access condition object.
     * 
     * @return array
     */
    public function addOptionalAccessConditionHeader($headers, $accessCondition)
    {
        if (!is_null($accessCondition)) {
            $header = $accessCondition->getHeader();
            
            if ($header != Resources::EMPTY_STRING) {
                $value = $accessCondition->getValue();
                if ($value instanceof \DateTime) {
                    $value = gmdate(
                        Resources::AZURE_DATE_FORMAT,
                        $value->getTimestamp()
                    );
                }
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }
    
    /**
     * Adds optional header to headers if set
     * 
     * @param array           $headers         The array of request headers.
     * @param AccessCondition $accessCondition The access condition object.
     * 
     * @return array
     */
    public function addOptionalSourceAccessConditionHeader(
        $headers, 
        $accessCondition
    ) {
        if (!is_null($accessCondition)) {
            $header     = $accessCondition->getHeader();
            $headerName = null;
            if (!empty($header)) {
                switch($header) {
                case Resources::IF_MATCH:
                    $headerName = Resources::X_MS_SOURCE_IF_MATCH;
                    break;
                
                case Resources::IF_UNMODIFIED_SINCE:
                    $headerName = Resources::X_MS_SOURCE_IF_UNMODIFIED_SINCE;
                    break;
                
                case Resources::IF_MODIFIED_SINCE:
                    $headerName = Resources::X_MS_SOURCE_IF_MODIFIED_SINCE;
                    break;
                
                case Resources::IF_NONE_MATCH:
                    $headerName = Resources::X_MS_SOURCE_IF_NONE_MATCH;
                    break;
                
                default:
                    throw new \Exception(Resources::INVALID_ACH_MSG);
                    break;
                }
            }
            $value = $accessCondition->getValue();
            if ($value instanceof \DateTime) {
                $value = gmdate(
                    Resources::AZURE_DATE_FORMAT,
                    $value->getTimestamp()
                );
            }
            
            $this->addOptionalHeader($headers, $headerName, $value);
        }
        
        return $headers;
    }
    
    /**
     * Adds HTTP POST parameter to the specified 
     * 
     * @param array  $postParameters An array of HTTP POST parameters.
     * @param string $key            The key of a HTTP POST parameter. 
     * @param string $value          the value of a HTTP POST parameter. 
     * 
     * @return array
     */
    public function addPostParameter(
        $postParameters,
        $key,
        $value
    ) {
        Validate::isArray($postParameters, 'postParameters');
        $postParameters[$key] = $value;
        return $postParameters; 
    }
    
    /**
     * Groups set of values into one value separated with Resources::SEPARATOR
     * 
     * @param array $values array of values to be grouped.
     * 
     * @return string
     */
    public function groupQueryValues($values)
    {
        Validate::isArray($values, 'values');
        $joined = Resources::EMPTY_STRING;
        
        foreach ($values as $value) {
            if (!is_null($value) && !empty($value)) {
                $joined .= $value . Resources::SEPARATOR;
            }
        }
        
        return trim($joined, Resources::SEPARATOR);
    }
    
    /**
     * Adds metadata elements to headers array
     * 
     * @param array $headers  HTTP request headers
     * @param array $metadata user specified metadata
     * 
     * @return array
     */
    protected function addMetadataHeaders($headers, $metadata)
    {
        $this->validateMetadata($metadata);
        
        $metadata = $this->generateMetadataHeaders($metadata);
        $headers  = array_merge($headers, $metadata);
        
        return $headers;
    }
    
    /**
     * Generates metadata headers by prefixing each element with 'x-ms-meta'.
     *
     * @param array $metadata user defined metadata.
     * 
     * @return array.
     */
    public function generateMetadataHeaders($metadata)
    {
        $metadataHeaders = array();
        
        if (is_array($metadata) && !is_null($metadata)) {
            foreach ($metadata as $key => $value) {
                $headerName = Resources::X_MS_META_HEADER_PREFIX;
                if (   strpos($value, "\r") !== false
                    || strpos($value, "\n") !== false
                ) {
                    throw new \InvalidArgumentException(Resources::INVALID_META_MSG);
                }
                
                $headerName                  .= strtolower($key);
                $metadataHeaders[$headerName] = $value;
            }
        }
        
        return $metadataHeaders;
    }
    
    /**
     * Gets metadata array by parsing them from given headers.
     *
     * @param array $headers HTTP headers containing metadata elements.
     * 
     * @return array.
     */
    public function getMetadataArray($headers)
    {
        $metadata = array();
        foreach ($headers as $key => $value) {
            $isMetadataHeader = Utilities::startsWith(
                strtolower($key),
                Resources::X_MS_META_HEADER_PREFIX
            );
            
            if ($isMetadataHeader) {
                $MetadataName = str_replace(
                    Resources::X_MS_META_HEADER_PREFIX,
                    Resources::EMPTY_STRING,
                    strtolower($key)
                );
                
                $metadata[$MetadataName] = $value;
            }
        }
        
        return $metadata;
    }
    
    /**
     * Validates the provided metadata array.
     * 
     * @param mix $metadata The metadata array.
     * 
     * @return none
     */
    public function validateMetadata($metadata)
    {
        if (!is_null($metadata)) {
            Validate::isArray($metadata, 'metadata');
        } else {
            $metadata = array();
        }
        
        foreach ($metadata as $key => $value) {
            Validate::isString($key, 'metadata key');
            Validate::isString($value, 'metadata value');
        }
    }
}


