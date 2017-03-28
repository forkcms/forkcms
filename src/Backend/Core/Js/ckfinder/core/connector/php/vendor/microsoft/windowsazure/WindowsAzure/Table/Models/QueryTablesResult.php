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
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Models;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;

/**
 * QueryTablesResult
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class QueryTablesResult
{
    /**
     * @var string 
     */
    private $_nextTableName;
    
    /**
     * @var array
     */
    private $_tables;
    
    /**
     * Creates new QueryTablesResult object
     * 
     * @param array $headers The HTTP response headers
     * @param array $entries The table entriess
     * 
     * @return \WindowsAzure\Table\Models\QueryTablesResult 
     */
    public static function create($headers, $entries)
    {
        $result  = new QueryTablesResult();
        $headers = array_change_key_case($headers);
        
        $result->setNextTableName(
            Utilities::tryGetValue(
                $headers, Resources::X_MS_CONTINUATION_NEXTTABLENAME
            )
        );
        $result->setTables($entries);
        
        return $result;
    }
    
    /**
     * Gets nextTableName
     * 
     * @return string
     */
    public function getNextTableName()
    {
        return $this->_nextTableName;
    }
    
    /**
     * Sets nextTableName
     * 
     * @param string $nextTableName value
     * 
     * @return none
     */
    public function setNextTableName($nextTableName)
    {
        $this->_nextTableName = $nextTableName;
    }
    
    /**
     * Gets tables
     * 
     * @return array
     */
    public function getTables()
    {
        return $this->_tables;
    }
    
    /**
     * Sets tables
     * 
     * @param array $tables value
     * 
     * @return none
     */
    public function setTables($tables)
    {
        $this->_tables = $tables;
    }
}


