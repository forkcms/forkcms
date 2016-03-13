<?php

namespace Backend\Modules\Locale\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the locale module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 * @author Stef Bastiaansen <stef.bastiaansen@wijs.be>
 * @author <thijs@wijs.be>
 */
class AnalyseModel extends Model
{
    /**
     * Get a string between two key strings
     *
     * @param string $start front key string
     * @param string $end back key string
     * @param string $str the string that needs to be checked
     * @return mixed return string or false
     */
    private static function getInbetweenStrings($start, $end, $str)
    {
        $matches = array();
        preg_match_all("@$start([a-zA-Z0-9_]*)$end@", $str, $matches);
        return (isset($matches[1])) ? current($matches[1]) :'';
    }

    /**
     * Get the locale that is used in the Backend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function getNonExistingBackendLocale($language)
    {
        $locale = array();
        $backendModuleFiles = array();
        $installedModules = BackendModel::getModules();

        // pickup the Backend module files
        $finder = new Finder();
        $finder
            ->name('*.php')
            ->name('*.html.twig')
            ->name('*.js');

        // collect all files
        foreach ($finder->files()->in(BACKEND_MODULES_PATH) as $file) {
            $module = self::getInbetweenStrings('Modules/', '/', $file->getPath());
            if (!in_array($module, $installedModules)) {
                continue;
            }
            $filename = $file->getFilename();
            $backendModuleFiles[$module][$filename] = $file;
        }

        //Find the locale in files an sort them
        foreach ($backendModuleFiles as $moduleName => $module) {
            $locale[$moduleName] = self::findLocaleInFiles($module);
        }

        // getAllBackendDBLocale
        $oldLocale = self::getSortLocaleFrom('Backend', $language);

        // filter the Foundlocale
        $nonExisting = array();
        foreach ($locale as $moduleName => &$module) {
            foreach ($module as $filename => &$file) {
                if (isset($oldLocale[$moduleName])) {
                    $file['locale'] = array_diff_key($file['locale'], $oldLocale[$moduleName]);
                }

                // extra filter for Core
                $file['locale'] = array_diff_key($file['locale'], $oldLocale['Core']);

                // extra filter for Pages
                $file['locale'] = array_diff_key($file['locale'], $oldLocale['Pages']);

                // output a converted array
                foreach ($file['locale'] as $localeName => $localeType) {
                    $key = $localeName;
                    $type = $localeType;
                    $nonExisting['Backend' . $key . $type . $moduleName] = array(
                        'language' => $language,
                        'application' => 'Backend',
                        'module' => $moduleName,
                        'type' => $type,
                        'name' => $key,
                        'used_in' => serialize($file['file'])
                    );
                }
            }
        }

        ksort($nonExisting);
        return $nonExisting;
    }

    /**
     * Get the locale that is used in the Frontend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function getNonExistingFrontendLocale($language)
    {
        $locale = array();
        $frontendModuleFiles = array();
        $installedModules = BackendModel::getModules();

        // pickup the Frontend module files
        $finder = new Finder();
        $finder->notPath('Cache')
            ->name('*.php')
            ->name('*.html.twig')
            ->name('*.js');

        // collect all files
        foreach ($finder->files()->in(FRONTEND_PATH) as $file) {
            // returns false if nothing found
            $module = self::getInbetweenStrings('Modules/', '/', $file->getPath());
            if ($module && !in_array($module, $installedModules)) {
                continue;
            }
            $filename = $file->getPath().'/'.$file->getFilename();
            $frontendModuleFiles['Core'][$filename] = $file;
        }

        // Find the locale in files an sort them
        foreach ($frontendModuleFiles as $moduleName => $module) {
            $locale[$moduleName] = self::findLocaleInFiles($module);
        }

        // getAllFrontendDBLocale
        $oldLocale = self::getSortLocaleFrom('Frontend', $language);

        // filter the Foundlocale
        $nonExisting = array();
        foreach ($locale as $moduleName => &$module) {
            foreach ($module as $filename => &$file) {
                // extra filter for Core
                $file['locale'] = array_diff_key($file['locale'], $oldLocale['Core']);

                // output a converted array
                foreach ($file['locale'] as $localeName => $localeType) {
                    $key = $localeName;
                    $type = $localeType;
                    $nonExisting['Frontend' . $key . $type . $moduleName] = array(
                        'language' => $language,
                        'application' => 'Frontend',
                        'module' => $moduleName,
                        'type' => $type,
                        'name' => $key,
                        'used_in' => serialize($file['file'])
                    );
                }
            }
        }

        ksort($nonExisting);
        return $nonExisting;
    }

    /**
     * Get the locale that is used in a sorted manner
     *
     * @param string $application the application
     * @param string $language the required language
     * @return array
     */
    public static function getSortLocaleFrom($application, $language)
    {
        $oldLocale = array();
        $type = array('lbl', 'act', 'err', 'msg');
        $allBackendDBLocale = self::getTranslations($application, '', $type, array($language), '', '');
        foreach ($allBackendDBLocale as $localeRecord) {
            foreach ($localeRecord as $record) {
                $oldLocale[$record['module']][$record['name']] = $record['name'];
            }
        }

        return $oldLocale;
    }

    /**
     * Find Locale in Files and return an array with of found files
     *
     * @param array $module
     * @return array found Locale Files
     */
    private static function findLocaleInFiles(array $module)
    {
        $locale = array();
        foreach ($module as $filename => $file) {
            $matches = array();
            $extension = $file->getExtension();
            $fileContent = $file->getContents();

            switch ($extension) {
                // PHP file
                case 'js':
                    preg_match_all('/\{\$(act|err|lbl|msg)(.*)(\|.*)?\}/iU', $fileContent, $matches);

                    if (count($matches[0]) > 0) {
                        $locale[$filename]['file'] = $filename;
                        $locale[$filename]['locale'] = array_combine($matches[2], $matches[1]);
                    }
                    break;

                // PHP file
                case 'php':
                    preg_match_all(
                        '/(FrontendLanguage|FL|BL|BackendLanguage)::(get(Label|Error|Message)|act|err|lbl|msg)\(\'(.*)\'(.*)?\)/iU',
                        $fileContent,
                        $matches
                    );

                    if (count($matches[0]) > 0) {
                        $locale[$filename]['file'] = $filename;
                        $locale[$filename]['locale'] = array_combine($matches[4], $matches[2]);
                    }
                    break;

                // TPL file
                case 'tpl':
                    preg_match_all(
                        '/\{\$(act|err|lbl|msg)([A-Z][a-zA-Z_]*)(\|.*)?\}/U',
                        $fileContent,
                        $matches
                    );

                    if (count($matches[0]) > 0) {
                        $locale[$filename]['file'] = $filename;
                        $locale[$filename]['locale'] = array_combine($matches[2], $matches[1]);
                    }
                    break;
            }
        }
        return $locale;
    }
}
