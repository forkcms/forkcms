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
 * @package   WindowsAzure\Common\Internal\Authentication
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal\Authentication;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Authentication\IAuthScheme;


/**
 * Base class for azure authentication schemes.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Authentication
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
abstract class StorageAuthScheme implements IAuthScheme
{
    protected $accountName;
    protected $accountKey;

    /**
     * Constructor.
     *
     * @param string $accountName storage account name.
     * @param string $accountKey  storage account primary or secondary key.
     * 
     * @return 
     * WindowsAzure\Common\Internal\Authentication\StorageAuthScheme
     */
    public function __construct($accountName, $accountKey)
    {
        $this->accountKey  = $accountKey;
        $this->accountName = $accountName;
    }

    /**
     * Computes canonicalized headers for headers array.
     *
     * @param array $headers request headers.
     * 
     * @see Constructing the Canonicalized Headers String section at 
     *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     * 
     * @return array
     */
    protected function computeCanonicalizedHeaders($headers)
    {
        $canonicalizedHeaders = array();
        $normalizedHeaders    = array();
        $validPrefix          =  Resources::X_MS_HEADER_PREFIX;

        if (is_null($normalizedHeaders)) {
            return $canonicalizedHeaders;
        }
        
        foreach ($headers as $header => $value) {            
            // Convert header to lower case.
            $header = strtolower($header);
            
            // Retrieve all headers for the resource that begin with x-ms-,
            // including the x-ms-date header.
            if (Utilities::startsWith($header, $validPrefix)) {
                // Unfold the string by replacing any breaking white space 
                // (meaning what splits the headers, which is \r\n) with a single 
                // space.
                $value = str_replace("\r\n", ' ', $value);
                
                // Trim any white space around the colon in the header.
                $value  = ltrim($value);
                $header = rtrim($header);
                
                $normalizedHeaders[$header] = $value;
            }
        }
        
        // Sort the headers lexicographically by header name, in ascending order.
        // Note that each header may appear only once in the string.
        ksort($normalizedHeaders);
        
        foreach ($normalizedHeaders as $key => $value) {
            $canonicalizedHeaders[] = $key . ':' . $value;
        }

        return $canonicalizedHeaders;
    }
    
    /**
     * Computes canonicalized resources from URL using Table formar
     *
     * @param string $url         request url.
     * @param array  $queryParams request query variables.
     * 
     * @see Constructing the Canonicalized Resource String section at 
     *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     * 
     * @return string
     */
    protected function computeCanonicalizedResourceForTable($url, $queryParams)
    {
        $queryParams = array_change_key_case($queryParams);
        
        // 1. Beginning with an empty string (""), append a forward slash (/), 
        //    followed by the name of the account that owns the accessed resource.
        $canonicalizedResource = '/' . $this->accountName;

        // 2. Append the resource's encoded URI path, without any query parameters.
        $canonicalizedResource .= parse_url($url, PHP_URL_PATH);
        
        // 3. The query string should include the question mark and the comp 
        //    parameter (for example, ?comp=metadata). No other parameters should 
        //    be included on the query string.
        if (array_key_exists(Resources::QP_COMP, $queryParams)) {
            $canonicalizedResource .= '?' . Resources::QP_COMP . '=';
            $canonicalizedResource .= $queryParams[Resources::QP_COMP];
        }
        
        return $canonicalizedResource;
    }

    /**
     * Computes canonicalized resources from URL.
     *
     * @param string $url         request url.
     * @param array  $queryParams request query variables.
     * 
     * @see Constructing the Canonicalized Resource String section at 
     *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     * 
     * @return string
     */
    protected function computeCanonicalizedResource($url, $queryParams)
    {
        $queryParams = array_change_key_case($queryParams);
        
        // 1. Beginning with an empty string (""), append a forward slash (/), 
        //    followed by the name of the account that owns the accessed resource.
        $canonicalizedResource = '/' . $this->accountName;

        // 2. Append the resource's encoded URI path, without any query parameters.
        $canonicalizedResource .= parse_url($url, PHP_URL_PATH);

        // 3. Retrieve all query parameters on the resource URI, including the comp 
        //    parameter if it exists.
        // 4. Sort the query parameters lexicographically by parameter name, in 
        //    ascending order.
        if (count($queryParams) > 0) {
            ksort($queryParams);
        }

        // 5. Convert all parameter names to lowercase.
        // 6. URL-decode each query parameter name and value.
        // 7. Append each query parameter name and value to the string in the 
        //    following format:
        //      parameter-name:parameter-value
        // 9. Group query parameters
        // 10. Append a new line character (\n) after each name-value pair.
        foreach ($queryParams as $key => $value) {
            // Grouping query parameters
            $values = explode(Resources::SEPARATOR, $value);
            sort($values);
            $separated = implode(Resources::SEPARATOR, $values);
            
            $canonicalizedResource .= "\n" . $key . ':' . $separated;
        }

        return $canonicalizedResource;
    }

    /**
     * Returns authorization header to be included in the request.
     *
     * @param array  $headers     request headers.
     * @param string $url         reuqest url.
     * @param array  $queryParams query variables.
     * @param string $httpMethod  request http method.
     * 
     * @see Specifying the Authorization Header section at 
     *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     * 
     * @abstract
     * 
     * @return string
     */
    abstract public function getAuthorizationHeader($headers, $url, $queryParams, 
        $httpMethod
    );

    /**
     * Computes the authorization signature.
     *
     * @param array  $headers     request headers.
     * @param string $url         reuqest url.
     * @param array  $queryParams query variables.
     * @param string $httpMethod  request http method.
     * 
     * @see check all authentication schemes at
     *      http://msdn.microsoft.com/en-us/library/windowsazure/dd179428.aspx
     * 
     * @abstract
     * 
     * @return string
     */
    abstract protected function computeSignature($headers, $url, $queryParams, 
        $httpMethod
    );
}


