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
 * @package   WindowsAzure\Common\Internal\Serialization
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal\Serialization;

/**
 * The serialization interface.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal\Serialization
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
interface ISerializer
{

    /** 
     * Serialize an object into a XML.
     * 
     * @param Object $targetObject The target object to be serialized. 
     * @param string $rootName     The name of the root.
     *
     * @return string
     */
    public static function objectSerialize($targetObject, $rootName);

    /**
     * Serializes given array. The array indices must be string to use them as
     * as element name.
     * 
     * @param array $array      The object to serialize represented in array.
     * @param array $properties The used properties in the serialization process.
     * 
     * @return string
     */
    public function serialize($array, $properties = null);

    
    /**
     * Unserializes given serialized string.
     * 
     * @param string $serialized The serialized object in string representation.
     * 
     * @return array
     */
    public function unserialize($serialized);
}


