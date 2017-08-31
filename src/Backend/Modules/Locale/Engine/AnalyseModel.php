<?php

namespace Backend\Modules\Locale\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the locale module
 */
class AnalyseModel extends Model
{
    /**
     * Get the locale that is used in the Backend but doesn't exists.
     *
     * @param string $language The language to check.
     *
     * @return array
     */
    public static function getNonExistingBackendLocale(string $language): array
    {
        $backendAnalyser = new LocaleAnalyser(
            'Backend',
            [BACKEND_MODULES_PATH, BACKEND_CORE_PATH],
            BackendModel::getModules()
        );

        return $backendAnalyser->findMissingLocale($language);
    }

    /**
     * Get the locale that is used in the Frontend but doesn't exists.
     *
     * @param string $language The language to check.
     *
     * @return array
     */
    public static function getNonExistingFrontendLocale(string $language): array
    {
        $frontendAnalyser = new LocaleAnalyser(
            'Frontend',
            [FRONTEND_MODULES_PATH, FRONTEND_CORE_PATH],
            BackendModel::getModules()
        );

        return $frontendAnalyser->findMissingLocale($language);
    }

    /**
     * Get the locale that is used in a sorted manner
     *
     * @param string $application the application
     * @param string $language the required language
     *
     * @deprecated This method is no longer used in the core and will be removed in fork 6
     * @return array
     */
    public static function getSortLocaleFrom(string $application, string $language): array
    {
        trigger_error('This method is no longer used in the core and will be removed in fork 6');

        $oldLocale = [];
        $type = ['lbl', 'act', 'err', 'msg'];
        $allBackendDBLocale = self::getTranslations($application, '', $type, [$language], '', '');
        foreach ($allBackendDBLocale as $localeRecord) {
            foreach ($localeRecord as $record) {
                $oldLocale[$record['module']][$record['name']] = $record['name'];
            }
        }

        return $oldLocale;
    }
}
