<?php

namespace Common;

use InvalidArgumentException;

/**
 * This class will make it possible to have 1 function to get the correct language class.
 * This is useful for when you want to use the same code in the Back- and Frontend.
 * For instance in a trait.
 */
final class Language
{
    /**
     * @return string
     */
    public static function get()
    {
        return APPLICATION . '\Core\Language\Language';
    }

    /**
     * @param $function
     * @param $parameters
     *
     * @throws InvalidArgumentException when the function can't be called
     *
     * @return mixed
     */
    public static function callLanguageFunction($function, $parameters = [])
    {
        $languageClass = self::get();
        $callback = [$languageClass, $function];
        if (!method_exists($languageClass, $function) || !is_callable($callback)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The function %s::%s does not exist',
                    $languageClass,
                    $function
                )
            );
        }

        return call_user_func_array($callback, $parameters);
    }

    /**
     * Get a label.
     * This only implements the key because the other parameters differ between the front- and backend.
     *
     * @param $key
     *
     * @return string
     */
    public static function lbl($key)
    {
        return self::callLanguageFunction('lbl', [$key]);
    }

    /**
     * Get an error.
     * This only implements the key because the other parameters differ between the front- and backend.
     *
     * @param $key
     *
     * @return string
     */
    public static function err($key)
    {
        return self::callLanguageFunction('err', [$key]);
    }

    /**
     * Get an action.
     *
     *
     * @param $key
     *
     * @throws InvalidArgumentException when used in the backend.
     *
     * @return string
     */
    public static function act($key)
    {
        return self::callLanguageFunction('act', [$key]);
    }

    /**
     * Get a message.
     * This only implements the key because the other parameters differ between the front- and backend.
     *
     * @param $key
     *
     * @return string
     */
    public static function msg($key)
    {
        return self::callLanguageFunction('msg', [$key]);
    }
}
