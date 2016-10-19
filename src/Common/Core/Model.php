<?php

namespace Common\Core;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../../../app/BaseModel.php';

/**
 * This class will initiate the frontend-application
 */
class Model extends \BaseModel
{
    /**
     * Cached modules
     *
     * @var    array
     */
    protected static $modules = array();

    /**
     * Add a number to the string
     *
     * @param string $string The string where the number will be appended to.
     *
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
            // remove last chunk
            array_pop($chunks);

            // join together, and increment the last one
            $string = implode('-', $chunks) . '-' . ((int) $last + 1);
        } else {
            // not numeric, so add -2
            $string .= '-2';
        }

        // return
        return $string;
    }

    /**
     * Generate a totally random but readable/speakable password
     *
     * @param int  $length           The maximum length for the password to generate.
     * @param bool $uppercaseAllowed Are uppercase letters allowed?
     * @param bool $lowercaseAllowed Are lowercase letters allowed?
     *
     * @return string
     */
    public static function generatePassword($length = 6, $uppercaseAllowed = true, $lowercaseAllowed = true)
    {
        // list of allowed vowels and vowel sounds
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
            'st',
        );

        // init vars
        $consonantsCount = count($consonants);
        $vowelsCount = count($vowels);
        $pass = '';
        $tmp = '';

        // create temporary pass
        for ($i = 0; $i < $length; ++$i) {
            $tmp .= ($consonants[mt_rand(0, $consonantsCount - 1)] .
                $vowels[mt_rand(0, $vowelsCount - 1)]);
        }

        // reformat the pass
        for ($i = 0; $i < $length; ++$i) {
            if (mt_rand(0, 1) == 1) {
                $pass .= mb_strtoupper(mb_substr($tmp, $i, 1));
            } else {
                $pass .= mb_substr($tmp, $i, 1);
            }
        }

        // reformat it again, if uppercase isn't allowed
        if (!$uppercaseAllowed) {
            $pass = mb_strtolower($pass);
        }

        // reformat it again, if uppercase isn't allowed
        if (!$lowercaseAllowed) {
            $pass = mb_strtoupper($pass);
        }

        // return pass
        return $pass;
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
     * Get the thumbnail folders
     *
     * @param string $path          The path
     * @param bool   $includeSource Should the source-folder be included in the return-array.
     *
     * @return array
     */
    public static function getThumbnailFolders($path, $includeSource = false)
    {
        $return = array();
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)) {
            return $return;
        }
        $finder = new Finder();
        $finder->name('/^([0-9]*)x([0-9]*)$/');
        if ($includeSource) {
            $finder->name('source');
        }

        foreach ($finder->directories()->in($path)->depth('== 0') as $directory) {
            $chunks = explode('x', $directory->getBasename(), 2);
            if (count($chunks) != 2 && !$includeSource) {
                continue;
            }

            $item = array();
            $item['dirname'] = $directory->getBasename();
            $item['path'] = $directory->getRealPath();
            if (mb_substr($path, 0, mb_strlen(PATH_WWW)) == PATH_WWW) {
                $item['url'] = mb_substr($path, mb_strlen(PATH_WWW));
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
     * Get the UTC date in a specific format. Use this method when inserting dates in the database!
     *
     * @param string $format    The format to return the timestamp in. Default is MySQL datetime format.
     * @param int    $timestamp The timestamp to use, if not provided the current time will be used.
     *
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
     *
     * @throws \Exception If provided $date, $time or both are invalid
     *
     * @return int
     */
    public static function getUTCTimestamp(\SpoonFormDate $date, \SpoonFormTime $time = null)
    {
        // validate date/time object
        if (!$date->isValid() || ($time !== null && !$time->isValid())
        ) {
            throw new \Exception('You need to provide two objects that actually contain valid data.');
        }

        // init vars
        $year = gmdate('Y', $date->getTimestamp());
        $month = gmdate('m', $date->getTimestamp());
        $day = gmdate('j', $date->getTimestamp());

        if ($time !== null) {
            // define hour & minute
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
     * Subscribe to an event, when the subscription already exists, the callback will be updated.
     *
     * @param string $eventModule The module that triggers the event.
     * @param string $eventName   The name of the event.
     * @param string $module      The module that subscribes to the event.
     * @param mixed  $callback    The callback that should be executed when the event is triggered.
     *
     * @throws \Exception          When the callback is invalid
     *
     * @deprecated use the symfony event dispatcher instead
     */
    public static function subscribeToEvent($eventModule, $eventName, $module, $callback)
    {
        trigger_error(
            'Deprecated, all events will be replaced with symfony events',
            E_USER_DEPRECATED
        );

        // validate
        if (!is_callable($callback)) {
            throw new \Exception('Invalid callback!');
        }

        // build record
        $item['event_module'] = (string) $eventModule;
        $item['event_name'] = (string) $eventName;
        $item['module'] = (string) $module;
        $item['callback'] = serialize($callback);
        $item['created_on'] = self::getUTCDate();

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
        } else {
            // insert
            $db->insert('hooks_subscriptions', $item);
        }
    }

    /**
     * Trigger an event
     *
     * @param string $module    The module that triggers the event.
     * @param string $eventName The name of the event.
     * @param mixed  $data      The data that should be send to subscribers.
     *
     * @deprecated use the symfony event dispatcher instead
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
            // init var
            $queuedItems = array();

            // loop items
            foreach ($subscriptions as $subscription) {
                // build record
                $item['module'] = $subscription['module'];
                $item['callback'] = $subscription['callback'];
                $item['data'] = serialize($data);
                $item['status'] = 'queued';
                $item['created_on'] = self::getUTCDate();

                // add
                $queuedItems[] = self::getContainer()->get('database')->insert('hooks_queue', $item);

                $log->info(
                    'Callback (' . $subscription['callback'] . ') is subscribed to event (' . $module . '/' .
                    $eventName . ').'
                );
            }

            // start processing
            self::startProcessingHooks();
        }
    }

    /**
     * Start processing the hooks
     *
     * @deprecated use the symfony event dispatcher instead
     */
    public static function startProcessingHooks()
    {
        $filesystem = new Filesystem();
        // is the queue already running?
        if ($filesystem->exists(self::getContainer()->getParameter('kernel.cache_dir') . '/Hooks/pid')) {
            // get the pid
            $pid = trim(file_get_contents(self::getContainer()->getParameter('kernel.cache_dir') . '/Hooks/pid'));

            // running on windows?
            if (mb_strtolower(mb_substr(php_uname('s'), 0, 3)) == 'win') {
                // get output
                $output = @shell_exec('tasklist.exe /FO LIST /FI "PID eq ' . $pid . '"');

                // validate output
                if ($output == '' || $output === false) {
                    // delete the pid file
                    $filesystem->remove(self::getContainer()->getParameter('kernel.cache_dir') . '/Hooks/pid');
                } else {
                    // already running
                    return true;
                }
            } elseif (mb_strtolower(mb_substr(php_uname('s'), 0, 6)) == 'darwin') {
                // darwin == Mac
                // get output
                $output = @posix_getsid($pid);

                // validate output
                if ($output === false) {
                    // delete the pid file
                    $filesystem->remove(self::getContainer()->getParameter('kernel.cache_dir') . '/Hooks/pid');
                } else {
                    // already running
                    return true;
                }
            } else {
                // UNIX
                // check if the process is still running, by checking the proc folder
                if (!$filesystem->exists('/proc/' . $pid)) {
                    // delete the pid file
                    $filesystem->remove(self::getContainer()->getParameter('kernel.cache_dir') . '/Hooks/pid');
                } else {
                    // already running
                    return true;
                }
            }
        }

        // init var
        $parts = parse_url(SITE_URL);
        $errNo = '';
        $errStr = '';
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
        $request = 'GET /backend/cronjob?module=Core&action=ProcessQueuedHooks HTTP/1.1' . "\r\n";
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
     * Unsubscribe from an event
     *
     * @param string $eventModule The module that triggers the event.
     * @param string $eventName   The name of the event.
     * @param string $module      The module that subscribes to the event.
     *
     * @deprecated use the symfony event dispatcher instead
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
}
