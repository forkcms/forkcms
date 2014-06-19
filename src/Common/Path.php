<?php

namespace Common;

use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\Exception;
use Backend\Core\Engine\Language as BL;

class Path
{
    /**
     * Returns the path for an image in a certain module and language
     *
     * @param string $module
     * @param string $language
     * @return string
     */
    public static function buildImagePath($module, $language = null)
    {
        if ($language === null) {
            $language = self::getLanguage();
        }

        return FRONTEND_FILES_PATH . '/' . $module
            . '/images/' . Model::get('current_site')->getId() . '/'
            . $language
        ;
    }

    /**
     * Returns the url for an image in a certain module and language
     *
     * @param string $module
     * @param string $language
     * @return string
     */
    public static function buildImageUrl($module, $language = null)
    {
        if ($language === null) {
            $language = self::getLanguage();
        }

        return FRONTEND_FILES_URL . '/' . $module
            . '/images/' . Model::get('current_site')->getId() . '/'
            . $language
        ;
    }

    /**
     * Fetches the current language indenpendent off the current application
     *
     * @todo this could be extracted to a Language class
     * @return string
     * @throws Frontend\Core\Engine\Exception
     */
    protected static function getLanguage()
    {
        switch (APPLICATION) {
            case 'Frontend':
            case 'FrontendAjax':
                return FRONTEND_LANGUAGE;
                break;
            case 'Backend':
            case 'BackendAjax':
            case 'BackendCronjob':
                return BL::getWorkingLanguage();
                break;
            case 'Api':
            case 'Install':
            default:
                throw new Exception('Invalid application: ' . APPLICATION);
                break;
        }
    }
}
