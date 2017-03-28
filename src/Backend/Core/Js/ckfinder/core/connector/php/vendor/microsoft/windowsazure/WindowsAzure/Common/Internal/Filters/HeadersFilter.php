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
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal\Filters;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\IServiceFilter;

/**
 * Adds all passed headers to the HTTP request headers.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class HeadersFilter implements IServiceFilter
{
    /**
     * @var array
     */
    private $_headers;
    
    /**
     * Constructor
     * 
     * @param array $headers static headers to be added.
     * 
     * @return HeadersFilter
     */
    public function __construct($headers)
    {
        $this->_headers = $headers;
    }
    
    /**
     * Adds static header(s) to the HTTP request headers
     *
     * @param HttpClient $request HTTP channel object.
     * 
     * @return \HTTP_Request2
     */
    public function handleRequest($request) 
    {
        foreach ($this->_headers as $key => $value) {
            $headers = $request->getHeaders();
            if (!array_key_exists($key, $headers)) {
                $request->setHeader($key, $value);
            }
        }

        return $request;
    }

    /**
     * Does nothing with the response.
     *
     * @param HttpClient              $request  HTTP channel object.
     * @param \HTTP_Request2_Response $response HTTP response object.
     * 
     * @return \HTTP_Request2_Response
     */
    public function handleResponse($request, $response) 
    {
        // Do nothing with the response.
        return $response;
    }
}


