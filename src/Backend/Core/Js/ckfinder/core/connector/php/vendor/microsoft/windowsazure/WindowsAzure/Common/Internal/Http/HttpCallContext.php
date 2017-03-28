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
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Http\Url;

/**
 * Holds basic elements for making HTTP call.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Http
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class HttpCallContext
{
    /**
     * The HTTP method used to make this call.
     * 
     * @var string
     */
    private $_method;    
    
    /**
     * HTTP request headers.
     * 
     * @var array
     */
    private $_headers;
    
    /**
     * The URI query parameters.
     * 
     * @var array
     */
    private $_queryParams;

    /** 
     * The HTTP POST parameters.
     * 
     * @var array.
     */
    private $_postParameters;
    
    /**
     * @var string
     */
    private $_uri;
    
    /**
     * The URI path.
     * 
     * @var string
     */
    private $_path;
    
    /**
     * The expected status codes.
     * 
     * @var array
     */
    private $_statusCodes;
    
    /**
     * The HTTP request body.
     * 
     * @var string
     */
    private $_body;
    
    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->_method         = null;
        $this->_body           = null;
        $this->_path           = null;
        $this->_uri            = null;
        $this->_queryParams    = array();
        $this->_postParameters = array();
        $this->_statusCodes    = array();
        $this->_headers        = array();
    }
    
    /**
     * Gets method.
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }
    
    /**
     * Sets method.
     * 
     * @param string $method The method value.
     * 
     * @return none
     */
    public function setMethod($method)
    {
        Validate::isString($method, 'method');
        
        $this->_method = $method;
    }
    
    /**
     * Gets headers.
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    /**
     * Sets headers.
     * 
     * Ignores the header if its value is empty.
     * 
     * @param array $headers The headers value.
     * 
     * @return none
     */
    public function setHeaders($headers)
    {
        $this->_headers = array();
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }
    }
    
    /**
     * Gets queryParams.
     * 
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->_queryParams;
    }
    
    /**
     * Sets queryParams.
     * 
     * Ignores the query variable if its value is empty.
     * 
     * @param array $queryParams The queryParams value.
     * 
     * @return none
     */
    public function setQueryParameters($queryParams)
    {
        $this->_queryParams = array();
        foreach ($queryParams as $key => $value) {
            $this->addQueryParameter($key, $value);
        }
    }
    
    /**
     * Gets uri.
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }
    
    /**
     * Sets uri.
     * 
     * @param string $uri The uri value.
     * 
     * @return none
     */
    public function setUri($uri)
    {
        Validate::isString($uri, 'uri');
        
        $this->_uri = $uri;
    }
    
    /**
     * Gets path.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Sets path.
     * 
     * @param string $path The path value.
     * 
     * @return none
     */
    public function setPath($path)
    {
        Validate::isString($path, 'path');
        
        $this->_path = $path;
    }
    
    /**
     * Gets statusCodes.
     * 
     * @return array
     */
    public function getStatusCodes()
    {
        return $this->_statusCodes;
    }
    
    /**
     * Sets statusCodes.
     * 
     * @param array $statusCodes The statusCodes value.
     * 
     * @return none
     */
    public function setStatusCodes($statusCodes)
    {
        $this->_statusCodes = array();
        foreach ($statusCodes as $value) {
            $this->addStatusCode($value);
        }
    }
    
    /**
     * Gets body.
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }
    
    /**
     * Sets body.
     * 
     * @param string $body The body value.
     * 
     * @return none
     */
    public function setBody($body)
    {
        Validate::isString($body, 'body');
        
        $this->_body = $body;
    }
    
    /**
     * Adds or sets header pair.
     * 
     * @param string $name  The HTTP header name.
     * @param string $value The HTTP header value.
     * 
     * @return none
     */
    public function addHeader($name, $value)
    {
        Validate::isString($name, 'name');
        Validate::isString($value, 'value');
        
        $this->_headers[$name] = $value;
    }
    
    /**
     * Adds or sets header pair.
     * 
     * Ignores header if it's value satisfies empty().
     * 
     * @param string $name  The HTTP header name.
     * @param string $value The HTTP header value.
     * 
     * @return none
     */
    public function addOptionalHeader($name, $value)
    {
        Validate::isString($name, 'name');
        Validate::isString($value, 'value');
        
        if (!empty($value)) {
            $this->_headers[$name] = $value;
        }
    }
    
    /**
     * Removes header from the HTTP request headers.
     * 
     * @param string $name The HTTP header name.
     * 
     * @return none
     */
    public function removeHeader($name)
    {
        Validate::isString($name, 'name');
        Validate::notNullOrEmpty($name, 'name');
        
        unset($this->_headers[$name]);
    }
    
    /**
     * Adds or sets query parameter pair.
     * 
     * @param string $name  The URI query parameter name.
     * @param string $value The URI query parameter value.
     * 
     * @return none
     */
    public function addQueryParameter($name, $value)
    {
        Validate::isString($name, 'name');
        Validate::isString($value, 'value');
        
        $this->_queryParams[$name] = $value;
    }
    
    /** 
     * Gets HTTP POST parameters. 
     *
     * @return array
     */
    public function getPostParameters()
    {   
        return $this->_postParameters;
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
        Validate::isArray($postParameters, 'postParameters');
        $this->_postParameters = $postParameters;
    }
    
    /**
     * Adds or sets query parameter pair.
     * 
     * Ignores query parameter if it's value satisfies empty().
     * 
     * @param string $name  The URI query parameter name.
     * @param string $value The URI query parameter value.
     * 
     * @return none
     */
    public function addOptionalQueryParameter($name, $value)
    {
        Validate::isString($name, 'name');
        Validate::isString($value, 'value');
        
        if (!empty($value)) {
            $this->_queryParams[$name] = $value;
        }
    }
    
    /**
     * Adds status code to the expected status codes.
     * 
     * @param integer $statusCode The expected status code.
     * 
     * @return none
     */
    public function addStatusCode($statusCode)
    {
        Validate::isInteger($statusCode, 'statusCode');
        
        $this->_statusCodes[] = $statusCode;
    }
    
    /**
     * Gets header value.
     * 
     * @param string $name The header name.
     * 
     * @return mix
     */
    public function getHeader($name)
    {
        return Utilities::tryGetValue($this->_headers, $name);
    }
    
    /**
     * Converts the context object to string.
     * 
     * @return string 
     */
    public function __toString()
    {
        $headers = Resources::EMPTY_STRING;
        $uri     = new Url($this->_uri);
        $uri     = $uri->getUrl();
        
        foreach ($this->_headers as $key => $value) {
            $headers .= "$key: $value\n";
        }
        
        $str  = "$this->_method $uri$this->_path HTTP/1.1\n$headers\n";
        $str .= "$this->_body";
        
        return $str;
    }
}


