<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;

/**
 * This is our class with custom modifiers.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class TemplateModifiers
{
    /**
     * Dumps the data
     *  syntax: {$var|dump}
     *
     * @param string $var The variable to dump.
     * @return string
     */
    public static function dump($var)
    {
        \Spoon::dump($var, false);
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {$var|formatdate}
     *
     * @param int $var The UNIX-timestamp to format.
     * @return string
     */
    public static function formatDate($var)
    {
        // get setting
        $format = Authentication::getUser()->getSetting('date_format');

        // format the date
        return \SpoonDate::getDate($format, (int) $var, Language::getInterfaceLanguage());
    }

    /**
     * Format a UNIX-timestamp as a datetime
     * syntax: {$var|formatdatetime}
     *
     * @param int $var The UNIX-timestamp to format.
     * @return string
     */
    public static function formatDateTime($var)
    {
        // get setting
        $format = Authentication::getUser()->getSetting('datetime_format');

        // format the date
        return \SpoonDate::getDate($format, (int) $var, Language::getInterfaceLanguage());
    }

    /**
     * Format a number as a float
     * syntax: {$var|formatfloat}
     *
     * @param float $number   The number to format.
     * @param int   $decimals The number of decimals.
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
     * syntax: {$var|formatnumber}
     *
     * @param float $var The number to format.
     * @return string
     */
    public static function formatNumber($var)
    {
        $var = (float) $var;

        // get setting
        $format = Authentication::getUser()->getSetting('number_format', 'dot_nothing');

        // get amount of decimals
        $decimals = (strpos($var, '.') ? strlen(substr($var, strpos($var, '.') + 1)) : 0);

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
        return number_format($var, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {$var|formatdate}
     *
     * @param int $var The UNIX-timestamp to format.
     * @return string
     */
    public static function formatTime($var)
    {
        // get setting
        $format = Authentication::getUser()->getSetting('time_format');

        // format the date
        return \SpoonDate::getDate($format, (int) $var, Language::getInterfaceLanguage());
    }

    /**
     * Convert a var into main-navigation-html
     *  syntax: {$var|getmainnavigation}
     *
     * @param string $var A placeholder var, will be replaced with the generated HTML.
     * @return string
     */
    public static function getMainNavigation($var = null)
    {
        return BackendModel::getContainer()->get('navigation')->getNavigation(1, 1);
    }

    /**
     * Convert a var into navigation-html
     * syntax: {$var|getnavigation:startdepth[:maximumdepth]}
     *
     * @param string $var        A placeholder var, will be replaced with the generated HTML.
     * @param int    $startDepth The start depth of the navigation to get.
     * @param int    $endDepth   The ending depth of the navigation to get.
     * @return string
     */
    public static function getNavigation($var = null, $startDepth = null, $endDepth = null)
    {
        $startDepth = ($startDepth !== null) ? (int) $startDepth : 2;
        $endDepth = ($endDepth !== null) ? (int) $endDepth : null;

        // return navigation
        return BackendModel::getContainer()->get('navigation')->getNavigation($startDepth, $endDepth);
    }

    /**
     * Convert a var into a URL
     * syntax: {$var|geturl:<action>[:<module>]}
     *
     * @param string $var    A placeholder variable, it will be replaced with the URL.
     * @param string $action The action to build the URL for.
     * @param string $module The module to build the URL for.
     * @param string $suffix A string to append.
     * @return string
     */
    public static function getURL($var = null, $action = null, $module = null, $suffix = null)
    {
        $action = ($action !== null) ? (string) $action : null;
        $module = ($module !== null) ? (string) $module : null;

        return BackendModel::createURLForAction($action, $module, Language::getWorkingLanguage()) . $suffix;
    }

    /**
     * Get a random var between a min and max
     * syntax: {$var|rand:min:max}
     *
     * @param string $var The string passed from the template.
     * @param int    $min The minimum number.
     * @param int    $max The maximum number.
     * @return int
     */
    public static function random($var = null, $min, $max)
    {
        return rand((int) $min, (int) $max);
    }

    /**
     * Convert a multiline string into a string without newlines so it can be handles by JS
     * syntax: {$var|stripnewlines}
     *
     * @param string $var The variable that should be processed.
     * @return string
     */
    public static function stripNewlines($var)
    {
        return str_replace(array("\n", "\r"), '', $var);
    }

    /**
     * Convert this string into a well formed label.
     *  syntax: {$var|tolabel}
     *
     * @param string $value The value to convert to a label.
     * @return string
     */
    public static function toLabel($value)
    {
        return \SpoonFilter::ucfirst(Language::lbl(\SpoonFilter::toCamelCase($value, '_', false)));
    }

    /**
     * Truncate a string
     *  syntax: {$var|truncate:max-length[:append-hellip]}
     *
     * @param string $var       A placeholder var, will be replaced with the generated HTML.
     * @param int    $length    The maximum length of the truncated string.
     * @param bool   $useHellip Should a hellip be appended if the length exceeds the requested length?
     * @return string
     */
    public static function truncate($var, $length, $useHellip = true)
    {
        // remove special chars
        $var = htmlspecialchars_decode($var, ENT_QUOTES);

        // remove HTML
        $var = strip_tags($var);

        // less characters
        if (mb_strlen($var) <= $length) {
            return \SpoonFilter::htmlspecialchars($var);
        } else {
            // hellip is seen as 1 char, so remove it from length
            if ($useHellip) {
                $length = $length - 1;
            }

            // get the amount of requested characters
            $var = mb_substr($var, 0, $length);

            // add hellip
            if ($useHellip) {
                $var .= '…';
            }

            return \SpoonFilter::htmlspecialchars($var, ENT_QUOTES);
        }
    }
}
