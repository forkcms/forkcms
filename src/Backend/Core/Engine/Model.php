<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use TijsVerkoyen\Akismet\Akismet;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Core\Engine\Model as BackendModel;
use Frontend\Core\Engine\Language as FrontendLanguage;

/**
 * In this file we store all generic functions that we will be using in the backend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Model extends \Common\Core\Model
{
    /**
     * Allowed module extras types
     *
     * @var    array
     */
    private static $allowedExtras = array('homepage', 'block', 'widget');

    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings()
    {
        $warnings = array();

        // check if debug-mode is active
        if (BackendModel::getContainer()->getParameter('kernel.debug')) {
            $warnings[] = array('message' => Language::err('DebugModeIsActive'));
        }

        // check if this action is allowed
        if (Authentication::isAllowedAction('Index', 'Settings')) {
            // check if the fork API keys are available
            if (self::get('fork.settings')->get('Core', 'fork_api_private_key') == '' ||
                self::get('fork.settings')->get('Core', 'fork_api_public_key') == ''
            ) {
                $warnings[] = array(
                    'message' => sprintf(
                        Language::err('ForkAPIKeys'),
                        self::createURLForAction('Index', 'Settings')
                    )
                );
            }
        }

        // check for extensions warnings
        $warnings = array_merge($warnings, BackendExtensionsModel::checkSettings());

        return $warnings;
    }

    /**
     * Creates an URL for a given action and module
     * If you don't specify an action the current action will be used.
     * If you don't specify a module the current module will be used.
     * If you don't specify a language the current language will be used.
     *
     * @param string $action     The action to build the URL for.
     * @param string $module     The module to build the URL for.
     * @param string $language   The language to use, if not provided we will use the working language.
     * @param array  $parameters GET-parameters to use.
     * @param bool   $urlencode  Should the parameters be urlencoded?
     *
     * @return string
     *
     * @throws \Exception If $action, $module or both are not set
     */
    public static function createURLForAction(
        $action = null,
        $module = null,
        $language = null,
        array $parameters = null,
        $urlencode = true
    ) {
        // redefine variables
        $action = ($action !== null) ? (string) $action : null;
        $module = ($module !== null) ? (string) $module : null;
        $language = ($language !== null) ? (string) $language : Language::getWorkingLanguage();
        $queryString = '';

        // checking if we have an url, because in a cronjob we don't have one
        if (self::getContainer()->has('url')) {
            // grab the URL from the reference
            $URL = self::getContainer()->get('url');

            // redefine
            if ($action === null) {
                $action = $URL->getAction();
            }
            if ($module === null) {
                $module = $URL->getModule();
            }
        }

        // error checking
        if ($action === null || $module === null) {
            throw new \Exception('Action and Module must not be empty when creating an URL.');
        }

        // lets create underscore cased module and action names
        $module = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $module));
        $action = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $action));

        // add offset, order & sort (only if not yet manually added)
        if (isset($_GET['offset']) && !isset($parameters['offset'])) {
            $parameters['offset'] = (int) $_GET['offset'];
        }
        if (isset($_GET['order']) && !isset($parameters['order'])) {
            $parameters['order'] = (string) $_GET['order'];
        }
        if (isset($_GET['sort']) && !isset($parameters['sort'])) {
            $parameters['sort'] = (string) $_GET['sort'];
        }

        // add at least one parameter
        $parameters['token'] = self::getToken();

        // add parameters
        $i = 1;
        foreach ($parameters as $key => $value) {
            // first element
            if ($i == 1) {
                $queryString .= '?' . $key . '=' . (($urlencode) ? urlencode($value) : $value);
            } else {
                $queryString .= '&' . $key . '=' . (($urlencode) ? urlencode($value) : $value);
            }

            $i++;
        }

        // build the URL and return it
        return self::get('router')->generate(
            'backend',
            array(
                '_locale' => $language,
                'module'  => $module,
                'action'  => $action
            )
        ) . $queryString;
    }

    /**
     * Delete a page extra by module, type or data.
     *
     * Data is a key/value array. Example: array(id => 23, language => nl);
     *
     * @param string $module The module wherefore the extra exists.
     * @param string $type   The type of extra, possible values are block, homepage, widget.
     * @param array  $data   Extra data that exists.
     */
    public static function deleteExtra($module = null, $type = null, array $data = null)
    {
        // init
        $query = 'SELECT i.id, i.data FROM modules_extras AS i WHERE 1';
        $parameters = array();

        // module
        if ($module !== null) {
            $query .= ' AND i.module = ?';
            $parameters[] = (string) $module;
        }

        // type
        if ($type !== null) {
            $query .= ' AND i.type = ?';
            $parameters[] = (string) $type;
        }

        // get extras
        $extras = (array) self::getContainer()->get('database')->getRecords($query, $parameters);

        // loop found extras
        foreach ($extras as $extra) {
            $deleteExtra = true;

            // match by parameters
            if ($data !== null && $extra['data'] !== null) {
                $extraData = (array) unserialize($extra['data']);

                // do not delete extra if parameters do not match
                foreach ($data as $dataKey => $dataValue) {
                    if (isset($extraData[$dataKey]) && $dataValue != $extraData[$dataKey]) {
                        $deleteExtra = false;
                    }
                }
            }

            // delete extra
            if ($deleteExtra) {
                self::deleteExtraById($extra['id']);
            }
        }
    }

    /**
     * Delete a page extra by its id
     *
     * @param int  $id          The id of the extra to delete.
     * @param bool $deleteBlock Should the block be deleted? Default is false.
     */
    public static function deleteExtraById($id, $deleteBlock = false)
    {
        $id = (int) $id;
        $deleteBlock = (bool) $deleteBlock;

        // delete the blocks
        if ($deleteBlock) {
            self::getContainer()->get('database')->delete('pages_blocks', 'extra_id = ?', $id);
        } else {
            self::getContainer()->get('database')->update(
                'pages_blocks',
                array('extra_id' => null),
                'extra_id = ?',
                $id
            );
        }

        // delete extra
        self::getContainer()->get('database')->delete('modules_extras', 'id = ?', $id);
    }

    /**
     * Delete all extras for a certain value in the data array of that module_extra.
     *
     * @param string $module The module for the extra.
     * @param string $field  The field of the data you want to check the value for.
     * @param string $value  The value to check the field for.
     * @param string $action In case you want to search for a certain action.
     */
    public static function deleteExtrasForData($module, $field, $value, $action = null)
    {
        $ids = self::getExtrasForData((string) $module, (string) $field, (string) $value, $action);

        // we have extras
        if (!empty($ids)) {
            // delete extras
            self::getContainer()->get('database')->delete('modules_extras', 'id IN (' . implode(',', $ids) . ')');

            // invalidate the cache for the module
            self::invalidateFrontendCache((string) $module, Language::getWorkingLanguage());
        }
    }

    /**
     * Deletes a module-setting from the DB and the cached array
     *
     * @deprecated
     * @param string $module The module to set the setting for.
     * @param string $key    The name of the setting.
     */
    public static function deleteModuleSetting($module, $key)
    {
        trigger_error(
            'BackendModel::deleteModuleSetting is deprecated.
             Use $container->get(\'fork.settings\')->delete instead',
            E_USER_DEPRECATED
        );

        return self::get('fork.settings')->delete($module, $key);
    }

    /**
     * Delete thumbnails based on the folders in the path
     *
     * @param string $path      The path wherein the thumbnail-folders exist.
     * @param string $thumbnail The filename to be deleted.
     */
    public static function deleteThumbnails($path, $thumbnail)
    {
        // if there is no image provided we can't do anything
        if ($thumbnail == '') {
            return;
        }

        $finder = new Finder();
        $fs = new Filesystem();
        foreach ($finder->directories()->in($path) as $directory) {
            $fileName = $directory->getRealPath() . '/' . $thumbnail;
            if (is_file($fileName)) {
                $fs->remove($fileName);
            }
        }
    }

    /**
     * Generate a random string
     *
     * @param int  $length    Length of random string.
     * @param bool $numeric   Use numeric characters.
     * @param bool $lowercase Use alphanumeric lowercase characters.
     * @param bool $uppercase Use alphanumeric uppercase characters.
     * @param bool $special   Use special characters.
     * @return string
     */
    public static function generateRandomString(
        $length = 15,
        $numeric = true,
        $lowercase = true,
        $uppercase = true,
        $special = true
    ) {
        $characters = '';
        $string = '';

        // possible characters
        if ($numeric) {
            $characters .= '1234567890';
        }
        if ($lowercase) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($uppercase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($special) {
            $characters .= '-_.:;,?!@#&=)([]{}*+%$';
        }

        // get random characters
        for ($i = 0; $i < $length; $i++) {
            // random index
            $index = mt_rand(0, strlen($characters));

            // add character to salt
            $string .= mb_substr($characters, $index, 1, self::getContainer()->getParameter('kernel.charset'));
        }

        return $string;
    }

    /**
     * Fetch the list of long date formats including examples of these formats.
     *
     * @return array
     */
    public static function getDateFormatsLong()
    {
        $possibleFormats = array();

        // loop available formats
        foreach ((array) self::get('fork.settings')->get('Core', 'date_formats_long') as $format) {
            // get date based on given format
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                Authentication::getUser()->getSetting('interface_language')
            );
        }

        return $possibleFormats;
    }

    /**
     * Fetch the list of short date formats including examples of these formats.
     *
     * @return array
     */
    public static function getDateFormatsShort()
    {
        $possibleFormats = array();

        // loop available formats
        foreach ((array) self::get('fork.settings')->get('Core', 'date_formats_short') as $format) {
            // get date based on given format
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                Authentication::getUser()->getSetting('interface_language')
            );
        }

        return $possibleFormats;
    }

    /**
     * Get extras
     *
     * @param array $ids The ids of the modules_extras to get.
     * @return array
     */
    public static function getExtras($ids)
    {
        // get db
        $db = self::getContainer()->get('database');

        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an array with an equal amount of question marks as ids provided
        $extraIdPlaceHolders = array_fill(0, count($ids), '?');

        // get extras
        return (array) $db->getRecords(
            'SELECT i.*
             FROM modules_extras AS i
             WHERE i.id IN (' . implode(', ', $extraIdPlaceHolders) . ')',
            $ids
        );
    }

    /**
     * Get extras for data
     *
     * @param string $module The module for the extra.
     * @param string $key    The key of the data you want to check the value for.
     * @param string $value  The value to check the key for.
     * @param string $action In case you want to search for a certain action.
     * @return array                    The ids for the extras.
     */
    public static function getExtrasForData($module, $key, $value, $action = null)
    {
        // init variables
        $module = (string) $module;
        $key = (string) $key;
        $value = (string) $value;
        $result = array();

        // init query
        $query = 'SELECT i.id, i.data
                 FROM modules_extras AS i
                 WHERE i.module = ? AND i.data != ?';

        // init parameters
        $parameters = array($module, 'NULL');

        // we have an action
        if ($action) {
            $query .= ' AND i.action = ?';
            $parameters[] = (string) $action;
        }

        // get items
        $items = (array) self::getContainer()->get('database')->getPairs($query, $parameters);

        // stop here when no items
        if (empty($items)) {
            return $result;
        }

        // loop items
        foreach ($items as $id => $data) {
            $data = unserialize($data);
            if (isset($data[$key]) && $data[$key] == $value) {
                $result[] = $id;
            }
        }

        return $result;
    }

    /**
     * Get the page-keys
     *
     * @param string $language The language to use, if not provided we will use the working language.
     * @return array
     */
    public static function getKeys($language = null)
    {
        $language = ($language !== null) ? (string) $language : Language::getWorkingLanguage();

        $cacheBuilder = BackendPagesModel::getCacheBuilder();
        return $cacheBuilder->getKeys($language);
    }

    /**
     * Get the modules that are available on the filesystem
     *
     * @param bool $includeCore Should core be included as a module?
     * @return array
     */
    public static function getModulesOnFilesystem($includeCore = true)
    {
        if ($includeCore) {
            $return = array('Core');
        } else {
            $return = array();
        }
        $finder = new Finder();
        foreach ($finder->directories()->in(PATH_WWW . '/src/Backend/Modules')->depth('==0') as $folder) {
            $return[] = $folder->getBasename();
        }

        return $return;
    }

    /**
     * Get a certain module-setting
     *
     * @deprecated
     * @param string $module       The module in which the setting is stored.
     * @param string $key          The name of the setting.
     * @param mixed  $defaultValue The value to return if the setting isn't present.
     * @return mixed
     */
    public static function getModuleSetting($module, $key, $defaultValue = null)
    {
        trigger_error(
            'BackendModel::getModuleSetting is deprecated.
             Use $container->get(\'fork.settings\')->get instead',
            E_USER_DEPRECATED
        );

        return self::get('fork.settings')->get($module, $key, $defaultValue);
    }

    /**
     * Get all module settings at once
     *
     * @deprecated
     * @param string $module You can get all settings for a module.
     * @return array
     * @throws Exception If the module settings were not saved in a correct format
     */
    public static function getModuleSettings($module = null)
    {
        trigger_error(
            'BackendModel::getModuleSettings is deprecated.
             Use $container->get(\'fork.settings\')->getForModule instead',
            E_USER_DEPRECATED
        );

        return self::get('fork.settings')->getForModule($module);
    }

    /**
     * Fetch the list of modules, but for a dropdown.
     *
     * @return array
     */
    public static function getModulesForDropDown()
    {
        $dropDown = array('Core' => 'Core');

        // fetch modules
        $modules = self::getModules();

        // loop and add into the return-array (with correct label)
        foreach ($modules as $module) {
            $dropDown[$module] = \SpoonFilter::ucfirst(Language::lbl(\SpoonFilter::toCamelCase($module)));
        }

        return $dropDown;
    }

    /**
     * Get the navigation-items
     *
     * @param string $language The language to use, if not provided we will use the working language.
     * @return array
     */
    public static function getNavigation($language = null)
    {
        $language = ($language !== null) ? (string) $language : Language::getWorkingLanguage();

        $cacheBuilder = BackendPagesModel::getCacheBuilder();
        return $cacheBuilder->getNavigation($language);
    }

    /**
     * Fetch the list of number formats including examples of these formats.
     *
     * @return array
     */
    public static function getNumberFormats()
    {
        $possibleFormats = array();

        foreach ((array) self::get('fork.settings')->get('Core', 'number_formats') as $format => $example) {
            $possibleFormats[$format] = $example;
        }

        return $possibleFormats;
    }

    /**
     * Fetch the list of time formats including examples of these formats.
     *
     * @return array
     */
    public static function getTimeFormats()
    {
        $possibleFormats = array();

        foreach (self::get('fork.settings')->get('Core', 'time_formats') as $format) {
            $possibleFormats[$format] = \SpoonDate::getDate(
                $format,
                null,
                Authentication::getUser()->getSetting('interface_language')
            );
        }

        return $possibleFormats;
    }

    /**
     * Get the token which will protect us
     *
     * @return string
     */
    public static function getToken()
    {
        if (\SpoonSession::exists('csrf_token') && \SpoonSession::get('csrf_token') != '') {
            $token = \SpoonSession::get('csrf_token');
        } else {
            $token = self::generateRandomString(10, true, true, false, false);
            \SpoonSession::set('csrf_token', $token);
        }

        return $token;
    }

    /**
     * Get URL for a given pageId
     *
     * @param int    $pageId   The id of the page to get the URL for.
     * @param string $language The language to use, if not provided we will use the working language.
     * @return string
     */
    public static function getURL($pageId, $language = null)
    {
        $pageId = (int) $pageId;
        $language = ($language !== null) ? (string) $language : Language::getWorkingLanguage();

        // init URL
        $URL = (self::getContainer()->getParameter('site.multilanguage')) ? '/' . $language . '/' : '/';

        // get the menuItems
        $keys = self::getKeys($language);

        // get the URL, if it doesn't exist return 404
        if (!isset($keys[$pageId])) {
            return self::getURL(404, $language);
        } else {
            $URL .= $keys[$pageId];
        }

        // return the unique URL!
        return urldecode($URL);
    }

    /**
     * Get the URL for a give module & action combination
     *
     * @param string $module   The module to get the URL for.
     * @param string $action   The action to get the URL for.
     * @param string $language The language to use, if not provided we will use the working language.
     * @return string
     */
    public static function getURLForBlock($module, $action = null, $language = null)
    {
        $module = (string) $module;
        $action = ($action !== null) ? (string) $action : null;
        $language = ($language !== null) ? (string) $language : Language::getWorkingLanguage();

        $pageIdForURL = null;
        $navigation = self::getNavigation($language);

        // loop types
        foreach ($navigation as $level) {
            foreach ($level as $pages) {
                foreach ($pages as $pageId => $properties) {
                    // only process pages with extra_blocks
                    if (!isset($properties['extra_blocks']) || $properties['hidden']) {
                        continue;
                    }

                    // loop extras
                    foreach ($properties['extra_blocks'] as $extra) {
                        if ($extra['module'] == $module && $extra['action'] == $action) {
                            // exact page was found, so return
                            return self::getURL($properties['page_id'], $language);
                        } elseif ($extra['module'] == $module && $extra['action'] == null) {
                            $pageIdForURL = (int) $pageId;
                        }
                    }
                }
            }
        }

        // still no page id?
        if ($pageIdForURL === null) {
            return self::getURL(404);
        }

        $URL = self::getURL($pageIdForURL, $language);

        // set locale with force
        FrontendLanguage::setLocale($language, true);

        // append action
        $URL .= '/' . urldecode(FrontendLanguage::act(\SpoonFilter::toCamelCase($action)));

        // return the unique URL!
        return $URL;
    }

    /**
     * Image Delete
     *
     * @param string $module       Module name.
     * @param string $filename     Filename.
     * @param string $subDirectory Subdirectory.
     * @param array  $fileSizes    Possible file sizes.
     */
    public static function imageDelete($module, $filename, $subDirectory = '', $fileSizes = null)
    {
        if (empty($fileSizes)) {
            $model = get_class_vars('Backend' . \SpoonFilter::toCamelCase($module) . 'Model');
            $fileSizes = $model['fileSizes'];
        }

        $fs = new Filesystem();
        foreach (array_keys($fileSizes) as $sizeDir) {
            $fullPath = FRONTEND_FILES_PATH . '/' . $module .
                        (empty($subDirectory) ? '/' : $subDirectory . '/') . $sizeDir . '/' . $filename;
            if (is_file($fullPath)) {
                $fs->remove($fullPath);
            }
        }
        $fullPath = FRONTEND_FILES_PATH . '/' . $module .
                    (empty($subDirectory) ? '/' : $subDirectory . '/') . 'source/' . $filename;
        if (is_file($fullPath)) {
            $fs->remove($fullPath);
        }
    }

    /**
     * Insert extra
     *
     * @param  string    $type           What type do you want to insert, 'homepage', 'block' or 'widget'.
     * @param  string    $module         The module you are inserting this extra for.
     * @param  string    $action         The action this extra will use.
     * @param  string    $label          Label which will be used when you want to connect this block.
     * @param  array     $data           Containing extra variables.
     * @param  bool      $hidden         Should this extra be visible in frontend or not?
     * @param  int       $sequence
     * @return int       The new extra id
     * @throws Exception If extra type is not allowed
     */
    public static function insertExtra($type, $module, $action = null, $label = null, $data = null, $hidden = false, $sequence = null)
    {
        $type = (string) $type;
        $module = (string) $module;

        // if action and label are empty, fallback to module
        $action = ($action == null) ? $module : (string) $action;
        $label = ($label == null) ? $module : (string) $label;

        // check if type is allowed
        if (!in_array($type, self::$allowedExtras)) {
            throw new Exception(
                'Type is not allowed, choose from "' . implode(', ', self::$allowedExtras) .'".'
            );
        }

        // get database
        $db = self::get('database');

        // sequence not given
        if ($sequence == null) {
            // redefine sequence: get maximum sequence for module
            $sequence = $db->getVar(
                'SELECT MAX(i.sequence) + 1
                 FROM modules_extras AS i
                 WHERE i.module = ?',
                array($module)
            );

            // sequence could not be found for module
            if (is_null($sequence)) {
                // redefine sequence: maximum sequence overall
                $sequence = $db->getVar(
                    'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
                     FROM modules_extras AS i'
                );
            }
        }

        // build extra
        $extra = array(
            'module' => $module,
            'type' => $type,
            'label' => $label,
            'action' => $action,
            'data' => serialize((array) $data),
            'hidden' => ($hidden) ? 'Y' : 'N',
            'sequence' => $sequence
        );

        // return id for inserted extra
        return $db->insert('modules_extras', $extra);
    }

    /**
     * Invalidate cache
     *
     * @param string $module   A specific module to clear the cache for.
     * @param string $language The language to use.
     */
    public static function invalidateFrontendCache($module = null, $language = null)
    {
        $module = ($module !== null) ? (string) $module : null;
        $language = ($language !== null) ? (string) $language : null;

        // get cache path
        $path = FRONTEND_CACHE_PATH . '/CachedTemplates';

        if (is_dir($path)) {
            // build regular expression
            if ($module !== null) {
                if ($language === null) {
                    $regexp = '/' . '(.*)' . $module . '(.*)_cache\.html.twig/i';
                } else {
                    $regexp = '/' . $language . '_' . $module . '(.*)_cache\.html.twig/i';
                }
            } else {
                if ($language === null) {
                    $regexp = '/(.*)_cache\.html.twig/i';
                } else {
                    $regexp = '/' . $language . '_(.*)_cache\.html.twig/i';
                }
            }

            $finder = new Finder();
            $fs = new Filesystem();
            foreach ($finder->files()->name($regexp)->in($path) as $file) {
                $fs->remove($file->getRealPath());
            }
        }

        // clear the php5.5+ opcode cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Is module installed?
     *
     * @param string $module
     * @return bool
     */
    public static function isModuleInstalled($module)
    {
        $modules = self::getModules();

        return (in_array((string) $module, $modules));
    }

    /**
     * Ping the known webservices
     *
     * @param string $pageOrFeedURL The page/feed that has changed.
     * @param string $category      An optional category for the site.
     * @return bool If everything went fne true will, otherwise false.
     */
    public static function ping($pageOrFeedURL = null, $category = null)
    {
        $siteTitle = self::get('fork.settings')->get('Core', 'site_title_' . Language::getWorkingLanguage(), SITE_DEFAULT_TITLE);
        $siteURL = SITE_URL;
        $pageOrFeedURL = ($pageOrFeedURL !== null) ? (string) $pageOrFeedURL : null;
        $category = ($category !== null) ? (string) $category : null;

        // get ping services
        $pingServices = self::get('fork.settings')->get('Core', 'ping_services', null);

        // no ping services available or older than one month ago
        if ($pingServices === null || $pingServices['date'] < strtotime('-1 month')) {
            // get ForkAPI-keys
            $publicKey = self::get('fork.settings')->get('Core', 'fork_api_public_key', '');
            $privateKey = self::get('fork.settings')->get('Core', 'fork_api_private_key', '');

            // validate keys
            if ($publicKey == '' || $privateKey == '') {
                return false;
            }

            // require the class
            require_once PATH_LIBRARY . '/external/fork_api.php';

            // create instance
            $forkAPI = new \ForkAPI($publicKey, $privateKey);

            // try to get the services
            try {
                $pingServices['services'] = $forkAPI->pingGetServices();
                $pingServices['date'] = time();
            } catch (Exception $e) {
                // check if the error should not be ignored
                if (strpos($e->getMessage(), 'Operation timed out') === false &&
                    strpos($e->getMessage(), 'Invalid headers') === false
                ) {
                    if (BackendModel::getContainer()->getParameter('kernel.debug')) {
                        throw $e;
                    } else {
                        // stop, hammertime
                        return false;
                    }
                }
            }

            // store the services
            self::get('fork.settings')->set('Core', 'ping_services', $pingServices);
        }

        // make sure services array will not trigger an error (even if we couldn't load any)
        if (!isset($pingServices['services']) || !$pingServices['services']) {
            $pingServices['services'] = array();
        }

        // loop services
        foreach ($pingServices['services'] as $service) {
            $client = new \SpoonXMLRPCClient($service['url']);
            $client->setUserAgent('Fork ' . FORK_VERSION);
            $client->setTimeOut(10);
            $client->setPort($service['port']);

            try {
                // extended ping?
                if ($service['type'] == 'extended') {
                    // no page or feed URL present?
                    if ($pageOrFeedURL === null) {
                        continue;
                    }

                    $parameters[] = array('type' => 'string', 'value' => $siteTitle);
                    $parameters[] = array('type' => 'string', 'value' => $siteURL);
                    $parameters[] = array('type' => 'string', 'value' => $pageOrFeedURL);
                    if ($category !== null) {
                        $parameters[] = array('type' => 'string', 'value' => $category);
                    }

                    $client->execute('weblogUpdates.extendedPing', $parameters);
                } else {
                    // default ping
                    $parameters[] = array('type' => 'string', 'value' => $siteTitle);
                    $parameters[] = array('type' => 'string', 'value' => $siteURL);

                    $client->execute('weblogUpdates.ping', $parameters);
                }
            } catch (Exception $e) {
                // check if the error should not be ignored
                if (strpos($e->getMessage(), 'Operation timed out') === false &&
                    strpos($e->getMessage(), 'Invalid headers') === false
                ) {
                    if (BackendModel::getContainer()->getParameter('kernel.debug')) {
                        throw $e;
                    }
                }
                continue;
            }
        }

        return true;
    }

    /**
     * Saves a module-setting into the DB and the cached array
     *
     * @deprecated
     * @param string $module The module to set the setting for.
     * @param string $key    The name of the setting.
     * @param string $value  The value to store.
     */
    public static function setModuleSetting($module, $key, $value)
    {
        trigger_error(
            'BackendModel::setModuleSetting is deprecated.
             Use $container->get(\'fork.settings\')->set instead',
            E_USER_DEPRECATED
        );

        return self::get('fork.settings')->set($module, $key, $value);
    }

    /**
     * Submit ham, this call is intended for the marking of false positives, things that were incorrectly marked as
     * spam.
     *
     * @param string $userIp    IP address of the comment submitter.
     * @param string $userAgent User agent information.
     * @param string $content   The content that was submitted.
     * @param string $author    Submitted name with the comment.
     * @param string $email     Submitted email address.
     * @param string $url       Commenter URL.
     * @param string $permalink The permanent location of the entry the comment was submitted to.
     * @param string $type      May be blank, comment, trackback, pingback, or a made up value like "registration".
     * @param string $referrer  The content of the HTTP_REFERER header should be sent here.
     * @param array  $others    Other data (the variables from $_SERVER).
     * @return bool If everything went fine, true will be returned, otherwise an exception will be triggered.
     */
    public static function submitHam(
        $userIp,
        $userAgent,
        $content,
        $author = null,
        $email = null,
        $url = null,
        $permalink = null,
        $type = null,
        $referrer = null,
        $others = null
    ) {
        $akismetKey = self::get('fork.settings')->get('Core', 'akismet_key');

        // no key, so we can't detect spam
        if ($akismetKey === '') {
            return false;
        }

        $akismet = new Akismet($akismetKey, SITE_URL);
        $akismet->setTimeOut(10);
        $akismet->setUserAgent('Fork CMS/2.1');

        // try it to decide it the item is spam
        try {
            // check with Akismet if the item is spam
            return $akismet->submitHam(
                $userIp,
                $userAgent,
                $content,
                $author,
                $email,
                $url,
                $permalink,
                $type,
                $referrer,
                $others
            );
        } catch (Exception $e) {
            if (BackendModel::getContainer()->getParameter('kernel.debug')) {
                throw $e;
            }
        }

        return false;
    }

    /**
     * Submit spam, his call is for submitting comments that weren't marked as spam but should have been.
     *
     * @param string $userIp    IP address of the comment submitter.
     * @param string $userAgent User agent information.
     * @param string $content   The content that was submitted.
     * @param string $author    Submitted name with the comment.
     * @param string $email     Submitted email address.
     * @param string $url       Commenter URL.
     * @param string $permalink The permanent location of the entry the comment was submitted to.
     * @param string $type      May be blank, comment, trackback, pingback, or a made up value like "registration".
     * @param string $referrer  The content of the HTTP_REFERER header should be sent here.
     * @param array  $others    Other data (the variables from $_SERVER).
     * @return bool If everything went fine true will be returned, otherwise an exception will be triggered.
     */
    public static function submitSpam(
        $userIp,
        $userAgent,
        $content,
        $author = null,
        $email = null,
        $url = null,
        $permalink = null,
        $type = null,
        $referrer = null,
        $others = null
    ) {
        $akismetKey = self::get('fork.settings')->get('Core', 'akismet_key');

        // no key, so we can't detect spam
        if ($akismetKey === '') {
            return false;
        }

        $akismet = new Akismet($akismetKey, SITE_URL);
        $akismet->setTimeOut(10);
        $akismet->setUserAgent('Fork CMS/2.1');

        // try it to decide it the item is spam
        try {
            // check with Akismet if the item is spam
            return $akismet->submitSpam(
                $userIp,
                $userAgent,
                $content,
                $author,
                $email,
                $url,
                $permalink,
                $type,
                $referrer,
                $others
            );
        } catch (Exception $e) {
            if (BackendModel::getContainer()->getParameter('kernel.debug')) {
                throw $e;
            }
        }

        return false;
    }

    /**
     * Update extra
     *
     * @param int    $id    The id for the extra.
     * @param string $key   The key you want to update.
     * @param string $value The new value.
     * @throws Exception If key parameter is not allowed
     */
    public static function updateExtra($id, $key, $value)
    {
        // recast key
        $key = (string) $key;

        // define allowed keys
        $allowedKeys = array('label', 'action', 'data', 'hidden', 'sequence');

        // key is not allowed
        if (!in_array((string) $key, $allowedKeys)) {
            throw new Exception('The key ' . $key . ' can\'t be updated.');
        }

        // key is 'data' and value is not serialized
        if ($key === 'data' && is_array($value)) {
            // serialize value
            $value = serialize($value);
        }

        $item = array();
        $item[(string) $key] = (string) $value;
        self::getContainer()->get('database')->update('modules_extras', $item, 'id = ?', array((int) $id));
    }

    /**
     * Update extra data
     *
     * @param int    $id    The id for the extra.
     * @param string $key   The key in the data you want to update.
     * @param string $value The new value.
     */
    public static function updateExtraData($id, $key, $value)
    {
        $db = self::getContainer()->get('database');

        $data = (string) $db->getVar(
            'SELECT i.data
             FROM modules_extras AS i
             WHERE i.id = ?',
            array((int) $id)
        );

        $data = unserialize($data);
        $data[(string) $key] = (string) $value;
        $db->update('modules_extras', array('data' => serialize($data)), 'id = ?', array((int) $id));
    }
}
