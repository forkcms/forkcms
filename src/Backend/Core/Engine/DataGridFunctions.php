<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BackendLanguage;
use SpoonDate;
use SpoonFilter;

/**
 * A set of commonly used functions that will be applied on rows or columns
 */
class DataGridFunctions
{
    protected static $dataGridUsers = [];

    /**
     * Formats plain text as HTML, links will be detected, paragraphs will be inserted
     *
     * @param string $var The data to cleanup.
     *
     * @return string
     */
    public static function cleanupPlainText(string $var): string
    {
        // detect links
        $var = SpoonFilter::replaceURLsWithAnchors($var);

        // replace newlines
        $var = str_replace("\r", '', $var);
        $var = preg_replace('/(?<!.)(\r\n|\r|\n){3,}$/m', '', $var);

        // replace br's into p's
        $var = '<p>' . str_replace("\n", '</p><p>', $var) . '</p>';

        // cleanup
        $var = str_replace("\n", '', $var);
        $var = str_replace('<p></p>', '', $var);

        return $var;
    }

    /**
     * Format a number as a float
     *
     * @param float $number The number to format.
     * @param int $decimals The number of decimals.
     *
     * @return string
     */
    public static function formatFloat(float $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, '.', ' ');
    }

    /**
     * Format a date according the users' settings
     *
     * @param int $timestamp The UNIX-timestamp to format as a human readable date.
     *
     * @return string
     */
    public static function getDate(int $timestamp): string
    {
        // if invalid timestamp return an empty string
        if ($timestamp <= 0) {
            return '';
        }

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('date_format');

        // format the date according the user his settings
        return SpoonDate::getDate($format, $timestamp, BackendLanguage::getInterfaceLanguage());
    }

    /**
     * Format a date as a long representation according the users' settings
     *
     * @param int $timestamp The UNIX-timestamp to format as a human readable date.
     *
     * @return string
     */
    public static function getLongDate(int $timestamp): string
    {
        // if invalid timestamp return an empty string
        if ($timestamp <= 0) {
            return '';
        }

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('datetime_format');

        // format the date according the user his settings
        return SpoonDate::getDate($format, $timestamp, BackendLanguage::getInterfaceLanguage());
    }

    /**
     * Format a time according the users' settings
     *
     * @param int $timestamp The UNIX-timestamp to format as a human readable time.
     *
     * @return string
     */
    public static function getTime(int $timestamp): string
    {
        // if invalid timestamp return an empty string
        if ($timestamp <= 0) {
            return '';
        }

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('time_format');

        // format the date according the user his settings
        return SpoonDate::getDate($format, $timestamp, BackendLanguage::getInterfaceLanguage());
    }

    /**
     * Get time ago as a string for use in a datagrid
     *
     * @param int $timestamp The UNIX-timestamp to convert in a time-ago-string.
     *
     * @return string
     */
    public static function getTimeAgo(int $timestamp): string
    {
        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('datetime_format');

        // get the time ago as a string
        $timeAgo = SpoonDate::getTimeAgo($timestamp, BackendLanguage::getInterfaceLanguage(), $format);

        return '<time data-toggle="tooltip" datetime="'
               . SpoonDate::getDate('Y-m-d H:i:s', $timestamp)
               . '" title="' . SpoonDate::getDate($format, $timestamp, BackendLanguage::getInterfaceLanguage())
               . '">' . $timeAgo . '</time>';
    }

    /**
     * Get the HTML for a user to use in a datagrid
     *
     * @param int $id The Id of the user.
     *
     * @return string
     */
    public static function getUser(int $id): string
    {
        // nothing in cache
        if (!isset(self::$dataGridUsers[$id])) {
            // create user instance
            $user = new User($id);

            // get settings
            $avatar = $user->getSetting('avatar', 'no-avatar.gif');
            $nickname = $user->getSetting('nickname');
            $allowed = Authentication::isAllowedAction('Edit', 'Users');

            // build html
            $html = '<div class="fork-data-grid-avatar">' . "\n";
            if ($allowed) {
                $html .= '     <a href="' .
                    BackendModel::createUrlForAction(
                        'Edit',
                        'Users'
                    ) . '&amp;id=' . $id . '">' . "\n";
            }
            $html .= '          <img class="img-circle" src="' . FRONTEND_FILES_URL . '/Users/avatars/32x32/' .
                $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";

            $html .= '<span>' . $nickname . '</span>';
            if ($allowed) {
                $html .= '</a>' . "\n";
            }
            $html .= '  </div>';

            self::$dataGridUsers[$id] = $html;
        }

        return self::$dataGridUsers[$id];
    }

    /**
     * This will grey out certain rows from common columns. These columns are:
     *
     * 'visible', 'hidden', 'active', 'published'
     *
     * @param string $type The type of column. This is given since some columns can have different meanings than others.
     * @param string|bool $value
     * @param array $attributes
     *
     * @return array
     */
    public static function greyOut(string $type, $value, array $attributes = []): array
    {
        $grayedOutClass = 'fork-data-grid-grayed-out grayedOut';
        $greyOut = false;

        switch ($type) {
            case 'visible':
            case 'active':
            case 'published':
                if (!$value) {
                    $greyOut = true;
                }
                break;
            case 'status':
                if ($value === 'hidden') {
                    $greyOut = true;
                }
                break;
            case 'hidden':
                if ($value) {
                    $greyOut = true;
                }
                break;
        }

        // add the grayedOut class to any existing attributes
        if ($greyOut) {
            if (array_key_exists('class', $attributes)) {
                $attributes['class'] .= ' ' . $grayedOutClass;
            } else {
                $attributes['class'] = $grayedOutClass;
            }
        }

        return $attributes;
    }

    /**
     * Returns an image tag
     *
     * @param string $path The path to the image.
     * @param string $image The filename of the image.
     * @param string $title The title (will be used as alt).
     * @param string $url The url
     * @param int $width The width for the <img element
     * @param int $height The height for the <img element
     * @param string $filter The LiipImagineBundle filter
     *
     * @return string
     */
    public static function showImage(
        string $path,
        string $image,
        string $title = '',
        string $url = null,
        int $width = null,
        int $height = null,
        string $filter = null
    ): string {
        if ($width === 0 || $height === 0) {
            throw new \Exception('An image must not have a width or height equal to 0, because the image will not be visible.');
        }

        $imagePath = $path . '/' . $image;

        if ($filter !== null) {
            $imagePath = BackendModel::get('liip_imagine.cache.manager')->getBrowserPath($imagePath, $filter);
        }

        $html = '<img src="' . $imagePath . '" alt="' . $title . '"';

        if ($width !== null) {
            $html .= ' width="' . $width . '"';
        }

        if ($height !== null) {
            $html .= ' height="' . $height . '"';
        }

        $html .= ' />';

        if ($url !== null) {
            $html = '<a href="' . $url . '" title="' . $title . '">' . $html . '</a>';
        }

        return $html;
    }

    /**
     * Truncate a string
     *
     * @param string $string The string to truncate.
     * @param int $length The maximum length for the string.
     * @param bool $useHellip Should a hellip be appended?
     *
     * @return string
     */
    public static function truncate(string $string, int $length, bool $useHellip = true): string
    {
        // remove special chars
        $string = htmlspecialchars_decode($string);

        // less characters
        if (mb_strlen($string) <= $length) {
            return SpoonFilter::htmlspecialchars($string);
        }

        // more characters
        // hellip is seen as 1 char, so remove it from length
        if ($useHellip) {
            --$length;
        }

        // get the amount of requested characters
        $string = mb_substr($string, 0, $length);

        // add hellip
        if ($useHellip) {
            $string .= '…';
        }

        return SpoonFilter::htmlspecialchars($string);
    }

    /**
     * This is an alias for the template modifier since it can also be used here and people didn't find it.
     *
     * @param string|bool $status
     * @param bool $reverse show the opposite of the status
     *
     * @return string
     */
    public static function showBool($status, bool $reverse = false): string
    {
        return TemplateModifiers::showBool($status, $reverse);
    }
}
