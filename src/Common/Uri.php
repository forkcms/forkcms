<?php

namespace Common;

use Behat\Transliterator\Transliterator;

/**
 * This is our Uri generating class
 */
class Uri
{
    /**
     * Prepares a string so that it can be used in urls.
     *
     * @param string $value The value that should be urlized.
     *
     * @return string  The urlized string.
     */
    public static function getUrl(string $value): string
    {
        // convert cyrlic, greek or other caracters to ASCII characters
        $value = Transliterator::transliterate($value);

        // make a clean url out of it
        return Transliterator::urlize($value);
    }
}
