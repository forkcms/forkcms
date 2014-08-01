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
        if (empty(self::$activeLanguages)) {
            // grab from settings
            $activeLanguages = (array) BackendModel::getModuleSetting('Core', 'active_languages');

            // store in cache
            self::$activeLanguages = $activeLanguages;
        }

        // return from cache
        return self::$activeLanguages;
    }

    /**
     * Get all active languages in a format usable by SpoonForm's addRadioButton
     *
     * @return array
     */
    public static function getCheckboxValues()
    {
        $languages = self::getActiveLanguages();
        $results = array();

        // stop here if no languages are present
        if (empty($languages)) {
            return array();
        }

        // addRadioButton requires an array with keys 'value' and 'label'
        foreach ($languages as $abbreviation) {
            $results[] = array(
                'value' => $abbreviation,
                'label' => self::lbl(strtoupper($abbreviation))
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
        if (isset(self::$err[$module][$key])) {
            return self::$err[$module][$key];
        }

        // check if the error exists in the Core
        if (isset(self::$err['Core'][$key])) {
            return self::$err['Core'][$key];
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
        return (array) self::$err;
    }

    /**
     * Get the current interface language
     *
     * @return string
     */
    public static function getInterfaceLanguage()
    {
        return self::$currentInterfaceLanguage;
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
            $languages[$key] = self::getLabel(mb_strtoupper($key), 'Core');
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
        if (isset(self::$lbl[$module][$key])) {
            return self::$lbl[$module][$key];
        }

        // check if the label exists in the Core
        if (isset(self::$lbl['Core'][$key])) {
            return self::$lbl['Core'][$key];
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
        return self::$lbl;
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
        if (isset(self::$msg[$module][$key])) {
            return self::$msg[$module][$key];
        }

        // check if the message exists in the Core
        if (isset(self::$msg['Core'][$key])) {
            return self::$msg['Core'][$key];
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
        return self::$msg;
    }

    /**
     * Get the current working language
     *
     * @return string
     */
    public static function getWorkingLanguage()
    {
        return self::$currentWorkingLanguage;
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
            $languages[$key] = self::getLabel(mb_strtoupper($key), 'Core');
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
        if (!is_file(BACKEND_CACHE_PATH . '/Locale/en.php')) {
            BackendLocaleModel::buildCache('en', APPLICATION);
        }
        if (!is_file(BACKEND_CACHE_PATH . '/Locale/' . $language . '.php')) {
            BackendLocaleModel::buildCache($language, APPLICATION);
        }

        // store
        self::$currentInterfaceLanguage = $language;

        // attempt to set a cookie
        try {
            CommonCookie::set('interface_language', $language);
        } catch (\SpoonCookieException $e) {
            // settings cookies isn't allowed, because this isn't a real problem we ignore the exception
        }

        // init vars
        $err = array();
        $lbl = array();
        $msg = array();

        // set English translations, they'll be the fallback
        require BACKEND_CACHE_PATH . '/Locale/en.php';
        self::$err = (array) $err;
        self::$lbl = (array) $lbl;
        self::$msg = (array) $msg;

        // overwrite with the requested language's translations
        require BACKEND_CACHE_PATH . '/Locale/' . $language . '.php';
        foreach ($err as $module => $translations) {
            if (!isset(self::$err[$module])) {
                self::$err[$module] = array();
            }
            self::$err[$module] = array_merge(self::$err[$module], $translations);
        }
        foreach ($lbl as $module => $translations) {
            if (!isset(self::$lbl[$module])) {
                self::$lbl[$module] = array();
            }
            self::$lbl[$module] = array_merge(self::$lbl[$module], $translations);
        }
        foreach ($msg as $module => $translations) {
            if (!isset(self::$msg[$module])) {
                self::$msg[$module] = array();
            }
            self::$msg[$module] = array_merge(self::$msg[$module], $translations);
        }
    }

    /**
     * Set the current working language
     *
     * @param string $language The language to use, if not provided we will use the working language.
     */
    public static function setWorkingLanguage($language)
    {
        self::$currentWorkingLanguage = (string) $language;
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
        return self::getError($key, $module);
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
        return self::getLabel($key, $module);
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
        return self::getMessage($key, $module);
    }
}
