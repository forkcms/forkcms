<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Common\Core\Twig\Extensions\BaseTwigModifiers;
use Backend\Core\Language\Language as BackendLanguage;
use SpoonDate;

/**
 * This is our class with custom modifiers.
 */
class TemplateModifiers extends BaseTwigModifiers
{
    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdate }}
     *
     * @param int|\DateTime $var The UNIX-timestamp to format.
     *
     * @return string
     */
    public static function formatDate($var): string
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
     * @param int|\DateTime $var The UNIX-timestamp to format.
     *
     * @return string
     */
    public static function formatDateTime($var): string
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
     * @param float $number The number to format.
     * @param int $decimals The number of decimals.
     *
     * @return string
     */
    public static function formatFloat(float $number, int $decimals = 2): string
    {
        // get setting
        $format = Authentication::getUser()->getSetting('number_format', 'dot_nothing');

        // get separators
        $separators = explode('_', $format);
        $separatorSymbols = ['comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => ''];
        $decimalSeparator = isset($separators[0], $separatorSymbols[$separators[0]])
            ? $separatorSymbols[$separators[0]] : null;
        $thousandsSeparator = isset($separators[1], $separatorSymbols[$separators[1]])
            ? $separatorSymbols[$separators[1]] : null;

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
    public static function formatNumber(float $number, int $decimals = null): string
    {
        // get setting
        $format = Authentication::getUser()->getSetting('number_format', 'dot_nothing');

        // get amount of decimals
        if ($decimals === null) {
            $decimals = (mb_strpos($number, '.') ? mb_strlen(mb_substr($number, mb_strpos($number, '.') + 1)) : 0);
        }

        // get separators
        $separators = explode('_', $format);
        $separatorSymbols = ['comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => ''];
        $decimalSeparator = isset($separators[0], $separatorSymbols[$separators[0]])
            ? $separatorSymbols[$separators[0]] : null;
        $thousandsSeparator = isset($separators[1], $separatorSymbols[$separators[1]])
            ? $separatorSymbols[$separators[1]] : null;

        // format the number
        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdate }}
     *
     * @param int|\DateTime $var The UNIX-timestamp to format.
     *
     * @return string
     */
    public static function formatTime($var): string
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
    public static function getUrl(
        string $action = null,
        string $module = null,
        string $suffix = null,
        string $language = null
    ): string {
        if (!array_key_exists($language, BackendLanguage::getWorkingLanguages())) {
            $language = BackendLanguage::getWorkingLanguage();
        }

        return BackendModel::createUrlForAction($action, $module, $language) . $suffix;
    }

    /**
     * Convert this string into a well formed label.
     *  syntax: {{ var|tolabel }}.
     *
     * @param string $value The value to convert to a label.
     *
     * @return string
     */
    public static function toLabel($value): string
    {
        return \SpoonFilter::ucfirst(BackendLanguage::lbl(\SpoonFilter::toCamelCase($value, '_', false)));
    }

    /**
     * Returns the count of the count of the array.
     *
     * @param array $data
     *
     * @return int
     */
    public static function count(array $data): int
    {
        return count($data);
    }
}
