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
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Blob\Models;

/**
 * Holds information about blob block.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Block
{
    /**
     * @var string
     */
    private $_blockId;
    
    /**
     * @var string
     */
    private $_type;
    
    /**
     * Sets the blockId.
     * 
     * @param string $blockId The id of the block.
     * 
     * @return none
     */
    public function setBlockId($blockId)
    {
        $this->_blockId = $blockId;
    }
    
    /**
     * Gets the blockId.
     * 
     * @return string
     */
    public function getBlockId()
    {
        return $this->_blockId;
    }
    
    /**
     * Sets the type.
     * 
     * @param string $type The type of the block.
     * 
     * @return none
     */
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    /**
     * Gets the type.
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
}


