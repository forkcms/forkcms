<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

use TijsVerkoyen\Akismet\Akismet;

use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

use Frontend\Core\Engine\Language as FrontendLanguage;

require_once __DIR__ . '/../../../../app/BaseModel.php';

/**
 * In this file we store all generic functions that we will be using in the backend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Model extends \BaseModel
{
    /**
     * The keys and structural data for pages
     *
     * @var    array
     */
    private static $keys = array();
    private static $navigation = array();

    /**
     * Cached modules
     *
     * @var    array
     */
    private static $modules = array();

    /**
     * Cached module settings
     *
     * @var    array
     */
    private static $moduleSettings;

    /**
     * Add a number to the string
     *
     * @param string $string The string where the number will be appended to.
     * @return string
     */
    public static function addNumber($string)
    {
        // split
        $chunks = explode('-', $string);

        // count the chunks
        $count = count($chunks);

        // get last chunk
        $last = $chunks[$count - 1];

        // is numeric
        if (\SpoonFilter::isNumeric($last)) {
            array_pop($chunks);
            $string = implode('-', $chunks) . '-' . ((int) $last + 1);
        } else {
            // not numeric, so add -2
            $string .= '-2';
        }

        return $string;
    }

    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings()
    {
        $warnings = array();

        // check if debug-mode is active
        if (SPOON_DEBUG) {
            $warnings[] = array('message' => Language::err('DebugModeIsActive'));
        }

        // check if this action is allowed
        if (Authentication::isAllowedAction('Index', 'Settings')) {
            // check if the fork API keys are available
            if (self::getModuleSetting('Core', 'fork_api_private_key') == '' ||
                self::getModuleSetting('Core', 'fork_api_public_key') == ''
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
     * @return string
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
            if ($action === null) $action = $URL->getAction();
            if ($module === null) $module = $URL->getModule();
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
                $queryString .= '&amp;' . $key . '=' . (($urlencode) ? urlencode($value) : $value);
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
            // match by parameters
            if ($data !== null && $extra['data'] !== null) {
                $extraData = (array) unserialize($extra['data']);

                // skip extra if parameters do not match
                if (count(array_intersect($data, $extraData)) !== count($data)) {
                    continue;
                }
            }

            // delete extra
            self::deleteExtraById($extra['id']);
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
     * Generate a totally random but readable/speakable password
     *
     * @param int  $length           The maximum length for the password to generate.
     * @param bool $uppercaseAllowed Are uppercase letters allowed?
     * @param bool $lowercaseAllowed Are lowercase letters allowed?
     * @return string
     */
    public static function generatePassword($length = 6, $uppercaseAllowed = true, $lowercaseAllowed = true)
    {
        // list of allowed vowels and vowelsounds
        $vowels = array('a', 'e', 'i', 'u', 'ae', 'ea');

        // list of allowed consonants and consonant sounds
        $consonants = array(
            'b',
            'c',
            'd',
            'g',
            'h',
            'j',
            'k',
            'm',
            'n',
            'p',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'tr',
            'cr',
            'fr',
            'dr',
            'wr',
            'pr',
            'th',
            'ch',
            'ph',
            'st'
        );

        // init vars
        $consonantsCount = count($consonants);
        $vowelsCount = count($vowels);
        $pass = '';
        $tmp = '';

        // create temporary pass
        for ($i = 0; $i < $length; $i++) {
            $tmp .= ($consonants[rand(0, $consonantsCount - 1)] . $vowels[rand(0, $vowelsCount - 1)]);
        }

        // reformat the pass
        for ($i = 0; $i < $length; $i++) {
            if (rand(0, 1) == 1) {
                $pass .= strtoupper(substr($tmp, $i, 1));
            } else {
                $pass .= substr($tmp, $i, 1);
            }
        }

        // reformat it again, if uppercase isn't allowed
        if (!$uppercaseAllowed) {
            $pass = strtolower($pass);
        }

        // reformat it again, if uppercase isn't allowed
        if (!$lowercaseAllowed) {
            $pass = strtoupper($pass);
        }

        return $pass;
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
            $string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
        }

        return $string;
    }

    /**
     * Generate thumbnails based on the folders in the path
     * Use
     *  - 128x128 as foldername to generate an image where the width will be
     *      128px and the height will be 128px
     *  - 128x as foldername to generate an image where the width will be
     *      128px, the height will be calculated based on the aspect ratio.
     *  - x128 as foldername to generate an image where the height will be
     *      128px, the width will be calculated based on the aspect ratio.
     *
     * @param string $path       The path wherein the thumbnail-folders will be stored.
     * @param string $sourceFile The location of the source file.
     */
    public static function generateThumbnails($path, $sourceFile)
    {
        // get folder listing
        $folders = self::getThumbnailFolders($path);
        $filename = basename($sourceFile);

        // loop folders
        foreach ($folders as $folder) {
            // generate the thumbnail
            $thumbnail = new \SpoonThumbnail($sourceFile, $folder['width'], $folder['height']);
            $thumbnail->setAllowEnlargement(true);

            // if the width & height are specified we should ignore the aspect ratio
            if ($folder['width'] !== null && $folder['height'] !== null) {
                $thumbnail->setForceOriginalAspectRatio(false);
            }
            $thumbnail->parseToFile($folder['path'] . '/' . $filename);
        }
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
        foreach ((array) self::getModuleSetting('Core', 'date_formats_long') as $format) {
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
        foreach ((array) self::getModuleSetting('Core', 'date_formats_short') as $format) {
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

        // create an array with an equal amount of questionmarks as ids provided
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

        // does the keys exists in the cache?
        if (!isset(self::$keys[$language]) || empty(self::$keys[$language])) {
            if (!is_file(FRONTEND_CACHE_PATH . '/Navigation/keys_' . $language . '.php')) {
                BackendPagesModel::buildCache($language);
            }

            $keys = array();
            require FRONTEND_CACHE_PATH . '/Navigation/keys_' . $language . '.php';
            self::$keys[$language] = $keys;
        }

        return self::$keys[$language];
    }

    /**
     * Get the modules
     *
     * @return array
     */
    public static function getModules()
    {
        if (empty(self::$modules)) {
            $modules = (array) self::getContainer()->get('database')->getColumn('SELECT m.name FROM modules AS m');
            foreach ($modules as $module) {
                self::$modules[] = $module;
            }
        }

        return self::$modules;
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
     * @param string $module       The module in which the setting is stored.
     * @param string $key          The name of the setting.
     * @param mixed  $defaultValue The value to return if the setting isn't present.
     * @return mixed
     */
    public static function getModuleSetting($module, $key, $defaultValue = null)
    {
        // redefine
        $module = (string) $module;
        $key = (string) $key;

        // define settings
        $settings = self::getModuleSettings($module);

        // return if exists, otherwise return default value
        return (isset($settings[$key])) ? $settings[$key] : $defaultValue;
    }

    /**
     * Get all module settings at once
     *
     * @param string $module You can get all settings for a module.
     * @return array
     */
    public static function getModuleSettings($module = null)
    {
        // redefine
        $module = ((bool) $module) ? (string) $module : false;

        // are the values available
        if (empty(self::$moduleSettings)) {
            // get all settings
            $moduleSettings = (array) self::getContainer()->get('database')->getRecords(
                'SELECT ms.module, ms.name, ms.value
                 FROM modules_settings AS ms'
            );

            // loop and store settings in the cache
            foreach ($moduleSettings as $setting) {
                $value = @unserialize($setting['value']);

                if ($value === false &&
                    serialize(false) != $setting['value']
                ) {
                    throw new Exception(
                        'The modulesetting (' . $setting['module'] . ': ' .
                        $setting['name'] . ') wasn\'t saved properly.'
                    );
                }

                // cache the setting
                self::$moduleSettings[$setting['module']][$setting['name']] = $value;
            }
        }

        if ($module) {
            // return module settings if there are some, if not return empty array
            return (isset(self::$moduleSettings[$module])) ? self::$moduleSettings[$module] : array();
        } else {
            // else return all settings
            return self::$moduleSettings;
        }
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
        $language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

        // does the keys exists in the cache?
        if (!isset(self::$navigation[$language]) || empty(self::$navigation[$language])) {
            if (!is_file(FRONTEND_CACHE_PATH . '/Navigation/navigation_' . $language . '.php')) {
                BackendPagesModel::buildCache($language);
            }

            $navigation = array();
            require FRONTEND_CACHE_PATH . '/Navigation/navigation_' . $language . '.php';

            self::$navigation[$language] = $navigation;
        }

        return self::$navigation[$language];
    }

    /**
     * Fetch the list of number formats including examples of these formats.
     *
     * @return array
     */
    public static function getNumberFormats()
    {
        $possibleFormats = array();

        foreach ((array) self::getModuleSetting('Core', 'number_formats') as $format => $example) {
            $possibleFormats[$format] = $example;
        }

        return $possibleFormats;
    }

    /**
     * Get the thumbnail folders
     *
     * @param string $path          The path
     * @param bool   $includeSource Should the source-folder be included in the return-array.
     * @return array
     */
    public static function getThumbnailFolders($path, $includeSource = false)
    {
        $return = array();
        $finder = new Finder();
        $finder->name('/^([0-9]*)x([0-9]*)$/');
        if ($includeSource) {
            $finder->name('source');
        }

        foreach ($finder->directories()->in($path) as $directory) {
            $chunks = explode('x', $directory->getBasename(), 2);
            if (count($chunks) != 2 && !$includeSource) {
                continue;
            }

            $item = array();
            $item['dirname'] = $directory->getBasename();
            $item['path'] = $directory->getRealPath();
            if (substr($path, 0, strlen(PATH_WWW)) == PATH_WWW) {
                $item['url'] = substr($path, strlen(PATH_WWW));
            }

            if ($item['dirname'] == 'source') {
                $item['width'] = null;
                $item['height'] = null;
            } else {
                $item['width'] = ($chunks[0] != '') ? (int) $chunks[0] : null;
                $item['height'] = ($chunks[1] != '') ? (int) $chunks[1] : null;
            }

            $return[] = $item;
        }

        return $return;
    }

    /**
     * Fetch the list of time formats including examples of these formats.
     *
     * @return array
     */
    public static function getTimeFormats()
    {
        $possibleFormats = array();

        foreach (self::getModuleSetting('Core', 'time_formats') as $format) {
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
        $URL = (SITE_MULTILANGUAGE) ? '/' . $language . '/' : '/';

        // get the menuItems
        $keys = self::getKeys($language);

        // get the URL, if it doesn't exist return 404
        if (!isset($keys[$pageId])) {
            return self::getURL(404);
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
                    if (!isset($properties['extra_blocks'])) {
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
     * Get the UTC date in a specific format. Use this method when inserting dates in the database!
     *
     * @param string $format    The format to return the timestamp in. Default is MySQL datetime format.
     * @param int    $timestamp The timestamp to use, if not provided the current time will be used.
     * @return string
     */
    public static function getUTCDate($format = null, $timestamp = null)
    {
        $format = ($format !== null) ? (string) $format : 'Y-m-d H:i:s';
        if ($timestamp === null) {
            return gmdate($format);
        }

        return gmdate($format, (int) $timestamp);
    }

    /**
     * Get the UTC timestamp for a date/time object combination.
     *
     * @param \SpoonFormDate $date An instance of \SpoonFormDate.
     * @param \SpoonFormTime $time An instance of \SpoonFormTime.
     * @return int
     */
    public static function getUTCTimestamp(\SpoonFormDate $date, \SpoonFormTime $time = null)
    {
        // validate date/time object
        if (!$date->isValid() || ($time !== null && !$time->isValid())
        ) {
            throw new Exception('You need to provide two objects that actually contain valid data.');
        }

        // init vars
        $year = gmdate('Y', $date->getTimestamp());
        $month = gmdate('m', $date->getTimestamp());
        $day = gmdate('j', $date->getTimestamp());

        if ($time !== null) {
            // time object was given
            list($hour, $minute) = explode(':', $time->getValue());
        } else {
            // user default time
            $hour = 0;
            $minute = 0;
        }

        // make and return timestamp
        return mktime($hour, $minute, 0, $month, $day, $year);
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
     */
    public static function insertExtra($type, $module, $action = null, $label = null, $data = null, $hidden = false, $sequence = null)
    {
        $type = (string) $type;
        $module = (string) $module;

        // if action and label are empty, fallback to module
        $action = ($action == null) ? $module : (string) $action;
        $label = ($label == null) ? $module : (string) $label;

        // check if type is allowed
        if (!in_array($type, array('homepage', 'block', 'widget'))) {
            throw new BackendException(
                'Type is not allowed, choose from "' . implode(', ', $allowedExtras) .'".'
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
                    $regexp = '/' . '(.*)' . $module . '(.*)_cache\.tpl/i';
                } else {
                    $regexp = '/' . $language . '_' . $module . '(.*)_cache\.tpl/i';
                }
            } else {
                if ($language === null) {
                    $regexp = '/(.*)_cache\.tpl/i';
                } else {
                    $regexp = '/' . $language . '_(.*)_cache\.tpl/i';
                }
            }

            $finder = new Finder();
            $fs = new Filesystem();
            foreach ($finder->files()->name($regexp)->in($path) as $file) {
                $fs->remove($file->getRealPath());
            }
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
        $siteTitle = self::getModuleSetting('Core', 'site_title_' . Language::getWorkingLanguage(), SITE_DEFAULT_TITLE);
        $siteURL = SITE_URL;
        $pageOrFeedURL = ($pageOrFeedURL !== null) ? (string) $pageOrFeedURL : null;
        $category = ($category !== null) ? (string) $category : null;

        // get ping services
        $pingServices = self::getModuleSetting('Core', 'ping_services', null);

        // no ping services available or older than one month ago
        if ($pingServices === null || $pingServices['date'] < strtotime('-1 month')) {
            // get ForkAPI-keys
            $publicKey = self::getModuleSetting('Core', 'fork_api_public_key', '');
            $privateKey = self::getModuleSetting('Core', 'fork_api_private_key', '');

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
                    if (SPOON_DEBUG) {
                        throw $e;
                    } else {
                        // stop, hammertime
                        return false;
                    }
                }
            }

            // store the services
            self::setModuleSetting('Core', 'ping_services', $pingServices);
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
                    if (SPOON_DEBUG) {
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
     * @param string $module The module to set the setting for.
     * @param string $key    The name of the setting.
     * @param string $value  The value to store.
     */
    public static function setModuleSetting($module, $key, $value)
    {
        $module = (string) $module;
        $key = (string) $key;
        $valueToStore = serialize($value);

        // store
        self::getContainer()->get('database')->execute(
            'INSERT INTO modules_settings(module, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            array($module, $key, $valueToStore, $valueToStore)
        );

        // cache it
        self::$moduleSettings[$module][$key] = $value;
    }

    /**
     * Start processing the hooks
     */
    public static function startProcessingHooks()
    {
        $fs = new Filesystem();

        // is the queue already running?
        if ($fs->exists(BACKEND_CACHE_PATH . '/Hooks/pid')) {
            // get the pid
            $pid = trim(file_get_contents(BACKEND_CACHE_PATH . '/Hooks/pid'));

            if (strtolower(substr(php_uname('s'), 0, 3)) == 'win') {
                $output = @shell_exec('tasklist.exe /FO LIST /FI "PID eq ' . $pid . '"');

                if ($output == '' || $output === false) {
                    $fs->remove(BACKEND_CACHE_PATH . '/Hooks/pid');
                } else {
                    return true;
                }
            } elseif (strtolower(substr(php_uname('s'), 0, 6)) == 'darwin') {
                // darwin == Mac
                $output = @posix_getsid($pid);

                if ($output === false) {
                    $fs->remove(BACKEND_CACHE_PATH . '/Hooks/pid');
                } else {
                    return true;
                }
            } else {
                if (!$fs->exists('/proc/' . $pid)) {
                    $fs->remove(BACKEND_CACHE_PATH . '/Hooks/pid');
                } else {
                    return true;
                }
            }
        }

        $parts = parse_url(SITE_URL);
        $errNo = '';
        $errStr = '';
        $defaultPort = 80;
        if ($parts['scheme'] == 'https') {
            $defaultPort = 433;
        }

        $socket = fsockopen(
            $parts['host'],
            (isset($parts['port'])) ? $parts['port'] : $defaultPort,
            $errNo,
            $errStr,
            1
        );

        $request = 'GET /backend/cronjob?module=Core&action=ProcessQueuedHooks HTTP/1.1' . "\r\n";
        $request .= 'Host: ' . $parts['host'] . "\r\n";
        $request .= 'Content-Length: 0' . "\r\n\r\n";
        $request .= 'Connection: Close' . "\r\n\r\n";

        fwrite($socket, $request);
        fclose($socket);

        return true;
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
        $akismetKey = self::getModuleSetting('Core', 'akismet_key');

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
            if (SPOON_DEBUG) {
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
        $akismetKey = self::getModuleSetting('Core', 'akismet_key');

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
            if (SPOON_DEBUG) {
                throw $e;
            }
        }

        return false;
    }

    /**
     * Subscribe to an event, when the subscription already exists, the callback will be updated.
     *
     * @param string $eventModule The module that triggers the event.
     * @param string $eventName   The name of the event.
     * @param string $module      The module that subscribes to the event.
     * @param mixed  $callback    The callback that should be executed when the event is triggered.
     */
    public static function subscribeToEvent($eventModule, $eventName, $module, $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Invalid callback!');
        }

        $item['event_module'] = (string) $eventModule;
        $item['event_name'] = (string) $eventName;
        $item['module'] = (string) $module;
        $item['callback'] = serialize($callback);
        $item['created_on'] = self::getUTCDate();

        $db = self::getContainer()->get('database');

        // check if the subscription already exists
        $exists = (bool) $db->getVar(
            'SELECT 1
             FROM hooks_subscriptions AS i
             WHERE i.event_module = ? AND i.event_name = ? AND i.module = ?
             LIMIT 1',
            array($eventModule, $eventName, $module)
        );

        if ($exists) {
            $db->update(
                'hooks_subscriptions',
                $item,
                'event_module = ? AND event_name = ? AND module = ?',
                array($eventModule, $eventName, $module)
            );
        } else {
            $db->insert('hooks_subscriptions', $item);
        }
    }

    /**
     * Trigger an event
     *
     * @param string $module    The module that triggers the event.
     * @param string $eventName The name of the event.
     * @param mixed  $data      The data that should be send to subscribers.
     */
    public static function triggerEvent($module, $eventName, $data = null)
    {
        $module = (string) $module;
        $eventName = (string) $eventName;

        // create log instance
        $log = self::getContainer()->get('logger');
        $log->info('Event (' . $module . '/' . $eventName . ') triggered.');

        // get all items that subscribe to this event
        $subscriptions = (array) self::getContainer()->get('database')->getRecords(
            'SELECT i.module, i.callback
             FROM hooks_subscriptions AS i
             WHERE i.event_module = ? AND i.event_name = ?',
            array($module, $eventName)
        );

        // any subscriptions?
        if (!empty($subscriptions)) {
            $queuedItems = array();

            foreach ($subscriptions as $subscription) {
                $item['module'] = $subscription['module'];
                $item['callback'] = $subscription['callback'];
                $item['data'] = serialize($data);
                $item['status'] = 'queued';
                $item['created_on'] = self::getUTCDate();

                $queuedItems[] = self::getContainer()->get('database')->insert('hooks_queue', $item);

                $log->info(
                    'Callback (' . $subscription['callback'] . ') is
                    subscribed to event (' . $module . '/' . $eventName . ').'
                );
            }

            self::startProcessingHooks();
        }
    }

    /**
     * Unsubscribe from an event
     *
     * @param string $eventModule The module that triggers the event.
     * @param string $eventName   The name of the event.
     * @param string $module      The module that subscribes to the event.
     */
    public static function unsubscribeFromEvent($eventModule, $eventName, $module)
    {
        $eventModule = (string) $eventModule;
        $eventName = (string) $eventName;
        $module = (string) $module;

        self::getContainer()->get('database')->delete(
            'hooks_subscriptions',
            'event_module = ? AND event_name = ? AND module = ?',
            array($eventModule, $eventName, $module)
        );
    }

    /**
     * Update extra
     *
     * @param int    $id    The id for the extra.
     * @param string $key   The key you want to update.
     * @param string $value The new value.
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
