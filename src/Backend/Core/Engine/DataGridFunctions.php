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
 * A set of commonly used functions that will be applied on rows or columns
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class DataGridFunctions
{
    /**
     * Formats plain text as HTML, links will be detected, paragraphs will be inserted
     *
     * @param string $var The data to cleanup.
     * @return string
     */
    public static function cleanupPlainText($var)
    {
        $var = (string) $var;

        // detect links
        $var = \SpoonFilter::replaceURLsWithAnchors($var);

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
     * @param float $number   The number to format.
     * @param int   $decimals The number of decimals.
     * @return string
     */
    public static function formatFloat($number, $decimals = 2)
    {
        $number = (float) $number;
        $decimals = (int) $decimals;

        return number_format($number, $decimals, '.', ' ');
    }

    /**
     * Format a date according the users' settings
     *
     * @param int $timestamp The UNIX-timestamp to format as a human readable date.
     * @return string
     */
    public static function getDate($timestamp)
    {
        $timestamp = (int) $timestamp;

        // if invalid timestamp return an empty string
        if ($timestamp <= 0) {
            return '';
        }

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('date_format');

        // format the date according the user his settings
        return \SpoonDate::getDate($format, $timestamp, Language::getInterfaceLanguage());
    }

    /**
     * Format a date as a long representation according the users' settings
     *
     * @param int $timestamp The UNIX-timestamp to format as a human readable date.
     * @return string
     */
    public static function getLongDate($timestamp)
    {
        $timestamp = (int) $timestamp;

        // if invalid timestamp return an empty string
        if ($timestamp <= 0) {
            return '';
        }

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('datetime_format');

        // format the date according the user his settings
        return \SpoonDate::getDate($format, $timestamp, Language::getInterfaceLanguage());
    }

    /**
     * Format a time according the users' settings
     *
     * @param int $timestamp The UNIX-timestamp to format as a human readable time.
     * @return string
     */
    public static function getTime($timestamp)
    {
        $timestamp = (int) $timestamp;

        // if invalid timestamp return an empty string
        if ($timestamp <= 0) {
            return '';
        }

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('time_format');

        // format the date according the user his settings
        return \SpoonDate::getDate($format, $timestamp, Language::getInterfaceLanguage());
    }

    /**
     * Get time ago as a string for use in a datagrid
     *
     * @param int $timestamp The UNIX-timestamp to convert in a time-ago-string.
     * @return string
     */
    public static function getTimeAgo($timestamp)
    {
        $timestamp = (int) $timestamp;

        // get user setting for long dates
        $format = Authentication::getUser()->getSetting('datetime_format');

        // get the time ago as a string
        $timeAgo = \SpoonDate::getTimeAgo($timestamp, Language::getInterfaceLanguage(), $format);

        return '<abbr title="' . \SpoonDate::getDate(
            $format,
            $timestamp,
            Language::getInterfaceLanguage()
        ) . '">' . $timeAgo . '</abbr>';
    }

    /**
     * Get the HTML for a user to use in a datagrid
     *
     * @param int $id The Id of the user.
     * @return string
     */
    public static function getUser($id)
    {
        $id = (int) $id;

        // create user instance
        $user = new User($id);

        // get settings
        $avatar = $user->getSetting('avatar', 'no-avatar.gif');
        $nickname = $user->getSetting('nickname');
        $allowed = Authentication::isAllowedAction('Edit', 'Users');

        // build html
        $html = '<div class="dataGridAvatar">' . "\n";
        $html .= '  <div class="avatar av24">' . "\n";
        if ($allowed) {
            $html .= '     <a href="' .
                     BackendModel::createURLForAction(
                         'Edit',
                         'Users'
                     ) . '&amp;id=' . $id . '">' . "\n";
        }
        $html .= '          <img src="' . FRONTEND_FILES_URL . '/backend_users/avatars/32x32/' .
                 $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";
        if ($allowed) {
            $html .= '     </a>' . "\n";
        }
        $html .= '  </div>';
        $html .= '  <p><a href="' .
                 BackendModel::createURLForAction(
                     'edit',
                     'users'
                 ) . '&amp;id=' . $id . '">' . $nickname . '</a></p>' . "\n";
        $html .= '</div>';

        return $html;
    }

    /**
     * This will grey out certain rows from common columns. These columns are:
     *
     * 'visible', 'hidden', 'active', 'published'
     *
     * @param string $type The type of column. This is given since some columns can have different meanings than others.
     * @param string $value
     * @param array  $attributes
     * @return array
     */
    public static function greyOut($type, $value, array $attributes = array())
    {
        $grayedOutClass = 'grayedOut';
        $greyOut = false;

        switch ($type) {
            case 'visible':
            case 'active':
            case 'published':
                if ($value == 'N') {
                    $greyOut = true;
                }
                break;
            case 'hidden':
                if ($value == 'Y') {
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
     * @param string $path  The path to the image.
     * @param string $image The filename of the image.
     * @param string $title The title (will be used as alt).
     * @return string
     */
    public static function showImage($path, $image, $title = '')
    {
        $path = (string) $path;
        $image = (string) $image;
        $title = (string) $title;

        return '<img src="' . $path . '/' . $image . '" alt="' . $title . '" />';
    }

    /**
     * Truncate a string
     *
     * @param string $string    The string to truncate.
     * @param int    $length    The maximumlength for the string.
     * @param bool   $useHellip Should a hellip be appended?
     * @return string
     */
    public static function truncate($string, $length, $useHellip = true)
    {
        // remove special chars
        $string = htmlspecialchars_decode($string);

        // less characters
        if (mb_strlen($string) <= $length) {
            return \SpoonFilter::htmlspecialchars($string);
        } else {
            // more characters
            // hellip is seen as 1 char, so remove it from length
            if ($useHellip) {
                $length = $length - 1;
            }

            // get the amount of requested characters
            $string = mb_substr($string, 0, $length);

            // add hellip
            if ($useHellip) {
                $string .= '…';
            }

            return \SpoonFilter::htmlspecialchars($string);
        }
    }
}
