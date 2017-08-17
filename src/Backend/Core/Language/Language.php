<?php

namespace Backend\Core\Language;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * This class will store the language-dependant content for the Backend, it will also store the
 * current language for the user.
 */
class Language
{
    /**
     * The errors
     *
     * @var array
     */
    protected static $err = [];

    /**
     * The labels
     *
     * @var array
     */
    protected static $lbl = [];

    /**
     * The messages
     *
     * @var array
     */
    protected static $msg = [];

    /**
     * The active languages
     *
     * @var array
     */
    protected static $activeLanguages;

    /**
     * The current interface-language
     *
     * @var string
     */
    protected static $currentInterfaceLanguage;

    /**
     * The current language that the user is working with
     *
     * @var string
     */
    protected static $currentWorkingLanguage;

    public static function getActiveLanguages(): array
    {
        // validate the cache
        if (empty(self::$activeLanguages)) {
            self::$activeLanguages = (array) Model::get('fork.settings')->get('Core', 'active_languages');
        }

        // return from cache
        return self::$activeLanguages;
    }

    /**
     * Get all active languages in a format usable by SpoonForm's addRadioButton
     *
     * @return array
     */
    public static function getCheckboxValues(): array
    {
        $languages = self::getActiveLanguages();
        $results = [];

        // stop here if no languages are present
        if (empty($languages)) {
            return [];
        }

        // addRadioButton requires an array with keys 'value' and 'label'
        foreach ($languages as $abbreviation) {
            $results[] = [
                'value' => $abbreviation,
                'label' => self::lbl(mb_strtoupper($abbreviation)),
            ];
        }

        return $results;
    }

    public static function getCurrentModule(): string
    {
        // Needed to make it possible to use the backend language in the console.
        if (defined('APPLICATION') && APPLICATION === 'Console') {
            return 'Core';
        }

        if (Model::getContainer()->has('url')) {
            return Model::get('url')->getModule();
        }

        if (Model::requestIsAvailable() && Model::getRequest()->query->has('module')) {
            return Model::getRequest()->query->get('module');
        }

        return 'Core';
    }

    public static function getError(string $key, string $module = null): string
    {
        $module = $module ?? self::getCurrentModule();

        $key = \SpoonFilter::toCamelCase($key);

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

    public static function getErrors(): array
    {
        return self::$err;
    }

    public static function getInterfaceLanguage(): string
    {
        if (self::$currentInterfaceLanguage === null) {
            self::$currentInterfaceLanguage = Model::getContainer()->getParameter('site.default_language');
        }

        return self::$currentInterfaceLanguage;
    }

    public static function getInterfaceLanguages(): array
    {
        $languages = [];

        // grab the languages from the settings & loop language to reset the label
        foreach ((array) Model::get('fork.settings')->get('Core', 'interface_languages', ['en']) as $key) {
            // fetch language's translation
            $languages[$key] = self::getLabel(mb_strtoupper($key), 'Core');
        }

        // sort alphabetically
        asort($languages);

        // return languages
        return $languages;
    }

    public static function getLabel(string $key, string $module = null): string
    {
        $module = $module ?? self::getCurrentModule();

        $key = \SpoonFilter::toCamelCase($key);

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

    public static function getLabels(): array
    {
        return self::$lbl;
    }

    public static function getMessage(string $key, string $module = null): string
    {
        $key = \SpoonFilter::toCamelCase((string) $key);
        $module = $module ?? self::getCurrentModule();

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

    public static function getMessages(): array
    {
        return self::$msg;
    }

    public static function getWorkingLanguage(): string
    {
        if (self::$currentWorkingLanguage === null) {
            self::$currentWorkingLanguage = Model::getContainer()->getParameter('site.default_language');
        }

        return self::$currentWorkingLanguage;
    }

    public static function getWorkingLanguages(): array
    {
        $languages = [];

        // grab the languages from the settings & loop language to reset the label
        foreach ((array) Model::get('fork.settings')->get('Core', 'languages', ['en']) as $key) {
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
    public static function setLocale(string $language): void
    {
        // validate file, generate it if needed
        if (!is_file(BACKEND_CACHE_PATH . '/Locale/en.json')) {
            BackendLocaleModel::buildCache('en', APPLICATION);
        }
        if (!is_file(BACKEND_CACHE_PATH . '/Locale/' . $language . '.json')) {
            // if you use the language in the console act like it is in the backend
            BackendLocaleModel::buildCache(
                $language,
                (defined('APPLICATION') && APPLICATION === 'Console') ? 'Backend' : APPLICATION
            );
        }

        // store
        self::$currentInterfaceLanguage = $language;

        // attempt to set a cookie
        try {
            // Needed to make it possible to use the backend language in the console.
            if (defined('APPLICATION') && APPLICATION !== 'Console') {
                Model::getContainer()->get('fork.cookie')->set('interface_language', $language);
            }
        } catch (RuntimeException|ServiceNotFoundException $e) {
            // settings cookies isn't allowed, because this isn't a real problem we ignore the exception
        }

        // set English translations, they'll be the fallback
        $translations = json_decode(
            file_get_contents(BACKEND_CACHE_PATH . '/Locale/en.json'),
            true
        );
        self::$err = (array) $translations['err'];
        self::$lbl = (array) $translations['lbl'];
        self::$msg = (array) $translations['msg'];

        // overwrite with the requested language's translations
        $translations = json_decode(
            file_get_contents(BACKEND_CACHE_PATH . '/Locale/' . $language . '.json'),
            true
        );
        $err = (array) $translations['err'];
        $lbl = (array) $translations['lbl'];
        $msg = (array) $translations['msg'];
        foreach ($err as $module => $translations) {
            if (!isset(self::$err[$module])) {
                self::$err[$module] = [];
            }
            self::$err[$module] = array_merge(self::$err[$module], $translations);
        }
        foreach ($lbl as $module => $translations) {
            if (!isset(self::$lbl[$module])) {
                self::$lbl[$module] = [];
            }
            self::$lbl[$module] = array_merge(self::$lbl[$module], $translations);
        }
        foreach ($msg as $module => $translations) {
            if (!isset(self::$msg[$module])) {
                self::$msg[$module] = [];
            }
            self::$msg[$module] = array_merge(self::$msg[$module], $translations);
        }
    }

    /**
     * @param string $language The language to use, if not provided we will use the working language.
     */
    public static function setWorkingLanguage(string $language): void
    {
        self::$currentWorkingLanguage = $language;
    }

    public static function err(string $key, string $module = null): string
    {
        return self::getError($key, $module);
    }

    public static function lbl(string $key, string $module = null): string
    {
        return self::getLabel($key, $module);
    }

    public static function msg(string $key, string $module = null): string
    {
        return self::getMessage($key, $module);
    }
}
