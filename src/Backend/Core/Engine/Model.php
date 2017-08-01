<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModuleExtraType;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings(): array
    {
        $warnings = [];

        // check if debug-mode is active
        if (BackendModel::getContainer()->getParameter('kernel.debug')) {
            $warnings[] = ['message' => BackendLanguage::err('DebugModeIsActive')];
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
     * @param string $action The action to build the URL for.
     * @param string $module The module to build the URL for.
     * @param string $language The language to use, if not provided we will use the working language.
     * @param array $parameters GET-parameters to use.
     * @param bool $encodeSquareBrackets Should the square brackets be allowed so we can use them in de datagrid?
     *
     * @throws \Exception If $action, $module or both are not set
     *
     * @return string
     */
    public static function createUrlForAction(
        string $action = null,
        string $module = null,
        string $language = null,
        array $parameters = null,
        bool $encodeSquareBrackets = true
    ): string {
        $language = $language ?? BackendLanguage::getWorkingLanguage();

        // checking if we have an url, because in a cronjob we don't have one
        if (self::getContainer()->has('url')) {
            // grab the URL from the reference
            $url = self::getContainer()->get('url');
            $action = $action ?? $url->getAction();
            $module = $module ?? $url->getModule();
        }

        // error checking
        if ($action === null || $module === null) {
            throw new \Exception('Action and Module must not be empty when creating an url.');
        }

        $parameters['token'] = self::getToken();
        $queryParameterBag = self::getRequest()->query;

        // add offset, order & sort (only if not yet manually added)
        if (!isset($parameters['offset']) && $queryParameterBag->has('offset')) {
            $parameters['offset'] = $queryParameterBag->getInt('offset');
        }
        if (!isset($parameters['order']) && $queryParameterBag->has('order')) {
            $parameters['order'] = $queryParameterBag->get('order');
        }
        if (!isset($parameters['sort']) && $queryParameterBag->has('sort')) {
            $parameters['sort'] = $queryParameterBag->get('sort');
        }

        $queryString = '?' . http_build_query($parameters);

        if (!$encodeSquareBrackets) {
            // we use things like [id] to parse database column data in so we need to unescape those
            $queryString = str_replace([urlencode('['), urlencode(']')], ['[', ']'], $queryString);
        }

        return self::get('router')->generate(
            'backend',
            [
                '_locale' => $language,
                'module' => self::camelCaseToLowerSnakeCase($module),
                'action' => self::camelCaseToLowerSnakeCase($action),
            ]
        ) . $queryString;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function camelCaseToLowerSnakeCase(string $string): string
    {
        return mb_strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    /**
     * Delete a page extra by module, type or data.
     *
     * Data is a key/value array. Example: array(id => 23, language => nl);
     *
     * @param string $module The module wherefore the extra exists.
     * @param string $type The type of extra, possible values are block, homepage, widget.
     * @param array $data Extra data that exists.
     */
    public static function deleteExtra(string $module = null, string $type = null, array $data = null): void
    {
        // init
        $query = 'SELECT i.id, i.data FROM modules_extras AS i WHERE 1';
        $parameters = [];

        // module
        if ($module !== null) {
            $query .= ' AND i.module = ?';
            $parameters[] = $module;
        }

        // type
        if ($type !== null) {
            $query .= ' AND i.type = ?';
            $parameters[] = $type;
        }

        // get extras
        $extras = (array) self::getContainer()->get('database')->getRecords($query, $parameters);

        // loop found extras
        foreach ($extras as $extra) {
            // get extra data
            $extraData = $extra['data'] !== null ? (array) unserialize($extra['data']) : null;

            // if we have $data parameter set and $extraData not null we should not delete such extra
            if ($data !== null && $extraData === null) {
                continue;
            }

            if ($data !== null && $extraData !== null) {
                foreach ($data as $dataKey => $dataValue) {
                    if (isset($extraData[$dataKey]) && $dataValue !== $extraData[$dataKey]) {
                        continue 2;
                    }
                }
            }

            self::deleteExtraById($extra['id']);
        }
    }

    /**
     * Delete a page extra by its id
     *
     * @param int $id The id of the extra to delete.
     * @param bool $deleteBlock Should the block be deleted? Default is false.
     */
    public static function deleteExtraById(int $id, bool $deleteBlock = false): void
    {
        self::getContainer()->get('database')->delete('modules_extras', 'id = ?', $id);

        if ($deleteBlock) {
            self::getContainer()->get('database')->delete('pages_blocks', 'extra_id = ?', $id);

            return;
        }

        self::getContainer()->get('database')->update(
            'pages_blocks',
            ['extra_id' => null],
            'extra_id = ?',
            $id
        );
    }

    /**
     * Delete all extras for a certain value in the data array of that module_extra.
     *
     * @param string $module The module for the extra.
     * @param string $field The field of the data you want to check the value for.
     * @param string $value The value to check the field for.
     * @param string $action In case you want to search for a certain action.
     */
    public static function deleteExtrasForData(
        string $module,
        string $field,
        string $value,
        string $action = null
    ): void {
        $ids = self::getExtrasForData($module, $field, $value, $action);

        // we have extras
        if (!empty($ids)) {
            // delete extras
            self::getContainer()->get('database')->delete('modules_extras', 'id IN (' . implode(',', $ids) . ')');
        }
    }

    /**
     * Delete thumbnails based on the folders in the path
     *
     * @param string $path The path wherein the thumbnail-folders exist.
     * @param string|null $thumbnail The filename to be deleted.
     */
    public static function deleteThumbnails(string $path, ?string $thumbnail): void
    {
        // if there is no image provided we can't do anything
        if ($thumbnail === null || $thumbnail === '') {
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
     * @param int $length Length of random string.
     * @param bool $numeric Use numeric characters.
     * @param bool $lowercase Use alphanumeric lowercase characters.
     * @param bool $uppercase Use alphanumeric uppercase characters.
     * @param bool $special Use special characters.
     *
     * @return string
     */
    public static function generateRandomString(
        int $length = 15,
        bool $numeric = true,
        bool $lowercase = true,
        bool $uppercase = true,
        bool $special = true
    ): string {
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
            $index = random_int(0, mb_strlen($characters));

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
    public static function getDateFormatsLong(): array
    {
        $possibleFormats = [];

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
    public static function getDateFormatsShort(): array
    {
        $possibleFormats = [];

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

    public static function getExtras(array $ids): array
    {
        // get database
        $database = self::getContainer()->get('database');

        array_walk($ids, 'intval');

        // create an array with an equal amount of question marks as ids provided
        $extraIdPlaceHolders = array_fill(0, count($ids), '?');

        // get extras
        return (array) $database->getRecords(
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
     * @param string $key The key of the data you want to check the value for.
     * @param string $value The value to check the key for.
     * @param string $action In case you want to search for a certain action.
     *
     * @return array The ids for the extras.
     */
    public static function getExtrasForData(string $module, string $key, string $value, string $action = null): array
    {
        $query = 'SELECT i.id, i.data
                 FROM modules_extras AS i
                 WHERE i.module = ? AND i.data != ?';
        $parameters = [$module, 'NULL'];

        // Filter on the action if it is given.
        if ($action !== null) {
            $query .= ' AND i.action = ?';
            $parameters[] = $action;
        }

        $moduleExtras = (array) self::getContainer()->get('database')->getPairs($query, $parameters);

        // No module extra's found
        if (empty($moduleExtras)) {
            return [];
        }

        return array_keys(
            array_filter(
                $moduleExtras,
                function (array $data) use ($key, $value) {
                    $data = $data === null ? [] : unserialize($data);

                    return isset($data[$key]) && $data[$key] === $value;
                }
            )
        );
    }

    /**
     * Get the page-keys
     *
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return array
     */
    public static function getKeys(string $language = null): array
    {
        if ($language === null) {
            $language = BackendLanguage::getWorkingLanguage();
        }

        return BackendPagesModel::getCacheBuilder()->getKeys($language);
    }

    /**
     * Get the modules that are available on the filesystem
     *
     * @param bool $includeCore Should core be included as a module?
     *
     * @return array
     */
    public static function getModulesOnFilesystem(bool $includeCore = true): array
    {
        $modules = $includeCore ? ['Core'] : [];
        $finder = new Finder();
        $directories = $finder->directories()->in(__DIR__ . '/../../Modules')->depth('==0');
        foreach ($directories as $directory) {
            $modules[] = $directory->getBasename();
        }

        return $modules;
    }

    /**
     * Fetch the list of modules, but for a dropdown.
     *
     * @return array
     */
    public static function getModulesForDropDown(): array
    {
        $dropDown = ['Core' => 'Core'];

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
    public static function getNavigation(string $language = null): array
    {
        if ($language === null) {
            $language = BackendLanguage::getWorkingLanguage();
        }

        $cacheBuilder = BackendPagesModel::getCacheBuilder();

        return $cacheBuilder->getNavigation($language);
    }

    /**
     * Fetch the list of number formats including examples of these formats.
     *
     * @return array
     */
    public static function getNumberFormats(): array
    {
        return (array) self::get('fork.settings')->get('Core', 'number_formats');
    }

    /**
     * Fetch the list of time formats including examples of these formats.
     *
     * @return array
     */
    public static function getTimeFormats(): array
    {
        $possibleFormats = [];
        $interfaceLanguage = Authentication::getUser()->getSetting('interface_language');

        foreach (self::get('fork.settings')->get('Core', 'time_formats') as $format) {
            $possibleFormats[$format] = \SpoonDate::getDate($format, null, $interfaceLanguage);
        }

        return $possibleFormats;
    }

    /**
     * Get the token which will protect us
     *
     * @return string
     */
    public static function getToken(): string
    {
        if (self::getSession()->has('csrf_token') && self::getSession()->get('csrf_token') !== '') {
            return self::getSession()->get('csrf_token');
        }

        $token = self::generateRandomString(10, true, true, false, false);
        self::getSession()->set('csrf_token', $token);

        return $token;
    }

    /**
     * Get URL for a given pageId
     *
     * @param int $pageId The id of the page to get the URL for.
     * @param string $language The language to use, if not provided we will use the working language.
     *
     * @return string
     */
    public static function getUrl(int $pageId, string $language = null): string
    {
        if ($language === null) {
            $language = BackendLanguage::getWorkingLanguage();
        }

        // Prepend the language if the site is multi language
        $url = self::getContainer()->getParameter('site.multilanguage') ? '/' . $language . '/' : '/';

        // get the menuItems
        $keys = self::getKeys($language);

        // get the URL, if it doesn't exist return 404
        if (!isset($keys[$pageId])) {
            return self::getUrl(404, $language);
        }

        // return the unique URL!
        return urldecode($url . $keys[$pageId]);
    }

    /**
     * Get the URL for a give module & action combination
     *
     * @param string $module The module wherefore the URL should be build.
     * @param string $action The specific action wherefore the URL should be build.
     * @param string $language The language wherein the URL should be retrieved,
     *                         if not provided we will load the language that was provided in the URL.
     * @param array $data An array with keys and values that partially or fully match the data of the block.
     *                         If it matches multiple versions of that block it will just return the first match.
     *
     * @return string
     */
    public static function getUrlForBlock(
        string $module,
        string $action = null,
        string $language = null,
        array $data = null
    ): string {
        if ($language === null) {
            $language = BackendLanguage::getWorkingLanguage();
        }

        $pageIdForUrl = null;
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
                        if ($extra['module'] === $module && $extra['action'] === $action && $extra['action'] !== null) {
                            // if there is data check if all the requested data matches the extra data
                            if ($data !== null && isset($extra['data'])
                                && array_intersect_assoc($data, (array) $extra['data']) !== $data
                            ) {
                                // It is the correct action but has the wrong data
                                continue;
                            }

                            // exact page was found, so return
                            return self::getUrl($properties['page_id'], $language);
                        }

                        if ($extra['module'] === $module && $extra['action'] === null) {
                            // if there is data check if all the requested data matches the extra data
                            if ($data !== null && isset($extra['data'])) {
                                if (array_intersect_assoc($data, (array) $extra['data']) !== $data) {
                                    // It is the correct module but has the wrong data
                                    continue;
                                }

                                $pageIdForUrl = (int) $pageId;
                                $dataMatch = true;
                            }

                            if ($data === null && $extra['data'] === null) {
                                $pageIdForUrl = (int) $pageId;
                                $dataMatch = true;
                            }

                            if (!$dataMatch) {
                                $pageIdForUrl = (int) $pageId;
                            }
                        }
                    }
                }
            }
        }

        // Page not found so return the 404 url
        if ($pageIdForUrl === null) {
            return self::getUrl(404, $language);
        }

        $url = self::getUrl($pageIdForUrl, $language);

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
     * @param string $module Module name.
     * @param string $filename Filename.
     * @param string $subDirectory Subdirectory.
     * @param array $fileSizes Possible file sizes.
     */
    public static function imageDelete(
        string $module,
        string $filename,
        string $subDirectory = '',
        array $fileSizes = null
    ): void {
        if (empty($fileSizes)) {
            $model = get_class_vars('Backend' . \SpoonFilter::toCamelCase($module) . 'Model');
            $fileSizes = $model['fileSizes'];
        }

        // also include the source directory
        $fileSizes[] = 'source';

        $baseDirectory = FRONTEND_FILES_PATH . '/' . $module . (empty($subDirectory) ? '/' : '/' . $subDirectory . '/');
        $filesystem = new Filesystem();
        array_walk(
            $fileSizes,
            function (string $sizeDirectory) use ($baseDirectory, $filename, $filesystem) {
                $fullPath = $baseDirectory . basename($sizeDirectory) . '/' . $filename;
                if (is_file($fullPath)) {
                    $filesystem->remove($fullPath);
                }
            }
        );
    }

    /**
     * Insert extra
     *
     * @param ModuleExtraType $type What type do you want to insert, 'homepage', 'block' or 'widget'.
     * @param string $module The module you are inserting this extra for.
     * @param string $action The action this extra will use.
     * @param string $label Label which will be used when you want to connect this block.
     * @param array $data Containing extra variables.
     * @param bool $hidden Should this extra be visible in frontend or not?
     * @param int $sequence
     *
     * @throws Exception If extra type is not allowed
     *
     * @return int The new extra id
     */
    public static function insertExtra(
        ModuleExtraType $type,
        string $module,
        string $action = null,
        string $label = null,
        array $data = null,
        bool $hidden = false,
        int $sequence = null
    ): int {
        // return id for inserted extra
        return self::get('database')->insert(
            'modules_extras',
            [
                'module' => $module,
                'type' => $type,
                'label' => $label ?? $module, // if label is empty, fallback to module
                'action' => $action ?? null,
                'data' => $data === null ? null : serialize($data),
                'hidden' => $hidden,
                'sequence' => $sequence ?? self::getNextModuleExtraSequenceForModule($module),
            ]
        );
    }

    /**
     * @param string $module
     *
     * @return int
     */
    private static function getNextModuleExtraSequenceForModule(string $module): int
    {
        $database = self::get('database');
        // set next sequence number for this module
        $sequence = (int) $database->getVar(
            'SELECT MAX(sequence) + 1 FROM modules_extras WHERE module = ?',
            [$module]
        );

        // this is the first extra for this module: generate new 1000-series
        if ($sequence > 0) {
            return $sequence;
        }

        return (int) $database->getVar(
            'SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM modules_extras'
        );
    }

    /**
     * Is module installed?
     *
     * @param string $module
     *
     * @return bool
     */
    public static function isModuleInstalled(string $module): bool
    {
        return in_array($module, self::getModules(), true);
    }

    /**
     * Submit ham, this call is intended for the marking of false positives, things that were incorrectly marked as
     * spam.
     *
     * @param string $userIp IP address of the comment submitter.
     * @param string $userAgent User agent information.
     * @param string $content The content that was submitted.
     * @param string $author Submitted name with the comment.
     * @param string $email Submitted email address.
     * @param string $url Commenter URL.
     * @param string $permalink The permanent location of the entry the comment was submitted to.
     * @param string $type May be blank, comment, trackback, pingback, or a made up value like "registration".
     * @param string $referrer The content of the HTTP_REFERER header should be sent here.
     * @param array $others Other data (the variables from $_SERVER).
     *
     * @throws Exception
     *
     * @return bool If everything went fine, true will be returned, otherwise an exception will be triggered.
     */
    public static function submitHam(
        string $userIp,
        string $userAgent,
        string $content,
        string $author = null,
        string $email = null,
        string $url = null,
        string $permalink = null,
        string $type = null,
        string $referrer = null,
        array $others = null
    ): bool {
        try {
            $akismet = self::getAkismet();
        } catch (InvalidArgumentException $invalidArgumentException) {
            return false;
        }

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
     * @param string $userIp IP address of the comment submitter.
     * @param string $userAgent User agent information.
     * @param string $content The content that was submitted.
     * @param string $author Submitted name with the comment.
     * @param string $email Submitted email address.
     * @param string $url Commenter URL.
     * @param string $permalink The permanent location of the entry the comment was submitted to.
     * @param string $type May be blank, comment, trackback, pingback, or a made up value like "registration".
     * @param string $referrer The content of the HTTP_REFERER header should be sent here.
     * @param array $others Other data (the variables from $_SERVER).
     *
     * @throws Exception
     *
     * @return bool If everything went fine true will be returned, otherwise an exception will be triggered.
     */
    public static function submitSpam(
        string $userIp,
        string $userAgent,
        string $content,
        string $author = null,
        string $email = null,
        string $url = null,
        string $permalink = null,
        string $type = null,
        string $referrer = null,
        array $others = null
    ): bool {
        try {
            $akismet = self::getAkismet();
        } catch (InvalidArgumentException $invalidArgumentException) {
            return false;
        }

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
     * @param int $id The id for the extra.
     * @param string $key The key you want to update.
     * @param string|array $value The new value.
     *
     * @throws Exception If key parameter is not allowed
     */
    public static function updateExtra(int $id, string $key, $value): void
    {
        // define allowed keys
        $allowedKeys = ['label', 'action', 'data', 'hidden', 'sequence'];

        // key is not allowed
        if (!in_array($key, $allowedKeys, true)) {
            throw new Exception('The key ' . $key . ' can\'t be updated.');
        }

        // key is 'data' and value is not serialized
        if ($key === 'data' && is_array($value)) {
            // serialize value
            $value = $value === null ? null : serialize($value);
        }

        self::getContainer()->get('database')->update('modules_extras', [$key => $value], 'id = ?', [$id]);
    }

    /**
     * Update extra data
     *
     * @param int $id The id for the extra.
     * @param string $key The key in the data you want to update.
     * @param string|array $value The new value.
     */
    public static function updateExtraData(int $id, string $key, $value): void
    {
        $database = self::getContainer()->get('database');

        $data = (string) $database->getVar(
            'SELECT i.data
             FROM modules_extras AS i
             WHERE i.id = ?',
            [$id]
        );

        $data = $data === null ? [] : unserialize($data);
        $data[$key] = $value;
        $database->update('modules_extras', ['data' => serialize($data)], 'id = ?', [$id]);
    }
}
