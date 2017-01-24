<?php

namespace Common;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Behat\Transliterator\Transliterator;

/**
 * This is our Uri generating class
 */
class Uri
{
    /**
     * Prepares a string so that it can be used in urls.
     *
     * @param  string $value The value that should be urlised.
     *
     * @return string        The urlised string.
     */
    public static function getUrl($value)
    {
        // convert cyrlic, greek or other caracters to ASCII characters
        $value = Transliterator::transliterate($value);

        // make a clean url out of it
        return Transliterator::urlize($value);
    }
}
