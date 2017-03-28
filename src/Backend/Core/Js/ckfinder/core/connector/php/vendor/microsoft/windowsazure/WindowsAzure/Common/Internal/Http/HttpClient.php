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
use WindowsAzure\Common\Internal\Http\IHttpClient;
use WindowsAzure\Common\Internal\IServiceFilter;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Http\IUrl;

require_once 'HTTP/Request2.php';

/**
 * HTTP client which sends and receives HTTP requests and responses.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Http
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class HttpClient implements IHttpClient
{
    /**
     * @var \HTTP_Request2
     */
    private $_request;
    
    /**
     * @var WindowsAzure\Common\Internal\Http\IUrl 
     */
    private $_requestUrl;
    
    /**
     * Holds the latest response object
     * 
     * @var \HTTP_Request2_Response
     */
    private $_response;
    
    /**
     * Holds expected status code after sending the request.
     * 
     * @var array
     */
    private $_expectedStatusCodes;
    
    /**
     * Initializes new HttpClient object.
     * 
     * @param string $certificatePath          The certificate path.
     * @param string $certificateAuthorityPath The path of the certificate authority.
     * 
     * @return WindowsAzure\Common\Internal\Http\HttpClient
     */
    function __construct(
        $certificatePath = Resources::EMPTY_STRING,
        $certificateAuthorityPath = Resources::EMPTY_STRING
    ) {
        $config = array(
            Resources::USE_BRACKETS    => true,
            Resources::SSL_VERIFY_PEER => false,
            Resources::SSL_VERIFY_HOST => false
        );

        if (!empty($certificatePath)) {
            $config[Resources::SSL_LOCAL_CERT]  = $certificatePath;
            $config[Resources::SSL_VERIFY_HOST] = true;
        }

        if (!empty($certificateAuthorityPath)) {
            $config[Resources::SSL_CAFILE]      = $certificateAuthorityPath;
            $config[Resources::SSL_VERIFY_PEER] = true;
        }

        $this->_request = new \HTTP_Request2(
            null, null, $config
        );

        $this->setHeader('user-agent', null);
        
        $this->_requestUrl          = null;
        $this->_response            = null;
        $this->_expectedStatusCodes = array();
    }
    
    /**
     * Makes deep copy from the current object.
     * 
     * @return WindowsAzure\Common\Internal\Http\HttpClient
     */
    public function __clone()
    {
        $this->_request = clone $this->_request;
        
        if (!is_null($this->_requestUrl)) {
            $this->_requestUrl = clone $this->_requestUrl;
        }
    }

    /**
     * Sets the request url.
     *
     * @param WindowsAzure\Common\Internal\Http\IUrl $url request url.
     * 
     * @return none.
     */
    public function setUrl($url)
    {
        $this->_requestUrl = $url;
    }

    /**
     * Gets request url. Note that you must check if the returned object is null or
     * not.
     *
     * @return WindowsAzure\Common\Internal\Http\IUrl
     */ 
    public function getUrl()
    {
        return $this->_requestUrl;
    }

    /**
     * Sets request's HTTP method. You can use \HTTP_Request2 constants like
     * Resources::HTTP_GET or strings like 'GET'.
     * 
     * @param string $method request's HTTP method.
     * 
     * @return none
     */
    public function setMethod($method)
    {
        $this->_request->setMethod($method);
    }

    /**
     * Gets request's HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_request->getMethod();
    }

    /**
     * Gets request's headers. The returned array key (header names) are all in
     * lower case even if they were set having some upper letters.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_request->getHeaders();
    }

    /**
     * Sets a an existing request header to value or creates a new one if the $header
     * doesn't exist.
     * 
     * @param string $header  header name.
     * @param string $value   header value.
     * @param bool   $replace whether to replace previous header with the same name
     * or append to its value (comma separated)
     * 
     * @return none
     */
    public function setHeader($header, $value, $replace = false)
    {
        Validate::isString($value, 'value');
        
        $this->_request->setHeader($header, $value, $replace);
    }
    
    /**
     * Sets request headers using array
     * 
     * @param array $headers headers key-value array
     * 
     * @return none
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * Sets HTTP POST parameters.
     * 
     * @param array $postParameters The HTTP POST parameters.
     * 
     * @return none
     */
    public function setPostParameters($postParameters)
    {
        $this->_request->addPostParameter($postParameters);
    }

    /**
     * Processes the reuqest through HTTP pipeline with passed $filters, 
     * sends HTTP request to the wire and process the response in the HTTP pipeline.
     * 
     * @param array $filters HTTP filters which will be applied to the request before
     * send and then applied to the response.
     * @param IUrl  $url     Request url.
     * 
     * @throws WindowsAzure\Common\ServiceException
     * 
     * @return string The response body
     */
    public function send($filters, $url = null)
    {
        if (isset($url)) {
            $this->setUrl($url);
            $this->_request->setUrl($this->_requestUrl->getUrl());
        }
        
        $contentLength = Resources::EMPTY_STRING;
        if (    strtoupper($this->getMethod()) != Resources::HTTP_GET
            && strtoupper($this->getMethod()) != Resources::HTTP_DELETE
            && strtoupper($this->getMethod()) != Resources::HTTP_HEAD
        ) {
            $contentLength = 0;
            
            if (!is_null($this->getBody())) {
                $contentLength = strlen($this->getBody());
            }
            $this->_request->setHeader(Resources::CONTENT_LENGTH, $contentLength);
        }

        foreach ($filters as $filter) {
            $this->_request = $filter->handleRequest($this)->_request;
        }

        $this->_response = $this->_request->send();

        $start = count($filters) - 1;
        for ($index = $start; $index >= 0; $index--) {
            $this->_response = $filters[$index]->handleResponse(
                $this, $this->_response
            );
        }
        
        self::throwIfError(
            $this->_response->getStatus(),
            $this->_response->getReasonPhrase(),
            $this->_response->getBody(),
            $this->_expectedStatusCodes
        );

        return $this->_response->getBody();
    }
    
    /**
     * Sets successful status code
     * 
     * @param array|string $statusCodes successful status code.
     * 
     * @return none
     */
    public function setExpectedStatusCode($statusCodes)
    {
        if (!is_array($statusCodes)) {
            $this->_expectedStatusCodes[] = $statusCodes;
        } else {
            $this->_expectedStatusCodes = $statusCodes;
        }
    }
    
    /**
     * Gets successful status code
     * 
     * @return array
     */
    public function getSuccessfulStatusCode()
    {
        return $this->_expectedStatusCodes;
    }
    
    /**
     * Sets configuration parameter.
     * 
     * @param string $name  The configuration parameter name.
     * @param mix    $value The configuration parameter value.
     * 
     * @return none
     */
    public function setConfig($name, $value = null)
    {
        $this->_request->setConfig($name, $value);
    }
    
    /**
     * Gets value for configuration parameter.
     * 
     * @param string $name configuration parameter name.
     * 
     * @return string
     */
    public function getConfig($name)
    {
        return $this->_request->getConfig($name);
    }
    
    /**
     * Sets the request body.
     * 
     * @param string $body body to use.
     * 
     * @return none
     */
    public function setBody($body)
    {
        Validate::isString($body, 'body');
        $this->_request->setBody($body);
    }
    
    /**
     * Gets the request body.
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->_request->getBody();
    }
    
    /**
     * Gets the response object.
     * 
     * @return \HTTP_Request2_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
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
    public static function throwIfError($actual, $reason, $message, $expected)
    {
        if (!in_array($actual, $expected)) {
            throw new ServiceException($actual, $reason, $message);
        }
    }
}


