<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Block\Widget as FrontendBlockWidget;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Common\Core\Twig\Extensions\BaseTwigModifiers;
use \SpoonDate;

/**
 * Contains all Frontend-related custom modifiers
 */
class TemplateModifiers extends BaseTwigModifiers
{
    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdate }}
     *
     * @param int $var The UNIX-timestamp to format or \DateTime
     *
     * @return string
     */
    public static function formatDate($var)
    {
        // get setting
        $format = FrontendModel::get('fork.settings')->get('Core', 'date_format_short');

        if ($var instanceof \DateTime) {
            $var = $var->getTimestamp();
        }

        // format the date
        return SpoonDate::getDate($format, (int) $var, Locale::frontendLanguage());
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdatetime }}
     *
     * @param int $var The UNIX-timestamp to format or \DateTime
     *
     * @return string
     */
    public static function formatDateTime($var)
    {
        // get setting
        $format = FrontendModel::get('fork.settings')->get('Core', 'date_format_long');

        if ($var instanceof \DateTime) {
            $var = $var->getTimestamp();
        }

        // format the date
        return SpoonDate::getDate($format, (int) $var, Locale::frontendLanguage());
    }

    /**
     * Format a number as a float
     *    syntax: {{ $number|formatfloat($decimals) }}
     *
     * @param float $number   The number to format.
     * @param int   $decimals The number of decimals.
     *
     * @return string
     */
    public static function formatFloat($number, $decimals = 2)
    {
        return number_format((float) $number, (int) $decimals, '.', ' ');
    }

    /**
     * Format a number
     *    syntax: {{ $string|formatnumber($decimals) }}
     *
     * @param float $string The number to format.
     * @param int $decimals The number of decimals
     *
     * @return string
     */
    public static function formatNumber($string, $decimals = null)
    {
        // redefine
        $string = (float) $string;

        // get setting
        $format = FrontendModel::get('fork.settings')->get('Core', 'number_format');

        // get amount of decimals
        if ($decimals === null) {
            $decimals = (mb_strpos($string, '.') ? mb_strlen(mb_substr($string, mb_strpos($string, '.') + 1)) : 0);
        }

        // get separators
        $separators = explode('_', $format);
        $separatorSymbols = array('comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => '');
        $decimalSeparator = (isset($separators[0], $separatorSymbols[$separators[0]]) ? $separatorSymbols[$separators[0]] : null);
        $thousandsSeparator = (isset($separators[1], $separatorSymbols[$separators[1]]) ? $separatorSymbols[$separators[1]] : null);

        // format the number
        return number_format($string, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Format a UNIX-timestamp as a date
     * syntax: {{ $var|formatdate }}
     *
     * @param int $var The UNIX-timestamp to format or \DateTime
     *
     * @return string
     */
    public static function formatTime($var)
    {
        // get setting
        $format = FrontendModel::get('fork.settings')->get('Core', 'time_format');

        if ($var instanceof \DateTime) {
            $var = $var->getTimestamp();
        }

        // format the date
        return SpoonDate::getDate($format, (int) $var, Locale::frontendLanguage());
    }

    /**
     * Get the navigation html
     *    syntax: {{ getnavigation($type, $parentId, $depth, $excludeIds-splitted-by-dash, $template) }}
     *
     * @param string $type       The type of navigation, possible values are: page, footer.
     * @param int    $parentId   The parent wherefore the navigation should be build.
     * @param int    $depth      The maximum depth that has to be build.
     * @param string $excludeIds Which pageIds should be excluded (split them by -).
     * @param string $template        The template that will be used.
     *
     * @return string
     */
    public static function getNavigation(
        $type = 'page',
        $parentId = 0,
        $depth = null,
        $excludeIds = null,
        $template = '/Core/Layout/Templates/Navigation.html.twig'
    ) {
        // build excludeIds
        if ($excludeIds !== null) {
            $excludeIds = (array) explode('-', $excludeIds);
        }

        // get HTML
        try {
            $return = (string) Navigation::getNavigationHTML($type, $parentId, $depth, $excludeIds, $template);
        } catch (Exception $e) {
            // if something goes wrong just return as fallback
            return '';
        }

        // return the var
        if ($return != '') {
            return $return;
        }

        // fallback
        return '';
    }

    /**
     * Formats a timestamp as a string that indicates the time ago
     *    syntax: {{ $string|timeago }}.
     *
     * @param string $string A UNIX-timestamp that will be formatted as a time-ago-string.
     *
     * @return string
     */
    public static function timeAgo($string = null)
    {
        $string = (int) $string;

        // invalid timestamp
        if ($string == 0) {
            return '';
        }

        // return
        return '<abbr title="'.\SpoonDate::getDate(
            FrontendModel::get('fork.settings')->get('Core', 'date_format_long').', '.FrontendModel::get('fork.settings')->get(
                'Core',
                'time_format'
            ),
            $string,
            Locale::frontendLanguage()
        ).'">'.\SpoonDate::getTimeAgo($string, Locale::frontendLanguage()).'</abbr>';
    }

    /**
     * Get a given field for a page-record
     *    syntax: {{ $pageId|getpageinfo($field) }}
     *
     * @param int    $pageId   The id of the page to build the URL for.
     * @param string $field    The field to get.
     *
     * @return string
     */
    public static function getPageInfo($pageId, $field = 'title')
    {
        // redefine
        $field = (string) $field;

        // get page
        $page = Navigation::getPageInfo((int) $pageId);

        // validate
        if (empty($page)) {
            return '';
        }
        if (!isset($page[$field])) {
            return '';
        }

        // return page info
        return $page[$field];
    }

    /**
     * Fetch the path for an include (theme file if available, core file otherwise)
     *    syntax: {{ getpath($file) }}
     *
     * @param string $file The base path.
     *
     * @return string
     */
    public static function getPath($file)
    {
        return Theme::getPath($file);
    }

    /**
     * Get the subnavigation html
     *   syntax: {{ getsubnavigation($type, $parentId, $startdepth, $enddepth, $excludeIds-splitted-by-dash, $template) }}
     *
     *   NOTE: When supplying more than 1 ID to exclude, the single quotes around the dash-separated list are mandatory.
     *
     * @param string $type       The type of navigation, possible values are: page, footer.
     * @param int    $pageId     The parent wherefore the navigation should be build.
     * @param int    $startDepth The depth to start from.
     * @param int    $endDepth   The maximum depth that has to be build.
     * @param string $excludeIds Which pageIds should be excluded (split them by -).
     * @param string $template        The template that will be used.
     *
     * @return string
     */
    public static function getSubNavigation(
        $type = 'page',
        $pageId = 0,
        $startDepth = 1,
        $endDepth = null,
        $excludeIds = null,
        $template = '/Core/Layout/Templates/Navigation.html.twig'
    ) {
        // build excludeIds
        if ($excludeIds !== null) {
            $excludeIds = (array) explode('-', $excludeIds);
        }

        // get info about the given page
        $pageInfo = Navigation::getPageInfo($pageId);

        // validate page info
        if ($pageInfo === false) {
            return '';
        }

        // split URL into chunks
        $chunks = (array) explode('/', $pageInfo['full_url']);

        // remove language chunk
        $hasMultiLanguages = FrontendModel::getContainer()->getParameter('site.multilanguage');
        $chunks = ($hasMultiLanguages) ? (array) array_slice($chunks, 2) : (array) array_slice($chunks, 1);
        if (count($chunks) == 0) {
            $chunks[0] = '';
        }

        // init var
        $parentURL = '';

        // build url
        for ($i = 0; $i < $startDepth - 1; ++$i) {
            $parentURL .= $chunks[$i] . '/';
        }

        // get parent ID
        $parentID = Navigation::getPageId($parentURL);

        try {
            // get HTML
            $return = (string) Navigation::getNavigationHTML(
                $type,
                $parentID,
                $endDepth,
                $excludeIds,
                (string) $template
            );
        } catch (Exception $e) {
            return '';
        }

        // return the var
        if ($return != '') {
            return $return;
        }

        // fallback
        return;
    }

    /**
     * Get the URL for a given pageId & language
     *    syntax: {{ geturl($pageId, $language) }}
     *
     * @param int    $pageId   The id of the page to build the URL for.
     * @param string $language The language to use, if not provided we will use the loaded language.
     *
     * @return string
     */
    public static function getURL($pageId, $language = null)
    {
        $language = ($language !== null) ? (string) $language : null;

        return Navigation::getURL((int) $pageId, $language);
    }

    /**
     * Get the URL for a give module & action combination
     *    syntax: {{ geturlforblock($module, $action, $language, $data) }}
     *
     * @param string $module   The module wherefore the URL should be build.
     * @param string $action   A specific action wherefore the URL should be build, otherwise the default will be used.
     * @param string $language The language to use, if not provided we will use the loaded language.
     * @param array $data      An array with keys and values that partially or fully match the data of the block.
     *                         If it matches multiple versions of that block it will just return the first match.
     *
     * @return string
     */
    public static function getURLForBlock($module, $action = null, $language = null, array $data = null)
    {
        $action = ($action !== null) ? (string) $action : null;
        $language = ($language !== null) ? (string) $language : null;

        return Navigation::getURLForBlock((string) $module, $action, $language, $data);
    }

    /**
     * Fetch an URL based on an extraId
     *    syntax: {{ geturlforextraid($extraId, $language) }}
     *
     * @param int    $extraId  The id of the extra.
     * @param string $language The language to use, if not provided we will use the loaded language.
     *
     * @return string
     */
    public static function getURLForExtraId($extraId, $language = null)
    {
        $language = ($language !== null) ? (string) $language : null;

        return Navigation::getURLForExtraId((int) $extraId, $language);
    }

    /**
     * Parse a widget straight from the template, rather than adding it through pages.
     *    syntax: {{ parsewidget($module, $action, $id) }}
     *
     * @internal if your widget outputs random data you should cache it inside the widget
     * Fork checks the output and if the output of the widget is random it will loop until the random data
     * is the same as in the previous iteration
     *
     * @param string $module The module whose module we want to execute.
     * @param string $action The action to execute.
     * @param string $id     The widget id (saved in data-column).
     *
     * @return null|string
     * @throws Exception
     */
    public static function parseWidget($module, $action, $id = null)
    {
        // create new widget instance and return parsed content
        $extra = FrontendBlockWidget::getForId(
            FrontendModel::get('kernel'),
            $module,
            $action,
            $id
        );

        // set parseWidget because we will need it to skip setting headers in the display
        FrontendModel::getContainer()->set('parseWidget', true);

        try {
            $extra->execute();
            $content = $extra->getContent();
            FrontendModel::getContainer()->set('parseWidget', null);

            return $content;
        } catch (RedirectException $redirectException) {
            throw new \Twig_Error('redirect fix from template modifier', null, null, $redirectException);
        } catch (Exception $e) {
            // if we are debugging, we want to see the exception
            if (FrontendModel::getContainer()->getParameter('kernel.debug')) {
                throw $e;
            }

            return;
        }
    }

    /**
     * Output a profile setting
     *    syntax: {{ profilesetting($string, $name) }}
     *
     * @param string $string  The variable
     * @param string $name The name of the setting
     *
     * @return string
     */
    public static function profileSetting($string, $name)
    {
        $profile = FrontendProfilesModel::get((int) $string);
        if ($profile === false) {
            return '';
        }

        // convert into array
        $profile = $profile->toArray();

        // @remark I know this is dirty, but I couldn't find a better way.
        if (in_array($name, array('display_name', 'registered_on', 'full_url')) && isset($profile[$name])) {
            return $profile[$name];
        } elseif (isset($profile['settings'][$name])) {
            return $profile['settings'][$name];
        } else {
            return '';
        }
    }

    /**
     * Get the value for a user-setting
     *    syntax {{ usersetting($setting, $userId) }}
     *
     * @param string|null $string  The string passed from the template.
     * @param string $setting The name of the setting you want.
     * @param int    $userId  The userId, if not set by $string.
     *
     * @return string
     * @throws Exception
     */
    public static function userSetting($string, $setting, $userId = null)
    {
        $userId = ($string !== null) ? (int) $string : (int) $userId;
        $setting = (string) $setting;

        // validate
        if ($userId === 0) {
            throw new Exception('Invalid user id');
        }

        // get user
        $user = User::getBackendUser($userId);

        // return
        return (string) $user->getSetting($setting);
    }

    /**
     * Formats plain text as HTML, links will be detected, paragraphs will be inserted
     *    syntax: {{ $string|cleanupPlainText }}.
     *
     * @param string $string The text to cleanup.
     *
     * @return string
     */
    public static function cleanupPlainText($string)
    {
        // redefine
        $string = (string) $string;

        // detect links
        $string = \SpoonFilter::replaceURLsWithAnchors(
            $string,
            FrontendModel::get('fork.settings')->get('Core', 'seo_nofollow_in_comments', false)
        );

        // replace newlines
        $string = str_replace("\r", '', $string);
        $string = preg_replace('/(?<!.)(\r\n|\r|\n){3,}$/m', '', $string);

        // replace br's into p's
        $string = '<p>'.str_replace("\n", '</p><p>', $string).'</p>';

        // cleanup
        $string = str_replace("\n", '', $string);
        $string = str_replace('<p></p>', '', $string);

        // return
        return $string;
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
        return \SpoonFilter::ucfirst(Language::lbl(\SpoonFilter::toCamelCase($value, '_', false)));
    }
}
