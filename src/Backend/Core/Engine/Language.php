<?php

namespace Backend\Core\Engine;

use Backend\Core\Language\Language as BackendLanguage;

/**
 * @deprecated
 */
class Language extends BackendLanguage
{
    /**
     * Get the active languages
     *
     * @deprecated
     *
     * @return array
     */
    public static function getActiveLanguages()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getActiveLanguages();
    }

    /**
     * Get all active languages in a format usable by SpoonForm's addRadioButton
     *
     * @deprecated
     *
     * @return array
     */
    public static function getCheckboxValues()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getCheckboxValues();
    }

    /**
     * @return string
     */
    public static function getCurrentModule()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getCurrentModule();
    }

    /**
     * Get an error from the language-file
     *
     * @param string $key The key to get.
     * @param string $module The module wherein we should search.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getError($key, $module = null)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getError($key, $module);
    }

    /**
     * Get all the errors from the language-file
     *
     * @deprecated
     *
     * @return array
     */
    public static function getErrors()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getErrors();
    }

    /**
     * Get the current interface language
     *
     * @deprecated
     *
     * @return string
     */
    public static function getInterfaceLanguage()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getInterfaceLanguage();
    }

    /**
     * Get all the possible interface languages
     *
     * @deprecated
     *
     * @return array
     */
    public static function getInterfaceLanguages()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getInterfaceLanguages();
    }

    /**
     * Get a label from the language-file
     *
     * @param string $key The key to get.
     * @param string $module The module wherein we should search.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getLabel($key, $module = null)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getLabel($key, $module);
    }

    /**
     * Get all the labels from the language-file
     *
     * @deprecated
     *
     * @return array
     */
    public static function getLabels()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getLabels();
    }

    /**
     * Get a message from the language-file
     *
     * @param string $key The key to get.
     * @param string $module The module wherein we should search.
     *
     * @deprecated
     *
     * @return string
     */
    public static function getMessage($key, $module = null)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getMessage($key, $module);
    }

    /**
     * Get the messages
     *
     * @deprecated
     *
     * @return array
     */
    public static function getMessages()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getMessages();
    }

    /**
     * Get the current working language
     *
     * @deprecated
     *
     * @return string
     */
    public static function getWorkingLanguage()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getWorkingLanguage();
    }

    /**
     * Get all possible working languages
     *
     * @deprecated
     *
     * @return array
     */
    public static function getWorkingLanguages()
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::getWorkingLanguages();
    }

    /**
     * Set locale
     * It will require the correct file and init the needed vars
     *
     * @param string $language The language to load.
     */
    public static function setLocale($language)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        parent::setLocale($language);
    }

    /**
     * Set the current working language
     *
     * @param string $language The language to use, if not provided we will use the working language.
     */
    public static function setWorkingLanguage($language)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        parent::setWorkingLanguage($language);
    }

    /**
     * Get an error from the language-file
     *
     * @param string $key The key to get.
     * @param string $module The module wherein we should search.
     *
     * @deprecated
     *
     * @return string
     */
    public static function err($key, $module = null)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::err($key, $module);
    }

    /**
     * Get a label from the language-file
     *
     * @param string $key The key to get.
     * @param string $module The module wherein we should search.
     *
     * @deprecated
     *
     * @return string
     */
    public static function lbl($key, $module = null)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::lbl($key, $module);
    }

    /**
     * Get a message from the language-file
     *
     * @param string $key The key to get.
     * @param string $module The module wherein we should search.
     *
     * @deprecated
     *
     * @return string
     */
    public static function msg($key, $module = null)
    {
        trigger_error(
            'Backend\Core\Engine\Language is deprecated.
             It has been moved to Backend\Core\Language\Language',
            E_USER_DEPRECATED
        );

        return parent::msg($key, $module);
    }
}
