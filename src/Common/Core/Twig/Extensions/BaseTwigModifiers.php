<?php

namespace Common\Core\Twig\Extensions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use SpoonFilter;

/**
 * Contains Base Frontend-related custom modifiers.
 * These filters work independent of front/backend.
 */
class BaseTwigModifiers
{
    /**
     * Format a number as currency
     *    syntax: {{ $string|formatcurrency($currency, $decimals) }}.
     *
     * @param float $number The string to form.
     * @param string $currency The currency to will be used to format the number.
     * @param int $decimals The number of decimals to show.
     *
     * @return string
     */
    public static function formatCurrency(float $number, string $currency = 'EUR', int $decimals = null): string
    {
        $decimals = $decimals === null ? 2 : $decimals;

        // @later get settings from backend
        switch ($currency) {
            case 'EUR':
                $currency = '€';
                break;
            default:
        }

        return $currency . '&nbsp;' . static::formatNumber($number, $decimals);
    }

    /**
     * Fallback for if our parent functions don't implement this method
     *
     * @param float $number
     * @param int $decimals
     *
     * @return string
     */
    public static function formatNumber(float $number, int $decimals = null): string
    {
        if ($decimals === null) {
            $decimals = 2;
        }

        return number_format($number, $decimals, ',', '&nbsp;');
    }

    /**
     * Highlights all strings in <code> tags.
     *    syntax: {{ $string|highlight }}.
     *
     * @param string $string The string passed from the template.
     *
     * @return string
     */
    public static function highlightCode(string $string): string
    {
        // regex pattern
        $pattern = '/<code>.*?<\/code>/is';

        // find matches
        if (preg_match_all($pattern, $string, $matches)) {
            // loop matches
            foreach ($matches[0] as $match) {
                // encase content in highlight_string
                $string = str_replace($match, highlight_string($match, true), $string);

                // replace highlighted code tags in match
                $string = str_replace(['&lt;code&gt;', '&lt;/code&gt;'], '', $string);
            }
        }

        return $string;
    }

    /**
     * Get a random var between a min and max
     *    syntax: {{ rand($min, $max) }}.
     *
     * @param int $min The minimum random number.
     * @param int $max The maximum random number.
     *
     * @return int
     */
    public static function random(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /**
     * Convert a multi line string into a string without newlines so it can be handles by JS
     *    syntax: {{ $string|stripnewlines }}.
     *
     * @param string $string The variable that should be processed.
     *
     * @return string
     */
    public static function stripNewlines(string $string): string
    {
        return str_replace(["\r\n", "\n", "\r"], ' ', $string);
    }

    /**
     * Transform the string to uppercase.
     *    syntax: {{ $string|uppercase }}.
     *
     * @param string $string The string that you want to apply this method on.
     *
     * @return string The string, completly uppercased.
     */
    public static function uppercase(string $string): string
    {
        return mb_convert_case($string, MB_CASE_UPPER, \Spoon::getCharset());
    }

    /**
     * Makes this string lowercase.
     *    syntax: {{ $string|lowercase }}.
     *
     *
     * @param string $string The string that you want to apply this method on.
     *
     * @return string The string, completely lowercased.
     */
    public static function lowercase(string $string): string
    {
        return mb_convert_case($string, MB_CASE_LOWER, \Spoon::getCharset());
    }

    /**
     * snakeCase Converter.
     *    syntax: {{ $string|snakecase }}.
     *
     * @internal Untested, Needs testing
     *
     * @param string $string
     *
     * @return string
     */
    public static function snakeCase(string $string): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $string)), '_');
    }

    /**
     * CamelCase Converter.
     *    syntax: {{ $string|camelcase }}.
     *
     * @internal Untested, Needs testing
     *
     * @param string $string
     *
     * @return string
     */
    public static function camelCase(string $string): string
    {
        // non-alpha and non-numeric characters become spaces
        $string = preg_replace('/[^a-z0-9' . implode('', []) . ']+/i', ' ', $string);
        $string = trim($string);
        // uppercase the first character of each word
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = lcfirst($string);

        return $string;
    }

    /**
     * Formats a language specific date.
     *    syntax: {{ $timestamp|spoondate($format, $language) }}.
     *
     * @param string|int $timestamp The timestamp or date that you want to apply the format to.
     * @param string $format The optional format that you want to apply on the provided timestamp.
     * @param string $language The optional language that you want this format in (Check SpoonLocale for the possible languages).
     *
     * @return string The formatted date according to the timestamp, format and provided language.
     */
    public static function spoonDate($timestamp, $format = 'Y-m-d H:i:s', $language = 'en')
    {
        if (is_string($timestamp) && !is_numeric($timestamp)) {
            // use strptime if you want to restrict the input format
            $timestamp = strtotime($timestamp);
        }

        return \SpoonDate::getDate($format, $timestamp, $language);
    }

    /**
     * Shows a v or x to indicate the boolean state (Y|N, j|n, true|false).
     *    syntax: {{ showbool($status, $reverse) }}.
     *
     * @param string|bool $status
     * @param bool $reverse show the opposite of the status
     *
     * @return string
     */
    public static function showBool($status, bool $reverse = false): string
    {
        $showTrue = '<strong style="color:green">&#10003;</strong>';
        $showFalse = '<strong style="color:red">&#10008;</strong>';

        if ($status === 'Y' || $status === 'y' || $status === 1 || $status === '1' || $status === true) {
            return $reverse ? self::showBool(false) : $showTrue;
        }

        if ($status === 'N' || $status === 'n' || $status === 0 || $status === '0' || $status === false) {
            return $reverse ? self::showBool(true) : $showFalse;
        }

        return $status;
    }

    /**
     * Truncate a string
     *    syntax: {{ $string|truncate($max-length, $append-hellip, $closest-word) }}.
     *
     * @param string $string The string passed from the template.
     * @param int $length The maximum length of the truncated string.
     * @param bool $useHellip Should a hellip be appended if the length exceeds the requested length?
     * @param bool $closestWord Truncate on exact length or on closest word?
     *
     * @return string
     */
    public static function truncate(
        string $string,
        int $length,
        bool $useHellip = true,
        bool $closestWord = false
    ): string {
        // remove special chars, all of them, also the ones that shouldn't be there.
        $string = SpoonFilter::htmlentitiesDecode($string, null, ENT_QUOTES);

        // remove HTML
        $string = strip_tags($string);

        // less characters
        if (mb_strlen($string) <= $length) {
            return SpoonFilter::htmlspecialchars($string);
        }

        // more characters
        // hellip is seen as 1 char, so remove it from length
        if ($useHellip) {
            --$length;
        }

        // truncate
        $string = $closestWord
            ? mb_substr($string, 0, strrpos(substr($string, 0, $length + 1), ' '), 'UTF-8')
            : mb_substr($string, 0, $length, 'UTF8');

        // add hellip
        if ($useHellip) {
            $string .= '…';
        }

        // return
        return SpoonFilter::htmlspecialchars($string, ENT_QUOTES);
    }
}
