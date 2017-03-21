<?php

namespace Common\Core;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\BaseModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * This class will initiate the frontend-application
 */
class Model extends BaseModel
{
    /**
     * Cached modules
     *
     * @var array
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
}
