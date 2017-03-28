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

/**
 * Batch parameter names.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchOperationParameterName
{
    const BP_TABLE         = 'table';
    const BP_ENTITY        = 'entity';
    const BP_PARTITION_KEY = 'PartitionKey';
    const BP_ROW_KEY       = 'RowKey';
    const BP_ETAG          = 'etag';
    
    /**
     * Validates if $paramName is already defined.
     * 
     * @param string $paramName The batch operation parameter name.
     * 
     * @return boolean 
     */
    public static function isValid($paramName)
    {
        switch ($paramName) {
        case self::BP_TABLE:
        case self::BP_ENTITY:
        case self::BP_PARTITION_KEY:
        case self::BP_ROW_KEY:
        case self::BP_ETAG:
        return true;

        default:
        return false;
        }
    }
}


