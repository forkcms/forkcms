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
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Internal;

/**
 * Interface for MIME reading and writing.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
interface IMimeReaderWriter
{
    /**
     * Given array of MIME parts in raw string, this function converts them into MIME
     * representation. 
     * 
     * @param array $bodyPartContents The MIME body parts.
     * 
     * @return array Returns array with two elements 'headers' and 'body' which
     * represents the MIME message.
     */
    public function encodeMimeMultipart($bodyPartContents);
    
    /**
     * Parses given mime HTTP response body into array. Each array element 
     * represents a change set result.
     * 
     * @param string $mimeBody The raw MIME body result.
     * 
     * @return array
     */
    public function decodeMimeMultipart($mimeBody);
}


