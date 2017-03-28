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
 * @package   WindowsAzure\Table\Models\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Models\Filters;

/**
 * Filter operations
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Models\Filters
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class Filter
{
    /**
     * Apply and operation between two filters
     * 
     * @param Filter $left  The left filter
     * @param Filter $right The right filter
     * 
     * @return \WindowsAzure\Table\Models\Filters\BinaryFilter 
     */
    public static function applyAnd($left, $right)
    {
        $filter = new BinaryFilter($left, 'and', $right);
        return $filter;
    }
   
    /**
     * Applies not operation on $operand
     * 
     * @param Filter $operand The operand
     * 
     * @return \WindowsAzure\Table\Models\Filters\UnaryFilter 
     */
    public static function applyNot($operand)
    {
        $filter = new UnaryFilter('not', $operand);
        return $filter;
    }

    /**
     * Apply or operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyOr($left, $right)
    {
        $filter = new BinaryFilter($left, 'or', $right);
        return $filter;
    }

    /**
     * Apply eq operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyEq($left, $right)
    {
        $filter = new BinaryFilter($left, 'eq', $right);
        return $filter;
    }

    /**
     * Apply ne operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyNe($left, $right)
    {
        $filter = new BinaryFilter($left, 'ne', $right);
        return $filter;
    }

    /**
     * Apply ge operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyGe($left, $right)
    {
        $filter = new BinaryFilter($left, 'ge', $right);
        return $filter;
    }

    /**
     * Apply gt operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyGt($left, $right)
    {
        $filter = new BinaryFilter($left, 'gt', $right);
        return $filter;
    }

    /**
     * Apply lt operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyLt($left, $right)
    {
        $filter = new BinaryFilter($left, 'lt', $right);
        return $filter;
    }

    /**
     * Apply le operation on the passed filers
     * 
     * @param Filter $left  The left operand
     * @param Filter $right The right operand
     * 
     * @return BinaryFilter
     */
    public static function applyLe($left, $right)
    {
        $filter = new BinaryFilter($left, 'le', $right);
        return $filter;
    }

    /**
     * Apply constant filter on value.
     * 
     * @param mix    $value   The filter value
     * @param string $edmType The value EDM type.
     * 
     * @return \WindowsAzure\Table\Models\Filters\ConstantFilter 
     */
    public static function applyConstant($value, $edmType = null)
    {
        $filter = new ConstantFilter($edmType, $value);
        return $filter;
    }

    /**
     * Apply propertyName filter on $value
     * 
     * @param string $value The filter value
     * 
     * @return \WindowsAzure\Table\Models\Filters\PropertyNameFilter 
     */
    public static function applyPropertyName($value)
    {
        $filter = new PropertyNameFilter($value);
        return $filter;
    }

    /**
     * Takes raw string filter
     * 
     * @param string $value The raw string filter expression
     * 
     * @return \WindowsAzure\Table\Models\Filters\QueryStringFilter 
     */
    public static function applyQueryString($value)
    {
        $filter = new QueryStringFilter($value);
        return $filter;
    }
}


