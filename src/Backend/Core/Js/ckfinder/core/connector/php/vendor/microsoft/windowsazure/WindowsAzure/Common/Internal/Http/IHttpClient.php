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
 * @package   WindowsAzure\Common\Internal\Http
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal\Http;

/**
 * Defines required methods for a HTTP client proxy.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Http
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
interface IHttpClient
{
    /**
     * Sets the request url.
     *
     * @param WindowsAzure\Common\Internal\Http\IUrl $url request url.
     * 
     * @return none.
     */
    public function setUrl($url);

    /**
     * Gets request url.
     *
     * @return WindowsAzure\Common\Internal\Http\IUrl
     */
    public function getUrl();
    
    /**
     * Sets request's HTTP method.
     * 
     * @param string $method request's HTTP method.
     * 
     * @return none.
     */
    public function setMethod($method);

    /**
     * Gets request's HTTP method.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Gets request's headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Sets a an existing request header to value or creates a new one if the $header
     * doesn't exist.
     * 
     * @param string $header  header name.
     * @param string $value   header value.
     * @param bool   $replace whether to replace previous header with the same name
     * or append to its value (comma separated)
     * 
     * @return none.
     */
    public function setHeader($header, $value, $replace = false);
    
    /**
     * Sets request headers using array
     * 
     * @param array $headers headers key-value array
     * 
     * @return none.
     */
    public function setHeaders($headers);

    /**
     * Sets HTTP POST parameters.
     *
     * @param array $postParameters The HTTP POST parameters.
     *
     * @return none
     */
    public function setPostParameters($postParameters);

    /**
     * Processes the reuqest through HTTP pipeline with passed $filters, 
     * sends HTTP request to the wire and process the response in the HTTP pipeline.
     * 
     * @param array $filters HTTP filters which will be applied to the request before
     * send and then applied to the response.
     * @param IUrl  $url     Request url.
     * 
     * @return string The response body.
     */
    public function send($filters, $url = null);
    
    /**
     * Sets successful status code
     * 
     * @param array|string $statusCodes successful status code.
     * 
     * @return none.
     */
    public function setExpectedStatusCode($statusCodes);
    
    /**
     * Gets successful status code
     * 
     * @return array.
     */
    public function getSuccessfulStatusCode();
    
    /**
     * Sets a configuration element for the request.
     * 
     * @param string $name  configuration parameter name.
     * @param mix    $value configuration parameter value.
     * 
     * @return none.
     */
    public function setConfig($name, $value = null);
    
    /**
     * Gets value for configuration parameter.
     * 
     * @param string $name configuration parameter name.
     * 
     * @return string.
     */
    public function getConfig($name);
    
    /**
     * Sets the request body.
     * 
     * @param string $body body to use.
     * 
     * @return none.
     */
    public function setBody($body);
    
    /**
     * Gets the request body.
     * 
     * @return string.
     */
    public function getBody();
    
    /**
     * Makes deep copy from the current object.
     * 
     * @return WindowsAzure\Common\Internal\Http\HttpClient
     */
    public function __clone();
    
    /**
     * Gets the response object.
     * 
     * @return \HTTP_Request2_Response.
     */
    public function getResponse();
    
    /**
     * Throws ServiceException if the recieved status code is not expected.
     * 
     * @param string $actual   The received status code.
     * @param string $reason   The reason phrase.
     * @param string $message  The detailed message (if any).
     * @param array  $expected The expected status codes.
     * 
     * @return none
     * 
     * @static
     * 
     * @throws ServiceException
     */
    public static function throwIfError($actual, $reason, $message, $expected);
}


