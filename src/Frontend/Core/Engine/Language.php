<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Language\Language as FrontendLanguage;

/**
 * This class will store the language-dependant content for the frontend.
 *
 * @deprecated
 */
class Language extends FrontendLanguage
{
    /**
     * Build the language files
     *
     * @param string $language    The language to build the locale-file for.
     * @param string $application The application to build the locale-file for.
     *
     * @deprecated
     */
    public static function buildCache($language, $application)
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::buildCache($language, $application);
    }

    /**
     * Get an action from the language-file
     *
     * @param string $key      The key to get.
     * @param bool   $fallback Should we provide a fallback in English?
     *
     * @return string
     *
     * @deprecated
     */
    public static function getAction($key, $fallback = true)
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getAction($key, $fallback);
    }

    /**
     * Get all the actions
     *
     * @return array
     */
    public static function getActions()
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getActions();
    }

    /**
     * Get the active languages
     *
     * @return array
     */
    public static function getActiveLanguages()
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getActiveLanguages();
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getBrowserLanguage($forRedirect);
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getError($key, $fallback);
    }

    /**
     * Get all the errors
     *
     * @return array
     */
    public static function getErrors()
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getErrors();
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getLabel($key, $fallback);
    }

    /**
     * Get all the labels
     *
     * @return array
     */
    public static function getLabels()
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getLabels();
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getMessage($key, $fallback);
    }

    /**
     * Get all the messages
     *
     * @return array
     */
    public static function getMessages()
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getMessages();
    }

    /**
     * Get the redirect languages
     *
     * @return array
     */
    public static function getRedirectLanguages()
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getRedirectLanguages();
    }

    /**
     * Set locale
     *
     * @param string $language The language to load, if not provided we will load the language based on the URL.
     * @param bool   $force    Force the language, so don't check if the language is active.
     *
     * @throws Exception
     */
    public static function setLocale($language = null, $force = false)
    {
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::setLocale($language, $force);
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::act($key, $fallback);
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::err($key, $fallback);
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::lbl($key, $fallback);
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
        trigger_error(
            'Frontend\Core\Engine\Language is deprecated.
             It has been moved to Frontend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::msg($key, $fallback);
    }
}
