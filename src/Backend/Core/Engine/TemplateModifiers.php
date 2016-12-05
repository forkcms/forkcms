<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Common\Core\Twig\Extensions\BaseTwigModifiers;
use Backend\Core\Language\Language as BackendLanguage;
use \SpoonDate;

/**
 * This is our class with custom modifiers.
 */
class TemplateModifiers extends BaseTwigModifiers
{
    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdate }}
     *
     * @param int $var The UNIX-timestamp to format.
     *
     * @return string
     */
    public static function formatDate($var)
    {
        // get setting
        $format = Authentication::getUser()->getSetting('date_format');

        if ($var instanceof \DateTime) {
            $var = $var->getTimestamp();
        }

        // format the date
        return SpoonDate::getDate($format, (int) $var, BackendLanguage::getInterfaceLanguage());
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdatetime }}
     *
     * @param int $var The UNIX-timestamp to format.
     *
     * @return string
     */
    public static function formatDateTime($var)
    {
        // get setting
        $format = Authentication::getUser()->getSetting('datetime_format');

        if ($var instanceof \DateTime) {
            $var = $var->getTimestamp();
        }

        // format the date
        return SpoonDate::getDate($format, (int) $var, BackendLanguage::getInterfaceLanguage());
    }

    /**
     * Format a number as a float
     * syntax: {$var|formatfloat}
     *
     * @param float $number   The number to format.
     * @param int   $decimals The number of decimals.
     *
     * @return string
     */
    public static function formatFloat($number, $decimals = 2)
    {
        $number = (float) $number;
        $decimals = (int) $decimals;

        // get setting
        $format = Authentication::getUser()->getSetting('number_format', 'dot_nothing');

        // get separators
        $separators = explode('_', $format);
        $separatorSymbols = array('comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => '');
        $decimalSeparator = (
            isset($separators[0], $separatorSymbols[$separators[0]]) ? $separatorSymbols[$separators[0]] : null
        );
        $thousandsSeparator = (
            isset($separators[1], $separatorSymbols[$separators[1]]) ? $separatorSymbols[$separators[1]] : null
        );

        // format the number
        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Format a number
     *    syntax: {{ $string|formatnumber($decimals) }}
     *
     * @param float $number The number to format.
     * @param int $decimals The number of decimals
     *
     * @return string
     */
    public static function formatNumber($number, $decimals = null)
    {
        $number = (float) $number;

        // get setting
        $format = Authentication::getUser()->getSetting('number_format', 'dot_nothing');

        // get amount of decimals
        if ($decimals === null) {
            $decimals = (mb_strpos($number, '.') ? mb_strlen(mb_substr($number, mb_strpos($number, '.') + 1)) : 0);
        }

        // get separators
        $separators = explode('_', $format);
        $separatorSymbols = array('comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => '');
        $decimalSeparator = (
            isset($separators[0], $separatorSymbols[$separators[0]]) ? $separatorSymbols[$separators[0]] : null
        );
        $thousandsSeparator = (
            isset($separators[1], $separatorSymbols[$separators[1]]) ? $separatorSymbols[$separators[1]] : null
        );

        // format the number
        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdate }}
     *
     * @param int $var The UNIX-timestamp to format.
     *
     * @return string
     */
    public static function formatTime($var)
    {
        // get setting
        $format = Authentication::getUser()->getSetting('time_format');

        if ($var instanceof \DateTime) {
            $var = $var->getTimestamp();
        }

        // format the date
        return SpoonDate::getDate($format, (int) $var, BackendLanguage::getInterfaceLanguage());
    }

    /**
     * Convert a var into a URL
     * syntax: {{ geturl:<action>[:<module>] }}
     *
     * @param string $action The action to build the URL for.
     * @param string $module The module to build the URL for.
     * @param string $suffix A string to append.
     * @param string $language A language code
     *
     * @return string
     */
    public static function getURL($action = null, $module = null, $suffix = null, $language = null)
    {
        if (!in_array($language, BackendLanguage::getActiveLanguages())) {
            $language = BackendLanguage::getWorkingLanguage();
        }

        $action = ($action !== null) ? (string) $action : null;
        $module = ($module !== null) ? (string) $module : null;

        return BackendModel::createURLForAction($action, $module, $language) . $suffix;
    }

    /**
     * Convert this string into a well formed label.
     *  syntax: {{ var|tolabel }}.
     *
     * @param string $value The value to convert to a label.
     *
     * @return string
     */
    public static function toLabel($value)
    {
        return \SpoonFilter::ucfirst(BackendLanguage::lbl(\SpoonFilter::toCamelCase($value, '_', false)));
    }

    /**
     * Truncate a string
     *    syntax: {$var|truncate:max-length[:append-hellip][:closest-word]}
     *
     * @param string $var      The string passed from the template.
     * @param int    $length      The maximum length of the truncated string.
     * @param bool   $useHellip   Should a hellip be appended if the length exceeds the requested length?
     * @param bool   $closestWord Truncate on exact length or on closest word?
     *
     * @return string
     */
    public static function truncate($var, $length, $useHellip = true, $closestWord = false)
    {
        // init vars
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');

        // remove special chars, all of them, also the ones that shouldn't be there.
        $var = \SpoonFilter::htmlentitiesDecode($var, null, ENT_QUOTES);

        // remove HTML
        $var = strip_tags($var);

        // less characters
        if (mb_strlen($var) <= $length) {
            return \SpoonFilter::htmlspecialchars($var);
        } else {
            // more characters
            // hellip is seen as 1 char, so remove it from length
            if ($useHellip) {
                --$length;
            }

            // truncate
            if ($closestWord) {
                $var = mb_substr($var, 0, mb_strrpos(mb_substr($var, 0, $length + 1), ' '), $charset);
            } else {
                $var = mb_substr($var, 0, $length, $charset);
            }

            // add hellip
            if ($useHellip) {
                $var .= '…';
            }

            // return
            return \SpoonFilter::htmlspecialchars($var, ENT_QUOTES);
        }
    }

    /**
     * Returns the count of the count of the array.
     *
     * @param array $data
     *
     * @return int
     */
    public static function count(array $data)
    {
        return count($data);
    }
}
