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
 * Defines what are main url functionalities that should be supported
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Http
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
interface IUrl
{
    /**
     * Returns the query portion of the url
     * 
     * @return string
     */
    public function getQuery();

    /**
     * Returns the query portion of the url in array form
     * 
     * @return array
     */
    public function getQueryVariables();
    
    /**
     * Sets a an existing query parameter to value or creates a new one if the $key
     * doesn't exist.
     * 
     * @param string $key   query parameter name.
     * @param string $value query value.
     * 
     * @return none.
     */
    public function setQueryVariable($key, $value);
    
    /**
     * Gets actual URL string.
     * 
     * @return string.
     */
    public function getUrl();
    
    /**
     * Sets url path
     * 
     * @param string $urlPath url path to set.
     * 
     * @return none.
     */
    public function setUrlPath($urlPath);
    
    /**
     * Appends url path
     * 
     * @param string $urlPath url path to append.
     * 
     * @return none.
     */
    public function appendUrlPath($urlPath);
    
    /**
     * Gets actual URL string.
     * 
     * @return string.
     */
    public function __toString();
    
    /**
     * Makes deep copy from the current object.
     * 
     * @return WindowsAzure\Common\Internal\Http\Url
     */
    public function __clone();
    
    /**
     * Sets the query string to the specified variables in $array
     * 
     * @param array $array key/value representation of query variables.
     * 
     * @return none.
     */
    public function setQueryVariables($array);
}


