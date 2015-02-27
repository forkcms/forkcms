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
class Model
{
    /**
     * Build the language files
     *
     * @param string $language    The language to build the locale-file for.
     * @param string $application The application to build the locale-file for.
     */
    public static function buildCache($language, $application)
    {
        $cacheBuilder = new CacheBuilder(BackendModel::get('database'));
        $cacheBuilder->buildCache($language, $application);
    }

    /**
     * Build a query for the URL based on the filter
     *
     * @param array $filter The filter.
     * @return string
     */
    public static function buildURLQueryByFilter($filter)
    {
        $query = http_build_query($filter);
        if ($query != '') $query = '&' . $query;

        return $query;
    }

    /**
     * Create an XML-string that can be used for export.
     *
     * @param array $items The items.
     * @return string
     */
    public static function createXMLForExport(array $items)
    {
        $xml = new \DOMDocument('1.0', SPOON_CHARSET);

        // set some properties
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        // locale root element
        $root = $xml->createElement('locale');
        $xml->appendChild($root);

        // loop applications
        foreach ($items as $application => $modules) {
            // create application element
            $applicationElement = $xml->createElement($application);
            $root->appendChild($applicationElement);

            // loop modules
            foreach ($modules as $module => $types) {
                // create application element
                $moduleElement = $xml->createElement($module);
                $applicationElement->appendChild($moduleElement);

                // loop types
                foreach ($types as $type => $items) {
                    // loop items
                    foreach ($items as $name => $translations) {
                        // create application element
                        $itemElement = $xml->createElement('item');
                        $moduleElement->appendChild($itemElement);

                        // attributes
                        $itemElement->setAttribute('type', static::getTypeName($type));
                        $itemElement->setAttribute('name', $name);

                        // loop translations
                        foreach ($translations as $translation) {
                            // create translation
                            $translationElement = $xml->createElement('translation');
                            $itemElement->appendChild($translationElement);

                            // attributes
                            $translationElement->setAttribute('language', $translation['language']);

                            // set content
                            $translationElement->appendChild(new \DOMCdataSection($translation['value']));
                        }
                    }
                }
            }
        }

        return $xml->saveXML();
    }

    /**
     * Delete (multiple) items from locale
     *
     * @param array $ids The id(s) to delete.
     */
    public static function delete(array $ids)
    {
        // loop and cast to integers
        foreach ($ids as &$id) $id = (int) $id;

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($ids), '?');

        // delete records
        BackendModel::getContainer()->get('database')->delete(
            'locale',
            'id IN (' . implode(', ', $idPlaceHolders) . ')',
            $ids
        );

        // rebuild cache
        static::buildCache(BL::getWorkingLanguage(), 'Backend');
        static::buildCache(BL::getWorkingLanguage(), 'Frontend');
    }

    /**
     * Does an id exist.
     *
     * @param int $id The id to check for existence.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM locale
             WHERE id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Does a locale exists by its name.
     *
     * @param string $name        The name of the locale.
     * @param string $type        The type of the locale.
     * @param string $module      The module wherein will be searched.
     * @param string $language    The language to use.
     * @param string $application The application wherein will be searched.
     * @param int    $id          The id to exclude in the check.
     * @return bool
     */
    public static function existsByName($name, $type, $module, $language, $application, $id = null)
    {
        $name = (string) $name;
        $type = (string) $type;
        $module = (string) $module;
        $language = (string) $language;
        $application = (string) $application;
        $id = ($id !== null) ? (int) $id : null;

        // get db
        $db = BackendModel::getContainer()->get('database');

        // return
        if ($id !== null) return (bool) $db->getVar(
            'SELECT 1
             FROM locale
             WHERE name = ? AND type = ? AND module = ? AND language = ? AND application = ? AND id != ?
             LIMIT 1',
            array($name, $type, $module, $language, $application, $id)
        );

        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM locale
             WHERE name = ? AND type = ? AND module = ? AND language = ? AND application = ?
             LIMIT 1',
            array($name, $type, $module, $language, $application)
        );
    }

    /**
     * Get a single item from locale.
     *
     * @param int $id The id of the item to get.
     * @return array
     */
    public static function get($id)
    {
        // fetch record from db
        $record = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT * FROM locale WHERE id = ?',
            array((int) $id)
        );

        // actions are urlencoded
        if ($record['type'] == 'act') $record['value'] = urldecode($record['value']);

        return $record;
    }

    /**
     * Get a locale by its name
     *
     * @param string $name        The name of the locale.
     * @param string $type        The type of the locale.
     * @param string $module      The module wherein will be searched.
     * @param string $language    The language to use.
     * @param string $application The application wherein will be searched.
     * @return bool
     */
    public static function getByName($name, $type, $module, $language, $application)
    {
        $name = (string) $name;
        $type = (string) $type;
        $module = (string) $module;
        $language = (string) $language;
        $application = (string) $application;

        return BackendModel::getContainer()->get('database')->getVar(
            'SELECT l.id
             FROM locale AS l
             WHERE name = ? AND type = ? AND module = ? AND language = ? AND application = ?',
            array($name, $type, $module, $language, $application)
        );
    }

    /**
     * Grab labels found in the backend navigation.
     *
     * @return array
     */
    private static function getLabelsFromBackendNavigation()
    {
        return (array) BackendModel::getContainer()->get('database')->getColumn('SELECT label FROM backend_navigation');
    }

    /**
     * Get the languages for a multicheckbox.
     *
     * @param bool $includeInterfaceLanguages Should we also get the interfacelanguages?
     * @return array
     */
    public static function getLanguagesForMultiCheckbox($includeInterfaceLanguages = false)
    {
        // get working languages
        $aLanguages = BL::getWorkingLanguages();

        // add the interface languages if needed
        if ($includeInterfaceLanguages) $aLanguages = array_merge($aLanguages, BL::getInterfaceLanguages());

        // create a new array to redefine the languages for the multicheckbox
        $languages = array();

        // loop the languages
        foreach ($aLanguages as $key => $lang) {
            // add to array
            $languages[$key]['value'] = $key;
            $languages[$key]['label'] = $lang;
        }

        return $languages;
    }

    /**
     * Get the locale that is used in the backend but doesn't exists.
     *
     * @param string $language The language to check.
     * @return array
     */
    public static function getNonExistingBackendLocale($language)
    {
        $modules = BackendModel::getModules();

        // search fo the error module
        $key = array_search('error', $modules);

        // remove error module
        if ($key !== false) unset($modules[$key]);

        $used = array();

        // get labels from navigation
        $lbl = static::getLabelsFromBackendNavigation();
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
                                $specificModule = trim(str_replace(array(',', '\''), '', $specificModule));

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

    /**
     * Get the translations
     *
     * @param string $application The application.
     * @param string $module      The module.
     * @param array  $types       The types of the translations to get.
     * @param array  $languages   The languages of the translations to get.
     * @param string $name        The name.
     * @param string $value       The value.
     * @return array
     */
    public static function getTranslations($application, $module, $types, $languages, $name, $value)
    {
        $languages = (array) $languages;

        // create an array for the languages, surrounded by quotes (example: 'en')
        $aLanguages = array();
        foreach ($languages as $key => $val) {
            $aLanguages[$key] = '\'' . $val . '\'';
        }

        // surround the types with quotes
        foreach ($types as $key => $val) {
            $types[$key] = '\'' . $val . '\'';
        }

        // get db
        $db = BackendModel::getContainer()->get('database');

        // build the query
        $query =
            'SELECT l.id, l.application, l.module, l.type, l.name, l.value, l.language, UNIX_TIMESTAMP(l.edited_on) as edited_on
             FROM locale AS l
             WHERE
                 l.language IN (' . implode(',', $aLanguages) . ') AND
                 l.name LIKE ? AND
                 l.value LIKE ? AND
                 l.type IN (' . implode(',', $types) . ')';

        // add the parameters
        $parameters = array('%' . $name . '%', '%' . $value . '%');

        // add module to the query if needed
        if ($module) {
            $query .= ' AND l.module = ?';
            $parameters[] = $module;
        }

        // add module to the query if needed
        if ($application) {
            $query .= ' AND l.application = ?';
            $parameters[] = $application;
        }

        // get the translations
        $translations = (array) $db->getRecords($query, $parameters);

        // create an array for the sorted translations
        $sortedTranslations = array();

        // loop translations
        foreach ($translations as $translation) {
            // add to the sorted array
            $sortedTranslations[$translation['type']][$translation['name']][$translation['module']][$translation['language']] = array(
                'id' => $translation['id'],
                'value' => $translation['value'],
                'edited_on' => $translation['edited_on'],
                'application' => $translation['application'],
            );
        }

        // create an array to use in the datagrid
        $dataGridTranslations = array();

        // an id that is used for in the datagrid, this is not the id of the translation!
        $id = 0;

        // save the number of languages so this has not to be executed x number of times
        $numberOfLanguages = count($languages);

        // loop the sorted translations
        foreach ($sortedTranslations as $type => $references) {
            // create array for each type
            $dataGridTranslations[$type] = array();

            foreach ($references as $reference => $translation) {
                // loop modules
                foreach ($translation as $module => $t) {
                    // create translation (and increase id)
                    // we init the application here so it appears in front of the datagrid
                    $trans = array(
                        'application' => '',
                        'module' => $module,
                        'name' => $reference,
                        'id' => $id++
                    );

                    // reset this var for every language
                    $edited_on = '';

                    foreach ($languages as $lang) {
                        // if the translation exists the for this language, fill it up
                        // else leave a space for the empty field
                        if (isset($t[$lang])) {
                            $trans[$lang] = $t[$lang]['value'];
                            $trans['application'] = $t[$lang]['application'];

                            // only alter edited_on if the date of a previously added date of another
                            // language is smaller
                            if ($edited_on < $t[$lang]['edited_on']) {
                                $edited_on = $t[$lang]['edited_on'];
                            }

                            if ($numberOfLanguages == 1) {
                                $trans['translation_id'] = $t[$lang]['id'];
                            } else {
                                $trans['translation_id_' . $lang] = $t[$lang]['id'];
                            }
                        } else {
                            $trans[$lang] = '';

                            if ($numberOfLanguages == 1) {
                                $trans['translation_id'] = '';
                            } else {
                                $trans['translation_id_' . $lang] = '';
                            }
                        }
                    }
                    // at the end of the array, add the generated edited_on date
                    $trans['edited_on'] = $edited_on;

                    // add the translation to the array
                    $dataGridTranslations[$type][] = $trans;
                }
            }
        }

        return $dataGridTranslations;
    }

    /**
     * Get full type name.
     *
     * @param string $type The type of the locale.
     * @return string
     */
    public static function getTypeName($type)
    {
        // get full type name
        switch ($type) {
            case 'act':
                $type = 'action';
                break;
            case 'err':
                $type = 'error';
                break;
            case 'lbl':
                $type = 'label';
                break;
            case 'msg':
                $type = 'message';
                break;
        }

        return $type;
    }

    /**
     * Get all locale types.
     *
     * @return array
     */
    public static function getTypesForDropDown()
    {
        // fetch types
        $types = BackendModel::getContainer()->get('database')->getEnumValues('locale', 'type');

        // init
        $labels = $types;

        // loop and build labels
        foreach ($labels as &$row) $row = \SpoonFilter::ucfirst(BL::msg(mb_strtoupper($row), 'Core'));

        // build array
        return array_combine($types, $labels);
    }

    /**
     * Get all locale types for a multicheckbox.
     *
     * @return array
     */
    public static function getTypesForMultiCheckbox()
    {
        // fetch types
        $aTypes = BackendModel::getContainer()->get('database')->getEnumValues('locale', 'type');

        // init
        $labels = $aTypes;

        // loop and build labels
        foreach ($labels as &$row) $row = \SpoonFilter::ucfirst(BL::msg(mb_strtoupper($row), 'Core'));

        // build array
        $aTypes = array_combine($aTypes, $labels);

        // create a new array to redefine the types for the multicheckbox
        $types = array();

        // loop the languages
        foreach ($aTypes as $key => $type) {
            // add to array
            $types[$key]['value'] = $key;
            $types[$key]['label'] = $type;
        }

        // return the redefined array
        return $types;
    }

    /**
     * Import a locale XML file.
     *
     * @param \SimpleXMLElement $xml                The locale XML.
     * @param bool              $overwriteConflicts Should we overwrite when there is a conflict?
     * @param array             $frontendLanguages  The frontend languages to install locale for.
     * @param array             $backendLanguages   The backend languages to install locale for.
     * @param int               $userId             Id of the user these translations should be inserted for.
     * @param int               $date               The date the translation has been inserted.
     * @return array The import statistics
     */
    public static function importXML(
        \SimpleXMLElement $xml,
        $overwriteConflicts = false,
        $frontendLanguages = null,
        $backendLanguages = null,
        $userId = null,
        $date = null
    ) {
        $overwriteConflicts = (bool) $overwriteConflicts;
        $statistics = array(
            'total' => 0,
            'imported' => 0
        );

        // set defaults if necessary
        // we can't simply use these right away, because this function is also calls by the installer,
        // which does not have Backend-functions
        if ($frontendLanguages === null) $frontendLanguages = array_keys(BL::getWorkingLanguages());
        if ($backendLanguages === null) $backendLanguages = array_keys(BL::getInterfaceLanguages());
        if ($userId === null) $userId = BackendAuthentication::getUser()->getUserId();
        if ($date === null) $date = BackendModel::getUTCDate();

        // get database instance
        $db = BackendModel::getContainer()->get('database');

        // possible values
        $possibleApplications = array('Frontend', 'Backend');
        $possibleModules = (array) $db->getColumn('SELECT m.name FROM modules AS m');

        // types
        $typesShort = (array) $db->getEnumValues('locale', 'type');
        foreach ($typesShort as $type) $possibleTypes[$type] = static::getTypeName($type);

        // install English translations anyhow, they're fallback
        $possibleLanguages = array(
            'Frontend' => array_unique(array_merge(array('en'), $frontendLanguages)),
            'Backend' => array_unique(array_merge(array('en'), $backendLanguages))
        );

        // current locale items (used to check for conflicts)
        $currentLocale = (array) $db->getColumn(
            'SELECT CONCAT(application, module, type, language, name)
             FROM locale'
        );

        // applications
        foreach ($xml as $application => $modules) {
            // application does not exist
            if (!in_array($application, $possibleApplications)) continue;

            // modules
            foreach ($modules as $module => $items) {
                // module does not exist
                if (!in_array($module, $possibleModules)) continue;

                // items
                foreach ($items as $item) {
                    // attributes
                    $attributes = $item->attributes();
                    $type = \SpoonFilter::getValue($attributes['type'], $possibleTypes, '');
                    $name = \SpoonFilter::getValue($attributes['name'], null, '');

                    // missing attributes
                    if ($type == '' || $name == '') continue;

                    // real type (shortened)
                    $type = array_search($type, $possibleTypes);

                    // translations
                    foreach ($item->translation as $translation) {
                        // statistics
                        $statistics['total']++;

                        // attributes
                        $attributes = $translation->attributes();
                        $language = \SpoonFilter::getValue(
                            $attributes['language'],
                            $possibleLanguages[$application],
                            ''
                        );

                        // language does not exist
                        if ($language == '') continue;

                        // the actual translation
                        $translation = (string) $translation;

                        // locale item
                        $locale['user_id'] = $userId;
                        $locale['language'] = $language;
                        $locale['application'] = $application;
                        $locale['module'] = $module;
                        $locale['type'] = $type;
                        $locale['name'] = $name;
                        $locale['value'] = $translation;
                        $locale['edited_on'] = $date;

                        // check if translation does not yet exist, or if the translation can be overridden
                        if (!in_array(
                                $application . $module . $type . $language . $name,
                                $currentLocale
                            ) || $overwriteConflicts
                        ) {
                            $db->execute(
                                'INSERT INTO locale (user_id, language, application, module, type, name, value, edited_on)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                 ON DUPLICATE KEY UPDATE user_id = ?, value = ?, edited_on = ?',
                                array(
                                    $locale['user_id'],
                                    $locale['language'],
                                    $locale['application'],
                                    $locale['module'],
                                    $locale['type'],
                                    $locale['name'],
                                    $locale['value'],
                                    $locale['edited_on'],
                                    $locale['user_id'],
                                    $locale['value'],
                                    $locale['edited_on']
                                )
                            );

                            // statistics
                            $statistics['imported']++;
                        }
                    }
                }
            }
        }

        // rebuild cache
        foreach ($possibleApplications as $application) {
            foreach ($possibleLanguages[$application] as $language) static::buildCache($language, $application);
        }

        return $statistics;
    }

    /**
     * Insert a new locale item.
     *
     * @param array $item The data to insert.
     * @return int
     */
    public static function insert(array $item)
    {
        // actions should be urlized
        if ($item['type'] == 'act' && urldecode($item['value']) != $item['value']) $item['value'] = CommonUri::getUrl(
            $item['value']
        );

        // insert item
        $item['id'] = (int) BackendModel::getContainer()->get('database')->insert('locale', $item);

        // rebuild the cache
        static::buildCache($item['language'], $item['application']);

        // return the new id
        return $item['id'];
    }

    /**
     * Update a locale item.
     *
     * @param array $item The new data.
     */
    public static function update(array $item)
    {
        // actions should be urlized
        if ($item['type'] == 'act' && urldecode($item['value']) != $item['value']) $item['value'] = CommonUri::getUrl(
            $item['value']
        );

        // update category
        $updated = BackendModel::getContainer()->get('database')->update('locale', $item, 'id = ?', array($item['id']));

        // rebuild the cache
        static::buildCache($item['language'], $item['application']);

        return $updated;
    }
}
