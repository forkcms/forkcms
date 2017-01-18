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
use Frontend\Core\Language\Language as FrontendLanguage;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * In this file we store all generic functions that we will be using in the backend.
 */
class Model extends \Common\Core\Model
{
    /**
     * Allowed module extras types
     *
     * @var array
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
            $warnings[] = array('message' => BackendLanguage::err('DebugModeIsActive'));
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
     * @throws \Exception If $action, $module or both are not set
     *
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
        $language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();
        $queryString = '';

        // checking if we have an url, because in a cronjob we don't have one
        if (self::getContainer()->has('url')) {
            // grab the URL from the reference
            $url = self::getContainer()->get('url');

            // redefine
            if ($action === null) {
                $action = $url->getAction();
            }
            if ($module === null) {
                $module = $url->getModule();
            }
        }

        // error checking
        if ($action === null || $module === null) {
            throw new \Exception('Action and Module must not be empty when creating an URL.');
        }

        // lets create underscore cased module and action names
        $module = mb_strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $module));
        $action = mb_strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $action));

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
                $queryString .= '?' . $key . '=' . (($urlencode) ? rawurlencode($value) : $value);
            } else {
                $queryString .= '&' . $key . '=' . (($urlencode) ? rawurlencode($value) : $value);
            }

            ++$i;
        }

        // build the URL and return it
        return self::get('router')->generate(
            'backend',
            array(
                '_locale' => $language,
                'module' => $module,
                'action' => $action,
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

            // get extra data
            $extraData = $extra['data'] !== null ? (array) unserialize($extra['data']) : null;

            // if we have $data parameter set and $extraData not null we should not delete such extra
            if (isset($data) && !isset($extraData)) {
                $deleteExtra = false;
            } elseif (isset($data) && isset($extraData)) {
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
        $filesystem = new Filesystem();
        foreach ($finder->directories()->in($path) as $directory) {
            $fileName = $directory->getRealPath() . '/' . $thumbnail;
            if (is_file($fileName)) {
                $filesystem->remove($fileName);
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
     *
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
        for ($i = 0; $i < $length; ++$i) {
            // random index
            $index = mt_rand(0, mb_strlen($characters));

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
     *
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
     *
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
     *
     * @return array
     */
    public static function getKeys($language = null)
    {
        $language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

        $cacheBuilder = BackendPagesModel::getCacheBuilder();

        return $cacheBuilder->getKeys($language);
    }

    /**
     * Get the modules that are available on the filesystem
     *
     * @param bool $includeCore Should core be included as a module?
     *
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
        $directories = $finder->directories()->in(
            __DIR__ . '/../../Modules'
        )->depth('==0');
        foreach ($directories as $directory) {
            $return[] = $directory->getBasename();
        }

        return $return;
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
            $dropDown[$module] = \SpoonFilter::ucfirst(BackendLanguage::lbl(\SpoonFilter::toCamelCase($module)));
        }

        return $dropDown;
    }

    /**
     * Get the navigation-items
     *
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return array
     */
    public static function getNavigation($language = null)
    {
        $language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

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
     *
     * @return string
     */
    public static function getURL($pageId, $language = null)
    {
        $pageId = (int) $pageId;
        $language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

        // init URL
        $url = (self::getContainer()->getParameter('site.multilanguage')) ? '/' . $language . '/' : '/';

        // get the menuItems
        $keys = self::getKeys($language);

        // get the URL, if it doesn't exist return 404
        if (!isset($keys[$pageId])) {
            return self::getURL(404, $language);
        } else {
            $url .= $keys[$pageId];
        }

        // return the unique URL!
        return urldecode($url);
    }

    /**
     * Get the URL for a give module & action combination
     *
     * @param string $module   The module wherefore the URL should be build.
     * @param string $action   The specific action wherefore the URL should be build.
     * @param string $language The language wherein the URL should be retrieved,
     *                         if not provided we will load the language that was provided in the URL.
     * @param array $data      An array with keys and values that partially or fully match the data of the block.
     *                         If it matches multiple versions of that block it will just return the first match.
     *
     * @return string
     */
    public static function getURLForBlock($module, $action = null, $language = null, array $data = null)
    {
        $module = (string) $module;
        $action = ($action !== null) ? (string) $action : null;
        $language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

        $pageIdForURL = null;
        $navigation = self::getNavigation($language);

        $dataMatch = false;
        // loop types
        foreach ($navigation as $level) {
            // loop level
            foreach ($level as $pages) {
                // loop pages
                foreach ($pages as $pageId => $properties) {
                    // only process pages with extra_blocks that are visible
                    if (!isset($properties['extra_blocks']) || $properties['hidden']) {
                        continue;
                    }

                    // loop extras
                    foreach ($properties['extra_blocks'] as $extra) {
                        // direct link?
                        if ($extra['module'] == $module && $extra['action'] == $action  && $extra['action'] !== null) {
                            // if there is data check if all the requested data matches the extra data
                            if (isset($extra['data']) && $data !== null
                                && array_intersect_assoc($data, (array) $extra['data']) !== $data) {
                                // It is the correct action but has the wrong data
                                continue;
                            }
                            // exact page was found, so return
                            return self::getURL($properties['page_id'], $language);
                        }

                        if ($extra['module'] == $module && $extra['action'] == null) {
                            // if there is data check if all the requested data matches the extra data
                            if (isset($extra['data']) && $data !== null) {
                                if (array_intersect_assoc($data, (array) $extra['data']) !== $data) {
                                    // It is the correct module but has the wrong data
                                    continue;
                                }

                                $pageIdForURL = (int) $pageId;
                                $dataMatch = true;
                            }

                            if ($extra['data'] === null && $data === null) {
                                $pageIdForURL = (int) $pageId;
                                $dataMatch = true;
                            }

                            if (!$dataMatch) {
                                $pageIdForURL = (int) $pageId;
                            }
                        }
                    }
                }
            }
        }

        // still no page id?
        if ($pageIdForURL === null) {
            return self::getURL(404, $language);
        }

        $url = self::getURL($pageIdForURL, $language);

        // set locale with force
        FrontendLanguage::setLocale($language, true);

        // append action
        if ($action !== null) {
            $url .= '/' . urldecode(FrontendLanguage::act(\SpoonFilter::toCamelCase($action)));
        }

        // return the unique URL!
        return $url;
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

        $filesystem = new Filesystem();
        foreach ($fileSizes as $sizeDir) {
            $fullPath = FRONTEND_FILES_PATH . '/' . $module .
                        (empty($subDirectory) ? '/' : '/' . $subDirectory . '/') . $sizeDir . '/' . $filename;
            if (is_file($fullPath)) {
                $filesystem->remove($fullPath);
            }
        }
        $fullPath = FRONTEND_FILES_PATH . '/' . $module .
                    (empty($subDirectory) ? '/' : '/' . $subDirectory . '/') . 'source/' . $filename;
        if (is_file($fullPath)) {
            $filesystem->remove($fullPath);
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
     *
     * @throws Exception If extra type is not allowed
     *
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
            'sequence' => $sequence,
        );

        // return id for inserted extra
        return $db->insert('modules_extras', $extra);
    }

    /**
     * Is module installed?
     *
     * @param string $module
     *
     * @return bool
     */
    public static function isModuleInstalled($module)
    {
        $modules = self::getModules();

        return (in_array((string) $module, $modules));
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
     *
     * @return bool If everything went fine, true will be returned, otherwise an exception will be triggered.
     * @throws Exception
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
     *
     * @return bool If everything went fine true will be returned, otherwise an exception will be triggered.
     * @throws Exception
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
     *
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
