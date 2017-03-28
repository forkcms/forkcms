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
use WindowsAzure\Common\Internal\InvalidArgumentTypeException;
use WindowsAzure\Common\Internal\Resources;

/**
 * Validates aganist a condition and throws an exception in case of failure.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Validate
{
    /**
     * Throws exception if the provided variable type is not array.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws InvalidArgumentTypeException.
     *
     * @return none
     */
    public static function isArray($var, $name)
    {
        if (!is_array($var)) {
            throw new InvalidArgumentTypeException(gettype(array()), $name);
        }
    }

    /**
     * Throws exception if the provided variable type is not string.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws InvalidArgumentTypeException
     *
     * @return none
     */
    public static function isString($var, $name)
    {
        try {
            (string)$var;
        } catch (\Exception $e) {
            throw new InvalidArgumentTypeException(gettype(''), $name);
        }
    }

    /**
     * Throws exception if the provided variable type is not boolean.
     *
     * @param mix $var variable to check against.
     *
     * @throws InvalidArgumentTypeException
     *
     * @return none
     */
    public static function isBoolean($var)
    {
        (bool)$var;
    }

    /**
     * Throws exception if the provided variable is set to null.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws \InvalidArgumentException
     *
     * @return none
     */
    public static function notNullOrEmpty($var, $name)
    {
        if (is_null($var) || empty($var)) {
            throw new \InvalidArgumentException(
                sprintf(Resources::NULL_OR_EMPTY_MSG, $name)
            );
        }
    }

    /**
     * Throws exception if the provided variable is not double.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws \InvalidArgumentException
     *
     * @return none
     */
    public static function isDouble($var, $name)
    {
        if (!is_numeric($var)) {
            throw new InvalidArgumentTypeException('double', $name);
        }
    }

    /**
     * Throws exception if the provided variable type is not integer.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws InvalidArgumentTypeException
     *
     * @return none
     */
    public static function isInteger($var, $name)
    {
        try {
            (int)$var;
        } catch (\Exception $e) {
            throw new InvalidArgumentTypeException(gettype(123), $name);
        }
    }

    /**
     * Returns whether the variable is an empty or null string.
     *
     * @param string $var value.
     *
     * @return boolean
     */
    public static function isNullOrEmptyString($var)
    {
        try {
            (string)$var;
        } catch (\Exception $e) {
            return false;
        }

        return (!isset($var) || trim($var)==='');
    }

    /**
     * Throws exception if the provided condition is not satisfied.
     *
     * @param bool   $isSatisfied    condition result.
     * @param string $failureMessage the exception message
     *
     * @throws \Exception
     *
     * @return none
     */
    public static function isTrue($isSatisfied, $failureMessage)
    {
        if (!$isSatisfied) {
            throw new \InvalidArgumentException($failureMessage);
        }
    }

    /**
     * Throws exception if the provided $date is not of type \DateTime
     *
     * @param mix $date variable to check against.
     *
     * @throws WindowsAzure\Common\Internal\InvalidArgumentTypeException
     *
     * @return none
     */
    public static function isDate($date)
    {
        if (gettype($date) != 'object' || get_class($date) != 'DateTime') {
            throw new InvalidArgumentTypeException('DateTime');
        }
    }

    /**
     * Throws exception if the provided variable is set to null.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws \InvalidArgumentException
     *
     * @return none
     */
    public static function notNull($var, $name)
    {
        if (is_null($var)) {
            throw new \InvalidArgumentException(sprintf(Resources::NULL_MSG, $name));
        }
    }

    /**
     * Throws exception if the object is not of the specified class type.
     *
     * @param mixed  $objectInstance An object that requires class type validation.
     * @param mixed  $classInstance  The instance of the class the the
     * object instance should be.
     * @param string $name           The name of the object.
     *
     * @throws \InvalidArgumentException
     *
     * @return none
     */
    public static function isInstanceOf($objectInstance, $classInstance, $name)
    {
        Validate::notNull($classInstance, 'classInstance');
        if (is_null($objectInstance)) {
            return true;
        }

        $objectType = gettype($objectInstance);
        $classType  = gettype($classInstance);

        if ($objectType === $classType) {
            return true;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    Resources::INSTANCE_TYPE_VALIDATION_MSG,
                    $name,
                    $objectType,
                    $classType
                )
            );
        }
    }

    /**
     * Creates a anonymous function that check if the given uri is valid or not.
     *
     * @return callable
     */
    public static function getIsValidUri()
    {
        return function ($uri) {
            return Validate::isValidUri($uri);
        };
    }

    /**
     * Throws exception if the string is not of a valid uri.
     *
     * @param string $uri String to check.
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    public static function isValidUri($uri)
    {
        $isValid = filter_var($uri, FILTER_VALIDATE_URL);

        if ($isValid) {
            return true;
        } else {
            throw new \RuntimeException(
                sprintf(Resources::INVALID_CONFIG_URI, $uri)
            );
        }
    }

    /**
     * Throws exception if the provided variable type is not object.
     *
     * @param mix    $var  The variable to check.
     * @param string $name The parameter name.
     *
     * @throws InvalidArgumentTypeException.
     *
     * @return boolean
     */
    public static function isObject($var, $name)
    {
        if (!is_object($var)) {
            throw new InvalidArgumentTypeException('object', $name);
        }

        return true;
    }

    /**
     * Throws exception if the object is not of the specified class type.
     *
     * @param mixed  $objectInstance An object that requires class type validation.
     * @param string $class          The class the object instance should be.
     * @param string $name           The parameter name.
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    public static function isA($objectInstance, $class, $name)
    {
        Validate::isString($class, 'class');
        Validate::notNull($objectInstance, 'objectInstance');
        Validate::isObject($objectInstance, 'objectInstance');

        $objectType = get_class($objectInstance);

        if (is_a($objectInstance, $class)) {
            return true;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    Resources::INSTANCE_TYPE_VALIDATION_MSG,
                    $name,
                    $objectType,
                    $class
                )
            );
        }
    }

    /**
     * Validate if method exists in object
     *
     * @param object $objectInstance An object that requires method existing
     *                               validation
     * @param string $method         Method name
     * @param string $name           The parameter name
     *
     * @return boolean
     */
    public static function methodExists($objectInstance, $method, $name)
    {
        Validate::isString($method, 'method');
        Validate::notNull($objectInstance, 'objectInstance');
        Validate::isObject($objectInstance, 'objectInstance');

        if (method_exists($objectInstance, $method)) {
            return true;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    Resources::ERROR_METHOD_NOT_FOUND,
                    $method,
                    $name
                )
            );
        }
    }

    /**
     * Validate if string is date formatted
     *
     * @param string $value Value to validate
     * @param string $name  Name of parameter to insert in erro message
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    public static function isDateString($value, $name)
    {
        Validate::isString($value, 'value');

        try {
            new \DateTime($value);
            return true;
        }
        catch (\Exception $e) {
            throw new \InvalidArgumentException(
                sprintf(
                    Resources::ERROR_INVALID_DATE_STRING,
                    $name,
                    $value
                )
            );
        }
    }

}


