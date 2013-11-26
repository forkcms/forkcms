<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use TijsVerkoyen\Akismet\Akismet;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../../../app/BaseModel.php';

/**
 * In this file we store all generic functions that we will be using in the frontend.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class FrontendModel extends BaseModel
{
    /**
     * Cached modules
     *
     * @var    array
     */
    private static $modules = array();

    /**
     * Cached module-settings
     *
     * @var    array
     */
    private static $moduleSettings = array();

    /**
     * Visitor id from tracking cookie
     *
     * @var    string
     */
    private static $visitorId;

    /**
     * Add a number to the string
     *
     * @param  string $string The string where the number will be appended to.
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
        if (SpoonFilter::isNumeric($last)) {
            // remove last chunk
            array_pop($chunks);

            // join together, and increment the last one
            $string = implode('-', $chunks) . '-' . ((int) $last + 1);
        } // not numeric, so add -2
        else {
            $string .= '-2';
        }

        // return
        return $string;
    }

    /**
     * Add parameters to an URL
     *
     * @param  string $url        The URL to append the parameters too.
     * @param  array  $parameters The parameters as key-value-pairs.
     * @return string
     */
    public static function addURLParameters($url, array $parameters)
    {
        $url = (string) $url;

        if (empty($parameters)) {
            return $url;
        }

        $chunks = explode('#', $url, 2);
        $hash   = '';
        if (isset($chunks[1])) {
            $url  = $chunks[0];
            $hash = '#' . $chunks[1];
        }

        // build query string
        $queryString = http_build_query($parameters, null, '&amp;');
        if (mb_strpos($url, '?') !== false) {
            $url .= '&' . $queryString . $hash;
        } else {
            $url .= '?' . $queryString . $hash;
        }

        return $url;
    }

    /**
     * Get plain text for a given text
     *
     * @param  string           $text           The text to convert.
     * @param  bool  [optional] $includeAHrefs  Should the url be appended after the link-text?
     * @param  bool  [optional] $includeImgAlts Should the alt tag be inserted for images?
     * @return string
     */
    public static function convertToPlainText($text, $includeAHrefs = true, $includeImgAlts = true)
    {
        // remove tabs, line feeds and carriage returns
        $text = str_replace(array("\t", "\n", "\r"), '', $text);

        // remove the head-, style- and script-tags and all their contents
        $text = preg_replace('|\<head[^>]*\>(.*\n*)\</head\>|isU', '', $text);
        $text = preg_replace('|\<style[^>]*\>(.*\n*)\</style\>|isU', '', $text);
        $text = preg_replace('|\<script[^>]*\>(.*\n*)\</script\>|isU', '', $text);

        // put back some new lines where needed
        $text = preg_replace(
            '#(\<(h1|h2|h3|h4|h5|h6|p|ul|ol)[^\>]*\>.*\</(h1|h2|h3|h4|h5|h6|p|ul|ol)\>)#isU',
            "\n$1",
            $text
        );

        // replace br tags with newlines
        $text = preg_replace('#(\<br[^\>]*\>)#isU', "\n", $text);

        // replace links with the inner html of the link with the url between ()
        // eg.: <a href="http://site.domain.com">My site</a> => My site (http://site.domain.com)
        if ($includeAHrefs) {
            $text = preg_replace('|<a.*href="(.*)".*>(.*)</a>|isU', '$2 ($1)', $text);
        }

        // replace images with their alternative content
        // eg. <img src="path/to/the/image.jpg" alt="My image" /> => My image
        if ($includeImgAlts) {
            $text = preg_replace('|\<img[^>]*alt="(.*)".*/\>|isU', '$1', $text);
        }

        // decode html entities
        $text = html_entity_decode($text, ENT_QUOTES, 'ISO-8859-15');

        // remove space characters at the beginning and end of each line and clear lines with nothing but spaces
        $text = preg_replace('/^\s*|\s*$|^\s*$/m', '', $text);

        // strip tags
        $text = strip_tags($text, '<h1><h2><h3><h4><h5><h6><p><li>');

        // format heading, paragraphs and list items
        $text = preg_replace('|\<h[123456]([^\>]*)\>(.*)\</h[123456]\>|isU', "\n** $2 **\n", $text);
        $text = preg_replace('|\<p([^\>]*)\>(.*)\</p\>|isU', "$2\n", $text);
        $text = preg_replace('|\<li([^\>]*)\>\n*(.*)\n*\</li\>|isU', "- $2\n", $text);

        // replace 3 and more line breaks in a row by 2 line breaks
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        // use php constant for new lines
        $text = str_replace("\n", PHP_EOL, $text);

        // trim line breaks at the beginning and ending of the text
        $text = trim($text, PHP_EOL);

        // return the plain text
        return $text;
    }

    /**
     * Generate a totally random but readable/speakable password
     *
     * @param  int  [optional] $length           The maximum length for the password to generate.
     * @param  bool [optional] $uppercaseAllowed Are uppercase letters allowed?
     * @param  bool [optional] $lowercaseAllowed Are lowercase letters allowed?
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
        $vowelsCount     = count($vowels);
        $pass            = '';
        $tmp             = '';

        // create temporary pass
        for ($i = 0; $i < $length; $i++) {
            $tmp .= ($consonants[rand(0, $consonantsCount - 1)] . $vowels[rand(
                    0,
                    $vowelsCount - 1
                )]);
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

        // return pass
        return $pass;
    }

    /**
     * Generate thumbnails based on the folders in the path
     * Use
     *  - 128x128 as folder name to generate an image where the width will be 128px and the height will be 128px
     *  - 128x as folder name to generate an image where the width will be 128px, the height will be calculated based on the aspect ratio.
     *  - x128 as folder name to generate an image where the height will be 128px, the width will be calculated based on the aspect ratio.
     *
     * @param string $path       The path wherein the thumbnail-folders will be stored.
     * @param string $sourceFile The location of the source file.
     */
    public static function generateThumbnails($path, $sourceFile)
    {
        // get folder listing
        $folders  = self::getThumbnailFolders($path);
        $filename = basename($sourceFile);

        // loop folders
        foreach ($folders as $folder) {
            // generate the thumbnail
            $thumbnail = new SpoonThumbnail($sourceFile, $folder['width'], $folder['height']);
            $thumbnail->setAllowEnlargement(true);

            // if the width & height are specified we should ignore the aspect ratio
            if ($folder['width'] !== null && $folder['height'] !== null) {
                $thumbnail->setForceOriginalAspectRatio(false);
            }
            $thumbnail->parseToFile($folder['path'] . '/' . $filename);
        }
    }

    /**
     * Get the modules
     *
     * @return array
     */
    public static function getModules()
    {
        // validate cache
        if (empty(self::$modules)) {
            // get all modules
            $modules = (array) self::getContainer()->get('database')->getColumn('SELECT m.name FROM modules AS m');

            // add modules to the cache
            foreach ($modules as $module) {
                self::$modules[] = $module;
            }
        }

        return self::$modules;
    }

    /**
     * Get a module setting
     *
     * @param  string             $module       The module wherefore a setting has to be retrieved.
     * @param  string             $name         The name of the setting to be retrieved.
     * @param  mixed   [optional] $defaultValue A value that will be stored if the setting isn't present.
     * @return mixed
     */
    public static function getModuleSetting($module, $name, $defaultValue = null)
    {
        // redefine
        $module = (string) $module;
        $name   = (string) $name;

        // get them all
        if (empty(self::$moduleSettings)) {
            // fetch settings
            $settings = (array) self::getContainer()->get('database')->getRecords(
                                    'SELECT ms.module, ms.name, ms.value
                                     FROM modules_settings AS ms
                                     INNER JOIN modules AS m ON ms.module = m.name'
            );

            // loop settings and cache them, also unserialize the values
            foreach ($settings as $row) {
                self::$moduleSettings[$row['module']][$row['name']] = unserialize(
                    $row['value']
                );
            }
        }

        // if the setting doesn't exists, store it (it will be available from te cache)
        if (!array_key_exists($module, self::$moduleSettings) || !array_key_exists(
                $name,
                self::$moduleSettings[$module]
            )
        ) {
            self::setModuleSetting($module, $name, $defaultValue);
        }

        // return
        return self::$moduleSettings[$module][$name];
    }

    /**
     * Get all module settings at once
     *
     * @param  string $module The module wherefore all settings has to be retrieved.
     * @return array
     */
    public static function getModuleSettings($module)
    {
        $module = (string) $module;

        // get them all
        if (empty(self::$moduleSettings[$module])) {
            // fetch settings
            $settings = (array) self::getContainer()->get('database')->getRecords(
                                    'SELECT ms.module, ms.name, ms.value
                                     FROM modules_settings AS ms'
            );

            // loop settings and cache them, also unserialize the values
            foreach ($settings as $row) {
                self::$moduleSettings[$row['module']][$row['name']] = unserialize(
                    $row['value']
                );
            }
        }

        // validate again
        if (!isset(self::$moduleSettings[$module])) {
            return array();
        }

        // return
        return self::$moduleSettings[$module];
    }

    /**
     * Get all data for a page
     *
     * @param  int   $pageId The pageId wherefore the data will be retrieved.
     * @return array
     */
    public static function getPage($pageId)
    {
        // redefine
        $pageId = (int) $pageId;

        // get database instance
        $db = self::getContainer()->get('database');

        // get data
        $record = (array) $db->getRecord(
                             'SELECT p.id, p.parent_id, p.revision_id, p.template_id, p.title, p.navigation_title, p.navigation_title_overwrite, p.data,
                                  m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
                                  m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
                                  m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
                                  m.custom AS meta_custom,
                                  m.url, m.url_overwrite,
                                  m.data AS meta_data,
                                  t.path AS template_path, t.data AS template_data
                              FROM pages AS p
                              INNER JOIN meta AS m ON p.meta_id = m.id
                              INNER JOIN themes_templates AS t ON p.template_id = t.id
                              WHERE p.id = ? AND p.status = ? AND p.hidden = ? AND p.language = ?
                              LIMIT 1',
                                 array($pageId, 'active', 'N', FRONTEND_LANGUAGE)
        );

        // validate
        if (empty($record)) {
            return array();
        }

        // unserialize page data and template data
        if (isset($record['data']) && $record['data'] != '') {
            $record['data'] = unserialize($record['data']);
        }
        if (isset($record['meta_data']) && $record['meta_data'] != '') {
            $record['meta_data'] = unserialize(
                $record['meta_data']
            );
        }
        if (isset($record['template_data']) && $record['template_data'] != '') {
            $record['template_data'] = @unserialize(
                $record['template_data']
            );
        }

        // get blocks
        $blocks = (array) $db->getRecords(
                             'SELECT pe.id AS extra_id, pb.html, pb.position,
                              pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
                              FROM pages_blocks AS pb
                              INNER JOIN pages AS p ON p.revision_id = pb.revision_id
                              LEFT OUTER JOIN modules_extras AS pe ON pb.extra_id = pe.id AND pe.hidden = ?
                              WHERE pb.revision_id = ? AND p.status = ? AND pb.visible = ?
                              ORDER BY pb.position, pb.sequence',
                                 array('N', $record['revision_id'], 'active', 'Y')
        );

        // init positions
        $record['positions'] = array();

        // loop blocks
        foreach ($blocks as $block) {
            // unserialize data if it is available
            if (isset($block['data'])) {
                $block['data'] = unserialize($block['data']);
            }

            // save to position
            $record['positions'][$block['position']][] = $block;
        }

        return $record;
    }

    /**
     * Get a revision for a page
     *
     * @param  int   $revisionId The revisionID.
     * @return array
     */
    public static function getPageRevision($revisionId)
    {
        $revisionId = (int) $revisionId;

        // get database instance
        $db = self::getContainer()->get('database');

        // get data
        $record = (array) $db->getRecord(
                             'SELECT p.id, p.revision_id, p.template_id, p.title, p.navigation_title, p.navigation_title_overwrite, p.data,
                                  m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
                                  m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
                                  m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
                                  m.custom AS meta_custom,
                                  m.url, m.url_overwrite,
                                  t.path AS template_path, t.data AS template_data
                              FROM pages AS p
                              INNER JOIN meta AS m ON p.meta_id = m.id
                              INNER JOIN themes_templates AS t ON p.template_id = t.id
                              WHERE p.revision_id = ? AND p.language = ?
                              LIMIT 1',
                                 array($revisionId, FRONTEND_LANGUAGE)
        );

        // validate
        if (empty($record)) {
            return array();
        }

        // unserialize page data and template data
        if (isset($record['data']) && $record['data'] != '') {
            $record['data'] = unserialize($record['data']);
        }
        if (isset($record['template_data']) && $record['template_data'] != '') {
            $record['template_data'] = @unserialize(
                $record['template_data']
            );
        }

        // get blocks
        $blocks = (array) $db->getRecords(
                             'SELECT pe.id AS extra_id, pb.html, pb.position,
                              pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
                              FROM pages_blocks AS pb
                              INNER JOIN pages AS p ON p.revision_id = pb.revision_id
                              LEFT OUTER JOIN modules_extras AS pe ON pb.extra_id = pe.id AND pe.hidden = ?
                              WHERE pb.revision_id = ?
                              ORDER BY pb.position, pb.sequence',
                                 array('N', $record['revision_id'])
        );

        // init positions
        $record['positions'] = array();

        // loop blocks
        foreach ($blocks as $block) {
            // unserialize data if it is available
            if (isset($block['data'])) {
                $block['data'] = unserialize($block['data']);
            }

            // save to position
            $record['positions'][$block['position']][] = $block;
        }

        return $record;
    }

    /**
     * Get the thumbnail folders
     *
     * @param  string           $path          The path
     * @param  bool  [optional] $includeSource Should the source-folder be included in the return-array.
     * @return array
     */
    public static function getThumbnailFolders($path, $includeSource = false)
    {
        $return = array();
        $fs     = new Filesystem();
        if (!$fs->exists($path)) {
            return $return;
        }
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

            $item            = array();
            $item['dirname'] = $directory->getBasename();
            $item['path']    = $directory->getRealPath();
            if (substr($path, 0, strlen(PATH_WWW)) == PATH_WWW) {
                $item['url'] = substr($path, strlen(PATH_WWW));
            }

            if ($item['dirname'] == 'source') {
                $item['width']  = null;
                $item['height'] = null;
            } else {
                $item['width']  = ($chunks[0] != '') ? (int) $chunks[0] : null;
                $item['height'] = ($chunks[1] != '') ? (int) $chunks[1] : null;
            }

            $return[] = $item;
        }

        return $return;
    }

    /**
     * Get the UTC date in a specific format. Use this method when inserting dates in the database!
     *
     * @param  string [optional] $format    The format wherein the data will be returned, if not provided we will return it in MySQL-datetime-format.
     * @param  int    [optional] $timestamp A UNIX-timestamp that will be used as base.
     * @return string
     */
    public static function getUTCDate($format = null, $timestamp = null)
    {
        // init var
        $format = ($format !== null) ? (string) $format : 'Y-m-d H:i:s';

        // no timestamp given
        if ($timestamp === null) {
            return gmdate($format);
        }

        // timestamp given
        return gmdate($format, (int) $timestamp);
    }

    /**
     * Get the UTC timestamp for a date/time object combination.
     *
     * @param  SpoonFormDate            $date An instance of SpoonFormDate.
     * @param  SpoonFormTime [optional] $time An instance of SpoonFormTime.
     * @return int
     */
    public static function getUTCTimestamp(SpoonFormDate $date, SpoonFormTime $time = null)
    {
        // validate date/time object
        if (!$date->isValid() || ($time !== null && !$time->isValid())
        ) {
            throw new FrontendException('You need to provide two objects that actually contain valid data.');
        }

        // init vars
        $year  = gmdate('Y', $date->getTimestamp());
        $month = gmdate('m', $date->getTimestamp());
        $day   = gmdate('j', $date->getTimestamp());

        // time object was given
        if ($time !== null) {
            // define hour & minute
            list($hour, $minute) = explode(':', $time->getValue());
        } // user default time
        else {
            $hour   = 0;
            $minute = 0;
        }

        // make and return timestamp
        return mktime($hour, $minute, 0, $month, $day, $year);
    }

    /**
     * Get the visitor's id (using a tracking cookie)
     *
     * @return string
     */
    public static function getVisitorId()
    {
        // check if tracking id is fetched already
        if (self::$visitorId !== null) {
            return self::$visitorId;
        }

        // get/init tracking identifier
        self::$visitorId = CommonCookie::exists('track') ? (string) CommonCookie::get('track') : md5(
            uniqid() . SpoonSession::getSessionId()
        );

        if (!FrontendModel::getModuleSetting('core', 'show_cookie_bar', false) || CommonCookie::hasAllowedCookies()) {
            CommonCookie::set('track', self::$visitorId, 86400 * 365);
        }

        return self::getVisitorId();
    }

    /**
     * General method to check if something is spam
     *
     * @param  string                $content   The content that was submitted.
     * @param  string                $permaLink The permanent location of the entry the comment was submitted to.
     * @param  string     [optional] $author    Commenter's name.
     * @param  string     [optional] $email     Commenter's email address.
     * @param  string     [optional] $URL       Commenter's URL.
     * @param  string     [optional] $type      May be blank, comment, trackback, pingback, or a made up value like "registration".
     * @return bool|string           Will return a boolean, except when we can't decide the status (unknown will be returned in that case)
     */
    public static function isSpam($content, $permaLink, $author = null, $email = null, $URL = null, $type = 'comment')
    {
        // get some settings
        $akismetKey = self::getModuleSetting('core', 'akismet_key');

        // invalid key, so we can't detect spam
        if ($akismetKey === '') {
            return false;
        }

        // create new instance
        $akismet = new Akismet($akismetKey, SITE_URL);

        // set properties
        $akismet->setTimeOut(10);
        $akismet->setUserAgent('Fork CMS/' . FORK_VERSION);

        // try it, to decide if the item is spam
        try {
            // check with Akismet if the item is spam
            return $akismet->isSpam($content, $author, $email, $URL, $permaLink, $type);
        } // catch exceptions
        catch (Exception $e) {
            // in debug mode we want to see exceptions, otherwise the fallback will be triggered
            if (SPOON_DEBUG) {
                throw $e;
            }

            // return unknown status
            return 'unknown';
        }
    }

    /**
     * Push a notification to Apple's notifications-server
     *
     * @param mixed             $alert             The message/dictionary to send.
     * @param int    [optional] $badge             The number for the badge.
     * @param string [optional] $sound             The sound that should be played.
     * @param array  [optional] $extraDictionaries Extra dictionaries.
     */
    public static function pushToAppleApp($alert, $badge = null, $sound = null, array $extraDictionaries = null)
    {
        // get ForkAPI-keys
        $publicKey  = FrontendModel::getModuleSetting('core', 'fork_api_public_key', '');
        $privateKey = FrontendModel::getModuleSetting('core', 'fork_api_private_key', '');

        // no keys, so stop here
        if ($publicKey == '' || $privateKey == '') {
            return;
        }

        // get all apple-device tokens
        $deviceTokens = (array) FrontendModel::getContainer()->get('database')->getColumn(
                                             'SELECT s.value
                                              FROM users AS i
                                              INNER JOIN users_settings AS s
                                              WHERE i.active = ? AND i.deleted = ? AND s.name = ? AND s.value != ?',
                                                 array('Y', 'N', 'apple_device_token', 'N;')
        );

        // no devices, so stop here
        if (empty($deviceTokens)) {
            return;
        }

        // init var
        $tokens = array();

        // loop devices
        foreach ($deviceTokens as $row) {
            // unserialize
            $row = unserialize($row);

            // loop and add
            foreach ($row as $item) {
                $tokens[] = $item;
            }
        }

        // no tokens, so stop here
        if (empty($tokens)) {
            return;
        }

        // require the class
        require_once PATH_LIBRARY . '/external/fork_api.php';

        // create instance
        $forkAPI = new ForkAPI($publicKey, $privateKey);

        try {
            // push
            $response = $forkAPI->applePush($tokens, $alert, $badge, $sound, $extraDictionaries);

            if (!empty($response)) {
                // get db
                $db = FrontendModel::getContainer()->get('database');

                // loop the failed keys and remove them
                foreach ($response as $deviceToken) {
                    // get setting wherein the token is available
                    $row = $db->getRecord(
                              'SELECT i.*
                               FROM users_settings AS i
                               WHERE i.name = ? AND i.value LIKE ?',
                                  array('apple_device_token', '%' . $deviceToken . '%')
                    );

                    // any rows?
                    if (!empty($row)) {
                        // reset data
                        $data = unserialize($row['value']);

                        // loop keys
                        foreach ($data as $key => $token) {
                            // match and unset if needed.
                            if ($token == $deviceToken) {
                                unset($data[$key]);
                            }
                        }

                        // no more tokens left?
                        if (empty($data)) {
                            $db->delete(
                               'users_settings',
                                   'user_id = ? AND name = ?',
                                   array($row['user_id'], $row['name'])
                            );
                        } // save
                        else {
                            $db->update(
                               'users_settings',
                                   array('value' => serialize($data)),
                                   'user_id = ? AND name = ?',
                                   array($row['user_id'], $row['name'])
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            if (SPOON_DEBUG) {
                throw $e;
            }
        }
    }

    /**
     * Store a module setting
     *
     * @param string $module The module wherefore a setting has to be stored.
     * @param string $name   The name of the setting.
     * @param mixed  $value  The value (will be serialized so make sure the type is correct).
     */
    public static function setModuleSetting($module, $name, $value)
    {
        $module = (string) $module;
        $name   = (string) $name;
        $value  = serialize($value);

        // store
        self::getContainer()->get('database')->execute(
            'INSERT INTO modules_settings (module, name, value)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
                array($module, $name, $value, $value)
        );

        // store in cache
        self::$moduleSettings[$module][$name] = unserialize($value);
    }

    /**
     * Start processing the hooks
     */
    public static function startProcessingHooks()
    {
        $fs = new Filesystem();
        // is the queue already running?
        if ($fs->exists(FRONTEND_CACHE_PATH . '/hooks/pid')) {
            // get the pid
            $pid = trim(file_get_contents(FRONTEND_CACHE_PATH . '/hooks/pid'));

            // running on windows?
            if (strtolower(substr(php_uname('s'), 0, 3)) == 'win') {
                // get output
                $output = @shell_exec('tasklist.exe /FO LIST /FI "PID eq ' . $pid . '"');

                // validate output
                if ($output == '' || $output === false) {
                    // delete the pid file
                    $fs->remove(FRONTEND_CACHE_PATH . '/hooks/pid');
                } // already running
                else {
                    return true;
                }
            } // Mac
            elseif (strtolower(substr(php_uname('s'), 0, 6)) == 'darwin') {
                // get output
                $output = @posix_getsid($pid);

                // validate output
                if ($output === false) {
                    // delete the pid file
                    $fs->remove(FRONTEND_CACHE_PATH . '/hooks/pid');
                } // already running
                else {
                    return true;
                }
            } // UNIX
            else {
                // check if the process is still running, by checking the proc folder
                if (!$fs->exists('/proc/' . $pid)) {
                    // delete the pid file
                    $fs->remove(FRONTEND_CACHE_PATH . '/hooks/pid');
                } // already running
                else {
                    return true;
                }
            }
        }

        // init var
        $parts       = parse_url(SITE_URL);
        $errNo       = '';
        $errStr      = '';
        $defaultPort = 80;
        if ($parts['scheme'] == 'https') {
            $defaultPort = 433;
        }

        // open the socket
        $socket = fsockopen(
            $parts['host'],
            (isset($parts['port'])) ? $parts['port'] : $defaultPort,
            $errNo,
            $errStr,
            1
        );

        // build the request
        $request = 'GET /backend/cronjob.php?module=core&action=process_queued_hooks HTTP/1.1' . "\r\n";
        $request .= 'Host: ' . $parts['host'] . "\r\n";
        $request .= 'Content-Length: 0' . "\r\n\r\n";
        $request .= 'Connection: Close' . "\r\n\r\n";

        // send the request
        fwrite($socket, $request);

        // close the socket
        fclose($socket);

        // return
        return true;
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
        // validate
        if (!is_callable($callback)) {
            throw new FrontendException('Invalid callback!');
        }

        // build record
        $item['event_module'] = (string) $eventModule;
        $item['event_name']   = (string) $eventName;
        $item['module']       = (string) $module;
        $item['callback']     = serialize($callback);
        $item['created_on']   = FrontendModel::getUTCDate();

        // get db
        $db = self::getContainer()->get('database');

        // check if the subscription already exists
        $exists = (bool) $db->getVar(
                            'SELECT 1
                             FROM hooks_subscriptions AS i
                             WHERE i.event_module = ? AND i.event_name = ? AND i.module = ?
                             LIMIT 1',
                                array($eventModule, $eventName, $module)
        );

        // update
        if ($exists) {
            $db->update(
               'hooks_subscriptions',
                   $item,
                   'event_module = ? AND event_name = ? AND module = ?',
                   array($eventModule, $eventName, $module)
            );
        } // insert
        else {
            $db->insert('hooks_subscriptions', $item);
        }
    }

    /**
     * Trigger an event
     *
     * @param string                $module    The module that triggers the event.
     * @param string                $eventName The name of the event.
     * @param mixed      [optional] $data      The data that should be send to subscribers.
     */
    public static function triggerEvent($module, $eventName, $data = null)
    {
        $module    = (string) $module;
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
            // init var
            $queuedItems = array();

            // loop items
            foreach ($subscriptions as $subscription) {
                // build record
                $item['module']     = $subscription['module'];
                $item['callback']   = $subscription['callback'];
                $item['data']       = serialize($data);
                $item['status']     = 'queued';
                $item['created_on'] = FrontendModel::getUTCDate();

                // add
                $queuedItems[] = self::getContainer()->get('database')->insert('hooks_queue', $item);

                $log->info(
                    'Callback (' . $subscription['callback'] . ') is subscribed to event (' . $module . '/' . $eventName . ').'
                );
            }

            // start processing
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
        $eventName   = (string) $eventName;
        $module      = (string) $module;

        self::getContainer()->get('database')->delete(
            'hooks_subscriptions',
                'event_module = ? AND event_name = ? AND module = ?',
                array($eventModule, $eventName, $module)
        );
    }
}
