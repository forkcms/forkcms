<?php

namespace ForkCMS\Core\Domain\Util;

final class ArrayUtil
{
    /**
     * Turn a multidimensional array into a flat array with dot notation
     *
     * @param array<string,mixed> $array
     * @return array<string,mixed>
     */
    public static function flatten(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach($array as $key=>$value) {
            if(is_array($value)) {
                $result += self::flatten($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
