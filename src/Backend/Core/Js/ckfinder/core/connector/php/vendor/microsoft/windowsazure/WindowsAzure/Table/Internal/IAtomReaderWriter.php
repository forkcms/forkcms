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
 * Defines how to serialize and unserialize table wrapper xml
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
interface IAtomReaderWriter
{
    /**
     * Constructs XML representation for table entry.
     * 
     * @param string $name The name of the table.
     * 
     * @return string
     */
    public function getTable($name);
    
    /**
     * Parses one table entry.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return string 
     */
    public function parseTable($body);
    
    /**
     * Constructs array of tables from HTTP response body.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return array
     */
    public function parseTableEntries($body);
    
    /**
     * Constructs XML representation for entity.
     * 
     * @param Models\Entity $entity The entity instance.
     * 
     * @return string
     */
    public function getEntity($entity);
    
    /**
     * Constructs entity from HTTP response body.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return Models\Entity
     */
    public function parseEntity($body);
    
    /**
     * Constructs array of entities from HTTP response body.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return array
     */
    public function parseEntities($body);
}


