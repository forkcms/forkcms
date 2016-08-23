<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Backend\Modules\Locale\Engine\CacheBuilder;

/**
 * This class will store the language-dependant content for the frontend.
 */
class Language
{
    /**
     * Locale arrays
     *
     * @var    array
     */
    private static $act = array();
    private static $err = array();
    private static $lbl = array();
    private static $msg = array();

    /**
     * Locale fallback arrays
     *
     * @var    array
     */
    private static $fallbackAct = array();
    private static $fallbackErr = array();
    private static $fallbackLbl = array();
    private static $fallbackMsg = array();

    /**
     * The possible languages
     *
     * @var    array
     */
    private static $languages = array('active' => array(), 'possible_redirect' => array());

    /**
     * Build the language files
     *
     * @param string $language    The language to build the locale-file for.
     * @param string $application The application to build the locale-file for.
     */
    public static function buildCache($language, $application)
    {
        $cacheBuilder = new CacheBuilder(Model::get('database'));
        $cacheBuilder->buildCache($language, $application);
    }

    /**
     * Get an action from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function getAction($key, $fallback = true)
    {
        // redefine
        $key = \SpoonFilter::toCamelCase((string) $key);

        // if the action exists return it,
        if (isset(self::$act[$key])) {
            return self::$act[$key];
        }

        // If we should fallback and the fallback label exists, return it
        if (
            isset(self::$fallbackAct[$key]) &&
            $fallback === true &&
            Model::getContainer()->getParameter('kernel.debug') === false
        ) {
            return self::$fallbackAct[$key];
        }

        // otherwise return the key in label-format
        return '{$act' . $key . '}';
    }

    /**
     * Get all the actions
     *
     * @return array
     */
    public static function getActions()
    {
        return (Model::getContainer()->getParameter('kernel.debug')) ? self::$act : array_merge(self::$fallbackAct, self::$act);
    }

    /**
     * Get the active languages
     *
     * @return array
     */
    public static function getActiveLanguages()
    {
        // validate the cache
        if (empty(self::$languages['active'])) {
            // grab from settings
            $activeLanguages = (array) Model::get('fork.settings')->get('Core', 'active_languages');

            // store in cache
            self::$languages['active'] = $activeLanguages;
        }

        // return from cache
        return self::$languages['active'];
    }

    /**
     * Get the preferred language by using the browser-language
     *
     * @param bool $forRedirect Only look in the languages to redirect?
     *
     * @return string
     */
    public static function getBrowserLanguage($forRedirect = true)
    {
        // browser language set
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && mb_strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) >= 2) {
            // get languages
            $redirectLanguages = self::getRedirectLanguages();

            // preferred languages
            $acceptedLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $browserLanguages = array();

            foreach ($acceptedLanguages as $language) {
                $qPos = mb_strpos($language, 'q=');
                $weight = 1;

                if ($qPos !== false) {
                    $endPos = mb_strpos($language, ';', $qPos);
                    $weight = ($endPos === false) ? (float) mb_substr($language, $qPos + 2) : (float) mb_substr(
                        $language,
                        $qPos + 2,
                        $endPos
                    );
                }

                $browserLanguages[$language] = $weight;
            }

            // sort by weight
            arsort($browserLanguages);

            // loop until result
            foreach (array_keys($browserLanguages) as $language) {
                // redefine language
                $language = mb_substr($language, 0, 2); // first two characters

                // find possible language
                if ($forRedirect) {
                    // check in the redirect-languages
                    if (in_array($language, $redirectLanguages)) {
                        return $language;
                    }
                }
            }
        }

        // fallback
        return SITE_DEFAULT_LANGUAGE;
    }

    /**
     * Get an error from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function getError($key, $fallback = true)
    {
        // redefine
        $key = \SpoonFilter::toCamelCase((string) $key);

        // if the error exists return it,
        if (isset(self::$err[$key])) {
            return self::$err[$key];
        }

        // If we should fallback and the fallback label exists, return it
        if (
            isset(self::$fallbackErr[$key]) &&
            $fallback === true &&
            Model::getContainer()->getParameter('kernel.debug') === false
        ) {
            return self::$fallbackErr[$key];
        }

        // otherwise return the key in label-format
        return '{$err' . $key . '}';
    }

    /**
     * Get all the errors
     *
     * @return array
     */
    public static function getErrors()
    {
        return (Model::getContainer()->getParameter('kernel.debug')) ? self::$err : array_merge(self::$fallbackErr, self::$err);
    }

    /**
     * Get a label from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function getLabel($key, $fallback = true)
    {
        // redefine
        $key = \SpoonFilter::toCamelCase((string) $key);

        // if the error exists return it,
        if (isset(self::$lbl[$key])) {
            return self::$lbl[$key];
        }

        // If we should fallback and the fallback label exists, return it
        if (
            isset(self::$fallbackLbl[$key]) &&
            $fallback === true &&
            Model::getContainer()->getParameter('kernel.debug') === false
        ) {
            return self::$fallbackLbl[$key];
        }

        // otherwise return the key in label-format
        return '{$lbl' . $key . '}';
    }

    /**
     * Get all the labels
     *
     * @return array
     */
    public static function getLabels()
    {
        return (Model::getContainer()->getParameter('kernel.debug')) ? self::$lbl : array_merge(self::$fallbackLbl, self::$lbl);
    }

    /**
     * Get a message from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function getMessage($key, $fallback = true)
    {
        // redefine
        $key = \SpoonFilter::toCamelCase((string) $key);

        // if the error exists return it,
        if (isset(self::$msg[$key])) {
            return self::$msg[$key];
        }

        // If we should fallback and the fallback label exists, return it
        if (
            isset(self::$fallbackMsg[$key]) &&
            $fallback === true &&
            Model::getContainer()->getParameter('kernel.debug') === false
        ) {
            return self::$fallbackMsg[$key];
        }

        // otherwise return the key in label-format
        return '{$msg' . $key . '}';
    }

    /**
     * Get all the messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return (Model::getContainer()->getParameter('kernel.debug') === true) ? self::$msg : array_merge(self::$fallbackMsg, self::$msg);
    }

    /**
     * Get the redirect languages
     *
     * @return array
     */
    public static function getRedirectLanguages()
    {
        // validate the cache
        if (empty(self::$languages['possible_redirect'])) {
            // grab from settings
            $redirectLanguages = (array) Model::get('fork.settings')->get('Core', 'redirect_languages');

            // store in cache
            self::$languages['possible_redirect'] = $redirectLanguages;
        }

        // return
        return self::$languages['possible_redirect'];
    }

    /**
     * Set locale
     *
     * @param string $language The language to load, if not provided we will load the language based on the URL.
     * @param bool   $force    Force the language, so don't check if the language is active.
     */
    public static function setLocale($language = null, $force = false)
    {
        // redefine
        $language = ($language !== null) ? (string) $language : LANGUAGE;

        // validate language
        if (!$force && !in_array($language, self::getActiveLanguages())) {
            throw new Exception('Invalid language (' . $language . ').');
        }

        // validate file, generate it if needed
        $filesystem = new Filesystem();
        if (!$filesystem->exists(FRONTEND_CACHE_PATH . '/Locale/en.json')) {
            self::buildCache('en', 'Frontend');
        }
        if (!$filesystem->exists(FRONTEND_CACHE_PATH . '/Locale/' . $language . '.json')) {
            self::buildCache($language, 'Frontend');
        }

        // set English translations, they'll be the fallback
        $fallbackTranslations = json_decode(
            file_get_contents(FRONTEND_CACHE_PATH . '/Locale/en.json'),
            true
        );
        self::$fallbackAct = (array) $fallbackTranslations['act'];
        self::$fallbackErr = (array) $fallbackTranslations['err'];
        self::$fallbackLbl = (array) $fallbackTranslations['lbl'];
        self::$fallbackMsg = (array) $fallbackTranslations['msg'];

        // We will overwrite with the requested language's translations upon request
        $translations = json_decode(
            file_get_contents(FRONTEND_CACHE_PATH . '/Locale/' . $language . '.json'),
            true
        );
        self::$act = (array) $translations['act'];
        self::$err = (array) $translations['err'];
        self::$lbl = (array) $translations['lbl'];
        self::$msg = (array) $translations['msg'];
    }

    /**
     * Get an action from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function act($key, $fallback = true)
    {
        return self::getAction($key, $fallback);
    }

    /**
     * Get an error from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function err($key, $fallback = true)
    {
        return self::getError($key, $fallback);
    }

    /**
     * Get a label from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function lbl($key, $fallback = true)
    {
        return self::getLabel($key, $fallback);
    }

    /**
     * Get a message from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     */
    public static function msg($key, $fallback = true)
    {
        return self::getMessage($key, $fallback);
    }
}
