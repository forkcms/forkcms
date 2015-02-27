<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Cookie as CommonCookie;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This class will store the language-dependant content for the Backend, it will also store the
 * current language for the user.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Language
{
    /**
     * The labels
     *
     * @var    array
     */
    protected static $err = array();
    protected static $lbl = array();
    protected static $msg = array();

    /**
     * The active languages
     *
     * @var    array
     */
    protected static $activeLanguages;

    /**
     * The current interface-language
     *
     * @var    string
     */
    protected static $currentInterfaceLanguage;

    /**
     * The current language that the user is working with
     *
     * @var    string
     */
    protected static $currentWorkingLanguage;

    /**
     * Get the active languages
     *
     * @return array
     */
    public static function getActiveLanguages()
    {
        // validate the cache
        if (empty(static::$activeLanguages)) {
            // grab from settings
            $activeLanguages = (array) BackendModel::getModuleSetting('Core', 'active_languages');

            // store in cache
            static::$activeLanguages = $activeLanguages;
        }

        // return from cache
        return static::$activeLanguages;
    }

    /**
     * Get all active languages in a format usable by SpoonForm's addRadioButton
     *
     * @return array
     */
    public static function getCheckboxValues()
    {
        $languages = static::getActiveLanguages();
        $results = array();

        // stop here if no languages are present
        if (empty($languages)) {
            return array();
        }

        // addRadioButton requires an array with keys 'value' and 'label'
        foreach ($languages as $abbreviation) {
            $results[] = array(
                'value' => $abbreviation,
                'label' => static::lbl(strtoupper($abbreviation))
            );
        }

        return $results;
    }

    /**
     * Get an error from the language-file
     *
     * @param string $key    The key to get.
     * @param string $module The module wherein we should search.
     * @return string
     */
    public static function getError($key, $module = null)
    {
        // do we know the module
        if ($module === null) {
            if (BackendModel::getContainer()->has('url')) {
                $module = BackendModel::getContainer()->get('url')->getModule();
            } elseif (isset($_GET['module']) && $_GET['module'] != '') {
                $module = (string) $_GET['module'];
            } else {
                $module = 'Core';
            }
        }

        $key = \SpoonFilter::toCamelCase((string) $key);
        $module = (string) $module;

        // check if the error exists
        if (isset(static::$err[$module][$key])) {
            return static::$err[$module][$key];
        }

        // check if the error exists in the Core
        if (isset(static::$err['Core'][$key])) {
            return static::$err['Core'][$key];
        }

        // otherwise return the key in label-format
        return '{$err' . \SpoonFilter::toCamelCase($module) . $key . '}';
    }

    /**
     * Get all the errors from the language-file
     *
     * @return array
     */
    public static function getErrors()
    {
        return (array) static::$err;
    }

    /**
     * Get the current interface language
     *
     * @return string
     */
    public static function getInterfaceLanguage()
    {
        return static::$currentInterfaceLanguage;
    }

    /**
     * Get all the possible interface languages
     *
     * @return array
     */
    public static function getInterfaceLanguages()
    {
        $languages = array();

        // grab the languages from the settings & loop language to reset the label
        foreach ((array) BackendModel::getModuleSetting('Core', 'interface_languages', array('en')) as $key) {
            // fetch language's translation
            $languages[$key] = static::getLabel(mb_strtoupper($key), 'Core');
        }

        // sort alphabetically
        asort($languages);

        // return languages
        return $languages;
    }

    /**
     * Get a label from the language-file
     *
     * @param string $key    The key to get.
     * @param string $module The module wherein we should search.
     * @return string
     */
    public static function getLabel($key, $module = null)
    {
        // do we know the module
        if ($module === null) {
            if (BackendModel::getContainer()->has('url')) {
                $module = BackendModel::getContainer()->get('url')->getModule();
            } elseif (isset($_GET['module']) && $_GET['module'] != '') {
                $module = (string) $_GET['module'];
            } else {
                $module = 'Core';
            }
        }

        $key = \SpoonFilter::toCamelCase((string) $key);
        $module = (string) $module;

        // check if the label exists
        if (isset(static::$lbl[$module][$key])) {
            return static::$lbl[$module][$key];
        }

        // check if the label exists in the Core
        if (isset(static::$lbl['Core'][$key])) {
            return static::$lbl['Core'][$key];
        }

        // otherwise return the key in label-format
        return '{$lbl' . \SpoonFilter::toCamelCase($module) . $key . '}';
    }

    /**
     * Get all the labels from the language-file
     *
     * @return array
     */
    public static function getLabels()
    {
        return static::$lbl;
    }

    /**
     * Get a message from the language-file
     *
     * @param string $key    The key to get.
     * @param string $module The module wherein we should search.
     * @return string
     */
    public static function getMessage($key, $module = null)
    {
        if ($module === null) {
            if (BackendModel::getContainer()->has('url')) {
                $module = BackendModel::getContainer()->get('url')->getModule();
            } elseif (isset($_GET['module']) && $_GET['module'] != '') {
                $module = (string) $_GET['module'];
            } else {
                $module = 'Core';
            }
        }

        $key = \SpoonFilter::toCamelCase((string) $key);
        $module = (string) $module;

        // check if the message exists
        if (isset(static::$msg[$module][$key])) {
            return static::$msg[$module][$key];
        }

        // check if the message exists in the Core
        if (isset(static::$msg['Core'][$key])) {
            return static::$msg['Core'][$key];
        }

        // otherwise return the key in label-format
        return '{$msg' . \SpoonFilter::toCamelCase($module) . $key . '}';
    }

    /**
     * Get the messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return static::$msg;
    }

    /**
     * Get the current working language
     *
     * @return string
     */
    public static function getWorkingLanguage()
    {
        return static::$currentWorkingLanguage;
    }

    /**
     * Get all possible working languages
     *
     * @return array
     */
    public static function getWorkingLanguages()
    {
        $languages = array();

        // grab the languages from the settings & loop language to reset the label
        foreach ((array) BackendModel::getModuleSetting('Core', 'languages', array('en')) as $key) {
            // fetch the language's translation
            $languages[$key] = static::getLabel(mb_strtoupper($key), 'Core');
        }

        // sort alphabetically
        asort($languages);

        return $languages;
    }

    /**
     * Set locale
     * It will require the correct file and init the needed vars
     *
     * @param string $language The language to load.
     */
    public static function setLocale($language)
    {
        $language = (string) $language;

        // validate file, generate it if needed
        if (!is_file(BACKEND_CACHE_PATH . '/Locale/en.json')) {
            BackendLocaleModel::buildCache('en', APPLICATION);
        }
        if (!is_file(BACKEND_CACHE_PATH . '/Locale/' . $language . '.json')) {
            BackendLocaleModel::buildCache($language, APPLICATION);
        }

        // store
        static::$currentInterfaceLanguage = $language;

        // attempt to set a cookie
        try {
            CommonCookie::set('interface_language', $language);
        } catch (\SpoonCookieException $e) {
            // settings cookies isn't allowed, because this isn't a real problem we ignore the exception
        }

        // set English translations, they'll be the fallback
        $translations = json_decode(
            file_get_contents(BACKEND_CACHE_PATH . '/Locale/en.json'),
            true
        );
        static::$err = (array) $translations['err'];
        static::$lbl = (array) $translations['lbl'];
        static::$msg = (array) $translations['msg'];

        // overwrite with the requested language's translations
        $translations = json_decode(
            file_get_contents(BACKEND_CACHE_PATH . '/Locale/' . $language . '.json'),
            true
        );
        $err = (array) $translations['err'];
        $lbl = (array) $translations['lbl'];
        $msg = (array) $translations['msg'];
        foreach ($err as $module => $translations) {
            if (!isset(static::$err[$module])) {
                static::$err[$module] = array();
            }
            static::$err[$module] = array_merge(static::$err[$module], $translations);
        }
        foreach ($lbl as $module => $translations) {
            if (!isset(static::$lbl[$module])) {
                static::$lbl[$module] = array();
            }
            static::$lbl[$module] = array_merge(static::$lbl[$module], $translations);
        }
        foreach ($msg as $module => $translations) {
            if (!isset(static::$msg[$module])) {
                static::$msg[$module] = array();
            }
            static::$msg[$module] = array_merge(static::$msg[$module], $translations);
        }
    }

    /**
     * Set the current working language
     *
     * @param string $language The language to use, if not provided we will use the working language.
     */
    public static function setWorkingLanguage($language)
    {
        static::$currentWorkingLanguage = (string) $language;
    }

    /**
     * Get an error from the language-file
     *
     * @param string $key    The key to get.
     * @param string $module The module wherein we should search.
     * @return string
     */
    public static function err($key, $module = null)
    {
        return static::getError($key, $module);
    }

    /**
     * Get a label from the language-file
     *
     * @param string $key    The key to get.
     * @param string $module The module wherein we should search.
     * @return string
     */
    public static function lbl($key, $module = null)
    {
        return static::getLabel($key, $module);
    }

    /**
     * Get a message from the language-file
     *
     * @param string $key    The key to get.
     * @param string $module The module wherein we should search.
     * @return string
     */
    public static function msg($key, $module = null)
    {
        return static::getMessage($key, $module);
    }
}
