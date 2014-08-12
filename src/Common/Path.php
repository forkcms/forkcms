<?php

namespace Common;

use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\Exception;
use Backend\Core\Engine\Language as BL;

/**
 * Responsible for handling paths in Fork.
 *
 * @author <per@wijs.be>
 * @author <wouter@wijs.be>
 * @todo Make the class work for language/site independent modules
 */
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
        return self::prependImagePath(FRONTEND_FILES_PATH, $module, $language);
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
        return self::prependImagePath(FRONTEND_FILES_URL, $module, $language);
    }

    /**
     * Given the prefix for an image path, return the full path.
     *
     * @param string $prefix
     * @param string $module
     * @param string $language
     * @return string
     * @internal This is a helper for buildImage{Path,Url}()
     */
    protected static function prependImagePath($prefix, $module, $language = null)
    {
        if ($language === null) {
            $language = self::getLanguage();
        }

        return $prefix . '/' . $module
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
