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
 * Supported batch operations.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchOperationType
{
    const INSERT_ENTITY_OPERATION         = 'InsertEntityOperation';
    const UPDATE_ENTITY_OPERATION         = 'UpdateEntityOperation';
    const DELETE_ENTITY_OPERATION         = 'DeleteEntityOperation';
    const MERGE_ENTITY_OPERATION          = 'MergeEntityOperation';
    const INSERT_REPLACE_ENTITY_OPERATION = 'InsertOrReplaceEntityOperation';
    const INSERT_MERGE_ENTITY_OPERATION   = 'InsertOrMergeEntityOperation';
    
    /**
     * Validates if $type is already defined.
     * 
     * @param string $type The operation type.
     * 
     * @return boolean 
     */
    public static function isValid($type)
    {
        switch ($type) {
        case self::INSERT_ENTITY_OPERATION:
        case self::UPDATE_ENTITY_OPERATION:
        case self::DELETE_ENTITY_OPERATION:
        case self::MERGE_ENTITY_OPERATION:
        case self::INSERT_REPLACE_ENTITY_OPERATION:
        case self::INSERT_MERGE_ENTITY_OPERATION:
        return true;
                
        default:
        return false;
        }
    }
}


