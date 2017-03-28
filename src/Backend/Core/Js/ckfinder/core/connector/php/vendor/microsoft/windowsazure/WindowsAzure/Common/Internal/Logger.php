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
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Common\Internal;

/**
 * Logger class for debugging purpose.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Logger
{
    /**
     * @var string
     */
    private static $_filePath;

    /**
     * Logs $var to file.
     *
     * @param mix    $var The data to log.
     * @param string $tip The help message.
     * 
     * @static
     * 
     * @return none
     */
    public static function log($var, $tip = Resources::EMPTY_STRING)
    {
        if (!empty($tip)) {
            error_log($tip . "\n", 3, self::$_filePath);
        }
        
        if (is_array($var) || is_object($var)) {
            error_log(print_r($var, true), 3, self::$_filePath);
        } else {
            error_log($var . "\n", 3, self::$_filePath);
        }
    }
    
    /**
     * Sets file path to use.
     *
     * @param string $filePath The log file path.
     * 
     * @static
     * 
     * @return none
     */
    public static function setLogFile($filePath)
    {
        self::$_filePath = $filePath;
    }
}


