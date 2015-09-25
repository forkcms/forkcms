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
    public static function getInbetweenStrings($start, $end, $str)
    {
        $matches = array();
        preg_match_all("@$start([a-zA-Z0-9_]*)$end@", $str, $matches);
        return (isset($matches[1]))? current($matches[1]): '';
    }

    /**
     * Get the locale that is used in the backend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function getNonExistingBackendLocale($language)
    {
        // pickup the Backend module files
        $finder = new Finder();
        $finder
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.js');

        $backendModuleFiles = array();
        foreach ($finder->files()->in(BACKEND_MODULES_PATH) as $file) {
            $module = self::getInbetweenStrings('Modules/', '/', $file->getPath());
            $filename = $file->getFilename();
            $backendModuleFiles[$module][$filename] = $file;
        }

        // get installed modules
        $installedModules = BackendModel::getModules();

        // Find the modules files an sort them
        $locale = $this->findLocaleInFiles($backendModuleFiles, $installedModules);

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
     * Get the locale that is used in the Frontend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function getNonExistingFrontendLocale($language)
    {
        // pickup the Frontend module files
        $finder = new Finder();
        $finder->notPath('Cache')
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.js');

        $frontendModuleFiles = array();
        foreach ($finder->files()->in(FRONTEND_PATH) as $file) {
            $filename = $file->getPath().'/'.$file->getFilename();
            $frontendModuleFiles['Core'][$filename] = $file;
        }

        // get installed modules
        $installedModules = BackendModel::getModules();

        // Find the modules files an sort them
        $locale = $this->findLocaleInFiles($frontendModuleFiles, $installedModules);

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
     * Find Locale in Files
     *
     * @param array $moduleFiles
     * @param array $installedModules
     * @return array found Locale Files
     */
    private function findLocaleInFiles(array $moduleFiles, array $installedModules)
    {
        $locale = array();
        foreach ($moduleFiles as $moduleName => $module) {

            foreach ($module as $filename => $file) {

                $extension = $file->getExtension();
                $fileContent = $file->getContents();

                // only installed modules
                if (!in_array($moduleName, $installedModules)) {
                    unset($moduleFiles[$moduleName]);
                    continue;
                }

                // search / finding locale
                $matches = array();
                switch ($extension) {

                    // PHP file
                    case 'js':
                        // get matches
                        preg_match_all('/\{\$(act|err|lbl|msg)(.*)(\|.*)?\}/iU', $fileContent, $matches);

                        if (count($matches[0]) > 0) {
                            $locale[$moduleName][$filename]['file'] = $filename;
                            $locale[$moduleName][$filename]['locale'] = array_combine($matches[2], $matches[1]);
                        }
                        break;

                    // PHP file
                    case 'php':
                        // get matches
                        preg_match_all(
                            '/(FrontendLanguage|FL)::(get(Label|Error|Message)|act|err|lbl|msg)\(\'(.*)\'(.*)?\)/iU',
                            $fileContent,
                            $matches
                        );

                        if (count($matches[0]) > 0) {
                            $locale[$moduleName][$filename]['file'] = $filename;
                            $locale[$moduleName][$filename]['locale'] = array_combine($matches[4], $matches[2]);
                        }
                        break;

                    // TPL file
                    case 'tpl':
                        // get matches
                        preg_match_all(
                            '/\{\$(act|err|lbl|msg)([A-Z][a-zA-Z_]*)(\|.*)?\}/U',
                            $fileContent,
                            $matches
                        );

                        if (count($matches[0]) > 0) {
                            $locale[$moduleName][$filename]['file'] = $filename;
                            $locale[$moduleName][$filename]['locale'] = array_combine($matches[2], $matches[1]);
                        }
                        break;
                }
            }
        }
        return $locale;
    }
}
