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

use Common\Uri as CommonUri;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

use Frontend\Core\Engine\Language as FL;
use Common\ProjectIntel;

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
        // pickup the Back module files
        $finder = new Finder();
        $finder
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.js');

        foreach ($finder->files()->in(BACKEND_MODULES_PATH) as $file) {
            $module = self::getInbetweenStrings('Modules/', '/', $file->getPath());
            $filename = $file->getFilename();
            $backendModuleFiles[$module][$filename] = $file;
        }

        // loop over the modules
        foreach ($backendModuleFiles as $moduleName => $module) {
            foreach ($module as $filename => $file) {
                $extension = $file->getExtension();
                $fileContent = $file->getContents();

                // search / finding locale
                switch ($extension) {
                    // PHP file
                    case 'php':
                        $matches = array();

                        // get matches
                        preg_match_all(
                            '/(BackendLanguage|BL)::(get(Label|Error|Message)|act|err|lbl|msg)\(\'(.*)\'(.*)?\)/iU',
                            $fileContent,
                            $matches
                        );

                        if (count($matches[0]) > 0) {
                            $locale[$moduleName]['locale'] = array_combine($matches[4], $matches[2]);
                            $locale[$moduleName]['file'] = $file->getPath();
                        }
                        break;
                }
            }
        }

        // getAllBackendDBLocale
        $type = array(
            0 => 'lbl',
            1 => 'act',
            2 => 'err',
            3 => 'msg',
        );
        $language = BL::getWorkingLanguage();
        $allBackendDBLocale = self::getTranslations('Backend', '', $type, $language, '', '');
        foreach ($allBackendDBLocale as $localeRecord) {
            foreach ($localeRecord as $record) {
                $oldLocale[$record['module']][$record['name']] = $record['name'];
            }
        }

        // filter the Foundlocale
        foreach ($locale as $moduleName => &$module) {
            $localeFilter = $module['locale'];
            if (isset($oldLocale[$moduleName])) {
                $localeFilter = array_diff_key($locale, $oldLocale[$moduleName]);
            }
        }

        // output a converted array
        foreach ($locale as $moduleName => $module) {
            $localeFilter = $module['locale'];
            $file = $module['file'];
            foreach ($module['locale'] as $localeName => $localeType) {
                $key = $localeName;
                $type = $localeType;
                $nonExisting['Backend' . $key . $type . $moduleName] = array(
                    'language' => $language,
                    'application' => 'Backend',
                    'module' => $moduleName,
                    'type' => $type,
                    'name' => $key,
                    'used_in' => serialize($file)
                );
            }
        }

        ksort($nonExisting);

        return $nonExisting;
    }

    /**
     * Get the locale that is used in the backend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function ggetNonExistingBackendLocale($language)
    {
        $modules = BackendModel::getModules();

        // search fo the error module
        $key = array_search('error', $modules);

        // remove error module
        if ($key !== false) unset($modules[$key]);

        $used = array();

        // get labels from navigation
        $lbl = self::getLabelsFromBackendNavigation();
        foreach ((array) $lbl as $label) $used['lbl'][$label] = array(
            'files' => array('<small>used in navigation</small>'),
            'module_specific' => array()
        );

        // get labels from table
        $lbl = (array) BackendModel::getContainer()->get('database')->getColumn('SELECT label FROM modules_extras');
        foreach ((array) $lbl as $label) $used['lbl'][$label] = array(
            'files' => array('<small>used in database</small>'),
            'module_specific' => array()
        );

        $finder = new Finder();
        $finder->notPath('Cache')
            ->notPath('Core/Js/ckeditor')
            ->notPath('Core/Js/ckfinder')
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.js');

        foreach ($finder->files()->in(BACKEND_PATH) as $file) {

            // grab content
            $content = $file->getContents();

            // process based on extension
            switch ($file->getExtension()) {
                // javascript file
                case 'js':
                    $matches = array();

                    // get matches
                    preg_match_all('/\{\$(act|err|lbl|msg)(.*)(\|.*)?\}/iU', $content, $matches);

                    // any matches?
                    if (isset($matches[2])) {
                        // loop matches
                        foreach ($matches[2] as $key => $match) {
                            // set type
                            $type = $matches[1][$key];



                            // loop modules
                            foreach ($modules as $module) {
                                // determine if this is a module specific locale
                                if (substr($match, 0, mb_strlen($module)) == \SpoonFilter::toCamelCase(
                                        $module
                                    ) && mb_strlen($match) > mb_strlen($module)
                                ) {
                                    // cleanup
                                    $match = str_replace(\SpoonFilter::toCamelCase($module), '', $match);

                                    // init if needed
                                    if (!isset($used[$type][$match])) $used[$type][$match] = array(
                                        'files' => array(),
                                        'module_specific' => array()
                                    );

                                    // add module
                                    $used[$type][$match]['module_specific'][] = $module;
                                }
                            }

                            // init if needed
                            if (!isset($used[$match])) $used[$type][$match] = array(
                                'files' => array(),
                                'module_specific' => array()
                            );

                            // add file
                            if (!in_array(
                                $file->getRealPath(),
                                $used[$type][$match]['files']
                            )
                            ) $used[$type][$match]['files'][] = $file->getRealPath();
                        }
                    }
                    break;

                // PHP file
                case 'php':
                    $matches = array();
                    $matchesURL = array();

                    // get matches
                    preg_match_all(
                        '/(BackendLanguage|BL)::(get(Label|Error|Message)|act|err|lbl|msg)\(\'(.*)\'(.*)?\)/iU',
                        $content,
                        $matches
                    );

                    // match errors
                    preg_match_all('/&(amp;)?(error|report)=([A-Z0-9-_]+)/i', $content, $matchesURL);

                    // any errormessages
                    if (!empty($matchesURL[0])) {
                        // loop matches
                        foreach ($matchesURL[3] as $key => $match) {
                            $type = 'lbl';
                            if ($matchesURL[2][$key] == 'error') $type = 'Error';
                            if ($matchesURL[2][$key] == 'report') $type = 'Message';

                            $matches[0][] = '';
                            $matches[1][] = 'BL';
                            $matches[2][] = '';
                            $matches[3][] = $type;
                            $matches[4][] = \SpoonFilter::toCamelCase(\SpoonFilter::toCamelCase($match, '-'), '_');
                            $matches[5][] = '';
                        }
                    }

                    // any matches?
                    if (!empty($matches[4])) {
                        // loop matches
                        foreach ($matches[4] as $key => $match) {
                            // set type
                            $type = 'lbl';
                            if ($matches[3][$key] == 'Error' || $matches[2][$key] == 'err') $type = 'err';
                            if ($matches[3][$key] == 'Message' || $matches[2][$key] == 'msg') $type = 'msg';

                            // specific module?
                            if (isset($matches[5][$key]) && $matches[5][$key] != '') {
                                // try to grab the module
                                $specificModule = $matches[5][$key];
                                $specificModule = ucfirst(trim(str_replace(array(',', '\''), '', $specificModule)));

                                // not core?
                                if ($specificModule != 'Core') {
                                    // dynamic module
                                    if ($specificModule == '$this->URL->getModule(' || $specificModule == '$this->getModule(') {
                                        // init var
                                        $count = 0;

                                        // replace
                                        $modulePath = str_replace(
                                            realpath(BACKEND_MODULES_PATH),
                                            '',
                                            realpath($file),
                                            $count
                                        );

                                        // validate
                                        if ($count == 1) {
                                            // split into chunks
                                            $chunks = (array) explode('/', trim($modulePath, '/'));

                                            // set specific module
                                            if (isset($chunks[0])) $specificModule = $chunks[0];

                                            // skip
                                            else continue;
                                        }
                                    }

                                    // init if needed
                                    if (!isset($used[$type][$match])) $used[$type][$match] = array(
                                        'files' => array(),
                                        'module_specific' => array()
                                    );

                                    // add module
                                    $used[$type][$match]['module_specific'][] = $specificModule;
                                }
                            } else {
                                // loop modules
                                foreach ($modules as $module) {
                                    // determine if this is a module specific locale
                                    if (substr($match, 0, mb_strlen($module)) == \SpoonFilter::toCamelCase(
                                            $module
                                        ) && mb_strlen($match) > mb_strlen($module) && ctype_upper(
                                            substr($match, mb_strlen($module) + 1, 1)
                                        )
                                    ) {
                                        // cleanup
                                        $match = str_replace(\SpoonFilter::toCamelCase($module), '', $match);

                                        // init if needed
                                        if (!isset($used[$type][$match])) $used[$type][$match] = array(
                                            'files' => array(),
                                            'module_specific' => array()
                                        );

                                        // add module
                                        $used[$type][$match]['module_specific'][] = $module;
                                    }
                                }
                            }

                            // init if needed
                            if (!isset($used[$type][$match])) $used[$type][$match] = array(
                                'files' => array(),
                                'module_specific' => array()
                            );

                            // add file
                            if (!in_array($file->getRealPath(), $used[$type][$match]['files'])) {
                                $used[$type][$match]['files'][] = $file->getRealPath();
                            }
                        }
                    }
                    break;

                // template file
                case 'tpl':
                    $matches = array();

                    // get matches
                    preg_match_all('/\{\$(act|err|lbl|msg)([A-Z][a-zA-Z_]*)(\|.*)?\}/U', $content, $matches);

                    // any matches?
                    if (isset($matches[2])) {
                        // loop matches
                        foreach ($matches[2] as $key => $match) {
                            // set type
                            $type = $matches[1][$key];

                            // loop modules
                            foreach ($modules as $module) {
                                // determine if this is a module specific locale
                                if (substr($match, 0, mb_strlen($module)) == \SpoonFilter::toCamelCase(
                                        $module
                                    ) && mb_strlen($match) > mb_strlen($module)
                                ) {
                                    // cleanup
                                    $match = str_replace(\SpoonFilter::toCamelCase($module), '', $match);

                                    // init if needed
                                    if (!isset($used[$type][$match])) $used[$type][$match] = array(
                                        'files' => array(),
                                        'module_specific' => array()
                                    );

                                    // add module
                                    $used[$type][$match]['module_specific'][] = $module;
                                }
                            }

                            // init if needed
                            if (!isset($used[$type][$match])) $used[$type][$match] = array(
                                'files' => array(),
                                'module_specific' => array()
                            );

                            // add file
                            if (!in_array($file->getRealPath(), $used[$type][$match]['files'])) {
                                $used[$type][$match]['files'][] = $file->getRealPath();
                            }
                        }
                    }
                    break;
            }
        }

        // init var
        $nonExisting = array();

        // check if the locale is present in the current language
        foreach ($used as $type => $items) {
            // loop items
            foreach ($items as $key => $data) {
                // process based on type
                switch ($type) {
                    // error
                    case 'err':
                        // module specific?
                        if (!empty($data['module_specific'])) {
                            // loop modules
                            foreach ($data['module_specific'] as $module) {

                                // if the error isn't found add it to the list
                                if (substr_count(
                                        BL::err($key, $module),
                                        '{$' . $type
                                    ) > 0
                                ) $nonExisting['Backend' . $key . $type . $module] = array(
                                    'language' => $language,
                                    'application' => 'Backend',
                                    'module' => $module,
                                    'type' => $type,
                                    'name' => $key,
                                    'used_in' => serialize(
                                        $data['files']
                                    )
                                );
                            }
                        } // not specific
                        else {
                            // if the error isn't found add it to the list
                            if (substr_count(BL::err($key), '{$' . $type) > 0) {
                                // init var
                                $exists = false;

                                // loop files
                                foreach ($data['files'] as $file) {
                                    // init var
                                    $count = 0;

                                    // replace
                                    $modulePath = str_replace(
                                        realpath(BACKEND_MODULES_PATH),
                                        '',
                                        realpath($file),
                                        $count
                                    );

                                    // validate
                                    if ($count == 1) {
                                        // split into chunks
                                        $chunks = (array) explode('/', trim($modulePath, '/'));

                                        // first part is the module
                                        if (isset($chunks[0]) && BL::err(
                                                                     $key,
                                                                     $chunks[0]
                                                                 ) != '{$' . $type . \SpoonFilter::toCamelCase(
                                                $chunks[0]
                                            ) . $key . '}'
                                        ) $exists = true;
                                    }
                                }

                                // doesn't exists
                                if (!$exists) $nonExisting['Backend' . $key . $type . 'Core'] = array(
                                    'language' => $language,
                                    'application' => 'Backend',
                                    'module' => 'Core',
                                    'type' => $type,
                                    'name' => $key,
                                    'used_in' => serialize(
                                        $data['files']
                                    )
                                );
                            }
                        }
                        break;

                    // label
                    case 'lbl':
                        // module specific?
                        if (!empty($data['module_specific'])) {
                            // loop modules
                            foreach ($data['module_specific'] as $module) {
                                // if the label isn't found add it to the list
                                if (substr_count(
                                        BL::lbl($key, $module),
                                        '{$' . $type
                                    ) > 0
                                ) $nonExisting['Backend' . $key . $type . $module] = array(
                                    'language' => $language,
                                    'application' => 'Backend',
                                    'module' => $module,
                                    'type' => $type,
                                    'name' => $key,
                                    'used_in' => serialize(
                                        $data['files']
                                    )
                                );
                            }
                        } // not specific
                        else {
                            // if the label isn't found, check in the specific module
                            if (substr_count(BL::lbl($key), '{$' . $type) > 0) {
                                // init var
                                $exists = false;

                                // loop files
                                foreach ($data['files'] as $file) {
                                    // init var
                                    $count = 0;

                                    // replace
                                    $modulePath = str_replace(
                                        realpath(BACKEND_MODULES_PATH),
                                        '',
                                        realpath($file),
                                        $count
                                    );

                                    // validate
                                    if ($count == 1) {
                                        // split into chunks
                                        $chunks = (array) explode('/', trim($modulePath, '/'));

                                        // first part is the module
                                        if (isset($chunks[0]) && BL::lbl(
                                                                     $key,
                                                                     $chunks[0]
                                                                 ) != '{$' . $type . \SpoonFilter::toCamelCase(
                                                $chunks[0]
                                            ) . $key . '}'
                                        ) $exists = true;
                                    }
                                }

                                // doesn't exists
                                if (!$exists) $nonExisting['Backend' . $key . $type . 'Core'] = array(
                                    'language' => $language,
                                    'application' => 'Backend',
                                    'module' => 'Core',
                                    'type' => $type,
                                    'name' => $key,
                                    'used_in' => serialize(
                                        $data['files']
                                    )
                                );
                            }
                        }
                        break;

                    // message
                    case 'msg':
                        // module specific?
                        if (!empty($data['module_specific'])) {
                            // loop modules
                            foreach ($data['module_specific'] as $module) {
                                // if the message isn't found add it to the list
                                if (substr_count(BL::msg($key, $module), '{$' . $type) > 0) {
                                    $nonExisting['Backend' . $key . $type . $module] = array(
                                        'language' => $language,
                                        'application' => 'Backend',
                                        'module' => $module,
                                        'type' => $type,
                                        'name' => $key,
                                        'used_in' => serialize($data['files'])
                                    );
                                }
                            }
                        } // not specific
                        else {
                            // if the message isn't found add it to the list
                            if (substr_count(BL::msg($key), '{$' . $type) > 0) {
                                // init var
                                $exists = false;

                                // loop files
                                foreach ($data['files'] as $file) {
                                    // init var
                                    $count = 0;

                                    // replace
                                    $modulePath = str_replace(
                                        realpath(BACKEND_MODULES_PATH),
                                        '',
                                        realpath($file),
                                        $count
                                    );

                                    // validate
                                    if ($count == 1) {
                                        // split into chunks
                                        $chunks = (array) explode('/', trim($modulePath, '/'));

                                        // first part is the module
                                        if (isset($chunks[0]) && BL::msg(
                                                                     $key,
                                                                     $chunks[0]
                                                                 ) != '{$' . $type . \SpoonFilter::toCamelCase(
                                                $chunks[0]
                                            ) . $key . '}'
                                        ) $exists = true;
                                    }
                                }

                                // doesn't exists
                                if (!$exists) $nonExisting['Backend' . $key . $type . 'Core'] = array(
                                    'language' => $language,
                                    'application' => 'Backend',
                                    'module' => 'Core',
                                    'type' => $type,
                                    'name' => $key,
                                    'used_in' => serialize(
                                        $data['files']
                                    )
                                );
                            }
                        }
                        break;
                }
            }
        }

        var_dump(count($nonExisting));exit;

        ksort($nonExisting);

        // return
        return $nonExisting;
    }

    /**
     * Get the locale that is used in the frontend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function getNonExistingFrontendLocale($language)
    {
        $used = array();
        $finder = new Finder();
        $finder->notPath('cache')
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.js');

        // loop files
        foreach ($finder->files()->in(FRONTEND_PATH) as $file) {
            /** @var $file \SplFileInfo */
            // grab content
            $content = $file->getContents();

            // process the file based on extension
            switch ($file->getExtension()) {
                // javascript file
                case 'js':
                    $matches = array();

                    // get matches
                    preg_match_all('/\{\$(act|err|lbl|msg)(.*)(\|.*)?\}/iU', $content, $matches);

                    // any matches?
                    if (isset($matches[2])) {
                        // loop matches
                        foreach ($matches[2] as $key => $match) {
                            // set type
                            $type = $matches[1][$key];

                            // init if needed
                            if (!isset($used[$match])) $used[$type][$match] = array('files' => array());

                            // add file
                            if (!in_array($file->getRealPath(), $used[$type][$match]['files'])) {
                                $used[$type][$match]['files'][] = $file->getRealPath();
                            }
                        }
                    }
                    break;

                // PHP file
                case 'php':
                    $matches = array();

                    // get matches
                    preg_match_all(
                        '/(FrontendLanguage|FL)::(get(Action|Label|Error|Message)|act|lbl|err|msg)\(\'(.*)\'\)/iU',
                        $content,
                        $matches
                    );

                    // any matches?
                    if (!empty($matches[4])) {
                        // loop matches
                        foreach ($matches[4] as $key => $match) {
                            $type = 'lbl';
                            if ($matches[3][$key] == 'Action') $type = 'act';
                            if ($matches[2][$key] == 'act') $type = 'act';
                            if ($matches[3][$key] == 'Error') $type = 'err';
                            if ($matches[2][$key] == 'err') $type = 'err';
                            if ($matches[3][$key] == 'Message') $type = 'msg';
                            if ($matches[2][$key] == 'msg') $type = 'msg';

                            // init if needed
                            if (!isset($used[$type][$match])) $used[$type][$match] = array('files' => array());

                            // add file
                            if (!in_array($file->getRealPath(), $used[$type][$match]['files'])) {
                                $used[$type][$match]['files'][] = $file->getRealPath();
                            }
                        }
                    }
                    break;

                // template file
                case 'tpl':
                    $matches = array();

                    // get matches
                    preg_match_all('/\{\$(act|err|lbl|msg)([a-z-_]*)(\|.*)?\}/iU', $content, $matches);

                    // any matches?
                    if (isset($matches[2])) {
                        // loop matches
                        foreach ($matches[2] as $key => $match) {
                            // set type
                            $type = $matches[1][$key];

                            // init if needed
                            if (!isset($used[$type][$match])) $used[$type][$match] = array('files' => array());

                            // add file
                            if (!in_array($file->getRealPath(), $used[$type][$match]['files'])) {
                                $used[$type][$match]['files'][] = $file->getRealPath();
                            }
                        }
                    }
                    break;
            }
        }

        // init var
        $nonExisting = array();

        // set language
        FL::setLocale($language);

        // check if the locale is present in the current language
        foreach ($used as $type => $items) {
            // loop items
            foreach ($items as $key => $data) {
                // process based on type
                switch ($type) {
                    case 'act':
                        // if the action isn't available add it to the list
                        if (FL::act(
                                $key,
                                false
                            ) == '{$' . $type . $key . '}'
                        ) $nonExisting['Frontend' . $key . $type] = array(
                            'language' => $language,
                            'application' => 'Frontend',
                            'module' => 'Core',
                            'type' => $type,
                            'name' => $key,
                            'used_in' => serialize($data['files'])
                        );
                        break;

                    case 'err':
                        // if the error isn't available add it to the list
                        if (FL::err(
                                $key,
                                false
                            ) == '{$' . $type . $key . '}'
                        ) $nonExisting['Frontend' . $key . $type] = array(
                            'language' => $language,
                            'application' => 'Frontend',
                            'module' => 'Core',
                            'type' => $type,
                            'name' => $key,
                            'used_in' => serialize($data['files'])
                        );
                        break;

                    case 'lbl':
                        // if the label isn't available add it to the list
                        if (FL::lbl(
                                $key,
                                false
                            ) == '{$' . $type . $key . '}'
                        ) $nonExisting['Frontend' . $key . $type] = array(
                            'language' => $language,
                            'application' => 'Frontend',
                            'module' => 'Core',
                            'type' => $type,
                            'name' => $key,
                            'used_in' => serialize($data['files'])
                        );
                        break;

                    case 'msg':
                        // if the message isn't available add it to the list
                        if (FL::msg(
                                $key,
                                false
                            ) == '{$' . $type . $key . '}'
                        ) $nonExisting['Frontend' . $key . $type] = array(
                            'language' => $language,
                            'application' => 'Frontend',
                            'module' => 'Core',
                            'type' => $type,
                            'name' => $key,
                            'used_in' => serialize($data['files'])
                        );
                        break;
                }
            }
        }

        ksort($nonExisting);

        return $nonExisting;
    }

}
