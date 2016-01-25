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

/**
 * This is our class with custom modifiers.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class TemplateModifiers extends BaseTwigModifiers
{
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
     * syntax: {{ $var|formatdate }}
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
     *  syntax: {{ getmainnavigation($class) }}
     *
     * @param string $class Class attribute of ul list
     *
     * @return string
     */
    public static function getMainNavigation($class = null)
    {
        return BackendModel::getContainer()->get('navigation')->getNavigation(1, 1, $class);
    }

    /**
     * Convert a var into navigation-html
     * syntax: {{ getnavigation(startdepth, maximumdepth) }}
     *
     * @param int    $startDepth The start depth of the navigation to get.
     * @param int    $endDepth   The ending depth of the navigation to get.
     * @param string $class Class attribute of ul list
     * @return string
     */
    public static function getNavigation($startDepth = null, $endDepth = null, $class = null)
    {
        $startDepth = ($startDepth !== null) ? (int) $startDepth : 2;
        $endDepth = ($endDepth !== null) ? (int) $endDepth : null;

        // return navigation
        return BackendModel::getContainer()->get('navigation')->getNavigation($startDepth, $endDepth, $class);
    }

    /**
     * Convert a var into a URL
     * syntax: {{ geturl:<action>[:<module>] }}
     *
     * @param string $action The action to build the URL for.
     * @param string $module The module to build the URL for.
     * @param string $suffix A string to append.
     * @param string $language A language code
     * @return string
     */
    public static function getURL($action = null, $module = null, $suffix = null, $language = null)
    {
        if (!in_array($language, Language::getActiveLanguages())) {
            $language = Language::getWorkingLanguage();
        }

        $action = ($action !== null) ? (string) $action : null;
        $module = ($module !== null) ? (string) $module : null;

        return BackendModel::createURLForAction($action, $module, $language) . $suffix;
    }

    /**
     * Translate a string.
     *
     * @param string $string The string that you want to apply this method on.
     *
     * @throw exception thrown when no 'dot' is found in string
     *
     * @return string The string, to translate.
     */
    public static function trans($string)
    {
        if (strpos($string, '.') === false) {
            throw new Exception('translation needs a dot character in : '.$string);
        }
        list($action, $string) = explode('.', $string);

        return Language::$action($string);
    }

    /**
     * Convert this string into a well formed label.
     *  syntax: {$var|tolabel}.
     *
     * @param string $value The value to convert to a label.
     *
     * @return string
     */
    public static function toLabel($value)
    {
        return \SpoonFilter::ucfirst(Language::lbl(\SpoonFilter::toCamelCase($value, '_', false)));
    }
}
